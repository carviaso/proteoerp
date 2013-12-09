<?php
class Bmov extends Controller {
	var $mModulo='BMOV';
	var $titp='Movimiento de Bancos';
	var $tits='Movimiento de Bancos';
	var $url ='finanzas/bmov/';

	function Bmov(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'BMOV', $ventana=0 );
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

		$readyLayout = $grid->readyLayout2( 212	, 115, $param['grids'][0]['gridname']);

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		$mSQL1     = "SELECT codbanc codigo, tbanco banco, format(saldo,2) saldo, numcuent FROM banc WHERE activo='S' ORDER BY IF(tbanco='CAJ','ZZZ',tbanco) ";
		$colModel = "{name:'codigo',index:'codigo', label:'Cod', width:15 }, {name:'banco',index:'banco', label:'Bco', width:25}, {name:'saldo', index:'saldo', label:'Saldo', align:'right', width:80}, {name:'numcuent', index:'numcuent', label:'Nro. Cuenta', align:'center', width:140} ";

		//$datos = $this->datasis->jqdata($mSQL1,'databanc');
		$funciones = $this->datasis->jqtablawest('saldobanc', 'Saldo de Bancos', $colModel,  $mSQL1);

		$WpAdic = "<tr><td><div class=\"tema1\"><table id=\"saldobanc\"></table></div></td></tr>\n";
		$grid->setWpAdicional($WpAdic);

		//Botones Panel Izq
		//$grid->wbotonadd(array('id'=>'bimpriau', 'img'=>'assets/default/images/print.png', 'alt' => 'Imprimir Auditoria', 'label'=>'Imprimir Auditoria' ));
		$WestPanel = $grid->deploywestp();

		//Panel Central y Sur
		$centerpanel = $grid->centerpanel( $id = 'adicional', $param['grids'][0]['gridname'] );

		$adic = array(
			array('id'=>'fedita',  'title'=>'Agregar/Editar Registro')
		);
		$SouthPanel = $grid->SouthPanel($this->datasis->traevalor('TITULO1'), $adic);

		$funciones .= '
		function ltransac(el, val, opts){
			var link=\'<div><a href="#" onclick="tconsulta(\'+"\'"+el+"\'"+\');">\' +el+ \'</a></div>\';
			return link;
		};';

		$param['WestPanel']    = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['readyLayout']  = $readyLayout;
		$param['SouthPanel']   = $SouthPanel;
		$param['listados']     = $this->datasis->listados('BMOV', 'JQ');
		$param['otros']        = $this->datasis->otros('BMOV', 'JQ');

		$param['centerpanel']  = $centerpanel;
		$param['funciones']    = $funciones;
		$param['tema1']        = 'darkness';
		$param['anexos']       = 'anexos1';
		$param['bodyscript']   = $bodyscript;
		$param['tabs']         = false;
		$param['encabeza']     = $this->titp;

		$this->load->view('jqgrid/crud2',$param);
	}

	//***************************
	//Funciones de los Botones
	//fuera del doc ready
	//***************************
	function bodyscript( $grid0 ){
		$bodyscript = '<script type="text/javascript">';

		$bodyscript .= '
		function tconsulta(transac){
			if (transac)	{
				window.open(\''.site_url('contabilidad/casi/localizador/transac/procesar').'/\'+transac, \'_blank\', \'width=800, height=600, scrollbars=yes, status=yes, resizable=yes,screenx=((screen.availHeight/2)-300), screeny=((screen.availWidth/2)-400)\');
			} else {
				$.prompt("<h1>Transaccion invalida</h1>");
			}
		};';

		$bodyscript .= '
		$("#a1").click( function(){
			var id = jQuery("#newapi'. $grid0.'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'.$grid0.'").jqGrid(\'getRowData\',id);
				window.open(\''.site_url('formatos/ver/BMOV/').'/\'+id, \'_blank\', \'width=900,height=800,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-450), screeny=((screen.availWidth/2)-400)\');
			} else { $.prompt("<h1>Por favor Seleccione un Movimiento</h1>");}
		});';

		$bodyscript .= '</script>';
		return $bodyscript;
	}




	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'    => "'center'",
			'frozen'   => 'true',
			'width'    => 60,
			'editable' => 'false',
			'search'   => 'false'
		));

		$grid->addField('codbanc');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'align'      => "'center'",
			'search'     => 'true',
			'editable'   => $editar,
			'width'      => 40,
			'edittype'   => "'text'",
		));

/*
		$grid->addField('moneda');
		$grid->label('Moneda');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
		));
*/

		$grid->addField('numcuent');
		$grid->label('N.Cuenta');
		$grid->params(array(
			'search'     => 'true',
			'editable'   => $editar,
			'width'      => 150,
			'edittype'   => "'text'",
		));


		$grid->addField('banco');
		$grid->label('Banco');
		$grid->params(array(
			'search'     => 'true',
			'editable'   => $editar,
			'width'      => 120,
			'edittype'   => "'text'",
		));

