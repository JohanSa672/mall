<?php
//Includ the essential config-file which also creats the $asterios variable with its defaults.
include(__DIR__.'/config.php');

	//Connect to a MySQL database using PHP PDO
	$db=new CDatabase($nereus['database']);
	
	//Create an object
	$UserA= new CUser();

	// Check if user is authenticated.
	$output=$UserA->userAuthenticated();

	// Logout the user
	//$UserA->logOut($_POST['logout']);

//var_dump($res);



//Do it and store it all in variables in the Nereus container.
$nereus['title']="Logout";

$nereus['header']=<<<EOD
<img class='sitelogo' src='img/nereus.png' alt='Nereus Logo'/>
<span class='sitetitle'>Nereus webbtemplate</span>
<span class='siteslogan'>Återanvändvara moduler för webbutvecklng med PHP.</span>
<link rel='shortcut icon' href='favicon.ico'/>
EOD;

$nereus['main']=<<<EOD
<h1>{$nereus['title']}</h1>
<form method=post>
  <fieldset>
  <output><b>{$output}</b></output>
  </fieldset>
</form>

EOD;

$nereus['footer']=<<<EOD
<footer><span class='sitefooter'>Copyright (c) Johan Salomonsson | <a href='https://github.com/mosbth/Anax-base'>Anax på GitHub</a> | <a href='http://validator.w3.org/unicorn/check?ucn_uri=referer&amp;ucn_task=conformance'>Unicorn</a></span></footer>
EOD;

//Finally
include(NEREUS_THEME_PATH);
