<?php
/*--------------------------
gb-edit.php
Allows editing of chosen post or comment
--------------------------*/

include_once("HTMLTemplate.php");

if(!isset($_SESSION["username"])){
	header("Location: index.php");
	exit();
}

include_once("connstring.php");
$tablePost = "post";
$tableComment = "comment";

$feedback = "";
$name = "";
$msg = "";


$postId = isset($_GET['pid'])?$_GET['pid']:'';
$commId = isset($_GET['cid'])?$_GET['cid']:'';

$type = ($postId != '')?"post":"comment";


if(!empty($_POST)){

	//Edit post or comment in DB
	
	$name = isset($_POST['name'])?$_POST['name']:'';
	$msg = isset($_POST['msg'])?$_POST['msg']:'';
	
	if($name ==''||$msg == ''){
	
		$feedback = "<p class=\"feedback-yellow\">Please fill out all fields.</p>";
	} else {
		//---------------------------------
		//Prevents SQL injections
		$name = utf8_encode($mysqli->real_escape_string($name));
		$msg = utf8_encode($mysqli->real_escape_string($msg));
		
		$query = "";

		if($postId == '' && $commId == ''){
			$content = <<<END
			<div id="breadcrumbs">
				<p><a href="gb.php">Guestbook</a>&gt; Edit</p>
			</div><!-- breadcrumbs -->
			<div id="container">
				<p>No post or comment has been choosen. Please try again.</p>
			</div><!-- container -->
	
END;

		}else if($postId != ''){
			$postId = $mysqli->real_escape_string($postId);
			$type = "post";
			
			//-----------------------------
				//SQL query
				$query = <<<END
				-- 
				-- Changes chosen post in DB 
				-- 
				UPDATE {$tablePost} 
				SET postName = '{$name}', postMessage = '{$msg}' 
				WHERE postId = {$postId};
				
END;

		}else if ($commId !=''){
			$commId = $mysqli->real_escape_string($commId);
			$type = "comment";
			
			//---------------------------
				//SQL query
				$query = <<<END
				--
				-- Changes chosen comment in DB 
				-- 
				UPDATE {$tableComment} 
				SET commName = '{$name}', commMessage = '{$msg}' 
				WHERE commentId = {$commId};
END;

	$content = 'cid';

	}
	
	$mysqli->query($query)or die("Could not query database" . $mysqli->errno .":" . $mysqli->error);//Performs query
	
	if($mysqli->affected_rows >=1){
		$feedback = "The {$type} has been changed.";
	}else{
		$feedback = "You haven't changed any thing so the {$type} was not changed.";
	}
	
	$mysqli->close();
	
	$content = <<<END
			<div id="breadcrumbs">
				<p><a href="gb.php">Guestbook</a>&gt; Edit</p>
			</div><!-- breadcrumbs -->
			<div id="container">
				<p>{$feedback}</p>
				<p><a href="gb.php">Back to guestbook</a></p>
			</div><!-- container -->
	
END;

	}
}else{

	if($type == "post"){
		$query = <<<END
		-- 
		-- Gets chosen post from DB
		-- 
		SELECT postId, postName, postMessage, postTimestamp, adminId
		FROM {$tablePost}
		WHERE postId ={$postId};
		
END;
		
	}else{
		$query = <<<END
		-- 
		-- Gets chosen comment from DB
		-- 
		SELECT commentId, commName, commMessage, commTimestamp, adminId
		FROM {$tableComment}
		WHERE commentId = {$commId};
		
END;
		
	}
	
	$res = $mysqli->query($query) or die("Could not query database". $mysqli->errno .":". $mysqli->error);//Performs query
	
	if($res->num_rows<1){
	$content=<<<END
		<div id="container">
			<p>The {$type} you chose cannot be found. Please try again.</p>
			<p><a href="gb.php">Guestbook</a></p>
		</div><!-- container -->
END;

	}else{
		$row = $res->fetch_object();//Gets result from DB
		
		$name = ($type == "post") ? $row->postName :$row->commName;
		$msg = ($type == "post") ? $row->postMessage : $row->commMessage;
		
		$name = utf8_decode($name);
		$msg = utf8_decode($msg);
		
		$content = getFormHTML($type, $postId, $commId, $name, $msg, $feedback);
	}
}

echo $header;
echo $content;
echo $footer;

function getFormHTML($type, $postId, $commId, $name, $msg, $feedback){
	
	$name = htmlspecialchars($name);
	$msg = htmlspecialchars($msg);
	
	return<<<END
		 <div id = "breadcrumbs">
			<p><a href="gb.php">Guestbook</a>&gt; Edit</p>
		 </div><!-- breadcrumbs -->
		 
		 <div id="container">
			<h2>Edit the chosen {$type}</h2>
			{$feedback}
			<form action="gb-edit.php?pid={$postId}&cid={$commId}" method="post">
				<lable for="name">Name:</lable><br>
				<input type="text" id="name" name="name" value="{$name}" /><br>
				<lable for="msg">Message:</lable><br>
				<textarea id="msg" name="msg">{$msg}</textarea><br>
				<input type="submit" value="Save changes" />
			</form>
		 </div><!-- container -->
END;

}

?>