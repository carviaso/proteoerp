<?php
class Sfacfiscal extends Controller{

	function Sfacfiscal(){
		parent::Controller();
		$this->load->library('rapyd');
	}

	function index(){
		$this->rapyd->load('datafilter','datagrid');

		$link=site_url('supervisor/sfacfiscal/arreglaserial');
		$linkd=site_url('supervisor/sfacfiscal/arreglaserialdev');
		$link2=site_url('supervisor/sfacfiscal/arreglanfiscal');
		$link4=site_url('supervisor/sfacfiscal/arreglanfiscaldev');
		$link3=site_url('supervisor/sfacfiscal/traereferen');
		$jquery='
		function riega(id){
			if(confirm("Esta seguro que regar el serial para las facturas?")){
				if(confirm("Desea cambiar solo los registros vacios?"))
					resp=0;
				else
					resp=1;
				$.post("'.$link.'",{ cajero:$("#cajero").val(),fecha:$("#fecha").val(), serial:id ,noresp:resp, nulos:$("#nulos").val()},
				function(data){
					alert(data);
				});
			}
		}

		function riegadev(id){
			if(confirm("Esta seguro que regar el serial por las devoluciones?")){
				if(confirm("Desea cambiar solo los registros vacios?"))
					resp=0;
				else
					resp=1;
				$.post("'.$linkd.'",{ cajero:$("#cajero").val(),fecha:$("#fecha").val(), serial:id ,noresp:resp, nulos:$("#nulos").val()},
				function(data){
					alert(data);
				});
			}
		}

		function nfiscal(id){
			if(confirm("Esta seguro que regar el numero fiscal?")){
				if(confirm("Desea cambiar solo los registros vacios?"))
					resp=0;
				else
					resp=1;
				$.post("'.$link2.'",{ cajero:$("#cajero").val(),fecha:$("#fecha").val(), numero:id ,noresp:resp, nulos:$("#nulos").val()},
				function(data){
					alert(data);
				});
			}
		}

		function nfiscaldev(id){
			if(confirm("Esta seguro que regar el numero fiscal?")){
				if(confirm("Desea cambiar solo los registros vacios?"))
					resp=0;
				else
					resp=1;
				$.post("'.$link4.'",{ cajero:$("#cajero").val(),fecha:$("#fecha").val(), numero:id ,noresp:resp, nulos:$("#nulos").val()},
				function(data){
					alert(data);
				});
			}
		}


		function buscaref(){
			referenc = prompt("Introduce una referencia");
			$.post("'.$link3.'",{ referen: referenc },
			function(data){
				alert(data);
			});
		}';

		function exissinv($cen,$t,$tipo_doc){
			if(!empty($cen)){
				if($t==1){
					if($tipo_doc=='F')
						$rt=form_button('asignar',$cen,'onclick="riega(\''.$cen.'\');"');
					else
						$rt=form_button('asignar',$cen,'onclick="riegadev(\''.$cen.'\');"');
				}else{
					if($tipo_doc=='F')
						$rt=form_button('asignar',$cen,'onclick="nfiscal(\''.$cen.'\');"');
					else
						$rt=form_button('asignar',$cen,'onclick="nfiscaldev(\''.$cen.'\');"');
				}
			}else{
				$rt='--';
			}
			return $rt;
		}

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0');

		$filter = new DataFilter('','sfac');
		//$filter->db->where('tipo_doc','F');
		$filter->script($jquery);

		$filter->fecha  = new dateonlyField('Desde','fecha');
		$filter->fecha->clause  ='where';
		$filter->fecha->db_name ='fecha';
		$filter->fecha->insertValue = date('Y-m-d');
		$filter->fecha->operator='=';
		$filter->fecha->rule    ='required';
		$filter->fecha->append("<a onclick='buscaref()'>Traer de referencia</a>");

		$filter->cajero = new dropdownField('Cajero', 'cajero');
		$filter->cajero->option('','Seleccionar');
		$filter->cajero->option(' ','Creditos');
		$filter->cajero->options('SELECT cajero, CONCAT_WS("-",cajero,nombre) FROM scaj ORDER BY cajero');

		$filter->usuario = new dropdownField('Usuario', 'usuario');
		$filter->usuario->option('','Todos');
		$filter->usuario->options('SELECT us_codigo,us_codigo FROM usuario ORDER BY us_codigo');

		$filter->tipo_doc = new dropdownField('Tipo Doc.', 'tipo_doc');
		$filter->tipo_doc->option('F','Facturas');
		$filter->tipo_doc->option('D','Devoluciones');
		$filter->tipo_doc->rule = 'required';

		$filter->nulos = new dropdownField('Filtrar seriales nulos', 'nulos');
		$filter->nulos->option('s','Si');
		$filter->nulos->option('n','No');
		$filter->nulos->clause ='';
		$filter->nulos->group='No afecta el filtro';
		$filter->nulos->append('Si se activa esta opcion no se riega el n&uacute;mero en los campos donde el serial de la m&aacute;quina fiscal es nulo');

		$filter->buttons('reset','search');
		$filter->build();

		if($this->rapyd->uri->is_set('search') AND $filter->is_valid()){
			$fecha=$filter->fecha->newValue;

			$fields = $this->db->field_data('sfac');
			$ppk=array();
			foreach ($fields as $field){
				if($field->primary_key==1){
					$ppk[]='<#'.$field->name.'#>';
				}
			}

			$llink=anchor('supervisor/sfacfiscal/editsfac/modify/'.implode('/',$ppk),'<#tipo_doc#><#numero#>');
			$uri2 = anchor_popup('formatos/verhtml/FACTURA/<#tipo_doc#>/<#numero#>','Ver HTML',$atts);

			$grid = new DataGrid('');
			$grid->use_function('exissinv');
			$grid->per_page = 30;
			$grid->db->orderby('numero');
			$grid->column('Fecha'      ,'<dbdate_to_human><#fecha#></dbdate_to_human>' ,'fecha');
			$grid->column('Nombre'     ,'nombre');
			$grid->column('Referencia' ,$llink);
			$grid->column('Usuario'    ,'usuario');
			$grid->column('Cajero'     ,'cajero');
			$grid->column('Monto'      ,'<nformat><#totalg#></nformat>','align="right"');
			$grid->column('N.Fiscal'   ,'<exissinv><#nfiscal#>|2|<#tipo_doc#></exissinv>'  ,'align="center"');
			$grid->column('Serial Maq.','<exissinv><#maqfiscal#>|1|<#tipo_doc#></exissinv>','align="center"');
			$grid->column('Ver factura',$uri2,'align="center"');
			$grid->build();
			//echo $grid->db->last_query();
			$mSQL=$grid->db->last_query();
			$mSQL=str_replace('*', 'SUM(totalg)',$mSQL );
			$corte=stripos($mSQL, 'ORDER');
			if($corte!==false) $mSQL=substr($mSQL,0,$corte);
			$monto=$this->datasis->dameval($mSQL);

			$tabla='Monto: '.nformat($monto).$grid->output;
			$mSQL='SELECT serial, MAX(factura) AS factura,SUM(exento+base+iva+base1+iva1+base2+iva2-ncexento-ncbase-nciva-ncbase1-nciva1-ncbase2-nciva2) AS total FROM fiscalz WHERE fecha="'.$fecha.'" GROUP BY serial';
			$query = $this->db->query($mSQL);

			foreach ($query->result() as $row){
				$tabla .= $row->serial.' - '.$row->factura.' - '.nformat($row->total).br();
			}
		}else{
			$tabla='';
		}

		$data['content']  = $filter->output.$tabla;
		$data['title']    = '<h1>Arreglos de consistencias fiscal en facturas</h1>';
		$data['head']     = $this->rapyd->get_head().script('jquery.js');
		$this->load->view('view_ventanas', $data);
	}

