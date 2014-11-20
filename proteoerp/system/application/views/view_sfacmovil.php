<div data-role="panel" id="presumen" data-display="overlay" style='width:350px;'>

	<p>
	<ul data-role="listview" id='sinvlist'>
		<li>Lista de productos</li>
	</ul>
	</p>
	<div>
	<table style='width: 100%;font-size:0.8em'>
		<tr>
			<td>Impuesto:</td>
			<td style='text-align:right' id='resuiva'></td>
		</tr>
		<tr>
			<td>Sub-total:</td>
			<td style='text-align:right' id='resusubtota'></td>
		</tr>
		<tr>
			<td>Total:</td>
			<td style='text-align:right;font-size:1.2em;font-weight:bold' id='resutota'></td>
		</tr>
	</table>
	</div>

	<p>
		<a href="#" class="ui-btn ui-btn-inline ui-icon-action ui-btn-icon-left ui-shadow-icon ui-mini" onclick='prefact()'>Pre-Fact.</a>
		<a href="#" class="ui-btn ui-btn-inline ui-icon-delete ui-btn-icon-left ui-shadow-icon ui-mini" onclick='descarta();'>Descartar</a>
	</p>
</div>

<div data-role="popup" id="popuplogin" data-theme="a" class="ui-corner-all" data-position-to="#header" data-dismissible="false">
	<form>
		<div style="padding:10px 20px;">
			<h3>Por favor ingrese</h3>
			<div class='alert' style='color:red;'></div>
			<label for="un" class="ui-hidden-accessible">Usuario:</label>
			<input name="usr" id="un" value="" placeholder="usuario" data-theme="a" type="text">
			<label for="pw" class="ui-hidden-accessible">Clave:</label>
			<input name="pws" id="pw" value="" placeholder="clave" data-theme="a" type="password">
			<a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b ui-btn-icon-left ui-icon-check" onclick='login()'>Acceder</a>

		</div>
	</form>
</div>


<div data-role="popup" id="dialogfac" data-overlay-theme="b" data-theme="b" data-dismissible="false" style="max-width:400px;">
	<div data-role="header" data-theme="a">
	<h1>Pre-Facturar pedido</h1>
	</div>
	<div role="main" class="ui-content">
		<h3 class="ui-title"></h3>
		<div style='color:red' id='alertscli'></div>
		<form id='sclidialog'>
		<input type="hidden" name="sclidialogcliente" id="sclidialogcliente" >

		<label for="sclidialogrifci">Rif/CI:</label>
		<input name="sclidialogrifci" id="sclidialogrifci" placeholder="" value="" type="text" />

		<label for="sclidialognombre">Nombre:</label>
		<input name="sclidialognombre" id="sclidialognombre" placeholder="" value="" type="text" />

		<div id='sclires'>
			<label for="sclidialogdire11">Direcci&oacute;n:</label>
			<input name="sclidialogdire11" id="sclidialogdire11" placeholder="" value="" type="text" />

			<label  for="sclitelefono">Tel&eacute;fono:</label>
			<input name="sclitelefono" id="sclitelefono" placeholder="" value="" type="text" />

			<label for="sclidialogciudad1">Ciudad:</label>
			<?php
			$query = $this->db->query('SELECT TRIM(ciudad) codigo, ciudad FROM ciud ORDER BY ciudad');
			$options = array(''=>'SELECCIONAR');
			foreach ($query->result() as $row){
				$options[$row->codigo]=$row->ciudad;
			}
			$defecto=$this->datasis->traevalor('CIUDAD');
			echo form_dropdown('sclidialogciudad1', $options, $defecto);
			?>

			<label for="sclidialoggrupo">Grupo:</label>
			<?php
			$query = $this->db->query('SELECT TRIM(grupo) AS grupo, CONCAT(grupo," ",gr_desc) gr_desc FROM grcl ORDER BY gr_desc');
			$options = array(''=>'SELECCIONAR');
			foreach ($query->result() as $row){
				$options[$row->grupo]=$row->gr_desc;
			}
			$defecto=$this->datasis->dameval('SELECT grupo FROM grcl WHERE gr_desc like "CONSUMIDOR FINAL%"');
			echo form_dropdown('sclidialoggrupo', $options, $defecto);
			?>

			<label for="sclidialogtiva">Tipo F&iacute;scal:</label>
			<?php
			$options = array(
				'N'=>'No Contribuyente',
				'C'=>'Contribuyente',
				'E'=>'Especial',
				'R'=>'Regimen Exento',
				'O'=>'Otro'
			);

			$defecto='N';
			echo form_dropdown('sclidialogtiva', $options, $defecto);
			?>

			<label for="sclidialogzona">Zona:</label>
			<?php
			$query = $this->db->query('SELECT TRIM(codigo) AS codigo, CONCAT(codigo," ", nombre) nombre FROM zona ORDER BY nombre');
			$options = array(''=>'SELECCIONAR');
			foreach ($query->result() as $row){
				$options[$row->codigo]=$row->nombre;
			}
			$defecto=$this->datasis->traevalor('ZONAXDEFECTO');
			echo form_dropdown('sclidialogzona', $options, $defecto);
			?>

			<label for="sclidialogemail">E-mail:</label>
			<input name="sclidialogemail" id="sclidialogemail" placeholder="" value="" type="email"/>
		</div>
		</form>
		<a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" onclick="$('#dialogfac').popup('close');return false">Cancelar</a>
		<a href="#" class="ui-btn ui-corner-all ui-shadow ui-btn-inline ui-btn-b" onclick='guarda()'>Aceptar</a>
	</div>
