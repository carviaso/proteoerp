<?php
class Prenom extends Controller {

	function Prenom(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->titulo='Generar Prenomina';
	}

	function index(){
		$this->rapyd->load("dataform");
		$form = new DataForm('nomina/prenom/index/process');

		$script = '
		$(function() {
			$("#fechac").datepicker({dateFormat:"dd/mm/yy"});
			$("#fechap").datepicker({dateFormat:"dd/mm/yy"});
		});
		';

		$form->script($script,'modify');
		$form->script($script,'create');

		$form->contrato = new dropdownField("Contrato", "contrato");
		$form->contrato->option("","Seleccionar");
		$form->contrato->options("SELECT codigo, concat(codigo,' ',nombre) nombre FROM noco ORDER BY nombre");
		$form->contrato->rule='required';

		$form->fechac = new dateonlyField("Fecha de corte", "fechac");
		$form->fechac->rule='required|chfecha';
		$form->fechac->insertValue = date("Y-m-d");
		$form->fechac->size     = 12;
		$form->fechac->calendar = false;
		

		$form->fechap = new dateonlyField("Fecha de pago", "fechap");
		$form->fechap->rule='required|chfecha';
		$form->fechap->insertValue = date("Y-m-d");
		$form->fechap->size=12;
		$form->fechap->calendar = false;

		$form->build_form();

		if ($form->on_success()){
			$this->load->dbforge();

			$prenom  ='prenom';
			$pretab  ='pretab';

			$contrato= $form->contrato->newValue;
			$fechac  = $form->fechac->newValue;
			$fechap  = $form->fechap->newValue;

			$this->_creaprenom($contrato, $fechac, $fechap );
			$this->_creapretab();
			$this->calcuto();

			
			echo "crea los 2 $contrato, $fechac, $fechap";
		} else 
			echo $form->output;
		
	}

	//******************************************************************
	//  Crea Pretab => Tabla de Prenomina Detalle
	//
	function _creaprenom($contrato, $fecha, $fechap ){
		$contdb = $this->db->escape($contrato);
		$prenom  = 'prenom';
		$pretab  = 'pretab';
		
		// APLICA AUMENTOS DE SUELDO
		$mSQL  = "UPDATE ausu a JOIN pers b ON a.codigo=b.codigo ";
		$mSQL .= "SET b.sueldo=a.sueldo WHERE a.fecha<='".$fecha."'";
		//$this->db->query($mSQL);
		
		// TRAE FRECUENCIA DE CONTRATO
		$FRECUEN = $this->datasis->dameval("SELECT tipo FROM noco WHERE codigo=".$contdb);
		$this->db->query("UPDATE pers SET tipo='".$FRECUEN."' WHERE contrato=".$contdb);

		$this->db->query("TRUNCATE ${prenom}");
		
		// ---- CONCEPTOS FIJOS ---- //
		$mSQL  = "INSERT IGNORE INTO prenom (contrato, codigo, nombre, concepto, grupo, tipo, descrip, formula, monto, fecha, fechap ) ";
		$mSQL .= "SELECT ".$contdb." contrato, b.codigo, CONCAT(RTRIM(b.apellido),', ',b.nombre) nombre,";
		$mSQL .= "a.concepto, c.grupo, a.tipo, a.descrip, a.formula, 0, '".$fecha."', '".$fechap."' ";
		$mSQL .= "FROM asig a JOIN pers b ON a.codigo=b.codigo ";
		$mSQL .= "JOIN conc c ON a.concepto=c.concepto ";
		$mSQL .= "WHERE b.tipo='".$FRECUEN."' AND b.contrato=".$contdb." AND b.status='A' ";
		$this->db->query($mSQL);

		$mSQL  = "INSERT IGNORE INTO prenom (contrato, codigo,nombre, concepto, grupo, tipo, descrip, formula, monto, fecha, fechap ) ";
		$mSQL .= "SELECT ".$contdb.", b.codigo, CONCAT(RTRIM(b.apellido),', ',b.nombre) nombre, ";
		$mSQL .= "a.concepto, a.grupo, a.tipo, a.descrip, a.formula, 0, '".$fecha."', '".$fechap."' ";
		$mSQL .= "FROM conc a JOIN itnoco c ON a.concepto=c.concepto ";
		$mSQL .= "JOIN pers b ON b.contrato=c.codigo WHERE c.codigo=".$contdb." AND b.status='A' ";
		$this->db->query($mSQL);

		$this->db->query("UPDATE prenom SET trabaja=contrato");

	}

