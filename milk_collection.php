<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch cow data for the dropdown
$cow_data = [];
$result = $conn->query("SELECT Cow_Id, Cow_Control_Num FROM cow");
while ($row = $result->fetch_assoc()) {
    $cow_data[] = $row;
}

$update_success = false;

// Create
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['create'])) {
    $cow_id = $_POST['cow_id'];
    $date_milked = $_POST['date_milked'];
    $liter_collect = $_POST['liter_collect'];
    $price_per_litre = $_POST['price_per_litre'];
    $total_price = $liter_collect * $price_per_litre;

    $stmt = $conn->prepare("INSERT INTO milk (Cow_Id, User_Id, Date_Milked, Liter_Collect, Price_per_Litre, Total_Price) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisddd", $cow_id, $user_id, $date_milked, $liter_collect, $price_per_litre, $total_price);
    $stmt->execute();
    $stmt->close();
}

// Read
$milk_data = [];
$result = $conn->query("SELECT * FROM milk");
while ($row = $result->fetch_assoc()) {
    $milk_data[] = $row;
}

// Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $milk_id = $_POST['milk_id'];
    $cow_id = $_POST['cow_id'];
    $date_milked = $_POST['date_milked'];
    $liter_collect = $_POST['liter_collect'];
    $price_per_litre = $_POST['price_per_litre'];
    $total_price = $liter_collect * $price_per_litre;

    $stmt = $conn->prepare("UPDATE milk SET Cow_Id = ?, User_Id = ?, Date_Milked = ?, Liter_Collect = ?, Price_per_Litre = ?, Total_Price = ? WHERE Milk_Id = ?");
    $stmt->bind_param("iisdddi", $cow_id, $user_id, $date_milked, $liter_collect, $price_per_litre, $total_price, $milk_id);
    if ($stmt->execute()) {
        $update_success = true;
    }
    $stmt->close();
}

// Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $milk_id = $_POST['milk_id'];
    $stmt = $conn->prepare("DELETE FROM milk WHERE Milk_Id = ?");
    $stmt->bind_param("i", $milk_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Milk Collection</title>
    <link rel="stylesheet" type="text/css" href="stylesmilk.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgb(0,0,0);
            background-color: rgba(0,0,0,0.4);
            padding-top: 60px;
        }
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
        }
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <br><br>

    <div class="container">
        <h2>Milk Collection</h2>

        <h3>Create New Milk Record</h3>
        <form method="post" action="">
            <input type="hidden" name="create" value="1">
            <label for="cow_id">Cow ID:</label>
            <select name="cow_id" required>
                <?php foreach ($cow_data as $cow): ?>
                    <option value="<?php echo $cow['Cow_Id']; ?>"><?php echo $cow['Cow_Id']; ?></option>
                <?php endforeach; ?>
            </select><br>
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <label for="date_milked">Date Milked:</label>
            <input type="date" name="date_milked" required><br>
            <label for="liter_collect">Liters Collected:</label>
            <input type="number" step="0.01" name="liter_collect" required><br>
            <label for="price_per_litre">Price per Litre:</label>
            <input type="number" step="0.01" name="price_per_litre" required><br>
            <input type="submit" value="Create">
        </form>

        <h3>Milk Collection Records</h3>
        <table>
            <thead>
                <tr>
                    <th>Milk ID</th>
                    <th>Cow ID</th>
                    <th>User ID</th>
                    <th>Date Milked</th>
                    <th>Liters Collected</th>
                    <th>Price per Litre</th>
                    <th>Total Price</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($milk_data as $milk): ?>
                <tr>
                    <td><?php echo $milk['Milk_Id']; ?></td>
                    <td><?php echo $milk['Cow_Id']; ?></td>
                    <td><?php echo $milk['User_Id']; ?></td>
                    <td><?php echo $milk['Date_Milked']; ?></td>
                    <td><?php echo $milk['Liter_Collect']; ?></td>
                    <td><?php echo $milk['Price_per_Litre']; ?></td>
                    <td><?php echo $milk['Total_Price']; ?></td>
                    <td>
                        <button onclick="openUpdateModal(<?php echo $milk['Milk_Id']; ?>, <?php echo $milk['Cow_Id']; ?>, '<?php echo $milk['Date_Milked']; ?>', <?php echo $milk['Liter_Collect']; ?>, <?php echo $milk['Price_per_Litre']; ?>)">Update</button>
                        <form method="post" action="" style="display:inline-block;">
                            <input type="hidden" name="milk_id" value="<?php echo $milk['Milk_Id']; ?>">
                            <input type="hidden" name="delete" value="1">
                            <input type="submit" value="Delete" onclick="return confirm('Are you sure you want to delete this record?');">
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- The Modal -->
    <div id="updateModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeUpdateModal()">&times;</span>
            <h3>Update Milk Record</h3>
            <form method="post" action="">
                <input type="hidden" name="milk_id" id="updateMilkId">
                <input type="hidden" name="update" value="1">
                <label for="cow_id">Cow ID:</label>
                <select name="cow_id" id="updateCowId" required>
                    <?php foreach ($cow_data as $cow): ?>
                        <option value="<?php echo $cow['Cow_Id']; ?>"><?php echo $cow['Cow_Control_Num']; ?></option>
                    <?php endforeach; ?>
                </select><br>
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <label for="date_milked">Date Milked:</label>
                <input type="date" name="date_milked" id="updateDateMilked" required><br>
                <label for="liter_collect">Liters Collected:</label>
                <input type="number" step="0.01" name="liter_collect" id="updateLiterCollect" required><br>
                <label for="price_per_litre">Price per Litre:</label>
                <input type="number" step="0.01" name="price_per_litre" id="updatePricePerLitre" required><br>
                <input type="submit" value="Update">
            </form>
        </div>
    </div>

    <script>
        function openUpdateModal(milkId, cowId, dateMilked, literCollect, pricePerLitre) {
            document.getElementById('updateMilkId').value = milkId;
            document.getElementById('updateCowId').value = cowId;
            document.getElementById('updateDateMilked').value = dateMilked;
            document.getElementById('updateLiterCollect').value = literCollect;
            document.getElementById('updatePricePerLitre').value = pricePerLitre;
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

        <?php if ($update_success): ?>
        alert("Successfully Updated");
        <?php endif; ?>
    </script>
</body>
</html>
