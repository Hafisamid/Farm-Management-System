<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Side Menu</title>
    <link rel="stylesheet" href="styless.css">
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
            <li><a href="customer.php" class="<?= basename($_SERVER['PHP_SELF']) == 'customer.php' ? 'active' : '' ?>"><i class="fas fa-users"></i> Customer</a></li>
            <li><a href="shed_management.php" class="<?= basename($_SERVER['PHP_SELF']) == 'shed_management.php' ? 'active' : '' ?>"><i class="fas fa-warehouse"></i> Shed</a></li>
            <li><a href="information.php" class="<?= basename($_SERVER['PHP_SELF']) == 'information.php' ? 'active' : '' ?>"><i class="fas fa-paw"></i> Livestock</a></li>
            <li><a href="vaccine.php" class="<?= basename($_SERVER['PHP_SELF']) == 'vaccine.php' ? 'active' : '' ?>"><i class="fas fa-syringe"></i> Vaccination</a></li>
            <li><a href="staff.php" class="<?= basename($_SERVER['PHP_SELF']) == 'staff.php' ? 'active' : '' ?>"><i class="fas fa-user-tie"></i> Staff</a></li>
            <li><a href="#products"><i class="fas fa-box"></i> Products</a></li>
            <li><a href="cow_sale.php" class="<?= basename($_SERVER['PHP_SELF']) == 'cow_sale.php' ? 'active' : '' ?>"><i class="fas fa-dollar-sign"></i> Meat Sales</a></li>
            <li><a href="#sales"><i class="fas fa-dollar-sign"></i> Sales</a></li>
            <li><a href="#expense"><i class="fas fa-receipt"></i> Expense</a></li>
            <li><a href="#financial-report"><i class="fas fa-chart-line"></i> Financial Report</a></li>
            <li><a href="#settings"><i class="fas fa-cog"></i> Settings</a></li>
            <li><a href="#profile"><i class="fas fa-user"></i> Profile</a></li>
        </ul>
    </div>
    <script src="scriptside.js"></script>
</body>
</html>
