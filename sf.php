<?php

$sf_config = array(
	client_id     => '3MVG9gtjsZa8aaSV4ayM_wa_OC02dG7go88eZDfok180duLsZbrIORMt5m8G6raFO_6x4sq7HkanmoiyX0Nap', // from salesforce OAuth configuration (add new app ...)
	client_secret => '05C0BB5E93F814FABB7B24D25F3EE72100BECA806D6209A3BB71E19658CBC413',
	username      => 'integrationuser@lacitec.on.ca.devfull', // username login to salesforce
	password      => 'a;kA5-8UdB', // no need to concat password token
	url           => 'https://test.salesforce.com/services/oauth2/token',
	grant_type    => 'password',
	query_srv_url => '/services/data/v58.0/query/'
);

$soql_query = 'SELECT id, name from Account';

// ---------
// first act - get token from salesforce

$curl_post_param  = 'grant_type='. $sf_config[grant_type];
$curl_post_param .= '&client_id='. $sf_config[client_id];
$curl_post_param .= '&client_secret='. $sf_config[client_secret];
$curl_post_param .= '&username='. $sf_config[username];
$curl_post_param .= '&password='. $sf_config[password];

$curl_getToken = curl_init();

curl_setopt_array($curl_getToken, array(
	CURLOPT_URL            => $sf_config[url],
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING       => "",
	CURLOPT_MAXREDIRS      => 10,
	CURLOPT_TIMEOUT        => 30,
	CURLOPT_SSL_VERIFYPEER => false,
	CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST  => "POST",
	CURLOPT_POSTFIELDS     => $curl_post_param,
	CURLOPT_HTTPHEADER     => array("content-type: application/x-www-form-urlencoded"),
));

$response = curl_exec($curl_getToken);
$err      = curl_error($curl_getToken);

curl_close($curl_getToken);

if ($err)
{
	echo "cURL Error #:" . $err;
	die();
};

$sf_token = json_decode($response, true);

echo "Salesforce token: \n";
var_dump($sf_token);

// ----------
// Second act - send a soql query to salesforce

$curl_url = $sf_token[instance_url] . $sf_config[query_srv_url] . '?q=' .urlencode($soql_query);

$curl_getSoql = curl_init();
curl_setopt_array($curl_getSoql, array(
	CURLOPT_URL            => $curl_url,
	CURLOPT_RETURNTRANSFER => true,
	CURLOPT_ENCODING       => "",
	CURLOPT_MAXREDIRS      => 10,
	CURLOPT_TIMEOUT        => 30,
	CURLOPT_SSL_VERIFYPEER => false,
	CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
	CURLOPT_CUSTOMREQUEST  => "GET",
	CURLOPT_HTTPHEADER     => array('Authorization: '. $sf_token[token_type] .' '. $sf_token[access_token]),
));

$response = curl_exec($curl_getSoql);
$err      = curl_error($curl_getSoql);

curl_close($curl_getSoql);

if ($err)
{
	echo "cURL Error #:" . $err;
	die();
};

$sf_soql_reply = json_decode($response, true);
echo "Salesforce SOQL reply: \n";
var_dump($sf_soql_reply);
