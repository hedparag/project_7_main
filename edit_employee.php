<?php
require('include/config.php');
session_start();
if(!(isset($_SESSION['user_full_name']) && isset($_SESSION['userid']))){
    header("Location: login.php");
    exit();
}
$query = "SELECT employee_id FROM users WHERE user_id = $1";
$result = pg_query_params($conn, $query, [$_SESSION['userid']]);
$row = pg_fetch_assoc($result);
$empId = $row['employee_id'];
$query1 = "SELECT * FROM employees WHERE employee_id = $1";
$result1 = pg_query_params($conn, $query1, [$empId]);
$employee = pg_fetch_assoc($result1);
if (isset($_POST['update']) && $_POST['update'] == "Update") {
    $empname = pg_escape_string($conn, $_POST['empname']);
    $empmail = pg_escape_string($conn, $_POST['empmail']);
    $empphn = pg_escape_string($conn, $_POST['empphn']);
    $details = pg_escape_string($conn, $_POST['empdetails']);
    $skills = pg_escape_string($conn, $_POST['skills']);
    $updateQuery = "UPDATE employees SET employee_name = $1, employee_email = $2, employee_phone = $3, 
                  employee_details = $4, employee_skils = $5, updated_at = NOW() WHERE employee_id = $6";
    $params = [$empname, $empmail, $empphn, $details, $skills, $empId];
    $updateResult = pg_query_params($conn, $updateQuery, $params);
    if ($updateResult) {
        echo "<script>alert('Details updated successfully!'); window.location.href='edit_employee.php';</script>";
    } else {
        echo "<script>alert('Error updating details!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-secondary">
<?php include("templates/login_header.php"); ?>
    <div class="container mt-5">
        <h2 class="text-center text-light mb-4 py-4">Edit Details</h2>
        <form method="POST" class="p-4 border rounded bg-white shadow">
            <input type="hidden" name="empid" value="<?= htmlspecialchars($employee['employee_id']) ?>">
            <div class="mb-3">
                <label class="form-label">Employee Name</label>
                <input type="text" name="empname" class="form-control" value="<?= htmlspecialchars($employee['employee_name'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Employee Email</label>
                <input type="email" name="empmail" class="form-control" value="<?= htmlspecialchars($employee['employee_email'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="tel" name="empphn" class="form-control" value="<?= htmlspecialchars($employee['employee_phone'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Details</label>
                <input type="text" name="empdetails" class="form-control" value="<?= htmlspecialchars($employee['employee_details'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Skills</label>
                <input type="text" name="skills" class="form-control" value="<?= htmlspecialchars($employee['employee_skils'], ENT_QUOTES, 'UTF-8') ?>" required>
            </div>
            <div class="mb-3">
                <input type="submit" name="update" class="form-control btn btn-success" value="Update" required>
            </div>
        </form>
    </div>
<?php include "templates/footer.php"; ?>
</body>
</html>