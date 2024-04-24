<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$ScenarioId = $_GET['ScenarioId'];
$CallerNumber = $_GET['CallerNumber'];
$CallerName = $_GET['CallerName'];
$StudentID = $_GET['StudentID'];
$ContactID = $_GET['ContactID'];
$ContactName = $_GET['ContactName'];

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
