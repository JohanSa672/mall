<?php 
/**
 * This is a Nereus pagecontroller.
 *
 */
// Include the essential config-file which also creates the $anax variable with its defaults.
include(__DIR__.'/config.php'); 


// Connect to a MySQL database using PHP PDO
$db = new CDatabase($nereus['database']);


// Check if user is authenticated.
$acronym = isset($_SESSION['user']) ? $_SESSION['user']->acronym : null;

if($acronym) {
  $output = "Du är inloggad som: $acronym ({$_SESSION['user']->name})";
}
else {
  $output = "Du är INTE inloggad.";
}


// Check if user and password is okey
if(isset($_POST['login'])) {
  $sql = "SELECT acronym, name FROM User WHERE acronym = ? AND password = md5(concat(?, salt))";
  $res = $db->ExecuteSelectQueryAndFetchAll($sql, array($_POST['acronym'], $_POST['password']));
  if(isset($res[0])) {
    $_SESSION['user'] = $res[0];
  }
  header('Location: login.php');
}



// Do it and store it all in variables in the Anax container.
$nereus['title'] = "Login";

$nereus['main'] = <<<EOD
<h1>{$nereus['title']}</h1>

<form method=post>
  <fieldset>
  <legend>Login</legend>
  <p><em>Du kan logga in med doe:doe eller admin:admin.</em></p>
  <p><label>Användare:<br/><input type='text' name='acronym' value=''/></label></p>
  <p><label>Lösenord:<br/><input type='password' name='password' value=''/></label></p>
  <p><input type='submit' name='login' value='Login'/></p>
  <p><a href='logout.php'>Logout</a></p>
  <output><b>{$output}</b></output>
  </fieldset>
</form>

EOD;



// Finally, leave it all to the rendering phase of Anax.
include(NEREUS_THEME_PATH);