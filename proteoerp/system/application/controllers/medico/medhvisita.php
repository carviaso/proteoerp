<?php
/**
* ProteoERP
*
* @autor    Andres Hocevar
* @license  GNU GPL v3
*/
class Medhvisita extends Controller {
	var $mModulo = 'MEDHVISITA';
	var $titp    = 'CONTROL DE VISITA';
	var $tits    = 'CONTROL DE VISITA';
	var $url     = 'medico/medhvisita/';

	function Medhvisita(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'MEDHVISITA', $ventana=0 );
	}

	function index(){
		$this->datasis->creaintramenu(array('modulo'=>'172','titulo'=>'Visitas','mensaje'=>'Control de Visitas','panel'=>'SALUD','ejecutar'=>'medico/medhvisita','target'=>'popu','visible'=>'S','pertenece'=>'1','ancho'=>900,'alto'=>600));
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
		//$grid->wbotonadd(array("id"=>"edocta",   "img"=>"images/pdf_logo.gif",  "alt" => "Formato PDF", "label"=>"Ejemplo"));
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
		$param['listados']    = $this->datasis->listados('MEDHVISITA', 'JQ');
		$param['otros']       = $this->datasis->otros('MEDHVISITA', 'JQ');
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

		$bodyscript .= $this->jqdatagrid->bsshow('medhvisita', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'medhvisita', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'medhvisita', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('medhvisita', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '300', '400' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '300', '400' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '300', '400' );

		$bodyscript .= '});';

		$bodyscript .= '</script>';

		return $bodyscript;
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
			'width'         => 40,
			'editable'      => 'false',
			'search'        => 'false'
		));


		$grid->addField('historia');
		$grid->label('Historia');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
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

		$grid->addField('tabula');
		$grid->label('Tabula');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));


		$grid->addField('descripcion');
		$grid->label('Descripcion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
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
		$grid->setAdd(    $this->datasis->sidapuede('MEDHVISITA','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('MEDHVISITA','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('MEDHVISITA','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('MEDHVISITA','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: medhvisitaadd, editfunc: medhvisitaedit, delfunc: medhvisitadel, viewfunc: medhvisitashow");

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
		$mWHERE = $grid->geneTopWhere('medhvisita');

		$response   = $grid->getData('medhvisita', array(array()), array(), false, $mWHERE );
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
				$check = $this->datasis->dameval("SELECT count(*) FROM medhvisita WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('medhvisita', $data);
					echo "Registro Agregado";

					logusu('MEDHVISITA',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM medhvisita WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM medhvisita WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE medhvisita SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("medhvisita", $data);
				logusu('MEDHVISITA',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('medhvisita', $data);
				logusu('MEDHVISITA',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM medhvisita WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM medhvisita WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->query("DELETE FROM medhvisita WHERE id=$id ");
				logusu('MEDHVISITA',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//******************************************************************
	// Edicion 

	function dataedit(){
		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});
		';

		$edit = new DataEdit('', 'medhvisita');

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

		$edit->historia = new inputField('Historia','historia');
		$edit->historia->rule='';
		$edit->historia->size =22;
		$edit->historia->maxlength =20;

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->calendar=false;
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;
		$edit->fecha->insertValue = date('Y-m-d');

		$edit->tabula = new dropdownField('Tabula','tabula');
		$edit->tabula->option('','Seleccionar');
		$edit->tabula->options('SELECT id, CONCAT(indice," ",nombre ) tabu FROM medhtab WHERE grupo > 1 ORDER BY indice');
		$edit->tabula->rule ='required';
		$edit->tabula->style='width:180px;';
		//$edit->tabula->insertValue = $grupo;

		$edit->descripcion = new textareaField('Descripcion','descripcion');
		$edit->descripcion->rule='';
		$edit->descripcion->cols = 50;
		$edit->descripcion->rows = 4;

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

	//******************************************************************
	// Edicion 

	function dataefla(){

		$idhistoria = intval($this->uri->segment(5));
		$idanterior = intval($this->uri->segment(6));
		
		$fecha     = date('Y-m-d');
		$historia  = '';
		$cedula    = '';
		$nombres   = '';
		$apellidos = '';

		if ( $idhistoria == 'insert'){
			$idhistoria = intval($this->uri->segment(4));
			$idanterior = 0;			
		} 
		

		if ( $idhistoria ){
			$ante  = $this->datasis->damerow("SELECT numero, CONCAT(nacional,cedula) cedula, nombre, papellido FROM medhisto WHERE id=$idhistoria");
			$historia  = $ante['numero'];
			$cedula    = $ante['cedula'];
			$nombres   = $ante['nombre'];
			$apellidos = $ante['papellido'];
		} 


		if ( $idanterior ){
			$ante  = $this->datasis->damerow("SELECT historia, fecha FROM medhvisita WHERE id=$idanterior");
			$historia = $ante['historia'];
			$fecha    = $ante['fecha'];
			
		} 

		$this->rapyd->load('dataedit');
		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
			$.post(\''.site_url('medico/medhvisita/get_tabula').'\',{ historia:"'.$historia.'", fecha:$("#fecha").val() },function(data){$("#tabulados").html(data);})
		});
		
		$("#descripcion").focus(function(){
			$.post(\''.site_url('medico/medhvisita/get_tabula').'\',{ historia:"'.$historia.'", fecha:$("#fecha").val() },function(data){$("#tabulados").html(data);})
		});

		$("#fecha").change(function(){
			$.post(\''.site_url('medico/medhvisita/get_tabula').'\',{ historia:"'.$historia.'", fecha:$("#fecha").val() },function(data){$("#tabulados").html(data);})
		});


		function elivisita(id){
			$.prompt("<h1>Eliminar entrada</h1>", {
				buttons: { Eliminar: true, Cancelar: false },
				submit: function(e,v,m,f){
					if (v) {
						$.ajax({ url: "'.site_url('medico/medhvisita/elimina').'/"+id,
							complete: function(){ 
								//alert(("Entrada Eliminada")) 
								$.post(\''.site_url('medico/medhvisita/get_tabula').'\',{ historia:"'.$historia.'", fecha:$("#fecha").val() },function(data){$("#tabulados").html(data);})
							}
						});
					}
				}
			});
		}
		';

		$edit = new DataEdit('', 'medhvisita');

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

		$html = '<table width="100%" style="background-color:#BCF5A9;font-size:14px;"><tr><td>Historia: </td><td style="font-weight:bold;">'.$historia.'</td><td>Nombre:</td><td style="font-weight:bold;">'.$nombres.' '.$apellidos.'</td><td>C.I.:</td><td style="font-weight:bold;">'.$cedula.'</td></td></tr></table>';
		$edit->cabeza = new containerField('cabeza',$html);  

		$edit->historia = new hiddenField('','historia');
		$edit->historia->insertValue = $historia;

		$edit->tabula = new dropdownField('Tabula','tabula');
		$edit->tabula->option('','Seleccionar');
		$edit->tabula->options('SELECT id, CONCAT(indice," ",nombre ) tabu FROM medhtab WHERE grupo > 1 ORDER BY indice');
		$edit->tabula->rule ='required';
		$edit->tabula->style='width:350px;';

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->calendar=false;
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;
		$edit->fecha->insertValue = $fecha; 

		$edit->descripcion = new textareaField('Descripcion','descripcion');
		$edit->descripcion->rule='';
		$edit->descripcion->cols = 60;
		$edit->descripcion->rows = 4;

		$div = "<br><div style='overflow:auto;border: 1px solid #9AC8DA;background: #EAEAEA;height:210px' id='tabulados'></div>";
		$edit->contenedor = new containerField('contenedor',$div);  


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

	function elimina( $id = 0){
		$id = intval($id);
		$this->db->delete('medhvisita', array('id'=>$id));
	}

	function get_tabula( $s = 0  ){
		$historia = $this->input->post('historia');
		$fecha    = $this->input->post('fecha');
		$msalida = '';
		if( $historia>0 && $fecha > 0 ){
			$fecha = substr($fecha,6,4).substr($fecha,3,2).substr($fecha,0,2);
			$mSQL=$this->db->query("SELECT b.indice, a.descripcion, a.id FROM medhvisita a JOIN medhtab b ON a.tabula=b.id WHERE a.historia=$historia AND a.fecha='$fecha' ORDER BY b.indice DESC");
			if($mSQL){
				$msalida .= "<table width='100%' style='font-size:12px;'>";
				$mod = 0;
				foreach($mSQL->result() AS $fila ){
					$msalida .= "<tr bgcolor='";
					if(!$mod) 
						$msalida .= '#CFFFFF'; 
					else
						$msalida .= '#00FFFF';
	
					$msalida .= "'><td>".$fila->indice."</td><td>".$fila->descripcion."</td><td align='right'><a onclick='elivisita(".$fila->id.")'>".img(array('src'=>"images/delete.png", 'height'=>15, 'alt'=>'Eliminar', 'title'=>'Eliminar', 'border'=>'0'))."</a></td></tr>";
					$mod = !$mod;
				}
			}
			$msalida .= "</table>";
		}else{
			$msalida .= "No se encontraron datos ";
		}
		if ( $s )
			return $msalida;
		else
			echo $msalida;
	}

	function _pre_insert($do){
		$historia = $do->get('historia');
		$fecha    = $do->get('fecha');
		$tabula   = $do->get('tabula');
		$mSQL = "SELECT COUNT(*) FROM medhvisita WHERE historia=$historia AND fecha='$fecha' AND tabula=$tabula";
		//memowrite($mSQL);
		$cuantos = $this->datasis->dameval($mSQL);
		if ( $cuantos > 0  ) {
			$mSQL = "SELECT id FROM medhvisita WHERE historia=$historia AND fecha='$fecha' AND tabula=$tabula LIMIT 1";
			$id = $this->datasis->dameval($mSQL);
			// Actualiza la descripcion
			$this->db->where("id", $id);
			$this->db->update('medhvisita', array('descripcion'=>$do->get('descripcion')));
			$do->error_message_ar['pre_ins']='Numero de tabulador repetido!, se actualizo la descripcion';
			return false;
		} else {
			$do->error_message_ar['pre_ins']='';
			return true;
		}
	}

	function _pre_update($do){
		$do->error_message_ar['pre_upd']='';
		return true;
	}

	function _pre_delete($do){
		$do->error_message_ar['pre_del']='';
		return false;
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
		if (!$this->db->table_exists('medhvisita')) {
			$mSQL="
			CREATE TABLE `medhvisita` (
				id          INT(11) NOT NULL AUTO_INCREMENT,
				historia    VARCHAR(20) NOT NULL DEFAULT '0',
				fecha       DATE NOT NULL,
				tabula      INT(11) NULL DEFAULT '0',
				descripcion TEXT NULL,
				PRIMARY KEY (id)
			) ENGINE=MyISAM CHARSET=latin1 ROW_FORMAT=DYNAMIC
			";

		$this->db->query($mSQL);
		}
	}
}

?>
