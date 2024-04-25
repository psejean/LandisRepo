<?php

require_once 'vendor/autoload.php';

// Function to log messages to a file
function logMessage($message) {
    $logFile = 'salesforce_logs.txt';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

// Get ScenarioId from the URL and sanitize it
$ScenarioId = isset($_GET['ScenarioId']) ? htmlspecialchars($_GET['ScenarioId']) : '';

// Salesforce OAuth 2.0 details
$salesforceLoginUrl = 'https://test.salesforce.com';
$salesforceClientId = '3MVG9gtjsZa8aaSV4ayM_wa_OC02dG7go88eZDfok180duLsZbrIORMt5m8G6raFO_6x4sq7HkanmoiyX0Nap'; // Replace with your Salesforce Connected App's client ID
$salesforceClientSecret = '05C0BB5E93F814FABB7B24D25F3EE72100BECA806D6209A3BB71E19658CBC413'; // Replace with your Salesforce Connected App's client secret
$salesforceUsername = 'integrationuser@lacitec.on.ca.devfull'; 
$salesforcePassword = 'a;kA5-8UdB';
$salesforceSecurityToken = 'zPe1wuotnE6eungIJDH1WyYM5'; // Replace with your Salesforce security token

// Log Salesforce credentials
logMessage("Salesforce credentials: Username - $salesforceUsername, ClientId - $salesforceClientId");

// Perform OAuth 2.0 authentication with Salesforce
$authUrl = "$salesforceLoginUrl/services/oauth2/token";
$authParams = array(
    'grant_type' => 'password',
    'client_id' => $salesforceClientId,
    'client_secret' => $salesforceClientSecret,
    'username' => $salesforceUsername,
    'password' => $salesforcePassword . $salesforceSecurityToken // Include the security token in the password
);


// Initialize cURL session
$ch = curl_init();

// Set cURL options for authentication
curl_setopt($ch, CURLOPT_URL, $authUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($authParams));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute cURL request
$authResponse = curl_exec($ch);

// Check for errors
if (curl_error($ch)) {
    // Log cURL error
    logMessage("cURL error: " . curl_error($ch));
}

// Decode the JSON response
$authData = json_decode($authResponse, true);

// Extract access token
$accessToken = $authData['access_token'];

// Log access token
logMessage("Access token obtained: $accessToken");

// Salesforce API endpoint for custom object query
$salesforceQueryUrl = 'https://collegelacite--devfull.sandbox.lightning.force.com/services/data/v59.0/query/?q=';

// Query to retrieve data from Salesforce
$query = "SELECT CallerNumber__c, CallerName__c, StudentID__c, Contact__c, ContactName__c, Name FROM ContactCallLog__c WHERE Name='$ScenarioId'";

// Set up cURL session for Salesforce API call
curl_setopt($ch, CURLOPT_URL, $salesforceQueryUrl . urlencode($query));
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    "Authorization: Bearer $accessToken", // Include the access token in the Authorization header
    "Content-Type: application/json",     // Specify the content type as JSON
));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute cURL request to Salesforce API
$response = curl_exec($ch);

// Check for errors
if (curl_error($ch)) {
    // Log cURL error
    logMessage("cURL error: " . curl_error($ch));
}

// Close cURL session
curl_close($ch);

// Decode JSON response from Salesforce API
$result = json_decode($response, true);

// Log Salesforce response
logMessage("Salesforce response: " . json_encode($result));

// Check if $result is empty or null or if records are empty
if (empty($result) || !isset($result['records']) || empty($result['records'])) {
    // Handle the case when no records are returned
    echo "No records found for the provided ScenarioId.";
} else {
    // Extract data from Salesforce response
    $CallerNumber = isset($result['records'][0]['CallerNumber__c']) ? $result['records'][0]['CallerNumber__c'] : '';
    $CallerName = isset($result['records'][0]['CallerName__c']) ? $result['records'][0]['CallerName__c'] : '';
    $StudentID = isset($result['records'][0]['StudentID__c']) ? $result['records'][0]['StudentID__c'] : '';
    $ContactID = isset($result['records'][0]['Contact__c']) ? $result['records'][0]['Contact__c'] : '';
    $ContactName = isset($result['records'][0]['ContactName__c']) ? $result['records'][0]['ContactName__c'] : '';

    // Log extracted data
    logMessage("Extracted data: CallerNumber - $CallerNumber, CallerName - $CallerName, StudentID - $StudentID, ContactID - $ContactID, ContactName - $ContactName");

    // Now you can use the extracted data in your HTML output as needed
}

?>

<!doctype html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-Equiv="Cache-Control" Content="no-cache">
<meta http-Equiv="Pragma" Content="no-cache">
<meta http-Equiv="Expires" Content="0">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Landis-Salesforce-Popup</title>
<style type="text/css">
body {
    background-color: #FFFFFF;
    color: #000000;
    font-size: 18px;
}
</style>
</head>

<script language="javascript">
function F_Launch(IN){
    var V_URL="https://collegelacite.lightning.force.com/lightning/r/Contact/"+IN+"/view";
    window.open(V_URL,'Landis-SF');
    return; 
}
</script>

<body>
    <table width="95%" border="4" align="center">
      <tbody>
        <tr>
          <td align="center" valign="middle" nowrap="nowrap" bgcolor="#000000" style="color: #FFFFFF; font-size: 24px; font-weight: bold;">Landis - Salesforce</td>
        </tr>
      </tbody>
    </table>
    <table width="90%" border="2" align="center">
      <tbody>
        <tr>
          <td width="25%" align="right" valign="middle" bgcolor="#AAAAAA" style="font-size: 18px">ScenarioId</td>
          <td align="left" valign="middle" bgcolor="#FFFFFF" style="font-size: 18px; color: #000000;"><?php echo $ScenarioId; ?></td>
        </tr>
        <tr>
          <td width="25%" align="right" valign="middle" bgcolor="#AAAAAA" style="font-size: 18px">CallerNumber</td>
          <td align="left" valign="middle" bgcolor="#FFFFFF" style="font-size: 18px; color: #000000;"><?php echo $CallerNumber; ?></td>
        </tr>
        <tr>
          <td width="25%" align="right" valign="middle" bgcolor="#AAAAAA" style="font-size: 18px">CallerName</td>
          <td align="left" valign="middle" bgcolor="#FFFFFF" style="font-size: 18px; color: #000000;"><?php echo $CallerName; ?></td>
        </tr>
        <tr>
          <td width="25%" align="right" valign="middle" bgcolor="#AAAAAA" style="font-size: 18px">StudentID</td>
          <td align="left" valign="middle" bgcolor="#FFFFFF" style="font-size: 18px; color: #000000;"><?php echo $StudentID; ?></td>
        </tr>
      </tbody>
</table>
    <p>&nbsp;</p>
    <table width="80%" border="4" align="center">
      <tbody>
        <tr>
          <td align="center" valign="middle" bgcolor="#9297FF" onClick="F_Launch('<?php echo trim($ContactID); ?>')"><?php echo trim($ContactName); ?><BR><?php echo trim($ContactID); ?></td>
        </tr>
      </tbody>
</table>
</body>
</html>
