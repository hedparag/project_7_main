<?php
require('include/config.php');
session_start();
if(!(isset($_SESSION['user_full_name']) && isset($_SESSION['userid']))){
    header("Location: login.php");
    exit();
} 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-secondary d-flex flex-column min-vh-100">
<?php include("templates/login_header.php"); ?>
<div class="container mt-4 pt-5 flex-grow-1">
    <h1 class="text-center mb-3 text-light">Welcome User</h1>
    <h2 class="text-center mb-3 text-light"><?= htmlspecialchars($_SESSION['user_full_name'], ENT_QUOTES, 'UTF-8') ?></h2>
</div>
<?php include "templates/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>