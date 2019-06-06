<?php 
$username = "";
$password = "";
$merchantId='';
$servername = "localhost";
$db_username = "root";
$db_password = "root";
$dbname = "flyhi_travels";
$conn = new mysqli($servername, $db_username, $db_password, $dbname);
if (!$conn->connect_error) {
	$sql = "SELECT * FROM myCredentials";
	$result = $conn->query($sql);
	if ($result->num_rows > 0) {
    	while($row = $result->fetch_assoc()) {
        $username=$row['username'];
        $password=$row['password'];
        $merchantId=$row['merchantId'];
		} 
	}
}
function callAPI($method, $url, $data, $username, $password){


$curl = curl_init();
 
switch ($method){
case "POST":
curl_setopt($curl, CURLOPT_POST, 1);
if ($data)
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
// echo "<pre>"; print_r($data);
// die;
break;
case "PUT":
curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
if ($data)
curl_setopt($curl, CURLOPT_POSTFIELDS, $data);  
break;
default:
if ($data)
$url = sprintf("%s?%s", $url, http_build_query($data));
}
//echo '<pre>';print_r($url);
// OPTIONS:
curl_setopt($curl, CURLOPT_URL, $url);
curl_setopt($curl, CURLOPT_HTTPHEADER, array(
'Authorization:Basic bWVyY2hhbnQuODExMDI5MTU4MDo1YjBjYzQyNWRjMTM1ZDVhYTgxMDE2YmQyNGU1MWE1Zg==',
'Content-Type: application/x-www-form-urlencoded',
));
curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
//curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
curl_setopt($curl, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
curl_setopt($curl, CURLOPT_USERPWD, "$username:$password");
// EXECUTE:
//echo '<pre>';print_r($url);
$result = curl_exec($curl);
$err = curl_error($curl); 
//print_r($result);
if($err){die("Connection Failure");}
curl_close($curl);
return $result;
}

session_start();
$order=(object)['amount' => $_SESSION['amount'], "currency" => $_SESSION['currency'], "reference" => $_SESSION['3dsecure']];
$expiry=(object)["month"=> $_SESSION['expiry-month'],"year" => $_SESSION['expiry-year'] ];
$card=(object)["number" =>$_SESSION['card-number'],"expiry" =>$expiry, "securityCode" => $_SESSION['securityCode'] ];
$provided=(object)["card"=> $card];
$sourceOfFunds=(object)["type" => 'CARD', "provided" => $provided];

$authorise_request =(object)["apiOperation" => "AUTHORIZE","3DSecureId" => $_SESSION['3dsecure'],"sourceOfFunds" => $sourceOfFunds,'order' => $order];

//echo '<pre>';print_r($authorise_request);exit;


$PROCESS_ACS_RESULT= callAPI('PUT', 'https://gateway-japa.americanexpress.com/api/rest/version/48/merchant/'.$merchantId.'/order/'.$_SESSION['3dsecure'] .'/transaction/'.$_SESSION['3dsecure'].'1'  , json_encode($authorise_request), $username, $password);
$content=json_decode($PROCESS_ACS_RESULT,true);

// Capture
$provide = new stdClass;
$sourceOfFunds=(object)["type" => 'CARD', "provided" => $provide];
$transaction=(object)["amount" => $_SESSION['amount'], "currency" => $_SESSION['currency']];

$capture_res =(object)["apiOperation" => "CAPTURE","3DSecureId" => $_SESSION['3dsecure'], "sourceOfFunds" => $sourceOfFunds ,"transaction" => $transaction];
//echo '<pre>';print_r(json_encode($capture_res));exit;
$process_capture = callAPI('PUT', 'https://gateway-japa.americanexpress.com/api/rest/version/48/merchant/'.$merchantId.'/order/'.$_SESSION['3dsecure'] .'/transaction/'.$_SESSION['3dsecure'].'2'  , json_encode($capture_res), $username, $password);
$expiry=(object)["month"=> $_SESSION['expiry-month'],"year" => $_SESSION['expiry-year'] ];
$card=(object)["number" =>$_SESSION['card-number'],"expiry" =>$expiry ];
$provided=(object)["card"=> $card];
$order=(object)['amount' => $_SESSION['amount'], "currency" => $_SESSION['currency'] ];
$sourceOfFunds=(object)["type" => 'CARD', "provided" => $provided];
$verify_res =(object)["apiOperation" => "VERIFY", "sourceOfFunds" => $sourceOfFunds, "order" => $order];
$process_verify = callAPI('PUT', 'https://gateway-japa.americanexpress.com/api/rest/version/48/merchant/'.$merchantId.'/order/'.$_SESSION['3dsecure'].'3' .'/transaction/'.$_SESSION['3dsecure'].'3'  , json_encode($verify_res),$username, $password);

$content =json_decode($process_verify,true);


echo '<pre>'; print_r($content);exit;
?>
