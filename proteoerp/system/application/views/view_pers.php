<?php

$container_bl=join('&nbsp;', $form->_button_container['BL']);
$container_br=join('&nbsp;', $form->_button_container['BR']);
$container_tr=join('&nbsp;', $form->_button_container['TR']);

if ($form->_status=='delete' || $form->_action=='delete' || $form->_status=='unknow_record'):
	echo $form->output;
else:

if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>';

echo $form_scripts;
echo $form_begin
?>
<script type="text/javascript" charset="utf-8">
	$("#nacimi").datepicker({  dateFormat: "dd/mm/yy" });
	$("#vence").datepicker({   dateFormat: "dd/mm/yy" });
	$("#ingreso").datepicker({ dateFormat: "dd/mm/yy" });
	$("#retiro").datepicker({  dateFormat: "dd/mm/yy" });
	$("#vari5").datepicker({   dateFormat: "dd/mm/yy" });

	$("#maintabcontainer").tabs();

	function creascli(id){
		<?php
		$codigo=$form->get_from_dataobjetct('codigo');
		if(!empty($codigo)){
			$codigo=trim($codigo);
		?>
		var enlace = $('#enlace').val().toUpperCase();
		enlace = enlace.replace(/^\s*|\s*$/g,"");
		var rt= $.ajax({ type: "POST", data: {codigo: enlace},url: "<?php echo site_url('ventas/scli/creafrompers/insert') ?>/"+id, async: false }).responseText;
		if(rt==''){
			if(enlace==''){
				$('#enlace').val('E<?php echo $codigo ?>');
			}else{
				$('#enlace').val(enlace);
			}
			$.prompt("<h1>Cliente guardado</h1>");
		}else{
			$.prompt("<h1>"+rt+"</h1>");
		}
		<?php } ?>
	}

	function get_depto(){
		var divi=$("#divi").val();
		$.ajax({
			url: "<?php echo site_url('nomina/pers/depto'); ?>"+'/'+divi,
			success: function(msg){
				$("#td_depto").html(msg);
			}
		});
	}
</script>


		<fieldset style='border: 1px outset #8A0808;background: #FFFBE9;'>
		<legend class="subtitulotabla" >Datos del Trabajador </legend>
		<table width='100%'  border='0'>
			<tr>
				<td class="littletableheaderc"><?php echo $form->codigo->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->codigo->output ?></td>
				<td class="littletablerow"    ><?php echo $form->tipo->label .' '.$form->tipo->output  ?></td>
				<td class="littletableheaderc"><?php echo $form->nombre->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->nombre->output ?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->nacional->label; ?></td>
				<td class="littletablerow"    ><?php echo $form->nacional->output ?></td>
				<td class="littletablerow"    ><?php echo $form->cedula->output ?></td>
				<td class="littletableheaderc"><?php echo $form->apellido->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->apellido->output ?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->rif->label   ?></td>
				<td class="littletablerow"    ><?php echo $form->rif->output  ?></td>
				<td><a href="javascript:consulrif();" title="Consultar RIF en el SENIAT" onclick="">Consultar RIF en el SENIAT</a></td>
				<td class="littletableheaderc"><?php echo $form->telefono->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->telefono->output ?></td>
			</tr>
		</table>
		</fieldset>

		<fieldset style='border: 1px outset #8A0808;background: #FFFBE9;'>
		<table width='100%'  border='0'>
			<tr>
				<td class="littletableheaderc"><?php echo $form->contrato->label  ?></td>
				<td colspan='3' class="littletablerow"    ><?php echo $form->contrato->output ?></td>
				<td class="littletableheaderc"><?php echo $form->vencimiento->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->vencimiento->output ?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->tipo->label    ?></td>
				<td class="littletablerow"    ><?php echo $form->tipo->output   ?></td>
				<td class="littletableheaderc"><?php echo $form->cargo->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->cargo->output ?></td>
				<td class="littletableheaderc"><?php echo $form->sueldo->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->sueldo->output ?></td>
			</tr>
		</table>
		</fieldset>

