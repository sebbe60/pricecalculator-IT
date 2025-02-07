<?php
// Mailchimp API credentials
$apiKey = 'YOUR_MAILCHIMP_API_KEY';
$listId = 'YOUR_AUDIENCE_ID';
$serverPrefix = 'usXX'; // Replace 'usXX' with your Mailchimp data center, e.g., 'us1', 'us20'

// Set headers to allow access only from your website (replace 'yourwebsite.com')
header('Access-Control-Allow-Origin: https://yourwebsite.com');
header('Access-Control-Allow-Methods: POST');
header('Content-Type: application/json');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

$email = $data['email'] ?? null;
$name = $data['name'] ?? null;
$location = $data['location'] ?? null;
$membership = $data['membership'] ?? null;

// Validate required fields
if (!$email || !$name) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid input']);
    exit;
}

// Mailchimp API endpoint
$url = "https://$serverPrefix.api.mailchimp.com/3.0/lists/$listId/members";

// Prepare data for Mailchimp
$postData = [
    'email_address' => $email,
    'status' => 'subscribed',
    'merge_fields' => [
        'FNAME' => $name,
        'LOCATION' => $location,
        'MEMBERSHIP' => $membership,
    ],
];

// Make the API request
$options = [
    'http' => [
        'header' => "Authorization: apikey $apiKey\r\nContent-Type: application/json\r\n",
        'method' => 'POST',
        'content' => json_encode($postData),
    ],
];

$context = stream_context_create($options);
$response = file_get_contents($url, false, $context);

if ($response === FALSE) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Failed to add to Mailchimp']);
} else {
    echo json_encode(['success' => true, 'message' => 'Successfully added to Mailchimp!']);
}
?>
