<?php

class Reportes extends Controller{
	var $cargo=0;
	var $opciones=array();

	function Reportes(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->opciones=array('PDF'=>'pdf','XLS'=>'xls','plano'=>'xls (plano)','HTML'=>'html');
	}

	function index($repo=false){
		if($this->session->userdata('usuario')=='') redirect('/ajax/reccierraventana');
		$dbrepo = $this->db->escape('%'.$repo);

		$data['pre']   = $repo;
		$data['titu']  = "Listados ${repo} ".$this->session->userdata('usuario');
		$data['repo']  = $repo;
		$data['titulo']= $this->datasis->dameval("SELECT titulo FROM intramenu a WHERE a.panel='REPORTES' AND a.ejecutar LIKE ${dbrepo}");
		$this->load->view('view_repoframe',$data);
	}

	function ver($repo,$esta=null){
		$this->rapyd->load('datafilter2');

		$mSQL= 'SELECT proteo FROM reportes WHERE nombre='.$this->db->escape($repo);
		$mc  = $this->datasis->dameval($mSQL);
		$nombre =strtolower($repo).'.pdf';

		if(empty($mc)) $mc=$this->_crearep($repo,'proteo');
		if(!empty($mc)){
			if(empty($esta)) $esta=$this->datasis->dameval('SELECT modulo FROM intrarepo WHERE nombre='.$this->db->escape($repo));
			$data['regresar']='<a href='.site_url('/reportes/enlistar/'.$esta).'>'.image('go-previous.png','Regresar',array('border'=>0)).'Regresar'.'</a>';

			$_formato=$this->input->post('salformat');

			switch ($_formato) {
				case 'XLS':
					$_mclase='XLSReporte';
					break;
				case 'PDF':
					$_mclase='PDFReporte';
					break;
				case 'plano':
					$_mclase='XLSReporteplano';
					$mc=str_replace('new PDFReporte(','new XLSReporteplano(',$mc);
					break;
				case 'HTML':
					$_mclase='HTMLReporte';
					break;
				default:
					$_mclase='PDFReporte';
			}

			$this->load->library($_mclase);
			$this->db->_escape_char='';
			$this->db->_protect_identifiers=false;
			eval($mc);
		} else {
			echo 'Reporte '.$repo.' no definido para ProteoERP <br>';
			echo '<a href='.site_url('/reportes/enlistar/'.$esta).'>Regresar</a>';
		}
	}