	function editsfac(){
		$this->rapyd->load('dataedit');
		$edit = new DataEdit('Ajustes fiscales', 'sfac');
		$edit->back_save  =true;
		$edit->back_cancel=true;
		$edit->back_cancel_save=true;
		$edit->back_url = site_url('supervisor/sfacfiscal/index');

		$edit->tipo_doc = new inputField('Referencia','tipo_doc');
		$edit->tipo_doc->mode='autohide';
		$edit->numero = new inputField('Referencia','numero');
		$edit->numero->mode='autohide';
		$edit->numero->in='tipo_doc';

		$edit->nfiscal =  new inputField('N&uacute;mero fiscal','nfiscal');
		$edit->nfiscal->size = 15;
		$edit->nfiscal->maxlength=30;
		$edit->nfiscal->rule = 'trim|strtoupper';

		$edit->maqfiscal =  new inputField('Serial de la maquina fiscal', 'maqfiscal');
		$edit->maqfiscal->size     =15;
		$edit->maqfiscal->maxlength=20;
		$edit->maqfiscal->rule = 'trim|strtoupper';

		$edit->buttons('modify','save', 'undo', 'back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = '<h1>Ajustes fiscales</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function traereferen(){
		$numero=$this->db->escape($this->input->post('referen'));
		$query = $this->db->query("SELECT fecha,cajero FROM sfac WHERE numero=$numero AND tipo_doc='F'");
		if ($query->num_rows() > 0){
			$row = $query->row();
			echo 'Fecha: '.dbdate_to_human($row->fecha).' Cajero: "'.$row->cajero.'"';

			//echo json_encode($row);
		}else{
			echo 'Referencia no encontrada';
		}
	}

	function arreglaserial(){
		$cajero=$this->input->post('cajero');
		$fecha =$this->input->post('fecha');
		$serial=$this->input->post('serial');
		if($cajero===false or $fecha===false or $serial===false) {
			echo 'Error en los parametros';
			return false;
		}
		if(!$this->input->post('noresp')){
			$and=' AND LENGTH(TRIM(maqfiscal))=0';
		}else{
			$and='';
		}

		$fecha =human_to_dbdate($fecha);
		$mSQL='UPDATE sfac SET maqfiscal='.$this->db->escape($serial).' WHERE tipo_doc=\'F\' AND cajero='.$this->db->escape($cajero).' AND fecha='.$this->db->escape($fecha).$and;

		if(!$this->db->simple_query($mSQL))
			echo 'Hubo un problema en el cambio';
		else
			echo 'Cambio realizado';
	}

	function arreglanfiscal(){
		$cajero=$this->input->post('cajero');
		$fecha =$this->input->post('fecha');
		$numero=$this->input->post('numero');
		$nulos =$this->input->post('nulos');

		if($cajero===false or $fecha===false or $numero===false) {
			echo 'Error en los parametros';
			return false;
		}
		$noresp=$this->input->post('noresp');

		$cajero=$this->db->escape($cajero);
		$fecha =$this->db->escape(human_to_dbdate($fecha));
		$numero=trim($numero);

		$nnumero=ltrim($numero,'0');
		$wwhere="`fecha` = $fecha AND `cajero` = $cajero AND `tipo_doc`= 'F'";
		if($nulos=='s') $wwhere.=' AND maqfiscal IS NOT NULL';
		$mSQL="SELECT TRIM(nfiscal) AS nfiscal,tipo_doc,numero FROM (`sfac`) WHERE $wwhere ORDER BY `numero`";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			$c=false;
			foreach ($query->result() as $row){
				if($numero==$row->nfiscal){
					$c=true;
					continue;
				}
				if($c){
					$nnumero++;
					if(empty($row->nfiscal) or $noresp){
						$nnnumero=str_pad($nnumero,8 , '0', STR_PAD_LEFT);
						$data = array('nfiscal' => $nnnumero);
						$where = 'tipo_doc = '.$this->db->escape($row->tipo_doc).' AND numero='.$this->db->escape($row->numero);
						$str = $this->db->update_string('sfac', $data, $where);
						//echo $str."\n";
						$this->db->simple_query($str);
					}
				}
			}
		}

		$c=false;
		$nnumero=ltrim($numero,'0');
		$mSQL="SELECT TRIM(nfiscal) AS nfiscal,tipo_doc,numero FROM (`sfac`) WHERE $wwhere ORDER BY `numero` DESC";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			$c=false;
			foreach ($query->result() as $row){
				if($numero==$row->nfiscal){
					$c=true;
					continue;
				}
				if($c){
					$nnumero--;
					if(empty($row->nfiscal) or $noresp){
						$nnnumero=str_pad($nnumero,8 , '0', STR_PAD_LEFT);
						$data = array('nfiscal' => $nnnumero);
						$where = 'tipo_doc = '.$this->db->escape($row->tipo_doc).' AND numero='.$this->db->escape($row->numero);
						$str = $this->db->update_string('sfac', $data, $where);
						//echo $str."\n";
						$this->db->simple_query($str);
					}
				}
			}
		}
		echo 'Cambio realizado';
	}


