<?php
/*----------------------------------
hangman.php
Game of hangman
-----------------------------------*/

include_once("HTMLTemplate.php");

include_once("HangmanSVG.php");


//-- Create an array that you name $words with at least 5 different words

$words = array("mint","perspective","persistence","ambition","struggling","sophisticated");


/*-- get the POST-variables $char, $word and $guessed (with the same names from POST)
	NB! If $word is empty it should be given a random number between 0 and the length of $words - 1
*/

$char = isset($_POST['char'])?$_POST['char']:'';
$word = isset($_POST['word'])?$_POST['word']:mt_rand(0,(count($words)-1));
$guessed = isset($_POST['guessed'])?$_POST['guessed']:'';

$maxWrong = 9;

$guessed = strtoupper($guessed . $char); //Adds last guessed char to char string and makes uppercare

//--Declare the variable $theWord and give it the value of the chosen word from $words (use $word as index)

$theWord = trim($words[$word]);

$new = $theWord;

//Replaces characters with - when it has not been guessed
for($i = 0; $i < strlen($theWord); $i++) {
	if(stripos($guessed, $new[$i]) === FALSE) {
		$new[$i] = "-";		
	}
}
//-- Transform $new to uppercase 

$new = strtoupper($new);

//-- Use strlen and count_chars to count the number of unique guesses. Give this value to $noGuessed

$noGuessed = strlen(count_chars($guessed,3));

//-- Use strlen, count_chars and str_replace("-", "", $new) to count the number of unique, correct guesses. Give this value to $noCorrect

$noCorrect = strlen(count_chars(str_replace("-", "", $new),3));

//-- Use $noGuessed and $noCorrect to calculate the number of failed guesses. Give this value to $noFailed

$noFailed = $noGuessed - $noCorrect;

$guess = "The word: " . $new . " (" . count_chars($guessed, 3) . ")"; //Prints guessed characters;

//If won, redirects to highscore
if (substr_count($new, "-") == 0) {
	header("Location: hangman-highscore.php?sc=" . $noFailed);	
} 
//If failed
$message = ($noFailed == $maxWrong) ? "You killed him... <br />The word was: " . strtoupper($theWord) 	: "";

/*-- Using an if clause, check if the player has made a wrong guess the same amoung of times as $maxWrong. If they have, set $disabled = 'disabled="disabled"'
	if not, set $disabled = ""
 */

 if( $noFailed == $maxWrong ){
	
	$disabled = 'disabled="disabled"';
	
 }else{
	
	$disabled = "";
	
 }
 
 
$content = <<<END

		<div id="container">
			<h2>Hang a man! (or not...)</h2>
			<p>Guess the correct word.</p>
			<br />
			<p>{$guess}</p>
			<form action="hangman.php" method="post" id="hangman">
				<input type="hidden" name="word" value="{$word}" />
				<input type="hidden" name="guessed" value="{$guessed}" />
				<input type="text" name="char" maxlength="1" {$disabled} autofocus />
				<input type="submit"{$disabled} value="Guess"></input>
			</form>
			<p>{$message}</p>
			<p><a href="{$_SERVER['PHP_SELF']}">Restart the game</a></p>
		</div><!-- container -->
			
END;

$content .= getHangmanSVG($noFailed);


echo $header;
echo $content;
echo $footer;

?>