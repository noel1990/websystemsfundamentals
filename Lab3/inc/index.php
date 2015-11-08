<?php
/*----------------------------------
index.php
Start page with welcome
The first page the visitor sees
------------------------------*/

include_once("HTMLTemplate.php");

$content = <<<END
			<div id="container">
				<h1>Welcome!</h1>
				<p>This is the first page of our website! ^-^</p>
			</div><!-- container -->
END;

echo $header; 
echo $content; 
echo $footer;

?>