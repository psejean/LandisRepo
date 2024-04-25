<?php

require_once 'vendor/autoload.php';

use Azure\Core\Credentials\AzureKeyCredential;
use Azure\Identity\DefaultAzureCredentialBuilder;
use Azure\Security\KeyVault\Certificates\CertificateClient;
use Azure\Security\KeyVault\Certificates\CertificateContentType;

// Function to log messages to a file
function logMessage($message) {
    $logFile = 'salesforce_logs.txt';
    $timestamp = date('Y-m-d H:i:s');
    $logMessage = "[$timestamp] $message\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
}

// Azure Key Vault details
$keyVaultUrl = 'https://landiskey.vault.azure.net/';
$certificateName = 'LandisCert'; // Name of your certificate in Key Vault

// Create Azure Key Vault Certificate client
$credential = new DefaultAzureCredentialBuilder();
$client = new CertificateClient($keyVaultUrl, new AzureKeyCredential($credential->build()));

// Retrieve the certificate from Azure Key Vault
$certificate = $client->getCertificate($certificateName);
$certificateContents = $client->getCertificatePolicy($certificateName)->getContentType() === CertificateContentType::PFX ?
    $client->getCertificatePolicy($certificateName)->getBase64EncodedValue() : $client->getCertificatePolicy($certificateName)->getX509Certificate();

// Extract private key from certificate
$certificatePem = "-----BEGIN CERTIFICATE-----\n" . chunk_split($certificateContents, 64, "\n") . "-----END CERTIFICATE-----\n";
openssl_x509_export($certificatePem, $certificatePem);

// Log fetched certificate
logMessage("Fetched certificate from Azure Key Vault");

// Rest of the script...

// Define Salesforce credentials
$salesforceUsername = 'psejea@collegelacite.ca.devfull';
$salesforceClientId = '3MVG9gtjsZa8aaSV4ayM_wa_OC1dZPK9KPGWJVuUrjEuVN4Ynn1IAvavLU9INXqkq_Wi2D3q4H.OuXHGC.IE2';
$salesforceLoginUrl = 'https://test.salesforce.com';
$salesforcePrivateKey = $certificatePem; // Use the extracted private key

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
