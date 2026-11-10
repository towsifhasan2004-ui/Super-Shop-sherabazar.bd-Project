<?php
session_start();

if(isset($_SESSION['username'])){
    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title>Shera Bazar | Login</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" rel="stylesheet">

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:'Segoe UI',sans-serif;
}

body{

height:100vh;

background:linear-gradient(135deg,#0f172a,#1d4ed8);

display:flex;

justify-content:center;

align-items:center;

}

.login-box{

width:420px;

background:#fff;

border-radius:20px;

padding:40px;

box-shadow:0 20px 50px rgba(0,0,0,.3);

animation:fade .6s;

}

@keyframes fade{

from{
opacity:0;
transform:translateY(25px);
}

to{
opacity:1;
transform:translateY(0);
}

}

.logo{

width:90px;
height:90px;

background:#0d6efd;

color:#fff;

margin:auto;

border-radius:50%;

display:flex;

justify-content:center;

align-items:center;

font-size:40px;

margin-bottom:20px;

}

h2{

text-align:center;

font-weight:bold;

margin-bottom:5px;

}

.subtitle{

text-align:center;

color:#777;

margin-bottom:30px;

}

.form-control{

height:50px;

border-radius:10px;

}

.input-group-text{

border-radius:10px 0 0 10px;

}

.btn-login{

height:50px;

border-radius:10px;

font-size:18px;

font-weight:bold;

}

.footer{

text-align:center;

margin-top:25px;

color:#777;

font-size:14px;

}

</style>

</head>

<body>

<div class="login-box">

<div class="logo">

<i class="fas fa-store"></i>

</div>

<h2>Shera Bazar POS</h2>

<p class="subtitle">

Super Shop Management System

</p>

<form method="POST" action="authenticate.php">

<div class="mb-3">

<label class="mb-2">

Username

</label>

<div class="input-group">

<span class="input-group-text">

<i class="fas fa-user"></i>

</span>

<input

type="text"

name="username"

class="form-control"

placeholder="Enter Username"

required>

</div>

</div>

<div class="mb-4">

<label class="mb-2">

Password

</label>

<div class="input-group">

<span class="input-group-text">

<i class="fas fa-lock"></i>

</span>

<input

type="password"

name="password"

class="form-control"

placeholder="Enter Password"

required>

</div>

</div>

<button

type="submit"

class="btn btn-primary btn-login w-100">

<i class="fas fa-sign-in-alt"></i>

Login

</button>

</form>

<div class="footer">

© <?php echo date('Y');?> Shera Bazar POS<br>

Powered by Towsif Hasan

</div>

</div>

</body>

</html>