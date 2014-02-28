<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
if (!defined('BASEPATH')) exit('No direct script access allowed');

class Cheques {

	function Cheques(){
		$this->CI =& get_instance();
		$this->CI->load->library('dompdf/cidompdf');
		$this->CI->load->plugin('numletra');
	}

	function genera($nombre,$monto,$banco,$_fecha=null,$endosable=true){
		$meses=array('Enero','Febrero','Marzo','Abril','Mayo','Junio', 'Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre');
		$ciudad= strtoupper($this->CI->datasis->traevalor('CIUDAD'));
		if(empty($ffecha)){
			$mkt = mktime();
		}else{
			if(preg_match('/(?P<anio>\d{4})-?(?P<mes>\d{2})-?(?P<dia>\d{2})/', $_fecha, $matches)){
				$mkt = mktime(0, 0, 0, $matches['mes'], $matches['dia'], $matches['anio']);
			}else{
				show_error('Error en el formato de la fecha, debe ser YYYY-MM-DD');
			}
		}

		$_fnombre = $this->CI->datasis->dameval('SELECT formato FROM tban WHERE cod_banc='.$this->CI->db->escape($banco));

		if(empty($_fnombre)){
			$_fnombre='CHEQUE';
		}
		$_arch_nombre='cheque.pdf';
		$_dbfnombre=$this->CI->db->escape($_fnombre);
		$this->CI->load->library('dompdf/cidompdf');
		$query = $this->CI->db->query('SELECT proteo FROM formatos WHERE nombre='.$_dbfnombre);
		if ($query->num_rows() > 0){
			$row  = $query->row();
			$forma= $row->proteo;
			if(empty($forma)){
				$forma=$this->_crearep($_fnombre);
			}
			if(empty($forma)){
				$forma=$this->_crearep('CHEQUE');
			}

			ob_start();
				echo eval('?>'.preg_replace('/;*\s*\?>/', '; ?>', str_replace('<?=', '<?php echo ', $forma)).'<?php ');
				$_html=ob_get_contents();
			@ob_end_clean();
			if(strlen($_html)>0)
				$this->CI->cidompdf->html2pdf($_html,$_arch_nombre,true);
			else
				echo 'Formato no definido';
		}else{
			$forma=$this->_crearep($_fnombre);
			echo 'Formato no existe';
		}

	}

	function _crearep($nombre,$tipo='proteo'){
		$nombre = strtoupper($nombre);
		$arch = "./formrep/formatos/${tipo}/${nombre}.for";
		if (file_exists($arch)){
			$forma=file_get_contents($arch);
			$data = array('nombre' => $nombre, $tipo => $forma);
			$mSQL = $this->CI->db->insert_string('formatos', $data).' ON DUPLICATE KEY UPDATE proteo=VALUES(proteo)';
			$ban=$this->CI->db->simple_query($mSQL);
			if($ban==false){
				return '';
			}
			return $forma;
		}else{
			return '';
		}

	}

}
