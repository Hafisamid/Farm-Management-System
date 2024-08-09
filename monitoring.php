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
    $time_feed = $_POST['time_feed'];
    $quantity = $_POST['quantity'];
    $food_type = $_POST['food_type'];
    $date_feed = $_POST['date_feed'];

    $stmt = $conn->prepare("INSERT INTO feed (Cow_Id, User_Id, Time_Feed, Quantity, Food_Type, Date_Feed) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iisdds", $cow_id, $user_id, $time_feed, $quantity, $food_type, $date_feed);
    $stmt->execute();
    $stmt->close();
}

// Read
$feed_data = [];
$result = $conn->query("SELECT f.Feed_Id, f.Cow_Id, u.Full_Name as User_Name, f.Time_Feed, f.Quantity, f.Food_Type, f.Date_Feed FROM feed f JOIN user u ON f.User_Id = u.User_Id");
while ($row = $result->fetch_assoc()) {
    $feed_data[] = $row;
}

// Update
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $feed_id = $_POST['feed_id'];
    $cow_id = $_POST['cow_id'];
    $time_feed = $_POST['time_feed'];
    $quantity = $_POST['quantity'];
    $food_type = $_POST['food_type'];
    $date_feed = $_POST['date_feed'];

    $stmt = $conn->prepare("UPDATE feed SET Cow_Id = ?, User_Id = ?, Time_Feed = ?, Quantity = ?, Food_Type = ?, Date_Feed = ? WHERE Feed_Id = ?");
    $stmt->bind_param("iisddsi", $cow_id, $user_id, $time_feed, $quantity, $food_type, $date_feed, $feed_id);
    if ($stmt->execute()) {
        $update_success = true;
    }
    $stmt->close();
}

// Delete
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete'])) {
    $feed_id = $_POST['feed_id'];
    $stmt = $conn->prepare("DELETE FROM feed WHERE Feed_Id = ?");
    $stmt->bind_param("i", $feed_id);
    $stmt->execute();
    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Feed Monitoring</title>
    <link rel="stylesheet" type="text/css" href="stylesmonitoring.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <div class="container">
        <h2>Feed Monitoring</h2>

        <h3>Create New Feed Record</h3>
        <form method="post" action="">
            <input type="hidden" name="create" value="1">
            <label for="cow_id">Cow ID:</label>
            <select name="cow_id" required>
                <?php foreach ($cow_data as $cow): ?>
                    <option value="<?php echo $cow['Cow_Id']; ?>"><?php echo $cow['Cow_Control_Num']; ?></option>
                <?php endforeach; ?>
            </select><br>
            <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
            <label for="time_feed">Time Feed:</label>
            <input type="time" name="time_feed" required><br>
            <label for="quantity">Quantity:</label>
            <input type="number" step="0.01" name="quantity" required><br>
            <label for="food_type">Food Type:</label>
            <input type="text" name="food_type" required><br>
            <label for="date_feed">Date Feed:</label>
            <input type="date" name="date_feed" required><br>
            <input type="submit" value="Create">
        </form>

        <h3>Feed Records</h3>
        <table>
            <thead>
                <tr>
                    <th>Feed ID</th>
                    <th>Cow ID</th>
                    <th>User Name</th>
                    <th>Time Feed</th>
                    <th>Quantity</th>
                    <th>Food Type</th>
                    <th>Date Feed</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($feed_data as $feed): ?>
                <tr>
                    <td><?php echo $feed['Feed_Id']; ?></td>
                    <td><?php echo $feed['Cow_Id']; ?></td>
                    <td><?php echo $feed['User_Name']; ?></td>
                    <td><?php echo $feed['Time_Feed']; ?></td>
                    <td><?php echo $feed['Quantity']; ?></td>
                    <td><?php echo $feed['Food_Type']; ?></td>
                    <td><?php echo $feed['Date_Feed']; ?></td>
                    <td>
                        <button onclick="openUpdateModal(<?php echo $feed['Feed_Id']; ?>, <?php echo $feed['Cow_Id']; ?>, '<?php echo $feed['Time_Feed']; ?>', <?php echo $feed['Quantity']; ?>, '<?php echo $feed['Food_Type']; ?>', '<?php echo $feed['Date_Feed']; ?>')">Update</button>
                        <form method="post" action="" style="display:inline-block;">
                            <input type="hidden" name="feed_id" value="<?php echo $feed['Feed_Id']; ?>">
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
            <h3>Update Feed Record</h3>
            <form method="post" action="">
                <input type="hidden" name="feed_id" id="updateFeedId">
                <input type="hidden" name="update" value="1">
                <label for="cow_id">Cow ID:</label>
                <select name="cow_id" id="updateCowId" required>
                    <?php foreach ($cow_data as $cow): ?>
                        <option value="<?php echo $cow['Cow_Id']; ?>"><?php echo $cow['Cow_Control_Num']; ?></option>
                    <?php endforeach; ?>
                </select><br>
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <label for="time_feed">Time Feed:</label>
                <input type="time" name="time_feed" id="updateTimeFeed" required><br>
                <label for="quantity">Quantity:</label>
                <input type="number" step="0.01" name="quantity" id="updateQuantity" required><br>
                <label for="food_type">Food Type:</label>
                <input type="text" name="food_type" id="updateFoodType" required><br>
                <label for="date_feed">Date Feed:</label>
                <input type="date" name="date_feed" id="updateDateFeed" required><br>
                <input type="submit" value="Update">
            </form>
        </div>
    </div>

    <script>
        function openUpdateModal(feedId, cowId, timeFeed, quantity, foodType, dateFeed) {
            document.getElementById('updateFeedId').value = feedId;
            document.getElementById('updateCowId').value = cowId;
            document.getElementById('updateTimeFeed').value = timeFeed;
            document.getElementById('updateQuantity').value = quantity;
            document.getElementById('updateFoodType').value = foodType;
            document.getElementById('updateDateFeed').value = dateFeed;
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
