<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
include('metodos.php');

class Generar extends Metodos {

	function Generar(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->modulo=602;
		set_time_limit(3600);
	}

	function index() {
		$this->datasis->modulo_id($this->modulo,1);
		$this->rapyd->load('datagrid','dataform','fields');

		$control=$this->uri->segment(4);
		$checkbox =  '<input type="checkbox" name="genera[]" value="<#modulo#>" CHECKED>';

		$grid = new DataGrid('Seleccione los m&oacute;dulos que desea generar ');
		$grid->db->select('modulo, descripcion');
		$grid->db->from('`reglascont`');
		$grid->db->groupby('modulo');
		$grid->db->orderby('modulo,regla');

		$grid->column("M&oacute;dulo"     , "modulo"     );
		$grid->column("Descripci&oacute;n", "descripcion");
		$grid->column('Generar'    , $checkbox,'align="center"');
		$grid->build();

		$form = new DataForm('contabilidad/generar/procesar');
		$form->title('Rango de fecha para la Generaci&oacute;n');

		$form->fechai = new dateonlyField("Fecha Desde", "fechai","d/m/Y");
		$form->fechaf = new dateonlyField("Fecha Hasta", "fechaf","d/m/Y");

		$form->fechaf->size = $form->fechai->size=10;

		$form->fechai->insertValue =($this->input->post('fechai') ? $this->input->post('fechai') : date("Ymd"));
		$form->fechaf->insertValue =($this->input->post('fechaf') ? $this->input->post('fechaf') : date("Ymd"));

		$form->tabla= new containerField('tabla',$grid->output);
		if ($control) $form->control= new containerField('control','Contabilidad Generada');
		//$form->submit("btn_submit","Generar Depurado");
		$form->build_form();

		$data['script']="<script type='text/javascript'>
		var handlerFunc = function(t) {
			document.getElementById('preloader').style.display='none';
			new Effect.Opacity('contenido', {duration:0.5, from:0.3, to:1.0});
			alert(t.responseText);
		}

		var errFunc = function(t) {
			document.getElementById('preloader').style.display='none';
			new Effect.Opacity('contenido', {duration:0.5, from:0.3, to:1.0});
			alert('Error ' + t.status + ' -- ' + t.statusText);
		}

		function generar() {
			new Effect.toggle('preloader', 'appear');
			new Effect.Opacity('contenido', {duration:0.5, from:1.0, to:0.3});
			new Ajax.Request('".site_url('contabilidad/generar/procesar')."',{
			method: 'post',
			parameters : Form.serialize('df1'),
			onSuccess:handlerFunc,
			onFailure:errFunc});
		}
		</script>";

		$data['extras']="<div id='preloader' style='display: none;	position:absolute; left:40%; top:40%; font-family:Verdana, Arial, Helvetica, sans-serif;'>
			<center>".image("loading4.gif")."<br>".image("loadingBarra.gif")."<br>
			<b>Generando . . . </b>
			</center>
		</div>";
		$data['content'] = $form->output."<input type=button value='Generar' onclick='generar()'>";
		$data["head"]    = $this->rapyd->get_head();
		$data["head"]   .= script("prototype.js");
		$data["head"]   .= script("scriptaculous.js");
		$data["head"]   .= script("effects.js");
		$data['title']   ="<h1>Generar Contabilidad</h1>";
		$this->load->view('view_ventanas', $data);
	}


