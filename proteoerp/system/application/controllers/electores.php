<?php
/** ProteoERP
 *  Busca Electores
 *
*/
class Electores extends Controller {
	var $autolimit=50; //Limite en el autocomplete;

	function Electores(){
		parent::Controller();
		session_write_close();
	}

	function index(){

	}

	//***************************************
	//           Auto complete
	//***************************************
	function busca(){
		$papellido  = $this->uri->segment($this->uri->total_segments());
		$pnombre    = $this->uri->segment($this->uri->total_segments()-1);

		$dbpn = $this->db->escape($pnombre.'%');
		$dbpa = $this->db->escape($papellido.'%');
		
		$data = 'Consulta Vacia';
		$mSQL="SELECT CONCAT(nacionalidad,cedula) cedula, pnombre, snombre, papellido, sapellido, nacimiento, sexo
			FROM matloca_vente.electores WHERE papellido LIKE ${dbpa} AND pnombre like ${dbpn} LIMIT 100";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			$data = '<table>';
			foreach( $query->result_array() as  $row ) {
				$data .= '<tr>';
				$data .= '<td>'.$row['cedula'].'</td>';
				$data .= '<td>'.$row['pnombre'].'</td>';
				$data .= '<td>'.$row['snombre'].'</td>';
				$data .= '<td>'.$row['papellido'].'</td>';
				$data .= '<td>'.$row['sapellido'].'</td>';
				$data .= '<td>'.$row['nacimiento'].'</td>';
				$data .= '<td>'.$row['sexo'].'</td>';
				$data .= '</tr>';
			}
			$data .= '</table>';
		}
		
		echo $data;
		//return true;

	}


}
