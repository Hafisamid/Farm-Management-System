<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

include 'db.php';

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['customer_id'])) {
    die("Customer ID is required.");
}

$customer_id = $_GET['customer_id'];

// Fetch customer details
$customer_query = $conn->prepare("SELECT name, due_pay, status_pay, created_at FROM customer WHERE customer_id = ?");
$customer_query->bind_param("i", $customer_id);
$customer_query->execute();
$customer_query->bind_result($customer_name, $due_pay, $status_pay, $created_at);
$customer_query->fetch();
$customer_query->close();

// Fetch sales records
$sales_records = [];
$sales_query = $conn->prepare("SELECT sale_type, quantity, price FROM sales_record WHERE customer_id = ?");
$sales_query->bind_param("i", $customer_id);
$sales_query->execute();
$sales_query->bind_result($sale_type, $quantity, $price);

while ($sales_query->fetch()) {
    $sales_records[] = [
        'sale_type' => $sale_type,
        'quantity' => $quantity,
        'price' => $price
    ];
}
$sales_query->close();

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Invoice</title>
    <link rel="stylesheet" type="text/css" href="styleinvoice.css">
    <style>
        @media print {
            .no-print {
                display: none;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Invoice</h1>
        <button class="no-print" onclick="window.print()">Print Invoice</button>
        <p><strong>Customer Name:</strong> <?php echo htmlspecialchars($customer_name); ?></p>
        <p><strong>Due Payment:</strong> RM <?php echo htmlspecialchars($due_pay); ?></p>
        <p><strong>Payment Status:</strong> <?php echo htmlspecialchars($status_pay); ?></p>
        <p><strong>Date Pay:</strong> <?php echo htmlspecialchars($created_at); ?></p>
        <table>
            <thead>
                <tr>
                    <th>Sale Type</th>
                    <th>Quantity</th>
                    <th>Price</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($sales_records as $record): ?>
                <tr>
                    <td><?php echo htmlspecialchars($record['sale_type']); ?></td>
                    <td><?php echo htmlspecialchars($record['quantity']); ?></td>
                    <td>RM<?php echo htmlspecialchars($record['price']); ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>
</html>
