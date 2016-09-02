<?
use Bitrix\Main\Localization\Loc;
Loc::loadMessages(__FILE__);

class beTransInfoComponent extends CBitrixComponent
{
	protected $module_id = "devtm.begateway";
	protected function checkToken()
	{
		global $USER;

		if( ! \Bitrix\Main\Loader::includeModule( $this->module_id ) ||
			! \Bitrix\Main\Loader::includeModule( "sale" ) ||
			! $USER->isAuthorized()
		)
			throw new Exception( Loc::getMessage("DEVTM_BEGATEWAY_NO_TRANS_INFO") );

    $token = $_REQUEST['token'];

    return $token;
	}

	public function executeComponent()
	{
		global $APPLICATION;
		try
		{
			$token = $this->checkToken();

			\beGateway\Settings::$shopId = (int)\Bitrix\Main\Config\Option::get( $this->module_id, "shop_id" );;
			\beGateway\Settings::$shopKey = \Bitrix\Main\Config\Option::get( $this->module_id, "shop_key" );
			\beGateway\Settings::$gatewayBase = "https://". \Bitrix\Main\Config\Option::get( $this->module_id, "domain_gateway" );
			\beGateway\Settings::$checkoutBase = "https://". \Bitrix\Main\Config\Option::get( $this->module_id, "domain_payment_page" );

			$query = new \beGateway\QueryByToken();
			$query->setToken($token);
			$response = $query->submit()->getResponse();

			if( ! isset( $response->checkout ) )
				throw new Exception( Loc::getMessage("DEVTM_BEGATEWAY_FAIL_TOKEN_QUERY") );
      $money = new \beGateway\Money;
      $money->setCents($response->checkout->order->amount);
      $money->setCurrency($response->checkout->order->currency);

			$response->checkout->order->amount = CCurrencyLang::CurrencyFormat( $money->getAmount(), $money->getCurrency() );

			$this->arResult = $response->checkout;
      $type = $this->arResult->transaction_type;

      $this->arResult->order->description = $APPLICATION->ConvertCharset($this->arResult->order->description, 'utf-8', SITE_CHARSET);
      $this->arResult->gateway_response->$type->billing_descriptor = $APPLICATION->ConvertCharset($this->arResult->gateway_response->$type->billing_descriptor, 'utf-8', SITE_CHARSET);

			$this->IncludeComponentTemplate();

		}catch(Exception $e){
			ShowError( $e->getMessage() );
		}
	}
}
