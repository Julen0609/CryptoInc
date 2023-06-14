<?php

	$con = mysqli_connect('localhost','id20727352_root', 'qGkyfx~ZiR7F^CXS', 'id20727352_unitytest');
	//check that connection happend
	if (mysqli_connect_errno())
	{
		echo "1 : Connection failed"; // error code #1 = Connection failed
		exit();
	}

	$username = $_POST["name"];
	$newscore = $_POST["score"];

	$namecheckquery = "SELECT username FROM players WHERE username='". $username . "';";

	$namecheck = mysqli_query($con, $namecheckquery) or die("2 : Name Check query failed"); // error code #2 = name check query failed

	if (mysqli_num_rows($namecheck) != 1)
	{
		echo "5 : Either no user with name or more than 1"; // Error code #5 - no name or more than one.
		exit();
	}

	$updatequery = "UPDATE players SET score = " . $newscore . " WHERE username = '" . $username . "';";
	mysqli_query($con, $updatequery) or die("7: Save query failed"); //error code #7 - UPDATE query failed

	echo "0";




?>