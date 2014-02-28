<?php
/** 
 * ProteoERP 
 * 
 * @autor    Andres Hocevar 
 * @license  GNU GPL v3
*/
class Dimensiones extends Controller {
	
	function Dimensiones(){
		parent::Controller();
		$this->olap=$this->load->database('olap',TRUE);
		//$this->load->library("rapyd");
		//$this->rapyd->set_connection('olap');
		//$this->load->database('olap',TRUE);
	}
	
	function index(){
		
	}
	
	
	function fact_ventas(){
		$dborigen=$this->db->database;
		$mSQL='DROP TABLE IF EXISTS fact_ventas';
		echo $this->olap->query($mSQL);
		
		$mSQL="CREATE TABLE fact_ventas
		SELECT 
			c.id AS fk_dim_producto,
			d.id AS fk_dim_tiempo,
			e.id AS fk_dim_cliente,
			f.id AS fk_dim_vendedor,
			g.id AS fk_dim_zona,
			a.tipo_doc,a.numero,a.referen,
			a.totals*IF(a.tipo_doc='D',-1,1) AS totals,
			a.totalg*IF(a.tipo_doc='D',-1,1) AS totalg,
			a.cajero,a.hora,a.zona,a.ciudad,a.comision,b.codigoa,b.cana,b.tota,b.pvp
		FROM ${dborigen}.sfac   AS a
		JOIN ${dborigen}.sitems AS b ON a.tipo_doc=b.tipoa AND a.numero=b.numa
		JOIN ${dborigen}.sinv   AS c ON b.codigoa=c.codigo
		JOIN dim_tiempo         AS d ON a.fecha  =d.fecha
		JOIN dim_cliente        AS e ON a.cod_cli=e.codigo
		JOIN dim_vendedor       AS f ON a.vd     =f.codigo
		JOIN dim_zona           AS g ON a.zona   =g.codigo
		WHERE a.tipo_doc<>'X' AND a.fecha>=20080101";
		
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE fact_ventas  ADD id INT AUTO_INCREMENT PRIMARY KEY NULL FIRST";
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE `fact_ventas` ENGINE = InnoDB";
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE fact_ventas ADD FOREIGN KEY (fk_dim_producto) REFERENCES dim_producto(id)";
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE fact_ventas ADD FOREIGN KEY (fk_dim_tiempo) REFERENCES dim_tiempo(id)";
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE fact_ventas ADD FOREIGN KEY (fk_dim_cliente) REFERENCES dim_cliente(id)";
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE fact_ventas ADD FOREIGN KEY (fk_dim_vendedor) REFERENCES dim_vendedor(id)";
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE fact_ventas ADD FOREIGN KEY (fk_dim_zona) REFERENCES dim_zona(id)";
		echo $this->olap->query($mSQL);
	}
	
	//**********************************
	//Herarquias
	//**********************************
	
	function her_dpto(){
		
		$tabla='her_dpto';
		$db=$this->olap->database;
		$dborigen=$this->db->database;
		echo $tabla;
		
		$mSQL="DROP TABLE IF EXISTS $tabla CASCADE";
		echo $this->olap->query($mSQL);
		
		$mSQL="CREATE TABLE $tabla
		SELECT 
		a.depto   AS codigo,
		a.descrip AS descrip
		FROM ${dborigen}.dpto AS a WHERE a.tipo='I'";
		echo $this->olap->query($mSQL);

		$mSQL="ALTER TABLE `$tabla` ENGINE = InnoDB";
		echo $this->olap->query($mSQL);

		$mSQL="ALTER TABLE $tabla  ADD id INT AUTO_INCREMENT PRIMARY KEY NULL FIRST";
		echo $this->olap->query($mSQL);
	}

