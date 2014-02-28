<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
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
		$this->instalar();
		$this->rapyd->load('datafilter2');

		$dbrepo = $this->db->escape($repo);
		$mSQL   = 'SELECT proteo FROM reportes WHERE nombre='.$dbrepo;
		$mc     = $this->datasis->dameval($mSQL);
		$nombre = strtolower($repo).'.pdf';

		$_formato=$this->input->post('salformat');
		if(empty($mc)){
			$mc=$this->_crearep($repo,'proteo');
		}else{
			if(($this->db->char_set=='latin1') && ($this->config->item('charset')=='UTF-8') && $_formato!='PDF'){
				$mc=utf8_encode($mc);
			}
		}
		if(!empty($mc)){
			$sql="UPDATE reportes SET instancias = instancias+1 WHERE nombre=${dbrepo}";
			$this->db->simple_query($sql);
			if(empty($esta)) $esta=$this->datasis->dameval('SELECT modulo FROM intrarepo WHERE nombre='.$this->db->escape($repo));
			$data['regresar'] = '<a href='.site_url('/reportes/enlistar/'.$esta).'>'.image('go-previous.png','Regresar',array('border'=>0)).'Regresar'.'</a>';
			$data['regresar'].= '<p style="font-size:0.6em;text-align:center;padding:0">..::'.$repo.'::..</p>';

			switch ($_formato) {
				case 'XLS':
					$_mclase='XLSReporte';
					break;
				case 'PDF':
					$_mclase='PDFReporte';
					break;
				case 'plano':
					//$_mclase='XLSReporteplano';
					//$mc=str_replace('new PDFReporte(','new XLSReporteplano(',$mc);
					$_mclase='XLSXReporte';
					$mc=str_replace('new PDFReporte(','new XLSXReporte(',$mc);
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

	function enlistar($repo=null){
		if(empty($repo)){
			echo 'Faltan parametros';
			return '';
		}
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
			foreach($query->result_array() as $row){
				$opts[]=$row;
			}

			$this->db->select(array('a.titulo','a.mensaje','TRIM(a.nombre) AS nombre','"P" AS siste'));
			$this->db->from('intrarepo AS a');
			$this->db->join('tmenus    AS b',"CONCAT(a.modulo,'LIS')=b.modulo AND b.ejecutar LIKE CONCAT('%(_',a.nombre,'_)%')",'left');
			$this->db->where('b.codigo IS NULL');
			$this->db->where('a.modulo',$repo );
			$this->db->where('a.activo','S');
			$this->db->orderby('a.titulo');

			$query = $this->db->get();
			foreach($query->result_array() as $row){
				$opts[]=$row;
			}
		}
		$data['forma']  = '';
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

		$grupo = new dropdownField('Subcategoria', 'grupo');
		if (!empty($_POST["linea"]) AND !empty($_POST["dpto"])) {
			if($_POST["dpto"]!='T')$where .= "depto = ".$this->db->escape($_POST["dpto"]).' AND ';
			$where .= "linea = ".$this->db->escape($_POST["linea"]);
			$sql = "SELECT grupo,CONCAT_WS('-',grupo,nom_grup) FROM grup ${where}";
			$grupo->option("","");
			$grupo->options($sql);
		}else{
			$grupo->option('','Seleccione una l&iacute;nea');
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

		$campos=$this->db->list_fields('reportes');

		if(!in_array('proteo',$campos)){
			$mSQL="ALTER TABLE `reportes` ADD `proteo` TEXT NULL";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('harbour',$campos)){
			$mSQL="ALTER TABLE `reportes` ADD `harbour` TEXT NULL";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('instancias',$campos)){
			$mSQL="ALTER TABLE `reportes` ADD COLUMN `instancias` INT(11) NULL DEFAULT '0' AFTER `harbour`";
			$this->db->simple_query($mSQL);
		}

		//$mSQL="UPDATE tmenus SET ejecutar=REPLACE(ejecutar,"."'".'" )'."','".'")'."') ";
		//$this->db->simple_query($mSQL);
	}
}
