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
MIIEvQIBADANBgkqhkiG9w0BAQEFAASCBKcwggSjAgEAAoIBAQCjboZHobdj2mCZ
YBzFCym/m+U1f3scOn10vSUeQ9drrIkFYegvo8a8w/OzUVc6HmDfjDFWlAccS4Ys
gRDyc0JEX3fycdIm13yzK13iA0iD8eHO0WO1B370t2JrorcGNztVj5xc/vg6KWoD
sJ6sSyctoN2BLFdsZmFkZ4dS2DB5KuBtA7EdwX4YWSro9jPgVbsm63JrS1hh35M9
HtBOeTi/xu8/Qc9HqFervJbbEItgdKG9sv5X682ct04ozow7bfsTUxbq7PUiywvo
DCSXgF8idn/Ia0m47o8OFXFphxaRj6e2ZMLtdDlkLmRcDwz6IbHz7n0k8ogj2EhX
9YkPAhW7AgMBAAECggEATmRH9ZkM2dEYT8lp01wg6N8yyP/+gdJOdg35/XPmIygt
y06krCE/RTEqtoMYN3D135Sj39b+OCTD1ZoNEnaVvS0b4EeXWXk4P8rX6/7OLDf8
K5OFY4KWc3R5OY7hgpJAnDBX/MVyNJfD2cRAv/0GbF0CkqC9yGmtYpdEMkkvqxZQ
zQuUwSomQpPW1nEKfUD6HjbmjdnUJwZT2uYd63swYtCepZCA2FNzKGr9um+oZZAn
yOvnhdJT87IJ0XQEyDmtOmKdTKR0cwa52y1b+ZyEtxTm8xYaZWmH+pSqrakVXocA
ilKLROuPZZLD2BtrIKBIB+WgtqapJZm7OIbrZmla1QKBgQDaZcPCtrdfnbjqXj2m
FVHJ++/V2Gi8MkfWwm8jgk3mDtGAy8rZTn/juLb/q4XcHx+bnWIp1s0ndaXB/DRU
xPHZdHN2+XBSRDQt6VwJzL7I7V1BXq5QeObLIjwq8Yx8mjAG1BEzpUY+f+XKRk0R
6v6G2pKtzYG/YssEzycsFYByTwKBgQC/kg0cxT8ETa4phHi9oGEp3+06TFE4qMyN
jLAh7EQ+Yo9GDIDQveohL7YUKbhOheSmm88IZ/krcI+ivpQuJxOPGXDiw5ZN5QaE
h4g+iOcNlaHxaSfNY2QEf+UryZFvGOCrEr93bOaYHspX4MoLiqH0qUvolarTYSQP
MnMZCJDm1QKBgHS2M5KH8KEheaON72YlOIs3nujp+LayLCAB0kDortaGDsHEpsfQ
opnAqdMiB6wl2c9goQf46bPvtEBhllnC0fhCuj3XeYYNOtFaRzxZdY+NAewgPAl3
QudFiV/trAUepRHRHMw7w7k8wkGBpkgwDAtnHUHSdEch9ZrBVY7Cgt0TAoGAbXJB
r4g5Qnom4G1gleXE3Smj3MSxOo+lndEc56SWMJYaiMin55o44xDhE4/qTmJMiatG
kuTkBB9g0HfVLLECiaTdS5C2lHYeTSUpf6CzcJ1mUgfjx4HbKH7xLR0Ry8kIwnQJ
k29SJuKgc6hnhkSD1sXKKm0nlXBQK6aE/25XaqkCgYEAlk4LR3gG1yCVRO37bcB9
R3LD6v81dsLUkGAimQIzOIejfWLvnixUicY9OKJztuoqjgyGnKTFob5ag5fF7OaH
9CsqPNji6trD/7Xk9vMdEJbohrtgq3+NqNeh+VjDJffk6jDiWwL2+p7eRN4hVLig
LtUk7GtR6zZ4iYknt0KxBAA=
-----END PRIVATE KEY-----';

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
