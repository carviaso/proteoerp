<?php
class extimpor extends Controller {

	function extimpor(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->titulo = 'Tabla importada';
		$this->tabla  = 'impor_data';
	}

	function index(){
		$this->rapyd->load('datafilter','datagrid');
		$id_tabla = 1;
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
			'descrip' => 'Descipción',
			'precio1' => 'Precio 1',
			'precio2' => 'Precio 2',
			'precio3' => 'Precio 3',
			'precio4' => 'Precio 4',
			'base1'   => 'Base 1',
			'base2'   => 'Base 2',
			'base3'   => 'Base 3',
			'base4'   => 'Base 4',

		);

		$uri = anchor('finanzas/bmov/dataedit/show/<#codbanc#>/<#tipo_op#>/<#numero#>','<#numero#>');
		$grid = new DataGrid('Tabla importada');
		$grid->db->from($tabla);
		$grid->db->where('id_tabla',$id_tabla);
		//$grid->db->where('fila >',0);
		$grid->db->groupby('fila');
		$grid->db->orderby('fila');
		$grid->per_page = 15;

		for($i=0;$i<$ccolum;$i++){
			$select[]="GROUP_CONCAT(IF(columna=${i},valor,NULL)) AS c${i}";
			$titulo=(isset($titu[$i]))? $titu[$i]: 'Columna '.($i+1);
			$titulo=form_dropdown("c${i}", $options, '');
			$grid->column_orderby($titulo,"c${i}","c${i}");
		}
		$grid->db->select($select);
		$grid->build();

		$data['content'] = $grid->output;
		$data['title']   = heading('Tabla importada');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}


	function load(){
		$this->load->library('path');
		$this->load->helper('html');
		$path=new Path();
		$path->setPath($this->config->item('uploads_dir'));
		$path->append('/archivos');
		$this->upload_path =$path->getPath().'/';

		$this->rapyd->load('dataform');
		$form = new DataForm('sincro/extimpor/load/process');
		$form->title('Importar Archivos');

		$form->container = new containerField('adver','');

		$form->archivo = new uploadField('Archivo','archivo');
		$form->archivo->upload_path   = '';
		$form->archivo->allowed_types = 'xls';
		$form->archivo->delete_file   = false;
		$form->archivo->upload_root   = '/tmp';
		$form->archivo->rule          = 'required';
		$form->archivo->append('Solo archivos en formato xls (Excel 97-2003)');

		//$accion="javascript:window.location='".site_url('ventas/metas/filteredgrid')."'";
		//$form->button('btn_pfl','Regresar',$accion,'TR');

		$form->submit('btnsubmit','Enviar');
		$form->build_form();

		$rti='';
		if ($form->on_success()){
			$idtabla = intval($this->datasis->dameval('SELECT MAX(id_tabla) AS maxid FROM '.$this->tabla));
			$idtabla++;

			$arch= '/tmp/'.$form->archivo->upload_data['file_name'];
			$rt=$this->_xlsread($arch,$idtabla);
			$rti="<p>${rt}</p>";
		}

		$data['content'] = $rti.$form->output;
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


	function instalar(){
		$mSQL="CREATE TABLE IF NOT EXISTS `impor_data` (
			`id` int(10) NOT NULL AUTO_INCREMENT,
			`id_tabla` int(10) DEFAULT '0',
			`fila` int(10) DEFAULT NULL,
			`columna` int(10) DEFAULT NULL,
			`valor` varchar(200) DEFAULT NULL,
			`tipo` varchar(20) DEFAULT NULL,
			`destino` varchar(100) DEFAULT NULL,
			PRIMARY KEY (`id`),
			UNIQUE KEY `id_tabla` (`id_tabla`,`fila`,`columna`)
		) ENGINE=MyISAM CHARSET=latin1 COMMENT='Contenido de las tablas importadas'";

		var_dump($this->db->simple_query($mSQL));
	}
}
