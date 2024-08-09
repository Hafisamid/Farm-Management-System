<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Pagination settings
$results_per_page = 4; // Number of results per page
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start_from = ($page - 1) * $results_per_page;

// Fetch categories for the dropdown
$category_data = [];
$result = $conn->query("SELECT category_id, name FROM category");
while ($row = $result->fetch_assoc()) {
    $category_data[] = $row;
}

// Initialize success flags
$delete_success = false;
$update_success = false;

// Create
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $category_id = $_POST['category_id'];
    $date = $_POST['date'];
    $total_price = $_POST['total_price'];
    $prove = '';

    if (isset($_FILES['prove']) && $_FILES['prove']['error'] == 0) {
        $prove = 'uploads/' . basename($_FILES['prove']['name']);
        move_uploaded_file($_FILES['prove']['tmp_name'], $prove);
    }

    $stmt = $conn->prepare("INSERT INTO expense (category_id, date, total_price, prove) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isds", $category_id, $date, $total_price, $prove);
    $stmt->execute();
    $stmt->close();
}

// Read with pagination
$expense_data = [];
$result = $conn->query("SELECT e.*, c.name as category_name FROM expense e 
                        LEFT JOIN category c ON e.category_id = c.category_id
                        LIMIT $start_from, $results_per_page");
while ($row = $result->fetch_assoc()) {
    $expense_data[] = $row;
}

// Count total records for pagination
$result = $conn->query("SELECT COUNT(expense_id) AS total FROM expense");
$row = $result->fetch_assoc();
$total_records = $row['total'];
$total_pages = ceil($total_records / $results_per_page);

// Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $expense_id = $_POST['expense_id'];
    $category_id = $_POST['category_id'];
    $date = $_POST['date'];
    $total_price = $_POST['total_price'];
    $prove = $_POST['existing_prove'];

    if (isset($_FILES['prove']) && $_FILES['prove']['error'] == 0) {
        $prove = 'uploads/' . basename($_FILES['prove']['name']);
        move_uploaded_file($_FILES['prove']['tmp_name'], $prove);
    }

    $stmt = $conn->prepare("UPDATE expense SET category_id = ?, date = ?, total_price = ?, prove = ? WHERE expense_id = ?");
    $stmt->bind_param("isdsi", $category_id, $date, $total_price, $prove, $expense_id);
    if ($stmt->execute()) {
        $update_success = true;
    }
    $stmt->close();
}

// Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $expense_id = $_POST['expense_id'];
    $stmt = $conn->prepare("DELETE FROM expense WHERE expense_id = ?");
    $stmt->bind_param("i", $expense_id);
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
    <title>Expense Management</title>
    <link rel="stylesheet" type="text/css" href="styleexpense.css">
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
        <h2>Expense Management</h2>

        <!-- Add Expense Button -->
        <button class="add-expense-btn" onclick="openCreateModal()">Add Expense</button>

        <!-- The Create Modal -->
        <div id="createModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeCreateModal()">&times;</span>
                <h3>Create New Expense</h3>
                <form method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="create" value="1">
                    <label for="category_id">Category:</label>
                    <select name="category_id" required>
                        <?php foreach ($category_data as $category): ?>
                            <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                        <?php endforeach; ?>
                    </select><br>
                    <label for="date">Date:</label>
                    <input type="date" name="date" required><br>
                    <label for="total_price">Total Price:</label>
                    <input type="number" name="total_price" required><br>
                    <label for="prove">Prove:</label>
                    <input type="file" name="prove"><br>
                    <input type="submit" value="Create">
                </form>
            </div>
        </div>

        <h3>Expense Records</h3>
        <table>
            <thead>
                <tr>
                    <th>Expense ID</th>
                    <th>Category</th>
                    <th>Date</th>
                    <th>Total Price</th>
                    <th>Prove</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($expense_data as $expense): ?>
                <tr>
                    <td><?php echo $expense['expense_id']; ?></td>
                    <td><?php echo htmlspecialchars($expense['category_name']); ?></td>
                    <td><?php echo $expense['date']; ?></td>
                    <td><?php echo $expense['total_price']; ?></td>
                    <td>
                        <?php if (!empty($expense['prove'])): ?>
                            <a href="<?php echo $expense['prove']; ?>" target="_blank">View</a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <button class="update" onclick="openUpdateModal(<?php echo $expense['expense_id']; ?>, <?php echo $expense['category_id']; ?>, '<?php echo $expense['date']; ?>', <?php echo $expense['total_price']; ?>, '<?php echo $expense['prove']; ?>')">Update</button>
                        <form method="post" action="" class="delete-form">
                            <input type="hidden" name="expense_id" value="<?php echo $expense['expense_id']; ?>">
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
                <a href="expense.php?page=<?php echo $i; ?>" class="<?php if ($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>
        </div>
    </div>

    <!-- The Update Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUpdateModal()">&times;</span>
            <h3>Update Expense</h3>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="expense_id" id="updateExpenseId">
                <input type="hidden" name="update" value="1">
                <input type="hidden" name="existing_prove" id="updateExistingProve">
                <label for="category_id">Category:</label>
                <select name="category_id" id="updateCategoryId" required>
                    <?php foreach ($category_data as $category): ?>
                        <option value="<?php echo $category['category_id']; ?>"><?php echo htmlspecialchars($category['name']); ?></option>
                    <?php endforeach; ?>
                </select><br>
                <label for="date">Date:</label>
                <input type="date" name="date" id="updateDate" required><br>
                <label for="total_price">Total Price:</label>
                <input type="number" name="total_price" id="updateTotalPrice" required><br>
                <label for="prove">Prove:</label>
                <input type="file" name="prove"><br>
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
                window.location.href = 'expense.php';
            }
            
            if (<?php echo $update_success ? 'true' : 'false'; ?>) {
                alert("Successfully Updated");
                window.location.href = 'expense.php';
            }

        };

        function openCreateModal() {
            document.getElementById('createModal').style.display = 'block';
        }

        function closeCreateModal() {
            document.getElementById('createModal').style.display = 'none';
        }

        function openUpdateModal(expenseId, categoryId, date, totalPrice, prove) {
            document.getElementById('updateExpenseId').value = expenseId;
            document.getElementById('updateCategoryId').value = categoryId;
            document.getElementById('updateDate').value = date;
            document.getElementById('updateTotalPrice').value = totalPrice;
            document.getElementById('updateExistingProve').value = prove;
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
