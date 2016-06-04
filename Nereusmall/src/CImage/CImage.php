<?php

class CImage{

// Define some constant values, append slash
private $maxHeight;
private $maxWidth;

//Constructor
public function __construct(){
	$this->maxHeight;
	$this->maxWidth;
 $this->maxWidth = $this->maxHeight = 2000;
}

/**
 * Display error message.
 *
 * @param string $message the error message to display.
 */
public function errorMessage($message) {
  header("Status: 404 Not Found");
  die('img.php says 404 - ' . htmlentities($message));
}	


public function imgShow($_GETA,$IMG_PATH,$CACHE_PATH){

	
	//
// Get the incoming arguments
//
$src      = isset($_GETA['src']) ? $_GET['src']     :  null;
$verbose  = isset($_GETA['verbose']) ? true              : null;
$saveAs   = isset($_GETA['save-as']) ? $_GET['save-as']  : null;
$quality  = isset($_GETA['quality']) ? $_GET['quality']  : 60;
$ignoreCache = isset($_GETA['no-cache']) ? true           : null;
$newWidth   = isset($_GETA['width'])   ? $_GET['width']    : null;
$newHeight  = isset($_GETA['height'])  ? $_GET['height']   : null;
$cropToFit  = isset($_GETA['crop-to-fit']) ? true : null;
$sharpen    = isset($_GETA['sharpen']) ? true : null;
$grayscale = isset($_GETA['grayscale'])? true: null;
$sepia = isset($_GETA['sepia'])? true: null;


$pathToImage = realpath($IMG_PATH . $src);

//
// Validate incoming arguments
$this->validate($IMG_PATH,$CACHE_PATH,$src,$pathToImage,$saveAs,$quality,$newWidth,$newHeight,$cropToFit);


//$verbose=true;
//$verbose=false;
//$cim=new CImage2();
//$cacheFileName2="img/car.png";
$cacheFileName3="cache/-.-car_491_323_q60.png";

if($verbose) {
  $query = array();
  parse_str($_SERVER['QUERY_STRING'], $query);
  unset($query['verbose']);
  $url = '?' . http_build_query($query);
  echo $this->getdisplay($url);
}



// Get information on the image
list($width, $height,$filesize)=$this->getInfoImage($pathToImage,$verbose);


// Calculate new width and height for the image
list($cropWidth,$cropHeight,$newWidth,$newHeight)=$this->getNewWidthAndHeight($width, $height,$cropToFit,$newWidth,$newHeight,$verbose);

// Creating a filename for the cache.Is there already a valid image in the cache directory, then use it and exit
//If cached image is valid, output it.
list($cacheFileName,$fileExtension,$saveAs)=$this->createNameAndOutputCache($pathToImage,$saveAs,$quality,$cropToFit,$sharpen,$sepia,$src,$newWidth,$newHeight,$ignoreCache,$verbose);

// Open up the image from file
//$image=$cim->OpenUpImage($verbose,$fileExtension,$pathToImage);
$imageopen=$this->OpenUpImage($verbose,$fileExtension,$pathToImage);

// Resize the image if needed
list($imagepic,$width,$height)=$this->resizeImage($cropToFit,$verbose,$width,$height,$cropHeight,$cropWidth, $newWidth, $newHeight,$imageopen);

if($verbose){
	$this->verbose("Detta är save-as:".$saveAs);
	$this->verbose("OLIKA MÅTT:"."CPW".$cropWidth."CPH".$cropHeight."NW".$newWidth."NH".$newHeight);
	$this->verbose("Detta är grayscale:".$grayscale);
}

// Apply filters and postprocessing of image
if($sharpen) {
  $imagepic = $this->sharpenImage($imagepic);
}

if($grayscale){
	$imagepic =$this->grayScaleImage($imagepic);
}

if($sepia){
	$imagepic =$this->sepiaImage($imagepic);
}

// Save the image
//
$this->saveTheImage($saveAs,$verbose,$imagepic,$cacheFileName, $quality,$filesize);


	
$this->outputImage($cacheFileName, $verbose);	
	
}

/**************************************************/

/**
 * Display log message.
 *
 * @param string $message the log message to display.
 */
public function verbose($message) {
  echo "<p>".htmlentities($message)."</p>";
}	

/**
 * Create new image and keep transparency
 *
 * @param resource $image the image to apply this filter on.
 * @return resource $image as the processed image.
 */
public function createImageKeepTransparency($width, $height) {
    $img = imagecreatetruecolor($width, $height);
    imagealphablending($img, false);
    imagesavealpha($img, true);  
    return $img;
}

/**
 * Sharpen image as http://php.net/manual/en/ref.image.php#56144
 * http://loriweb.pair.com/8udf-sharpen.html
 *
 * @param resource $image the image to apply this filter on.
 * @return resource $image as the processed image.
 */
public function sharpenImage($image) {
  $matrix = array(
    array(-1,-1,-1,),
    array(-1,16,-1,),
    array(-1,-1,-1,)
  );
  $divisor = 8;
  $offset = 0;
  imageconvolution($image, $matrix, $divisor, $offset);
  return $image;
}

// Grayscale the image
public function grayScaleImage($imagepic){
	imagefilter($imagepic, IMG_FILTER_GRAYSCALE);
	return $imagepic;
}

// Sepia 
public function sepiaImage($imagepic){
	$imagepic=$this->grayScaleImage($imagepic);
	imagefilter($imagepic, IMG_FILTER_BRIGHTNESS, -10);
	imagefilter($imagepic, IMG_FILTER_CONTRAST, -20);
	imagefilter($imagepic, IMG_FILTER_COLORIZE, 120,60,0,0);
	$imagepic=$this->sharpenImage($imagepic);
	return $imagepic;
}

public function outputImage($file, $verbose) {
	
  $info = getimagesize($file);
  !empty($info) or $this->errorMessage("The file doesn't seem to be an image.");
  $mime   = $info['mime'];
  
  //echo"("test output vardum:".$mime."Filen:".$file)"; //**************test
  
  $lastModified = filemtime($file);  
  $gmdate = gmdate("D, d M Y H:i:s", $lastModified);

  if($verbose) {
    $this->verbose("Memory peak: " . round(memory_get_peak_usage() /1024/1024) . "M");
    $this->verbose("Memory limit: " . ini_get('memory_limit'));
    $this->verbose("Time is {$gmdate} GMT.");
  }

  if(!$verbose) header('Last-Modified: ' . $gmdate . ' GMT');
  if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE']) && strtotime($_SERVER['HTTP_IF_MODIFIED_SINCE']) == $lastModified){
    if($verbose) { $this->verbose("Would send header 304 Not Modified, but its verbose mode."); exit; }
    header('HTTP/1.0 304 Not Modified');
  } else {  
    if($verbose) { $this->verbose("Would send header to deliver image with modified time: {$gmdate} GMT, but its verbose mode."); exit; }
    header('Content-type: ' . $mime);  
    readfile($file);
   
  }
  exit;
}	
	