</div>

<div data-theme="a" data-role="header" data-id="mainHeader">
	<?php echo (isset($header))? $header:''; ?>
</div>

<div data-role="content">

	<h3>Art&iacute;culos</h3>
	<a href="#presumen" class="ui-btn ui-icon-bars ui-btn-icon-left ui-shadow-icon" >Ver Res&uacute;men</a>
	<form class="ui-filterable">
		<input id="autocomplete-input" data-type="search" placeholder="Buscar art&iacute;culo...">
		<ul id="autocomplete" data-role="listview" data-inset="true" data-filter="true" data-input="#autocomplete-input"></ul>
	</form>

</div>

<div data-theme="a" data-role="footer" data-position="fixed">
	<?php echo (isset($footer))? $footer:''; ?>
</div>

<script type="text/javascript">
var db_data = {};
var db_scli = {};
var tipop='1';
$(document).on("pagecreate", "#mainpage", function(){

	if(typeof(Storage) !== "undefined"){
		if(localStorage.getItem("data") !== null){
			try{
				db_data = JSON.parse(localStorage.getItem("data"));
			}catch(e){
				db_data = {};
				localStorage.setItem("data",JSON.stringify(db_data));
			}
		}

		if(localStorage.getItem("scli") !== null){
			try{
				db_scli = JSON.parse(localStorage.getItem("scli"));
			}catch(e){
				db_scli = {};
				localStorage.setItem("scli",JSON.stringify(db_scli));
			}
		}

	}else{
		alert('Su navegador no soporta esta aplicacion');
	}

	$("#presumen").panel({
		beforeopen: function( event, ui ) {
			var html='';
			var base, precio,iva,tiva=0,tota=0,tsubtota=0;

			$.each(db_data, function ( i, val ){
				if(tipop=='4'){
					base = val.base4;
				}else if(tipop=='3'){
					base = val.base3;
				}else if(tipop=='2'){
					base = val.base2;
				}else{
					base = val.base1;
				}
				precio = base*(1+(val.iva/100));
				ccodigo= val.codigo.replace(/[\\"']/g, '\\$&').replace(/\u0000/g, '\\0');


				html += "<li><a href='#' onclick='buscaart(\""+ccodigo+"\")'>";
				html += "<h2 style='font-size:0.8em'>"+val.descrip+"";
				html += "</h2>";
				html += "<p>C&oacute;digo: <b>"+val.codigo+"</b> Cant.:<b>"+val.cana+"</b> Base: <b>"+nformat(base,2)+"</b></p>";
				html += "Importe: <b id='tot_"+val.id+"'>"+nformat(base*val.cana,2)+"</b>";
				html += "</li>";

				iva   = roundNumber(base*val.cana*(val.iva/100),2);
				tiva += iva;
				tota += base*val.cana+iva;
				tsubtota += base*val.cana;
			});

			$('#resuiva').text(nformat(tiva,2));
			$('#resusubtota').text(nformat(tsubtota,2));
			$('#resutota').text(nformat(tota,2));

			$('#sinvlist').html(html);
			$('#sinvlist').listview( "refresh" );
			$('#sinvlist').trigger( "updatelayout");
		}
	});

	$("#autocomplete").on("filterablebeforefilter", function(e, data){
		var $ul    = $(this),
			$input = $( data.input ),
			value  = $input.val(),
			html   = "";
		var base, precio;
		$ul.html("");
		if(value && value.length > 2){
			$ul.html( "<li><div class='ui-loader'><span class='ui-icon ui-icon-loading'></span></div></li>" );
			$ul.listview( "refresh" );
			$.ajax({
				url: "<?php echo site_url('ajax/buscasinv'); ?>",
				dataType: "json",
				type: "POST",
				crossDomain: false,
				data: {
					q   : $input.val(),
					alma: "<?php echo $this->secu->getalmacen(); ?>"
				}
			}).then(function(response){
				$.each(response, function ( i, val ){

					if(tipop=='4'){
						base = val.base4;
					}else if(tipop=='3'){
						base = val.base3;
					}else if(tipop=='2'){
						base = val.base2;
					}else{
						base = val.base1;
					}
					precio = base*(1+(val.iva/100));
					if(typeof db_data[val.id] != 'undefined'){
						valor = Number(db_data[val.id].cana);
						ptota = precio*valor;
					}else{
						valor = '';
						ptota = 0;
					}

					html += "<li><a href='#'>";
					html += "<h2>"+val.descrip+"";
					html += "</h2>";
					html += "<p>Codigo: <b>"+val.codigo+"</b> Existencia:<b>"+val.existen+"</b> Precio: <b>"+nformat(precio,2)+"</b></p>";
					html += "<input name='codigoa["+val.codigo+"]' type = 'number' placeholder='Cantidad' size='10' value='"+valor+"' type='text' onkeyup='cana_add(this,"+val.id+","+JSON.stringify(val)+")' onchange='cana_add(this,"+val.id+","+JSON.stringify(val)+")' >";
					html += "<b id='tot_"+val.id+"'>"+nformat(ptota,2)+"</b>";
					html += "</li>";
				});
				$ul.html(html);
				$ul.listview( "refresh" );
				$ul.trigger( "updatelayout");
			});
		}
	});

	$("#sclidialogrifci").focusout(function(){

		rif=$(this).val().toUpperCase().replace(/[^VEPG0-9]+/g, '');
		$(this).val(rif);
		$("#sclidialogrifci").val(rif);
		if(!chrif(rif)){
			alert("Al parecer el RIF colocado no es correcto, por favor verifiquelo.");
			return true;
		}else{
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('ajax/intelirifci') ?>",
				dataType: "json",
				data: {rifci: rif},
				success: function(response){
					if(response.error==0){
						if(response.data.length==1){
							$("#sclidialognombre").val(response.data[0].nombre);
							$("#sclidialogcliente").val(response.data[0].codigo);
							if(response.data[0].codigo!=''){
								$('#sclires').hide();
							}else{
								$('#sclires').show();
							}
						}else{
							$('#sclires').show();
						}
					}
				}
			});
		}
	});

	$('#popuplogin').on({
		popupafterclose: function() {
			chlogin();
		},
		popupafteropen: function () {
			var center = ($(document).width() - $('.ui-popup-container').width()) / 2;
			$('.ui-popup-container').css({top: 0,left: center});
		}
	});

	$('#dialogfac').on({
		popupafteropen: function (){
			if($('#sclicliente').val()!=''){
				$('#sclires').hide();
			}else{
				$('#sclires').show();
			}
		}
	});

	chlogin();

	if($('#autocomplete-input').val()!=''){
		$('#autocomplete-input').keyup();
	}

});