/*
		$grid->addField('saldo');
		$grid->label('Saldo');
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
*/

		$grid->addField('tipo_op');
		$grid->label('Tipo');
		$grid->params(array(
			'align'      => "'center'",
			'search'     => 'true',
			'editable'   => $editar,
			'width'      => 40,
			'edittype'   => "'text'",
		));


		$grid->addField('numero');
		$grid->label('N&uacute;mero');
		$grid->params(array(
			'search'     => 'true',
			'editable'   => $editar,
			'width'      => 90,
			'edittype'   => "'text'",
		));


		$grid->addField('fecha');
		$grid->label('Fecha');
		$grid->params(array(
			'search'      => 'true',
			'editable'    => $editar,
			'width'       => 80,
			'align'       => "'center'",
			'edittype'    => "'text'",
			'editrules'   => '{ required:true,date:true}',
			'formoptions' => '{ label:"Fecha" }'
		));


		$grid->addField('monto');
		$grid->label('Monto');
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


		$grid->addField('benefi');
		$grid->label('Beneficiario');
		$grid->params(array(
			'search'     => 'true',
			'editable'   => $editar,
			'width'      => 200,
			'edittype'   => "'text'",
		));


		$grid->addField('concepto');
		$grid->label('Concepto');
		$grid->params(array(
			'search'     => 'true',
			'editable'   => $editar,
			'width'      => 200,
			'edittype'   => "'text'",
		));


		$grid->addField('concep2');
		$grid->label('Concepto 2');
		$grid->params(array(
			'search'     => 'true',
			'editable'   => $editar,
			'width'      => 200,
			'edittype'   => "'text'",
		));


		$grid->addField('concep3');
		$grid->label('Concepto 3');
		$grid->params(array(
			'search'     => 'true',
			'editable'   => $editar,
			'width'      => 200,
			'edittype'   => "'text'",
		));



		$grid->addField('clipro');
		$grid->label('Cli/Pro');
		$grid->params(array(
			'search'     => 'true',
			'editable'   => $editar,
			'width'      => 40,
			'edittype'   => "'text'",
		));


		$grid->addField('codcp');
		$grid->label('Codcp');
		$grid->params(array(
			'search'     => 'true',
			'editable'   => $editar,
			'width'      => 50,
			'edittype'   => "'text'",
		));


		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
		));


/*
		$grid->addField('documen');
		$grid->label('Documento');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
		));
*/

		$grid->addField('comprob');
		$grid->label('Comprobante');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 60,
			'edittype'      => "'text'",
		));


		$grid->addField('status');
		$grid->label('Estatus');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
		));

/*
		$grid->addField('cuenta');
		$grid->label('Cuenta');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
		));


		$grid->addField('enlace');
		$grid->label('Enlace');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
		));
*/

		$grid->addField('bruto');
		$grid->label('Bruto');
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


		$grid->addField('comision');
		$grid->label('Comisi&oacute;n');
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


		$grid->addField('impuesto');
		$grid->label('Impuesto');
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


		$grid->addField('registro');
		$grid->label('Registro');
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


		$grid->addField('liable');
		$grid->label('Liable');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 40,
			'edittype'      => "'select'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{value: {"S":"Conciliable","N":"No Conciliable" }, style:"width:180px" }',

		));


		$grid->addField('concilia');
		$grid->label('Conciliado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'false',
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:false,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));

/*
		$grid->addField('posdata');
		$grid->label('Posdata');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));
*/

		$grid->addField('abanco');
		$grid->label('A.Banco');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
		));


		$grid->addField('anulado');
		$grid->label('Anulado');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
		));


		$grid->addField('negreso');
		$grid->label('N.Egreso');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
		));

