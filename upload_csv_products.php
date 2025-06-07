<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: application/json');

require 'db_conn.php';

$response = [
    'added' => 0,
    'updated' => 0,
    'failed' => 0,
    'errors' => [],
];

if (!isset($_FILES['csvFile']) || $_FILES['csvFile']['error'] !== UPLOAD_ERR_OK) {
    echo json_encode(['success' => false, 'message' => 'Upload error: ' . $_FILES['csvFile']['error']]);
    exit();
}

$tmpName = $_FILES['csvFile']['tmp_name'];

if (!is_uploaded_file($tmpName) || !($handle = fopen($tmpName, 'r'))) {
    echo json_encode(['success' => false, 'message' => 'Unable to open the CSV file.']);
    exit();
}

// Read header row
$header = fgetcsv($handle);
$header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]); // handle BOM

$expectedHeaders = ['cat_code','prod_code','name','description','colors','sizes','material','manufacturer','qty','price'];

if ($header !== $expectedHeaders) {
    echo json_encode(['success' => false, 'message' => 'CSV headers do not match expected format. Found: ' . implode(', ', $header)]);
    exit();
}

while (($data = fgetcsv($handle)) !== false) {
    if (count($data) < 10) {
        $response['failed']++;
        $response['errors'][] = "Invalid column count: " . implode(",", $data);
        continue;
    }

    $escapedData = array_map([$conn, 'real_escape_string'], $data);
    list($cat_code, $prod_code, $name, $description, $colors, $sizes, $material, $manufacturer, $qty, $price) = $escapedData;

    // Check if product exists by prod_code
    $checkSql = "SELECT prod_code FROM products WHERE prod_code = '$prod_code'";
    $result = $conn->query($checkSql);

    if ($result && $result->num_rows > 0) {
        // Update if exists
        $updateSql = "UPDATE products 
                      SET cat_code='$cat_code', name='$name', description='$description', colors='$colors', 
                          sizes='$sizes', material='$material', manufacturer='$manufacturer', qty='$qty', price='$price' 
                      WHERE prod_code='$prod_code'";
        if ($conn->query($updateSql) === TRUE) {
            $response['updated']++;
        } else {
            $response['failed']++;
            $response['errors'][] = "Failed to update: " . implode(", ", $data) . " | Error: " . $conn->error;
        }
    } else {
        // Insert if not exists
        $insertSql = "INSERT INTO products 
                      (cat_code, prod_code, name, description, colors, sizes, material, manufacturer, qty, price) 
                      VALUES ('$cat_code', '$prod_code', '$name', '$description', '$colors', '$sizes', '$material', '$manufacturer', '$qty', '$price')";
        if ($conn->query($insertSql) === TRUE) {
            $response['added']++;
        } else {
            $response['failed']++;
            $response['errors'][] = "Failed to insert: " . implode(", ", $data) . " | Error: " . $conn->error;
        }
    }
}

fclose($handle);
$conn->close();

echo json_encode([
    'success' => true,
    'message' => "{$response['added']} products added, {$response['updated']} updated, {$response['failed']} failed.",
    'details' => $response['errors']
]);
?>
