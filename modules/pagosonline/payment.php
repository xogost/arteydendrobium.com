<?php

/* SSL Management */
$useSSL = true;

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/pagosonline.php');

if (!$cookie->isLogged())
    Tools::redirect('authentication.php?back=order.php');
$pagosonline = new PagosOnline();
echo $pagosonline->execPayment($cart);

include_once(dirname(__FILE__).'/../../footer.php');

?>