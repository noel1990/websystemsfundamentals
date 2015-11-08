<?php
/*-----------------------------
contact.php
Shows contact form and
sends e-mail after submit
-----------------------------*/

include_once("HTMLTemplate.php");

if(!empty($_POST)){

	foreach($_POST as $value) { 
		if(stripos($value, 'Content-Type:') !== FALSE) { 
					echo "There was a problem. Try again."; 
					exit; 
		} 
}

	$name = isset($_POST['name'])?$_POST['name']:'';
	$email = isset($_POST['email'])?$_POST['email']:'';
	$msg = isset($_POST['msg'])?$_POST['msg']:'';
	$address=isset($_POST['address'])?$_POST['address']:'';
	
if( !$address == '' ){
	die("I think you're a robot. If you're not, go back and try again.");
}	
	
	if($name ==''||$email == ''|| $msg ==''){
		$form = formHTML($name, $email, $msg);
		$content = <<<END
		
			<div id="container">
			<p>Please fill out all fields.</p>
				{$form}
			</div><!-- container -->
		
END;
	}else{
		$to = "qiwan12@student.hh.se";
		$subject = "Message from my webpage. Sender:" . $name;
		$headers = "MIME-Version: 1.0" . "\r\n"; 
		$headers .= "Content-type:text/html;charset=utf-8" . "\r\n"; 
		$headers .= "From: {$email}" . "\r\n"; 
		$headers .= "Reply-To: {$email}";
		
	
		if(mail($to, $subject, $msg, $headers)){
		$content = <<<END
		<div id="container">
			<p>Your message has been sent. Thank you!</p>
		</div><!-- container -->

END;
	}else{
	 $content = <<<END
	 
	 <div id="container">
		<p>I'm sorry, something went wrong when sending your e-mail. Please try again.</p>
		<p><ahref="contact.php">Back to contact page.</a></p>
	 </div><!-- container -->

END;
	}
		
	}
} else {
	$form = formHTML();
	$content = <<<END
	
		<div id="container">
			{$form}
		</div><!-- container -->

END;

}

echo $header; 
echo $content; 
echo $footer;

//---------------------------------------
//Returns HTML for contact form
function formHTML($name = "", $email = "", $msg = ""){
	$name = htmlspecialchars($name);
	$email = htmlspecialchars($email);
	$msg = htmlspecialchars($msg);
	return<<<END
			<form action="contact.php" method="post">
				<label for="name">Name:</label><br>
				<input type="text" id="name" name="name" value="{$name}"/><br>	
				<label for="email">E-mail:</label><br>
				<input type="text" id="email" name="email" value="{$email}"/><br>
				<input type="text" id="address" name="address"/><br>	
				<label for="msg">Message:</label><br>
				<textarea id="msg" name="msg">{$msg}</textarea><br>
				<input type="submit" value="Submit" />
			</form>

END;
		
}

?>