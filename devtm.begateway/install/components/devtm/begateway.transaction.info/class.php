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
		
		if( strlen( $this->arParams["TOKEN"] ) != 64)
			throw new Exception( Loc::getMessage("DEVTM_BEGATEWAY_WRONG_TOKEN_LONG") );	
	
		if( md5( $USER->GetID() .":". $this->arParams["TOKEN"] ) != $_SESSION["token"])	
			throw new Exception( Loc::getMessage("DEVTM_BEGATEWAY_NO_TOKEN_ACCESS") );
		
		unset( $_SESSION["token"] );
	}
	
	protected function floatAmount( $amount )
	{
		$str1 = substr($amount, 0, strlen($amount) - 2);
		$str2 = substr($amount, strlen($amount) - 2, strlen($amount));
		return $str1 . "." . $str2;	
	}
	
	public function executeComponent()
	{
		global $APPLICATION;
		try
		{
			$this->checkToken();
			
			\beGateway\Settings::$shopId = (int)\Bitrix\Main\Config\Option::get( $this->module_id, "shop_id" );;
			\beGateway\Settings::$shopKey = \Bitrix\Main\Config\Option::get( $this->module_id, "shop_key" );
			\beGateway\Settings::$gatewayBase = "https://". \Bitrix\Main\Config\Option::get( $this->module_id, "domain_gateway" );
			\beGateway\Settings::$checkoutBase = "https://". \Bitrix\Main\Config\Option::get( $this->module_id, "domain_payment_page" );
			
			$query = new \beGateway\QueryByToken();
			$query->setToken($this->arParams["TOKEN"]);
			$response = $query->submit()->getResponse();
			
			if( ! isset( $response->checkout ) )
				throw new Exception( Loc::getMessage("DEVTM_BEGATEWAY_FAIL_TOKEN_QUERY") );
			
			$response->checkout->order->amount = $this->floatAmount( $response->checkout->order->amount );
			
			$this->arResult = $response->checkout;
					
			$this->IncludeComponentTemplate();

		}catch(Exception $e){
			ShowError( $e->getMessage() );
		}
	}
}