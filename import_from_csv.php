<?php
// Function to validate voucher record
function validateVoucherRecord($record)
{
    $customerId = $record[0] ?? '';
    $customerName = $record[1] ?? '';
    $voucherType = $record[2] ?? '';
    $amount = $record[3] ?? '';
    $issuedDate = $record[4] ?? '';
    $expirationDate = $record[5] ?? '';

    if (empty($customerId) || empty($customerName) || empty($voucherType) || empty($amount) || empty($issuedDate) || empty($expirationDate)) {
        echo "Error: Required Fields Cannot Be Empty.";
        return false;
    }

    if (!is_numeric($amount) || $amount <= 0) {
        echo "Error: Amount must be numeric.";
        return false;
    }

    $validVoucherTypes = ['GST', 'Rebate', 'CDAC'];
    if (!in_array($voucherType, $validVoucherTypes)) {
        echo "Error: Invalid Voucher Type.";
        return false;
    }

    if (!strtotime($issuedDate) || !strtotime($expirationDate)) {
        echo "Error: Invalid Date Format.";
        return false;
    }

    if (strtotime($expirationDate) < strtotime($issuedDate)) {
        echo "Error: ExpirationDate Cannot Be Earlier Than IssuedDate.";
        return false;
    }

    return true;
}

// Initialize counters for successful and error records
$successCount = 0;
$errorCount = 0;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['csv_file'])) {
        $file = $_FILES['csv_file'];

        if ($file['type'] === 'text/csv' || $file['type'] === 'application/vnd.ms-excel') {
            $handle = fopen($file['tmp_name'], 'r');

            if ($handle !== false) {
                // Database connection details
                $servername = "localhost";
                $username = "root";
                $password = "";
                $dbname = "voucher";

                // Create a new PDO instance
                $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);

                // Set PDO error mode to exception
                $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

                // Get the total number of lines in the CSV file
                $totalLines = count(file($file['tmp_name']));

                // Skip the first line
                fgetcsv($handle);

                // Loop through each line in the CSV file
                while (($data = fgetcsv($handle)) !== false) {
                    // Perform validation on each record
                    if (validateVoucherRecord($data)) {
                        // Insert the valid voucher record into the database
                        $stmt = $conn->prepare("INSERT INTO vouchers (customer_id, customer_name, voucher_type, amount, issued_date, expiration_date) VALUES (?, ?, ?, ?, ?, ?)");
                        $stmt->execute($data);

                        // Increment the successful record count
                        $successCount++;
                    } else {
                        // Increment the error record count
                        $errorCount++;
                    }
                }

                fclose($handle);
            } else {
                echo "<p>Error opening CSV file.</p>";
            }
        } else {
            echo "<p>Please upload a valid CSV file.</p>";
        }
    } else {
        echo "<p>CSV file is not uploaded.</p>";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title><strong>Import Vouchers from CSV</strong></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin: 20px;
        }

        h1 {
            margin-bottom: 20px;
            font-size: 24px;
            font-weight: bold;
        }

        form {
            margin-bottom: 20px;
        }

        label {
            font-weight: bold;
        }

        input[type="file"] {
            margin-top: 5px;
        }

        .message {
            margin-top: 10px;
            padding: 10px;
            border-radius: 4px;
            font-size: 18px;
            text-align: center;
        }

        .success {
            background-color: #DFF2BF;
            color: #4F8A10;
        }

        .error {
            background-color: #FFBABA;
            color: #D8000C;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Import Vouchers from CSV</h1>
        <form method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label for="csv_file">CSV File:</label>
                <input type="file" class="form-control-file" id="csv_file" name="csv_file" accept=".csv">
            </div>
            <button type="submit" class="btn btn-primary">Import</button>
        </form>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            echo "<div class='message success'>Total records stored: $successCount</div>";
            echo "<div class='message error'>Total error records: $errorCount</div>";
        }
        ?>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>