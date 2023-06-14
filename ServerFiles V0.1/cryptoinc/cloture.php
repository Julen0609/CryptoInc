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
	$trade = $_POST["idtrade"];
	$quantite = $_POST["quantite"];

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


	$trades_infoscmd = "SELECT trades_ex.quantite, trades_ex.prix_achat, trades_ex.id_contrat, trades_ex.id_user, trades_ex.etat, contrat_ex.prix, contrat_ex.fees,contrat_ex.max_supply, contrat_ex.supply FROM trades_ex INNER JOIN contrat_ex ON contrat_ex.id = trades_ex.id_contrat WHERE trades_ex.id = '".$trade."';";

	$trade_infos = mysqli_query($con, $trades_infoscmd) or die("2");
 	$infotrd = mysqli_fetch_assoc($trade_infos);



 	if ($infotrd["id_user"] != $user)
	{
		echo "16"; // error code #16 - User is not trade owner
		exit();
	}

	if ($infotrd["etat"] == "end")
	{
		echo "17"; // error code #17 - Trade didn't exist already
		exit();
	}

	if ($infotrd["quantite"] < $quantite)
	{
		echo "13"; // error code #13 - not enough crypto
		exit();
	}

	$quantiterestante = $infotrd["quantite"]-$quantite;
	$leverage = substr($infotrd["etat"],4);

	if (substr($infotrd["etat"],0,4) == "long")
	{
		if ($infotrd["supply"] - $quantite < 0)
		{
			echo "11"; // error code #11 - not enough supply
			exit();
		}

		$roi = $quantite * $infotrd["prix"] - $infotrd["fees"] * $leverage - $infotrd["prix_achat"]* $quantite * (1- 1/ $leverage); //Not Benef, just return on invest

		if ($argent + $roi < 0)
		{
			echo "9"; // error code #9 - not enough money
			exit();
		}

		if ($quantiterestante == 0)
		{
			$modif = "UPDATE trades_ex SET etat = 'end' WHERE trades_ex.id = '".$trade."';";
			$update = "UPDATE users SET argent = '".$argent+$roi."' WHERE id = '".$user."';";
			$updatecontrat = "UPDATE contrat_ex SET supply = '".$infotrd["supply"] - $quantite."' WHERE id = '".$infotrd["id_contrat"]."';";
		}else
		{
			$modif = "UPDATE trades_ex SET quantite = ".$quantiterestante." WHERE trades_ex.id = '".$trade."';";
			$update = "UPDATE users SET argent = '".$argent+$roi."' WHERE id = '".$user."';";
			$updatecontrat = "UPDATE contrat_ex SET supply = '".$infotrd["supply"] - $quantite."' WHERE id = '".$infotrd["id_contrat"]."';";
		}

		mysqli_query($con, $modif) or die ("10"); // error code #10 - data update failed
		mysqli_query($con, $update) or die ("10"); // error code #10 - data update failed
		mysqli_query($con, $updatecontrat) or die ("10"); // error code #10 - data update failed
	}
	else if (substr($infotrd["etat"],0,4) == "shrt")
	{
		if ($infotrd["supply"] + $quantite > $infotrd["max_supply"])
		{
			echo "11"; // error code #11 - not enough supply
			exit();
		}

		$roi = $infotrd["prix_achat"] * $quantite  - $infotrd["fees"] * $leverage - $quantite * $infotrd["prix"] * (1- 1/ $leverage);

		if ($argent + $roi < 0)
		{
			echo "9"; // error code #9 - not enough money
			exit();
		}

		if ($quantiterestante == 0)
		{
			$modif = "UPDATE trades_ex SET etat = 'end' WHERE trades_ex.id = '".$trade."';";
			$update = "UPDATE users SET argent = '".$argent+$roi."' WHERE id = '".$user."';";
			$updatecontrat = "UPDATE contrat_ex SET supply = '".$infotrd["supply"] + $quantite."' WHERE id = '".$infotrd["id_contrat"]."';";
		}else
		{
			$modif = "UPDATE trades_ex SET quantite = ".$quantiterestante." WHERE trades_ex.id = '".$trade."';";
			$update = "UPDATE users SET argent = '".$argent+$roi."' WHERE id = '".$user."';";
			$updatecontrat = "UPDATE contrat_ex SET supply = '".$infotrd["supply"] + $quantite."' WHERE id = '".$infotrd["id_contrat"]."';";
		}

		mysqli_query($con, $modif) or die ("10"); // error code #10 - data update failed
		mysqli_query($con, $update) or die ("10"); // error code #10 - data update failed
		mysqli_query($con, $updatecontrat) or die ("10"); // error code #10 - data update failed

	}
	else 
	{
		echo "18"; // error code #18 - Not short or long 
		exit();
	}

	echo "0";

	

?>