<?php

// github.com/ddeboer/imap
use Ddeboer\Imap\Server;
require_once('IMAP-library/autoload.php');

// github.com/PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$verteiler_name  = 'alle';
$email_addr      = 'xxx@xxx.de';
$host            = 'xxx.yyy.com';
$port            =  465;
$postfach		     = 'INBOX';
$SMTPSecure      = 'ssl';
$SMTPAuth        =  true;
$IsHTML          =  true;
$user            = 'xxx';
$pass            = 'xxx';
$folder          = 'Anhaenge';
$verteiler_array = array('xxx@xxx.de','yyy@yyy.de'); 
$signatur 		 = '<br> <a href=\'https://xxx.de/verteiler/\'>Link</a><br><br>';

$phpmailer       = 1;
$ausgabe         = 1;
$SMTPDebug       = 0;



////////¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ IMAP Class ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯\\\\\\\\\\\\\\\\\\\\ 
$server          = new Server($host);
$connection      = $server->authenticate($user, $pass);
$mailboxes       = $connection->getMailboxes();
$mailbox         = $connection->getMailbox($postfach);
$messages        = $mailbox->getMessages();

foreach ($messages as $message)
  {
    if ($message->isSeen() != 1)
      {
        $nummer       = $message->getNumber();
        $gelesen      = $message->isSeen();
        $header_array = Array( $message->getHeaders() );
        $betreff      = '[' . $verteiler_name . '] ';
        $betreff     .= $message->getSubject();
        $messagetext  = $message->getBodyHtml();
        $messagetext .= $signatur;
		
        $anhangname  = array();
        $j           = 0; // für die Übergabe an den Mailer werden die Namen der Anhänge benötigt 
        $attachments = $message->getAttachments();
        foreach ($attachments as $attachment)
          {
            file_put_contents($folder . '/' . $verteiler_name . '-' . $nummer . '-' . $attachment->getFilename(), $attachment->getDecodedContent());
            $anhangname[$j] = $attachment->getFilename();
            $j++;
          }
//\\\\\\_________________________ IMAP Class _______________________/////////////////// 
		  
		  
		  
		  
////////¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ Ausgabe ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯\\\\\\\\\\\\\\\\\\\\            
        if ($ausgabe == 1)
          {
            //print_r($header_array);echo "<br>";
            echo "<b>Nummer: </b>" . $nummer . "<br>";
            echo "<b>Betreff: </b>" . $betreff . "<br>";
            echo "<b>gelesen: </b>" . $gelesen . "<br>";
            echo "<b>Text: </b>" . $messagetext;
            // Durch die vielen Anhänge schleifen
            for ($k = 0; $k < sizeof($anhangname); $k++)
              {
                echo "<b>Anhang: </b>" . $anhangname[$k] . "<br>";
              }
            echo "<br>";
          } 
//\\\\\\_________________________ Ausgabe _______________________///////////////////
		  
		  
        
		
////////¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯ PHPMailer ¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯¯\\\\\\\\\\\\\\\\\\\\
        if ($phpmailer == 1) // nur zum debuggen ausschaltbar
          {
            $mail = new PHPMailer\PHPMailer\PHPMailer();
            $mail->IsSMTP(); // enable SMTP
            $mail->SMTPDebug  = $SMTPDebug; // debugging: 1 = errors and messages, 2 = messages only
            $mail->SMTPAuth   = $SMTPAuth; // authentication enabled
            $mail->SMTPSecure = $SMTPSecure; // secure transfer enabled REQUIRED for Gmail
            $mail->Host       = $host;
            $mail->Port       = $port; // or 587 
            $mail->Username   = $user;
            $mail->Password   = $pass;
            $mail->Subject    = $betreff;
            $mail->Body       = $messagetext;
            $mail->SetFrom($email_addr);
            $mail->IsHTML($IsHTML); 
			
            // Durch die Verteilerliste schleifen
            foreach ($verteiler_array as $verteiler_adrr)
              {
                $mail->AddAddress($verteiler_adrr);
              }
			  	
            // Durch die vielen Anhänge schleifen
            for ($k = 0; $k < sizeof($anhangname); $k++)
              {
                $mail->addAttachment($folder . '/' . $verteiler_name . '-' . $nummer . '-' . $anhangname[$k], $anhangname[$k]);
              }
			  
            // Senden 
            if (!$mail->Send())
              {
                echo "<br>=================Mailer Error: " . $mail->ErrorInfo . "=================<br><br><br><br>";
              }
            else
              {
				$message->setFlag('\\Seen \\Flagged'); //Mail als gelesen und wichtig markieren
                echo "<br>=================Message has been sent=================<br><br><br><br>";
              }
			  
          } //if phpmailer == 1
//\\\\\\_________________________ PHPMailer _______________________///////////////////
		
		
      } // $message->isSeen
  } // foreach messages
