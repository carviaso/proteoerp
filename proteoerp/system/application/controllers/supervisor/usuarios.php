<?php		$funciones = "";
class Usuarios extends Controller {
	var $mModulo='Usuarios del Sistema';
	var $titp = 'Modulo de Usuarios del Sistema';
	var $tits = 'Modulo de Usuarios del Sistema';
	var $url  = 'supervisor/usuarios/';

	function Usuarios(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_id('NNN',1);
	}

	function index(){
		if ( !$this->datasis->iscampo('usuario','id') ) {
			$this->db->simple_query('ALTER TABLE usuario DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE usuario ADD UNIQUE INDEX codigo (codigo)');
			$this->db->simple_query('ALTER TABLE usuario ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};

		if(!$this->datasis->iscampo('usuario','uuid')){
			$this->db->simple_query("ALTER TABLE `usuario` ADD COLUMN `uuid` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Dispositivo movil para pedidos' AFTER `activo`");
		}
		$this->db->simple_query('DELETE FROM sida USING sida LEFT JOIN usuario ON sida.usuario=usuario.us_codigo WHERE usuario.us_codigo IS NULL ');
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		//Botones Panel Izq
		$grid->wbotonadd(array("id"=>"camclave",   "img"=>"images/candado.png",  "alt" => "Cambiar Clave", "label"=>"Cambiar Clave"));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array("id"=>"fedita",  "title"=>"Agregar/Editar Usuario"),
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$funciones = '';

		$param['WestPanel']  = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['SouthPanel'] = $SouthPanel;
		$param['funciones'] = $funciones;

		$param['listados'] = $this->datasis->listados('USUARIO', 'JQ');
		$param['otros']    = $this->datasis->otros('USUARIO', 'JQ');
		$param['tema1']     = 'darkness';
		$param['anexos']    = 'anexos1';
		$param['bodyscript'] = $bodyscript;
		$param['tabs'] = false;
		$param['encabeza'] = $this->titp;
		$this->load->view('jqgrid/crud2',$param);
	}


	//*******************************
	// Body Script
	//*******************************
	function bodyscript( $grid0 ){
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		$(function() {
			$( "input:submit, a, button", ".a1" ).button();
		});
		jQuery("#camclave").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'. $grid0.'").jqGrid(\'getRowData\',id);
				$.get(\''.base_url().'supervisor/usuarios/cclave/\'+id ,function(data){
					$.prompt(data,{
						buttons: { Guardar: true, Cancelar: false },
						focus: 1,
						submit: function(e,v,m,f){
							if ( v == true ){
								if ( f.us_clave1 == f.us_clave ){
									$(\'#fclave\').submit();
								} else {
									m.children(\'#error\').html("ERROR: Claves Diferentes!!! intente de nuevo...");
									return false;
								}
							}
						}
					});
				})
			} else {
				$.prompt("<h2>Por favor Seleccione un Usuario</h2>");}
		});
		';

		$bodyscript .= "\n</script>\n";
		$bodyscript .= "";
		return $bodyscript;
	}


	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "true";

		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'    => "'center'",
			'frozen'   => 'true',
			'width'    => 40,
			'editable' => 'false',
			'search'   => 'false'
		));

		$grid->addField('us_codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 12 }',
		));


		$grid->addField('us_nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));

		$grid->addField('activo');
		$grid->label('Activo');
		$grid->params(array(
			'align'       => "'center'",
			'width'       => 40,
			'editable'    => 'true',
			'edittype'    => "'checkbox'",
			'search'      => 'false',
			'editrules'   => '{ required:true}',
			'editoptions' => '{value: "S:N" }'
		));

		$grid->addField('supervisor');
		$grid->label('Super');
		$grid->params(array(
			'align'       => "'center'",
			'width'       => 40,
			'editable'    => 'true',
			'edittype'    => "'checkbox'",
			'search'      => 'false',
			'editrules'   => '{ required:true}',
			'editoptions' => '{value: "S:N" }',
			'formoptions'   => '{ label:"Supervisor" }'
		));

		$mSQL = "SELECT TRIM(vendedor) vendedor, CONCAT(trim(vendedor), ' ', trim(nombre)) nombre FROM vend ORDER BY vendedor ";
		$link = $this->datasis->llenajqselect($mSQL, true);
		$grid->addField('vendedor');
		$grid->label('Vende');
		$grid->params(array(
			'align'         => "'center'",
			'width'         => 50,
			'editable'      => 'true',
			'edittype'      => "'select'",
			'editoptions'   => '{ value: '.$link.', style:"width:250px "}',
			'formoptions'   => '{ label:"Vendedor" }'
		));

		$mSQL = "SELECT cajero, CONCAT(cajero, ' ', nombre) nombre FROM scaj ORDER BY nombre";
		$link = $this->datasis->llenajqselect($mSQL, true);
		$grid->addField('cajero');
		$grid->label('Cajero');
		$grid->params(array(
			'align'    => "'center'",
			'width'         => 50,
			'editable'      => 'true',
			'edittype'      => "'select'",
			'editoptions'   => '{ value: '.$link.', style:"width:250px "}',
		));

		$mSQL = "SELECT ubica, CONCAT(ubica, ' ', ubides) ubides FROM caub WHERE gasto='N' AND invfis='N' ORDER BY ubica ";
		$link = $this->datasis->llenajqselect($mSQL, true);
		$grid->addField('almacen');
		$grid->label('Almacen');
		$grid->params(array(
			'align'         => "'center'",
			'width'         => 50,
			'editable'      => 'true',
			'edittype'      => "'select'",
			'editoptions'   => '{ value: '.$link.', style:"width:250px" }',
		));

		$mSQL = "SELECT TRIM(codigo) codigo, CONCAT(TRIM(codigo),' ',TRIM(sucursal)) sucursal FROM sucu ORDER BY codigo";
		$link = $this->datasis->llenajqselect($mSQL, true);
		$grid->addField('sucursal');
		$grid->label('Sucursal');
		$grid->params(array(
			'align'    => "'center'",
			'width'         => 50,
			'editable'      => 'true',
			'edittype'      => "'select'",
			'editoptions'   => '{ value: '.$link.', style:"width:250px" }',
		));

		$grid->addField('us_clave');
		$grid->label('Clave');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'edittype'      => "'password'",
			'hidden'        => 'true',
			'editrules'     => '{edithidden:true, required:true}'
		));

		$grid->addField('us_fechae');
		$grid->label('Entrada');
		$grid->params(array(
			'search'        => 'false',
			'editable'      => 'false',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('uuid');
		$grid->label('Movil');
		$grid->params(array(
			'search'        => 'false',
			'editable'      => 'true',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}'
		));

/*
		$grid->addField('us_horae');
		$grid->label('Hora Entr');
		$grid->params(array(
			'search'        => 'false',
			'editable'      => 'false',
			'width'         => 60,
			'edittype'      => "'text'",
		));


		$grid->addField('us_fechas');
		$grid->label('Salida');
		$grid->params(array(
			'search'        => 'false',
			'editable'      => 'false',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('us_horas');
		$grid->label('Hora Sal');
		$grid->params(array(
			'search'        => 'false',
			'editable'      => 'false',
			'width'         => 60,
			'edittype'      => "'text'",
		));
*/

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('
			closeAfterEdit:false,
			mtype: "POST",
			width: 400,
			height:340,
			closeOnEscape: true,
			top: 50,
			left:20,
			recreateForm:true,
			afterSubmit: function(a,b){
				if (a.responseText.length > 0)
					$.prompt(a.responseText);
				return [true, a ];
				},
			beforeShowForm: function(frm){
					$(\'#us_codigo\').attr(\'readonly\',\'readonly\');
				},
			afterShowForm: function(frm){
					$("select").selectmenu({style:"popup"});
				}

		');
		$grid->setFormOptionsA('
			closeAfterAdd:true,
			mtype: "POST",
			width: 400,
			height:340,
			closeOnEscape: true,
			top: 50,
			left:20,
			recreateForm:true,
			afterSubmit: function(a,b){
				if (a.responseText.length > 0)
					$.prompt(a.responseText);
				return [true, a ];
			},
			afterShowForm: function(frm){
					$("select").selectmenu({style:"popup"});
				}

		');

		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(true);
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdata/'));

		if ($deployed) {
			return $grid->deploy();
		} else {
			return $grid;
		}
	}

	/**
	* Busca la data en el Servidor por json
	*/
	function getdata()
	{
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('usuario');

		$response   = $grid->getData('usuario', array(array()), array(), false, $mWHERE, 'us_nombre' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData()
	{
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$this->db->insert('usuario', $data);
			}
			echo "Registro Agregado";

		} elseif($oper == 'edit') {
			unset($data['us_codigo']);
			$this->db->where('id', $id);
			$this->db->update('usuario', $data);
			echo "Registro Modificado";

		} elseif($oper == 'del') {
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM usuario WHERE id='$id' ");
			$us_codigo = $data['us_codigo'];
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query('DELETE FROM usuario WHERE id='.$id );
				$this->db->simple_query('DELETE FROM sida USING sida LEFT JOIN usuario ON sida.usuario=usuario.us_codigo WHERE usuario.us_codigo IS NULL ');
				logusu('USUARIO','Registro '.$this->db->escape($us_codigo).' ELIMINADO');
				echo "Registro Eliminado";
			}
		}
	}

	function cambiaclave(){
		echo anchor(site_url('usuarios/cambiac'), img('src'=>'images/llave.png') ) ;
	}


	function 

/*
class Usuarios extends Controller {

	function Usuarios(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('menues');
		$this->datasis->modulo_id(906,1);
	}

	function index(){
		redirect('supervisor/usuarios/filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');
		$this->rapyd->uri->keep_persistence();

		$filter = new DataFilter('Filtro de Usuarios');
		$filter->db->select("a.us_codigo,a.us_nombre,a.supervisor,a.almacen,a.vendedor,a.cajero,
							c.nombre as vendnom,b.ubides as almdes,d.nombre as cajnom");
		$filter->db->from('usuario AS a');
		$filter->db->join('caub AS b','b.ubica=a.almacen'    ,'left');
		$filter->db->join('vend AS c','c.vendedor=a.vendedor','left');
		$filter->db->join('scaj AS d','d.cajero=a.cajero'    ,'left');

		$filter->us_codigo = new inputField('C&oacute;digo Usuario', 'us_codigo');
		$filter->us_codigo->size=15;

		$filter->us_nombre = new inputField('Nombre', 'us_nombre');
		$filter->us_nombre->size=15;

		$filter->buttons('reset','search');
		$filter->build();

		$uri  = anchor('supervisor/usuarios/dataedit/show/<#us_codigo#>','<#us_codigo#>');
		$uri2 = anchor('supervisor/usuarios/cclave/modify/<#us_codigo#>','Cambiar clave');
		$uri3 = anchor('supervisor/usuarios/accesos/<#us_codigo#>','Asignar Accesos');

		$grid = new DataGrid('Lista de Usuarios');
		$grid->order_by('us_codigo','asc');
		$grid->per_page = 10;

		$grid->column_orderby('C&oacute;digo', $uri,'us_codigo' );
		$grid->column_orderby('Nombre','us_nombre' ,'us_nombre' );
		$grid->column_orderby('Supervisor'         ,'supervisor' ,'supervisor','align="center"');
		$grid->column_orderby('Almac&eacute;n'     ,'almdes'     ,'almdes','align=\'left\'');
		$grid->column_orderby('Vendedor'           ,'<#vendedor#>-<#vendnom#>','vendedor','align=\'center\'');
		$grid->column_orderby('Cajero'             ,'<#cajero#>-<#cajnom#>','cajero'     ,'align=\'center\'');
		$grid->column('Cambio clave'   ,$uri2      ,'align="center"');

		$grid->add('supervisor/usuarios/dataedit/create','Crear un nuevo usuario');
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = heading('Usuarios');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load('dataedit');
		$this->rapyd->uri->keep_persistence();

		$edit = new DataEdit('Usuarios', 'usuario');
		$edit->back_url = site_url('supervisor/usuarios/filteredgrid');

		$edit->pre_process( 'delete','_pre_delete');
		$edit->post_process('delete','_pos_delete');
		$edit->post_process('insert','_pos_insert');
		$edit->post_process('update','_pos_update');

		$edit->us_codigo = new inputField('C&oacute;digo de Usuario', 'us_codigo');
		$edit->us_codigo->rule = 'strtoupper|required';
		$edit->us_codigo->mode = 'autohide';
		$edit->us_codigo->size = 20;
		$edit->us_codigo->maxlength = 15;

		$edit->us_nombre = new inputField('Nombre', 'us_nombre');
		$edit->us_nombre->rule = 'strtoupper|required';
		$edit->us_nombre->size = 45;

		$edit->activo = new dropdownField('Activo', 'activo');
		$edit->activo->rule = 'required';
		$edit->activo->option('S','Si');
		$edit->activo->option('N','No');
		$edit->activo->style='width:80px';

		$edit->almacen = new dropdownField('Almac&eacute;n', 'almacen');
		$edit->almacen->option('','Ninguno');
		$edit->almacen->options("SELECT ubica, CONCAT_WS('-',ubica,ubides) AS descrip FROM caub ORDER BY ubica");

		$edit->vendedor = new dropdownField('Vendedor', 'vendedor');
		$edit->vendedor->option('','Ninguno');
		$edit->vendedor->options("SELECT vendedor, CONCAT(vendedor,'-',nombre) AS nom FROM vend WHERE tipo IN ('V','A') ORDER BY vendedor");

		$edit->cajero = new dropdownField('Cajero', 'cajero');
		$edit->cajero->option('','Ninguno');
		$edit->cajero->options("SELECT cajero,CONCAT_WS('-',cajero, nombre) AS descri FROM scaj ORDER BY nombre");

		$edit->supervisor = new dropdownField('Es Supervisor', 'supervisor');
		$edit->supervisor->rule = 'required';
		$edit->supervisor->option('N','No');
		$edit->supervisor->option('S','Si');
		$edit->supervisor->style='width:80px';

		$edit->buttons('modify', 'save', 'undo', 'back','delete');
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = heading('Usuarios');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function accesos($usr){
		$this->rapyd->load('datagrid2');
		$mSQL="SELECT a.modulo,a.titulo, IFNULL(b.acceso,'N') AS acceso,a.panel,MID(a.modulo,1,1) AS pertenece
			FROM intramenu AS a
			LEFT JOIN intrasida AS b ON a.modulo=b.modulo AND b.usuario='$usr'
			WHERE MID(a.modulo,1,1)!=0 ORDER BY MID(a.modulo,1,1), a.panel,a.modulo";
		$select=array('a.modulo','a.titulo', "IFNULL(b.acceso,'N') AS acceso",'a.panel',"MID(a.modulo,1,1) AS pertenece");

		$mc = $this->db->query($mSQL);
		$tabla=form_open('accesos/guardar').form_hidden('usuario',$usr).'<div id=\'ContenedoresDeData\'><table width=100% cellspacing="0">';
		$i=0;
		$panel = '';
		foreach( $mc->result() as $row ){
			if(strlen($row->modulo)==1) {
				$tabla .= '<tr><th colspan=2>'.$row->titulo.'</th></tr>';
				$panel = '';
			}

			elseif( strlen($row->modulo)==3 ) {
				if ($panel <> $row->panel ) {
				    $tabla .= '<tr><td colspan=2 bgcolor="#CCDDCC">'.$row->panel.'</td></tr>';
				    $panel = $row->panel ;
				};

				$tabla .= '<tr><td>'.$row->titulo.'</td><td>'.form_checkbox('accesos['.$i.']',$row->modulo,$row->acceso).'</td></tr>';
				$i++;
			}else{
				$tabla .= '<tr><td><b>&nbsp;&nbsp;-&nbsp;</b>'.$row->titulo.'</td><td>'.form_checkbox('accesos['.$i.']',$row->modulo,$row->acceso).'</td></tr>';
				$i++;
			}
		}
		$tabla.='</table></div>';
		$tabla.=form_hidden('usuario',$usr).form_submit('pasa','Guardar').form_close();

		$data['content'] = $tabla;
		$data['title']   = heading('Asignar Accesos');
		$data['head']    = style('estilos.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function _pre_delete($do) {
		$codigo=$do->get('us_codigo');
		if ($codigo==$this->session->userdata('usuario')){
			$do->error_message_ar['pre_del'] = 'No se puede borrar usted mismo';
			return FALSE;
		}
		return TRUE;
	}

	function _pos_delete($do){
		$codigo=$do->get('us_codigo');
		$mSQL="DELETE FROM intrasida WHERE usuario='$codigo'";
		$this->db->query($mSQL);
		logusu('USUARIOS',"BORRADO EL USUARIO $codigo");
		return TRUE;
	}

	function _pos_insert($do){
		$codigo=$do->get('us_codigo');
		$superv=$do->get('supervisor');
		logusu('USUARIOS',"CREADO EL USUARIO $codigo, SUPERVISOR $superv");
		redirect("supervisor/usuarios/cclave/modify/$codigo");
		return TRUE;
	}

	function _pos_update($do){
		$codigo=$do->get('us_codigo');
		$superv=$do->get('supervisor');
		logusu('USUARIOS',"MODIFICADO EL USUARIO $codigo, SUPERVISOR $superv");
		return TRUE;
	}

*/
	function soporte(){
		$mSQL="INSERT INTO `usuario` (`us_codigo`, `us_nombre`, `us_clave`,`supervisor`) VALUES ('SOPORTE', 'PERS. DREMANVA', 'DREMANVA','S');";
		$this->db->simple_query($mSQL);
	}

	function instalar(){
		if ( !$this->datasis->iscampo('usuario','almacen') ) {
			$mSQL="ALTER TABLE `usuario`  ADD COLUMN `almacen` CHAR(4) NULL";
			$this->db->simple_query($mSQL);
			echo "Agregado campo almacen";
		}
		if ( !$this->datasis->iscampo('usuario','sucursal') ) {
			$mSQL="ALTER TABLE `usuario`  ADD COLUMN `sucursal` CHAR(2) NULL";
			$this->db->simple_query($mSQL);
			echo "Agregado campo sucursal";
		}
		if ( !$this->datasis->iscampo('usuario','activo') ) {
			$mSQL="ALTER TABLE `usuario`  ADD COLUMN `activo` CHAR(1) NULL";
			$this->db->simple_query($mSQL);
			echo "Agregado campo activo";
		}
	}


	function cclave(){
		$id     = $this->uri->segment($this->uri->total_segments());

		$us_codigo = $this->datasis->dameval("SELECT us_codigo FROM usuario WHERE id=$id");
		$us_nombre = $this->datasis->dameval("SELECT us_nombre FROM usuario WHERE id=$id");

		$salir = '
<h2>Cambio de Clave:</h2><center><h1>'.$us_nombre.'</h1></center>
<p id="error" style="color:red"></p>
<form action="'.base_url().'supervisor/usuarios/cclaveg" method="post" id="fclave">
	<table style="margin: 0pt; width: 98%;">
		<tbody>
		<tr id="tr_us_codigo">
			<td style="width: 120px;" >Código</td>
			<td style="padding: 1px;" id="td_us_codigo">'.$us_codigo.'&nbsp;</td>
		</tr>
		<tr id="tr_us_clave">
			<td style="width: 120px;" >Clave*</td>
			<td style="padding: 1px;" id="td_us_clave"><input name="us_clave" value="" id="us_clave" size="12" maxlength="15" type="password">&nbsp;</td>
		</tr>
		<tr id="tr_us_clave1">
			<td style="width: 120px;">Confirmar*</td>
			<td style="padding: 1px;" id="td_us_clave1"><input name="us_clave1" value="" id="us_clave1" size="12" maxlength="15" type="password">&nbsp;</td>
		</tr>
		</tbody>
	</table>
	<input name="id" value="'.$id.'" id="id" type="hidden">
</form>
';
		echo $salir;

	}

	function cclaveg(){
		$us_clave  = $this->input->post('us_clave');
		$us_clave1 = $this->input->post('us_clave1');
		$id        = $this->input->post('id');
		if ( $us_clave == $us_clave1) {
			$clave = $this->db->escape($us_clave);
			if ( $id > 0)
				$this->db->simple_query("UPDATE usuario SET us_clave=".$clave." WHERE id=$id");
		}
		redirect($this->url.'jqdatag');
	}

	function _pos_updatec($do){
		$codigo=$do->get('us_codigo');
		$superv=$do->get('supervisor');
		logusu('USUARIOS',"CAMBIO DE CLAVE DEL USUARIO $codigo");
		return true;
	}
}
?>
