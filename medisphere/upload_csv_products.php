<?php
    error_reporting(E_ALL);
    ini_set('display_errors', 1);

    require 'db_conn.php';

    $response = [
        'added' => 0,
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
    

    // Open the uploaded CSV file
    $handle = fopen($_FILES['csvFile']['tmp_name'], 'r');
    if ($handle === false) {
        echo json_encode(['success' => false, 'message' => 'Unable to open the CSV file.']);
        exit();
    }

    // Read header row
    $header = fgetcsv($handle);
    $header[0] = preg_replace('/^\xEF\xBB\xBF/', '', $header[0]);

    if ($header === false) {
        echo json_encode(['success' => false, 'message' => 'Failed to read header row.']);
        // exit();
    }

    // Debug: Show what PHP sees
    // echo json_encode(['success' => false, 'message' => 'Headers found: ' . implode(' | ', $header)]);
    // exit();

    $expectedHeaders = ['cat_code','prod_code','name','description','colors','sizes','material','manufacturer','qty','price'];

    if ($header !== $expectedHeaders) {
        echo json_encode(['success' => false, 'message' => 'CSV headers do not match expected format. Found: ' . implode(', ', $header)]);
        exit();
    }

    // Loop through each row
    while (($data = fgetcsv($handle)) !== false) {
        if (count($data) < 10) {
            $response['failed']++;
            $response['errors'][] = "Invalid column count: " . implode(",", $data);
            continue;
        }

        $escapedData = array_map([$conn, 'real_escape_string'], $data);
        $cat_code = $escapedData[0];
        $prod_code = $escapedData[1];
        $name = $escapedData[2];
        $description = $escapedData[3];
        $colors = $escapedData[4];
        $sizes = $escapedData[5];
        $material = $escapedData[6];
        $manufacturer = $escapedData[7];
        $qty = $escapedData[8];
        $price = $escapedData[9];

        $sql = "INSERT INTO products (cat_code, prod_code, name, description, colors, sizes, material, manufacturer, qty, price) 
                VALUES ('$cat_code', '$prod_code', '$name', '$description', '$colors', '$sizes', '$material', '$manufacturer', '$qty', '$price')";

        if ($conn->query($sql) === TRUE) {
            $response['added']++;
        } else {
            $response['failed']++;
            $response['errors'][] = "Failed to insert: " . implode(", ", $data) . " | Error: " . $conn->error;
        }
    }

    fclose($handle);
    $conn->close();

    echo json_encode([
        'success' => true,
        'message' => "{$response['added']} products added successfully. {$response['failed']} failed.",
        'details' => $response['errors']
    ]);
?>
