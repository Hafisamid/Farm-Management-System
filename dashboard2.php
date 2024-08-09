<?php
include 'db.php';

// Function to check and handle SQL errors
function check_sql_error($stmt, $conn) {
    if (!$stmt) {
        die("SQL error: " . $conn->error);
    }
}

// Query for total staff
$total_staff_stmt = $conn->prepare("SELECT COUNT(*) AS total_staff FROM user");
check_sql_error($total_staff_stmt, $conn);
$total_staff_stmt->execute();
$total_staff_result = $total_staff_stmt->get_result();
$total_staff = $total_staff_result->fetch_assoc()['total_staff'];

// Query for total cows
$total_cows_stmt = $conn->prepare("SELECT SUM(Quantity) AS total_cows FROM cow");
check_sql_error($total_cows_stmt, $conn);
$total_cows_stmt->execute();
$total_cows_result = $total_cows_stmt->get_result();
$total_cows = $total_cows_result->fetch_assoc()['total_cows'];

// Query for cows by category
$cows_by_category_stmt = $conn->prepare("
    SELECT cc.Category_Name, SUM(c.Quantity) AS total_cows
    FROM cow c
    LEFT JOIN cow_category cc ON c.Category_Id = cc.Category_Id
    GROUP BY cc.Category_Name
");
check_sql_error($cows_by_category_stmt, $conn);
$cows_by_category_stmt->execute();
$cows_by_category_result = $cows_by_category_stmt->get_result();

$cows_by_category_data = [];
while ($row = $cows_by_category_result->fetch_assoc()) {
    if (!empty($row['Category_Name']) && !empty($row['total_cows'])) {
        $cows_by_category_data[] = $row;
    }
}

// Query for total milk collected
$total_milk_collected_stmt = $conn->prepare("SELECT SUM(Litre_Collect) AS total_milk_collected FROM milk_sale");
check_sql_error($total_milk_collected_stmt, $conn);
$total_milk_collected_stmt->execute();
$total_milk_collected_result = $total_milk_collected_stmt->get_result();
$total_milk_collected = $total_milk_collected_result->fetch_assoc()['total_milk_collected'];

// Query for total meat collected
$total_meat_collected_stmt = $conn->prepare("SELECT SUM(Quantity) AS total_meat_collected FROM cow_sale");
check_sql_error($total_meat_collected_stmt, $conn);
$total_meat_collected_stmt->execute();
$total_meat_collected_result = $total_meat_collected_stmt->get_result();
$total_meat_collected = $total_meat_collected_result->fetch_assoc()['total_meat_collected'];

// Query for total sales
$total_sales_stmt = $conn->prepare("SELECT SUM(due_pay) AS total_sales FROM customer WHERE status_pay = 'Paid'");
check_sql_error($total_sales_stmt, $conn);
$total_sales_stmt->execute();
$total_sales_result = $total_sales_stmt->get_result();
$total_sales = $total_sales_result->fetch_assoc()['total_sales'];

// Query for total expenses
$total_expenses_stmt = $conn->prepare("SELECT SUM(total_price) AS total_expenses FROM expense");
check_sql_error($total_expenses_stmt, $conn);
$total_expenses_stmt->execute();
$total_expenses_result = $total_expenses_stmt->get_result();
$total_expenses = $total_expenses_result->fetch_assoc()['total_expenses'];

// Query for monthly sales for the past year based on due_pay and status_pay from customer table
$monthly_sales_stmt = $conn->prepare("
    SELECT DATE_FORMAT(DATE_SUB(NOW(), INTERVAL (n - 1) MONTH), '%Y-%m') AS month,
           COALESCE(SUM(IF(DATE_FORMAT(customer.created_at, '%Y-%m') = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL (n - 1) MONTH), '%Y-%m') AND customer.status_pay = 'Paid', customer.due_pay, 0)), 0) AS total_sales
    FROM (
        SELECT 1 AS n UNION ALL SELECT 2 UNION ALL SELECT 3 UNION ALL SELECT 4 UNION ALL SELECT 5 UNION ALL SELECT 6
        UNION ALL SELECT 7 UNION ALL SELECT 8 UNION ALL SELECT 9 UNION ALL SELECT 10 UNION ALL SELECT 11 UNION ALL SELECT 12
    ) AS numbers
    LEFT JOIN customer ON DATE_FORMAT(customer.created_at, '%Y-%m') = DATE_FORMAT(DATE_SUB(NOW(), INTERVAL (numbers.n - 1) MONTH), '%Y-%m')
    GROUP BY month
    ORDER BY month
");
check_sql_error($monthly_sales_stmt, $conn);
$monthly_sales_stmt->execute();
$monthly_sales_result = $monthly_sales_stmt->get_result();

$monthly_sales_data = [];
while ($row = $monthly_sales_result->fetch_assoc()) {
    $monthly_sales_data[] = $row;
}


// Fetch customer list
$customer_list_stmt = $conn->prepare("SELECT name, due_pay, status_pay FROM customer");
check_sql_error($customer_list_stmt, $conn);
$customer_list_stmt->execute();
$customer_list_result = $customer_list_stmt->get_result();

