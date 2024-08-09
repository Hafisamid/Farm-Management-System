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
$delete_success = false;

// Pagination setup
$results_per_page = 4; // Number of entries to show in a page.
if (isset($_GET["page"])) {
    $page = $_GET["page"];
} else {
    $page = 1;
}
$start_from = ($page-1) * $results_per_page;

// Fetch cow data for dropdowns
$cow_data = [];
$result = $conn->query("SELECT Cow_Id, Type FROM cow WHERE Category_Id = 4");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cow_data[] = $row;
    }
} else {
    die("Error fetching cow data: " . $conn->error);
}

// Fetch cow category data for dropdowns
$category_data = [];
$result = $conn->query("SELECT Category_Id, Category_Name FROM cow_category WHERE Category_Id = 4");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $category_data[] = $row;
    }
} else {
    die("Error fetching category data: " . $conn->error);
}

// Create
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $cow_id = $_POST['cow_id'];
    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];
    $cost_price_per_kg = $_POST['cost_price_per_kg'];
    $sale_price_per_kg = $_POST['sale_price_per_kg'];

    $stmt = $conn->prepare("INSERT INTO cow_sale (Cow_Id, Category_Id, Name, Quantity, Cost_Price_per_kg, Sale_Price_per_kg) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisddd", $cow_id, $category_id, $name, $quantity, $cost_price_per_kg, $sale_price_per_kg);
    $stmt->execute();
    $stmt->close();
}

// Read
$cow_sale_data = [];
$result = $conn->query("SELECT cs.*, c.Type, cc.Category_Name FROM cow_sale cs LEFT JOIN cow c ON cs.Cow_Id = c.Cow_Id LEFT JOIN cow_category cc ON cs.Category_Id = cc.Category_Id LIMIT $start_from, $results_per_page");
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cow_sale_data[] = $row;
    }
} else {
    die("Error fetching cow sale data: " . $conn->error);
}

// Total pages calculation
$result = $conn->query("SELECT COUNT(CowSale_Id) AS total FROM cow_sale");
$row = $result->fetch_assoc();
$total_pages = ceil($row["total"] / $results_per_page);

// Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $cow_sale_id = $_POST['cow_sale_id'];
    $cow_id = $_POST['cow_id'];
    $category_id = $_POST['category_id'];
    $name = $_POST['name'];
    $quantity = $_POST['quantity'];
    $cost_price_per_kg = $_POST['cost_price_per_kg'];
    $sale_price_per_kg = $_POST['sale_price_per_kg'];

    $stmt = $conn->prepare("UPDATE cow_sale SET Cow_Id = ?, Category_Id = ?, Name = ?, Quantity = ?, Cost_Price_per_kg = ?, Sale_Price_per_kg = ? WHERE CowSale_Id = ?");
    $stmt->bind_param("iisdddi", $cow_id, $category_id, $name, $quantity, $cost_price_per_kg, $sale_price_per_kg, $cow_sale_id);
    if ($stmt->execute()) {
        $update_success = true;
    }
    $stmt->close();
}

// Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $cow_sale_id = $_POST['cow_sale_id'];
    $stmt = $conn->prepare("DELETE FROM cow_sale WHERE CowSale_Id = ?");
    $stmt->bind_param("i", $cow_sale_id);
    if ($stmt->execute()) {
        $delete_success = true;
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Cow Sale Management</title>
    <link rel="stylesheet" type="text/css" href="stylesale.css">
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
            <li><a href="financialreport.php" class="<?= basename($_SERVER['PHP_SELF']) == 'financialreport.php' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Financial Report</a></li>
            <li><a href="profile.php" class="<?= basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : '' ?>"><i class="fas fa-user"></i> Profile</a></li>
        </ul>
    </div>

    <?php include 'header.php'; ?>

    <br><br>

    <div class="container">
        <h2>Cow Sale Management</h2>

        <!-- Add Cow Sale Button -->
        <button onclick="openCreateModal()">Add Cow Sale</button>

        <!-- The Create Modal -->
        <div id="createModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeCreateModal()">&times;</span>
                <h3>Create New Cow Sale</h3>
                <form method="post" action="">
                    <input type="hidden" name="create" value="1">
                    <label for="cow_id">Cow:</label>
                    <select name="cow_id" required>
                        <?php foreach ($cow_data as $cow): ?>
                            <option value="<?php echo $cow['Cow_Id']; ?>"><?php echo htmlspecialchars($cow['Type']); ?></option>
                        <?php endforeach; ?>
                    </select><br>
                    <label for="category_id">Category:</label>
                    <select name="category_id" required>
                        <?php foreach ($category_data as $category): ?>
                            <option value="<?php echo $category['Category_Id']; ?>"><?php echo htmlspecialchars($category['Category_Name']); ?></option>
                        <?php endforeach; ?>
                    </select><br>
                    <label for="name">Name:</label>
                    <input type="text" name="name" required><br>
                    <label for="quantity">Quantity (kg):</label>
                    <input type="number" name="quantity" step="0.01" required><br>
                    <label for="cost_price_per_kg">Cost Price per kg:</label>
                    <input type="number" name="cost_price_per_kg" step="0.01" required><br>
                    <label for="sale_price_per_kg">Sale Price per kg:</label>
                    <input type="number" name="sale_price_per_kg" step="0.01" required><br>
                    <input type="submit" value="Create">
                </form>
            </div>
        </div>

        <h3>Cow Sale Records</h3>
        <table>
            <thead>
                <tr>
                    <th>Cow Sale ID</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Category</th>
                    <th>Quantity (kg)</th>
                    <th>Cost Price per kg</th>
                    <th>Sale Price per kg</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($cow_sale_data as $sale): ?>
                <tr>
                    <td><?php echo $sale['CowSale_Id']; ?></td>
                    <td><?php echo $sale['Name']; ?></td>
                    <td><?php echo $sale['Type']; ?></td>
                    <td><?php echo $sale['Category_Name']; ?></td>
                    <td><?php echo $sale['Quantity']; ?></td>
                    <td><?php echo $sale['Cost_Price_per_kg']; ?></td>
                    <td><?php echo $sale['Sale_Price_per_kg']; ?></td>
                    <td>
                        <button class="update" onclick="openUpdateModal(<?php echo $sale['CowSale_Id']; ?>, '<?php echo $sale['Name']; ?>', <?php echo $sale['Cow_Id']; ?>, <?php echo $sale['Category_Id']; ?>, <?php echo $sale['Quantity']; ?>, <?php echo $sale['Cost_Price_per_kg']; ?>, <?php echo $sale['Sale_Price_per_kg']; ?>)">Update</button>
                        <form method="post" action="" class="delete-form">
                            <input type="hidden" name="cow_sale_id" value="<?php echo $sale['CowSale_Id']; ?>">
                            <input type="hidden" name="delete" value="1">
                            <button class="delete" type="submit" onclick="return confirmDelete();">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="pagination">
            <?php
            for ($i=1; $i<=$total_pages; $i++) {
                echo "<a href='cow_sale.php?page=".$i."'".($i == $page ? " class='active'" : "").">".$i."</a> ";
            }
            ?>
        </div>
    </div>

    <!-- The Update Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUpdateModal()">&times;</span>
            <h3>Update Cow Sale</h3>
            <form method="post" action="">
                <input type="hidden" name="cow_sale_id" id="updateCowSaleId">
                <input type="hidden" name="update" value="1">
                <label for="cow_id">Cow:</label>
                <select name="cow_id" id="updateCowId" required>
                    <?php foreach ($cow_data as $cow): ?>
                        <option value="<?php echo $cow['Cow_Id']; ?>"><?php echo htmlspecialchars($cow['Type']); ?></option>
                    <?php endforeach; ?>
                </select><br>
                <label for="category_id">Category:</label>
                <select name="category_id" id="updateCategoryId" required>
                    <?php foreach ($category_data as $category): ?>
                        <option value="<?php echo $category['Category_Id']; ?>"><?php echo htmlspecialchars($category['Category_Name']); ?></option>
                    <?php endforeach; ?>
                </select><br>
                <label for="name">Name:</label>
                <input type="text" name="name" id="updateName" required><br>
                <label for="quantity">Quantity (kg):</label>
                <input type="number" name="quantity" id="updateQuantity" step="0.01" required><br>
                <label for="cost_price_per_kg">Cost Price per kg:</label>
                <input type="number" name="cost_price_per_kg" id="updateCostPricePerKg" step="0.01" required><br>
                <label for="sale_price_per_kg">Sale Price per kg:</label>
                <input type="number" name="sale_price_per_kg" id="updateSalePricePerKg" step="0.01" required><br>
                <input type="submit" value="Update">
            </form>
        </div>
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
            if (<?php echo $delete_success ? 'true' : 'false'; ?>) {
                alert("Successfully Deleted");
                window.location.href = 'cow_sale.php';
            }
            if (<?php echo $update_success ? 'true' : 'false'; ?>) {
                alert("Successfully Updated");
                window.location.href = 'cow_sale.php';
            }
        };

        function openCreateModal() {
            document.getElementById('createModal').style.display = 'block';
        }

        function closeCreateModal() {
            document.getElementById('createModal').style.display = 'none';
        }

        function openUpdateModal(cowSaleId, name, cowId, categoryId, quantity, costPricePerKg, salePricePerKg) {
            document.getElementById('updateCowSaleId').value = cowSaleId;
            document.getElementById('updateCowId').value = cowId;
            document.getElementById('updateCategoryId').value = categoryId;
            document.getElementById('updateName').value = name;
            document.getElementById('updateQuantity').value = quantity;
            document.getElementById('updateCostPricePerKg').value = costPricePerKg;
            document.getElementById('updateSalePricePerKg').value = salePricePerKg;
            document.getElementById('updateModal').style.display = "block";
        }

        function closeUpdateModal() {
            document.getElementById('updateModal').style.display = "none";
        }

        function confirmDelete() {
            return confirm("Are you sure you want to delete this record?");
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('createModal')) {
                closeCreateModal();
            }
            if (event.target == document.getElementById('updateModal')) {
                closeUpdateModal();
            }
        }
    </script>
</body>
</html>
