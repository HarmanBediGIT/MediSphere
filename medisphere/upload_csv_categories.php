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

    $expectedHeaders = ['code','name','price_range'];

    if ($header !== $expectedHeaders) {
        echo json_encode(['success' => false, 'message' => 'CSV headers do not match expected format. Found: ' . implode(', ', $header)]);
        exit();
    }

    // Loop through each row
    while (($data = fgetcsv($handle)) !== false) {
        if (count($data) < 3) {
            $response['failed']++;
            $response['errors'][] = "Invalid column count: " . implode(",", $data);
            continue;
        }

        $escapedData = array_map([$conn, 'real_escape_string'], $data);
        $code = $escapedData[0];
        $name = $escapedData[1];
        $priceRange = $escapedData[2];

        $sql = "INSERT INTO categories (code, name, price_range) 
                VALUES ('$code', '$name', '$priceRange')";

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
        'message' => "{$response['added']} categories added successfully. {$response['failed']} failed.",
        'details' => $response['errors']
    ]);
?>
