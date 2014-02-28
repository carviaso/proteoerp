<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
//la funcion ejecuta() es la que actualiza de la tabla sinvactu a la tabla prueba
class cargasinv extends Controller{
	var $upload_path;
	function cargasinv(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->load->library("path");
		$this->load->library('encrypt');
		$this->load->helper('string');
		$path=new Path();
		$path->setPath($this->config->item('uploads_dir'));
		$path1=reduce_double_slashes(FCPATH.'/uploads/archivos');


		$path->append('/archivos');
		$this->upload_path =$path->getPath().'/';
	}

	function index(){
		redirect("sincro/cargasinv/carga");
	}

	function carga(){
		$this->rapyd->load("dataform");


		$form = new DataForm("sincro/cargasinv/carga/procesa");
		$form->title('Cargar Archivo de Productos (*.txt)');

		$form->archivo = new uploadField("Archivo","archivo1");
		$form->archivo->upload_path  = $this->upload_path;
		$form->archivo->allowed_types = "TXT";
		$form->archivo->delete_file   =false;
		$form->archivo->rule   ="required";
		$form->archivo->file_name ='precios.txt';

		$form->submit("btnsubmit","Cargar");

		$form->build_form();
		if ($form->on_success()){
			set_time_limit(600);
			$nombre=$form->archivo->upload_data['file_name'];
			$dir   ='./uploads/archivos/'.$nombre;
			$msg='Carga &Eacute;xitosa';
			redirect("sincro/cargasinv/procesa/$nombre");
			//$this->procesa();
		}

		$data['content'] = $form->output;
		$data['title']   = "<h1>Actualizaci&oacute;n de Inventario</h1>";
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function procesa($nombre){
		$atras=site_url('sincro/cargasinv/carga');
		$link=site_url('sincro/cargasinv/deshacer');
		$script ='
		function deshacer(){
			a=confirm("�Esta Seguro que de desea deshacer la ultima actualizaci&oacute;n realizada?");
			if(a){
				$.ajax({
					url: "'.$link.'",
					success: function(msg){
						if(msg){
							alert("Fue realizada exitosamente la operaci&oacute;n");
						}
						else{
							alert("La operaci&oacute;n no pudo ser completada. Intente mas tarde");
						}
					}
				});
			}
		}
		';

		$path1=reduce_double_slashes(FCPATH.'/uploads/archivos/');
		$campos=array();
		$query="TRUNCATE TABLE sinvactu";
		$this->db->query($query);
		$archivo = file($path1.$nombre);
		$i=0;
		foreach($archivo as $linea){
			//$campos[]=explode("\t",$linea);
			//			$campos[]= nl2br($linea);
			$campos[]=$this->parte($linea);
			$codigo= $campos[$i][0];
			$descrip= $campos[$i][1];
			$monto=$campos[$i][2];
			$query="INSERT INTO sinvactu (codigo,descrip,costo) VALUES ('$codigo','$descrip','$monto')";
			$this->db->query($query);
			$i++;
		}
		$eje=$this->ejecuta();
		if($eje==1){
			$msj= "Actualizacion Correcta<br>";
			$msj.='<a href="javascript:deshacer();" title="Haz Click para Deshacer La Ultima Actualizaci&oacute;n" onclick="">Deshacer La Ultima Actualizaci&oacute;n</a>';
		}else	$msj= "No se pudo actualizar inventario";
		//				print("<pre>");
		//				print_r($campos);

		$data['content'] =$msj;
		$data['smenu']="<a href=".$atras.">ATRAS</a>";
		$data['title']   = "<h1>Actualizaci&oacute;n de Inventario</h1>";
		$data["head"]    = script("jquery.pack.js").$this->rapyd->get_head().script($script);
		$this->load->view('view_ventanas', $data);

	}

	function parte($linea){
		$fixed=array(25,52,76);
		$pivot=0;
		foreach($fixed AS $cortes){
			$dbinsert[]=trim(substr($linea,$pivot,$cortes-$pivot));
			$pivot=$cortes;
		}
		return $dbinsert;
	}

	function ejecuta(){
		if($this->respaldo()==1){
			$this->db->select("a.descrip,a.costo,a.codigo");
			$this->db->from("sinvactu AS a");
			$this->db->join("sinv AS b","b.codigo=a.codigo");
			$query = $this->db->get();
			$data=array();
			foreach ($query->result_array() as $row){
				$ban=false;
				$ban3=false;
				$salida=$row['codigo'];
				$update="UPDATE sinv SET";
				$codigo=$row['codigo'];
				$update.=" codigo='$codigo'";

				$descrip=$row['descrip'];
				$update.=", descrip='$descrip'";

				$ultimo=$row['costo'];
				$update.=", ultimo=$ultimo";

				$update.=" WHERE codigo='$codigo'";

				$mSQL2=$this->db->query($update);

			}
			$query="UPDATE sinv SET
					margen1=50.00,
					margen2=37.50,
					margen3=33.33,
					margen4=23.08,

					base1 =IF(formcal='U',ultimo,IF(formcal='P',pond,GREATEST(ultimo,pond)))*100/(100-50.00),
					base2 =IF(formcal='U',ultimo,IF(formcal='P',pond,GREATEST(ultimo,pond)))*100/(100-37.50),
					base3 =IF(formcal='U',ultimo,IF(formcal='P',pond,GREATEST(ultimo,pond)))*100/(100-33.33),
					base4 =IF(formcal='U',ultimo,IF(formcal='P',pond,GREATEST(ultimo,pond)))*100/(100-23.08),
					precio1=(IF(formcal='U',ultimo,IF(formcal='P',pond,GREATEST(ultimo,pond)))*100/(100-50.00))*(1+(iva/100)),
					precio2=(IF(formcal='U',ultimo,IF(formcal='P',pond,GREATEST(ultimo,pond)))*100/(100-37.50))*(1+(iva/100)),
					precio3=(IF(formcal='U',ultimo,IF(formcal='P',pond,GREATEST(ultimo,pond)))*100/(100-33.33))*(1+(iva/100)),
					precio4=(IF(formcal='U',ultimo,IF(formcal='P',pond,GREATEST(ultimo,pond)))*100/(100-23.08))*(1+(iva/100))";
			$this->db->query($query);
			return 1;
		}

	}

	function respaldo(){
		$actualizo=$this->db->query("UPDATE sinvactu AS a
									LEFT JOIN sinv b ON a.codigo = b.codigo
		SET
		a.antdescrip =b.descrip,
		a.antdescrip2=b.descrip2,
		a.antclave   =b.clave,
		a.antgrupo   =b.grupo,
		a.antprecio1 =b.precio1,
		a.antprecio2 =b.precio2,
		a.antprecio3 =b.precio3,
		a.antprecio4 =b.precio4,
		a.antmargen1 =b.margen1,
		a.antmargen2 =b.margen2,
		a.antmargen3 =b.margen3,
		a.antmargen4 =b.margen4,
		a.antbase1   =b.base1,
		a.antbase2   =b.base2,
		a.antbase3   =b.base3,
		a.antbase4   =b.base4,
		a.antcosto   =b.ultimo,
		a.antiva     =b.iva
		");
		return $actualizo;
	}

	function deshacer(){

		$actualizo=$this->db->query("
		UPDATE sinv AS a
		JOIN sinvactu b
		ON a.codigo = b.codigo
		SET
		a.descrip  = b.antdescrip,
		a.descrip2 = b.antdescrip2,
		a.clave    = b.antclave,
		a.grupo    = b.antgrupo,
		a.precio1  = b.antprecio1,
		a.precio2  = b.antprecio2,
		a.precio3  = b.antprecio3,
		a.precio4  = b.antprecio4,
		a.margen1  = b.antmargen1,
		a.margen2  = b.antmargen2,
		a.margen3  = b.antmargen3,
		a.margen4  = b.antmargen4,
		a.base1    = b.antbase1,
		a.base2    = b.antbase2,
		a.base3    = b.antbase3,
		a.base4    = b.antbase4,
		a.ultimo   = b.antcosto,
		a.iva      = b.antiva
		");
		echo $actualizo;
	}


	function instalar(){
//		$mSQL="DROP TABLE sinvactu";
//		$this->db->query($mSQL);
		$mSQL='
			CREATE TABLE `sinvactu` (
  `codigo` varchar(15) NOT NULL default "",
  `descrip` varchar(45) default NULL,
  `clave` varchar(8) default NULL,
  `descrip2` varchar(45) default NULL,
  `antdescrip2` varchar(45) default NULL,
  `grupo` varchar(4) default NULL,
  `costo` decimal(13,2) unsigned default NULL,
  `precio1` decimal(13,2) unsigned default NULL,
  `antcosto` decimal(13,2) unsigned default NULL,
  `antprecio1` decimal(13,2) unsigned default NULL,
  `iva` decimal(6,2) unsigned default NULL,
  `antiva` decimal(6,2) unsigned default NULL,
  `precio2` decimal(13,2) default NULL,
  `precio3` decimal(13,2) default NULL,
  `precio4` decimal(13,2) unsigned default NULL,
  `base1` decimal(13,2) unsigned default NULL,
  `base2` decimal(13,2) default NULL,
  `base3` decimal(13,2) unsigned default NULL,
  `base4` decimal(13,2) unsigned default NULL,
  `margen1` decimal(13,2) unsigned default NULL,
  `margen2` decimal(13,2) unsigned default NULL,
  `margen3` decimal(13,2) unsigned default NULL,
  `margen4` decimal(13,2) unsigned default NULL,
  `antdescrip` varchar(45) default NULL,
  `antclave` varchar(8) default NULL,
  `antgrupo` varchar(4) default NULL,
  `antprecio2` decimal(13,2) unsigned default NULL,
  `antprecio3` decimal(13,2) unsigned default NULL,
  `antprecio4` decimal(13,2) unsigned default NULL,
  `antbase1` decimal(13,2) unsigned default NULL,
  `antbase2` decimal(13,2) unsigned default NULL,
  `antbase3` decimal(13,2) unsigned default NULL,
  `antbase4` decimal(13,2) unsigned default NULL,
  `antmargen1` decimal(13,2) unsigned default NULL,
  `antmargen2` decimal(13,2) unsigned default NULL,
  `antmargen3` decimal(13,2) unsigned default NULL,
  `antmargen4` decimal(13,2) unsigned default NULL,
  PRIMARY KEY  (`codigo`)
)
	';
		$this->db->query($mSQL);
	}


}
?>
