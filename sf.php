<?php

require_once 'vendor/autoload.php';
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

// Function to log messages to a file
function logMessage($message) {
    $logFile = 'salesforce_logs.txt';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

// Salesforce OAuth 2.0 details
$salesforceLoginUrl = 'https://test.salesforce.com';
$salesforceClientId = 'your_client_id'; // Replace with your Salesforce Connected App's client ID
$salesforceClientSecret = 'your_client_secret'; // Replace with your Salesforce Connected App's client secret
$salesforceUsername = 'your_salesforce_username'; 
$salesforcePassword = 'your_salesforce_password';
$salesforceSecurityToken = 'your_salesforce_security_token'; // Replace with your Salesforce security token

// Log Salesforce credentials
logMessage("Salesforce credentials: Username - $salesforceUsername, ClientId - $salesforceClientId");

// Initialize Guzzle HTTP client
$client = new Client();

try {
    // Salesforce OAuth 2.0 authentication endpoint
    $authUrl = "$salesforceLoginUrl/services/oauth2/token";

    // Parameters for OAuth 2.0 authentication
    $authParams = [
        'form_params' => [
            'grant_type' => 'password',
            'client_id' => $salesforceClientId,
            'client_secret' => $salesforceClientSecret,
            'username' => $salesforceUsername,
            'password' => $salesforcePassword . $salesforceSecurityToken
        ]
    ];

    // Make a POST request to obtain the access token
    $authResponse = $client->post($authUrl, $authParams);

    // Decode the JSON response
    $authData = json_decode($authResponse->getBody(), true);

    // Extract access token
    $accessToken = $authData['access_token'];

    // Log access token
    logMessage("Access token obtained: $accessToken");

    // Salesforce REST API endpoint
    $salesforceApiUrl = 'https://your_salesforce_instance_url/services/data/v52.0/query?q=SELECT+Id+FROM+Account+LIMIT+1';

    // Make a GET request to Salesforce API
    $response = $client->get($salesforceApiUrl, [
        'headers' => [
            'Authorization' => 'Bearer ' . $accessToken
        ]
    ]);

    // Get response body
    $result = $response->getBody();

    // Output the result
    echo $result;
} catch (RequestException $e) {
    // Log error if request fails
    logMessage("Request failed: " . $e->getMessage());
    echo "Failed to connect to Salesforce.";
}

?>
