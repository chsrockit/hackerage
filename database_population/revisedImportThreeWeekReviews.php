<?php

set_time_limit(120);

$dbc = @mysqli_connect ("localhost", "testuser", "password", "culturehack") OR die ('Could not connect to MySQL: '. mysqli_connect_error() );

$myFile = "threeweeksreviews.json";
$fh = fopen($myFile, 'r');
$rawJSON = fread($fh, filesize($myFile));
fclose($fh);

$myFile = "threeweeks_matches.txt";
$fh = fopen($myFile, 'r');
$tupletext = fread($fh, filesize($myFile));
fclose($fh);

$tupletext = substr($tupletext, 2, strlen($tupletext)-3);

$tuplearray = array_unique(explode("], [", $tupletext));

$i = 0;

foreach($tuplearray as $tuple) {

	$i += 1;
	echo $i."<br/>";

	$tuple = substr($tuple, 1, strlen($tuple)-2);

	$details = explode("', '",$tuple);

	//print_r($details);


	$full_id = $details[0];
	$review_name = mysql_real_escape_string($details[1]);

	$reviewJSON = json_decode($rawJSON, true);


	foreach($reviewJSON as $currentReview) {
	

		if($currentReview['review']['name'] == $review_name){
	
	
			$review_author = mysql_real_escape_string(substr($currentReview['review']['author'], 1, strlen($currentReview['review']['author']) -2));
			$review_score = substr($currentReview['review']['score'],11,1);
			$review_source = "ThreeWeeks";
			$review_text = mysql_real_escape_string($currentReview['review']['description']);
			$review_url = mysql_real_escape_string($currentReview['review']['url']);

	
			$q = "SELECT id FROM shows WHERE official_id = '$full_id'";
			$r = @mysqli_query ($dbc, $q);
	
		
			// If the show already exists in the 'shows' table, get the appropriate show_id and process as normal.
			if($r && $r->num_rows > 0) {
				$value = $r->fetch_row();
				$review_show = $value[0];
		
			
				$q = "INSERT INTO reviews (show_id, author, score, body_text, source, url, created_at) VALUES ('$review_show', '$review_author', '$review_score', '$review_text', '$review_source', '$review_url', NOW() );";
				$r = @mysqli_query ($dbc, $q);
				
				echo $q;
		
				echo "Success<br/>";

			} else {
				echo "Query failed<br/>";
		
			}
		}
	}

}

?>