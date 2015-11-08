<?php
/*------------------------------
HTMLTemplate.php
Contains HTML code that is the same
over several pages
------------------------------*/

$header = <<<END
<!DOCTYPE html>
<html lang="en">
	<head>
		<title></title>
		<link rel="stylesheet" type="text/css" href="cssmall.css"/>
	</head>
	<body>
	
		<nav id="navigation">
			<ul id="menu">	
				<li class="active"><a href="index.php">Home</a></li>
				<li><a href="cv.php">CV</a></li>
				<li><a href="#">Guestbook</a></li>
				<li><a href="#">Game</a></li>
				<li><a href="contact.php">Contact</a></li>
			</ul>
		</nav><!-- header -->
				
		<div id="content">
		
END;

$footer = <<<END
		
		</div><!-- content -->
	
	</body>
</html>

END;

?>