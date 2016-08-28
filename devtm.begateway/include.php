<?
$lib_path = '/bitrix/modules/devtm.begateway/lib/beGateway/lib/beGateway';

$classes = array(
  "\beGateway\ApiAbstract" => "$lib_path/ApiAbstract.php",
  "\beGateway\Authorization" => "$lib_path/Authorization.php",
  "\beGateway\Capture" => "$lib_path/Capture.php",
  "\beGateway\Card" => "$lib_path/Card.php",
  "\beGateway\CardToken" => "$lib_path/CardToken.php",
  "\beGateway\ChildTransaction" => "$lib_path/ChildTransaction.php",
  "\beGateway\Credit" => "$lib_path/Credit.php",
  "\beGateway\Customer" => "$lib_path/Customer.php",
  "\beGateway\GatewayTransport" => "$lib_path/GatewayTransport.php",
  "\beGateway\GetPaymentToken" => "$lib_path/GetPaymentToken.php",
  "\beGateway\Language" => "$lib_path/Language.php",
  "\beGateway\Logger" => "$lib_path/Logger.php",
  "\beGateway\Money" => "$lib_path/Money.php",
  "\beGateway\Payment" => "$lib_path/Payment.php",
  "\beGateway\QueryByToken" => "$lib_path/QueryByToken.php",
  "\beGateway\QueryByTrackingId" => "$lib_path/QueryByTrackingId.php",
  "\beGateway\QueryByUid" => "$lib_path/QueryByUid.php",
  "\beGateway\Refund" => "$lib_path/Refund.php",
  "\beGateway\Response" => "$lib_path/Response.php",
  "\beGateway\ResponseBase" => "$lib_path/ResponseBase.php",
  "\beGateway\ResponseCardToken" => "$lib_path/ResponseCardToken.php",
  "\beGateway\ResponseCheckout" => "$lib_path/ResponseCheckout.php",
  "\beGateway\Settings" => "$lib_path/Settings.php",
  "\beGateway\Void" => "$lib_path/Void.php",
  "\beGateway\Webhook" => "$lib_path/Webhook.php"
 );
error_log(print_r($classes,true));
CModule::AddAutoloadClasses("", $classes);
