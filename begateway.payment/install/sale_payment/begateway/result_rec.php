<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
use Bitrix\Sale\Order;
use Bitrix\Main\Localization\Loc;

$module_id = "begateway.payment";
if( ! \Bitrix\Main\Loader::includeModule($module_id) ) return;
require_once dirname(__FILE__) . '/common.php';

Loc::loadMessages(__FILE__);

$webhook = new \BeGateway\Webhook;

list($order_id, $payment_id) = explode(':', $webhook->getTrackingId());

$order = Order::load($order_id);

if (!$order) {
  _output_message('ERROR: INVALID ORDER ID ' . $order_id);
}

$payment = $order->getPaymentCollection()->getItemById($payment_id);

if (!$payment) {
  _output_message('ERROR: INVALID PAYMENT ID ' . $payment_id);
}

$arOrder = CSaleOrder::GetByID($order_id);
CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"], '', array(), $payment->getFieldValues());

\BeGateway\Settings::$shopId = CSalePaySystemAction::GetParamValue("SHOP_ID");
\BeGateway\Settings::$shopKey = CSalePaySystemAction::GetParamValue("SHOP_KEY");
\BeGateway\Settings::$shopPubKey = CSalePaySystemAction::GetParamValue("SHOP_PUBLIC_KEY");

if (isset($_SERVER['CONTENT_SIGNATURE']) && !is_null(\BeGateway\Settings::$shopPubKey)) {
  $signature = base64_decode($_SERVER['CONTENT_SIGNATURE']);
  $public_key = "-----BEGIN PUBLIC KEY-----\n". \BeGateway\Settings::$shopPubKey . "\n-----END PUBLIC KEY-----";
  $key = openssl_pkey_get_public($public_key);
  if (openssl_verify(file_get_contents('php://input'), $signature, $key, OPENSSL_ALGO_SHA256) != 1) {
    _output_message('ERROR: WEBHOOK SIGNATURE IS NOT VALID');
  }
} else {
  if (!$webhook->isAuthorized()) {
    _output_message('ERROR: WEBHOOK IS NOT AUTHORIZED');
  }
}

$money = new \BeGateway\Money;
$money->setCurrency($webhook->getResponse()->transaction->currency);
$money->setCents($webhook->getResponse()->transaction->amount);

$message = array();
$message []= Loc::getMessage("SALE_BEGATEWAY_STATUS_MESSAGE_UID") . ' ' . $webhook->getUid(). ". " . Loc::getMessage("SALE_BEGATEWAY_STATUS_MESSAGE_TIME") . ' ' . $webhook->getResponse()->transaction->paid_at;

if(isset($webhook->getResponse()->transaction->three_d_secure_verification->pa_status)) {
  $message[] = "3-D Secure: " .$webhook->getResponse()->transaction->three_d_secure_verification->pa_status;
}

# save payment token data for result.php
$order = \Bitrix\Sale\Order::load($order_id);
$paymentCollection = $order->getPaymentCollection();
$payment = $paymentCollection->getItemById($payment_id);

$arFields = array(
  "PS_STATUS" => ($webhook->isSuccess() ? "Y" : "N"),
  # glue uid with saved payment token data
  "PS_INVOICE_ID" => implode(':', array($webhook->getUid(), $payment->getField('PS_INVOICE_ID'))),
  "PS_STATUS_DESCRIPTION" => implode("\n",$message),
  "PS_SUM" => $money->getAmount(),
  "PS_CURRENCY" => $money->getCurrency(),
  "PS_RESPONSE_DATE" => new \Bitrix\Main\Type\DateTime(),
  "USER_ID" => $arOrder["USER_ID"]
);

if ($arOrder["PAYED"] != "Y" &&
    $arFields["PS_STATUS"] == "Y" &&
    $arOrder["PRICE"] == $money->getAmount()) {
  CSaleOrder::PayOrder($arOrder["ID"], "Y", True, True, 0, $arFields);
  CSaleOrder::StatusOrder($arOrder["ID"], "P");
  _output_message("OK: UPDATED. UID " . $webhook->getUid());
}

_output_message("OK: NOT UPDATED. UID " . $webhook->getUid());
