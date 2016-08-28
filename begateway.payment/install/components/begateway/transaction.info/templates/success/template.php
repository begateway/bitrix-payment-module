<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
$type = $arResult->transaction_type;
?>

<section id="success">
	<h1><?= Loc::getMessage("COMPONENT_BEGATEWAY_SUCCESS_TITLE")?></h1>
	<div id="order-description"><?= Loc::getMessage("COMPONENT_BEGATEWAY_SUCCESS_ORDER_DESC")?> <span><?= $arResult->order->description?></span></div>
	<div id="amount"><?= Loc::getMessage("COMPONENT_BEGATEWAY_SUCCESS_AMOUNT")?> <span><?= $arResult->order->amount ?></span> <span><?= $arResult->order->currency ?></span></div>
	<div id="uid-transaction"><?= Loc::getMessage("COMPONENT_BEGATEWAY_SUCCESS_UID")?> <span><?= $arResult->gateway_response->$type->uid?></span></div>
	<div id="auth-code"><?= Loc::getMessage("COMPONENT_BEGATEWAY_SUCCESS_AUTH_CODE")?> <span><?= $arResult->gateway_response->$type->auth_code?></span></div>
	<div id="auth-number"><?= Loc::getMessage("COMPONENT_BEGATEWAY_SUCCESS_AUTH_NUMBER")?> <span><?= $arResult->gateway_response->$type->rrn?></span></div>
	<div id="billing-descriptor"><?= Loc::getMessage("COMPONENT_BEGATEWAY_SUCCESS_BILLING_DESCRIPTOR")?> <span><?= $arResult->gateway_response->$type->billing_descriptor?></span></div>
</section>
