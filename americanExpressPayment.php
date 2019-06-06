<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();
$username = "";
$password = "";
$merchetId='';
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
//  echo "merchantId" .$merchantId;echo '<pre>';
// echo "username" .$username;echo '<pre>';
// echo "password" .$password;echo '<pre>';

function randomNumber($length) {
    $result = '';

    for($i = 0; $i < $length; $i++) {
        $result .= mt_rand(0, 9);
    }

    return $result;
}
$_SESSION['amount'] =$_POST['amount'];
$_SESSION['currency'] =$_POST['currency'];
$_SESSION['expiry-month'] =$_POST['expiry-month'];
$_SESSION['expiry-year'] =$_POST['expiry-year'];
$_SESSION['securityCode'] =$_POST['security-code'];
$_SESSION['card-number'] =$_POST['card-number'];
$_SESSION['3dsecure'] = randomNumber(13);

$order=(object)['amount' => $_POST['amount'], "currency" => $_POST['currency']];
$expiry=(object)["month"=> $_POST['expiry-month'],"year" => $_POST['expiry-year'] ];
$card=(object)["number" =>$_POST['card-number'],"expiry" =>$expiry];
$provided=(object)["card"=>$card];
$sourceOfFunds=(object)['provided' => $provided];
$responseUrl=(object)["responseUrl" => "http://staging.php-dev.in:8844/MHADA/flyhi_payment/amex_res.php"];
$secure=(object)["authenticationRedirect"=>$responseUrl];

$abc=(object)["apiOperation" => "CHECK_3DS_ENROLLMENT","3DSecure" =>$secure, "sourceOfFunds" => $sourceOfFunds,"order" => $order];
// echo "<pre>"; print_r($abc); exit();

function callAPI($method, $url, $data, $username, $password){

// $username = "merchant.test8110291580";
// $password = "3e368a4c8e0b6735370161d24dd561dc";

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
'Authorization:Basic bWVyY2hhbnQudGVzdDgxMTAyOTE1ODA6M2UzNjhhNGM4ZTBiNjczNTM3MDE2MWQyNGRkNTYxZGM=',
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
print_r($err);
//exit;
if($err){die("Connection Failure");}
curl_close($curl);
return $result;
}


//echo 'https://gateway-japa.americanexpress.com/api/rest/version/48/merchant/'.$merchantId.'/3DSecureId/'.$_SESSION['3dsecure']; exit;
// Fisr request check 3d enrollment
$CHECK_3DS_ENROLLMENT = callAPI('PUT', 'https://gateway-japa.americanexpress.com/api/rest/version/48/merchant/'.$merchantId.'/3DSecureId/'.$_SESSION['3dsecure'], json_encode($abc), $username, $password);
$content=json_decode($CHECK_3DS_ENROLLMENT,true);
echo "<pre>";print_r($content);exit();
//echo "https://gateway-japa.americanexpress.com/api/rest/version/48/merchant/".$merchantId."/3DSecureId/".$_SESSION['3dsecure'];

//estract htmlBodyContent from responce 
$html = $content['3DSecure']['authenticationRedirect']['simple']['htmlBodyContent'];
$dom = new DOMDocument();
$dom->loadHTML($html);
$xpath = new DOMXPath($dom);

//$query = "//input[@id='t2']";
//search for rquired PaReq value using DOM
$queryPaReq = "//input[@type='hidden' and @name = 'PaReq']/@value"; 
$queryPa1Req = "//form[@name = 'echoForm']/@action"; 

$PaReq = '';
$url='';
$UrlAction = '';
$entriePaReq = $xpath->query($queryPaReq);
$entrieaction = $xpath->query($queryPa1Req);
foreach ($entriePaReq as $entriePaReq_value) {
$PaReq = $entriePaReq_value->textContent; //PaReq value;
}
foreach ($entrieaction as $entrieaction_value) {
$UrlAction = $entrieaction_value->textContent; //PaReq value;
}
echo '<pre>';print_r($UrlAction);exit;

//second request url

$_SESSION['UrlAction'] = $UrlAction;
//data have to pass in next request 
$data_for_B2B_Call=array("PaReq" => $PaReq, "selectAuthResult" => "AUTHENTICATED", "selectSplitAuthResult" => "selectSplitAuthResult", "customEci" => "", "customCavv" => "", "hiddenSecretValue" => "TNSTNS", "TermUrl" => "http://staging.php-dev.in:8844/MHADA/flyhi_payment/amex_res.php" , "MD" => "","UrlAction" => $UrlAction);
//echo '<pre>';print_r(json_encode($data_for_B2B_Call));exit;
//second request: B2B Call 
$response= array();
$B2B_Call = callAPI('POST', $UrlAction , http_build_query($data_for_B2B_Call),$username, $password);
//$errors = $response['response']['errors'];
//echo "<pre>";print_r($errors);exit();
//$data = $response['response']['data'][0];
print $B2B_Call;
?>