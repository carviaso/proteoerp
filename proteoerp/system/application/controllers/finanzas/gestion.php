<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class gestion extends Controller {
	var $titp='Indicadores de Gesti&oacute;n';
	var $tits='Indicadores de Gesti&oacute;n';
	var $url ='finanzas/gestion/';

	function gestion(){
		parent::Controller();
		$this->load->library('rapyd');
		//$this->datasis->modulo_id(216,1);
		$this->instalar();
	}

	function index(){
		redirect($this->url.'filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter($this->titp, 'gestion_indicador');

		$filter->unidad = new inputField('Unidad','unidad');
		$filter->unidad->rule      ='max_length[8]';
		$filter->unidad->size      =10;
		$filter->unidad->maxlength =8;

		$filter->descrip = new inputField('Descripci&oacute;n','descrip');
		$filter->descrip->rule      ='max_length[100]';
		$filter->descrip->maxlength =100;

		$filter->indicador = new inputField('Indicador','indicador');
		$filter->indicador->rule      ='max_length[100]';
		$filter->indicador->maxlength =100;

		$filter->puntos = new inputField('Puntos','puntos');
		$filter->puntos->rule      ='max_length[11]';
		$filter->puntos->size      =13;
		$filter->puntos->maxlength =11;

		$filter->objetivo = new inputField('Objetivo','objetivo');
		$filter->objetivo->rule      ='max_length[12]|numeric';
		$filter->objetivo->css_class ='inputnum';
		$filter->objetivo->size      =14;
		$filter->objetivo->maxlength =12;

		$filter->ejecuta = new inputField('Ejecuta','ejecuta');
		$filter->ejecuta->css_class ='inputnum';
		$filter->ejecuta->size      =14;
		$filter->ejecuta->maxlength =12;

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		$grid = new DataGrid('');
		$grid->order_by('id');
		$grid->per_page = 40;

		$grid->column_orderby('Id'         ,$uri,'id','align="left"');
		$grid->column_orderby('Grupo'      ,'<nformat><#id_gestion_grupo#></nformat>','id_gestion_grupo','align="right"');
		$grid->column_orderby('Unidad'     ,'unidad'   ,'unidad','align="left"');
		$grid->column_orderby('Descripción','descrip'  ,'descrip','align="left"');
		$grid->column_orderby('Indicador'  ,'indicador','indicador','align="left"');
		$grid->column_orderby('Puntos'     ,'<nformat><#puntos#></nformat>','puntos','align="right"');
		$grid->column_orderby('Objetivo'   ,'<nformat><#objetivo#></nformat>','objetivo','align="right"');
		$grid->column_orderby('Ejecuta'    ,'ejecuta'  ,'ejecuta','align="left"');

		$grid->add($this->url.'dataedit/create');
		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = heading($this->titp);
		$this->load->view('view_ventanas', $data);

	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit($this->tits, 'gestion_indicador');

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->pre_process( 'delete','_pre_delete' );

		$edit->id_gestion_grupo = new inputField('Grupo','id_gestion_grupo');
		$edit->id_gestion_grupo->rule='max_length[10]|integer';
		$edit->id_gestion_grupo->css_class='inputonlynum';
		$edit->id_gestion_grupo->size =12;
		$edit->id_gestion_grupo->maxlength =10;

		$edit->activo = new dropdownField('Activo', 'activo');
		$edit->activo->style='width:100px;';
		$edit->activo->option('S' ,'Si');
		$edit->activo->option('N' ,'No');
		$edit->activo->rule='enum[S,N]';

		$edit->unidad = new inputField('Unidad','unidad');
		$edit->unidad->rule='max_length[8]';
		$edit->unidad->size =10;
		$edit->unidad->maxlength =8;

		$edit->descrip = new inputField('Descripción','descrip');
		$edit->descrip->rule='max_length[100]';
		$edit->descrip->size =102;
		$edit->descrip->maxlength =100;

		$edit->indicador = new inputField('Indicador','indicador');
		$edit->indicador->rule='max_length[100]';
		$edit->indicador->size =102;
		$edit->indicador->maxlength =100;

		$edit->objetivo = new inputField('Objetivo','objetivo');
		$edit->objetivo->rule='max_length[12]|numeric';
		$edit->objetivo->css_class='inputnum';
		$edit->objetivo->size =14;
		$edit->objetivo->maxlength =12;

		$edit->ejecuta = new textareaField('Ejecuta','ejecuta');
		$edit->ejecuta->rule='required';
		$edit->ejecuta->cols = 70;
		$edit->ejecuta->rows = 4;

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		$script= '<script type="text/javascript" >
		$(function() {
			$(".inputnum").numeric(".");
			$(".inputonlynum").numeric();
		});
		</script>';

		$data['content'] = $edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['script']  = script('jquery.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
		$data['script'] .= $script;
		$data['title']   = heading($this->tits);
		$this->load->view('view_ventanas', $data);
	}

	function cdatos(){
		$this->rapyd->uri->keep_persistence();
		$this->rapyd->load('datagrid2','fields');

		function ctipo($id,$tipo,$ejecuta){
			if($tipo=='C'){
				return 'Calculado';
			}else{
				$rt  = str_replace('return', '', $ejecuta);
				$rt  = str_replace(';'     , '', $rt);
				$name= "pvalor[${id}]";
				$data = array(
					'name'        => $name,
					'id'          => $name,
					'value'       => trim($rt),
					'maxlength'   => '100',
					'size'        => '15',
					'class'       => 'inputnum',
				);

				return form_input($data);
			}
		}

		//echo date('Y-m-d H:i:s',mktime(0, 0, 0, date('n')-1, 1)).' - '.date('Y-m-d H:i:s',mktime(0, 0, 0, date('n')  , 0));

		$fdesde = new dateonlyField('Fecha de inicio: ','fdesde');
		$fhasta = new dateonlyField('Fecha Final: '    ,'fhasta');

		$fdesde->insertValue = date('Y-m-d H:i:s',mktime(0, 0, 0, date('n')-1,1));
		$fhasta->insertValue = date('Y-m-d H:i:s',mktime(0, 0, 0, date('n')  ,0));
		$fdesde->status  =$fhasta->status = 'create';
		$fdesde->size    =$fhasta->size   = 8;
		$fdesde->dbformat=$fhasta->dbformat='Ymd';
		$fdesde->build();
		$fhasta->build();

		$fcorte1 = new dateonlyField('Fecha de Corte: ','fcorte1');
		$fcorte1->insertValue = date('Y-m-d H:i:s',mktime(0, 0, 0, date('n')-1,15));
		$fcorte1->dbformat='Ymd';
		$fcorte1->status = 'create';
		$fcorte1->size   = 8;
		$fcorte1->build();

		$error=$msj='';
		if($this->input->post('pros') !== false){
			$fdesde->_getNewValue();
			$fhasta->_getNewValue();
			$fcorte1->_getNewValue();

			$url = $this->url.'ejecutor/'.$fdesde->newValue.'/'.$fcorte1->newValue.'/'.$fhasta->newValue;
			redirect($url);

			$pmargen = $this->input->post('pmargen');
			$pobject = $this->input->post('pobjetivo');
			$pvalor  = $this->input->post('pvalor');

			if(is_array($pmargen)){
				if(array_sum($pmargen)==100){
					foreach($pmargen AS $id=>$pm){
						if(is_numeric($pobject[$id])){
							$po = $pobject[$id];
						}else{
							$po=0;
						}

						if(isset($pvalor[$id]) && is_numeric($pvalor[$id])){
							$pv = 'return '.$pvalor[$id].';';
						}else{
							$pv = null;
						}
						if(is_numeric($pm) && $pm>=0){
							$this->db->where('id', $id);

							$data=array();
							$data['puntos']   = $pm;
							$data['objetivo'] = $po;
							if(!empty($pv)){
								$data['ejecuta']  = $pv;
							}

							$this->db->update('gestion_indicador',$data);
						}else{
							$error.='Valor no num&eacute;rico o negativo '.$id;
						}
					}
				}else{
					$error.='La suma de los valores debe dar exactamente 100';
				}
			}else{
				$error='No se puede procesar el requerimiento';
			}
		}

		$ggrid =form_open('/finanzas/gestion/cdatos').'<p align="center">';
		$ggrid .= '<b>'.$fdesde->label.'</b>'.$fdesde->output;
		$ggrid .= '<b>'.$fhasta->label.'</b>'.$fhasta->output;
		$ggrid .= br().'<b>'.$fcorte1->label.'</b>'.$fcorte1->output;
		$ggrid .= '</p>';

		$grid = new DataGrid2('Selecci&oacute;n de indicadores');
		$grid->agrupar(' ', 'nomgrup');
		$grid->use_function('ctipo');
		$select=array('a.descrip','a.indicador','a.tipo','a.puntos','a.id','a.objetivo','a.ejecuta','b.nombre AS nomgrup','unidad');
		$grid->db->select($select);
		$grid->db->from('gestion_indicador AS a');
		$grid->db->join('gestion_grupo AS b','a.id_gestion_grupo=b.id');
		$grid->db->where('a.activo','S');
		$grid->order_by('nomgrup');

		$campo = new inputField('Campo', 'puntos');
		$campo->grid_name='pmargen[<#id#>]';
		$campo->status       = 'modify';
		$campo->size         = 6;
		$campo->autocomplete = false;
		$campo->css_class    = 'inputnum';
		$campo->disable_paste= true;

		$meta = new inputField('Meta', 'objetivo');
		$meta->grid_name='pobjetivo[<#id#>]';
		$meta->status       = 'modify';
		$meta->size         = 15;
		$meta->autocomplete = false;
		$meta->css_class    = 'inputnum';
		$meta->disable_paste= true;

		$grid->column_orderby('Indicador' , 'indicador'  ,'indicador');
		$grid->column('Puntos %' , $campo ,'align=\'center\'');
		$grid->column('Objetivo' , $meta  ,'align=\'center\'');
		$grid->column('Unidad'    , 'unidad' );
		$grid->column('Tipo',"<ctipo><#id#>|<#tipo#>|<#ejecuta#></ctipo>");


		$action = "javascript:window.location='".site_url('ventas/metas/filteredgrid')."'";
		$grid->button('btn_regresa', 'Regresar', $action, 'TR');

		$grid->submit('pros', 'Generar','BR');
		$grid->build();

		$ggrid.=$grid->output;
		$ggrid.=form_close();

		$script='<script language="javascript" type="text/javascript">
		$(function(){
			$(".inputnum").numeric(".");

			$(\'input[name^="pmargen"]\').focus(function() {
				obj  = $(this);
				vval = Number(obj.val());

				tota=0;
				$(\'input[name^="pmargen"]\').each(function (i) {
					tota+=Number(this.value);
				});
				val=roundNumber(100-(tota-vval),2);
				obj.val(val);
				obj.select();
			});
		});
		</script>';

		$data['content'] ='<div class="alert">'.$error.'</div>';
		$data['content'].='<div>'.$msj.'</div>';
		$data['content'].= $ggrid;
		$data['title']   = heading('Indicadores de Gesti&oacute;n');
		$data['script']  = $script;
		$data['script'] .= phpscript('nformat.js');
		$data['head']    = $this->rapyd->get_head().script('jquery.pack.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
		$data['head']   .= style('estilos.css');
		$data['head']   .= phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function gauge($val=0){
		$this->load->library('dial_gauge',array('value'=>$val));
		header('Content-Type: image/png');
		$this->dial_gauge->display_png();
	}

	function ejecutor(){
		$fcorte = func_get_args();
		$fdesde = array_shift($fcorte);
		$fhasta = $fcorte[count($fcorte)-1];

		$query = $this->db->query("SELECT b.nombre AS grupo,a.unidad,a.descrip,a.indicador,a.puntos,a.objetivo,a.ejecuta
		FROM gestion_indicador AS a
		JOIN gestion_grupo AS b ON a.id_gestion_grupo=b.id
		WHERE a.puntos > 0 AND a.activo='S'
		ORDER BY b.nombre");

		$content  = '<table  width=100%>';
		$content .= '<col>';
		$content .= '<col>';
		$content .= '<col>';
		foreach($fcorte as $id=>$corte){
			$v = ($id%2)+1;
			$content .= '<col class="colbg'.$v.'">';
			$content .= '<col class="colbg'.$v.'">';
			$content .= '<col class="colbg'.$v.'">';
		}
		$content .= '<thead><tr><th rowspan=2 >Concepto</th><th rowspan=2>Puntos</th><th rowspan=2>Objetivo</th>';
		foreach($fcorte as $corte){
			$content .= '<td colspan="3" align="center">Corte '.dbdate_to_human($corte).'</td>';
		}
		$content .= '<th rowspan=2 >Medida</th></tr>';

		$content .= '<tr>';
		foreach($fcorte as $corte){
			$content .= '<td align="center">Objetivo logrado</td>';
			$content .= '<td align="center">% Logrado</td>';
			$content .= '<td align="center">Puntos Ganados</td>';
		}
		$content .= '</tr></thead>';

		$grupo='';
		$pinta=true;
		$puntos_porce=0;
		$puntos_total=array();
		foreach ($query->result() as $__row){
			$resul   = $this->_resultado($fdesde,$fcorte,$__row->ejecuta);
			$objetivo= $__row->objetivo;
			$unidad  = $__row->unidad;
			$puntos  = $__row->puntos;
			$ccana   = count($resul);
			if($grupo!=$__row->grupo){
				$grupo = $__row->grupo;
				$cspan = $ccana*3+4;
				$content .= '<tr class=\'rowgroup\' style=\'background-color:#FFAD28;\'><td colspan='.$cspan.'>'.$__row->grupo.'</td></tr>';
			}

			$puntos_porce += $puntos;
			$content .= '<tr ';
			$content .= ($pinta) ? 'class="odd"' : '';
			$pinta = !$pinta;
			$content .='>';

			$content .= '<td><p class=\'miniblanco\'>'.$__row->descrip.'</p>'.$__row->indicador.'</td>';
			$content .= '<td align="center">'.$puntos.'</td>';
			$content .= '<td align="right">'.htmlnformat($objetivo).' '.$unidad.'</td>';

			foreach($resul as $pos=>$val){
				if(!is_array($val)){
					$pp=$val*100/$objetivo;
					$acumulado = ceil($this->_escalas($pp)*$puntos);

					$content .= '<td align="right">'.htmlnformat($val).' '.$unidad.'</td>';
					$content .= '<td align="right">'.htmlnformat($pp).'%</td>';
					$content .= '<td align="right">'.$acumulado.'</td>';

					if(!isset($puntos_total[$pos])) $puntos_total[$pos]=0;
					$puntos_total[$pos] += $acumulado;
					if($pos==($ccana-1)){
						$content .= '<td align="center">'.img($this->url.'gauge/'.round($pp,1)).'</td>';
					}
				}else{
					foreach($val as $detalle){



					}
				}
			}
			$content .= '</tr>';
		}

		$content .= '<tr style="font-size: 28pt;background-color:#5846FF;">';
		$content .= '<td><b>Totales</b></td>';
		$content .= '<td align="center">'.$puntos_porce.'</td>';
		$content .= '<td></td>';
		for($i=0;$i<$ccana;$i++){
			$content .= '<td colspan=2></td>';
			$content .= '<td align="right">'.$puntos_total[$i].'</td>';
		}
		$content .= '<td align="center">'.img($this->url.'gauge/'.round($puntos_total[$i-1],1)).'</td>';
		$content .= '</table>';
		$content .= '</tr>';
		$content .= anchor($this->url.'cdatos','Regresar');

		$data['head']    = style('mayor/estilo.css');
		$data['script']  = script('jquery.js');
		$data['script'] .= phpscript('nformat.js');
		$data['content'] = $content;
		$data['title']   = '<h1>Indicadores de Gesti&oacute;n desde el '.dbdate_to_human($fdesde).' hasta el '.dbdate_to_human($fhasta).'</h1>';
		$this->load->view('view_ventanas_lite', $data);

	}

	function _escalas($valor){
		if($valor <= 75 ){
			return 0;
		}elseif($valor > 75 && 85 >= $valor){
			return 0.5;
		}elseif($valor > 85 && 95 >= $valor){
			return 0.75;
		}else{
			return 1;
		}
	}

	function _resultado($fdesde,$fcorte,$prog){
		$arr=array();
		foreach($fcorte as $fhasta){
			$arr[]=eval($prog);
		}
		return $arr;
	}

	function _pre_insert($do){
		return true;
	}

	function _pre_update($do){
		return true;
	}

	function _pre_delete($do){
		return true;
	}

	function _post_insert($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}

	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	function instalar(){
		if(!$this->db->table_exists('gestion_indicador')) {
			$mSQL="CREATE TABLE `gestion_indicador` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `id_gestion_grupo` int(10) DEFAULT '0',
			  `activo` CHAR(1) NULL DEFAULT 'S',
			  `unidad` char(8) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `descrip` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `indicador` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
			  `puntos` int(11) DEFAULT NULL,
			  `objetivo` decimal(12,2) DEFAULT NULL,
			  `ejecuta` longtext COLLATE utf8_unicode_ci,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Guarda los indicadores de gestion'";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('gestion_indicador');
		if(!in_array('id',$campos)){
			$mSQL="ALTER TABLE `gestion_indicador` ADD COLUMN `activo` CHAR(1) NULL DEFAULT 'S' AFTER `id_gestion_grupo`";
			$this->db->simple_query($mSQL);
		}
	}

}
