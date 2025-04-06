<?php
header('Content-Type: application/json');
$rootDir = __DIR__ . '/../../../';
include_once($rootDir . "config/app_config.php");
include_once($rootDir . "config/globals.php");
$dbh = include($rootDir . 'config/connection.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    try {
        $blacklistName = $_POST['blacklist'];
        if (preg_match('/^[a-zA-Z0-9]+$/', $blacklistName)) {
        $stmt = $dbh->prepare("INSERT INTO blacklists (name, datetime) VALUES (:name, NOW())");
        $stmt->bindParam(':name', $blacklistName);
        $stmt->execute();
        $response = array(
            'success' => true,
            'message' => 'Blacklist name received: ' . htmlspecialchars($blacklistName)
        );
        } else {
            // Response on validation failure
            $response = array(
                'error' => true,
                'message' => 'Invalid blacklist name. Only alphanumeric characters are allowed.'
            );
        }
    } catch (PDOException $e) {
        $response = array(
            'error' => true,
            'message' => 'Error creating blacklist: ' . $e->getMessage()
        );
    }
    echo json_encode($response);
} else {
    $response = array(
        'success' => false,
        'message' => 'Invalid request method.'
    );
    echo json_encode($response);
}
?>
