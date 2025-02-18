<?php
if (!isset($conn)) {
    require('include/config.php');
}
session_start();
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = md5(uniqid(rand(), true));
}
$toastMessage = "";
$toastType = "";
$is_error = false;

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['submit']) && $_POST['submit'] == "Login") {
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        echo "<script>
            alert('CSRF token validation failed! Possible attack detected.');
            window.location.href = 'login.php';
          </script>";
        exit();
    }

    $_SESSION['csrf_token'] = md5(uniqid(rand(), true));

    $empname = pg_escape_string($conn, stripslashes($_POST['empname']));
    $emppass = pg_escape_string($conn, stripslashes($_POST['password']));
    
    if (empty($empname)) {
        $name_error = "User Name is required***";
        $is_error = true;
    }

    if (empty($emppass)) {
        $password_error = "Password is required***";
        $is_error = true;
    }

    if (!$is_error) {
        $query = "SELECT * FROM users WHERE username = $1;";
        $result = pg_query_params($conn, $query, [$empname]) or die("Query Failed: " . pg_last_error($conn));

        if (pg_num_rows($result) == 1) {
            $row = pg_fetch_assoc($result);
            $hashed_password = $row['password'];
   
            if(password_verify($emppass, $hashed_password)) {
                $_SESSION['user_full_name'] = htmlspecialchars($row['full_name'], ENT_QUOTES, 'UTF-8');
                $_SESSION['userid'] = $row['user_id'];
                unset($_SESSION['csrf_token']);

                $query2 = "UPDATE users SET last_login_time = NOW() WHERE user_id = $1;";
                $result2 = pg_query_params($conn, $query2, [$row['user_id']]) or die("Query Failed: " . pg_last_error($conn));
                $uid = $row['user_type_id'];
                $query3 = "SELECT status FROM user_types WHERE user_type_id = $uid;";
                $result3 = pg_query($conn,$query3)  or die("Query Failed: " . pg_last_error($conn));
                $row3 = pg_fetch_assoc($result3);
                $status = $row3['status'];
                if ($status == 't') {
                    header("Location: admin_dashboard.php");
                    exit();
                } else {
                    header("Location: user_dashboard.php");
                    exit();
                }
            } else {
                $toastMessage = "Wrong Password!";
                $toastType = "error";
            }
        } else {
            $toastMessage = "Invalid Credential!";
            $toastType = "error";
        }
    }
    
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<?php include("templates/header.php"); ?>
<body class="bg-secondary">
<div class="container d-flex justify-content-center align-items-center min-vh-100"> 
    <div class="col-lg-4 col-md-6 col-sm-8"> 
        <form method="POST" name="login" class="p-4 border rounded bg-light shadow text-center" novalidate>
            <div class="text-dark mb-3">
                <h2 class="text-dark bg-white p-2 rounded w-100">Login Page</h2>
            </div>
            <?php if (!empty($name_error)) { ?>
                <p class="text-danger text-start">
                    <?= htmlspecialchars($name_error) ?>
                </p>    
            <?php } ?>
            <div class="mb-3">
            <input type="text" class="form-control" name="empname" 
                value="<?= isset($_POST['empname']) ? htmlspecialchars($_POST['empname']) : '' ?>" 
                placeholder="User Name"/>
            </div>
            <?php if(!empty($password_error)){ ?>
                <p class="text-danger text-start">
                    <?= htmlspecialchars($password_error) ?>
                </p>
            <?php } ?>
            <div class="mb-3">
                <input type="password" class="form-control" name="password" 
                value="<?= isset($_POST['password']) ? htmlspecialchars($_POST['password']) : '' ?>" placeholder="Password"/>
            </div>
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">
            <div class="mb-3">
                <button type="submit" name="submit" value="Login" class="btn btn-primary w-100">Submit</button>
            </div>
            <div class="text-center text-dark">
                <p class="mb-0">Don't have an account?<br><a class="text-primary" href="register.php">Register here first for admin approve</a></p>
            </div>
        </form>
    </div>
</div>
<div id="toast-container" class="toast-container position-fixed bottom-0 end-0 p-3"
     data-message="<?= addslashes($toastMessage) ?>" 
     data-type="<?= $toastType ?>">
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="js/register.js"></script>
<?php include "templates/footer.php"; ?>
</body>
</html>
