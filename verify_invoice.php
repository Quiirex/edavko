<?php

// Get the values from the form
$eor = $_POST['edavko_verify_invoice_eor'];

// Set the API endpoint URL with query parameter
$url = 'http://studentdocker.informatika.uni-mb.si:49163/check-invoice?eor=' . urlencode($eor);

// Set the Bearer token
$token = 'Bearer 1002376637';

// Set the headers for the request
$headers = array(
    'Content-Type: application/json',
    'Authorization: ' . $token
);

// Create the cURL request
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the request
$response = curl_exec($ch);

// Check for errors
if (curl_errno($ch)) {
    echo 'Error: ' . curl_error($ch);
} else {
    // Decode the response JSON
    $result = json_decode($response, true);

    // Check the response status
    if ($result['status'] == 'SUCCESS') {
        echo 'Račun je veljaven.';
    } else {
        echo 'Račun ni veljaven.';
    }
}

// Close the cURL request
curl_close($ch);

?>