<?php
class Accesos extends Controller{
	var $niveles;

	function Accesos(){
		parent::Controller();
		$this->datasis->modulo_id(904,1);
		$this->load->library('rapyd');
		$this->niveles=$this->config->item('niveles_menu');
	}

	function index(){

		$data['script']  ='<script type="text/javascript">
		$(function() {
			$("#tree").treeview({
				collapsed: true,
				animated: "medium",
				control:"#sidetreecontrol",
				persist: "location"
			});
		})
		</script>';
		$data['content'] = '<div id="sidetreecontrol"><a href="?#">Contraer todos</a> | <a href="?#">Expandir todos</a> | <a href="?#">Invertir </a></div>'.$out;
		$data['head']    = script("jquery.pack.js").script("jquery.treeview.pack.js").$this->rapyd->get_head().style('jquery.treeview.css');
		$data['title']   = '<h1>Administraci&oacute;n del Men&uacute;</h1>';
		$this->load->view('view_ventanas', $data);

	}

	function instalar(){
		for($i=1;$i<=65535;$i++)
			$this->db->simple_query("INSERT INTO serie SET hexa=HEX($i)");
		echo "hola mundo";
		//$mSQL='ALTER TABLE `intramenu` DROP PRIMARY KEY';
		//$this->db->simple_query($mSQL);
		//$mSQL='ALTER TABLE intramenu ADD id INT AUTO_INCREMENT PRIMARY KEY';
		//$this->db->simple_query($mSQL);
		//$mSQL='ALTER TABLE `intramenu` ADD `pertenece` VARCHAR(10) DEFAULT NULL NULL AFTER `visible`';
		//$this->db->simple_query($mSQL);
		//$mSQL='UPDATE intramenu SET pertenece=MID(modulo,1,1) WHERE MID(modulo,1,1)!= "0" AND modulo REGEXP  "[[:digit:]]" AND CHAR_LENGTH(modulo)>1';
		//$this->db->simple_query($mSQL);
		////ALTER TABLE `intramenu` ADD PRIMARY KEY (`modulo`)
	}
}
?>
