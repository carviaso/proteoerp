<?php
class Instalador extends Controller {
	function Instalador(){
		parent::Controller();
	}
	function index(){


		//./buscar.php
		$mSQL="CREATE TABLE IF NOT EXISTS `modbus` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `uri` varchar(50) NOT NULL default '',
		  `idm` varchar(50) NOT NULL default '',
		  `parametros` text,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=1745 DEFAULT CHARSET=latin1";

		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `stal`  CHANGE COLUMN `nombre` `nombre` TEXT NULL DEFAULT NULL";
		$this->db->simple_query($query);


		//./pasajes/tbgastos.php
		if (!$this->db->table_exists('tbgastos')) {
			$mSQL="CREATE TABLE `tbgastos` (
			  `codgas` varchar(5) NOT NULL DEFAULT '',
			  `nomgas` varchar(60) DEFAULT '',
			  `ref_liq` char(1) DEFAULT '',
			  `ref_cua` char(1) DEFAULT '',
			  `var_bus` char(1) DEFAULT '',
			  `moddes` varchar(60) DEFAULT '0',
			  `mondes` double DEFAULT '0',
			  `opcmod` varchar(1) DEFAULT NULL,
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `codgas` (`codgas`),
			  KEY `ref_cua` (`ref_cua`)
			) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('tbgastos');
		//if(!in_array('<#campo#>',$campos)){ }


		//./pasajes/tbpasa.php
		if (!$this->db->table_exists('tbpasa')) {
			$mSQL="CREATE TABLE `tbpasa` (
			  `nropasa` double NOT NULL AUTO_INCREMENT,
			  `codppr` varchar(20) DEFAULT NULL,
			  `nacio` varchar(10) DEFAULT NULL,
			  `codcli` varchar(20) DEFAULT '',
			  `nomcli` varchar(150) DEFAULT '',
			  `codcarnet` varchar(20) DEFAULT '',
			  `dtn` varchar(100) DEFAULT '',
			  `fecven` varchar(15) DEFAULT '',
			  `tippas` varchar(5) DEFAULT '',
			  `anula` char(1) DEFAULT '',
			  `prepas` double DEFAULT '0',
			  `seguro` double DEFAULT '0',
			  `mondes` double DEFAULT '0',
			  `moncomi` double DEFAULT '0',
			  `codofi` varchar(5) DEFAULT '',
			  `tipven` varchar(10) DEFAULT '',
			  `horpas` varchar(20) DEFAULT '',
			  `codptos` double DEFAULT '0',
			  `coddes` varchar(15) DEFAULT '',
			  `usuario` varchar(50) DEFAULT '',
			  `tippag` varchar(10) DEFAULT NULL,
			  `tasa` double DEFAULT '0',
			  `codrut` varchar(10) DEFAULT NULL,
			  `fecpas` varchar(15) DEFAULT NULL,
			  UNIQUE KEY `nropasa` (`nropasa`),
			  KEY `codptos` (`codptos`),
			  KEY `codofi` (`codofi`),
			  KEY `fecven` (`fecven`),
			  KEY `tipven` (`tipven`),
			  KEY `codcli` (`codcli`),
			  KEY `codppr` (`codppr`),
			  KEY `usuario` (`usuario`)
			) ENGINE=MyISAM AUTO_INCREMENT=6863833 DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('tbpasa');
		//if(!in_array('<#campo#>',$campos)){ }


		//./pasajes/tbrutas.php
		if (!$this->db->table_exists('tbrutas')) {
			$mSQL="CREATE TABLE `tbrutas` (
			  `tipserv` varchar(2) DEFAULT '',
			  `codrut` varchar(6) NOT NULL DEFAULT '',
			  `horsal` varchar(6) DEFAULT '',
			  `tipuni` varchar(5) DEFAULT '',
			  `origen` varchar(100) DEFAULT '',
			  `destino` varchar(100) DEFAULT '',
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `codrut` (`codrut`)
			) ENGINE=MyISAM AUTO_INCREMENT=108 DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('tbrutas');
		//if(!in_array('<#campo#>',$campos)){ }


		//./pasajes/tbautobus.php
		if (!$this->db->table_exists('tbautobus')) {
			$mSQL="CREATE TABLE `tbautobus` (
			  `codbus` varchar(10) NOT NULL DEFAULT '' COMMENT 'Cod de Autobus',
			  `codacc` varchar(10) NOT NULL DEFAULT '' COMMENT 'Accionista -> tbaccio',
			  `capasidad` double NOT NULL DEFAULT '0' COMMENT 'Nro de Asientos',
			  `tipbus` varchar(10) NOT NULL DEFAULT '' COMMENT 'Tipo de unidad ->tbmodbus',
			  `placa` varchar(10) NOT NULL DEFAULT '' COMMENT 'Nro Matricula',
			  `marca` varchar(20) NOT NULL DEFAULT '' COMMENT 'Marca',
			  `modelo` varchar(40) NOT NULL DEFAULT '' COMMENT 'Modelo',
			  `ano` int(11) NOT NULL DEFAULT '2001' COMMENT 'Anno',
			  `serialc` varchar(50) NOT NULL DEFAULT '' COMMENT 'Serial Carroceria',
			  `serialm` varchar(50) NOT NULL DEFAULT '' COMMENT 'Serial Motor',
			  `color` varchar(50) NOT NULL DEFAULT '' COMMENT 'Serial Motor',
			  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Serial Motor',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `codbus` (`codbus`)
			) ENGINE=MyISAM AUTO_INCREMENT=189 DEFAULT CHARSET=latin1 COMMENT='Autobus'";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('tbautobus');
		//if(!in_array('<#campo#>',$campos)){ }


		//./pasajes/tbofici.php
		if (!$this->db->table_exists('tbofici')) {
			$mSQL="CREATE TABLE `tbofici` (
			  `codofi` varchar(5) NOT NULL DEFAULT '',
			  `desofi` varchar(100) DEFAULT '',
			  `dirofi` varchar(100) DEFAULT NULL,
			  `telofi` varchar(50) DEFAULT NULL,
			  `gereofi` varchar(50) DEFAULT NULL,
			  `telegofi` varchar(50) DEFAULT NULL,
			  `estado` varchar(50) DEFAULT NULL,
			  `zona` varchar(4) DEFAULT NULL,
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `codofi` (`codofi`)
			) ENGINE=MyISAM AUTO_INCREMENT=132 DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('tbofici');
		//if(!in_array('<#campo#>',$campos)){ }


		//./pasajes/tbmodbus.php
		if (!$this->db->table_exists('tbmodbus')) {
			$mSQL="CREATE TABLE `tbmodbus` (
			  `tipbus` varchar(5) DEFAULT '' COMMENT 'tipo de bus',
			  `desbus` varchar(100) DEFAULT '' COMMENT 'descripcion',
			  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'id',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `codigo` (`tipbus`)
			) ENGINE=MyISAM AUTO_INCREMENT=10 DEFAULT CHARSET=latin1 COMMENT='Tipos de Autobuses -> detalle es tbtipbus (asientos)'";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('tbmodbus');
		//if(!in_array('<#campo#>',$campos)){ }


		//./pasajes/tbaccio.php
		if (!$this->db->table_exists('tbaccio')) {
			$mSQL="CREATE TABLE `tbaccio` (
			  `codacc` varchar(10) NOT NULL DEFAULT '',
			  `nomacc` varchar(100) DEFAULT '',
			  `telfacc1` varchar(20) DEFAULT '',
			  `telfacc2` varchar(20) DEFAULT '',
			  `correo` varchar(150) NOT NULL,
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `codacc` (`codacc`)
			) ENGINE=MyISAM AUTO_INCREMENT=76 DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('tbaccio');
		//if(!in_array('<#campo#>',$campos)){ }


		//./accesos.php
		$fields = $this->db->field_data('intrasida','modulo');
		if($fields[1]->type!='string'){
			$mSQL="ALTER TABLE `intrasida`  CHANGE COLUMN `modulo` `modulo` VARCHAR(11) NOT NULL DEFAULT '0' AFTER `usuario`";
			$this->db->simple_query($mSQL);
		}

		//./concesionario/compra.php
		if (!$this->db->table_exists('sinvehiculo')) {
			$mSQL="CREATE TABLE `sinvehiculo` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`id_sfac` INT(10) NULL DEFAULT NULL,
				`id_scst` INT(10) NULL DEFAULT NULL,
				`codigo_sinv` VARCHAR(15) NULL DEFAULT '0',
				`modelo` VARCHAR(50) NULL DEFAULT '0',
				`color` VARCHAR(50) NULL DEFAULT '0',
				`motor` VARCHAR(50) NULL DEFAULT '0',
				`carroceria` VARCHAR(50) NULL DEFAULT '0',
				`uso` VARCHAR(50) NULL DEFAULT '0',
				`anio` VARCHAR(50) NULL DEFAULT '0',
				`peso` DECIMAL(10,2) NULL DEFAULT '0.00',
				`transmision` VARCHAR(50) NULL DEFAULT '0.00',
				`placa` VARCHAR(10) NULL DEFAULT NULL,
				`precioplaca` DECIMAL(10,2) NULL DEFAULT NULL,
				`tasa` DECIMAL(10,2) NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
			)
			COMMENT='Vehiculos a la venta'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}


		//./concesionario/inicio.php
		//if(!$this->db->field_exists('neumaticos', 'sinvehiculo')){
		//	$mSQL = "ALTER TABLE `sinvehiculo`
		//	ADD COLUMN `neumaticos` INT NULL DEFAULT NULL AFTER `poliza`,
		//	ADD COLUMN `tipo_neumatico` VARCHAR(50) NULL DEFAULT NULL AFTER `neumaticos`,
		//	ADD COLUMN `distanciaeje` FLOAT NULL DEFAULT NULL AFTER `marca`;";
		//	$this->db->simple_query($mSQL);
		//}



		//./nomina/accesos.php
		if (!$this->db->field_exists('manual', 'cacc')){
			$query="ALTER TABLE `cacc`  ADD COLUMN `manual` CHAR(1) NOT NULL DEFAULT 'N' AFTER `hora`";
			$this->db->simple_query($query);
		}

