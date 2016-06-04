<?php
/**
* This is a Nereus pagecontroller.
*
*/
//Includ the essential config-file which also creats the $asterios variable with its defaults.
include(__DIR__.'/config.php');

//Do it and store it all in variables in the Nereus container.
$nereus['title']="Hello World";
//img_v4.php?src=kodim15.png&save-as=jpg&width=250.

$nereus['header']=<<<EOD
<img class='sitelogo' src='img.php?src=o2.png&width=70' alt='Nereus Logo'/>
<span class='sitetitle'>Nereus webbtemplate</span>
<span class='siteslogan'>Återanvändvara moduler för webbutvecklng med PHP.</span>
<link rel='shortcut icon' href='favicon.ico'/>
EOD;

$nereus['main']=<<<EOD
<h1>Hej Världen</h1>
<p>Detta är en exempelsida som visar hur Nereus ser ut och fungerar.</p>
EOD;

$nereus['footer']=<<<EOD
<footer><span class='sitefooter'>Copyright (c) Johan Salomonsson | <a href='https://github.com/mosbth/Anax-base'>Anax på GitHub</a> | <a href='http://validator.w3.org/unicorn/check?ucn_uri=referer&amp;ucn_task=conformance'>Unicorn</a></span></footer>
EOD;

//Finally
include(NEREUS_THEME_PATH);