$customer_list_data = [];
while ($row = $customer_list_result->fetch_assoc()) {
    $customer_list_data[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Dashboard</title>
    <link rel="stylesheet" type="text/css" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .chart-container, .customer-container {
            display: none;
        }
    </style>
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
            <li><a href="dashboard2.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard2.php' ? 'active' : '' ?>"><i class="fas fa-home"></i> Home</a></li>
            <li><a href="customer2.php" class="<?= basename($_SERVER['PHP_SELF']) == 'customer2.php' ? 'active' : '' ?>"><i class="fas fa-users"></i> Customers</a></li>
            <li><a href="shed_management2.php" class="<?= basename($_SERVER['PHP_SELF']) == 'shed_management2.php' ? 'active' : '' ?>"><i class="fas fa-warehouse"></i> Shed</a></li>
            <li><a href="information2.php" class="<?= basename($_SERVER['PHP_SELF']) == 'information2.php' ? 'active' : '' ?>"><i class="fas fa-paw"></i> Livestock</a></li>
            <li><a href="vaccine2.php" class="<?= basename($_SERVER['PHP_SELF']) == 'vaccine2.php' ? 'active' : '' ?>"><i class="fas fa-syringe"></i> Vaccination</a></li>
            <li><a href="cow_sale2.php" class="<?= basename($_SERVER['PHP_SELF']) == 'cow_sale2.php' ? 'active' : '' ?>"><i class="fas fa-cow"></i> Meat Collect</a></li>
            <li><a href="milk_sale2.php" class="<?= basename($_SERVER['PHP_SELF']) == 'milk_sale2.php' ? 'active' : '' ?>"><i class="fas fa-glass-whiskey"></i> Milk Collect</a></li>
            <li><a href="sale2.php" class="<?= basename($_SERVER['PHP_SELF']) == 'sale2.php' ? 'active' : '' ?>"><i class="fas fa-dollar-sign"></i> Sales</a></li>
            <li><a href="expense2.php" class="<?= basename($_SERVER['PHP_SELF']) == 'expense2.php' ? 'active' : '' ?>"><i class="fas fa-receipt"></i> Expense</a></li>
            <li><a href="profile2.php" class="<?= basename($_SERVER['PHP_SELF']) == 'profile2.php' ? 'active' : '' ?>"><i class="fas fa-user"></i> Profile</a></li>
        </ul>
    </div>

    <?php include 'header.php'; ?>

    <div class="container">
        <h2>Dashboard</h2>

        <div class="card-container">
            <div class="card staff">
                <i class="fas fa-users card-icon"></i>
                <div class="card-content">
                    <h3>Total Staff</h3>
                    <div class="value"><?php echo htmlspecialchars($total_staff); ?></div>
                    <div class="description">Total number of staff</div>
                </div>
            </div>
            <div class="card cows">
                <i class="fas fa-cow card-icon"></i>
                <div class="card-content">
                    <h3>Total Cows</h3>
                    <div class="value"><?php echo htmlspecialchars($total_cows); ?></div>
                    <div class="description">Total number of cows</div>
                </div>
            </div>
            <div class="card milk-collected">
                <i class="fas fa-glass-whiskey card-icon"></i>
                <div class="card-content">
                    <h3>Total Milk Collected</h3>
                    <div class="value"><?php echo htmlspecialchars($total_milk_collected); ?> liters</div>
                    <div class="description">Total milk collected</div>
                </div>
            </div>
            <div class="card meat-collected">
                <i class="fas fa-drumstick-bite card-icon"></i>
                <div class="card-content">
                    <h3>Total Meat Collected</h3>
                    <div class="value"><?php echo htmlspecialchars($total_meat_collected); ?> kg</div>
                    <div class="description">Total meat collected</div>
                </div>
            </div>
            <div class="card milk-sold">
                <i class="fas fa-shopping-cart card-icon"></i>
                <div class="card-content">
                    <h3>Total Sales</h3>
                    <div class="value">RM <?php echo htmlspecialchars($total_sales); ?></div>
                    <div class="description">Total sales from paid customers</div>
                </div>
            </div>
            <div class="card expenses">
                <i class="fas fa-dollar-sign card-icon"></i>
                <div class="card-content">
                    <h3>Total Expenses</h3>
                    <div class="value">RM <?php echo htmlspecialchars($total_expenses); ?></div>
                    <div class="description">Total expenses</div>
                </div>
            </div>
        </div>

        <div class="card-container">
            <?php foreach ($cows_by_category_data as $category): ?>
            <div class="card category">
                <i class="fas fa-cow card-icon"></i>
                <div class="card-content">
                    <h3><?php echo htmlspecialchars($category['Category_Name']); ?></h3>
                    <div class="value"><?php echo htmlspecialchars($category['total_cows']); ?></div>
                    <div class="description">Total cows in this category</div>
                </div>
            </div>
            <?php endforeach; ?> 
        </div>
        
        <div class="chart-and-customer">
            <div class="chart-container">
                <h3>Monthly Sales</h3>
                <canvas id="monthlySalesChart" width="400" height="200"></canvas>
            </div>

            <div class="customer-container">
                <h3>Customer List</h3>
                <table>
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Due Pay</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($customer_list_data as $customer): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($customer['name']); ?></td>
                            <td><?php echo htmlspecialchars($customer['due_pay']); ?></td>
                            <td><?php echo htmlspecialchars($customer['status_pay']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        // Pass PHP data to JavaScript
        const monthlySalesData = <?php echo json_encode($monthly_sales_data); ?>;
        
        // Extract months and sales from the data
        const labels = monthlySalesData.map(data => data.month);
        const sales = monthlySalesData.map(data => data.total_sales);

        const ctxMonthlySales = document.getElementById('monthlySalesChart').getContext('2d');
        new Chart(ctxMonthlySales, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Monthly Sales (RM)',
                    data: sales,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Side menu functions
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
    </script>
</body>
</html>
