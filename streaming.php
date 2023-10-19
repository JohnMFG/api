<?php
header('Content-Type: text/event-stream');
header('Cache-Control: no-cache');
header('Access-Control-Allow-Origin: http://localhost:3000');

// Function to send data as an SSE event
function sendSSE($data) {
    echo "data: $data\n\n";
    ob_flush();
    flush();
}

// Simulate streaming data (you can replace this with your actual data source)
for ($i = 0; $i < 10; $i++) {
    $message = "This is message $i";
    sendSSE($message);
    sleep(1); // Simulate data arriving every 1 second
}
