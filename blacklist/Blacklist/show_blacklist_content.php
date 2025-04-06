<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['name'])) {
    $blacklistName = $_POST['name'];
//var_dump($blacklistName);
    // Sanitize the input to avoid command injection
//    $blacklistName = escapeshellarg($blacklistName);

    // Execute the Asterisk command to show the blacklist
    $command = "asterisk -rx \"database show $blacklistName\"";
//    var_dump( $command);
    $output = shell_exec($command);

    if ($output) {
        $response = array(
            'success' => true,
            'data' => $output
        );
    } else {
        $response = array(
            'success' => false,
            'message' => 'Failed to retrieve blacklist content.'
        );
    }

    echo json_encode($response);
} else {
    $response = array(
        'success' => false,
        'message' => 'Invalid request.'
    );
    echo json_encode($response);
}
?>
