<?php
/**
* ProteoERP
*
* @autor    Andres Hocevar
* @license  GNU GPL v3
*/
class Edgasto extends Controller {
	var $mModulo = 'EDGASTO';
	var $titp    = 'GASTOS DE CONDOMINIO';
	var $tits    = 'GASTOS DE CONDOMINIO';
	var $url     = 'construccion/edgasto/';

	function Edgasto(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'EDGASTO', $ventana=0 );
	}

	function index(){
		//$this->datasis->creaintramenu(array('modulo'=>'000','titulo'=>'<#titulo#>','mensaje'=>'<#mensaje#>','panel'=>'<#panal#>','ejecutar'=>'<#ejecuta#>','target'=>'popu','visible'=>'S','pertenece'=>'<#pertenece#>','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//******************************************************************
	// Layout en la Ventana
	//
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		//Botones Panel Izq
		$grid->wbotonadd(array("id"=>"edtraegas",   "img"=>"images/engrana.png",  "alt" => "Traer Gastos", "label"=>"Traer Gastos"));
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
		$param['listados']    = $this->datasis->listados('EDGASTO', 'JQ');
		$param['otros']       = $this->datasis->otros('EDGASTO', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//******************************************************************
	// Funciones de los Botones
	//
	function bodyscript( $grid0 ){
		$bodyscript = '<script type="text/javascript">';
		$ngrid = '#newapi'.$grid0;

		$ano1 = date('Y',mktime(0,0,0,date('m'),date('d'),date('Y')));
		$ano2 = date('Y',mktime(0,0,0,date('m'),date('d'),date('Y')-1));
		$ano3 = date('Y',mktime(0,0,0,date('m'),date('d'),date('Y')-2));
		$mano = '<select id=\'mano\' name=\'mano\'><option value=\''.$ano1.'\'>'.$ano1.'</option><option value=\''.$ano2.'\'>'.$ano2.'</option><option value=\''.$ano3.'\'>'.$ano3.'</option></select>';
		$mes  = '<select id=\'mmes\' name=\'mmes\'><option value=\'01\'>01</option><option value=\'02\'>02</option><option value=\'03\'>03</option><option value=\'04\'>04</option><option value=\'05\'>05</option><option value=\'06\'>06</option><option value=\'07\'>07</option><option value=\'08\'>08</option><option value=\'09\'>09</option><option value=\'10\'>10</option><option value=\'11\'>11</option><option value=\'12\'>12</option></select>';

		$bodyscript .= '
		$("#edtraegas").click(function(){
			var meco = "";
			var mgene = {
			state0: {
				html:"<h1>Traer Gastos: </h1><br/><center>Fecha: '.$mano.'&nbsp; Mes: '.$mes.'</center><br/>",
				buttons: { Cancelar: false, Aceptar: true },
				focus: 1,
				submit:function(e,v,m,f){
					if(v){
						e.preventDefault();
						$.ajax({
							url: \''.site_url('construccion/edgasto/edtraegas').'\',
							global: false,
							type: "POST",
							data: ({ anomes : encodeURIComponent(f.mano+f.mmes) }),
							dataType: "text",
							async: false,
							success: function(sino) {
								meco = " sino="+sino;
								if (sino.substring(0,1)=="S"){
									$.prompt.goToState("state1");
								} else {
									$.prompt.close();
								}
							},
							error: function(h,t,e) { alert("Error.. ",e) }
						});
						return false;
					}
				}
			},
			state1: {
				html:"Gastos transferidos!"+meco,
				buttons: { Regresar: -1, Salir: 0 },
				focus: 1,
				submit:function(e,v,m,f){
					e.preventDefault();
					if(v==0)
						$.prompt.close();
					else if(v==-1)
						$.prompt.goToState("state0");
				}
			}
			};
			$.prompt(mgene);
		});';

		$bodyscript .= $this->jqdatagrid->bsshow('edgasto', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'edgasto', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'edgasto', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('edgasto', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '350', '500' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '300', '400' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '300', '400' );

		$bodyscript .= '});';

		$bodyscript .= '</script>';

		return $bodyscript;
	}

	//******************************************************************
	//  Trae Gastos
	//
	function edtraegas($anomes = 0){
		if ( $anomes == 0 ) $anomes = $this->input->post('anomes');
		if ( $anomes <= 0  ) {
			echo 'No se Guardo '.$anomes;
			return false;
		}
		$dbanomes = $this->db->escape($anomes);
		//Genera los recibos
		$mSQL = "CALL sp_edgasto(${dbanomes})";
		$this->db->query($mSQL);
		echo "Si Ejecutado CALL sp_edgasto(${dbanomes})";

	}


	//******************************************************************
	// Definicion del Grid o Tabla 
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'         => "'center'",
			'frozen'        => 'true',
			'hidden'        => 'true',
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));


		$grid->addField('tipo_doc');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'align'         => "'center'",
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));


		$grid->addField('numero');
		$grid->label('Numero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));


		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('causado');
		$grid->label('Causado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('proveed');
		$grid->label('Proveed');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('detalle');
		$grid->label('Detalle');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('base');
		$grid->label('Base');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('iva');
		$grid->label('Iva');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));


		$grid->addField('total');
		$grid->label('Total');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('partida');
		$grid->label('Partida');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 40,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));

		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'align'         => "'center'",
			'width'         => 50,
			'edittype'      => "'textarea'",
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
		$grid->setAdd(    $this->datasis->sidapuede('EDGASTO','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('EDGASTO','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('EDGASTO','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('EDGASTO','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: edgastoadd, editfunc: edgastoedit, delfunc: edgastodel, viewfunc: edgastoshow");

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
		$mWHERE = $grid->geneTopWhere('edgasto');

		$response   = $grid->getData('edgasto', array(array()), array(), false, $mWHERE );
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
		$mcodp  = "??????";
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$check = $this->datasis->dameval("SELECT count(*) FROM edgasto WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('edgasto', $data);
					echo "Registro Agregado";
					logusu('EDGASTO',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM edgasto WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM edgasto WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE edgasto SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("edgasto", $data);
				logusu('EDGASTO',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('edgasto', $data);
				logusu('EDGASTO',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM edgasto WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM edgasto WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->query("DELETE FROM edgasto WHERE id=$id ");
				logusu('EDGASTO',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//******************************************************************
	// Edicion 
	function dataedit(){
		$this->rapyd->load('dataedit');
		$script = '
		$(function() {
			$("#fecha").datepicker(  {dateFormat:"dd/mm/yy"});
			$("#causado").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});
		';

		$script .= '
		$("#proveed").autocomplete({
			delay: 600,
			autoFocus: true,
			source: function( req, add){
			$.ajax({
				url: "'.site_url('ajax/buscasprv').'",
				type: "POST",
				dataType: "json",
				data: {"q":req.term},
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$("#proveedor").val("");
							$("#proveedor_val").text("");
							$("#proveed").val("");
							$("#sprvreteiva").val("75");
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
				$("#proveed").attr("readonly", "readonly");
				$("#proveedor").val(ui.item.nombre);
				$("#rif").val(ui.item.rif);
				
				setTimeout(function(){ $("#proveed").removeAttr("readonly"); }, 1500);
				$("#serie").change();
				//ajaxsanncprov();
			}
		});
		';

		$script .= '
		$("#base").keyup(function(){
			totaliza();
		})

		$("#iva").keyup(function(){
			totaliza();
		})

		function totaliza(){
			var total = 0;
			var iva   = 0;
			var base  = 0;
			base = Number($("#base").val());
			if ( $("#iva").val() == "" ){
				$("#iva").val( 0 ); 
				$("#iva_val").text(nformat(0,2));
				
			}
			total = base + Number($("#iva").val());
			
			$("#total").val(roundNumber(total,2));
			$("#total_val").text(nformat(total,2));
		}

		';

		$edit = new DataEdit('', 'edgasto');

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert', '_pre_insert' );
		$edit->pre_process('update', '_pre_update' );
		$edit->pre_process('delete', '_pre_delete' );

		$edit->aplicacion = new inputField('Aplicacion','aplicacion');
		$edit->aplicacion->rule='';
		$edit->aplicacion->size =7;
		$edit->aplicacion->maxlength =5;

		$edit->tipo_doc = new dropdownField('Tipo', 'tipo_doc');
		$edit->tipo_doc->option('FC','Factura');
		$edit->tipo_doc->option('RB','Recibo de Pago');
		$edit->tipo_doc->option('OT','Otro');
		$edit->tipo_doc->rule = 'required';
		$edit->tipo_doc->style='width:140px;';

		$edit->numero = new inputField('Numero','numero');
		$edit->numero->rule      = '';
		$edit->numero->size      = 15;
		$edit->numero->maxlength = 20;
		$edit->numero->rule      = 'required';

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule        = 'chfecha';
		$edit->fecha->calendar    = false;
		$edit->fecha->size        = 10;
		$edit->fecha->maxlength   = 8;
		$edit->fecha->insertValue = date('Y-m-d');

		$edit->causado = new dateonlyField('F. Causado','causado');
		$edit->causado->rule      = 'chfecha';
		$edit->causado->calendar  = false;
		$edit->causado->size      = 10;
		$edit->causado->maxlength = 8;
		$edit->causado->insertValue = date('Y-m-d');

		$edit->proveed = new inputField('Proveedor', 'proveed');
		$edit->proveed->size     = 10;
		$edit->proveed->autocomplete=false;

		$edit->partida = new  dropdownField ('Partida', 'partida');
		$edit->partida->options('SELECT id, CONCAT(codigo," ",descrip) descrip FROM mgas ORDER BY descrip');
		$edit->partida->rule = 'required';
		$edit->partida->style='width:250px;';

		$edit->detalle = new textareaField('Detalle','detalle');
		$edit->detalle->rule='';
		$edit->detalle->cols = 50;
		$edit->detalle->rows = 2;

		$edit->base = new inputField('Base','base');
		$edit->base->rule='numeric';
		$edit->base->css_class='inputnum';
		$edit->base->size =12;
		$edit->base->maxlength =10;

		$edit->iva = new inputField('Iva','iva');
		$edit->iva->rule='numeric';
		$edit->iva->css_class='inputnum';
		$edit->iva->size =12;
		$edit->iva->maxlength =10;

		$edit->total = new inputField('Total','total');
		$edit->total->rule='numeric';
		$edit->total->css_class='inputnum';
		$edit->total->size =12;
		$edit->total->maxlength =10;
		$edit->total->readonly  = true;

		$edit->rif = new inputField('RIF','rif');
		$edit->rif->rule='';
		$edit->rif->size =13;
		$edit->rif->maxlength =13;

		$edit->proveedor = new inputField('Nombre','proveedor');
		$edit->proveedor->rule='';
		$edit->proveedor->size =42;
		$edit->proveedor->maxlength =80;

		$edit->tipo = new hiddenField('Tipo','tipo');
		$edit->tipo->insertValue = 'M';
		$edit->tipo->updateValue = 'M';

		//$edit->tipo = new autoUpdateField('Tipo','M', 'M');


		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			$conten['form']  =&  $edit;
			$data['content']  =  $this->load->view('view_edgasto', $conten, false);
		}
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
		if (!$this->db->table_exists('edgasto')) {
			$mSQL="CREATE TABLE `edgasto` (
			  id       int(11)  NOT NULL AUTO_INCREMENT,
			  tipo_doc char(2)  NOT NULL DEFAULT 'FC',
			  numero   char(20) DEFAULT NULL,
			  fecha    date     DEFAULT NULL,
			  causado  date     DEFAULT NULL,
			  proveed  char(5)  DEFAULT NULL,
			  partida  int(11)  DEFAULT '0',
			  detalle  text,
			  base     decimal(10,2) DEFAULT '0.00',
			  iva      decimal(10,2) DEFAULT '0.00',
			  total    decimal(10,2) DEFAULT '0.00',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `numero` (`numero`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='Gastos de Condominio'";
			$this->db->query($mSQL);
		}
	}
}

?>
