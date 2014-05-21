<?php
/** 
 * ProteoERP 
 * 
 * @autor    Andres Hocevar 
 * @license  GNU GPL v3
*/
class Lvaca extends Controller {
	var $mModulo = 'LVACA';
	var $titp    = 'Vaqueras';
	var $tits    = 'Vaqueras';
	var $url     = 'leche/lvaca/';

	function Lvaca(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'LVACA', $ventana=0 );
	}

	function index(){
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		$this->datasis->creaintramenu( $data = array('modulo'=>'222','titulo'=>'Vaqueras','mensaje'=>'Vaqueras','panel'=>'LECHE','ejecutar'=>'leche/lvaca','target'=>'popu','visible'=>'S','pertenece'=>'2','ancho'=>900,'alto'=>600));
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
		$grid->wbotonadd(array("id"=>"recibo",   "img"=>"assets/default/images/print.png",  "alt" => "Imprimir", "label"=>"Imprimir"));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita', 'title'=>'Agregar/Editar Vaquera' ),
			array('id'=>'fshow' , 'title'=>'Mostrar Vaquera'),
			array('id'=>'fborra', 'title'=>'Agregar/Editar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('LVACA', 'JQ');
		$param['otros']       = $this->datasis->otros('LVACA', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//***************************
	//Funciones de los Botones
	//***************************
	function bodyscript( $grid0 ){
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		function lvacaadd() {
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function lvacaedit() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				});
			} else { $.prompt("<h1>Por favor Seleccione un Registro</h1>");}
		};';

		$bodyscript .= '
		function lvacashow(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
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
		function lvacadel() {
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret    = $("#newapi'.$grid0.'").getRowData(id);
					mId = id;
					$.post("'.site_url($this->url.'dataedit/do_delete').'/"+id, function(data){
						try{
							var json = JSON.parse(data);
							if (json.status == "A"){
								apprise("Registro eliminado");
								jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
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
		$(function() {
			$("#dialog:ui-dialog").dialog( "destroy" );
			var mId = 0;
			var montotal = 0;
			var ffecha = $("#ffecha");
			var grid = jQuery("#newapi'.$grid0.'");
			var s;
			var allFields = $( [] ).add( ffecha );
			var tips = $( ".validateTips" );
			s = grid.getGridParam(\'selarrrow\');
			';

		$bodyscript .= '
		jQuery("#recibo").click( function(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.site_url('formatos/ver/APANCO').'/\'+id, \'_blank\', \'width=900,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
			} else { $.prompt("<h1>Por favor Seleccione una vaquera</h1>");}
		});';


		$bodyscript .= '
		$("#fedita").dialog({
			autoOpen: false, height: 360, width: 550, modal: true,
			buttons: {
			"Guardar": function() {
				var bValid = true;
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
								apprise("Registro Guardado");
								$( "#fedita" ).dialog( "close" );
								grid.trigger("reloadGrid");
								//'.$this->datasis->jwinopen(site_url('formatos/ver/LVACA').'/\'+res.id+\'/id\'').';
								return true;
							} else {
								apprise(json.mensaje);
							}
						}catch(e){
							$("#fedita").html(r);
						}
					}
			})},
			"Cancelar":
				function() {
					$( this ).dialog( "close" );
					$("#fedita").html("");
				}

			},
			close: function() {
				allFields.val( "" ).removeClass( "ui-state-error" );
				$("#fedita").html("");
			}
		});';

		$bodyscript .= '
		$("#fshow").dialog({
			autoOpen: false, height: 300, width: 500, modal: true,
			buttons: {
				"Aceptar": function() {
					$("#fshow").html("");
					$(this).dialog("close");
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
					jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
					$( this ).dialog( "close" );
				},
			},
			close: function() {
				jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
				$("#fborra").html("");
			}
		});';

		$bodyscript .= '});'."\n";

		$bodyscript .= '</script>';
		return $bodyscript;
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 10 }',
		));

		$grid->addField('nombre');
		$grid->label('Vaquera Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));

		$grid->addField('ruta');
		$grid->label('Ruta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));

		$grid->addField('zona');
		$grid->label('Zona');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:4, maxlength: 4 }',
		));

		$grid->addField('ubicacion');
		$grid->label('Ubicaci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:200, maxlength: 200 }',
		));

		$grid->addField('animal');
		$grid->label('Animal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));

		$grid->addField('tipolec');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('codprv');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));

		$grid->addField('proveed');
		$grid->label('Propietario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:45, maxlength: 45 }',
		));

		$grid->addField('finca');
		$grid->label('Finca');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow(' function(id){
			if (id){
				var ret = jQuery(gridId1).jqGrid(\'getRowData\',id);
				$(gridId1).jqGrid("setCaption", ret.nombre);
				$.ajax({
					url: "'.site_url($this->url).'/resumen/"+id,
					success: function(msg){
						$("#ladicional").html(msg);
					}
				});
			}
		}');


		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('LVACA','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('LVACA','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('LVACA','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('LVACA','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: lvacaadd, editfunc: lvacaedit, delfunc: lvacadel, viewfunc: lvacashow");

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


	function resumen(){

		$mSQL = '
		SELECT LPAD(a.id,5,"0") numero, IF(b.transporte>0,DATE_SUB(b.fecha, INTERVAL 1 DAY),b.fecha) AS fecha, b.ruta, c.codigo, c.nombre nomvaca, e.ultimo leche, f.ultimo frio, g.ultimo grasa, h.ultimo bacter, d.proveed, d.nombre, a.lista, d.banco1, d.cuenta1, a.lista monto, a.dtoagua, a.temp, ROUND(a.lista*e.ultimo,2) monto, ROUND(a.lista*(f.ultimo+g.ultimo+h.ultimo)*(c.tipolec="F"),2) incent, (f.ultimo+g.ultimo+h.ultimo) pincent, ROUND(a.lista*e.ultimo,2)+ROUND(a.lista*(f.ultimo+g.ultimo+h.ultimo)*(c.tipolec="F"),2) total, ROUND(a.lista*IF(c.animal="B",i.ultimo, 0 ),2) bufala, ROUND(a.lista*e.ultimo,2)+ROUND(a.lista*(f.ultimo+g.ultimo+h.ultimo)*(c.tipolec="F")+ROUND(a.lista*IF(c.animal="B",i.ultimo, 0 ),2),2) gtotal
		FROM (itlrece AS a)
		JOIN lrece AS b ON a.id_lrece=b.id
		JOIN lvaca AS c ON a.id_lvaca=c.id
		LEFT JOIN sprv AS d ON c.codprv=d.proveed
		LEFT JOIN sinv e ON e.codigo="ZLCALIENTE"
		LEFT JOIN sinv f ON f.codigo="ZMANFRIO"
		LEFT JOIN sinv g ON g.codigo="ZPGRASA"
		LEFT JOIN sinv h ON h.codigo="ZBACTE"
		LEFT JOIN sinv i ON i.codigo="ZBUFALA"
		WHERE a.lista > 0 AND MID(b.ruta,1,1) <> "G" AND (b.fecha BETWEEN "2013-01-28" AND "2013-02-03" AND b.transporte<=0) OR (b.fecha BETWEEN "2013-01-29" AND "2013-02-04" AND b.transporte>0)
		UNION ALL
		SELECT referen numero, fecha, "XXXX" ruta, "XXXX" codigo, "GATOS Y DEDUCCIONES" nomvaca, 0 leche, 0 frio, 0 grasa, 0 bacter, a.proveed, a.nombre, 0, b.banco1, b.cuenta1, 0 monto, 0 dtoagua, 0 temp, 0 monto, 0 incent, 0 pincent, -a.total, 0 bufala, -a.total gtotal
		FROM lgasto a JOIN sprv b ON a.proveed=b.proveed
		WHERE a.pago=0
		ORDER BY proveed, codigo';


	}

	/**
	* Busca la data en el Servidor por json
	*/
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('lvaca');

		$response   = $grid->getData('lvaca', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$mcodp  = "??????";
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM lvaca WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('lvaca', $data);
					echo "Registro Agregado";

					logusu('LVACA',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM lvaca WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM lvaca WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE lvaca SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("lvaca", $data);
				logusu('LVACA',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('lvaca', $data);
				logusu('LVACA',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM lvaca WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM lvaca WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM lvaca WHERE id=$id ");
				logusu('LVACA',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$script='
		$(document).ready(function() {
			$("#codprv").autocomplete({
				source: function( req, add){
					$.ajax({
						url:  "'.site_url('ajax/buscasprv').'",
						type: "POST",
						dataType: "json",
						data: "q="+req.term,
						success:
							function(data){
								var sugiere = [];
								if(data.length==0){
									$("#proveed").val("");
									$("#proveed_val").text("");
									$("#codprv").val("");
								}else{
									$.each(data,
										function(i, val){
											sugiere.push( val );
										}
									);
								}
								add(sugiere);
							},
					})
				},
				minLength: 2,
				select: function( event, ui ) {
					$("#codprv").attr("readonly", "readonly");

					$("#proveed").val(ui.item.nombre);
					$("#proveed_val").text(ui.item.nombre);
					$("#codprv").val(ui.item.proveed);

					setTimeout(function(){ $("#codprv").removeAttr("readonly"); }, 1500);
				}
			});
		});';

		$edit = new DataEdit('', 'lvaca');
		$edit->on_save_redirect=false;
		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert','_pre_insert');
		$edit->pre_process( 'update','_pre_update');
		$edit->pre_process( 'delete','_pre_delete');

		$edit->codigo = new inputField('C&oacute;digo','codigo');
		$edit->codigo->rule='max_length[10]';
		$edit->codigo->size =12;
		$edit->codigo->maxlength =10;
		$edit->codigo->mode = 'autohide';

		$edit->nombre = new inputField('Vaquera Nombre','nombre');
		$edit->nombre->rule='max_length[100]';
		$edit->nombre->maxlength =100;

		$edit->finca = new inputField('Finca','finca');
		$edit->finca->rule='max_length[100]';
		$edit->finca->maxlength =100;

		$edit->ruta = new dropdownField('Ruta', 'ruta');
		$edit->ruta->rule = 'trim';
		$edit->ruta->option('','Seleccionar');
		$edit->ruta->options('SELECT codigo, CONCAT(codigo," ", nombre) nombre FROM lruta ORDER BY nombre');
		$edit->ruta->style = 'width:166px';

		$edit->zona = new dropdownField('Zona', 'zona');
		$edit->zona->rule = 'trim|max_length[4]';
		$edit->zona->option('','Seleccionar');
		$edit->zona->options('SELECT codigo, CONCAT(codigo," ", nombre) nombre FROM zona ORDER BY nombre');
		$edit->zona->style = 'width:166px';

		$edit->ubicacion = new inputField('Ubicaci&oacute;n','ubicacion');
		$edit->ubicacion->rule='max_length[200]';
		$edit->ubicacion->maxlength =200;

		$edit->animal = new  dropdownField ('Animal', 'animal');
		//$edit->animal->option('M' ,'Mezcla');
		$edit->animal->option('V' ,'Vaca');
		$edit->animal->option('B' ,'Bufala');
		$edit->animal->rule = 'required';
		$edit->animal->style= 'width:100px;';

		$edit->tipolec = new  dropdownField ('Tipo', 'tipolec');
		$edit->tipolec->option('C' ,'Caliente');
		$edit->tipolec->option('F' ,'Fria');
		$edit->tipolec->rule = 'required';
		$edit->tipolec->style= 'width:100px;';

		$edit->codprv = new inputField('Propietario','codprv');
		$edit->codprv->rule='max_length[15]';
		$edit->codprv->size =7;
		$edit->codprv->maxlength =15;

		$edit->proveed = new inputField('Proveed','proveed');
		$edit->proveed->size =47;
		$edit->proveed->type='inputhidden';
		$edit->proveed->maxlength =45;
		$edit->proveed->in ='codprv';

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);

			echo json_encode($rt);
		}else{
			echo $edit->output;
		}
	}

	function _pre_insert($do){
		return true;
	}

	function _pre_update($do){
		return true;
	}

	function _pre_delete($do){
		$id_lvaca = $this->db->escape($do->get('id'));
		$check    = $this->datasis->dameval('SELECT COUNT(*) AS cana FROM itlrece WHERE  lista > 0 AND id_lvaca='.$id_lvaca);

		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Vaquera con recepcion, no puede ser borrada';
			return false;
		}

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
		$id_lvaca = $this->db->escape($do->get('id'));
		$mSQL='DELETE FROM itlrece WHERE  id_lvaca='.$id_lvaca;
		$this->db->simple_query($mSQL);

		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}
}