	function enlistar($repo){
		$repo=strtoupper($repo);

		$this->rapyd->load('datatable');
		$this->rapyd->config->set_item('theme','clean');

		$mSQL="UPDATE tmenus SET ejecutar=REPLACE(ejecutar,"."'".'( "'."','".'("'."') WHERE modulo LIKE '%LIS'";
		$this->db->simple_query($mSQL);

		$mSQL="UPDATE tmenus SET ejecutar=REPLACE(ejecutar,"."'".'" )'."','".'")'."') WHERE modulo LIKE '%LIS'";
		$this->db->simple_query($mSQL);

		if($repo){
			$opts=array();

			$this->db->_escape_char='';
			$this->db->_protect_identifiers=false;

			$sel = array("CONCAT(a.secu,' ',a.titulo) AS titulo",'TRIM(a.mensaje) AS mensaje', "TRIM(REPLACE(MID(a.ejecutar,10,30),"."'".'")'."','')) AS nombre",'"D" AS siste');
			$this->db->select($sel);
			$this->db->from('tmenus   AS a');
			$this->db->join('sida     AS b','a.codigo=b.modulo');
			$this->db->join('reportes AS d',"REPLACE(MID(a.ejecutar,10,30),"."'".'")'."','')=d.nombre");
			$this->db->where('b.acceso','S');
			$this->db->where('b.usuario',$this->session->userdata('usuario') );
			$this->db->like('a.ejecutar','REPOSQL', 'after');
			$this->db->where('a.modulo',$repo.'LIS');
			$this->db->orderby('a.secu');

			$query = $this->db->get();
			foreach ($query->result_array() as $row){
				$opts[]=$row;
			}

			$this->db->select(array('a.titulo','a.mensaje','TRIM(a.nombre) AS nombre','"P" AS siste'));
			$this->db->from('intrarepo AS a');
			$this->db->join('tmenus    AS b',"CONCAT(a.modulo,'LIS')=b.modulo AND b.ejecutar LIKE CONCAT('%',a.nombre,'%')",'left');
			$this->db->where('b.codigo IS NULL');
			$this->db->where('a.modulo',$repo );
			$this->db->where('a.activo','S');
			$this->db->orderby('a.titulo');

			$query = $this->db->get();
			foreach ($query->result_array() as $row){
				$opts[]=$row;
			}

			/*
			$grid = new DataTable();
			$grid->db->_escape_char='';
			$grid->db->_protect_identifiers=false;

			$grid->db->select("CONCAT(a.secu,' ',a.titulo) titulo, a.mensaje, TRIM(REPLACE(MID(a.ejecutar,10,30),"."'".'")'."','')) AS nombre");
			$grid->db->from('tmenus    a');
			$grid->db->join('sida      b','a.codigo=b.modulo');
			$grid->db->join('reportes  d',"REPLACE(MID(a.ejecutar,10,30),"."'".'")'."','')=d.nombre");
			$grid->db->where('b.acceso','S');
			$grid->db->where('b.usuario',$this->session->userdata('usuario') );
			$grid->db->like('a.ejecutar','REPOSQL', 'after');
			$grid->db->where('a.modulo',$repo.'LIS');
			$grid->db->orderby('a.secu');

			$grid->per_row = 3;
			$grid->cell_template = '
			<div style="color:#119911; font-weight:bold; font-size:16px;">'.
			anchor('reportes/ver/<#nombre#>/'.$repo,"<#titulo#>",array('onclick'=>"parent.navegador.afiltro()")).'</div>
			<div style="color:#114411; font-weight:normal; font-size:12px;padding:4px;border-bottom: 1px solid;">
			<htmlspecialchars><#mensaje#></htmlspecialchars></div>';
			$grid->build();

			$grid1 = new DataTable();
			$grid1->db->_escape_char='';
			$grid1->db->_protect_identifiers=false;

			$grid1->db->select('a.titulo, a.mensaje, TRIM(a.nombre) AS nombre');
			$grid1->db->from('intrarepo a');
			$grid1->db->join("tmenus    b","CONCAT(a.modulo,'LIS')=b.modulo AND b.ejecutar LIKE CONCAT('%',a.nombre,'%') ","left");
			$grid1->db->where('b.codigo IS NULL');
			$grid1->db->where('a.modulo',$repo );
			$grid->db->where('a.activo','S');
			$grid1->db->orderby('a.titulo');
			$grid1->per_row = 3;
			$grid1->cell_template = '
			<div style="color:#119911; font-weight:bold; font-size:16px;background-color:#D3E3D3;padding:4px;border-left: 1px solid;">'
			.anchor('reportes/ver/<#nombre#>/'.$repo,"<#titulo#>",array('onclick'=>"parent.navegador.afiltro()")).'
			</div><div style="color:#112211; font-weight:normal; font-size:12px;padding:4px;border-left: 1px solid;">
			<htmlspecialchars><#mensaje#></htmlspecialchars></div>';
			$grid1->build();
			*/
		}
		$data['forma'] = '';
		//if($repo AND $grid->recordCount>0) {
		//	$data['forma']  = '<div style="color:#111911; font-weight:bold; font-size:10px;background-color:#F1FFF1">SISTEMA</div>';
		//	$data['forma'] .= $grid->output;
		//} else {
		//	$data['forma'] = '<p class="mainheader">No hay reportes disponibles del SISTEMA.</p>';
		//}
		//if($repo AND $grid1->recordCount>0) {
		//	$data['forma'] .= '<div style="color:#111911; font-weight:bold; font-size:10px;background-color:#F1FFF1">PROTEO</div>';
		//	$data['forma'] .=$grid1->output;
		//};

		$data['opts']   = $opts;
		$data['head']   = '';
		$data['titulo'] = '';
		$data['repo']   = $repo;
		$this->load->view('view_reportes', $data);
	}

