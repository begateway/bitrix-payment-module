<?

$classes = array(
				"\beGateway\ApiAbstract" => "lib/beGateway/lib/beGateway/ApiAbstract.php",
				"\beGateway\Authorization" => "lib/beGateway/lib/beGatewayAuthorization.php",
				"\beGateway\Capture" => "lib/beGateway/lib/beGatewayCapture.php",
				"\beGateway\Card" => "lib/beGateway/lib/beGatewayCard.php",
				"\beGateway\CardToken" => "lib/beGateway/lib/beGatewayCardToken.php",
				"\beGateway\ChildTransaction" => "lib/beGateway/lib/beGatewayChildTransaction.php",
				"\beGateway\Credit" => "lib/beGateway/lib/beGatewayCredit.php",
				"\beGateway\Customer" => "lib/beGateway/lib/beGatewayCustomer.php",
				"\beGateway\GatewayTransport" => "lib/beGateway/lib/beGatewayGatewayTransport.php",
				"\beGateway\GetPaymentToken" => "lib/beGateway/lib/beGatewayGetPaymentToken.php",
				"\beGateway\Language" => "lib/beGateway/lib/beGatewayLanguage.php",
				"\beGateway\Logger" => "lib/beGateway/lib/beGatewayLogger.php",
				"\beGateway\Money" => "lib/beGateway/lib/beGatewayMoney.php",
				"\beGateway\Payment" => "lib/beGateway/lib/beGatewayPayment.php",
				"\beGateway\QueryByToken" => "lib/beGateway/lib/beGatewayQueryByToken.php",
				"\beGateway\QueryByTrackingId" => "lib/beGateway/lib/beGatewayQueryByTrackingId.php",
				"\beGateway\QueryByUid" => "lib/beGateway/lib/beGatewayQueryByUid.php",
				"\beGateway\Refund" => "lib/beGateway/lib/beGatewayRefund.php",
				"\beGateway\Response" => "lib/beGateway/lib/beGatewayResponse.php",
				"\beGateway\ResponseBase" => "lib/beGateway/lib/beGatewayResponseBase.php",
				"\beGateway\ResponseCardToken" => "lib/beGateway/lib/beGatewayResponseCardToken.php",
				"\beGateway\ResponseCheckout" => "lib/beGateway/lib/beGatewayResponseCheckout.php",
				"\beGateway\Settings" => "lib/beGateway/lib/beGatewaySettings.php",
				"\beGateway\Void" => "lib/beGateway/lib/beGatewayVoid.php",
				"\beGateway\Webhook" => "lib/beGateway/lib/beGatewayWebhook.php",
		   );

CModule::AddAutoloadClasses("devtm.begateway", $classes);
