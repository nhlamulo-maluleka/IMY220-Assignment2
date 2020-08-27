<?php
	// See all errors and warnings
	error_reporting(E_ALL);
	ini_set('error_reporting', E_ALL);

	$server = "localhost";
	$username = "root";
	$password = "";
	$database = "dbUser";
	$mysqli = mysqli_connect($server, $username, $password, $database);

	$email = isset($_POST["loginEmail"]) ? $_POST["loginEmail"] : false;
	$pass = isset($_POST["loginPass"]) ? $_POST["loginPass"] : false;	
	// if email and/or pass POST values are set, set the variables to those values, otherwise make them false
	function displayImages($userId)
	{
		global $mysqli;
		echo "<h1>Image Gallery</h1><div class='row'>";
		$images = $mysqli->query("SELECT filename FROM tbgallery WHERE user_id = '$userId'");
		if($images->num_rows > 0)
		{
			while($image = $images->fetch_assoc())
			{
				echo("<div class='col-3' style='position relative; background-image: url(gallery/$image[filename]); background-size: 100% 100%; height: 190px; background-repeat: no-repeat;'></div>");
			}
		}
		else
		{
			echo("<h4 class='ml-3'>You do not yet have images uploaded...</h4>");
		}
		echo "</div>";
	}

	function uploadImages($files, $userId)
	{
		global $mysqli;
		foreach ($files["name"] as $i => $value) 
		{
			$ext = strtolower(pathinfo($files["name"][$i], PATHINFO_EXTENSION));
			if(in_array($ext, array("jpg", "png", "jpeg", "gif")))
			{
				$name = basename($files["name"][$i]);
				if($files["size"][$i] <= 1048576)
				{
					$upload = $mysqli->query("INSERT INTO tbgallery (user_id, filename) VALUES ('$userId', '$name')");
					if($upload)
						move_uploaded_file($files["tmp_name"][$i], "./gallery/$name");
					else
						echo($upload->error);
				}
				else
				{
					echo($name." is bigger than the required size.");
				}
			}
			else
			{
				echo("Invalid File");
			}
		}
		unset($files);
	}

	if(isset($_FILES["picToUpload"]))
	{
		uploadImages($_FILES["picToUpload"], $_POST["userId"]);
	}
?>

<!DOCTYPE html>
<html>
<head>
	<title>IMY 220 - Assignment 2</title>
	<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" integrity="sha384-JcKb8q3iqJ61gNV9KGb8thSsNjpSL0n8PARn9HuZOnIxN0hoP+VmmDGMN5t9UJ0Z" crossorigin="anonymous">
	<link rel="stylesheet" type="text/css" href="style.css" />
	<meta charset="utf-8" />
	<meta name="author" content="Nhlamulo Maluleka">
	<!-- Replace Name Surname with your name and surname -->
</head>
<body>
	<div class="container">
		<?php
			if($email && $pass){
				$query = "SELECT * FROM tbusers WHERE email = '$email' AND password = '$pass'";
				$res = $mysqli->query($query);
				if($row = mysqli_fetch_array($res)){
					echo 	"<table class='table table-bordered mt-3'>
								<tr>
									<td>Name</td>
									<td>" . $row['name'] . "</td>
								<tr>
								<tr>
									<td>Surname</td>
									<td>" . $row['surname'] . "</td>
								<tr>
								<tr>
									<td>Email Address</td>
									<td>" . $row['email'] . "</td>
								<tr>
								<tr>
									<td>Birthday</td>
									<td>" . $row['birthday'] . "</td>
								<tr>
							</table>";
				
					echo 	"<form action='login.php' method='POST' enctype='multipart/form-data'>
								<div class='form-group'>
									<input type='file' class='form-control' name='picToUpload[]' id='picToUpload' multiple/><br/>
									<input type='hidden' name='loginEmail' value='$_POST[loginEmail]'/>
									<input type='hidden' name='loginPass' value='$_POST[loginPass]'/>
									<input type='hidden' name='userId' value='$row[user_id]'/>
									<input type='submit' class='btn btn-standard' value='Upload Image' name='submit' />
								</div>
						  	</form>";
				}
				else{
					echo 	'<div class="alert alert-danger mt-3" role="alert">
	  							You are not registered on this site!
	  						</div>';
				}
				displayImages($row["user_id"]);
			} 
			else{
				echo 	'<div class="alert alert-danger mt-3" role="alert">
	  						Could not log you in
	  					</div>';
			}
		?>
	</div>
</body>
</html>