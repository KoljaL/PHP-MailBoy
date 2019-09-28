<?php
// https://www.php-kurs.com/loesung-einlogg-script.htm
// https://blog.kulturbanause.de/2016/08/php-session-mit-timeout-funktion/
session_start();

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
		echo "<br><b>ausloggen erfolgreich</b>";
}// Logout


// Login
if (isset($_POST['benutzername']) and $_POST['benutzername'] != "" and isset($_POST['kennwort']) and $_POST['kennwort'] != "") {
    $username = $_POST['benutzername'];
    $db = new PDO('sqlite:verteiler.sqt');
    $select = $db->query("SELECT pass FROM verteiler WHERE verteiler_name = '$username' ");
    $nachrichten = $select->fetchAll(PDO::FETCH_ASSOC);
    // Ergeblisse, hoffentlich nur eins, durlaufen und Passwort finden
    foreach ($nachrichten as $nachricht) {
        extract($nachricht);
        $passwort = $pass;
    }
    // einloggen
    if ($_POST['kennwort'] == $passwort) {
        $_SESSION['benutzername'] = $_POST['benutzername'];
        $_SESSION['eingeloggt'] = true;
        echo "<br><b>einloggen erfolgreich</b>";
    }
    // ablehnen
    else {
        echo "<b> ungueltige Eingabe</b>";
        $_SESSION['eingeloggt'] = false;
        echo(time() - $_SESSION['last_visit']);
    }
} // Login


// Benutzer begruessen
if (isset($_SESSION['eingeloggt']) and $_SESSION['eingeloggt'] == true) {
    echo "<h1>Hallo ". $_SESSION['benutzername'] . "</h1>";
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
    exit; // Programm wird hier beendet, denn Benutzer ist noch nicht eingeloggt
}


// Hier kommt keiner ohne Session hin ;-)
echo "<br>";
echo "<a href='passwort?get=logout'>logout</a>";
?>
