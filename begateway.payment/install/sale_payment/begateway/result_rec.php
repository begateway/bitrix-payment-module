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
\BeGateway\Settings::$gatewayBase = "https://" . CSalePaySystemAction::GetParamValue("DOMAIN_GATEWAY");
\BeGateway\Settings::$checkoutBase = "https://" . CSalePaySystemAction::GetParamValue("DOMAIN_PAYMENT_PAGE");

if (!$webhook->isAuthorized()) {
  _output_message('ERROR: WEBHOOK IS NOT AUTHORIZED');
}

if (!$webhook->isAuthorized()) {
  _output_message('ERROR: WEBHOOK IS NOT AUTHORIZED');
}

$money = new \BeGateway\Money;
$money->setCurrency($webhook->getResponse()->transaction->currency);
$money->setCents($webhook->getResponse()->transaction->amount);

$message = array();
$message []= Loc::getMessage("SALE_BEGATEWAY_STATUS_MESSAGE_UID") . ' ' . $webhook->getUid(). ". " . Loc::getMessage("SALE_BEGATEWAY_STATUS_MESSAGE_TIME") . ' ' . $webhook->getResponse()->transaction->paid_at;

if(isset($webhook->getResponse()->transaction->three_d_secure_verification->pa_status)) {
  $message[] = "3-D Secure: " .$webhook->getResponse()->transaction->three_d_secure_verification->pa_status;
}

$arFields = array(
  "PS_STATUS" => ($webhook->isSuccess() ? "Y" : "N"),
  "PS_INVOICE_ID" => $webhook->getUid(),
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
