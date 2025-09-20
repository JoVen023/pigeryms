
<?php
include './includes/config.php';
$mode = $_GET['mode'] ?? '';

// Check if the mode is set to 'signup' and add the 'sign-up-mode' class to the container accordingly
if ($mode === 'signup') {
    $containerClass = 'container sign-up-mode';
} else {
    $containerClass = 'container';
}


$signupError = "";

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Check if it's a login or sign-up request
  if (isset($_POST["login"])) {
    // Process login form data
    $username = $_POST["username"];
    $password = $_POST["password"];

    $sql = "SELECT id, EmailId, Password, FullName FROM tblusers WHERE EmailId = :username";
    $query = $dbh->prepare($sql);
    $query->bindParam(':username', $username, PDO::PARAM_STR);
    $query->execute();
    $results = $query->fetch(PDO::FETCH_ASSOC);
    
    if ($results && password_verify($password, $results['Password'])) {
        // Login successful
        $_SESSION['login'] = $_POST['username'];
        $_SESSION['customer'] = $results['id'];
        $_SESSION['fname'] = $results['FullName'];
    
        header("Location: index.php");
        exit();
    } else {
        // Login failed
        $loginError = "Invalid login credentials";
    }
  } elseif (isset($_POST["signup"])) {
    // Process sign-up form data
    $fullName = $_POST["fullName"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $mobileNumber = $_POST["mobileNumber"];
    $dob = $_POST["dob"];
    $address = $_POST["address"];
    $confirmPassword = $_POST["confirmPassword"];
    
    // Validate and sanitize the form inputs
    $isInputValid = true; // Assume inputs are valid by default
    
    // Validate password confirmation
    if ($password !== $confirmPassword) {
      $isInputValid = false;
      $signupError = "Password does not match";
    }
    
    if ($isInputValid) {
     
      // Hash the password
      $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

      // Store the new user in the database
      // Replace the database credentials with your own
      
      // Prepare the SQL statement
      $sql="INSERT INTO  tblusers(FullName,EmailId,ContactNo,dob,Address, Password) VALUES(:fullName,:email,:mobileNumber,:dob,:address,:password)";
      $query = $dbh->prepare($sql);
      $query->bindParam(':fullName',$fullName,PDO::PARAM_STR);
      $query->bindParam(':email',$email,PDO::PARAM_STR);
      $query->bindParam(':mobileNumber',$mobileNumber,PDO::PARAM_STR);
      $query->bindParam(':dob',$dob,PDO::PARAM_STR);
      $query->bindParam(':address',$address,PDO::PARAM_STR);
      $query->bindParam(':password',$hashedPassword,PDO::PARAM_STR);
      $query->execute();
      echo "<script>alert('Registration successfull. Now you can login');  window.location.href = 'login.php';</script>";
      // Redirect the user to a success page or display a success message
      exit();
    }
    
  }
}
?>
<script>
function checkAvailability() {
$("#loaderIcon").show();
jQuery.ajax({
url: "check_availability.php",
data:'emailid='+$("#emailid").val(),
type: "POST",
success:function(data){
$("#user-availability-status").html(data);
$("#loaderIcon").hide();
},
error:function (){}
});
}
</script>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script
      src="https://kit.fontawesome.com/64d58efce2.js"
      crossorigin="anonymous"
    ></script>
    <link rel="shortcut icon" href="assets/images/logos.jpeg">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <link rel="stylesheet" href="style.css" />
    <title>Login/SignUp</title>
  </head>
  <body>
    <div class="<?php echo $containerClass; ?>">
      <div class="forms-container">
        <div class="signin-signup">
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>"  method="post" class="sign-in-form">
            <h2 class="title">Sign in</h2>
            <div class="input-field">
              <i class="fas fa-user"></i>
              <input type="email" name="username" placeholder="Username" required />
            </div>
            <div class="input-field">
              <i class="fas fa-lock"></i>
              <input type="password" id="login-password" name="password" placeholder="Password" required />
  <span class="toggle-password" onclick="togglePasswordVisibility('login-password')">
    <i id="login-password-eye-icon" class="fas fa-eye-slash"></i>
  </span>
