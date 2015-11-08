<?php
/*----------------------------
gb-delete.php
Deletes chosen post or comment from DB
----------------------------*/

include_once("HTMLTemplate.php");

if(!isset($_SESSION["username"])){
	header("Location: index.php");
	exit();
}

include_once("connstring.php");

$tablePost = "post";
$tableComment = "comment";

$postId = isset($_GET['pid'])?$_GET['pid']:'';
$commId = isset($_GET['cid'])?$_GET['cid']:'';
$s = isset($_GET['s'])?$_GET['s']:'';

if($postId == '' && $commId == ''){
	$content = <<<END
			<div id="breadcrumbs">
				<p><a href="gb.php">Guestbook</a>&gt; Delete</p>
			</div><!-- breadcrumbs -->
			<div id="container">
				<p>No post or comment has been choosen. Please try again.</p>
			</div><!-- container -->
	
END;

}else if($s == "y"){
	$query = "";
	
	if($postId != ''){
		$postId = $mysqli->real_escape_string($postId);
		$type = "post";
		
		//-----------------------------
			//SQL query
			$query = <<<END
			-- 
			-- Deletes chosen post
			-- 
			DELETE FROM {$tablePost} 
			WHERE postId = {$postId};
			
END;

	}else if ($commId !=''){
		$commId = $mysqli->real_escape_string($commId);
		$type = "comment";
		
		//---------------------------
			//SQL query
			$query = <<<END
			--
			-- Deletes chosen comment 
			-- 
			DELETE FROM {$tableComment} 
			WHERE commentId = {$commId};
END;
	}
	
	$mysqli->query($query)or die("Could not query database" . $mysqli->errno .":" . $mysqli->error);//Performs query
	
	if($mysqli->affected_rows >=1){
		$feedback = "The {$type} has been removed.";
	}else{
		$feedback = "Something went wrong and the {$type} was not removed.";
	}
	
	$mysqli->close();
	
	$content = <<<END
			<div id="breadcrumbs">
				<p><a href="gb.php">Guestbook</a>&gt; Delete</p>
			</div><!-- breadcrumbs -->
			<div id="container">
				<p>{$feedback}</p>
				<p><a href="gb.php">Back to guestbook</a></p>
			</div><!-- container -->
	
END;

}else{
	$type = ($postId != '')?"post":"comment";
	
	$content = <<<END
			<div id="breadcrumbs">
				<p><a href="gb.php">Guestbook</a>&gt; Delete</p>
			</div><!-- breadcrumbs -->
			<div id="container">
				<p>Are you sure you want to remove the chosen {$type}?</p>
				<p><a href="gb-delete.php?pid={$postId}&cid={$commId}&s=y">Yes</a>&nbsp&middot&nbsp<a href="gb.php">No</a></p>
			</div><!-- container -->
	
END;
}

echo $header;
echo $content;
echo $footer;

?>