function buscaart(codigo){
	$('#autocomplete-input').val(codigo);
	$('#autocomplete-input').keyup();
	$('#presumen').panel('close');
}

function descarta(){
	if(confirm("Seguro desea descartar el pedido?")){
		limpiar();
	}
}

function limpiar(){
	db_data=db_scli= {};
	localStorage.setItem("data",JSON.stringify(db_data));
	localStorage.setItem("scli",JSON.stringify(db_scli));
	$("#presumen").panel("close");
	tipop=1;
	$('input[data-type="search"]').val("");
	$('input[data-type="search"]').trigger("keyup");
	$('#sclidialog')[0].reset();
}

function cana_add(este,id,row){
	precio=row.base1*(1+(row.iva/100));

	if(Number(este.value)>0){
		db_data[id]={
			'cana'    :este.value,
			'descrip' :row.descrip,
			'base1'   :row.base1,
			'base2'   :row.base2,
			'base3'   :row.base3,
			'base4'   :row.base4,
			'iva'     :row.iva,
			'codigo'  :row.codigo,
			'sinvpeso':row.peso,
			'sinvtipo':row.tipo
		};

		$('#tot_'+row.id).text(nformat(precio*Number(este.value),2));
	}else{
		if(typeof db_data[id] != 'undefined'){
			delete db_data[id];
		}
		$('#tot_'+id).text(nformat(0,2));
	}
	localStorage.setItem("data",JSON.stringify(db_data));
}

