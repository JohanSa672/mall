<?php

/**
*	Search class
*
*/

class CMovieSearch {
	
	/**
	*	Members
	*/
	private $sqlOrig="";
	private $where=null;
	private $groupby="";
	private $params = array();
	private $rows;
	/**
	*	Constructor
	*/
	public function __construct(){
		
	}
	//
	public function searchMovies($genre, $genres, $hits, $title, $year1, $year2){
		$html = "
		<form>
		<fieldset>
		<legend>Sök</legend>
		<input type=hidden name=genre value='{$genre}'/>
		<input type=hidden name=hits value='{$hits}'/>
		<input type=hidden name=page value='1'/>
		<p><label>Titel (delsträng, använd % som *): <input type='search' name='title' value='{$title}'/></label></p>
		<p><label>Välj genre:</label> {$genres}</p>
		<p><label>Skapad mellan åren: 
		<input type='text' name='year1' value='{$year1}'/></label>
		- 
		<label><input type='text' name='year2' value='{$year2}'/></label>
    
		</p>
		<p><input type='submit' name='submit' value='Sök'/></p>
		<p><a href='?'>Visa alla</a></p>
		</fieldset>
		</form>
		";
		return $html;
	}
	
	public function prepareSQL($orderby, $order, $title, $year1, $year2, $genre, $hits, $page, $db){
		// Prepare the query based on incoming arguments
		$this->sqlOrig = '
		SELECT 
		M.*,
		GROUP_CONCAT(G.name) AS genre
		FROM Movie AS M
		LEFT OUTER JOIN Movie2Genre AS M2G
		ON M.id = M2G.idMovie
		INNER JOIN Genre AS G
	   	  ON M2G.idGenre = G.id
	   	  ';
	   	  $this->where    = null;
	   	  $this->groupby  = ' GROUP BY M.id';
	   	  $limit    = null;
	   	  $sort     = " ORDER BY $orderby $order";
	   	  //$params   = array();

	   	  // Select by title
	   	  if($title) {
	   	  	  $this->where .= ' AND title LIKE ?';
	   	  	  $this->params[] = $title;
	   	  } 

	   	  // Select by year
	   	  if($year1) {
	   	  	  $this->where .= ' AND year >= ?';
	   	  	  $this->params[] = $year1;
	   	  } 
	   	  if($year2) {
	   	  	  $this->where .= ' AND year <= ?';
	   	  	  $this->params[] = $year2;
	   	  } 

	   	  // Select by genre
	   	  if($genre) {
	   	  	  $this->where .= ' AND G.name = ?';
	   	  	  $this->params[] = $genre;
	   	  } 

	   	  // Pagination
	   	  if($hits && $page) {
	   	  	  $limit = " LIMIT $hits OFFSET " . (($page - 1) * $hits);
	   	  }
		
	   	  // Complete the sql statement
	   	  $this->where = $this->where ? " WHERE 1 {$this->where}" : null;
	   	  $sql = $this->sqlOrig . $this->where . $this->groupby . $sort . $limit;
	   	  $res = $db->ExecuteSelectQueryAndFetchAll($sql, $this->params);
	   	  
	   	  return $res;
	}
	
	// Get all genres that are active
	public function getAllGenres($db){
	$sql = '
	SELECT DISTINCT G.name
 	FROM Genre AS G
   	INNER JOIN Movie2Genre AS M2G
   	ON G.id = M2G.idGenre
   	';
   	$res = $db->ExecuteSelectQueryAndFetchAll($sql);

   	return $res;
   	}
	
   	
   	// Get max pages for current query, for navigation
   	public function getMaxPagesNav($hits,$db){
   		$sql = "
   		SELECT
   		COUNT(id) AS rows
   		FROM 
   		(
   		{$this->getSqlOrig()} {$this->getWhere()} {$this->getGroupBy()}
   		) AS Movie
   		";
   		$res = $db->ExecuteSelectQueryAndFetchAll($sql, $this->getParams());
   		$this->rows = $res[0]->rows;
   		$max = ceil($this->rows / $hits);
   		return $max;
   	}
	
   	public function getRows(){
		return $this->rows;
	}
   	
	public function getSqlOrig(){
		return $this->sqlOrig;
	}
	
	public function getWhere(){
		return $this->where;
	}
	
	public function getGroupBy(){
		return $this->groupby;
	}
	
	public function getParams(){
		return $this->params;
	}
		
}