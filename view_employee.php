<?php
require('include/config.php');
session_start();
if(!(isset($_SESSION['user_full_name']) && isset($_SESSION['userid']))){
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    die("Invalid Employee ID.");
}

$empId = intval($_GET['id']);

$query = "SELECT * FROM employees WHERE employee_id = $1";
$result = pg_query_params($conn, $query, [$empId]);

if (!$result || pg_num_rows($result) == 0) {
    die("Employee not found.");
}

$employee = pg_fetch_assoc($result);

if (isset($_POST['approve']) && $_POST['approve'] == "Approve") {
    $userTypeId = $_POST['userTypeId'];
    $deptId = $_POST['deptId'];
    $posId = $_POST['posId'];
    $empId = pg_escape_string($conn, $_POST['empid']);
    $empname = pg_escape_string($conn, $_POST['empname']);
    $empmail = pg_escape_string($conn, $_POST['empmail']);
    $empphn = pg_escape_string($conn, $_POST['empphn']);
    $details = pg_escape_string($conn, $_POST['empdetails']);
    $skills = pg_escape_string($conn, $_POST['skills']);
    $password = pg_escape_string($conn, $_POST['password']);
    $salary = pg_escape_string($conn, $_POST['salary']);

    $updateQuery = "UPDATE employees SET user_type_id=$1, department_id=$2, position_id=$3, employee_name = $4, employee_email = $5, employee_phone = $6, 
                    employee_details = $7, employee_skils = $8, salary = $9, status = 't' WHERE employee_id = $10";
    $params = [$userTypeId, $deptId, $posId, $empname, $empmail, $empphn, $details, $skills, $salary, $empId];
    $updateResult = pg_query_params($conn, $updateQuery, $params); 
    $password = password_hash($password, PASSWORD_DEFAULT);
    $u1 = strtolower(str_replace(' ', '', $empname));
    $randomNumber = rand(100, 999);
    $specialChar = "!@#"[rand(0, 2)];
    $uname = $u1.$specialChar.$randomNumber;
    $query3 = "INSERT INTO users(employee_id, user_type_id, full_name, username, password, created_at, status) VALUES ($1, $2, $3, $4, $5, NOW(), 't');";
    $result3 = pg_query_params($conn, $query3, array($empId,$userTypeId,$empname,$uname,$password)) or die("Query Failed: " . pg_last_error($conn));
    if ($result3) {
        echo "<script>alert('Employee Approved successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error approving employee!');</script>";
    }
}

if (isset($_POST['reject']) && $_POST['reject'] == "Reject") {
    $empId = pg_escape_string($conn, $_POST['empid']);
    $query = "DELETE FROM employees WHERE employee_id = $1";
    $result = pg_query_params($conn, $query, array($empId)) or die("Query Failed: " . pg_last_error($conn));
    if ($result) {
        echo "<script>alert('Employee Rejected successfully!'); window.location.href='admin_dashboard.php';</script>";
    } else {
        echo "<script>alert('Error deleting employee!');</script>";
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body class="bg-secondary">
<?php include("templates/login_header.php"); ?>
    <div class="container mt-5">
        <h2 class="text-center text-light mb-4 py-4">Employee Details</h2>
        <form method="POST" enctype="multipart/form-data" class="p-4 border rounded bg-white shadow">
            <input type="hidden" name="empid" value="<?= htmlspecialchars($employee['employee_id']) ?>">
            <div class="mb-3">
                <label class="form-label">Employee Name</label>
                <input type="text" name="empname" class="form-control" value="<?= htmlspecialchars($employee['employee_name']) ?>" required>
            </div>

            <div class="mb-3">
                <select class="form-select" name="userTypeId" required>
                    <option value="">-- Select user type --</option>
                    <?php
                    $query2 = "SELECT * FROM user_types;";
                    $user_types = pg_query($conn, $query2);
                    $selectedUserType = isset($_POST['userTypeId']) ? $_POST['userTypeId'] : $employee['user_type_id'];
                    while ($key = pg_fetch_assoc($user_types)) {
                        $isSelected = ($selectedUserType == $key['user_type_id']) ? 'selected' : '';
                        echo "<option value='".$key['user_type_id']."' $isSelected>".$key['user_type']."</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <select class="form-select" name="deptId" required>
                    <option value="">-- Select Department --</option>
                    <?php
                    $query2 = "SELECT * FROM departments WHERE status = 't'";
                    $departments = pg_query($conn, $query2);
                    $selectedDeptType = isset($_POST['deptId']) ? $_POST['deptId'] : $employee['department_id'];
                    while ($key = pg_fetch_assoc($departments)) {
                        $isSelected = ($selectedDeptType == $key['department_id']) ? 'selected' : '';
                        echo "<option value='".$key['department_id']."' $isSelected>".$key['department_name']."</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <select class="form-select" name="posId" required>
                    <option value="">-- Select position --</option>
                    <?php
                    $query2 = "SELECT * FROM positions WHERE status = 't'";
                    $positions = pg_query($conn, $query2);
                    $selectedPosType = isset($_POST['posId']) ? $_POST['posId'] : $employee['position_id'];
                    while ($key = pg_fetch_assoc($positions)) {
                        $isSelected = ($selectedPosType == $key['position_id']) ? 'selected' : '';
                        echo "<option value='".$key['position_id']."' $isSelected>".$key['position_name']."</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="mb-3">
                <label class="form-label">Employee Email</label>
                <input type="email" name="empmail" class="form-control" value="<?= htmlspecialchars($employee['employee_email']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="tel" name="empphn" class="form-control" value="<?= htmlspecialchars($employee['employee_phone']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Details</label>
                <input type="text" name="empdetails" class="form-control" value="<?= htmlspecialchars($employee['employee_details']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Skills</label>
                <input type="text" name="skills" class="form-control" value="<?= htmlspecialchars($employee['employee_skils']) ?>" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" name="password" class="form-control">
            </div>
            <div class="mb-3">
                <label class="form-label">Salary</label>
                <input type="number" name="salary" class="form-control">
            </div>
            <div class="row">
                    <div class="col">
                        <button type="submit" name="approve" value="Approve" class="btn btn-success w-100">Approve</button>
                    </div>
                    <div class="col">
                        <button type="submit" name="reject" value="Reject" class="btn btn-danger w-100">Reject</button>
                    </div>
            </div>
        </form>
    </div>
<?php include "templates/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
