<?php


	$con = mysqli_connect('localhost','root', 'root', 'cryptoinc');
	//check that connection happend
	if (mysqli_connect_errno())
	{
		echo "1"; // error code #1 - Connection failed
		exit();
	}

	$contrat = $_POST["contrat"];
	$user = $_POST["user"];
	$password = $_POST["password"];

	$namecheckquery = "SELECT username,id, argent, hash, salt FROM users WHERE id ='". $user . "';";



	$namecheck = mysqli_query($con, $namecheckquery) or die("2"); // error code #2 - data recovery failed

	if (mysqli_num_rows($namecheck) != 1)
	{
		echo "5"; // Error code #5 - no username in database or more than one
		exit();
	}
	

	$existinginfo = mysqli_fetch_assoc($namecheck);

	$salt = $existinginfo["salt"];
	$hash = $existinginfo["hash"];

	$loginhash = crypt($password, $salt);

	if ($hash != $loginhash)
	{
		echo "6"; // error code #6 - incorrect password
		exit();
	}




	$getpaire = "SELECT contrat_ex.prix, contrat_ex.supply, game_token.name, contrat_ex.type, contrat_ex.leverage_max FROM contrat_ex INNER JOIN game_token ON contrat_ex.token_id = game_token.id WHERE contrat_ex.id = '" . $contrat . "';";

	$gethold = "SELECT trades_ex.quantite, trades_ex.etat, trades_ex.id, trades_ex.prix_achat, trades_ex.liquidation FROM trades_ex WHERE trades_ex.id_contrat = '" . $contrat . "' AND trades_ex.id_user = '" . $user . "' AND trades_ex.etat != 'end';";


	$pairecheck = mysqli_query($con, $getpaire) or die("2"); // error code #2 - data recovery failed
	$holdcheck = mysqli_query($con, $gethold) or die("2"); // error code #2 - data recovery failed

	$paire = mysqli_fetch_assoc($pairecheck);

	if ($paire["type"] == "spot")
	{
		echo "0";
		echo "\t";

		echo $paire["type"];
		echo "\t";


		echo $paire["prix"];
		echo "\t";

		echo $existinginfo["argent"];
		echo "\t";
		echo $paire["name"];

		echo "\t";
		echo $paire["supply"];
		echo "\t";


		$quantite = 0;

		foreach ($holdcheck as $hold) {
			$quantite = $quantite + $hold["quantite"];
		}
		echo $quantite;
	}

	if ($paire["type"] == "derivee")
	{
		echo "0";

		echo "\t";

		echo $paire["type"];
		echo "\t";

		echo $paire["prix"];
		echo "\t";
		echo $existinginfo["argent"];
		echo "\t";
		echo $paire["name"];
		echo "\t";
		echo $paire["supply"];
		echo "\t";
		echo $paire["leverage_max"];
		echo "\t";

		$n = 0;

		if (mysqli_num_rows($holdcheck) < 1)
		{
			exit();
		}

		foreach ($holdcheck as $hold) {



			if ($n == 0 ) {
				echo $hold["id"];
				echo "°";
				echo $hold["prix_achat"];
				echo "°";
				echo $hold["quantite"];
				echo "°";
				echo substr($hold["etat"], 4);
				echo "°";
				if (substr($hold["etat"],0,4) == "long")
				{
					echo "Long";
				}
				else 
				{
					echo "Short";
				}
				echo "°";
				echo $hold["liquidation"];
			}


			if ($n != 0)
			{
				echo "=";
				echo $hold["id"];
				echo "°";
				echo $hold["prix_achat"];
				echo "°";
				echo $hold["quantite"];
				echo "°";
				echo substr($hold["etat"], 4);
				echo "°";
				if (substr($hold["etat"],0,4) == "long")
				{
					echo "Long";
				}
				else 
				{
					echo "Short";
				}
				echo "°";
				echo $hold["liquidation"];
			}

			$n = $n+1;
			
			

		}
		


	}





	

	

?>