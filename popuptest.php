<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Function to log messages to a file
function logMessage($message) {
    $logFile = 'salesforce_logs.txt';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

// Get ScenarioId from the URL
$ScenarioId = $_GET['ScenarioId'];

// Log the start of the script
logMessage("Script execution started.");

// Define Salesforce credentials
$salesforceUsername = 'psejea@collegelacite.ca.devfull';
$salesforceClientId = '3MVG9gtjsZa8aaSW0LGVNeGQ_A9o7iTmvW_vb_pUP5oz5at2YX7O4QuHm.fuGLOoMMgjZEylOZSM6Z222x4fh';
$salesforceLoginUrl = 'https://test.salesforce.com';
$salesforcePrivateKey = '-----BEGIN PRIVATE KEY-----
MIIEvgIBADANBgkqhkiG9w0BAQEFAASCBKgwggSkAgEAAoIBAQCpboxNKqrErfxC
r+MaES8JEcLYQdKJ5MThW7R5IAwqU5w6dQ27qh1Fi9CmYMKuhYRLGqlkrEk1oXFH
0N8b7iByGlmsFMkAiianCpd+c+FOlaAfrWZAYt8tIbqaKh+Lju3eQ75Oq9sLGOk0
eS2AyCYoOVoPp6/cjge5//drveX2+Nk4iUwBr7SmX9jcc/2Dwu1RtdDQtSvo2sv0
4QgneHAQ9HVcDosBbUWPBypZbnAGDQUcASVjB3gwXRJfO8N3JsIVK2Zhg99EZXDq
BWJrbhVIfgYN8Os4p+5ZgwNUuAIZH+PCPSe51VZcIdWcR3aM0c0vyt3Bh1hBuE/8
ZdzREgUBAgMBAAECggEBAIzqh8Aqa2s3NWaVePGWNyN45TAN1rifT2wLZJeVIukV
LwujjS929e+AsKGgOmsCWxxH6Xj0ndMAGgJb4yQMsmmUJt6rTt2nCSzG72bZpBtC
8LFH+5IzaWDU+6j6vc/JqWbBuwcdggnBxzvASSshzDKKOLBqjCaI7j4xeKvgfeIg
jQPJiw0hZoE6TWxxX5PE9ZPWX0o7ZN/RVzvk/2rixkpdJOWM64tPOuzh/ha4VgFh
6lbgbnjO2be3Rj76sUURj6XbJZ7fYEcTFTKIB/YzTYFDCih8kShoP0cKViwSi/gy
dILJeTgLXZi+OKviBdTccl/zcPWy+V9mkdjwttlMD50CgYEAwwqA7CYwgSU9w6lO
M1dEv8Pf37IyeIj/EvHYdYOzPdSnuV4QZ0MKCKLnG5U2XgFIlKseNJu6c1vpORp6
D1THrevHx457seTzCPWpBrHxgqrgDyRCK5mTLoxSbz5eArhmoQa7TyjSPFLQ2okN
uAiKFv0LSmGqUR3K+rFEHFiCM5sCgYEA3mMEg7oq2LJROrfA7eCEdxJS1tHge5dm
WT8El+G5C5FCj2iJdxM/Z/sNMjMGY/xcWBb/nKauX4YtpLipJf8C21XDla9Av2Ov
r8XSFjaPzCbRS/6ou9mh2OiPFVZImds6nMGwJTybIQsUE9I8HgrVPnXKAxVf+rmw
cwyNFdKw2ZMCgYAYtQXr5FKUqZEPbi0X1+A/oqKDheFa34/gaH6RNGPKW1v74WyW
iCmHOouoNNi0Q9lb6+lhpLCT2HrM3wvDUWwSHiIqp2QH/wbChcwpqvT7JoZHpMI1
H7lDVkdDDFWAZrepgl7MAlHPjnYimOYCACLuEpQRkhmvOOTzqO0F4jhsLQKBgAUl
y5v0+jrr3b97M2cONGLBNNOuJgEWXxMfx05wtiTTZvQE2nG8K1KP2B1aWwKDe+u6
FI6euRiS9YmDkL7FaV6EXLOhS+FiQFXUQWmsN6XlHCEjMuquPfXUZEN9LM8K6Q9p
2Fb0US7xn7RZwHR9kbQRa+yoWQFnvPLczoM7zkYrAoGBAL6zUHkikVZkRFImBmqU
6W7PbbmzIbSsYmZbZY2m0Kc1M2W0Sjtin+hW4S59csU5OsbdJEx7Z42rt7hHSt27
ocOV7MrYEyuS+ZhfSNsDIsc9HApx6osZ485Z5zoINuLgHQq7NezYSWWb8KORCzxH
mSt76OwLPAzZOxcBxxfY6r5q
-----END PRIVATE KEY-----'

// Log Salesforce credentials
logMessage("Salesforce credentials: Username - $salesforceUsername, ClientId - $salesforceClientId");

// Generate JWT token
$issuedAt = time();
$expirationTime = $issuedAt + 3600; // JWT token expiration time (1 hour)

$payload = array(
    'iss' => $salesforceClientId,
    'sub' => $salesforceUsername,
    'aud' => $salesforceLoginUrl,
    'exp' => $expirationTime,
);

// Create JWT token
$header = json_encode(['alg' => 'RS256', 'typ' => 'JWT']);
$encodedHeader = base64_encode($header);
$encodedPayload = base64_encode(json_encode($payload));
$data = $encodedHeader . '.' . $encodedPayload;
$signature = '';
openssl_sign($data, $signature, $salesforcePrivateKey, OPENSSL_ALGO_SHA256);
$encodedSignature = base64_encode($signature);
$jwt = $data . '.' . $encodedSignature;

// Log JWT token
logMessage("JWT token generated: $jwt");

// Salesforce API endpoint for custom object query
$salesforceQueryUrl = 'https://collegelacite--devfull.sandbox.lightning.force.com/services/data/v59.0/query/?q=';

$query = "SELECT CallerNumber__c, CallerName__c, StudentID__c, Contact__c, ContactName__c, Name FROM ContactCallLog__c WHERE Name='$ScenarioId'";

// Log the Salesforce query
logMessage("Salesforce query: $query");

// Set up cURL session for Salesforce API call
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $salesforceQueryUrl . urlencode($query));
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer $jwt"));
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
if(empty($response)) {
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
