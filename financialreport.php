<?php
include 'db.php';

// Function to check and handle SQL errors
function check_sql_error($stmt, $conn) {
    if (!$stmt) {
        die("SQL error: " . $conn->error);
    }
}

// Get the selected month from the form, default to the current month
$selected_month = isset($_POST['selected_month']) ? $_POST['selected_month'] : date('Y-m');

// Query for total sale
$total_sale_stmt = $conn->prepare("SELECT SUM(due_pay) AS total_sale FROM customer WHERE status_pay = 'Paid'");
check_sql_error($total_sale_stmt, $conn);
$total_sale_stmt->execute();
$total_sale_result = $total_sale_stmt->get_result();
$total_sale = $total_sale_result->fetch_assoc()['total_sale'];

// Query for total cost (assuming total cost is calculated from cow_sale)
$total_cost_stmt = $conn->prepare("SELECT SUM(Sale_Price_per_kg * Quantity) AS total_cost FROM cow_sale");
check_sql_error($total_cost_stmt, $conn);
$total_cost_stmt->execute();
$total_cost_result = $total_cost_stmt->get_result();
$total_cost = $total_cost_result->fetch_assoc()['total_cost'];

// Query for total due
$total_due_stmt = $conn->prepare("SELECT SUM(due_pay) AS total_due FROM customer WHERE status_pay = 'Pending'");
check_sql_error($total_due_stmt, $conn);
$total_due_stmt->execute();
$total_due_result = $total_due_stmt->get_result();
$total_due = $total_due_result->fetch_assoc()['total_due'];

// Query for total expense
$total_expense_stmt = $conn->prepare("SELECT SUM(total_price) AS total_expense FROM expense");
check_sql_error($total_expense_stmt, $conn);
$total_expense_stmt->execute();
$total_expense_result = $total_expense_stmt->get_result();
$total_expense = $total_expense_result->fetch_assoc()['total_expense'];

// Calculate profit
$profit = $total_sale - $total_cost - $total_expense;

// Query for expense report by category for the selected month
$expense_report_stmt = $conn->prepare("
    SELECT c.name AS category_name, SUM(e.total_price) AS total_expense
    FROM expense e
    JOIN category c ON e.category_id = c.category_id
    WHERE DATE_FORMAT(e.date, '%Y-%m') = ?
    GROUP BY c.name
");
check_sql_error($expense_report_stmt, $conn);
$expense_report_stmt->bind_param("s", $selected_month);
$expense_report_stmt->execute();
$expense_report_result = $expense_report_stmt->get_result();

$expense_report_data = [];
while ($row = $expense_report_result->fetch_assoc()) {
    $expense_report_data[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Financial Report</title>
    <link rel="stylesheet" type="text/css" href="stylesfinancialreport.css">
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

    <div class="container">
        <h2>Financial Report</h2>

        <div class="card-container">
            <div class="card sale">
                <i class="fas fa-dollar-sign card-icon"></i>
                <div class="card-content">
                    <h3>Total Sale</h3>
                    <div class="value"> RM <?php echo number_format($total_sale, 2); ?></div>
                </div>
            </div>
            <div class="card cost">
                <i class="fas fa-money-bill-wave card-icon"></i>
                <div class="card-content">
                    <h3>Total Cost</h3>
                    <div class="value"> RM <?php echo number_format($total_cost, 2); ?></div>
                </div>
            </div>
            <div class="card due">
                <i class="fas fa-exclamation-circle card-icon"></i>
                <div class="card-content">
                    <h3>Total Due</h3>
                    <div class="value"> RM <?php echo number_format($total_due, 2); ?></div>
                </div>
            </div>
            <div class="card expense">
                <i class="fas fa-receipt card-icon"></i>
                <div class="card-content">
                    <h3>Total Expense</h3>
                    <div class="value"> RM <?php echo number_format($total_expense, 2); ?></div>
                </div>
            </div>
            <div class="card profit">
                <i class="fas fa-chart-line card-icon"></i>
                <div class="card-content">
                    <h3>Profit</h3>
                    <div class="value"> RM <?php echo number_format($profit, 2); ?></div>
                </div>
            </div>
        </div>

        <h3>Expense Report by Category</h3>

        <!-- Month Filter Form -->
        <form method="post" action="" class="filter-form">
            <label for="selected_month">Select Month:</label>
            <input type="month" name="selected_month" id="selected_month" value="<?php echo $selected_month; ?>" required>
            <input type="submit" value="Filter">
        </form>

        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Total Expense (RM)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expense_report_data as $expense): ?>
                <tr>
                    <td><?php echo htmlspecialchars($expense['category_name']); ?></td>
                    <td> RM <?php echo number_format($expense['total_expense'], 2); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
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
        };
    </script>
</body>
</html>