	//******************************************************************
	//  Crea Pretab => Tabla de Prenomina Resumen
	//
	function _creapretab(){
		$prenom  ='prenom';
		$pretab  ='pretab';

		$this->db->query("DROP TABLE IF EXISTS  ${pretab}");
		$mSQL  = "CREATE TABLE ${pretab} (";
		$mSQL .= "	codigo   CHAR(15)      NOT NULL DEFAULT '', ";
		$mSQL .= "	frec     CHAR(1)       NULL DEFAULT NULL, ";
		$mSQL .= "	fecha    DATE          NULL DEFAULT NULL, ";
		$mSQL .= "	nombre   CHAR(80)      NULL DEFAULT NULL, ";
		$mSQL .= "	total    DECIMAL(17,2) NULL DEFAULT '0.00', ";
		
		$query = $this->db->query("SELECT concepto FROM ${prenom} GROUP BY concepto ");
		foreach ($query->result() as $row){
			$mSQL .= "	c".$row->concepto." DECIMAL(17,2) DEFAULT 0.00, ";
		}
		$mSQL .= "	id       INT(11)       NOT NULL AUTO_INCREMENT, ";
		$mSQL .= "	PRIMARY KEY (id), ";
		$mSQL .= "	UNIQUE INDEX codigo (codigo) ";
		$mSQL .= ") ";
		$mSQL .= "COLLATE='latin1_swedish_ci' ";
		$mSQL .= "ENGINE=MyISAM; ";
		$this->db->query($mSQL);

		// -- LLENA PRETAB
		$mSQL = "
		INSERT IGNORE INTO pretab (codigo, frec, fecha, nombre)
		SELECT a.codigo, b.tipo, a.fecha, a.nombre 
		FROM prenom a JOIN noco b ON a.contrato=b.codigo 
		GROUP BY a.codigo";
		$this->db->query($mSQL);

	}


