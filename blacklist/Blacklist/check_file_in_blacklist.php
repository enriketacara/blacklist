<?php
header('Content-Type: application/json');
$rootDir = __DIR__ . '/../../../';
include_once($rootDir . "config/app_config.php");
include_once($rootDir . "config/globals.php");
$dbh = include($rootDir . 'config/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['file'])) {
    // Check if the file is uploaded
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

    // Collect invalid numbers
    $invalidNumbers = array();

    // Validate each number
    foreach ($fileContent as $number) {
        // Trim whitespace and check if the number contains only numerical characters or starts with a plus
        $number = trim($number);
        if (!preg_match('/^\+?\d+$/', $number)) {
            $invalidNumbers[] = $number;
        }
    }

    // Check if there are any invalid numbers
    if (!empty($invalidNumbers)) {
        $response = array(
            'success' => false,
            'message' => 'Invalid number format found in the file. Only numerical characters are allowed.',
            'invalidNumbers' => $invalidNumbers // Include invalid numbers in the response
        );
        echo json_encode($response);
        exit;
    }

    // Retrieve all blacklists from the database
    try {
        $stmt = $dbh->query("SELECT name FROM blacklists");
        $blacklists = $stmt->fetchAll(PDO::FETCH_COLUMN);
    } catch (PDOException $e) {
        $response = array(
            'success' => false,
            'message' => 'Error retrieving blacklists: ' . $e->getMessage()
        );
        echo json_encode($response);
        exit;
    }

    $blacklistNumbers = array();

    // Check each number in each blacklist
    foreach ($fileContent as $number) {
        foreach ($blacklists as $blacklist) {
            $command = escapeshellcmd("asterisk -rx \"database show $blacklist $number\"");
            $output = shell_exec($command);

            if (strpos($output, $number) !== false) {
                if (!isset($blacklistNumbers[$blacklist])) {
                    $blacklistNumbers[$blacklist] = array();
                }
                $blacklistNumbers[$blacklist][] = $number;
            }
        }
    }

    if (!empty($blacklistNumbers)) {
        $html = '<ul>';
        foreach ($blacklistNumbers as $blacklist => $numbers) {
            $html .= '<li>' . htmlspecialchars($blacklist) . ': ' . htmlspecialchars(implode(', ', $numbers)) . '</li>';
        }
        $html .= '</ul>';

        $response = array(
            'success' => true,
            'message' => count($blacklistNumbers) . ' numbers are in the blacklist(s).',
            'blacklistNumbers' => $blacklistNumbers,
            'html' => $html
        );
    } else {
        $response = array(
            'success' => true,
            'message' => 'None of the numbers in the file are in any blacklist.'
        );
    }

    echo json_encode($response);

} else {
    // Invalid request method
    $response = array(
        'success' => false,
        'message' => 'Invalid request method or no file uploaded.'
    );
    echo json_encode($response);
}
?>