/*
		$grid->addField('susti');
		$grid->label('Susti');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 160,
			'edittype'      => "'text'",
		));
*/

		$grid->addField('ndebito');
		$grid->label('N.D&eacute;bito');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
		));


		$grid->addField('ncausado');
		$grid->label('N.Causado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
		));


		$grid->addField('ncredito');
		$grid->label('N.Cr&eacute;dito');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
		));


		$grid->addField('transac');
		$grid->label('Transaci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
			'formatter'     => 'ltransac'
		));

		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
		));


		$grid->addField('estampa');
		$grid->label('Estampa');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->addField('hora');
		$grid->label('Hora');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 80,
			'edittype'      => "'text'",
		));

		$grid->addField('modificado');
		$grid->label('Modificado');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 140,
			'align'         => "'center'",
			'edittype'      => "'text'",
			'editrules'     => '{ required:true,date:true}',
			'formoptions'   => '{ label:"Fecha" }'
		));


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('270');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(false);
		$grid->setEdit(false);
		$grid->setDelete(false);
		$grid->setSearch(true);
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		$grid->setonSelectRow('
			function(id){
				$.ajax({
					url: "'.base_url().$this->url.'tabla/"+id,
					success: function(msg){
						//alert( "El ultimo codigo ingresado fue: " + msg );
						$("#adicional").html(msg);
					}
				});
			},afterInsertRow:
			function( rid, aData, rowe){
				if(aData.anulado == "S" ){
					$(this).jqGrid( "setCell", rid, "id","", {color:"#FFFFFF", background:"#C90623" });
				}
			}'
		);

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
	function getdata(){
		$grid       = $this->jqdatagrid;

		// CREA EL WHERE PARA LA BUSQUEDA EN EL ENCABEZADO
		$mWHERE = $grid->geneTopWhere('bmov');

		$response   = $grid->getData('bmov', array(array()), array(), false, $mWHERE, 'id', 'desc' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = intval($this->input->post('id'));
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			return 'Deshabilitado';

		}elseif($oper == 'edit'){

			if($this->datasis->sidapuede('BMOV','MODIFICA%')){
				echo 'No tiene acceso a modificar';
				return false;
			}

			$posibles=array('concilia','liable');
			foreach($data as $ind=>$val){
				if(!in_array($ind,$posibles)){
					echo 'Campo no permitido ('.$ind.')';
					return false;
				}
			}

			$this->db->where('id', $id);
			$this->db->update('bmov', $data);
			logusu('BMOV',"Movimiento de bancos  ".$id." MODIFICADO");
			echo 'Movimiento Modificado';
			//return "Registro Modificado";

		} elseif($oper == 'del') {
			echo 'Deshabilitado';
		}
	}

	function tabla() {
		$id = $this->uri->segment($this->uri->total_segments());

		$row = $this->datasis->damereg("SELECT clipro, tipo_op, numero, estampa, transac FROM bmov WHERE id=$id");

		$transac  = $row['transac'];
		$numero   = $row['numero'];
		$tipo_doc = $row['tipo_op'];
		$estampa  = $row['estampa'];

		$td1  = "<td style='border-style:solid;border-width:1px;border-color:#78FFFF;' valign='top' align='center'>\n";
		$td1 .= "<table width='98%'>\n<caption style='background-color:#5E352B;color:#FFFFFF;font-style:bold'>";

		$mSQL = "SELECT cod_prv, MID(nombre,1,25) nombre, tipo_doc, numero, monto, abonos
			FROM sprm WHERE transac='$transac' ORDER BY cod_prv ";

		$query = $this->db->query($mSQL);
		$codcli = 'XXXXXXXXXXXXXXXX';
		$salida = '<table width="100%"><tr>';
		$saldo  = 0;
		if($query->num_rows() > 0){
			$salida .= $td1;
			$salida .= "Movimiento en Proveedores</caption>";
			$salida .= "<tr bgcolor='#E7E3E7'><td>Nombre</td><td>Tp</td><td align='center'>Numero</td><td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row){
				if ( $row['tipo_doc'] == 'FC' ) {
					$saldo = $row['monto']-$row['abonos'];
				}
				$salida .= "<tr>";
				$salida .= "<td>".$row['cod_prv'].'-'.$row['nombre']."</td>";
				$salida .= "<td>".$row['tipo_doc']."</td>";
				$salida .= "<td>".$row['numero'].  "</td>";
				$salida .= "<td align='right'>".nformat($row['monto']).   "</td>";
				$salida .= "</tr>";
			}
			$salida .= "<tr bgcolor='#d7c3c7'><td colspan='4' align='center'>Saldo : ".nformat($saldo). "</td></tr>";
			$salida .= "</table></td>";
		}

		$mSQL = "SELECT cod_cli, MID(nombre,1,25) nombre, tipo_doc, numero, monto, abonos
			FROM smov WHERE transac='$transac' ORDER BY cod_cli ";
		$query = $this->db->query($mSQL);
		$codcli = 'XXXXXXXXXXXXXXXX';
		$saldo = 0;
		if ( $query->num_rows() > 0 ){
			$salida .= $td1;
			$salida .= "Movimiento en Clientes</caption>";
			$salida .= "<tr bgcolor='#e7e3e7'><td>Nombre</td><td>Tp</td><td align='center'>N&uacute;mero</td><td align='center'>Monto</td></tr>";
			foreach ($query->result_array() as $row){
				if ( $row['tipo_doc'] == 'FC' ) {
					$saldo = $row['monto']-$row['abonos'];
				}
				$salida .= "<tr>";
				$salida .= "<td>".$row['cod_cli'].'-'.$row['nombre']."</td>";
				$salida .= "<td>".$row['tipo_doc']."</td>";
				$salida .= "<td>".$row['numero'].  "</td>";
				$salida .= "<td align='right'>".nformat($row['monto']).   "</td>";
				$salida .= "</tr>";
			}
			//$salida .= "<tr bgcolor='#d7c3c7'><td colspan='4' align='center'>Saldo : ".nformat($saldo). "</td></tr>";
			$salida .= "</table></td>";
		}


		echo $salida.'</tr></table>';
	}

	function instalar(){
		$campos=$this->db->list_fields('bmov');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE bmov DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE bmov ADD UNIQUE INDEX idunico (codbanc, tipo_op, numero)');
			$this->db->simple_query('ALTER TABLE bmov ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}
		$this->datasis->creaintramenu(array('modulo'=>'51E','titulo'=>'Movimientos de Bancos','mensaje'=>'Movimientos de Bancos','panel'=>'TESORERIA','ejecutar'=>'finanzas/bmov','target'=>'popu','visible'=>'S','pertenece'=>'5','ancho'=>900,'alto'=>600));

	}

}
