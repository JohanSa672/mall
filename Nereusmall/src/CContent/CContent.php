<?php

class CContent {
	
	public function __construct(){
		
	}
	
public function createTableForContent(){
	// Restore the database to its original settings
	$sql      = 'reset.sql';
	$mysql    = 'C:\wamp\bin\mysql\mysql5.6.17\bin\mysql.exe';
	$host     = 'localhost';
	$login    = 'root'; //acronym
	$password = '';
	$output = null;
	
	 $cmd = "$mysql -h{$host} -u{$login} < $sql";
	 $test=exec($cmd);
	 var_dump($test);
	 $output = "<p>Databasen är återställd via kommandot<br/><code>{$cmd}</code></p>";
	 
	 return $output;
}

public function createInsertToContent($db,$us,$save,$url,$slug,$url,$type,$title,$cat,$data,$filter,$published){
	// Check if form was submitted
	$output = null;
	if($save) {
		$own=$us->getOwnerId($db);
		$sql = '
		INSERT INTO content VALUES
		(null,?,?,?,?,?,?,?,?,null,?,NOW(),null,null)  ';
		$url = empty($url) ? null : $url;
		$params = array($slug,$url,$type,$title,$cat,$data,$filter,$own,$published);
		$res = $db->ExecuteQuery($sql,$params);
		if($res) {
			$output = 'Informationen sparades.';
		}
		else {
			$output = 'Informationen sparades EJ.<br><pre>'; //. print_r($db->ErrorInfo(), 1) . '</pre>';
		}
	}
	return $output;
	
}

public function editUpdateContent($db,$save, $title, $slug, $url, $data, $type, $filter, $published, $id){
	// Check if form was submitted
	$output = null;
	if($save) {
		$sql = '
		UPDATE Content SET
		title   = ?,
		slug    = ?,
		url     = ?,
		data    = ?,
		type    = ?,
		filter  = ?,
		published = ?,
     	updated = NOW()
     	WHERE 
    	 id = ?
    	 ';
    	 $url = empty($url) ? null : $url;
    	 $params = array($title, $slug, $url, $data, $type, $filter, $published, $id);
    	 $res = $db->ExecuteQuery($sql, $params);
 	if($res) {
 		$output = 'Informationen sparades.';
 	}
 	else {
 		$output = 'Informationen sparades EJ.<br><pre>' . print_r($db->ErrorInfo(), 1) . '</pre>';
 	}
 	}
 	return $output;
}

public function deleteContent($db,$delete,$id){
$output = null;
if($delete) {
  $sql = '
    DELETE FROM content  
    WHERE 
      id = ?
  ';
  $params = array($id);
  $res = $db->ExecuteQuery($sql, $params);
  if($res) {
    $output = 'Informationen togs bort.';
  }
  else {
    $output = 'Informationen togs INTE bort.<br><pre>' . print_r($db->ErrorInfo(), 1) . '</pre>';
  }
  return $output;
}
	
	
}

public function selectFromDatabase($db,$id){
	// Select from database
	$sql = 'SELECT * FROM Content WHERE id = ?';
	$res = $db->ExecuteSelectQueryAndFetchAll($sql, array($id));

	if(isset($res[0])) {
		$c = $res[0];
	}
	else {
		die('Misslyckades: det finns inget innehåll med sådant id.');
	}
	return $c;
}

/**
 * Create a slug of a string, to be used as url.
 *
 * @param string $str the string to format as slug.
 * @returns str the formatted slug. 
 */
public function slugify($str) {
  $str = mb_strtolower(trim($str));
  $str = str_replace(array('å','ä','ö'), array('a','a','o'), $str);
  $str = preg_replace('/[^a-z0-9-]/', '-', $str);
  $str = trim(preg_replace('/-+/', '-', $str), '-');
  return $str;
}

// Get content	
public function getContentPage($db,$url,$fi,$acronym){
$sql = "
SELECT *
FROM Content
WHERE
  type = 'page' AND
  url = ? AND
  published <= NOW();
";
$res = $db->ExecuteSelectQueryAndFetchAll($sql, array($url));

if(isset($res[0])) {
  $c = $res[0];
  
}
else {
  die('Misslyckades: det finns inget innehåll.');
}

 // Sanitize content before using it.
$title  = htmlentities($c->title, null, 'UTF-8');
$data   = $fi->doFilter(htmlentities($c->data, null, 'UTF-8'), $c->filter);
//Editlink
$editLink = $acronym ? "<a href='edit.php?id={$c->id}'>Uppdatera sidan</a>" : null;
$d=array($title,$data,$editLink);
return $d;
}

// Get content Blog
public function getContentBlog($db,$slug){
	
	$slugSql = $slug ? 'slug = ?' : '1';
	$sql = "
	SELECT *
	FROM Content
	WHERE
	type = 'post' AND
	$slugSql AND
 	published <= NOW()
 	ORDER BY updated DESC
 	;
 	";
 	
 	$res = $db->ExecuteSelectQueryAndFetchAll($sql, array($slug));
 	return $res;
}

// // Sanitize blog content before using it.
public function getSanitizeBlog($c,$slug,$acronym,$nereus,$fi){
    $title  = htmlentities($c->title, null, 'UTF-8');
    $data   = $fi->doFilter(htmlentities($c->data, null, 'UTF-8'), $c->filter);

    if($slug) {
      $nereus['title'] = "$title | " . $nereus['title'];
    }
    $editLink = $acronym ? "<a href='editc.php?id={$c->id}'>Uppdatera posten</a>" : null;

$e=array($title,$data,$nereus,$editLink);
return $e;

}

// Get blog post in category
public function getContentCat($db,$cat){
	$sql = "
	SELECT *
	FROM Content
	WHERE
	type = 'post' AND
	category='$cat' AND
 	published <= NOW()
 	ORDER BY updated DESC
 	;
 	";
	
 	$res = $db->queryfetch($sql);
 	return $res;
}

//show category
public function getcategorystring($db){
$sql2='SELECT category FROM content WHERE category IS NOT NULL 
AND (published <= NOW()) AND type="post" GROUP BY category';

// Show category
$catstr="Visa kategori:";
$rescat=$db->queryfetch($sql2);
sort($rescat);
foreach($rescat AS $val){
	$catstr .="<a href='blog.php?cat={$val->category}'> {$val->category} </a>";
}
return $catstr;
}

// Get all content
public function getItems($db){
$sql = '
  SELECT *, (published <= NOW()) AS available
  FROM Content;
';
$res = $db->ExecuteSelectQueryAndFetchAll($sql);

// Put results into a list
$items = null;
foreach($res AS $key => $val) {
	if($val->type=='post'){
		$items .= "<li>{$val->type} (" . (!$val->available ? 'inte ' : null) . "publicerad): " . htmlentities($val->title, null, 'UTF-8') . " Kategori:{$val->category}". " (<a href='editc.php?id={$val->id}'>editera</a> <a href='" . getUrlToContent($val) . "'>visa</a> <a href='delete.php?id={$val->id}'>radera</a>)</li>\n";
  	}
  	else{
  		$items .= "<li>{$val->type} (" . (!$val->available ? 'inte ' : null) . "publicerad): " . htmlentities($val->title, null, 'UTF-8') . " (<a href='editc.php?id={$val->id}'>editera</a> <a href='" . getUrlToContent($val) . "'>visa</a> <a href='delete.php?id={$val->id}'>radera</a>)</li>\n";	
  	}
}
return $items;
}

//navbarfunction
public function nav($db){
	
	$sql = "
	SELECT id,slug,url,type,title
	FROM Content
	WHERE
 	type = 'page' OR type='post' AND
 	published <= NOW();
 	";
 	$res = $db->queryfetch($sql);
 	$restotal=null;
 	foreach($res as $b){  
 	if($b->type=="page"){
 	$results=array($b->slug =>array('text' =>$b->slug ,'url'=>$b->type.".php?url=".$b->url,'title'=>$b->title));
 	}
 	if($b->type=="post"){
 	$results=array($b->slug =>array('text' =>$b->slug ,'url'=>"blog.php?slug=".$b->slug,'title'=>$b->title));	
 	}
 	if(is_array($restotal)){
 	$restotal=array_merge($restotal, $results);	
 	}
 	else{
 		$restotal=$results;
 	}
 	
 	}
 	
 	return $restotal;
}

}