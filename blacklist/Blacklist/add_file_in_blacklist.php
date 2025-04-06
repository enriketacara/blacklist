<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {

    // Check if the file is uploaded successfully
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $response = array(
            'success' => false,
            'message' => 'Error uploading the file.'
        );
        echo json_encode($response);
        exit;
    }

    // Get the file content
    $fileTmpPath = $_FILES['file']['tmp_name'];
    $fileContent = file($fileTmpPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    $blacklist = $_POST['blacklist']; // Get the blacklist name from the POST data
    $addedNumbers = [];
    $failedNumbers = [];

    // Process each number in the file
    foreach ($fileContent as $number) {
        // Trim whitespace and check if the number contains only numerical characters
        $number = trim($number);
        if (preg_match('/^\+?\d+$/', $number)) {
            $command = "asterisk -rx \"database put $blacklist $number 1\"";
            $output = shell_exec($command);
            // Verify if the number was successfully added
            $verifyCommand = "asterisk -rx \"database show $blacklist $number\"";
            $verifyOutput = shell_exec($verifyCommand);
            if (strpos($verifyOutput, $number) !== false) {
                $addedNumbers[] = $number;
            } else {
                $failedNumbers[] = $number;
            }
        } else {
            $failedNumbers[] = $number;
        }
    }

    if (!empty($addedNumbers)) {
        $response = array(
            'success' => true,
            'message' => count($addedNumbers) . ' numbers were added to the blacklist: ' . $blacklist,
            'failed' => $failedNumbers
        );
    } else {
        $response = array(
            'success' => false,
            'message' => 'No valid numbers were added to the blacklist.',
            'failed' => $failedNumbers
        );
    }

    // Output the response in JSON format
    echo json_encode($response);
} else {
    $response = array(
        'success' => false,
        'message' => 'Invalid request method or no file uploaded.'
    );
    echo json_encode($response);
}
?>


