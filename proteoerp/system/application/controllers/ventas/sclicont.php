<?php
/**
* ProteoERP
*
* @autor    Andres Hocevar
* @license  GNU GPL v3
*/
require_once(BASEPATH.'application/controllers/ventas/sfac.php');
class Sclicont extends sfac {
	var $mModulo = 'SCLICONT';
	var $titp    = 'CONTRATOS PERIODICOS';
	var $tits    = 'CONTRATOS PERIODICOS';
	var $url     = 'ventas/sclicont/';

	function Sclicont(){
		parent::Sfac();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'SCLICONT', $ventana=0 );
	}

	function index(){
		$this->datasis->creaintramenu(array('modulo'=>'152','titulo'=>'Contratos','mensaje'=>'Contratos','panel'=>'CONTRATOS','ejecutar'=>'ventas/sclicont','target'=>'popu','visible'=>'S','pertenece'=>'1','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
		$this->instalar();
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
		$grid->wbotonadd(array("id"=>"gfactura",   "img"=>"images/pdf_logo.gif",  "alt" => "Facturar", "label"=>"Facturar"));
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
		$param['listados']    = $this->datasis->listados('SCLICONT', 'JQ');
		$param['otros']       = $this->datasis->otros('SCLICONT', 'JQ');
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

		$bodyscript .= $this->jqdatagrid->bsshow('sclicont', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'sclicont', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'sclicont', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('sclicont', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '350', '600' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '300', '400' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '300', '400' );

		$bodyscript .= '});';

		$bodyscript .= '
		$("#gfactura").click(function(){
			alert("Hola");
		});
		';

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


		$grid->addField('inicio');
		$grid->label('Inicio');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
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
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('cliente');
		$grid->label('Cliente');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:5, maxlength: 5 }',
		));


		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('descrip');
		$grid->label('Descrip');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'textarea'",
			'editoptions'   => "'{rows:2, cols:60}'",
		));


		$grid->addField('cantidad');
		$grid->label('Cantidad');
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


		$grid->addField('precio');
		$grid->label('Precio');
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
		$grid->setAdd(    $this->datasis->sidapuede('SCLICONT','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('SCLICONT','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('SCLICONT','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('SCLICONT','BUSQUEDA%'));
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setBarOptions("addfunc: sclicontadd, editfunc: sclicontedit, delfunc: sclicontdel, viewfunc: sclicontshow");

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
		$mWHERE = $grid->geneTopWhere('sclicont');

		$response   = $grid->getData('sclicont', array(array()), array(), false, $mWHERE );
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
				$check = $this->datasis->dameval("SELECT count(*) FROM sclicont WHERE $mcodp=".$this->db->escape($data[$mcodp]));
				if ( $check == 0 ){
					$this->db->insert('sclicont', $data);
					echo "Registro Agregado";

					logusu('SCLICONT',"Registro ????? INCLUIDO");
				} else
					echo "Ya existe un registro con ese $mcodp";
			} else
				echo "Fallo Agregado!!!";

		} elseif($oper == 'edit') {
			$nuevo  = $data[$mcodp];
			$anterior = $this->datasis->dameval("SELECT $mcodp FROM sclicont WHERE id=$id");
			if ( $nuevo <> $anterior ){
				//si no son iguales borra el que existe y cambia
				$this->db->query("DELETE FROM sclicont WHERE $mcodp=?", array($mcodp));
				$this->db->query("UPDATE sclicont SET $mcodp=? WHERE $mcodp=?", array( $nuevo, $anterior ));
				$this->db->where("id", $id);
				$this->db->update("sclicont", $data);
				logusu('SCLICONT',"$mcodp Cambiado/Fusionado Nuevo:".$nuevo." Anterior: ".$anterior." MODIFICADO");
				echo "Grupo Cambiado/Fusionado en clientes";
			} else {
				unset($data[$mcodp]);
				$this->db->where("id", $id);
				$this->db->update('sclicont', $data);
				logusu('SCLICONT',"Grupo de Cliente  ".$nuevo." MODIFICADO");
				echo "$mcodp Modificado";
			}

		} elseif($oper == 'del') {
			$meco = $this->datasis->dameval("SELECT $mcodp FROM sclicont WHERE id=$id");
			//$check =  $this->datasis->dameval("SELECT COUNT(*) FROM sclicont WHERE id='$id' ");
			if ($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			} else {
				$this->db->query("DELETE FROM sclicont WHERE id=$id ");
				logusu('SCLICONT',"Registro ????? ELIMINADO");
				echo "Registro Eliminado";
			}
		};
	}

	//******************************************************************
	// Edicion

	function dataedit(){
		$this->rapyd->load('dataedit');
		$script = '
		var mtasa = 12;
		$(function() {
			$("#fecha").datepicker({dateFormat:"dd/mm/yy"});
			$("#inicio").datepicker({dateFormat:"dd/mm/yy"});
			$("#final").datepicker({dateFormat:"dd/mm/yy"});
			$(".inputnum").numeric(".");
		});
		';

		$script .= '
		$("#cliente").autocomplete({
			source: function( req, add){
				$.ajax({
					url:  "'.site_url('ajax/buscascli').'",
					type: "POST",
					dataType: "json",
					data: {"q":req.term},
					success:
						function(data){
							var sugiere = [];
							if(data.length==0){
								$("#cliente").val("");
								$("#nombre").html("");
								apprise("Cliente inexistente");
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
				select: function( event, ui ){
					$("#cliente").attr("readonly", "readonly");
					$("#cliente").val(ui.item.value);
					$("#nombre").html(ui.item.nombre);
					setTimeout(function() {  $("#cliente").removeAttr("readonly"); }, 1500);
				}
			});
		';


		$script .= '
		$("#codigo").autocomplete({
			delay: 600,
			autoFocus: true,
			source: function( req, add){
			$.ajax({
				url:  "'.site_url("ajax/buscasinv").'",
				type: "POST",
				dataType: "json",
				data: {"q":req.term.trim(), "alma": "0001" },
				success:
					function(data){
						var sugiere = [];
						if(data.length==0){
							$("#codigoa").val("")
							$("#descrip").val("");
							$("#base").val("");
							$("#iva").val("");
							$("#precio").val("");
						}else{
							$.each(data,
								function(i, val){
									sugiere.push( val );
								}
							);
							add(sugiere);
						}
					},
			})
		},
		minLength: 2,
			select: function( event, ui ){
				var cana = $("#cantidad").val();
				mtasa = ui.item.iva;

				$("#codigo").attr("readonly", "readonly");
				$("#codigo").val(ui.item.value);
				$("#descrip").val(ui.item.descrip);
				if ( !cana ){
					$("#cantidad").val(1);
				}
				$("#precio").val(ui.item.base1);
				$("#base").val(ui.item.base1);
				totaliza();
				setTimeout(function() {  $("#codigo").removeAttr("readonly"); }, 1500);
			}
		});
		';


		$script .= '
		function totaliza(){
			var iva      = 0;
			var precio   = 0;
			var base     = 0;
			var importe  = 0;
			var cantidad = 0;

			cantidad = Number($("#cantidad").val())
			precio   = Number($("#precio").val());

			base    = roundNumber(cantidad*precio,2);
			iva     = roundNumber(base*mtasa/100,2);
			importe = base+iva;

			$("#base").val(base);
			$("#iva").val(iva);
			$("#importe").val(importe);
		}
		';

		$script .= '
		$("#cantidad").change(function () {
			totaliza();
		})

		$("#precio").change(function () {
			totaliza();
		})

		';


		$edit = new DataEdit('', 'sclicont');

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

		$edit->status = new dropdownField('Estatus','status');
		$edit->status->options(array('A'=> 'Activo','S'=>'Suspendido', 'T'=>'Terminado'));
		$edit->status->style='width:80px';
		$edit->status->rule ='required';

		$edit->fecha = new dateonlyField('Fecha','fecha');
		$edit->fecha->rule='chfecha';
		$edit->fecha->calendar=false;
		$edit->fecha->size =10;
		$edit->fecha->maxlength =8;
		$edit->fecha->insertValue = date('Y-m-d');

		$edit->inicio = new dateonlyField('Inicio','inicio');
		$edit->inicio->rule='chfecha';
		$edit->inicio->calendar=false;
		$edit->inicio->size =10;
		$edit->inicio->maxlength =8;

		$edit->final = new dateonlyField('Final','final');
		$edit->final->rule='chfecha';
		$edit->final->calendar=false;
		$edit->final->size =10;
		$edit->final->maxlength =8;

		$edit->cliente = new inputField('Cliente','cliente');
		$edit->cliente->rule='';
		$edit->cliente->size =7;
		$edit->cliente->maxlength =5;

		$edit->codigo = new inputField('Codigo','codigo');
		$edit->codigo->rule='';
		$edit->codigo->size =14;
		$edit->codigo->maxlength =15;

		$edit->descrip = new textareaField('Descripcion','descrip');
		$edit->descrip->rule='';
		$edit->descrip->cols = 35;
		$edit->descrip->rows = 2;

		$edit->cantidad = new inputField('Cantidad','cantidad');
		$edit->cantidad->rule='numeric';
		$edit->cantidad->css_class='inputnum';
		$edit->cantidad->size =8;
		$edit->cantidad->maxlength =17;

		$edit->base = new inputField('Base','base');
		$edit->base->rule='numeric';
		$edit->base->css_class='inputnum';
		$edit->base->size =16;
		$edit->base->maxlength =17;
		$edit->base->readonly  =true;

		$edit->iva = new inputField('IVA','iva');
		$edit->iva->rule='numeric';
		$edit->iva->css_class='inputnum';
		$edit->iva->size =16;
		$edit->iva->maxlength =17;
		$edit->iva->readonly  =true;

		$edit->precio = new inputField('Precio','precio');
		$edit->precio->rule='numeric';
		$edit->precio->css_class='inputnum';
		$edit->precio->size =16;
		$edit->precio->maxlength =17;

		$edit->importe = new inputField('Importe','importe');
		$edit->importe->rule='numeric';
		$edit->importe->readonly  = true;
		$edit->importe->css_class='inputnum';
		$edit->importe->size =16;
		$edit->importe->maxlength =17;

		$edit->observa = new textareaField('Observaciones','observa');
		$edit->observa->rule='';
		$edit->observa->cols = 68;
		$edit->observa->rows = 2;


		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' =>'A',
				'mensaje'=>'Registro guardado',
				'pk'     =>$edit->_dataobject->pk
			);
			echo json_encode($rt);
		} else {
			$conten['form']  =&  $edit;
			$data['content']  =  $this->load->view('view_sclicont', $conten, false);
		}
	}

	function _pre_insert($do){
		$numero = $do->get('numero');
		if ( $numero == '') {
			$numero = $this->datasis->fprox_numero('nsclicont');
			$do->set('numero',$numero);
		}
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

	// Factura Mensualidades
	function lote($status=null){
		$this->load->helper('download');
		$this->genesal=false;

		$data   = '';

		if($status=='insert'){
			$codigo = $this->datasis->traevalor('SINVTARIFA');
			$iva    = $this->datasis->dameval("SELECT iva FROM sinv WHERE codigo=".$this->db->escape($codigo));
			$descrip= $this->datasis->dameval("SELECT descrip FROM sinv WHERE codigo=".$this->db->escape($codigo));
			$ut     = $this->datasis->dameval("SELECT valor FROM utributa ORDER BY fecha DESC LIMIT 1");
			$cana   = 1;

			$mSQL="SELECT TRIM(b.nombre) AS nombre, TRIM(b.rifci) AS rifci, b.cliente, b.tipo, b.dire11 AS direc, a.cantidad, a.precio, a.base, b.telefono, a.codigo, a.descrip, c.iva, a.upago
					FROM sclicont a JOIN scli b ON a.cliente=b.cliente JOIN sinv c ON a.codigo=c.codigo
				WHERE a.status = 'A'
				ORDER BY b.rifci";

			$query = $this->db->query($mSQL);
			foreach ($query->result() as $row){
				$saldo=0;
				$dbcliente= $this->db->escape($row->cliente);
				$sql      = "SELECT SUM(monto*(tipo_doc IN ('FC','GI','ND'))) AS debe, SUM(monto*(tipo_doc IN ('NC','AB','AN'))) AS haber FROM smov WHERE cod_cli=${dbcliente}";
				$qquery   = $this->db->query($sql);
				if ($qquery->num_rows() > 0){
					$rrow = $qquery->row();
					$saldo= $rrow->debe-$rrow->haber;
				}

				$saldo += $row->base*(1+($row->iva/100));
				$sql="UPDATE scli SET credito='S',tolera=10,maxtole=10,limite=${saldo},formap=30 WHERE cliente=${dbcliente}";
				$this->db->simple_query($sql);

				$desde = dbdate_to_human($row->upago.'01','m/Y');

				$_POST['btn_submit']  = 'Guardar';
				$_POST['pfac']        = '';
				$_POST['fecha']       = date('d/m/Y');
				$_POST['cajero']      = $this->secu->getcajero();
				$_POST['vd']          = $this->secu->getvendedor();
				$_POST['almacen']     = $this->secu->getalmacen();
				$_POST['tipo_doc']    = 'F';
				$_POST['factura']     = '';
				$_POST['cod_cli']     = $row->cliente;
				$_POST['sclitipo']    = '1';
				$_POST['nombre']      = $row->nombre;
				$_POST['rifci']       = $row->rifci;
				$_POST['direc']       = $row->direc;
				$_POST['upago']       = $row->upago;
				$_POST['codigoa_0']   = $row->codigo;
				$_POST['desca_0']     = $row->descrip;

				$_POST['detalle_0']   = "correspondiente al mes ${desde}";
				$_POST['cana_0']      = $row->cantidad;
				$_POST['preca_0']     = $row->precio;

				$_POST['tota_0']      = $row->base;
				$_POST['precio1_0']   = 0;
				$_POST['precio2_0']   = 0;
				$_POST['precio3_0']   = 0;
				$_POST['precio4_0']   = 0;
				$_POST['itiva_0']     = $row->iva;
				$_POST['sinvpeso_0']  = 0;
				$_POST['sinvtipo_0']  = 'Servicio';

				$_POST['tipo_0']       = '';
				$_POST['sfpafecha_0']  = '';
				$_POST['num_ref_0']    = '';
				$_POST['banco_0']      = '';
				$_POST['monto_0']      = $row->precio*(1+($row->iva/100)) ;


				ob_start();
					parent::dataedit();
					$rt = ob_get_contents();
				@ob_end_clean();

				$getdata=json_decode($rt,true);

				if($getdata['status']=='A'){
					$id=$getdata['pk']['id'];
					$url=$this->_direccion='http://localhost/'.site_url('formatos/descargartxt/FACTSER/'.$id);
					$data .= file_get_contents($url);
					$data .= "<FIN>\r\n";
				}else{
					echo $getdata['mensaje'];
				}
			}
			//force_download('inprin.prn', preg_replace("/[\r]*\n/","\r\n",$data));
		}
	}


	function instalar(){
		if (!$this->db->table_exists('sclicont')) {
			$mSQL="CREATE TABLE `sclicont` (
			  id       INT(11) NOT NULL AUTO_INCREMENT,
			  numero   CHAR(8) DEFAULT NULL,
			  status   CHAR(1) NOT NULL DEFAULT 'A' COMMENT 'Activo, Suspendido, Terminado',
			  fecha    DATE DEFAULT NULL,
			  inicio   DATE DEFAULT NULL,
			  final    DATE DEFAULT NULL,
			  cliente  CHAR(5) DEFAULT NULL,
			  codigo   VARCHAR(15) DEFAULT NULL,
			  descrip  TEXT,
			  cantidad DECIMAL(17,2) DEFAULT '0.00',
			  base     DECIMAL(17,2) DEFAULT '0.00',
			  iva      DECIMAL(17,2) DEFAULT '0.00',
			  precio   DECIMAL(17,2) DEFAULT '0.00',
			  observa  TEXT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='Contratos recurrentes'";
			$this->db->query($mSQL);
		}
		$campos=$this->db->list_fields('sclicont');
		if(!in_array('observa',$campos)){
			$this->db->simple_query('ALTER TABLE sclicont ADD COLUMN observa TEXT NULL ');
		};

	}
}
