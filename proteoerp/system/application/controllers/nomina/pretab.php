<?php
class Pretab extends Controller {
	var $mModulo = 'PRETAB';
	var $titp    = 'PRENOMINA';
	var $tits    = 'PRENOMINA';
	var $url     = 'nomina/pretab/';

	function Pretab(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'PRETAB', $ventana=0 );
	}

	function index(){
		if ( !$this->datasis->iscampo('pretab','id') ) {
			$this->db->simple_query('ALTER TABLE pretab DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE pretab ADD UNIQUE INDEX codigo (codigo)');
			$this->db->simple_query('ALTER TABLE pretab ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};
		$this->datasis->creaintramenu(array('modulo'=>'716','titulo'=>'Prenomina','mensaje'=>'Prenomina','panel'=>'TRANSACCIONES','ejecutar'=>'nomina/pretab','target'=>'popu','visible'=>'S','pertenece'=>'7','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 900, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//******************************************************************
	//  Layout en la Ventana
	//
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'genepre',   'img'=>'images/star.png',  'alt' => 'Genera Prenomina', 'label'=>'Genera Prenomina'));
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
		$param['listados']    = $this->datasis->listados('PRETAB', 'JQ');
		$param['otros']       = $this->datasis->otros('PRETAB', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//******************************************************************
	//  Funciones de los Botones
	//
	function bodyscript( $grid0 ){
		$bodyscript = '		<script type="text/javascript">';

		// Prepara Prenomina
		$bodyscript .= '
		$("#genepre").click( function() {
			$.post("'.base_url().'nomina/prenom/",
			function(data){
				$("#fedita").dialog( {height: 230, width: 450, title: "Cruce Cliente Cliente"} );
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		});
		';

		$bodyscript .= '
		function frecibo( id ){
			$.ajax({
				url: "'.base_url().$this->url.'recibo/"+id,
				success: function(msg){
					$("#ladicional").html(msg);
				}
			});
		};
		';


		$bodyscript .= '
		function pretabadd(){
			$.post("'.site_url($this->url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';

		$bodyscript .= '
		function pretabedit(){
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
		function pretabshow(){
			var id     = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
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
		function pretabdel() {
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
									'.$this->datasis->jwinopen(site_url('formatos/ver/PRETAB').'/\'+res.id+\'/id\'').';
									return true;
								} else {
									apprise(json.mensaje);
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

		$bodyscript .= '});'."\n";

		$bodyscript .= "\n</script>\n";
		$bodyscript .= "";
		return $bodyscript;
	}


	//******************************************************************
	//  Resumen rapido
	//
	function recibo( $id ) {

		$row = $this->datasis->damereg("SELECT a.codigo, a.nombre, CONCAT(b.nacional,b.cedula) ci, b.enlace FROM pretab a JOIN pers b ON a.codigo=b.codigo WHERE a.id=$id");
		$codigo  = $row['codigo'];
		$nombre  = $row['nombre'];
		$cedula  = $row['ci'];
		$cod_cli = $row['enlace'];
		$fecha   = date('Y-m-d');
		$mPRESTA = 0;
		$mSALDO  = 0;


		$mSQL = "SELECT a.concepto, b.descrip, a.tipo, a.monto, a.valor, a.fecha FROM prenom a JOIN conc b ON a.concepto=b.concepto WHERE MID(a.concepto,1,1)<>'9' AND a.valor<>0 AND a.codigo=".$this->db->escape($codigo)." ORDER BY tipo, codigo ";
		$query = $this->db->query($mSQL);

		//$data = $query->row();
		$salida = '';
		$salida  .= '<table width="90%" style="background:#FBEC88;" align="center">';
		$salida  .= '<tr><td>Cod: '.$codigo.'</td><td>C.I. '.$cedula.'</td></tr>';
		$salida  .= '<tr><td colspan="2">'.$nombre.'</td></tr>';
		$salida  .= '</table>';
		
		$salida  .= '<table width="90%" border="1" align="center" cellspacing="0" cellpadding="0">';
		$total = 0;

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				if ( $row->valor < 0 )
					$salida .= "<tr><td>".$row->descrip."</td><td align='right' style='color:red;'>".nformat($row->valor)."</td></tr>\n";
				else
					$salida .= "<tr><td>".$row->descrip."</td><td align='right'>".nformat($row->valor)."</td></tr>\n";

				$total += $row->valor;
				$fecha = $row->fecha;
			}
		}
		$salida .= "<tr style='background:#BAF202;'><td>Total a Pagar</td><td align='right'>".nformat($total)."</td></tr>\n";
		$salida .= "</table>\n";


		// PRESTAMOS
		if ( !empty($cod_cli) ){ 
			$mSQL  = "SELECT a.tipo_doc, a.numero, b.monto, b.abonos, a.cuota, b.monto-b.abonos saldo ";
			$mSQL .= "FROM pres a JOIN smov b ON a.cod_cli=b.cod_cli AND a.tipo_doc=b.tipo_doc AND a.numero=b.numero ";
			$mSQL .= "WHERE a.cod_cli='".$cod_cli."' AND b.monto>b.abonos AND a.apartir<='".$fecha."'";
			$query = $this->db->query($mSQL);

			if ($query->num_rows() > 0){
				$salida .= '<table width="90%" border="0" align="center" style="border:1px solid; background:#E4E4E4;">';
				$salida .= '<tr><td colspan="3" align="center">PRESTAMOS</td></tr>';
				foreach ($query->result() as $row){
					$salida .= '<tr><td>'.$row->tipo_doc.$row->numero.'</td><td>'.nformat($row->saldo).'</td><td>'.nformat($row->cuota).'</td></tr>';
					$mSALDO += $row->cuota;
				}
				$salida .= "<tr style='background:#BAF202;'><td>Neto a Pagar</td><td align='right' colspan='2'>".nformat($total-$mSALDO)."</td></tr>\n";
				$salida .= "</table>\n";

			}

		}


		//$salida .= '<table width="90%" border="0" align="center" style="border:1px solid; background:#E4E4E4;"><tr>';
		//$salida .= '<td align="center"><a href="#" onclick="fresumen('.$id.','.$anterior.')"> '.img('images/arrow_left.png').'</a></td>';
		//$salida .= '<td align="center"><a href="#" onclick="crecalban()" >RECALCULAR</a></td>';
		//$salida .= '<td align="center"><a href="#" onclick="fresumen('.$id.','.$proximo.')">'.img('images/arrow_right.png').'</a>';
		//$salida .= "</td></tr></table>\n";

		echo $salida;
	}



	//******************************************************************
	//  Definicion del Grid y la Forma
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('frec');
		$grid->label('Frec');
		$grid->params(array(
			'search'        => 'true',
			'hidden'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));


		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'hidden'        => 'true',
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
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

		$query = $this->db->query("DESCRIBE pretab");
		$i = 0;
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				if ( substr($row->Field,0,1) == 'c' && $row->Field != 'codigo' ) {

					$etiq = $this->datasis->dameval("SELECT CONCAT(TRIM(encab1), ' ', encab2 ) encabeza FROM conc WHERE concepto=".$this->db->escape(substr($row->Field,1,4)));
					$grid->addField($row->Field);
					$grid->label($etiq);
					$grid->params(array(
						'search'        => 'true',
						'editable'      => $editar,
						'align'         => "'right'",
						'edittype'      => "'text'",
						'width'         => 90,
						'editrules'     => '{ required:true }',
						'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
						'formatter'     => "'number'",
						'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
					));
				}
			}
		}

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setOnSelectRow('
		function(id){
			if (id){
				var ret = jQuery(gridId1).jqGrid(\'getRowData\',id);
				frecibo(id);
			}
		}'
		);


		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(    $this->datasis->sidapuede('PRETAB','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('PRETAB','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('PRETAB','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('PRETAB','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: pretabadd, editfunc: pretabedit, delfunc: pretabdel, viewfunc: pretabshow");

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
		$mWHERE = $grid->geneTopWhere('pretab');

		$response   = $grid->getData('pretab', array(array()), array(), false, $mWHERE );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	//******************************************************************
	// Guarda la Informacion
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
				$check = $this->datasis->dameval("SELECT count(*) FROM pretab WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('pretab', $data);
					echo "Registro Agregado";

					logusu('PRETAB',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM pretab WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM pretab WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE pretab SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("pretab", $data);
				logusu('PRETAB',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('pretab', $data);
				logusu('PRETAB',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM pretab WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM pretab WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->simple_query("DELETE FROM pretab WHERE id=$id ");
				logusu('PRETAB',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	function dataedit(){
		$this->rapyd->load('dataform');

		$id = $this->uri->segment($this->uri->total_segments());

		$edit = new DataForm('nomina/pretab/dataedit/process');

		$edit->on_save_redirect=false;

		//$edit->back_url = site_url($this->url.'filteredgrid');

/*
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');
		$edit->pre_process('insert', '_pre_insert' );
		$edit->pre_process('update', '_pre_update' );
		$edit->pre_process('delete', '_pre_delete' );

		$script= '
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
		});		';
		$edit->script($script,'create');
		$edit->script($script,'modify');
*/

		$mReg = $this->datasis->damereg("SELECT codigo, frec, fecha, nombre, total FROM pretab WHERE id=$id");

		$codigo = $mReg['codigo'];
		
		if ( empty($mReg) ){
			echo 'Registro no encontrado '.$id;
			return true;
		}

		$edit->codigo = new inputField('Codigo','codigo');
		$edit->codigo->rule        = '';
		$edit->codigo->size        = 10;
		$edit->codigo->maxlength   = 15;
		$edit->codigo->insertValue = $codigo;

		$edit->frec = new inputField('Frecuencia','frec');
		$edit->frec->rule        = '';
		$edit->frec->size        =  3;
		$edit->frec->maxlength   =  1;
		$edit->frec->insertValue = $mReg['frec'];

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule      = 'chfecha';
		$edit->fecha->size      = 10;
		$edit->fecha->maxlength =  8;
		$edit->fecha->calendar  = false;
		$edit->fecha->insertValue = $mReg['fecha'];

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule      = '';
		$edit->nombre->size      = 30;
		$edit->nombre->maxlength = 30;
		$edit->nombre->insertValue = $mReg['nombre'];

		$edit->total = new inputField('Total','total');
		$edit->total->rule      = 'numeric';
		$edit->total->css_class = 'inputnum';
		$edit->total->size      = 12;
		$edit->total->maxlength = 12;
		$edit->total->insertValue = $mReg['total'];

		$query = $this->db->query("DESCRIBE pretab");
		$i = 0;
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){

				if ( substr($row->Field,0,1) == 'c' && $row->Field != 'codigo' && substr($row->Field,1,1) != '9' ) {
					$reg     = $this->datasis->damereg('SELECT descrip, formula FROM conc WHERE concepto="'.substr($row->Field,1,4).'"');
					$nombre  = $reg['descrip'];
					$formula = $reg['formula'];

					if ( strpos($formula, 'MONTO') ) {
						$dReg = $this->datasis->damereg('SELECT monto, valor FROM prenom WHERE codigo="'.$codigo.'" AND concepto="'.substr($row->Field,1,4).'"');

						$obj = $row->Field;
						$edit->$obj = new inputField($nombre, $obj);
						$edit->$obj->rule      = 'numeric';
						$edit->$obj->css_class = 'inputnum';
						$edit->$obj->size      = 10;
						$edit->$obj->maxlength = 10;
						$edit->$obj->insertValue = $dReg['monto'];
					}
				}
			}
		}

		$edit->build();

		if($edit->on_success()){
			$codigo  = $edit->codigo->newValue;

			$query = $this->db->query("DESCRIBE pretab");
			$i = 0;
			if ($query->num_rows() > 0){
				foreach ($query->result() as $row){
					if ( substr($row->Field,0,1) == 'c' && $row->Field != 'codigo' && substr($row->Field,1,1) != '9' ) {
						$reg     = $this->datasis->damereg('SELECT descrip, formula FROM conc WHERE concepto="'.substr($row->Field,1,4).'"');
						$nombre  = $reg['descrip'];
						$formula = $reg['formula'];

						if ( strpos($formula, 'MONTO') ) {
							$obj = $row->Field;
							$this->db->query('UPDATE prenom SET monto='.$edit->$obj->newValue.' WHERE codigo="'.$codigo.'" AND concepto="'.substr($row->Field,1,4).'"');
							memowrite('UPDATE prenom SET monto='.$edit->$obj->newValue.' WHERE codigo="'.$codigo.'" AND concepto="'.substr($row->Field,1,4).'"','meco');
						}
					}
				}
			}

			$rt=array(
				'status'  => 'A',
				'mensaje' => 'Registro guardado',
				'pk'      => $codigo
			);
			echo json_encode($rt);

		}else{
			$conten['form'] =&  $edit;
			$this->load->view('view_pretab', $conten);
			
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
		if (!$this->db->table_exists('pretab')) {
			$mSQL="CREATE TABLE `pretab` (
			  `codigo` char(15) NOT NULL DEFAULT '',
			  `frec` char(1) DEFAULT NULL,
			  `fecha` date DEFAULT NULL,
			  `nombre` char(30) DEFAULT NULL,
			  `total` decimal(17,2) DEFAULT '0.00',
			  `c010` decimal(17,2) DEFAULT '0.00',
			  `c018` decimal(17,2) DEFAULT '0.00',
			  `c030` decimal(17,2) DEFAULT '0.00',
			  `c060` decimal(17,2) DEFAULT '0.00',
			  `c070` decimal(17,2) DEFAULT '0.00',
			  `c080` decimal(17,2) DEFAULT '0.00',
			  `c090` decimal(17,2) DEFAULT '0.00',
			  `c102` decimal(17,2) DEFAULT '0.00',
			  `c110` decimal(17,2) DEFAULT '0.00',
			  `c120` decimal(17,2) DEFAULT '0.00',
			  `c125` decimal(17,2) DEFAULT '0.00',
			  `c130` decimal(17,2) DEFAULT '0.00',
			  `c195` decimal(17,2) DEFAULT '0.00',
			  `c330` decimal(17,2) DEFAULT '0.00',
			  `c340` decimal(17,2) DEFAULT '0.00',
			  `c600` decimal(17,2) DEFAULT '0.00',
			  `c610` decimal(17,2) DEFAULT '0.00',
			  `c620` decimal(17,2) DEFAULT '0.00',
			  `c650` decimal(17,2) DEFAULT '0.00',
			  `c690` decimal(17,2) DEFAULT '0.00',
			  `c900` decimal(17,2) DEFAULT '0.00',
			  `c910` decimal(17,2) DEFAULT '0.00',
			  `c920` decimal(17,2) DEFAULT '0.00',
			  `c930` decimal(17,2) DEFAULT '0.00',
			  PRIMARY KEY (`codigo`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('pretab');
		//if(!in_array('<#campo#>',$campos)){ }
	}
}

?>
