<?php
class Cargahisto extends Controller {

	function Cargahisto(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('13B',1);
		$this->cajero='';
	}

	function index(){
		$this->rapyd->load('datatable','dataform');

		$form = new DataForm('supermercado/cargahisto/index/process');
		$form->title('Este modulo permite cargar manual las ventas sacadas de los historicos de las cajas');

		$form->fecha = new dateonlyField('Fecha', 'fecha','d/m/Y');
		$form->fecha->db_format='Ymd';
		$form->fecha->insertValue = date('Y-m-d');
		$form->fecha->size=11;
		$form->fecha->autocomplete=false;
		$form->fecha->rule = 'required|chfecha';

		$form->hf  = new uploadField('Facturas (<b>hf</b>YYYYMMCCCAAA.dbf)', 'hf');
		$form->hf->upload_path = 'tmp';
		$form->hf->upload_root='/';
		$form->hf->rule='required';
		$form->hf->allowed_types = 'csv|dbf';

		$form->hi  = new uploadField('Art&iacute;culos (<b>hi</b>YYYYMMCCCAAA.dbf)', 'hi');
		$form->hi->upload_path = 'tmp';
		$form->hi->upload_root='/';
		$form->hi->rule='required';
		$form->hi->allowed_types = 'csv|dbf';

		$form->hs  = new uploadField('Pagos (<b>hs</b>YYYYMMCCCAAA.dbf)', 'hs');
		$form->hs->upload_path = 'tmp';
		$form->hs->upload_root='/';
		$form->hs->rule='required';
		$form->hs->allowed_types = 'csv|dbf';

		$form->submit('btnsubmit','Procesar');
		$form->build_form();

		if($form->on_success()){
			$fecha=$form->fecha->newValue;
			$encab=false;
			if(!empty($form->hf->upload_data['full_path'])){
				if(substr($form->hf->upload_data['file_name'],0,2)=='hf'){
					$encab=true;
					$this->_cargadbf($form->hf->upload_data['full_path'],$fecha);
					unlink($form->hf->upload_data['full_path']);
				}else{
					$form->error='Encabezado primero';
					$form->build_form();
				}
			}

			if(!empty($form->hi->upload_data['full_path']) && $encab){
				$this->_cargadbf($form->hi->upload_data['full_path'],$fecha);
				unlink($form->hi->upload_data['full_path']);
			}

			if(!empty($form->hs->upload_data['full_path']) && $encab){
				$this->_cargadbf($form->hs->upload_data['full_path'],$fecha);
				unlink($form->hs->upload_data['full_path']);
			}
		}

		$data['content'] = $form->output;
		$data['title']   = heading('Cargar ventas de los hist&oacute;ricos de cajas');
		$data['script']  = script('jquery-1.2.6.js');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function _cargadbf($fdbf,$fecha){
		$ff=substr($fecha,0,6);
		$error=0;

		$ptabla['viepag'] = 'hs'.$ff;
		$ptabla['vieite'] = 'hi'.$ff;
		$ptabla['viefac'] = 'hf'.$ff;
		$tabla='';

		foreach($ptabla as $ttabla => $ht){
			if(strripos($fdbf,$ht)!==false){
				$tabla=$ttabla;
				break;
			}
		}

		if(!empty($tabla)){
			$db = dbase_open($fdbf, 0);
			$cc=$error=0;
			if ($db) {
				$cajero='';
				$record_numbers = dbase_numrecords($db);
				for ($i = 1; $i <= $record_numbers; $i++) {
					$row = dbase_get_record_with_names($db, $i);
					if($row['FECHA']==$fecha){
						$dbfecha=$this->db->escape($fecha);
						if($cc==0){
							$mSQL="DELETE FROM ${tabla} WHERE fecha=${dbfecha} AND caja=".$this->db->escape(trim($row['CAJA']));
							$ban=$this->db->simple_query($mSQL);
							if(!$ban){ memowrite($mSQL,'cargahisto'); $error++; }
						}

						$data=array();
						if($tabla=='vieite'){
							if(empty($cajero)) $cajero=$this->cajero;
							$data['numero']     =trim($row['NUMERO']);
							$data['fecha']      =$row['FECHA'];
							$data['codigo']     =trim($row['CODIGO']);
							$data['precio']     =$row['PRECIO'];
							$data['monto']      =$row['MONTO'];
							$data['cantidad']   =$row['CANTIDAD'];
							$data['impuesto']   =$row['IMPUESTO'];
							$data['costo']      =$row['COSTO'];
							$data['almacen']    ='0002';
							$data['cajero']     =$cajero;
							$data['caja']       =trim($row['CAJA']);
							$data['referen']    =trim($row['REFERAN']);
						}elseif($tabla=='viefac'){
							$data['numero']     =trim($row['NUMERO']);
							$data['tipo']       =trim($row['TIPO']);
							$data['fecha']      =$row['FECHA'];
							$data['cajero']     =trim($row['CAJERO']);
							$data['caja']       =trim($row['CAJA']);
							$data['cliente']    =trim($row['CLIENTE']);
							$data['nombres']    =trim($row['NOMBRES']);
							$data['apellidos']  =trim($row['APELLIDOS']);
							$data['impuesto']   =$row['IMPUESTO'];
							$data['gtotal']     =$row['GTOTAL'];
							$data['hora']       =$row['HORA'];
							$data['cuadre']     ='';
							$this->cajero = trim($row['CAJERO']);
						}else{
							if($row['F_FACTURA']!=$row['FECHA']) continue;
							if(empty($cajero)) $cajero=$this->cajero;
							$data['tipo_doc']   =trim($row['TIPO_DOC']);
							$data['numero']     =$row['NUMERO'];
							$data['tipo']       =trim($row['TIPO']);
							$data['monto']      =$row['MONTO'];
							$data['num_ref']    =trim($row['NUM_REF']);
							$data['clave']      =trim($row['CLAVE']);
							$data['fecha']      =$row['FECHA'];
							$data['banco']      =trim($row['BANCO']);
							$data['f_factura']  =$row['F_FACTURA'];
							$data['cajero']     =$cajero;
							$data['caja']       =trim($row['CAJA']);
						}

						$mSQL=$this->db->insert_string($tabla, $data);
						$ban=$this->db->simple_query($mSQL);
						if(!$ban){ memowrite($mSQL,'cargahisto'); $error++; }
						$cc++;
					}
				}
			}
		}
	}
}
