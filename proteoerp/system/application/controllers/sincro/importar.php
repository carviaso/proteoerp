<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Importar extends Controller {

	function Importar(){
		parent::Controller();
		$this->geneticket=true;
		$this->noborra=false;
		$this->timeout=900;
		$this->load->helper('string');
		$this->load->library('rapyd');
		$this->load->library('encrypt');
		$this->sucu = $this->datasis->traevalor('NROSUCU');
		$this->clave=sha1($this->config->item('encryption_key'));
		$this->depura=false;

		$this->dir=reduce_double_slashes($this->config->item('uploads_dir').'/traspasos');
		//$this->dir='./uploads/traspasos/';
		$path=reduce_double_slashes(FCPATH.'/uploads/traspasos');
		if(!file_exists($path)) if(!mkdir($path)) exit("Error: no se pudo crear el directorio $path");
		if(!is_writable($path)) exit("Error: no tiene permisos de escritura en $path");
		if(empty($this->sucu)) redirect('supervisor/valores/dataedit/show/NROSUCU');
	}

	function index(){
		$data['content'] = '';
		$data['title']   = heading('Importar');
		$data['script']  = '';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

//***********************
// Interfaces graficas
//***********************
	function uitraeg(){
		$this->rapyd->load('dataform');
		$sucu=$this->db->escape($this->sucu);
		$this->datasis->modulo_id('91E',1);

		$form = new DataForm('sincro/importar/uitraeg/process');

		$form->sucu = new dropdownField('Sucursal', 'sucu');
		$form->sucu->rule ='required';
		$form->sucu->option('','Selecionar');
		$form->sucu->options("SELECT codigo, sucursal  FROM sucu WHERE codigo <> $sucu AND CHAR_LENGTH(url)>0");

		$form->qtrae = new dropdownField('Que traer?', 'qtrae');
		$form->qtrae->rule ='required';
		$form->qtrae->option('','Selecionar');
		//$form->qtrae->option('scli'       ,'Clientes');
		$form->qtrae->option('sclilimit'  ,'Clientes');
		$form->qtrae->option('sprv'       ,'Proveedores');
		$form->qtrae->option('sinv'       ,'Inventario (clonar)');
		$form->qtrae->option('sinvprec'   ,'Inventario (Solo precios)');
		$form->qtrae->option('maes'       ,'Inventario Supermercado');
		$form->qtrae->option('smov'       ,'Movimientos de clientes');
		$form->qtrae->option('transa'     ,'Facturas y transferencias');
		$form->qtrae->option('rcaj'       ,'Cierres de caja');
		$form->qtrae->option('fiscalz'    ,'Cierres Z');
		$form->qtrae->option('sfacfis'    ,'Auditoria Fiscal');
		$form->qtrae->option('supertransa','Transacciones de Supermercado');

		$form->fecha = new dateonlyField('Fecha','fecha');
		$form->fecha->insertValue = date('Y-m-d');
		$form->fecha->rule ='required|chfecha';
		$form->fecha->size =12;
		$form->submit('btnsubmit','Descargar');
		$form->build_form();

		$exito='';
		if ($form->on_success()){
			$fecha=$form->fecha->newValue;
			$sucu =$form->sucu->newValue;
			$obj='_'.str_replace('_','',$form->qtrae->newValue);
			if(method_exists($this,$obj))
				$rt=$this->$obj($sucu,$fecha);
			else
				$rt='Metodo no definido ('.$form->qtrae->newValue.')';
			if(strlen($rt)>0){
				$form->error_string=$rt;
				$form->build_form();
			}else{
				$exito='Transferencia &Eacute;xitosa';
			}
		}

		$data['content'] = $form->output.$exito;
		$data['title']   = heading('Importar data de Sucursal');
		$data['script']  = '';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);

	}

	function uitrae($metodo=null){
		$obj='_'.str_replace('_','',$metodo); if(!method_exists($this,$obj)) show_404('page');
		$this->rapyd->load('dataform');
		$sucu=$this->db->escape($this->sucu);

		$form = new DataForm("sincro/importar/uitrae/$metodo/process");

		$form->sucu = new dropdownField("Sucursal", "sucu");
		$form->sucu->rule ='required';
		$form->sucu->option("","Selecionar");
		$form->sucu->options("SELECT codigo, sucursal  FROM sucu WHERE codigo <> $sucu");

		$form->fecha = new dateonlyField("Fecha","fecha");
		$form->fecha->insertValue = date("Y-m-d");
		$form->fecha->rule ="required|chfecha";
		$form->fecha->size =12;
		$form->submit("btnsubmit","Descargar");
		$form->build_form();

		$exito='';
		if ($form->on_success()){
			$fecha=$form->fecha->newValue;
			$sucu =$form->sucu->newValue;

			$rt=$this->$obj($sucu,$fecha);
			if(strlen($rt)>0){
				$form->error_string=$rt;
				$form->build_form();
			}else{
				$exito='Transferencia &Eacute;xitosa';
			}
		}

		$data['content'] = $form->output.$exito;
		$data['title']   = heading('Importar data de Sucursal ('.$metodo.')');
		$data['script']  = '';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
#########################################
# Interfaces para exportar con parametros
#########################################

	function uitraepara(){
		$this->rapyd->load('dataform');
		$sucu=$this->db->escape($this->sucu);
		$this->datasis->modulo_id('91E',1);

		$form = new DataForm("sincro/importar/uitraepara/process");

		$form->sucu = new dropdownField("Sucursal", "sucu");
		$form->sucu->rule ='required';
		$form->sucu->option("","Selecionar");
		$form->sucu->options("SELECT codigo, sucursal  FROM sucu WHERE codigo <> $sucu AND CHAR_LENGTH(url)>0");

		$form->qtrae = new dropdownField("Que traer?", "qtrae");
		$form->qtrae->rule ='required';
		$form->qtrae->option("","Selecionar");
		$form->qtrae->option("maesalma" ,"Inventario Supermercado");
		$form->qtrae->option("tranalma" ,"Facturas y transferencias");
		$form->qtrae->option("ubicalma" ,"Movimientos de invent. Supermercado");

		$form->fecha = new dateonlyField("Fecha","fecha");
		$form->fecha->insertValue = date("Y-m-d");
		$form->fecha->rule ="required|chfecha";
		$form->fecha->size =12;
		$form->submit("btnsubmit","Descargar");

		$form->alma= new dropdownField("Almac&eacute;n", "alma");
		$form->alma->rule ='required';
		$form->alma->option("","Selecionar");
		$form->alma->options("SELECT ubica, CONCAT(ubica,'-',ubides) AS descrip FROM caub ORDER BY ubica");
		$form->build_form();

		$exito='';
		if ($form->on_success()){
			$fecha=$form->fecha->newValue;
			$sucu =$form->sucu->newValue;
			$alma =$form->alma->newValue;
			$obj='_'.str_replace('_','',$form->qtrae->newValue);
			if(method_exists($this,$obj))
				$rt=$this->$obj($sucu,$fecha,$alma);
			else
				$rt='Metodo no definido ('.$form->qtrae->newValue.')';
			if(strlen($rt)>0){
				$form->error_string=$rt;
				$form->build_form();
			}else{
				$exito='Transferencia &Eacute;xitosa';
			}
		}

		$data['content'] = $form->output.$exito;
		$data['title']   = heading('Importar data de Sucursal');
		$data['script']  = '';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function uicarga(){
		set_time_limit(600);
		$this->rapyd->load('dataform');
		$this->load->library('Sqlinex');

		$form = new DataForm("sincro/importar/uicarga/process");

		$form->upl = new uploadField("Archivo Zip", "arch");
		$form->upl->upload_path = $this->dir;
		$form->upl->max_size     =6000;
		$form->upl->allowed_types = "zip";

		$form->submit("btnsubmit","Cargar");
		$form->build_form();

		$msg='';
		if ($form->on_success()){

			$nombre=$form->upl->upload_data['file_name'];
			$rt=$this->__cargazip($nombre);
			if(!empty($rt)){
				$form->error_string=$rt;
				$form->build_form();
			}else{
				$msg='<p>Carga completada</p>';
			}
		}

		$data['content'] = $form->output.$msg;
		$data['title']   = heading('Cargas de Zip');
		$data['script']  = '';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function vendambul($sucu=null){
		set_time_limit($this->timeout);
		if(empty($sucu)) $sucu='01';
		$ssucu=$this->db->escape($sucu);

		$cant=$this->datasis->dameval("SELECT * FROM sucu WHERE codigo=$ssucu");
		if($cant>0){
			$rt=$this->__traerzip($sucu,'finanzas/exportar/vendambul');
			if ($rt)
				$msg='Hubo un error en la trasnferencia, se genero un ticket '.anchor('supervisor/tiket','ir a tickets');
			else
				$msg='Tranferencia &eacute;xitosa';
		}else{
			$msg='Error, la sucursal '.$sucu.' no existe, revise la configuracion aqui: '.anchor('supervisor/sucu','sucursales');
		}
		$data['content'] = $msg.'<p>'.anchor('inventario/fotos/traerfotos/'.$sucu,'Traer fotos de invetario').'</p>';
		$data['title']   = heading('Descarga de informaci&oacute;n para vendedores ambulantes');
		$data['script']  = '';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

//**************************
// Correr desde Shell
//**************************
	function traeshell($psucu='*',$metodo,$fecha=null){
		if(isset($_SERVER['argv']) && !isset($_SERVER['SERVER_NAME'])){ //asegura que se ejecute desde shell

			$obj='_'.str_replace('_','',$metodo);
			if(!method_exists($this,$obj)) { echo "Error metodo $metodo no existe \n"; return false;}
			if(!$this->__chekfecha($fecha)){ echo "Error fecha no valida \n"; return false;}
			if($psucu!='*') $where='AND codigo ='.$this->db->escape($psucu); else $where='';

			$sucu=$this->sucu;
			$query = $this->db->query("SELECT * FROM sucu WHERE codigo<>$sucu $where");
			if(empty($fecha)) $fecha = date('Ymd');

			if ($query->num_rows() > 0){
				$rt='';
				foreach ($query->result() as $row){
					$rt.=$this->$obj($row->codigo,$fecha);
				}
				echo $rt;
			}
		}
	}

	function gtraeshell($sucu=null,$metodo=null,$fecha=null){
		if($this->secu->es_shell()){
			if(empty($sucu) || empty($metodo)){
				echo "USO: gtraesusu sucursal metodo [fecha] \n";
				$sucu=$this->sucu;
				$query = $this->db->query("SELECT * FROM sucu WHERE codigo<>$sucu");

				if ($query->num_rows() > 0){
					echo " Sucursales:\n";
					foreach ($query->result() as $row){
						echo '   '.$row->codigo.' -> '.$row->sucursal."\n";
					}
				}
				echo " Metodos: scli,sprv,maes,smov,fiscalz...\n";
				echo " Fecha Ymd \n";
				echo " Ej: ./traeshell.sh 01 sinv 20110101 \n";
				return true;
			}

			if(empty($fecha)) $fecha = date('Ymd');
			$cana = $this->datasis->dameval('SELECT COUNT(*) FROM sucu WHERE codigo='.$this->db->escape($sucu));
			if($cana>0){
				$obj='_'.str_replace('_','',$metodo);
				if(method_exists($this,$obj)){
					$rt =$this->$obj($sucu,$fecha);
					return $rt;
				}else{
					echo "Metodo '$metodo' no existe\n";
					return true;
				}
			}else{
				echo "Sucursal '$sucu' no valida \n";
				return true;
			}
		}
		return true;
	}

	function traetodosucu($principal,$fecha=null){
		if(isset($_SERVER['argv']) && !isset($_SERVER['SERVER_NAME'])){ //asegura que se ejecute desde shell

			if(empty($fecha)) $fecha = date('Ymd');

			$rt=$this->_scli($principal,null);
			$rt.=$this->_sinv($principal,null);
			echo $rt;
		}
	}

	function traesinvprec($principal,$fecha=null){
		if(isset($_SERVER['argv']) && !isset($_SERVER['SERVER_NAME'])){ //asegura que se ejecute desde shell
			if(empty($fecha)) $fecha = date('Ymd');
			$rt=$this->_sinvprec($principal,null);
			echo $rt;
			logusu('exportar','Importacion sinvprec realizada por shell');
		}
	}

	function traetodoprin($fecha=null){
		if(isset($_SERVER['argv']) && !isset($_SERVER['SERVER_NAME'])){ //asegura que se ejecute desde shell

			$sucu=$this->sucu;
			$query = $this->db->query("SELECT * FROM sucu WHERE codigo<>$sucu");
			if(empty($fecha)) $fecha = date('Ymd');

			if ($query->num_rows() > 0){
				$rt='';
				foreach ($query->result() as $row){
					//$rt.=$this->_smov($row->codigo,$fecha);
					$rt.=$this->_transa($row->codigo,$fecha);
					$rt.=$this->_fiscalz($row->codigo,$fecha);
					$rt.=$this->_rcaj($row->codigo,$fecha);
				}
				echo $rt;
			}
		}
	}

	function traesclisucu($fecha=null){
		if(isset($_SERVER['argv']) && !isset($_SERVER['SERVER_NAME'])){ //asegura que se ejecute desde shell
			$this->geneticket=false;
			$sucu=$this->sucu;
			$query = $this->db->query("SELECT * FROM sucu WHERE codigo<>$sucu");
			if(empty($fecha)) $fecha = date('Ymd');

			if ($query->num_rows() > 0){
				$rt='';
				foreach ($query->result() as $row){
					$rt.=$this->_scli($row->codigo,$fecha);
				}
				echo $rt;
			}
		}
	}

	function traesclilimitsucu($fecha=null){
		if(isset($_SERVER['argv']) && !isset($_SERVER['SERVER_NAME'])){ //asegura que se ejecute desde shell
			$this->geneticket=false;
			$sucu=$this->sucu;
			$query = $this->db->query("SELECT * FROM sucu WHERE codigo<>$sucu");
			if(empty($fecha)) $fecha = date('Ymd');

			if ($query->num_rows() > 0){
				$rt='';
				foreach ($query->result() as $row){
					$rt.=$this->_sclilimit($row->codigo,$fecha);
				}
				echo $rt;
			}
		}
	}

	function traemaesalma($fecha=null,$sucu,$alma){
		if(isset($_SERVER['argv']) && !isset($_SERVER['SERVER_NAME'])){ //asegura que se ejecute desde shell
			if(empty($fecha)) $fecha = date('Ymd');
			$rt=$this->_maesalma($sucu,$fecha,$alma);
			echo $rt;
		}
	}

	function traetranalma($fecha=null,$sucu,$alma){
		if(isset($_SERVER['argv']) && !isset($_SERVER['SERVER_NAME'])){ //asegura que se ejecute desde shell
			if(empty($fecha)) $fecha = date('Ymd');
			$rt=$this->_tranalma($sucu,$fecha,$alma);
			echo $rt;
		}
	}

	function traeubicalma($fecha=null,$sucu,$alma){
		if(isset($_SERVER['argv']) && !isset($_SERVER['SERVER_NAME'])){ //asegura que se ejecute desde shell
			if(empty($fecha)) $fecha = date('Ymd');
			$rt=$this->_ubicalma($sucu,$fecha,$alma);
			echo $rt;
		}
	}

//**************************
//Metodos para traer data
//**************************

	function _scli($sucu,$fecha=null){
		set_time_limit($this->timeout);
		$rt=$this->__traerzip($sucu,'sincro/exportar/uri/'.$this->clave.'/scli/'.$fecha,'scli');
		return $rt;
	}

	function _sprv($sucu,$fecha=null){
		set_time_limit($this->timeout);
		$rt=$this->__traerzip($sucu,'sincro/exportar/uri/'.$this->clave.'/sprv/'.$fecha,'sprv');
		return $rt;
	}

	function _datacenter($sucu,$fecha=null){
		set_time_limit($this->timeout);
		$rt=$this->__traerzip($sucu,'sincro/exportar/uri/'.$this->clave.'/datacenter/'.$fecha,'datacenter');
		return $rt;
	}

	function _datacentersinv($sucu,$fecha=null){
		set_time_limit($this->timeout);
		$rt=$this->__traerzip($sucu,'sincro/exportar/uri/'.$this->clave.'/datacentersinv/'.$fecha,'datacenter');
		return $rt;
	}

	function _datacentercostos($sucu,$fecha=null){
		set_time_limit($this->timeout);
		$rt=$this->__traerzip($sucu,'sincro/exportar/uri/'.$this->clave.'/datacentercostos/'.$fecha,'datacenter');
		return $rt;
	}

	//Clientes con limite de credito 0
	function _sclilimit($sucu,$fecha=null){
		set_time_limit($this->timeout);
		$rt=$this->__traerzip($sucu,'sincro/exportar/uri/'.$this->clave.'/sclilimit/'.$fecha,'scli');
		return $rt;
	}


	function _sinvprec($sucu,$fecha=null){
		set_time_limit($this->timeout);
		$rt='';
		$rt.= $this->__traerzip($sucu,'sincro/exportar/uri/'.$this->clave.'/sinvprec/'.$fecha,'sinvpre');
		if ($this->db->table_exists('sinvcontrol')){
			$rt.= $this->__traerzip($sucu,'sincro/exportar/uri/'.$this->clave.'/sinvcontrol/'.$fecha,'sinvpre');
			logusu('exportar','Importacion sinvprec realizada');
		}
		return $rt;
	}

	function _sinv($sucu,$fecha=null){
		set_time_limit($this->timeout);
		$rt=$this->__traerzip($sucu,'sincro/exportar/uri/'.$this->clave.'/sinv','sinv');
		return $rt;
	}

	function _smov($sucu,$fecha=null){
		set_time_limit($this->timeout);
		$rt=$this->__traerzip($sucu,'sincro/exportar/uri/'.$this->clave.'/smov/'.$fecha,'smov');
		return $rt;
	}

	function _fiscalz($sucu,$fecha=null){
		set_time_limit($this->timeout);
		$rt=$this->__traerzip($sucu,'sincro/exportar/uri/'.$this->clave.'/fiscalz/'.$fecha,'fiscalz');
		return $rt;
	}

	function _sfacfis($sucu,$fecha=null){
		set_time_limit($this->timeout);
		$rt=$this->__traerzip($sucu,'sincro/exportar/uri/'.$this->clave.'/sfacfis/'.$fecha,'sfacfis');
		return $rt;
	}

	function _transa($sucu,$fecha=null){
		set_time_limit($this->timeout);
		$rt=$this->__traerzip($sucu,'sincro/exportar/uri/'.$this->clave.'/transacciones/'.$fecha,'transacciones');
		return $rt;
	}

	function _supertransa($sucu,$fecha=null){
		set_time_limit($this->timeout);
		$rt=$this->__traerzip($sucu,'sincro/exportar/uri/'.$this->clave.'/supertransa/'.$fecha,'supertransa');
		return $rt;
	}

	function _maes($sucu,$fecha=null){
		set_time_limit($this->timeout);
		$rt=$this->__traerzip($sucu,'sincro/exportar/uri/'.$this->clave.'/maes/'.$fecha,'maes');
		return $rt;
	}

	function _rcaj($sucu,$fecha=null){
		set_time_limit($this->timeout);
		$rt=$this->__traerzip($sucu,'sincro/exportar/uri/'.$this->clave.'/rcaj/'.$fecha,'rcaj');
		return $rt;
	}

	function _tranalma($sucu,$fecha=null,$alma=null){
		set_time_limit($this->timeout);
		$rt=$this->__traerzip($sucu,'sincro/exportar/uri/'.$this->clave.'/tranalma/'.$fecha.'/'.$alma,'maesalma');
		return $rt;
	}

	function _maesalma($sucu,$fecha=null,$alma=null){
		set_time_limit($this->timeout);
		$rt=$this->__traerzip($sucu,'sincro/exportar/uri/'.$this->clave.'/maesalma/'.$fecha.'/'.$alma,'maesalma');
		return $rt;
	}

	function _ubicalma($sucu,$fecha=null,$alma=null){
		set_time_limit($this->timeout);
		$rt=$this->__traerzip($sucu,'sincro/exportar/uri/'.$this->clave.'/ubicalma/'.$fecha.'/'.$alma,'maesalma');
		return $rt;
	}


//***********************
//  Metodos de Validacion
//***********************
	function __chekfecha($fecha){
		if(is_numeric($fecha) AND $fecha>10000000){
			$anio=substr($fecha,0,4);
			$mes =substr($fecha,4,2);
			$dia =substr($fecha,6);
			if(checkdate($mes,$dia,$anio))
				return TRUE;
		}
		return FALSE;
	}

//***********************
//  Metodos Generales
//***********************
	function __traerzip($sucu,$dir_url,$iden=null){
		$ssucu = $this->db->escape($sucu);
		$cc    = $this->datasis->dameval("SELECT COUNT(*) FROM sucu WHERE codigo=$ssucu");
		if($cc==0) return "Surursal no existe ($sucu)";
		set_time_limit($this->timeout);
		$this->load->library('Sqlinex');
		$sucu  = $this->db->escape($sucu);

		$query = $this->db->query("SELECT * FROM sucu WHERE codigo=$sucu");
		$fecha = date('Ymd');
		$error='';
		$dir   ='./uploads/traspasos/';

		if ($query->num_rows() > 0){
			$row = $query->row();
			$url=$row->url;
			$url=$row->url.'/'.$row->proteo.'/'.$dir_url;
			$url=reduce_double_slashes($url);
			$c_url='http://'.$url;
			$ch = curl_init($c_url);
			if($this->depura) echo "$c_url \n";
			$tmpfname = tempnam($dir, 'cargagen');

			$fp = fopen($tmpfname, 'w');
			curl_setopt($ch, CURLOPT_FILE, $fp);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			//curl_setopt($ch, CURLOPT_TIMEOUT, 10);

			if(curl_exec($ch) === false){
				$errort='Curl error: '.curl_error($ch);
				//memowrite($url,'importar');
				curl_close($ch);
				fclose($fp);
				unlink($tmpfname);

				return $errort;
			}else{
				$nombre=basename($tmpfname);
				$error=$this->__cargazip($nombre);
			}
			curl_close($ch);
			fclose($fp);

			if(!empty($error) AND $this->geneticket){
				$atts = array(
				    'width'      => '800',
				    'height'     => '600',
				    'scrollbars' => 'yes',
				    'status'     => 'yes',
				    'resizable'  => 'yes',
				    'screenx'    => '0',
				    'screeny'    => '0'
				);

				$link=anchor_popup('sincro/importar/uitrae/'.$iden,'traer manual',$atts);
				$data['padre']      ='S';
				$data['prioridad']  ='5';
				$data['usuario']    ='TRANF';
				$data['contenido']  ="Error en transferencia: Sucursal: $this->sucu, Proceso: $iden, Fecha: $fecha, Mensaje: $error, ".$link;
				$data['estado']     ='N';
				$mSQL = $this->db->insert_string('tiket', $data);

				$this->db->simple_query($mSQL);
			}
		}
		return $error;
	}

	function __cargazip($nombre=null){
		set_time_limit($this->timeout);
		if(empty($nombre)) return 'Nombre vacio';

		//$dir = $this->dir.'/';
		$dir   ='./uploads/traspasos/';
		$zip = new ZipArchive;

		$ban=false;
		$res = $zip->open($dir.$nombre);

		if ($res === TRUE) {
			if($zip->numFiles==2){
				for($i=0;$i<2;$i++){
					if($zip->getNameIndex($i)!='firma.txt'){
						$exp_nombre=$zip->getNameIndex($i);
					}else{
						$ban=true;
					}
				}
				if(!$ban){
					unlink($dir.$nombre);
					return 'Archivo zip no firmado';
				}
				$sucu = $this->sucu;
				$dir_nom=$dir.str_replace('.','_',$nombre).'tmp';

				mkdir($dir_nom,0777);
				$zip->extractTo($dir_nom);
				$zip->close();
				$firma=file_get_contents($dir_nom.'/firma.txt');
				$firma=$this->encrypt->decode($firma);
				$datas=explode($this->sqlinex->separador,$firma);

				if(count($datas)==2){
					$sucursal=$datas[0];
					if($sucu==$sucursal){
						unlink($dir_nom.'/firma.txt');
						unlink($dir_nom.'/'.$exp_nombre);
						if($this->noborra==false)
							unlink($dir.$nombre);
						rmdir($dir_nom);
						return 'No se puede cargar el archivo en la misma sucursal que fue generada';
					}
					$firma = $datas[1];
				}else{
					$firma = $datas[0];
				}
				$firma2=md5_file($dir_nom.'/'.$exp_nombre);
				if($firma!=$firma2){
					unlink($dir_nom.'/firma.txt');
					unlink($dir_nom.'/'.$exp_nombre);
					if($this->noborra==false)
						unlink($dir.$nombre);
					rmdir($dir_nom);
					return 'Firmas no concuerdan';
				}

				$this->sqlinex->import($dir_nom.'/'.$exp_nombre);

				unlink($dir_nom.'/firma.txt');
				unlink($dir_nom.'/'.$exp_nombre);
				if($this->noborra==false)
					unlink($dir.$nombre);
				rmdir($dir_nom);
			}else{
				if($this->noborra==false)
					unlink($dir.$nombre);
				return 'El archivo zip no parece ser de importacion';
			}
		} else {
			if($this->noborra==false)
				unlink($dir.$nombre);
			return 'Error con el archivo zip ('.$res.')';
		}
		return '';
	}
}
