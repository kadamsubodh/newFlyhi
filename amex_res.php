<?php 
session_start();
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

$secureId=$_SESSION['3dsecure'];
$UrlAction = $_SESSION['UrlAction'];
$PaRes=$_POST['PaRes'];
$secure=(object)["paRes"=>$PaRes ];

$abc=(object)["apiOperation" => "PROCESS_ACS_RESULT","3DSecure" =>$secure];

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
$PROCESS_ACS_RESULT=callAPI('POST', 'https://gateway-japa.americanexpress.com/api/rest/version/48/merchant/'.$merchantId.'/3DSecureId/'.$secureId, json_encode($abc),$username, $password);
$content=json_decode($PROCESS_ACS_RESULT,true);
//echo "<pre>"; print_r($content);exit();
//estract htmlBodyContent from responce 
$html = $content['3DSecure']['authenticationRedirect']['simple']['htmlBodyContent'];
$dom = new DOMDocument();
$dom->loadHTML($html);
$xpath = new DOMXPath($dom);


//$query = "//input[@id='t2']";
//search for rquired PaReq value using DOM
$queryPaReq = "//input[@type='hidden' and @name = 'PaReq']/@value"; 
$PaReq = '';
$url='';
$entries = $xpath->query($queryPaReq);
foreach ($entries as $entry) {
$PaReq = $entry->textContent; //PaReq value;
}
//print_r($PaReq);exit;
$data_for_autho =array("PaReq" => $PaReq, "selectAuthResult" => "AUTHENTICATED", "selectSplitAuthResult" => "selectSplitAuthResult", "customEci" => "", "customCavv" => "", "hiddenSecretValue" => "TNSTNS", "TermUrl" => "http://staging.php-dev.in:8844/MHADA/flyhi_payment/amex_res_return.php" , "MD" => "");
$authorize = callAPI('POST', $UrlAction , http_build_query($data_for_autho),$username, $password);

print $authorize;

?>