	//Para las devoluciones
	function arreglaserialdev(){
		$cajero=$this->input->post('cajero');
		$fecha =$this->input->post('fecha');
		$serial=$this->input->post('serial');
		if($cajero===false or $fecha===false or $serial===false) {
			echo 'Error en los parametros';
			return false;
		}
		if(!$this->input->post('noresp')){
			$and=' AND LENGTH(TRIM(maqfiscal))=0';
		}else{
			$and='';
		}

		$fecha =human_to_dbdate($fecha);
		$mSQL='UPDATE sfac SET maqfiscal='.$this->db->escape($serial).' WHERE tipo_doc=\'D\' AND cajero='.$this->db->escape($cajero).' AND fecha='.$this->db->escape($fecha).$and;

		if(!$this->db->simple_query($mSQL))
			echo 'Hubo un problema en el cambio';
		else
			echo 'Cambio realizado';
	}

	function arreglanfiscaldev(){
		$cajero=$this->input->post('cajero');
		$fecha =$this->input->post('fecha');
		$numero=$this->input->post('numero');
		$nulos =$this->input->post('nulos');

		if($cajero===false or $fecha===false or $numero===false) {
			echo 'Error en los parametros';
			return false;
		}
		$noresp=$this->input->post('noresp');

		$cajero=$this->db->escape($cajero);
		$fecha =$this->db->escape(human_to_dbdate($fecha));
		$numero=trim($numero);

		$nnumero=ltrim($numero,'0');
		$wwhere="`fecha` = $fecha AND `cajero` = $cajero AND `tipo_doc`= 'D'";
		if($nulos=='s') $wwhere.=' AND maqfiscal IS NOT NULL';
		$mSQL="SELECT TRIM(nfiscal) AS nfiscal,tipo_doc,numero FROM (`sfac`) WHERE $wwhere ORDER BY `numero`";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			$c=false;
			foreach ($query->result() as $row){
				if($numero==$row->nfiscal){
					$c=true;
					continue;
				}
				if($c){
					$nnumero++;
					if(empty($row->nfiscal) or $noresp){
						$nnnumero=str_pad($nnumero,8 , '0', STR_PAD_LEFT);
						$data = array('nfiscal' => $nnnumero);
						$where = 'tipo_doc = '.$this->db->escape($row->tipo_doc).' AND numero='.$this->db->escape($row->numero);
						$str = $this->db->update_string('sfac', $data, $where);
						//echo $str."\n";
						$this->db->simple_query($str);
					}
				}
			}
		}

		$c=false;
		$nnumero=ltrim($numero,'0');
		$mSQL="SELECT TRIM(nfiscal) AS nfiscal,tipo_doc,numero FROM (`sfac`) WHERE $wwhere ORDER BY `numero` DESC";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			$c=false;
			foreach ($query->result() as $row){
				if($numero==$row->nfiscal){
					$c=true;
					continue;
				}
				if($c){
					$nnumero--;
					if(empty($row->nfiscal) or $noresp){
						$nnnumero=str_pad($nnumero,8 , '0', STR_PAD_LEFT);
						$data = array('nfiscal' => $nnnumero);
						$where = 'tipo_doc = '.$this->db->escape($row->tipo_doc).' AND numero='.$this->db->escape($row->numero);
						$str = $this->db->update_string('sfac', $data, $where);
						//echo $str."\n";
						$this->db->simple_query($str);
					}
				}
			}
		}
		echo 'Cambio realizado';
	}
}
