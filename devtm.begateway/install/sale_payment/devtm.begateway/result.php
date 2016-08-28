<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();?><?
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$module_id = "devtm.begateway";
if( ! \Bitrix\Main\Loader::includeModule($module_id) ) return;

\beGateway\Settings::$shopId = CSalePaySystemAction::GetParamValue("SHOP_ID");
\beGateway\Settings::$shopKey = CSalePaySystemAction::GetParamValue("SHOP_KEY");
\beGateway\Settings::$gatewayBase = "https://" . CSalePaySystemAction::GetParamValue("DOMAIN_GATEWAY");
\beGateway\Settings::$checkoutBase = "https://" . CSalePaySystemAction::GetParamValue("DOMAIN_PAYMENT_PAGE");

$payment_id = CSalePaySystemAction::GetParamValue("ORDER_PAYMENT_ID");
$order_id = IntVal($GLOBALS["SALE_INPUT_PARAMS"]["ORDER"]["ID"]);

$tracking_id = SITE_ID . ":" . $order_id . ":" . $payment_id;

set_time_limit(0);

$query = new \beGateway\QueryByTrackingId;
$query->setTrackingId($tracking_id);

$response = $query->submit();

if ($response && $response != 'error') {
  $money = new \beGateway\Money;
  $money->setCurrency($response->getResponse()->transaction->currency);
  $money->setCents($response->getResponse()->transaction->amount);

	if($response->getTrackingId() == $tracking_id) {
		$arOrder = CSaleOrder::GetByID($ORDER_ID);
		$arFields = array(
				"PS_STATUS" => ($response->isSuccess() ? "Y":"N"),
				"PS_STATUS_DESCRIPTION" => json_encode(array($response->getUid() => $response->getResponse()->transaction->type)),
				"PS_STATUS_MESSAGE" => $response->getMessage(),
				"PS_SUM" => DoubleVal($money->getAmount()),
				"PS_CURRENCY" => $money->getCurrency(),
				"PS_RESPONSE_DATE" => Date(CDatabase::DateFormatToPHP(CLang::GetDateFormat("FULL", LANG))),
			);

		if ($arOrder["PAYED"] != "Y" && $arFields["PS_STATUS"] == "Y" && Doubleval($arOrder["PRICE"]) == DoubleVal($arFields["PS_SUM"])) {
			CSaleOrder::PayOrder($arOrder["ID"], "Y");
		}
	}
	if(!empty($arFields))
		CSaleOrder::Update($ORDER_ID, $arFields);

	return true;
}

return false;
?>
