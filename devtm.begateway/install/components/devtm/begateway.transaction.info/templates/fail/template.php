<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
?>

<section id="fail">
	<h1><?= Loc::getMessage("DEVTM_BEGATEWAY_FAIL_TITLE")?></h1>
	<div id="order-description"><?= Loc::getMessage("DEVTM_BEGATEWAY_FAIL_ORDER_DESC")?> <span><?= $arResult->order->description?></span></div>
	<div id="amount"><?= Loc::getMessage("DEVTM_BEGATEWAY_FAIL_AMOUNT")?> <span><?= $arResult->order->amount ." ".$arResult->order->currency?></span></div>
	<div id="uid-transaction"><?= Loc::getMessage("DEVTM_BEGATEWAY_FAIL_UID")?> <span><?= $arResult->gateway_response->payment->uid?></span></div>
	<div id="rejection-reason"><?= Loc::getMessage("DEVTM_BEGATEWAY_FAIL_REJECTION_REASON")?> <span><?= $arResult->gateway_response->payment->auth_code?></span></div>
	<br/>
	<div id="rejection-description">
		<?= Loc::getMessage("DEVTM_BEGATEWAY_FAIL_REJECTION_DESC", array(
																		"#amount#" => $arResult->order->amount ." ".$arResult->order->currency,
																		"#bdesc#" => $arResult->gateway_response->payment->billing_descriptor
																	)
			)?>
	</div>
</section>