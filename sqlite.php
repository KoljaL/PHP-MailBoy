 <?php
// https://www.php-einfach.de/php-tutorial/_get-und-_post/


// Datenbank anlegen 
if ($_GET['get'] == "anlegen")
  {
    
    $datenbank = "verteiler.sqt";
    
    if (!file_exists($datenbank))
      {
        $db = new PDO('sqlite:' . $datenbank);
        $db->exec("CREATE TABLE verteiler(id INTEGER PRIMARY KEY, verteiler_name TEXT, email_addr TEXT, verteiler_array TEXT, signatur TEXT, host TEXT, port TEXT, user TEXT, pass TEXT, postfach TEXT, SMTPSecure TEXT, SMTPAuth TEXT, IsHTML TEXT, folder TEXT, datum DATE)");
        chmod($datenbank, 0777);
      } //!file_exists($datenbank)
    else
      {
        // Verbindung
        $db = new PDO('sqlite:' . $datenbank);
      }
    
    // Schreibrechte überprüfen
    if (!is_writable($datenbank))
      {
        chmod($datenbank, 0777);
      } //!is_writable($datenbank)
    
  } // get == anlegen


// Neu
if ($_GET['get'] == "neu")
  {
?>   
    <!DOCTYPE html>
    <html lang="de">
    <head>
    <meta charset="UTF-8">
    <title>Verteiler eintragen</title>
    <style>
        body {
        font-family: Verdana, Arial, Sans-Serif;
        background: Whitesmoke;
        }
        a:link, a:visited {
        color: Royalblue;
		}
        text-decoration: None;
    </style>
    </head>
    <body>
    <h3>Verteiler eintragen</h3>
    <p><a href="sqlite?get=bearbeiten">Verteiler anzeigen/bearbeiten</a></p>

	<?php
		if ($_SERVER["REQUEST_METHOD"] == "POST")
		  {
			//    include "sqlite.php?get=anlegen";
			$datenbank = "verteiler.sqt";
			$db        = new PDO('sqlite:' . $datenbank);
			
			$insert = $db->prepare("INSERT INTO verteiler (`verteiler_name`,`email_addr`,`verteiler_array`,`signatur`,`host`,`port`,`user`,`pass`,`postfach`,`SMTPSecure`,`SMTPAuth`,`IsHTML`,`folder`,`datum`) VALUES (:verteiler_name, :email_addr, :verteiler_array, :signatur, :host, :port, :user, :pass, :postfach, :SMTPSecure, :SMTPAuth, :IsHTML, :folder, :datum)");
			//    if (!$insert) {
			//    echo "\nPDO::errorInfo():\n";
			//    print_r($db->errorInfo());}
			$insert->bindValue(':verteiler_name', $_POST["verteiler_name"]);
			$insert->bindValue(':email_addr', $_POST["email_addr"]);
			$insert->bindValue(':verteiler_array', $_POST["verteiler_array"]);
			$insert->bindValue(':signatur', $_POST["signatur"]);
			$insert->bindValue(':host', $_POST["host"]);
			$insert->bindValue(':port', $_POST["port"]);
			$insert->bindValue(':user', $_POST["user"]);
			$insert->bindValue(':pass', $_POST["pass"]);
			$insert->bindValue(':postfach', $_POST["postfach"]);
			$insert->bindValue(':SMTPSecure', $_POST["SMTPSecure"]);
			$insert->bindValue(':SMTPAuth', $_POST["SMTPAuth"]);
			$insert->bindValue(':IsHTML', $_POST["IsHTML"]);
			$insert->bindValue(':folder', $_POST["folder"]);
			$insert->bindValue(':datum', date("Y-m-d H:i:s"));
			if ($insert->execute())
			  {
				echo '<p>Der Verteiler wurde eingetragen.</p>';
			  } //$insert->execute()
			else
			  {
				print_r($insert->errorInfo());
			  }
		  } //$_SERVER["REQUEST_METHOD"] == "POST"
		  
			// Formular für neuen Verteiler
	?>			
			<form action="sqlite.php?get=neu" method="post">
			<p><label>verteiler_name:   <input type="text" name="verteiler_name"     	size="35" required="required"></label></p>
			<p><label>email_addr:       <input type="text" name="email_addr"         	size="35" required="required"></label></p>
			<p><label>verteiler_array:  <input type="text" name="verteiler_array"     	size="35" required="required"></label></p>
			<p><label>signatur:         <input type="text" name="signatur"             size="35" required="required"></label></p>
			<p><label>host:             <input type="text" name="host"                 size="35" required="required"></label></p>
			<p><label>port:             <input type="text" name="port"                 size="35" required="required"></label></p>
			<p><label>user:             <input type="text" name="user"                 size="35" required="required"></label></p>
			<p><label>pass:             <input type="text" name="pass"                 size="35" required="required"></label></p>
			<p><label>postfach:         <input type="text" name="postfach"             size="35" required="required"></label></p>
			<p><label>SMTPSecure:       <input type="text" name="SMTPSecure"         	size="35" required="required"></label></p>
			<p><label>SMTPAuth:         <input type="text" name="SMTPAuth"             size="35" required="required"></label></p>
			<p><label>IsHTML:           <input type="text" name="IsHTML"             	size="35" required="required"></label></p>
			<p><label>folder:           <input type="text" name="folder"             	size="35" required="required"></label></p>
									 <input type="submit" value="Absenden">
			</form>
			</body>
			</html> 
	<?php
  } // get == neu


