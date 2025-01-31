<?php
// Start session to get sessionId
session_start();

// Define API configuration
$apiUrl = "http://41.216.166.147:8081/v1/chat/completions";
$apiKey = "rc2A8ranm8PBehbN50V3u26AMX7ikVgY4BaH0oCViGU";

// Function to generate chat response from API
function generateResponse($userMessage, $sessionId)
{
    global $apiUrl, $apiKey;

    // Prepare payload
    $payload = [
        "prompt" => $userMessage,
        "sessionId" => $sessionId,
        "location" => null
    ];

    // Initialize cURL
    $ch = curl_init();

    // Set cURL options
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Content-Type: application/json",
        "x-api-key: $apiKey"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));

    // Execute the request and handle errors
    $response = curl_exec($ch);

    if (curl_errno($ch)) {
        return ["success" => false, "error" => curl_error($ch)];
    }

    // Close cURL
    curl_close($ch);

    // Decode and return response
    return json_decode($response, true);
}

// Handle chat input from the user
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $userMessage = trim($_POST['message']);
    $sessionId = $_POST['sessionId'] ?? $_SESSION['sessionId'] ?? null;

    if (empty($userMessage)) {
        echo json_encode(["success" => false, "error" => "Message cannot be empty"]);
        exit;
    }

    // Check if session ID is available
    if (!$sessionId) {
        echo json_encode(["success" => false, "error" => "Session ID is missing or expired"]);
        exit;
    }

    // Generate API response
    $apiResponse = generateResponse($userMessage, $sessionId);

    if ($apiResponse['success']) {
        echo json_encode([
            "success" => true,
            "response" => $apiResponse['data']['response'] ?? "No response received"
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "error" => $apiResponse['error'] ?? "An unknown error occurred"
        ]);
    }
    exit;
}
