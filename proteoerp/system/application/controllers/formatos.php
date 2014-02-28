<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Formatos extends Controller{

	var $_direccion;
	function Formatos(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->plugin('numletra');
		$this->load->helper('string');
	}

	function index(){

	}

	function ver1(){
		$parametros= func_get_args();
		$this->_direccion='http://localhost/'.trim_slashes($this->config->item('base_url'));
		if (count($parametros)>0){
			$_fnombre=array_shift($parametros);
			$_dbfnombre=$this->db->escape($_fnombre);
			$this->load->plugin('html2pdf');
			$query = $this->db->query('SELECT proteo FROM formatos WHERE nombre='.$_dbfnombre);
			if ($query->num_rows() > 0){
				$row = $query->row();
				ob_start();
					echo eval('?>'.preg_replace('/;*\s*\?>/', '; ?>', str_replace('<?=', '<?php echo ', $row->proteo)).'<?php ');
					$_html=ob_get_contents();
				@ob_end_clean();
				if(strlen($_html)>0)
					pdf_create($_html, $_arch_nombre);
				else
					echo 'Formato no definido';
			}else{
				echo 'Formato no existe';
			}
		}else{
			echo 'Faltan parametros';
		}
	}

	function ver(){
		$parametros= func_get_args();
		$this->_direccion='http://localhost/'.trim_slashes($this->config->item('base_url'));
		if (count($parametros)>0){
			$_arch_nombre=implode('-',$parametros).'.pdf';
			$_fnombre=array_shift($parametros);
			$_dbfnombre=$this->db->escape($_fnombre);
			$this->load->library('dompdf/cidompdf');
			$query = $this->db->query('SELECT proteo FROM formatos WHERE nombre='.$_dbfnombre);
			if ($query->num_rows() > 0){
				$row = $query->row();
				$forma= $row->proteo;
				if(empty($forma)){
					$forma=$this->_crearep($_fnombre);
				}
				ob_start();
					echo eval('?>'.preg_replace('/;*\s*\?>/', '; ?>', str_replace('<?=', '<?php echo ', $forma)).'<?php ');
					$_html=ob_get_contents();
				@ob_end_clean();
				if(strlen($_html)>0)
					$this->cidompdf->html2pdf($_html,$_arch_nombre);
				else
					echo 'Formato no definido';
			}else{
				$forma=$this->_crearep($_fnombre);
				echo 'Formato no existe';
			}
		}else{
			echo 'Faltan parametros';
		}
	}

	function descargar(){
		$parametros= func_get_args();
		$this->_direccion='http://localhost/'.trim_slashes($this->config->item('base_url'));
		if (count($parametros)>0){
			$_arch_nombre=implode('-',$parametros).'.pdf';
			$_fnombre=array_shift($parametros);
			$_dbfnombre=$this->db->escape($_fnombre);
			$this->load->library('dompdf/cidompdf');
			$query = $this->db->query('SELECT proteo FROM formatos WHERE nombre='.$_dbfnombre);
			if ($query->num_rows() > 0){
				$row = $query->row();
				$forma= $row->proteo;
				if(empty($forma)){
					$forma=$this->_crearep($_fnombre);
				}
				ob_start();
					echo eval('?>'.preg_replace('/;*\s*\?>/', '; ?>', str_replace('<?=', '<?php echo ', $forma)).'<?php ');
					$_html=ob_get_contents();
				@ob_end_clean();
				if(strlen($_html)>0)
					$this->cidompdf->html2pdf($_html,$_arch_nombre,true);
				else
					echo 'Formato no definido';
			}else{
				$forma=$this->_crearep($_fnombre);
				echo 'Formato no existe';
			}
		}else{
			echo 'Faltan parametros';
		}
	}

	function descargartxt(){
		$this->load->helper('download');
		$parametros= func_get_args();
		if (count($parametros)>0){
			//$_arch_nombre=implode('-',$parametros);
			$_fnombre=array_shift($parametros);
			$_dbfnombre=$this->db->escape($_fnombre);
			$query = $this->db->query('SELECT txt FROM formatos WHERE nombre='.$_dbfnombre);
			if ($query->num_rows() > 0){
				$row = $query->row();
				$forma= $row->txt;
				if(empty($forma)){
					$forma=$this->_crearep($_fnombre,'txt');
				}
				ob_start();
					echo eval('?>'.preg_replace('/;*\s*\?>/', '; ?>', str_replace('<?=', '<?php echo ', $forma)).'<?php ');
					$_txt=ob_get_contents();
				@ob_end_clean();
				if(strlen($_txt)>0){
					if(!array_key_exists('HTTP_USER_AGENT', $_SERVER))
						$_SERVER['HTTP_USER_AGENT']='curl';

					if(!isset($_arch_nombre)) $_arch_nombre='inprin.prn';
					force_download($_arch_nombre, preg_replace("/[\r]*\n/","\r\n",$_txt));
				}else{
					$forma=$this->_crearep($_fnombre,'txt');
					echo 'Formato no definido';
				}
			}else{
				$forma=$this->_crearep($_fnombre,'txt');
				echo 'Formato no existe';
			}
		}else{
			echo 'Faltan parametros';
		}
	}

	function incluir($nombre){
		$_dbfnombre=$this->db->escape($nombre);
		$query = $this->db->query('SELECT proteo FROM formatos WHERE nombre='.$_dbfnombre);
		if ($query->num_rows() > 0){
			$row = $query->row();
			if(empty($row->proteo)){
				$rep = $this->_crearep($nombre,'proteo');
			}else{
				$rep = $row->proteo;
			}
		}else{
			$rep = $this->_crearep($nombre,'proteo');
		}
		echo eval('?>'.preg_replace('/;*\s*\?>/', '; ?>', str_replace('<?=', '<?php echo ', $rep )).'<?php ');
	}

	function verhtml(){
		$parametros= func_get_args();
		$this->_direccion='/'.trim_slashes($this->config->item('base_url'));
		if (count($parametros)>0){
			$_fnombre  = array_shift($parametros);
			$_dbfnombre= $this->db->escape($_fnombre);
			$query = $this->db->query('SELECT proteo FROM formatos WHERE nombre='.$_dbfnombre);
			if ($query->num_rows() > 0){
				$row  = $query->row();
				$forma= $row->proteo;
				if(empty($forma)){
					$forma=$this->_crearep($_fnombre);
				}
				eval('?>'.preg_replace('/;*\s*\?>/', '; ?>', str_replace('<?=', '<?php echo ', $forma)).'<?php ');
			}else{
				$forma=$this->_crearep($_fnombre);
				eval('?>'.preg_replace('/;*\s*\?>/', '; ?>', str_replace('<?=', '<?php echo ', $forma)).'<?php ');
			}
		}else{
			echo 'Faltan parametros';
		}
	}

	function verhtmllocal(){
		$parametros= func_get_args();
		$this->_direccion='http://localhost/'.trim_slashes($this->config->item('base_url'));
		if (count($parametros)>0){
			$_fnombre  = array_shift($parametros);
			$_dbfnombre= $this->db->escape($_fnombre);
			$query = $this->db->query('SELECT proteo FROM formatos WHERE nombre='.$_dbfnombre);
			if ($query->num_rows() > 0){
				$row  = $query->row();
				$forma= $row->proteo;
				if(empty($forma)){
					$forma=$this->_crearep($_fnombre);
				}
				eval('?>'.preg_replace('/;*\s*\?>/', '; ?>', str_replace('<?=', '<?php echo ', $forma)).'<?php ');
			}else{
				$forma=$this->_crearep($_fnombre);
				eval('?>'.preg_replace('/;*\s*\?>/', '; ?>', str_replace('<?=', '<?php echo ', $forma)).'<?php ');
			}
		}else{
			echo 'Faltan parametros';
		}
	}

	function cintillo($cinti = 'CINTILLO'){
		// Cintillo por defecto si no existe
		$mhtml = "\n";
		$mhtml .= '			<!-- CINTILLO -->'."\n";
		$mhtml .= '			<div id="section_header">'."\n";
		$mhtml .= '				<table style="width: 100%;" >'."\n";
		$mhtml .= '					<tr>'."\n";
		$mhtml .= '						<td width=140 rowspan="3"><img src="\'.$this->_direccion.\'/images/logo.jpg" width="127"></td>'."\n";
		$mhtml .= '						<td><span style="text-align:left;font-size:1.4em;font-style:italic;font-weight: bold;">\'.trim($this->datasis->traevalor("TITULO1")).\'</span></td>'."\n";
		$mhtml .= '					</tr><tr>'."\n";
		$mhtml .= '						<td style="font-size: 8pt"><b>RIF: \'.$this->datasis->traevalor("RIF").\'</b></td>'."\n";
		$mhtml .= '					</tr><tr>'."\n";
		$mhtml .= '						<td><div style="font-size: 8pt">\'.trim($this->datasis->traevalor("TITULO2")).\' \'.trim($this->datasis->traevalor("TITULO3")).\'<div></td>'."\n";
		$mhtml .= '					</tr>'."\n";
		$mhtml .= '				</table>'."\n";
		$mhtml .= '			</div>'."\n";
		$mhtml .= '			<!-- FIN CINTILLO -->'."\n";

		if ( $this->datasis->dameval('SELECT COUNT(*) FROM formatos WHERE nombre="X_'.$cinti.'"') == 0 ){
			$this->db->query('INSERT INTO formatos SET proteo=?, nombre="X_'.$cinti.'"', array($mhtml));
		}
		return $this->traeyeva($cinti);

	}

	function scriptphp($script = 'SCRIPTP'){
		$mhtml  = "\n";
		$mhtml .= '<!-- SCRIPT PARA CONTROL DE FORMATO -->'."\n";
		$mhtml .= '<script type="text/php">'."\n";
		$mhtml .= 'if ( isset($pdf) ) {'."\n";
		$mhtml .= '	$font = Font_Metrics::get_font("verdana");;'."\n";
		$mhtml .= '	$size = 6;'."\n";
		$mhtml .= '	$color = array(0,0,0);'."\n";
		$mhtml .= '	$text_height = Font_Metrics::get_font_height($font, $size);'."\n";
		$mhtml .= '	$foot = $pdf->open_object();'."\n";
		$mhtml .= '	$w = $pdf->get_width();'."\n";
		$mhtml .= '	$h = $pdf->get_height();'."\n";
		$mhtml .= '	// Draw a line along the bottom'."\n";
		$mhtml .= '	$y = $h - $text_height - 24;'."\n";
		$mhtml .= '	$pdf->line(16, $y, $w - 16, $y, $color, 0.5);'."\n";
		$mhtml .= '	$pdf->close_object();'."\n";
		$mhtml .= '	$pdf->add_object($foot, "all");'."\n";
		$mhtml .= '	$text = "PP {PAGE_NUM} de {PAGE_COUNT}";  '."\n";
		$mhtml .= '	// Center the text'."\n";
		$mhtml .= '	$width = Font_Metrics::get_text_width("PP 1 de 2", $font, $size);'."\n";
		$mhtml .= '	$pdf->page_text($w / 2 - $width / 2, $y, $text, $font, $size, $color);'."\n";
		$mhtml .= '}'."\n";
		$mhtml .= '</script>'."\n";
		$mhtml .= '<!-- FIN SCRIPTPHP -->'."\n";

		if($this->datasis->dameval('SELECT COUNT(*) FROM formatos WHERE nombre="X_'.$script.'"') == 0 ){
			$this->db->query('INSERT INTO formatos SET proteo=?, nombre="X_'.$script.'"', array($mhtml));
		}
		return $this->traeyeva($script);
	}

	function rephead($rhead = 'REPHEAD', $titulo){
		$mhtml  = '<html>'."\n";
		$mhtml .= '<head>'."\n";
		$mhtml .= '<title>'.$titulo.'</title>'."\n";
		$mhtml .= '<link rel="STYLESHEET" href="\'.$this->_direccion.\'/assets/default/css/formatos.css" type="text/css" />'."\n";
		$mhtml .= '</head>'."\n";

		if ( $this->datasis->dameval('SELECT COUNT(*) FROM formatos WHERE nombre="X_'.$rhead.'"') == 0 ){
			$this->db->query('INSERT INTO formatos SET proteo=?, nombre="X_'.$rhead.'"', array($mhtml));
		}
		return $this->traeyeva($rhead);
	}

	function _crearep($nombre,$tipo='proteo'){
		$nombre = strtoupper($nombre);
		$arch = "./formrep/formatos/${tipo}/${nombre}.for";
		if (file_exists($arch)){
			$forma=file_get_contents($arch);
			$data = array('nombre' => $nombre, $tipo => $forma);
			$mSQL = $this->db->insert_string('formatos', $data).' ON DUPLICATE KEY UPDATE proteo=VALUES(proteo)';
			$ban=$this->db->simple_query($mSQL);
			if($ban==false){
				return '';
			}
			return $forma;
		}else{
			return '';
		}

	}

	function traeyeva($parte){
		$mhtml =  $this->datasis->dameval('SELECT proteo FROM formatos WHERE nombre="X_'.$parte.'"');
		$salida = '';
		eval("\$salida = '". $mhtml."';");
		return $salida;
	}

	function us_ascii2html($str){
		$rt =trim($str);

		if($this->db->char_set=='latin1'){
			$rt=utf8_encode($rt);
		}
		//Convierte los caracteres de us-ascii
		$rt =str_replace(utf8_encode(chr(165)),'Ñ',$rt);
		$rt =str_replace(utf8_encode(chr(164)),'ñ',$rt);
		$rt =str_replace(utf8_encode(chr(166)),'º',$rt);

		$rt =htmlspecialchars($rt,ENT_COMPAT,'UTF-8');
		if($this->config->item('charset')!='UTF-8'){
			$rt= utf8_decode($rt);
		}
		return $rt;
	}

	function instalar(){
		$campos=$this->db->list_fields('formatos');
		if(!in_array('proteo' ,$campos)) $this->db->simple_query("ALTER TABLE `formatos` ADD COLUMN `proteo`  TEXT NULL AFTER `forma`"  );
		if(!in_array('harbour',$campos)) $this->db->simple_query("ALTER TABLE `formatos` ADD COLUMN `harbour` TEXT NULL AFTER `proteo`" );
		if(!in_array('tcpdf'  ,$campos)) $this->db->simple_query("ALTER TABLE `formatos` ADD COLUMN `tcpdf`   TEXT NULL AFTER `forma`"  );
		if(!in_array('txt'    ,$campos)) $this->db->simple_query("ALTER TABLE `formatos` ADD COLUMN `txt`     TEXT NULL AFTER `harbour`");
	}
}
