<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/pagosonline.php');

$currency = new Currency(intval(isset($_POST['currency_payement']) ? $_POST['currency_payement'] : $cookie->id_currency));
$total = floatval($cart->getOrderTotal(true, 3));
$mailVars = array(
	'{pagosonline_usuarioId}' => nl2br(Configuration::get('PAGOS_ONLINE_USUARIOID')),
);

//cho "esta es la moneda ". $currency;
$pagosonline = new PagosOnline();
$pagosonline->validateOrder($cart->id, _PS_OS_PAGOSONLINE_, $total, $pagosonline->displayName, NULL, $mailVars, $currency->id);
$order = new Order($pagosonline->currentOrder);
Tools::redirectLink(__PS_BASE_URI__.'order-confirmation.php?id_cart='.$cart->id.'&id_module='.$pagosonline->id.'&id_order='.$pagosonline->currentOrder.'&key='.$order->secure_key);
?>