// BEARBEIETN
if ($_GET['get'] == "bearbeiten")
	{
	?>
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
		<h3>Verteiler</h3>
		<p><a href="sqlite.php?get=neu">Verteiler eintragen</a></p>
	<?php
    
    // Bearbeiten 
    $datenbank = "verteiler.sqt";
    $db        = new PDO('sqlite:' . $datenbank);   
    if (isset($_GET["id"]))
      {      
        $select = $db->prepare("SELECT `id`,`verteiler_name`,`email_addr`,`verteiler_array`,`signatur`,`host`,`port`,`user`,`pass`,`postfach`,`SMTPSecure`,`SMTPAuth`,`IsHTML`,`folder`,`datum` FROM `verteiler` WHERE `id` = :id");
        $select->bindValue(':id', $_GET["id"]);
        $select->execute();
        $nachricht = $select->fetch();
        if ($nachricht["id"] == $_GET["id"])
          {
            // Formular zum bearbeiten der Verteiler 
            echo '<form action="sqlite?get=bearbeiten" method="post">  
			<p><label>verteiler_name:     	<input type="text" name="verteiler_name"     	value="' . $nachricht["verteiler_name"] . '"     	size="35" required="required"></label></p>
			<p><label>email_addr:         	<input type="text" name="email_addr"         	value="' . $nachricht["email_addr"] . '"         	size="35" required="required"></label></p>
			<p><label>verteiler_array:  	<input type="text" name="verteiler_array"     	value="' . $nachricht["verteiler_array"] . '"     	size="35" required="required"></label></p>
			<p><label>signatur:         	<input type="text" name="signatur"             	value="' . $nachricht["signatur"] . '"             	size="35" required="required"></label></p>
			<p><label>host:             	<input type="text" name="host"                 	value="' . $nachricht["host"] . '"                 	size="35" required="required"></label></p>
			<p><label>port:             	<input type="text" name="port"                 	value="' . $nachricht["port"] . '"                 	size="35" required="required"></label></p>
			<p><label>user:            		<input type="text" name="user"                 	value="' . $nachricht["user"] . '"                 	size="35" required="required"></label></p>
			<p><label>pass:             	<input type="text" name="pass"                 	value="' . $nachricht["pass"] . '"                 	size="35" required="required"></label></p>
			<p><label>postfach:         	<input type="text" name="postfach"             	value="' . $nachricht["postfach"] . '"             	size="35" required="required"></label></p>
			<p><label>SMTPSecure:         	<input type="text" name="SMTPSecure"         	value="' . $nachricht["SMTPSecure"] . '"         	size="35" required="required"></label></p>
			<p><label>SMTPAuth:         	<input type="text" name="SMTPAuth"             	value="' . $nachricht["SMTPAuth"] . '"             	size="35" required="required"></label></p>
			<p><label>IsHTML:             	<input type="text" name="IsHTML"             	value="' . $nachricht["IsHTML"] . '"             	size="35" required="required"></label></p>
			<p><label>folder:             	<input type="text" name="folder"             	value="' . $nachricht["folder"] . '"             	size="35" required="required"></label></p>
			<p><label><input type="radio" name="option" value="edit" checked="checked"> Ändern</label>
			<label><input type="radio" name="option" value="delete" required="required"> Löschen</label>
			<input type="hidden" name="id" value="' . $nachricht["id"] . '"></p>
			<input type="submit" value="Absenden">
			</form>';            
          } //$nachricht["id"] == $_GET["id"]
      } //isset($_GET["id"])
    
	// Editieren
    if ("POST" == $_SERVER["REQUEST_METHOD"])
      {   
        if ($_POST["option"] == 'edit')
          {     
            $update = $db->prepare("UPDATE `verteiler` SET `verteiler_name` = :verteiler_name,`email_addr` = :email_addr, `verteiler_array` = :verteiler_array, `signatur` = :signatur, `host` = :host, `port` = :port, `user` = :user, `pass` = :pass, `postfach` = :postfach, `SMTPSecure` = :SMTPSecure, `SMTPAuth` = :SMTPAuth, `IsHTML` = :IsHTML,  `folder` = :folder WHERE `id` = :id");
            $update->bindValue(':verteiler_name', $_POST["verteiler_name"]);
            $update->bindValue(':email_addr', $_POST["email_addr"]);
            $update->bindValue(':verteiler_array', $_POST["verteiler_array"]);
            $update->bindValue(':signatur', $_POST["signatur"]);
            $update->bindValue(':host', $_POST["host"]);
            $update->bindValue(':port', $_POST["port"]);
            $update->bindValue(':user', $_POST["user"]);
            $update->bindValue(':pass', $_POST["pass"]);
            $update->bindValue(':postfach', $_POST["postfach"]);
            $update->bindValue(':SMTPSecure', $_POST["SMTPSecure"]);
            $update->bindValue(':SMTPAuth', $_POST["SMTPAuth"]);
            $update->bindValue(':IsHTML', $_POST["IsHTML"]);
            $update->bindValue(':folder', $_POST["folder"]);
            $update->bindValue(':id', $_POST["id"]);
            if ($update->execute())
              {
                echo '<p>Der Verteier wurde bearbeitet.</p>';
              } //$update->execute()
            else
              {
                print_r($update->errorInfo());
              }
          } //$_POST["option"] == 'edit'
        
		// Löschen
        if ($_POST["option"] == 'delete')
          {           
            $delete = $db->prepare("DELETE FROM `verteiler` WHERE `id` = :id");
            $delete->bindValue(':id', $_POST["id"]);
            if ($delete->execute())
              {
                echo '<p>Der Verteiler wurde gelöscht</p>';
              } //$delete->execute()
            else
              {
                print_r($delete->errorInfo());
              }
          } //$_POST["option"] == 'delete'
      } //"POST" == $_SERVER["REQUEST_METHOD"]
    
    
    $select = $db->query("SELECT `id`,`verteiler_name`,`email_addr`,`verteiler_array`,`signatur`,`host`,`port`,`user`,`pass`,`postfach`,`SMTPSecure`,`SMTPAuth`,`IsHTML`,`folder`,`datum` FROM `verteiler` ORDER BY `datum` DESC");
    $nachrichten = $select->fetchAll(PDO::FETCH_ASSOC);
    echo '<h2>' . count($nachrichten) . ' Verteiler</h2>';   
    foreach ($nachrichten as $nachricht)
      {
        extract($nachricht);
        sscanf($datum, "%4s-%2s-%2s", $jahr, $monat, $tag);  
        echo '<p><b>' . $verteiler_name . '</b> <em>' . $email_addr . ' </em> ' . nl2br($verteiler_array) . ' <small> ' . $tag . '.' . $monat . '.' . $jahr . '</small><br>
		<a href="sqlite.php?get=bearbeiten&id=' . $id . '"><small>Verteiler bearbeiten</small></a></p>';
      } //$nachrichten as $nachricht
?>
</body>
</html>      
<?php
  } //$_GET['get'] == "bearbeiten"
?> 
