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

	//check if name exist

	$namecheckquery = "SELECT username FROM users WHERE username ='".$username."';";

	$namecheck = mysqli_query($con, $namecheckquery) or die("2"); // error code #2 - data recovery failed

	if (mysqli_num_rows($namecheck) > 0 )
	{
		echo "3"; // error code #3 - name exist cannot register
		exit();
	}

	//add user to the table 

	$salt = "\$5\$rounds=5000\$" . "steamedhams" . $username . "\$";
	$hash = crypt($password, $salt);
	$insertuserquery = "INSERT INTO users (username, hash, salt) VALUES ('" . $username . "', '" . $hash . "', '" . $salt . "');";
	mysqli_query($con, $insertuserquery) or die("4"); //error code #4 - insert query failed

	echo ("0");

	
 

?>