	function procesar(){
		$this->datasis->modulo_id($this->modulo,1);
		$this->load->library('validation');
		$fechai=$this->input->post('fechai');
		$fechaf=$this->input->post('fechaf');

		$fechamin = $this->datasis->dameval('SELECT inicio FROM cemp LIMIT 1');

		if ( $fechamin > substr(human_to_dbdate($fechai),0,10) ){
			echo 'Error: Fecha inicial menor a la permitida '.substr(human_to_dbdate($fechai),0,10); 
			return false;
		}

		$ban=0;
		$ban+=$this->validation->chfecha($fechai);
		$ban+=$this->validation->chfecha($fechaf);

		if($ban!=2) {echo 'Error: Fechas erroneas'; return false;}
		session_write_close();

		$qfechai=date("Ymd",timestampFromInputDate($fechai, 'd/m/Y'));
		$qfechaf=date("Ymd",timestampFromInputDate($fechaf, 'd/m/Y'));
		$generar=$this->input->post('genera');

		$salida=$this->_procesar($qfechai,$qfechaf,$generar);
		echo $salida;
		return true;
		//redirect('contabilidad/generar/index/completo');
	}

	function procesarshellmes($anio=null,$mes=null,$modulos='APAN,BCAJ,CRUC,GSER,NOMI,OTIN,PRMO,RCAJ,SCST,SFAC,SMOV,SPRM'){
		session_write_close();
		if(empty($anio) || empty($mes) || strlen($anio)!=4 || strlen($mes)!=2){
			echo "USO: php index.php contabilidad generar procesarshellmems anio mes [modulos]\n";
			echo "  anio YYYY\n";
			echo "  mes   MM\n";
			echo "  modulos APAN,BCAJ,CRUC,GSER,NOMI,OTIN,PRMO,RCAJ,SCST,SFAC,SMOV,SPRM \n";
			return TRUE;
		}
		$this->load->helper('date');
		$udate   = days_in_month($mes, $anio);
		$qfechai = $anio.$mes.'01';
		$qfechaf = $anio.$mes.str_pad($udate, 2, '0', STR_PAD_LEFT);
		$this->procesarshell($qfechai,$qfechaf,$modulos);
		return false;
	}

	function procesarshell($qfechai=null,$qfechaf=null,$modulos='APAN,BCAJ,CRUC,GSER,NOMI,OTIN,PRMO,RCAJ,SCST,SFAC,SMOV,SPRM'){
		session_write_close();
		if(isset($_SERVER['argv']) && !isset($_SERVER['SERVER_NAME'])){ //asegura que se ejecute desde shell
			if(empty($qfechai) OR empty($qfechai) OR strlen($qfechai)+strlen($qfechaf)<16){
				echo "USO: php index.php contabilidad generar procesarshell fecha_inicial fecha_final [modulos]\n";
				echo "  fecha_inicial YYYYMMDD\n";
				echo "  fecha_final   YYYYMMDD\n";
				echo "  modulos APAN,BCAJ,CRUC,GSER,NOMI,OTIN,PRMO,RCAJ,SCST,SFAC,SMOV,SPRM \n";
				return TRUE;
			}

			preg_match('/(?P<anio>\d{4})(?P<mes>\d{2})(?P<dia>\d{2})/', $qfechai, $matches);
			if(!checkdate($matches['mes'],$matches['dia'],$matches['anio'])) { echo 'Error: fecha inicial invalida'."\n"; return TRUE; }
			preg_match('/(?P<anio>\d{4})(?P<mes>\d{2})(?P<dia>\d{2})/', $qfechaf, $matches);
			if(!checkdate($matches['mes'],$matches['dia'],$matches['anio'])) { echo 'Error: fecha final invalida'."\n"; return TRUE; }

			$generar=explode(',',$modulos);
			$salida=$this->_procesar($qfechai,$qfechaf,$generar);
			echo $salida."\n";
			return FALSE;
		}else{
			show_404();
		}
		return TRUE;
	}


