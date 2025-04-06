<?php
header('Content-Type: application/json');
$rootDir = __DIR__ . '/../../../';
include_once($rootDir . "config/app_config.php");
include_once($rootDir . "config/globals.php");
$dbh = include($rootDir . 'config/connection.php');
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Retrieve the number and blacklist name from the POST data
    $number = $_POST['number'];
    $blacklist = $_POST['blacklist'];

    // Check if the number contains only numerical characters with no whitespace
    if (!preg_match('/^\+?\d+$/', $number)) {
        $response = array(
            'success' => false,
            'message' => 'Invalid number format. Only numerical characters are allowed with no whitespace.'
        );
        echo json_encode($response);
        exit;
    }
    // Execute the Asterisk command to check if the number is already in the blacklist
    $checkCommand = escapeshellcmd("asterisk -rx \"database show $blacklist $number\"");
    $checkOutput = shell_exec($checkCommand);

    if (strpos($checkOutput, $number) !== false) {
        // The number is already in the blacklist
        $response = array(
            'warning' => true,
            'message' => 'Number ' . $number . ' is already in the blacklist: ' . $blacklist
        );
    } else {
        // The number is not in the blacklist, so add it

        $addCommand = escapeshellcmd("asterisk -rx \"database put $blacklist $number 1\"");
        $addOutput = shell_exec($addCommand);

        // Verify if the number was successfully added
        $verifyOutput = shell_exec($checkCommand);
        if (strpos($verifyOutput, $number) !== false) {
            $response = array(
                'success' => true,
                'message' => 'Number ' . $number . ' was successfully added to the blacklist: ' . $blacklist
            );
        } else {
            $response = array(
                'success' => false,
                'message' => 'Failed to add number ' . $number . ' to the blacklist: ' . $blacklist
            );
        }
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
