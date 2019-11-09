<?php
/**
 * payment-complete.php
 *
 * Payment complete page. As per our own request, this page is 
 * redirected to by PayPal when a payment is authorized (still need to capture the funds though). 
 */
 
// Instantiate our payment adapter
$adapter = new PayPal_Adapter();
 
// PayPal will call this page, and send back the 'confirm_payment' variable.
$paymentConfirmed = !empty($_REQUEST['confirm_payment']);
 
// ...Are we good to go?
if ($paymentConfirmed == true) {
	// Yes. We now have a token, and the payer ID needed
	// to call the ECDoExpressCheckout API call.
 
	$token = $_REQUEST['token'];
	$payer_id = $_REQUEST['PayerID'];
	$amount = $_SESSION['CHECKOUT_AMOUNT'];
	$currency_code = 'USD'; // Assuming we are still using the US Dollar.
 
	// Capture the funds!
	$reply = $adapter->ecDoExpressCheckout($token, $payer_id, $amount, $currency_code);
 
	// Did we get a valid reply?
	if ($reply->isSuccessfull()) {
		// Yes! We would usually save our order data at this point,
		// but, we can just output to the screen for now. :-)
		// The funds may or may not have been captured.
		// Check the $replyData->ACK property to know for sure.
 
		$replyData = $adapter->parse($reply->getBody());
 
		print_r($replyData);
 
		// Clean up.
		unset($_SESSION['CHECKOUT_AMOUNT']);
	} else {
		// No. Throw an exception.
		throw new Exception('We were unable to complete the ECDoExpressCheckout API call.');
	}
} else {
	// No. Throw an exception.
	throw new Exception('It appears we did not receive a confirmed payment flag.');
}
 
?>