<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Fetch customers for the dropdown
$customer_data = [];
$result = $conn->query("SELECT customer_id, name FROM customer");
while ($row = $result->fetch_assoc()) {
    $customer_data[] = $row;
}

// Fetch milk sales for the checkboxes
$milk_sale_data = [];
$result = $conn->query("SELECT ms.MilkSale_Id, c.Type, ms.Litre_Collect, ms.Sale_Price_Per_Litre FROM milk_sale ms 
                        JOIN cow c ON ms.Cow_Id = c.Cow_Id");
while ($row = $result->fetch_assoc()) {
    $row['total_price'] = $row['Litre_Collect'] * $row['Sale_Price_Per_Litre'];
    $milk_sale_data[] = $row;
}

// Fetch cow sales for the checkboxes
$cow_sale_data = [];
$result = $conn->query("SELECT cs.CowSale_Id, cs.Name, cs.Quantity, cs.Sale_Price_per_kg FROM cow_sale cs");
while ($row = $result->fetch_assoc()) {
    $row['total_price'] = $row['Quantity'] * $row['Sale_Price_per_kg'];
    $cow_sale_data[] = $row;
}

$record_success = false;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['record_sale'])) {
    $customer_id = $_POST['customer_id'];
    $total_price = 0;

    // Calculate total price for selected milk sales
    if (isset($_POST['milk_sales'])) {
        foreach ($_POST['milk_sales'] as $sale_id) {
            foreach ($milk_sale_data as $sale) {
                if ($sale['MilkSale_Id'] == $sale_id) {
                    $litres_bought = $_POST['litres'][$sale_id];
                    $price = $litres_bought * $sale['Sale_Price_Per_Litre'];
                    $total_price += $price;

                    // Update quantity in milk_sale table
                    $new_quantity = $sale['Litre_Collect'] - $litres_bought;
                    $update_stmt = $conn->prepare("UPDATE milk_sale SET Litre_Collect = ? WHERE MilkSale_Id = ?");
                    $update_stmt->bind_param("di", $new_quantity, $sale_id);
                    if (!$update_stmt->execute()) {
                        die("Error updating milk sale: " . $update_stmt->error);
                    }
                    $update_stmt->close();

                    // Record the sale
                    $sale_date = date("Y-m-d H:i:s");
                    $record_stmt = $conn->prepare("INSERT INTO sales_record (customer_id, item_id, sale_type, quantity, price, sale_date) VALUES (?, ?, 'milk', ?, ?, ?)");
                    $record_stmt->bind_param("iiids", $customer_id, $sale_id, $litres_bought, $price, $sale_date);
                    if (!$record_stmt->execute()) {
                        die("Error recording sale: " . $record_stmt->error);
                    }
                    $record_stmt->close();
                }
            }
        }
    }

    // Calculate total price for selected cow sales
    if (isset($_POST['cow_sales'])) {
        foreach ($_POST['cow_sales'] as $sale_id) {
            foreach ($cow_sale_data as $sale) {
                if ($sale['CowSale_Id'] == $sale_id) {
                    $quantity_bought = $_POST['quantities'][$sale_id];
                    $price = $quantity_bought * $sale['Sale_Price_per_kg'];
                    $total_price += $price;

                    // Update quantity in cow_sale table
                    $new_quantity = $sale['Quantity'] - $quantity_bought;
                    $update_stmt = $conn->prepare("UPDATE cow_sale SET Quantity = ? WHERE CowSale_Id = ?");
                    $update_stmt->bind_param("di", $new_quantity, $sale_id);
                    if (!$update_stmt->execute()) {
                        die("Error updating cow sale: " . $update_stmt->error);
                    }
                    $update_stmt->close();

                    // Record the sale
                    $sale_date = date("Y-m-d H:i:s");
                    $record_stmt = $conn->prepare("INSERT INTO sales_record (customer_id, item_id, sale_type, quantity, price, sale_date) VALUES (?, ?, 'cow', ?, ?, ?)");
                    $record_stmt->bind_param("iiids", $customer_id, $sale_id, $quantity_bought, $price, $sale_date);
                    if (!$record_stmt->execute()) {
                        die("Error recording sale: " . $record_stmt->error);
                    }
                    $record_stmt->close();
                }
            }
        }
    }

    // Update due_pay in the customer table
    $stmt = $conn->prepare("UPDATE customer SET due_pay = due_pay + ? WHERE customer_id = ?");
    $stmt->bind_param("di", $total_price, $customer_id);
    if (!$stmt->execute()) {
        die("Error updating customer: " . $stmt->error);
    }
    $stmt->close();

    $record_success = true;
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Record Sale</title>
    <link rel="stylesheet" type="text/css" href="stylesales.css">
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
        <h2>Record Sale</h2>

        <?php if ($record_success): ?>
            <div class="success-message" id="successMessage">Record Successfully!</div>
        <?php endif; ?>

        <form method="post" action="">
            <label for="customer_id">Customer:</label>
            <select name="customer_id" required>
                <?php foreach ($customer_data as $customer): ?>
                    <option value="<?php echo $customer['customer_id']; ?>"><?php echo htmlspecialchars($customer['name']); ?></option>
                <?php endforeach; ?>
            </select><br>

            <h3>Milk Sales</h3>
            <?php foreach ($milk_sale_data as $milk_sale): ?>
                <div class="sale-item">
                    <input type="checkbox" id="milk_sale_<?php echo $milk_sale['MilkSale_Id']; ?>" name="milk_sales[]" value="<?php echo $milk_sale['MilkSale_Id']; ?>" onchange="toggleInput(this, 'litres_<?php echo $milk_sale['MilkSale_Id']; ?>')">
                    <label for="milk_sale_<?php echo $milk_sale['MilkSale_Id']; ?>">
                        <?php echo htmlspecialchars($milk_sale['Type'] . " - " . $milk_sale['Litre_Collect'] . " L @ RM " . $milk_sale['Sale_Price_Per_Litre'] . "/L (Total: RM " . $milk_sale['total_price'] . ")"); ?>
                    </label>
                    <input type="number" id="litres_<?php echo $milk_sale['MilkSale_Id']; ?>" name="litres[<?php echo $milk_sale['MilkSale_Id']; ?>]" min="1" max="<?php echo $milk_sale['Litre_Collect']; ?>" placeholder="Enter litres" style="display:none;">
                </div>
            <?php endforeach; ?>

            <h3>Cow Sales</h3>
            <?php foreach ($cow_sale_data as $cow_sale): ?>
                <div class="sale-item">
                    <input type="checkbox" id="cow_sale_<?php echo $cow_sale['CowSale_Id']; ?>" name="cow_sales[]" value="<?php echo $cow_sale['CowSale_Id']; ?>" onchange="toggleInput(this, 'quantities_<?php echo $cow_sale['CowSale_Id']; ?>')">
                    <label for="cow_sale_<?php echo $cow_sale['CowSale_Id']; ?>">
                        <?php echo htmlspecialchars($cow_sale['Name'] . " - " . $cow_sale['Quantity'] . " kg @ RM " . $cow_sale['Sale_Price_per_kg'] . "/kg (Total: RM " . $cow_sale['total_price'] . ")"); ?>
                    </label>
                    <input type="number" id="quantities_<?php echo $cow_sale['CowSale_Id']; ?>" name="quantities[<?php echo $cow_sale['CowSale_Id']; ?>]" min="1" max="<?php echo $cow_sale['Quantity']; ?>" placeholder="Enter quantity" style="display:none;">
                </div>
            <?php endforeach; ?>

            <input type="submit" name="record_sale" value="Record Sale">
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
            <?php if ($record_success): ?>
                setTimeout(function() {
                    document.getElementById('successMessage').style.display = 'none';
                }, 3000);
            <?php endif; ?>
        };

        function toggleInput(checkbox, inputId) {
            var input = document.getElementById(inputId);
            if (checkbox.checked) {
                input.style.display = 'inline';
                input.required = true;
            } else {
                input.style.display = 'none';
                input.required = false;
            }
        }
    </script>
</body>
</html>