<div id="maintabcontainer">
	<ul>
		<li><a href="#tab1">Relaci&oacute;n Laboral</a></li>
		<li><a href="#tab2">Valores</a></li>
		<li><a href="#tab3">Variables</a></li>
		<li><a href="#tab4">Horarios</a></li>
	</ul>
	<div id="tab1" style='background:#EEFFFF'>

		<fieldset style='border: 1px outset #8A0808;'>
		<table width='100%'>
			<tr>
				<td class="littletableheaderc"><?php echo $form->civil->label   ?></td>
				<td class="littletablerow"    ><?php echo $form->civil->output  ?></td>
				<td class="littletableheaderc"><?php echo $form->direc1->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->direc1->output ?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->nacimi->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->nacimi->output ?></td>
				<td class="littletableheaderc"><?php echo $form->direc2->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->direc2->output ?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->sexo->label    ?></td>
				<td class="littletablerow"    ><?php echo $form->sexo->output   ?></td>
				<td class="littletableheaderc"><?php echo $form->direc3->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->direc3->output ?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->profes->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->profes->output ?></td>
				<td class="littletableheaderc"><?php echo $form->email->label   ?></td>
				<td class="littletablerow"    ><?php echo $form->email->output  ?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->divi->label      ?></td>
				<td class="littletablerow"    ><?php echo $form->divi->output     ?></td>
				<td class="littletableheaderc"><?php echo $form->sucursal->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->sucursal->output ?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->depa->label   ?></td>
				<td class="littletablerow" id='td_depto'><?php echo $form->depa->output ?></td>
				<td class="littletableheaderc"></td>
				<td class="littletablerow"    ></td>
			</tr>
		</table>
		</fieldset>
	</div>

	<div id="tab2" style='background:#EEFFFF'>
		<fieldset style='border: 1px outset #8A0808;'>
		<table width='100%'>
			<tr>
				<td class="littletableheaderc"><?php echo $form->enlace->label  ?>
				<?php
				if($form->_status!='show' && $form->_status!='create'){
					$id=$form->get_from_dataobjetct('id');
				?>
				<a href="javascript:creascli(<?php echo $id ?>);" title="Crear empleado como cliente"><?php echo image('list_plus.png','Crear empleado como cliente',array('border'=>'0')); ?></a></td>
				<?php } ?>

				</td>
				<td class="littletablerow"    ><?php echo $form->enlace->output ?></td>
				<td class="littletableheaderc"></td>
				<td class="littletablerow"    ></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->ingreso->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->ingreso->output ?></td>
				<td class="littletableheaderc"><?php echo $form->retiro->label   ?></td>
				<td class="littletablerow"    ><?php echo $form->retiro->output  ?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->dialib->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->dialib->output ?></td>
				<td class="littletableheaderc"><?php echo $form->dialab->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->dialab->output ?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->status->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->status->output ?></td>
				<td class="littletableheaderc"><?php echo $form->carnet->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->carnet->output ?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->tipocuent->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->tipocuent->output ?></td>
				<td class="littletableheaderc"><?php echo $form->cuentab->label    ?></td>
				<td class="littletablerow"    ><?php echo $form->cuentab->output   ?></td>
			</tr>
		</table>
		</fieldset>
	</div>


	<div id="tab3" style='background:#EEFFFF'>
		<fieldset style='border: 1px outset #8A0808;'>
		<table width='100%' border='0' >
			<tr>
				<td class="littletableheaderc"><?php echo $form->vari1->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->vari1->output ?></td>
				<td class="littletableheaderc"><?php echo $form->vari2->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->vari2->output ?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->vari3->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->vari3->output ?></td>
				<td class="littletableheaderc"><?php echo $form->vari4->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->vari4->output ?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->vari5->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->vari5->output ?></td>
				<td class="littletableheaderc"><?php echo $form->vari6->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->vari6->output ?></td>
			</tr>
		</table>
		</fieldset>
	</div>

	<div id="tab4" style='background:#EEFFFF'>
		<fieldset style='border: 1px outset #8A0808;'>
		<table width='100%' border='0' >
			<tr>
				<td class="littletableheaderc"><?php echo $form->turno->label  ?></td>
				<td class="littletablerow"    ><?php echo $form->turno->output ?></td>
				<td class="littletableheaderc">&nbsp;</td>
				<td class="littletablerow"    >&nbsp;</td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->horame->label.' Desde'  ?></td>
				<td class="littletablerow"    ><?php echo $form->horame->output ?></td>
				<td class="littletableheaderc"><?php echo 'Hasta'  ?></td>
				<td class="littletablerow"    ><?php echo $form->horams->output ?></td>
			</tr>
			<tr>
				<td class="littletableheaderc"><?php echo $form->horate->label.' Desde'  ?></td>
				<td class="littletablerow"    ><?php echo $form->horate->output ?></td>
				<td class="littletableheaderc"><?php echo 'Hasta' ?></td>
				<td class="littletablerow"    ><?php echo $form->horats->output ?></td>
			</tr>
		</table>
		</fieldset>
	</div>

</div>
<?php echo $form_end?>
<?php echo $container_bl ?>
<?php echo $container_br ?>
<?php endif; ?>
