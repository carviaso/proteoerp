<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class acdatasis extends Controller {
	var $mModulo='VIEW_TMENUSACC';
	var $titp='Accesos a los Modulo';
	var $tits='Accesos a los Modulo';
	var $url ='supervisor/acdatasis/';

	function acdatasis(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
	}

	function index(){
		if(!$this->datasis->iscampo('sida','proteo')){
			$this->db->simple_query('ALTER TABLE sida ADD COLUMN proteo CHAR(1)');
		}
		redirect($this->url.'jqdatag');
	}


	//***************************
	//Layout en la Ventana
	//
	//***************************
	function jqdatag(){

		$grid = $this->defgrid();
		$param['grid'] = $grid->deploy();

		$grid1Id = '#newapi'. $param['grid']['gridname'];

		$bodyscript = '
		<script type="text/javascript">
		$(function() {
			$( "input:submit, a, button", ".otros" ).button();
		});

		$("#bcopiar").click( function(){
			var usuario = $("#usuario").val();
			var ucopia  = $("#copia").val();
			if ( ucopia == usuario ){
				$.prompt("<h1>Usuario y copia iguales!!!</h1>");
			} else {
				if (ucopia)	{
					esperar(\''.site_url("supervisor/acdatasis/copia/").'\'+"/"+usuario+"/"+ucopia);
				} else {
					$.prompt("<h1>Por favor Seleccione un Usuario del cual Copiar!!!</h1>");
				}
			}
		});
		</script>';

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		$mSQL = "SELECT us_codigo, CONCAT(us_codigo,' ', us_nombre) FROM usuario WHERE supervisor='N' ORDER BY us_codigo";

		$WestPanel = '
			<div id="LeftPane" class="ui-layout-west ui-widget ui-widget-content">
			<div class="anexos">

			<table id="west-grid" align="center">
				<tr>
				<td>
					<table width="100%"><tr>
						<td style="font-size:16px;font-weight:bold;">Usuario:</td>
						<td>&nbsp;'.$this->datasis->llenaopciones($mSQL, false, $id='usuario' ).'</td>
						<td rowspan="2"><div class="otros"><a style="width:70px;height:30px;" href="#" id="bcopiar">Copiar</a></div><td>
					</tr><tr>
						<td style="font-size:16px;font-weight:bold;">Copiar de:</td><td>&nbsp;'.
							$this->datasis->llenaopciones($mSQL, true, $id='copia' )
						.'</td>
					</tr></table>
				</td>
				</tr>
				<tr>
					<td><div class="tema1"><table id="titulos"></table></div></td>
				</tr>
			</table>
			<table id="west-grid" align="center">
				<tr>
					<td></td>
				</tr>
			</table>
			</div>
			</div> <!-- #LeftPane -->';


		$SouthPanel = '
			<div id="BottomPane" class="ui-layout-south ui-widget ui-widget-content">
			<p>'.$this->datasis->traevalor('TITULO1').'</p>
			</div> <!-- #BottomPanel -->';

		$funciones = '
			$("#titulos").jqGrid({
				datatype: "local",
				height: \'360\',
				colNames:[\'Id\',\'Modulo\',\'Nombre\'],
				colModel:[
					{name:\'id\',    index:\'id\',     hidden:true},
					{name:\'modulo\',index:\'modulo\', width:60},
					{name:\'nombre\',index:\'nombre\', width:200}
				],
				multiselect: false,
				hiddengrid: false,
				width: 375,
				caption: "Reportes",
				ondblClickRow: function(id, row, col, e){
					var ret = $("#titulos").getRowData(id);
					jQuery("'.$grid1Id.'").jqGrid(\'setGridParam\',{url:"'.site_url($this->url.'getdata/').'/"+ret.modulo+"/"+$("#usuario").val(), page:1});
					jQuery("'.$grid1Id.'").trigger("reloadGrid");
				}
			});
			'.$this->datasis->menuMod().'

			for(var i=0;i<=datamenu.length;i++) jQuery("#titulos").jqGrid(\'addRowData\',i+1,datamenu[i]);';

		$postready = '';

		$param['WestPanel']  = $WestPanel;
		//$param['onclick1']   = $onclick1;
		$param['WestSize']   = 390;
		$param['funciones']  = $funciones;
		//$param['postready']  = $postready;
		//$param['EastPanel']  = $EastPanel;
		$param['SouthPanel'] = $SouthPanel;
		$param['listados']   = $this->datasis->listados('VIEW_TMENUSACC', 'JQ');
		$param['otros']      = $this->datasis->otros('VIEW_TMENUSACC', 'JQ');
		$param['tema1']      = 'darkness';
		$param['anexos']     = 'anexos1';
		$param['bodyscript'] = $bodyscript;
		$param['tabs']       = false;
		$param['encabeza']   = $this->titp;

		$this->load->view('jqgrid/crud',$param);
	}

	//***********************************
	//Definicion del Grid y la Forma
	//***********************************
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
			'editable' => $editar,
			'search'   => 'false',
			'hidden'   => 'true'
		));

		$grid->addField('modulo');
		$grid->label('M&oacute;dulo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 70,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 10 }',
		));

		$grid->addField('secu');
		$grid->label('Sec.');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'false',
			'editable'      => $editar,
			'width'         => 40,
			'edittype'      => "'text'",
		));

		$grid->addField('nombre');
		$grid->label('Nombre');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 60 }',
		));


		$grid->addField('acceso');
		$grid->label('Acceso');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'false',
			'editable'      => 'true',
			'width'         => 40,
			'edittype'      => "'checkbox'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{  value: "S:N" }',
		));

		$grid->addField('usuario');
		$grid->label('Usuario');
		$grid->params(array(
			'align'         => "'center'",
			'search'        => 'true',
			'editable'      => 'true',
			'width'         => 40,
			'edittype'      => "'text'",
			'hidden'        => 'true',
			'editoptions'   => "{ readonly: 'readonly'}"
		));

		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('390');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('-');
		$grid->setFormOptionsA('-');
		$grid->setAfterSubmit('-');

		$grid->setonSelectRow('function(id){
					if(id && id !== lastsel2){
						jQuery(gridId1).jqGrid(\'saveRow\',lastsel2);
						jQuery(gridId1).jqGrid(\'editRow\',id,true);
						lastsel2=id;
					}
				}
		');

		$onclick1 = '
			,ondblClickRow: function(id){
				grid.editRow(id, {mtype:\'POST\'});
				return;
			}
		';
		$grid->setOndblClickRow($onclick1);

		#show/hide navigations buttons
		$grid->setAdd(false);
		$grid->setEdit(false);
		$grid->setDelete(false);
		$grid->setSearch(false);
		$grid->setRowNum(100);
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

	/*********************************************
	* Busca la data en el Servidor por json
	*/
	function getdata(){
		$grid   = $this->jqdatagrid;
		$modulo = $this->uri->segment(4);
		$rs = '';
		if($modulo){
			$usuario = $this->uri->segment(5);
			// CREA SI FALTA ALGUNO
			$dbusuario = $this->db->escape(trim($usuario));
			$mSQL = "INSERT IGNORE INTO sida (usuario, modulo, acceso, proteo)  SELECT ${dbusuario} usuario, codigo modulo, 'N' acceso, 'N' proteo FROM tmenus";
			$this->db->simple_query($mSQL);

			if($modulo == 'MENUDTS'){
				$mSQL  = "SELECT a.codigo id, a.modulo, a.secu, a.titulo nombre, b.acceso, b.usuario ";
				$mSQL .= "FROM tmenus a LEFT JOIN sida b ON a.codigo = b.modulo ";
				$mSQL .= "AND b.usuario=${dbusuario} ";
				$mSQL .= "WHERE a.modulo <> 'MENUINT' AND a.modulo REGEXP '^[1-9][0-9]*$'  ";
				$mSQL .= "ORDER BY modulo,secu";
			}else{
				$mSQL  = "SELECT a.codigo id, a.modulo, a.secu, a.titulo nombre, b.acceso, b.usuario ";
				$mSQL .= "FROM tmenus a LEFT JOIN sida b ON a.codigo = b.modulo ";
				$mSQL .= "AND b.usuario=${dbusuario} ";
				$mSQL .= "WHERE a.modulo <> 'MENUINT' AND NOT a.modulo REGEXP '^[1-9][0-9]*$' ";
				$mSQL .= "AND a.modulo LIKE ".$this->db->escape($modulo."%");
				$mSQL .= "ORDER BY modulo,secu";
			}
			$response   = $grid->getDataSimple($mSQL);
			$rs = $grid->jsonresult( $response);
		}
		echo $rs;
	}

	/***********************************************
	* Guarda la Informacion
	*/
	function setData(){
		$this->load->library('jqdatagrid');
		$oper   = $this->input->post('oper');
		$id     = $this->input->post('id');
		$data   = $_POST;
		$check  = 0;

		unset($data['oper']);
		unset($data['id']);
		if($oper == 'add'){
			echo "Fallo Agregado!!!";

		}elseif($oper == 'edit'){
			$id       = $this->input->post('id');
			$usuario  = $this->input->post('usuario');
			unset($data['usuario']);
			$this->db->where('modulo' , $id);
			$this->db->where('usuario', $usuario);
			$this->db->update('sida'  , $data);
			logusu('SIDA',"REGISTRO ${id}, ${usuario}  MODIFICADO");
			echo 'Registro Modificado';
		}elseif($oper == 'del'){
			if($check > 0){
				echo " El registro no puede ser eliminado; tiene movimiento ";
			}else{

			}
		}
	}

	function copia(){
		$usuario = trim($this->uri->segment(4));
		$copia   = trim($this->uri->segment(5));

		if($usuario != $copia){
			//Borra los del usuario
			$mSQL = "DELETE FROM sida WHERE usuario=? ";
			$this->db->query($mSQL, array($usuario));
			echo "Eliminado Accesos anteriores<br>";

			//Porsia Agrega desde tmenus
			$mSQL = "INSERT IGNORE INTO sida (usuario, modulo, acceso) SELECT ? usuario, codigo modulo, 'N' FROM tmenus ";
			$this->db->query($mSQL, array($copia));
			echo 'Insertados Accesos Faltantes<br>';

			//Copia desde el usuario copia
			$mSQL = "INSERT INTO sida (usuario, modulo, acceso) SELECT ? usuario, modulo, acceso FROM sida WHERE usuario=? ";
			$this->db->query($mSQL, array($usuario, $copia ));
			echo "Insertados Nuevos Accesos <br>";

			echo "<h1>El Usuario ${usuario} ahora tiene los accesos de ${copia}</h1>";
			logusu('SIDA',"Copiado accesos de ${usuario} a  ${copia}");
		} else {
			echo "<h1>Usuarios origen y destino iguales ${usuario} = ${copia}</h1>";
		}
	}

}
