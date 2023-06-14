<?php


	$con = mysqli_connect('localhost','root', 'root', 'cryptoinc');
	//check that connection happend
	if (mysqli_connect_errno())
	{
		echo "1"; // error code #1 - Connection failed
		exit();
	}


	$getexchange = "SELECT name, id FROM game_exchange ORDER BY game_exchange.fonds DESC;";


	$exchcheck = mysqli_query($con, $getexchange) or die("2"); // error code #2 - data recovery failed


	echo "0";

	echo "\t";

	foreach ($exchcheck as $exchange) {
		echo $exchange["name"] . "=" . $exchange["id"];
		echo "\t";
	}

	

	

?>