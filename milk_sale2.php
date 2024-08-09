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
$user_id = $_SESSION['user_id'];

// Fetch cows for the dropdown with Category_Id = 3
$cow_data = [];
$result = $conn->query("SELECT Cow_Id, Type FROM cow WHERE Category_Id = 3");
while ($row = $result->fetch_assoc()) {
    $cow_data[] = $row;
}

// Pagination logic
$results_per_page = 4; // Number of entries to show in a page.
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Create
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $cow_id = $_POST['cow_id'];
    $litre_collect = $_POST['litre_collect'];
    $date_collected = $_POST['date_collected'];
    $cost_price_per_litre = $_POST['cost_price_per_litre'];
    $sale_price_per_litre = $_POST['sale_price_per_litre'];
    $category_id = 3; // Default category_id

    $stmt = $conn->prepare("INSERT INTO milk_sale (User_Id, Cow_Id, Category_Id, Litre_Collect, Date_Collected, Cost_Price_Per_Litre, Sale_Price_Per_Litre) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiissdd", $user_id, $cow_id, $category_id, $litre_collect, $date_collected, $cost_price_per_litre, $sale_price_per_litre);
    $stmt->execute();
    $stmt->close();
}

// Read
$milk_sale_data = [];
$result = $conn->query("SELECT ms.*, u.Username, c.Type, cc.Category_Name FROM milk_sale ms 
                        JOIN user u ON ms.User_Id = u.User_Id 
                        JOIN cow c ON ms.Cow_Id = c.Cow_Id 
                        JOIN cow_category cc ON ms.Category_Id = cc.Category_Id
                        LIMIT $start_from, $results_per_page");
while ($row = $result->fetch_assoc()) {
    $milk_sale_data[] = $row;
}

// Count total records for pagination
$result = $conn->query("SELECT COUNT(MilkSale_Id) AS total FROM milk_sale");
$row = $result->fetch_assoc();
$total_pages = ceil($row["total"] / $results_per_page);

// Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $milk_sale_id = $_POST['milk_sale_id'];
    $cow_id = $_POST['cow_id'];
    $litre_collect = $_POST['litre_collect'];
    $date_collected = $_POST['date_collected'];
    $cost_price_per_litre = $_POST['cost_price_per_litre'];
    $sale_price_per_litre = $_POST['sale_price_per_litre'];
    $category_id = 3; // Default category_id

    $stmt = $conn->prepare("UPDATE milk_sale SET User_Id = ?, Cow_Id = ?, Category_Id = ?, Litre_Collect = ?, Date_Collected = ?, Cost_Price_Per_Litre = ?, Sale_Price_Per_Litre = ? WHERE MilkSale_Id = ?");
    $stmt->bind_param("iiissddi", $user_id, $cow_id, $category_id, $litre_collect, $date_collected, $cost_price_per_litre, $sale_price_per_litre, $milk_sale_id);
    if ($stmt->execute()) {
        $update_success = true;
    }
    $stmt->close();
}

// Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $milk_sale_id = $_POST['milk_sale_id'];
    $stmt = $conn->prepare("DELETE FROM milk_sale WHERE MilkSale_Id = ?");
    $stmt->bind_param("i", $milk_sale_id);
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
    <title>Milk Sale Management</title>
    <link rel="stylesheet" type="text/css" href="stylemilksale.css">
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

    <br><br>

    <div class="container">
        <h2>Milk Sale Management</h2>

        <!-- Add Milk Sale Button -->
        <button onclick="openCreateModal()">Add Milk Sale</button>

        <!-- The Create Modal -->
        <div id="createModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeCreateModal()">&times;</span>
                <h3>Create New Milk Sale</h3>
                <form method="post" action="">
                    <input type="hidden" name="create" value="1">
                    <label for="cow_id">Cow:</label>
                    <select name="cow_id" required>
                        <?php foreach ($cow_data as $cow): ?>
                            <option value="<?php echo $cow['Cow_Id']; ?>"><?php echo htmlspecialchars($cow['Type']); ?></option>
                        <?php endforeach; ?>
                    </select><br>
                    <label for="litre_collect">Litre Collected:</label>
                    <input type="number" name="litre_collect" step="0.01" required><br>
                    <label for="date_collected">Date Collected:</label>
                    <input type="date" name="date_collected" required><br>
                    <label for="cost_price_per_litre">Cost Price per Litre:</label>
                    <input type="number" name="cost_price_per_litre" step="0.01" required><br>
                    <label for="sale_price_per_litre">Sale Price per Litre:</label>
                    <input type="number" name="sale_price_per_litre" step="0.01" required><br>
                    <input type="submit" value="Create">
                </form>
            </div>
        </div>

        <h3>Milk Sale Records</h3>
        <table>
            <thead>
                <tr>
                    <th>Milk Sale ID</th>
                    <th>Username</th>
                    <th>Cow Type</th>
                    <th>Category</th>
                    <th>Litre Collected</th>
                    <th>Date Collected</th>
                    <th>Cost Price/Litre</th>
                    <th>Sale Price/Litre</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($milk_sale_data as $milk_sale): ?>
                <tr>
                    <td><?php echo $milk_sale['MilkSale_Id']; ?></td>
                    <td><?php echo htmlspecialchars($milk_sale['Username']); ?></td>
                    <td><?php echo htmlspecialchars($milk_sale['Type']); ?></td>
                    <td><?php echo htmlspecialchars($milk_sale['Category_Name']); ?></td>
                    <td><?php echo $milk_sale['Litre_Collect']; ?></td>
                    <td><?php echo $milk_sale['Date_Collected']; ?></td>
                    <td><?php echo $milk_sale['Cost_Price_Per_Litre']; ?></td>
                    <td><?php echo $milk_sale['Sale_Price_Per_Litre']; ?></td>
                    <td>
                        <button class="update" onclick="openUpdateModal(<?php echo $milk_sale['MilkSale_Id']; ?>, <?php echo $milk_sale['Cow_Id']; ?>, <?php echo $milk_sale['Litre_Collect']; ?>, '<?php echo $milk_sale['Date_Collected']; ?>', <?php echo $milk_sale['Cost_Price_Per_Litre']; ?>, <?php echo $milk_sale['Sale_Price_Per_Litre']; ?>)">Update</button>
                        <form method="post" action="" class="delete-form">
                            <input type="hidden" name="milk_sale_id" value="<?php echo $milk_sale['MilkSale_Id']; ?>">
                            <input type="hidden" name="delete" value="1">
                            <button class="delete" type="submit" onclick="return confirmDelete();">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <!-- Pagination controls -->
        <div class="pagination">
            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                <a href="milk_sale2.php?page=<?php echo $i; ?>" class="<?php if ($page == $i) echo 'active'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>

    <!-- The Update Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUpdateModal()">&times;</span>
            <h3>Update Milk Sale</h3>
            <form method="post" action="">
                <input type="hidden" name="milk_sale_id" id="updateMilkSaleId">
                <input type="hidden" name="update" value="1">
                <label for="cow_id">Cow:</label>
                <select name="cow_id" id="updateCowId" required>
                    <?php foreach ($cow_data as $cow): ?>
                        <option value="<?php echo $cow['Cow_Id']; ?>"><?php echo htmlspecialchars($cow['Type']); ?></option>
                    <?php endforeach; ?>
                </select><br>
                <label for="litre_collect">Litre Collected:</label>
                <input type="number" name="litre_collect" id="updateLitreCollect" step="0.01" required><br>
                <label for="date_collected">Date Collected:</label>
                <input type="date" name="date_collected" id="updateDateCollected" required><br>
                <label for="cost_price_per_litre">Cost Price per Litre:</label>
                <input type="number" name="cost_price_per_litre" id="updateCostPricePerLitre" step="0.01" required><br>
                <label for="sale_price_per_litre">Sale Price per Litre:</label>
                <input type="number" name="sale_price_per_litre" id="updateSalePricePerLitre" step="0.01" required><br>
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
                window.location.href = 'milk_sale2.php';
            }
            if (<?php echo $update_success ? 'true' : 'false'; ?>) {
                alert("Successfully Updated");
                window.location.href = 'milk_sale2.php';
            }
        };

        function openCreateModal() {
            document.getElementById('createModal').style.display = 'block';
        }

        function closeCreateModal() {
            document.getElementById('createModal').style.display = 'none';
        }

        function openUpdateModal(milkSaleId, cowId, litreCollect, dateCollected, costPricePerLitre, salePricePerLitre) {
            document.getElementById('updateMilkSaleId').value = milkSaleId;
            document.getElementById('updateCowId').value = cowId;
            document.getElementById('updateLitreCollect').value = litreCollect;
            document.getElementById('updateDateCollected').value = dateCollected;
            document.getElementById('updateCostPricePerLitre').value = costPricePerLitre;
            document.getElementById('updateSalePricePerLitre').value = salePricePerLitre;
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
