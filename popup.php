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

// Rest of your script remains unchanged...
// You'll need to modify it to extract the private key from $certificatePem

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
