<?php
require 'db_conn.php';

// Set headers for download
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename=products_export.csv');

$output = fopen('php://output', 'w');

// Write CSV column headers
fputcsv($output, ['cat_code','prod_code','name','description','colors','sizes','material','manufacturer','qty','price']);

// Query all products
$sql = "SELECT cat_code, prod_code, name, description, colors, sizes, material, manufacturer, qty, price FROM products";
$result = $conn->query($sql);

// Write each row
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        fputcsv($output, $row);
    }
}

fclose($output);
$conn->close();
exit();
?>
