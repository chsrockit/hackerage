<?php

$dbc = @mysqli_connect ("localhost", "testuser", "password", "culturehack") OR die ('Could not connect to MySQL: '. mysqli_connect_error() );

$myFile = "listreviews.json";
$fh = fopen($myFile, 'r');
$rawJSON = fread($fh, filesize($myFile));
fclose($fh);

$myFile = "list_matches.txt";
$fh = fopen($myFile, 'r');
$tupletext = fread($fh, filesize($myFile));
fclose($fh);

$tupletext = substr($tupletext, 2, strlen($tupletext)-3);

$tuplearray = explode("], [", $tupletext);

foreach($tuplearray as $tuple) {

$tuple = substr($tuple, 1, strlen($tuple)-2);

$details = explode("', '",$tuple);

print_r($details);


$full_id = $details[0];
$review_name = mysql_real_escape_string($details[1]);



$reviewJSON = json_decode($rawJSON, true);


foreach($reviewJSON as $currentReview) {

	echo $currentReview['review']['name'] . "<br/>";
	echo $review_name . "<br/>";
	echo "<br/>";

	if($currentReview['review']['name'] == $review_name){
	
	$review_author = $currentReview['review']['author']; 
	$review_score = $currentReview['review']['score'];
	$review_source = $currentReview['review']['source'];
	$review_text = $currentReview['review']['actual_review'];
	$review_url = $currentReview['review']['url'];
	
	$q = "SELECT id FROM shows WHERE official_id = '$full_id'";
	
	$r = @mysqli_query ($dbc, $q);
	
		
	// If the show already exists in the 'shows' table, get the appropriate show_id and process as normal.
	if($r && $r->num_rows > 0) {
		$value = $r->fetch_row();
		$review_show = $value[0];
		
		echo "Show: " . $review_show . "<br/>";
		echo "Author: " . $review_author . "<br/>";
		echo "Score: " . $review_score . "<br/>";
		echo "Text: " . $review_text . "<br/>";
		echo "Source: " . $review_source . "<br/>";
		echo "URL: " . $review_url . "<br/>";
		echo "<br/>";
		
		$q = "INSERT INTO reviews (show_id, author, score, body_text, source, url, created_at) VALUES ('$review_show', '$review_author', '$review_score', '$review_text', '$review_source', '$review_url', NOW() )";
		$r = @mysqli_query ($dbc, $q);
		
		echo "Success<br/>";

	} else {
		echo "Failed with " . $review_name . "<br/>";
		
		/*$q = "SELECT id FROM shows WHERE description LIKE '%$review_name%'";
		$r = @mysqli_query ($dbc, $q);
		
		if($r && $r->num_rows > 0) {
			$value = $r->fetch_row();
			$review_show = $value[0];
		
			$q = "INSERT INTO reviews (show_id, author, score, body_text, source, url, created_at) VALUES ('$review_show', '$review_author', '$review_score', '$review_text', '$review_source', '$review_url', NOW() )";
			$r = @mysqli_query ($dbc, $q);
		
			echo "Success<br/>";
		
		// Log that shit.
	}*/
	}
	}
	}
	
	
	


}

?>