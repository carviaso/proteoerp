<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Bienvenido extends Controller {
	function Bienvenido(){
		parent::Controller();
	}

	function index(){
		$this->session->set_userdata('panel', $this->uri->segment(3));
		$data['titulo1']  = '<center>';
		$data['titulo1'] .= '<div id="tumblelog">';
		$data['titulo1'] .= image('portada.png','Sistema PoteoERP');
		$data['titulo1'] .= '<h2>Sistemas Administrativos</h2>';
		$data['titulo1'] .= '<p></p>';
		$data['titulo1'] .= '</div>';

		if ($this->datasis->login())
			$data['titulo1']  .= '<p>&nbsp;</p>';

		$data['titulo1']  .= '</center>';
		$this->layout->buildPage('bienvenido/home', $data);
	}

	function autentificar(){
		$usr=$this->db->escape($_POST['user']);
		$pws=sha1($_POST['pws']);

		$campos=$this->db->list_fields('usuario');
		if(!in_array('activo',$campos)){
			$mSQL="ALTER TABLE `usuario`  ADD COLUMN `activo` CHAR(1) NULL";
			$this->db->simple_query($mSQL);
		}
		if(!in_array('remoto',$campos)){
			$this->db->simple_query("ALTER TABLE `usuario` ADD COLUMN `remoto` CHAR(1) NULL DEFAULT 'S' COMMENT 'Si permite acceso remoto'");
		}
		$this->db->simple_query("UPDATE usuario SET activo='S' WHERE activo <> 'N' ");
		$this->db->simple_query("UPDATE usuario SET activo='S' WHERE activo IS NULL ");

		//if (!preg_match("/^[^'\"]+$/", $usr)>0){
		//	$sess_data = array('logged_in'=> FALSE);
		//	$this->session->set_userdata($sess_data);
		//	redirect($this->session->userdata('estaba'));
		//}
		if(!$this->secu->es_interno()){
			$ww='';
		}else{
			$ww=' AND remoto=\'S\'';
		}

		$cursor=$this->db->query("SELECT us_nombre FROM usuario WHERE us_codigo=${usr} AND SHA(us_clave)='${pws}' AND activo='S' ${ww}");
		if($cursor->num_rows() > 0){
			$rr = $cursor->row_array();
			$sal = each($rr);
			$sess_data = array('usuario' => $_POST['user'],'nombre'  => $sal[1],'logged_in'=> TRUE );
		} else {
			$sess_data = array('logged_in'=> FALSE);
		}
		$this->session->set_userdata($sess_data);
		if($sess_data['logged_in']) logusu('MENU','Entro en Proteo');
		redirect($this->session->userdata('estaba'));
	}

	function cese(){
		$this->session->sess_destroy();
		redirect();
	}

	function ingresar(){
		$viene=$this->session->userdata('estaba');
		$attributes       = array('name' => 'ingresar_form');
		$data['titulo1']  = form_open('bienvenido/autentificar',$attributes);
		$attributes       = array('name' => 'user','size' => '6','autocomplete'=>'off');
		$data['titulo1'] .='<table><tr><td>Usuario: </td><td>'.form_input($attributes).'</td></tr>';
		$attributes       = array('name' => 'pws','size' => '6','type' => 'password','autocomplete'=>'off');
		$data['titulo1'] .='<tr><td> Clave:  </td><td>'.form_input($attributes).'</td></tr>';
		$data['titulo1'] .='<tr><td></td><td>'.form_submit('usr_submit', 'Enviar').form_close().'</td></tr></table>';
		// Build the thing
		$this->layout->buildPage('bienvenido/ingresar', $data);
	}

	function ingresarVentana(){
		$viene=$this->session->userdata('estaba');
		$data['estilos'] = style("estilos.css");
		$attributes  = array('name' => 'ingresar_form');
		$data['cuerpo'] = form_open('bienvenido/autentificar',$attributes);
		$attributes  = array('name' => 'user','size' => '6','autocomplete'=>'off');
		$data['cuerpo'] .='<table><tr><td>Usuario: </td><td>'.form_input($attributes).'</td></tr>';
		$attributes  = array('name' => 'pws','size' => '6','type' => 'password','autocomplete'=>'off');
		$data['cuerpo'] .='<tr><td> Clave:  </td><td>'.form_input($attributes).'</td></tr>';
		$data['cuerpo'] .='<tr><td></td><td>'.form_submit('usr_submit', 'Enviar').form_close().'</td></tr></table>';
		// Build the thing
		$this->load->view('ingreso', $data);
	}

	function noautorizado($adicional=''){
		$viene=$this->session->userdata('estaba');
		$data['content'] ='<center><h1 style="font-size:28px;color:red;">Acceso Denegado</h1><br>No tiene suficientes derechos para entrar a este Modulo<br><br><br>'.img("images/perrotriste.png").'</center>'.$adicional;
		// Build the thing
		$this->load->view('view_ventanas', $data);
	}

	function accordion($pertenece=NULL){
		if(empty($pertenece)) return;
		$out='';
		$utf8c=($this->db->char_set=='latin1') && ($this->config->item('charset')=='UTF-8');
		$arreglo=arr_menu(2,$pertenece);
		$arreglo=arr2panel($arreglo);

		if (count($arreglo)>0){
			//$out ='<div id=\'accordion\'>';
			foreach($arreglo as $panel => $opciones ){
				$out .="<div class='myAccordion-declencheur'><h1>".htmlentities($panel)."</h1></div>\n";
				$out .= "<div class='myAccordion-content'><table width='100%' cellspacing='0' border='0'>\n";
				$color = "#FFFFFF";
				foreach ($opciones as $opcion) {
					$out .= "<tr bgcolor='$color'><td>";
					$out .= arr2link($opcion,$utf8c);
					$out .= "</td></tr>\n";
					if ( $color == "#FFFFFF" ) $color = "#F4F4F4"; else  $color = "#FFFFFF";
				}$out .="</table></div>\n";
			}//$out .='</div>';
		}
		echo $out;
	}

	function submenup($pertenece=NULL){
		if(empty($pertenece)) return;
		$out='';
		$utf8c=($this->db->char_set=='latin1') && ($this->config->item('charset')=='UTF-8');
		$arreglo=arr_menu(2,$pertenece);
		$arreglo=arr2panel($arreglo);

		if (count($arreglo)>0){
			//$out ='<div id=\'accordion\'>';
			foreach($arreglo as $panel => $opciones ){
				$out .="<div class='myAccordion-declencheur'><h1>".htmlentities($panel)."</h1></div>\n";
				$out .= "<div class='myAccordion-content'><table width='100%' cellspacing='0' border='0'>\n";
				$color = "#FFFFFF";
				foreach ($opciones as $opcion) {
					$out .= "<tr bgcolor='$color'><td>";
					$out .= arr2link($opcion,$utf8c);
					$out .= "</td></tr>\n";
					if ( $color == "#FFFFFF" ) $color = "#F4F4F4"; else  $color = "#FFFFFF";
				}$out .="</table></div>\n";
			}//$out .='</div>';
		}
		echo $out;
	}



	function error(){
		$this->layout->buildPage('bienvenido/error');
	}

	function cargapanel($pertenece=NULL) {
		if(empty($pertenece)) return;

		$utf8c=($this->db->char_set=='latin1') && ($this->config->item('charset')=='UTF-8');
		$dbpertenece = $this->db->escape($pertenece);
		$out         = '';
		$arreglo     = arr_menu(2,$pertenece);
		$arreglo     = arr2panel($arreglo);
		if (count($arreglo)>0){
			$out    = '';
			$desca  = $this->datasis->dameval("SELECT mensaje FROM intramenu WHERE modulo=${dbpertenece}");
			$imagen = $this->datasis->dameval("SELECT TRIM(imagen) imagen  FROM intramenu WHERE modulo=${dbpertenece}");
			if($utf8c) $desca= utf8_encode($desca);
			$desca  = htmlentities($desca,ENT_COMPAT,'UTF-8');
			$out   .= '<div style="margin:0px 0px;">';
			$out   .= '<table width="100%" border="0"><tr>';
			if ( strlen($imagen) == 0  )
				$out .= '<td>&nbsp;</td>';
			else
				$out .= '<td width="70">'.img(array('src'=>'images/'.$imagen,'height'=>30)).'</td>';
			$out .= '<td style="font-size:1.4em;font-weigth:bold">'.$desca.'</td>';
			$out .= '</tr></table>';
			$out .= '</div>';
			$out .= '<div id="maso">';
			$i=0;

			foreach($arreglo as $panel => $opciones){
				$i++;
				if($panel != 'REPORTES' && $panel != 'CONSULTAS'){

					if($pertenece == '9')
						$out .= '<div class=\'box col1\' style="color:#FAFAFA;background:#C11B17;"><span style="font-size:1.1em;font-weight:900;margin-bottom:18px">'.htmlentities($panel).'</span>';
					else
						$out .= '<div class=\'box col1\' style="color:#030C3F;background:#COCOCO;"><span style="font-size:1.1em;font-weight:900;margin-bottom:18px">'.htmlentities($panel).'</span>';

					$out .= '<table width=\'100%\' cellspacing=\'1\' border=\'0\'>';
					foreach ($opciones as $id=>$opcion) {
						$color = ($id%2==0)? 'F8F8F8':'FFFFFF';
						$out .= "<tr bgcolor='#${color}'><td>";
						$out .= arr2link($opcion,$utf8c);
						$out .= '</td></tr>';
					}
					$out .='</table></div>';
				}
			}
			$out .= '</div>';
		}
		echo $out;
	}
}
?>
