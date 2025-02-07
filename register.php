<?php
require('include/config.php');

error_reporting(0);
ini_set('display_errors', 0);

$toastMessage = "";
$toastType = "";
$is_error = false;

if (isset($_POST['submit']) && $_POST['submit'] == "Register") {
    $userTypeId = $_POST['userTypeId'];
    $deptId = $_POST['deptId'];
    $posId = $_POST['posId'];
    $empname = pg_escape_string($conn, stripslashes($_POST['empname']));
    $empmail = pg_escape_string($conn, stripslashes($_POST['empmail']));
    $empphn = pg_escape_string($conn, stripslashes($_POST['empphn']));
    $date = pg_escape_string($conn, stripslashes($_POST['date']));
    $details = pg_escape_string($conn, stripslashes($_POST['details']));
    $skills = pg_escape_string($conn, stripslashes($_POST['skills']));

    if (empty($userTypeId)) {
        $user_error = "User Type is required***";
        $is_error = true;
    }

    if (empty($deptId)) {
        $dept_error = "Department is required***";
        $is_error = true;
    }

    if (empty($posId)) {
        $pos_error = "Position is required***";
        $is_error = true;
    }
    
    if (empty($empname)) {
        $name_error = "Employee Name is required***";
        $is_error = true;
    }
    
    if (empty($empmail)) {
        $email_error = "Employee mail is required***";
        $is_error = true;
    }
    else if (!filter_var($empmail, FILTER_VALIDATE_EMAIL)) {
        $email_error = "Invalid email format***";
        $is_error = true;
    }
    
    if (!preg_match("/^[0-9]{10}$/", $empphn)) {
        $phn_error = "Phone number must be of 10 digits***";
        $is_error = true;
    }
    
    if (empty($date)) {
        $dob_error = "Date of birth is required***";
        $is_error = true;
    }

    if ($_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $imageTmpPath = $_FILES['image']['tmp_name'];
        $imageType = mime_content_type($imageTmpPath);
        
        if (!in_array($imageType, ['image/jpeg', 'image/png'])) {
            $image_error = "Only JPEG and PNG images are allowed***";
            $is_error = true;
        } else {
            $imageData = file_get_contents($imageTmpPath);
            $imageEscaped = pg_escape_bytea($conn, $imageData);
        }
    } else {
        $image_error = "Upload image***";
        $imageEscaped = null;
        $is_error = true;
    }

    if (!$is_error) {
        $query = "INSERT INTO employees(
            user_type_id, department_id, position_id, employee_name, employee_email, 
            employee_phone, employee_details, employee_skils, dob, created_at, updated_at, profile_image) VALUES ('$userTypeId', '$deptId', '$posId', '$empname', '$empmail', '$empphn', '$details', '$skills', '$date', NOW(), NOW(), $1 );";
        
        $result = @pg_query_params($conn, $query, [$imageEscaped]);

        if ($result) {
            $toastMessage = "Registration Successful!";
            $toastType = "success";
            $_POST = array();
        } else {
            $errorMessage = pg_last_error($conn);
            $toastMessage = "Error: " . addslashes($errorMessage);
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
    <title>Registration Page</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<?php include("templates/header.php"); ?>
<body class="bg-secondary">
<div class="container d-flex justify-content-center align-items-center min-vh-100 mt-3 pt-5"> 
    <div class="col-lg-4 col-md-6 col-sm-8"> 
        <form method="POST" enctype="multipart/form-data" name="register" class="p-4 border rounded bg-light shadow text-center" novalidate>
            <div class="text-dark mb-3">
                <h2 class="text-dark bg-white p-2 rounded w-100">Registration Page</h2>
            </div>

            <?php if(!empty($user_error)){ ?>
                <p class="text-danger text-start">
                    <?= htmlspecialchars($user_error) ?>
            </p><?php } ?>
            <div class="mb-3">
                <select class="form-select" name="userTypeId" required>
                    <option value="">-- Select user type --</option>
                    <?php
                    $query2 = "SELECT * FROM user_types;";
                    $user_types = pg_query($conn, $query2);
                    $selectedUserType = isset($_POST['userTypeId']) ? $_POST['userTypeId'] : '';
                    while ($key = pg_fetch_assoc($user_types)) {
                        $isSelected = ($selectedUserType == $key['user_type_id']) ? 'selected' : '';
                        echo "<option value='".$key['user_type_id']."' $isSelected>".$key['user_type']."</option>";
                    }
                    ?>
                </select>
            </div>
            <?php if(!empty($dept_error)){ ?>
                <p class="text-danger text-start">
                    <?= htmlspecialchars($dept_error) ?>
            </p><?php } ?>
            <div class="mb-3">
                <select class="form-select" name="deptId" required>
                    <option value="">-- Select Department --</option>
                    <?php
                    $query2 = "SELECT * FROM departments WHERE status = 't'";
                    $departments = pg_query($conn, $query2);
                    $selectedDeptType = isset($_POST['deptId']) ? $_POST['deptId'] : '';
                    while ($key = pg_fetch_assoc($departments)) {
                        $isSelected = ($selectedDeptType == $key['department_id']) ? 'selected' : '';
                        echo "<option value='".$key['department_id']."' $isSelected>".$key['department_name']."</option>";
                    }
                    ?>
                </select>
            </div>
            <?php if(!empty($pos_error)){ ?>
                <p class="text-danger text-start">
                    <?= htmlspecialchars($pos_error) ?>
            </p><?php } ?>
            <div class="mb-3">
                <select class="form-select" name="posId" required>
                    <option value="">-- Select position --</option>
                    <?php
                    $query2 = "SELECT * FROM positions WHERE status = 't'";
                    $positions = pg_query($conn, $query2);
                    $selectedPosType = isset($_POST['posId']) ? $_POST['posId'] : '';
                    while ($key = pg_fetch_assoc($positions)) {
                        $isSelected = ($selectedPosType == $key['position_id']) ? 'selected' : '';
                        echo "<option value='".$key['position_id']."' $isSelected>".$key['position_name']."</option>";
                    }
                    ?>
                </select>
            </div>
            <?php if(!empty($name_error)){ ?>
                <p class="text-danger text-start">
                    <?= htmlspecialchars($name_error) ?>
            </p><?php } ?>
            <div class="mb-3">
            <input type="text" class="form-control" name="empname" 
                value="<?= isset($_POST['empname']) ? htmlspecialchars($_POST['empname']) : '' ?>" 
                placeholder="Employee Name"/>
            </div>
            <?php if(!empty($email_error)){ ?>
                <p class="text-danger text-start">
                    <?= htmlspecialchars($email_error) ?>
                </p><?php } ?>
            <div class="mb-3">
                <input type="email" class="form-control" name="empmail" 
                value="<?= isset($_POST['empmail']) ? htmlspecialchars($_POST['empmail']) : '' ?>" 
                placeholder="Employee Email"/>
            </div>
            <?php if(!empty($phn_error)){ ?>
                <p class="text-danger text-start">
                    <?= htmlspecialchars($phn_error) ?>
            </p><?php } ?>
            <div class="mb-3">
                <input type="tel" class="form-control" name="empphn" 
                value="<?= isset($_POST['empphn']) ? htmlspecialchars($_POST['empphn']) : '' ?>" 
                placeholder="Employee Phone"/>
            </div>
            <?php if(!empty($dob_error)){ ?>
                <p class="text-danger text-start">
                    <?= htmlspecialchars($dob_error) ?>
                </p><?php } ?>
            <div class="mb-3">
                <input type="date" class="form-control" name="date" 
                value="<?= isset($_POST['date']) ? htmlspecialchars($_POST['date']) : '' ?>"/>
            </div>
            <?php if(!empty($image_error)){ ?>
                <p class="text-danger text-start">
                    <?= htmlspecialchars($image_error) ?>
                </p><?php } ?>
            <div class="mb-3">
                <input type="file" class="form-control" name="image"/>
            </div>
            <div class="mb-3">
                <p for="detail" class="text-start text-dark">Employee Details</p>
                <textarea id="detail" class="form-control" name="details"><?= isset($_POST['details']) ? htmlspecialchars($_POST['details']) : '' ?></textarea>
            </div>
            <div class="mb-3">
                <p for="skill" class="text-start text-dark">Employee Skills</p>
                <textarea id="skill" class="form-control" name="skills"><?= isset($_POST['skills']) ? htmlspecialchars($_POST['skills']) : '' ?></textarea>
            </div>
            <div class="mb-3">
                <button type="submit" name="submit" value="Register" class="btn btn-primary w-100">Submit</button>
            </div>
            <div class="text-center text-dark">
                <p class="mb-0">Already have an account? <a class="text-primary" href="login.php">Login here</a></p>
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
