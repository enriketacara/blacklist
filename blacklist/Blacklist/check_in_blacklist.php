<?php
//header('Content-Type: application/json');
//$rootDir = __DIR__ . '/../../../';
//include_once($rootDir . "config/app_config.php");
//include_once($rootDir . "config/globals.php");
//$dbh = include($rootDir . 'config/connection.php');
//if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//
//    // Retrieve the number from the POST data
//    $number = $_POST['number'];
//    $blacklist = $_POST['blacklist'];
//    // Check if the number contains only numerical characters
//    if (!preg_match('/^\d+$/', $number)) {
//        $response = array(
//            'success' => false,
//            'message' => 'Invalid number format. Only numerical characters are allowed with no whitespace.'
//        );
//        echo json_encode($response);
//        exit;
//    }
//    // Execute the Asterisk command to check the number in the blacklist
//    $command = "asterisk -rx \"database show $blacklist '$number'\"";
//    $output = shell_exec("$command");
//
//    // Check if the output contains the number
//    if (strpos($output, $number) !== false) {
//        $response = array(
//            'success' => true,
//            'message' => 'Number ' . $number . ' is in the blacklist: '.$blacklist
//        );
//    } else {
//        $response = array(
//            'success' => false,
//            'message' => 'Number ' . $number . ' is not in the blacklist: '.$blacklist
//        );
//    }
//
//    // Output the response in JSON format
//    echo json_encode($response);
//
//
//} else {
//    $response = array(
//        'success' => false,
//        'message' => 'Invalid request method.'
//    );
//    echo json_encode($response);
//}

header('Content-Type: application/json');
$rootDir = __DIR__ . '/../../../';
include_once($rootDir . "config/app_config.php");
include_once($rootDir . "config/globals.php");
$dbh = include($rootDir . 'config/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Retrieve the number from the POST data
    $number = $_POST['number'];

    // Check if the number contains only numerical characters
    if (!preg_match('/^\+?\d+$/', $number)) {
        $response = array(
            'success' => false,
            'message' => 'Invalid number format. Only numerical characters are allowed with no whitespace.'
        );
        echo json_encode($response);
        exit;
    }

    try {
        // Retrieve all blacklists from the database
        $stmt = $dbh->query("SELECT name FROM blacklists");
        $blacklists = $stmt->fetchAll(PDO::FETCH_COLUMN);

        $foundInBlacklists = [];

        // Check the number against each blacklist
        foreach ($blacklists as $blacklist) {
            $command = "asterisk -rx \"database show $blacklist $number\"";
            $output = shell_exec($command);

            if (strpos($output, $number) !== false) {
                $foundInBlacklists[] = $blacklist;
            }
        }

        // Prepare the response
        if (!empty($foundInBlacklists)) {
            $response = array(
                'success' => true,
                'message' => 'Number ' . $number . ' is in the following blacklists: ' . implode(', ', $foundInBlacklists)
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'Number ' . $number . ' is not found in any blacklists.'
            );
        }

    } catch (PDOException $e) {
        $response = array(
            'success' => false,
            'message' => 'Error checking blacklists: ' . $e->getMessage()
        );
    }

    // Output the response in JSON format
    echo json_encode($response);

} else {
    $response = array(
        'success' => false,
        'message' => 'Invalid request method.'
    );
    echo json_encode($response);
}


?>
