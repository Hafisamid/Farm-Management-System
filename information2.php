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
$user_id = $_SESSION['user_id']; // Get the user ID from the session
$username = $_SESSION['username']; // Get the user name from the session

// Fetch shed data for the dropdown
$shed_data = [];
$result = $conn->query("SELECT Shed_Id, Name FROM shed");
while ($row = $result->fetch_assoc()) {
    $shed_data[] = $row;
}

// Fetch category data for the dropdown
$category_data = [];
$result = $conn->query("SELECT Category_Id, Category_Name FROM cow_category");
while ($row = $result->fetch_assoc()) {
    $category_data[] = $row;
}

// Initialize cow_data
$cow_data = [];

// Ensure the uploads directory exists
$uploadDir = 'uploads';
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Create
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $gender = $_POST['gender'];
    $type = $_POST['type'];
    $shed_id = $_POST['shed_id'];
    $category_id = $_POST['category_id'];
    $quantity = $_POST['quantity'];

    // Handle file upload
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($_FILES["cow_image"]["name"]);
    move_uploaded_file($_FILES["cow_image"]["tmp_name"], $target_file);

    $stmt = $conn->prepare("INSERT INTO cow (User_Id, Gender, Cow_Image, Type, Shed_Id, Category_Id, Quantity, Last_Updated_By) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssiisi", $user_id, $gender, $target_file, $type, $shed_id, $category_id, $quantity, $user_id);
    $stmt->execute();
    $stmt->close();
}

// Pagination logic
$results_per_page = 5;
$total_results_query = $conn->query("SELECT COUNT(*) AS total FROM cow");
$total_results = $total_results_query->fetch_assoc()['total'];
$total_pages = ceil($total_results / $results_per_page);

if (!isset($_GET['page'])) {
    $page = 1;
} else {
    $page = $_GET['page'];
}

$starting_limit_number = ($page - 1) * $results_per_page;

// Read
$result = $conn->query("SELECT c.*, cc.Category_Name, u.Username, c.Last_Update_Timestamp 
                        FROM cow c 
                        LEFT JOIN cow_category cc ON c.Category_Id = cc.Category_Id 
                        LEFT JOIN user u ON c.Last_Updated_By = u.User_Id 
                        LIMIT " . $starting_limit_number . ',' . $results_per_page);
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $cow_data[] = $row;
    }
}

// Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $cow_id = $_POST['cow_id'];
    $gender = $_POST['gender'];
    $type = $_POST['type'];
    $shed_id = $_POST['shed_id'];
    $category_id = $_POST['category_id'];
    $quantity = $_POST['quantity'];

    // Handle file upload
    if (!empty($_FILES["cow_image"]["name"])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["cow_image"]["name"]);
        move_uploaded_file($_FILES["cow_image"]["tmp_name"], $target_file);
    } else {
        $target_file = $_POST['existing_cow_image'];
    }

    $stmt = $conn->prepare("UPDATE cow SET User_Id = ?, Gender = ?, Cow_Image = ?, Type = ?, Shed_Id = ?, Category_Id = ?, Quantity = ?, Last_Updated_By = ? WHERE Cow_Id = ?");
    $stmt->bind_param("isssiisii", $user_id, $gender, $target_file, $type, $shed_id, $category_id, $quantity, $user_id, $cow_id);
    if ($stmt->execute()) {
        $update_success = true;
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
    $stmt->close();
}

// Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $cow_id = $_POST['cow_id'];
    $stmt = $conn->prepare("DELETE FROM cow WHERE Cow_Id = ?");
    $stmt->bind_param("i", $cow_id);
    if ($stmt->execute()) {
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    }
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Information Management</title>
    <link rel="stylesheet" type="text/css" href="information.css">
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
        <h2>Information Management</h2>

        <!-- Add Livestock Button -->
        <button onclick="openCreateModal()"> Add Livestock</button>

        <!-- The Create Modal -->
        <div id="createModal" class="modal">
            <div class="modal-content">
                <span class="close" onclick="closeCreateModal()">&times;</span>
                <h3>Create New Cow Record</h3>
                <form method="post" action="" enctype="multipart/form-data">
                    <input type="hidden" name="create" value="1">
                    <label for="gender">Gender:</label>
                    <select name="gender" required>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                    </select><br>
                    <label for="cow_image">Cow Image:</label>
                    <input type="file" name="cow_image" required><br>
                    <label for="type">Type:</label>
                    <input type="text" name="type" required><br>
                    <label for="shed_id">Shed:</label>
                    <select name="shed_id" required>
                        <?php foreach ($shed_data as $shed): ?>
                            <option value="<?php echo $shed['Shed_Id']; ?>"><?php echo $shed['Name']; ?></option>
                        <?php endforeach; ?>
                    </select><br>
                    <label for="category_id">Category:</label>
                    <select name="category_id" required>
                        <?php foreach ($category_data as $category): ?>
                            <option value="<?php echo $category['Category_Id']; ?>"><?php echo $category['Category_Name']; ?></option>
                        <?php endforeach; ?>
                    </select><br>
                    <label for="quantity">Quantity:</label>
                    <input type="number" name="quantity" required><br>
                    <input type="submit" value="Create">
                </form>
            </div>
        </div>

        <h3>Cow Records</h3>
        <table>
            <thead>
                <tr>
                    <th>Cow ID</th>
                    <th>Last Updated</th>
                    <th>Updated By</th>
                    <th>Gender</th>
                    <th>Cow Image</th>
                    <th>Type</th>
                    <th>Shed</th>
                    <th>Category</th>
                    <th>Quantity</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($cow_data)): ?>
                    <?php foreach ($cow_data as $cow): ?>
                    <tr>
                        <td><?php echo $cow['Cow_Id']; ?></td>
                        <td><?php echo date('Y-m-d H:i:s', strtotime($cow['Last_Update_Timestamp'])); ?></td>
                        <td><?php echo $cow['Username']; ?></td>
                        <td><?php echo $cow['Gender']; ?></td>
                        <td><img src="<?php echo $cow['Cow_Image']; ?>" alt="Cow Image" style="width: 50px; height: 50px;"></td>
                        <td><?php echo $cow['Type']; ?></td>
                        <td><?php echo $cow['Shed_Id']; ?></td>
                        <td><?php echo $cow['Category_Name']; ?></td>
                        <td><?php echo $cow['Quantity']; ?></td>
                        <td class="actions">
                            <button class="update" onclick="openUpdateModal(<?php echo $cow['Cow_Id']; ?>, '<?php echo $cow['Gender']; ?>', '<?php echo $cow['Cow_Image']; ?>', '<?php echo $cow['Type']; ?>', <?php echo $cow['Shed_Id']; ?>, <?php echo $cow['Category_Id']; ?>, <?php echo $cow['Quantity']; ?>)">Update</button>
                            <form method="post" action="" style="display:inline-block;">
                                <input type="hidden" name="cow_id" value="<?php echo $cow['Cow_Id']; ?>">
                                <input type="hidden" name="delete" value="1">
                                <button class="delete" type="submit" onclick="return confirm('Are you sure you want to delete this record?');">Delete</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10">No records found.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Pagination Links -->
        <div class="pagination">
            <?php if ($page > 1): ?>
            <a href="information.php?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
            <?php endif; ?>

            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <a href="information.php?page=<?php echo $i; ?>" class="<?php if ($i == $page) echo 'active'; ?>"><?php echo $i; ?></a>
            <?php endfor; ?>

            <?php if ($page < $total_pages): ?>
            <a href="information.php?page=<?php echo $page + 1; ?>">Next &raquo;</a>
            <?php endif; ?>
        </div>
    </div>

    <!-- The Update Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUpdateModal()">&times;</span>
            <h3>Update Cow Record</h3>
            <form method="post" action="" enctype="multipart/form-data">
                <input type="hidden" name="cow_id" id="updateCowId">
                <input type="hidden" name="update" value="1">
                <label for="gender">Gender:</label>
                <select name="gender" id="updateGender" required>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select><br>
                <label for="cow_image">Cow Image:</label>
                <input type="file" name="cow_image"><br>
                <label for="cow_image">Current Cow Image:</label>
                <img id="updateCowImagePreview" src="" alt="Current Cow Image" style="width: 100px; height: 100px;"><br>
                <input type="hidden" name="existing_cow_image" id="existingCowImage"><br>
                <label for="type">Type:</label>
                <input type="text" name="type" id="updateType" required><br>
                <label for="shed_id">Shed:</label>
                <select name="shed_id" id="updateShedId" required>
                    <?php foreach ($shed_data as $shed): ?>
                        <option value="<?php echo $shed['Shed_Id']; ?>"><?php echo $shed['Name']; ?></option>
                    <?php endforeach; ?>
                </select><br>
                <label for="category_id">Category:</label>
                <select name="category_id" id="updateCategoryId" required>
                    <?php foreach ($category_data as $category): ?>
                        <option value="<?php echo $category['Category_Id']; ?>"><?php echo $category['Category_Name']; ?></option>
                    <?php endforeach; ?>
                </select><br>
                <label for="quantity">Quantity:</label>
                <input type="number" name="quantity" id="updateQuantity" required><br>
                <input type="submit" value="Update">
            </form>
        </div>
    </div>

    <script>
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

    function openCreateModal() {
        document.getElementById('createModal').style.display = 'block';
    }

    function closeCreateModal() {
        document.getElementById('createModal').style.display = 'none';
    }

    function openUpdateModal(cowId, gender, cowImage, type, shedId, categoryId, quantity) {
        console.log("Opening update modal with data:", cowId, gender, cowImage, type, shedId, categoryId, quantity);
        document.getElementById('updateCowId').value = cowId;
        document.getElementById('updateGender').value = gender;
        var imageElement = document.getElementById('updateCowImagePreview');
        if (imageElement) {
            imageElement.src = cowImage; // Set the image preview
        }
        document.getElementById('existingCowImage').value = cowImage;
        document.getElementById('updateType').value = type;
        document.getElementById('updateShedId').value = shedId;
        document.getElementById('updateCategoryId').value = categoryId;
        document.getElementById('updateQuantity').value = quantity;
        document.getElementById('updateModal').style.display = "block";
    }

    function closeUpdateModal() {
        document.getElementById('updateModal').style.display = "none";
    }

    window.onclick = function(event) {
        if (event.target == document.getElementById('createModal')) {
            closeCreateModal();
        }
        if (event.target == document.getElementById('updateModal')) {
            closeUpdateModal();
        }
    }

    <?php if ($update_success): ?>
    alert("Successfully Updated");
    <?php endif; ?>
</script>

</body>
</html>
