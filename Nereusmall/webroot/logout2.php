<?php 
/**
 * This is a Nereus pagecontroller.
 *
 */
// Include the essential config-file which also creates the $anax variable with its defaults.
include(__DIR__.'/config.php'); 



// Get incoming parameters
$acronym = isset($_SESSION['user']) ? $_SESSION['user']->acronym : null;

if($acronym) {
  $output = "Du är inloggad som: $acronym ({$_SESSION['user']->name})";
}
else {
  $output = "Du är INTE inloggad.";
}


// Logout the user
if(isset($_POST['logout'])) {
  unset($_SESSION['user']);
  header('Location: logout.php');
}



// Do it and store it all in variables in the Anax container.
$nereus['title'] = "Logout";

$nereus['main'] = <<<EOD
<h1>{$nereus['title']}</h1>

<form method=post>
  <fieldset>
  <legend>Login</legend>
  <p><input type='submit' name='logout' value='Logout'/></p>
  <p><a href='login.php'>Login</a></p>
  <output><b>{$output}</b></output>
  </fieldset>
</form>

EOD;



// Finally, leave it all to the rendering phase of Anax.
include(NEREUS_THEME_PATH);