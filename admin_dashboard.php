<?php
require('include/config.php');
session_start();
if(!(isset($_SESSION['user_full_name']) && isset($_SESSION['userid']))){
    header("Location: login.php");
    exit();
} 

$query = "SELECT employee_id, user_type_id, department_id, position_id, employee_name, employee_email, employee_phone FROM employees WHERE status = 'f' ORDER BY employee_id ASC";
$result = pg_query($conn, $query);

if (!$result) {
    die("Error fetching employees: " . pg_last_error($conn));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-secondary d-flex flex-column min-vh-100">
<?php include("templates/login_header.php"); ?>
<div class="container mt-4 pt-5 flex-grow-1">
    <h1 class="text-center mb-3 text-light">Welcome Admin <?= $_SESSION['user_full_name'] ?></h1>
    <h2 class="text-center mb-3 text-light">Employees to approve</h2>
    <div class="text-end mb-3">
        <a href="admin_add_employee.php" class="btn btn-success">+ Add New Employee</a>
    </div>
    <table class="table table-bordered bg-white shadow-sm text-center">
        <thead class="table-dark">
            <tr>
                <th>Employee id</th>
                <th>User type</th>
                <th>Department</th>
                <th>Position</th>
                <th>Name</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (pg_num_rows($result) > 0) { ?>
                <?php while ($row = pg_fetch_assoc($result)) { ?>
                    <tr>
                        <td class="text-center"><?= htmlspecialchars($row['employee_id']) ?></td>
                        <?php 
                            $query1 = "SELECT user_type FROM user_types WHERE user_type_id = $1";
                            $result1 = pg_query_params($conn, $query1, array($row['user_type_id'])) or die("Query Failed: " . pg_last_error($conn));
                            $row1 = pg_fetch_assoc($result1);
                        ?>
                        <td class="text-center"><?= htmlspecialchars($row1['user_type']) ?></td>
                        <?php 
                            $query2 = "SELECT department_name FROM departments WHERE department_id = $1";
                            $result2 = pg_query_params($conn, $query2, array($row['department_id'])) or die("Query Failed: " . pg_last_error($conn));
                            $row2 = pg_fetch_assoc($result2);
                        ?>
                        <td class="text-center"><?= htmlspecialchars($row2['department_name']) ?></td>
                        <?php 
                            $query3 = "SELECT position_name FROM positions WHERE position_id = $1";
                            $result3 = pg_query_params($conn, $query3, array($row['position_id'])) or die("Query Failed: " . pg_last_error($conn));
                            $row3 = pg_fetch_assoc($result3);
                        ?>
                        <td class="text-center"><?= htmlspecialchars($row3['position_name']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['employee_name']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['employee_email']) ?></td>
                        <td class="text-center"><?= htmlspecialchars($row['employee_phone']) ?></td>
                        <td>
                            <a href="view_employee.php?id=<?= $row['employee_id'] ?>" class="btn btn-success btn-sm">View Details</a>
                        </td>
                    </tr>
                <?php } ?>
            <?php } else { ?>
                <tr><td colspan="8" class="text-center">No employees found.</td></tr>
            <?php } ?>
        </tbody>
    </table>
</div>
<?php include "templates/footer.php"; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>