	function her_line(){
		$dborigen=$this->db->database;
		$tabla='her_line';
		$db=$this->olap->database;
		echo $tabla;
		
		$mSQL="DROP TABLE IF EXISTS $tabla CASCADE";
		echo $this->olap->query($mSQL);
		
		$mSQL="CREATE TABLE $tabla
		SELECT b.id AS fk_her_dpto,
		a.linea   AS codigo,
		a.descrip AS descrip
		FROM ${dborigen}.line AS a
		JOIN $db.her_dpto AS b ON a.depto=b.codigo";
		echo $this->olap->query($mSQL);

		$mSQL="ALTER TABLE $tabla  ADD id INT AUTO_INCREMENT PRIMARY KEY NULL FIRST";
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE `$tabla` ENGINE = InnoDB";
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE $tabla ADD FOREIGN KEY (fk_her_dpto) REFERENCES her_dpto(id)";
		echo $this->olap->query($mSQL);
	}
	
	function her_grup(){
		$dborigen=$this->db->database;
		$tabla='her_grup';
		echo $tabla;
		
		
		$mSQL="DROP TABLE IF EXISTS $tabla CASCADE";
		$db=$this->olap->database;
		echo $this->olap->query($mSQL);
		
		$mSQL="CREATE TABLE $tabla
		SELECT b.id AS fk_her_line,
		a.grupo AS codigo,
		a.nom_grup AS descrip
		FROM ${dborigen}.grup AS a 
		JOIN $db.her_line AS b ON a.linea=b.codigo";
		echo $this->olap->query($mSQL);

		$mSQL="ALTER TABLE $tabla  ADD id INT AUTO_INCREMENT PRIMARY KEY NULL FIRST";
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE `$tabla` ENGINE = InnoDB";
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE $tabla ADD FOREIGN KEY (fk_her_line) REFERENCES her_line(id)";
		echo $this->olap->query($mSQL);
	}
	
	function her_grcl(){
		$dborigen=$this->db->database;
		$tabla='her_grcl';
		$db=$this->olap->database;
		echo $tabla;
		
		$mSQL="DROP TABLE IF EXISTS $tabla CASCADE";
		echo $this->olap->query($mSQL);
		
		$mSQL="CREATE TABLE $tabla
		SELECT 
		a.grupo   AS codigo,
		a.gr_desc AS descrip
		FROM ${dborigen}.grcl AS a";
		echo $this->olap->query($mSQL);

		$mSQL="ALTER TABLE `$tabla` ENGINE = InnoDB";
		echo $this->olap->query($mSQL);

		$mSQL="ALTER TABLE $tabla  ADD id INT AUTO_INCREMENT PRIMARY KEY NULL FIRST";
		echo $this->olap->query($mSQL);
	}
	
	//****************************
	//Dimensiones
	//****************************
	
	function dim_producto(){
		$dborigen=$this->db->database;
		
		$mSQL='DROP TABLE IF EXISTS fact_ventas';
		echo $this->olap->query($mSQL);
		$mSQL='DROP TABLE IF EXISTS `her_grup`';
		echo $this->olap->query($mSQL);
		$mSQL='DROP TABLE IF EXISTS `her_line`';
		echo $this->olap->query($mSQL);
		$mSQL='DROP TABLE IF EXISTS `her_dpto`';
		echo $this->olap->query($mSQL);
		
		$this->her_dpto();
		$this->her_line();
		$this->her_grup();
		echo 'dim_producto ';
		$mSQL='DROP TABLE IF EXISTS dim_producto CASCADE';
		echo $this->olap->query($mSQL);
		$db=$this->olap->database;
		$table='dim_producto';
		$mSQL="CREATE TABLE $table
		SELECT 
			a.id,
			b.id AS fk_her_grupo,
			a.codigo,CONCAT_WS(' ',a.descrip,a.descrip2,a.modelo) AS descrip,
			a.unidad,a.tipo,
			a.prov1,a.prov2,a.prov3,
			a.exmin,a.existen,a.exmax,
			a.pond,a.ultimo,
			a.margen1,a.margen2,a.margen3,a.margen4,
			a.base1,a.base2,a.base3,a.base4,
			a.precio1,a.precio2,a.precio3,a.precio4,
			a.iva,a.activo,a.dolar,a.formcal,a.peso,a.marca
		FROM ${dborigen}.sinv AS a
		JOIN ${db}.her_grup AS b ON a.grupo=b.codigo";
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE `$table` ADD PRIMARY KEY (`id`)";
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE `dim_producto` ENGINE = InnoDB";
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE dim_producto ADD FOREIGN KEY (fk_her_grupo) REFERENCES her_grup(id)";
		echo $this->olap->query($mSQL);
	}

