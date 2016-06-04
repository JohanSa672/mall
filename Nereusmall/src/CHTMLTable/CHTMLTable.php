<?php

class CHTMLTable {
	
	
	/**
	*	Members
	*/
	
	/**
	*	Constructor
	*/
	public function __construct(){
		
	}
	
	/**
	* Use the current querystring as base, modify it according to $options and return the modified query string.
	*
	* @param array $options to set/change.
	* @param string $prepend this to the resulting query string
	* @return string with an updated query string.
	*/
	public function getQueryString($options=array(), $prepend='?') {
	// parse query string into array
	$query = array();
	parse_str($_SERVER['QUERY_STRING'], $query);

	// Modify the existing query string with new options
 	$query = array_merge($query, $options);

 	// Return the modified querystring
 	return $prepend . htmlentities(http_build_query($query));
	}
	
	
/**
 * Create navigation among pages.
 *
 * @param integer $hits per page.
 * @param integer $page current page.
 * @param integer $max number of pages. 
 * @param integer $min is the first page number, usually 0 or 1. 
 * @return string as a link to this page.
 */
public function getPageNavigation($hits, $page, $max, $min=1) {
	$nav  = ($page != $min) ? "<a href='" . $this->getQueryString(array('page' => $min)) . "'>&lt;&lt;</a> " : '&lt;&lt; ';
	$nav .= ($page > $min) ? "<a href='" . $this->getQueryString(array('page' => ($page > $min ? $page - 1 : $min) )) . "'>&lt;</a> " : '&lt; ';

	for($i=$min; $i<=$max; $i++) {
		if($page == $i) {
			$nav .= "$i ";
		}
		else {
			$nav .= "<a href='" . $this->getQueryString(array('page' => $i)) . "'>$i</a> ";
   	}
   	}

   	$nav .= ($page < $max) ? "<a href='" . $this->getQueryString(array('page' => ($page < $max ? $page + 1 : $max) )) . "'>&gt;</a> " : '&gt; ';
   	$nav .= ($page != $max) ? "<a href='" . $this->getQueryString(array('page' => $max)) . "'>&gt;&gt;</a> " : '&gt;&gt; ';
  	return $nav;
	}
	
/**
 * Function to create links for sorting
 *
 * @param string $column the name of the database column to sort by
 * @return string with links to order by column.
 */
public function orderby($column) {
	$nav  = "<a href='" . $this->getQueryString(array('orderby'=>$column, 'order'=>'asc')) . "'>&darr;</a>";
	$nav .= "<a href='" . $this->getQueryString(array('orderby'=>$column, 'order'=>'desc')) . "'>&uarr;</a>";
	return "<span class='orderby'>" . $nav . "</span>";
}

/**
 * Create links for hits per page.
 *
 * @param array $hits a list of hits-options to display.
 * @param array $current value.
 * @return string as a link to this page.
 */
public function getHitsPerPage($hits, $current=null) {
	$nav = "Träffar per sida: ";
	foreach($hits AS $val) {
		if($current == $val) {
			$nav .= "$val ";
		}
		else {
			$nav .= "<a href='" . $this->getQueryString(array('hits' => $val)) . "'>$val</a> ";
		}
	}  
	return $nav;
	}
	
	

// Put results into a HTML-table
public function resultHTML($res){
	$tr = "<tr><th>Rad</th><th>Id " . $this->orderby('id') . "</th><th>Bild</th><th>Titel " . $this->orderby('title') . "</th><th>År " . $this->orderby('year') . "</th><th>Genre</th></tr>";
	foreach($res AS $key => $val) {
		$tr .= "<tr><td>{$key}</td><td>{$val->id}</td><td><img width='80' height='40' src='{$val->image}' alt='{$val->title}' /></td><td>{$val->title}</td><td>{$val->year}</td><td>{$val->genre}</td></tr>";
}
	return $tr;
}


// Get all genres in HTML-table.
public function getHTMLGenre($res,$genre,$htmlT){
	$genres = null;
	
   	foreach($res as $val) {
   		if($val->name == $genre) {
   			$genres .= "$val->name ";
   		}
   		else {
   			$genres .= "<a href='" . $htmlT->getQueryString(array('genre' => $val->name)) . "'>{$val->name}</a> ";
   		}
}
	return $genres;
}

}