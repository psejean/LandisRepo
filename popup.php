<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Get ScenarioId from the URL
$ScenarioId = $_GET['ScenarioId'];

// Define the URL of your Power Automate flow
$flowUrl = 'https://prod-74.westus.logic.azure.com:443/workflows/51955eb8bdf84eec89268f4d1b0e9b1f/triggers/manual/paths/invoke?api-version=2016-06-01&ScenarioId='.$ScenarioId;

// Define default values for other parameters (if needed)
$CallerNumber = $_GET['CallerNumber'] ?? '';
$CallerName = $_GET['CallerName'] ?? '';
$StudentID = $_GET['StudentID'] ?? '';
$ContactID = $_GET['ContactID'] ?? '';
$ContactName = $_GET['ContactName'] ?? '';

// Define the data to be sent to the flow
$data = array(
    'ScenarioId' => $ScenarioId,
    'CallerNumber' => $CallerNumber,
    'CallerName' => $CallerName,
    'StudentID' => $StudentID,
    'ContactID' => $ContactID,
    'ContactName' => $ContactName
);

// Create JSON payload for the request
$payload = json_encode($data);

// Set up the cURL session
$ch = curl_init($flowUrl);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

// Execute the cURL request
$response = curl_exec($ch);

// Check for errors
if(curl_error($ch)) {
    echo 'Error: ' . curl_error($ch);
}

// Close cURL session
curl_close($ch);

// Output response from Power Automate (if needed)
echo $response;
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
        <?php for($i = 0; $i < count($ContactID); $i++): ?>
        <tr>
          <td align="center" valign="middle" bgcolor="#9297FF" onClick="F_Launch('<?php echo trim($ContactID[$i]); ?>')"><?php echo trim($ContactName[$i]); ?><BR><?php echo trim($ContactID[$i]); ?></td>
        </tr>
        <?php endfor; ?>
      </tbody>
</table>
</body>
</html>
