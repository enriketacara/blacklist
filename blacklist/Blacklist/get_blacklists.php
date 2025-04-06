<?php
header('Content-Type: application/json');
$rootDir = __DIR__ . '/../../../';
include_once($rootDir . "config/app_config.php");
include_once($rootDir . "config/globals.php");
$dbh = include($rootDir . 'config/connection.php');
if ($_SERVER['REQUEST_METHOD'] === 'GET') {

    try {
        // Retrieve data from the blacklist table
        $stmt = $dbh->query("SELECT * FROM blacklists");
        $blacklistData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Prepare data for DataTables
        $data = [];
        foreach ($blacklistData as $row) {
            $data[] = [
                'id' => $row['id'],
                'name' => $row['name'],
                'datetime' => $row['datetime']
            ];
        }

        // Response with data for DataTables
        $response = array(
            'data' => $data
        );

        // Output the response in JSON format
        echo json_encode($response);
    } catch (PDOException $e) {
        // Response on exception
        $response = array(
            'error' => true,
            'message' => 'Error: ' . $e->getMessage()
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
