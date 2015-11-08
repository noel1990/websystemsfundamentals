<?php
/*----------------------------------
cv.php
Prints out all elements in CV
with image and name
----------------------------------*/

include_once("HTMLTemplate.php");
include_once("CVList.php");


$content = '<div id="container">';

foreach($cvList as $key => $item) { 
	$urlKey = urlencode($key);
	$content .=<<<END
	<div class="listItem">
		<a href="cv-item.php?p={$key}">
		<img src="{$item["image"]}" alt="" />
		<p>{$key}</p>
		</a>
	</div>
	
END;
}

$content .="</div>";

echo $header; 
echo $content; 
echo $footer;

?>