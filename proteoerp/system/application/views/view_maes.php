<?php echo $form_scripts?>
<?php echo $form_begin?>
<?php 
//$container_tr=join("&nbsp;", $form->_button_container["TR"]);
//$container_bl=join("&nbsp;", $form->_button_container["BL"]);
//$container_br=join("&nbsp;", $form->_button_container["BR"]);
?>
<?php if(isset($form->error_string))echo '<div class="alert">'.$form->error_string.'</div>'; ?>
<table border=0 width="100%">
<tr><td align='right' ><?php echo $container_tr; ?></td></tr>
<tr><td>
<fieldset>
<legend class="subtitulotabla" >Producto </legend>
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
</table>
</fieldset>

<legend class="subtitulotabla" >Producto </legend>
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

<legend class="subtitulotabla" >Producto </legend>
<table border=0 width="100%">
	<tr>
		<td class="littletableheader"><?=$form->empaque->label  ?></td>
		<td class="littletablerow"   ><?=$form->empaque->output ?></td>
	</tr>
	<tr>
		<td class="littletableheader"><?=$form->ensambla->label  ?></td>
		<td class="littletablerow"><?=$form->ensambla->output ?></td>
	</tr>
	<tr>
		<td class="littletableheader">Presentaci&oacute;n</td>
		<td class="littletablerow"   ><?php echo $form->fracxuni->output; ?> X <?php echo $form->dempaq->output; ?> = <?php echo $form->mempaq->output; ?></td>
	</tr>
	<tr>
		<td class="littletableheader"><?php echo $form->alcohol->label;   ?></td>
		<td class="littletablerow"   ><?php echo $form->alcohol->output;  ?>X<?php echo $form->implic->output   ?></td>
	</tr>
	<tr>
		<td class="littletableheader"><?php echo $form->conjunto->label; ?></td>
		<td class="littletablerow"   ><?php echo $form->conjunto->output;?></td>
	</tr>

</table>
</fieldset>
</tr>
</td>
</table>
<div class="tabber">
	<div class="tabbertab">
	<h2>Costos y Precios</h2>
	<p>
		<table border=0 width="100%">
			<tr>
				<td></td>
				<td></td>
				<td></td>
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
				<td><?=$form->margen2->output ?></td>
				<td><?=$form->base2->output   ?></td>
				<td><?=$form->precio2->output ?></td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->iva->label   ?></td>
				<td class="littletablerow"   ><?php echo $form->iva->output  ?>%</td>
				<td class="littletableheader">Precio 3</td>
				<td><?=$form->margen3->output ?></td>
				<td><?=$form->base3->output   ?></td>
				<td><?=$form->precio3->output ?></td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->fcalc->label; ?></td>
				<td class="littletablerow"   ><?php echo $form->fcalc->output;   ?></td>
				<td class="littletableheader">Precio 4</td>
				<td><?=$form->margen4->output ?></td>
				<td><?=$form->base4->output   ?></td>
				<td><?=$form->precio4->output ?></td>
			</tr>
			<tr>
				<td class="littletableheader"><?php echo $form->redondeo->label ?></td>
				<td class="littletablerow"   ><?php echo $form->redondeo->output?></td>
				<td class="littletableheader">Precio 5</td>
				<td><?=$form->margen5->output ?></td>
				<td><?=$form->base5->output   ?></td>
				<td><?=$form->precio5->output ?></td>
			</tr>		
		</table>	
		</p>
	 </div>
	
	<div class="tabbertab">
	<h2>Existencias</h2>
	<p>
	  <table>
	  	<tr>
	  		<td valign="top">
					<table  border=0 width="100%">
					  <tr>
							<td class="littletableheader"><?=$form->tamano->label  ?></td>
							<td ><?=$form->tamano->output ?></td>
					  </tr>
						<tr>
							<td class="littletableheader"><?=$form->medida->label  ?></td>
							<td ><?=$form->medida->output ?></td>
						</tr>
						<tr>
							<td class="littletableheader"><?=$form->serial->label  ?></td>
							<td ><?=$form->serial->output ?></td>
						</tr>
						<tr>
							<td class="littletableheader"><?=$form->minimo->label  ?></td>
							<td "><?=$form->minimo->output ?></td>
						</tr>
						<tr>
							<td class="littletableheader"><?=$form->maximo->label  ?></td>
							<td ><?=$form->maximo->output ?></td>
						</tr>
						<tr>
							<td class="littletableheader"><?=$form->ordena->label  ?></td>
							<td ><?=$form->ordena->output ?></td>
						</tr>
					</table>
		  </td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td valign="top"><?=$form->almacenes->output ?></td>
		</tr>
	</table>
		
	</p>
	</div>
	<div class="tabbertab">
	<h2>Proveedores</h2>
	<p>
		<table border=0 width="100%">
			<tr>
				<td class="littletableheader">Código</td>
				<td class="littletableheader"><p align="center">Nombre</p></td>
				<td class="littletableheader">Fecha</td>
				<td class="littletableheader">P/Unidad</td>
				<td class="littletableheader">P/Bulto</td>
			</tr>
			<tr>
				<td><?=$form->cprv1->output  ?></td>
				<td><?=$form->nprv1->output  ?></td>
				<td><?=$form->fprv1->output  ?></td>
				<td><?=$form->pprv1->output  ?></td>
				<td><?=$form->uprv1->output  ?></td>
			</tr>
			<tr>
				<td><?=$form->cprv2->output  ?></td>
				<td><?=$form->nprv2->output  ?></td>
				<td><?=$form->fprv2->output  ?></td>
				<td><?=$form->pprv2->output  ?></td>
				<td><?=$form->uprv2->output  ?></td>
			</tr>
			<tr>
				<td><?=$form->cprv3->output  ?></td>
				<td><?=$form->nprv3->output  ?></td>
				<td><?=$form->fprv3->output  ?></td>
				<td><?=$form->pprv3->output  ?></td>
				<td><?=$form->uprv3->output  ?></td>
			</tr>
			<tr>
				<td><?=$form->cprv4->output  ?></td>
				<td><?=$form->nprv4->output  ?></td>
				<td><?=$form->fprv4->output  ?></td>
				<td><?=$form->pprv4->output  ?></td>
				<td><?=$form->uprv4->output  ?></td>
			</tr>
			<tr>
				<td><?=$form->cprv5->output  ?></td>
				<td><?=$form->nprv5->output  ?></td>
				<td><?=$form->fprv5->output  ?></td>
				<td><?=$form->pprv5->output  ?></td>
				<td><?=$form->uprv5->output  ?></td>
			</tr>
		</table>
	</p>
</div>
<?php echo $container_bl.$container_br; ?>
</div>     
<?php echo $form_end?>
<?php 
	//foreach (get_object_vars($form) as $label=>$tiene)
	//	echo "$label => $tiene <br>";
	//echo '<pre>';print_r($form->grupo->request);echo '</pre>'
?>
