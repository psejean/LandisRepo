<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get ScenarioId from the URL
$ScenarioId = $_GET['ScenarioId'];

// Define Salesforce credentials
$salesforceUsername = 'edev_salesforce@collegelacite.ca.devfull';
$salesforceClientId = '3MVG9gtjsZa8aaSW0LGVNeGQ_A9o7iTmvW_vb_pUP5oz5at2YX7O4QuHm.fuGLOoMMgjZEylOZSM6Z222x4fh';
$salesforcePrivateKey = getenv('PRIVATE_KEY'); // Fetch private key from environment variable
$salesforceLoginUrl = 'https://test.salesforce.com';

// Include Salesforce JWT token generation library
require_once('jwt/JWT.php');
use \Firebase\JWT\JWT;

// Generate JWT token
$issuedAt = time();
$expirationTime = $issuedAt + 3600;  // JWT token expiration time (1 hour)
$payload = array(
    'iss' => $salesforceClientId,
    'sub' => $salesforceUsername,
    'aud' => $salesforceLoginUrl,
    'exp' => $expirationTime,
);
$jwt = JWT::encode($payload, $salesforcePrivateKey, 'RS256'); // Use the fetched private key

// Salesforce API endpoint for custom object query
$salesforceQueryUrl = 'https://your_salesforce_instance.salesforce.com/services/data/v52.0/query/?q=';
$query = "SELECT CallerNumber__c, CallerName__c, StudentID__c, Contact__c, ContactName__c FROM ContactCallLog__c WHERE ScenarioId__c='$ScenarioId'";

// Set up cURL session for Salesforce API call
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $salesforceQueryUrl . urlencode($query));
curl_setopt($ch, CURLOPT_HTTPHEADER, array("Authorization: Bearer $jwt"));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute cURL request to Salesforce API
$response = curl_exec($ch);

// Check for errors
if(curl_error($ch)) {
    echo 'Error: ' . curl_error($ch);
}

// Close cURL session
curl_close($ch);

// Decode JSON response from Salesforce API
$result = json_decode($response, true);

// Extract data from Salesforce response
$CallerNumber = $result['records'][0]['CallerNumber__c'];
$CallerName = $result['records'][0]['CallerName__c'];
$StudentID = $result['records'][0]['StudentID__c'];
$ContactID = $result['records'][0]['Contact__c'];
$ContactName = $result['records'][0]['ContactName__c'];

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
          <td align="center" valign="middle" bgcolor="#9297FF" onClick="F_Launch('<?php echo $ContactID; ?>')"><?php echo $ContactName; ?><BR><?php echo $ContactID; ?></td>
        </tr>
      </tbody>
</table>
</body>
</html>
