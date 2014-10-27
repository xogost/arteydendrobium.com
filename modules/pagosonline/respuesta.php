<?php

include(dirname(__FILE__).'/../../config/config.inc.php');
include(dirname(__FILE__).'/../../header.php');
include(dirname(__FILE__).'/pagosonline.php');

	$numero = count($_GET);
	$tags = array_keys($_GET);// obtiene los nombres de las varibles
	$valores = array_values($_GET);// obtiene los valores de las varibles

	// crea las variables y les asigna el valor
	for($i=0;$i<$numero;$i++){
		$$tags[$i]=$valores[$i];
	}


	switch($medio_pago)
	{
		case 10: $mensaje_medio_pago = "Visa";break;
		case 11: $mensaje_medio_pago = "MasterCard";break;
		case 12: $mensaje_medio_pago = "American Express";break;
		case 22: $mensaje_medio_pago = "Diners";break;
		case 24: $mensaje_medio_pago = "Verified by Visa";break;
		case 25: $mensaje_medio_pago = "PSE (Proveedor de Servicios Electr&oacute;nicos)";break;
		case 27: $mensaje_medio_pago = "Visa d&eacute;bito";break;
		case 30: $mensaje_medio_pago = "Efecty";break;
		case 31: $mensaje_medio_pago = "Pago Referenciado";break;
	}


	$llavelocal = Configuration::get('PAGOSONLINE_LLAVEENCRIPCION');
	$clave_sin= $llavelocal."~".$usuario_id."~".$ref_venta."~".$valor."~".$moneda."~".$estado_pol;
	$firma_local = md5($clave_sin);

	if (strtoupper($firma_local) == $firma)
	{
		
		echo '<div class="block tags_block">';
		echo '<h1> Gracias por su compra</h1>';
		
		echo '<p> Apreciado cliente, la transaccion No.';
		echo $ref_pol;
		echo '  fue recibida por nuestro sistema.</p>';
		echo '<h2>Datos de compra:</h2>';
		echo '<table border="0">
<tbody>
<tr>
<td width="240"><strong> Codigo de Referencia: </strong>&nbsp;</td>
<td width="240">';
		echo $ref_venta;
		echo '</td>
</tr>
<tr>
<td><strong> Valor: </strong></td>
<td>$ ';
		echo $valor;
		echo '</td>
</tr>
<tr>
<td><strong> Moneda: </strong></td>
<td>';
		echo $moneda;
		echo '</td>
</tr>
</tbody>
</table><h2>Datos de la transaccion:</h2>
<table border="0">
<tbody>
<tr>
<td width="240"><strong> Fecha de Procesamiento: </strong>&nbsp;</td>
<td width="240">';
		echo $fecha_procesamiento;
		echo '</td>
</tr>
<tr>
<td><strong> Transaccion No.: </strong></td>
<td>';
		echo $ref_pol;
		echo '</td>
</tr>
<tr>
<td><strong> Banco o Franquicia: </strong></td>
<td>';
		echo $mensaje_medio_pago;
		echo '<br /></td>
</tr>
<tr>
<td><strong> Codigo de Respuesta POL: </strong></td>
<td>';
		echo $mensaje;
		echo '</td>
</tr>
<tr>
<td><strong> Errores: </strong></td>
<td>';
		echo $mensaje_error;
		echo '</td>
</tr>
</tbody>
</table>';
		echo '</div>';
		
	}
	else
	{
 		echo "firma digital no valida";
	}


include(dirname(__FILE__).'/../../footer.php');

?>
