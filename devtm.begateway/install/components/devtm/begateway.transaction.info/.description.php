<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arComponentDescription = array(
	"NAME" => Loc::getMessage("DEVTM_BEGATEWAY_COMP_NAME"),
	"DESCRIPTION" => Loc::getMessage("DEVTM_BEGATEWAY_COMP_DESC"),
	"ICON" => "",
	"CACHE_PATH" => "Y",
	"PATH" => array(
		"ID" => "devtm",
		"NAME" => Loc::getMessage("DEVTM_BEGATEWAY_DEVTM_SERVICE")
	),
);
?>