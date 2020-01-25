<?
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED!==true)die();

if (!CModule::IncludeModule("sale"))
{
	ShowError(GetMessage("COMPONENT_BEGATEWAY_SALE_MODULE_NOT_INSTALL"));
	return;
}

use Bitrix\Sale\Order;
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class beGatewayTransactionInfoComponent extends CBitrixComponent {

  protected $module_id = "begateway.payment";

	protected function checkToken() {
		global $USER;

    if ( ! \Bitrix\Main\Loader::includeModule( $this->module_id ) ||
			   ! \Bitrix\Main\Loader::includeModule( "sale" ) ||
			   ! $USER->isAuthorized()
		)
			throw new Exception( Loc::getMessage("COMPONENT_BEGATEWAY_NO_TRANS_INFO") );

    $token = $_REQUEST['token'];

    return $token;
	}

	public function executeComponent()
	{
		global $APPLICATION;

		try {
      # verify token is valid
			$token = $this->checkToken();

      # locate order and its payment system
      $order_id = $_REQUEST['order_id'];
      $payment_id = $_REQUEST['payment_id'];
      $uid = $_REQUEST['uid'];
      $order = Order::load($order_id);

      if (!$order)
        throw new Exception( Loc::getMessage("COMPONENT_BEGATEWAY_WRONG_ORDER_ID") . $order_id);

      $payment = $order->getPaymentCollection()->getItemById($payment_id);

      if (!$payment)
        throw new Exception( Loc::getMessage("COMPONENT_BEGATEWAY_WRONG_PAYMENT_ID") . $payment_id);

      $arOrder = CSaleOrder::GetByID($order_id);
      CSalePaySystemAction::InitParamArrays($arOrder, $arOrder["ID"], '', array(), $payment->getFieldValues());

      \beGateway\Settings::$shopId = CSalePaySystemAction::GetParamValue("SHOP_ID");
      \beGateway\Settings::$shopKey = CSalePaySystemAction::GetParamValue("SHOP_KEY");
      \beGateway\Settings::$gatewayBase = "https://" . CSalePaySystemAction::GetParamValue("DOMAIN_GATEWAY");
      \beGateway\Settings::$checkoutBase = "https://" . CSalePaySystemAction::GetParamValue("DOMAIN_PAYMENT_PAGE");

			$query = new \beGateway\QueryByToken();
			$query->setToken($token);
			$response = $query->submit()->getResponse();

			if( ! isset( $response->checkout ) )
				throw new Exception( Loc::getMessage("COMPONENT_BEGATEWAY_FAIL_TOKEN_QUERY") );

      # verify token matches uid
			$this->arResult = $response->checkout;
      $type = $this->arResult->transaction_type;

      if (! isset($this->arResult->gateway_response))
        throw new Exception( Loc::getMessage("COMPONENT_BEGATEWAY_NO_TRANS_INFO") );

      if ($this->arResult->gateway_response->$type->uid != $uid)
          throw new Exception( Loc::getMessage("COMPONENT_BEGATEWAY_NO_UID_TOKEN_ACCESS") );

      if ($this->arResult->order->tracking_id != $order_id . ':' . $payment_id)
				throw new Exception( Loc::getMessage("COMPONENT_BEGATEWAY_WRONG_TRACKING_ID") );

      $money = new \beGateway\Money;
      $money->setCents($response->checkout->order->amount);
      $money->setCurrency($response->checkout->order->currency);

			$response->checkout->order->amount = CCurrencyLang::CurrencyFormat( $money->getAmount(), $money->getCurrency() );

      $this->arResult->order->description = $APPLICATION->ConvertCharset($this->arResult->order->description, 'utf-8', SITE_CHARSET);
      $this->arResult->gateway_response->$type->billing_descriptor = $APPLICATION->ConvertCharset($this->arResult->gateway_response->$type->billing_descriptor, SITE_CHARSET, 'utf-8');

			$this->IncludeComponentTemplate();

		} catch(Exception $e) {
			ShowError( $e->getMessage() );
		}
	}
}
