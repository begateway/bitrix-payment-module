<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arComponentParameters = array(
	"GROUPS" => array(
	),
	"PARAMETERS" => array(
		"TOKEN" => array(
			"PARENT" => "BASE",
			"NAME" => Loc::getMessage("DEVTM_BEGATEWAY_TOKEN"),
			"TYPE" => "STRING",
			"DEFAULT" => '={$_REQUEST["token"]}',
		)
	)
);