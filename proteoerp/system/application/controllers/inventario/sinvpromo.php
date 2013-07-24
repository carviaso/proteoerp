<?php
require_once(BASEPATH.'application/controllers/validaciones.php');
class sinvpromo extends validaciones {
	function sinvpromo(){
		parent::Controller();
		$this->load->library("rapyd");
	}

	function index(){
		//$this->datasis->modulo_id(312,1);
		redirect('inventario/sinvpromo/filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter2','datagrid');

		$link2=site_url('inventario/common/get_linea');
		$link3=site_url('inventario/common/get_grupo');

		$mSINV=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
			'codigo' =>'C&oacute;odigo',
			'descrip'=>'Descripci&oacute;n',
			'descrip2'=>'Descripci&oacute;n 2'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'codigo'),
			'titulo'  =>'Buscar Codigo');
		$bSINV=$this->datasis->modbus($mSINV);

		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed'),
			'titulo'  =>'Buscar Proveedor');
		$bSPRV=$this->datasis->modbus($mSPRV);


		$attr = array(
		  'width'      => '800',
		  'height'     => '600',
		  'scrollbars' => 'yes',
		  'status'     => 'yes',
		  'resizable'  => 'yes',
		  'screenx'    => '0',
		  'screeny'    => '0'
		);

		$op = array();
		$mSQL='SELECT margen,CONCAT(margen,"%") AS val FROM sinvpromo GROUP BY margen';
		$query=$this->db->query($mSQL);
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$op[$row->margen]=$row->val;
			}
		}

		$sop=serialize($op);
		function dropdown($cod,$margen,$sop){
			$op=unserialize($sop);
			return form_dropdown($cod,$op,$margen);
		}

		$js='function depto(){
			if($("#depto").val()!=""){
				$("#nom_depto").attr("disabled","disabled");
			}else{
				$("#nom_depto").attr("disabled","");
			}
		}
		function linea(){
			if($("#linea").val()!=""){
				$("#nom_linea").attr("disabled","disabled");
			}else{
				$("#nom_linea").attr("disabled","");
			}
		}
		function grupo(){
			if($("#grupo").val()!=""){
				$("#nom_grupo").attr("disabled","disabled");
			}else{
				$("#nom_grupo").attr("disabled","");
			}
		}';

		$select=array('a.id','a.codigo','b.ultimo','b.descrip','b.marca'
		,'b.precio1','a.margen','a.cantidad','b.precio1*(1-(a.margen/100)) AS pfinal','b.id AS sinvid');

		$filter = new DataFilter2('Filtro de promociones');
		$filter->script($js);
		$filter->db->select($select);
		$filter->db->from('sinvpromo AS a');
		$filter->db->join('sinv AS b','a.codigo=b.codigo');

		$filter->codigo = new inputField('C&oacute;digo de producto', 'codigo');
		$filter->codigo->db_name   ='a.codigo';
		$filter->codigo->size      = 15;
		$filter->codigo->maxlength = 15;
		$filter->codigo->append($bSINV);

		$filter->descrip = new inputField('Descripci&oacute;n', 'descrip');
		$filter->descrip->db_name   ='b.descrip';

		$filter->proveed = new inputField('Proveedor', 'proveed');
		$filter->proveed->append($bSPRV);
		$filter->proveed->db_name='b.prov1';
		$filter->proveed->size=25;

		$filter->depto = new dropdownField('Departamento','depto');
		$filter->depto->db_name='b.depto';
		$filter->depto->option("","Seleccione un Departamento");
		$filter->depto->options("SELECT depto, descrip FROM dpto WHERE tipo='I' ORDER BY depto");

		$filter->linea2 = new dropdownField("L&iacute;nea","linea");
		$filter->linea2->db_name="b.linea";
		$filter->linea2->option("","Seleccione un Departamento primero");
		$depto=$filter->getval('depto');
		if($depto!==FALSE){
			$filter->linea2->options("SELECT linea, descrip FROM line WHERE depto='$depto' ORDER BY descrip");
		}else{
			$filter->linea2->option("","Seleccione un Departamento primero");
		}

		$filter->grupo = new dropdownField("Grupo", "grupo");
		$filter->grupo->db_name='c.grupo';
		$filter->grupo->option("","Seleccione una L&iacute;nea primero");
		$linea=$filter->getval('linea2');
		if($linea!==FALSE){
			$filter->grupo->options("SELECT grupo, nom_grup FROM grup WHERE linea='$linea' ORDER BY nom_grup");
		}else{
			$filter->grupo->option('','Seleccione un Departamento primero');
		}

		$filter->margen = new dropdownField('Promoci&oacute;n', 'margen');
		$filter->margen->db_name ='a.margen';
		$filter->margen->option('','Todas');
		$filter->margen->options($op );
		$filter->margen->style='width:120px;';

		$filter->marca = new dropdownField('Marca', 'marca');
		$filter->marca->db_name='b.marca';
		$filter->marca->option('','Todas');
		$filter->marca->options('SELECT TRIM(marca) AS clave, TRIM(marca) AS valor FROM marc ORDER BY marca');
		$filter->marca->style='width:220px;';

		$filter->buttons('reset','search');
		$filter->build();

		$link =anchor('/inventario/sinvpromo/dataedit/modify/<#id#>','<#codigo#>');
		$llink=anchor_popup('inventario/consultas/preciosgeneral/<#codigo#>', 'Consultar precio', $attr);
		$attr['width']  = '420';
		$attr['height'] = '400';
		$llin2=anchor_popup('inventario/precios_sinv/dataedit/modify/<#sinvid#>', '<nformat><#precio1#></nformat>', $attr);

		function pinta($u,$d,$char){
			if($u > $d) return "<b style='color:red'>$char</b>";
			return "<b style='color:green'>$char</b>";
		}

		$grid = new DataGrid('Lista de Art&iacute;culos');
		$grid->use_function('dropdown','pinta');
		$grid->order_by('codigo','asc');
		$grid->per_page = 15;

		$grid->column_orderby('C&oacute;digo'     ,$link     ,'codigo');
		$grid->column_orderby('Descripci&oacute;n','<pinta><#ultimo#>|<#pfinal#>|<#descrip#></pinta>' ,'descrip');
		$grid->column_orderby('Costo'  ,'<nformat><#ultimo#></nformat>'  ,'ultimo' ,'align=\'right\'');
		$grid->column_orderby('P.Final','<nformat><#pfinal#></nformat>'  ,'pfinal' ,'align=\'right\'');
		$grid->column_orderby('PVP'    ,$llin2    ,'precio1','align=\'right\'');
		$grid->column_orderby('Marca'  ,'marca'   ,'marca'  );
		$grid->column_orderby('Promoci&oacute;n',"<dropdown><#id#>|<#margen#>|$sop</dropdown>",'margen','align="right"');
		$grid->column('Consulta' ,$llink);
		//$grid->column_orderby('F.Desde'        ,'<dbdate_to_human>fechad</dbdate_to_human>','fechad');
		//$grid->column_orderby('F.Hasta'        ,'<dbdate_to_human>fechah</dbdate_to_human>','fechah');

		$grid->add('inventario/sinvpromo/dataedit/create');
		$grid->build();
		//echo $grid->db->last_query();

		$this->rapyd->jquery[]='
		$("select:not(#margen)").change( function() {
			$.ajax({
				type: "POST",
				url: "'.site_url('inventario/sinvpromo/cmargen').'",
				data: "cod="+$(this).attr("name")+"&margen="+$(this).val(),
				success: function(msg){
					if (msg=="0"){
						alert("Error al actualizar");
					}
				 } });
		});';

		$this->rapyd->jquery[]='
		$("#depto").change(function(){
			depto();
			$.post("'.$link2.'",{ depto:$(this).val() },function(data){$("#linea").html(data);})
			$.post("'.$link3.'",{ linea:"" },function(data){$("#grupo").html(data);})
		});
		$("#linea").change(function(){
			linea();
			$.post("'.$link3.'",{ linea:$(this).val() },function(data){$("#grupo").html(data);})
		});

		$("#grupo").change(function(){
			grupo();
		});
		depto();linea();grupo();';

		$data['content'] = $filter->output.form_open('',array('id' => 'selec_promo')).$grid->output.form_close();
		$data['title']   = '<h1>Descuentos promocionales</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit() {
		$this->rapyd->load('dataedit');

		$mSINV=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
			'codigo' =>'C&oacute;odigo',
			'descrip'=>'Descripci&oacute;n',
			'descrip2'=>'Descripci&oacute;n 2'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'codigo'),
			'titulo'  =>'Buscar Codigo');
		$bSINV=$this->datasis->modbus($mSINV);

		$script='
		<script language="javascript" type="text/javascript">
		$(function(){
			$(".inputnum").numeric(".");
		});
		</script>';

		$edit = new DataEdit('Art&iacute;culo en Promoci&oacute;n', 'sinvpromo');
		$edit->back_url = site_url('inventario/sinvpromo/filteredgrid/');

		$edit->codigo = new inputField('C&oacute;digo', 'codigo');
		$edit->codigo->size      =  15;
		$edit->codigo->maxlength =  15;
		$edit->codigo->rule      = 'required';
		$edit->codigo->append($bSINV);

		$edit->margen = new inputField('Porcentaje de descuento', 'margen');
		$edit->margen->size      = 15;
		$edit->margen->maxlength = 15;
		$edit->margen->css_class = 'inputnum';
		$edit->margen->rule      = 'required|callback_chporcent';

		/*$edit->cantidad = new inputField('Cantidad', 'cantidad');
		$edit->cantidad->size     = 15;
		$edit->cantidad->maxlength= 15;
		$edit->cantidad->css_class= 'inputnum';
		$edit->cantidad->rule     = 'required';*/

		/*$edit->fechad = new dateonlyField('Desde', 'fechad','d/m/Y');
		$edit->fechad->insertValue = date('Y-m-d',mktime(0,0,0,date('m')+3,date('j'),date('Y')));

		$edit->fechah = new dateonlyField('Hasta', 'fechah','d/m/Y');
		$edit->fechah->insertValue = date('Y-m-d');*/

		$edit->buttons('modify', 'save','undo','delete' ,'back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['head']    = script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head().$script;
		$data['title']   = '<h1>C&oacute;digo Barras de Inventario</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function cierraventana(){
		$script='
		<script language="javascript" type="text/javascript">
		$(function(){
			$(window).unload(function() { window.opener.location.reload(); });
			window.close();
		});
		</script>';

		$data['content'] = '<center>Operaci&oacute;n Exitosa</center>';
		$data['head']    = script('jquery.js').$script;
		$data['title']   = '';
		$this->load->view('view_ventanas', $data);
	}


	function dataeditexpress($codigo){
		$this->rapyd->load('dataedit');

		$script='
		<script language="javascript" type="text/javascript">
		$(function(){
			$(".inputnum").numeric(".");
		});
		</script>';

		$edit = new DataEdit('Art&iacute;culo en Promoci&oacute;n', 'sinvpromo');
		$edit->back_save   = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save   = true;
		$edit->back_cancel_delete = true;
		$edit->back_url = site_url('inventario/sinvpromo/cierraventana');

		$descrip=$this->datasis->dameval('SELECT descrip FROM sinv WHERE codigo='.$this->db->escape($codigo));
		$edit->free = new freeField('Descripci&oacute;n','libre',$descrip);

		$edit->codigo = new hiddenField('', 'codigo');
		$edit->codigo->rule       = 'required|existesinv|unique';
		$edit->codigo->insertValue= $codigo;

		$edit->margen = new inputField('Porcentaje de descuento', 'margen');
		$edit->margen->size      = 15;
		$edit->margen->maxlength = 15;
		$edit->margen->css_class = 'inputnum';
		$edit->margen->rule      = 'required|callback_chporcent';

		$edit->buttons('modify', 'save','undo','delete');
		$edit->build();

		$data['content'] = $edit->output;
		$data['head']    = script('jquery.js').script('jquery-ui.js').script("plugins/jquery.numeric.pack.js").script('plugins/jquery.meiomask.js').style('vino/jquery-ui.css').$this->rapyd->get_head().$script;
		$data['title']   = '<h1>C&oacute;digo Barras de Inventario</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function cmargen(){
		$margen=$this->input->post('margen');
		$codigo=$this->input->post('cod');
		if($margen!==false && $codigo!==false){
			//$codigo=trim($codigo);
			$mSQL='UPDATE sinvpromo SET margen='.$this->db->escape($margen).' WHERE id='.$this->db->escape($codigo);
			//memowrite($mSQL);
			//echo $mSQL;
			$rt=$this->db->simple_query($mSQL);
			echo ($rt)? 1 : 0;
		}
		echo 0;
	}

	function instala(){
		if (!$this->db->table_exists('sinvpromo')) {
			$mSQL="CREATE TABLE `sinvpromo` (
				`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
				`cliente` CHAR(5) NULL DEFAULT NULL,
				`codigo` VARCHAR(15) NULL DEFAULT NULL,
				`tipo` CHAR(1) NULL DEFAULT NULL,
				`margen` DECIMAL(18,2) NULL DEFAULT NULL,
				`cantidad` DECIMAL(18,3) NULL DEFAULT NULL,
				`fdesde` DATETIME NULL DEFAULT NULL,
				`fhasta` DATETIME NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				INDEX `codigo` (`codigo`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM;";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('sinvpromo');
		if(!in_array('fdesde',$campos)){
			$mSQL="ALTER TABLE `sinvpromo`
			ADD COLUMN `fdesde` DATETIME NULL DEFAULT NULL AFTER `cantidad`,
			ADD COLUMN `fhasta` DATETIME NULL DEFAULT NULL AFTER `fdesde`";
			$this->db->simple_query($mSQL);
		}
	}
}
