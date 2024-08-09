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
$delete_success = false; // Flag for delete success
$user_id = $_SESSION['user_id']; // Get the user ID from the session

// Fetch shed data for the dropdown
$shed_data = [];
$result = $conn->query("SELECT Shed_Id, Name FROM shed");
while ($row = $result->fetch_assoc()) {
    $shed_data[] = $row;
}

// Pagination logic
$results_per_page = 4;
$total_results_query = $conn->query("SELECT COUNT(*) AS total FROM vaccine");
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
    $shed_id = $_POST['shed_id'];
    $date_vaccine = $_POST['date_vaccine'];
    $dose = $_POST['dose'];
    $disease = $_POST['disease'];
    $duration = $_POST['duration'];

    $stmt = $conn->prepare("INSERT INTO vaccine (Shed_Id, User_Id, Date_Vaccine, Dose, Disease, Duration) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisiss", $shed_id, $user_id, $date_vaccine, $dose, $disease, $duration);
    $stmt->execute();
    $stmt->close();
}

// Read
$vaccine_data = [];
$result = $conn->query("SELECT v.*, s.Name as Shed_Name, u.username FROM vaccine v 
                        LEFT JOIN shed s ON v.Shed_Id = s.Shed_Id 
                        LEFT JOIN user u ON v.User_Id = u.User_Id 
                        LIMIT " . $starting_limit_number . ',' . $results_per_page);
while ($row = $result->fetch_assoc()) {
    $vaccine_data[] = $row;
}

// Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $vaccine_id = $_POST['vaccine_id'];
    $shed_id = $_POST['shed_id'];
    $date_vaccine = $_POST['date_vaccine'];
    $dose = $_POST['dose'];
    $disease = $_POST['disease'];
    $duration = $_POST['duration'];

    $stmt = $conn->prepare("UPDATE vaccine SET Shed_Id = ?, User_Id = ?, Date_Vaccine = ?, Dose = ?, Disease = ?, Duration = ? WHERE Vaccine_Id = ?");
    $stmt->bind_param("iisissi", $shed_id, $user_id, $date_vaccine, $dose, $disease, $duration, $vaccine_id);
    if ($stmt->execute()) {
        $update_success = true;
    }
    $stmt->close();
}

// Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $vaccine_id = $_POST['vaccine_id'];
    $stmt = $conn->prepare("DELETE FROM vaccine WHERE Vaccine_Id = ?");
    $stmt->bind_param("i", $vaccine_id);
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
    <title>Vaccine Management</title>
    <link rel="stylesheet" type="text/css" href="stylevaccine.css">
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
        <h2>Vaccine Management</h2>

        <!-- Add Vaccine Button -->
        <button onclick="openCreateModal()">Add Vaccine</button>

        <!-- The Create Modal -->
        <div id="createModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeCreateModal()">&times;</span>
                <h3>Create New Vaccine Record</h3>
                <form method="post" action="">
                    <input type="hidden" name="create" value="1">
                    <label for="shed_id">Shed:</label>
                    <select name="shed_id" required>
                        <?php foreach ($shed_data as $shed): ?>
                            <option value="<?php echo $shed['Shed_Id']; ?>"><?php echo $shed['Name']; ?></option>
                        <?php endforeach; ?>
                    </select><br>
                    <label for="date_vaccine">Date of Vaccine:</label>
                    <input type="date" name="date_vaccine" required><br>
                    <label for="dose">Dose:</label>
                    <input type="number" name="dose" required><br>
                    <label for="disease">Disease:</label>
                    <input type="text" name="disease" required><br>
                    <label for="duration">Duration:</label>
                    <input type="text" name="duration" required><br>
                    <input type="submit" value="Create">
                </form>
            </div>
        </div>

        <h3>Vaccine Records</h3>
        <table>
            <thead>
                <tr>
                    <th>Vaccine ID</th>
                    <th>Shed Name</th>
                    <th>Username</th>
                    <th>Date of Vaccine</th>
                    <th>Dose</th>
                    <th>Disease</th>
                    <th>Duration</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vaccine_data as $vaccine): ?>
                <tr>
                    <td><?php echo $vaccine['Vaccine_Id']; ?></td>
                    <td><?php echo $vaccine['Shed_Name']; ?></td>
                    <td><?php echo $vaccine['username']; ?></td>
                    <td><?php echo $vaccine['Date_Vaccine']; ?></td>
                    <td><?php echo $vaccine['Dose']; ?></td>
                    <td><?php echo $vaccine['Disease']; ?></td>
                    <td><?php echo $vaccine['Duration']; ?></td>
                    <td>
                        <button class="update" onclick="openUpdateModal(<?php echo $vaccine['Vaccine_Id']; ?>, <?php echo $vaccine['Shed_Id']; ?>, '<?php echo $vaccine['Date_Vaccine']; ?>', <?php echo $vaccine['Dose']; ?>, '<?php echo $vaccine['Disease']; ?>', '<?php echo $vaccine['Duration']; ?>')">Update</button>
                        <form method="post" action="" class="delete-form">
                            <input type="hidden" name="vaccine_id" value="<?php echo $vaccine['Vaccine_Id']; ?>">
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
            <a href="vaccine2.php?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="vaccine2.php?page=<?php echo $i; ?>" class="<?php if ($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
            <a href="vaccine2.php?page=<?php echo $page + 1; ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- The Update Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUpdateModal()">&times;</span>
            <h3>Update Vaccine Record</h3>
            <form method="post" action="">
                <input type="hidden" name="vaccine_id" id="updateVaccineId">
                <input type="hidden" name="update" value="1">
                <label for="shed_id">Shed:</label>
                <select name="shed_id" id="updateShedId" required>
                    <?php foreach ($shed_data as $shed): ?>
                        <option value="<?php echo $shed['Shed_Id']; ?>"><?php echo $shed['Name']; ?></option>
                    <?php endforeach; ?>
                </select><br>
                <label for="date_vaccine">Date of Vaccine:</label>
                <input type="date" name="date_vaccine" id="updateDateVaccine" required><br>
                <label for="dose">Dose:</label>
                <input type="number" name="dose" id="updateDose" required><br>
                <label for="disease">Disease:</label>
                <input type="text" name="disease" id="updateDisease" required><br>
                <label for="duration">Duration:</label>
                <input type="text" name="duration" id="updateDuration" required><br>
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
            window.location.href = 'vaccine2.php';
        }
        if (<?php echo $update_success ? 'true' : 'false'; ?>) {
            alert("Successfully Updated");
            window.location.href = 'vaccine2.php';
        }
    };

    function openCreateModal() {
        document.getElementById('createModal').style.display = 'block';
    }

    function closeCreateModal() {
        document.getElementById('createModal').style.display = 'none';
    }

    function openUpdateModal(vaccineId, shedId, dateVaccine, dose, disease, duration) {
        document.getElementById('updateVaccineId').value = vaccineId;
        document.getElementById('updateShedId').value = shedId;
        document.getElementById('updateDateVaccine').value = dateVaccine;
        document.getElementById('updateDose').value = dose;
        document.getElementById('updateDisease').value = disease;
        document.getElementById('updateDuration').value = duration;
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
