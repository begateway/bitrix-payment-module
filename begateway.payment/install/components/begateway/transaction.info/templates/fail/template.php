<?if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);
$type = $arResult->transaction_type;
?>

<section id="fail">
	<h1><?= Loc::getMessage("COMPONENT_BEGATEWAY_FAIL_TITLE")?></h1>
	<div id="order-description"><?= Loc::getMessage("COMPONENT_BEGATEWAY_FAIL_ORDER_DESC")?> <span><?= $arResult->order->description?></span></div>
	<div id="amount"><?= Loc::getMessage("COMPONENT_BEGATEWAY_FAIL_AMOUNT")?> <span><?= $arResult->order->amount?></span> <span><?= $arResult->order->currency ?></span></div>
	<div id="uid-transaction"><?= Loc::getMessage("COMPONENT_BEGATEWAY_FAIL_UID")?> <span><?= $arResult->gateway_response->$type->uid?></span></div>
	<div id="rejection-reason"><?= Loc::getMessage("COMPONENT_BEGATEWAY_FAIL_REJECTION_REASON")?> <span><?= $arResult->gateway_response->$type->message?></span></div>
	<br/>
	<div id="rejection-description">
		<?= Loc::getMessage("COMPONENT_BEGATEWAY_FAIL_REJECTION_DESC", array(
																		"#amount#" => $arResult->order->amount ,
                                    "#currency#" => $arResult->order->currency,
																		"#bdesc#" => $arResult->gateway_response->$type->billing_descriptor
																	)
			)?>
	</div>
</section>
