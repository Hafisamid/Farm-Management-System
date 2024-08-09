<?php
include 'db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $user_type = 'worker'; // Set the user type to worker

    $sql = "INSERT INTO User (Username, Password, Full_Name, Email, Contact, User_Type) 
            VALUES ('$username', '$password', '$full_name', '$email', '$contact', '$user_type')";

    if ($conn->query($sql) === TRUE) {
        echo "<script>
                alert('New Register Successfully Record!');
                window.location.href='register.php';
              </script>";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }

    $conn->close();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Register</title>
    <link rel="stylesheet" type="text/css" href="stylesregister.css">
    <script>
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
        };
    </script>
</head>
<body>
    <div class="slideshow">
        <div class="slide" style="background-image: url('farm1.jpg');"></div>
        <div class="slide" style="background-image: url('farm2.jpg');"></div>
        <div class="slide" style="background-image: url('farm3.jpeg');"></div>
    </div>
    <div class="register-container">
        <div class="register-box">
            <div class="register-header">
                <img src="user-icon.png" alt="User Icon" class="user-icon">
                <h2>Register</h2>
            </div>
            <form method="post" action="">
                <div class="input-group">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" required>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" required>
                </div>
                <div class="input-group">
                    <label for="full_name">Full Name</label>
                    <input type="text" name="full_name" id="full_name" required>
                </div>
                <div class="input-group">
                    <label for="email">Email</label>
                    <input type="email" name="email" id="email" required>
                </div>
                <div class="input-group">
                    <label for="contact">Contact</label>
                    <input type="text" name="contact" id="contact" required>
                </div>
                <!-- Hidden input for user type -->
                <input type="hidden" name="user_type" value="worker">
                <input type="submit" value="Register" class="btn">
            </form>
            <div class="register-link">
                <p>Already have an account? <a href="login.php">Log In here</a></p>
            </div>
        </div>
    </div>
</body>
</html>
