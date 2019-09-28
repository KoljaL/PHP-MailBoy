<?php
// github.com/ddeboer/imap
use Ddeboer\Imap\Server;

require_once('IMAP-library/autoload.php');

// github.com/PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

////////DEBUG//////////
$phpmailer       = 1;//
$ausgabe         = 1;//
$SMTPDebug       = 0;//
////////DEBUG//////////

// wenn die Empfänger nicht die Adressen der anderen Teilnehmer sehen sollen: $BCC = 1;
$BCC = 1;

$datenbank = "verteiler.sqt";
$db = new PDO('sqlite:' . $datenbank);
$select = $db->query("SELECT `id`,`verteiler_name`,`verteiler_email`,`verteiler_array`,`server_host`,`server_port`,`server_user`,
  `server_passwort`,`server_postfach`,`SMTPSecure`,`SMTPAuth`,`IsHTML`,`attachment_folder` FROM `verteiler` ORDER BY `id` DESC");

$db_verteiler = $select->fetchAll(PDO::FETCH_ASSOC);
// durch die Verteiler schleifen
foreach ($db_verteiler as $nachricht) {
    extract($nachricht);
    // Verteiler String aus der Datenbank in ein Array umwandeln
    $verteiler_array    = explode(',', $verteiler_array);


    ////////¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ IMAP Class ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯\\\\\\\\\\\\\\\\\\\\
    $server          = new Server($server_host);
    $connection      = $server->authenticate($server_user, $server_passwort);
    $mailboxes       = $connection->getMailboxes();
    $mailbox         = $connection->getMailbox($server_postfach);
    $messages        = $mailbox->getMessages();

    // durch die Nachrichten schleifen
    foreach ($messages as $message) {
        // nur durch die ungelesenen
        if ($message->isSeen() != 1) {
            $nummer       = $message->getNumber();
            $gelesen      = $message->isSeen();
            $header_array = array( $message->getHeaders() );
            $betreff      = '[' . $verteiler_name . '] ';
            $betreff     .= $message->getSubject();
            $messagetext  = $message->getBodyHtml();
            $messagetext .= $verteiler_signatur;

            $anhangname  = array();
            $j           = 0; // für die Übergabe an den Mailer werden die Namen der Anhänge benötigt
            $attachments = $message->getAttachments();
            foreach ($attachments as $attachment) {
                file_put_contents($attachment_folder . '/' . $verteiler_name . '-' . $nummer . '-' . $attachment->getFilename(), $attachment->getDecodedContent());
                $anhangname[$j] = $attachment->getFilename();
                $j++;
            }
            //\\\\\\_________________________ IMAP Class _______________________///////////////////




            ////////¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ Ausgabe ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯\\\\\\\\\\\\\\\\\\\\
            if ($ausgabe == 1) {
                echo "<meta http-equiv='Content-Type' content='text/html; charset=UTF-8'>";
                //print_r($header_array);echo "<br>";
                echo "<b>Nummer: </b>" . $nummer . "<br>";
                echo "<b>Betreff: </b>" . $betreff . "<br>";
                echo "<b>an: </b>" . implode(",", $verteiler_array) . "<br>";
                echo "<b>Text: </b>" .substr(strip_tags($messagetext), 0, 250) . "<br>";
                // Durch die vielen Anhänge schleifen
                for ($k = 0; $k < sizeof($anhangname); $k++) {
                    echo "<b>Anhang: </b>" . $anhangname[$k] . "<br>";
                }
                echo "<br>";
            }
            //\\\\\\_________________________ Ausgabe _______________________///////////////////




            ////////¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ PHPMailer ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯\\\\\\\\\\\\\\\\\\\\
        if ($phpmailer == 1) { // nur zum debuggen ausschaltbar
            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->IsSMTP(); // enable SMTP
            $mail->SMTPDebug  = $SMTPDebug; // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth   = $SMTPAuth; // authentication enabled
            $mail->SMTPSecure = $SMTPSecure; // secure transfer enabled REQUIRED for Gmail
            $mail->Host       = $server_host;
            $mail->Port       = $server_port; // or 587
            $mail->Username   = $server_user;
            $mail->Password   = $server_passwort;
            $mail->Subject    = $betreff;
            $mail->Body       = $messagetext;
            $mail->SetFrom($verteiler_email);
            $mail->IsHTML($IsHTML);

            // Durch die Verteilerliste schleifen
            foreach ($verteiler_array as $verteiler_adrr) {
                if ($BCC == 1) {
                    $mail->AddBCC($verteiler_adrr);
                } else {
                    $mail->AddAddress($verteiler_adrr);
                }
            }

            // Durch die vielen Anhänge schleifen
            for ($k = 0; $k < sizeof($anhangname); $k++) {
                $mail->addAttachment($attachment_folder . '/' . $verteiler_name . '-' . $nummer . '-' . $anhangname[$k], $anhangname[$k]);
            }

            // Senden
            if (!$mail->Send()) {
                echo "<br>=================Mailer Error: " . $mail->ErrorInfo . "=================<br><br><br><br>";
            } else {
                $message->setFlag('\\Seen \\Flagged'); //Mail als gelesen und wichtig markieren
                echo "<br>=================Message has been sent=================<br><br><br><br>";
            }
        } //if phpmailer == 1
        //\\\\\\_________________________ PHPMailer _______________________///////////////////
        } // $message->isSeen
    } // foreach messages
} // for each verteiler
