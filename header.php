<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
    <link rel="stylesheet" type="text/css" href="header.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
<div class="header">
    <div class="nav-container">
        <div class="nav-right">
            <div class="profile-dropdown">
                <a href="#" class="nav-link profile-icon" onclick="toggleDropdown(event)">
                    <span style="font-size: small; font-weight: bold;"> <?php echo htmlspecialchars($_SESSION['user_type']); ?></span> &nbsp;
                
                    <i class="fas fa-user"></i> 
                <div class="dropdown-content">
                    <span style="font-size: small; font-weight: bold;">Name: <?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    
                    <a href="#" class="nav-link logout" onclick="confirmLogout(event)"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    function toggleDropdown(event) {
        event.preventDefault();
        document.querySelector('.dropdown-content').classList.toggle('show');
    }

    function confirmLogout(event) {
        event.preventDefault(); // Prevent the default link behavior
        var confirmed = confirm("Are you sure you want to log out?");
        if (confirmed) {
            window.location.href = "logout.php";
        }
    }

    // Close the dropdown if the user clicks outside of it
    window.onclick = function(event) {
        if (!event.target.matches('.profile-icon, .profile-icon *')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            for (var i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    }
</script>
</body>
</html>
