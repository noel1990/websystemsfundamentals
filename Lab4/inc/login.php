<?php
/*-----------------------------------------
login.php
checks with the database 
if the user exists 
and if the password is correct
-----------------------------------------*/
//ini_set("error_reporting","E_ALL & ~E_NOTICE"); 
include_once("HTMLTemplate.php");

$username = "";
$password = "";
$feedback = "";

if(!empty($_POST)){
	include_once("connstring.php");
	$table = 'admin';
	$username = isset($_POST['username'])?$_POST['username']:'';
	$password = isset($_POST['password'])?$_POST['password']:'';

	if($username ==''||$password == ''){
		$feedback = "<p class=\"feedback-yellow\">Please fill out all fields.</p>";
	} else {
	//--------------------------
	//Prevents SQL injections
	$username = $mysqli->real_escape_string($username);
	$password = $mysqli->real_escape_string($password);
	
	//---------------------------
	//SQL query
	$query = <<<END
	--
	-- Gets username and password based on user input
	--
	SELECT adminId,adminName,adminPassword
	FROM {$table}
	WHERE adminName = '{$username}';

END;
	
	$res = $mysqli->query($query) or die("Could not query database" . $mysqli->errno . ":" . $mysqli->error);//Performs query
	
	if($res->num_rows == 1){
		$pswmd5 = md5($password);
		$row = $res->fetch_object();
		if($row->adminPassword == $pswmd5){
			//die("login success");
			
			session_start();
			session_regenerate_id();
			
			$_SESSION["username"] = $username;
			$_SESSION["userId"] = $row->adminId;
			
			header("Location: index.php");
			
		}else{
			$feedback = "<p class=\"feedback-red\">Password is incorrect.</p>";
		}
		$res->close();
	}else{
		$feedback = "<p class=\"feedback-red\">Username is incorrect.</p>";
	}
	
	$mysqli->close();
	
	}
}

	$username = htmlspecialchars($username);
	$password = htmlspecialchars($password);

	$content = <<<END
				<div id="container">
					{$feedback}	
				<form action="login.php" method="post" id ="login-form">
					<label for="username">Username:</label><br>
					<input type="text" id="username" name="username" placeholder="User Name" value="{$username}" /><br>	
					<label for="password">Password:</label><br>
					<input type="password" id="password" name="password" value=""/><br>
					<input type="text" id="address" name="address"/><br>	
					<input type="submit" value="Login" />
				</form>

END;

echo $header; 
echo $content; 
echo $footer;


?>