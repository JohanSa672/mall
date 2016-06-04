<?php

class CPage {
	
public function writepage($title,$data,$editLink){
	$page=<<<EOD
<article>
<header>
<h1>{$title}</h1>
</header>
{$data}

<footer>
{$editLink}
</footer
</article>
EOD;

return $page;
}

}