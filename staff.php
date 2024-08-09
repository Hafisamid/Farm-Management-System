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

// Pagination logic
$results_per_page = 4;
$total_results_query = $conn->query("SELECT COUNT(*) AS total FROM user");
$total_results = $total_results_query->fetch_assoc()['total'];
$total_pages = ceil($total_results / $results_per_page);

if (!isset($_GET['page'])) {
    $page = 1;
} else {
    $page = $_GET['page'];
}

$starting_limit_number = ($page - 1) * $results_per_page;

// Create
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $user_type = $_POST['user_type'];

    $stmt = $conn->prepare("INSERT INTO user (Username, Password, Full_Name, Email, Contact, User_Type) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssss", $username, $password, $full_name, $email, $contact, $user_type);
    $stmt->execute();
    $stmt->close();
}

// Read
$staff_data = [];
$result = $conn->query("SELECT * FROM user LIMIT " . $starting_limit_number . ',' . $results_per_page);
while ($row = $result->fetch_assoc()) {
    $staff_data[] = $row;
}

// Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $user_id = $_POST['user_id'];
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $contact = $_POST['contact'];
    $user_type = $_POST['user_type'];

    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
        $stmt = $conn->prepare("UPDATE user SET Username = ?, Password = ?, Full_Name = ?, Email = ?, Contact = ?, User_Type = ? WHERE User_Id = ?");
        $stmt->bind_param("ssssssi", $username, $password, $full_name, $email, $contact, $user_type, $user_id);
    } else {
        $stmt = $conn->prepare("UPDATE user SET Username = ?, Full_Name = ?, Email = ?, Contact = ?, User_Type = ? WHERE User_Id = ?");
        $stmt->bind_param("sssssi", $username, $full_name, $email, $contact, $user_type, $user_id);
    }

    if ($stmt->execute()) {
        $update_success = true;
    }
    $stmt->close();
}

// Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $user_id = $_POST['user_id'];
    $stmt = $conn->prepare("DELETE FROM user WHERE User_Id = ?");
    $stmt->bind_param("i", $user_id);
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
    <title>Staff Management</title>
    <link rel="stylesheet" type="text/css" href="stylestaff.css">
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
        <h2>Staff Management</h2>

        <!-- Add Staff Button -->
        <button onclick="openCreateModal()">Add Staff</button>

        <!-- The Create Modal -->
        <div id="createModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeCreateModal()">&times;</span>
                <h3>Create New Staff</h3>
                <form method="post" action="">
                    <input type="hidden" name="create" value="1">
                    <label for="username">Username:</label>
                    <input type="text" name="username" required><br>
                    <label for="password">Password:</label>
                    <input type="password" name="password" required><br>
                    <label for="full_name">Full Name:</label>
                    <input type="text" name="full_name" required><br>
                    <label for="email">Email:</label>
                    <input type="email" name="email" required><br>
                    <label for="contact">Contact:</label>
                    <input type="text" name="contact" required><br>
                    <label for="user_type">User Type:</label>
                    <select name="user_type" required>
                        <option value="admin">Admin</option>
                        <option value="farmer">Farmer</option>
                        <option value="health manager">Health Manager</option>
                        <option value="worker">Worker</option>
                    </select><br>
                    <input type="submit" value="Create">
                </form>
            </div>
        </div>

        <h3>Staff Records</h3>
        <table>
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Full Name</th>
                    <th>Email</th>
                    <th>Contact</th>
                    <th>User Type</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($staff_data as $staff): ?>
                <tr>
                    <td><?php echo htmlspecialchars($staff['Username']); ?></td>
                    <td><?php echo htmlspecialchars($staff['Full_Name']); ?></td>
                    <td><?php echo htmlspecialchars($staff['Email']); ?></td>
                    <td><?php echo htmlspecialchars($staff['Contact']); ?></td>
                    <td><?php echo htmlspecialchars($staff['User_Type']); ?></td>
                    <td>
                        <button class="update" onclick="openUpdateModal(<?php echo $staff['User_Id']; ?>, '<?php echo htmlspecialchars($staff['Username']); ?>', '<?php echo htmlspecialchars($staff['Full_Name']); ?>', '<?php echo htmlspecialchars($staff['Email']); ?>', '<?php echo htmlspecialchars($staff['Contact']); ?>', '<?php echo htmlspecialchars($staff['User_Type']); ?>')">Update</button>
                        <form method="post" action="" class="delete-form">
                            <input type="hidden" name="user_id" value="<?php echo $staff['User_Id']; ?>">
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
            <?php if ($page > 1): ?>
            <a href="staff.php?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="staff.php?page=<?php echo $i; ?>" class="<?php if ($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
            <a href="staff.php?page=<?php echo $page + 1; ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- The Update Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUpdateModal()">&times;</span>
            <h3>Update Staff</h3>
            <form method="post" action="">
                <input type="hidden" name="user_id" id="updateUserId">
                <input type="hidden" name="update" value="1">
                <label for="username">Username:</label>
                <input type="text" name="username" id="updateUsername" required><br>
                <label for="password">Password:</label>
                <input type="password" name="password" id="updatePassword"><br>
                <label for="full_name">Full Name:</label>
                <input type="text" name="full_name" id="updateFullName" required><br>
                <label for="email">Email:</label>
                <input type="email" name="email" id="updateEmail" required><br>
                <label for="contact">Contact:</label>
                <input type="text" name="contact" id="updateContact" required><br>
                <label for="user_type">User Type:</label>
                <select name="user_type" id="updateUserType" required>
                    <option value="admin">Admin</option>
                    <option value="farmer">Farmer</option>
                    <option value="health manager">Health Manager</option>
                    <option value="worker">Worker</option>
                </select><br>
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
                window.location.href = 'staff.php';
            }
            if (<?php echo $update_success ? 'true' : 'false'; ?>) {
                alert("Successfully Updated");
                window.location.href = 'staff.php';
            }
        };

        function openCreateModal() {
            document.getElementById('createModal').style.display = 'block';
        }

        function closeCreateModal() {
            document.getElementById('createModal').style.display = 'none';
        }

        function openUpdateModal(userId, username, fullName, email, contact, userType) {
            document.getElementById('updateUserId').value = userId;
            document.getElementById('updateUsername').value = username;
            document.getElementById('updateFullName').value = fullName;
            document.getElementById('updateEmail').value = email;
            document.getElementById('updateContact').value = contact;
            document.getElementById('updateUserType').value = userType;
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
