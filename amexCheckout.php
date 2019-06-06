<?php
session_start();
$paymentType=$_SESSION['paymentType'];
$orderID=$_SESSION['orderID'];
$sessionID=$_SESSION['sessionID'];
$sessionVersion=$_SESSION['sessionVersion'];
$successIndicator=$_SESSION['successIndicator'];
$merchantId = $_SESSION['merchantId'];

$FirstName =  $_SESSION["FirstName"];
$LastName = $_SESSION['LastName'];
$email = $_SESSION['email'];
$phone = $_SESSION['phone'];
$currency = $_SESSION['currency'];
$amount = $_SESSION['amount'];
$description = $_SESSION['description'];
?>
<script src="https://gateway-japa.americanexpress.com/checkout/version/50/checkout.js"
	data-error="errorCallback"
	data-cancel="cancelCallback" data-complete="completeCallback">
</script>

<script type="text/javascript">
	function afterRedirect(data) {
		window.location.href = "https://in.linkedin.com/";

	}

	function errorCallback(error) {
		console.log('Payment error');
		console.log(JSON.stringify(error));
	}
	function cancelCallback() {
		console.log('Payment cancelled');
// Reload the page to generate a new session ID - the old one is out of date as soon as the lightbox is invoked
window.location.href = "https://www.facebook.com/";
}

// This handles the response from Hosted Checkout and redirects to the appropriate endpoint
function completeCallback(response) {
resultIndicator = response;
var result = (resultIndicator === "<?php echo $successIndicator; ?>") ? "SUCCESS" : "ERROR";
// var counter = 0;
// var myInterval = setInterval(function () {
//   ++counter;
// }, 4000);
// if(counter == 5){
// 	clearInterval(myInterval);
	window.location.href = "amexGetOrderRequest.php";

	// }


}
var amount = <?php echo $amount;?>;

Checkout.configure({
	merchant: 'test8110291580',
	order: {
		amount: function () {
			return amount + 0 ;
		},
		currency: "<?php echo $currency;?>",
		description: "<?php echo $description; ?>",
		id: "<?php echo $orderID; ?>"
	},
	session: {
		id: "<?php echo $sessionID; ?>",
		version: "<?php echo $sessionVersion; ?>"
	},
	interaction: {
		merchant: {
			name: "Flyma",
			address: {
				line1: 'Mos Espa',
				line2: 'Tatooine'
			}
		}}
	});

var paymentType = "<?php echo $paymentType; ?>";
if (paymentType == "lightbox") {
	var result = Checkout.showLightbox();
console.log(result);
}
else {
	var result = Checkout.showPaymentPage();
	console.log(result);
}
$(document).ready(function(){
	
})

</script>