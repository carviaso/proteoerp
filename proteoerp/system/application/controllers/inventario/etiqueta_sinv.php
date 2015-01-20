<?php
/**
 * ProteoERP
 *
 * @autor Judelvis A. Rivas
 * @autor Andres Hocevar
 * @license  GNU GPL v3
*/
require_once(APPPATH.'/controllers/inventario/consultas.php');

class etiqueta_sinv extends Controller {

	function etiqueta_sinv(){
		parent::Controller();
		$this->load->library('rapyd');
	}

	function index(){
		redirect('inventario/etiqueta_sinv/menu');
	}

	function menu(){
		$thtml='<b>Seleccione m&eacute;todo para generar los habladores</b>';
		$html[]=anchor('inventario/etiqueta_sinv/num_compra'  ,'Por n&uacute;mero compra'   ).': generar habladores con todos los productos pertenecientes a una compra';
		$html[]=anchor('inventario/etiqueta_sinv/lee_barras'  ,'Por c&oacute;digo de barras').': permite generar habladores por productos seleccionados';
		$html[]=anchor('inventario/etiqueta_sinv/filteredgrid','Por filtro de productos'    ).': permite generar los habladores filtrandolos por cacter&iacute;sticas comunes';
		$data['title']  = '<h1>Men&uacute; de Habladores</h1>';
		$data['content']=$thtml.ul($html).'<p style="font-size:0.5em;text-align:center">Formato: <b>ETIQUETA1</b></p>';
		$this->load->view('view_ventanas', $data);
	}

