<?php session_start(); ?>
<!DOCTYPE html>
<html lang="de">
 <head>
  <meta charset="UTF-8">
  <title>Verteiler</title>
  <style>
  body {
   font-family: Verdana, Arial, Sans-Serif;
   background: Whitesmoke;
  }
  a:link, a:visited {
   color: Royalblue;
   text-decoration: None;
  }
  </style>
 </head>
<body>

<?php
// https://werner-zenk.de/scripte/sqlite_datenbank.php
$datenbank = "verteiler.sqt";

// damit das erstellen einer Datenbank ohne Passwort möglich ist
if (file_exists($datenbank)) {
    passwort();
}


// Datenbank anlegen
if (!file_exists($datenbank)) {
    if (!file_exists($datenbank)) {
        $db = new PDO('sqlite:' . $datenbank);
        $db->exec("CREATE TABLE verteiler(id INTEGER PRIMARY KEY, verteiler_name TEXT, verteiler_passwort TEXT, verteiler_beschreibung TEXT, verteiler_email TEXT, verteiler_array TEXT, verteiler_signatur TEXT, server_host TEXT, server_port TEXT, server_user TEXT, server_passwort TEXT, server_postfach TEXT, SMTPSecure TEXT, SMTPAuth TEXT, IsHTML TEXT, attachment_folder TEXT, datum DATE)");
        chmod($datenbank, 0777);
        $_SESSION['eingeloggt'] = true;
        $_GET['get'] = "neu";
    } //!file_exists($datenbank)
    else {
        // Verbindung
        $db = new PDO('sqlite:' . $datenbank);
    }

    // Schreibrechte überprüfen
    if (!is_writable($datenbank)) {
        chmod($datenbank, 0777);
    } //!is_writable($datenbank)
} // get == anlegen



// Neuen Verteiler anlegen
if ($_GET['get'] == "neu") {
    $_SESSION['eingeloggt'] = true;
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $db = new PDO('sqlite:' . $datenbank);
        $insert = $db->prepare("INSERT INTO verteiler (`verteiler_name`,`verteiler_passwort`,`verteiler_beschreibung`,`verteiler_email`,`verteiler_array`,`verteiler_signatur`,`server_host`,`server_port`,`server_user`,`server_passwort`,`server_postfach`,`SMTPSecure`,`SMTPAuth`,`IsHTML`,`attachment_folder`,`datum`) VALUES (:verteiler_name, :verteiler_passwort, :verteiler_beschreibung, :verteiler_email, :verteiler_array, :verteiler_signatur, :server_host, :server_port, :server_user, :server_passwort, :server_postfach, :SMTPSecure, :SMTPAuth, :IsHTML, :attachment_folder, :datum)");
        $insert->bindValue(':verteiler_name', $_POST["verteiler_name"]);
        $insert->bindValue(':verteiler_passwort', $_POST["verteiler_passwort"]);
        $insert->bindValue(':verteiler_beschreibung', $_POST["verteiler_beschreibung"]);
        $insert->bindValue(':verteiler_email', $_POST["verteiler_email"]);
        $insert->bindValue(':verteiler_array', $_POST["verteiler_array"]);
        $insert->bindValue(':verteiler_signatur', $_POST["verteiler_signatur"]);
        $insert->bindValue(':server_host', $_POST["server_host"]);
        $insert->bindValue(':server_port', $_POST["server_port"]);
        $insert->bindValue(':server_user', $_POST["server_user"]);
        $insert->bindValue(':server_passwort', $_POST["server_passwort"]);
        $insert->bindValue(':server_postfach', $_POST["server_postfach"]);
        $insert->bindValue(':SMTPSecure', $_POST["SMTPSecure"]);
        $insert->bindValue(':SMTPAuth', $_POST["SMTPAuth"]);
        $insert->bindValue(':IsHTML', $_POST["IsHTML"]);
        $insert->bindValue(':attachment_folder', $_POST["attachment_folder"]);
        $insert->bindValue(':datum', date("Y-m-d H:i:s"));
        if ($insert->execute()) {
            echo '<p>Der Verteiler wurde eingetragen.</p>';
        } //$insert->execute()
        else {
            print_r($insert->errorInfo());
        }
    } //$_SERVER["REQUEST_METHOD"] == "POST"

            // Formular für neuen Verteiler
    ?>
    <h3>Verteiler</h3>
    <p><a href="sqlite.php?get=neu">neuer Verteiler</a></p>
			<form action="sqlite.php?get=neu" method="post">
      <p><label>verteiler_name: <input type="text" name="verteiler_name" size="35" required="required"></label></p>
      <p><label>verteiler_passwort: <input type="text" name="verteiler_passwort" size="35" required="required"></label></p>
      <p><label>verteiler_beschreibung: <input type="text" name="verteiler_beschreibung" size="35" required="required"></label></p>
			<p><label>verteiler_email: <input type="text" name="verteiler_email" size="35" required="required"></label></p>
			<p><label>verteiler_array: <input type="text" name="verteiler_array" size="35" required="required"></label></p>
			<p><label>verteiler_signatur: <input type="text" name="verteiler_signatur" size="35" required="required"></label></p>
			<p><label>server_host: <input type="text" name="server_host" size="35" required="required"></label></p>
			<p><label>server_port: <input type="text" name="server_port" size="35" required="required"></label></p>
			<p><label>server_user: <input type="text" name="server_user" size="35" required="required"></label></p>
			<p><label>server_passwort: <input type="text" name="server_passwort" size="35" required="required"></label></p>
			<p><label>server_postfach: <input type="text" name="server_postfach" size="35" required="required"></label></p>
			<p><label>SMTPSecure: <input type="text" name="SMTPSecure" size="35" required="required"></label></p>
			<p><label>SMTPAuth: <input type="text" name="SMTPAuth" size="35" required="required"></label></p>
			<p><label>IsHTML: <input type="text" name="IsHTML" size="35" required="required"></label></p>
			<p><label>attachment_folder: <input type="text" name="attachment_folder" size="35" required="required"></label></p>
			<input type="submit" value="Absenden">
			</form>
			</body>
			</html>
	<?php
} // get == neu


