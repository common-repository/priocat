<?php
include("../../../wp-config.php");

function connect() {
if (!mysql_connect(DB_HOST,DB_USER,DB_PASSWORD)):
echo_error("Error - try again later! Couldn't connect to the database.");
endif;
if(!mysql_select_db(DB_NAME)):
echo_error("Error - try again later! Couldn't connect to the database.");
endif;
}

function close() {
if(!mysql_close()):
echo_error("Error - try again later! Couldn't connect to the database.");
endif;
}

function echo_error($fejl) {
echo "<p>$fejl";
if ($mysql_fejl = mysql_error()):
echo "<br>The error is: <em>$mysql_fejl</em>";
endif;
}

if($_POST['data']):
	parse_str($_POST['data']);
	
	$cat_priot = "cat_priot_" . $_REQUEST[cat];
	$ranking = 0;
	
	connect();
	mysql_query("DELETE FROM $_REQUEST[tabel] WHERE meta_key = '$cat_priot'")
	or die(mysql_error());  
	close();
	
	for ($i = 0; $i < count($list_to_sort); $i++):
		
		$list_to_sort[$i] = str_replace("item_", "", $list_to_sort[$i]);
		
		connect();
		
		mysql_query("INSERT INTO $_REQUEST[tabel] (post_id, meta_key, meta_value)
		VALUES ('$list_to_sort[$i]', '$cat_priot', '$ranking')")
		or die(mysql_error());  
		
		close();
		
		$ranking = $ranking + 1;
	endfor;
	
endif;
?>