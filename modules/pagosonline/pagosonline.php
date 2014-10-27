<?php

class PagosOnline extends PaymentModule
{
	private $_html = '';
	private $_postErrors = array();

	public  $usuarioId;
	public  $llaveEncripcion;
	public	$gateway;
	public	$prueba;
	

	public function __construct()
	{
		$this->name = 'pagosonline';
		$this->tab = 'Payment';
		$this->version = '1.0';
		
		$this->currencies = true;
		$this->currencies_mode = 'checkbox';

		$config = Configuration::getMultiple(array('PAGOSONLINE_USUARIOID','PAGOSONLINE_LLAVEENCRIPCION','PAGOSONLINE_PRUEBA','PAGOSONLINE_GATEWAY'));
		if (isset($config['PAGOSONLINE_LLAVEENCRIPCION']))
			$this->llaveEncripcion = $config['PAGOSONLINE_LLAVEENCRIPCION'];
		if (isset($config['PAGOSONLINE_USUARIOID']))
			$this->usuarioId = $config['PAGOSONLINE_USUARIOID'];
		if (isset($config['PAGOSONLINE_PRUEBA']))
			$this->prueba = $config['PAGOSONLINE_PRUEBA'];
		if (isset($config['PAGOSONLINE_GATEWAY']))
			$this->gateway = $config['PAGOSONLINE_GATEWAY'];

		parent::__construct();

		$this->displayName = $this->l('Pagosonline');
		$this->description = $this->l('Accept payments by credit card');
		$this->confirmUninstall = $this->l('Are you sure you want to delete your details?');
		if (!isset($this->llaveEncripcion) OR !isset($this->usuarioId) OR !isset($this->prueba))
			$this->warning = $this->l('Account llaveEncripcion, usuarioId must be configured in order to use this module correctly');
		if (!sizeof(Currency::checkPaymentCurrencies($this->id)))
			$this->warning = $this->l('No currency set for this module');
	}

	public function install()
	{
		if (!parent::install() OR !$this->registerHook('payment') OR !$this->registerHook('paymentReturn'))
			return false;
		return true;
	}

	public function uninstall()
	{
		if (!Configuration::deleteByName('PAGOSONLINE_USUARIOID')
				OR !Configuration::deleteByName('PAGOSONLINE_LLAVEENCRIPCION')
				OR !Configuration::deleteByName('PAGOSONLINE_PRUEBA')
				OR !Configuration::deleteByName('PAGOSONLINE_GATEWAY')
				OR !parent::uninstall())
			return false;
		return true;
	}

	private function _postValidation()
	{
		if (isset($_POST['btnSubmit']))
		{
			if (empty($_POST['usuarioId']))
				$this->_postErrors[] = $this->l('account usuarioId are required.');
			if (empty($_POST['llaveEncripcion']))
				$this->_postErrors[] = $this->l('account llaveEncripcion are required.');
			if (empty($_POST['gateway']))
				$this->_postErrors[] = $this->l('account gateway are required.');
		}
	}

	private function _postProcess()
	{
		if (isset($_POST['btnSubmit']))
		{
			Configuration::updateValue('PAGOSONLINE_USUARIOID', $_POST['usuarioId']);
			Configuration::updateValue('PAGOSONLINE_LLAVEENCRIPCION', $_POST['llaveEncripcion']);
			Configuration::updateValue('PAGOSONLINE_PRUEBA', $_POST['prueba']);
			Configuration::updateValue('PAGOSONLINE_GATEWAY', $_POST['gateway']);
		}
		$this->_html .= '<div class="conf confirm"><img src="../img/admin/ok.gif" alt="'.$this->l('ok').'" /> '.$this->l('Settings updated').'</div>';
	}

