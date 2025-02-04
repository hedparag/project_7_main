    <?php
    require('include/config.php');
    
    if (isset($_POST['submit'])=="register") {
        $userTypeId = $image = $_POST['userTypeId'];
        $deptId = $image = $_POST['deptId'];
        $posId = $image = $_POST['posId'];
        $empname = pg_escape_string($conn, stripslashes($_POST['empname']));
        $empmail = pg_escape_string($conn, stripslashes($_POST['empmail']));
        $empphn = pg_escape_string($conn, stripslashes($_POST['empphn']));
        $date = pg_escape_string($conn, stripslashes($_POST['date']));
        $image = $_POST['image'];
        $details = pg_escape_string($conn, stripslashes($_POST['empdetails']));
        $skills = pg_escape_string($conn, stripslashes($_POST['skills']));
        
        $query = "INSERT INTO employees(
	              user_type_id, department_id, position_id, employee_name, employee_email, employee_phone, profile_image, employee_details, employee_skils, dob, created_at, updated_at)
	              VALUES ('$userTypeId', '$deptId', '$posId', '$empname', '$empmail', '$empphn', '$image', '$details', '$skills', '$date', NOW(), NOW());";
        $result = pg_query($conn, $query);
    
        if ($result) {
            echo '<script>alert("Registration successfull!!!")</script>';
        } 
        else {
            echo '<script>alert("Error occurred => '.pg_last_error().'")</script>';
        }
    }    
    ?>
    <!doctype html>
    <html lang="en">
        <head>
            <meta charset="utf-8">
            <meta name="viewport" content="width=device-width, initial-scale=1">
            <title>Registration Page</title>
            <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        </head>
        <?php include("templates/header.php"); ?>
        <body class="bg-secondary">
        <div class="container d-flex justify-content-center align-items-center min-vh-100 mb-3 mt-3"> 
            <div class="col-lg-4 col-md-6 col-sm-8"> 
                <form method="POST" name="register" class="p-4 border rounded bg-light shadow text-center">
                    <div class="text-dark mb-3">
                        <h2 class="text-dark bg-white p-2 rounded w-100">Registration Page</h2>
                    </div>
                    <div class="mb-3">
                        <select class="form-select" name="userTypeId" aria-label="User Type">
                            <option>-- Select user type --</option>
                            <?php
                            $query2 = "SELECT * FROM user_types WHERE status = 't'";
                            $user_types = pg_query($conn, $query2);

                            while ($key = pg_fetch_assoc($user_types)) {
                            ?>
                            <option value="<?=$key['user_type_id']?>"><?=$key['user_type']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <select class="form-select" name="deptId" aria-label="Department">
                            <option>-- Select department --</option>
                            <?php
                            $query2 = "SELECT * FROM departments WHERE status = 't'";
                            $user_types = pg_query($conn, $query2);

                            while ($key = pg_fetch_assoc($user_types)) {
                            ?>
                            <option value="<?=$key['department_id']?>"><?=$key['department_name']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <select class="form-select" name="posId" aria-label="Position">
                            <option>-- Select position --</option>
                            <?php
                            $query2 = "SELECT * FROM positions WHERE status = 't'";
                            $user_types = pg_query($conn, $query2);

                            while ($key = pg_fetch_assoc($user_types)) {
                            ?>
                            <option value="<?=$key['position_id']?>"><?=$key['position_name']?></option>
                            <?php } ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" name="empname" placeholder="Employee Name" required />
                    </div>
                    <div class="mb-3">
                        <input type="email" class="form-control" name="empmail" placeholder="Employee Email" required />
                    </div>
                    <div class="mb-3">
                        <input type="tel" class="form-control" name="empphn" placeholder="Employee Phone" required />
                    </div>
                    <div class="mb-3">
                        <input type="date" class="form-control" name="date" required />
                    </div>
                    <div class="mb-3">
                        <input type="file" class="form-control" name="image" required />
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" name="empdetails" placeholder="Details" required />
                    </div>
                    <div class="mb-3">
                        <input type="text" class="form-control" name="skills" placeholder="Skills" required />
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
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
        <?php include "templates/footer.php"; ?>
    </body>
</html>