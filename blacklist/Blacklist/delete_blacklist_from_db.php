<?php
header('Content-Type: application/json');
$rootDir = __DIR__ . '/../../../';
include_once($rootDir . "config/app_config.php");
include_once($rootDir . "config/globals.php");
$dbh = include($rootDir . 'config/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];

    // Database connection
    try {

        // Delete the entry from the database
        $stmt = $dbh->prepare("DELETE FROM blacklists WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            $response = array(
                'success' => true,
                'message' => 'Blacklist entry deleted successfully from the database.'
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'Failed to delete blacklist entry from the database.'
            );
        }
        echo json_encode($response);
    } catch (PDOException $e) {
        $response = array(
            'success' => false,
            'message' => 'Database error: ' . $e->getMessage()
        );
        echo json_encode($response);
    }
} else {
    $response = array(
        'success' => false,
        'message' => 'Invalid request method.'
    );
    echo json_encode($response);
}
?>