	private function _displaypagosonline()
	{
		$this->_html .= '<img src="../modules/pagosonline/pagosonline.jpg" style="float:left; margin-right:15px;"><b>'.$this->l('This module allows you to accept payments by credit card.').'</b><br /><br />
		'.$this->l('If the client chooses this payment mode, the order will change its status into a \'Waiting for payment\' status.').'<br />
		'.$this->l('Therefore, you will need to manually confirm the order as soon as you receive a in validation state..').'<br /><br /><br />';
	}

	private function _displayForm()
	{
		$this->_html .=
		'<form action="'.$_SERVER['REQUEST_URI'].'" method="post">
			<fieldset>
			<legend><img src="../img/admin/contact.gif" />'.$this->l('Contact details').'</legend>
				<table border="0" width="400" cellpadding="0" cellspacing="0" id="form">
					<tr><td colspan="2">'.$this->l('Please specify the pagos online account details for customers').'.<br /><br /></td></tr>
					<tr><td width="200" style="height: 35px;">'.$this->l('Account llaveEncripcion').'</td><td><input type="text" name="llaveEncripcion" value="'.htmlentities(Tools::getValue('llaveEncripcion', $this->llaveEncripcion), ENT_COMPAT, 'UTF-8').'" style="width: 200px;" /></td></tr>
					<tr><td width="200" style="height: 35px;">'.$this->l('Account usuarioId').'</td><td><input type="text" name="usuarioId" value="'.htmlentities(Tools::getValue('usuarioId', $this->usuarioId), ENT_COMPAT, 'UTF-8').'" style="width: 200px;" /></td></tr>
					<tr><td>'.$this->l('Account prueba').'</td><td>
							<input type="radio" name="prueba" value="1" '.(htmlentities(Tools::getValue('prueba', $this->prueba), ENT_COMPAT, 'UTF-8') == 1 ? 'checked' : '').' /> <label class="t">'.$this->l('Si').'</label>
							<input type="radio" name="prueba" value="0" '.(htmlentities(Tools::getValue('prueba', $this->prueba), ENT_COMPAT, 'UTF-8') == 0 ? 'checked' : '').' /> <label class="t">'.$this->l('No').'</label>
					</td></tr>
					<tr><td width="200" style="height: 35px;">'.$this->l('Account gateway').'</td><td><input type="text" name="gateway" value="'.htmlentities(Tools::getValue('gateway', $this->gateway), ENT_COMPAT, 'UTF-8').'" style="width: 200px;" /></td></tr>

					<tr><td colspan="2" align="center"><input class="button" name="btnSubmit" value="'.$this->l('Update settings').'" type="submit" /></td></tr>
				</table>
			</fieldset>
		</form>';
	}

	public function getContent()
	{
		$this->_html = '<h2>'.$this->displayName.'</h2>';

		if (!empty($_POST))
		{
			$this->_postValidation();
			if (!sizeof($this->_postErrors))
				$this->_postProcess();
			else
				foreach ($this->_postErrors AS $err)
					$this->_html .= '<div class="alert error">'. $err .'</div>';
		}
		else
			$this->_html .= '<br />';

		$this->_displaypagosonline();
		$this->_displayForm();

		return $this->_html;
	}

	public function execPayment($cart)
	{
		if (!$this->active)
			return ;
		if (!$this->_checkCurrency($cart))
			return ;

		global $cookie, $smarty;

		$smarty->assign(array(
			'nbProducts' => $cart->nbProducts(),
			'cust_currency' => $cookie->id_currency,
			'currencies' => $this->getCurrency(),
			'total' => $cart->getOrderTotal(true, 3),
			'isoCode' => Language::getIsoById(intval($cookie->id_lang)),
			'pagosonlineusuarioId' => $this->usuarioId,
			'this_path' => $this->_path,
			'this_path_ssl' => Tools::getHttpHost(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));

		return $this->display(__FILE__, 'payment_execution.tpl');
	}

	public function hookPayment($params)
	{
		if (!$this->active)
			return ;
		if (!$this->_checkCurrency($params['cart']))
			return ;

		global $smarty;

		$smarty->assign(array(
			'this_path' => $this->_path,
			'this_path_ssl' => Tools::getHttpHost(true, true).__PS_BASE_URI__.'modules/'.$this->name.'/'
		));
		return $this->display(__FILE__, 'payment.tpl');
	}

	public function hookPaymentReturn($params)
	{
		if (!$this->active)
			return ;

		global $smarty, $cart, $cookie;

		$prueba = Tools::getValue('prueba', $this->prueba);
		$pagosonlineurl= Tools::getValue('prueba', $this->gateway);
		
		$extra1 = $this->id;
		$extra2 = $params['objOrder']->id_cart;

		$llaveEncripcion = Tools::getValue('llaveEncripcion', $this->llaveEncripcion);								
		$usuarioId = Tools::getValue('usuarioId', $this->usuarioId);									
		$refVenta = $params['objOrder']->id;
		$valor = $params['total_to_pay'];
		//$valor = str_replace('.', '', $valor);
		
		$monedatodo =$this->getCurrency();
		$id_moneda = $params['objOrder']->id_currency;
		foreach ($monedatodo as $mon) 
		{
			if ($id_moneda == $mon['id_currency'] )
				$moneda = $mon['iso_code'];
		}
		if ($moneda == '')  //si no existe la moneda
		 $moneda = 'COP';
		
		$firma_sin= "$llaveEncripcion~$usuarioId~$refVenta~$valor~$moneda";
	        $firma_codificada = md5($firma_sin);

		$state = $params['objOrder']->getCurrentState();


		if ($state == _PS_OS_PAGOSONLINE_ OR $state == _PS_OS_OUTOFSTOCK_)
			$smarty->assign(array(
				'total_to_pay' => Tools::displayPrice($params['total_to_pay'], $params['currencyObj'], false, false),
				'status' => 'ok',
				'firma_codificada' => $firma_codificada,
				'usuarioId' => $this->usuarioId,
				'refVenta' => $refVenta,
				'extra1' => $extra1,
				'extra2' => $extra2,
				'valor' => $valor,
				'moneda' => $moneda,
				'baseDevolucionIva' => '0',
				'iva' => '0',
				'lng' => Language::getIsoById(intval($cookie->id_lang)),
				'descripcion' => 'Pedido No. '.$refVenta.' en '.'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__,
				'prueba' => $prueba,
				'pagosonlineurl' => $pagosonlineurl,
				'urlrespuesta' => 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/pagosonline/respuesta.php',
				'urlconfirmacion' => 'http://'.$_SERVER['HTTP_HOST'].__PS_BASE_URI__.'modules/pagosonline/confirmacion.php',
			));
		else
			$smarty->assign('status', 'failed');
		return $this->display(__FILE__, 'payment_return.tpl');
	}
	
	private function _checkCurrency($cart)
	{
		$currency_order = new Currency(intval($cart->id_currency));
		$currencies_module = $this->getCurrency();
		$currency_default = Configuration::get('PS_CURRENCY_DEFAULT');
		
		if (is_array($currencies_module))
			foreach ($currencies_module AS $currency_module)
				if ($currency_order->id == $currency_module['id_currency'])
					return true;
	}
}
