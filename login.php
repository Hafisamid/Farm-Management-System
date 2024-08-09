<?php
session_start();
include 'db.php';

$login_success = false;
$redirect_url = '';

// Initialize attempt count and lockout time if not already set
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = 0;
}
if (!isset($_SESSION['lockout_time'])) {
    $_SESSION['lockout_time'] = 0;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if ($_SESSION['login_attempts'] >= 3) {
        $current_time = time();
        $lockout_remaining = 10 - ($current_time - $_SESSION['lockout_time']);
        if ($lockout_remaining <= 0) {
            // Reset attempts after 10 seconds
            $_SESSION['login_attempts'] = 0;
            $_SESSION['lockout_time'] = 0;
            $lockout_remaining = 0;
        } else {
            $error = "You have been locked out due to too many failed login attempts. Please try again in $lockout_remaining seconds.";
        }
    }

    if ($_SESSION['login_attempts'] < 3) {
        $username = $_POST['username'];
        $password = $_POST['password'];

        $sql = "SELECT * FROM User WHERE Username = '$username'";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            if (password_verify($password, $row['Password'])) {
                $_SESSION['user_id'] = $row['User_Id'];
                $_SESSION['username'] = $row['Username'];
                $_SESSION['user_type'] = $row['User_Type'];
                $_SESSION['login_attempts'] = 0; // Reset attempt count on successful login
                $login_success = true;
                $redirect_url = ($row['User_Type'] == 'admin') ? 'dashboard.php' : 'dashboard2.php';
            } else {
                $_SESSION['login_attempts']++;
                if ($_SESSION['login_attempts'] >= 3) {
                    $_SESSION['lockout_time'] = time();
                    $lockout_remaining = 10;
                }
                $error = "Invalid password. Attempt " . $_SESSION['login_attempts'] . " of 3.";
            }
        } else {
            $_SESSION['login_attempts']++;
            if ($_SESSION['login_attempts'] >= 3) {
                $_SESSION['lockout_time'] = time();
                $lockout_remaining = 10;
            }
            $error = "No user found with that username. Attempt " . $_SESSION['login_attempts'] . " of 3.";
        }
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link rel="stylesheet" type="text/css" href="styleslogin.css">
    <script>
        function redirectToDashboard(url) {
            alert("Log In Successfully");
            window.location.href = url;
        }

        // JavaScript for background slideshow
        let slideIndex = 0;

        function showSlides() {
            let slides = document.getElementsByClassName("slide");
            for (let i = 0; i < slides.length; i++) {
                slides[i].style.opacity = 0;
            }
            slideIndex++;
            if (slideIndex > slides.length) {
                slideIndex = 1;
            }
            slides[slideIndex - 1].style.opacity = 1;
            setTimeout(showSlides, 5000); // Change image every 5 seconds
        }

        window.onload = function() {
            showSlides();
            <?php if (isset($lockout_remaining) && $lockout_remaining > 0): ?>
                startCountdown(<?php echo $lockout_remaining; ?>);
            <?php endif; ?>
        };

        function startCountdown(seconds) {
            let countdownElement = document.getElementById('countdown');
            let loginButton = document.querySelector('.btn');
            let errorMessage = document.querySelector('.error');
            loginButton.disabled = true;

            let interval = setInterval(function() {
                countdownElement.textContent = "Please wait " + seconds + " seconds before trying again.";
                if (seconds <= 0) {
                    clearInterval(interval);
                    countdownElement.textContent = "";
                    loginButton.disabled = false;
                    if (errorMessage) {
                        errorMessage.textContent = "";
                    }
                }
                seconds--;
            }, 1000);
        }
    </script>
</head>
<body>
    <div class="slideshow">
        <div class="slide" style="background-image: url('farm1.jpg');"></div>
        <div class="slide" style="background-image: url('farm2.jpg');"></div>
        <div class="slide" style="background-image: url('farm3.jpeg');"></div>
    </div>
    <div class="login-container">
        <div class="login-box">
            <div class="login-header">
                <img src="user-icon.jpg" alt="User Icon" class="user-icon">
                <h2>Login</h2>
            </div>
            <form method="post" action="">
                <div class="input-group">
                    <label for="username">Email</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <button type="submit" class="btn" <?php if (isset($lockout_remaining) && $lockout_remaining > 0) echo 'disabled'; ?>>Login</button>
                <!--<div class="extra-options">
                    <label><input type="checkbox" name="remember"> Remember Me</label>
                    <a href="#">Forgot Password?</a>
                </div>-->
            </form>
            <?php if (isset($error)): ?>
                <div class="error"><?php echo $error; ?></div>
            <?php endif; ?>

            <div id="countdown" class="countdown"></div>

            <div class="register-link">
                <p>Do not have an account? <a href="register.php">Register here</a></p>
            </div>
        </div>
    </div>

    <?php if ($login_success): ?>
        <script>
            redirectToDashboard('<?php echo $redirect_url; ?>');
        </script>
    <?php endif; ?>
</body>
</html>
