<?
$lib_path = '/bitrix/modules/begateway.payment/lib/lib/BeGateway';

$classes = array(
  "\BeGateway\AdditionalData" => "$lib_path/AdditionalData.php",
  "\BeGateway\ApiAbstract" => "$lib_path/ApiAbstract.php",
  "\BeGateway\AuthorizationOperation" => "$lib_path/AuthorizationOperation.php",
  "\BeGateway\CaptureOperation" => "$lib_path/CaptureOperation.php",
  "\BeGateway\Card" => "$lib_path/Card.php",
  "\BeGateway\CardToken" => "$lib_path/CardToken.php",
  "\BeGateway\ChildTransaction" => "$lib_path/ChildTransaction.php",
  "\BeGateway\CreditOperation" => "$lib_path/CreditOperation.php",
  "\BeGateway\Customer" => "$lib_path/Customer.php",
  "\BeGateway\GatewayTransport" => "$lib_path/GatewayTransport.php",
  "\BeGateway\GetPaymentToken" => "$lib_path/GetPaymentToken.php",
  "\BeGateway\Language" => "$lib_path/Language.php",
  "\BeGateway\Logger" => "$lib_path/Logger.php",
  "\BeGateway\Money" => "$lib_path/Money.php",
  "\BeGateway\PaymentOperation" => "$lib_path/PaymentOperation.php",
  "\BeGateway\Product" => "$lib_path/Product.php",
  "\BeGateway\PaymentMethod\Base" => "$lib_path/PaymentMethod/Base.php",
  "\BeGateway\PaymentMethod\Erip" => "$lib_path/PaymentMethod/Erip.php",
  "\BeGateway\PaymentMethod\CreditCard" => "$lib_path/PaymentMethod/CreditCard.php",
  "\BeGateway\PaymentMethod\CreditCardHalva" => "$lib_path/PaymentMethod/CreditCardHalva.php",
  "\BeGateway\QueryByPaymentToken" => "$lib_path/QueryByPaymentToken.php",
  "\BeGateway\QueryByTrackingId" => "$lib_path/QueryByTrackingId.php",
  "\BeGateway\QueryByUid" => "$lib_path/QueryByUid.php",
  "\BeGateway\RefundOperation" => "$lib_path/RefundOperation.php",
  "\BeGateway\Response" => "$lib_path/Response.php",
  "\BeGateway\ResponseApi" => "$lib_path/ResponseApi.php",
  "\BeGateway\ResponseApiProduct" => "$lib_path/ResponseApiProduct.php",
  "\BeGateway\ResponseBase" => "$lib_path/ResponseBase.php",
  "\BeGateway\ResponseCardToken" => "$lib_path/ResponseCardToken.php",
  "\BeGateway\ResponseCheckout" => "$lib_path/ResponseCheckout.php",
  "\BeGateway\Settings" => "$lib_path/Settings.php",
  "\BeGateway\VoidOperation" => "$lib_path/VoidOperation.php",
  "\BeGateway\Webhook" => "$lib_path/Webhook.php"
 );

CModule::AddAutoloadClasses("", $classes);
