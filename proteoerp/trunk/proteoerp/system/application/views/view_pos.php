<script type="text/javascript">
var idtot=0;
var idsfpa=1;
$(document).ready(function() {
	$('form').submit(function() {return false;});
	$("#radioset").buttonset();
	$("mysubmit").button();
	$('#barras').focus();
	$('#barras').focus(function() { $(this).val(''); });
	$('#barras').focusout(function() {
		var tipo_doc = $('input:radio[name=tipo_doc]:checked').val();
		if(tipo_doc=='F')
			$(this).val('Introduzca un c�digo de producto');
		else
			$('#barras').val('Introduzca la referencia de la factura'); 
	});
	$("#tarjeta_0").numeric(".");
	$('#tipo_doc1').change(function() { $('#barras').val('Introduzca un c�digo de producto');       });
	$('#tipo_doc2').change(function() { $('#barras').val('Introduzca la referencia de la factura'); });
	
	$('input[name^="tarjeta_"]').keyup( function(event) { porpagar(this,event); });

	$('input[name^="tarjeta_"]').focusout(function() {
		addsfpa();
	});

	$('#barras').autocomplete({
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ventas/pos/buscasinv'); ?>",
				type: "POST",
				dataType: "json",
				data: jQuery.param({ q: req.term , tipo_doc: $('input:radio[name=tipo_doc]:checked').val() }),
				success:
					function(data){
						var cana=0;
						var sugiere = [];
						$.each(data,
							function(i, val){
								cana=cana+1;
								sugiere.push( val );
							}
						);
						add(sugiere);
					},
			});
		},
		autoSelectFist: true,
		autoFocus: true,
		delay: 10,
		minLength: 2,
		select: function( event, ui ) {
			id=idtot.toString();
			var crea=true;
			var arr =$('input[name^="codigo_"]');
			jQuery.each(arr, function() {
				nom=this.name;
				pos=this.name.lastIndexOf('_');
				if(pos>0){
					if(ui.item.codigo==this.value){
						ind = this.name.substring(pos+1);
						cc  = Number($('#cana_'+ind).val());
						$('#cana_'+ind).val(cc+1);
						crea=false;
						cimporte(Number(ind));
						totaliza();
					}
				}
			});

			if(crea){
				precio=Number(ui.item.precio);
				html = "<tr id='sitems_"+id+"'>";
				html+= "<td><input type='hidden' name='codigo_"+id+"' id='codigo_"+id+"' value='"+ui.item.codigo+"'>"+ui.item.codigo+"</td>";
				html+= "<td align='right'><input type='text' style='text-align: right;' onkeyup='cimporte(\""+id+"\")' name='cana_"+id+"' id='cana_"+id+"' size=6 class='ui-widget-content ui-corner-all' value='1' autocomplete='off'></td>";
				html+= "<td align='right'><input type='text' style='text-align: right;' name='precio_"+id+"' id='precio_"+id+"' size=8 class='ui-widget-content ui-corner-all' value='"+ui.item.precio+"' autocomplete='off' ><input type='hidden' name='iva_"+id+"' id='iva_"+id+"' value='"+ui.item.iva+"'></td>";
				html+= "<td align='right'><div id='vimporte_"+id+"'>"+ui.item.precio+"</div><input type='hidden' name='importe_"+id+"' id='importe_"+id+"' value='"+ui.item.precio+"'></td>";
				html+= "</tr>";
				html+= "<tr>";
				html+= "<td colspan='5' style='font-size:12px'>"+ui.item.descrip+"</td>";
				html+= "</tr>";
				$("#_itemul").after(html);
				$("#precio_"+id).numeric(".");
				$("#cana_"+id).numeric(".");
				totaliza();
				idtot=idtot+1;
			}
		},
		close: function(event, ui) { $('#barras').val(''); }
	});

	$('#rifci').autocomplete({
		source: function( req, add){
			$.ajax({
				url:  "<?php echo site_url('ventas/pos/buscascli'); ?>",
				type: "POST",
				dataType: "json",
				data: "q="+req.term,
				success:
					function(data){
						var sugiere = [];
						$.each(data,
							function(i, val){
								sugiere.push( val );
							}
						);
						add(sugiere);
					},
			})
		},
		minLength: 2,
		select: function( event, ui ) {
			$('#nombre').val(ui.item.nombre);
			$('#rifci').val(ui.item.rifci);
		}
	});

	$( "#dialog-scli" ).dialog({
		autoOpen: false,
		height: 300,
		width : 350,
		modal : true,
		buttons: {
			"Crear usuario": function() {
				var bValid = true;
				$("#sclirifci").removeClass( "ui-state-error");
				$("#sclinombre").removeClass("ui-state-error");

				bValid = bValid && checkLength( $("#sclirifci") ,"Rif o cedula" ,2, 9 );
				bValid = bValid && checkLength( $("#sclinombre"),"Nombre",6, 80);
				bValid = bValid && checkRegexp( $("#sclirifci") , /((^[VEJG][0-9]+$)|(^[P][A-Z0-9]+$))/i, "Este campo debe tener el siguiente formato V=Venezolano(a), E=Extranjero(a), G=Gobierno, P=Pasaporte o J=Juridico Como primer caracter seguido del numero de documento. Ej: V123456, J5555555, P56H454" );

				if ( bValid ) {
					alert('Paso');
				}
			},
			Cancel: function() {
				$( this ).dialog( "close" );
			}
		},
		close: function() {
			$("#sclirifci").removeClass( "ui-state-error" );
			$("#sclinombre").removeClass( "ui-state-error" );
		}
	});

	$( "#create-scli" ).click(function() {
		$( "#dialog-scli" ).dialog( "open" );
	});
});

