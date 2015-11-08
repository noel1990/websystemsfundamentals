<?php
/*-----------------------------
gb-comm.php
Shows chosen GB-post with comment
and allows adding of comments
-----------------------------*/

include_once("HTMLTemplate.php");
include_once("connstring.php");

date_default_timezone_set('UTC');

$tableComment = "comment";
$tablePost = "post";

$feedback = "";
$name = "";
$msg = "";

$postId = isset($_GET['id'])? $_GET['id']:'';
	
//Checks that post id has been entered
if( $postId ==''){
	header("Location: gb.php");
	exit();
	}

//--------------------------
//Prevents SQL injections
$postId = $mysqli->real_escape_string($postId);
	
//---------------------------
//SQL query
$query = <<<END
--
-- Gets chosen post from DB
--
SELECT postName, postMessage, postTimestamp,adminId
FROM {$tablePost}
WHERE postId = {$postId};

END;

$res = $mysqli->query($query) or die("Could not query database" . $mysqli->errno . ":" . $mysqli->error);//Performs query
	
//Checks that post exists
if($res->num_rows<1){
	$content=<<<END
		<div id="container">
			<p>The post you chose cannot be found. Please try again.</p>
			<p><a href="gb.php">Guestbook</a></p>
		</div><!-- container -->
END;
	
}else{
		if(!empty($_POST)){
			$name = isset($_POST['name']) ? $_POST['name']:'';
			$msg = isset($_POST['msg']) ? $_POST['msg']:'';
			$spamTest = isset($_POST['address']) ? $_POST['address']:'';
			
			if($spamTest !=''){
				die("I think you're a robot. If you're not, go back and try again.");
			}
			
			if($name == ''||$msg ==''){
				$feedback = "<p class=\"feedback-yellow\">Please fill out all fields.</p>";
			}else{
				//-------------------------
				//Prevents SQL injections
				$name = utf8_encode($mysqli->real_escape_string($name));
				$msg = utf8_encode($mysqli->real_escape_string($msg));
				
				$adminId = isset($_SESSION["userId"])?$_SESSION["userId"]:"NULL";
				
				//-------------------------
				//SQL query
				$query = <<<END
							-- 
							-- Inserts new comment into DB 
							-- 
								INSERT INTO {$tableComment}(commName, commMessage, postId,adminId) 
								VALUES('{$name}', '{$msg}', {$postId},{$adminId});
				
END;

				$mysqli->query($query) or die("Could not query database" . $mysqli->errno . ":" . $mysqli->error);//Performs query
				$feedback = "<p class=\"feedback-green\">Your comment has been added. Thanks!</p>";
				
				$msg = "";
				$name = "";
		
		}
	}	
		$row = $res->fetch_object();//Gets chosen post from DB
		
		//Formats date
		$date = strtotime($row->postTimestamp);
		$date = date("d M Y H : i", $date);
		
		$postName = utf8_decode(htmlspecialchars($row->postName));
		$postMessage = utf8_decode(htmlspecialchars($row->postMessage));
		
		$adminClass = (!is_null($row->adminId))?"admin":"";
		
		$postHTML = <<<END
		
			<h3>Write a comment to:</h3>
			<div class="gb-post{$adminClass}">
				<p class="gb-name">Written by:{$postName}</p>
				<p class="gb-msg">{$postMessage}</p>
				<p class="gb-date">{$date}</p>
			</div>
END;

	$name = isset($_SESSION["username"])?$_SESSION["username"]:$name;

	$name = htmlspecialchars($name);
	$msg = htmlspecialchars($msg);


	$content = <<<END
					<div id="breadcrumbs">
						<p><a href="gb.php">Guestbook</a> &gt; Comment</p>
					</div><!-- breadcrumbs -->
					
					<div id="container">
						{$feedback}
						<form action="gb-comm.php?id={$postId}" method="post">
							<label for="name">Name:</label><br>
							<input type="text" id="name" name="name" placeholder="User Name" value="{$name}" /><br>
							<input type="text" id="address" name="address" /><br>
							<label for="msg">Message:</label><br>
							<textarea id="msg" name="msg">{$msg}</textarea><br>
							<input type="submit" value="submit" /><br>
						</form>
						
						{$postHTML}
	
END;

	//---------------------------
	//SQL query
	$query = <<<END
	--
	-- Gets all comments for chosen post from DB
	--
	SELECT commName, commMessage, commTimestamp, adminId
	FROM {$tableComment}
	WHERE postId = {$postId}
	ORDER BY commTimestamp ASC;

END;

	$res = $mysqli->query($query) or die("Could not query database" . $mysqli->errno . ":" . $mysqli->error);//Performs query
	
	//Loops through results
	while($row = $res->fetch_object()){
		$date = strtotime($row->commTimestamp);
		$date = date("d M Y H:i", $date);
		
		$commName = utf8_decode(htmlspecialchars($row->commName));
		$commMessage = utf8_decode(htmlspecialchars($row->commMessage));
		
		$adminClass = (!is_null($row->adminId))?"admin":"";
		
		$content .= <<<END
			<div class="gb-comm{$adminClass}">
				<p class="gb-name">Written by: {$commName}</p>
				<p class="gb-msg">{$commMessage}</p>
				<p class="gb-date">{$date}</p>
			</div>

END;
	}

	$content .="</div><!-- container -->";
	
	$res->close();//Closes results
	$mysqli->close();//Closes DB connection
}


echo $header;
echo $content;
echo $footer;

?>