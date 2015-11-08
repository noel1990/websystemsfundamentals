<?php
/*-----------------------------
gb.php
Displays guestbook posts
and handles adding of new posts
-----------------------------*/

include_once("HTMLTemplate.php");
include_once("connstring.php");

date_default_timezone_set('UTC');

$tablePost = "post";
$tableComment = "comment";

$feedback = "";
$name = "";
$msg = "";

if(!empty($_POST)){
	
	$name = isset($_POST['name'])? $_POST['name']:'';
	$msg = isset($_POST['msg'])? $_POST['msg']:'';
	$spamTest = isset($_POST['address'])? $_POST['address']:'';
	
	if($spamTest != ''){
		die("I think you're a robot. If you're not, go back and try again.");
	}
	
	if($name =='' || $msg ==''){
		$feedback = "<p class=\"feedback-yellow\">Please fill out all fields.</p>";
		
	}else{
		//---------------------------------
		//Prevents SQL injections
		$name = utf8_encode($mysqli->real_escape_string($name));
		$msg = utf8_encode($mysqli->real_escape_string($msg));
		
		$adminId = isset($_SESSION["userId"])?$_SESSION["userId"]:"NULL";
		
		//---------------------------------
		//SQL query
		$query = <<<END
		--
		-- Inserts new message into DB
		--
		INSERT INTO {$tablePost}(postName, postMessage,adminId)
		VALUES('{$name}','{$msg}',{$adminId});

END;
	
	$res = $mysqli->query($query) or die("Could not query database". $mysqli->errno .":". $mysqli->error);//Performs query
	$feedback = "<p class=\"fessdback-green\">Your post has been added. Thanks!</p>";
	}
}

$name = isset($_SESSION["username"])?$_SESSION["username"]:$name;

$name = htmlspecialchars($name);
$msg = htmlspecialchars($msg);

$content = <<<END
				<div id="container">
					{$feedback}
					<form action="gb.php" method="post">
						<label for="name">Name:</label><br>
						<input type="text" id="name" name="name" placeholder="User Name" value="{$name}" /><br>
						<input type="text" id="address" name="address" /><br>
						<label for="msg">Message:</label><br>
						<textarea id="msg" name="msg">{$msg}</textarea><br>
						<input type="submit" value="submit" /><br>
					</form>
				</div><!-- container -->

END;

//----------------------------------------
//SQL query
	$query = <<<END
	--
	-- Gets all posts from DB
	--
	SELECT postId, postName, postMessage, postTimestamp,adminId
	FROM {$tablePost}
	ORDER BY postTimestamp DESC;

END;

$res = $mysqli->query($query) or die("Could not query database" . $mysqli -> errno . ":" . $mysqli->error);//Performs query

//Loops through results
while($row = $res->fetch_object()){
	$date = strtotime($row->postTimestamp);
	$date = date("d M Y H:i", $date);
	$postName = utf8_decode(htmlspecialchars($row->postName));
	$postMessage = utf8_decode(htmlspecialchars($row->postMessage));
	
	$adminRow = "";
	$adminClass = (!is_null($row->adminId))?"admin":"";
	
	if(isset($_SESSION["username"])){
		$adminRow = <<<END
		<p class="gb-admin-row"><a href="gb-edit.php?pid={$row->postId}">Edit</a> &middot; <a href="gb-delete.php?pid={$row->postId}">Delete</a></p>
END;
	}
	
	$content .=<<<END
		<div class="gb-post{$adminClass}">
			<p class="gb-name">Written by:{$postName}</p><br>
			<p class="gb-msg">{$postMessage}</p><br>
			<p><span class="gb-comment">
				<a href="gb-comm.php?id={$row->postId}">Write a comment</a>
				</span>
				<span class="gb-date">{$date}</span><br>
			</p><br>
			{$adminRow}
		</div>
END;

	//Query for comments
	$query = <<<END
	--
	-- Gets all comments for current post from DB
	--
	SELECT commName, commMessage, commTimestamp, commentId , adminId
	FROM {$tableComment} 
	WHERE postId = {$row->postId}
	ORDER BY commTimestamp ASC;

END;

	$res2 = $mysqli->query($query) or die("Could not query database" . $mysqli->errno . ":" . $mysqli->error);//Performs query
	
	//Loops through results for comments
	while($row2 = $res2->fetch_object()){
		$date = strtotime($row2->commTimestamp);
		$date = date("d M Y H:i", $date);
		
		$commName = utf8_decode(htmlspecialchars($row2->commName));
		$commMessage = utf8_decode(htmlspecialchars($row2->commMessage));
		
		$adminClass = (!is_null($row2->adminId))?"admin":"";
		
		if(isset($_SESSION["username"])){
		$adminRow = <<<END
		<p class="gb-admin-row"><a href="gb-edit.php?cid={$row2->commentId}">Edit</a> &middot; <a href="gb-delete.php?cid={$row2->commentId}">Delete</a></p>
END;
	}
		
		$content .= <<<END
			<div class="gb-commsub{$adminClass}">
				<p class="gb-name">Written by: {$commName}</p><br>
				<p class="gb-msg">{$commMessage}</p><br>
				<p class="gb-date">{$date}</p>
				{$adminRow}
			</div>

END;

	}
}


$content .="</div><!-- container -->";

$res->close();//Closes results
$mysqli->close();//Closes DB connection

echo $header;
echo $content;
echo $footer;

?>