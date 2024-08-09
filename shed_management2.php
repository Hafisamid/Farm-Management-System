<?php
session_start();

include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$update_success = false;

// Create
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO shed (Name, Description) VALUES (?, ?)");
    $stmt->bind_param("ss", $name, $description);
    $stmt->execute();
    $stmt->close();
}

// Read
// Define the number of results per page
$results_per_page = 4;

// Determine the total number of pages available
$total_results_query = $conn->query("SELECT COUNT(*) AS total FROM shed");
$total_results = $total_results_query->fetch_assoc()['total'];
$total_pages = ceil($total_results / $results_per_page);

// Determine which page number the visitor is currently on
if (!isset($_GET['page'])) {
    $page = 1;
} else {
    $page = $_GET['page'];
}

// Determine the SQL LIMIT starting number for the results on the displaying page
$starting_limit_number = ($page - 1) * $results_per_page;

// Fetch the selected results from the database
$shed_data = [];
$result = $conn->query("SELECT * FROM shed LIMIT " . $starting_limit_number . ',' . $results_per_page);
while ($row = $result->fetch_assoc()) {
    $shed_data[] = $row;
}

// Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $shed_id = $_POST['shed_id'];
    $name = $_POST['name'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE shed SET Name = ?, Description = ? WHERE Shed_Id = ?");
    $stmt->bind_param("ssi", $name, $description, $shed_id);
    if ($stmt->execute()) {
        $update_success = true;
    }
    $stmt->close();
}

// Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $shed_id = $_POST['shed_id'];
    $stmt = $conn->prepare("DELETE FROM shed WHERE Shed_Id = ?");
    $stmt->bind_param("i", $shed_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Shed Management</title>
    <link rel="stylesheet" type="text/css" href="styleshed.css">
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
        <h2>Shed Management</h2>

        <h3>Create New Shed Record</h3>
        <form method="post" action="">
            <input type="hidden" name="create" value="1">
            <label for="name">Shed Name:</label>
            <input type="text" name="name" required><br>
            <label for="description">Description:</label>
            <textarea name="description" required></textarea><br>
            <input type="submit" value="Create">
        </form>

        <h3>Shed Records</h3>
        <table>
            <thead>
                <tr>
                    <th>Shed ID</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($shed_data as $shed): ?>
                <tr>
                    <td><?php echo $shed['Shed_Id']; ?></td>
                    <td><?php echo $shed['Name']; ?></td>
                    <td><?php echo $shed['Description']; ?></td>
                    <td>
                        <button onclick="openUpdateModal(<?php echo $shed['Shed_Id']; ?>, '<?php echo $shed['Name']; ?>', '<?php echo $shed['Description']; ?>')">Update</button>
                        <form method="post" action="" style="display:inline-block;">
                            <input type="hidden" name="shed_id" value="<?php echo $shed['Shed_Id']; ?>">
                            <input type="hidden" name="delete" value="1">
                            <input type="submit" value="Delete" onclick="return confirm('Are you sure you want to delete this record?');">
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="pagination">
            <?php if ($page > 1): ?>
            <a href="shed_management.php?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="shed_management.php?page=<?php echo $i; ?>" class="<?php if ($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
            <a href="shed_management.php?page=<?php echo $page + 1; ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- The Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUpdateModal()">&times;</span>
            <h3>Update Shed Record</h3>
            <form method="post" action="">
                <input type="hidden" name="shed_id" id="updateShedId">
                <input type="hidden" name="update" value="1">
                <label for="name">Shed Name:</label>
                <input type="text" name="name" id="updateName" required><br>
                <label for="description">Description:</label>
                <textarea name="description" id="updateDescription" required></textarea><br>
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

        function openUpdateModal(shedId, name, description) {
            document.getElementById('updateShedId').value = shedId;
            document.getElementById('updateName').value = name;
            document.getElementById('updateDescription').value = description;
            document.getElementById('updateModal').style.display = "block";
        }

        function closeUpdateModal() {
            document.getElementById('updateModal').style.display = "none";
        }

        window.onclick = function(event) {
            if (event.target == document.getElementById('updateModal')) {
                closeUpdateModal();
            }
        }

        window.onload = function() {
            var menuOpen = localStorage.getItem('menuOpen') === 'true';
            if (menuOpen) {
                document.getElementById('sideMenu').classList.add('open');
                document.body.classList.add('menu-open');
            }
        }

        <?php if ($update_success): ?>
        alert("Successfully Updated");
        <?php endif; ?>
    </script>
</body>
</html>
