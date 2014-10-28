<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Prenom extends Controller {

	function Prenom(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->titulo='Generar Prenomina';
	}

	function index(){
		$this->rapyd->load('dataform');
		$form = new DataForm('nomina/prenom/index/process');

		$script = '
		$(function() {
			$("#fechac").datepicker({dateFormat:"dd/mm/yy"});
			$("#fechap").datepicker({dateFormat:"dd/mm/yy"});
		});
		';

		$form->script($script,'modify');
		$form->script($script,'create');

		$form->contrato = new dropdownField('Contrato', 'contrato');
		$form->contrato->option('','Seleccionar');
		$form->contrato->options("SELECT codigo, concat(codigo,' ',nombre) nombre FROM noco ORDER BY nombre");
		$form->contrato->rule='required';

		$form->fechac = new dateonlyField('Fecha de corte', 'fechac');
		$form->fechac->rule='required|chfecha';
		$form->fechac->insertValue = date('Y-m-d');
		$form->fechac->size     = 12;
		$form->fechac->calendar = false;

		$form->fechap = new dateonlyField('Fecha de pago', 'fechap');
		$form->fechap->rule='required|chfecha';
		$form->fechap->insertValue = date('Y-m-d');
		$form->fechap->size=12;
		$form->fechap->calendar = false;

		$form->build_form();

		if ($form->on_success()){
			$this->load->dbforge();

			$prenom  ='prenom';
			$pretab  ='pretab';

			$contrato= $form->contrato->newValue;
			$fechap  = $form->fechap->newValue;

			$fechac = $form->fechac->newValue;
			$date   = DateTime::createFromFormat('Ymd', $fechac);
			$ultdia = intval(days_in_month(substr($fechac,4,2), substr($fechac,0,4)));
			$dia    = intval(substr($fechac,6,2));

			$dbcont = $this->db->escape($contrato);
			$tipo = $this->datasis->dameval('SELECT tipo FROM noco WHERE codigo='.$dbcont);
			if($tipo=='Q'){
				if($dia != $ultdia || $dia != 15){
					if($dia<15){
						$fechac = substr($fechac,0,6).'15';
					}else{
						$fechac = substr($fechac,0,6).$ultdia;
					}
				}
			}elseif($tipo=='M'){
				if($dia != $ultdia){
					$fechac = substr($fechac,0,6).$ultdia;
				}
			}elseif($tipo=='S'){ //Fecha - 7
				$date->sub(new DateInterval('P7D'));
				$fechac = $date->format('Ymd');

			}elseif($tipo=='S'){ //Fecha -14
				$date->sub(new DateInterval('P14D'));
				$fechac = $date->format('Ymd');
			}else{

			}
			$this->_creaprenom($contrato, $fechac, $fechap,true);
			$this->_creapretab();  // Crea Pretabla
			$this->calcuto();      // Calcula todos

			echo "Crea los 2 ${contrato}, ${fechac}, ${fechap}";
		} else
			echo $form->output;

	}


	//******************************************************************
	//  Genera Prenomina
	//
	function geneprenom(){
		$this->load->helper('date');
		$contrato = $this->input->post('contrato');
		$fechac   = $this->input->post('fechac');
		$fechap   = $this->input->post('fechap');
		$pers     = $this->input->post('pers');

		$prenom  ='prenom';
		$pretab  ='pretab';

		$date   = DateTime::createFromFormat('d/m/Y', $fechap);
		$fechap = $date->format('Ymd');

		$date   = DateTime::createFromFormat('d/m/Y', $fechac);
		$fechac = $date->format('Ymd');

		$ultdia = intval(days_in_month(substr($fechac,4,2), substr($fechac,0,4)));
		$dia    = intval(substr($fechac,6,2));

		$dbcont = $this->db->escape($contrato);
		$tipo = trim($this->datasis->dameval('SELECT tipo FROM noco WHERE codigo='.$dbcont));
		if($tipo == 'Q'){
			if($dia != $ultdia || $dia != 15){
				if($dia<=15){
					$fechac = substr($fechac,0,6).'15';
				}else{
					$fechac = substr($fechac,0,6).$ultdia;
				}
			}
		}elseif($tipo=='M'){
			if($dia != $ultdia){
				$fechac = substr($fechac,0,6).$ultdia;
			}
			$pers = null;
		}elseif($tipo=='S'){ //Fecha - 7
			//$date->sub(new DateInterval('P7D'));
			$fechac = $date->format('Ymd');
			$pers = null;
		}elseif($tipo=='B'){ //Fecha -14
			//$date->sub(new DateInterval('P14D'));
			$fechac = $date->format('Ymd');
			$pers = null;
		}else{
			if(empty($pers)){
				$pers = $contrato;
			}
		}

		$this->_creaprenom($contrato, $fechac, $fechap,true,$pers);
		$this->_creapretab();  // Crea Pretabla
		$this->calcuto();      // Calcula todos

		echo 'Prenomina Generada exitosamente, Contrato: '.$contrato.' Fecha de Corte: '.dbdate_to_human($fechac).' Fecha de pago: '.dbdate_to_human($fechap) ;
	}

	//******************************************************************
	//  Genera Prenomina
	//
	function regenepre(){
		$this->load->helper('date');

		$mreg = $this->datasis->damereg('SELECT contrato, trabaja, fecha, fechap FROM prenom LIMIT 1');
		$contrato = $mreg['contrato'];
		$fechac   = $mreg['fecha'];
		$fechap   = $mreg['fechap'];

		$prenom  ='prenom';
		$pretab  ='pretab';

		$dbcont = $this->db->escape($contrato);
		$tipo = $this->datasis->dameval('SELECT tipo FROM noco WHERE codigo='.$dbcont);

		// Elimina los Trabajadores que no estan en el contrato
		$mSQL = "DELETE prenom FROM prenom JOIN pers ON prenom.codigo = pers.codigo AND prenom.contrato=pers.contrato ";
		$this->db->query($mSQL);

		// Elimina los Conceptos que no estan en el contrato
		$mSQL = "DELETE prenom FROM prenom LEFT JOIN itnoco ON prenom.contrato = itnoco.codigo WHERE itnoco.codigo IS NULL";
		$this->db->query($mSQL);

		$this->_creaprenom($contrato, $fechac, $fechap, false );
		$this->_creapretab();  // Crea Pretabla
		$this->calcuto();      // Calcula todos

		$rt=array(
			'status'  => 'A',
			'mensaje' => 'Nomina Regenerada Contrato: '.$contrato.' Fecha de Corte: '.$fechac.' Fecha de pago: '.$fechap,
			'pk'      => $contrato
		);
		echo json_encode($rt);

	}



	function chcfecha($fecha){
		$cont=$this->input->post('contrato');
		$arr_fecha = explode('/',$fecha);
		$ultdia    = days_in_month($arr_fecha[1], $arr_fecha[2]);
		$dia       = $arr_fecha[0];

		if($cont!==false){
			$dbcont = $this->db->escape($cont);
			$tipo = $this->datasis->dameval('SELECT tipo FROM noco WHERE codigo='.$dbcont);
			if($tipo=='Q'){
				if($dia != $ultdia || $dia != '15'){
					$this->validation->set_message('chcfecha', "La nomina quincenal solo se puede cortar el 15 o el ${ultdia} del mes.");
					return false;
				}
			}elseif($tipo=='M'){
				if($dia != $ultdia){
					$this->validation->set_message('chcfecha', "La nomina mensual solo se puede cortar el ${ultdia} del mes.");
					return false;
				}
			}elseif($tipo=='S'){

			}

		}
		return true;
	}

	//******************************************************************
	//  Crea Pretab => Tabla de Prenomina Detalle
	//
	function _creaprenom($contrato, $fecha, $fechap, $sobrescribe = true ,$pers=null){
		$prenom  = 'prenom';
		$pretab  = 'pretab';

		$contdb = $this->db->escape($contrato);
		if(!empty($pers)){
			if(is_array($pers)){
				//Implementar
			}else{
				$contpr=$this->db->escape($pers);
			}
		}else{
			$contpr=$contdb;
		}

		// APLICA AUMENTOS DE SUELDO
		$mSQL  = "UPDATE ausu a JOIN pers b ON a.codigo=b.codigo ";
		$mSQL .= "SET b.sueldo=a.sueldo WHERE a.fecha<='${fecha}'";
		//$this->db->query($mSQL);

		// TRAE FRECUENCIA DE CONTRATO
		$FRECUEN  = $this->datasis->dameval("SELECT tipo FROM noco WHERE codigo=".$contdb);
		$dbFRECUEN= $this->db->escape($FRECUEN);
		$this->db->query("UPDATE pers SET tipo=${dbFRECUEN} WHERE contrato=".$contdb);

		if($sobrescribe){
			$this->db->query("TRUNCATE ${prenom}");
		}

		// ---- CONCEPTOS PARTICULARES ---- //
		$mSQL  = "INSERT IGNORE INTO ${prenom} (contrato, codigo, nombre, concepto, grupo, tipo, descrip, formula, monto, fecha, fechap ) ";
		$mSQL .= "SELECT ${contdb} contrato, b.codigo, CONCAT(RTRIM(b.apellido),', ',b.nombre) nombre,";
		$mSQL .= "a.concepto, c.grupo, a.tipo, a.descrip, a.formula, 0, '${fecha}', '${fechap}' ";
		$mSQL .= "FROM asig AS a ";
		$mSQL .= "JOIN pers AS b ON a.codigo=b.codigo ";
		$mSQL .= "JOIN conc AS c ON a.concepto=c.concepto ";
		$mSQL .= "WHERE b.tipo=${dbFRECUEN} AND b.contrato=${contpr} AND b.status='A' ";
		$this->db->query($mSQL);

		$mSQL  = "INSERT IGNORE INTO ${prenom} (contrato, codigo,nombre, concepto, grupo, tipo, descrip, formula, monto, fecha, fechap ) ";
		$mSQL .= "SELECT ${contdb}, b.codigo, CONCAT(RTRIM(b.apellido),', ',b.nombre) nombre, ";
		$mSQL .= "a.concepto, a.grupo, a.tipo, a.descrip, a.formula, 0, '${fecha}', '${fechap}' ";
		$mSQL .= "FROM conc   AS a ";
		$mSQL .= "JOIN itnoco AS c ON a.concepto=c.concepto ";
		$mSQL .= "JOIN pers   AS b ON b.contrato=${contpr} ";
		$mSQL .= "WHERE c.codigo=${contdb} AND b.status='A' ";
		$this->db->query($mSQL);

		$this->db->query("UPDATE ${prenom} SET trabaja=contrato");

	}

	//******************************************************************
	//  Crea Pretab => Tabla de Prenomina Resumen
	//
	function _creapretab(){
		$this->load->library('pnomina');
		$this->pnomina->creapretab();
	}


	//******************************************************************
	// Calcula un Trabajador
	//
	function calcula( $codigo = '' ) {
		if ( !$codigo ) {
			echo 'Vacio';
			return false;
		}

		$this->load->library('pnomina');
		$this->pnomina->CODIGO = $codigo;

		$mCONTRATO = $this->datasis->dameval('SELECT contrato FROM prenom LIMIT 1');
		$fhasta    = $this->datasis->dameval('SELECT fecha FROM prenom LIMIT 1');

		$this->pnomina->fhasta = $fhasta;

		// Busca la Frecuencia en el Contrato
		$mFREC = $this->datasis->dameval("SELECT tipo FROM noco WHERE codigo=".$this->db->escape($mCONTRATO));

		// Busca la fecha inical
		if ( $mFREC == 'Q' ){        // Quincenal
			if ( substr($fhasta,8,2) > 15 ) {
				$this->pnomina->fdesde = substr($fhasta,0,8).'16' ;
			} else
				$this->pnomina->fdesde = substr($fhasta,0,8).'01' ;

		} elseif ( $mFREC == 'M'){   // Mensual
			$this->pnomina->fdesde = substr($fhasta,0,8).'01' ;

		} elseif ( $mFREC == 'S'){   // Semanal
			$d = new DateTime($fhasta);
			$d->sub(new DateInterval('P7D'));
			$this->pnomina->fdesde = $d->format('Y-m-d');

		} elseif ( $mFREC == 'B'){   // Bisemanal
			$d = new DateTime($fhasta);
			$d->sub(new DateInterval('P14D'));
			$this->pnomina->fdesde = $d->format('Y-m-d');
		}

		$query = $this->db->query('SELECT a.*, b.*, c.dias, c.psueldo FROM prenom a JOIN pers b ON a.codigo=b.codigo JOIN conc c ON a.concepto=c.concepto WHERE a.codigo='.$this->db->escape($codigo).' ORDER BY a.tipo, a.concepto');
		if ($query->num_rows() > 0) {
			$this->db->query("UPDATE pretab SET total=0 WHERE codigo=".$this->db->escape($codigo) );
			$SPROME = 0;
			$DIAS   = 0;
			foreach ($query->result() as $row){
				$this->pnomina->MONTO   = $row->monto;
				$this->pnomina->SUELDO  = $row->sueldo;
				$this->pnomina->SPROME  = $row->sueldo;
				$this->pnomina->DIAS    = 1;

				$this->pnomina->VARI1  = $row->vari1;
				$this->pnomina->VARI2  = $row->vari2;
				$this->pnomina->VARI3  = $row->vari3;
				$this->pnomina->VARI4  = $row->vari4;
				$this->pnomina->VARI5  = $row->vari5;
				$this->pnomina->VARI6  = $row->vari6;

				$valor = $this->pnomina->evalform($row->formula);
				$this->db->query("UPDATE prenom SET valor=${valor} WHERE concepto='".$row->concepto."' AND codigo=".$this->db->escape($codigo) );

				$this->db->query("UPDATE pretab SET c".$row->concepto."=${valor} WHERE codigo=".$this->db->escape($codigo) );

				if ( substr($row->concepto,0,1) != '9' )
					$this->db->query("UPDATE pretab SET total=total+${valor} WHERE codigo=".$this->db->escape($codigo) );

				// Calcula los dias Trabajados
				$mSQL = "SELECT dias FROM conc WHERE concepto=".$this->db->escape($row->concepto);
				if ( $valor <> 0 ){
					if ($row->monto <> 0)
						$DIAS += $this->datasis->dameval($mSQL)*$row->monto;
					else
						$DIAS += $this->datasis->dameval($mSQL);
				}
				// Calcula Sueldo Promedio
				if ( $row->psueldo == 'S' )
					$SPROME += $valor;


			}

			$this->db->query("UPDATE pretab SET total=0 WHERE codigo=".$this->db->escape($codigo) );
			foreach ($query->result() as $row){
				$this->pnomina->MONTO   = $row->monto;
				$this->pnomina->SUELDO  = $row->sueldo;
				$this->pnomina->SPROME  = $SPROME;
				$this->pnomina->DIAS    = $DIAS;

				$this->pnomina->VARI1  = $row->vari1;
				$this->pnomina->VARI2  = $row->vari2;
				$this->pnomina->VARI3  = $row->vari3;
				$this->pnomina->VARI4  = $row->vari4;
				$this->pnomina->VARI5  = $row->vari5;
				$this->pnomina->VARI6  = $row->vari6;

				$valor = $this->pnomina->evalform($row->formula);
				$this->db->query("UPDATE prenom SET valor=${valor} WHERE concepto='".$row->concepto."' AND codigo=".$this->db->escape($codigo) );

				$this->db->query("UPDATE pretab SET c".$row->concepto."=${valor} WHERE codigo=".$this->db->escape($codigo) );

				if ( substr($row->concepto,0,1) != '9' )
					$this->db->query("UPDATE pretab SET total=total+${valor} WHERE codigo=".$this->db->escape($codigo) );
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
/*
	1.- Calcular la primera nomina desde la fecha de ingreso
*/
