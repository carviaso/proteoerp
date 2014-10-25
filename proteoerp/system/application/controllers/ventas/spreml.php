<?php
/**
* ProteoERP
*
* @autor    Andres Hocevar
* @license  GNU GPL v3
*/
class Spreml extends Controller {
	var $mModulo = 'SPREML';
	var $titp    = 'ORDEN MERCADO LIBRE';
	var $tits    = 'ORDEN MERCADO LIBRE';
	var $url     = 'ventas/spreml/';

	function Spreml(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->instalar();
		//$this->datasis->modulo_nombre( 'SPREML', $ventana=0 );
	}

	function index(){
		$this->instalar();
		$this->datasis->creaintramenu(array('modulo'=>'160','titulo'=>'Pagos Mercado Libre','mensaje'=>'PAGOS MERCADO LIBRE','panel'=>'TRANSACCIONES','ejecutar'=>'ventas/spreml','target'=>'popu','visible'=>'S','pertenece'=>'1','ancho'=>900,'alto'=>600));
		//$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		redirect($this->url.'jqdatag');
	}

	//******************************************************************
	// Layout en la Ventana
	//
	function jqdatag(){
		$this->datasis->modulo_nombre( 'SPREML', $ventana=0 );

		$grid = $this->defgrid();
		$param['grids'][] = $grid->deploy();

		//Funciones que ejecutan los botones
		$bodyscript = $this->bodyscript( $param['grids'][0]['gridname']);

		//Botones Panel Izq
		$grid->wbotonadd(array("id"=>"fconfirma", "img"=>"images/engrana.png",  "alt" => "Confirmar Pago", "label"=>"Confirmar Pago"));
		$grid->wbotonadd(array("id"=>"factura",   "img"=>"images/engrana.png",  "alt" => "Facturar",       "label"=>"Facturar"));
		$grid->wbotonadd(array("id"=>"fenvia",    "img"=>"images/engrana.png",  "alt" => "Guia de Envio",  "label"=>"Guia de Envio"));

		$Adic = '
		<table>
			<tr><td>Status</td><td>Pendiente</td></tr>
			<tr><td>Status</td><td>Confirmado</td></tr>
			<tr><td>&nbsp;</td><td>Facturado</td></tr>
			<tr><td>&nbsp;</td><td>Enviado</td></tr>
			<tr><td>&nbsp;</td><td>Nulo</td></tr>
		</tr></table>';
		//$grid->setWpAdicional($Adic);
		$WestPanel = $grid->deploywestp();

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro'),
			array('id'=>'ffact'  , 'title'=>'Convertir en factura'),
			array('id'=>'fshow' ,  'title'=>'Mostrar Registro'),
			array('id'=>'fborra',  'title'=>'Eliminar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$param['WestPanel']   = $WestPanel;
		//$param['EastPanel'] = $EastPanel;
		$param['SouthPanel']  = $SouthPanel;
		$param['listados']    = $this->datasis->listados('SPREML', 'JQ');
		$param['otros']       = $this->datasis->otros('SPREML', 'JQ');
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

		$bodyscript .= $this->jqdatagrid->bsshow('spreml', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'spreml', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'spreml', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('spreml', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '500', '700' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '300', '400' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '300', '400' );

		$bodyscript .= '});';

		$bodyscript .= '
		$("#fconfirma").click(function(){
			var meco = "";
			var id  = jQuery("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = jQuery("'.$ngrid.'").jqGrid(\'getRowData\',id);
				if(ret.status != "P"){ 
					$.prompt("<h1>Por favor Seleccione un Pago tipo Pendiente</h1>");
					return; 
				};
				var mgene = {
				state0: {
					html:"<h1>Confirmar Pago</h1><br/><center>Fecha: <b>"+ret.fechadep+"</b><br>Banco: <b>"+ret.codbanc+"</b><br>Numero: "+ret.tipo_op+ret.num_ref+" </center><br/>",
					buttons: { Cancelar: false, Confirmar: true },
					focus: 1,
					submit:function(e,v,m,f){
						if(v){
							e.preventDefault();
							$.ajax({
								url: \''.site_url('ventas/spreml/confirma').'/\'+id,
								global: false,
								type: "POST",
								data: ({ }),
								dataType: "text",
								async: false,
								success: function(sino) {
									meco = " sino="+sino;
									$.prompt.goToState("state1");
								},
								error: function(h,t,e) { alert("Error.. ",e) }
							});
							return false;
						}}
				},
				state1: {
					html:"<h1>Pago Confirmado! "+meco+"</h1>",
					buttons: { Salir: 0 },
					focus: 1,
					submit:function(e,v,m,f){
						e.preventDefault();
						$.prompt.close();
					}
				}
				};
				$.prompt(mgene);
			} else {
				$.prompt("<h1>Por favor Seleccione una Orden</h1>");
			}
		});';

		$bodyscript .= '
		$("#factura").click(function(){
			var id = $("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret    = $("#newapi'.$grid0.'").getRowData(id);
				if( ret.status == "C" ){
					$.post("'.site_url('ventas/sfac/creafromspreml').'/"+ret.numero+"/insert",
					function(data){
						var json = JSON.parse(data);
						if ( json.status == "A" ) {
							$("#newapi'.$grid0.'").trigger("reloadGrid");
							window.open(\''.site_url('ventas/sfac/dataprint/modify').'/\'+json.pk.id, \'_blank\', \'width=400,height=420,scrollbars=yes,status=yes,resizable=yes\');
						}
					});
				} else { $.prompt("<h1>Por favor Seleccione una Orden Confirmada</h1>");}
			} else { $.prompt("<h1>Por favor Seleccione una Orden</h1>");}
		});';


		$bodyscript .= '
		$("#fenvia").click(function(){
			var meco = "";
			var id  = $("#newapi'.$grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = $("'.$ngrid.'").jqGrid(\'getRowData\',id);
				if( ret.status != "F"){ 
					$.prompt("<h1>Por favor Seleccione una Orden Facturada</h1>");
					return false; 
				};
				var mgene = {
				state0: {
					html:"<h1>Registrar Envio</h1><br/><table align=\"center\"><tr><td>Fecha:</td><td><input type=\"text\" name=\"fechaenv\" id=\"fechaenv\" value=\''.date('d/m/Y').'\'></td></tr><tr><td>Nro Guia:</td><td><input type=\"text\" id=\"nguia\" name=\"nguia\" value=\'"+ret.guia+"\' ></td></tr></table><br/>",
					buttons: { Cancelar: false, Confirmar: true },
					focus: 1,
					submit:function(e,v,m,f){
						if(v){
							e.preventDefault();
							if ( f.nguia != "" ){
								$.ajax({
									url: \''.site_url('ventas/spreml/envia').'/\'+id,
									global: false,
									type: "POST",
									data: { guia : f.nguia, fecha : f.fechaenv },
									dataType: "text",
									async: false,
									success: function(sino) {
										meco = " sino="+sino;
										$.prompt.goToState("state1");
									},
									error: function(h,t,e) { alert("Error.. ",e) }
								});
								return false;
							}
						}
					}
				},
				state1: {
					html:"Envio efectuado! "+meco,
					buttons: { Salir: 0 },
					focus: 1,
					submit:function(e,v,m,f){
						e.preventDefault();
						$.prompt.close();
					}
				}
				};
				$.prompt(mgene);
				$("#fechaenv").datepicker({dateFormat:"dd/mm/yy"});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		});';


		$bodyscript .= '</script>';
		return $bodyscript;
	}

	//******************************************************************
	// Confirmar Pago
	function confirma( $id = 0 ){
		if ( $id == 0 )
			$id = $this->uri->segment($this->uri->total_segments());
		$id = intval($id);
		$msj = 'Pago Confirmado';
		$this->db->where('id',$id);
		$this->db->update('spreml',array('status'=>'C'));
		echo $msj;
	}

	//******************************************************************
	// Confirmar Envio
	function envia( $id = 0 ){
		if ( $id == 0 )
			$id = $this->uri->segment($this->uri->total_segments());
		
		$fecha1 = $this->input->post('fecha');
		$guia  = $this->input->post('guia');
		$fecha =  substr($fecha1,6,4).substr($fecha1,3,2).substr($fecha1,0,2);
		$id = intval($id);
		$msj = 'Envio Confirmado ';
		$this->db->where('id',$id);
		$this->db->update('spreml',array('status'=>'F', 'fechaenv'=>$fecha, 'guia'=>$guia ));

		$email  = $this->datasis->dameval('SELECT email  FROM spreml WHERE id='.$id);
		$numero = $this->datasis->dameval('SELECT numero FROM spreml WHERE id='.$id);
		// Envia correo
		$notifica  = "		
Estimado cliente, ya se procesó el envío de su pedido Nro ${numero}.\n\n
           El No de guia es ${guia}\n\n
Por favor tome en cuenta que el envío va asegurado por ZOOM, 
al retirar el paquete ábralo y verifíquelo dentro de las oficinas de 
ZOOM, debe estar completo, sin daños en pantalla y/o accesorios; 
una vez se haya retirado de las oficinas de ZOOM, ellos dejan de 
hacerse responsables de algún daño o percance; de igual manera 
en caso de entrega a domicilio deberá hacer la verificación en 
presencia del mensajero de ZOOM. 
Puede rastrear su envío a 
través de http://www.grupozoom.com/ en la sección de Rastreo de 
envíos o a través del 0800-7679666 (opción 1).\n
Cualquier inquietud no dude en comunicarse con nosotros. Gracias 
por preferirnos, no olvide calificarnos en MercadoLibre para activar su 
garantía, ya nosotros lo hemos hecho de manera POSITIVA (+).\n
Saludos cordiales.\n";

		$this->datasis->correo( $email, 'Notificacion de Envio '.$numero, utf8_decode($notifica) );
		

		echo $msj.$guia." ".$fecha;
	}


	//******************************************************************
	// Definicion del Grid o Tabla 
	//
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('status');
		$grid->label('Status');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
		));

		$grid->addField('numero');
		$grid->label('Numero');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:8, maxlength: 8 }',
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

		$grid->addField('rifci');
		$grid->label('Rifci');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 130,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:13, maxlength: 13 }',
		));

		$grid->addField('envrifci');
		$grid->label('Envrifci');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 130,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:13, maxlength: 13 }',
		));

		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));

		$grid->addField('direccion');
		$grid->label('Direccion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));

		$grid->addField('estado');
		$grid->label('Estado');
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

		$grid->addField('ciudad');
		$grid->label('Ciudad');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));

		$grid->addField('email');
		$grid->label('Email');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:100, maxlength: 100 }',
		));

		$grid->addField('telefono');
		$grid->label('Telefono');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 30 }',
		));

		$grid->addField('mercalib');
		$grid->label('Mercalib');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));

		$grid->addField('envnombre');
		$grid->label('Envnombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));

		$grid->addField('envdirec');
		$grid->label('Envdirec');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));

		$grid->addField('codbanc');
		$grid->label('Codbanc');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));

		$grid->addField('tipo_op');
		$grid->label('Tipo_op');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));

		$grid->addField('fechadep');
		$grid->label('F. Pago');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

		$grid->addField('num_ref');
		$grid->label('Num_ref');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:20, maxlength: 20 }',
		));

		$grid->addField('totalg');
		$grid->label('Totalg');
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

		$grid->addField('observa');
		$grid->label('Observa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
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

		$grid->addField('guia');
		$grid->label('Guia');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:40, maxlength: 40 }',
		));

		$grid->addField('fechaenv');
		$grid->label('F. Envio');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
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
		$grid->setAdd(    $this->datasis->sidapuede('SPREML','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('SPREML','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('SPREML','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('SPREML','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: spremladd, editfunc: spremledit, delfunc: spremldel, viewfunc: spremlshow");

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
		$mWHERE = $grid->geneTopWhere('spreml');

		$response   = $grid->getData('spreml', array(array()), array(), false, $mWHERE );
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
				$check = $this->datasis->dameval("SELECT count(*) FROM spreml WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('spreml', $data);
					echo "Registro Agregado";

					logusu('SPREML',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM spreml WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM spreml WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE spreml SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("spreml", $data);
				logusu('SPREML',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('spreml', $data);
				logusu('SPREML',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM spreml WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM spreml WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->query("DELETE FROM spreml WHERE id=$id ");
				logusu('SPREML',"Registro ????? ELIMINADO");
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
			$("#fechadep").datepicker({dateFormat:"dd/mm/yy"});
			$("#fechaenv").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});
		';

		$edit = new DataEdit('', 'spreml');

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

		$edit->numero = new inputField('Numero','numero');
		$edit->numero->rule='';
		$edit->numero->size =10;
		$edit->numero->maxlength =8;

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->calendar=false;
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;

		$edit->rifci = new inputField('Cedula/RIF','rifci');
		$edit->rifci->rule='';
		$edit->rifci->size =12;
		$edit->rifci->maxlength =13;

		$edit->envrifci = new inputField('Cedula/RIF','envrifci');
		$edit->envrifci->rule='';
		$edit->envrifci->size =15;
		$edit->envrifci->maxlength =13;

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='';
		$edit->nombre->size =25;
		$edit->nombre->maxlength =40;

		$edit->direccion = new textareaField('Direccion','direccion');
		$edit->direccion->rule='';
		$edit->direccion->cols = 50;
		$edit->direccion->rows = 2;

		$edit->estado = new dropdownField('Estado','estado');
		$edit->estado->style='width:150px;';
		$edit->estado->option('','Seleccione un Estado');
		$edit->estado->options('SELECT codigo, entidad FROM estado ORDER BY entidad');
		$edit->estado->insertValue=$this->datasis->dameval("SELECT codigo FROM estado WHERE entidad=".$this->db->escape(trim($this->datasis->traevalor('ESTADO'))));

		$edit->ciudad = new inputField('Ciudad','ciudad');
		$edit->ciudad->rule='';
		$edit->ciudad->size =20;
		$edit->ciudad->maxlength =40;

		$edit->email = new inputField('Email','email');
		$edit->email->rule='';
		$edit->email->size =32;
		$edit->email->maxlength =100;

		$edit->telefono = new inputField('Telefono','telefono');
		$edit->telefono->rule='';
		$edit->telefono->size =18;
		$edit->telefono->maxlength =30;

		$edit->mercalib = new inputField('Alias ML','mercalib');
		$edit->mercalib->rule='';
		$edit->mercalib->size =20;
		$edit->mercalib->maxlength =50;

		$edit->envnombre = new inputField('Nombre','envnombre');
		$edit->envnombre->rule='';
		$edit->envnombre->size =25;
		$edit->envnombre->maxlength =40;

		$edit->envdirec = new textareaField('Direcccion','envdirec');
		$edit->envdirec->rule='';
		$edit->envdirec->cols = 50;
		$edit->envdirec->rows = 2;

		$edit->envestado = new dropdownField('Estado','envestado');
		$edit->envestado->style='width:160px;';
		$edit->envestado->option('','Seleccione un Estado');
		$edit->envestado->options('SELECT codigo, entidad FROM estado ORDER BY entidad');
		$edit->envestado->insertValue=$this->datasis->dameval("SELECT codigo FROM estado WHERE entidad=".$this->db->escape(trim($this->datasis->traevalor('ESTADO'))));

		$edit->envciudad = new inputField('Ciudad','envciudad');
		$edit->envciudad->rule='';
		$edit->envciudad->size =20;
		$edit->envciudad->maxlength =40;

		$edit->envtelef = new inputField('Telefono','envtelef');
		$edit->envtelef->rule='';
		$edit->envtelef->size =20;
		$edit->envtelef->maxlength =30;

		$edit->codbanc = new dropdownField('Banco','codbanc');
		$edit->codbanc->option('','Seleccionar');
		$edit->codbanc->options('SELECT codbanc, CONCAT(banco,\' \',numcuent) banco FROM banc WHERE activo="S" AND tipocta="C" ORDER BY banco');
		$edit->codbanc->style='width:170px;';
		$edit->codbanc->size = 2;
		$edit->codbanc->rule = 'required';

		$edit->tipo_op = new dropdownField('Tipo','tipo_op');
		$edit->tipo_op->option('','Seleccionar');
		$edit->tipo_op->options(array('NC'=> 'Transferencia','DE'=>'Deposito'));
		$edit->tipo_op->style='width:87px';
		$edit->tipo_op->rule = 'required';

		$edit->fechadep = new dateonlyField('Fecha de pago','fechadep');
		$edit->fechadep->rule='chfecha';
		$edit->fechadep->calendar=false;
		$edit->fechadep->size =10;
		$edit->fechadep->maxlength =8;

		$edit->num_ref = new inputField('Referencia','num_ref');
		$edit->num_ref->rule='';
		$edit->num_ref->size =22;
		$edit->num_ref->maxlength =20;

		$edit->totalg = new inputField('Monto','totalg');
		$edit->totalg->rule='numeric';
		$edit->totalg->css_class='inputnum';
		$edit->totalg->size =12;
		$edit->totalg->maxlength =12;

		$edit->agencia = new inputField('Agencia','agencia');
		$edit->agencia->rule='';
		$edit->agencia->size =32;
		$edit->agencia->maxlength =50;

		$edit->observa = new textareaField('Observacion','observa');
		$edit->observa->rule='';
		$edit->observa->cols = 50;
		$edit->observa->rows = 2;

		$edit->status = new dropdownField('Status','status');
		$edit->status->options(array(''=>'--','P'=>'Pendiente','C'=>'Confirmado','F'=>'Facturado','E'=>'Enviado','N'=>'Nulo'));
		$edit->status->style='width:80px';

		$edit->guia = new inputField('Nro Guia','guia');
		$edit->guia->rule='';
		$edit->guia->size =15;
		$edit->guia->maxlength =30;

		$edit->fechaenv = new dateonlyField('Fecha de envio','fechaenv');
		$edit->fechaenv->rule='chfecha';
		$edit->fechaenv->calendar=false;
		$edit->fechaenv->size =10;
		$edit->fechaenv->maxlength =8;

		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		}else{
			//echo $edit->output;
			$conten['form']  =&  $edit;
			$data['content'] = $this->load->view('view_spreml', $conten);
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


	//******************************************************************
	// Edicion 

	function decliente(){
		$this->rapyd->load('dataedit');
		$script= '';

		$edit = new DataEdit('', 'spreml');

		$edit->script($script,'modify');
		$edit->script($script,'create');
		$edit->on_save_redirect=false;

		$edit->back_url = site_url($this->url.'create');

		$edit->post_process('insert','_clpost_insert');
		$edit->post_process('update','_clpost_update');
		$edit->post_process('delete','_clpost_delete');
		$edit->pre_process('insert', '_clpre_insert' );
		$edit->pre_process('update', '_clpre_update' );
		$edit->pre_process('delete', '_clpre_delete' );

		$edit->numero = new inputField('Nro. Orden','numero');
		$edit->numero->rule='';
		$edit->numero->size =8;
		$edit->numero->maxlength =8;
		$edit->numero->rule = 'required';
		$edit->numero->title = 'Numero de la orden que recibio por email';

		$edit->mercalib = new inputField('Alias Mercado Libre','mercalib');
		$edit->mercalib->rule='';
		$edit->mercalib->size =20;
		$edit->mercalib->maxlength =50;
		$edit->mercalib->rule = 'required';
		$edit->mercalib->title = 'Nombre de la cuenta en mercado libre';

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->calendar=false;
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;
		$edit->fecha->insertValue = date('Y-m-d');

		$edit->rifci = new inputField('Cedula/RIF','rifci');
		$edit->rifci->size =10;
		$edit->rifci->maxlength =13;
		$edit->rifci->rule = 'required';

		$edit->nombre = new inputField('Nombre','nombre');
		$edit->nombre->rule='';
		$edit->nombre->size =31;
		$edit->nombre->maxlength =40;
		$edit->nombre->rule = 'required';

		$edit->direccion = new textareaField('Direccion','direccion');
		$edit->direccion->rule='';
		$edit->direccion->cols = 50;
		$edit->direccion->rows = 2;

		$edit->estado = new dropdownField('Estado','estado');
		$edit->estado->style='width:160px;';
		$edit->estado->option('','Seleccione un Estado');
		$edit->estado->options('SELECT codigo, entidad FROM estado ORDER BY entidad');
		$edit->estado->insertValue=$this->datasis->dameval("SELECT codigo FROM estado WHERE entidad=".$this->db->escape(trim($this->datasis->traevalor('ESTADO'))));

		$edit->ciudad = new inputField('Ciudad','ciudad');
		$edit->ciudad->rule='';
		$edit->ciudad->size =18;
		$edit->ciudad->maxlength =40;
		$edit->ciudad->rule = 'required';

		$edit->email = new inputField('Email','email');
		$edit->email->rule='';
		$edit->email->size =20;
		$edit->email->maxlength =100;
		$edit->email->rule = 'required';

		$edit->telefono = new inputField('Telefono','telefono');
		$edit->telefono->rule='';
		$edit->telefono->size =17;
		$edit->telefono->maxlength =30;
		$edit->telefono->rule = 'required';

		$edit->envrifci = new inputField('Cedula/RIF','envrifci');
		$edit->envrifci->rule='';
		$edit->envrifci->size =10;
		$edit->envrifci->maxlength =13;

		$edit->envnombre = new inputField('Nombre','envnombre');
		$edit->envnombre->rule      = '';
		$edit->envnombre->size      = 31;
		$edit->envnombre->maxlength = 40;

		$edit->envdirec = new textareaField('Direccion o Agencia Zoom de Envio','envdirec');
		$edit->envdirec->rule = '';
		$edit->envdirec->cols = 50;
		$edit->envdirec->rows =  2;

		$edit->envestado = new dropdownField('Estado','envestado');
		$edit->envestado->style='width:160px;';
		$edit->envestado->option('','Seleccione un Estado');
		$edit->envestado->options('SELECT codigo, entidad FROM estado ORDER BY entidad');
		$edit->envestado->insertValue=$this->datasis->dameval("SELECT codigo FROM estado WHERE entidad=".$this->db->escape(trim($this->datasis->traevalor('ESTADO'))));

		$edit->envciudad = new inputField('Ciudad','envciudad');
		$edit->envciudad->rule='';
		$edit->envciudad->size =18;
		$edit->envciudad->maxlength =40;

		$edit->envtelef = new inputField('Telefono','envtelef');
		$edit->envtelef->rule='';
		$edit->envtelef->size =17;
		$edit->envtelef->maxlength =30;

		$edit->codbanc = new dropdownField('Banco','codbanc');
		$edit->codbanc->option('','Seleccionar');
		$edit->codbanc->options('SELECT codbanc, CONCAT(banco,\' \',numcuent) banco FROM banc WHERE activo="S" AND tipocta="C" ORDER BY banco');
		$edit->codbanc->style='width:180px;';
		$edit->codbanc->size = 2;
		$edit->codbanc->rule = 'required';

		$edit->tipo_op = new dropdownField('Tipo','tipo_op');
		$edit->tipo_op->option('','Seleccionar');
		$edit->tipo_op->options(array('NC'=> 'Transferencia','DE'=>'Deposito'));
		$edit->tipo_op->style='width:97px';
		$edit->tipo_op->rule = 'required';

		$edit->fechadep = new DateonlyField('Fecha', 'fechadep','d/m/Y');
		$edit->fechadep->insertValue = date('Y-m-d');
		$edit->fechadep->updateValue = date('Y-m-d');
		$edit->fechadep->rule = 'required';
		$edit->fechadep->size = 10;
		$edit->fechadep->calendar=false;

		$edit->num_ref = new inputField('Nro de referencia','num_ref');
		$edit->num_ref->rule='required';
		$edit->num_ref->size =15;
		$edit->num_ref->maxlength =20;

		$edit->agencia = new inputField('Agencia Zoom','agencia');
		$edit->agencia->size =20;
		$edit->agencia->maxlength =50;
		
		$edit->totalg = new inputField('Monto','totalg');
		$edit->totalg->rule='numeric';
		$edit->totalg->css_class='inputnum';
		$edit->totalg->size =10;
		$edit->totalg->maxlength =12;
		$edit->totalg->readonly  = true;

		$edit->observa = new textareaField('Observacion','observa');
		$edit->observa->rule='';
		$edit->observa->cols = 50;
		$edit->observa->rows = 3;
		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			//echo json_encode($rt);

			$contenido  = '
<h1>Felicitaciones !!!</h1>

<p>Su información ha sido recibida satisfactoriamente. 
Una vez confirmado su pago procederemos a hacer el despacho de su artículo. 
En un lapso de 24 Horas le haremos llegar a su correo el número de guia Zoom 
correspondiente.</p>

<h1>Gracias por su Compra!!!</h1>

			<button onclick="volver()">Volver</button>
			<script>
			function volver(){
				window.location.href = "'.site_url('ventas/spreml/decliente/create').'";
			}
			</script>
			';


			$data['content'] = $contenido;
			$data["head"]    = script("jquery-min.js");
			$data["head"]   .= $this->rapyd->get_head();

			$data["head"]   .= style("rapyd.css");
			$data["head"]   .= style("themes/proteo/proteo.css");
			$data["head"]   .= style("themes/darkness/darkness.css");
			$data["head"]   .= style("themes/anexos1/anexos1.css");

			$data["target"] = 'dialogo';
			$data['title']   = heading('Registro de Pago');
			$this->load->view('view_ventanas', $data);

		}else{
			//echo $edit->output;
			$estilo = '
<style >
.ui-autocomplete {max-height: 150px;overflow-y: auto;max-width: 600px;}
html.ui-autocomplete {height: 150px;width: 600px;}
</style>';


			$estilo = '
<script language="javascript" type="text/javascript">
$(function(){
	$( document ).tooltip();
	$("#fechadep").datepicker({dateFormat:"dd/mm/yy"});
	$(".inputnum").numeric(".");

	$("#rifci").focusout(function(){
		rif=$(this).val();
		traenombre( rif, "nombre" )
	});
	$("#envrifci").focusout(function(){
		rif=$(this).val();
		traenombre( rif, "envnombre" )
	});

	$("#mercalib").focusout(function(){
		numer = $("#numero").val();
		merca = $("#mercalib").val();
		$.ajax({
			type: "POST",
			url: "'.site_url('ventas/spreml/buscaspre').'",
			dataType: "json",
			data: {numero: numer, mercalib: merca},
			success: function(data){
				if(data.error==0){
					$("#totalg").val(data.monto);
					$("#producto").html(data.producto);
				} else {
					alert(data.msj);
					$("#numero").focus();
				}
			}
		});
	});
});

'.$this->datasis->traenombre().'

'.$this->datasis->validarif().'
</script>
';

			$conten["form"]  =&  $edit;
			$data['content'] = $this->load->view('view_sprecl', $conten,true);
			
			$data["head"]    = script("jquery-min.js");
			$data["head"]   .= script("jquery-migrate-min.js");
			$data["head"]   .= script("jquery-ui.custom.min.js");
			$data["head"]   .= script("plugins/jquery.numeric.pack.js");
			$data["head"]   .= script("plugins/jquery.floatnumber.js");
			$data["head"]   .= $this->rapyd->get_head();
			
			$data["head"]   .= style("rapyd.css");
			$data["head"]   .= style("themes/proteo/proteo.css");
			$data["head"]   .= style("themes/darkness/darkness.css");
			$data["head"]   .= style("themes/anexos1/anexos1.css");
			$data["head"]   .= $estilo;
			
			$data["target"] = 'dialogo';
			$data['title']   = heading('Registro de Pago');
			$this->load->view('view_ventanas', $data);
		}
	}

	function _clpre_insert($do){
		// Valida si ya existe
		$devo = true;
		$do->error_message_ar['pre_ins']='';
		$numero   = str_pad(trim($do->get('numero')),8,'0', STR_PAD_LEFT);
		$mercalib = trim($do->get('mercalib'));
		$do->set('numero', $numero);
		$do->set('status', 'P');

		$numero   = $this->db->escape($numero);
		$mercalib = $this->db->escape($mercalib);
		$mSQL     = "SELECT count(*) FROM spre WHERE numero=LPAD(${numero},8,'0') AND mercalib=${mercalib} ";
		$cant     = $this->datasis->dameval($mSQL);
		if ($cant == 0) {
			$do->error_message_ar['pre_ins']='Orden invalida';
			$devo = false;
		}

		// BUSCA EN sprml
		$mSQL   = "SELECT count(*) FROM spreml WHERE numero=LPAD(${numero},8,'0') AND mercalib=${mercalib} ";
		$cant  = $this->datasis->dameval($mSQL);
		if ( $cant > 0 ){
			$do->error_message_ar['pre_ins']='Ya existe un pago para esa orden';
			$devo = false;
		}

		return $devo;
	}

	function _clpre_update($do){
		$do->error_message_ar['pre_upd']='';
		return true;
	}

	function _clpre_delete($do){
		$do->error_message_ar['pre_del']='';
		return false;
	}

	function _clpost_insert($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
		$banco = $this->datasis->damereg("SELECT * FROM banc WHERE codbanc=".$this->db->escape($do->get('codbanc')));

		$notifica  = "Gracias por su compra. Hemos recibido satisfactoriamente los datos de su orden.\n";
		$notifica  = "Una vez hecho el envio nos comunicaremos con usted al correo electronico para indicarle el numero de guia correspondiente. Por favor tome en cuenta que los envios demoran entre 24 y 48 horas habiles para su procesamiento.\n\n";
		$notifica .= "Institucion bancaria: ".$banco['banco'].' '.$banco['numcuent']."\n";
		$notifica .= "Tipo de Operacion: ".$do->get('tipo_op')."\n";
		$notifica .= "Fecha de pago: ".$do->get('fechadep')."\n";
		$notifica .= "Numero de Referencia: ".$do->get('num_ref')."\n";
		$notifica .= "Monto: ".$do->get('totalg')."\n\n";
		$notifica .= "Gracias por preferirnos.\n";
		$this->datasis->correo( $do->get('email'), 'Registro de Pago, Pedido '.$do->get('numero'), $notifica );
	}

	function _clpost_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}

	function _clpost_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	//******************************************************************
	//  Valida orden y Nombre ML
	function buscaspre(){
		$numero    = $this->input->post('numero');
		$mercalib  = $this->input->post('mercalib');
		
		$t=array( 'error'=>1, 'msj'=>'No se encontro la Orden', 'monto'=>0 );
		if( $numero && $mercalib ){
			$numero   = $this->db->escape($numero);
			$mercalib = $this->db->escape($mercalib);
			$mSQL   = "SELECT totalg FROM spre WHERE numero=LPAD(${numero},8,'0') AND mercalib=${mercalib} ";
			$monto  = $this->datasis->dameval($mSQL);
			if ($monto)
				// Busca el Producto en itspre
				$mSQL   = "SELECT CONCAT('<table style=\"width:100%;background:#0EF210;\"><tr><td>',desca,'</td><td>Cant.', cana,'</td></tr></table>') prod FROM itspre WHERE numero=LPAD(${numero},8,'0') ";
				$producto  = $this->datasis->dameval($mSQL);
				
				$t=array('error'=>0, 'msj'=>'Orden valida', 'monto'=>$monto, 'producto'=>$producto);
			// Busca en sprml
			$mSQL   = "SELECT count(*) FROM spreml WHERE numero=LPAD(${numero},8,'0') AND mercalib=${mercalib} ";
			$cant  = $this->datasis->dameval($mSQL);
			if ( $cant > 0 )
				$t=array('error'=>1,'msj'=>'Ya se realizo el registro, comuniquese a ventas@tecbloom.com','monto'=>0);
		} else {
			$t=array('error'=>1,'msj'=>'Numero o Alias incorrecto','monto'=>0);
		}
		echo json_encode($t);
	}

	//******************************************************************
	//
	function instalar(){
		if (!$this->db->table_exists('spreml')) {
			$mSQL="
			CREATE TABLE spreml (
				numero    VARCHAR(8)    DEFAULT NULL,
				fecha     DATE          DEFAULT NULL,
				rifci     VARCHAR(13)   DEFAULT NULL,
				envrifci  VARCHAR(13)   DEFAULT NULL,
				nombre    VARCHAR(40)   DEFAULT NULL,
				direccion TEXT          COMMENT 'Direccion del Cliente',
				estado    INT(11)       DEFAULT NULL,
				ciudad    VARCHAR(40)   DEFAULT NULL,
				email     VARCHAR(100)  DEFAULT NULL,
				telefono  VARCHAR(30)   DEFAULT NULL,
				mercalib  VARCHAR(50)   DEFAULT NULL,
				envnombre VARCHAR(40)   DEFAULT NULL COMMENT 'Nombre del Destinatario',
				envdirec  TEXT          COMMENT 'Direccion de Envio',
				envestado INT(11)       DEFAULT NULL,
				envciudad VARCHAR(40)   DEFAULT NULL,
				envtelef  VARCHAR(30)   DEFAULT NULL,
				codbanc   CHAR(2)       DEFAULT NULL,
				tipo_op   CHAR(2)       DEFAULT NULL,
				fechadep  DATE          DEFAULT NULL COMMENT 'Fecha del Deposito',
				num_ref   VARCHAR(20)   DEFAULT NULL,
				totalg    DECIMAL(12,2) DEFAULT NULL,
				agencia   VARCHAR(50)   DEFAULT NULL,
				observa   TEXT,
				id        INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (id),
				UNIQUE KEY numero (numero)
			) ENGINE=MyISAM DEFAULT 
			CHARSET=latin1 
			ROW_FORMAT=DYNAMIC 
			COMMENT='Presupuestos para Mercado Libre'";
			$this->db->query($mSQL);
		}
		$campos=$this->db->list_fields('spreml');
		if(!in_array('status',   $campos)) $this->db->query('ALTER TABLE spreml ADD COLUMN status   CHAR(1)     NULL DEFAULT NULL AFTER observa');
		if(!in_array('guia',     $campos)) $this->db->query('ALTER TABLE spreml ADD COLUMN guia     VARCHAR(30) NULL DEFAULT NULL AFTER status');
		if(!in_array('fechaenv', $campos)) $this->db->query('ALTER TABLE spreml ADD COLUMN fechaenv DATE        NULL DEFAULT NULL AFTER guia');
	}
}
?>
