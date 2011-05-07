<?php

$dbc = @mysqli_connect ("localhost", "testuser", "password", "culturehack") OR die ('Could not connect to MySQL: '. mysqli_connect_error() );

$myFile = "guardianreviews.json";
$fh = fopen($myFile, 'r');
$rawJSON = fread($fh, filesize($myFile));
fclose($fh);

$myFile = "guardian_matches.txt";
$fh = fopen($myFile, 'r');
$tupletext = fread($fh, filesize($myFile));
fclose($fh);

$tupletext = substr($tupletext, 2, strlen($tupletext)-3);

$tuplearray = explode("], [", $tupletext);

foreach($tuplearray as $tuple) {

	$tuple = substr($tuple, 1, strlen($tuple)-2);

	$details = array_unique(explode("', '",$tuple));

	print_r($details);
	echo "<BR/>";

$full_id = $details[0];
$review_name = mysql_real_escape_string($details[1]);

$reviewJSON = json_decode($rawJSON, true);


foreach($reviewJSON as $currentReview) {

	if($currentReview['name'] == $review_name){
	
	$review_author = mysql_real_escape_string($currentReview['author']); 
	$review_score = $currentReview['star rating'];
	$review_source = "Guardian";
	$review_text = mysql_real_escape_string($currentReview['bodytext']);
	$review_url = mysql_real_escape_string($currentReview['link']);
	
	$q = "SELECT id FROM shows WHERE official_id = '$full_id'";
	
	$r = @mysqli_query ($dbc, $q);
	
		
	// If the show already exists in the 'shows' table, get the appropriate show_id and process as normal.
	if($r && $r->num_rows > 0) {
		$value = $r->fetch_row();
		$review_show = $value[0];
		
		$q = "INSERT INTO reviews (show_id, author, score, body_text, source, url, created_at) VALUES ('$review_show', '$review_author', '$review_score', '$review_text', '$review_source', '$review_url', NOW() )";
		$r = @mysqli_query ($dbc, $q);
		
		echo "Success<br/>";

	} else {
		echo "Failed with " . $review_name . "<br/>";

	}
	}
	}
	
	
	


}

?>