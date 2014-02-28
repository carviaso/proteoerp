<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class extimpor extends Controller {
	var $url ='sincro/extimpor/';

	function extimpor(){
		parent::Controller();
		$this->datasis->modulo_id(927,1);
		$this->load->library('rapyd');
		$this->titulo   = 'Tabla importada';
		$this->tabla    = 'impor_data';
		$this->val_error= '';
		$this->afecta = 0;
	}

	function index(){
		$this->instalar();
		redirect($this->url.'load');
	}

	function procesar($id_tabla=null){
		$this->rapyd->load('dataedit','datagrid','fields');
		if(empty($id_tabla))  { show_error('Faltan parametros');   }
		$id_tabla = intval($id_tabla);
		if($id_tabla<=0){ show_error('Error en parametros'); }

		$tabla    = $this->tabla;
		$select   = $titu=array();

		//Se trae la cantidad de columnas
		$dbide_tabla = $this->db->escape($id_tabla);
		$ccolum=$this->datasis->dameval("SELECT MAX(aa.cana) FROM (SELECT COUNT(*) AS cana FROM ${tabla} WHERE id_tabla=${dbide_tabla} GROUP BY fila) AS aa");

		//Se trae la definicion de la tabla
		//$query = $this->db->query("SELECT columna,valor,tipo FROM `impor_data` WHERE fila=0 AND id_tabla=${dbide_tabla}");
		//if($query->num_rows()>0){
		//	foreach ($query->result() as $row){
		//		$titu[$row->columna]=$row->valor;
		//	}
		//}

		$options = array(
			''        => 'Ignorar',
			'codigo'  => 'Código',
			'descrip' => 'Descripción',
			//'ultimo'  => 'Costo Último',
			//'pond'    => 'Costo Promedio',
			'standard'=> 'Costo Estandar',
			'exmin'   => 'Mínimo',
			'exmax'   => 'Máximo',
			'marca'   => 'Marca',
			//'margen'  => 'Promocion',
			//'precio1' => 'Precio 1', //Desarrollar validaciones
			//'precio2' => 'Precio 2', //Desarrollar validaciones
			//'precio3' => 'Precio 3', //Desarrollar validaciones
			//'precio4' => 'Precio 4', //Desarrollar validaciones
			//'base1'   => 'Base 1',   //Desarrollar validaciones
			//'base2'   => 'Base 2',   //Desarrollar validaciones
			//'base3'   => 'Base 3',   //Desarrollar validaciones
			//'base4'   => 'Base 4',   //Desarrollar validaciones

		);

		$colunma = new dropdownField('Tabla', 'tabla');
		$colunma->options($options );
		$colunma->status= 'create';
		$colunma->style = 'width:100%;';
		$colunma->rule  = 'required';

		$grid = new DataGrid('Tabla importada');
		$grid->db->from($tabla);
		$grid->db->where('id_tabla',$id_tabla);
		//$grid->db->where('fila >',0);
		$grid->db->groupby('fila');
		$grid->db->orderby('fila');
		$grid->per_page = 25;

		$select[]='(fila+1) AS fila';
		$grid->column('Fila','<b><#fila#></b>','align="right"');
		for($i=0;$i<$ccolum;$i++){
			$select[]="GROUP_CONCAT(IF(columna=${i},valor,NULL)) AS c${i}";
			//$titulo=(isset($titu[$i]))? $titu[$i]: 'Columna '.($i+1);

			$colunma->name=$colunma->id="c${i}";
			$colunma->build();
			$titulo='Columna '.($i+1).$colunma->output;
			$grid->column_orderby($titulo,"c${i}","c${i}");
		}
		$grid->db->select($select);

		$action = "javascript:window.location='".site_url($this->url.'load')."'";
		$grid->button('btn_regresa', 'Regresar', $action, 'TR');
		$grid->build();
		//echo $grid->db->last_query();

		$form = new DataForm($this->url.'procesar/'.$id_tabla.'/process');

		$form->tabla = new dropdownField('Tabla', 'tabla');
		$form->tabla->option('sinv'     ,'Inventario' );
		//$form->tabla->option('sinvpromo','Promociones');
		$form->tabla->rule = 'required|enum[sinv,sinvpromo]';

		$form->fila = new inputField('Procesar a partir de la fila', 'fila');
		$form->fila->append(' Incluyente.');
		$form->fila->size = 5;
		$form->fila->insertValue = '1';
		$form->fila->rule = 'numeric|positive';

		$form->errores = new checkboxField('Ignorar Errores', 'errores', 'S','N');
		$form->errores->insertValue = 'N';
		$form->errores->rule = 'enum[S,N]';

		$form->container = new containerField('tabla',$grid->output);

		$form->submit('btnsubmit','Procesar');
		$form->build_form();

		if($form->on_success()){
			$def = array();
			$val = false;
			for($o=0;$o<$i;$o++){
				$itdef=$this->input->post('c'.$o);
				if(!empty($itdef)){
					if(!in_array($itdef,$def)){
						$def[$o]=$itdef;
					}else{
						$val = true;
						$form->error_string ='Columna '.($o+1).' tiene la defición repetida.';
						$form->build_form();
						break;
					}
				}
			}

			if(!$val){
				$rt = $this->_validar($form->tabla->newValue,$id_tabla,$def,intval($form->fila->newValue),$form->errores->newValue);
				if(!$rt){
					$form->error_string =$this->val_error;
					$form->build_form();
				}else{
					$rt=$this->_procesar($form->tabla->newValue,$id_tabla,$def);
					if($rt){
						redirect($this->url.'resultado/'.$this->afecta);
					}else{
						$form->error_string = 'Hubo problemas para cagar la data.';
						$form->build_form();
					}
				}
			}
		}

		$data['content'] = $form->output;
		$data['title']   = heading('Tabla Importada');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function _procesar($tabla,$idtabla,$def){
		$ttabla = $this->ttabla;
		$pos    = array_flip($def);

		if($tabla=='sinv'){
			unset($def[$pos['codigo']]);

			//Guarda los valores anteriores
			$sel=array('a.fila');
			foreach($def as $campo){
				$sel[]="a.${campo}";
				$sel[]="b.${campo} AS sinv${campo}";
			}
			$this->db->select($sel);
			$this->db->from($ttabla.' AS a');
			$this->db->join('sinv AS b','a.codigo=b.codigo');
			$query = $this->db->get();
			foreach ($query->result() as $row){
				foreach($def as $columna=>$campo){
					$obj   = 'sinv'.$campo;
					$where = "columna = ${columna} AND fila = ".$row->fila." AND id_tabla = ${idtabla}";
					$mSQL  = $this->db->update_string($this->tabla, array('anterior' => $row->$obj), $where);
					$this->db->simple_query($mSQL);
				}
			}
			//Fin del respaldo de los valores anteriores

			//Pasa la data
			$cprecio=false;
			$set=array();
			foreach($def as $campo){
				if('ultimo'==$campo || 'pond'==$campo || 'standard'==$campo){
					$set[]="a.${campo} = ROUND(b.${campo},2)";
					$cprecio=true;
				}else{
					$set[]="a.${campo} = b.${campo}";
				}
			}

			if($cprecio){
				$cultimo  = 'a.ultimo';
				$cpond    = 'a.pond';
				$cstandard= 'a.standard';
				if(in_array('ultimo',$def)){
					$cultimo= 'b.ultimo';
					$costo  = 'b.ultimo';
					$set[]  = "a.formcal = 'U'";
				}
				if(in_array('pond',$def)){
					$cpond = 'b.pond';
					$costo = 'b.pond';
					$set[] = "a.formcal = 'P'";
				}
				if(in_array('standard',$def)){
					$cstandard= 'b.standard';
					$costo    = 'b.standard';
					$set[]    = "a.formcal = 'S'";
				}

				//$costo = "ROUND(IF(a.formcal='U',${cultimo},IF(a.formcal='P',${cpond},IF(a.formcal='S',${cstandard},GREATEST(${cpond},${cultimo})))),2)";
				$base1 = "ROUND(${costo}*100/(100-a.margen1),2)";
				$base2 = "ROUND(${costo}*100/(100-a.margen2),2)";
				$base3 = "ROUND(${costo}*100/(100-a.margen3),2)";
				$base4 = "ROUND(${costo}*100/(100-a.margen4),2)";

				$set[]="a.base1=${base1}";
				$set[]="a.base2=${base2}";
				$set[]="a.base3=${base3}";
				$set[]="a.base4=${base4}";
				$set[]="a.precio1=ROUND(${base1}*(1+(a.iva/100)),2)";
				$set[]="a.precio2=ROUND(${base2}*(1+(a.iva/100)),2)";
				$set[]="a.precio3=ROUND(${base3}*(1+(a.iva/100)),2)";
				$set[]="a.precio4=ROUND(${base4}*(1+(a.iva/100)),2)";
			}

			$mSQL="UPDATE sinv AS a JOIN ${ttabla} AS b ON a.codigo=b.codigo SET ".implode(',',$set);
			$ban=$this->db->simple_query($mSQL);
			$this->afecta=$this->db->affected_rows();

			if(in_array('marca',$def)){
				$mSQL= "INSERT IGNORE INTO marc (marca,margen) SELECT TRIM(marca),0 FROM sinv GROUP BY marca";
				$ban=$this->db->simple_query($mSQL);
			}

			return $ban;
		}else{
			$this->pros_error='Tabla no valida.';
			return false;
		}

	}

	function _validar($tabla,$idtabla,$def,$apartir,$ignorar){
		if($tabla=='sinv'){
			if(!in_array('codigo',$def)){
				$this->val_error='Debe tener al menos una columna de codigo.';
				return false;
			}

			if(count($def)<2){
				$this->val_error='Debe tener al menos una columna de data.';
				return false;
			}

			$campos=$this->db->list_fields($tabla);

			$select=array();
			foreach($def as $i=>$nombre){
				if(!in_array($nombre,$campos)){
					continue;
					//$this->val_error="La columna ${nombre} no exiten en la tabla ${tabla}.";
					//return false;
				}
				$select[]="GROUP_CONCAT(IF(columna=${i},TRIM(valor),NULL)) AS ${nombre}";
			}

			if($apartir>0) $ww='AND fila > '.($apartir-1); else $ww='';
			$content_id = md5(uniqid(time()));
			$ttabla = $tabla.'_'.$content_id;
			$this->ttabla = $ttabla;
			$mSQL  = "CREATE TEMPORARY TABLE ${ttabla} (codigo VARCHAR(15)  NOT NULL) ";
			$mSQL .= 'SELECT '.implode(',',$select).',fila FROM '.$this->tabla." WHERE id_tabla=${idtabla} ${ww} GROUP BY fila ORDER BY fila";
			$this->db->simple_query($mSQL);

			$mSQL = "DELETE FROM ${ttabla} WHERE codigo IS NULL OR LENGTH(codigo)=0";
			$query = $this->db->query($mSQL);

			$error=false;
			$mSQL="SELECT codigo, COUNT(*) AS cana FROM ${ttabla} GROUP BY codigo HAVING cana>1";
			$query = $this->db->query($mSQL);
			foreach($query->result() as $row){
				if($ignorar=='S'){
					$dbcodigo=$this->db->escape($row->codigo);
					$mSQL="DELETE FROM ${ttabla} WHERE codigo=${dbcodigo}";
					$ban=$this->db->simple_query($mSQL);
				}else{
					$this->val_error .= 'El código '.$row->codigo.' esta repetido '.$row->cana.".<br>";
					$error=true;
				}
			}

			$cnumero = array('ultimo','pond','standard');
			foreach($cnumero as $colum){
				if(in_array($colum,$def)){
					$mSQL="SELECT codigo  FROM ${ttabla} WHERE ${colum} NOT REGEXP '^[0-9\.]+$'";
					$query = $this->db->query($mSQL);
					foreach($query->result() as $row){
						if($ignorar=='S'){
							$dbcodigo=$this->db->escape($row->codigo);
							$mSQL="DELETE FROM ${ttabla} WHERE codigo=${dbcodigo}";
							$ban=$this->db->simple_query($mSQL);
						}else{
							$this->val_error .= 'El código "'.$row->codigo.'" tiene un valor no apto para el costo.<br>';
							$error=true;
						}
					}
				}
			}

			$ww = array();
			if(!$error){
				$mSQL="ALTER TABLE `${ttabla}` ADD PRIMARY KEY (`codigo`)";
				$this->db->simple_query($mSQL);

				foreach($cnumero as $colum){
					if(in_array($colum,$def)){
						$mSQL="ALTER TABLE `${ttabla}` CHANGE COLUMN `ultimo` `${colum}` FLOAT NULL";
						$this->db->simple_query($mSQL);
						$mSQL="UPDATE `${ttabla}` SET `${colum}` = ROUND(`${colum}`,2)";
						$this->db->simple_query($mSQL);
						$ww[] = "a.${colum} <= 0";
					}
				}

				if($ignorar=='S'){
					$mSQL="DELETE FROM ${ttabla} AS a WHERE ".implode(' AND ',$ww);
					$ban=$this->db->simple_query($mSQL);
				}else{
					if(count($ww)>0){
						$mSQL="SELECT a.codigo FROM ${ttabla} AS a JOIN sinv AS b ON a.codigo=b.codigo WHERE ".implode(' AND ',$ww);
						$query = $this->db->query($mSQL);
						foreach($query->result() as $row){
							$this->val_error .= 'El producto código "'.$row->codigo.'" tiene costo menor o igual a cero.<br>';
							$error=true;
						}
					}
					if($error){
						return false;
					}
				}
			}else{
				return false;
			}

			if($ignorar=='S'){
				return true;
			}else{
				$mSQL="SELECT GROUP_CONCAT(a.codigo SEPARATOR ', ') AS codigo FROM ${ttabla} AS a LEFT JOIN sinv AS b ON a.codigo=b.codigo WHERE b.codigo IS NULL";
				$query = $this->db->query($mSQL);
				foreach($query->result() as $row){
					$this->val_error .= 'Código(s) no registrado(s) en el inventario: '.$row->codigo."<br>";
					$error=false;
				}
			}

		}else{
			$this->val_error='Tabla no valida.';
			return false;
		}
	}

	function load(){
		$this->load->library('path');
		$this->load->helper('html');
		$path=new Path();
		$path->setPath($this->config->item('uploads_dir'));
		$path->append('/archivos');
		$this->upload_path =$path->getPath().'/';

		$this->rapyd->load('dataform');
		$form = new DataForm($this->url.'load/process');
		$form->title('Importar Archivos');
		$form->explica1  = new containerField('',"<p style='color:blue;background-color:C6DAF6;align:center'>Para cargar un nuevo archivo de data seleccionelo en el el bot&oacute;n Examinar y luego presione enviar.</p>");
		$form->container = new containerField('adver','Solo archivos en formato EXCEL 97, asegurese de que la informacion que desea importar este en la hoja 1.');
		$form->archivo   = new uploadField('Archivo','archivo');
		$form->archivo->upload_path   = '';
		$form->archivo->allowed_types = 'xls';
		$form->archivo->delete_file   = false;
		$form->archivo->upload_root   = '/tmp';
		$form->archivo->rule          = 'required';
		$form->archivo->append('Solo archivos en formato xls (Excel 97-2003)');

		$form->submit('btnsubmit','Enviar');
		$form->build_form();

		$rti='';
		if($form->on_success()){
			$idtabla = intval($this->datasis->dameval('SELECT MAX(id_tabla) AS maxid FROM '.$this->tabla));
			$idtabla++;

			$arch= '/tmp/'.$form->archivo->upload_data['file_name'];
			$rt=$this->_xlsread($arch,$idtabla);
			$rti="<p>${rt}</p>";
		}

		$lista=array();
		$mSQL='SELECT id_tabla,COUNT(*) AS cana  FROM '.$this->tabla .' GROUP BY id_tabla ORDER BY id_tabla DESC LIMIT 10';
		$query = $this->db->query($mSQL);
		if($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$lista[]=anchor($this->url.'procesar/'.$row->id_tabla,'Tabla: '.$row->id_tabla.' ('.$row->cana.' registros).');
			}
			$listaul= '<h2>Tablas ya importadas</h2>'.ul($lista);
		}else{
			$listaul='';
		}

		$data['content'] = $rti.$form->output.$listaul;
		$data['title']   = heading('Importaci&oacute;n de data desde archivos');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function _xlsread($arch='',$tablaid=1){
		$tabla = $this->tabla;
		$mSQL  = "DELETE FROM ${tabla} WHERE id_tabla=".$tablaid;
		$this->db->simple_query($mSQL);

		$cana=$error=$c=$f=0;
		$this->load->library('Spreadsheet_Excel_Reader');
		$this->spreadsheet_excel_reader->setOutputEncoding('CP1251');
		$this->spreadsheet_excel_reader->read($arch);
		//$hojas=count($this->spreadsheet_excel_reader->sheets);
		foreach($this->spreadsheet_excel_reader->sheets[0]['cells'] as $row){
			foreach($row as $idr=>$val){
				$c=$idr-1;
				if(!empty($val)){
					$data = array('id_tabla'=>$tablaid,'fila'=> $f,'columna'=>$c,'valor'=>$val);
					$mSQL = $this->db->insert_string($tabla, $data);

					$ban=$this->db->simple_query($mSQL);
					if(!$ban){ memowrite($mSQL,'extimpor'); $error++; }
				}
			}
			$cana++;
			$f++;
		}

		if(file_exists($arch)) unlink($arch);
		if($error>0){
			return 'Hubo algunos errores se generaron centinelas.';
		}else{
			return "Fueron cargadas ${cana} registro.";
		}
	}

	function resultado($afecta=null){
		$msj = (empty($afecta))? '' : ", Registros cargados: <b>${afecta}</b>";
		$data['content'] = "<p style='text-align:center'><span style='font-size:2em;'>Data cargada con &eacute;xito${msj} </span><br>".anchor($this->url.'load','Regresar').'</p>';
		$data['title']   = heading('Importaci&oacute;n de data desde archivos');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function eliminar($id_table=null){
		$id_table=intval($id_table);
		if($id_table>0){
			$mSQL = 'DELETE FROM '.$this->tabla.' WHERE id_tabla='.$id_table;
			$this->db->simple_query($mSQL);
		}
		redirect($this->url.'load');
	}

	function instalar(){
		$this->datasis->creaintramenu(array('modulo'=>'927','titulo'=>'Importar archivo','mensaje'=>'Importar archivo xls, csv...','panel'=>'EXPORT/IMPORT','ejecutar'=>'sincro/extimpor','target'=>'popu','visible'=>'S','pertenece'=>'9','ancho'=>960,'alto'=>600));
		if(!$this->db->table_exists($this->tabla)){
			$mSQL="CREATE TABLE IF NOT EXISTS  `".$this->tabla."` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`id_tabla` INT(10) NULL DEFAULT '0',
				`fila` INT(10) NULL DEFAULT NULL,
				`columna` INT(10) NULL DEFAULT NULL,
				`valor` VARCHAR(200) NULL DEFAULT NULL,
				`tipo` VARCHAR(20) NULL DEFAULT NULL,
				`destino` VARCHAR(100) NULL DEFAULT NULL,
				`anterior` VARCHAR(200) NULL DEFAULT NULL,
				`valida` CHAR(1) NULL DEFAULT NULL,
				`msj` VARCHAR(100) NULL DEFAULT NULL,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `id_tabla` (`id_tabla`, `fila`, `columna`)
			)
			COMMENT='Contenido de las tablas importadas'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}
	}
}
