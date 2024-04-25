<?php

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

try {
    // Salesforce OAuth 2.0 authentication endpoint
    $authUrl = "$salesforceLoginUrl/services/oauth2/token";

    // Parameters for OAuth 2.0 authentication
    $authParams = [
        'grant_type' => 'password',
        'client_id' => $salesforceClientId,
        'client_secret' => $salesforceClientSecret,
        'username' => $salesforceUsername,
        'password' => $salesforcePassword . $salesforceSecurityToken
    ];

    // Set context options for HTTP request
    $contextOptions = [
        'http' => [
            'method' => 'POST',
            'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
            'content' => http_build_query($authParams)
        ]
    ];

    // Create context for stream
    $context = stream_context_create($contextOptions);

    // Make a POST request to obtain the access token
    $authResponse = file_get_contents($authUrl, false, $context);

    // Decode the JSON response
    $authData = json_decode($authResponse, true);

    // Extract access token
    $accessToken = $authData['access_token'];

    // Log access token
    logMessage("Access token obtained: $accessToken");

    // Salesforce REST API endpoint to query Account object
    $salesforceApiUrl = 'https://your_salesforce_instance_url/services/data/v52.0/query?q=SELECT+Name,+Type,+Industry+FROM+Account+LIMIT+1';

    // Make a GET request to Salesforce API to retrieve Account data
    $apiResponse = file_get_contents($salesforceApiUrl, false, stream_context_create([
        'http' => [
            'header' => "Authorization: Bearer $accessToken\r\n"
        ]
    ]));

    // Decode the JSON response
    $accountData = json_decode($apiResponse, true);

    // Extract account information
    $accountName = $accountData['records'][0]['Name'];
    $accountType = $accountData['records'][0]['Type'];
    $accountIndustry = $accountData['records'][0]['Industry'];

    // Display account information
    echo "<h1>Account Information</h1>";
    echo "<p><strong>Name:</strong> $accountName</p>";
    echo "<p><strong>Type:</strong> $accountType</p>";
    echo "<p><strong>Industry:</strong> $accountIndustry</p>";

} catch (Exception $e) {
    // Log error if request fails
    logMessage("Request failed: " . $e->getMessage());
    echo "Failed to connect to Salesforce.";
}

?>