	//******************************************************************
	// Calcula un Trabajador
	//
	function calcula($codigo){
		$this->load->library('pnomina');
		$this->pnomina->CODIGO = $codigo;

		$mCONTRATO = $this->datasis->dameval('SELECT contrato FROM prenom LIMIT 1');
		$fhasta    = $this->datasis->dameval('SELECT fecha FROM prenom LIMIT 1');

		$this->pnomina->fhasta = $fhasta;

		// Busca la Frecuencia en el Contrato
		$mFREC = $this->datasis->dameval("SELECT tipo FROM noco WHERE codigo=".$this->db->escape($mCONTRATO));

		// Busca la fecha inical
		if ( $mFREC == 'Q' ){
			//$d = new DateTime($fhasta);
			//$this->pnomina->fdesde = $d->format('Y-m-t');
			if ( substr($fhasta,8,2) > 15 ) {
				$this->pnomina->fdesde = substr($fhasta,0,8).'15' ;
			} else
				$this->pnomina->fdesde = substr($fhasta,0,8).'01' ;

		} elseif ( $mFREC == 'M'){
			$this->pnomina->fdesde = substr($fhasta,0,8).'01' ;
		} elseif ( $mFREC == 'S'){

		}


	
		$query = $this->db->query('SELECT * FROM prenom a JOIN pers b ON a.codigo=b.codigo WHERE a.codigo='.$this->db->escape($codigo).' ORDER BY a.tipo, a.concepto');
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$this->pnomina->MONTO   = $row->monto;
				$this->pnomina->SUELDO  = $row->sueldo;

				$this->pnomina->VARI1  = $row->vari1;
				$this->pnomina->VARI2  = $row->vari2;
				$this->pnomina->VARI3  = $row->vari3;
				$this->pnomina->VARI4  = $row->vari4;
				$this->pnomina->VARI5  = $row->vari5;
				$this->pnomina->VARI6  = $row->vari6;

				$valor = $this->pnomina->evalform($row->formula);
				$this->db->query("UPDATE prenom SET valor=${valor} WHERE concepto='".$row->concepto."' AND codigo=".$this->db->escape($codigo) );

			}
		}
	}

	//******************************************************************
	// Calcula todos los Trabajadores
	//
	function calcuto(){
		$query = $this->db->query('SELECT codigo FROM prenom GROUP BY codigo');
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$this->calcula($row->codigo);
			}
		}
	}



	function montos(){
		$this->rapyd->load('datagrid','fields','datafilter');

		$error='';
		if($this->input->post('pros')!==FALSE){
			$concepto = $this->db->escape($this->input->post('concepto'));
			$pmontos  = $this->input->post('monto');

			$this->load->library('pnomina');
			$formula=$this->datasis->dameval("SELECT formula FROM conc WHERE concepto=$concepto");

			foreach($pmontos AS $cod=>$cant){
				if(!is_numeric($cant)){
					$error.="$cant no es un valor num&eacute;rico<br>";
				}else{
					$this->pnomina->CODIGO = $cod;
					$this->pnomina->MONTO  = $cant;
					//$valor=0;
					$valor=$this->pnomina->evalform($formula);

					$cod = $this->db->escape($cod);
					$data  = array('monto' => $cant,'valor'=>$valor);
					$where = "codigo = $cod  AND concepto =$concepto ";
					$mSQL  = $this->db->update_string('prenom', $data, $where);
					$this->db->simple_query($mSQL);
				}
			}
		}

		$filter = new DataFilter('&nbsp;', 'prenom');
		$filter->error_string=$error;

		$filter->concepto = new dropdownField('Concepto', 'concepto');
		$filter->concepto->option('','Seleccionar');
		$filter->concepto->options("SELECT concepto,descrip FROM prenom GROUP BY concepto ORDER BY descrip");
		$filter->concepto->clause  ='where';
		$filter->concepto->operator='=';
		$filter->concepto->rule    = 'required';

		$filter->buttons('reset','search');
		$filter->build();

		$ggrid='';
		if ($filter->is_valid()){
			$ggrid =form_open('/nomina/prenom/montos/search/osp');
			$ggrid.=form_hidden('concepto', $filter->concepto->newValue);

			$monto = new inputField('Monto', 'monto');
			$monto->grid_name='monto[<#codigo#>]';
			$monto->status   ='modify';
			$monto->size     =12;
			$monto->css_class='inputnum';

			$grid = new DataGrid("Concepto (".$filter->concepto->newValue.") ".$filter->concepto->options[$filter->concepto->newValue]);
			//$grid->per_page = $filter->db->num_rows() ;
			$grid->column('C&oacute;digo', 'codigo');
			$grid->column('Nombre', 'nombre');
			$grid->column('Monto' , $monto  ,'align=\'right\'');
			$grid->column('Valor' , 'valor' ,'align=\'right\'');
			$grid->submit('pros', 'Guardar','BR');
			$grid->build();
			$ggrid.=$grid->output;
			$ggrid.=form_close();

		}
		$script ='
		<script type="text/javascript">
		$(function() {
			$(".inputnum").numeric(".");
		});
		</script>';
		$data['content'] = $filter->output.$ggrid;
		$data['title']   = '<h1>Asignaci&oacute;n de montos</h1>';
		$data['script']  = $script;
		$data['head']    = $this->rapyd->get_head().script('jquery.pack.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
		$this->load->view('view_ventanas', $data);
	}

	function formulas(){
		$this->load->library('pnomina');
		$this->pnomina->CODIGO='002';
		$this->pnomina->MONTO =2500;

		$query = $this->db->query('SELECT * FROM conc');
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				echo $row->formula." = ";
				echo $this->pnomina->evalform($row->formula);
				echo "\n";
			}
		}
	}

	function tabla(){
		$this->rapyd->load('datagrid','fields');
		$contrato="'DIR01'";

		$ggrid =form_open('/nomina/prenom/montos/search/osp');
		$ggrid.=form_hidden('concepto', 'alguno');

		$grid = new DataGrid('Asignaciones','pretab');

		$grid->column('C&oacute;digo', 'codigo');
		$grid->column('Nombre', 'nombre');

		$query = $this->db->query("SELECT descrip,concepto FROM itnoco WHERE codigo=$contrato ORDER BY concepto");
		foreach ($query->result() as $row){
			$ind = 'c'.trim($row->concepto);

			$campo = new inputField('Campo', $ind);
			$campo->grid_name=$ind.'[<#codigo#>]';
			$campo->status   ='modify';
			$campo->size     =12;
			$campo->css_class='inputnum';

			$grid->column($row->descrip , $campo,'align=\'center\'');
		}
		$grid->submit('pros', 'Guardar','BR');
		$grid->build();

		$ggrid.=$grid->output;
		$ggrid.=form_close();

		$data['content'] = $ggrid;
		$data['title']   = '<h1>Tabla de montos</h1>';
		//$data['script']  = $script;
		$data['head']    = $this->rapyd->get_head().script('jquery.pack.js').script('plugins/jquery.numeric.pack.js').script('plugins/jquery.floatnumber.js');
		$this->load->view('view_ventanas', $data);

	}

	function prueba(){
		function &a(){
			return array(1,2,3);
		}

		//echo a()[0];
		$fdesde='2010-06-01';
		$fhasta='2010-07-20';
		
		$dsemana=1; //1 para lunes, 2 para martes .... 7 domingo
		$dated = new DateTime($fdesde);
		$dateh = new DateTime($fhasta);
		$dias  = 0;
		$intervalo='P1D';

		while($dated<=$dateh){
			if(date('N',$dated->getTimestamp())==$dsemana) {
				$dias++;
				$intervalo='P7D';
			}
			$dated->add(new DateInterval($intervalo));
		}
		echo 'Hay '.$dias." Lunes \n";
	}

}
?>
