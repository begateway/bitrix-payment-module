<?

$classes = array(
				"\beGateway\ApiAbstract" => "lib/ApiAbstract.php",
				"\beGateway\Authorization" => "lib/Authorization.php",
				"\beGateway\Capture" => "lib/Capture.php",
				"\beGateway\Card" => "lib/Card.php",
				"\beGateway\CardToken" => "lib/CardToken.php",
				"\beGateway\ChildTransaction" => "lib/ChildTransaction.php",
				"\beGateway\Credit" => "lib/Credit.php",
				"\beGateway\Customer" => "lib/Customer.php",
				"\beGateway\GatewayTransport" => "lib/GatewayTransport.php",
				"\beGateway\GetPaymentToken" => "lib/GetPaymentToken.php",
				"\beGateway\Language" => "lib/Language.php",
				"\beGateway\Logger" => "lib/Logger.php",
				"\beGateway\Money" => "lib/Money.php",
				"\beGateway\Payment" => "lib/Payment.php",
				"\beGateway\QueryByToken" => "lib/QueryByToken.php",
				"\beGateway\QueryByTrackingId" => "lib/QueryByTrackingId.php",
				"\beGateway\QueryByUid" => "lib/QueryByUid.php",
				"\beGateway\Refund" => "lib/Refund.php",
				"\beGateway\Response" => "lib/Response.php",
				"\beGateway\ResponseBase" => "lib/ResponseBase.php",
				"\beGateway\ResponseCardToken" => "lib/ResponseCardToken.php",
				"\beGateway\ResponseCheckout" => "lib/ResponseCheckout.php",
				"\beGateway\Settings" => "lib/Settings.php",
				"\beGateway\Void" => "lib/Void.php",
				"\beGateway\Webhook" => "lib/Webhook.php",
		   );

CModule::AddAutoloadClasses("devtm.begateway", $classes);
