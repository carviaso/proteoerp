<?php echo $form_scripts?>
<?php echo $form_begin?>
<?php if(isset($form->error_string)) echo '<div class="alert">'.$form->error_string.'</div>'; ?>

<fieldset style='border: 1px outset #9AC8DA;background: #FFFDE9;'>
<table border=0 width="100%">
	<tr>
		<td class="littletableheader"><?php echo $form->codigo->label;  ?></td>
		<td class="littletablerow"   ><?php echo $form->codigo->output; ?></td>
		<td class="littletableheader"><?php echo $form->tipo->label;    ?></td>
		<td class="littletablerow"   ><?php echo $form->tipo->output    ?></td>
	</tr>
	<tr>
		<td class="littletableheader"><?php echo $form->descrip->label;  ?></td>
		<td class="littletablerow"   ><?php echo $form->descrip->output; ?></td>
		<td class="littletableheader"><?php echo $form->corta->label;    ?></td>
		<td class="littletablerow"   ><?php echo $form->corta->output;   ?></td>
	</tr>
	<tr>
		<td class="littletableheader"><?php echo $form->marca->label;  ?></td>
		<td class="littletablerow"   ><?php echo $form->marca->output; ?></td>
		<td class="littletableheader"><?php echo $form->susti->label;  ?></td>
		<td class="littletablerow"   ><?php echo $form->susti->output; ?></td>
	</tr>
	<tr>
		<td class="littletableheader"><?php echo $form->empaque->label  ?></td>
		<td class="littletablerow"   ><?php echo $form->empaque->output ?></td>
		<td class="littletableheader"><?php echo $form->conjunto->label; ?></td>
		<td class="littletablerow"   ><?php echo $form->conjunto->output;?></td>
	</tr>
</table>
</fieldset>

