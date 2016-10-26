<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true) die();

use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

$arComponentDescription = array(
  "NAME" => Loc::getMessage("COMPONENT_BEGATEWAY_NAME"),
  "DESCRIPTION" => Loc::getMessage("COMPONENT_BEGATEWAY_DESC"),
  "ICON" => "",
  "CACHE_PATH" => "Y",
  "PATH" => array(
    "ID" => "e-store",
    "CHILD" => array(
      "ID" => "sale_order",
      "NAME" => GetMessage("SOP_NAME")
    )
  )
);
?>
