<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Plano extends Controller {
	var $mModulo = 'PLANO';
	var $titp    = 'Gerencia de categorías';
	var $tits    = 'Gerencia de categorías';
	var $url     = 'inventario/plano/';

	function Plano(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'PLANO', $ventana=0 );
	}

	function index(){
		$this->datasis->creaintramenu(array('modulo'=>'331','titulo'=>'Gerencia de Categorías','mensaje'=>'Gerencia de Categorías','panel'=>'REGISTROS','ejecutar'=>'inventario/plano','target'=>'popu','visible'=>'S','pertenece'=>'3','ancho'=>800,'alto'=>600));
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//******************************************************************
	//Layout en la Ventana
	//
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'desac',   'img'=>'images/candado.png' ,  'alt' => 'Cerrar localidad', 'label'=>'Cerrar'));
		$grid->wbotonadd(array('id'=>'adesa',   'img'=>'images/candado.png' ,  'alt' => 'Abrir localidad' , 'label'=>'Abrir' ));
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('PLANO', 'JQ');
		$param['otros']       = $this->datasis->otros('PLANO', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//******************************************************************
	//Funciones de los Botones
	//
	function bodyscript( $grid0 ){
		$bodyscript = '		<script type="text/javascript">';

		$bodyscript .= '
		function planoadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function planoedit(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
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
		function planoshow(){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'dataedit/show').'/"+id, function(data){
					$("#fshow").html(data);
					$("#fshow").dialog("open");
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';

		$bodyscript .= '
		function planodel() {
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
		$(function(){
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
		$("#desac").click(function (){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'cerrar').'/"+id, function(data){
					var json = JSON.parse(data);
					if(json.status!=\'A\'){
						$.prompt("<h1>"+json.msj+"</h1>");
					}else{
						$.prompt("<h1>Localidad cerrada.</h1>");
					}
					$("#newapi'.$grid0.'").trigger("reloadGrid");
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		});';

		$bodyscript .= '
		$("#adesa").click(function (){
			var id = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = $("#newapi'.$grid0.'").getRowData(id);
				mId = id;
				$.post("'.site_url($this->url.'abrir').'/"+id, function(data){
					var json = JSON.parse(data);
					if(json.status!=\'A\'){
						$.prompt("<h1>"+json.msj+"</h1>");
					}else{
						$.prompt("<h1>Localidad abierta.</h1>");
					}
					$("#newapi'.$grid0.'").trigger("reloadGrid");
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		});';

		$bodyscript .= '
		$("#fedita").dialog({
			autoOpen: false, height: 500, width: 700, modal: true,
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
									//'.$this->datasis->jwinopen(site_url('formatos/ver/PLANO').'/\'+json.pk.id+\'/id\'').';
									return true;
								} else {
									$.prompt(json.mensaje);
								}
							}catch(e){
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
			autoOpen: false, height: 500, width: 700, modal: true,
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
					jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
					$( this ).dialog( "close" );
				},
			},
			close: function() {
				jQuery("#newapi'.$grid0.'").trigger("reloadGrid");
				$("#fborra").html("");
			}
		});';

		$bodyscript .= '});';
		$bodyscript .= '</script>';
		return $bodyscript;
	}

	//******************************************************************
	// Definicion del Grid o Tabla
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));


		$grid->addField('coordenadas');
		$grid->label('Coordenadas');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));


		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('inicio');
		$grid->label('Inicio');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('final');
		$grid->label('Final');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 120,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
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

		$grid->setOndblClickRow('');		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('PLANO','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('PLANO','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('PLANO','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('PLANO','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions('addfunc: planoadd, editfunc: planoedit, delfunc: planodel, viewfunc: planoshow');

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

	//******************************************************************
	// Busca la data en el Servidor por json
	//
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('plano');

		$response   = $grid->getData('plano', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion del Grid o Tabla
	//
	function setData(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$mcodp  = '';
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			echo 'Deshabilitado';
		}elseif($oper == 'edit'){
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM plano WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM plano WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE plano SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("plano", $data);
				logusu('PLANO',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('plano', $data);
				logusu('PLANO',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		}elseif($oper == 'del'){
			echo 'Deshabilitado';
		}
	}

	//******************************************************************
	// Edicion

	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function(){
			$("#inicio").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");

			$("#codigo").autocomplete({
				delay: 600,
				autoFocus: true,
				source: function(req, add){
					$.ajax({
						url:  "'.site_url('ajax/buscasinv').'",
						type: "POST",
						dataType: "json",
						data: {"q":req.term},
						success:
							function(data){
								var sugiere = [];
								if(data.length==0){
									$("#codigo").val("");
									$("#sinvdescrip").val("");
									$("#sinvdescrip_val").text("");
								}else{
									$.each(data,
										function(i, val){
											sugiere.push(val);
										}
									);
									add(sugiere);
								}
							},
					});
				},
				minLength: 2,
				select: function( event, ui ) {
					$("#codigo").attr("readonly", "readonly");
					$("#codigo").val(ui.item.codigo);
					$("#sinvdescrip").val(ui.item.descrip);
					$("#sinvdescrip_val").text(ui.item.descrip);
					setTimeout(function(){ $("#codigo").removeAttr("readonly"); }, 1500);
				}
			});
		});';

		$do = new DataObject('plano');
		$do->pointer('sinv' ,'sinv.codigo=plano.codigo','sinv.descrip AS sinvdescrip','left');

		$edit = new DataEdit($this->tits, 'plano');

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process( 'insert', '_pre_insert');
		$edit->pre_process( 'update', '_pre_update');
		$edit->pre_process( 'delete', '_pre_delete');

		$edit->codigo = new inputField('C&oacute;digo','codigo');
		$edit->codigo->rule='required|existesinv';
		$edit->codigo->size =17;
		$edit->codigo->maxlength =15;

		$edit->sinvdescrip = new inputField('Descripci&oacute;n','sinvdescrip');
		$edit->sinvdescrip->rule='required';
		$edit->sinvdescrip->size =30;
		$edit->sinvdescrip->maxlength =50;
		$edit->sinvdescrip->type='inputhidden';
		$edit->sinvdescrip->pointer=true;

		$edit->coordenadas = new inputField('Coordenadas','coordenadas');
		$edit->coordenadas->rule='required|strtoupper';
		$edit->coordenadas->size =30;
		$edit->coordenadas->maxlength =50;

		$edit->inicio = new dateField('Fecha de Inicio','inicio');
		$edit->inicio->rule='chfecha';
		$edit->inicio->size =12;
		$edit->inicio->insertValue=date('Y-m-d');
		$edit->inicio->calendar=false;
		$edit->inicio->maxlength =8;

		$edit->container = new containerField('tabla','<div></div>');

		//$edit->final = new inputField('F. Final','final');
		//$edit->final->rule='';
		//$edit->final->size =10;
		//$edit->final->maxlength =8;

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

	function cerrar($id){
		$dbid=intval($id);
		$rt=array('status'=>'A', 'msj'=>'');
		$final=$this->datasis->dameval("SELECT final FROM plano WHERE id=${dbid}");
		if(empty($final)){
			$mSQL="UPDATE plano SET final=CURDATE() WHERE id=${dbid}";
			$ban =$this->db->simple_query($mSQL);
			$rt['msj']='Error actualizando';
		}else{
			$rt['status']='B';
			$rt['msj']   ='Localidad ya fue cerrada el día '.dbdate_to_human($final);
		}

		echo json_encode($rt);
	}

	function abrir($id){
		$dbid=intval($id);
		$rt=array('status'=>'A', 'msj'=>'');
		$final=$this->datasis->dameval("SELECT final FROM plano WHERE id=${dbid}");
		if(!empty($final)){
			$mSQL="UPDATE plano SET final=NULL WHERE id=${dbid}";
			$ban =$this->db->simple_query($mSQL);
			$rt['msj']='Error actualizando';
		}else{
			$rt['status']='B';
			$rt['msj']   ='Localidad no esta cerrada';
		}

		echo json_encode($rt);
	}

	function _pre_insert($do){
		$do->error_message_ar['pre_ins']='';
		return true;
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']='';
		return true;
	}

	function _pre_delete($do){
		$do->error_message_ar['pre_del']='';
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
		if (!$this->db->table_exists('plano')) {
			$mSQL="CREATE TABLE `plano` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`coordenadas` VARCHAR(50) NULL DEFAULT NULL,
				`codigo` VARCHAR(15) NULL DEFAULT NULL ,
				`unidades` DECIMAL(10,2) NULL DEFAULT '0.00',
				`inicio` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`final` TIMESTAMP NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				INDEX `codigo` (`codigo`)
			)
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('plano');
		//if(!in_array('<#campo#>',$campos)){ }
	}
}