	function dim_cliente(){
		$dborigen=$this->db->database;
		
		$table='dim_cliente';
		echo $table;
		$db=$this->olap->database;
		$mSQL="DROP TABLE IF EXISTS $table CASCADE";
		echo $this->olap->query($mSQL);
		$this->her_grcl();
		
		$mSQL="CREATE TABLE $table
		SELECT b.id AS fk_her_grcl,
			a.cliente AS codigo,a.nombre,a.grupo,a.tipo,a.tiva
		FROM ${dborigen}.scli AS a
		JOIN ${db}.her_grcl AS b ON a.grupo=b.codigo";
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE $table  ADD id INT AUTO_INCREMENT PRIMARY KEY NULL FIRST";
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE `$table` ENGINE = InnoDB";
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE `$table` ADD FOREIGN KEY (fk_her_grcl) REFERENCES her_grcl(id)";
		echo $this->olap->query($mSQL);
	}
	
	function dim_vendedor(){
		$dborigen=$this->db->database;
		
		$table='dim_vendedor';
		echo $table;
		$db=$this->olap->database;
		$mSQL="DROP TABLE IF EXISTS $table CASCADE";
		echo $this->olap->query($mSQL);
		
		$mSQL="CREATE TABLE $table
		SELECT
			a.vendedor AS codigo,
			a.nombre,a.telefono
		FROM ${dborigen}.vend AS a";
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE $table  ADD id INT AUTO_INCREMENT PRIMARY KEY NULL FIRST";
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE `$table` ENGINE = InnoDB";
		echo $this->olap->query($mSQL);
	}
	
	function dim_zona(){
		$dborigen=$this->db->database;
		
		$table='dim_zona';
		echo $table;
		$db=$this->olap->database;
		$mSQL="DROP TABLE IF EXISTS $table CASCADE";
		echo $this->olap->query($mSQL);
		
		$mSQL="CREATE TABLE $table
		SELECT
			a.codigo AS codigo,
			a.nombre,
			a.descrip
		FROM ${dborigen}.zona AS a";
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE $table  ADD id INT AUTO_INCREMENT PRIMARY KEY NULL FIRST";
		echo $this->olap->query($mSQL);
		
		$mSQL="ALTER TABLE `$table` ENGINE = InnoDB";
		echo $this->olap->query($mSQL);
	}
	
	function dim_tiempo(){
		$mSQL="CREATE TABLE IF NOT EXISTS `dim_tiempo` (
		   `id` int(11) unsigned NOT NULL auto_increment,
		  `fecha` date default NULL,
		  `anio` int(4) unsigned default NULL,
		  `mes` int(2) unsigned default NULL,
		  `dia` int(2) unsigned default NULL,
		  `semana` int(1) unsigned default NULL,
		  `aniomes` int(5) unsigned default NULL,
		  `trimestre` int(1) unsigned default NULL,
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`),
		  KEY `id_2` (`id`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1";
		echo $this->olap->query($mSQL);
		
		for($i=1 ; $i<3650 ; $i++){	
			$tempo =mktime(0, 0, 0, 1, $i, 2007);
			
			$fecha  = date("Y-m-d", $tempo);
			$semana = date("N", $tempo);
			$aniomes= date("mY", $tempo);
			$mes    = date("n", $tempo);
			$dia    = date("j", $tempo);
			$anio   = date("Y", $tempo);
			
			$trimestre=1;
			if     ($mes > 3 AND $mes <= 6) $trimestre=2;
			elseif ($mes > 6 AND $mes <= 9) $trimestre=3;
			else $trimestre=4;
			
			$data = array('fecha'    => $fecha    ,
										'anio'     => $anio     ,
										'mes'      => $mes      ,
										'dia'      => $dia      ,
										'semana'   => $semana   ,
										'aniomes'  => $aniomes  ,
										'trimestre'=> $trimestre);
			
			$mSQL=$this->db->insert_string('dim_tiempo', $data);
			echo $this->olap->query($mSQL);
		}
	}

}
?>