		if (!$this->db->table_exists('cerberus')){
			$mSQL="CREATE TABLE `cerberus` (
			`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
			`ids` INT(10) UNSIGNED NOT NULL DEFAULT '0',
			`ip` VARCHAR(32) NOT NULL COLLATE 'utf8_unicode_ci',
			`usr` VARCHAR(32) NOT NULL COLLATE 'utf8_unicode_ci',
			`pwd` VARCHAR(32) NOT NULL COLLATE 'utf8_unicode_ci',
			`sid` INT(11) UNSIGNED NOT NULL DEFAULT '0',
			`activo` CHAR(1) NOT NULL DEFAULT 'S',
			PRIMARY KEY (`id`)
			)
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
			$this->db->simple_query($mSQL);
		}


		//./nomina/pers.php
		$campos=$this->db->list_fields('pers');
		if(!in_array('email',$campos))
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `email` VARCHAR(100) NULL");

		if(!in_array('tipoe',$campos))
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `tipoe` VARCHAR(10)");

		if(!in_array('escritura',$campos))
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `escritura` VARCHAR(25)");

		if(!in_array('rif',$campos))
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `rif` VARCHAR(15)");

		if(!in_array('observa',$campos))
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `observa` TEXT");

		if(!in_array('turno',$campos))
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `turno` CHAR(2) NULL");

		if(!in_array('horame',$campos))
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `horame` VARCHAR(10)");

		if(!in_array('horams',$campos))
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `horams` VARCHAR(10)");

		if(!in_array('horate',$campos))
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `horate` VARCHAR(10)");

		if(!in_array('horats',$campos))
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `horats` VARCHAR(10)");

		if(!in_array('modificado',$campos))
			$this->db->simple_query("ALTER TABLE pers ADD COLUMN `modificado` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP AFTER vence");

		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE pers DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE pers ADD UNIQUE INDEX codigo (codigo)');
			$this->db->simple_query('ALTER TABLE pers ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		$tablas = $this->db->list_tables();
		if(!in_array('tipot',$tablas))
			$this->db->simple_query("CREATE TABLE tipot (codigo int(10) unsigned NOT NULL AUTO_INCREMENT,tipo varchar(50) DEFAULT NULL,PRIMARY KEY (codigo) )");

		if(!in_array('posicion',$tablas))
			$this->db->simple_query("CREATE TABLE `posicion`(`codigo` varchar(10) NOT NULL,`posicion` varchar(30) DEFAULT NULL,PRIMARY KEY (`codigo`))");

		if(!in_array('tipoe',$tablas))
			$this->db->simple_query("CREATE TABLE tipoe (codigo varchar(10) NOT NULL DEFAULT '', tipo varchar(50) DEFAULT NULL, PRIMARY KEY (codigo))");

		if(!in_array('nedu',$tablas)){
			$this->db->simple_query("CREATE TABLE IF NOT EXISTS nedu (codigo varchar(4) NOT NULL, nivel varchar(40) DEFAULT NULL, PRIMARY KEY (`codigo`)) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC");
			$this->db->simple_query("INSERT INTO nedu (codigo, nivel) VALUES ('00', 'Sin Educacion Formal'),('01', 'Primaria'),('02', 'Secundaria'),('03', 'Tecnico'),	('04', 'T.S.U.'),('05', 'Universitario'),('06', 'Post Universitario'),('07', 'Doctor'),('08', 'Guru')");
		}


		//./nomina/pres.php
		$campos=$this->db->list_fields('pres');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE pres DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE pres ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE pres ADD UNIQUE INDEX cliente (cod_cli, tipo_doc, numero )');
		}


		//./nomina/asig.php
		$mSQL="ALTER TABLE asig ADD PRIMARY KEY (codigo);";
		$this->db->simple_query($mSQL);


		//./nomina/aumentosueldo.php
		$mSQL="ALTER TABLE ausu ADD PRIMARY KEY (codigo,fecha);";
		$this->db->simple_query($mSQL);


		//./nomina/notabu.php
		//if ( !$this->datasis->iscampo('notabu','id') ) {
		//	$this->db->simple_query('ALTER TABLE notabu DROP PRIMARY KEY');
		//	$this->db->simple_query('ALTER TABLE notabu ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
		//	$this->db->simple_query('ALTER TABLE notabu ADD UNIQUE INDEX princi (contrato, ano, mes, dia)');
		//}


		//./nomina/ausu.php
		//$mSQL="ALTER TABLE ausu ADD PRIMARY KEY (codigo,fecha);";
		//$this->db->simple_query($mSQL);

		//./nomina/nomina.php
		if (!$this->db->table_exists('nomina')) {
			$mSQL="CREATE TABLE `nomina` (
			  `numero` char(8) DEFAULT NULL,
			  `frecuencia` char(1) DEFAULT NULL,
			  `contrato` char(8) DEFAULT NULL,
			  `depto` char(8) DEFAULT NULL,
			  `codigo` char(15) NOT NULL DEFAULT '',
			  `nombre` char(30) DEFAULT NULL,
			  `concepto` char(4) NOT NULL DEFAULT '',
			  `tipo` char(1) DEFAULT NULL,
			  `descrip` char(35) DEFAULT NULL,
			  `grupo` char(4) DEFAULT NULL,
			  `formula` char(120) DEFAULT NULL,
			  `monto` double DEFAULT NULL,
			  `fecha` date DEFAULT NULL,
			  `cuota` int(11) DEFAULT NULL,
			  `cuotat` int(11) DEFAULT NULL,
			  `valor` decimal(17,2) DEFAULT '0.00',
			  `estampa` date DEFAULT NULL,
			  `usuario` char(12) DEFAULT NULL,
			  `transac` char(8) DEFAULT NULL,
			  `hora` char(8) DEFAULT NULL,
			  `fechap` date DEFAULT NULL,
			  `trabaja` char(8) DEFAULT NULL,
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  KEY `numero` (`numero`),
			  KEY `codigo` (`codigo`),
			  KEY `concepto` (`concepto`),
			  KEY `fecha` (`fecha`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('nomina');
		//if(!in_array('<#campo#>',$campos)){ }


		//./construccion/eduso.php
		if (!$this->db->table_exists('eduso')) {
			$mSQL="CREATE TABLE `eduso` (
				  `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				  `uso` CHAR(80) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
				  PRIMARY KEY (`id`)
				  )
				  COMMENT='Usos de los inmuebles'
				  COLLATE='latin1_swedish_ci'
				  ENGINE=MyISAM
				  ROW_FORMAT=DEFAULT
				  AUTO_INCREMENT=1";
			$this->db->simple_query($mSQL);
		}

		//./construccion/edcont.php
		if (!$this->db->table_exists('edcont')) {
			$mSQL="CREATE TABLE `edcont` (
				`id` int(11) NOT NULL AUTO_INCREMENT,
				`id_edres` int(11) DEFAULT '0',
				`numero_edres` char(8) DEFAULT NULL,
				`numero` char(8) DEFAULT NULL,
				`fecha` date DEFAULT NULL,
				`cliente` char(5) DEFAULT NULL,
				`edificacion` int(11) DEFAULT '0',
				`inmueble` int(11) DEFAULT '0',
				`inicial` decimal(17,2) DEFAULT '0.00',
				`financiable` decimal(17,2) DEFAULT '0.00',
				`firma` decimal(17,2) DEFAULT '0.00',
				`precioxmt2` decimal(17,2) DEFAULT '0.00',
				`mt2` decimal(17,2) DEFAULT '0.00',
				`monto` decimal(17,2) DEFAULT '0.00',
				`notas` text,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COMMENT='Reserva de Inmuebles'";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('uso', 'edcont')){
			$mSQL="ALTER TABLE `edcont` ADD COLUMN `uso` INT(11) NOT NULL AFTER `notas`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('status', 'edcont')){
			$mSQL="ALTER TABLE `edcont` ADD COLUMN `status` CHAR(1) NOT NULL DEFAULT 'P' AFTER `numero`";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists('itedcont')) {
			$mSQL="CREATE TABLE `itedcont` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `id_edcont` int(11) NOT NULL,
			  `vencimiento` date NOT NULL,
			  `monto` decimal(10,2) NOT NULL,
			  PRIMARY KEY (`id`),
			 KEY `id_edcont` (`id_edcont`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('especial', 'itedcont')){
			$mSQL= "ALTER TABLE `itedcont` ADD COLUMN `especial` CHAR(1) NOT NULL DEFAULT 'N' AFTER `id_edcont`";
		}



		//./construccion/ediftipo.php
		if (!$this->db->table_exists('ediftipo')) {
			$mSQL="CREATE TABLE `ediftipo` (
				  `id` INT(11) NOT NULL AUTO_INCREMENT,
				  `descrip` CHAR(50) NULL DEFAULT NULL,
				  PRIMARY KEY (`id`)
				  )
				  COMMENT='Tipos de Edificaciones'
				  COLLATE='latin1_swedish_ci'
				  ENGINE=MyISAM
				  ROW_FORMAT=DEFAULT
				  AUTO_INCREMENT=1";
			$this->db->simple_query($mSQL);
		}


		//./construccion/edif.php
		if (!$this->db->table_exists('edif')) {
			$mSQL="CREATE TABLE `edif` (
				  `id` INT(11) NOT NULL AUTO_INCREMENT,
				  `nombre` CHAR(120) NULL DEFAULT NULL,
				  `tipo` INT(10) NULL DEFAULT NULL,
				  `direccion` TEXT NULL,
				  `descripcion` TEXT NULL,
				  `promotora` CHAR(5) NULL DEFAULT NULL,
				  PRIMARY KEY (`id`)
				  )
				  COMMENT='Edificaciones'
				  COLLATE='latin1_swedish_ci'
				  ENGINE=MyISAM
				  ROW_FORMAT=DEFAULT";
			$this->db->simple_query($mSQL);
		}


		//./construccion/edinmue.php
		if (!$this->db->table_exists('edinmue')) {
			$mSQL="CREATE TABLE `edinmue` (
			  `id` INT(11) NOT NULL AUTO_INCREMENT,
			  `codigo` CHAR(15) NULL DEFAULT NULL,
			  `descripcion` CHAR(100) NULL DEFAULT NULL,
			  `edificacion` INT(11) NULL DEFAULT NULL,
			  `uso` INT(11) NULL DEFAULT NULL,
			  `usoalter` INT(11) NULL DEFAULT NULL,
			  `ubicacion` INT(11) NULL DEFAULT NULL,
			  `caracteristicas` TEXT NULL,
			  `area` DECIMAL(15,2) NULL DEFAULT NULL,
			  `estaciona` INT(10) NULL DEFAULT NULL,
			  `deposito` INT(11) NULL DEFAULT NULL,
			  `preciomt2` DECIMAL(15,2) NULL DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE INDEX `codigo` (`codigo`)
			)
			COMMENT='Inmuebles'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->field_exists('preciomt2e', 'edinmue')) {
			$mSQL="ALTER TABLE `edinmue`  CHANGE COLUMN `preciomt2` `preciomt2e` DECIMAL(15,2) NULL AFTER `deposito`,  ADD COLUMN `preciomt2c` DECIMAL(15,2) NULL AFTER `preciomt2e`,  ADD COLUMN `preciomt2a` DECIMAL(15,2) NULL AFTER `preciomt2c`";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->field_exists('objeto', 'edinmue')) {
			$mSQL="ALTER TABLE `edinmue`  ADD COLUMN `objeto` CHAR(1) NOT NULL AFTER `preciomt2a`";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->field_exists('status', 'edinmue')) {
			$mSQL="ALTER TABLE `edinmue`  ADD COLUMN `status` CHAR(1) NOT NULL COMMENT 'Alquilado, Vendido, Reservado,Disponible, Otro' AFTER `objeto`;";
			$this->db->simple_query($mSQL);
		}



		//./construccion/edifubica.php
		if (!$this->db->table_exists('edifubica')) {
			$mSQL="CREATE TABLE `edifubica` (
			  `id` INT(11) NOT NULL AUTO_INCREMENT,
			  `id_edif` INT(11) NULL DEFAULT NULL,
			  `descripcion` CHAR(50) NULL DEFAULT NULL,
			PRIMARY KEY (`id`)
			)
			COMMENT='Ubicaciones dentro de una edificacion'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
			$this->db->simple_query($mSQL);
		}


		//./construccion/edres.php
		if (!$this->db->table_exists('edres')) {
			$mSQL="CREATE TABLE `edres` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `numero` char(8) DEFAULT NULL,
			  `fecha` date DEFAULT NULL,
			  `cliente` char(5) DEFAULT NULL,
			  `edificacion` int(11) DEFAULT '0',
			  `inmueble` int(11) DEFAULT '0',
			  `reserva` decimal(17,2) DEFAULT '0.00',
			  `formap1` char(2) DEFAULT '0',
			  `banco1` char(3) DEFAULT '0',
			  `nummp1` varchar(20) DEFAULT '0',
			  `monto1` decimal(17,2) DEFAULT '0.00',
			  `formap2` char(2) DEFAULT '0',
			  `banco2` char(3) DEFAULT '0',
			  `nummp2` varchar(20) DEFAULT '0',
			  `monto2` decimal(17,2) DEFAULT '0.00',
			  `formap3` char(2) DEFAULT '0',
			  `banco3` char(3) DEFAULT '0',
			  `nummp3` varchar(20) DEFAULT '0',
			  `monto3` decimal(17,2) DEFAULT '0.00',
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Reserva de Inmuebles'";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->field_exists('notas', 'edres')){
			$mSQL="ALTER TABLE `edres`  ADD COLUMN `notas` TEXT NULL DEFAULT NULL AFTER `monto3`";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->field_exists('pfecha1', 'edres')){
			$mSQL="ALTER TABLE `edres`  ADD COLUMN `pfecha1` DATE NULL AFTER `monto1`,  ADD COLUMN `pfecha2` DATE NULL AFTER `monto2`,  ADD COLUMN `pfecha3` DATE NULL AFTER `monto3`";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->field_exists('transac', 'edres')){
			$mSQL="ALTER TABLE `edres` ADD COLUMN `transac` VARCHAR(8) NULL AFTER `notas`";
			$this->db->simple_query($mSQL);
		}


		//./leche/lcierre.php
		if(!$this->db->table_exists('lcierre')){
			$mSQL = "CREATE TABLE `lcierre` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`fecha` DATE NULL DEFAULT NULL,
				`dia` VARCHAR(50) NULL DEFAULT NULL,
				`status` CHAR(1) NULL DEFAULT 'A',
				`recepcion` DECIMAL(12,2) NULL DEFAULT NULL,
				`enfriamiento` DECIMAL(12,2) NULL DEFAULT NULL,
				`requeson` DECIMAL(12,2) NULL DEFAULT NULL,
				`requesonteorico` DECIMAL(12,2) NULL DEFAULT NULL,
				`requesonreal` DECIMAL(12,2) NULL DEFAULT NULL,
				`usuario` VARCHAR(50) NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
			)
			COMMENT='Cierre de produccion de lacteos'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('itlcierre')){
			$mSQL = "CREATE TABLE `itlcierre` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`id_lcierre` INT(10) NULL DEFAULT NULL,
				`codigo` VARCHAR(15) NULL DEFAULT NULL,
				`descrip` VARCHAR(45) NULL DEFAULT NULL,
				`unidades` DECIMAL(10,2) NULL DEFAULT NULL,
				`cestas` DECIMAL(10,2) NULL DEFAULT NULL,
				`peso` DECIMAL(10,2) NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				INDEX `id_lcierre` (`id_lcierre`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('lcierre');
		if (!in_array('status',$campos)){
			$mSQL="ALTER TABLE `lcierre` ADD COLUMN `status` CHAR(1) NULL DEFAULT 'A' AFTER `dia`;";
			$this->db->simple_query($mSQL);
		}


		//./leche/lrece.php
		if(!$this->db->table_exists('lrece')){
			$mSQL="CREATE TABLE `lrece` (
				`numero` CHAR(8) NULL DEFAULT NULL,
				`fecha` DATETIME NULL DEFAULT NULL,
				`transporte` INT(11) NULL DEFAULT NULL COMMENT 'Transporte',
				`fechal` DATE NULL DEFAULT NULL COMMENT 'Fecha de Llegada',
				`fechar` DATE NULL DEFAULT NULL COMMENT 'Fecha de Recoleccion',
				`ruta` CHAR(4) NULL DEFAULT NULL COMMENT 'Ruta Grupo de Proveedor',
				`flete` CHAR(5) NULL DEFAULT NULL COMMENT 'Proveedor Flete',
				`nombre` CHAR(45) NULL DEFAULT NULL COMMENT 'Nombre Chofer ',
				`lleno` DECIMAL(16,2) NULL DEFAULT NULL COMMENT 'Peso de la Unidad llena',
				`vacio` DECIMAL(16,2) NULL DEFAULT NULL COMMENT 'Peso de la Unidad Vacia',
				`neto` DECIMAL(16,2) NULL DEFAULT NULL COMMENT 'Neto lleno-vacio',
				`densidad` DECIMAL(10,4) NULL DEFAULT NULL COMMENT 'Densidad',
				`litros` DECIMAL(16,2) NULL DEFAULT NULL COMMENT 'Total Litros neto*densidad',
				`lista` DECIMAL(16,2) NULL DEFAULT NULL COMMENT 'Segun Lista',
				`diferen` DECIMAL(16,3) NULL DEFAULT NULL COMMENT 'Diferencia Neto/Lista',
				`animal` CHAR(1) NULL DEFAULT NULL COMMENT 'Vaca o Bufala',
				`crios` DECIMAL(10,0) NULL DEFAULT NULL COMMENT 'Crioscopia',
				`h2o` DECIMAL(10,2) NULL DEFAULT NULL COMMENT '% de Agua',
				`temp` DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Temperatura',
				`brix` DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Grados Brix',
				`grasa` DECIMAL(10,3) NULL DEFAULT NULL COMMENT '% Grasa',
				`acidez` DECIMAL(10,0) NULL DEFAULT NULL COMMENT 'Acidez',
				`cloruros` DECIMAL(10,0) NULL DEFAULT NULL COMMENT 'Cloruros',
				`dtoagua` DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Dto. Agua',
				`pago` INT(11) NULL DEFAULT '0' COMMENT 'Nro de Pago',
				`montopago` DECIMAL(12,2) NULL DEFAULT '0.00' COMMENT 'Monto del pago',
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `numero` (`numero`),
				INDEX `fecha` (`fecha`),
				INDEX `transporte` (`transporte`)
			)
			COMMENT='Recepcion de Leche'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('montopago', 'lrece')){
			$mSQL="ALTER TABLE `lrece` ADD COLUMN `montopago` DECIMAL(12,2) NULL DEFAULT '0' COMMENT 'Monto del pago' AFTER `pago`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('estampa', 'lrece')){
			$mSQL="ALTER TABLE `lrece` ADD COLUMN `estampa` DATETIME NULL DEFAULT NULL AFTER `montopago`";
			$this->db->simple_query($mSQL);
			$mSQL="UPDATE lrece SET estampa=fecha";
			$this->db->simple_query($mSQL);
			$mSQL="ALTER TABLE `lrece` CHANGE COLUMN `fecha` `fecha` DATE NULL DEFAULT NULL AFTER `numero`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('transporte', 'lrece')){
			$mSQL="ALTER TABLE `lrece` ADD COLUMN `transporte` INT(11) NULL DEFAULT NULL COMMENT 'Transporte' AFTER `fecha`, ADD INDEX `transporte` (`transporte`)";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('fechal', 'lrece')){
			$mSQL="ALTER TABLE `lrece` ADD COLUMN `fechal` DATE NULL DEFAULT NULL COMMENT 'Fecha de Llegada' AFTER `transporte`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('fechar', 'lrece')){
			$mSQL="ALTER TABLE `lrece` ADD COLUMN `fechar` DATE NULL DEFAULT NULL COMMENT 'Fecha de Recoleccion' AFTER `fechal`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('alcohol', 'itlrece')){
			$mSQL = "ALTER TABLE itlrece ADD COLUMN `alcohol` DECIMAL(10,3) NULL DEFAULT NULL COMMENT 'alcohol' AFTER `dtoagua`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('montopago', 'itlrece')){
			$mSQL = "ALTER TABLE `itlrece` ADD COLUMN `montopago` DECIMAL(12,2) NULL DEFAULT '0' COMMENT 'Monto del pago' AFTER `pago`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('alcohol', 'lanal')){
			$mSQL = "ALTER TABLE lanal ADD COLUMN `alcohol` DECIMAL(10,3) NULL DEFAULT NULL COMMENT 'alcohol' AFTER `dtoagua`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('itlrece')){
			$mSQL="CREATE TABLE `itlrece` (
				`vaquera` VARCHAR(5) NULL DEFAULT NULL COMMENT 'Vaquera',
				`nombre` VARCHAR(45) NULL DEFAULT NULL COMMENT 'Productor ',
				`densidad` DECIMAL(10,4) NULL DEFAULT '1.0164' COMMENT 'Densidad',
				`lista` DECIMAL(16,2) NULL DEFAULT NULL COMMENT 'Segun Lista',
				`animal` CHAR(1) NULL DEFAULT 'V' COMMENT 'Vaca o Bufala',
				`crios` DECIMAL(10,0) NULL DEFAULT '0' COMMENT 'Crioscopia',
				`h2o` DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT '% de Agua',
				`temp` DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT 'Temperatura',
				`brix` DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT 'Grados Brix',
				`grasa` DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT '% Grasa',
				`acidez` DECIMAL(10,0) NULL DEFAULT '0' COMMENT 'Acidez',
				`cloruros` DECIMAL(10,0) NULL DEFAULT '0' COMMENT 'Cloruros',
				`dtoagua` DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT 'Dto. Agua',
				`alcohol` DECIMAL(10,0) NULL DEFAULT '0' COMMENT 'alcohol',
				`id_lrece` INT(11) NULL DEFAULT NULL,
				`id_lvaca` INT(11) NULL DEFAULT NULL,
				`activa` CHAR(1) NULL DEFAULT 'A' COMMENT 'Activa Si o No',
				`pago` INT(11) NULL DEFAULT '0' COMMENT 'ID del pago lpago',
				`montopago` DECIMAL(12,2) NULL DEFAULT '0' COMMENT 'Monto del pago',
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `vaquera` (`vaquera`, `id_lrece`),
				INDEX `id_lrece` (`id_lrece`),
				INDEX `id_lvaca` (`id_lvaca`)
			)
			COMMENT='Detalle Recepcion de Leche'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}


		//./leche/lprod.php
		if(!$this->db->table_exists('lprod')){
			$mSQL = "
			CREATE TABLE `lprod` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`codigo` VARCHAR(15) NULL DEFAULT NULL,
				`descrip` VARCHAR(45) NULL DEFAULT NULL,
				`fecha` DATE NULL DEFAULT NULL,
				`peso` DECIMAL(12,2) NULL DEFAULT NULL,
				`litros` DECIMAL(12,2) NULL DEFAULT NULL,
				`inventario` DECIMAL(12,2) NULL DEFAULT NULL,
				`estampa` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
				`usuario` VARCHAR(15) NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
			)
			COMMENT='Control de produccion de lacteos'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM;";
			$this->db->simple_query($mSQL);
		}


		if(!$this->db->table_exists('itlprod')){
			$mSQL = "
			CREATE TABLE `itlprod` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`id_lprod` INT(10) NOT NULL DEFAULT '0',
				`codrut` CHAR(4) NOT NULL DEFAULT '0',
				`nombre` VARCHAR(50) NOT NULL DEFAULT '0',
				`litros` DECIMAL(12,2) NOT NULL DEFAULT '0.00',
				PRIMARY KEY (`id`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			AUTO_INCREMENT=0;";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('lcierre')){
			$mSQL = "CREATE TABLE `lcierre` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`fecha` DATE NULL DEFAULT NULL,
				`dia` VARCHAR(50) NULL DEFAULT NULL,
				`status` CHAR(1) NULL DEFAULT 'A',
				`recepcion` DECIMAL(12,2) NULL DEFAULT NULL,
				`enfriamiento` DECIMAL(12,2) NULL DEFAULT NULL,
				`requeson` DECIMAL(12,2) NULL DEFAULT NULL,
				`requesonteorico` DECIMAL(12,2) NULL DEFAULT NULL,
				`requesonreal` DECIMAL(12,2) NULL DEFAULT NULL,
				`usuario` VARCHAR(50) NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
			)
			COMMENT='Cierre de produccion de lacteos'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('itlcierre')){
			$mSQL = "CREATE TABLE `itlcierre` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`id_lcierre` INT(10) NULL DEFAULT NULL,
				`codigo` VARCHAR(15) NULL DEFAULT NULL,
				`descrip` VARCHAR(45) NULL DEFAULT NULL,
				`unidades` DECIMAL(10,2) NULL DEFAULT NULL,
				`cestas` DECIMAL(10,2) NULL DEFAULT NULL,
				`peso` DECIMAL(10,2) NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				INDEX `id_lcierre` (`id_lcierre`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}


		//./leche/lgasto.php
		if (!$this->db->table_exists('lgasto')) {
			$mSQL="CREATE TABLE `lgasto` (
				`proveed` CHAR(5) NULL DEFAULT NULL COMMENT 'productor',
				`nombre` VARCHAR(100) NULL DEFAULT NULL COMMENT 'nombre',
				`tipo` CHAR(1) NULL DEFAULT 'D' COMMENT 'Dedudccion, Adicion',
				`referen` VARCHAR(100) NULL DEFAULT NULL,
				`status` CHAR(1) NULL DEFAULT 'A',
				`fecha` DATE NULL DEFAULT NULL COMMENT 'nombre',
				`descrip` VARCHAR(100) NULL DEFAULT NULL COMMENT 'finca',
				`cantidad` DECIMAL(17,2) NULL DEFAULT NULL COMMENT 'ruta a en lruta',
				`precio` DECIMAL(17,2) NULL DEFAULT NULL COMMENT 'zona',
				`total` DECIMAL(17,2) NULL DEFAULT NULL COMMENT 'direccion',
				`pago` INT(11) NULL DEFAULT '0' COMMENT 'id de pago lpago',
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				INDEX `proveed` (`proveed`),
				INDEX `fecha` (`fecha`)
			)
			COMMENT='Gastos'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('lgasto');
		if (!in_array('tipo',$campos)){
			$mSQL="ALTER TABLE `lgasto` ADD COLUMN `tipo` CHAR(1) NULL DEFAULT 'D' COMMENT 'Dedudccion, Adicion' AFTER `nombre`";
			$this->db->simple_query($mSQL);
		}


		//./leche/lacida.php
		if (!$this->db->table_exists('lacida')) {
			$mSQL="
			CREATE TABLE `lacida` (
				fecha     DATE          NULL DEFAULT NULL,
				ruta      CHAR(4)       NULL DEFAULT NULL   COMMENT 'Ruta ',
				vaquera   INT(11)       NULL DEFAULT NULL   COMMENT 'Vaquera',
				nomvaca   VARCHAR(45)   NULL DEFAULT NULL   COMMENT 'Nombre de la ruta o vaquera',
				litros    DECIMAL(16,2) NULL DEFAULT NULL   COMMENT 'Litros de Leche Acida',
				acidez    DECIMAL(10,0) NULL DEFAULT NULL   COMMENT 'Acidez',
				alcohol   DECIMAL(10,0) NULL DEFAULT '0'    COMMENT 'Alcohol',
				codigo    VARCHAR(15)   NULL DEFAULT NULL   COMMENT 'Queso producido ',
				descrip   VARCHAR(45)   NULL DEFAULT NULL   COMMENT 'Descripcion del Producto',
				precio    DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT 'Precio del queso',
				precioref DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT 'Precio Referencia',
				descuento DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT 'Monto a Descontar',
				promedio  DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT 'Promedio Litros/Kg',
				gadm      DECIMAL(10,2) NULL DEFAULT '0.40' COMMENT 'Gasstos Administrativos',
				pleche    DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT 'Precio de la leche',
				pago      INT(11)       NULL DEFAULT '0'    COMMENT 'Nro de Pago',
				id        INT(11)   NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (id),
				INDEX fecha (fecha)
			)
			COMMENT='Notificacion de leche Acida'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			";
			$this->db->simple_query($mSQL);
		}


		//./leche/lpago.php
		if(!$this->db->table_exists('lpago')) {
			$mSQL="CREATE TABLE `lpago` (
				`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`numero` VARCHAR(8) NULL DEFAULT NULL,
				`tipo` CHAR(1) NULL DEFAULT NULL COMMENT 'Transportista y Productor',
				`fecha` DATE NULL DEFAULT NULL,
				`proveed` VARCHAR(10) NULL DEFAULT NULL,
				`nombre` VARCHAR(100) NULL DEFAULT NULL,
				`banco` VARCHAR(50) NULL DEFAULT NULL,
				`numche` VARCHAR(100) NULL DEFAULT NULL,
				`benefi` VARCHAR(200) NULL DEFAULT NULL,
				`monto` DECIMAL(12,2) NULL DEFAULT NULL,
				`deduc` DECIMAL(12,2) NULL DEFAULT NULL,
				`montopago` DECIMAL(12,2) NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				INDEX `proveed` (`proveed`),
				INDEX `numero` (`numero`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('lpago');
		if (!in_array('id_lpagolote',$campos)){
			$mSQL="ALTER TABLE `lpago` ADD COLUMN `id_lpagolote` INT NULL DEFAULT NULL";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists('lpagolote')) {
			$mSQL="CREATE TABLE `lpagolote` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`enbanco` VARCHAR(5) NULL DEFAULT NULL COMMENT 'Banco con que se paga',
				`banco` VARCHAR(5) NULL DEFAULT NULL COMMENT 'Banco donde se deposita',
				`tipo` CHAR(2) NULL DEFAULT NULL,
				`numero` VARCHAR(50) NULL DEFAULT NULL,
				`benefi` VARCHAR(100) NULL DEFAULT NULL,
				`fecha` DATE NULL DEFAULT NULL,
				`monto` DECIMAL(12,2) NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('lpagolote');
		if (!in_array('banco',$campos)){
			$mSQL="ALTER TABLE `lpagolote` CHANGE COLUMN `enbanco` `enbanco` VARCHAR(5) NULL DEFAULT NULL COMMENT 'Banco con que se paga' AFTER `id`, ADD COLUMN `banco` VARCHAR(5) NULL DEFAULT NULL COMMENT 'Banco donde se deposita' AFTER `enbanco`";
			$this->db->simple_query($mSQL);
		}


		//./formatos.php
		$campos=$this->db->list_fields('formatos');
		if(!in_array('proteo' ,$campos)) $this->db->simple_query("ALTER TABLE `formatos` ADD COLUMN `proteo`  TEXT NULL AFTER `forma`"  );
		if(!in_array('harbour',$campos)) $this->db->simple_query("ALTER TABLE `formatos` ADD COLUMN `harbour` TEXT NULL AFTER `proteo`" );
		if(!in_array('tcpdf'  ,$campos)) $this->db->simple_query("ALTER TABLE `formatos` ADD COLUMN `tcpdf`   TEXT NULL AFTER `forma`"  );
		if(!in_array('txt'    ,$campos)) $this->db->simple_query("ALTER TABLE `formatos` ADD COLUMN `txt`     TEXT NULL AFTER `harbour`");


		//./taller/recep.php
		if (!$this->db->table_exists('recep')) {
			$mSQL="CREATE TABLE `recep` (
			  `recep` char(8) NOT NULL DEFAULT '',
			  `fecha` date DEFAULT NULL,
			  `clipro` varchar(5) DEFAULT NULL,
			  `refe` char(8) DEFAULT NULL,
			  `tipo` char(2) DEFAULT NULL,
			  `observa` text,
			  `status` char(2) DEFAULT NULL,
			  `user` varchar(50) DEFAULT NULL,
			  `estampa` timestamp NULL DEFAULT NULL,
			  `origen` varchar(20) DEFAULT NULL,
			  PRIMARY KEY (`recep`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
		$this->db->simple_query("ALTER TABLE `recep`  ADD COLUMN `nombre` VARCHAR(50) NULL DEFAULT NULL");


		//./taller/tecn.php
		if (!$this->db->table_exists('tecn')) {
			$mSQL="CREATE TABLE `tecn` (
			  `codigo` varchar(5) NOT NULL DEFAULT '',
			  `clave` varchar(5) DEFAULT NULL,
			  `nombre` varchar(30) DEFAULT NULL,
			  `direc1` varchar(35) DEFAULT NULL,
			  `direc2` varchar(35) DEFAULT NULL,
			  `telefono` varchar(13) DEFAULT NULL,
			  `comive` decimal(5,2) DEFAULT NULL,
			  `comicob` decimal(5,2) DEFAULT NULL,
			  `recargo` decimal(5,2) DEFAULT NULL,
			  `tipo` char(1) DEFAULT NULL,
			  `almacen` varchar(4) DEFAULT NULL,
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `codigo` (`codigo`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}

		//./inventario/estajefe.php

		if (!$this->db->table_exists('estajefe')) {
			$mSQL="CREATE TABLE `estajefe` (
				`codigo` VARCHAR(5) NOT NULL DEFAULT '',
				`nombre` VARCHAR(30) NULL DEFAULT NULL,
				`cedula` VARCHAR(12) NULL DEFAULT NULL,
				`direc1` VARCHAR(35) NULL DEFAULT NULL,
				`direc2` VARCHAR(35) NULL DEFAULT NULL,
				`telefono` VARCHAR(13) NULL DEFAULT NULL,
				`correo` VARCHAR(250) NULL DEFAULT NULL,
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `unico` (`codigo`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('estajefe');
		if(!in_array('cedula',$campos)){
			$mSQL="ALTER TABLE `estajefe` ADD COLUMN `cedula` VARCHAR(12) NULL DEFAULT NULL AFTER `nombre`";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('id',$campos)){
			$mSQL="ALTER TABLE `estajefe`
			ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT AFTER `correo`,
			DROP PRIMARY KEY,
			ADD UNIQUE INDEX `unico` (`codigo`),
			ADD PRIMARY KEY (`id`)";
			$this->db->simple_query($mSQL);
		}



		//./inventario/barraspos.php
		$mSQL="CREATE TABLE IF NOT EXISTS `barraspos` (
  			`codigo` char(15) NOT NULL DEFAULT '',
  			`suplemen` char(15) NOT NULL DEFAULT '',
  		PRIMARY KEY (`codigo`,`suplemen`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1
		";
		$this->db->simple_query($mSQL);


		//./inventario/recep.php
		if (!$this->db->table_exists('recep')) {
			$mSQL = "CREATE TABLE `recep` (
				`recep` CHAR(8) NOT NULL DEFAULT '',
				`fecha` DATE NULL DEFAULT NULL,
				`clipro` VARCHAR(5) NULL DEFAULT NULL,
				`nombre` VARCHAR(100) NULL DEFAULT NULL,
				`refe` CHAR(8) NULL DEFAULT NULL,
				`tipo_refe` CHAR(2) NULL DEFAULT NULL,
				`tipo` CHAR(2) NULL DEFAULT NULL,
				`observa` TEXT NULL,
				`status` CHAR(2) NULL DEFAULT NULL,
				`user` VARCHAR(50) NULL DEFAULT NULL,
				`estampa` TIMESTAMP NULL DEFAULT NULL,
				`origen` VARCHAR(20) NULL DEFAULT NULL,
				PRIMARY KEY (`recep`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
			$this->db->simple_query($mSQL);
		}

		//if (!$this->db->table_exists('view_clipro')) {
		//	$user=$this->db->username;
		//	$host=$this->db->hostname;
        //
		//	$mSQL = "CREATE ALGORITHM = UNDEFINED DEFINER= `$user`@`$host` VIEW view_clipro AS
		//	SELECT 'Proveedor' tipo, b.proveed codigo, b.nombre, b.rif, concat_ws(' ', b.direc1, b.direc2, b.direc3) direc FROM `sprv` b
		//	UNION ALL
		//	SELECT 'Cliente' Cliente, a.cliente, a.nombre, a.rifci, concat_ws(' ',a.dire11, a.dire12, a.dire21, a.dire22) direc FROM `scli` a";
		//	$this->db->simple_query($mSQL);
		//}

		$fields = $this->db->list_fields('seri');
		if(!in_array('cant',$fields)){
			$query="ALTER TABLE `seri`  ADD COLUMN `cant` DECIMAL(19,2) NOT NULL DEFAULT '1'";
			$this->db->simple_query($query);
		}
		if(!in_array('recep',$fields)){
			$query="ALTER TABLE `seri` ADD COLUMN `recep` CHAR(8) NOT NULL";
			$this->db->simple_query($query);
		}
		if(!in_array('frecep',$fields)){
			$query="ALTER TABLE `seri` ADD COLUMN `frecep` DATE NOT NULL";
			$this->db->simple_query($query);
		}
		if(!in_array('barras',$fields)){
			$query="ALTER TABLE `seri`  ADD COLUMN `barras` VARCHAR(50) NOT NULL";
			$this->db->simple_query($query);
		}


		//./inventario/conv.php
		$mSQL = "ALTER TABLE conv ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ";;
		$this->db->simple_query($mSQL);


		//./inventario/sinvactu.php
		$mSQL='
			CREATE TABLE /*!32312 IF NOT EXISTS*/ `sinvactu` (
		  `codigo` varchar(15) NOT NULL default "",
		  `descrip` varchar(45) default NULL,
		  `clave` varchar(8) default NULL,
		  `descrip2` varchar(45) default NULL,
		  `antdescrip2` varchar(45) default NULL,
		  `grupo` varchar(4) default NULL,
		  `costo` decimal(13,2) unsigned default NULL,
		  `precio1` decimal(13,2) unsigned default NULL,
		  `antcosto` decimal(13,2) unsigned default NULL,
		  `antprecio1` decimal(13,2) unsigned default NULL,
		  `iva` decimal(6,2) unsigned default NULL,
		  `antiva` decimal(6,2) unsigned default NULL,
		  `precio2` decimal(13,2) default NULL,
		  `precio3` decimal(13,2) default NULL,
		  `precio4` decimal(13,2) unsigned default NULL,
		  `base1` decimal(13,2) unsigned default NULL,
		  `base2` decimal(13,2) default NULL,
		  `base3` decimal(13,2) unsigned default NULL,
		  `base4` decimal(13,2) unsigned default NULL,
		  `margen1` decimal(13,2) unsigned default NULL,
		  `margen2` decimal(13,2) unsigned default NULL,
		  `margen3` decimal(13,2) unsigned default NULL,
		  `margen4` decimal(13,2) unsigned default NULL,
		  `antdescrip` varchar(45) default NULL,
		  `antclave` varchar(8) default NULL,
		  `antgrupo` varchar(4) default NULL,
		  `antprecio2` decimal(13,2) unsigned default NULL,
		  `antprecio3` decimal(13,2) unsigned default NULL,
		  `antprecio4` decimal(13,2) unsigned default NULL,
		  `antbase1` decimal(13,2) unsigned default NULL,
		  `antbase2` decimal(13,2) unsigned default NULL,
		  `antbase3` decimal(13,2) unsigned default NULL,
		  `antbase4` decimal(13,2) unsigned default NULL,
		  `antmargen1` decimal(13,2) unsigned default NULL,
		  `antmargen2` decimal(13,2) unsigned default NULL,
		  `antmargen3` decimal(13,2) unsigned default NULL,
		  `antmargen4` decimal(13,2) unsigned default NULL,
		  PRIMARY KEY  (`codigo`)
		)';
		$this->db->simple_query($mSQL);


		//./inventario/marc.php
		$campos=$this->db->list_fields('marc');
		if(!in_array('margen',$campos)){
			$query="ALTER TABLE `marc` ADD COLUMN `margen` DOUBLE(5,2) UNSIGNED NOT NULL DEFAULT '0.00'";
			$this->db->simple_query();
		}

		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE marc DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE marc ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE marc ADD UNIQUE INDEX marca (marca)');
		}


		//./inventario/ordp.php
		if (!$this->db->table_exists('ordp')) {
			$mSQL="CREATE TABLE `ordp` (
				`numero` VARCHAR(8) NOT NULL DEFAULT '',
				`fecha` DATE NULL DEFAULT NULL,
				`codigo` VARCHAR(15) NULL DEFAULT NULL COMMENT 'Codigo de inventario',
				`almacen` VARCHAR(4) NULL DEFAULT NULL COMMENT 'Almacen de descuento',
				`cana` DECIMAL(10,2) NULL DEFAULT NULL COMMENT 'Cantidad a producir',
				`status` CHAR(2) NULL DEFAULT '0' COMMENT 'Activa, Pausada, Finalizada',
				`cliente` CHAR(5) NULL DEFAULT NULL,
				`nombre` VARCHAR(40) NULL DEFAULT NULL COMMENT 'Nombre del Cliente',
				`instrucciones` TEXT NULL,
				`reserva` CHAR(1) NULL COMMENT 'Si ya se resevo el inventario',
				`estampa` DATE NOT NULL DEFAULT '0000-00-00',
				`usuario` VARCHAR(12) NOT NULL DEFAULT '',
				`hora` VARCHAR(8) NOT NULL DEFAULT '',
				`modificado` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `numero` (`numero`),
				INDEX `modificado` (`modificado`)
			)
			COMMENT='Orden de Produccion'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			AUTO_INCREMENT=1";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->field_exists('reserva', 'ordp')){
			$mSQL="ALTER TABLE `ordp` ADD COLUMN `reserva` CHAR(1) NULL COMMENT 'Si ya se resevo el inventario' AFTER `instrucciones`;";
			$this->db->simple_query($mSQL);
		}

		if ($this->db->field_exists('almacen', 'ordp')){
			$mSQL="ALTER TABLE `ordp` ADD COLUMN `almacen` VARCHAR(4) NULL DEFAULT NULL COMMENT 'Almacen de descuento' AFTER `codigo`";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists('ordpindi')) {
			$mSQL="CREATE TABLE `ordpindi` (
				`numero` VARCHAR(8) NULL DEFAULT '',
				`codigo` VARCHAR(6) NULL DEFAULT NULL COMMENT 'Codigo de Gasto',
				`descrip` VARCHAR(40) NULL DEFAULT NULL,
				`porcentaje` DECIMAL(14,3) NULL DEFAULT '0.000',
				`estampa` DATE NULL DEFAULT NULL,
				`usuario` VARCHAR(12) NULL DEFAULT '',
				`hora` VARCHAR(8) NULL DEFAULT '',
				`tipo` CHAR(1) NULL DEFAULT 'P' COMMENT 'P porcentual, M monetario',
				`modificado` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				`id_ordp` INT(11) NULL DEFAULT NULL,
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				INDEX `modificado` (`modificado`),
				INDEX `numero` (`numero`)
			)
			COMMENT='Costos indirectos Orden de Produccion'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			AUTO_INCREMENT=1";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists('ordpitem')) {
			$mSQL="CREATE TABLE `ordpitem` (
				`numero` VARCHAR(8) NULL DEFAULT '',
				`codigo` VARCHAR(15) NULL DEFAULT NULL,
				`descrip` VARCHAR(40) NULL DEFAULT NULL,
				`cantidad` DECIMAL(14,3) NULL DEFAULT '0.000',
				`merma` DECIMAL(10,2) NULL DEFAULT '0.00',
				`costo` DECIMAL(17,2) NULL DEFAULT '0.00',
				`estampa` DATE NULL DEFAULT NULL,
				`usuario` VARCHAR(12) NULL DEFAULT '',
				`hora` VARCHAR(8) NULL DEFAULT '',
				`modificado` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				`fijo` CHAR(1) NULL DEFAULT 'N' COMMENT 'N si depende de la cantidad, S si no depende de la cantidad',
				`id_ordp` INT(11) NULL DEFAULT NULL,
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				INDEX `modificado` (`modificado`),
				INDEX `numero` (`numero`)
			)
			COMMENT='Insumos de la Orden de Produccion'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			AUTO_INCREMENT=1";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists('ordpbita')) {
			$mSQL="CREATE TABLE `ordpbita` (
				`id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
				`id_ordplabor` INT(11) UNSIGNED NOT NULL,
				`id_ordp` INT(11) UNSIGNED NULL DEFAULT NULL,
				`fechahora` DATETIME NOT NULL,
				`status` CHAR(1) NOT NULL COMMENT 'I iniciado, P pausado, T terminado',
				`observacion` TEXT NOT NULL,
				`estampa` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
				`hora` VARCHAR(8) NOT NULL,
				`usuario` VARCHAR(50) NOT NULL,
				PRIMARY KEY (`id`),
				INDEX `id_ordplabor` (`id_ordplabor`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			AUTO_INCREMENT=1";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists('ordplabor')) {
			$mSQL="CREATE TABLE `ordplabor` (
				`producto` VARCHAR(15) NULL DEFAULT '' COMMENT 'Producto Terminado',
				`secuencia` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'Secuencia de las actividades',
				`estacion` VARCHAR(5) NULL DEFAULT NULL,
				`nombre` VARCHAR(40) NULL DEFAULT NULL,
				`actividad` VARCHAR(100) NOT NULL,
				`tunidad` CHAR(1) NULL DEFAULT 'H',
				`tiempo` DECIMAL(10,2) UNSIGNED NULL DEFAULT '0',
				`tiemporeal` INT(6) UNSIGNED NULL DEFAULT '0',
				`estampa` DATE NULL DEFAULT NULL,
				`usuario` VARCHAR(12) NULL DEFAULT '',
				`hora` VARCHAR(8) NULL DEFAULT '',
				`modificado` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				`id_ordp` INT(11) NULL DEFAULT NULL,
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				INDEX `modificado` (`modificado`)
			)
			COMMENT='Acciones de la Orden de Produccion'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			AUTO_INCREMENT=1";
			$this->db->simple_query($mSQL);
		}

		if ($this->db->field_exists('minutos', 'ordplabor')){
			$mSQL="ALTER TABLE `ordplabor`
			CHANGE COLUMN `minutos` `tunidad` CHAR(1) NULL DEFAULT 'H' AFTER `actividad`,
			CHANGE COLUMN `segundos` `tiempo` DECIMAL(10,2) UNSIGNED NULL DEFAULT '0' AFTER `tunidad`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('fijo', 'ordpitem')){
			$mSQL="ALTER TABLE `ordpitem` ADD COLUMN `fijo` CHAR(1) NULL DEFAULT 'N' COMMENT 'N si depende de la cantidad, S si no depende de la cantidad' AFTER `modificado`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('tipo', 'ordpindi')){
			$mSQL="ALTER TABLE `ordpindi` ADD COLUMN `tipo` CHAR(1) NULL DEFAULT 'P' COMMENT 'P porcentual, M monetario' AFTER `hora`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('ordp', 'stra')){
			$mSQL="ALTER TABLE `stra`
			ADD COLUMN `ordp` VARCHAR(8) NULL DEFAULT NULL AFTER `numere`,
			ADD COLUMN `esta` VARCHAR(5) NULL DEFAULT NULL AFTER `ordp`";
			$this->db->simple_query($mSQL);
		}


		//./inventario/grup.php
		$campos=$this->db->list_fields('grup');

		if(!in_array('id'  ,$campos)){
			$this->db->simple_query('ALTER TABLE grup DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE grup ADD UNIQUE INDEX grupo (grupo)');
			$this->db->simple_query('ALTER TABLE grup ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		if(!in_array('precio'  ,$campos)){
			$this->db->simple_query("ALTER TABLE grup ADD COLUMN precio CHAR(1) NULL DEFAULT '0'");
		}

		if(!in_array('margen',$campos)){
			$mSQL="ALTER TABLE `grup`
			ADD COLUMN `margen` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `comision`,
			ADD COLUMN `margenc` DECIMAL(10,2) NOT NULL DEFAULT '0.00' AFTER `margen`";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('status',$campos)){
			$mSQL="ALTER TABLE `grup` ADD COLUMN `status` CHAR(1) NULL DEFAULT NULL";
			$this->db->simple_query($mSQL);
		}


		//./inventario/line.php
		$campos=$this->db->list_fields('line');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE line DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE line ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE line ADD UNIQUE INDEX linea (linea)');
		}

		if(!$this->db->table_exists('view_line')) {
			$mSQL = 'CREATE ALGORITHM=UNDEFINED DEFINER=`'.$this->db->username.'`@`'.$this->db->hostname.'`
				SQL SECURITY INVOKER VIEW `view_line` AS
				select `a`.`linea` AS `linea`,`a`.`descrip` AS `descrip`,`a`.`cu_cost` AS `cu_cost`,`a`.`cu_inve` AS `cu_inve`,`a`.`cu_venta` AS `cu_venta`,`a`.`cu_devo` AS `cu_devo`,`a`.`depto` AS `depto`,`a`.`id` AS `id`,concat(`b`.`depto`, " ", `b`.`descrip`) AS `desdepto` from (`line` `a` join `dpto` `b` on((`a`.`depto` = `b`.`depto`)))';
			$this->db->simple_query($mSQL);
		}


		//./inventario/pactivo.php
		if (!$this->db->table_exists('pactivo')) {
			$mSQL="CREATE TABLE `pactivo` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `nombre` varchar(250) COLLATE utf8_unicode_ci DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `nombre` (`nombre`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='Principios Activos'";
			$this->db->simple_query($mSQL);
		}


		//./inventario/esta.php
		if (!$this->db->table_exists('esta')){
			$mSQL="CREATE TABLE `esta` (
				`estacion` VARCHAR(5) NOT NULL DEFAULT '',
				`nombre` VARCHAR(30) NULL DEFAULT NULL,
				`descrip` TEXT NULL,
				`ubica` TEXT NULL,
				`jefe` CHAR(5) NULL DEFAULT NULL COMMENT 'tecnico',
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `vendedor` (`estacion`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists('estajefe')){
			$mSQL="CREATE TABLE `estajefe` (
				`codigo` VARCHAR(5) NOT NULL DEFAULT '',
				`nombre` VARCHAR(30) NULL DEFAULT NULL,
				`cedula` VARCHAR(12) NULL DEFAULT NULL,
				`direc1` VARCHAR(35) NULL DEFAULT NULL,
				`direc2` VARCHAR(35) NULL DEFAULT NULL,
				`telefono` VARCHAR(13) NULL DEFAULT NULL,
				`correo` VARCHAR(250) NULL DEFAULT NULL,
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `unico` (`codigo`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		//if(!$this->db->field_exists('cedula', 'estajefe')){
		//	$mSQL="ALTER TABLE `estajefe` ADD COLUMN `cedula` VARCHAR(12) NULL DEFAULT NULL AFTER `nombre`";
		//	$this->db->simple_query($mSQL);
		//}

		$campos=$this->db->list_fields('esta');
		if(!in_array('id',$campos)){
			$mSQL="ALTER TABLE `esta` ADD COLUMN `ubica` TEXT NULL AFTER `descrip`";
			$this->db->simple_query($mSQL);
		}


		//./inventario/icon.php
		if (!$this->db->table_exists('icon')) {
			$mSQL="CREATE TABLE `icon` (
				codigo   char( 6) DEFAULT NULL,
				concepto char(30) DEFAULT NULL,
				gasto    char( 6) DEFAULT NULL,
				gastode  char(30) DEFAULT NULL,
				ingreso  char( 5) DEFAULT NULL,
				ingresod char(30) DEFAULT NULL,
				depto    char( 2) DEFAULT NULL,
				tipo     char( 1) DEFAULT 'E',
				id       INT( 11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}


		//./inventario/sinvpromo.php
		if (!$this->db->table_exists('sinvpromo')) {
			$mSQL="CREATE TABLE `sinvpromo` (
				`id` BIGINT(20) NOT NULL AUTO_INCREMENT,
				`cliente` CHAR(5) NULL DEFAULT NULL,
				`codigo` VARCHAR(15) NULL DEFAULT NULL,
				`tipo` CHAR(1) NULL DEFAULT NULL,
				`margen` DECIMAL(18,2) NULL DEFAULT NULL,
				`cantidad` DECIMAL(18,3) NULL DEFAULT NULL,
				`fdesde` DATETIME NULL DEFAULT NULL,
				`fhasta` DATETIME NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				INDEX `codigo` (`codigo`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM;";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('sinvpromo');
		if(!in_array('fdesde',$campos)){
			$mSQL="ALTER TABLE `sinvpromo`
			ADD COLUMN `fdesde` DATETIME NULL DEFAULT NULL AFTER `cantidad`,
			ADD COLUMN `fhasta` DATETIME NULL DEFAULT NULL AFTER `fdesde`";
			$this->db->simple_query($mSQL);
		}


		//./inventario/dpto.php
		//if (!$this->db->table_exists('dpto')) {
		//	$mSQL="CREATE TABLE `dpto` (
		//	  `tipo` char(1) NOT NULL DEFAULT 'I',
		//	  `depto` char(3) NOT NULL DEFAULT '',
		//	  `descrip` varchar(30) DEFAULT NULL,
		//	  `cu_venta` varchar(15) DEFAULT NULL,
		//	  `cu_inve` varchar(15) DEFAULT NULL,
		//	  `cu_cost` varchar(15) DEFAULT NULL,
		//	  `cu_devo` varchar(15) DEFAULT NULL,
		//	  `id` int(11) NOT NULL AUTO_INCREMENT,
		//	  PRIMARY KEY (`id`),
		//	  UNIQUE KEY `depto` (`depto`),
		//	  KEY `depto_2` (`depto`)
		//	) ENGINE=MyISAM AUTO_INCREMENT=13 DEFAULT CHARSET=latin1 COMMENT='Departamentos de Inv'";
		//	$this->db->simple_query($mSQL);
		//}

		$campos=$this->db->list_fields('dpto');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE dpto DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE dpto ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE dpto ADD UNIQUE INDEX depto (depto)');
		}

		//$this->db->simple_query("INSERT IGNORE INTO dpto (depto,tipo,descrip) VALUES ('99','G','INVERSION EN ACTIVOS')ON DUPLICATE KEY UPDATE depto='99', tipo='G',descrip='INVERSION EN ACTIVOS'");
		//$this->db->simple_query("INSERT IGNORE INTO dpto (depto,tipo,descrip) VALUES ('98','G','GASTOS FINANCIEROS')ON DUPLICATE KEY UPDATE depto='98', tipo='G',descrip='GASTOS FINANCIEROS'");
		//$this->db->simple_query("INSERT IGNORE INTO dpto (depto,tipo,descrip) VALUES ('97','G','GASTOS DE ADMINISTRACION')ON DUPLICATE KEY UPDATE depto='97', tipo='G',descrip='GASTOS DE ADMINISTRACION'");
		//$this->db->simple_query("INSERT IGNORE INTO dpto (depto,tipo,descrip) VALUES ('96','G','GASTOS DE VENTA')ON DUPLICATE KEY UPDATE depto='96', tipo='G',descrip='GASTOS DE VENTA'");
		//$this->db->simple_query("INSERT IGNORE INTO dpto (depto,tipo,descrip) VALUES ('95','G','GASTOS DE COMPRA')ON DUPLICATE KEY UPDATE depto='95', tipo='G',descrip='GASTOS DE COMPRA'");



		//./inventario/sinv.php

		$campos = $this->db->list_fields('sinv');
		if (!in_array('id',$campos)){
			$mSQL='ALTER TABLE `sinv` DROP PRIMARY KEY';
			$this->db->simple_query($mSQL);
			$mSQL='ALTER TABLE `sinv` ADD UNIQUE `codigo` (`codigo`)';
			$this->db->simple_query($mSQL);
			$mSQL='ALTER TABLE sinv ADD id INT AUTO_INCREMENT PRIMARY KEY';
			$this->db->simple_query($mSQL);
		}

		if (!in_array('alto'       ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN alto          DECIMAL(10,2)");
		if (!in_array('ancho'      ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN ancho         DECIMAL(10,2)");
		if (!in_array('largo'      ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN largo         DECIMAL(10,2)");
		if (!in_array('forma'      ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN forma         VARCHAR(50)");
		if (!in_array('exento'     ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN exento        CHAR(1) DEFAULT 'N'");
		if (!in_array('mmargen'    ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN mmargen       DECIMAL(7,2) DEFAULT 0 COMMENT 'Margen al Mayor'");
		if (!in_array('pm'         ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `pm`          DECIMAL(19,2) NULL DEFAULT '0.00' COMMENT 'porcentaje mayor'");
		if (!in_array('pmb'        ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `pmb`         DECIMAL(19,2) NULL DEFAULT '0.00' COMMENT 'porcentaje mayor'");
		if (!in_array('mmargenplus',$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `mmargenplus` DECIMAL(7,2) NULL DEFAULT '0.00' COMMENT 'Margen al Mayor'");
		if (!in_array('escala1'    ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `escala1`     DECIMAL(12,2) NULL DEFAULT '0.00'");
		if (!in_array('pescala1'   ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `pescala1`    DECIMAL(5,2) NULL DEFAULT '0.00' COMMENT 'porcentaje descuento escala1'");
		if (!in_array('escala2'    ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `escala2`     DECIMAL(12,2) NULL DEFAULT '0.00'");
		if (!in_array('pescala2'   ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `pescala2`    DECIMAL(5,2) NULL DEFAULT '0.00' COMMENT 'porcentaje descuento escala2'");
		if (!in_array('escala3'    ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `escala3`     DECIMAL(12,2) NULL DEFAULT '0.00'");
		if (!in_array('pescala3'   ,$campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `pescala3`    DECIMAL(5,2) NULL DEFAULT '0.00' COMMENT 'porcentaje descuento escala3'");
		if (!in_array('mpps',       $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `mpps`        VARCHAR(20) NULL  COMMENT 'Numero de Ministerior de Salud'");
		if (!in_array('cpe',        $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `cpe`         VARCHAR(20) NULL  COMMENT 'Registro de CPE'");
		if (!in_array('tasa',       $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `cpe`         VARCHAR(20) NULL  COMMENT 'Tasa asociada'");

		if ( $this->datasis->traevalor('SUNDECOP') == 'S') {
			if (!in_array('dcomercial', $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `dcomercial`  INT(6)     NULL  COMMENT 'Destino Comercial'");
			if (!in_array('rubro',      $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `rubro`       INT(6)     NULL  COMMENT 'Rubro'");
			if (!in_array('subrubro',   $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `subrubro`    INT(6)     NULL  COMMENT 'Sub Rubro'");
			if (!in_array('cunidad',    $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `cunidad`     INT(6)     NULL  COMMENT 'Unidad de Medida'");
			if (!in_array('cmarca',     $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `cmarca`      INT(6)     NULL  COMMENT 'Marca'");
			if (!in_array('cmaterial',  $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `cmaterial`   INT(6)     NULL  COMMENT 'Material'");
			if (!in_array('cpresenta',  $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `cforma`      INT(6)     NULL  COMMENT 'Forma o Presentacion'");
			if (!in_array('cpactivo',   $campos)) $this->db->simple_query("ALTER TABLE `sinv` ADD COLUMN `cpactivo`    INT(6)     NULL  COMMENT 'Principio Activo'");
		}

		if(!$this->db->table_exists('sinvcombo')){
			$mSQL="CREATE TABLE `sinvcombo` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`combo` CHAR(15) NOT NULL,
				`codigo` CHAR(15) NOT NULL DEFAULT '',
				`descrip` CHAR(30) NULL DEFAULT NULL,
				`cantidad` DECIMAL(10,3) NULL DEFAULT NULL,
				`precio` DECIMAL(15,2) NULL DEFAULT NULL,
				`transac` CHAR(8) NULL DEFAULT NULL,
				`estampa` DATE NULL DEFAULT NULL,
				`hora` CHAR(8) NULL DEFAULT NULL,
				`usuario` CHAR(12) NULL DEFAULT NULL,
				`costo` DECIMAL(17,2) NULL DEFAULT '0.00',
				`ultimo` DECIMAL(19,2) NULL DEFAULT '0.00',
				`pond` DECIMAL(19,2) NULL DEFAULT '0.00',
				PRIMARY KEY (`id`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('sinvpitem')){
			$mSQL="CREATE TABLE `sinvpitem` (
				`producto` VARCHAR(15) NULL DEFAULT NULL COMMENT 'codigo del prod terminado (sinv)',
				`codigo` VARCHAR(15) NULL DEFAULT NULL COMMENT 'codigo del Insumo (sinv)',
				`descrip` VARCHAR(40) NULL DEFAULT NULL,
				`cantidad` DECIMAL(14,3) NULL DEFAULT '0.000',
				`merma` DECIMAL(10,2) NULL DEFAULT '0.00' COMMENT 'Porcentaje de merma',
				`estampa` DATE NULL DEFAULT NULL,
				`usuario` VARCHAR(12) NULL DEFAULT '',
				`hora` VARCHAR(8) NULL DEFAULT '',
				`modificado` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				`id_sinv` INT(11) NULL DEFAULT NULL,
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`ultimo` DECIMAL(19,2) NOT NULL DEFAULT '0.00',
				`pond` DECIMAL(19,2) NOT NULL DEFAULT '0.00',
				`formcal` CHAR(1) NOT NULL,
				PRIMARY KEY (`id`),
				INDEX `modificado` (`modificado`)
			)
			COMMENT='Insumos de un producto terminado'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DYNAMIC
			AUTO_INCREMENT=1";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('sinvplabor')){
			$mSQL="CREATE TABLE `sinvplabor` (
				`producto` VARCHAR(15) NULL DEFAULT '' COMMENT 'Producto Terminado',
				`estacion` VARCHAR(5) NULL DEFAULT NULL,
				`nombre` VARCHAR(40) NULL DEFAULT NULL,
				`actividad` VARCHAR(100) NOT NULL,
				`minutos` INT(6) NULL DEFAULT '0',
				`segundos` INT(6) NULL DEFAULT '0',
				`estampa` DATE NULL DEFAULT NULL,
				`usuario` VARCHAR(12) NULL DEFAULT '',
				`hora` VARCHAR(8) NULL DEFAULT '',
				`modificado` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				INDEX `modificado` (`modificado`)
			)
			COMMENT='Acciones de la Orden de Produccion'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			AUTO_INCREMENT=1";
			$this->db->simple_query($mSQL);
		}

		if ($this->db->field_exists('minutos', 'sinvplabor')){
			$mSQL="ALTER TABLE `sinvplabor`
			ADD COLUMN `tiempo` DECIMAL(10,2) NULL DEFAULT '0' AFTER `actividad`,
			ADD COLUMN `tunidad` CHAR(1) NULL DEFAULT 'H' COMMENT 'Unidad de tiempo Horas Dias Semanas' AFTER `tiempo`,
			DROP COLUMN `minutos`,
			DROP COLUMN `segundos`";
			$this->db->simple_query($mSQL);
		}


		if(!$this->db->table_exists('esta')){
			$mSQL="CREATE TABLE `esta` (
				`estacion` VARCHAR(5) NOT NULL DEFAULT '',
				`nombre` VARCHAR(30) NULL DEFAULT NULL,
				`descrip` TEXT NULL,
				`ubica` TEXT NULL,
				`jefe` VARCHAR(5) NULL DEFAULT NULL COMMENT 'tecnico',
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `vendedor` (`estacion`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			AUTO_INCREMENT=1;";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('sinvprov')){
			$mSQL="CREATE TABLE `sinvprov` (
				`proveed` CHAR(5) NOT NULL DEFAULT '',
				`codigop` CHAR(15) NOT NULL DEFAULT '',
				`codigo` CHAR(15) NOT NULL DEFAULT '',
				PRIMARY KEY (`proveed`, `codigop`, `codigo`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
		}

		if(!$this->db->table_exists('barraspos')){
			$query="CREATE TABLE `barraspos` (
				`codigo` CHAR(15) NOT NULL DEFAULT '',
				`suplemen` CHAR(15) NOT NULL DEFAULT '',
				PRIMARY KEY (`codigo`, `suplemen`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
			$this->db->simple_query($query);
		}
		if(!$this->db->table_exists('invfelr')){
			$query="CREATE TABLE `invfelr` (
				`codigo` CHAR(15) NOT NULL DEFAULT '',
				`fecha` DATE NOT NULL DEFAULT '0000-00-00',
				`precio` DECIMAL(17,2) NOT NULL DEFAULT '0.00',
				`existen` DECIMAL(17,2) NULL DEFAULT NULL,
				`anterior` DECIMAL(17,2) NULL DEFAULT NULL,
				`parcial` DECIMAL(17,2) NULL DEFAULT NULL,
				`alma` CHAR(4) NOT NULL DEFAULT '',
				`tipo` CHAR(1) NULL DEFAULT NULL,
				`fhora` TIMESTAMP NOT NULL DEFAULT '0000-00-00 00:00:00',
				`usuario` CHAR(12) NULL DEFAULT NULL,
				`ubica` CHAR(10) NOT NULL DEFAULT ''
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
		}


		//./inventario/stra.php
		$campos=$this->db->list_fields('stra');

		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE stra DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE stra ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE stra ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		if(!in_array('proveed',$campos)){
			$this->db->simple_query('ALTER TABLE `stra` ADD COLUMN `proveed` CHAR(5) NULL DEFAULT NULL COMMENT \'Para el caso de las transferencias por RMS\'');
		}

		if(!in_array('ordp',$campos)){
			$mSQL="ALTER TABLE `stra`
			ADD COLUMN `ordp` VARCHAR(8) NULL DEFAULT NULL,
			ADD COLUMN `esta` VARCHAR(5) NULL DEFAULT NULL ";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('tipoordp',$campos)){
			$mSQL="ALTER TABLE `stra` ADD COLUMN `tipoordp` CHAR(1) NULL DEFAULT NULL COMMENT 'Si es entrega a estacion o retiro de estacion'";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('condiciones',$campos)){
			$mSQL="ALTER TABLE `stra` ADD COLUMN `condiciones` TEXT NULL DEFAULT NULL";
			$this->db->simple_query($mSQL);
		}


		//./inventario/usol.php
		if (!$this->db->table_exists('usol')) {
			$mSQL="CREATE TABLE `usol` (
			  `codigo` char(2) NOT NULL DEFAULT '',
			  `nombre` char(30) DEFAULT NULL,
			  `gasto` char(6) DEFAULT NULL,
			  `depto` char(3) DEFAULT NULL,
			  `sucursal` char(2) DEFAULT NULL,
			  PRIMARY KEY (`codigo`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('usol');
		//if(!in_array('<#campo#>',$campos)){ }


		//./inventario/scon.php
		if (!$this->db->table_exists('scon')) {
			$mSQL="CREATE TABLE IF NOT EXISTS `scon` (
				`numero` CHAR(8) NULL DEFAULT NULL,
				`fecha` DATE NULL DEFAULT NULL,
				`tipo` CHAR(1) NULL DEFAULT NULL,
				`tipod` CHAR(1) NULL DEFAULT NULL,
				`status` CHAR(1) NULL DEFAULT 'T',
				`asociado` CHAR(8) NULL DEFAULT NULL,
				`clipro` CHAR(5) NULL DEFAULT NULL,
				`almacen` CHAR(4) NULL DEFAULT NULL,
				`nombre` CHAR(40) NULL DEFAULT NULL,
				`direc1` CHAR(40) NULL DEFAULT NULL,
				`direc2` CHAR(40) NULL DEFAULT NULL,
				`observ1` CHAR(33) NULL DEFAULT NULL,
				`observ2` CHAR(33) NULL DEFAULT NULL,
				`stotal` DECIMAL(12,2) NULL DEFAULT NULL,
				`impuesto` DECIMAL(12,2) NULL DEFAULT NULL,
				`gtotal` DECIMAL(12,2) NULL DEFAULT NULL,
				`peso` DECIMAL(10,3) NULL DEFAULT NULL,
				`id` INT(15) UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `numero` (`numero`, `tipo`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
			var_dump($this->db->simple_query($mSQL));
		}

		if (!$this->db->table_exists('itscon')) {
			$mSQL="CREATE TABLE IF NOT EXISTS `itscon` (
				`numero` CHAR(8) NULL DEFAULT NULL,
				`codigo` VARCHAR(15) NULL DEFAULT NULL,
				`desca` VARCHAR(40) NULL DEFAULT NULL,
				`cana` DECIMAL(5,0) NULL DEFAULT NULL,
				`recibido` DECIMAL(5,0) NULL DEFAULT NULL,
				`precio` DECIMAL(12,2) NULL DEFAULT NULL,
				`importe` DECIMAL(12,2) NULL DEFAULT NULL,
				`iva` DECIMAL(8,2) NULL DEFAULT NULL,
				`id_scon` INT(15) UNSIGNED NOT NULL,
				`id` INT(15) UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				INDEX `id_scon` (`id_scon`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('scon','origen')){
			$mSQL="ALTER TABLE scon ADD COLUMN origen CHAR(1) NOT NULL DEFAULT 'L' AFTER peso";
			var_dump($this->db->simple_query($mSQL));
		}


		//./inventario/ssal.php
		$campos=$this->db->list_fields('ssal');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE ssal DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE ssal ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE ssal ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};


		//./inventario/caub.php
		$campos=$this->db->list_fields('caub');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE caub DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE caub ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE caub ADD UNIQUE INDEX ubica (ubica)');
		}

		if(!in_array('url',$campos)){
			$this->db->simple_query('ALTER TABLE caub ADD COLUMN url VARCHAR(100)');
		}

		if(!in_array('odbc',$campos)){
			$this->db->simple_query('ALTER TABLE caub ADD COLUMN odbc VARCHAR(100)');
		}

		if(!in_array('tipo',$campos)){
			$this->db->simple_query('ALTER TABLE caub ADD COLUMN tipo CHAR(1)');
		}

		$this->db->simple_query('UPDATE caub SET tipo="S" WHERE tipo="" OR tipo IS NULL ');
		$this->db->simple_query('UPDATE caub SET tipo="N" WHERE gasto="S" OR invfis = "S" ');



		$c=$this->datasis->dameval('SELECT COUNT(*) FROM caub WHERE ubica="AJUS"');
		if(!($c>0)) $this->db->simple_query('INSERT IGNORE INTO caub (ubica,ubides,gasto,invfis) VALUES ("AJUS","AJUSTES","S","N")');
		$this->db->simple_query('UPDATE caub SET ubides="AJUSTES", gasto="S",invfis="N" WHERE  ubica="AJUS" ');

		$c=$this->datasis->dameval("SELECT COUNT(*) FROM caub WHERE ubica='INFI'");
		if(!($c>0)) $this->db->simple_query("INSERT IGNORE INTO caub (ubica,ubides,gasto,invfis) VALUES ('INFI','INVENTARIO FISICO','S','S')");
		$this->db->simple_query("UPDATE caub SET ubides='INVENTARIO FISICO', gasto='S',invfis='S' WHERE ubica='INFI'");

		//./ventas/grcl.php
		//if (!$this->db->table_exists('grcl')) {
		//	$mSQL="CREATE TABLE `grcl` (
		//	  `grupo` varchar(4) NOT NULL DEFAULT '',
		//	  `gr_desc` varchar(25) DEFAULT NULL,
		//	  `clase` char(1) DEFAULT NULL,
		//	  `cuenta` varchar(15) DEFAULT NULL,
		//	  PRIMARY KEY (`grupo`)
		//	) ENGINE=MyISAM DEFAULT CHARSET=latin1";
		//	$this->db->simple_query($mSQL);
		//}

		$campos=$this->db->list_fields('grcl');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE grcl DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE grcl ADD UNIQUE INDEX grupo (grupo)');
			$this->db->simple_query('ALTER TABLE grcl ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');

		}


		//./ventas/chofer.php
		if (!$this->db->table_exists('chofer')) {
			$mSQL="CREATE TABLE `chofer` (
			  `codigo` varchar(5) NOT NULL DEFAULT '',
			  `cedula` varchar(15) DEFAULT NULL,
			  `nombre` varchar(30) DEFAULT NULL,
			  `direc1` varchar(35) DEFAULT NULL,
			  `direc2` varchar(35) DEFAULT NULL,
			  `telefono` varchar(13) DEFAULT NULL,
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `chofer` (`codigo`)
			) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('chofer');
		//if(!in_array('<#campo#>',$campos)){ }


		//./ventas/zona.php
		 $campos = $this->db->list_fields('zona');

		if (!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE zona DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE zona ADD UNIQUE INDEX codigo (codigo)');
			$this->db->simple_query('ALTER TABLE zona ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		if (!in_array('margen',$campos)){
			$this->db->simple_query("ALTER TABLE `zona` ADD COLUMN `margen` DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT '0.00'");
		}

		//./ventas/flota.php
		if (!$this->db->table_exists('flota')) {
			$mSQL="CREATE TABLE `flota` (
			  `codigo` varchar(10) NOT NULL DEFAULT '' COMMENT 'Cod de Autobus',
			  `descrip` varchar(30) NOT NULL DEFAULT '' COMMENT 'Accionista -> tbaccio',
			  `tipo` varchar(10) NOT NULL DEFAULT '' COMMENT 'Tipo de unidad ->tbmodbus',
			  `placa` varchar(10) NOT NULL DEFAULT '' COMMENT 'Nro Matricula',
			  `marca` varchar(20) NOT NULL DEFAULT '' COMMENT 'Marca',
			  `modelo` varchar(40) NOT NULL DEFAULT '' COMMENT 'Modelo',
			  `ano` int(11) NOT NULL DEFAULT '2001' COMMENT 'Anno',
			  `capacidad` decimal(10,2) NOT NULL DEFAULT '0.00' COMMENT 'Nro de Asientos',
			  `serialc` varchar(50) NOT NULL DEFAULT '' COMMENT 'Serial Carroceria',
			  `serialm` varchar(50) NOT NULL DEFAULT '' COMMENT 'Serial Motor',
			  `color` varchar(50) NOT NULL DEFAULT '' COMMENT 'Serial Motor',
			  `propietario` varchar(50) NOT NULL DEFAULT '' COMMENT 'Propietario',
			  `cedula` varchar(15) NOT NULL DEFAULT '' COMMENT 'C.I.',
			  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'Serial Motor',
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `codigo` (`codigo`)
			) ENGINE=MyISAM AUTO_INCREMENT=189 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC COMMENT='Flota '";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('flota');
		//if(!in_array('<#campo#>',$campos)){ }


		//./ventas/scaj.php

		//$mSQL="CREATE TABLE IF NOT EXISTS `vieite` (
		//	`numero` char(8) default NULL,
		//	`fecha` date default '0000-00-00',
		//	`codigo` char(15) default NULL,
		//	`precio` decimal(10,2) default '0.00',
		//	`monto` decimal(18,2) default '0.00',
		//	`cantidad` decimal(12,3) default NULL,
		//	`impuesto` decimal(6,2) default '0.00',
		//	`costo` decimal(18,2) default '0.00',
		//	`almacen` char(4) default NULL,
		//	`cajero` char(5) default NULL,
		//	`caja` char(5) NOT NULL default '',
		//	`referen` char(15) default NULL,
		//	KEY `fecha` (`fecha`),
		//	KEY `codigo` (`codigo`)
		//	) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='ventas por articulo'";
		//$this->db->simple_query($mSQL);

		//$mSQL="CREATE TABLE IF NOT EXISTS `fmay` (
		//	`fecha` date default NULL,
		//	`numero` varchar(8) NOT NULL default '',
		//	`presup` varchar(8) default NULL,
		//	`almacen` varchar(4) default NULL,
		//	`cod_cli` varchar(5) default NULL,
		//	`nombre` varchar(40) default NULL,
		//	`vence` date default NULL,
		//	`vende` varchar(5) default NULL,
		//	`stotal` decimal(17,2) default '0.00',
		//	`impuesto` decimal(17,2) default '0.00',
		//	`gtotal` decimal(17,2) default '0.00',
		//	`tipo` char(1) default NULL,
		//	`observa1` varchar(40) default NULL,
		//	`observa2` varchar(40) default NULL,
		//	`observa3` varchar(40) default NULL,
		//	`porcenta` decimal(17,2) default '0.00',
		//	`descuento` decimal(17,2) default '0.00',
		//	`cajero` varchar(5) default NULL,
		//	`dire1` varchar(30) default NULL,
		//	`dire2` varchar(30) default NULL,
		//	`rif` varchar(15) default NULL,
		//	`nit` varchar(15) default NULL,
		//	`exento` decimal(17,2) default '0.00',
		//	`transac` varchar(8) default NULL,
		//	`estampa` date default NULL,
		//	`hora` varchar(5) default NULL,
		//	`usuario` varchar(12) default NULL,
		//	`nfiscal` varchar(12) NOT NULL default '0',
		//	`tasa` decimal(19,2) default NULL,
		//	`reducida` decimal(19,2) default NULL,
		//	`sobretasa` decimal(17,2) default NULL,
		//	`montasa` decimal(17,2) default NULL,
		//	`monredu` decimal(17,2) default NULL,
		//	`monadic` decimal(17,2) default NULL,
		//	`cedula` varchar(13) default NULL,
		//	`dirent1` varchar(40) default NULL,
		//	`dirent2` varchar(40) default NULL,
		//	`dirent3` varchar(40) default NULL,
		//	PRIMARY KEY  (`numero`)
		//	) ENGINE=MyISAM DEFAULT CHARSET=latin1";
		//$this->db->simple_query($mSQL);

		$this->db->simple_query('UPDATE scaj SET cajero=TRIM(cajero)');
		$campos=$this->db->list_fields('scaj');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE scaj DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE scaj ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE scaj ADD UNIQUE INDEX cajero (cajero)');
		}



		//./ventas/fiscalz.php
		if (!$this->db->table_exists('fiscalz')) {
			$mSQL="CREATE TABLE `fiscalz` (
			  `caja` char(4) DEFAULT NULL,
			  `serial` char(12) NOT NULL DEFAULT '',
			  `numero` char(4) NOT NULL DEFAULT '',
			  `fecha` date DEFAULT NULL,
			  `factura` char(8) DEFAULT NULL,
			  `fecha1` date DEFAULT NULL,
			  `hora` time DEFAULT NULL,
			  `exento` decimal(12,2) unsigned DEFAULT NULL,
			  `base` decimal(12,2) unsigned DEFAULT NULL,
			  `iva` decimal(12,2) unsigned DEFAULT NULL,
			  `base1` decimal(12,2) unsigned DEFAULT NULL,
			  `iva1` decimal(12,2) unsigned DEFAULT NULL,
			  `base2` decimal(12,2) unsigned DEFAULT NULL,
			  `iva2` decimal(12,2) unsigned DEFAULT NULL,
			  `ncexento` decimal(12,2) unsigned DEFAULT NULL,
			  `ncbase` decimal(12,2) unsigned DEFAULT NULL,
			  `nciva` decimal(12,2) unsigned DEFAULT NULL,
			  `ncbase1` decimal(12,2) unsigned DEFAULT NULL,
			  `nciva1` decimal(12,2) unsigned DEFAULT NULL,
			  `ncbase2` decimal(12,2) unsigned DEFAULT NULL,
			  `nciva2` decimal(12,2) unsigned DEFAULT NULL,
			  `ncnumero` char(8) DEFAULT NULL,
			  `manual` char(1) DEFAULT 'N',
			  PRIMARY KEY (`serial`,`numero`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('fiscalz');
		if(!in_array('manual',$campos)){
			$mSQL="ALTER TABLE `fiscalz` ADD `manual` CHAR(1)DEFAULT 'N' NULL";
			$this->db->simple_query($mSQL);
		}

		//./ventas/tarjeta.php
		$campos=$this->db->list_fields('tarjeta');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE tarjeta DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE tarjeta ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE tarjeta ADD UNIQUE INDEX tipo (tipo)');
		}

		if(!in_array('activo',$campos)){
			$mSQL="ALTER TABLE `tarjeta` ADD COLUMN `activo`  CHAR(1) NULL DEFAULT 'S' AFTER `mensaje`";
			$this->db->query($mSQL);
		}

		//./ventas/pfac.php
		if (!$this->db->field_exists('dxapli','itpfac'))
		$this->db->query("ALTER TABLE `itpfac`  ADD COLUMN `dxapli` VARCHAR(20) NOT NULL COMMENT 'descuento por aplicar'");
		$this->db->query("ALTER TABLE `itpfac`  CHANGE COLUMN `dxapli` `dxapli` VARCHAR(20) NULL COMMENT 'descuento por aplicar'");

		//./ventas/caja.php
		$campos=$this->db->list_fields('caja');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE caja DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE caja ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE caja ADD UNIQUE INDEX caja (caja)');
		}


		//./ventas/mvcerti.php
		if (!$this->db->table_exists('mvcerti')) {
			$mSQL = "CREATE TABLE mvcerti (
					id BIGINT(20) NOT NULL AUTO_INCREMENT,
					cliente CHAR(5) NULL DEFAULT NULL COMMENT 'Codigo del Cliente',
					numero CHAR(32) NULL DEFAULT NULL COMMENT 'Numero de Certificado',
					fecha DATE NULL DEFAULT NULL COMMENT 'Fecha del certificado',
					obra VARCHAR(200) NULL DEFAULT NULL COMMENT 'Nombre de la Obra',
					status CHAR(1) NULL DEFAULT 'A' COMMENT 'Activo Cerrado',
					PRIMARY KEY (id),
					UNIQUE INDEX numero (numero),
					INDEX cliente (cliente)
				)
				COLLATE='latin1_swedish_ci'
				ENGINE=MyISAM
				ROW_FORMAT=DEFAULT";
			$this->db->simple_query($mSQL);

			$mSQL = "CREATE ALGORITHM=UNDEFINED SQL SECURITY INVOKER VIEW `view_mvcerti` AS
				select `a`.`id` AS `id`,if((`a`.`status` = 'A'),'ACTIVO','CERRADO') AS `status`,`a`.`cliente` AS `cliente`,`b`.`nombre` AS `nombre`,`a`.`fecha` AS `fecha`,`a`.`numero` AS `numero`,`a`.`obra` AS `obra`
				from (`mvcerti` `a` join `scli` `b` on((`a`.`cliente` = `b`.`cliente`)))
				order by `a`.`id` desc";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists('view_mvcerti')){
			$mSQL = "CREATE ALGORITHM=UNDEFINED
					DEFINER=`".$this->db->username."`@`".$this->db->hostname."`
					SQL SECURITY INVOKER VIEW `view_mvcerti` AS
					select `a`.`id` AS `id`,if((`a`.`status` = 'A'),'ACTIVO','CERRADO') AS `status`,`a`.`cliente` AS `cliente`,`b`.`nombre` AS `nombre`,`a`.`fecha` AS `fecha`,`a`.`numero` AS `numero`,`a`.`obra` AS `obra`
					from (`mvcerti` `a` join `scli` `b` on((`a`.`cliente` = `b`.`cliente`)))
					order by `a`.`id` desc";
			$this->db->simple_query($mSQL);
		}

		//./ventas/spre.php
		$campos=$this->db->list_fields('spre');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE spre DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE spre ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE spre ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}


		//./ventas/pfaclitemayor.php
		if(!$this->db->field_exists('dxm', 'itpfac')){
			$mSQL="ALTER TABLE `itpfac`
			CHANGE COLUMN `dxapli` `dxapli` VARCHAR(20) NULL COMMENT 'descuento por aplicar' AFTER `id`,
			ADD COLUMN `dxm` DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'descuento por marca' AFTER `dxapli`,
			ADD COLUMN `dxg` DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'descuento por grupo' AFTER `dxm`,
			ADD COLUMN `dxz` DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'descuento por zona' AFTER `dxg`,
			ADD COLUMN `dxc` DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'descuento por cliente' AFTER `dxz`,
			ADD COLUMN `dxe` DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'descuento por escala' AFTER `dxc`,
			ADD COLUMN `dxp` DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'descuento plus' AFTER `dxe`,
			ADD COLUMN `escala` INT(8) UNSIGNED NOT NULL DEFAULT '0' COMMENT 'cantidad para la escala' AFTER `dxe`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('margen', 'zona')){
			$mSQL="ALTER TABLE `zona` ADD COLUMN `margen` DECIMAL(5,2) UNSIGNED NOT NULL DEFAULT '0' AFTER `descrip`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('margen', 'marc')){
			$mSQL="ALTER TABLE `marc` ADD COLUMN `margen` DOUBLE(5,2) UNSIGNED NOT NULL DEFAULT '0' AFTER `marca`";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('escala1', 'sinv')){
			$mSQL="ALTER TABLE `sinv`
			ADD COLUMN `escala1` DECIMAL(12,2) NULL DEFAULT '0.00' AFTER `pmb`,
			ADD COLUMN `pescala1` DECIMAL(5,2) NULL DEFAULT '0.00' COMMENT 'porcentaje descuento escala1' AFTER `escala1`,
			ADD COLUMN `escala2` DECIMAL(12,2) NULL DEFAULT '0.00' AFTER `pescala1`,
			ADD COLUMN `pescala2` DECIMAL(5,2) NULL DEFAULT '0.00' COMMENT 'porcentaje descuento escala2' AFTER `escala2`,
			ADD COLUMN `escala3` DECIMAL(12,2) NULL DEFAULT '0.00' AFTER `pescala2`,
			ADD COLUMN `pescala3` DECIMAL(5,2) NULL DEFAULT '0.00' COMMENT 'porcentaje descuento escala3' AFTER `escala3`";
			$this->db->simple_query($mSQL);

			$mSQL="ALTER TABLE `sinv` ADD COLUMN `mmargenplus` DECIMAL(7,2) NULL DEFAULT '0.00' COMMENT 'Margen al Mayor' AFTER `mmargen`";
			$this->db->simple_query($mSQL);
		}



		//./ventas/snot.php
		$campos=$this->db->list_fields('snot');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE snot DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE snot ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE snot ADD UNIQUE INDEX numero (numero)');
		}


		//./ventas/sfac.php
		$campos = $this->db->list_fields('sfac');

		if(!in_array('ereiva'  ,$campos)){
			$this->db->simple_query("ALTER TABLE sfac ADD ereiva DATE AFTER freiva");
		}
		if(!in_array('entregado'  ,$campos)){
			$this->db->simple_query("ALTER TABLE sfac ADD entregado DATE ");
			$this->db->simple_query("UPDATE sfac SET entregado=fecha");
		}

		if(!in_array('comiadi'  ,$campos)){
			$this->db->simple_query("ALTER TABLE sfac ADD comiadi DECIMAL(10,2) DEFAULT 0 ");
		}

		if(!in_array('upago'  ,$campos)){
			$this->db->query("ALTER TABLE sfac ADD upago INT(10)");
		}

		if(!in_array('manual'  ,$campos)){
			$this->db->query("ALTER TABLE `sfac` ADD COLUMN `manual` CHAR(50) NULL DEFAULT 'N'");
		}


		//./ventas/sfacman.php
		if(!$this->datasis->iscampo('sfac','mandatario')){
			$mSQL="ALTER TABLE sfac ADD COLUMN mandatario VARCHAR(5) NULL DEFAULT NULL COMMENT ''";
			$ban=$this->db->simple_query($mSQL);
		}


		//./ventas/reparto.php
		if (!$this->db->table_exists('reparto')) {
			$mSQL="CREATE TABLE `reparto` (
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  `tipo` char(1) NOT NULL COMMENT 'Tipo Pendiente, Cargado, Despachado, Finalizado, Anulado',
			  `fecha` date DEFAULT NULL COMMENT 'Fecha de Despacho',
			  `retorno` date DEFAULT NULL COMMENT 'Fecha que regresa',
			  `chofer` char(5) DEFAULT NULL COMMENT 'Chofer tabla chofer',
			  `vehiculo` char(10) DEFAULT NULL COMMENT 'Vehiculo => flota',
			  `observa` text,
			  `peso` decimal(10,2) DEFAULT NULL COMMENT 'Peso total',
			  `facturas` int(11) DEFAULT NULL COMMENT 'Nro de Faturas',
			  `estampa` date DEFAULT NULL,
			  `usuario` char(12) DEFAULT NULL,
			  `hora` char(8) DEFAULT NULL,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}

		//./ventas/metas.php
		if(!$this->db->field_exists('pmargen', 'vend')){
			$mSQL="ALTER TABLE `vend` ADD COLUMN `pmargen` DECIMAL(5,2) UNSIGNED NULL DEFAULT '0' AFTER `almacen`";
			$rt=$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('metas')){
			$mSQL="CREATE TABLE `metas` (
				`id` INT(15) UNSIGNED NOT NULL AUTO_INCREMENT,
				`codigo` VARCHAR(15) NOT NULL DEFAULT '',
				`cantidad` DECIMAL(12,3) NULL DEFAULT NULL,
				`peso` DECIMAL(12,3) NULL DEFAULT NULL,
				`fecha` INT(10) NOT NULL DEFAULT '0',
				`tipo` CHAR(1) NOT NULL DEFAULT 'T' COMMENT 'Unidad de medida T=Tonelada',
				PRIMARY KEY (`id`),
				UNIQUE INDEX `codfec` (`fecha`, `codigo`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		//./ventas/rcaj.php

		if(!$this->db->table_exists('rret')){
			$mSQL="CREATE TABLE `rret` (
			`id` int(20) unsigned NOT NULL AUTO_INCREMENT,
			`cierre` varchar(8) DEFAULT NULL,
			`cajero` varchar(5) DEFAULT NULL,
			`tipo` char(2) DEFAULT NULL,
			`monto` decimal(12,2) DEFAULT NULL,
			`fecha` date DEFAULT NULL,
			`estampa` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`),
			KEY `Index 2` (`tipo`,`cajero`)
			) ENGINE=MyISAM COMMENT='Retiros de caja'";

			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('itrcaj')){
			$mSQL="CREATE TABLE `itrcaj` (
				`numero` VARCHAR(8) NOT NULL DEFAULT '' COLLATE 'latin1_swedish_ci',
				`tipo` VARCHAR(15) NOT NULL DEFAULT '' COLLATE 'latin1_swedish_ci',
				`cierre` CHAR(1) NOT NULL DEFAULT 'N' COLLATE 'latin1_swedish_ci',
				`recibido` DECIMAL(17,2) NULL DEFAULT NULL,
				`sistema` DECIMAL(17,2) NULL DEFAULT NULL,
				`diferencia` DECIMAL(17,2) NULL DEFAULT NULL,
				PRIMARY KEY (`numero`, `tipo`, `cierre`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM;";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('rcaj');
		if(!in_array('xventa',$campos)){
			$this->db->query('ALTER TABLE rcaj ADD COLUMN xventa DECIMAL(17,2) NULL DEFAULT 0');
		}

		if(!in_array('xviva',$campos)){
			$this->db->query('ALTER TABLE rcaj ADD COLUMN xviva DECIMAL(17,2) NULL DEFAULT 0');
		}

		if(!in_array('xdevo',$campos)){
			$this->db->query('ALTER TABLE rcaj ADD COLUMN xdevo DECIMAL(17,2) NULL DEFAULT 0');
		}

		if(!in_array('xdiva',$campos)){
			$this->db->query('ALTER TABLE rcaj ADD COLUMN xdiva DECIMAL(17,2) NULL DEFAULT 0');
		}

		if(!in_array('maqfiscal',$campos)){
			$this->db->query('ALTER TABLE rcaj ADD COLUMN maqfiscal VARCHAR(17) NULL ');
		}

		if(!in_array('ultimafc',$campos)){
			$this->db->query('ALTER TABLE rcaj ADD COLUMN ultimafc VARCHAR(10) NULL ');
		}

		if(!in_array('ultimanc',$campos)){
			$this->db->query('ALTER TABLE rcaj ADD COLUMN ultimanc VARCHAR(10) NULL ');
		}


		$itcampos=$this->db->list_fields('itrcaj');
		if(!in_array('cierre',$itcampos)){
			$mSQL="ALTER TABLE `itrcaj`  ADD COLUMN `cierre` CHAR(1) NOT NULL DEFAULT 'N' AFTER `tipo`";
			$this->db->simple_query($mSQL);
		}

		//$mSQL="ALTER TABLE `itrcaj`  DROP PRIMARY KEY,  ADD PRIMARY KEY (`numero`, `tipo`, `cierre`)";
		//$this->db->simple_query($mSQL);

		if($this->db->field_exists('cierre', 'sfpa')){
			$mSQL="ALTER TABLE `sfpa`  ADD COLUMN `cierre` CHAR(8) DEFAULT '' AFTER `hora`";
			$this->db->simple_query($mSQL);
		}

		//$mSQL="ALTER TABLE `rcaj` CHANGE COLUMN `estampa` `estampa` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP";
		//$this->db->simple_query($mSQL);



		//./ventas/scli.php
		$seniat=$this->db->escape('http://contribuyente.seniat.gob.ve/BuscaRif/BuscaRif.jsp');
		$mSQL  ="REPLACE INTO valores (nombre,valor,descrip) VALUES ('CONSULRIF',$seniat,'Pagina de consulta de rif del seniat') ON DUPLICATE KEY UPDATE valor=$seniat";
		$this->db->simple_query($mSQL);

		$campos = array();
		$fields = $this->db->field_data('scli');
		foreach ($fields as $field){
			if    ($field->name=='formap' && $field->type!='int')     $this->db->simple_query('ALTER TABLE `scli`  CHANGE COLUMN `formap` `formap` INT(6) NULL DEFAULT 0');
			elseif($field->name=='email'  && $field->max_length!=100) $this->db->simple_query('ALTER TABLE `scli`  CHANGE COLUMN `email` `email` VARCHAR(100) NULL DEFAULT NULL');
			elseif($field->name=='clave'  && $field->max_length!=50)  $this->db->simple_query('ALTER TABLE `scli`  CHANGE COLUMN `clave` `clave` VARCHAR(50) NULL DEFAULT NULL');
			$campos[]=$field->name;
		}

		if (!in_array('id',$campos)){
			$mSQL='ALTER TABLE `scli` DROP PRIMARY KEY, ADD UNIQUE `cliente` (`cliente`)';
			$this->db->simple_query($mSQL);
			$mSQL='ALTER TABLE `scli` ADD `id` INT AUTO_INCREMENT PRIMARY KEY';
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->table_exists('sclibitalimit')){
			$mSQL="CREATE TABLE `sclibitalimit` (
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				`cliente`    CHAR(5) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
				`credito`    CHAR(1) NULL DEFAULT NULL COLLATE 'latin1_swedish_ci',
				`creditoant` CHAR(1) NULL DEFAULT NULL,
				`limite`     BIGINT(20) NULL DEFAULT NULL,
				`limiteant`  BIGINT(20) NULL DEFAULT NULL,
				`tolera`     DECIMAL(9,2) NULL DEFAULT NULL,
				`toleraant`  DECIMAL(9,2) NULL DEFAULT NULL,
				`maxtol`     DECIMAL(9,2) NULL DEFAULT NULL,
				`maxtolant`  DECIMAL(9,2) NULL DEFAULT NULL,
				`motivo`     TEXT NULL,
				`formap`     DECIMAL(9,0) NULL DEFAULT NULL,
				`formapsant` DECIMAL(9,0) NULL DEFAULT NULL,
				`estampa`    TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
				`usuario`    VARCHAR(12) NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				INDEX `cliente` (`cliente`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			AUTO_INCREMENT=1";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('creditoant', 'sclibitalimit')){
			$mSQL="ALTER TABLE `sclibitalimit`
			ADD COLUMN `creditoant` CHAR(1) NULL DEFAULT NULL AFTER `credito`,
			ADD COLUMN `toleraant` DECIMAL(9,2) NULL DEFAULT NULL AFTER `tolera`,
			ADD COLUMN `maxtolant` DECIMAL(9,2) NULL DEFAULT NULL AFTER `maxtol`,
			ADD COLUMN `formap` INT(6) NULL DEFAULT NULL AFTER `maxtol`,
			ADD COLUMN `formapsant` INT(6) NULL DEFAULT NULL AFTER `formap`";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('modifi'  ,$campos)) $this->db->simple_query("ALTER TABLE `scli` ADD COLUMN `modifi` TIMESTAMP DEFAULT CURRENT_TIMESTAMP NULL AFTER `mensaje`");
		if(!in_array('credito' ,$campos)) $this->db->simple_query("ALTER TABLE `scli` ADD COLUMN `credito` CHAR(1) NOT NULL DEFAULT 'N' AFTER `limite`");
		if(!in_array('sucursal',$campos)) $this->db->simple_query("ALTER TABLE `scli` ADD COLUMN `sucursal` CHAR(2) NULL DEFAULT NULL");
		if(!in_array('mmargen' ,$campos)) $this->db->simple_query("ALTER TABLE `scli` ADD COLUMN `mmargen` DECIMAL(7,2) NULL DEFAULT 0 COMMENT 'Margen al Mayor'");
		if(!in_array('tolera'  ,$campos)) $this->db->simple_query("ALTER TABLE `scli` ADD COLUMN `tolera` DECIMAL(9,2) NULL DEFAULT '0' AFTER `credito`");
		if(!in_array('maxtole' ,$campos)) $this->db->simple_query("ALTER TABLE `scli` ADD COLUMN `maxtole` DECIMAL(9,2) NULL DEFAULT '0' AFTER `tolera`");


		//./ventas/otin.php
		$campos=$this->db->list_fields('otin');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE `otin` DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE `otin` ADD UNIQUE INDEX `numero` (`tipo_doc`, `numero`)');
			$this->db->simple_query('ALTER TABLE `otin` ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		//./ventas/vend.php
		if (!$this->db->table_exists('vend')) {
			$mSQL="CREATE TABLE `vend` (
			  `vendedor` varchar(5) NOT NULL DEFAULT '',
			  `clave` varchar(5) DEFAULT NULL,
			  `nombre` varchar(30) DEFAULT NULL,
			  `direc1` varchar(35) DEFAULT NULL,
			  `direc2` varchar(35) DEFAULT NULL,
			  `telefono` varchar(13) DEFAULT NULL,
			  `comive` decimal(5,2) DEFAULT NULL,
			  `comicob` decimal(5,2) DEFAULT NULL,
			  `recargo` decimal(5,2) DEFAULT NULL,
			  `tipo` char(1) DEFAULT NULL,
			  `almacen` varchar(4) DEFAULT NULL,
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`)
			) ENGINE=MyISAM AUTO_INCREMENT=33 DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('vend');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE vend DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE vend ADD UNIQUE INDEX vendedor (vendedor)');
			$this->db->simple_query('ALTER TABLE vend ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}


		//./ventas/sfacter.php
		if(!$this->datasis->iscampo('sfac','sprv')){
			$mSQL="ALTER TABLE sfac ADD COLUMN sprv VARCHAR(5) NULL DEFAULT NULL COMMENT ''";
			$ban=$this->db->simple_query($mSQL);
		}


		//./formams.php
		if (!$this->db->table_exists('formaesp')) {
			$mSQL="CREATE TABLE `formaesp` (  `nombre` varchar(20) NOT NULL DEFAULT '',  `descrip` varchar(200) DEFAULT NULL,  `word` longtext,  PRIMARY KEY (`nombre`),  UNIQUE KEY `nombre` (`nombre`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='Formatos especiales'";
			$this->db->simple_query($mSQL);
		}


		//./sincro/notifica.php
		if (!$this->db->table_exists('cacatua')) {
			$mSQL="CREATE TABLE `cacatua` (
			`nombre` VARCHAR(50) NULL,
			`indices` VARCHAR(100) NULL,
			`fecha` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (`nombre`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM;";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists('eventos')) {
			$mSQL="CREATE TABLE `eventos` (
			`id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			`nombre` varchar(100) NOT NULL,
			`comenta` varchar(100) NOT NULL COMMENT 'Comentario del evento',
			`fechahora` datetime NOT NULL,
			`activador` text NOT NULL COMMENT 'Funcion a evaluar, si devuelve verdadero se dispara',
			`concurrencia` char(1) NOT NULL COMMENT 'S semanal, D diario, H cada hora,',
			`para` tinytext NOT NULL COMMENT 'a quienes se les notifica',
			`accion` text NOT NULL,
			`disparo` datetime NOT NULL,
			`activo` char(1) NOT NULL DEFAULT 'N',
			`usuario` varbinary(10) NOT NULL,
			`estampa` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`),
			UNIQUE KEY `nombre` (`nombre`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 COMMENT='Tabla que guarda las acciones por eventos'";
			$this->db->simple_query($mSQL);
		}

		if(!$this->db->field_exists('postaccion', 'eventos')){
			$mSQL="ALTER TABLE eventos ADD COLUMN postaccion TEXT NOT NULL AFTER accion";
			$this->db->simple_query($mSQL);
		}


		//./sincro/b2b.php
		if (!$this->db->table_exists('b2b_config')) {
			$mSQL="CREATE TABLE `b2b_config` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`proveed` CHAR(5) NOT NULL COMMENT 'Codigo del proveedor',
				`prefijo` VARCHAR(50) NOT NULL COMMENT 'Prefijo para los codigos de productos',
				`url` VARCHAR(100) NOT NULL,
				`puerto` INT(5) NOT NULL DEFAULT '80',
				`proteo` VARCHAR(20) NOT NULL DEFAULT 'proteoerp',
				`usuario` VARCHAR(100) NOT NULL COMMENT 'Codigo de cliente en el proveedor',
				`clave` VARCHAR(100) NOT NULL,
				`tipo` CHAR(1) NULL DEFAULT NULL COMMENT 'I para inventario G para gasto',
				`depo` VARCHAR(4) NULL DEFAULT NULL COMMENT 'Almacen',
				`margen1` DECIMAL(6,2) NULL DEFAULT NULL COMMENT 'Margen para el precio1',
				`margen2` DECIMAL(6,2) NULL DEFAULT NULL COMMENT 'Margen para el precio 2',
				`margen3` DECIMAL(6,2) NULL DEFAULT NULL COMMENT 'Margen para el precio3',
				`margen4` DECIMAL(6,2) NULL DEFAULT NULL COMMENT 'Margen para el precio4',
				`margen5` DECIMAL(6,2) NULL DEFAULT NULL COMMENT 'Margen para el precio5 (solo supermercado)',
				`grupo` VARCHAR(5) NULL DEFAULT NULL COMMENT 'Grupo por defecto',
				PRIMARY KEY (`id`)
			)
			COMMENT='Configuracion para los b2b'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			AUTO_INCREMENT=1;";
			var_dump($this->db->simple_query($mSQL));
		}

		if (!$this->db->table_exists('b2b_scst')) {
			$mSQL="CREATE TABLE IF NOT EXISTS `b2b_scst` (
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  `fecha` date DEFAULT NULL,
			  `numero` varchar(8) DEFAULT NULL,
			  `proveed` varchar(5) DEFAULT NULL,
			  `nombre` varchar(30) DEFAULT NULL,
			  `depo` varchar(4) DEFAULT NULL,
			  `montotot` decimal(17,2) DEFAULT NULL,
			  `montoiva` decimal(17,2) DEFAULT NULL,
			  `montonet` decimal(17,2) DEFAULT NULL,
			  `vence` date DEFAULT NULL,
			  `tipo_doc` char(2) DEFAULT NULL,
			  `control` varchar(8) NOT NULL DEFAULT '',
			  `peso` decimal(12,2) DEFAULT NULL,
			  `estampa` date DEFAULT NULL,
			  `hora` varchar(8) DEFAULT NULL,
			  `usuario` varchar(12) DEFAULT NULL,
			  `nfiscal` varchar(12) DEFAULT NULL,
			  `exento` decimal(17,2) NOT NULL DEFAULT '0.00',
			  `sobretasa` decimal(17,2) NOT NULL DEFAULT '0.00',
			  `reducida` decimal(17,2) NOT NULL DEFAULT '0.00',
			  `tasa` decimal(17,2) NOT NULL DEFAULT '0.00',
			  `montasa` decimal(17,2) DEFAULT NULL,
			  `monredu` decimal(17,2) DEFAULT NULL,
			  `monadic` decimal(17,2) DEFAULT NULL,
			  `serie` char(12) DEFAULT NULL,
			  `pcontrol` char(8) DEFAULT NULL,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `proveednum` (`proveed`,`numero`),
			  KEY `proveedor` (`proveed`)
			) ENGINE=MyISAM AUTO_INCREMENT=1";
			var_dump($this->db->simple_query($mSQL));
		}

		if (!$this->db->table_exists('b2b_itscst')) {
			$mSQL="CREATE TABLE IF NOT EXISTS `b2b_itscst` (
			  `id_scst` int(11) DEFAULT NULL,
			  `fecha` date DEFAULT NULL,
			  `numero` varchar(8) DEFAULT NULL,
			  `proveed` varchar(5) DEFAULT NULL,
			  `depo` varchar(4) DEFAULT NULL,
			  `codigo` varchar(15) DEFAULT NULL,
			  `descrip` varchar(45) DEFAULT NULL,
			  `cantidad` decimal(10,3) DEFAULT NULL,
			  `devcant` decimal(10,3) DEFAULT NULL,
			  `devfrac` int(4) DEFAULT NULL,
			  `costo` decimal(17,2) DEFAULT NULL,
			  `importe` decimal(17,2) DEFAULT NULL,
			  `iva` decimal(5,2) DEFAULT NULL,
			  `montoiva` decimal(17,2) DEFAULT NULL,
			  `garantia` int(3) DEFAULT NULL,
			  `ultimo` decimal(17,2) DEFAULT NULL,
			  `precio1` decimal(15,2) DEFAULT NULL,
			  `precio2` decimal(15,2) DEFAULT NULL,
			  `precio3` decimal(15,2) DEFAULT NULL,
			  `precio4` decimal(15,2) DEFAULT NULL,
			  `estampa` date DEFAULT NULL,
			  `hora` varchar(8) DEFAULT NULL,
			  `usuario` varchar(12) DEFAULT NULL,
			  `licor` decimal(10,2) DEFAULT '0.00',
			  `barras` varchar(15) DEFAULT NULL,
			  `codigolocal` varchar(15) DEFAULT NULL,
			  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  KEY `id_scst` (`id_scst`),
			  KEY `fecha` (`fecha`),
			  KEY `codigo` (`codigo`),
			  KEY `proveedor` (`proveed`),
			  KEY `numero` (`numero`)
			) ENGINE=MyISAM AUTO_INCREMENT=1";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('scon','origen')){
			$mSQL="ALTER TABLE scon ADD COLUMN origen CHAR(1) NOT NULL DEFAULT 'L' COMMENT 'L= Local, R=Remoto' AFTER peso;";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','reteiva')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN `reteiva` DECIMAL(17,2) NULL DEFAULT '0.00' AFTER `reducida`;";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','cexento')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN  `cexento` decimal(17,2) DEFAULT NULL AFTER `reducida`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','cgenera')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN   `cgenera` decimal(17,2) DEFAULT NULL AFTER `reducida`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','civagen')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN   `civagen` decimal(17,2) DEFAULT NULL AFTER `reducida`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','creduci')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN   `creduci` decimal(17,2) DEFAULT NULL AFTER `reducida`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','civared')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN   `civared` decimal(17,2) DEFAULT NULL AFTER `reducida`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','cadicio')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN   `cadicio` decimal(17,2) DEFAULT NULL AFTER `reducida`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','civaadi')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN   `civaadi` decimal(17,2) DEFAULT NULL AFTER `reducida`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','cstotal')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN   `cstotal` decimal(17,2) DEFAULT NULL AFTER `reducida`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','ctotal')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN   `ctotal` decimal(17,2) DEFAULT NULL AFTER `reducida`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->datasis->iscampo('b2b_scst','cimpuesto')){
			$mSQL="ALTER TABLE `b2b_scst`  ADD COLUMN   `cimpuesto` decimal(17,2) DEFAULT NULL AFTER `reducida`";
			var_dump($this->db->simple_query($mSQL));
		}

		if (!$this->db->table_exists('b2b_scon')) {
			$mSQL="CREATE TABLE IF NOT EXISTS `b2b_scon` (
				`numero` CHAR(8) NULL DEFAULT NULL,
				`fecha` DATE NULL DEFAULT NULL,
				`tipo` CHAR(1) NULL DEFAULT NULL,
				`tipod` CHAR(1) NULL DEFAULT NULL,
				`status` CHAR(1) NULL DEFAULT 'T',
				`asociado` CHAR(8) NULL DEFAULT NULL,
				`clipro` CHAR(5) NULL DEFAULT NULL,
				`almacen` CHAR(4) NULL DEFAULT NULL,
				`nombre` CHAR(40) NULL DEFAULT NULL,
				`direc1` CHAR(40) NULL DEFAULT NULL,
				`direc2` CHAR(40) NULL DEFAULT NULL,
				`observ1` CHAR(33) NULL DEFAULT NULL,
				`observ2` CHAR(33) NULL DEFAULT NULL,
				`stotal` DECIMAL(12,2) NULL DEFAULT NULL,
				`impuesto` DECIMAL(12,2) NULL DEFAULT NULL,
				`gtotal` DECIMAL(12,2) NULL DEFAULT NULL,
				`peso` DECIMAL(10,3) NULL DEFAULT NULL,
				`pid` INT(15) NULL DEFAULT NULL,
				`id` INT(15) UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `numero` (`numero`, `tipo`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
			var_dump($this->db->simple_query($mSQL));
		}

		if (!$this->db->table_exists('b2b_itscon')) {
			$mSQL="CREATE TABLE IF NOT EXISTS `b2b_itscon` (
				`numero` CHAR(8) NULL DEFAULT NULL,
				`codigo` VARCHAR(15) NULL DEFAULT NULL,
				`codigolocal` VARCHAR(15) NULL DEFAULT NULL,
				`desca` VARCHAR(40) NULL DEFAULT NULL,
				`cana` DECIMAL(5,0) NULL DEFAULT NULL,
				`recibido` DECIMAL(5,0) NULL DEFAULT NULL,
				`precio` DECIMAL(12,2) NULL DEFAULT NULL,
				`importe` DECIMAL(12,2) NULL DEFAULT NULL,
				`iva` DECIMAL(8,2) NULL DEFAULT NULL,
				`id_scon` INT(15) UNSIGNED NOT NULL,
				`id` INT(15) UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				INDEX `id_scon` (`id_scon`),
				INDEX `numero_codigo` (`numero`, `codigo`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
			var_dump($this->db->simple_query($mSQL));
		}

		//./compras/sprv.php
		$campos=$this->db->list_fields('sprv');
		if (!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE sprv DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE sprv ADD UNIQUE INDEX proveed (proveed)');
			$this->db->simple_query('ALTER TABLE sprv ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		if(!in_array('copre'    ,$campos)) $this->db->query('ALTER TABLE sprv ADD COLUMN copre     VARCHAR(11)  NULL DEFAULT NULL   AFTER cuenta');
		if(!in_array('ocompra'  ,$campos)) $this->db->query('ALTER TABLE sprv ADD COLUMN ocompra   CHAR(1)      NULL DEFAULT NULL   AFTER copre');
		if(!in_array('dcredito' ,$campos)) $this->db->query('ALTER TABLE sprv ADD COLUMN dcredito  DECIMAL(3,0) NULL DEFAULT "0"    AFTER ocompra');
		if(!in_array('despacho' ,$campos)) $this->db->query('ALTER TABLE sprv ADD COLUMN despacho  DECIMAL(3,0) NULL DEFAULT NULL   AFTER dcredito');
		if(!in_array('visita'   ,$campos)) $this->db->query('ALTER TABLE sprv ADD COLUMN visita    VARCHAR(9)   NULL DEFAULT NULL   AFTER despacho');
		if(!in_array('cate'     ,$campos)) $this->db->query('ALTER TABLE sprv ADD COLUMN cate      VARCHAR(20)  NULL DEFAULT NULL   AFTER visita');
		if(!in_array('reteiva'  ,$campos)) $this->db->query('ALTER TABLE sprv ADD COLUMN reteiva   DECIMAL(7,2) NULL DEFAULT "0.00" AFTER cate');
		if(!in_array('ncorto'   ,$campos)) $this->db->query('ALTER TABLE sprv ADD COLUMN ncorto    VARCHAR(20)  NULL DEFAULT NULL   AFTER nombre');
		if(!in_array('prefpago' ,$campos)) $this->db->query('ALTER TABLE sprv ADD COLUMN prefpago  CHAR(1)      NULL DEFAULT "T"    COMMENT "Preferencia de pago, Transferencia, Deposito, Caja" AFTER reteiva');
		if(!in_array('canticipo',$campos)) $this->db->query("ALTER TABLE sprv ADD COLUMN canticipo VARCHAR(15)  NULL DEFAULT NULL   COMMENT 'Cuenta contable de Anticipo'                        AFTER cuenta");

		$this->db->simple_query('ALTER TABLE sprv CHANGE nomfis nomfis VARCHAR(200) DEFAULT NULL NULL');
		$this->db->simple_query('ALTER TABLE sprv CHANGE COLUMN telefono telefono TEXT NULL DEFAULT NULL');


		//./compras/grpr.php
		//if (!$this->db->table_exists('grpr')) {
		//	$mSQL="CREATE TABLE `grpr` (
		//	  `grupo` varchar(4) NOT NULL DEFAULT '',
		//	  `gr_desc` varchar(25) DEFAULT NULL,
		//	  `cuenta` varchar(15) DEFAULT NULL,
		//	  `id` int(11) NOT NULL AUTO_INCREMENT,
		//	  PRIMARY KEY (`id`),
		//	  UNIQUE KEY `grupo` (`grupo`)
		//	) ENGINE=MyISAM AUTO_INCREMENT=12 DEFAULT CHARSET=latin1";
		//	$this->db->simple_query($mSQL);
		//}

		$campos=$this->db->list_fields('grpr');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE grpr DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE grpr ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE grpr ADD UNIQUE INDEX grupo (grupo)');
		}

		//./compras/scst.php
		if (!$this->db->table_exists('sinvehiculo')) {
			$mSQL="CREATE TABLE `sinvehiculo` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`id_sfac` INT(10) NULL DEFAULT NULL,
				`id_scst` INT(10) NULL DEFAULT NULL,
				`codigo_sinv` VARCHAR(15) NULL DEFAULT NULL,
				`modelo` VARCHAR(50) NULL DEFAULT NULL,
				`color` VARCHAR(50) NULL DEFAULT NULL,
				`motor` VARCHAR(50) NULL DEFAULT NULL,
				`carroceria` VARCHAR(50) NULL DEFAULT NULL,
				`uso` VARCHAR(50) NULL DEFAULT NULL,
				`tipo` VARCHAR(50) NULL DEFAULT NULL,
				`clase` VARCHAR(50) NULL DEFAULT NULL,
				`anio` VARCHAR(50) NULL DEFAULT NULL,
				`peso` DECIMAL(10,2) NULL DEFAULT '0.00',
				`transmision` VARCHAR(50) NULL DEFAULT NULL,
				`placa` VARCHAR(10) NULL DEFAULT NULL,
				`precioplaca` DECIMAL(10,2) NULL DEFAULT NULL,
				`tasa` DECIMAL(10,2) NULL DEFAULT NULL,
				`nombre` VARCHAR(200) NULL DEFAULT NULL,
				`casa` VARCHAR(100) NULL DEFAULT NULL,
				`calle` VARCHAR(100) NULL DEFAULT NULL,
				`urb` VARCHAR(100) NULL DEFAULT NULL,
				`ciudad` VARCHAR(100) NULL DEFAULT NULL,
				`municipio` VARCHAR(100) NULL DEFAULT NULL,
				`estado` VARCHAR(100) NULL DEFAULT NULL,
				`cpostal` VARCHAR(10) NULL DEFAULT NULL,
				`ctelefono1` VARCHAR(4) NULL DEFAULT NULL,
				`telefono1` VARCHAR(8) NULL DEFAULT NULL,
				`ctelefono2` VARCHAR(4) NULL DEFAULT NULL,
				`telefono2` VARCHAR(8) NULL DEFAULT NULL,
				`distrito` VARCHAR(100) NULL DEFAULT NULL,
				`aseguradora` VARCHAR(200) NULL DEFAULT NULL,
				`vence` DATE NULL DEFAULT NULL,
				`nomban` VARCHAR(200) NULL DEFAULT NULL,
				`banrif` VARCHAR(20) NULL DEFAULT NULL,
				`representante` VARCHAR(100) NULL DEFAULT NULL,
				`concesionario` VARCHAR(100) NULL DEFAULT NULL,
				`concesionariorif` VARCHAR(20) NULL DEFAULT NULL,
				`poliza` VARCHAR(50) NULL DEFAULT NULL,
				`neumaticos` INT(11) NULL DEFAULT NULL,
				`tipo_neumatico` VARCHAR(50) NULL DEFAULT NULL,
				`distanciaeje` FLOAT NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
			)
			COMMENT='Vehiculos a la venta'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}

		if ( !$this->datasis->iscampo('scst','id') ) {
			$this->db->simple_query('ALTER TABLE scst DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE scst ADD UNIQUE INDEX control (control)');
			$this->db->simple_query('ALTER TABLE scst ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');

			$this->db->simple_query("update tmenus set secu=1 where titulo='Incluye'");
			$this->db->simple_query("update tmenus set secu=2 where titulo='Modifica'");
			$this->db->simple_query("update tmenus set secu=3 where titulo='Prox'");
			$this->db->simple_query("update tmenus set secu=4 where titulo='Ante'");
			$this->db->simple_query("update tmenus set secu=5 where titulo='Elimina'");
			$this->db->simple_query("update tmenus set secu=6 where titulo='Busca'");
			$this->db->simple_query("update tmenus set secu=7 where titulo='Tabla'");
			$this->db->simple_query("update tmenus set secu=8 where titulo='Lista'");
			$this->db->simple_query("update tmenus set secu=9 where titulo='Otros'");
		};

		//./supervisor/usuarios.php
		$campos=$this->db->list_fields('usuario');
		if(!in_array('almacen',$campos)){
			$mSQL="ALTER TABLE `usuario`  ADD COLUMN `almacen` CHAR(4) NULL";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('sucursal',$campos)){
			$mSQL="ALTER TABLE `usuario`  ADD COLUMN `sucursal` CHAR(2) NULL";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('activo',$campos)){
			$mSQL="ALTER TABLE `usuario`  ADD COLUMN `activo` CHAR(1) NULL";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE usuario DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE usuario ADD UNIQUE INDEX codigo (codigo)');
			$this->db->simple_query('ALTER TABLE usuario ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};

		if(!in_array('uuid',$campos)){
			$this->db->simple_query("ALTER TABLE `usuario` ADD COLUMN `uuid` VARCHAR(100) NULL DEFAULT NULL COMMENT 'Dispositivo movil para pedidos' AFTER `activo`");
		}
		$this->db->simple_query('DELETE FROM sida USING sida LEFT JOIN usuario ON sida.usuario=usuario.us_codigo WHERE usuario.us_codigo IS NULL');


		//./supervisor/formatos.php
		if ($this->db->table_exists('intrarepo')){
			$mSQL="CREATE TABLE IF NOT EXISTS `intrarepo` (
			`nombre` varchar(71) NOT NULL default '',
			`modulo` varchar(10) NOT NULL default '',
			`titulo` varchar(20) default NULL,
			`mensaje` varchar(60) default NULL,
			`activo` char(1) default 'S',
			PRIMARY KEY  (`nombre`,`modulo`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
		if ($this->db->field_exists('tcpdf2', 'formatos')) $this->db->simple_query("ALTER TABLE `formatos`  ADD COLUMN `tcpdf2` TEXT NULL");


		//./supervisor/sitemslog.php
		if(!$this->db->table_exists('sitemsest')){
			$mSQL="CREATE TABLE `sitemsest` (
			`id` INT(10) NOT NULL AUTO_INCREMENT,
			`usuario` VARCHAR(50) NOT NULL DEFAULT '0',
			`periodo` VARCHAR(6) NOT NULL DEFAULT '0',
			`agrega` INT(10) NOT NULL DEFAULT '0',
			`eliminado` INT(10) NOT NULL DEFAULT '0',
			`modifica` INT(10) NOT NULL DEFAULT '0',
			`facturado` INT(10) NOT NULL DEFAULT '0',
			PRIMARY KEY (`id`),
			UNIQUE INDEX `usuario_periodo` (`usuario`, `periodo`)
			)
			COMMENT='Resumen de sitems log'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			AUTO_INCREMENT=1";
			$this->db->simple_query($mSQL);
		}

		//./supervisor/ejecutasql.php
		if (!$this->db->table_exists('ejecutasql')) {
			$mSQL="CREATE TABLE `ejecutasql` (
			  `id` int(10) NOT NULL AUTO_INCREMENT,
			  `usuario` varchar(12) DEFAULT NULL,
			  `tipo` varchar(1) DEFAULT NULL COMMENT 'Restringido, Abierto',
			  `nombre` varchar(100) DEFAULT NULL COMMENT 'Restringido, Abierto',
			  `script` text COMMENT 'Restringido, Abierto',
			  PRIMARY KEY (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Escripts para ejecutar por usuario'";
			$this->db->simple_query($mSQL);
		}

		//./supervisor/sucu.php
		//if (!$this->db->table_exists('sucu')) {
		//	$mSQL="CREATE TABLE `sucu` (
		//	  `codigo` char(2) NOT NULL DEFAULT '',
		//	  `sucursal` varchar(30) DEFAULT NULL,
		//	  `url` varchar(200) DEFAULT NULL,
		//	  `prefijo` char(1) DEFAULT NULL,
		//	  `proteo` varchar(50) DEFAULT NULL,
		//	  `serie` char(1) DEFAULT NULL,
		//	  `odbc` varchar(100) DEFAULT NULL,
		//	  `usuario` char(20) DEFAULT NULL,
		//	  `clave` char(15) DEFAULT NULL,
		//	  `puerto` char(6) DEFAULT NULL,
		//	  `DB` varchar(100) DEFAULT NULL,
		//	  PRIMARY KEY (`codigo`)
		//	) ENGINE=MyISAM DEFAULT CHARSET=latin1";
		//	$this->db->simple_query($mSQL);
		//}
		$campos=$this->db->list_fields('sucu');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE sucu DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE sucu ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE sucu ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		$campos=$this->db->list_fields('sucu');
		if(!in_array('url',$campos)){
			$mSQL="ALTER TABLE `sucu` ADD `url` VARCHAR(200) NULL";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('prefijo',$campos)){
			$mSQL="ALTER TABLE `sucu` ADD `prefijo` VARCHAR(3) NULL";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('proteo',$campos)){
			$mSQL="ALTER TABLE `sucu` ADD `proteo` VARCHAR(50) NULL";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('codigo',$campos)){
			$mSQL="ALTER TABLE `sucu` ADD PRIMARY KEY (`codigo`)";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('db_nombre',$campos)){
			$mSQL="ALTER TABLE `sucu`  ADD COLUMN `db_nombre` VARCHAR(50) NULL DEFAULT NULL AFTER `proteo`";
			$this->db->simple_query($mSQL);
		}


		//./contabilidad/cpla.php
		$campos=$this->db->list_fields('cpla');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE cpla DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE cpla ADD UNIQUE INDEX codigo (codigo)');
			$this->db->simple_query('ALTER TABLE cpla ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}


		//./contabilidad/casi.php
		$campos=$this->db->list_fields('casi');
		if(!in_array('id',$campos)){
			$mSQL='ALTER TABLE `casi` DROP PRIMARY KEY, ADD UNIQUE `comprob` (`comprob`)';
			$this->db->simple_query($mSQL);
			$mSQL='ALTER TABLE casi ADD id INT AUTO_INCREMENT PRIMARY KEY';
			$this->db->simple_query($mSQL);
		}

		$campos=$this->db->list_fields('itcasi');
		if(!in_array('idcasi',$campos)){
			$mSQL='ALTER TABLE itcasi ADD idcasi INT(11)';
			$this->db->simple_query($mSQL);
			$mSQL='ALTER TABLE itcasi ADD INDEX idcasi (idcasi)';
			$this->db->simple_query($mSQL);
			$mSQL = "UPDATE itcasi a JOIN casi b ON a.comprob=b.comprob SET a.idcasi=b.id";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('idcasi',$campos)){
			$mSQL="ALTER TABLE `itcasi` ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT, ADD PRIMARY KEY (`id`)";
			$this->db->simple_query($mSQL);
		}

		//./finanzas/mgas.php
		$campos=$this->db->list_fields('mgas');
		if (!in_array('id',$campos)){
			$mSQL="ALTER TABLE `mgas`
			ADD COLUMN `id` INT(11) NOT NULL AUTO_INCREMENT,
			DROP PRIMARY KEY,
			ADD UNIQUE INDEX `unico` (`codigo`),
			ADD PRIMARY KEY (`id`);";
			$this->db->simple_query($mSQL);
		}

		if (!in_array('reten',$campos)) {
			$mSQL="ALTER TABLE mgas ADD COLUMN reten VARCHAR(4) NULL DEFAULT NULL AFTER rica, ADD COLUMN retej VARCHAR(4) NULL DEFAULT NULL AFTER reten";
			$this->db->simple_query($mSQL);
		}


		//./finanzas/cruc.php
		if (!$this->db->table_exists('cruc')) {
			$mSQL="CREATE TABLE `cruc` (
			  `numero`   varchar(8) NOT NULL DEFAULT '',
			  `fecha`    date          DEFAULT NULL,
			  `tipo`     char(3)       DEFAULT NULL,
			  `proveed`  varchar(5)    DEFAULT NULL,
			  `nombre`   varchar(40)   DEFAULT NULL,
			  `saldoa`   decimal(16,2) DEFAULT NULL,
			  `cliente`  varchar(5)    DEFAULT NULL,
			  `nomcli`   varchar(40)   DEFAULT NULL,
			  `saldod`   decimal(16,2) DEFAULT NULL,
			  `monto`    decimal(16,2) DEFAULT NULL,
			  `concept1` varchar(40)   DEFAULT NULL,
			  `concept2` varchar(40)   DEFAULT NULL,
			  `transac`  varchar(8)    DEFAULT NULL,
			  `estampa`  date          DEFAULT NULL,
			  `hora`     varchar(8)    DEFAULT NULL,
			  `usuario`  varchar(12)   DEFAULT NULL,
			  `id`       int(11) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `numero` (`numero`),
			  KEY `transaccion` (`transac`),
			  KEY `fecha` (`fecha`)
			) ENGINE=MyISAM AUTO_INCREMENT=437 DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}


		//./finanzas/banc.php
		$campos=$this->db->list_fields('banc');

		if(!in_array('id',$campos)) {
			$this->db->simple_query('ALTER TABLE banc DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE banc ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE banc ADD UNIQUE INDEX codbanc (codbanc)');
		}


		//./finanzas/rete.php
		$campos=$this->db->list_fields('rete');

		if(!in_array('concepto',$campos)){
			$this->db->simple_query('ALTER TABLE rete ADD COLUMN concepto VARCHAR(10) NULL ');
		}

		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE rete DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE rete ADD UNIQUE INDEX codigo (codigo)');
			$this->db->simple_query('ALTER TABLE rete ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		if(!in_array('ut',$campos)){
			$mSQL="ALTER TABLE ADD COLUMN ut DECIMAL(12,2) NULL DEFAULT NULL";
			$this->db->simple_query($mSQL);
		}

		//./finanzas/apan.php
		if(!$this->datasis->iscampo('apan','id')){
			$this->db->simple_query('ALTER TABLE apan DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE apan ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE apan ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};


		//./finanzas/provoca.php
		$campos=$this->db->list_fields('provoca');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE provoca DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE provoca DROP INDEX rif');
			$this->db->simple_query('ALTER TABLE provoca ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE provoca ADD UNIQUE INDEX rif (rif)');
		}
		$this->db->simple_query('UPDATE provoca SET rif=TRIM(rif)');


		//./finanzas/bcaj.php
		$campos=$this->db->list_fields('bcaj');

		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE bcaj DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE bcaj ADD UNIQUE INDEX numero (numero)');
			$this->db->simple_query('ALTER TABLE bcaj ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		if (!in_array('codbanc',$campos)){
			$this->db->query('ALTER TABLE bcaj ADD COLUMN codbanc CHAR(2) NULL DEFAULT NULL ');
		};

		$campos=$this->db->list_fields('sfpa');
		if(!in_array('deposito',$campos)){
			$this->db->query('ALTER TABLE sfpa ADD COLUMN deposito CHAR(12) NULL DEFAULT NULL');
		}



		//./finanzas/grga.php
		$campos=$this->db->list_fields('grga');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE grga DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE grga ADD UNIQUE INDEX grupo (grupo)');
			$this->db->simple_query('ALTER TABLE grga ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}


		//./finanzas/riva.php
		$campos=$this->db->list_fields('riva');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE riva DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE riva ADD UNIQUE INDEX comprob (nrocomp)');
			$this->db->simple_query('ALTER TABLE riva ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}


		//./finanzas/sprm.php
		$campos=$this->db->list_fields('sprm');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE sprm DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE sprm ADD UNIQUE INDEX `unico` (`cod_prv`, `tipo_doc`, `numero`)');
			$this->db->simple_query('ALTER TABLE sprm ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		if(!in_array('tbanco',$campos)){
			$this->db->simple_query('ALTER TABLE sprm ADD COLUMN `tbanco` CHAR(3)');
		}


		//./finanzas/invresu.php
		if(!$this->datasis->iscampo('invresu','id') ) {
			$this->db->simple_query('ALTER TABLE invresu DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE `invresu` ADD UNIQUE INDEX `mes_codigo` (`mes`, `codigo`);');
			$this->db->simple_query('ALTER TABLE invresu ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		};

		if(!$this->datasis->iscampo('invresu','conver') ) {
			$this->db->simple_query('ALTER TABLE invresu ADD COLUMN conver  DECIMAL(20,3) NULL DEFAULT "0.00" COMMENT "CONVERSIONES" AFTER compras');
		};

		if(!$this->datasis->iscampo('invresu','mconver') ) {
			$this->db->simple_query('ALTER TABLE invresu ADD COLUMN mconver DECIMAL(20,3) NULL DEFAULT "0.00" COMMENT "CONVERSIONES" AFTER mcompras');
		};

		if(!$this->datasis->iscampo('invresu','ajuste') ) {
			$this->db->simple_query('ALTER TABLE invresu ADD COLUMN ajuste  DECIMAL(20,3) NULL DEFAULT "0.00" COMMENT "AJUSTES DE INVENTARIO" AFTER trans');
		};

		if(!$this->datasis->iscampo('invresu','majuste') ) {
			$this->db->simple_query('ALTER TABLE invresu ADD COLUMN majuste DECIMAL(20,3) NULL DEFAULT "0.00" COMMENT "AJUSTES DE INVENTARIO" AFTER mtrans ');
		};

		//./finanzas/gser.php
		$query='SHOW INDEX FROM gser';
		$resul=$this->db->query($query);
		$existe=0;
		foreach($resul->result() as $ind){
			$nom= $ind->Column_name;
			if ($nom == 'id'){
				$existe=1;
				break;
			}
		}

		if($existe != 1) {
			$query="ALTER TABLE `gser` DROP PRIMARY KEY";
			var_dump($this->db->simple_query($query));
			$query="ALTER TABLE `gser` ADD UNIQUE INDEX `gser` (`fecha`, `numero`, `proveed`)";
			var_dump($this->db->simple_query($query));
			$query="ALTER TABLE `gser` ADD COLUMN `id` INT(15) UNSIGNED NULL AUTO_INCREMENT AFTER `ncausado`,  ADD PRIMARY KEY (`id`)";
			var_dump($this->db->simple_query($query));
			$query="ALTER TABLE `gitser` ADD COLUMN `id` INT(15) UNSIGNED NULL AUTO_INCREMENT AFTER `reteica`,  ADD PRIMARY KEY (`id`);";
			$this->db->simple_query($query);
			$query="ALTER TABLE `gitser` ADD COLUMN `idgser` INT(15) UNSIGNED NOT NULL DEFAULT '0' AFTER `id`, ADD INDEX `idgser` (`idgser`)";
			$this->db->simple_query($query);

			$query="UPDATE gitser AS a
				JOIN gser AS b on a.numero=b.numero and a.fecha = b.fecha and a.proveed = b.proveed
				SET a.idgser=b.id  WHERE a.idgser IS NULL";
			$this->db->simple_query($query);
		}

		$query="UPDATE gitser AS a
			JOIN gser AS b on a.numero=b.numero AND a.fecha = b.fecha AND a.proveed = b.proveed
			SET a.idgser=b.id WHERE a.idgser IS NULL";
		$this->db->simple_query($query);

		if (!$this->db->table_exists('gereten')) {
			$mSQL="CREATE TABLE `gereten` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`idd` INT(11) NULL DEFAULT NULL,
				`origen` CHAR(4) NULL DEFAULT NULL,
				`numero` VARCHAR(25) NULL DEFAULT NULL,
				`codigorete` VARCHAR(4) NULL DEFAULT NULL,
				`actividad` VARCHAR(45) NULL DEFAULT NULL,
				`base` DECIMAL(10,2) NULL DEFAULT NULL,
				`porcen` DECIMAL(5,2) NULL DEFAULT NULL,
				`monto` DECIMAL(10,2) NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($query);
		}

		if (!$this->db->table_exists('gserchi')) {
			$query="CREATE TABLE IF NOT EXISTS `gserchi` (
				`codbanc` varchar(5) NOT NULL DEFAULT '',
				`fechafac` date DEFAULT NULL,
				`numfac` varchar(8) DEFAULT NULL,
				`nfiscal` varchar(12) DEFAULT NULL,
				`rif` varchar(13) DEFAULT NULL,
				`proveedor` varchar(40) DEFAULT NULL,
				`codigo` varchar(6) DEFAULT NULL,
				`descrip` varchar(50) DEFAULT NULL,
				`moneda` char(2) DEFAULT NULL,
				`montasa` decimal(17,2) DEFAULT '0.00',
				`tasa` decimal(17,2) DEFAULT NULL,
				`monredu` decimal(17,2) DEFAULT '0.00',
				`reducida` decimal(17,2) DEFAULT NULL,
				`monadic` decimal(17,2) DEFAULT '0.00',
				`sobretasa` decimal(17,2) DEFAULT NULL,
				`exento` decimal(17,2) DEFAULT '0.00',
				`importe` decimal(12,2) DEFAULT NULL,
				`sucursal` char(2) DEFAULT NULL,
				`departa` char(2) DEFAULT NULL,
				`usuario` varchar(12) DEFAULT NULL,
				`estampa` date DEFAULT NULL,
				`hora` varchar(8) DEFAULT NULL,
				`id` int(11) unsigned NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`)
				) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC";
			$this->db->simple_query($query);
		}

		if (!$this->db->table_exists('rica')) {
			$query="CREATE TABLE `rica` (
				`codigo` CHAR(5)    NOT  NULL,
				`activi` CHAR(14)   NULL DEFAULT NULL,
				`aplica` CHAR(100)  NULL DEFAULT NULL,
				`tasa` DECIMAL(8,2) NULL DEFAULT NULL,
				PRIMARY KEY (`codigo`)
				) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC";
			$this->db->simple_query($query);
		}

		if (!$this->db->field_exists('ngasto','gserchi')) {
			$query="ALTER TABLE `gserchi` ADD COLUMN `ngasto` VARCHAR(8) NULL DEFAULT NULL AFTER `departa`";
			$this->db->simple_query($query);
		}

		if (!$this->db->field_exists('aceptado','gserchi')) {
			$query="ALTER TABLE gserchi ADD COLUMN aceptado CHAR(1) NULL DEFAULT NULL";
			$this->db->simple_query($query);
		}

		//./finanzas/prmo.php
		if (!$this->db->table_exists('prmo')) {
			$mSQL="CREATE TABLE `prmo` (
			  `tipop` char(1) DEFAULT NULL,
			  `numero` varchar(8) NOT NULL DEFAULT '',
			  `fecha` date DEFAULT NULL,
			  `codban` char(2) DEFAULT NULL,
			  `tipo` char(2) DEFAULT NULL,
			  `numche` varchar(12) DEFAULT NULL,
			  `benefi` varchar(30) DEFAULT NULL,
			  `comprob` varchar(6) DEFAULT NULL,
			  `clipro` varchar(5) DEFAULT NULL,
			  `nombre` varchar(30) DEFAULT NULL,
			  `docum` varchar(12) DEFAULT NULL,
			  `banco` varchar(10) DEFAULT NULL,
			  `monto` decimal(13,2) DEFAULT NULL,
			  `cuotas` int(2) DEFAULT NULL,
			  `vence` date DEFAULT NULL,
			  `observa1` varchar(50) DEFAULT NULL,
			  `observa2` varchar(50) DEFAULT NULL,
			  `transac` varchar(8) DEFAULT NULL,
			  `estampa` date DEFAULT NULL,
			  `hora` varchar(8) DEFAULT NULL,
			  `usuario` varchar(12) DEFAULT NULL,
			  `cadano` int(6) DEFAULT NULL,
			  `apartir` int(6) DEFAULT NULL,
			  `negreso` char(8) DEFAULT NULL,
			  `ningreso` char(8) DEFAULT NULL,
			  `retencion` char(14) DEFAULT NULL,
			  `factura` char(12) DEFAULT NULL,
			  `remision` date DEFAULT NULL,
			  PRIMARY KEY (`numero`),
			  KEY `transaccion` (`transac`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('prmo');
		//if(!in_array('<#campo#>',$campos)){ }


		//./finanzas/rivc.php
		if (!$this->db->table_exists('rivc')) {
			$mSQL="CREATE TABLE `rivc` (
			`id` int(6) NOT NULL AUTO_INCREMENT,
			`nrocomp` varchar(8) NOT NULL DEFAULT '',
			`emision` date DEFAULT NULL,
			`periodo` char(8) DEFAULT NULL,
			`fecha` date DEFAULT NULL,
			`cod_cli` varchar(5) DEFAULT NULL,
			`nombre` varchar(200) DEFAULT NULL,
			`rif` varchar(14) DEFAULT NULL,
			`exento` decimal(15,2) DEFAULT NULL,
			`tasa` decimal(5,2) DEFAULT NULL,
			`general` decimal(15,2) DEFAULT NULL,
			`geneimpu` decimal(15,2) DEFAULT NULL,
			`tasaadic` decimal(5,2) DEFAULT NULL,
			`adicional` decimal(15,2) DEFAULT NULL,
			`adicimpu` decimal(15,2) DEFAULT NULL,
			`tasaredu` decimal(5,2) DEFAULT NULL,
			`reducida` decimal(15,2) DEFAULT NULL,
			`reduimpu` decimal(15,2) DEFAULT NULL,
			`stotal` decimal(15,2) DEFAULT NULL,
			`impuesto` decimal(15,2) DEFAULT NULL,
			`gtotal` decimal(15,2) DEFAULT NULL,
			`reiva` decimal(15,2) DEFAULT NULL,
			`estampa` date DEFAULT NULL,
			`hora` char(8) DEFAULT NULL,
			`usuario` varchar(12) DEFAULT NULL,
			`modificado` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			`transac` varchar(8) DEFAULT NULL,
			`origen` char(1) DEFAULT NULL,
			`codbanc` char(2) DEFAULT NULL,
			`tipo_op` char(2) DEFAULT NULL,
			`numche` varchar(12) DEFAULT NULL,
			`sprmreinte` varchar(8) DEFAULT NULL,
			PRIMARY KEY (`id`),
			KEY `modificado` (`modificado`),
			KEY `nrocomp_cod_cli` (`nrocomp`,`cod_cli`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->field_exists('operacion', 'rivc')){
			$mSQL="ALTER TABLE rivc ADD COLUMN operacion CHAR(1) NOT NULL AFTER sprmreinte";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->field_exists('anulado', 'rivc')){
			$mSQL="ALTER TABLE `rivc`  ADD COLUMN `anulado` CHAR(1) NULL DEFAULT 'N' AFTER `operacion`";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->field_exists('cajero', 'rivc')){
			$mSQL="ALTER TABLE rivc ADD COLUMN cajero VARCHAR(5) NULL DEFAULT NULL AFTER sprmreinte";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists('itrivc')) {
			$mSQL="CREATE TABLE `itrivc` (
			`id` int(6) NOT NULL AUTO_INCREMENT,
			`idrivc` int(6) DEFAULT NULL,
			`tipo_doc` char(2) DEFAULT NULL,
			`fecha` date DEFAULT NULL,
			`numero` varchar(8) DEFAULT NULL,
			`nfiscal` char(12) DEFAULT NULL,
			`exento` decimal(15,2) DEFAULT NULL,
			`tasa` decimal(5,2) DEFAULT NULL,
			`general` decimal(15,2) DEFAULT NULL,
			`geneimpu` decimal(15,2) DEFAULT NULL,
			`tasaadic` decimal(5,2) DEFAULT NULL,
			`adicional` decimal(15,2) DEFAULT NULL,
			`adicimpu` decimal(15,2) DEFAULT NULL,
			`tasaredu` decimal(5,2) DEFAULT NULL,
			`reducida` decimal(15,2) DEFAULT NULL,
			`reduimpu` decimal(15,2) DEFAULT NULL,
			`stotal` decimal(15,2) DEFAULT NULL,
			`impuesto` decimal(15,2) DEFAULT NULL,
			`gtotal` decimal(15,2) DEFAULT NULL,
			`reiva` decimal(15,2) DEFAULT NULL,
			`transac` char(8) DEFAULT NULL,
			`estampa` date DEFAULT NULL,
			`hora` char(8) DEFAULT NULL,
			`usuario` char(12) DEFAULT NULL,
			`ffactura` date DEFAULT '0000-00-00',
			`modificado` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
			PRIMARY KEY (`id`),
			KEY `tipo_doc_numero` (`tipo_doc`,`numero`),
			KEY `Numero` (`numero`),
			KEY `modificado` (`modificado`),
			KEY `rivatra` (`transac`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 ROW_FORMAT=FIXED";
			$this->db->simple_query($mSQL);
		}


		//./finanzas/bconci.php
		if (!$this->db->table_exists('bconci')) {
			$mSQL="CREATE TABLE `bconci` (
				`fecha` DATE NULL DEFAULT NULL,
				`codbanc` CHAR(2) NULL DEFAULT NULL,
				`numcuent` VARCHAR(18) NULL DEFAULT NULL,
				`banco` VARCHAR(30) NULL DEFAULT NULL,
				`saldoi` DECIMAL(18,2) NULL DEFAULT NULL,
				`saldof` DECIMAL(18,2) NULL DEFAULT NULL,
				`deposito` DECIMAL(18,2) NULL DEFAULT NULL,
				`credito` DECIMAL(18,2) NULL DEFAULT NULL,
				`cheque` DECIMAL(18,2) NULL DEFAULT NULL,
				`debito` DECIMAL(18,2) NULL DEFAULT NULL,
				`status` CHAR(1) NULL DEFAULT NULL,
				`usuario` VARCHAR(4) NULL DEFAULT NULL,
				`estampa` DATE NULL DEFAULT NULL,
				`hora` VARCHAR(8) NULL DEFAULT NULL,
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				INDEX `fecha` (`fecha`),
				UNIQUE INDEX `fecha_codbanc` (`fecha`, `codbanc`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('bconci');
		//if(!in_array('<#campo#>',$campos)){ }


		//./finanzas/tban.php
		$campos=$this->db->list_fields('tban');

		if(!in_array('formato',$campos)){
			$mSQL="ALTER TABLE `tban` ADD COLUMN `formato` VARCHAR(50) NULL DEFAULT NULL COMMENT 'Formato de cheque';";
			$this->db->simple_query($mSQL);
			$mSQL="UPDATE tban SET formato=CONCAT('CHEQUE',cod_banc) WHERE formato IS NULL";
			$this->db->simple_query($mSQL);
		}

		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE tban DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE tban ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE tban ADD UNIQUE INDEX cod_banc (cod_banc)');
		}


		//./finanzas/smov.php
		$campos=$this->db->list_fields('smov');
		if (!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE smov DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE `otin` ADD UNIQUE INDEX `unico` (`cod_cli`, `tipo_doc`, `numero`, `fecha`)');
			$this->db->simple_query('ALTER TABLE smov ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}



		//./finanzas/ords.php
		$campos=$this->db->list_fields('ords');
		if(!in_array('id',$campos)){
			$this->db->simple_query('ALTER TABLE ords DROP PRIMARY KEY');
			$this->db->simple_query('ALTER TABLE ords ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id) ');
			$this->db->simple_query('ALTER TABLE ords ADD UNIQUE INDEX numero (numero)');
		}


		//./import/aran.php
		if (!$this->db->table_exists('aran')) {
			$mSQL="CREATE TABLE `aran` (
			  `codigo` varchar(15) NOT NULL DEFAULT '',
			  `descrip` text,
			  `tarifa` decimal(8,2) DEFAULT '0.00',
			  `unidad` varchar(20) DEFAULT NULL,
			  `dolar` decimal(8,2) DEFAULT NULL,
			  `id` int(11) NOT NULL AUTO_INCREMENT,
			  PRIMARY KEY (`id`),
			  UNIQUE KEY `codigo` (`codigo`)
			) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";
			$this->db->simple_query($mSQL);
		}
		//$campos=$this->db->list_fields('aran');
		//if(!in_array('<#campo#>',$campos)){ }


		//./import/ordi.php
		if(!$this->db->field_exists('ordeni', 'gser')){
			$mSQL='ALTER TABLE `gser`  ADD COLUMN `ordeni` INT(15) UNSIGNED NULL DEFAULT NULL AFTER `compra`';
			var_dump($this->db->simple_query($mSQL));
		}

		if (!$this->db->table_exists('itordi')) {
			$mSQL="CREATE TABLE `itordi` (
				`numero` INT(15) UNSIGNED NOT NULL,
				`fecha` DATE NULL DEFAULT NULL,
				`codigo` CHAR(15) NULL DEFAULT NULL,
				`descrip` CHAR(45) NULL DEFAULT NULL,
				`cantidad` DECIMAL(10,3) NULL DEFAULT NULL,
				`costofob` DECIMAL(17,2) NULL DEFAULT NULL,
				`importefob` DECIMAL(17,2) NULL DEFAULT NULL,
				`gastosi` DECIMAL(17,2) NULL DEFAULT NULL,
				`costocif` DECIMAL(17,2) NULL DEFAULT NULL,
				`importecif` DECIMAL(19,4) NULL DEFAULT NULL,
				`importeciflocal` DECIMAL(17,2) NULL DEFAULT NULL COMMENT 'importe cif en moneda local',
				`codaran` CHAR(15) NULL DEFAULT NULL,
				`arancel` DECIMAL(7,2) NULL DEFAULT NULL,
				`montoaran` DECIMAL(17,2) NULL DEFAULT NULL,
				`gastosn` DECIMAL(17,2) NULL DEFAULT NULL,
				`costofinal` DECIMAL(17,2) NULL DEFAULT NULL,
				`importefinal` DECIMAL(17,2) NULL DEFAULT NULL,
				`participam` DOUBLE NULL DEFAULT NULL,
				`participao` DOUBLE NULL DEFAULT NULL,
				`arancif` DECIMAL(17,4) NULL DEFAULT '0.0000' COMMENT 'Monto del valor en base al cual se calcula el motoaran',
				`iva` DECIMAL(17,2) NULL DEFAULT NULL,
				`precio1` DECIMAL(15,2) NULL DEFAULT NULL,
				`precio2` DECIMAL(15,2) NULL DEFAULT NULL,
				`precio3` DECIMAL(15,2) NULL DEFAULT NULL,
				`precio4` DECIMAL(15,2) NULL DEFAULT NULL,
				`estampa` DATE NULL DEFAULT NULL,
				`hora` CHAR(8) NULL DEFAULT NULL,
				`usuario` CHAR(12) NULL DEFAULT NULL,
				`id` INT(20) UNSIGNED NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`),
				INDEX `numero` (`numero`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=FIXED
			AUTO_INCREMENT=0";
			var_dump($this->db->simple_query($mSQL));
		}

		if (!$this->db->table_exists('ordi')) {
			$mSQL="CREATE TABLE `ordi` (
				`numero` INT(15) UNSIGNED NOT NULL AUTO_INCREMENT,
				`fecha` DATE NULL DEFAULT NULL,
				`status` CHAR(1) NOT NULL DEFAULT '' COMMENT 'Estatus de la Compra Abierto, Eliminado y Cerrado',
				`proveed` VARCHAR(5) NULL DEFAULT NULL COMMENT 'Proveedor',
				`nombre` VARCHAR(40) NULL DEFAULT NULL COMMENT 'Nombre del Proveedor',
				`agente` CHAR(5) NULL DEFAULT NULL COMMENT 'Agente Aduanal (Proveedor)',
				`nomage` VARCHAR(40) NULL DEFAULT NULL COMMENT 'Agente Aduanal (Proveedor)',
				`montofob` DECIMAL(19,2) NULL DEFAULT NULL COMMENT 'Total de la Factura extranjera',
				`gastosi` DECIMAL(19,2) NULL DEFAULT NULL COMMENT 'Gastos Internacionales (Fletes, Seguros, etc)',
				`montocif` DECIMAL(19,2) NULL DEFAULT NULL COMMENT 'Monto FOB+gastos Internacionales',
				`aranceles` DECIMAL(19,2) NULL DEFAULT NULL COMMENT 'Suma del Impuesto Arancelario',
				`gastosn` DECIMAL(19,2) NULL DEFAULT NULL COMMENT 'Gastos Nacionales',
				`montotot` DECIMAL(19,2) NULL DEFAULT NULL COMMENT 'Monto CIF + Gastos Nacionales',
				`montoiva` DECIMAL(19,2) NULL DEFAULT NULL COMMENT 'Monto del IVA pagado',
				`montoexc` DECIMAL(12,2) NULL DEFAULT NULL,
				`arribo` DATE NULL DEFAULT NULL COMMENT 'Fecha de Llegada',
				`factura` VARCHAR(20) NULL DEFAULT NULL COMMENT 'Nro de Factura',
				`cambioofi` DECIMAL(17,2) NOT NULL DEFAULT '0.00' COMMENT 'Cambio Fiscal US$ X Bs.',
				`cambioreal` DECIMAL(17,2) NOT NULL DEFAULT '0.00' COMMENT 'Cambio Efectivamente Aplicado',
				`peso` DECIMAL(12,2) NOT NULL DEFAULT '0.00' COMMENT 'Peso total',
				`condicion` TEXT NULL,
				`transac` VARCHAR(8) NOT NULL DEFAULT '',
				`estampa` DATE NOT NULL DEFAULT '0000-00-00',
				`usuario` VARCHAR(12) NOT NULL DEFAULT '',
				`hora` VARCHAR(8) NOT NULL DEFAULT '',
				`dua` CHAR(30) NULL DEFAULT NULL COMMENT 'DECLARACION UNICA ADUANAS',
				`cargoval` DECIMAL(19,2) NULL DEFAULT NULL COMMENT 'Diferencia Cambiara $ oficial y aplicado',
				`control` VARCHAR(8) NULL DEFAULT NULL COMMENT 'Apuntador a la factura con la que se relaciono',
				`crm` INT(11) UNSIGNED NULL DEFAULT NULL COMMENT 'Apuntador al conetendor',
				`estimadif` DECIMAL(10,2) NULL DEFAULT '0' COMMENT 'Diferencia en la estimacion',
				PRIMARY KEY (`numero`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DYNAMIC
			AUTO_INCREMENT=0";
			var_dump($this->db->simple_query($mSQL));
		}

		if (!$this->db->table_exists('ordiva')) {
			$mSQL="CREATE TABLE `ordiva` (
				`id` INT(15) UNSIGNED NOT NULL AUTO_INCREMENT,
				`ordeni` INT(15) UNSIGNED NULL DEFAULT NULL,
				`tasa` DECIMAL(7,2) NULL DEFAULT NULL,
				`base` DECIMAL(10,2) NULL DEFAULT NULL,
				`montoiva` DECIMAL(10,2) NULL DEFAULT NULL,
				`concepto` VARCHAR(100) NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `ordi` (`ordeni`, `tasa`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT
			AUTO_INCREMENT=0";
			var_dump($this->db->simple_query($mSQL));
		}

		if (!$this->db->table_exists('gseri')) {
			$mSQL="CREATE TABLE `gseri` (
			`ordeni` INT(15) UNSIGNED NOT NULL,
			`fecha` DATE NOT NULL DEFAULT '0000-00-00',
			`numero` VARCHAR(8) NOT NULL DEFAULT '',
			`concepto` VARCHAR(40) NULL DEFAULT NULL,
			`monto` DECIMAL(19,2) NULL DEFAULT NULL,
				`proveed` VARCHAR(5) NULL DEFAULT '',
				`nombre` VARCHAR(30) NULL DEFAULT '',
				`usuario` VARCHAR(12) NULL DEFAULT NULL,
				`estampa` DATE NULL DEFAULT NULL,
				`hora` VARCHAR(8) NULL DEFAULT NULL,
				`id` INT(11) NOT NULL AUTO_INCREMENT,
				PRIMARY KEY (`id`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DYNAMIC
			AUTO_INCREMENT=0";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->db->field_exists('importecifreal', 'itordi')){
			$mSQL="ALTER TABLE `itordi`  ADD COLUMN `importecifreal` DECIMAL(17,2) NULL DEFAULT NULL COMMENT 'importe cif en moneda local al cambio real' AFTER `importeciflocal`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->db->field_exists('costoreal', 'itordi')){
			$mSQL="ALTER TABLE `itordi`  ADD COLUMN `costoreal` DECIMAL(17,2) NULL DEFAULT NULL COMMENT 'costo unitario al dolar real' AFTER `importefinal`,  ADD COLUMN `importereal` DECIMAL(17,2) NULL DEFAULT NULL COMMENT 'importe al dolar real' AFTER `costoreal`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->db->field_exists('estimadif', 'ordi')){
			$mSQL="ALTER TABLE `ordi`ADD COLUMN `estimadif` DECIMAL(10,2) NULL DEFAULT '0' COMMENT 'Diferencia en la estimacion' AFTER `crm`";
			var_dump($this->db->simple_query($mSQL));
		}

		if(!$this->db->table_exists('ordiestima')){
			$mSQL="CREATE TABLE `ordiestima` (
				`id` INT(15) UNSIGNED NOT NULL AUTO_INCREMENT,
				`ordeni` INT(15) UNSIGNED NULL DEFAULT NULL,
				`monto` DECIMAL(10,2) NULL DEFAULT NULL,
				`concepto` VARCHAR(100) NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				INDEX `ordi` (`ordeni`)
			)
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";
			var_dump($this->db->simple_query($mSQL));
		}

		$this->prefijo='crm_';



		//./crm/callcenter.php
		if(!$this->db->field_exists('crm', 'scli')){
			$mSQL='ALTER TABLE `scli`  ADD COLUMN `crm` INT(15) UNSIGNED NULL DEFAULT NULL';
			var_dump($this->db->simple_query($mSQL));
		}
		$this->prefijo='crm_';


		//./crm/definiciones.php



		//./crm/contenedor.php
		$prefijo=$this->prefijo;

		if (!$this->db->table_exists("${prefijo}comentarios")) {
			$mSQL="CREATE TABLE `${prefijo}comentarios` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`usuario` varchar(50) DEFAULT NULL,
			`contenedor` int(11) NOT NULL DEFAULT '0',
			`fecha` date DEFAULT NULL,
			`motivo` varchar(200) DEFAULT NULL,
			`cuerpo` text,
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=".$this->db->char_set;
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists("${prefijo}definiciones")) {
			$mSQL="CREATE TABLE `${prefijo}definiciones` (
			`id` int(7) NOT NULL AUTO_INCREMENT,
			`nombre` varchar(50) DEFAULT '0',
			`estructura` text,
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=".$this->db->char_set;
			$this->db->simple_query($mSQL);
		}


		if (!$this->db->table_exists("${prefijo}contenedor")) {
			$mSQL="CREATE TABLE `${prefijo}contenedor` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`usuario` varchar(50) DEFAULT NULL,
			`derivado` int(11) DEFAULT '0',
			`definicion` int(7) DEFAULT '0',
			`tipo` int(7) DEFAULT '0',
			`status` int(7) DEFAULT '0',
			`fecha` date DEFAULT NULL,
			`cierre` date DEFAULT NULL,
			`resumen` varchar(200) DEFAULT NULL,
			`titulo` varchar(200) DEFAULT NULL,
			`cliente` varchar(5) DEFAULT NULL,
			`proveed` varchar(5) DEFAULT NULL,
			`descripcion` text,
			`condiciones` text,
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=".$this->db->char_set." COMMENT='contenedor'";
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists("${prefijo}eventos")) {
			$mSQL="CREATE TABLE `${prefijo}eventos` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`usuario` varchar(50) DEFAULT NULL,
			`contenedor` int(11) NOT NULL DEFAULT '0',
			`fecha` date DEFAULT NULL,
			`evento` varchar(200) DEFAULT NULL,
			`vence` date DEFAULT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=".$this->db->char_set;
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists("${prefijo}imagenes")) {
			$mSQL="CREATE TABLE `${prefijo}imagenes` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`usuario` varchar(50) DEFAULT NULL,
			`contenedor` int(11) NOT NULL DEFAULT '0',
			`fecha` date DEFAULT NULL,
			`nombre` varchar(200) DEFAULT NULL,
			`descripcion` text,
			`url` varchar(200) DEFAULT NULL,
			`imagen` blob,
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=".$this->db->char_set;
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists("${prefijo}status")) {
			$mSQL="CREATE TABLE `${prefijo}status` (
			`id` int(7) NOT NULL AUTO_INCREMENT,
			`usuario` varchar(50) DEFAULT NULL,
			`definicion` int(7) DEFAULT '0',
			`descrip` varchar(50) DEFAULT '0',
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=".$this->db->char_set;
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists("${prefijo}tipos")) {
			$mSQL="CREATE TABLE `${prefijo}tipos` (
			`id` int(7) NOT NULL AUTO_INCREMENT,
			`usuario` varchar(50) DEFAULT NULL,
			`definicion` int(7) DEFAULT '0',
			`descrip` varchar(50) DEFAULT '0',
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=".$this->db->char_set;
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists("${prefijo}adjuntos")) {
			$mSQL="CREATE TABLE `${prefijo}adjuntos` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`usuario` varchar(50) DEFAULT NULL,
			`contenedor` int(11) NOT NULL DEFAULT '0',
			`fecha` date DEFAULT NULL,
			`nombre` varchar(200) DEFAULT NULL,
			`descripcion` text,
			`url` varchar(200) DEFAULT NULL,
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=".$this->db->char_set;
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists("${prefijo}montos")) {
			$mSQL="CREATE TABLE `${prefijo}montos` (
			`id` int(11) NOT NULL AUTO_INCREMENT,
			`usuario` varchar(50) DEFAULT NULL,
			`contenedor` int(11) NOT NULL DEFAULT '0',
			`fecha` date DEFAULT NULL,
			`partida` varchar(15) DEFAULT NULL,
			`descripcion` varchar(200) DEFAULT NULL,
			`debe` decimal(19,0) DEFAULT '0',
			`haber` decimal(19,0) DEFAULT '0',
			PRIMARY KEY (`id`)
			) ENGINE=MyISAM DEFAULT CHARSET=".$this->db->char_set;
			$this->db->simple_query($mSQL);
		}

		if (!$this->db->table_exists("${prefijo}partidas")) {
			$mSQL="CREATE TABLE `${prefijo}partidas` (
			`codigo` varchar(15) NOT NULL DEFAULT '',
			`contenedor` int(7) NOT NULL,
			`descripcion` varchar(100) DEFAULT NULL,
			`enlace` varchar(6) DEFAULT NULL,
			`iva` decimal(5,2) DEFAULT NULL,
			`medida` varchar(5) DEFAULT NULL,
			`dacumu` varchar(5) DEFAULT NULL,
			PRIMARY KEY (`codigo`)
			) ENGINE=MyISAM DEFAULT CHARSET=".$this->db->char_set;
			$this->db->simple_query($mSQL);
		}



		//./crm/eventos.php

	}
}
