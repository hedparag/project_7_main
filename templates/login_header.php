<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top mb-3">
    <div class="container">
        <a class="navbar-brand" href="#">Employee System</a>
        <div class="dropdown ms-auto">
            <button class="btn btn-outline-light dropdown-toggle d-flex align-items-center" type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="images\sample\icon.png" alt="Profile" class="rounded-circle me-2" width="40" height="40">
                <span><?= htmlspecialchars($_SESSION['user_full_name']) ?></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
                <li><a class="dropdown-item" href="">Edit Profile</a></li>
                <li><a class="dropdown-item text-danger" href="logout.php">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>