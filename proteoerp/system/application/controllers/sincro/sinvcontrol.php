<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Sinvcontrol extends Controller {
	var $mModulo = 'SINVCONTROL';
	var $titp    = 'Control de sincronizacion de inventario';
	var $tits    = 'Control de sincronizacion de inventario';
	var $url     = 'sincro/sinvcontrol/';

	function Sinvcontrol(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'SINVCONTRO', $ventana=0 );
	}

	function index(){
		//$this->datasis->creaintramenu(array('modulo'=>'000','titulo'=>'<#titulo#>','mensaje'=>'<#mensaje#>','panel'=>'<#panal#>','ejecutar'=>'<#ejecuta#>','target'=>'popu','visible'=>'S','pertenece'=>'<#pertenece#>','ancho'=>900,'alto'=>600));
		$this->instalar();
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

			$WpAdic = "<tr><td>
				<div>
					<table cellpadding='0' cellspacing='0'>
						<tr>
							<td style='vertical-align:top;text-align:center'><b>Para cambiar la condici&oacute;n de sincronizaci&oacute;n debe precioar 2 veces sobre el registro deseado</b></td>
						</tr>
						<tr>
							<td style='vertical-align:top;'><div style='text-align:center;vertical-align:middle;height:35px;background-color:#004A00; color:#FFFFFF'>Producto sincronizable y con precio fijo en la sucursal destino</div></td>
						</tr>
						<tr>
							<td style='vertical-align:top;'><div style='text-align:center;vertical-align:middle;height:35px;background-color:#203255; color:#FFFFFF'>Producto no sincronizable y precio modificable en sucursal</div></td>
						</tr>
						<tr>
							<td style='vertical-align:top;text-align:center'><b>Para modificaciones en lote puede aplicar el filtro y seleccionar alguna de las siguientes opciones:</b></td>
						</tr>
					</table>
				</div>
			</td></tr>";
			$grid->setWpAdicional($WpAdic);

		//Botones Panel Izq
		$grid->wbotonadd(array('id'=>'bmsincro',  'img'=>'images/arrow_up.png'  ,'alt' => 'Marcar todos los productos filtrados como Sincronizables'   , 'label'=>'Marcar Sincronizables'));
		$grid->wbotonadd(array('id'=>'bmnosincro','img'=>'images/arrow_down.png','alt' => 'Marcar todos los productos filtrados como NO Sincronizables', 'label'=>'Marcar NO Sincronizables'));
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
		$param['listados']    = $this->datasis->listados('SINVCONTRO', 'JQ');
		$param['otros']       = $this->datasis->otros('SINVCONTRO', 'JQ');
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

		/*$bodyscript .= $this->jqdatagrid->bsshow('sinvcontrol', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'sinvcontrol', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'sinvcontrol', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('sinvcontrol', $ngrid, $this->url );*/

		$bodyscript .= '
		$("#bmsincro").click(
			function(){
				$.ajax({ url: "'.site_url('sincro/sinvcontrol/msync').'/",
					success: function(data){
						alert((data));
						jQuery("'.$ngrid.'").trigger("reloadGrid");
					}
				});
			}
		);';

		$bodyscript .= '
		function tsync(id){
			var aData = $("#newapi'.$grid0.'").getRowData(id);
			$.ajax({
				type: "POST",
				url: "'.site_url('sincro/sinvcontrol/tsync').'",
				data: { precio:aData.precio, codigo:aData.codigo , sucu:aData.sucursal }
			}).done(function( msg ) {
				if(msg==""){
					jQuery("'.$ngrid.'").trigger("reloadGrid");
				}else{
					alert(msg);
				}
			});
		}';

		$bodyscript .= '
		$("#bmnosincro").click(
			function(){
				$.ajax({ url: "'.site_url('sincro/sinvcontrol/nsync').'/",
					success: function(data){
						alert((data));
						jQuery("'.$ngrid.'").trigger("reloadGrid");
					}
				});
			}
		);';

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
		$editar = 'false';

		$grid  = new $this->jqdatagrid;

		$grid->addField('precio');
		$grid->label('Sinc.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'center'",
			'width'         => 40,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:1, maxlength: 1 }',
			'cellattr'      => 'function(rowId, tv, aData, cm, rdata){
				var tips = "";
				if(aData.precio !== undefined){
					if(aData.precio=="S"){
						tips = "Producto sincronizable y fijo en la sucursal "+aData.sucursal;
					}else{
						tips = "Producto NO sincronizable";
					}
				}
				return \'title="\'+tips+\'"\';
			}'
		));


		$grid->addField('sucursal');
		$grid->label('Sucursal');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 45,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:2, maxlength: 2 }',
		));


		$grid->addField('tipo');
		$grid->label('Tipo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 90,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));

		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 100,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));


		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 250,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:50, maxlength: 50 }',
		));

		$grid->addField('grupo');
		$grid->label('Grupo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 45,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:10, maxlength: 10 }',
		));

		$grid->addField('precio1');
		$grid->label('Precio1');
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

		$grid->addField('precio2');
		$grid->label('Precio2');
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

		$grid->addField('precio3');
		$grid->label('Precio3');
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

		$grid->addField('precio4');
		$grid->label('Precio4');
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

		$grid->addField('ultimo');
		$grid->label('C.Ultimo');
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

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];},afterShowForm: function(frm){$("select").selectmenu({style:"popup"});} ');
		$grid->setAfterSubmit("$('#respuesta').html('<span style=\'font-weight:bold; color:red;\'>'+a.responseText+'</span>'); return [true, a ];");

		$grid->setOndblClickRow(',ondblClickRow: function(rid){ tsync(rid); }');

		$grid->setAfterInsertRow('
			function( rid, aData, rowe){
				if(aData.precio !== undefined){
					if(aData.precio == "S"){
						$(this).jqGrid( "setCell", rid, "precio","", {color:"#FFFFFF", background:"#004A00" });
					}else{
						$(this).jqGrid( "setCell", rid, "precio","", {color:"#FFFFFF", background:"#203255" });
					}
				}
			}
		');

		$grid->setAdd(    false);
		$grid->setEdit(   false);
		$grid->setDelete( false);
		$grid->setSearch( false);
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		//$grid->setBarOptions("addfunc: sinvcontroladd, editfunc: sinvcontroledit, delfunc: sinvcontroldel, viewfunc: sinvcontrolshow");

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
		$join = array(
			array('table'=>'sucu','join'=>'sucu.codigo=sucu.codigo'),
			array('table'=>'sinvcontrol','join'=>'sinvcontrol.codigo=sinv.codigo AND sucu.codigo=sinvcontrol.sucursal','type'=>'left')
		);

		$fields=array(
			'sinv.codigo  AS codigo',
			'sinv.descrip AS descrip',
			'sinv.precio1 AS precio1',
			'sinv.precio2 AS precio2',
			'sinv.precio3 AS precio3',
			'sinv.precio4 AS precio4',
			'sinv.ultimo  AS ultimo',
			'sucu.codigo AS sucursal',
			'COALESCE(sinvcontrol.precio,"S") AS precio',
			'sinv.grupo AS grupo',
			'sinv.tipo AS tipo',
			'CONCAT(sinv.id,sucu.codigo) AS id'
		);

		$types=array(
			'precio1'=>'real',
			'precio2'=>'real',
			'precio3'=>'real',
			'precio4'=>'real',
			'id'     =>'int'
			);

		$mWHERE   = $grid->geneSelWhere($fields,$types);

		$sucu= $this->datasis->traevalor('SUCURSAL');
		if(!empty($sucu))
			$mWHERE[] = array('', 'sucu.codigo !=', $sucu, '' );
		$response = $grid->getData('sinv', $join , $fields, false, $mWHERE);
		$rs       = $grid->jsonresult($response);
		echo $rs;

		//Guarda en la BD el Where para usarlo luego
		$querydata = array('data2' => $this->session->userdata('dtgQuery'));
		$emp = strpos($querydata['data2'],'WHERE ');

		if($emp > 0){
			$querydata['data2'] = substr( $querydata['data2'], $emp );
			$emp = strpos($querydata['data2'],'ORDER BY ');
			if($emp > 0){
				$querydata['data2'] = substr( $querydata['data2'], 0, $emp );
			}
		}else{
			$querydata['data2'] = '';
		}

		$ids = $this->datasis->guardasesion($querydata);
	}

	//******************************************************************
	// Guarda la Informacion del Grid o Tabla
	//
	function setData(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$mcodp  = '??????';
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){

		} elseif($oper == 'edit') {

		} elseif($oper == 'del') {

		}
	}


	//Marca dbclick
	function tsync(){
		$sucu  = $this->input->post('sucu');
		$precio= $this->input->post('precio');
		$codigo= $this->input->post('codigo');
		//$rt=array('status'=>'B');
		if($sucu!==false && $precio!==false && $codigo!==false){
			$dbsucu  = $this->db->escape($sucu);
			$dbcodigo= $this->db->escape($codigo);
			if($precio=='S'){
				$mSQL= "INSERT INTO sinvcontrol (sucursal, codigo, precio) VALUES (${dbsucu},${dbcodigo},'N') ON DUPLICATE KEY UPDATE precio='N'";
			}else{
				$mSQL= "DELETE FROM sinvcontrol WHERE  sucursal=${dbsucu} AND codigo=${dbcodigo}";
			}

			$ban=$this->db->simple_query($mSQL);
			if($ban==false){
				echo 'Hubo problemas con elcambio';
			}else{
				//$rt['status']='A';
				echo '';
			}
		}
	}

	//Marca sincronizables
	function msync(){
		$data = $this->datasis->damesesion();

		if(isset($data['data2'])){
			$where = $data['data2'];
		}else{
			$where='';
		}

		$mSQL="DELETE sinvcontrol FROM (`sinv`)
		INNER JOIN `sucu` ON `sucu`.`codigo`=`sucu`.`codigo`
		LEFT JOIN `sinvcontrol` ON `sinvcontrol`.`codigo`=`sinv`.`codigo` AND sucu.codigo=sinvcontrol.sucursal
		${where}";

		$ban=$this->db->simple_query($mSQL);
		if($ban==false){
			echo 'Hubo problemas con elcambio';
		}else{
			echo 'Listo';
		}
	}

	//Marca no sincronizables
	function nsync(){
		$data = $this->datasis->damesesion();
				if(isset($data['data2'])){
			$where = $data['data2'];
		}else{
			$where='';
		}

		$mSQL="INSERT INTO sinvcontrol (sucursal, codigo, precio)
		SELECT `sucu`.`codigo`,`sinv`.`codigo`,'N' FROM (`sinv`)
		INNER JOIN `sucu` ON `sucu`.`codigo`=`sucu`.`codigo`
		LEFT JOIN `sinvcontrol` ON `sinvcontrol`.`codigo`=`sinv`.`codigo` AND sucu.codigo=sinvcontrol.sucursal
		${where} ON DUPLICATE KEY UPDATE
		`sinvcontrol`.`precio` = 'N'";

		$ban=$this->db->simple_query($mSQL);
		if($ban==false){
			echo 'Hubo problemas con elcambio';
		}else{
			echo 'Listo';
		}
	}

	function instalar(){
		if(!$this->db->table_exists('sinvcontrol')){
			$mSQL="CREATE TABLE `sinvcontrol` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `sucursal` varchar(2) NOT NULL,
			  `codigo` varchar(15) NOT NULL,
			  `precio` char(1) NOT NULL COMMENT 'S modifica el precio N no modifica el precio',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `sucursal_codigo` (`sucursal`,`codigo`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('sinvcontrol');
		//if(!in_array('<#campo#>',$campos)){ }
	}
}