//Calcula el monto restante por pagar
function porpagar(obj,event) {
	totals=Number($('#ftotal').val());
	pagado=apagar();
	valor =Number($(obj).val());

	if(pagado>totals){
		$(obj).val(roundNumber(totals-pagado+valor,2));
	}
	if (event.keyCode == '13' && totals>pagado) {
		addsfpa();
	}
}
<?php
$ssfpa=$sfpa;
unset($ssfpa['EF']);
?>
//Agrega filas para el pago
function addsfpa() {
	var pago=apagar();
	var totals=Number($('#ftotal').val());
	var diff=roundNumber(totals-pago,2);

	if(pago<totals){
		id=idsfpa.toString();

		html = "<tr id='sfpa_"+id+"'>";
		html+= <?php echo str_replace('ttarjeta_0','ttarjeta_\'+id+\'' , jsescape('<td>'.form_dropdown('ttarjeta_0', $ssfpa, '','class="ui-widget-content ui-corner-all"').'</td>')); ?>;
		html+= "<td align='right'><input type='text' name='tarjeta_"+id+"' id='tarjeta_"+id+"' style='text-align: right;' size=15 class='ui-widget-content ui-corner-all' value='"+diff.toString()+"' autocomplete='off'></td>";
		html+= "<td><span id='delete-sfpa' class='ui-icon ui-icon-closethick' onclick='delsfpa("+id+")'></span></td>";
		html+= "</tr>";
		html+= "<tr id='sfpa_tbanc_"+id+"'>";
		html+= <?php echo str_replace('tbanc_0','tbanc_\'+id+\'' , jsescape('<td>'.form_dropdown('tbanc_0', $tban, '','class="ui-widget-content ui-corner-all" style="width:195px;"').'</td>')); ?>;
		html+= "<td><input type='text' name='tnum_"+id+"' id='tnum_"+id+"' size=10 class='ui-widget-content ui-corner-all' value='' autocomplete='off'></td>";
		html+= "<td></td>";
		html+= "</tr>";

		$("#_itsfpa").before(html);
		$('input[name^="tarjeta_'+id+'"]').focusout(function() { addsfpa(); });
		$('input[name^="tarjeta_'+id+'"]').numeric(".");
		$('input[name^="tarjeta_'+id+'"]').keyup(function(event) { porpagar(this,event); });
		$('input[name^="tarjeta_'+id+'"]').focus();
		idsfpa+=1;
	}
}

//Elimina una forma de pago
function delsfpa(nid){
	id    = nid.toString();
	valor = Number($('#tarjeta_'+id).val());
	vvalor= Number($('#tarjeta_0').val());
	nvalor= roundNumber(valor+vvalor,2);
	$('#tarjeta_0').val(nvalor);
	$('#sfpa_'+id).remove();
	$('#sfpa_tbanc_'+id).remove();
}

//totaliza el monto por pagar
function apagar(){
	var pago=0;
	jQuery.each($('input[name^="tarjeta_"]'), function() {
		pago+=Number($(this).val());
	});
	return pago;
}

function updateTips( t ) {
	$( ".validateTips" )
		.text( t )
		.addClass( "ui-state-highlight" );
	setTimeout(function() {
		$( ".validateTips" ).removeClass( "ui-state-highlight", 1500 );
	}, 500 );
}

function checkLength( o, n, min, max ) {
	if ( o.val().length > max || o.val().length < min ) {
		o.addClass( "ui-state-error" );
		updateTips( "La longitud " + n + " debe estar entre " +
			min + " y " + max + "." );
		return false;
	} else {
		return true;
	}
}

function checkRegexp( o, regexp, n ) {
	if ( !( regexp.test( o.val() ) ) ) {
		o.addClass( "ui-state-error" );
		updateTips( n );
		return false;
	} else {
		return true;
	}
}

function cimporte(id){
	var precio =Number($('#precio_'+id).val());
	var cana   =Number($('#cana_'+id).val());
	var importe=precio*cana;

	$('#importe_'+id).val(roundNumber(importe,2));
	$('#vimporte_'+id).text(roundNumber(importe,2).toString());
	totaliza();
}