// BEARBEIETN
if ($_GET['get'] == "bearbeiten") {
    $db = new PDO('sqlite:' . $datenbank);
    if (isset($_GET["id"])) {
        $select = $db->prepare("SELECT `id`,`verteiler_name`,`verteiler_passwort`,`verteiler_beschreibung`,`verteiler_email`,`verteiler_array`,`verteiler_signatur`,`server_host`,`server_port`,`server_user`,`server_passwort`,`server_postfach`,`SMTPSecure`,`SMTPAuth`,`IsHTML`,`attachment_folder`,`datum` FROM `verteiler` WHERE `id` = :id");
        $select->bindValue(':id', $_GET["id"]);
        $select->execute();
        $nachricht = $select->fetch();
        if ($nachricht["id"] == $_GET["id"]) {
            // Formular zum bearbeiten der Verteiler
            echo '<h3>Verteiler</h3>
      <p><a href="sqlite.php?get=neu">neuer Verteiler</a></p>
      <form action="sqlite.php?get=bearbeiten" method="post">
      <p><label>verteiler_name: <input type="text" name="verteiler_name" value="' . $nachricht["verteiler_name"] . '" size="35" required="required"></label></p>
      <p><label>verteiler_passwort: <input type="text" name="verteiler_passwort" value="' . $nachricht["verteiler_passwort"] . '" size="35" required="required"></label></p>
      <p><label>verteiler_beschreibung: <input type="text" name="verteiler_beschreibung" value="' . $nachricht["verteiler_beschreibung"] . '" size="35" required="required"></label></p>
			<p><label>verteiler_email: <input type="text" name="verteiler_email" value="' . $nachricht["verteiler_email"] . '" size="35" required="required"></label></p>
			<p><label>verteiler_array: <input type="text" name="verteiler_array" value="' . $nachricht["verteiler_array"] . '" size="35" required="required"></label></p>
			<p><label>verteiler_signatur: <input type="text" name="verteiler_signatur" value="' . htmlentities($nachricht["verteiler_signatur"]) . '" size="35" required="required"></label></p>
			<p><label>server_host: <input type="text" name="server_host" value="' . $nachricht["server_host"] . '" size="35" required="required"></label></p>
			<p><label>server_port: <input type="text" name="server_port" value="' . $nachricht["server_port"] . '" size="35" required="required"></label></p>
			<p><label>server_user: <input type="text" name="server_user" value="' . $nachricht["server_user"] . '" size="35" required="required"></label></p>
			<p><label>server_passwort: <input type="text" name="server_passwort" value="' . $nachricht["server_passwort"] . '" size="35" required="required"></label></p>
			<p><label>server_postfach: <input type="text" name="server_postfach" value="' . $nachricht["server_postfach"] . '" size="35" required="required"></label></p>
			<p><label>SMTPSecure: <input type="text" name="SMTPSecure" value="' . $nachricht["SMTPSecure"] . '" size="35" required="required"></label></p>
			<p><label>SMTPAuth: <input type="text" name="SMTPAuth" value="' . $nachricht["SMTPAuth"] . '" size="35" required="required"></label></p>
			<p><label>IsHTML: <input type="text" name="IsHTML" value="' . $nachricht["IsHTML"] . '" size="35" required="required"></label></p>
			<p><label>attachment_folder: <input type="text" name="attachment_folder" value="' . $nachricht["attachment_folder"] . '" size="35" required="required"></label></p>
			<p><label><input type="radio" name="option" value="edit" checked="checked"> Ändern</label>
			<label><input type="radio" name="option" value="delete" required="required"> Löschen</label>
			<input type="hidden" name="id" value="' . $nachricht["id"] . '"></p>
			<input type="submit" value="Absenden">
			</form>';
        } //$nachricht["id"] == $_GET["id"]
    } //isset($_GET["id"])

    // Editieren
    if ("POST" == $_SERVER["REQUEST_METHOD"]) {
        if ($_POST["option"] == 'edit') {
            $update = $db->prepare("UPDATE `verteiler` SET `verteiler_name` = :verteiler_name,`verteiler_passwort` = :verteiler_passwort,`verteiler_beschreibung` = :verteiler_beschreibung,
              `verteiler_email` = :verteiler_email, `verteiler_array` = :verteiler_array, `verteiler_signatur` = :verteiler_signatur, `server_host` = :server_host, `server_port` = :server_port,
              `server_user` = :server_user, `server_passwort` = :server_passwort, `server_postfach` = :server_postfach, `SMTPSecure` = :SMTPSecure, `SMTPAuth` = :SMTPAuth, `IsHTML` = :IsHTML,
              `attachment_folder` = :attachment_folder WHERE `id` = :id");
            $update->bindValue(':verteiler_name', $_POST["verteiler_name"]);
            $update->bindValue(':verteiler_passwort', $_POST["verteiler_passwort"]);
            $update->bindValue(':verteiler_beschreibung', $_POST["verteiler_beschreibung"]);
            $update->bindValue(':verteiler_email', $_POST["verteiler_email"]);
            $update->bindValue(':verteiler_array', $_POST["verteiler_array"]);
            $update->bindValue(':verteiler_signatur', $_POST["verteiler_signatur"]);
            $update->bindValue(':server_host', $_POST["server_host"]);
            $update->bindValue(':server_port', $_POST["server_port"]);
            $update->bindValue(':server_user', $_POST["server_user"]);
            $update->bindValue(':server_passwort', $_POST["server_passwort"]);
            $update->bindValue(':server_postfach', $_POST["server_postfach"]);
            $update->bindValue(':SMTPSecure', $_POST["SMTPSecure"]);
            $update->bindValue(':SMTPAuth', $_POST["SMTPAuth"]);
            $update->bindValue(':IsHTML', $_POST["IsHTML"]);
            $update->bindValue(':attachment_folder', $_POST["attachment_folder"]);
            $update->bindValue(':id', $_POST["id"]);
            if ($update->execute()) {
                echo '<p>Der Verteier wurde bearbeitet.</p>';
            } //$update->execute()
            else {
                print_r($update->errorInfo());
            }
        } //$_POST["option"] == 'edit'

        // Löschen
        if ($_POST["option"] == 'delete') {
            $delete = $db->prepare("DELETE FROM `verteiler` WHERE `id` = :id");
            $delete->bindValue(':id', $_POST["id"]);
            if ($delete->execute()) {
                echo '<p>Der Verteiler wurde gelöscht</p>';
            } //$delete->execute()
            else {
                print_r($delete->errorInfo());
            }
        } //$_POST["option"] == 'delete'
    } //"POST" == $_SERVER["REQUEST_METHOD"]
} //$_GET['get'] == "bearbeiten"

if (file_exists($datenbank)) {
    $db = new PDO('sqlite:' . $datenbank);
    $select = $db->query("SELECT `id`,`verteiler_name`,`verteiler_passwort`,`verteiler_beschreibung`,`verteiler_email`,`verteiler_array`,`verteiler_signatur`,`server_host`,`server_port`,`server_user`,`server_passwort`,`server_postfach`,`SMTPSecure`,`SMTPAuth`,`IsHTML`,`attachment_folder`,`datum` FROM `verteiler` ORDER BY `datum` DESC");
    $nachrichten = $select->fetchAll(PDO::FETCH_ASSOC);
    echo '<h2>' . count($nachrichten) . ' Verteiler</h2>';
    echo '<p><a href="sqlite.php?get=neu">neuer Verteiler</a></p>';
    foreach ($nachrichten as $nachricht) {
        extract($nachricht);
        sscanf($datum, "%4s-%2s-%2s", $jahr, $monat, $tag);
        echo '<p><b>' . $verteiler_name . '</b> <em>' . $verteiler_email . ' </em> ' . nl2br($verteiler_array) . ' <small> ' . $tag . '.' . $monat . '.' . $jahr . '</small><br>
<a href="sqlite.php?get=bearbeiten&id=' . $id . '"><small>Verteiler bearbeiten</small></a></p>';
    } //$nachrichten as $nachricht
}
?>

</body>
</html>


<?php
// https://www.php-kurs.com/loesung-einlogg-script.htm
function passwort()
{
    // Timeout für die Session
    $session_timeout = 180; // 180 Sek = 30 Minuten
    if (!isset($_SESSION['last_visit'])) {
        $_SESSION['last_visit'] = time();
    }
    if ((time() - $_SESSION['last_visit']) > $session_timeout) {
        session_destroy();
    }
    $_SESSION['last_visit'] = time();


    // Logout
    if (isset($_GET['get']) and $_GET['get'] == "logout") {
        session_destroy();
        echo "<br><br><br><br><center><b>ausloggen erfolgreich</b></center>";

        exit;
    }// Logout


    // Login
    if (isset($_POST['benutzername']) and $_POST['benutzername'] != "" and isset($_POST['kennwort']) and $_POST['kennwort'] != "") {
        $username = $_POST['benutzername'];
        global $datenbank;
        $db = new PDO('sqlite:' . $datenbank);
        $select = $db->query("SELECT verteiler_passwort FROM verteiler WHERE verteiler_name = '$username' ");
        //print_r($db->query());
        $nachrichten = $select->fetchAll(PDO::FETCH_ASSOC);
        // Ergeblisse, hoffentlich nur eins, durlaufen und Passwort finden
        foreach ($nachrichten as $nachricht) {
            extract($nachricht);
            $passwort = $verteiler_passwort;
        }
        // einloggen
        if ($_POST['kennwort'] == $passwort) {
            $_SESSION['benutzername'] = $_POST['benutzername'];
            $_SESSION['eingeloggt'] = true;
        //  echo "<br><b>einloggen erfolgreich</b>";
        }
        // ablehnen
        else {
            echo "<b> ungueltige Eingabe</b>";
            $_SESSION['eingeloggt'] = false;
            echo($passwort);
        }
    } // Login


    // Benutzer begruessen
    if (isset($_SESSION['eingeloggt']) and $_SESSION['eingeloggt'] == true) {
//    echo "<h1>Hallo ". $_SESSION['benutzername'] . "</h1>";
    }

    // Einloggformular anzeigen
    else {
        echo "<h1>Bitte loggen Sie sich ein";
        $url = $_SERVER['SCRIPT_NAME'];
        echo '<form action="'. $url .'" method="POST">';
        echo '<p>Benutzername:<br>';
        echo '<input type="text" name="benutzername" value="">';
        echo '<p>Kennwort:<br>';
        echo '<input type="password" name="kennwort" value="">';
        echo '<p><input type="Submit" value="einloggen">';
        echo '</form>';
        exit; // Programm wird hier beendet
    }


    // Hier kommt keiner ohne Session hin ;-)
    echo "<br>";
    echo "<a href='sqlite.php?get=logout'>logout</a>";
}
?>
