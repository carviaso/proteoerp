<?php
class Zona extends Controller {
	var $mModulo='ZONA';
	var $titp='Zonas de ventas';
	var $tits='Zonas de ventas';
	var $url ='ventas/zona/';

	function Zona(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		//$this->datasis->modulo_id('NNN',1);
	}

	function index(){
		$this->instala();
		redirect($this->url.'jqdatag');
	}

	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grid'] = $grid->deploy();

		$bodyscript = '
		<script type="text/javascript">
		$(function() {
			$( "input:submit, a, button", ".otros" ).button();
		});

		jQuery("#a1").click( function(){
			var id = jQuery("#newapi'. $param['grid']['gridname'].'").jqGrid(\'getGridParam\',\'selrow\');
			if (id)	{
				var ret = jQuery("#newapi'. $param['grid']['gridname'].'").jqGrid(\'getRowData\',id);
				window.open(\'/proteoerp/formatos/ver/ZONA/\'+id, \'_blank\', \'width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400), screeny=((screen.availWidth/2)-300)\');
			} else { $.prompt("<h1>Por favor Seleccione un Movimiento</h1>");}
		});
		</script>';

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		$WestPanel = '
		<div id="LeftPane" class="ui-layout-west ui-widget ui-widget-content">
		<div class="otros">

		<table id="west-grid" align="center">
			<tr><td><div class="tema1">
				<table id="listados"></table>
				</div>
			</td></tr>
			<tr><td>
				<table id="otros"></table>
			</td></tr>
		</table>

		<table id="west-grid" align="center">
			<tr>
				<td><a style="width:190px" href="#" id="a1">Imprimir Copia</a></td>
			</tr>
		</table>
		</div>
		</div> <!-- #LeftPane -->';

		$SouthPanel = '
		<div id="BottomPane" class="ui-layout-south ui-widget ui-widget-content">
		<p>'.$this->datasis->traevalor('TITULO1').'</p>
		</div> <!-- #BottomPanel -->';
		$param['WestPanel']  = $WestPanel;
		//$param['EastPanel']  = $EastPanel;
		$param['SouthPanel'] = $SouthPanel;
		$param['listados'] = $this->datasis->listados('ZONA', 'JQ');
		$param['otros']    = $this->datasis->otros('ZONA', 'JQ');
		$param['tema1'] = 'darkness';
		$param['bodyscript'] = $bodyscript;
		$param['tabs'] = false;
		$param['encabeza'] = $this->titp;
		$this->load->view('jqgrid/crud',$param);
	}

	//***************************
	//Definicion del Grid y la Forma
	//***************************
	function defgrid( $deployed = false ){
		$i = 1;

		$grid  = new $this->jqdatagrid;

		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'align'    => "'center'",
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 60,
			'edittype'      => "'text'",
			'editoptions'   => '{ size:5, maxlength:4 }',
		));

		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 200,
			'edittype'      => "'text'",
			'editoptions'   => '{ size:30, maxlength:30 }',
		));

		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 300,
			'edittype'      => "'text'",
			'editoptions'   => '{ size:60, maxlength:90 }',
			'formoptions'   => '{ label:"Descripcion" }'
		));

		$grid->addField('margen');
		$grid->label('Margen');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => 'true',
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true, align:"right" }',
			'editoptions'   => '{ size:10, maxlength:10, dataInit: function(elem){ $(elem).numeric(); } }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 2 }'
		));

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'hidden'   => 'true',
			'align'    => "'center'",
			'frozen'   => 'true',
			'width'    => 40,
			'editable' => 'false',
			'search'   => 'false'
		));

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 540, height:210, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 540, height:210, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(true);
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

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
		$mWHERE = $grid->geneTopWhere('zona');

		$response   = $grid->getData('zona', array(array()), array(), false, $mWHERE );
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
		$codigo = $this->input->post('codigo');
		$data   = $_POST;
		$check  = 0;
		$mRet   = "";

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			if(false == empty($data)){
				$this->db->insert('zona', $data);
				logusu('ZONA',"Registro ".$data['codigo']." ".$data['nombre']." INCLUIDO");
			}
			$mRet = "Registro Agregado";

		} elseif($oper == 'edit') {
			$zonav  = $this->datasis->dameval("SELECT codigo FROM zona WHERE id=$id");
			if ( $codigo == $zonav){
				// Cuando la Zona es Igual el resto lo cambia sin problema
				unset($data['codigo']);
				$this->db->where( 'id',   $id);
				$this->db->update('zona', $data);
				logusu('ZONA',"Registro ".$zonav." EDITADO");
				$mRet = "Registro Modificado";

			} else {
				// Busca si esta repetida
				$check = $this->datasis->dameval("SELECT COUNT(*) FROM zona WHERE codigo=".$this->db->escape($codigo));
				if ( $check == 0 ) {
					//No esta repetida modifica en scli y sfac
					$this->db->where('id', $id);
					$this->db->update('zona', $data);
					$this->db->simple_query("UPDATE scli SET zona=".$this->db->escape($codigo)." WHERE zona=".$this->db->escape($zonav));
					$this->db->simple_query("UPDATE sfac SET zona=".$this->db->escape($codigo)." WHERE zona=".$this->db->escape($zonav));
					logusu('ZONA',"Registro ".$data['codigo']." ".$data['nombre']." INCLUIDO");
					$mRet = "Zona modificada y actualizada en clientes";
				} else {
					// Aqui deberia Fusionar
					$mRet  = "No se puede cambiar la zona a una que ya existe, debe fusionarlas<br>";
				}
			}
			echo $mRet;

		} elseif($oper == 'del') {
			$codigo = $this->datasis->dameval("SELECT codigo FROM zona WHERE id=$id");
			$check  = $this->datasis->dameval("SELECT COUNT(*) FROM scli WHERE zona=".$this->db->escape($codigo)." ");
			$check += $this->datasis->dameval("SELECT COUNT(*) FROM sfac WHERE zona=".$this->db->escape($codigo)." ");
			if ( $check > 0 ){
				echo " Esta Zona esta asociada a clientes y facturas; No se puede Eliminar!!! ";
			} else {
				$this->db->simple_query("DELETE FROM zona WHERE id=$id ");
				logusu('ZONA',"Registro zona=".$this->db->escape($codigo)."  ELIMINADO");
				echo 'Registro Eliminado';
			}
		};
	}

	function instala(){
		 $campos = $this->db->list_fields('zona');

		if (!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE zona DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE zona ADD UNIQUE INDEX codigo (codigo)');
			$this->db->simple_query('ALTER TABLE zona ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		if (!in_array('margen',$campos)){
			$this->db->simple_query("ALTER TABLE `zona` ADD COLUMN `margen` DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT '0.00'");
		}
	}
}