	function _procesar( $qfechai, $qfechaf, $generar=FALSE){
		$error=FALSE;

		if($generar){
			foreach ($generar as $modulo){
				$modulo=trim($modulo);
				$dbmodulo= $this->db->escape($modulo);

				$mod_query  = $this->db->query("SELECT origen, control FROM reglascont WHERE modulo=$dbmodulo AND regla=1");
				if ($mod_query->num_rows() > 0){
					$mod_row = $mod_query->row_array();

					$mTABLA   = $mod_row['origen'];
					$mCONTROL = $mod_row['control'];

					$query=$this->db->simple_query("DELETE FROM casi   WHERE fecha BETWEEN $qfechai AND $qfechaf AND origen='$modulo'");
					$query=$this->db->simple_query("DELETE FROM itcasi WHERE fecha BETWEEN $qfechai AND $qfechaf AND origen LIKE '$modulo%'");
				}else{
					continue;
				}

				if ($modulo == 'SCST' ) {
					$mSQL="UPDATE scst SET reten=0  WHERE reten IS NULL AND recep >= $qfechai ";
					$query = $this->db->query($mSQL);

					$mSQL="UPDATE scst SET reteiva=0 WHERE reteiva IS NULL AND  recep >= $qfechai ";
					$query = $this->db->query($mSQL);

					$mSQL="SELECT a.$mCONTROL mgrupo FROM $mTABLA WHERE a.recep BETWEEN $qfechai AND $qfechaf GROUP BY a.$mCONTROL";
				} else {
					$mSQL="SELECT a.$mCONTROL mgrupo FROM $mTABLA WHERE a.fecha BETWEEN $qfechai AND $qfechaf GROUP BY a.$mCONTROL";
				}

				$query = $this->db->query($mSQL);
				foreach ($query->result_array() as $fila){
					$aregla = $this->_hace_regla($modulo, $mCONTROL, $fila['mgrupo'],$qfechai);

					foreach ($aregla['casi'] as $casi){
						$ejecasi='INSERT IGNORE INTO casi ( comprob, fecha, descrip, origen ) '.$casi;
						$ejec=$this->db->simple_query($ejecasi);
						if($ejec==FALSE) {
							memowrite($ejecasi,'generarca');
							$error=true;
						}

					}
					$mm = 1;
					foreach ($aregla['itcasi'] as $itcasi){
						$ejeitcasi ='INSERT INTO itcasi (fecha, comprob, origen,  cuenta, referen, concepto, debe,  haber, sucursal, ccosto) '.$itcasi;
						$ejec=$this->db->simple_query($ejeitcasi);
						if($ejec==FALSE) {
							memowrite($ejeitcasi,'generarit'.$mm);
							$error=true;
						}
						$mm++;
					}
				}
				$this->_borra_huerfano();

				//Redondeo
				//$query=$this->db->query("SELECT comprob,sum(debe)-sum(haber) total, origen FROM itcasi WHERE (MID(comprob,1,2) IN ('VD','DV') OR MID(origen,1,4) IN ('SCOP','SCST')) AND fecha BETWEEN $qfechai AND $qfechaf GROUP BY comprob HAVING abs(total)>0 AND abs(total)<0.5");
				//foreach ($query->result_array() as $row){
				$mSQL="SELECT comprob,sum(debe)-sum(haber) total, origen FROM itcasi WHERE (MID(comprob,1,2) IN ('VD','DV') OR MID(origen,1,4) IN ('SCOP','SCST')) AND fecha BETWEEN $qfechai AND $qfechaf GROUP BY comprob HAVING abs(total)>0 AND abs(total)<0.5";
				$query = $this->db->query($mSQL);
				foreach ($query->result_array() as $row){
					//print_r($row);
					$mCOMPROB=$row['comprob'];
					$mORIGEN =$row['origen'] ;
					$mTOTAL  =$row['total']  ;
					$this->db->simple_query("UPDATE itcasi SET haber=haber+$mTOTAL WHERE comprob='$mCOMPROB' AND origen='$mORIGEN' ORDER BY haber DESC LIMIT 1 ");
					$this->db->simple_query("UPDATE casi SET debe=(SELECT sum(itcasi.debe) FROM itcasi WHERE casi.comprob=itcasi.comprob AND casi.comprob='$mCOMPROB' AND origen='$mORIGEN' GROUP BY itcasi.comprob) WHERE comprob='$mCOMPROB'");
					$this->db->simple_query("UPDATE casi SET haber=(SELECT sum(itcasi.haber) FROM itcasi WHERE casi.comprob=itcasi.comprob AND casi.comprob='$mCOMPROB' AND origen='$mORIGEN' GROUP BY itcasi.comprob) WHERE comprob='$mCOMPROB' ");
					$this->db->simple_query("UPDATE casi SET total=debe-haber WHERE comprob='$mCOMPROB' AND origen='$mORIGEN'");
				}
			}

			$lgenerar="'".implode("','",$generar)."'";
			$mSQL="SELECT comprob FROM itcasi WHERE fecha BETWEEN $qfechai AND $qfechaf GROUP BY comprob";
			//$query=$this->db->query($mSQL);

			//TOTALIZA EN ITCASI
			$query = $this->db->query($mSQL);
			foreach ($query->result_array() as $row){
				$usr  =$this->session->userdata('usuario');
				$comprob=$this->db->escape($row['comprob']);
				$sql="UPDATE casi
					SET debe=(SELECT sum(debe) FROM itcasi WHERE itcasi.comprob=casi.comprob),
					haber=(SELECT sum(haber) FROM itcasi WHERE itcasi.comprob=casi.comprob),
					total=(SELECT sum(debe)-sum(haber) FROM itcasi WHERE itcasi.comprob=casi.comprob),
					estampa=NOW(),
					usuario='$usr',
					hora=DATE_FORMAT(NOW(),'%H:%i:%s')
					WHERE comprob=$comprob ";
				//$ejec=$this->db->simple_query($sql,array($row['comprob']));
				$ejec=$this->db->simple_query($sql);
				if($ejec==FALSE){ memowrite($sql,'generar'); $error=true; }
			}

			if($error)
				$salida=utf8_encode('Hubo algunos errores en el proceso, se genero un centinela');
			else{
				$descuadre=$this->datasis->dameval("SELECT COUNT(*) FROM casi WHERE fecha BETWEEN $qfechai AND $qfechaf AND total<>0");
				$salida=utf8_encode('Listo! Asientos descuadrados: '.$descuadre);
			}
		}else{
			$salida=utf8_encode('Seleccione al menos un Modulo');
		}
		$mSQL='UPDATE itcasi JOIN casi ON itcasi.comprob=casi.comprob SET itcasi.idcasi=casi.id WHERE itcasi.idcasi IS NULL';
		$rt=$this->db->simple_query($mSQL);
		return $salida;
	}

