<?php

require_once 'config/db.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
	    error_log("ID received: " . $id); // Log the ID for debugging
    // Create SQL statement to get the file_path based on ID
    $sql = "SELECT file_path FROM detec_history WHERE id = ?";
    
    // Use prepared statement
    $stmt = $conn->prepare($sql);
    $stmt->execute([$id]);
    $filePath = $stmt->fetchColumn(); // Get the file path

    // Check if the file path is valid and the file exists
    if (!empty($filePath) && file_exists($filePath)) {
        // Set the content type appropriately
        header('Content-Type: text/plain'); // Change if necessary
        header('Content-Length: ' . filesize($filePath)); // Optional
        // Read the file and output it
        readfile($filePath);
        exit; // Ensure no further output is sent
    } else {
        // If the file is not found, send a 404 status
        http_response_code(404);
        echo json_encode(["error" => "File not found."]); // Return a JSON response
    }
} else {
    // If no ID is provided, send a 400 status
    http_response_code(400);
    echo json_encode(["error" => "Invalid request."]); // Return a JSON response
}
?>

