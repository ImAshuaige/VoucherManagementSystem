<?php
// Retrieve form data
$customerId = $_POST['customer_id'] ?? '';
$customerName = $_POST['customer_name'] ?? '';
$voucherType = $_POST['voucher_type'] ?? '';
$amount = $_POST['amount'] ?? '';
$issuedDate = $_POST['issued_date'] ?? '';
$expirationDate = $_POST['expiration_date'] ?? '';

// Validate form data
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (empty($customerId) || empty($customerName) || empty($voucherType) || empty($amount) || empty($issuedDate) || empty($expirationDate)) {
        $errors[] = "All fields are mandatory. Please fill in all the required fields.";
    }

    if (!is_numeric($amount) || $amount <= 0) {
        $errors[] = "Amount must be a numeric value greater than 0.";
    }

    $validVoucherTypes = ['GST', 'Rebate', 'CDAC'];
    if (!in_array($voucherType, $validVoucherTypes)) {
        $errors[] = "Invalid voucher type. Allowed voucher types are: GST, Rebate, CDAC.";
    }

    if (!strtotime($issuedDate) || !strtotime($expirationDate)) {
        $errors[] = "Invalid date format for issued date or expiration date.";
    }

    // Validate expiration date against issued date
    if (strtotime($expirationDate) < strtotime($issuedDate)) {
        $errors[] = "Expiration date cannot be earlier than the issued date.";
    }


    if (empty($errors)) {
        // Database connection details
        $servername = "localhost";
        $username = "root";
        $password = "";
        $dbname = "voucher";

        // Create a new PDO instance
        $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

        // Set PDO error mode to exception
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        try {
            // Prepare SQL statement to insert voucher record
            $stmt = $conn->prepare("INSERT INTO vouchers (customer_id, customer_name, voucher_type, amount, issued_date, expiration_date) VALUES (:customerId, :customerName, :voucherType, :amount, :issuedDate, :expirationDate)");

            // Bind parameters to the SQL statement
            $stmt->bindParam(':customerId', $customerId);
            $stmt->bindParam(':customerName', $customerName);
            $stmt->bindParam(':voucherType', $voucherType);
            $stmt->bindParam(':amount', $amount);
            $stmt->bindParam(':issuedDate', $issuedDate);
            $stmt->bindParam(':expirationDate', $expirationDate);

            // Execute the SQL statement
            $stmt->execute();

            // Clear form input values
            $customerId = '';
            $customerName = '';
            $voucherType = '';
            $amount = '';
            $issuedDate = '';
            $expirationDate = '';

            // Use JavaScript alert instead of echoing the success message
            echo '<script>alert("Voucher record inserted successfully.");</script>';
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }

        // Close the database connection
        $conn = null;
    }
}
?>


<!DOCTYPE html>
<html>

<head>
    <title>Add Voucher</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            font-family: Arial, sans-serif;
        }

        .container {
            width: 400px;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: #f7f7f7;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
        }

        label {
            margin-bottom: 5px;
        }

        input[type="text"],
        select,
        input[type="date"],
        input[type="submit"] {
            padding: 8px;
            border-radius: 3px;
            border: 1px solid #ccc;
            margin-bottom: 10px;
        }

        input[type="submit"] {
            background-color: #4caf50;
            color: white;
            cursor: pointer;
        }

        .error {
            color: red;
            margin-bottom: 10px;
            padding: 10px;
            border-radius: 5px;
            background-color: #ffe6e6;
        }

        .error p {
            margin: 0;
            padding: 5px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Add Voucher</h1>

        <form method="post" action="process_voucher.php">
            <label for="customer_id">Customer ID:</label>
            <input type="text" id="customer_id" name="customer_id" value="<?php echo $customerId; ?>">

            <label for="customer_name">Customer Name:</label>
            <input type="text" id="customer_name" name="customer_name" value="<?php echo $customerName; ?>">

            <label for="voucher_type">Voucher Type:</label>
            <select id="voucher_type" name="voucher_type">
                <option value="GST" <?php if ($voucherType === 'GST') echo 'selected'; ?>>GST</option>
                <option value="Rebate" <?php if ($voucherType === 'Rebate') echo 'selected'; ?>>Rebate</option>
                <option value="CDAC" <?php if ($voucherType === 'CDAC') echo 'selected'; ?>>CDAC</option>
            </select>

            <label for="amount">Amount:</label>
            <input type="text" id="amount" name="amount" value="<?php echo $amount; ?>">

            <label for="issued_date">Issued Date:</label>
            <input type="date" id="issued_date" name="issued_date" value="<?php echo $issuedDate; ?>">

            <label for="expiration_date">Expiration Date:</label>
            <input type="date" id="expiration_date" name="expiration_date" value="<?php echo $expirationDate; ?>">

            <input type="submit" value="Submit">
        </form>

        <?php if (!empty($errors)) : ?>
            <div class="error">
                <?php foreach ($errors as $error) : ?>
                    <p><?php echo $error; ?></p>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <script>
        // Reload the page with cleared fields after successful submission
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>


