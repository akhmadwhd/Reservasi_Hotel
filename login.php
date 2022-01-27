<?php

include('./admin/db_connect.php');

error_reporting(0);

session_start();

if (isset($_SESSION['username'])) {
    header("location:index.php?page=home");
}

if (isset($_POST['submit'])) {
    $email = $_POST['email'];
    $password = md5($_POST['password']);
    $sql = "SELECT * FROM user_konsumer WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);
    if ($result->num_rows > 0) {
        if (!empty($_POST["remember"])){
          setcookie ("email",$_POST["email"],time()+ 3600);
          setcookie ("username",$_POST["username"],time()+ 3600);
          setcookie ("password",$_POST["password"],time()+ 3600);
          echo "Cookies Set Successfuly";
          $row = mysqli_fetch_assoc($result);
          $_SESSION['username'] = $row['username'];
          $_SESSION['email'] = $row['email'];
          header("location:index.php?page=home");
        } else {
          $row = mysqli_fetch_assoc($result);
          $_SESSION['username'] = $row['username'];
          $_SESSION['email'] = $row['email'];
          header("location:index.php?page=home");
        }
    } else {
        echo "<script>alert('Email atau password Anda salah. Silahkan coba lagi!')</script>";
    }
}

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="style.css">

    <title>Amelia Hotel</title>
</head>
<body>
    <section>
          <?php echo $_SESSION['error']?>
    <div class="container">
      <div class="row justify-content-center mt-5">
                <form action="" method="POST" class="login-email">
                    <p class="login-text" style="font-size: 2rem; font-weight: 800;">Login</p>
                    <div class="input-group">
                        <input type="email" placeholder="Email" name="email" value="<?php echo $email; ?>" required>
                    </div>
                    <div class="input-group">
                        <input type="password" placeholder="Password" name="password" value="<?php echo $_POST['password']; ?>" required>
                    </div>
                    <div class="input-group">
                        <input type="checkbox" class="custom-control-input" name="remember" id="remember" <?php if(isset($_COOKIE["email"])) { ?> checked <?php } ?>>
                        <label class="custom-control-label" for="remember">Remember me?</label>
                    </div>
                    <div class="input-group">
                        <button name="submit" class="btn">Login</button>
                    </div>
                    <p class="login-register-text">Anda belum punya akun? <a href="register.php">Register</a></p>
                </form>
      </div>
    </div>
    </section>
</body>
</html>