	function _borra_huerfano(){
		$guery=$this->db->simple_query("DELETE FROM casi   WHERE comprob='' OR comprob IS NULL");
		$guery=$this->db->simple_query("DELETE FROM itcasi WHERE comprob='' OR comprob IS NULL");
		$guery=$this->db->simple_query("DELETE FROM itcasi USING itcasi LEFT JOIN casi ON itcasi.comprob=casi.comprob AND itcasi.origen like CONCAT(casi.origen,'%') WHERE casi.comprob IS NULL");
		$guery=$this->db->simple_query("DELETE FROM casi   USING casi LEFT JOIN itcasi ON itcasi.comprob=casi.comprob AND itcasi.origen like CONCAT(casi.origen,'%') WHERE itcasi.comprob IS NULL");
	}

	function instalar(){
		if(!$this->db->table_exists('cplacierre')){
			$mSQL="CREATE TABLE IF NOT EXISTS `cplacierre` (
			`id` int(20) unsigned NOT NULL AUTO_INCREMENT,
			`anno` int(10) DEFAULT NULL,
			`cuenta` varchar(250) DEFAULT NULL,
			`descrip` varchar(250) DEFAULT NULL,
			`monto` decimal(15,2) DEFAULT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `ac` (`anno`,`cuenta`)
			) ENGINE=MyISAM DEFAULT CHARSET=latin1 COMMENT='Cierres contables'";
			$this->db->simple_query($mSQL);
		}
	}

}
