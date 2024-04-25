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
$salesforceClientId = '3MVG9gtjsZa8aaSV4ayM_wa_OC02dG7go88eZDfok180duLsZbrIORMt5m8G6raFO_6x4sq7HkanmoiyX0Nap'; // Replace with your Salesforce Connected App's client ID
$salesforceClientSecret = '05C0BB5E93F814FABB7B24D25F3EE72100BECA806D6209A3BB71E19658CBC413'; // Replace with your Salesforce Connected App's client secret
$salesforceUsername = 'integrationuser@lacitec.on.ca.devfull'; 
$salesforcePassword = 'a;kA5-8UdB';
$salesforceSecurityToken = 'zPe1wuotnE6eungIJDH1WyYM5'; // Replace with your Salesforce security token

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
    $salesforceApiUrl = 'https://collegelacite--devfull.sandbox.lightning.force.com/services/data/v52.0/query?q=SELECT+Id+FROM+Account+LIMIT+1';

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
