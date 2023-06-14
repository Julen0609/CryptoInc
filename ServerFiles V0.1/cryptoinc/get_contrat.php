<?php


	$con = mysqli_connect('localhost','root', 'root', 'cryptoinc');
	//check that connection happend
	if (mysqli_connect_errno())
	{
		echo "1"; // error code #1 - Connection failed
		exit();
	}

	$exchange = $_POST["exchange"];

	$getcontrat = "SELECT game_token.sigle, contrat_ex.id FROM game_token INNER JOIN contrat_ex ON game_token.id = contrat_ex.token_id INNER JOIN game_exchange ON contrat_ex.exchange_id = game_exchange.id WHERE game_exchange.id = '". $exchange ."' ORDER BY game_token.price DESC;";



	$contratcheck = mysqli_query($con, $getcontrat) or die("2"); // error code #2 - data recovery failed


	echo "0";

	echo "\t";

	foreach ($contratcheck as $contrat) {
		echo $contrat["sigle"] . "=" . $contrat["id"];
		echo "\t";
	}

	

	

?>