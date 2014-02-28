<?php
/** 
 * ProteoERP 
 * 
 * @autor    Andres Hocevar 
 * @license  GNU GPL v3
*/

class Fotos extends Controller {
	var $upload_path;
	function Fotos(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('path');
		$path=new Path();
		$path->setPath($this->config->item('uploads_dir'));
		$path->append('/inventario/Image');
		$this->upload_path =$path->getPath().'/';
	}

	function index(){
		redirect('inventario/fotos/filteredgrid/index');
	}

	function filteredgrid(){
		$this->datasis->modulo_id(310,1);
		$this->rapyd->load("datafilter2","datagrid");
		$this->rapyd->uri->keep_persistence();

		//rapydlib("prototype");
		$ajax_onchange = '
			  function get_linea(){
			    var url = "'.site_url('reportes/sinvlineas').'";
			    var pars = "dpto="+$F("depto");
			    var myAjax = new Ajax.Updater("td_linea", url, { method: "post", parameters: pars });
			  }
			  function get_grupo(){
			    var url = "'.site_url('reportes/sinvgrupos').'";
			    var pars = "dpto="+$F("depto")+"&linea="+$F("linea");
			    var myAjax = new Ajax.Updater("td_grupo", url, { method: "post", parameters: pars });
			  }';

		$mSPRV=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
			'proveed' =>'C&oacute;digo',
			'nombre'=>'Nombre',
			'contacto'=>'Contacto'),
			'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed'),
			'titulo'  =>'Buscar Proveedor');

		$bSPRV=$this->datasis->modbus($mSPRV);

		$filter = new DataFilter2("Filtro por Producto");

		if ($this->input->post("fotos"))
			$ddire='';
		else
			$ddire='left';

		$filter->db->select("a.codigo as scodigo,a.descrip,a.grupo,b.codigo,a.id, a.marca, a.precio1,a.precio2,a.precio3,a.precio4");
		$filter->db->from("sinv AS a");
		$filter->db->join("sinvfot AS b","a.codigo=b.codigo",$ddire);
		$filter->db->groupby("a.codigo");
		$filter->script($ajax_onchange);

		$filter->codigo = new inputField("C&oacute;digo", "a.codigo");
		$filter->codigo->size=15;

		$filter->clave = new inputField("Clave", "clave");
		$filter->clave->size=15;

		$filter->proveed = new inputField("Proveedor", "proveed");
		$filter->proveed->append($bSPRV);
		$filter->proveed->clause ="in";
		$filter->proveed->db_name='( a.prov1, a.prov2, a.prov3 )';
		$filter->proveed->size=15;

		$filter->descrip = new inputField("Descripci&oacute;n", "a.descrip");
		$filter->descrip->db_name='CONCAT_WS(" ",descrip,descrip2)';
		$filter->descrip->size=34;

		$filter->dpto = new dropdownField("Departamento", "depto");
		$filter->dpto->clause='';
		$filter->dpto->option("","");
		$filter->dpto->options("SELECT depto, descrip FROM dpto ORDER BY descrip");
		$filter->dpto->onchange = "get_linea();";
		//$filter->dpto->style = "width:220px";

		$filter->linea = new dropdownField("Linea","linea");
		$filter->linea->clause='';
		$filter->linea->option("","Seleccione un departamento");
		$filter->linea->onchange = "get_grupo();";
		//$filter->linea->style = "width:300px";

		$filter->grupo = new dropdownField("Grupo","grupo");
		$filter->grupo->db_name="a.grupo";
		$filter->grupo->option("","Seleccione una Linea");
		//$filter->grupo->style = "width:220px";

		$filter->marca = new dropdownField("Marca", "marca");
		$filter->marca->option("","");
		$filter->marca->options("SELECT TRIM(marca) AS clave, TRIM(marca) AS valor FROM marc ORDER BY marca");
		$filter->marca->style = "width:140px";

		$filter->fotos = new checkboxField("Mostrar solo productos con fotos", "fotos", "y","n");
		$filter->fotos->clause='';
		$filter->fotos->insertValue = "n";

		$filter->buttons("reset","search");
		$filter->build('dataformfiltro');

		$grid = new DataGrid("Lista de Art&iacute;culos");
		$grid->order_by("a.codigo","asc");
		$grid->per_page = 50;
		$link=anchor('/inventario/fotos/dataedit/<#id#>/create/','<#scodigo#>');

		$grid->use_function('str_replace');
		$grid->column("C&oacute;digo",$link);
		$grid->column("Descripci&oacute;n",'descrip');
		$grid->column("Precio 1","<nformat><#precio1#></nformat>",'align=Right');
		$grid->column("Precio 2","<nformat><#precio2#></nformat>",'align=Right');
		$grid->column("Precio 3","<nformat><#precio3#></nformat>",'align=Right');
		$grid->column("Precio 4","<nformat><#precio4#></nformat>",'align=Right');

		$grid->build('datagridST');
		//echo $grid->db->last_query();


		//************ SUPER TABLE *************
		$extras = '<script type="text/javascript">
		//<![CDATA[
		(function() {
			var mySt = new superTable("demoTable", {
			cssSkin : "sSky",
			fixedCols : 1,
			headerRows : 1,
			onStart : function () {	this.start = new Date();},
			onFinish : function () {document.getElementById("testDiv").innerHTML += "Finished...<br>" + ((new Date()) - this.start) + "ms.<br>";}
			});
		})();
		//]]>
		</script>';
		$style ='
		<style type="text/css">
		.fakeContainer { /* The parent container */
		    margin: 5px;
		    padding: 0px;
		    border: none;
		    width: 740px; /* Required to set */
		    height: 320px; /* Required to set */
		    overflow: hidden; /* Required to set */
		}
		</style>';
		//****************************************

		$data['content'] = $grid->output;
		$data['filtro']  = $filter->output;

		$data['script']  = script('jquery.js');

		$data['head']    = $this->rapyd->get_head();
		$data['style']   = $style;

		$data['extras']  = $extras;

		$data['title']   = '<h1>Lista de Art&iacute;culos para Fotos </h1>';
		$this->load->view('view_ventanas', $data);
	}

	function dataedit($id=NULL){
		$this->datasis->modulo_id(310,1);
		$this->rapyd->uri->keep_persistence();
		if($id!=NULL OR $id!='create' OR $id!='show')
			$codigo=$this->datasis->dameval("SELECT codigo FROM sinv WHERE id=$id");

		$this->rapyd->load("dataedit");
		$sinv=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
			'codigo'  =>'C&oacute;digo',
			'descrip' =>'Descripci&oacuten'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'descrip'),
			'retornar'=>array('codigo'=>'mcod'),
			'titulo'  =>'Buscar Art&iacute;culo',
			'script'=>array('agregar()'));
		$bSINV=$this->datasis->modbus($sinv);

		$edit = new DataEdit('Fotos de Inventario', 'sinvfot');
		$edit->pre_process( 'insert','_pre_insert');
		$edit->pre_process( 'update','_pre_modifi');
		$edit->post_process('delete','_post_delete');
		$edit->back_url = site_url('inventario/fotos/filteredgrid/filteredgrid');

		$edit->codigo = new inputField('C&oacute;digo','codigo');
		$edit->codigo->size=30;
		$edit->codigo->rule = 'required';
		$edit->codigo->append($bSINV.'Seleccione todos los c&oacute;digos asociados a la foto separados por punto y coma (;)');
		$edit->codigo->insertValue = $codigo;

		$edit->foto = new uploadField('Imagen', 'nombre');
		$edit->foto->rule          = 'required';
		$edit->foto->upload_path   = $this->upload_path;
		$edit->foto->allowed_types = 'jpg';
		$edit->foto->delete_file   =false;
		$edit->foto->append('Solo imagenes JPG');
		$edit->foto->file_name = url_title($codigo).'_.jpg';

		/*$edit->url = new inputField('Direcci&oacute;n Web','url');
		$edit->url->size=30;
		$edit->url->rule = 'condi_required|callback_chfoto';
		$edit->url->append('Coloque la direccion URL si la foto viene de internet');
		$edit->url->when=array('create');*/

		$edit->principal = new dropdownField('Es foto principal','principal');
		$edit->principal->option('N','No');
		$edit->principal->option('S','Si');
		$edit->principal->style = 'width:50px';
		$edit->principal->rule='required|callback_principal';

		$edit->evaluacion = new textareaField("Comentario", "comentario");
		$edit->evaluacion->rows = 6;
		$edit->evaluacion->cols=70;
		//$edit->evaluacion->when=array('show');

		$edit->iframe = new iframeField("related", "inventario/fotos/verfotos/$id","210");
		$edit->iframe->when= array("create");

		$pk=$edit->_dataobject->pk;
		$pk=$pk['id'];
		$edit->miframe = new iframeField("related", "inventario/fotos/asocfotos/$pk","210");
		$edit->miframe->when = array('modify','show');

		$edit->buttons('modify', 'save','delete');
		$edit->build();

		$fhidden = array(
			'name' => 'mcod',
			'id'   => 'mcod',
			'type' => 'hidden');

		$data['script']  ='<script language="javascript" type="text/javascript">
			function agregar(){
				add=document.getElementById("mcod").value;
				codigo=document.getElementById("codigo");
				if (add.length>0)
					codigo.value=codigo.value+";"+add;
				else
					codigo.value=add;
			}
		</script>';
		$dbcodigo= $this->db->escape($codigo);
		$descrip = $this->datasis->dameval("SELECT descrip FROM sinv WHERE codigo=$dbcodigo");
		$gs=$this->googlesearch($descrip);
		if(count($gs)>0){
			$imgs='<p style="text-align:center"><b>Imagenes proporcionadas por Google</b>'.br();
			foreach($gs as $g){
				$imgs.='<a href="'.$g['url'].'" target="_blank">'.img($g['tbUrl'],true).'</a>';
			}
			$imgs.='</p>';
		}else{
			$imgs='';
		}

		$data['content'] = form_input($fhidden).$edit->output.$imgs;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Carga de Fotos');
		$this->load->view('view_ventanas', $data);
	}

	function chfoto($val){
		$file=$_FILES['nombreUserFile']['name'];
		if(empty($val) && empty($file)){
			$this->validation->set_message('chfoto','El campo %s es requerido en este caso');
			return false;
		}
		return true;
	}

	function asocfotos($id=''){
		$this->rapyd->config->set_item('theme','clean');
		$this->rapyd->load('datagrid');
		$dbid=$this->db->escape($id);

		$nombre='';
		$sinv_id='';
		$query = $this->db->query("SELECT nombre,sinv_id FROM sinvfot WHERE id=$dbid");
		if ($query->num_rows() > 0){
			$row = $query->row();
			$nombre  = $row->nombre;
			$sinv_id = $row->sinv_id ;
		}

		$grid = new DataGrid('Lista de Art&iacute;culos');
		$grid->db->select(array('a.codigo','a.id','a.estampa','b.descrip','b.precio1','b.precio2','b.precio3','b.precio4'));
		$grid->db->from('sinvfot AS a');
		$grid->db->join('sinv AS b','a.codigo=b.codigo');
		$grid->db->where('nombre',$nombre);
		$grid->order_by('codigo','asc');
		$grid->per_page = 15;
		$link='<a href="'.site_url("/inventario/fotos/dataedit/$sinv_id/show/<#id#>").'" target="_parent"><#codigo#></a>';

		$grid->use_function('str_replace');
		$grid->column('C&oacute;digo',$link);
		$grid->column('Descripci&oacute;n','descrip');
		$grid->column('Precio 1','precio1');
		$grid->column('Precio 2','precio2');
		$grid->column('Precio 3','precio3');
		$grid->column('Precio 4','precio4');
		$grid->build();
		$grid->db->last_query();

		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = '';
		$this->load->view('view_ventanas_sola', $data);
	}

	function googlesearch($q){
		$url = "https://ajax.googleapis.com/ajax/services/search/images?v=1.0&q=".urlencode($q);
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_REFERER, 'www.proteoerp.org');
		$body = curl_exec($ch);
		curl_close($ch);

		$json = json_decode($body);
		$results=$json->responseData->results;

		$rt=array();
		if(count($results)>0){
			foreach($results as $id=>$result){
				$rt[$id]['url']    = $result->url;
				$rt[$id]['tbUrl']  = $result->tbUrl;
				$rt[$id]['titulo'] = $result->titleNoFormatting;
				$rt[$id]['ancho']  = $result->width;
		        $rt[$id]['alto']   = $result->height;
			}
		}
		return $rt;
	}

	function verfotos($sinv_id){
		$this->rapyd->config->set_item('theme','clean');
		$this->rapyd->load('datatable');

		$table = new DataTable(null);
		$table->cell_attributes = 'style="vertical-align:middle; text-align: center;"';

		$table->db->select(array('nombre','id'));
		$table->db->from('sinvfot');
		$table->db->where('sinv_id',$sinv_id);

		$table->per_row = 4;
		$table->per_page = 16;
		$table->cell_template = "<a href='".site_url("/inventario/fotos/dataedit/$sinv_id/show/<#id#>")."' target='_parent' ><img src='".site_url('inventario/fotos/mostrar/<#nombre#>')."'  width=150 border=0></a>";
		$table->build();

		$data['content'] = $table->output;
		$data['title']   = '';
		$data['head']    = style("ventanas.css").style("estilos.css").$this->rapyd->get_head();
		$this->load->view('view_ventanas_sola', $data);
	}

	function _pre_insert($do){
		$codigos= explode(';',$do->get('codigo'));
		//$url    = $do->get('url');
		//$foto   = file_get_contents($url);

		$id=$this->datasis->dameval("SELECT id FROM sinv WHERE codigo='$codigos[0]'");
		$do->set('codigo' , $codigos[0]);
		$do->set('ruta'   , $this->upload_path);
		$do->set('sinv_id', $id);
		$c=false;
		foreach($codigos AS $codigo){
			if($c){
				$id=$this->datasis->dameval("SELECT id FROM sinv WHERE codigo='$codigo'");
				$mSQL="INSERT INTO sinvfot (sinv_id,codigo,nombre,ruta,comentario) VALUES (?,?,?,?,?)";
				$this->db->query($mSQL, array($id, $codigo, $do->get('nombre'),$this->upload_path,$do->get('comentario')));
			}else{
				$c=true;
			}
		}
		$do->rm_get('url');
	}

	function _pre_modifi($do){
		$codigos=explode(';',$do->get('codigo'));
		$nombre=$do->get('nombre');
		$id=$this->datasis->dameval("SELECT id FROM sinv WHERE codigo='$codigos[0]'");
		$do->set('codigo' , $codigos[0]);
		$do->set('ruta'   , $this->upload_path);
		$do->set('sinv_id', $id);
		$c=false;
		foreach($codigos AS $codigo){
			if($c){
				$cant=$this->datasis->dameval("SELECT COUNT(*) FROM sinvfot WHERE codigo='$codigo' AND nombre='$nombre'");
				if($cant==0){
					$id=$this->datasis->dameval("SELECT id FROM sinv WHERE codigo='$codigo'");
					$mSQL="INSERT INTO sinvfot (sinv_id,codigo,nombre,ruta,comentario) VALUES (?,?,?,?,?)";
					$this->db->query($mSQL, array($id, $codigo, $do->get('nombre'),$this->upload_path,$do->get('comentario')));
				}
			}else{
				$c=true;
			}
		}
	}

	function ver($id){
		$this->rapyd->load('datatable');

		$table = new DataTable(null);
		$table->cell_attributes = 'style="vertical-align:middle; text-align: center;"';

		$table->db->select(array('nombre','comentario'));
		$table->db->from('sinvfot');
		$table->db->where('sinv_id',$id);

		$table->per_row = 1;
		$table->per_page = 1;
		$table->cell_template = "<img src='".$this->upload_path."<#nombre#>' width='300' border=0><br><#comentario#>";
		$table->build();

		$data['content'] = '<center>'.$table->output.'</center>';
		$data['title']   = '';
		$data['head']    = style('ventanas.css').style('estilos.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas_sola', $data);
	}

	//****************************
	//Firma las imagenes con MD5
	//****************************
	function _firmar(){
		if($this->db->field_exists('md5sum','sinvfot')){
			$mSQL="ALTER TABLE `sinvfot`  ADD COLUMN `md5sum` VARCHAR(50) NULL DEFAULT NULL AFTER `principal`";
			$this->db->simple_query($mSQL);
		}
		$nombre=$this->datasis->dameval("SELECT id, nombre FROM sinvfot");
		$this->db->select(array('id','nombre'));
		$this->db->from('sinvfot');
		$query = $this->db->get();

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$dir='..'.$this->upload_path.$row->nombre;
				if(file_exists($dir)){
					$image = imagecreatefromjpeg($dir);
					$oancho= imagesx($image);
					$oalto = imagesy($image);
					imagedestroy($image);
					$data['md5sum']  = md5_file($dir);
					$data['alto_px'] = $oalto;
					$data['ancho_px']= $oancho;
					$mSQL = $this->db->update_string('sinvfot', $data,'id='.$row->id);
				}else{
					$mSQL = 'UPDATE sinvfot SET md5sum=NULL WHERE id='.$row->id;
				}
				$this->db->simple_query($mSQL);
			}
		}
	}

	//****************************
	//Elimina las fotos repetidas
	//****************************
	function limpiar(){
		$this->_firmar();
		$mSQL="SELECT COUNT(*) AS cana, GROUP_CONCAT(CONCAT_WS('--',id,nombre) ORDER BY id) AS ids FROM sinvfot WHERE md5sum IS NOT NULL GROUP BY md5sum HAVING cana >1";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$dir='..'.$this->upload_path;
				$ids=explode(',',$row->ids);
				$fot='';
				foreach($ids AS $idt){
					$pivot=explode('--',$idt);
					$id   =$pivot[0];
					if(empty($fot)){
						$fot=$pivot[1];
						continue;
					}else{
						if($fot!=$pivot[1]){
							$data['nombre']=$fot;
							$mSQL = $this->db->update_string('sinvfot', $data,'id='.$id);
							echo $mSQL.br();
							$ban=$this->db->simple_query($mSQL);
							if($ban==false) echo "ERROR ".$mSQL.br();
							if(file_exists($dir.$pivot[1]))
								unlink($dir.$pivot[1]);
						}
					}
				}
			}
		}
	}

	//********************************
	// Sede una imagen miniatura del
	// producto
	// id = id de sinv
	//********************************
	function thumbnail($id){
		$dbid=$this->db->escape($id);
		$nombre=$this->datasis->dameval("SELECT nombre FROM sinvfot WHERE sinv_id=$dbid ORDER BY principal DESC LIMIT 1");
		if(!empty($nombre)) $this->_creathum($nombre);
		$this->mostrar('th_'.$nombre);
	}

	// Crea el Thumbnail
	function _creathum($nombre){
		$path=new Path();
		$path->setPath($_SERVER['DOCUMENT_ROOT']);
		$path->append($this->upload_path);

		$vert  =true;//Para fijar el ancho
		$medida=300;

		$tm=$path->getPath().'/th_'.$nombre;
		$or=$path->getPath().'/'.$nombre;
		if (!file_exists($tm) && file_exists($or)){
			$image = imagecreatefromjpeg($or);
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
			imagejpeg($im,$tm);
		}
	}

	//******************************************
	// Envia Comentarios de la foto Principal
	// id = id de sinv
	//******************************************
	function comenta($id){
		$dbid=$this->db->escape($id);
		$comenta = $this->datasis->dameval("SELECT comentario FROM sinvfot WHERE sinv_id=$dbid ORDER BY principal DESC LIMIT 1");
		echo $comenta;
	}




	//********************************
	// Sede la imagen del producto
	// id = id de sinv
	//********************************
	function obtener($id){
		$nombre=$this->datasis->dameval("SELECT nombre FROM sinvfot WHERE sinv_id='$id' limit 1");
		$this->mostrar($nombre);
	}

	// Devuelve l foto como imagen
	function mostrar($nombre){
		$path=new Path();
		$path->setPath($_SERVER['DOCUMENT_ROOT']);
		$path->append($this->upload_path);
		$path->append($nombre);

		if (!empty($nombre) AND file_exists($path->getPath())){
			header('Content-type: image/jpg');
			$data = file_get_contents($path->getPath());
		}else{
			header('Content-type: image/jpg');
			$path=new Path();
			$path->setPath($_SERVER['DOCUMENT_ROOT']);
			$path->append($this->config->item('base_url'));
			$path->append('images/ndisp.jpg');
			$data = file_get_contents($path->getPath());
		}
		echo $data;
	}

	function principal($codigo){
		//$id=$this->input->post('id');
		$codigo=$this->input->post('codigo');
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM sinvfot WHERE codigo='$codigo' and principal='S'");
		if ($check > 0){
			$mSQL_1=$this->db->query("SELECT id FROM sinvfot WHERE codigo='$codigo' and principal='S'");
			$row = $mSQL_1->row();
			$ids =$row->id;
			$mSQL_2=$this->db->query("UPDATE sinvfot SET principal='N' WHERE id='$ids'");
			//return FALSE;
		}else {
			return TRUE;
		}
	}

	function _post_delete($do){
		$nombre=$do->get('nombre');
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM sinvfot WHERE nombre='$nombre'");
		if($check<=0){
			$path=new Path();
			$path->setPath($_SERVER['DOCUMENT_ROOT']);
			$path->append($this->upload_path);
			$path->append($nombre);
			unlink($path->getPath());
		}
	}

	function traerfotos($sucu=null){
		set_time_limit(1600);
		$this->load->helper('string');
		if(empty($sucu)) $sucu='01';

		$query = $this->db->query("SELECT * FROM sucu WHERE codigo=$sucu");
		$msg='';
		if ($query->num_rows() > 0){
			$row = $query->row();

			$url=$row->url;
			$url=$row->url.'/'.$row->proteo.'/uploads/inventario/Image/';

			$mSQL='SELECT nombre FROM sinvfot';
			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach ($query->result() as $row){
					$filename='../'.$this->upload_path.$row->nombre;
					$filename=reduce_double_slashes($filename);
					if (!file_exists($filename)) {
						$uurl=$url.'/'.$row->nombre;
						$uurl=reduce_double_slashes($uurl);

						$fp = fopen($filename, "w");
						$ch = curl_init('http://'.$uurl);
						curl_setopt($ch, CURLOPT_FILE, $fp);
						curl_setopt($ch, CURLOPT_HEADER, 0);
						curl_exec($ch);

						curl_close($ch);
						fclose($fp);

						$cont=file_get_contents($filename);

						$msg .= "Descargado $filename <br>";
					}
				}
			}
		}else{
			$msg= 'Error Sucursal no existe '.$sucu;
		}

		$data['content'] = $msg;
		$data['title']   = '<h1>Descarga de fotos de inventario</h1>';
		$data['script']  = '';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function instalar(){
		$mSQL='CREATE TABLE IF NOT EXISTS `sinvfot` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `codigo` varchar(15) default NULL,
		  `nombre` varchar(50) default NULL,
		  `alto_px` smallint(5) unsigned default NULL,
		  `ancho_px` smallint(6) default NULL,
		  `ruta` varchar(100) default NULL,
		  `comentario` text,
		  `estampa` timestamp NULL default NULL,
		  PRIMARY KEY  (`id`),
		  UNIQUE KEY `id` (`id`),
		  UNIQUE KEY `foto` (`codigo`,`nombre`),
		  KEY `id_2` (`id`,`codigo`)
		) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sinvfot` ADD `sinv_id` INT UNSIGNED NOT NULL AFTER `id`';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sinvfot` ADD INDEX `sinv_id` (`sinv_id`)';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sinvfot` CHANGE `estampa` `estampa` TIMESTAMP NOT NULL';
		$this->db->simple_query($mSQL);
		$mSQL='UPDATE sinvfot AS a JOIN sinv AS b ON a.codigo=b.codigo SET a.sinv_id=b.id';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sinvfot` ADD `principal` VARCHAR(3) NULL';
		$this->db->simple_query($mSQL);
	}
}
