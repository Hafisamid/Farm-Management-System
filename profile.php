<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$update_success = false;
$user_id = $_SESSION['user_id']; // Get the user ID from the session

// Fetch user data
$user_data = [];
$stmt = $conn->prepare("SELECT Username, Full_Name, Email, Contact FROM user WHERE User_Id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows > 0) {
    $user_data = $result->fetch_assoc();
}
$stmt->close();

// Update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE user SET Username = ?, Password = ?, Full_Name = ?, Email = ?, Contact = ? WHERE User_Id = ?");
        $stmt->bind_param("sssssi", $username, $password, $full_name, $email, $contact, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE user SET Username = ?, Full_Name = ?, Email = ?, Contact = ? WHERE User_Id = ?");
        $stmt->bind_param("ssssi", $username, $full_name, $email, $contact, $user_id);
    }

    if ($stmt->execute()) {
        $update_success = true;
        header("Location: profile.php?success=1");
        exit();
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Profile</title>
    <link rel="stylesheet" type="text/css" href="profilestyle.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="menu-toggle" onclick="toggleMenu()">
        <div class="bar"></div>
        <div class="bar"></div>
        <div class="bar"></div>
    </div>
    <div class="side-menu" id="sideMenu">
        <div class="menu-header">
            <h2>Farm</h2>
        </div>
        <ul class="menu-list">
            <li><a href="dashboard.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'active' : '' ?>"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="customer.php" class="<?= basename($_SERVER['PHP_SELF']) == 'customer.php' ? 'active' : '' ?>"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="shed_management.php" class="<?= basename($_SERVER['PHP_SELF']) == 'shed_management.php' ? 'active' : '' ?>"><i class="fas fa-warehouse"></i> Shed</a></li>
            <li><a href="information.php" class="<?= basename($_SERVER['PHP_SELF']) == 'information.php' ? 'active' : '' ?>"><i class="fas fa-paw"></i> Livestock</a></li>
            <li><a href="vaccine.php" class="<?= basename($_SERVER['PHP_SELF']) == 'vaccine.php' ? 'active' : '' ?>"><i class="fas fa-syringe"></i> Vaccination</a></li>
            <li><a href="staff.php" class="<?= basename($_SERVER['PHP_SELF']) == 'staff.php' ? 'active' : '' ?>"><i class="fas fa-user-tie"></i> Staff</a></li>
            <li><a href="cow_sale.php" class="<?= basename($_SERVER['PHP_SELF']) == 'cow_sale.php' ? 'active' : '' ?>"><i class="fas fa-cow"></i> Meat Collect</a></li>
            <li><a href="milk_sale.php" class="<?= basename($_SERVER['PHP_SELF']) == 'milk_sale.php' ? 'active' : '' ?>"><i class="fas fa-glass-whiskey"></i> Milk Collect</a></li>
            <li><a href="sale.php" class="<?= basename($_SERVER['PHP_SELF']) == 'sale.php' ? 'active' : '' ?>"><i class="fas fa-dollar-sign"></i> Sales</a></li>
            <li><a href="expense.php" class="<?= basename($_SERVER['PHP_SELF']) == 'expense.php' ? 'active' : '' ?>"><i class="fas fa-receipt"></i> Expense</a></li>
            <li><a href="financialreport.php" class="<?= basename($_SERVER['PHP_SELF']) == 'financialreport.php' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Financial              Report</a></li>
            <li><a href="profile.php" class="<?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>"><i class="fas fa-user"></i> Profile</a></li>
        </ul>
    </div>




    <?php include 'header.php'; ?>

    <br><br>

    <div class="container">
        <div class="icon-header">
            <i class="fas fa-user user-icon"></i>
        </div>
        <div class="icon-header">
            <h2>Update Profile</h2>
        </div>

        <?php if (isset($_GET['success']) && $_GET['success'] == 1): ?>
    <p id="successMessage" class="success">Profile updated successfully!</p>
        <?php endif; ?>

        <form method="post" action="">
            <input type="hidden" name="update" value="1">
            <label for="username">Username:</label>
            <input type="text" name="username" value="<?php echo htmlspecialchars($user_data['Username']); ?>" required><br>
            <label for="password">Password (leave blank to keep current):</label>
            <input type="password" name="password"><br>
            <label for="full_name">Full Name:</label>
            <input type="text" name="full_name" value="<?php echo htmlspecialchars($user_data['Full_Name']); ?>" required><br>
            <label for="email">Email:</label>
            <input type="email" name="email" value="<?php echo htmlspecialchars($user_data['Email']); ?>" required><br>
            <label for="contact">Contact:</label>
            <input type="text" name="contact" value="<?php echo htmlspecialchars($user_data['Contact']); ?>" required><br>
            <input type="submit" value="Update">
        </form>
    </div>

    <script>
        function toggleMenu() {
            var sideMenu = document.getElementById('sideMenu');
            sideMenu.classList.toggle('open');
            document.body.classList.toggle('menu-open');
            localStorage.setItem('menuOpen', sideMenu.classList.contains('open'));
        }

        window.onload = function() {
            var menuOpen = localStorage.getItem('menuOpen') === 'true';
            if (menuOpen) {
                document.getElementById('sideMenu').classList.add('open');
                document.body.classList.add('menu-open');
            }
        }

           setTimeout(function() {
            var message = document.getElementById('successMessage');
            if (message) {
                message.style.display = 'none';
            }
        }, 3000);
    </script>
</body>
</html>
