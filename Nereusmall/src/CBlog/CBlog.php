<?php

class CBlog {
	
public function writepageBlog($db,$c,$title,$data,$us,$editLink) {
	$page=<<<EOD
<section>
  <article>
  <header>
  <h1><a href='blog.php?slug={$c->slug}'>{$title}</a></h1>
  </header>

  {$data}<br/>
  Bloggen är skapad: {$c->created}<br/>
  Artikeln är författad av: {$us->getOwner($c->owner,$db)}
  <footer>
  {$editLink}
  </footer
  </article>
</section>
EOD;

return $page;
}
}