<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class logo extends Controller {
	var $upload_path;

	function logo(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->helper('download');
		$this->load->library('path');
		$path=new Path();
		$path->setPath($this->config->item('base_url'));
		$path->append('images');
		$this->upload_path =$path->getPath().'/';
		$this->write=is_writable('images');
	}

	function index(){
		$this->datasis->modulo_id(903,1);
		redirect('supervisor/logo/carga');
	}

	function carga(){
		$this->rapyd->load('dataform');

		if($this->write){
			$form = new DataForm('supervisor/logo/carga/ver');
			$form->archivo = new uploadField('Logotipo de la Empresa','logo');
			$form->archivo->upload_path   = $this->upload_path;
			$form->archivo->allowed_types = 'jpg|gif|png';
			$form->archivo->append('Solo imagenes jpg, gif, png');
			$form->archivo->rule = 'required';
			//$form->archivo->overwrite     = true;
			//$form->archivo->file_name = url_title('logo.jpg');

			$form->titulo = new inputField('Titulo', 'titulo');
			$form->titulo->insertValue='logo';
			$form->titulo->rule = 'trim|required|alpha';
			$form->titulo->size=10;

			$form->submit('btnsubmit','Subir');
			$form->build_form();

			if ($form->on_success()){
				$titu=$form->titulo->newValue;
				$arch=$form->archivo->upload_data['full_path'];
				$tipo=$form->archivo->upload_data['image_type'];
				switch ($tipo) {
					case 'jpeg':
						$im=imagecreatefromjpeg($arch);
						break;
					case 'gif':
						$im=imagecreatefromgif($arch);
						break;
					case 'png':
						$im=imagecreatefrompng($arch);
						break;
				}
				$mask = "images/${titu}*";
				array_map( 'unlink', glob( $mask ) );
				imagegd2($im,"images/${titu}_generatriz.gd2");
				imagedestroy($im);
				if(file_exists($arch))
					unlink($arch);
			}

			$this->load->library('table');

			$i=0;
			$pivot=array();
			foreach (glob('images/*_generatriz.gd2') as $nombre_archivo) {
				$nn=explode('_',basename($nombre_archivo));
				$url=site_url("supervisor/logo/traer/$nn[0].jpg");
				$pivot[]="<img src='".$url."' alt='$nombre_archivo'>";
				$i++;
				if($i%3==0){
					$this->table->add_row($pivot);
					$pivot=array();
				}
			}
			if($i==0)  $pivot[]="<img src='".site_url("supervisor/logo/traer/logo.jpg")."' alt='logo.jpg'>";
			if(count($pivot)>0) $this->table->add_row($pivot);
			$img=$this->table->generate();

			$data['content'] = $form->output.br().$img;
		}else{
			$data['content'] = "El directorio 'images' no se puede escribir.";
		}

		$data['title']   = '<h1>Subir Logotipo</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function traer($nombre='logo.jpg'){
		$this->load->helper('file');
		if(preg_match('/(?<nom>[a-zA-Z]+)(?<tam>\d*)\.(?<tip>(gif|jpg|png))/', $nombre, $match)>0){
			$arch="images/$nombre";
			$mime= get_mime_by_extension($nombre);
			header("Content-type: $mime");
			if(!file_exists($arch)){
				if(empty($match['tam'])) $match['tam']=127;
				$this->_crear($arch,$match['tam'],$match['tip'],$match['nom']);
			}
			if($this->write) echo read_file($arch);
		}else{
			show_404('');
		}
	}

	function traerv($nombre='logo.jpg'){
		$this->load->helper('file');
		if(preg_match('/(?<nom>[a-zA-Z]+)(?<tam>\d*)\.(?<tip>(gif|jpg|png))/', $nombre, $match)>0){
			$arch="images/$nombre";
			$mime= get_mime_by_extension($nombre);
			header("Content-type: $mime");
			if(!file_exists($arch)){
				if(empty($match['tam'])) $match['tam']=80;
				$this->_crear($arch,$match['tam'],$match['tip'],$match['nom'],true);
			}
			if($this->write) echo read_file($arch);
		}else{
			show_404('');
		}
	}

	function _crear($path,$medida=127,$formato='jpg',$nombre,$vert=false){
		$nom =basename($path,'.'.$formato);
		$gene=$nombre.'_generatriz.gd2';

		$dir =dirname($path).'/'.$gene;
		if(file_exists($dir)){  //si existe la generatriz
			$image = imagecreatefromgd2($dir);
			$oancho= imagesx($image);
			$oalto = imagesy($image);
			if($vert){
				$ancho=$medida;
				$alto  = round($ancho*$oalto/$oancho);
			}else{
				$alto = $medida;
				$ancho=round($alto*$oancho/$oalto);
			}
			$im    = imagecreatetruecolor($ancho, $alto);
			imagecopyresampled($im, $image, 0, 0, 0, 0, $ancho, $alto, $oancho, $oalto);
		}else{ //si no existe la generatriz
			$rif   = $this->datasis->traevalor('RIF');
			$titu  = (empty($rif)) ? 'Logotipo' : $rif;
			if($vert){
				$ancho=$medida;
				$alto  = 80;
			}else{
				$alto = $medida;
				$ancho=127;
			}
			$im    = imagecreate($ancho, $alto);
			$white = imagecolorallocate($im, 255, 255, 255);
			$black = imagecolorallocate($im, 0, 0, 0);
			$font_ancho   = imagefontwidth(5); // para calcular el grosor de la fuente
			$string_alto  = imagefontheight(5);
			$string_ancho = $font_ancho*strlen($titu);
			//imagefill($im, 0, 0, $white);      //Se crea una imagen con un unico color
			$x=floor(($ancho-$string_ancho)/2);
			$y=floor(($alto-$string_alto)/2);

			imagestring($im, 5,$x ,$y , $titu, $black); //El 5 viene a ser el tamaño de la letra 1-5
			imageline($im, $x, $y,$x+$string_ancho, $y, $black);
			imageline($im, $x, $y+$string_alto,$x+$string_ancho, $y+$string_alto, $black);
		}
		if(!$this->write) $path=null;
		switch ($formato) {
			case 'jpg':
				imagejpeg($im,$path);
				break;
			case 'gif':
				imagegif($im,$path);
				break;
			case 'png':
				imagepng($im,$path);
				break;
		}
		imagedestroy($im);
	}

	function ver($file='logo.logo'){
		//$file = $this->input->post('archivo');
		//echo 'Archivo='.$file;
		if(empty($file)) return FALSE;
		$this->load->helper('file');
		$string = read_file("./uploads/archivos/$file");
		$string = $string;
		echo $string;
	}
}