	function menuaja(){
		$thtml='<b>Seleccione m&eacute;todo para generar los habladores</b>';
		$html[]=anchor('inventario/etiqueta_sinv/num_compra'  ,'Por n&uacute;mero compra'   ).': generar habladores con todos los productos pertenecientes a una compra';
		$html[]=anchor('inventario/etiqueta_sinv/lee_barras'  ,'Por c&oacute;digo de barras').': permite generar habladores por productos seleccionados';
		$html[]=anchor('inventario/etiqueta_sinv/filteredgrid','Por filtro de productos'    ).': permite generar los habladores filtrandolos por cacter&iacute;sticas comunes';
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter2','datagrid');
		$link1 = site_url('inventario/etiqueta_sinv/menu');
		$link2 = site_url('inventario/common/get_linea');
		$link3 = site_url('inventario/common/get_grupo');
		$script='
		$(document).ready(function(){
			$("#depto").change(function(){
				$("#objnumero").val("");
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
			depto();
			linea();
			grupo();
		});

		function depto(){
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

		$filter = new DataFilter2('Filtro por Producto');
		$filter->script($script);

		$filter->descrip = new inputField('Descripci&oacute;n', 'descrip');
		$filter->descrip->db_name='CONCAT_WS(\' \',a.descrip,a.descrip2)';
		$filter->descrip->size=25;

		$filter->depto = new dropdownField('Departamento','depto');
		$filter->depto->db_name='d.depto';
		$filter->depto->option('','Seleccione un Departamento');
		$filter->depto->options("SELECT depto, descrip FROM dpto WHERE tipo='I' ORDER BY depto");

		$filter->linea2 = new dropdownField('L&iacute;nea','linea');
		$filter->linea2->db_name='c.linea';
		$filter->linea2->option('','Seleccione un Departamento primero');

		$depto=$filter->getval('depto');
		if($depto!==false){
			$filter->linea2->options("SELECT linea, descrip FROM line WHERE depto='$depto' ORDER BY descrip");
		}else{
			$filter->linea2->option('','Seleccione un Departamento primero');
		}

		$filter->grupo = new dropdownField('Grupo', 'grupo');
		$filter->grupo->db_name='b.grupo';
		$filter->grupo->option('','Seleccione una L&iacute;nea primero');

		$linea=$filter->getval('linea2');
		if($linea!==false){
			$filter->grupo->options("SELECT grupo, nom_grup FROM grup WHERE linea='$linea' ORDER BY nom_grup");
		}else{
			$filter->grupo->option('','Seleccione un Departamento primero');
		}

		$filter->marca = new dropdownField('Marca','marca');
		$filter->marca->option('','Seleccionar');
		$filter->marca->options('SELECT TRIM(marca) AS clave, TRIM(marca) AS valor FROM marc ORDER BY marca');
		$filter->marca -> style='width:220px;';

		$filter->cant=new inputField('Cantidad de etiquetas por productos','cant');
		$filter->cant->css_class='inputnum';
		$filter->cant->insertValue='1';
		$filter->cant->clause = '';
		$filter->cant->size=8;
		$filter->cant->rule='required|numeric';
		$filter->cant->group='Configuraci&oacute;n';

		$filter->button('btn_undo', 'Regresar', 'javascript:window.location=\''.site_url('inventario/etiqueta_sinv').'\'', 'BL');
		$filter->buttons('reset','search');
		$filter->build();


		if($this->rapyd->uri->is_set('search') && $filter->is_valid()){
			$tabla=form_open('forma/ver/etiqueta1');

			$select=array(
				'a.tipo',
				'a.id',
				'a.codigo',
				'a.descrip',
				'a.precio1 AS precio',
				'a.precio2 AS precio2',
				'a.precio3 AS precio3',
				'a.barras',
				'b.nom_grup',
				'b.grupo   AS grupoid',
				'c.descrip AS nom_linea',
				'c.linea',
				'd.descrip AS nom_depto',
				'd.depto   AS depto',
				'a.pfecha1  AS cfecha',
				'a.iva'
			);

			$grid = new DataGrid('Lista de Art&iacute;culos para imprimir');
			$grid->per_page = 15;

			$grid->db->select($select);
			$grid->db->from('sinv AS a');
			$grid->db->join('grup AS b','a.grupo=b.grupo');
			$grid->db->join('line AS c','b.linea=c.linea');
			$grid->db->join('dpto AS d','c.depto=d.depto');
			$grid->db->group_by('a.codigo');

			$grid->order_by('codigo','asc');
			$grid->column_orderby('C&oacute;digo'     ,'codigo'   ,'codigo');
			$grid->column_orderby('Departamento'      ,'nom_depto','nom_depto','align=\'left\'');
			$grid->column_orderby('L&iacute;nea'      ,'nom_linea','nom_linea','align=\'left\'');
			$grid->column_orderby('Grupo'             ,'nom_grup' ,'nom_grup' ,'align=\'left\'');
			$grid->column_orderby('Descripci&oacute;n','descrip'  ,'descrip');
			$grid->column_orderby('Precio'            ,'precio'   ,'precio' ,'align=\'right\'');
			$grid->build();

			$limite=300;
			if($grid->recordCount>0 && $grid->recordCount<=$limite){
				$consul=$this->db->last_query();
				$mSQL=substr($consul,0,strpos($consul, 'LIMIT'));

				$data = array(
					'cant'  => $this->input->post('cant'),
					'consul'=> $mSQL
				);
				$tabla.=form_hidden($data);

				$tabla.=$grid->output.form_submit('mysubmit', 'Generar');
				$tabla.=form_close();
			}elseif($grid->recordCount>$limite){
				$tabla='No se puede generar habladores con  m&aacute;s de '.$limite.' &aacute;rticulos';
			}else{
				$tabla = 'No se encontrar&oacute;n productos';
			}
		}else{
			$tabla=$filter->error_string;
		}

		$data['content'] = $filter->output.$tabla;
		$data['title']   = heading('Habladores por filtro de productos');
		$data['head']    = script('jquery.pack.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function num_compra(){
		$this->rapyd->load('dataform','datagrid','dataobject','fields');
		$link1=site_url('inventario/etiqueta_sinv/menu');
		$mSCST=array(
			'tabla'   =>'scst',
			'columnas'=>array(
				'control'=>'Control',
				'numero'=>'N&uacute;mero',
				'nombre'=>'Nombre',
				'montotot'=>'Monto'
				),
			'filtro'  =>array('numero'=>'N&uacute;mero','nombre'=>'Nombre'),
			'retornar'=>array('control'=>'control'),
			'titulo'  =>'Buscar Codigo');
		$bSCST = $this->datasis->modbus($mSCST);

		$filter = new DataForm('inventario/etiqueta_sinv/num_compra/process');

		$filter->control=new inputField('N&uacute;mero de control de la compra','control');
		$filter->control->size=15;
		$filter->control->rule='required';
		$filter->control->append($bSCST);

		$filter->cant=new inputField('Cantidad de etiquetas por productos','cant');
		$filter->cant->css_class='inputnum';
		$filter->cant->insertValue='1';
		$filter->cant->size=8;
		$filter->cant->rule='required|numeric';

		$filter->button('btn_undo', 'Regresar', 'javascript:window.location=\''.site_url('inventario/etiqueta_sinv').'\'', 'BL');
		$filter->submit('btnsubmit','Consultar');
		$filter->build_form();

		if ($filter->on_success()){
			$tabla=$this->_num_compra($filter->control->newValue,$filter->cant->newValue);
		}else{
			$tabla=$filter->output;
		}

		$data['content'] = $tabla;
		$data['title']   = '<h1>Habladores por compra</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function _num_compra($control){
		$dbcontrol=$this->db->escape($control);
		$tabla=form_open('forma/ver/etiqueta1');

		$sel=array(
			'a.barras  AS barras' ,
			'a.precio2 AS precio2',
			'a.grupo   AS grupoid',
			'a.precio3 AS precio3',
			'a.codigo  AS codigo' ,
			'a.descrip AS descrip',
			'a.precio1 AS precio' ,
			'b.control AS control',
			'a.pfecha1  AS cfecha',
			'a.iva'
		);

		$grid = new DataGrid('Lista de art&iacute;culos a imprimir');
		$grid->db->select($sel);
		$grid->db->from('sinv AS a');
		$grid->db->join('itscst AS b','a.codigo=b.codigo');
		$grid->db->where('b.control',$control);

		$grid->column('C&oacute;digo'     ,'codigo' );
		$grid->column('Descripci&oacute;n','descrip');
		$grid->column('Precio'            ,'precio' ,'align=\'right\'');

		$grid->button('btn_undo', 'Regresar', 'javascript:window.location=\''.site_url('inventario/etiqueta_sinv/num_compra').'\'', 'BL');
		$grid->button('btn_gene', 'Generar' , 'javascript:this.form.submit();', 'BL');

		$grid->build();

		if($grid->recordCount>0){
			$data = array(
				'cant'  => $this->input->post('cant'),
				'consul'=>$this->db->last_query()
			);

			$tabla.=form_hidden($data);
			$tabla.=$grid->output;//.form_submit('mysubmit', 'Generar');
			$tabla.=form_close();
		}else{
			$tabla='No se consiguieron productos asociados a esa compra';
		}
		return $tabla;
	}

	function lee_barras(){
		//$this->rapyd->load("datafilter2","datagrid","dataobject","fields");
		$link1=site_url('inventario/etiqueta_sinv/menu');
		$link2=site_url('inventario/sinv/barratonombre');
		$script=script('jquery.js').script('jquery-ui.js').style('le-frog/jquery-ui-1.7.2.custom.css').
		'<script type="text/javascript">
		var propa=false;

		$(document).ready(function() {
			var acum="";
			$("#bbarras").focus();
			$("#bbarras").keydown(function(e){
				if (e.which == 13) {
					cod=$(this).val();
					if(cod.length>0){
						acum=" "+acum+cod+",";
						$(this).val("");
						$("#prod").append(cod+"<br>");
						$("input[name=\'barras\']").val(acum);
						//alert(acum);
					}
					return false;
				}
			});
		});
		</script>';

		$data = array(
			'name'        => 'bbarras',
			'id'          => 'bbarras',
			'maxlength'   => '15',
			'size'        => '15',
			'autocomplete'=>'off'
		);

		$tabla = form_open('inventario/etiqueta_sinv/cant');
		$tabla.= 'Escriba el c&oacute;digo del producto y precione la tecla  <b>ENTER</b> para agregarlo a a lista, luego presione <b>Generar</b>';
		$tabla.= form_input($data);
		$tabla.= form_hidden('barras','');
		$tabla.= HTML::button('btn_regresa', 'Regresar', 'javascript:window.location=\''.site_url('inventario/etiqueta_sinv').'\'','button','button');
		$tabla.= form_submit('mysubmit', 'Generar');
		$tabla.= form_close();
		$tabla.= '<div id=\'prod\'></div>';

		$data['content'] = $tabla;
		$data['title']   = heading('Habladores por c&oacute;digo de barras');
		$data['head']    = $script;
		$this->load->view('view_ventanas', $data);
	}

	function cant(){
		$tabla=form_open('forma/ver/etiqueta1');
		$cbarra=$this->input->post('barras');

		$regresa=HTML::button('btn_regresa', 'Regresar', 'javascript:window.location=\''.site_url('inventario/etiqueta_sinv/lee_barras').'\'','button','button');
		$campos=$nbarras=array();

		if(!empty($cbarra)){
			$barras  = array_unique(explode(',',$cbarra));

			foreach($barras as $cod){
				$cod=trim($cod);
				if(empty($cod)) continue;
				$mSQL_p = 'SELECT codigo FROM sinv';
				$bbus   = array('codigo','barras','alterno');
				$q=consultas::_gconsul($mSQL_p,$cod,$bbus);
				if($q!==false){
					$row=$q->row();
					$campos[]=$this->db->escape($row->codigo);
				}else{
					$nbarras[]=$cod;
				}
			}

			if(count($campos)>0){
				$campos = implode(',',$campos);
				$consul="SELECT codigo,barras,descrip,precio1 AS precio, precio2, precio3 ,grupo AS grupoid,pfecha1 AS cfecha, iva FROM sinv WHERE codigo IN (${campos})";

				$data = array(
					'name'      => 'cant',
					'id'        => 'cant',
					'value'     => '1',
					'maxlength' => '5',
					'size'      => '5',
					'class'     => 'inputnum',
					'autocomplete'=>'off'
				);

				if(count($nbarras)>0){
					$tabla.='<p>C&oacute;digos no relacionado con alg&uacute;n producto: '.implode(',',$nbarras).'</p>';
				}

				$tabla.=form_hidden('consul', $consul);
				$tabla.=form_label('N&uacute;mero de etiquetas por producto:').nbs(4);
				$tabla.=form_input($data).'<br>';
				$tabla.=$regresa;
				$tabla.=form_submit('mysubmit', 'Generar');
				$tabla.=form_close();
			}else{
				$tabla.=heading('Oops! No existen productos con esos c&oacute;digos de barras',3).br().$regresa;
			}
		}else{
			$tabla.=heading('Lo siento, debes ingresar alg&uacute;n c&oacute;digo de barras para poder generar los habladores',3).br().$regresa;
		}

		$data['script'] ='<script type="text/javascript">
		$(function(){
			$(".inputnum").numeric(".");
		});
		</script>';
		$data['title']  = heading('Habladores por c&oacute;digo de barras');
		$data['head']   = script('jquery.js').script('plugins/jquery.numeric.pack.js');
		$data['content']= $tabla;
		$this->load->view('view_ventanas', $data);
	}
}
