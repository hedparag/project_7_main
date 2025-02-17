<?php
require('include/config.php');
session_start();

if(!(isset($_SESSION['user_full_name']) && isset($_SESSION['userid']))){
    header("Location: login.php");
    exit();
} 

$toastMessage = "";
$toastType = "";
$user_id = $_SESSION['userid'];
$pattern = '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{8,}$/';
$is_error = false;

$query = "SELECT password FROM users WHERE user_id = $user_id";
$result = pg_query($conn, $query);
$row = pg_fetch_assoc($result);
$stored_password_hash = $row['password'];

if (isset($_POST['change']) && $_POST['change'] == "Change") {
    $current_password = $_POST['currpass'];
    $new_password = $_POST['newpass'];
    $confirm_password = $_POST['conpass'];

    if (empty($current_password)) {
        $curr_pass_error = "Current Password is required***";
        $is_error = true;
    }

    if (empty($new_password)) {
        $new_pass_error = "New Password is required***";
        $is_error = true;
    }

    if (empty($confirm_password)) {
        $con_pass_error = "Confirm Password is required***";
        $is_error = true;
    }

    if (!$is_error) {
        
        if (!password_verify($current_password, $stored_password_hash)) {
            $message = "<div class='alert alert-danger'>Current password is incorrect!</div>";
        } 

        elseif (!preg_match($pattern, $new_password)) {
            $message = "<div class='alert alert-warning'>New password must have at least 8 characters, 
                    one uppercase letter, one lowercase letter, one number, and one special character.</div>";
        } 

        elseif ($new_password !== $confirm_password) {
            $message = "<div class='alert alert-warning'>New password and confirm password do not match!</div>";
        } 

        else {
            $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
            $updateQuery = "UPDATE users SET password = $1, updated_at = NOW() WHERE user_id = $2";
            $updateResult = pg_query_params($conn, $updateQuery, [$hashed_new_password, $user_id]);

            if ($updateResult) {
                $toastMessage = "Password changed successfully!";
                $toastType = "success";
                $_POST = array();
            } else {
                $toastMessage = "Something happened while password!";
                $toastType = "error";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<?php include("templates/login_header.php"); ?>
<body class="bg-secondary">
<div class="container d-flex justify-content-center align-items-center min-vh-100 mt-5"> 
    <div class="col-lg-4 col-md-6 col-sm-8"> 
        <form method="POST" name="change" class="p-4 border rounded bg-light shadow text-center" novalidate>
            <div class="text-dark mb-3">
                <h2 class="text-dark bg-white p-2 rounded w-100">Change Password</h2>
            </div>
            <?php if(isset($message)){ echo $message; } ?>
            <?php if(!empty($curr_pass_error)){ ?>
                <p class="text-danger text-start">
                    <?= htmlspecialchars($curr_pass_error) ?>
            </p><?php } ?>
            <div class="mb-3">
            <input type="password" class="form-control" name="currpass" 
                value="<?= isset($_POST['currpass']) ? htmlspecialchars($_POST['currpass']) : '' ?>" 
                placeholder="Current Password"/>
            </div>
            <?php if(!empty($new_pass_error)){ ?>
                <p class="text-danger text-start">
                    <?= htmlspecialchars($new_pass_error) ?>
                </p><?php } ?>
            <div class="mb-3">
                <input type="password" class="form-control" name="newpass" 
                value="<?= isset($_POST['newpass']) ? htmlspecialchars($_POST['newpass']) : '' ?>" placeholder="New Password"/>
            </div>
            <?php if(!empty($con_pass_error)){ ?>
                <p class="text-danger text-start">
                    <?= htmlspecialchars($con_pass_error) ?>
                </p><?php } ?>
            <div class="mb-3">
                <input type="password" class="form-control" name="conpass" 
                value="<?= isset($_POST['conpass']) ? htmlspecialchars($_POST['conpass']) : '' ?>" placeholder="Confirm Password"/>
            </div>
            <div class="mb-3">
                <button type="submit" name="change" value="Change" class="btn btn-primary w-100">Change Password</button>
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
