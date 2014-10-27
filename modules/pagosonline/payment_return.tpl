{if $status == 'ok'}
	<p>{l s='Your order on' mod='pagosonline'} <span class="bold">{$shop_name}</span> {l s='is complete.' mod='pagosonline'}
		<br /><br />
		{l s='Please send us a Please click the button below to complete payment.' mod='pagosonline'}
		<br /><br />- {l s='an amout of' mod='pagosonline'} <span class="price">{$total_to_pay}</span>
		<br /><br />- {l s='Do not forget to remember your order #' mod='pagosonline'} <span class="bold">{$refVenta}</span> {l s='for customer service' mod='pagosonline'}
		<br /><br />{l s='An e-mail has been sent to you with this information.' mod='pagosonline'}
		<br /><br /><span class="bold">{l s='Your order will be sent as soon as we receive your settlement.' mod='pagosonline'}</span>
		<br /><br />{l s='For any questions or for further information, please contact our' mod='pagosonline'} <a href="{$base_dir_ssl}contact-form.php">{l s='customer support' mod='pagosonline'}</a>.
		<br /><br />
	</p>

<center>
<form method="post" action="{$pagosonlineurl}" name="form_pay" id="form_pay">
	<input name="usuarioId" 	type="hidden" value="{$usuarioId}">
	<input name="firma" 		type="hidden" value="{$firma_codificada}">
	<input name="refVenta" 		type="hidden" value="{$refVenta}">
	<input name="extra1" 		type="hidden" value="{$extra1}">
	<input name="extra2" 		type="hidden" value="{$extra2}">
	<input name="descripcion" 	type="hidden" value="{$descripcion}">
	<input name="valor" 		type="hidden" value="{$valor}">
	<input name="moneda" 		type="hidden" value="{$moneda}">
	<input name="lng" 		type="hidden" value="{$lng}">
	<input name="iva" 		type="hidden" value="{$iva}">
	<input name="baseDevolucionIva" type="hidden" value="{$baseDevolucionIva}">
	<input name="url_respuesta" 	type="hidden" value="{$urlrespuesta}">
	<input name="url_confirmacion" 	type="hidden" value="{$urlconfirmacion}">
	<input name="prueba" 		type="hidden" value="{$prueba}">
	<input type="submit" name="Submit" value="Pagar">
</form>
</center>

{else}
	<p class="warning">
		{l s='We noticed a problem with your order. If you think this is an error, you can contact our' mod='pagosonline'} 
		<a href="{$base_dir_ssl}contact-form.php">{l s='customer support' mod='pagosonline'}</a>.
	</p>
{/if}

<script language="JavaScript" type="text/javascript">
<!--
	
	setTimeout("document.getElementById('form_pay').submit();",2000);
-->	
</script>	
	
