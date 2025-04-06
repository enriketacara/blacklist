<?php
header('Content-Type: application/json');
$rootDir = __DIR__ . '/../../../';
include_once($rootDir . "config/app_config.php");
include_once($rootDir . "config/globals.php");
$dbh = include($rootDir . 'config/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'];
    $name = $_POST['name'];

    try {
        // Delete the entry from the database
        $stmt = $dbh->prepare("DELETE FROM blacklists WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $dbSuccess = $stmt->execute();

        // Execute the Asterisk command to delete the blacklist content
        $command = "asterisk -rx \"database deltree $name\"";
        $output = shell_exec($command);
        $cmdSuccess = strpos($output, 'Removed') == false;

        if ($dbSuccess ) {
            $response = array(
                'success' => true,
                'message' => 'Blacklist entry and content deleted successfully.'
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'Failed to delete blacklist entry or content.'
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
