<?php

	$con = mysqli_connect('localhost','root', 'root', 'cryptoinc');
	//check that connection happend
	if (mysqli_connect_errno())
	{
		echo "1"; // error code #1 - Connection failed
		exit();
	}

	$username = mysqli_real_escape_string($con, $_POST["name"]);
	$usernameclean = filter_var($username, FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW | FILTER_FLAG_STRIP_HIGH);
	$password = $_POST["password"];

	if ($username != $usernameclean)
	{
		echo "8"; // Error code #8 - Username SQL Injection potential attempt
		exit();
	}

	$namecheckquery = "SELECT username, salt, hash, argent, id FROM users WHERE username ='". $usernameclean . "';";

	$namecheck = mysqli_query($con, $namecheckquery) or die("2"); // error code #2 - data recovery failed

	if (mysqli_num_rows($namecheck) != 1)
	{
		echo "5"; // Error code #5 - no username in database or more than one
		exit();
	}

	//Get login infos form query
	$existinginfo = mysqli_fetch_assoc($namecheck);
	$salt = $existinginfo["salt"];
	$hash = $existinginfo["hash"];

	$loginhash = crypt($password, $salt);

	if ($hash != $loginhash)
	{
		echo "6"; // error code #6 - incorrect password
		exit();
	}

	echo "0";
	echo "\t";
	echo $existinginfo["argent"];
	echo "\t";
	echo $existinginfo["id"];


?>