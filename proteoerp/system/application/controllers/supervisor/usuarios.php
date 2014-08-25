<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
$funciones = "";
class Usuarios extends Controller {
	var $mModulo='usuarios';
	var $titp = 'Usuarios del Sistema';
	var $tits = 'Usuarios del Sistema';
	var $url  = 'supervisor/usuarios/';

	function Usuarios(){
		parent::Controller();
		$this->datasis->modulo_id(901,1);
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
	}

	function index(){
		$this->instalar();
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
		$grid->wbotonadd(array('id'=>'camclave',   'img'=>'images/candado.png',  'alt' => 'Cambiar Clave', 'label'=>'Cambiar Clave', 'tema'=>'anexos'));
		$WestPanel = $grid->deploywestp();

		$funciones = '';

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['funciones']   = $funciones;
		$param['listados']    = $this->datasis->listados('USUARIO', 'JQ');
		$param['otros']       = $this->datasis->otros('USUARIO', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);

	}


	//*******************************
	// Body Script
	//*******************************
	function bodyscript( $grid0 ){
		$bodyscript = '<script type="text/javascript">';
		$ngrid = '#newapi'.$grid0;

		$bodyscript .= '
		function usuarioadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function usuarioedit(){
			var id     = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = $("'.$ngrid.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function usuarioshow(){
			var id     = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("'.$ngrid.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/show').'/"+id, function(data){
					$("#fshow").html(data);
					$("#fshow").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function usuariodel() {
			var id = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret    = $("'.$ngrid.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						try{
							var json = JSON.parse(data);
							if (json.status == "A"){
								apprise("Registro eliminado");
								jQuery("'.$ngrid.'").trigger("reloadGrid");
							}else{
								apprise("Registro no se puede eliminado");
							}
						}catch(e){
							$("#fborra").html(data);
							$("#fborra").dialog( "open" );
						}
					});
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';
		//Wraper de javascript
		$bodyscript .= '
		$(function(){
			$("#dialog:ui-dialog").dialog( "destroy" );
			var mId = 0;
			var montotal = 0;
			var ffecha = $("#ffecha");
			var grid = jQuery("'.$ngrid.'");
			var s;
			var allFields = $( [] ).add( ffecha );
			var tips = $( ".validateTips" );
			s = grid.getGridParam(\'selarrrow\');
			';

		$bodyscript .= '
		$("#fedita").dialog({
			autoOpen: false, height: 370, width: 480, modal: true,
			buttons: {
				"Guardar": function() {
					var murl = $("#df1").attr("action");
					allFields.removeClass( "ui-state-error" );
					$.ajax({
						type: "POST", dataType: "html", async: false,
						url: murl,
						data: $("#df1").serialize(),
						success: function(r,s,x){
							try{
								var json = JSON.parse(r);
								if (json.status == "A"){
									$.prompt("<h1>Registro Guardado</h1>",{
										submit: function(e,v,m,f){
											setTimeout(function(){ $("'.$ngrid.'").jqGrid(\'setSelection\',json.pk.id);}, 500);
										}}
									);
									$( "#fedita" ).dialog( "close" );
									grid.trigger("reloadGrid");
									idactual = json.pk.id;
									return true;
								} else {
									$.prompt(json.mensaje);
								}
							} catch(e){
								$("#fedita").html(r);
							}
						}
					})
				},
				"Cancelar": function() {
					$("#fedita").html("");
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				$("#fedita").html("");
				allFields.val( "" ).removeClass( "ui-state-error" );
			}
		});';

		$bodyscript .= '
		$("#fshow").dialog({
			autoOpen: false, height: 300, width: 400, modal: true,
			buttons: {
				"Aceptar": function() {
					$("#fshow").html("");
					$( this ).dialog( "close" );
				},
			},
			close: function() {
				$("#fshow").html("");
			}
		});';

		$bodyscript .= '
		$("#fborra").dialog({
			autoOpen: false, height: 300, width: 400, modal: true,
			buttons: {
				"Aceptar": function() {
					$("#fborra").html("");
					jQuery("'.$ngrid.'").trigger("reloadGrid");
					$( this ).dialog( "close" );
				},
			},
			close: function() {
				jQuery("'.$ngrid.'").trigger("reloadGrid");
				$("#fborra").html("");
			}
		});';

		$bodyscript .= '});'."\n";

		$bodyscript .= '
		jQuery("#camclave").click( function(){
			var id = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("'.$ngrid.'").jqGrid(\'getRowData\',id);
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
		$editar = 'false';

		// cajero
		$iscaja = $this->datasis->istabla('scaj');

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
		$grid->label('C&oacute;digo');
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

		if ($iscaja){
			
			$mSQL = "SELECT TRIM(vendedor) vendedor, CONCAT(trim(vendedor), ' ', trim(nombre)) nombre FROM vend ORDER BY vendedor ";
			$grid->addField('vendedor');
			$grid->label('Vende');
			$grid->params(array(
				'align'         => "'center'",
				'width'         => 50,
				'editable'      => $editar,
				'edittype'      => "'select'",
				'formoptions'   => '{ label:"Vendedor" }'
			));

			$mSQL = "SELECT cajero, CONCAT(cajero, ' ', nombre) nombre FROM scaj ORDER BY nombre";
			$grid->addField('cajero');
			$grid->label('Cajero');
			$grid->params(array(
				'align'    => "'center'",
				'width'         => 50,
				'editable'      => $editar,
				'edittype'      => "'select'",
				//'editoptions'   => '{ value: '.$link.', style:"width:250px "}',
			));

			$mSQL = "SELECT ubica, CONCAT(ubica, ' ', ubides) ubides FROM caub WHERE gasto='N' AND invfis='N' ORDER BY ubica ";
			$grid->addField('almacen');
			$grid->label('Almacen');
			$grid->params(array(
				'align'         => "'center'",
				'width'         => 50,
				'editable'      => $editar,
				'edittype'      => "'select'",
				//'editoptions'   => '{ value: '.$link.', style:"width:250px" }',
			));

			$grid->addField('sucursal');
			$grid->label('Sucursal');
			$grid->params(array(
				'align'    => "'center'",
				'width'         => 50,
				'editable'      => $editar,
				'edittype'      => "'select'",
			));

		}
		
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
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('uuid');
		$grid->label('Movil UUID');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:false}'
		));


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		$grid->setOndblClickRow('');
		$grid->setAdd(    $this->datasis->sidapuede('USUARIO','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('USUARIO','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('USUARIO','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('USUARIO','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: usuarioadd, editfunc: usuarioedit, delfunc: usuariodel, viewfunc: usuarioshow");

		//$grid->setGridComplete("cosas");


		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdata/'));


		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(true);
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: usuarioadd, editfunc: usuarioedit, delfunc: usuariodel, viewfunc: usuarioshow');

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

	/*******************************************************************
	* Busca la data en el Servidor por json
	*/
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('usuario');

		$response   = $grid->getData('usuario', array(array()), array(), false, $mWHERE, 'us_nombre' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/*******************************************************************
	* Guarda la Informacion
	*/
	function setData(){
	}

	function cambiaclave(){
		echo anchor( site_url('usuarios/cambiac'), img(array('src'=>'images/llave.png', 'height' => 16, 'alt'=>'Cambiar Clave', 'title'=>'Cambiar Clave', 'border'=>'0')) ) ;
	}

	//******************************************************************
	//
	//
	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});
		';

		$iscaja = $this->datasis->istabla('scaj');

		$edit = new DataEdit('', 'usuario');

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->pre_process( 'delete','_pre_delete');

		$edit->post_process('delete','_pos_delete');
		$edit->post_process('insert','_pos_insert');
		$edit->post_process('update','_pos_update');

		$edit->us_codigo = new inputField('C&oacute;digo', 'us_codigo');
		$edit->us_codigo->rule = 'strtoupper|required|unique';
		$edit->us_codigo->mode = 'autohide';
		$edit->us_codigo->size = 20;
		$edit->us_codigo->maxlength = 15;

		$edit->us_nombre = new inputField('Nombre', 'us_nombre');
		$edit->us_nombre->rule = 'strtoupper|required';
		$edit->us_nombre->size = 30;

		$edit->activo = new dropdownField('Activo', 'activo');
		$edit->activo->rule = 'required';
		$edit->activo->option('S','Si');
		$edit->activo->option('N','No');
		$edit->activo->style='width:80px';
		$edit->activo->insertValue='S';
		$edit->activo->rule='required|enum[S,N]';

		if ( $iscaja ) {

			$edit->almacen = new dropdownField('Almac&eacute;n', 'almacen');
			$edit->almacen->option('','Ninguno');
			$edit->almacen->options("SELECT TRIM(ubica) AS ubica, CONCAT_WS('-',ubica,ubides) AS descrip FROM caub ORDER BY ubica");
			$edit->almacen->rule = 'existecaub';
			$edit->almacen->style='width:180px';

			$edit->vendedor = new dropdownField('Vendedor', 'vendedor');
			$edit->vendedor->option('','Ninguno');
			$edit->vendedor->options("SELECT TRIM(vendedor) AS ven, CONCAT(vendedor,'-',nombre) AS nom FROM vend WHERE tipo IN ('V','A') ORDER BY vendedor");
			$edit->vendedor->rule = 'existevend|trim';
			$edit->vendedor->style='width:180px';

			$edit->cajero = new dropdownField('Cajero', 'cajero');
			$edit->cajero->option('','Ninguno');
			$edit->cajero->options("SELECT TRIM(cajero) cajero, CONCAT_WS('-',trim(cajero), nombre) AS descri FROM scaj ORDER BY nombre");
			$edit->cajero->rule = 'existescaj';
			$edit->cajero->style='width:180px';
		}
		
		$edit->sucursal = new dropdownField('Sucursal','sucursal');
		$edit->sucursal->option('','Ninguno');
		$edit->sucursal->options("SELECT TRIM(codigo) codigo, CONCAT(TRIM(codigo),' ',TRIM(sucursal)) sucursal FROM sucu ORDER BY codigo");
		$edit->sucursal->rule = 'existesucu';
		$edit->sucursal->style='width:180px';

		$edit->supervisor = new dropdownField('Es Supervisor', 'supervisor');
		$edit->supervisor->rule = 'required';
		$edit->supervisor->option('N','No');
		$edit->supervisor->option('S','Si');
		$edit->supervisor->insertValue='N';
		$edit->supervisor->rule='required|enum[S,N]';
		$edit->supervisor->style='width:80px';

		$edit->uuid = new inputField('Movil UUID','uuid');
		$edit->uuid->rule ='unique';
		$edit->uuid->size =32;
		$edit->uuid->maxlength =100;
		$edit->uuid->append('Solo para dispositivo movil de pedidos');

		$edit->propio = new dropdownField('Solo ver los propios', 'propio');
		$edit->propio->rule = 'required';
		$edit->propio->option('S','Si');
		$edit->propio->option('N','No');
		$edit->propio->style='width:80px';
		$edit->propio->insertValue='S';
		$edit->propio->rule='required|enum[S,N]';


		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' => 'A',
				'mensaje'=> 'Registro guardado',
				'pk'     => $edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			echo $edit->output;
		}
	}

	//******************************************************************
	//
	//
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
			return false;
		}
		return true;
	}

	function _pos_delete($do){
		$codigo  =$do->get('us_codigo');
		$dbcodigo=$this->db->escape($codigo);
		$mSQL="DELETE FROM intrasida WHERE usuario=${dbcodigo}";
		$this->db->query($mSQL);
		logusu('USUARIOS',"BORRADO EL USUARIO ${codigo}");
		return true;
	}

	function _pos_insert($do){
		$codigo=$do->get('us_codigo');
		$superv=$do->get('supervisor');
		logusu('USUARIOS',"CREADO EL USUARIO ".$this->db->escape($codigo).", SUPERVISOR '$superv'");
		return true;
	}

	function _pos_update($do){
		$codigo=$do->get('us_codigo');
		$superv=$do->get('supervisor');
		logusu('USUARIOS',"MODIFICADO EL USUARIO ".$this->db->escape($codigo).", SUPERVISOR $superv");
		return true;
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
		</form>';
		echo $salir;
	}

	function cclaveg(){
		$us_clave  = $this->input->post('us_clave');
		$us_clave1 = $this->input->post('us_clave1');
		$id        = $this->input->post('id');

		if ( $us_clave == $us_clave1) {
			$clave = $this->db->escape($us_clave);
			if ( $id > 0 ){
				$codigo = $this->datasis->dameval("SELECT us_codigo FROM usuario WHERE id=$id");
				$this->db->simple_query("UPDATE usuario SET us_clave=".$clave." WHERE id=$id");
				logusu('USUARIOS',"CAMBIO LA CLAVE DEL USUARIO $codigo");
			}
		}
		redirect($this->url.'jqdatag');
	}


	function ccclave(){
		$us_codigo =  $this->secu->usuario();
		$id        = $this->datasis->dameval('SELECT id FROM usuario WHERE us_codigo='.$this->db->escape($us_codigo));
		$us_nombre = $this->datasis->dameval("SELECT us_nombre FROM usuario WHERE id=$id");

		$salir = '
		<h2>Cambio de Clave:</h2><center><h1>'.$us_nombre.'</h1></center>
		<p id="error" style="color:red"></p>
		<form action="'.site_url('supervisor/usuarios/ccclaveg').'" method="post" id="fclave">
			<table style="margin: 0pt; width: 98%;">
				<tbody>
				<tr id="tr_us_codigo">
					<td style="width: 120px;" >Código</td>
					<td style="padding: 1px;" id="td_us_codigo">'.$us_codigo.'&nbsp;</td>
				</tr>
				<tr id="tr_us_actual">
					<td style="width: 120px;" >Clave Actual</td>
					<td style="padding: 1px;" id="td_us_actual"><input name="us_actual" value="" id="us_actual" size="12" maxlength="15" type="password">&nbsp;</td>
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
		</form>';
		echo $salir;

	}

	function ccclaveg(){
		$us_actual = $this->input->post('us_actual');

		$us_clave  = $this->input->post('us_clave');
		$us_clave1 = $this->input->post('us_clave1');

		$msg = 'Cambio Exitoso';
		$us_codigo = $this->secu->usuario();
		$clavea = $this->datasis->dameval("SELECT us_clave FROM usuario WHERE us_codigo=".$this->db->escape($us_codigo));
		$id = $this->datasis->dameval("SELECT id FROM usuario WHERE us_codigo=".$this->db->escape($us_codigo));
		if ( $clavea == $us_actual ){
			if ( $us_clave == $us_clave1) {
				$clave = $this->db->escape($us_clave);
				$codigo = $this->datasis->dameval("SELECT us_codigo FROM usuario WHERE id=${id}");
				$this->db->query("UPDATE usuario SET us_clave=${clave} WHERE id=${id}");
				logusu('USUARIOS',"El Usuario (${codigo}) cambio su clave");
			} else $msg = 'Calves no coinciden!!!';
		} else $msg = 'Calve actual incorrecta!!!';
		echo $msg;
	}


	function instalar(){
		$campos=$this->db->list_fields('usuario');
		if(!in_array('almacen',$campos)){
			$mSQL="ALTER TABLE `usuario`  ADD COLUMN `almacen` CHAR(4) NULL";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('sucursal',$campos)){
			$mSQL="ALTER TABLE `usuario`  ADD COLUMN `sucursal` CHAR(2) NULL";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('activo',$campos)){
			$mSQL="ALTER TABLE `usuario`  ADD COLUMN `activo` CHAR(1) NULL";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE usuario DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE usuario ADD UNIQUE INDEX codigo (codigo)');
			$this->db->simple_query('ALTER TABLE usuario ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};

		if(!in_array('uuid',$campos)){
			$this->db->simple_query("ALTER TABLE `usuario` ADD COLUMN `uuid` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Dispositivo movil para pedidos' AFTER `activo`");
		}

		if(!in_array('propio',$campos)){
			$this->db->simple_query("ALTER TABLE usuario ADD COLUMN `propio` CHAR(1) NULL DEFAULT 'N' COMMENT 'Solo puede modificar los registros creado por este usuario' AFTER `uuid`");
		}

		$this->db->simple_query('DELETE FROM sida USING sida LEFT JOIN usuario ON sida.usuario=usuario.us_codigo WHERE usuario.us_codigo IS NULL');
	}
}
