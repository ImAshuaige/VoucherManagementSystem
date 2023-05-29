<?php

// Connect to the database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "voucher";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Pagination variables
$records_per_page = 10;
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start_from = ($page - 1) * $records_per_page;

// Filter variables
$customer_id = isset($_GET['customer_id']) ? $_GET['customer_id'] : '';
$customer_name = isset($_GET['customer_name']) ? $_GET['customer_name'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : '';
$issued_date = isset($_GET['issued_date']) ? $_GET['issued_date'] : '';
$expiration_date = isset($_GET['expiration_date']) ? $_GET['expiration_date'] : '';

// Construct the SQL query without pagination
$count_query = "SELECT COUNT(*) AS total_records FROM vouchers WHERE 1=1";

// Construct the SQL query with pagination
$query = "SELECT * FROM vouchers WHERE 1=1";

// Filtering
if (!empty($customer_id)) {
    $count_query .= " AND customer_id LIKE '%$customer_id%'";
    $query .= " AND customer_id LIKE '%$customer_id%'";
}

if (!empty($customer_name)) {
    $count_query .= " AND customer_name LIKE '%$customer_name%'";
    $query .= " AND customer_name LIKE '%$customer_name%'";
}

if (!empty($type)) {
    $count_query .= " AND voucher_type = '$type'";
    $query .= " AND voucher_type = '$type'";
}

if (!empty($issued_date)) {
    $count_query .= " AND issued_date = '$issued_date'";
    $query .= " AND issued_date = '$issued_date'";
}

if (!empty($expiration_date)) {
    $count_query .= " AND expiration_date = '$expiration_date'";
    $query .= " AND expiration_date = '$expiration_date'";
}

// Execute the count query
$count_result = $conn->query($count_query);

if ($count_result) {
    $count_row = $count_result->fetch_assoc();
    $total_records = $count_row['total_records'];
} else {
    $total_records = 0;
}

// Calculate total number of pages
$total_pages = ceil($total_records / $records_per_page);

// Execute the query with pagination
$query .= " LIMIT $start_from, $records_per_page";

// Execute the query to fetch records
$result = $conn->query($query);
?>


<!DOCTYPE html>
<html>

<head>
    <title>Voucher Listing</title>
    <link rel="stylesheet" href="bootstrap.min.css">
    <style>
        /* custom styles here */
        table {
            border-collapse: collapse;
            width: 100%;
        }

        th,
        td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        .edit-form-container {
            display: none;
            border: 1px solid #ccc;
            padding: 10px;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background-color: #f9f9f9;
        }

        .pagination {
            display: flex;
            justify-content: center;
            align-items: flex-end;
        }

        .pagination ul {
            list-style-type: none;
            margin: 0;
            padding: 0;
        }

        .pagination li {
            display: inline-block;
            margin: 0 5px;
        }

        .pagination li a {
            text-decoration: none;
        }
    </style>
    <script src="bootstrap.min.js"></script>
    <script>
        function showEditForm(id) {
            var formContainer = document.getElementById('edit-form-container-' + id);
            formContainer.style.display = 'block';
        }

        function hideEditForm(id) {
            var formContainer = document.getElementById('edit-form-container-' + id);
            formContainer.style.display = 'none';
        }
    </script>
</head>

<body>
    <div class="container">
        <h2>Voucher Listing</h2>
        <form method="GET" action="display_voucher.php" class="mb-3">
            <div class="row">
                <div class="col-md-2">
                    <label for="customer_id" class="form-label">Customer ID:</label>
                    <input type="text" class="form-control" name="customer_id" value="<?php echo $customer_id; ?>">
                </div>
                <div class="col-md-2">
                    <label for="customer_name" class="form-label">Customer Name:</label>
                    <input type="text" class="form-control" name="customer_name" value="<?php echo $customer_name; ?>">
                </div>
                <div class="col-md-2">
                    <label for="type" class="form-label">Type:</label>
                    <select class="form-select" name="type">
                        <option value="">All</option>
                        <option value="GST" <?php if ($type == 'GST')
                            echo 'selected'; ?>>GST</option>
                        <option value="Rebate" <?php if ($type == 'Rebate')
                            echo 'selected'; ?>>Rebate</option>
                        <option value="CDAC" <?php if ($type == 'CDAC')
                            echo 'selected'; ?>>CDAC</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="issued_date" class="form-label">Issued Date:</label>
                    <input type="date" class="form-control" name="issued_date" value="<?php echo $issued_date; ?>">
                </div>
                <div class="col-md-2">
                    <label for="expiration_date" class="form-label">Expiration Date:</label>
                    <input type="date" class="form-control" name="expiration_date"
                        value="<?php echo $expiration_date; ?>">
                </div>
                <div class="col-md-2 mt-4">
                    <label class="form-label"></label>
                    <input type="submit" class="btn btn-success" value="Filter">
                </div>
            </div>
        </form>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer ID</th>
                    <th>Customer Name</th>
                    <th>Type</th>
                    <th>Amount</th>
                    <th>Issued Date</th>
                    <th>Expiration Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php
                // Display records
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td>" . $row["id"] . "</td>";
                        echo "<td>" . $row["customer_id"] . "</td>";
                        echo "<td>" . $row["customer_name"] . "</td>";
                        echo "<td>" . $row["voucher_type"] . "</td>";
                        echo "<td>" . $row["amount"] . "</td>";
                        echo "<td>" . $row["issued_date"] . "</td>";
                        echo "<td>" . $row["expiration_date"] . "</td>";
                        echo "<td>";
                        echo "<a href='javascript:void(0)' onclick='showEditForm(" . $row["id"] . ")' class='btn btn-primary btn-sm'>Edit</a>";
                        echo " ";
                        echo "<a href='?action=delete&id=" . $row["id"] . "' class='btn btn-danger btn-sm'>Delete</a>";
                        echo "</td>";
                        echo "</tr>";

                        // Edit form container
                        echo "<tr id='edit-form-container-" . $row["id"] . "' class='edit-form-container' style='display: none;'>";
                        echo "<td colspan='8'>";
                        echo "<h3>Edit Voucher</h3>";
                        echo "<form method='POST' action=''>";
                        echo "<input type='hidden' name='id' value='" . $row['id'] . "'>";
                        echo "<div class='row'>";
                        echo "<div class='col-md-4'>";
                        echo "<label class='form-label'>Customer ID:</label>";
                        echo "<input type='text' class='form-control' name='customer_id' value='" . $row['customer_id'] . "'>";
                        echo "</div>";
                        echo "<div class='col-md-4'>";
                        echo "<label class='form-label'>Customer Name:</label>";
                        echo "<input type='text' class='form-control' name='customer_name' value='" . $row['customer_name'] . "'>";
                        echo "</div>";
                        echo "<div class='col-md-4'>";
                        echo "<label class='form-label'>Type:</label>";
                        echo "<select class='form-select' name='voucher_type'>";
                        echo "<option value='GST'" . ($row['voucher_type'] == 'GST' ? 'selected' : '') . ">GST</option>";
                        echo "<option value='Rebate'" . ($row['voucher_type'] == 'Rebate' ? 'selected' : '') . ">Rebate</option>";
                        echo "<option value='CDAC'" . ($row['voucher_type'] == 'CDAC' ? 'selected' : '') . ">CDAC</option>";
                        echo "</select>";
                        echo "</div>";
                        echo "</div>";
                        echo "<div class='row'>";
                        echo "<div class='col-md-4'>";
                        echo "<label class='form-label'>Amount:</label>";
                        echo "<input type='text' class='form-control' name='amount' value='" . $row['amount'] . "'>";
                        echo "</div>";
                        echo "<div class='col-md-4'>";
                        echo "<label class='form-label'>Issued Date:</label>";
                        echo "<input type='date' class='form-control' name='issued_date' value='" . $row['issued_date'] . "'>";
                        echo "</div>";
                        echo "<div class='col-md-4'>";
                        echo "<label class='form-label'>Expiration Date:</label>";
                        echo "<input type='date' class='form-control' name='expiration_date' value='" . $row['expiration_date'] . "'>";
                        echo "</div>";
                        echo "</div>";
                        echo "<br>";
                        echo "<div class='row'>";
                        echo "<div class='col-md-4'>";
                        echo "<input type='submit' class='btn btn-primary' name='submit_edit' value='Save'>";
                        echo "</div>";
                        echo "<div class='col-md-4'>";
                        echo "<a href='javascript:void(0)' onclick='hideEditForm(" . $row["id"] . ")' class='btn btn-secondary'>Cancel</a>";
                        echo "</div>";
                        echo "</div>";
                        echo "</form>";
                        echo "</td>";
                        echo "</tr>";

                    }
                } else {
                    echo "<tr><td colspan='8'>No records found.</td></tr>";
                }
                ?>
        </table>
        <br>
        <?php
        $base_url = $_SERVER['PHP_SELF'];


        // Display pagination links
        echo "<div class='pagination'>";
        echo "<ul>";

        if ($page > 1) {
            echo "<li><a href='" . $base_url . "?page=" . ($page - 1) . "&customer_id=" . $customer_id . "&customer_name=" . $customer_name . "&type=" . $type . "&issued_date=" . $issued_date . "&expiration_date=" . $expiration_date . "' class='btn btn-primary btn-sm'>Previous</a></li>";
        }

        echo "<li><span class='current-page'>" . $page . "</span></li>";

        if ($page < $total_pages) {
            echo "<li><a href='" . $base_url . "?page=" . ($page + 1) . "&customer_id=" . $customer_id . "&customer_name=" . $customer_name . "&type=" . $type . "&issued_date=" . $issued_date . "&expiration_date=" . $expiration_date . "' class='btn btn-primary btn-sm'>Next</a></li>";
        }

        echo "</ul>";
        echo "</div>";


        // Process Delete Actions
        if (isset($_GET['action'])) {
            $action = $_GET['action'];
            $id = $_GET['id'];

            if ($action == 'delete') {
                // Delete the record
                $delete_query = "DELETE FROM vouchers WHERE id = $id";
                $delete_result = $conn->query($delete_query);

                if ($delete_result) {
                    echo "<script>alert('Record deleted successfully.');</script>";
                } else {
                    echo "<script>alert('Failed to delete the record.');</script>";
                }

                // Clear the action and redirect to the same page
                echo "<script>window.location.href = '$base_url?page=$page';</script>";
                exit;
            }
        }


        // Process the edited data
        if (isset($_POST['submit_edit'])) {
            $id = $_POST['id'];
            $customer_id = $_POST['customer_id'];
            $customer_name = $_POST['customer_name'];
            $voucher_type = $_POST['voucher_type'];
            $amount = $_POST['amount'];
            $issued_date = $_POST['issued_date'];
            $expiration_date = $_POST['expiration_date'];

            $errors = array();

            // Validate input data
            if (empty($customer_id) || empty($customer_name) || empty($voucher_type) || empty($amount) || empty($issued_date) || empty($expiration_date)) {
                $errors[] = "All fields are mandatory. Please fill in all the required fields.";
            }

            if (!is_numeric($amount) || $amount <= 0) {
                $errors[] = "Amount must be a numeric value greater than 0.";
            }

            $validVoucherTypes = ['GST', 'Rebate', 'CDAC'];
            if (!in_array($voucher_type, $validVoucherTypes)) {
                $errors[] = "Invalid voucher type. Allowed voucher types are: GST, Rebate, CDAC.";
            }

            if (!strtotime($issued_date) || !strtotime($expiration_date)) {
                $errors[] = "Invalid date format for issued date or expiration date.";
            }

            // Validate expiration date against issued date
            if (strtotime($expiration_date) < strtotime($issued_date)) {
                $errors[] = "Expiration date cannot be earlier than the issued date.";
            }


            if (empty($errors)) {
                // Update the record
                $update_query = "UPDATE vouchers SET customer_id = '$customer_id', customer_name = '$customer_name', voucher_type = '$voucher_type', amount = '$amount', issued_date = '$issued_date', expiration_date = '$expiration_date' WHERE id = $id";
                $update_result = $conn->query($update_query);
            
                if ($update_result) {
                    echo "<script>alert('Record updated successfully.');</script>";
                    echo "<script>window.location.href = '$base_url?page=$page';</script>";
                    exit;
                } else {
                    $error_message = "Failed to update the record.";
                }
            } else {
                $error_message = "Failed to update the record. Errors: " . implode('<br>', $errors);
                echo "<script>alert('$error_message');</script>";
            }
            // question about error handling here. Cannot output the desire way. 
        }


        // Close the database connection
        $conn->close();
        ?>
    </div>
</body>

</html>