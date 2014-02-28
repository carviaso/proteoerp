<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Cpla extends Controller {
	var $mModulo='CPLA';
	var $titp='Plan de Cuentas';
	var $tits='Plan de Cuentas';
	var $url ='contabilidad/cpla/';
	var $mensaje ='';

	function Cpla(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_nombre( 'CPLA', $ventana=0 );
	}

	function index(){
		$this->instalar();
		//$this->datasis->creaintramenu(array('modulo'=>'000','titulo'=>'<#titulo#>','mensaje'=>'<#mensaje#>','panel'=>'<#panal#>','ejecutar'=>'<#ejecuta#>','target'=>'popu','visible'=>'S','pertenece'=>'<#pertenece#>','ancho'=>900,'alto'=>600));
		$this->datasis->modintramenu( 800, 600, substr($this->url,0,-1) );
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
		$param['listados']    = $this->datasis->listados('CPLA', 'JQ');
		$param['otros']       = $this->datasis->otros('CPLA', 'JQ');
		$param['temas']       = array('proteo','darkness','anexos1');
		$param['bodyscript']  = $bodyscript;
		$param['tabs']        = false;
		$param['encabeza']    = $this->titp;
		$param['tamano']      = $this->datasis->getintramenu( substr($this->url,0,-1) );
		$this->load->view('jqgrid/crud2',$param);
	}

	//***************************
	//Funciones de los Botones
	//***************************
	function bodyscript($grid0){
		$bodyscript = '<script type="text/javascript">';
		$ngrid      = '#newapi'.$grid0;

		$bodyscript .= $this->jqdatagrid->bsshow('cpla', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsadd( 'cpla', $this->url );
		$bodyscript .= $this->jqdatagrid->bsdel( 'cpla', $ngrid, $this->url );
		$bodyscript .= $this->jqdatagrid->bsedit('cpla', $ngrid, $this->url );

		//Wraper de javascript
		$bodyscript .= $this->jqdatagrid->bswrapper($ngrid);

		$bodyscript .= $this->jqdatagrid->bsfedita( $ngrid, '250', '450' );
		$bodyscript .= $this->jqdatagrid->bsfshow( '250', '450' );
		$bodyscript .= $this->jqdatagrid->bsfborra( $ngrid, '200', '400' );

		$bodyscript .= '});';
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

		$grid->addField('codigo');
		$grid->label('C&oacute;digo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 110,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:15, maxlength: 15 }',
		));

		$grid->addField('descrip');
		$grid->label('Descripci&oacute;n');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 220,
			'edittype'      => "'text'",
			'editrules'     => '{ required:true}',
			'editoptions'   => '{ size:30, maxlength: 35 }',
		));

		$grid->addField('departa');
		$grid->label('Depto.');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 50,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"N":"No usa Deptos","S":"Usa Departamento" },  style:"width:200px" }',
			'editrules'     => '{ required:true}',
		));

		$grid->addField('moneta');
		$grid->label('C.Monetaria');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 65,
			'edittype'      => "'select'",
			'editoptions'   => '{value: {"N":"No Monetaria","S":"Cuenta Monetaria" },  style:"width:200px" }',
			'editrules'     => '{ required:true}',
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

		$grid->setFormOptionsE('
			closeAfterEdit:true,
			mtype: "POST",
			width: 420, height:200,
			closeOnEscape: true,
			top: 50, left:20,
			recreateForm:true,
			afterSubmit: function(a,b){
				if (a.responseText.length > 0){
					if ( a.responseText.substr(0,1) == "A" ){
						$.prompt(a.responseText.substr(1,90) );
						return [true, a ];
					} else {
						$.prompt(a.responseText.substr(1,90));
						return [false, a ];
					}
				} else
					return [false, a];
			},
			beforeShowForm: function(frm){
				$(\'#codigo\').attr(\'readonly\',\'readonly\');
			},
			afterShowForm: function(frm){$("select").selectmenu({style:"popup"});}
		');


		$grid->setFormOptionsA('
			closeAfterAdd:true,
			mtype: "POST",
			width: 420, height:200,
			closeOnEscape: true,
			top: 50, left:20,
			recreateForm:true,
			afterSubmit: function(a,b){
				if (a.responseText.length > 0){
					if ( a.responseText.substr(0,1) == "A" ){
						$.prompt(a.responseText.substr(1,90) );
						return [true, a ];
					} else {
						$.prompt(a.responseText.substr(1,90));
						return [false, a ];
					}
				} else
					return [false, a];
			},
			beforeShowForm: function(frm){
				$(\'#codigo\').val( cplasuge() );
			},
			afterShowForm: function(frm){$("select").selectmenu({style:"popup"});}
		');


		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		$grid->setBarOptions("addfunc: cplaadd, editfunc: cplaedit, delfunc: cpladel, viewfunc: cplashow");

		$grid->setOndblClickRow('');
		$grid->setAdd(    $this->datasis->sidapuede('CPLA','INCLUIR%' ));
		$grid->setEdit(   $this->datasis->sidapuede('CPLA','MODIFICA%'));
		$grid->setDelete( $this->datasis->sidapuede('CPLA','BORR_REG%'));
		$grid->setSearch( $this->datasis->sidapuede('CPLA','BUSQUEDA%'));
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
		$mWHERE = $grid->geneTopWhere('cpla');

		$response   = $grid->getData('cpla', array(array()), array(), false, $mWHERE, 'codigo' );
		$rs = $grid->jsonresult( $response);
		echo $rs;
	}

	/**
	* Guarda la Informacion
	*/
	function setData(){
	}

	function dataedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('','cpla');
		$edit->on_save_redirect=false;

		$edit->pre_process( 'delete','_pre_delete');
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->codigo = new inputField('C&oacute;digo', 'codigo');
		$edit->codigo->rule= 'trim|required|callback_chcodigo';
		$edit->codigo->mode= 'autohide';
		$edit->codigo->size=20;
		$edit->codigo->maxlength =15 ;

		$edit->descrip = new inputField('Descripci&oacute;n', 'descrip');
		$edit->descrip->rule= 'required|trim';
		$edit->descrip->size=20;
		$edit->descrip->maxlength =35;

		$edit->departa = new dropdownField('Usa departamento', 'departa');
		$edit->departa->option('N','No');
		$edit->departa->option('S','Si');
		$edit->departa->rule = 'enum[S,N]';
		$edit->departa->style='width:80px';

		$edit->moneta = new dropdownField('Cuenta Monetaria','moneta');
		$edit->moneta->option('N','No');
		$edit->moneta->option('S','Si');
		$edit->moneta->rule = 'enum[S,N]';
		$edit->moneta->style='width:80px';

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
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

	function chcodigo($codigo){
		if (preg_match("/^[0-9]+(\.[0-9]+)*$/",$codigo)>0){
			$formato=$this->datasis->dameval('SELECT formato FROM cemp LIMIT 1');
			$farr=explode('.',$formato);
			$carr=explode('.',$codigo);
			$max =count($carr);
			$mmac=count($farr);
			if($mmac>=$max ){
				for($i=0;$i<$max;$i++){
					if(strlen($farr[$i])!=strlen($carr[$i])){
						$this->mensaje = "El codigo dado no coincide con el formato: ${formato}";
						$this->validation->set_message('chcodigo',$this->mensaje);
						return false;
					}
				}
			}else{
				$this->mensaje = "El codigo dado no coincide con el formato: ${formato}";
				$this->validation->set_message('chcodigo',$this->mensaje);
				return false;
			}
			$pos=strrpos($codigo,'.');
			if($pos!==false){
				$str  =substr($codigo,0,$pos);
				$dbstr=$this->db->escape($str);
				$cant=$this->datasis->dameval("SELECT COUNT(*) AS cana FROM cpla WHERE codigo=${dbstr}");
				if($cant==0){
					$this->mensaje = "No existe la cuenta padre (${str}) para registrar esa cuenta";
					$this->validation->set_message('chcodigo',$this->mensaje);
					return false;
				}
			}
		}else{
			$this->mensaje = "El c&oacute;digo parece tener formato invalido";
			$this->validation->set_message('chcodigo',$this->mensaje);
			return false;
		}
		return true;
	}

	function autocomplete($campo,$cod=FALSE){
		if($cod!==false){
			$cod=$this->db->escape_like_str($cod);
			$qformato=$this->datasis->formato_cpla();
			$data['codigo']="SELECT codigo AS c1 ,descrip AS c2 FROM cpla WHERE $campo LIKE '$cod%' AND codigo LIKE '$qformato' ORDER BY $campo LIMIT 10";
			if(isset($data[$campo])){
				$query=$this->db->query($data[$campo]);
				if($query->num_rows() > 0){
					foreach($query->result() AS $row){
						echo $row->c1.'|'.$row->c2."\n";
					}
				}
			}
		}
	}

	function _post_insert($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('descrip');
		logusu('cpla',"PLAN DE CUENTA ${codigo} NOMBRE ${nombre} CREADO");
	}

	function _post_update($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('descrip');
		logusu('cpla',"PLAN DE CUENTA ${codigo} NOMBRE ${nombre} MODIFICADO");
	}

	function _post_delete($do){
		$codigo=$do->get('codigo');
		$nombre=$do->get('descrip');
		logusu('cpla',"PLAN DE CUENTA ${codigo} NOMBRE ${nombre} ELIMINADO ");
	}

	function _pre_delete($do) {
		$codigo  =$do->get('codigo');
		$dbcodigo=$this->db->escape($codigo);
		$dbccodigo=$this->db->escape($codigo.'%');
		$check =   $this->datasis->dameval("SELECT COUNT(*) FROM cpla   WHERE codigo LIKE ${dbccodigo} AND codigo<>${dbcodigo} ");
		$check +=  $this->datasis->dameval("SELECT COUNT(*) FROM itcasi WHERE cuenta=${dbcodigo}");
		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Plan de Cuenta tiene cuentas derivadas o movimientos';
			return false;
		}
		return true;
	}

	function cplabusca() {
		$start    = isset($_REQUEST['start'])  ? $_REQUEST['start']  :  0;
		$limit    = isset($_REQUEST['limit'])  ? $_REQUEST['limit']  : 25;
		$cuenta   = isset($_REQUEST['cuenta']) ? $_REQUEST['cuenta'] : '';
		$semilla  = isset($_REQUEST['query'])  ? $_REQUEST['query']  : '';

		$semilla = trim($semilla);

		$long = $this->datasis->dameval('SELECT LENGTH(TRIM(formato)) FROM cemp LIMIT 1');
		if($long===null) $long=0;
		$mSQL = '';

		$mSQL = "SELECT codigo item, CONCAT(codigo, ' ', descrip) valor FROM cpla WHERE LENGTH(TRIM(codigo))=$long ";
		if(strlen($semilla)>0 ){
			$mSQL .= " AND ( codigo LIKE '$semilla%' OR descrip LIKE '%$semilla%' ) ";
		} else {
			if ( strlen($cuenta)>0 ) $mSQL .= " AND ( codigo LIKE '$cuenta%' OR descrip LIKE '%$cuenta%' ) ";
		}
		$mSQL .= "ORDER BY descrip ";
		$results = $this->db->count_all('cpla');

		if ( empty($mSQL)) {
			echo '{success:true, message:"mSQL vacio, Loaded data", results: 0, data:'.json_encode(array()).'}';
		} else {
			$mSQL .= " limit $start, $limit ";
			$query = $this->db->query($mSQL);
			$arr = array();
			foreach ($query->result_array() as $row)
			{
				$meco = array();
				foreach( $row as $idd=>$campo ) {
					$meco[$idd] = utf8_encode($campo);
				}
				$arr[] = $meco;
			}
			echo '{success:true, message:"mSQL", results:'. $results.', data:'.json_encode($arr).'}';
		}
	}

	function instalar(){
		$campos=$this->db->list_fields('cpla');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE cpla DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE cpla ADD UNIQUE INDEX codigo (codigo)');
			$this->db->simple_query('ALTER TABLE cpla ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}
	}
}
