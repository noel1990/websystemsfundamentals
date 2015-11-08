<?php
/*-------------------------
hangman-highscore.php
Adds to and shows highscore for hangman
-------------------------*/
include_once("HTMLTemplate.php");

date_default_timezone_set('UTC');

if(!empty($_POST)) {

	
	/*-- Get the POST-variables name and score to the variables with the same name */
	
	$name = isset($_POST['name'])? $_POST['name']:'';
	$score = isset($_POST['score'])? $_POST['score']:'';
	
	/*-- Make sure that there is a score and a name, otherwise, give feedback.
		If both $name and $score have a value, continue with the following:
	*/
	
	if($name ==''||$score == ''){
	
		$content=<<<END
		<div id="container">
			<p>There is something wrong. Please try again.</p>
			<p><a href="hangman-highscore.php">Back</a></p>
		</div><!-- container -->
END;

	}else{

		include_once("connstring.php");
		$tableHighscore = "highscore";
		
		/*-- Prevent SQL-injections on $name and $score */

		$name = $mysqli->real_escape_string($name);
		$score = $mysqli->real_escape_string($score);
		
		
		$query = <<<END
			--
			-- Inserts new score into DB
			--
			INSERT INTO {$tableHighscore}(name, score) 
			VALUES('{$name}', '{$score}');
END;

		//-- Perform the query
		
		$res = $mysqli->query($query) or die("Could not query database". $mysqli->errno .":". $mysqli->error);//Performs query
		
		//------------------------
		//SQL query
		$query = <<<END
		--
		-- Gets all posts from DB
		-- 
		SELECT name, score, dateAdded
		FROM {$tableHighscore}
		ORDER BY score ASC;

END;

		//-- Perform the query
		
		$res = $mysqli->query($query) or die("Could not query database". $mysqli->errno .":". $mysqli->error);//Performs query
		
		$content = <<<END
		
			<div id="breadcrumbs">
				<p><a href="hangman.php">Game</a> &gt; Highscore</p>
			</div><!-- breadcrumbs -->
			<div id="container">
				<ol>
		
END;
	
		/*-- Using a while loop, go through the results from the query.
			Format the date to something more pleasant that the default.
			Don't forget to use htmlspecialchars() and utf8_decode()
			Add the results to $content in the list (a new <li> for each post)
		*/
		
		//Loops through results
		while($row = $res->fetch_object()){
		$date = strtotime($row->dateAdded);
		$date = date("d M Y H:i", $date);
		
		$name = utf8_decode(htmlspecialchars($row->name));
		$score = utf8_decode(htmlspecialchars($row->score));
		
		$content .= <<<END
			<div class="rank">
				<li>
					<tab>{$name}</tab>
					<tab>{$score}</tab>
					<tab>{$date}</tab>
				</li>
			</div>

END;
	
		}
		
		/*-- Close $res and $mysqli */
		
		$res->close();//Closes results
		$mysqli->close();//Closes DB connection
				
		$content .= '</ol></div><!-- container -->';		
		
		}


} else {
	//-- Get the GET-variable sc to the variable $score
	
	$score = isset($_GET['sc'])? $_GET['sc']:'';
	
	/*-- Make sure that $score is not empty. If it is, give appropriate feedback. If it isn't, continue with the following:
	*/
		if($score == ''){
		$content=<<<END
		<div id="container">
			<p>There is something wrong. Please try again.</p>
			<p><a href="hangman-highscore.php">Back</a></p>
		</div><!-- container -->
END;

		}else{
	
		//-- Use htmlspecialchars on $score
		
		$score = htmlspecialchars($score);
		
		}
	
		$content = <<<END
			<div id="breadcrumbs">
				<p><a href="hangman.php">Game</a> &gt; Highscore</p>
			</div><!-- breadcrumbs -->
	
			<div id="container">
				<h2>Congrats!</h2>
				<p>You guessed correctly and saved the man. You made {$score} wrong guesses before you got the right word. Enter your name below for the highscore list.</p>
				<br />
				<form action="hangman-highscore.php" method="post">
					<label for="name">Enter your name:</label>
					<input type="text" name="name" id="name"/>
					<input type="hidden" name="score" value="{$score}" />
					<input type="submit" value="Add to highscore" />
				</form>
			</div><!-- container -->
			
END;
	
}

echo $header;
echo $content;
echo $footer;

?>