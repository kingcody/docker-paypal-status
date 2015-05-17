<?php
/**
* Configurations
*/
$environment = $_ENV['PAYPAL_API_ENVIRONMENT'] ?: 'live';   // 'sandbox', 'beta-sandbox', or 'live'

$config = array(
    'username'  => $_ENV['PAYPAL_USERNAME'] ?: 'USERNAME',
    'password'  => $_ENV['PAYPAL_PASSWORD'] ?: 'PASSWORD',
    'signature' => $_ENV['PAYPAL_SIGNATURE'] ?: 'SIGNATURE',
    'version'   => $_ENV['PAYPAL_VERSION'] ?: '112.0'
);

$action = 'getBalance';

/**
* Get Paypal balance
*/
switch ($environment) {
    case 'sandbox':
$url = "https://api-3t.$environment.paypal.com/nvp";
break;
    case 'beta-sandbox':
        $url = "https://api-3t.$environment.paypal.com/nvp";
        break;
    default:
        $url = "https://api-3t.paypal.com/nvp";
}

foreach ($config as &$value) {
    $value = urlencode($value);
}

$request = http_build_query(array(
    "METHOD"    => $action,
    "VERSION"   => $config['version'],
    "USER"      => $config['username'],
    "PWD"       => $config['password'],
    "SIGNATURE" => $config['signature'],
    "RETURNALLCURRENCIES" => 1,
));

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_VERBOSE, 1);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, $request);

$response = curl_exec($ch);

var_dump($response);

if (!$response) {
    echo 'Failed to retrieve paypal balance: ' . curl_error($ch) . ' (' . curl_errno($ch) . ')';
    exit;
}

parse_str($response, $result);

foreach ($result as &$value) {
    $value = urldecode($value);
}

if (!isset($result['ACK']) || $result['ACK'] != "Success") {
    echo "{$result['L_SEVERITYCODE0']} {$result['L_ERRORCODE0']}: {$result['L_SHORTMESSAGE0']}\n{$result['L_LONGMESSAGE0']}\n";
    exit;
}

$balance = "{$result['L_CURRENCYCODE0']} {$result['L_AMT0']}";
//echo $balance;

$balance = ltrim($balance, "USD ");

$newbalance = $balance;
echo "current goal is: " . $newbalance;
var_dump($result);

?>