</div>

            <?php if (isset($loginError)): ?>
    <p class="error"><?php echo $loginError; ?></p>
  <?php endif; ?>

            <input type="submit" value="Login" name="login" class="btn solid" />
          </form>
          <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" class="sign-up-form" onsubmit="return validatePassword();">
            <h2 class="title">Sign up</h2>
            <div class="input-field">
              <i class="fas fa-user"></i>
              <input type="text" name="fullName" placeholder="Full Name" required/>
            </div>
           
            <div class="input-field">
              <i class="fas fa-envelope"></i>
              <input type="email" name="email" id="emailid" onBlur="checkAvailability()" placeholder="Email Address"required>
              <br>
              <span id="user-availability-status" style="font-size:12px;"></span> 
            </div>   
      
            <div class="input-field">
              <i class="fas fa-phone"></i>
              <input type="text" name="mobileNumber" id="mobilenumber" placeholder="Mobile Number"required oninput="this.value = this.value.replace(/[^0-9]/g, '');" />
            </div>
        
            <div class="input-field">
              <i class="fas fa-calendar"></i>
             
              <input type="text" id="dob" name="dob" placeholder="Date of Birth" required/>

            </div>
            <div class="input-field">
            
              <i class="fas fa-map-marker-alt"></i>
              <input type="text" name="address" placeholder="Address"required/> 
            </div>
            <div class="input-field">
              <i class="fas fa-lock"></i>
              <input type="password" id="signin-password" name="password" placeholder="Password" required />
  <span class="toggle-password" onclick="togglePasswordVisibility('signin-password')">
    <i id="signin-password-eye-icon" class="fas fa-eye-slash"></i>
  </span>
</div>
            <div class="input-field" id="confirm">
              <i class="fas fa-lock"></i>
              <input type="password" id="confirm-password" name="confirmPassword" placeholder="Confirm Password" required />
  <span class="toggle-password" onclick="togglePasswordVisibility('confirm-password')">
    <i id="confirm-password-eye-icon" class="fas fa-eye-slash"></i>
  </span>
</div>
            
       
            <input type="submit" class="btn" name="signup" value="Signup" onclick="return validatePassword();"/>
          </form>



          <br>
          <br>
        </div>
      </div>

      <div class="panels-container">
        <div class="panel left-panel">
          <div class="content">
            <h3>Are you new here?</h3>
            <p>
           Click Sign up to discover the finest selection of healthy and well-cared-for pigs for sale.
            </p>
            <button class="btn transparent" id="sign-up-btn">
              Sign up
            </button>
          </div>
          <img src="img/signlog.svg" class="image" alt="sign-up"/>

        </div>
        <div class="panel right-panel">
          <div class="content">
            <h3>Are you one of us ?</h3>
            <p>
            Sign in now and explore the best deals on high-quality pigs.
            </p>
            <button class="btn transparent" id="sign-in-btn">
              Sign in
            </button>
          </div>
          <img src="img/reglog.svg" class="image" alt="sign in"/>
          
        </div>
      </div>
    </div>
    <script>
      document.getElementById('dob').addEventListener('focus', function (e) {
    e.target.type = 'date';
});
      function togglePasswordVisibility(inputId) {
  var passwordInput = document.getElementById(inputId);
  var eyeIcon = document.getElementById(inputId + "-eye-icon");

  if (passwordInput.type === "password") {
    passwordInput.type = "text";
    eyeIcon.classList.remove("fa-eye-slash");
    eyeIcon.classList.add("fa-eye");
  } else {
    passwordInput.type = "password";
    eyeIcon.classList.remove("fa-eye");
    eyeIcon.classList.add("fa-eye-slash");
  }
}

function validatePassword() {
    var password = document.getElementById("signin-password").value;
    var confirmPassword = document.getElementById("confirm-password").value;
    var confirmPasswordFields = document.getElementById("confirm-password");
    var confirmPasswordField = document.getElementById("confirm");
    var dob = document.getElementById("dob").value;
    var mobile = document.getElementById("mobilenumber").value.trim();

    if (password.length < 8)  {
      alert("Password must be 8  characters or higher");
      return false; 
    }

    if (password !== confirmPassword) {
        alert("Passwords do not match");
        confirmPasswordField.value = "";
        confirmPasswordField.style.border = "2px solid red";
        confirmPasswordField.focus();
        return false;
    } else {
        confirmPasswordField.style.border = ""; 
    }

    var mobileRegex = /^[0-9]{11}$/; 
    if (!mobileRegex.test(mobile)) {
        alert("Mobile number must contain digits only");
        return false;
    }


  if (dob) {
        var today = new Date();
        var birthDate = new Date(dob);
        var age = today.getFullYear() - birthDate.getFullYear();
        var monthDiff = today.getMonth() - birthDate.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birthDate.getDate())) {
            age--;
        }

        if (age < 20) {
            alert("You must be at least 20 years old to sign up");
            return false;
        }
    }

    return true; 
}

    </script>
    
    <script src="assets/js/jquery.min.js"></script>
<script src="assets/js/bootstrap.min.js"></script> 
<script src="assets/js/interface.js"></script> 
    <script src="app.js"></script>
  </body>
</html>