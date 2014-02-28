<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class metas extends Controller{

	function metas(){
		parent::Controller();
		$this->datasis->modulo_id('12B',1);
		$this->load->library('rapyd');
		$this->instalar();
	}

	function index(){
		redirect('ventas/metas/filteredgrid');
	}

	function filteredgrid(){
		$this->load->helper('fecha');
		$this->rapyd->load('datafilter','datagrid');

		$filter = new DataFilter('Metas');
		$filter->db->select(array('a.*','c.descrip','c.peso AS pesosinv'));
		$filter->db->from('metas AS a');
		$filter->db->join('sinv AS c','a.codigo=c.codigo');

		$filter->fecha = new dateonlyField('Fecha', 'fecha','m/Y');
		$filter->fecha->clause  ='where';
		$filter->fecha->db_name ='fecha';
		$filter->fecha->operator = '=';
		$filter->fecha->dbformat = 'Ym';
		$filter->fecha->size     = 7;
		$filter->fecha->append(' mes/a&ntilde;o');
		$filter->fecha->rule = 'required';

		$filter->codigo = new dropdownField('Producto', 'codigo');
		$filter->codigo->option('','Todos');
		$filter->codigo->options('SELECT TRIM(codigo),CONCAT_WS("-",TRIM(codigo),TRIM(descrip)) AS valor FROM sinv ORDER BY codigo');
		$filter->codigo->style = 'width:150px';

		$accion="javascript:window.location='".site_url('ventas/metas/load')."'";
		$filter->button('btn_load','Cargar desde Excel',$accion,'TR');

		$accion="javascript:window.location='".site_url('ventas/metas/cmetas')."'";
		$filter->button('btn_load','Ajustar metas a vendedores',$accion,'TR');

		$filter->buttons('reset','search');
		$filter->build();

		function formfecha($mes){
			$anio = substr($mes,0,4);
			$nom=mesLetra(substr($mes,4));
			return "$nom-$anio";
		}

		$uri = anchor('ventas/metas/dataedit/show/<#id#>','<formfecha><#fecha#></formfecha>');

		$grid = new DataGrid('Lista de Metas');
		$grid->order_by('fecha','desc');
		$grid->use_function('formfecha');
		$grid->per_page=15;

		$grid->column_orderby('Fecha'     ,$uri      ,'fecha'   );
		$grid->column_orderby('Producto'  ,'codigo'  ,'<#codigo#>-<#descrip#>'  );
		$grid->column_orderby('Descripci&oacute;n' ,'descrip'  ,'descrip'  );
		$grid->column_orderby('Peso U.'   ,'<nformat><#pesosinv#></nformat>','pesosinv','align="right"');
		$grid->column_orderby('Peso Meta' ,'<nformat><#peso#></nformat>'    ,'peso'    ,'align="right"');
		$grid->column_orderby('Cant. Meta','<nformat><#cantidad#></nformat>','cantidad','align="right"');
		$action = "javascript:window.location='" . site_url('ventas/metas/compara')."'";
		$grid->button('btn_compa', 'Comparativo', $action, 'BL');

		$grid->add('ventas/metas/dataedit/create');
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = heading('Metas propuestas');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});';

		$edit = new DataEdit('Metas','metas');

		$edit->back_url = site_url('ventas/metas/filteredgrid');
		$edit->script($script, 'create');
		$edit->script($script, 'modify');

		$edit->fecha = new dateonlyField('Fecha', 'fecha','m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->dbformat='Ym';
		$edit->fecha->size=7;
		$edit->fecha->append('mes/año');
		$edit->fecha->rule = 'required';

		$edit->codigo = new dropdownField('Producto', 'codigo');
		$edit->codigo->option('','Seleccionar');
		$edit->codigo->rule='required';
		$edit->codigo->options('SELECT TRIM(codigo),CONCAT_WS("-",TRIM(codigo),TRIM(descrip)) AS valor FROM sinv ORDER BY codigo');
		//$edit->codigo->style = 'width:150px';
		$edit->codigo->rule  = 'required';

		$edit->cantidad = new inputField('Cantidad','cantidad');
		$edit->cantidad->size =12;
		$edit->cantidad->maxlength =12;
		$edit->cantidad->rule ='numeric|required|positive';
		$edit->cantidad->css_class='inputnum';

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back','add');
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = heading('Metas');
		$data['head']    = script('jquery.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function cmetas(){
		$this->rapyd->uri->keep_persistence();
		$this->rapyd->load('datagrid','fields');

		$error=$msj='';
		if($this->input->post('pros') !== false){
			$pmargen=$this->input->post('pmargen');
			if(is_array($pmargen)){
				$sum=array_sum($pmargen);

				if(round($sum,2)==100.00){
					foreach($pmargen AS $id=>$pm){
						if(is_numeric($pm) && $pm>=0){
							$this->db->where('id', $id);
							$this->db->update('vend',array('pmargen'=>$pm));
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

		$ggrid =form_open('/ventas/metas/cmetas');

		$grid = new DataGrid('Ajuste de distribuci&oacute;n de la meta');
		$grid->order_by('nombre');
		$select=array('vendedor','nombre','pmargen','id');
		$grid->db->select($select);
		$grid->db->from('vend AS a');

		$campo = new inputField('Campo', 'pmargen');
		$campo->grid_name='pmargen[<#id#>]';
		//$campo->pattern  ='';
		$campo->status   ='modify';
		$campo->size     =6;
		$campo->autocomplete=false;
		$campo->css_class   ='inputnum';
		$campo->disable_paste=true;

		$grid->column_orderby('Vendedor', 'vendedor','vendedor');
		$grid->column_orderby('Nombre'  , 'nombre'  ,'nombre');
		$grid->column('Margen %', $campo,'align=\'center\'');

		$action = "javascript:window.location='".site_url('ventas/metas/filteredgrid')."'";
		$grid->button('btn_regresa', 'Regresar', $action, 'TR');

		$grid->submit('pros', 'Guardar','BR');
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
		$data['title']   = heading('Cambio en las metas para vendedores');
		$data['script']  = $script;
		$data['script'] .= phpscript('nformat.js');
		$data['head']    = $this->rapyd->get_head().script('jquery.pack.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
		$data['head']   .= style('estilos.css');
		$data['head']   .= phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function load(){
		$this->load->library('path');
		$this->load->helper('html');
		$path=new Path();
		$path->setPath($this->config->item('uploads_dir'));
		$path->append('/archivos');
		$this->upload_path =$path->getPath().'/';

		$this->rapyd->load('dataform');
		$form = new DataForm('ventas/metas/load/process');
		$form->title('Cargar Metas (xls)');



		$list = array(
			'<b>C</b>: El nombre del negocio ('.$this->datasis->traevalor('TITULO1').').',
			'<b>F</b>: El c&oacute;digo del producto.',
			'<b>H</b>: La meta en toneladas para el mes 1.',
			'<b>I</b>: La meta en toneladas para el mes 2 (Colocar en cero si no se tiene).',
			'<b>J</b>: La meta en toneladas para el mes 3 (Colocar en cero si no se tiene).'
		);

		$form->container = new containerField("adver","Asegurese de que el formato este en <b>Excel 97-2003</b> y contenga las siguientes columnas: ".ul($list));

		$form->archivo = new uploadField('Archivo','archivo');
		$form->archivo->upload_path   = '';
		$form->archivo->allowed_types = 'xls';
		$form->archivo->delete_file   = false;
		$form->archivo->upload_root   = '/tmp';
		$form->archivo->rule          = 'required';
		$form->archivo->append('Solo archivos en formato xls (Excel 97-2003)');

		$accion="javascript:window.location='".site_url('ventas/metas/filteredgrid')."'";
		$form->button('btn_pfl','Regresar',$accion,'TR');

		$form->submit('btnsubmit','Enviar');
		$form->build_form();

		$rti='';
		if ($form->on_success()){
			$arch= '/tmp/'.$form->archivo->upload_data['file_name'];
			$rt=$this->_nread($arch);
			$rti="<p>$rt</p>";
		}

		$data['content'] = $rti.$form->output;
		$data['title']   = heading('Carga metas desde Archivo Excel');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function _nread($arch=''){
		$meses=array(
			'enero'     =>'01','febrero'   =>'02',
			'marzo'     =>'03','abril'     =>'04',
			'mayo'      =>'05','junio'     =>'06',
			'julio'     =>'07','agosto'    =>'08',
			'septiembre'=>'09','octubre'   =>'10',
			'noviembre' =>'11','diciembre' =>'12'
		);

		$cana=0;
		$nombre=trim($this->datasis->traevalor('TITULO1'));
		$this->load->library('Spreadsheet_Excel_Reader');
		//$this->spreadsheet_excel_reader->setColumnFormat(6, '_ * #.##0,00_ ;_ * -#.##0,00_ ;_ * "-"??_ ;_ @_');
		$sim=$error=0;
		$ind_ex=array(8=>'',9=>'',10=>'');
		$anio=date('Y');
		$data=$inex=array();
		$this->spreadsheet_excel_reader->setOutputEncoding('CP1251');
		$this->spreadsheet_excel_reader->read($arch);
		//$hojas=count($this->spreadsheet_excel_reader->sheets);
		foreach($this->spreadsheet_excel_reader->sheets[0]['cells'] as $id=>$row){
			if(!isset($row[3])) continue;

			//Saca la definicion de las columnas
			if(empty($ind_ex[8]) || empty($ind_ex[9]) || empty($ind_ex[10])){
				foreach($meses as $mes=>$val){
					for($i=8;$i<11;$i++){
						if(stripos(trim($row[$i]) , $mes)!==false){
							$ind_ex[$i] = $val;
							$inex[]     = $anio.$val;
							$mSQL='DELETE FROM metas WHERE fecha='.$this->db->escape($anio.$val);
							$ban=$this->db->simple_query($mSQL);
							if(!$ban){ memowrite($mSQL,'metas'); $error++; }
						}
					}
				}
			}

			similar_text(strtoupper($row[3]),strtoupper($nombre), $sim);
			if($sim>80){
				$data['codigo'] = $row[6];
				for($i=8;$i<11;$i++){
					$monto= preg_replace('/[^0-9\.]+/', '', $row[$i]);
					$monto=floatval($monto);
					if($monto>0){
						$data['peso']   = $monto*1000;
						$data['fecha']  = $anio.$ind_ex[$i];
						$mSQL = $this->db->insert_string('metas', $data);
						$mSQL.= 'ON DUPLICATE KEY UPDATE `peso`='.$data['peso'];
						$ban=$this->db->simple_query($mSQL);

						if(!$ban){ memowrite($mSQL,'metas'); $error++; }else{ $cana++; }
					}
				}
			}
		}

		$ww=implode(',',$inex);
		$mSQL="UPDATE
			metas AS a
			JOIN sinv AS b ON a.codigo=b.codigo
			SET a.cantidad=CEIL(a.peso/b.peso)
			WHERE b.peso>0";
		$ban=$this->db->simple_query($mSQL);

		if(file_exists($arch)) unlink($arch);
		if($error>0){
			return 'Hubo algunos errores se generaron centinelas.';
		}else{
			return "Fueron cargadas $cana metas.";
		}
	}

	function compara(){
		$this->load->helper('fecha');
		$this->rapyd->load('datafilter','datagrid');
		$this->db->_escape_char='';
		$this->db->_protect_identifiers=false;

		function colum($diferen){
			if ($diferen<0)
				return ('<b style="color:red;">'.$diferen.'</b>');
			else
				return ('<b style="color:green;">'.$diferen.'</b>');
		}

		function dif($a,$b){
			return nformat($a-$b);
		}

		function formfecha($mes){
			$anio = substr($mes,0,4);
			$nom=mesLetra(substr($mes,4));
			return "$nom-$anio";
		}

		$base_process_uri= $this->rapyd->uri->implode_uri('base_uri','gfid','orderby');
		$filter = new DataForm($this->rapyd->uri->add_clause($base_process_uri, 'search'));
		$filter->title('Filtro');
		//$filter->attributes=array('onsubmit'=>'is_loaded()');

		$filter->fecha = new dateonlyField('Fecha', 'd.fecha','m/Y');
		$filter->fecha->clause  ='where';
		$filter->fecha->insertValue = date('mY');
		$filter->fecha->operator= '=';
		$filter->fecha->dbformat= 'Ym';
		$filter->fecha->size    =7;
		$filter->fecha->append('mes/año');
		$filter->fecha->rule = 'required';

		$filter->vendedor = new dropdownField('Vendedor', 'vendedor');
		$filter->vendedor->option('','Todos');
		$filter->vendedor->options("SELECT vendedor, CONCAT(vendedor,'-',nombre) AS nom FROM vend WHERE tipo IN ('V','A') ORDER BY vendedor");

		$accion="javascript:window.location='".site_url('ventas/metas/filteredgrid')."'";
		$filter->button('btn_pfl','Regresar',$accion,'TR');

		$filter->submit('btnsubmit','Buscar');
		$filter->build_form();

		if($this->rapyd->uri->is_set('search') AND $filter->is_valid()){

			$fecha    = $filter->fecha->newValue;
			$vendedor = $filter->vendedor->newValue;
			$udia     = days_in_month(substr($fecha,4),substr($fecha,0,4));

			$fechai=$fecha.'01';
			$fechaf=$fecha.$udia;

			$grid = new DataGrid('Resultados');
			$grid->use_function('colum','dif','formfecha');

			$sel=array('a.codigo','a.descrip',
			'SUM(d.cana*IF(tipoa=\'D\',-1,1)) AS ventas');

			if(!empty($vendedor)){
				$dbvd=$this->db->escape($vendedor);
				$sel[]=$dbvd.' AS vendedor';
				$sel[]=$this->db->escape($filter->vendedor->options[$vendedor]).' AS nombrev';
				$sel[]='c.cantidad AS meta';

				$pmargen=$this->datasis->dameval('SELECT pmargen FROM vend WHERE vendedor='.$dbvd);
				if(empty($pmargen)) $pmargen=0; else $pmargen=$pmargen/100;
				$sel[]='c.cantidad*'.$pmargen.' AS meta';
				$ww= ' AND d.vendedor='.$dbvd;
			}else{
				$sel[]='c.cantidad AS meta';
				$ww='';
			}

			$grid->db->from('sinv   AS a');
			$grid->db->join('metas  AS c','a.codigo=c.codigo AND c.fecha='.$fecha);
			$grid->db->join('sitems AS d',"d.codigoa=c.codigo AND d.fecha BETWEEN $fechai AND $fechaf $ww",'left');
			$grid->db->join('vend   AS e','d.vendedor=e.vendedor','left');
			$grid->db->group_by('a.codigo');
			$grid->db->select($sel);

			$grid->column('C&oacute;digo'     ,'codigo');
			$grid->column('Descripci&oacute;n','descrip' );
			if(!empty($vendedor)){
				$grid->column('Vendedor'   ,'vendedor',"align='center'");
				$grid->column('Nombre'     ,'nombrev' ,"align='left'");
			}
			$grid->column('Venta'      ,'<nformat><#ventas#></nformat>',"align='right'");
			$grid->column('Meta'       ,'<nformat><#meta#></nformat>'  ,"align='right'");
			$grid->column('Diferencia' ,'<colum><dif><#ventas#>|<#meta#></dif></colum>' ,"align='right'");

			$grid->build();
			$tabla=$grid->output;
			//echo $grid->db->last_query();
		}else{
			$tabla='';
		}

		$data['content'] = $filter->output.$tabla;
		$data['title']   = heading('Estado de Metas');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function instalar(){
		if(!$this->db->field_exists('pmargen', 'vend')){
			$mSQL="ALTER TABLE `vend` ADD COLUMN `pmargen` DECIMAL(5,2) UNSIGNED NULL DEFAULT '0' AFTER `almacen`";
			$rt=$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('metas')){
			$mSQL="CREATE TABLE `metas` (
				`id` INT(15) UNSIGNED NOT NULL AUTO_INCREMENT,
				`codigo` VARCHAR(15) NOT NULL DEFAULT '',
				`cantidad` DECIMAL(12,3) NULL DEFAULT NULL,
				`peso` DECIMAL(12,3) NULL DEFAULT NULL,
				`fecha` INT(10) NOT NULL DEFAULT '0',
				`tipo` CHAR(1) NOT NULL DEFAULT 'T' COMMENT 'Unidad de medida T=Tonelada',
				PRIMARY KEY (`id`),
				UNIQUE INDEX `codfec` (`fecha`, `codigo`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}
	}
}
