<?php

include './includes/config.php';
if(isset($_POST['login']))
{
$email=$_POST['username'];
$password=md5($_POST['password']);
$sql ="SELECT UserName,Password FROM admin WHERE UserName=:email and Password=:password";
$query= $dbh -> prepare($sql);
$query-> bindParam(':email', $email, PDO::PARAM_STR);
$query-> bindParam(':password', $password, PDO::PARAM_STR);
$query-> execute();
$results=$query->fetchAll(PDO::FETCH_OBJ);
if($query->rowCount() > 0)
{
$_SESSION['alogin']=$_POST['username'];
echo "<script type='text/javascript'> document.location = 'dashboard.php'; </script>";
} else{

  echo "<script>alert('Invalid Details');</script>";

}

}

?>
<!doctype html>
<html lang="en" class="no-js">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, minimum-scale=1, maximum-scale=1">
	<meta name="description" content="">
	<meta name="author" content="">

	<title>Admin Login</title>
	<link rel="stylesheet" href="style.css">
	<link rel="icon" type="image/x-icon" href="img/logos.jpeg">
	<link href="https://fonts.googleapis.com/css?family=Poppins:600&display=swap" rel="stylesheet">
	<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.0.7/css/all.css">
	
</head>


<body>
		<img class="green" src="img/green.png" alt="green">
			<div class="wrapper">
				<div class="image">
					<img src="img/girl.svg">
				</div>
								
				<div class="loginan">
					<form method="post">
						<img src="img/logos.jpeg" alt="pig">

						<h1 class="okie">Welcome</h1>

						<div class="input-div user">
							<div class="icon">
								<i class="fas fa-user"></i>
							</div>
							<div class="div">
								<h5> Username </h5>
								<input type="username"  name="username" class="input" required>
							</div>
						</div>

						<div class="input-div pass">
							<div class="icon">
								<i class="fas fa-lock"></i>
							</div>
							<div class="div">
								<h5> Password </h5>
								<input type="password" name="password" class="input" required>
							</div>
						</div>

						<button class="btn-login" name="login" type="submit">LOG IN</button>

					</form>
				</div>
		</div>
				


	<script>

	const inputDivs = document.querySelectorAll('.input-div');

	inputDivs.forEach(inputDiv => {
  		inputDiv.addEventListener('click', () => {
    		inputDiv.classList.toggle('active');
   		});
	});
	</script>

</body>

</html>