public function outputImage2($file, $verbose) {
	header('Content-type: '.'image/png');  
	readfile($file);
	
}

//
// Validate incoming arguments
//
public function validate($IMG_PATH,$CACHE_PATH,$src,$pathToImage,$saveAs,$quality,$newWidth,$newHeight,$cropToFit){	
is_dir($IMG_PATH) or $this->errorMessage('The image dir is not a valid directory.');
is_writable($CACHE_PATH) or $this->errorMessage('The cache dir is not a writable directory.');
isset($src) or $this->errorMessage('Must set src-attribute.');
preg_match('#^[a-z0-9A-Z-_\.\/]+$#', $src) or $this->errorMessage('Filename contains invalid characters.');
substr_compare($IMG_PATH, $pathToImage, 0, strlen($IMG_PATH)) == 0 or $this->errorMessage('Security constraint: Source image is not directly below the directory IMG_PATH.');
is_null($saveAs) or in_array($saveAs, array('png', 'jpg', 'jpeg','gif')) or $this->errorMessage('Not a valid extension to save image as');
is_null($quality) or (is_numeric($quality) and $quality > 0 and $quality <= 100) or $this->errorMessage('Quality out of range');
is_null($newWidth) or (is_numeric($newWidth) and $newWidth > 0 and $newWidth <= $this->maxWidth) or $this->errorMessage('Width out of range');
is_null($newHeight) or (is_numeric($newHeight) and $newHeight > 0 and $newHeight <= $this->maxHeight) or $this->errorMessage('Height out of range');
is_null($cropToFit) or ($cropToFit and $newWidth and $newHeight) or $this->errorMessage('Crop to fit needs both width and height to work');

}

