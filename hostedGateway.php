<html>
<head>
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
console.log(response);
// Save the resultIndicator
resultIndicator = response;
var result = (resultIndicator === '58e2e2438e024dba') ? "SUCCESS" : "ERROR";
window.location.href = "https://gateway-japa.americanexpress.com/api/rest/version/50/merchant/test8110291580/order/dfdgsdf45435gfgf4590";
console.log("hi");
}

Checkout.configure({
	merchant: 'test8110291580',
	order: {
		amount: function () {
			return 1 + 0;
		},
		currency: 'INR',
		description: 'R2D2 Droid',
		id: 'dfdgsdf45435gfgf4590'
	},
	session: {
		id: 'SESSION0002963378512I7876377N07',
		version: '85e7a27c01'
	},
	interaction: {
		merchant: {
			name: 'Flyma',
			address: {
				line1: 'Mos Espa',
				line2: 'Tatooine'
			}
		}}
	});




</script>
</head>
<body>
	<input type="button" value="Pay with Lightbox" onclick="Checkout.showLightbox();" />
	<input type="button" value="Pay with Payment Page" onclick="Checkout.showPaymentPage();" />
</body>
</html>