<div id="maintabcontainer">
	<ul>
		<li><a href="#tab1">Parametros</a></li>
		<li><a href="#tab2">Precios</a></li>
		<li><a href="#tab3">Existencias</a></li>
		<li><a href="#tab4">Proveedores</a></li>
		<!-- li><a href="#tab5">Promociones</a></li>
		<li><a href="#tab6">Descuentos al Mayor</a></li>
		<li><a href="#tab7">Ficha Tec.</a></li -->
	</ul>
	<div id="tab1" style='background:#EFEFFF'>
		<fieldset style='border: 1px outset #B45FF4;background: #EFEFFF;'>
		<table border=0 width="100%">
			<tr>
				<td>
					<table border=0 width="100%">
						<tr>
							<td class="littletableheader"><?php echo $form->depto->label    ?></td>
							<td class="littletablerow"   ><?php echo $form->depto->output   ?></td>
						</tr>
						<tr>
							<td class="littletableheader"><?php echo $form->familia->label ?></td>
							<td class="littletablerow" id='td_familia'><?php echo $form->familia->output?></td>
						</tr>
						<tr>
							<td class="littletableheader"><?php echo $form->grupo->label   ?></td>
							<td class="littletablerow" id='td_grupo' ><?php echo $form->grupo->output  ?></td>
						</tr>
					</table>
				</td>
				<td>
					<table border=0 width="100%">
						<tr>
							<td class="littletableheader"><?php echo $form->referen->label;  ?></td>
							<td class="littletablerow"   ><?php echo $form->referen->output; ?></td>
						</tr>
						<tr>
							<td class="littletableheader"><?php echo $form->barras->label;  ?></td>
							<td class="littletablerow"   ><?php echo $form->barras->output ?></td>
						</tr>
						<tr>
							<td class="littletableheader"><?php echo $form->cu_inve->label;  ?></td>
							<td class="littletablerow"   ><?php echo $form->cu_inve->output; ?></td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		</fieldset>
		<br>
		<fieldset style='border: 1px outset #B45FF4;background: #EFEFFF;'>
		<table border='0' width="100%">
			<tr>
				<td class="littletableheader"><?php echo $form->ensambla->label;  ?></td>
				<td class="littletablerow"   ><?php echo $form->ensambla->output; ?></td>
			</tr>
			<tr>
				<td class="littletableheader">Presentaci&oacute;n</td>
				<td class="littletablerow"   ><?php echo $form->fracxuni->output; ?> X <?php echo $form->dempaq->output; ?> = <?php echo $form->mempaq->output; ?></td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->alcohol->label;   ?></td>
				<td class="littletablerow"   ><?php echo $form->alcohol->output;  ?>X<?php echo $form->implic->output   ?></td>
			</tr>
		</table>
		</fieldset>

	</div>		

	<div id="tab2" style='background:#EFEFFF'>
		<table border=0 width="100%">
			<tr>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td>&nbsp;</td>
				<td class="littletableheader">Margen</td>
				<td class="littletableheader">Base  </td>
				<td class="littletableheader">Precio</td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->costo->label; ?></td>
				<td class="littletablerow"   ><?php echo $form->costo->output;   ?></td>
				<td class="littletableheader">Precio 1</td>
				<td><?php echo $form->margen1->output; ?></td>
				<td><?php echo $form->base1->output;   ?></td>
				<td><?php echo $form->precio1->output; ?></td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->ultimo->label   ?></td>
				<td class="littletablerow"   ><?php echo $form->ultimo->output  ?></td>
				<td class="littletableheader">Precio 2</td>
				<td><?php echo $form->margen2->output ?></td>
				<td><?php echo $form->base2->output   ?></td>
				<td><?php echo $form->precio2->output ?></td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->iva->label   ?></td>
				<td class="littletablerow"   ><?php echo $form->iva->output  ?>%</td>
				<td class="littletableheader">Precio 3</td>
				<td><?php echo $form->margen3->output ?></td>
				<td><?php echo $form->base3->output   ?></td>
				<td><?php echo $form->precio3->output ?></td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->fcalc->label; ?></td>
				<td class="littletablerow"   ><?php echo $form->fcalc->output;   ?></td>
				<td class="littletableheader">Precio 4</td>
				<td><?php echo $form->margen4->output ?></td>
				<td><?php echo $form->base4->output   ?></td>
				<td><?php echo $form->precio4->output ?></td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->redondeo->label ?></td>
				<td class="littletablerow"   ><?php echo $form->redondeo->output?></td>
				<td class="littletableheader">Precio 5</td>
				<td><?php echo $form->margen5->output ?></td>
				<td><?php echo $form->base5->output   ?></td>
				<td><?php echo $form->precio5->output ?></td>
			</tr>		
		</table>	
	</div>

	<div id="tab3" style='background:#EFEFFF'>
		<table  border=0 width="100%">
			<tr>
				<td valign='top'>
					<table  border=0 width="100%">
						<tr>
							<td class="littletableheader"><?php echo $form->tamano->label;  ?></td>
							<td class="littletablerow"   ><?php echo $form->tamano->output; ?></td>
						</tr>
						<tr>
							<td class="littletableheader"><?php echo $form->medida->label;  ?></td>
							<td class="littletablerow"   ><?php echo $form->medida->output; ?></td>
						</tr>
						<tr>
							<td class="littletableheader"><?php echo $form->serial->label;  ?></td>
							<td class="littletablerow"   ><?php echo $form->serial->output; ?></td>
						</tr>
						<tr>
							<td class="littletableheader"><?php echo $form->minimo->label;  ?></td>
							<td class="littletablerow"   ><?php echo $form->minimo->output; ?></td>
						</tr>
						<tr>
							<td class="littletableheader"><?php echo $form->maximo->label;  ?></td>
							<td class="littletablerow"   ><?php echo $form->maximo->output; ?></td>
						</tr>
						<tr>
							<td class="littletableheader"><?php echo $form->ordena->label;  ?></td>
							<td class="littletablerow"   ><?php echo $form->ordena->output; ?></td>
						</tr>
					</table>
				</td>
				<td><?php echo $form->almacenes->output ?></td>
			</tr>
		</table>
	</div>

	<div id="tab4" style='background:#EFEFFF'>
		<table border=0 width="100%">
			<tr>
				<td class="littletableheader" align="center">Código</td>
				<td class="littletableheader" align="center">Nombre</td>
				<td class="littletableheader" align="center">Fecha</td>
				<td class="littletableheader" align="center">P/Unidad</td>
				<td class="littletableheader" align="center">P/Bulto</td>
			</tr>
			<tr>
				<td><?php echo $form->cprv1->value; ?></td>
				<td><?php echo $form->nprv1->value; ?></td>
				<td><?php echo $form->fprv1->value; ?></td>
				<td><?php echo $form->pprv1->value; ?></td>
				<td><?php echo $form->uprv1->value; ?></td>
			</tr>
			<tr>
				<td><?php echo $form->cprv2->value; ?></td>
				<td><?php echo $form->nprv2->value; ?></td>
				<td><?php echo $form->fprv2->value; ?></td>
				<td><?php echo $form->pprv2->value; ?></td>
				<td><?php echo $form->uprv2->value; ?></td>
			</tr>
			<tr>
				<td><?php echo $form->cprv3->value; ?></td>
				<td><?php echo $form->nprv3->value; ?></td>
				<td><?php echo $form->fprv3->value; ?></td>
				<td><?php echo $form->pprv3->value; ?></td>
				<td><?php echo $form->uprv3->value; ?></td>
			</tr>
			<tr>
				<td><?php echo $form->cprv4->value; ?></td>
				<td><?php echo $form->nprv4->value; ?></td>
				<td><?php echo $form->fprv4->value; ?></td>
				<td><?php echo $form->pprv4->value; ?></td>
				<td><?php echo $form->uprv4->value; ?></td>
			</tr>
			<tr>
				<td><?php echo $form->cprv5->value; ?></td>
				<td><?php echo $form->nprv5->value; ?></td>
				<td><?php echo $form->fprv5->value; ?></td>
				<td><?php echo $form->pprv5->value; ?></td>
				<td><?php echo $form->uprv5->value; ?></td>
			</tr>
		</table>

	</div>
</div>

<?php echo $container_bl.$container_br; ?>     
<?php echo $form_end?>
<?php 
	//foreach (get_object_vars($form) as $label=>$tiene)
	//	echo "$label => $tiene <br>";
	//echo '<pre>';print_r($form->grupo->request);echo '</pre>'
?>