//Display
public function getdisplay($url){
	$dis=<<<EOD
<html lang='en'>
<meta charset='UTF-8'/>
<title>img.php verbose mode</title>
<h1>Verbose mode</h1>
<p><a href=$url><code>$url</code></a><br>
<img src='img/car.png' />
<h2>ny bild</h2>
<img src='{$url}' /></p>

EOD;

//<img src='{$url}' /></p>
return $dis;
}

//
// Get information on the image
//
public function getInfoImage($pathToImage,$verbose){	
$imgInfo = list($width, $height, $type, $attr) = getimagesize($pathToImage);
!empty($imgInfo) or $this->errorMessage("The file doesn't seem to be an image.");
$mime = $imgInfo['mime'];
$filesize = filesize($pathToImage);

if($verbose) {
  $this->verbose("Image file: {$pathToImage}");
  $this->verbose("Image information: " . print_r($imgInfo, true));
  $this->verbose("Image width x height (type): {$width} x {$height} ({$type}).");
  $this->verbose("Image file size: {$filesize} bytes.");
  $this->verbose("Image mime type: {$mime}.");
}
$info=array($width, $height,$filesize);
return $info;
}

//
// Calculate new width and height for the image
public function getNewWidthAndHeight($width, $height,$cropToFit,$newWidth,$newHeight,$verbose){
$aspectRatio = $width / $height;

if($cropToFit && $newWidth && $newHeight) {
  $targetRatio = $newWidth / $newHeight;
  $cropWidth   = $targetRatio > $aspectRatio ? $width : round($height * $targetRatio);
  $cropHeight  = $targetRatio > $aspectRatio ? round($width  / $targetRatio) : $height;
  if($verbose) { $this->verbose("Crop to fit into box of {$newWidth}x{$newHeight}. Cropping dimensions: {$cropWidth}x{$cropHeight}."); }
}
else if($newWidth && !$newHeight) {
  $newHeight = round($newWidth / $aspectRatio);
  if($verbose) { $this->verbose("New width is known {$newWidth}, height is calculated to {$newHeight}."); }
}
else if(!$newWidth && $newHeight) {
  $newWidth = round($newHeight * $aspectRatio);
  if($verbose) { $this->verbose("New height is known {$newHeight}, width is calculated to {$newWidth}."); }
}
else if($newWidth && $newHeight) {
  $ratioWidth  = $width  / $newWidth;
  $ratioHeight = $height / $newHeight;
  $ratio = ($ratioWidth > $ratioHeight) ? $ratioWidth : $ratioHeight;
  $newWidth  = round($width  / $ratio);
  $newHeight = round($height / $ratio);
  if($verbose) { $this->verbose("New width & height is requested, keeping aspect ratio results in {$newWidth}x{$newHeight}."); }
}
else {
  $newWidth = $width;
  $newHeight = $height;
  if($verbose) { $this->verbose("Keeping original width & heigth."); }
}
if(!$cropToFit){
	$cropWidth=null;
	$cropHeight=null;	
}
$nwh=array($cropWidth,$cropHeight,$newWidth,$newHeight);
return $nwh;
}

//
// Creating a filename for the cache
public function createNameAndOutputCache($pathToImage,$saveAs,$quality,$cropToFit,$sharpen,$sepia,$src,$newWidth,$newHeight,$ignoreCache,$verbose){
$parts      = pathinfo($pathToImage);
$fileExtension  = $parts['extension'];
$saveAs     = is_null($saveAs) ? $fileExtension : $saveAs;
$quality_   = is_null($quality) ? null : "_q{$quality}";
$cropToFit_     = is_null($cropToFit) ? null : "_cf";
$sharpen_       = is_null($sharpen) ? null : "_s";
$sepia	=is_null($sepia) ? null : "_se";
$dirName    = preg_replace('/\//', '-', dirname($src));
$cacheFileName = CACHE_PATH . "-{$dirName}-{$parts['filename']}_{$newWidth}_{$newHeight}{$quality_}{$sepia}{$sharpen}{$cropToFit_}.{$saveAs}";
$cacheFileName = preg_replace('/^a-zA-Z0-9\.-_/', '', $cacheFileName);

if($verbose) { $this->verbose("Cache file is : {$cacheFileName}"); }

//
// Is there already a valid image in the cache directory, then use it and exit
//
$imageModifiedTime = filemtime($pathToImage);
$cacheModifiedTime = is_file($cacheFileName) ? filemtime($cacheFileName) : null;


// If cached image is valid, output it.
if(!$ignoreCache && is_file($cacheFileName) && $imageModifiedTime < $cacheModifiedTime) {
  if($verbose) { $this->verbose("Cache file is valid, output it."); }
 //$this->verbose("I createName..cachefilename:".$cacheFileName."verbose är:".$verbose."fileexteneion:".$fileExtension);
  $this->outputImage($cacheFileName, $verbose);                                                                
}

$cf=array($cacheFileName,$fileExtension,$saveAs);
return $cf;
}

