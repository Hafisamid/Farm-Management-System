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

// Create
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $name = $_POST['name'];
    $due_pay = $_POST['due_pay'];
    $status_pay = $_POST['status_pay'];
    $created_at = $_POST['created_at'];

    $stmt = $conn->prepare("INSERT INTO customer (name, due_pay, status_pay, created_at) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sdss", $name, $due_pay, $status_pay, $created_at);
    $stmt->execute();
    $stmt->close();
}

// Read
$results_per_page = 4;
$total_results_query = $conn->query("SELECT COUNT(*) AS total FROM customer");
$total_results = $total_results_query->fetch_assoc()['total'];
$total_pages = ceil($total_results / $results_per_page);

if (!isset($_GET['page'])) {
    $page = 1;
} else {
    $page = $_GET['page'];
}

$starting_limit_number = ($page - 1) * $results_per_page;

$customer_data = [];
$result = $conn->query("SELECT * FROM customer LIMIT " . $starting_limit_number . ',' . $results_per_page);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $customer_data[] = $row;
    }
} else {
    die("Error fetching customer data: " . $conn->error);
}

// Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $customer_id = $_POST['customer_id'];
    $name = $_POST['name'];
    $due_pay = $_POST['due_pay'];
    $status_pay = $_POST['status_pay'];
    $created_at = $_POST['created_at'];

    $stmt = $conn->prepare("UPDATE customer SET name = ?, due_pay = ?, status_pay = ?, created_at = ? WHERE customer_id = ?");
    $stmt->bind_param("sdssi", $name, $due_pay, $status_pay, $created_at, $customer_id);
    if ($stmt->execute()) {
        $update_success = true;
    }
    $stmt->close();
}

// Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $customer_id = $_POST['customer_id'];
    $stmt = $conn->prepare("DELETE FROM customer WHERE customer_id = ?");
    $stmt->bind_param("i", $customer_id);
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
    <title>Customer Management</title>
    <link rel="stylesheet" type="text/css" href="stylecustomer.css">
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
        <h2>Customer Management</h2>

        <!-- Add Customer Button -->
        <button onclick="openCreateModal()">Add Customer</button>

        <!-- The Create Modal -->
        <div id="createModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeCreateModal()">&times;</span>
                <h3>Create New Customer</h3>
                <form method="post" action="">
                    <input type="hidden" name="create" value="1">
                    <label for="name">Name:</label>
                    <input type="text" name="name" required><br>
                    <label for="due_pay">Due Payment:</label>
                    <input type="number" name="due_pay" step="0.01" required><br>
                    <label for="status_pay">Payment Status:</label>
                    <select name="status_pay" required>
                        <option value="Paid">Paid</option>
                        <option value="Pending">Pending</option>
                    </select><br>
                    <label for="created_at">Created At:</label>
                    <input type="datetime-local" name="created_at" required><br>
                    <input type="submit" value="Create">
                </form>
            </div>
        </div>

        <h3>Customer Records</h3>
        <table>
            <thead>
                <tr>
                    <th>Customer ID</th>
                    <th>Name</th>
                    <th>Due Payment</th>
                    <th>Payment Status</th>
                    <th>Created At</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($customer_data as $customer): ?>
                <tr>
                    <td><?php echo $customer['customer_id']; ?></td>
                    <td><?php echo $customer['name']; ?></td>
                    <td><?php echo $customer['due_pay']; ?></td>
                    <td><?php echo $customer['status_pay']; ?></td>
                    <td><?php echo $customer['created_at']; ?></td>
                    <td>
                        <button class="update" onclick="openUpdateModal(<?php echo $customer['customer_id']; ?>, '<?php echo $customer['name']; ?>', <?php echo $customer['due_pay']; ?>, '<?php echo $customer['status_pay']; ?>', '<?php echo $customer['created_at']; ?>')">Update</button>
                        <form method="post" action="" class="delete-form">
                            <input type="hidden" name="customer_id" value="<?php echo $customer['customer_id']; ?>">
                            <input type="hidden" name="delete" value="1">
                            <button class="delete" type="submit" onclick="return confirmDelete();">Delete</button>
                        </form>

                        <?php if ($customer['status_pay'] == 'Paid'): ?>
                            <a href="invoice.php?customer_id=<?php echo $customer['customer_id']; ?>" class="invoice-btn" target="_blank">Invoice Statement</a>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="pagination">
            <?php if ($page > 1): ?>
            <a href="customer.php?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="customer.php?page=<?php echo $i; ?>" class="<?php if ($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
            <a href="customer.php?page=<?php echo $page + 1; ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- The Update Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUpdateModal()">&times;</span>
            <h3>Update Customer</h3>
            <form method="post" action="">
                <input type="hidden" name="customer_id" id="updateCustomerId">
                <input type="hidden" name="update" value="1">
                <label for="name">Name:</label>
                <input type="text" name="name" id="updateName" required><br>
                <label for="due_pay">Due Payment:</label>
                <input type="number" name="due_pay" id="updateDuePay" step="0.01" required><br>
                <label for="status_pay">Payment Status:</label>
                <select name="status_pay" id="updateStatusPay" required>
                    <option value="Paid">Paid</option>
                    <option value="Pending">Pending</option>
                </select><br>
                <label for="created_at">Created At:</label>
                <input type="datetime-local" name="created_at" id="updateCreatedAt" required><br>
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
                window.location.href = 'customer.php';
            }
            if (<?php echo $update_success ? 'true' : 'false'; ?>) {
                alert("Successfully Updated");
                window.location.href = 'customer.php';
            }
        };

        function openCreateModal() {
            document.getElementById('createModal').style.display = 'block';
        }

        function closeCreateModal() {
            document.getElementById('createModal').style.display = 'none';
        }

        function openUpdateModal(customerId, name, duePay, statusPay, createdAt) {
            document.getElementById('updateCustomerId').value = customerId;
            document.getElementById('updateName').value = name;
            document.getElementById('updateDuePay').value = duePay;
            document.getElementById('updateStatusPay').value = statusPay;
            document.getElementById('updateCreatedAt').value = createdAt.replace(' ', 'T'); // Format for datetime-local input
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
