<?php

	$con = mysqli_connect('localhost','root', 'root', 'cryptoinc');
	//check that connection happend
	if (mysqli_connect_errno())
	{
		echo "1"; // error code #1 - Connection failed
		exit();
	}

	$user = $_POST["iduser"];
	$password = $_POST["password"];
	$contrat = $_POST["idcontrat"];
	$quantite = $_POST["quantite"];
	$transaction = $_POST["transaction"];
	if ($transaction == "short" or $transaction == "long")
	{
		$levier = $_POST["levier"];
	}
	else
	{
		$levier = 1;
	}



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

	$argent = $existinginfo["argent"];


	$getcontratinfo = "SELECT contrat_ex.prix, contrat_ex.fees, contrat_ex.supply, contrat_ex.max_supply, contrat_ex.type, contrat_ex.leverage_max FROM contrat_ex WHERE contrat_ex.id = '".$contrat."' ;"; 


	$contratest = mysqli_query($con, $getcontratinfo) or die("2"); // error code #2 - data recovery failed


	$contratinfo = mysqli_fetch_assoc($contratest);

	$prix = $contratinfo["prix"] * $quantite / $levier + ($contratinfo["fees"]* $levier) ;
	$supply = $contratinfo["supply"];
	$max_supply = $contratinfo["max_supply"];
	$type = $contratinfo["type"];
	$levier_max = $contratinfo["leverage_max"];


		

	if ($type == "spot")
	{



		if ($transaction == "buy")
		{
			if ($supply - $quantite < 0)
			{
				echo "11"; // error code #11 - not enough supply
				exit();
			}
			$supply = $supply - $quantite;

			if ($argent - $prix <0)
			{
				echo "9"; // error code #9 - not enough money
				exit();
			}


			$insertion = "INSERT INTO trades_ex (id_contrat, id_user, prix_achat, etat, quantite) VALUES ('".$contrat."','".$existinginfo["id"]."','".$contratinfo["prix"]."','spot','".$quantite."');";

			$update = "UPDATE users SET argent = '".$argent-$prix."' WHERE id = '".$user."';";
			$updatecontrat = "UPDATE contrat_ex SET supply = '".$supply."' WHERE id = '".$contrat."';"; 

			mysqli_query($con, $insertion) or die ("4"); // error code #4 - data insertion failed
			mysqli_query($con, $update) or die ("10"); // error code #10 - data update failed
			mysqli_query($con, $updatecontrat) or die ("10"); // error code #10 - data update failed
		}

		if ($transaction == "sell")
		{
			if ($supply + $quantite > $max_supply)
			{
				echo "12"; // error code #12 - up to max supply
				exit();
			}

			$gethold = "SELECT trades_ex.quantite FROM trades_ex WHERE trades_ex.id_user = '".$user."' AND trades_ex.id_contrat = '".$contrat."';";


			$holdcheck = mysqli_query($con, $gethold) or die("2"); // error code #2 - data recovery failed

			$temphold = 0;

			foreach ($holdcheck as $hold)
			{
				$temphold = $temphold + $hold["quantite"];
			}

			if ($temphold - $quantite <0)
			{
				echo "13"; // error code #13 - not enough crypto
				exit();
			}


			$supply = $supply + $quantite;

			$gettrades = "SELECT trades_ex.quantite, trades_ex.id, trades_ex.prix_achat FROM trades_ex WHERE trades_ex.id_user = '".$user."' AND trades_ex.id_contrat = '".$contrat."' AND trades_ex.etat != 'end';";


			$trades = mysqli_query($con, $gettrades) or die ("2"); // error code #2 - data recovery failed


			while ($quantite !=0)
			{

				$trd = mysqli_fetch_array($trades, MYSQLI_ASSOC);
				if ($trd["quantite"] > $quantite)
				{
					$modif = "UPDATE trades_ex SET quantite = '".$trd["quantite"] - $quantite."', prix_achat = '".$trd["prix_achat"] - $prix."' WHERE trades_ex.id = '".$trd["id"]."';";
					mysqli_query($con, $modif) or die ("10"); // error code #10 - data update failed
					$quantite = 0;
				}
				if ($trd["quantite"] <= $quantite)
				{

					$quantite = $quantite - $trd["quantite"];
					$modif = "UPDATE trades_ex SET etat = 'end' WHERE trades_ex.id = '".$trd["id"]."';";
					mysqli_query($con, $modif) or die ("10"); // error code #10 - data update failed
					$update = "UPDATE users SET argent = '".$argent+$prix."' WHERE id = '".$user."';";
					$updatecontrat = "UPDATE contrat_ex SET supply = '".$supply."' WHERE id = '".$contrat."';";

					mysqli_query($con, $update) or die ("10"); // error code #10 - data update failed
					mysqli_query($con, $updatecontrat) or die ("10"); // error code #10 - data update failed
				}	
			}
		}
	}
	elseif ($type == "derivee") 
	{
		

		if ($levier <= $levier_max and $levier >=1)
		{
			if ($transaction == "long")
			{
				if ($supply - $quantite < 0)
				{
					echo "11"; // error code #11 - not enough supply
					exit();
				}
				$supply = $supply - $quantite;

				if ($argent - $prix <0)
				{
					echo "9"; // error code #9 - not enough money
					exit();
				}



				$insertion = "INSERT INTO trades_ex (id_contrat, id_user, prix_achat, etat, quantite, liquidation) VALUES ('".$contrat."','".$existinginfo["id"]."','".$contratinfo["prix"]."','long".$levier."','".$quantite."', '".$contratinfo["prix"]-$contratinfo["prix"]/$levier."' );";

				$update = "UPDATE users SET argent = '".$argent-$prix."' WHERE id = '".$user."';";
				$updatecontrat = "UPDATE contrat_ex SET supply = '".$supply."' WHERE id = '".$contrat."';"; 

				mysqli_query($con, $insertion) or die ("4"); // error code #4 - data insertion failed
				mysqli_query($con, $update) or die ("10"); // error code #10 - data update failed
				mysqli_query($con, $updatecontrat) or die ("10"); // error code #10 - data update failed
			}
			if ($transaction == "short")
			{
				if ($supply + $quantite > $max_supply)
				{
					echo "11"; // error code #11 - not enough supply
					exit();
				}
				$supply = $supply + $quantite;

				if ($argent - $prix <0)
				{
					echo "9"; // error code #9 - not enough money
					exit();
				}


				$insertion = "INSERT INTO trades_ex (id_contrat, id_user, prix_achat, etat, quantite, liquidation) VALUES ('".$contrat."','".$existinginfo["id"]."','".$contratinfo["prix"]."','shrt".$levier."','".$quantite."', '".$contratinfo["prix"]+$contratinfo["prix"]/$levier."' );";

				$update = "UPDATE users SET argent = '".$argent-$prix."' WHERE id = '".$user."';";
				$updatecontrat = "UPDATE contrat_ex SET supply = '".$supply."' WHERE id = '".$contrat."';"; 

				mysqli_query($con, $insertion) or die ("4"); // error code #4 - data insertion failed
				mysqli_query($con, $update) or die ("10"); // error code #10 - data update failed
				mysqli_query($con, $updatecontrat) or die ("10"); // error code #10 - data update failed
			}
		}
		else
		{
			echo "15"; // error code #15 -  wrong leverage
			exit();
		}
	}
	else
	{
		echo "14"; // error code #14 - wrong trade type
		exit();
	}


		
	







	



	echo "0";

?>

