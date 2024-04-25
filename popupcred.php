<?php

require_once 'vendor/autoload.php';

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
$salesforceUsername = 'edev_salesforce@collegelacite.ca.devfull'; 
$salesforcePassword = 'a;kA5-8UdB';
$salesforceSecurityToken = 'oxqN4wFlX8qEk3gw4Zx29a7aG'; // Replace with your Salesforce security token

// Log Salesforce credentials
logMessage("Salesforce credentials: Username - $salesforceUsername, ClientId - $salesforceClientId");

// Perform OAuth 2.0 authentication with Salesforce
$authUrl = "$salesforceLoginUrl/services/oauth2/token";
$authParams = array(
    'grant_type' => 'password',
    'client_id' => $salesforceClientId,
    'client_secret' => $salesforceClientSecret,
    'username' => $salesforceUsername,
    'password' => $salesforcePassword . $salesforceSecurityToken
);

// Initialize cURL session
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $authUrl);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($authParams));

// Execute cURL request to authenticate with Salesforce
$response = curl_exec($ch);

// Check for errors
if (curl_error($ch)) {
    // Log cURL error
    logMessage("cURL error: " . curl_error($ch));
}

// Log the authentication response
logMessage("Authentication response: $response");

// Decode JSON response
$authResponse = json_decode($response, true);

// Extract access token
$accessToken = isset($authResponse['access_token']) ? $authResponse['access_token'] : '';

// Log access token
logMessage("Access token: $accessToken");

// Close cURL session
curl_close($ch);

// Salesforce API endpoint for custom object query
$salesforceQueryUrl = 'https://collegelacite--devfull.sandbox.lightning.force.com/services/data/v59.0/query/?q=';

// Query to retrieve data from Salesforce
$query = "SELECT CallerNumber__c, CallerName__c, StudentID__c, Contact__c, ContactName__c, Name FROM ContactCallLog__c WHERE Name='$ScenarioId'";

// Log the Salesforce query
logMessage("Salesforce query: $query");

// Set up cURL session for Salesforce API call with access token
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $salesforceQueryUrl . urlencode($query));
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer $accessToken"));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute cURL request to Salesforce API
$response = curl_exec($ch);

// Check for errors
if (curl_error($ch)) {
    // Log cURL error
    logMessage("cURL error: " . curl_error($ch));
}

// Log the cURL response
logMessage("cURL response: $response");

// Echo the response for debugging
echo "cURL Response: $response\n";

// Close cURL session
curl_close($ch);

// Check if response is empty
if (empty($response)) {
    echo "No response received from Salesforce.\n";
}

// Decode JSON response from Salesforce API
$result = json_decode($response, true);

// Log Salesforce response
logMessage("Salesforce response: " . json_encode($result));

// Echo the response for debugging
echo "Salesforce Response: ";
print_r($result);

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
          <td align="left" valign="middle" bgcolor="#FFFFFF" style="font-size: 18px; color: #000000;"><?PHP echo $ScenarioId; ?></td>
        </tr>
        <tr>
          <td width="25%" align="right" valign="middle" bgcolor="#AAAAAA" style="font-size: 18px">CallerNumber</td>
          <td align="left" valign="middle" bgcolor="#FFFFFF" style="font-size: 18px; color: #000000;"><?PHP echo $CallerNumber; ?></td>
        </tr>
        <tr>
          <td width="25%" align="right" valign="middle" bgcolor="#AAAAAA" style="font-size: 18px">CallerName</td>
          <td align="left" valign="middle" bgcolor="#FFFFFF" style="font-size: 18px; color: #000000;"><?PHP echo $CallerName; ?></td>
        </tr>
        <tr>
          <td width="25%" align="right" valign="middle" bgcolor="#AAAAAA" style="font-size: 18px">StudentID</td>
          <td align="left" valign="middle" bgcolor="#FFFFFF" style="font-size: 18px; color: #000000;"><?PHP echo $StudentID; ?></td>
        </tr>
      </tbody>
</table>
    <p>&nbsp;</p>
    <table width="80%" border="4" align="center">
      <tbody>
          <?PHP for($i=0;$i<count($ContactID);$i++): ?>
        <tr>
          <td align="center" valign="middle" bgcolor="#9297FF" onClick="F_Launch('<?PHP echo trim($ContactID[$i]); ?>')"><?PHP echo trim($ContactName[$i]); ?><BR><?PHP echo trim($ContactID[$i]); ?></td>
        </tr>
          <?PHP endfor; ?>
      </tbody>
</table>
</body>
</html>