function totaliza(){
	var arr=$('input[name^="importe_"]');
	var totals=0;
	jQuery.each(arr, function() {
		nom=this.name;
		pos=this.name.lastIndexOf('_');
		if(pos>0){
			ind     = this.name.substring(pos+1);
			//cana    = Number($("#cana_"+ind).val());
			//itiva   = Number($("#itiva_"+ind).val());
			tota    = Number(this.value);

			//iva     = iva+tota*(itiva/100);
			totals  = totals+tota;
		}
	});
	$('#total').text(roundNumber(totals,2).toString());
	$('#ftotal').val(totals);
	tarjeta(totals);
}

function tarjeta(monto){
	$('#tarjeta_0').val(monto);
}

</script>
<style>
		label, input { display:block; }
		input.text { margin-bottom:12px; width:95%; padding: .4em; }
		fieldset { padding:0; border:0; margin-top:25px; }
</style>

<?php echo form_open(''); ?>

<table class='ui-widget ui-widget-content:'>
<tr>
	<td>
		<input type='text' name='barras' id='barras' size=30 class='ui-button ui-widget ui-state-focus ui-corner-all ui-button-text-only' autocomplete='off'>
	</td>
</tr>
<tr>
	<td>
	<div class=" ui-widget-content ui-corner-all" >
		<div class="ui-widget-header ui-corner-top" style='text-align:center;'>
			Punto de venta
		</div>
		<div id="radioset" style="text-align:center;">
			<input type="radio" id="tipo_doc1" name="tipo_doc" value='F' checked="checked" /><label for="tipo_doc1" style="width:50%;">Facturaci&oacute;n</label>
			<input type="radio" id="tipo_doc2" name="tipo_doc" value='D' />                  <label for="tipo_doc2" style="width:50%;">Devoluci&oacute;n </label>
		</div>
		<div  class="ui-widget-content" id="dialog">
			<p>
				<table class=" ui-widget-content ui-corner-all" width='100%'>
					<tr class=" ui-widget-content ui-widget-header ui-corner-top" id='_itemul'>
						<th>C&oacute;digo</th>
						<th>Cantidad</th>
						<th>Precio</th>
						<th>Importe</th>
					</tr>
					<tr>
						<td colspan='2'></td>
						<td align='right' >Total:</td>
						<td align='right' ><b id='total'>0.00</b><input type='hidden' name='ftotal' id='ftotal' value=0></td>
					</tr>
				</table>
			</p>
			<p>
			<table class=" ui-widget-content ui-corner-all" width='100%'>
				<tr class=" ui-widget-content ui-widget-header ui-corner-top">
					<th colspan='3'>Formas de pago</th>
				</tr>
				<tr id='sfpa_0'>
					<td><?php echo form_dropdown('ttarjeta_0', $sfpa, 'EF','class="ui-widget-content ui-corner-all"'); ?></td>
					<td align='right'><input type='text' name='tarjeta_0' id='tarjeta_0' style='text-align: right;' size=15 class='ui-widget-content ui-corner-all' value='0.00' autocomplete='off'></td>
					<td>&nbsp;</td>
				</tr>
				<tr id='_itsfpa'>
					<td colspan='2'></td>
				</tr>
			</table>
			</p>

			<p>
			<table class=" ui-widget-content ui-corner-all" width='100%' >
				<tr class=" ui-widget-content ui-widget-header ui-corner-top">
					<th colspan='3'>Cliente</th>
				</tr>
				<tr>
					<td>Rif o C&eacute;dula</td>
					<td>
						<input type='text' name='rifci' id='rifci' size=20 class='ui-widget-content ui-corner-all' autocomplete='off'>
					</td>
					<td>
						<span id="create-scli" class="ui-icon ui-icon-plusthick"></span>
					</td>
				</tr>
				<tr>
					<td colspan='3'><input type='text' name='nombre' id='nombre' size=40 class='ui-widget-content ui-corner-all' autocomplete='off'></td>
				</tr>
			</table>
			</p>
			<center>
			<?php echo form_submit('mysubmit', 'Guardar',"class='ui-widget-content ui-corner-all'");?>
			</center>
		</div>
	</div>
	</td>
</tr>
</table>
<?php echo form_close(); ?>

<div id="dialog-scli" title="Crear nuevo cliente">
	<p class="validateTips"></p>
	<?php echo form_open('ventas/pos/sclicrea'); ?>
	<fieldset>
		<label for="sclirifci">Rif/CI*</label>
		<input type="text" name="sclirifci" id="sclirifci"   class="text ui-widget-content ui-corner-all" autocomplete='off' />
		<label for="sclinombre">Nombre* </label>
		<input type="text" name="sclinombre" id="sclinombre" class="text ui-widget-content ui-corner-all" autocomplete='off' />
	</fieldset>
	<?php echo form_close(); ?>
</div>