	function cabeza(){
		$data['repo']  =$this->uri->segment(3);
		$data['nombre']=$this->uri->segment(4);
		$meco = $this->datasis->dameval("SELECT titulo FROM intramenu a WHERE a.panel='REPORTES' AND a.ejecutar LIKE '%".$data['repo']."'");
		$data['titulo']="<h1 style='font-size: 20px;color: #FFFFFF' onclick='history.back()'>".$meco."</h1>";

		$this->load->view('view_repoCabeza',$data);
	}

	function consulstatus(){
		echo 'esto es una prueba';
	}

	function sinvlineas(){
		if (!empty($_POST['dpto'])){
			$departamento=$_POST['dpto'];
		}elseif (!empty($_POST['depto'])){
			$departamento=$_POST['depto'];
		}

		$this->rapyd->load('fields');
		$where = '';
		$sql = "SELECT linea, CONCAT_WS('-',linea,descrip) FROM line $where";
		$linea = new dropdownField("Subcategoria", "linea");

		if (!empty($departamento)){
			$where = "WHERE depto = ".$this->db->escape($departamento);
			$sql = "SELECT linea,CONCAT_WS('-',linea,descrip) FROM line $where";
			$linea->option("","");
			$linea->options($sql);
		}else{
			$linea->option('','Seleccione Un Departamento');
		}
		$linea->status   = 'modify';
		$linea->onchange = 'get_grupo();';
		$linea->build();
		echo $linea->output;
	}

	function sinvgrupos(){
		$this->rapyd->load('fields');
		$where = 'WHERE ';

		$grupo = new dropdownField("Subcategoria", "grupo");
		if (!empty($_POST["linea"]) AND !empty($_POST["dpto"])) {
			if($_POST["dpto"]!='T')$where .= "depto = ".$this->db->escape($_POST["dpto"]).' AND ';
			$where .= "linea = ".$this->db->escape($_POST["linea"]);
			$sql = "SELECT grupo,CONCAT_WS('-',grupo,nom_grup) FROM grup $where";
			$grupo->option("","");
			$grupo->options($sql);
		}else{
			$grupo->option("","Seleccione una l&iacute;nea");
		}
		$grupo->status = "modify";
		$grupo->build();
		echo $grupo->output;
	}

	function modelos(){
		$this->rapyd->load("fields");
		$where = "";
		$sql = "SELECT id,modelo FROM modelos $where";
		$modelo = new dropdownField("Subcategoria", "modelo");

		if (!empty($_POST["marca"])){
		  $where = "WHERE marca = ".$this->db->escape($_POST["marca"]);
		  $sql = "SELECT id, modelo FROM modelos $where";
		  $modelo->option("","");
			$modelo->options($sql);
		}else{
			 $modelo->option('','Seleccione una Marca');
		}
		$modelo->status   = 'modify';
		//$linea->onchange = "get_grupo();";
		$modelo->build();
		echo $modelo->output;
	}

	function _crearep($nombre,$tipo='proteo'){
		$nombre = strtoupper($nombre);
		$arch = "./formrep/reportes/${tipo}/${nombre}.rep";
		if (file_exists($arch)){
			$forma=file_get_contents($arch);
			$data = array('nombre' => $nombre, $tipo => $forma);
			$mSQL = $this->db->insert_string('reportes', $data).' ON DUPLICATE KEY UPDATE proteo=VALUES(proteo)';
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){
				return '';
			}
			return $forma;
		}else{
			return '';
		}
	}

	function instalar(){
		$mSQL="ALTER TABLE `reportes` ADD `proteo` TEXT NULL";
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `reportes` ADD `harbour` TEXT NULL";
		$this->db->simple_query($mSQL);
		$mSQL="UPDATE tmenus SET ejecutar=REPLACE(ejecutar,"."'".'" )'."','".'")'."') ";
		$this->db->simple_query($mSQL);
	}
}