// Open up the image from file
public function OpenUpImage($verbose,$fileExtension,$pathToImage){

if($verbose) { $this->verbose("File extension is: {$fileExtension}"); }

switch($fileExtension) {  
  case 'jpg':
  case 'jpeg': 
    $image = imagecreatefromjpeg($pathToImage);
    if($verbose) { $this->verbose("Opened the image as a JPEG image."); }
    break;  
  
  case 'png':  
    $image = imagecreatefrompng($pathToImage); 
    if($verbose) { $this->verbose("Opened the image as a PNG image."); }
    break;  

  case 'gif':
  	$image=imagecreatefromgif($pathToImage);
  	if($verbose) { $this->verbose("Opened the image as a GIF image."); }	
  	break;
  	
  default: errorPage('No support for this file extension.');
}
return $image;
}

//
// Resize the image if needed
public function resizeImage($cropToFit,$verbose,$width,$height,$cropHeight,$cropWidth, $newWidth, $newHeight,$image){
if($cropToFit) {
  if($verbose) { $this->verbose("Resizing, crop to fit."); }
  $cropX = round(($width - $cropWidth) / 2);  
  $cropY = round(($height - $cropHeight) / 2); 
  $imageResized = $this->createImageKeepTransparency($newWidth, $newHeight);
  if($verbose){
  	$this->verbose("Cropx".$cropX."Cropy".$cropY."newW".$newWidth."NewH".$newHeight."cropWidth".$cropWidth."cropHeight".$cropHeight);  
  }
  imagecopyresampled($imageResized, $image, 0, 0, $cropX, $cropY, $newWidth, $newHeight, $cropWidth, $cropHeight);
  $image = $imageResized;
  $width = $newWidth;
  $height = $newHeight;
}
else if(!($newWidth == $width && $newHeight == $height)) {
  if($verbose) { $this->verbose("Resizing, new height and/or width."); }
  $imageResized = $this->createImageKeepTransparency($newWidth, $newHeight);
  imagecopyresampled($imageResized, $image, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);
  $image  = $imageResized;
  $width  = $newWidth;
  $height = $newHeight;
}
$iwh=array($image,$width,$height);
return $iwh;
}

// Save the image
//
public function saveTheImage($saveAs,$verbose,$image, $cacheFileName, $quality,$filesize){
switch($saveAs) {
  case 'jpeg':
  case 'jpg':
    if($verbose) { $this->verbose("Saving image as JPEG to cache using quality = {$quality}."); }
    imagejpeg($image, $cacheFileName, $quality);
  break;  

  case 'png':  
    if($verbose) { $this->verbose("Saving image as PNG to cache."); }
     // Turn off alpha blending and set alpha flag
    imagealphablending($image, false);
    imagesavealpha($image, true);
    imagepng($image, $cacheFileName);  
  break;  
  
case 'gif':
	if($verbose) { $this->verbose("Saving image as GIF to cache."); }
	imagegif($image, $cacheFileName);
	break;
	
  default:
    $this->errorMessage('No support to save as this file extension.');
  break;
}


if($verbose) { 
  clearstatcache();
  $cacheFilesize = filesize($cacheFileName);
  $this->verbose("File size of cached file: {$cacheFilesize} bytes."); 
  $this->verbose("Cache file has a file size of " . round($cacheFilesize/$filesize*100) . "% of the original size.");
}
}

}