function prefact(){
	$('#dialogfac').popup('open');
}

function chrif(rif){
	rif.toUpperCase();
	var patt=/[EJPGV][0-9]{9} * /g;
	if(patt.test(rif)){
		var factor= new Array(4,3,2,7,6,5,4,3,2);
		var v=0;
		if(rif[0]=="V"){
			v=1;
		}else if(rif[0]=="E"){
			v=2;
		}else if(rif[0]=="J"){
			v=3;
		}else if(rif[0]=="P"){
			v=4;
		}else if(rif[0]=="G"){
			v=5;
		}
		acum=v*factor[0];
		for(i=1;i<9;i++){
			acum=acum+parseInt(rif[i])*factor[i];
		}
		acum=11-acum%11;
		if(acum>=10 || acum<=0){
			acum=0;
		}
		return (acum==parseInt(rif[9]));
	}else{
		return true;
	}
}

function guarda(){

	var cod_cli=$('#sclidialogcliente').val();
	if(cod_cli==null || cod_cli=='' || cod_cli==false){
		$.ajax({
			type: "POST", dataType: "json", async: false,
			url: '<?php echo site_url('ventas/scli/dataeditdialog/insert'); ?>',
			data: $("#sclidialog").serialize(),
			success: function(r,s,x){
				if(r.status=="B"){
					$('#alertscli').html(r.mensaje);
				}else{
					db_scli={
						'cliente'  : r.data.cliente,
						'nombre'   : r.data.nombre,
						'rifci'    : r.data.rifci,
						'tipo'     : r.data.tipo,
						'direc'    : r.data.direc,
						'descuento': 0
					}
					localStorage.setItem("scli",JSON.stringify(db_scli));
					prefac();
					return true;
				}
			}
		});
	}else{
		db_scli={
			'cliente'  : $('#sclidialogcliente').val(),
			'nombre'   : $('#sclidialognombre').val(),
			'rifci'    : $('#sclidialogrifci').val(),
			'tipo'     : tipop,
			'direc'    : $('#sclidialogdire11').val(),
			'descuento': 0
		}
		localStorage.setItem("scli",JSON.stringify(db_scli));
		prefac();
	}
	return false;
}

function prefac(){
	$.ajax({
		type: "POST", dataType: "json", async: false,
		url: '<?php echo site_url('ventas/sfac/creafrommovil/N/insert'); ?>',
		data: {'scli': db_scli ,'sitems': db_data},
		success: function(r,s,x){
			if(r.status=="B"){
				$('#alertscli').html(r.mensaje);
			}else{
				limpiar();
				$('#dialogfac').popup('close');
				return true;
			}
		}
	});
}

function login(){
	$.ajax({
		type: "POST", dataType: "json", async: false,
		url: '<?php echo site_url($this->url.'autentificar'); ?>',
		data: $('#popuplogin').find("form").serialize(),
		success: function(r,s,x){
			if(!r){
				$('#popuplogin').find(".alert").html('Usuario o clave no valida');
				return false;
			}else{
				$('#popuplogin').popup('close');
				return true;
			}
		}
	}).fail(function( jqXHR, textStatus ) {
		$("#popuplogin").find(".alert").html('Sin respuesta del servidor');
		return false;
	});
}

function chlogin(){
	$.ajax({
		type: "POST", dataType: "json", async: false,
		url: '<?php echo site_url($this->url.'chlogin'); ?>',
		data: $("#popuplogin").find("form").serialize(),
		success: function(r,s,x){
			if(!r){

				$('#popuplogin').popup('open');
				return false;
			}else{
				return true;
			}
		}
	});
}

</script>
