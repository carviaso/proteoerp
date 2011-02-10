<?php
class Bcaj extends Controller {
	function bcaj(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->config->load('datasis');
		//$this->guitipo=array('DE'=>'Deposito','TR'=>'Transferencia','RM'=>'Remesa');
		$this->guitipo=array('DE'=>'Deposito','TR'=>'Transferencia');
		$this->datasis->modulo_id('51D',1);
		$this->cajas=$this->config->item('cajas');
		foreach($this->cajas AS $inv=>$val){
			$codban=$this->db->escape($val);
			$cana=$this->datasis->dameval("SELECT COUNT(*) AS cana FROM banc WHERE codbanc=$codban");
			if($cana==0){
				show_error('La caja '.$val.' no esta registrada en el sistema, debe registrarla por el modulo de bancos o ajustar la configuracion en config/datasis.php');
			}
		}
	}

	function index(){
		$this->rapyd->load('datafilter','datagrid');
		$smenu['link']=barra_menu('51D');

		$filter = new DataFilter('Filtro','bcaj');

		$filter->fecha = new dateonlyField('Fecha','fecha');
		$filter->fecha->size=10;
		$filter->fecha->operator='=';

		$filter->numero = new inputField('N&uacute;mero', 'numero');
		$filter->numero->size=20;

		//$filter->nombre = new inputField('Nombre', 'nombre');
		//$filter->nombre->size=40;

		//$filter->banco = new dropdownField('Banco', 'codbanc');
		//$filter->banco->option('','');
		//$filter->banco->options('SELECT codbanc,banco FROM banc where tbanco<>\'CAJ\' ORDER BY codbanc');

		$filter->buttons('reset','search');
		$filter->build();

		$uri = anchor('finanzas/bcaj/dataedit/show/<#numero#>','<#numero#>');

		$grid = new DataGrid('Lista');
		$grid->order_by('numero','desc');
		$grid->per_page = 15;

		$grid->column_orderby('N&uacute;mero',$uri,'numero');
		$grid->column_orderby('Fecha'        ,'<dbdate_to_human><#fecha#></dbdate_to_human>','fecha');
		$grid->column_orderby('Env&iacute;a' ,'<#envia#>-<#bancoe#>','bancoe');
		$grid->column_orderby('Recibe'       ,'<#recibe#>-<#bancor#>','bancor');
		$grid->column_orderby('Monto'        ,'<nformat><#monto#></nformat>' ,'monto','align=right');
		$grid->column_orderby('Concepto'     ,'concepto','concepto');

		$grid->add('finanzas/bcaj/agregar');
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);

		$data['title']   = '<h1>Movimientos de Caja</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load('dataedit');
		$edit = new DataEdit('Deposito en caja', 'bcaj');
		$edit->back_url = site_url('finanzas/bcaj/index');

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule = 'chfecha|required';

		$edit->tipo = new dropdownField('Tipo', 'tipo');
		$edit->tipo->options($this->guitipo);
		$edit->tipo->rule = 'required';
		$edit->tipo->style = 'width:180px';

		$edit->envia = new dropdownField('Envia','envia');
		$edit->envia->option('','Seleccionar');

		$edit->recibe = new dropdownField('Recibe','recibe');
		$edit->recibe->option('','Seleccionar');

		//Poner los campos que faltan

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = '<h1>Depositos,transferencias y remesas</h1>';
		$data['head']    = $this->rapyd->get_head().phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function agregar(){
		$data['content'] = '<table align="center">'.br();
		
		$data['content'].= '<tr><td><img src="'.base_url().'images/dinero.jpg'.'" height="100px"></td><td>';
		$data['content'].= '<p>Esta opcion se utiliza para depositar lo recaudado en efectivo desde 
		                       las cajas para los bancos, debe tener a mano el numero del deposito</p>';
		                       
		$data['content'].= anchor('finanzas/bcaj/depositoefe'  ,'Deposito de efectivo').br();

		$data['content'].= '</td></tr><tr><td><img src="'.base_url().'images/tarjetas.jpg'.'" height="100px"></td><td>';
		$data['content'].= '<p>Para registrar lo recaudado mediante tarjetas electronicas (Credito, Debito, Cesta Ticket) 
		                       segun los valores impresos en los cierres diarios de los puntos de venta electronicos</p>';
		$data['content'].= anchor('finanzas/bcaj/depositotar'  ,'Deposito de tarjetas').br();

		$data['content'].= '</td></tr><tr><td><img src="'.base_url().'images/transfer.jpg'.'" height="100px" ></td><td>';
		$data['content'].= '<p>Puede hacer transferencias entre cajas o entre cuentas bancarias, las que correspondan a
		                       cuentas bancarias pueden realizarce mediante cheque-deposito (manual) o NC-ND por transferencia   
		                       electronica, en cualquier caso debe tener los numeros de documentos correspondientes.  </p>';
		$data['content'].= anchor('finanzas/bcaj/transferencia','Transferencias').br();
		

		$data['content'].= '</td></tr><tr><td><img src="'.base_url().'images/caja_activa.gif'.'" height="100px" ></td><td>';
		$data['content'].= '<p>Si por politica de la empresa se quiere descargar la caja de recaudacion todos los dias, esta
		                       opcion facilita el proceso ya que puede hacer varias transferencias en una sola operacion..  </p>';
		$data['content'].= anchor('finanzas/bcaj/autotranfer','Transferencias').br();

		$data['content'].= '</td></tr><tr><td colspan=2 align="center">'.anchor('finanzas/bcaj/index'        ,'Regresar').br();
		$data['content'].= '</td></tr></table>'.br();
		
		$data['title']   = heading('Selecciona la operaci&oacute;n que desea realizar');
		$data['head']    = $this->rapyd->get_head();  //.phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function transferencia(){
		$this->rapyd->load('dataform');

		$edit = new DataForm('finanzas/bcaj/transferencia/process');
		$edit->title='Deposito en caja';

		$edit->back_url = site_url('finanzas/bcaj/index');

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule = 'chfecha|required';

		$edit->envia = new dropdownField('Envia','envia');
		$edit->envia->option('','Seleccionar');

		$edit->recibe = new dropdownField('Recibe','recibe');
		$edit->recibe->option('','Seleccionar');

		$desca='CONCAT_WS(\'-\',codbanc,banco) AS desca';

		$link  = site_url('finanzas/bcaj/get_trrecibe');
		$script='
		function get_trrecibe(){
			$.post("'.$link.'",{ envia: $("#envia").val()}, function(data){
				//alert(data);
				$("#recibe").html(data);
			});
		}';
		$edit->script($script);

		$edit->envia->options("SELECT codbanc,$desca FROM banc ORDER BY banco");
		$edit->envia->onchange = 'get_trrecibe();';
		$edit->envia->rule     = 'required';

		$codigo=$this->input->post('envia');
		if($codigo!==false){
			$tipo= $this->_traetipo($codigo);
			$ww=($tipo=='CAJ') ? 'tbanco="CAJ"' : 'tbanco<>"CAJ"';
			$edit->recibe->options("SELECT codbanc,$desca FROM banc WHERE $ww AND codbanc<>".$this->db->escape($codigo)." ORDER BY banco");
		}else{
			$edit->recibe->option('','Seleccione una caja de envio');
		}
		$edit->recibe->rule  = 'required';

		$edit->monto = new inputField('Monto', 'monto');
		$edit->monto->css_class='inputnum';
		$edit->monto->rule='trim|numeric|required';
		$edit->monto->maxlength =15;
		$edit->monto->size = 20;
		$edit->monto->autocomplete=false;

		$edit->envia->rule   = 'required';
		$edit->envia->style  = 'width:180px';
		$edit->recibe->style = 'width:180px';

		$back_url = site_url('finanzas/bcaj/agregar');
		$edit->button('btn_undo', 'Regresar', "javascript:window.location='${back_url}'", 'BL');

		$edit->submit('btnsubmit','Guardar');
		$edit->build_form();

		if ($edit->on_success()){
			$fecha  = $edit->fecha->newValue;
			$monto  = $edit->monto->newValue;
			$envia  = $edit->envia->newValue;
			$recibe = $edit->recibe->newValue;
			$rt=$this->_transferencaj($fecha,$monto,$envia,$recibe);
			if($rt){
				redirect('/finanzas/bcaj/listo');
			}else{
				redirect('/finanzas/bcaj/listo/s');
			}
		}

		$this->rapyd->jquery[]='$(".inputnum").numeric(".");';
		$data['content'] = $edit->output;
		$data['title']   = heading('Transferencias');
		$data['head']    = $this->rapyd->get_head().phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function depositoefe(){
		$this->rapyd->load('dataform');

		$edit = new DataForm('finanzas/bcaj/depositoefe/process');
		$edit->title('Deposito de efectivo');

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size = 10;
		$edit->fecha->rule = 'chfecha|required';

		$edit->envia = new dropdownField('Envia','envia');
		$edit->envia->option('','Seleccionar');

		$edit->recibe = new dropdownField('Recibe','recibe');
		$edit->recibe->option('','Seleccionar');

		$edit->numeror = new inputField('N&uacute;mero de deposito', 'numeror');
		$edit->numeror->rule='required';
		$edit->numeror->size=20;

		$desca='CONCAT_WS(\'-\',codbanc,banco) AS desca';
		$script='
			function totaliza(){
				if($("#efectivo").val().length>0) efectivo=parseFloat($("#efectivo").val()); else efectivo=0;
				if($("#cheques").val().length>0)  cheques =parseFloat($("#cheques").val());  else cheques =0;
				monto   =efectivo+cheques;
				$("#monto").val(roundNumber(monto,2));
			}';

		$this->rapyd->jquery[]='$("#cheques,#efectivo").bind("keyup",function() { totaliza(); });';
		$edit->script($script);

		$edit->envia->options( "SELECT TRIM(codbanc) AS codbanc,$desca FROM banc WHERE tbanco='CAJ'");

		$edit->recibe->options("SELECT TRIM(codbanc) AS codbanc,$desca FROM banc WHERE tbanco<>'CAJ'");
		$edit->recibe->rule='callback_chtr|required';

		$campos=array(
				'cheques' =>'Cheques',
				'efectivo'=>'Efectivo',
				'monto'   =>'Monto total');
		foreach($campos AS $obj=>$titulo){
			$edit->$obj = new inputField($titulo, $obj);
			$edit->$obj->css_class='inputnum';
			$edit->$obj->rule='trim|numeric';
			$edit->$obj->maxlength =15;
			$edit->$obj->size = 20;
			$edit->$obj->group = 'Montos';
			$edit->$obj->autocomplete=false;
		}
		$edit->$obj->readonly=true;

		$edit->envia->rule   = 'required';
		$edit->envia->style  = 'width:180px';
		$edit->recibe->style = 'width:180px';

		$back_url = site_url('finanzas/bcaj/agregar');
		$edit->button('btn_undo', 'Regresar', "javascript:window.location='${back_url}'", 'TR');

		$edit->submit('btnsubmit','Guardar');
		$edit->build_form();

		//**********************
		//  Guarda el efecto
		//**********************
		if ($edit->on_success()){
			$fecha   = $edit->fecha->newValue;
			$envia   = $edit->envia->newValue;
			$recibe  = $edit->recibe->newValue;
			$numeror = $edit->numeror->newValue;
			$efectivo= $edit->efectivo->newValue;
			$cheque  = $edit->cheques->newValue;

			$rt=$this->_transferendepefe($fecha,$efectivo,$cheque,$envia,$recibe,$numeror);
			if($rt){
				redirect('finanzas/bcaj/listo');
			}else{
				redirect('finanzas/bcaj/listo/s');
			}
		}

		$data['content'] = $edit->output;
		$data['title']   = heading('Deposito');
		$data['head']    = $this->rapyd->get_head().phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function depositotar(){
		$this->rapyd->load('dataform');

		$edit = new DataForm('finanzas/bcaj/depositotar/process');
		$edit->title('Deposito en caja de tarjetas');

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size = 10;
		$edit->fecha->rule = 'chfecha|required';

		$edit->envia = new dropdownField('Envia','envia');
		$edit->envia->option('','Seleccionar');

		$edit->recibe = new dropdownField('Recibe','recibe');
		$edit->recibe->option('','Seleccionar');

		$edit->tipo = new dropdownField('Tipo','tipo');
		$edit->tipo->option('NC','Nota de credito');
		$edit->tipo->option('DE','Deposito');

		$edit->numero = new inputField('N&uacute;mero de deposito', 'numero');
		$edit->numero->rule='required';
		$edit->numero->size=20;

		$desca='CONCAT_WS(\'-\',codbanc,banco) AS desca';
		$sql='SELECT TRIM(a.codbanc) AS codbanc,b.comitc, b.comitd, b.impuesto FROM banc AS a JOIN tban AS b ON a.tbanco=b.cod_banc AND b.cod_banc<>\'CAJ\'';
		$query = $this->db->query($sql);
		$comis=array();
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$ind='_'.$row->codbanc;
				$comis[$ind]['comitc']  =$row->comitc;
				$comis[$ind]['comitd']  =$row->comitd;
				$comis[$ind]['impuesto']=$row->impuesto;
			}
		}
		$json_comis=json_encode($comis);
		$script='
			comis=eval('.$json_comis.');
			function calcomis(){
				if($("#recibe").val().length>0){
					tasa='.$this->datasis->traevalor('tasa').';
					banco="_"+$("#recibe").val();
					eval("td=comis."+banco+".comitd;"  );
					eval("tc=comis."+banco+".comitc;"  );
					eval("im=comis."+banco+".impuesto;");

					if($("#tarjeta").val().length>0)  tarjeta=parseFloat($("#tarjeta").val());   else tarjeta =0;
					if($("#tdebito").val().length>0)  tdebito =parseFloat($("#tdebito").val());  else tdebito =0;

					islr    =tarjeta*10/(100+tasa);
					islr    =islr*(im/10);
					comision=tarjeta*(tc/100)+tdebito*(td/100);
					monto   =tarjeta+tdebito-comision-islr;

					$("#monto").val(roundNumber(monto,2));
					$("#comision").val(roundNumber(comision,2));
					$("#islr").val(roundNumber(islr,2));
				}
			}

			function totaliza(){
				if($("#tarjeta").val().length>0)  tarjeta =parseFloat($("#tarjeta").val());  else tarjeta =0;
				if($("#tdebito").val().length>0)  tdebito =parseFloat($("#tdebito").val());  else tdebito =0;
				if($("#comision").val().length>0) comision=parseFloat($("#comision").val()); else comision=0;
				if($("#islr").val().length>0)     islr    =parseFloat($("#islr").val());     else     islr=0;
				monto   =tarjeta+tdebito-comision-islr;
				$("#monto").val(roundNumber(monto,2));
			}';

		$this->rapyd->jquery[]='$("#tarjeta,#tdebito").bind("keyup",function() { calcomis(); });';
		$this->rapyd->jquery[]='$("#comision,#islr").bind("keyup",function() { totaliza(); });';
		$this->rapyd->jquery[]='$("#recibe").change(function() { calcomis(); });';
		$edit->script($script);

		$edit->envia->options( "SELECT TRIM(codbanc) AS codbanc,$desca FROM banc WHERE tbanco='CAJ'");

		$edit->recibe->options("SELECT TRIM(codbanc) AS codbanc,$desca FROM banc WHERE tbanco<>'CAJ'");
		$edit->recibe->rule='callback_chtr|required';

		$campos=array(
				'tarjeta' =>'Tarjeta de Cr&eacute;dito',
				'tdebito' =>'Tarjeta de D&eacute;bito',
				'comision'=>'Comisi&oacute;n',
				'islr'    =>'I.S.L.R.',
				'monto'   =>'Monto total');
		foreach($campos AS $obj=>$titulo){
			$edit->$obj = new inputField($titulo, $obj);
			$edit->$obj->css_class='inputnum';
			$edit->$obj->rule='trim|numeric';
			$edit->$obj->maxlength =15;
			$edit->$obj->size = 20;
			$edit->$obj->group = 'Montos';
			$edit->$obj->autocomplete=false;
		}
		$edit->$obj->readonly=true;

		$edit->envia->rule   = 'required';
		$edit->envia->style  = 'width:180px';
		$edit->recibe->style = 'width:180px';

		$back_url = site_url('finanzas/bcaj/agregar');
		$edit->button('btn_undo', 'Regresar', "javascript:window.location='${back_url}'", 'TR');

		$edit->submit('btnsubmit','Guardar');
		$edit->build_form();

		//**********************
		//  Guarda el efecto
		//**********************
		if ($edit->on_success()){
			$fecha   =$edit->fecha->newValue;
			$envia   =$edit->envia->newValue;
			$recibe  =$edit->recibe->newValue;
			$tarjeta =$edit->tarjeta->newValue;
			$tdebito =$edit->tdebito->newValue;
			$comision=$edit->comision->newValue;
			$islr    =$edit->islr->newValue;
			$numeror =$edit->numero->newValue;
			$tipo    =$edit->tipo->newValue;

			$rt=$this->_transferendeptar($fecha,$tarjeta,$tdebito,$comision,$islr,$envia,$recibe,$numeror,$tipo);
			if($rt){
				redirect('/finanzas/bcaj/listo');
			}else{
				redirect('/finanzas/bcaj/listo/s');
			}
		}

		$data['content'] = $edit->output;
		$data['title']   = heading('Deposito');
		$data['head']    = $this->rapyd->get_head().phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}


	//Auto transferencia
	function autotranfer(){
		$this->rapyd->load('dataform');
		$edit = new DataForm('finanzas/bcaj/autotranfer/process');
		$edit->title='Transferencia automatica entre cajas';

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule = 'chfecha|required';
		$edit->fecha->dbformat='Y-m-d';
		$edit->fecha->size=10;
		
		$back_url=site_url('finanzas/bcaj/agregar');
		$edit->button('btn_undo','Regresar',"javascript:window.location='$back_url'",'BL');
		$edit->submit('btnsubmit','Siguiente');
		$edit->build_form();
		if ($edit->on_success()){
			$fecha=$edit->fecha->newValue;
			redirect('finanzas/bcaj/autotranfer2/'.$fecha);
		}

		$data['content'] = $edit->output;
		$data['title']   = heading('Conciliaci&oacute;n de cierre');
		$data['head']    = $this->rapyd->get_head().phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function autotranfer2($fecha=null){
		$this->rapyd->load('dataform');
		$this->load->library('validation');
		$val=$this->validation->chfecha($fecha,'Y-m-d');
		if($val){
			$montosis=$this->_montoautotranf($this->cajas['cobranzas'],$fecha);
			if($montosis>0){

				$script='
					function totaliza(){
						if($("#tarjeta").val().length>0)  tarjeta =parseFloat($("#tarjeta").val());  else tarjeta =0;
						if($("#efectivo").val().length>0) efectivo=parseFloat($("#efectivo").val()); else efectivo=0;
						if($("#gastos").val().length>0)   gastos  =parseFloat($("#gastos").val());   else gastos  =0;
						if($("#valores").val().length>0)  valores =parseFloat($("#valores").val());  else valores =0;
						monto=tarjeta+gastos+efectivo+valores;
						$("#monto").val(roundNumber(monto,2));
					}';

				$edit = new DataForm("finanzas/bcaj/autotranfer2/$fecha/process");
				$edit->title='Transferencia automatica entre cajas';
				$edit->script($script);

				//$edit->back_url = site_url('finanzas/bcaj/index');

				/*$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
				$edit->fecha->insertValue = date('Y-m-d');
				$edit->fecha->rule = 'chfecha|required';
				$edit->fecha->dbformat='Y-m-d';
				$edit->fecha->append(HTML::button('traesaldo', 'Consultar monto', '', 'button', 'button'));
				$edit->fecha->size=10;*/

				$campos=array(
					'efectivo'=>'Efectivo caja: '.$this->cajas['efectivo'],
					'tarjeta' =>'Tarjeta de D&eacute;bito y Cr&eacute;dito caja: '.$this->cajas['tarjetas'],
					'gastos'  =>'Gastos por Justificar caja: '.$this->cajas['gastos'],
					'valores' =>'Valores, Cesta Tickes y Cheques caja: '.$this->cajas['valores'],
					'monto'   =>'Monto total');

				foreach($campos AS $obj=>$titulo){
					$edit->$obj = new inputField($titulo, $obj);
					$edit->$obj->css_class='inputnum';
					$edit->$obj->rule='trim|numeric';
					$edit->$obj->maxlength =15;
					$edit->$obj->size = 20;
					$edit->$obj->group = 'Montos';
					$edit->$obj->autocomplete=false;
				}
				$edit->$obj->rule='trim|numeric|callback_chtotal|required';
				$edit->$obj->readonly=true;

				$back_url=site_url('finanzas/bcaj/index');
				$edit->button('btn_undo','Regresar',"javascript:window.location='$back_url'",'BL');
				$edit->submit('btnsubmit','Guardar');
				$edit->build_form();

				$salida  = 'El monto total a tranferir para la fecha <b id="ffecha">'.dbdate_to_human($fecha).'</b> debe ser de: <b id="mmonto">'.nformat($montosis).'</b>';
				if ($edit->on_success()){
					//$fecha=$edit->fecha->newValue;
					foreach($campos AS $obj=>$titulo){
						$$obj=$edit->$obj->newValue;
					}
					if($montosis==$efectivo+$tarjeta+$gastos+$valores){
						$rt=$this->_autotranfer($fecha,$efectivo,$tarjeta,$gastos,$valores);
						if($rt){
							redirect('/finanzas/bcaj/listo');
						}else{
							redirect('/finanzas/bcaj/listo/s');
						}
					}else{
						$edit->error_string='El monto total a transferir debe ser de :<b>'.nformat($montosis).'</b>';
						$edit->build_form();
						//$salida .= $edit->output;
					}
				}
				$salida .= $edit->output;

				$url=site_url('finanzas/bcaj/ajaxmonto');
				$this->rapyd->jquery[]='$(".inputnum").numeric(".");';
				$this->rapyd->jquery[]='$(".inputnum").bind("keyup",function() { totaliza(); });';
				$this->rapyd->jquery[]='$("td").removeAttr("style");';
				$this->rapyd->jquery[]='$("input[name=\'traesaldo\']").click(function() {
					fecha=$("#fecha").val();
					if(fecha.length > 0){
						$.post("'.$url.'", { fecha: $("#fecha").val() },
							function(data){
								$("#mmonto").html(nformat(data));
								$("#ffecha").html($("#fecha").val());
								$(".alert").hide("slow");
							});
					}else{
						alert("Debe introducir una fecha");
					}
					});';

			}else{
				$dbfecha=$this->db->escape($fecha);
				$mSQL = "SELECT COUNT(*) AS cana FROM bcaj WHERE concep2='AUTOTRANFER' AND fecha=$dbfecha";
				$cana = $this->datasis->dameval($mSQL);
				if($cana>0){
					$salida = 'Ya fue hecha una tranferencias para la fecha dada, si desea puede reversarla haciendo click '.anchor('finanzas/bcaj/reverautotranfer/'.$fecha,'aqui').' ';
					$salida.= ' o puede '.anchor('finanzas/bcaj/index','regresar').' al inicio.';
				}else{
					$salida = 'No hay monto disponible para transferir '.anchor('finanzas/bcaj/autotranfer','Regresar');
				}
			}
		}else{
			show_error('Falta el parametro fecha');
		}

		$data['content'] = $salida;
		$data['title']   = '<h1>Conciliaci&oacute;n de cierre </h1>';
		$data['head']    = $this->rapyd->get_head().phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function ajaxmonto(){
		$fecha=$this->input->post('fecha');
		if($fecha!==false){
			$fecha=human_to_dbdate($fecha);
			$monto=$this->_montoautotranf($this->cajas['cobranzas'],$fecha);
		}else{
			$monto=0;
		}
		echo $monto;
	}

	//Metodo que reversa las tranferencias automaticas
	function reverautotranfer($fecha){
		$this->load->library('validation');
		$val  = $this->validation->chfecha($fecha,'Y-m-d');
		$error= 0;
		if($val){
			$rt=$this->_reverautotranfer($fecha);
			if($rt)
				redirect('finanzas/bcaj/listo');
			else
				redirect('finanzas/bcaj/listo/s');
		}
	}

	function _reverautotranfer($fecha){
		$dbfecha=$this->db->escape($fecha);
		$sp_fecha= str_replace('-','',$fecha);
		$mSQL="SELECT transac,monto,envia,recibe FROM bcaj WHERE fecha=$dbfecha AND concep2='AUTOTRANFER'";
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$transac=$this->db->escape($row->transac);
				$sql="DELETE FROM bmov WHERE transac=$transac";
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'bcaj'); $error++; }
				
				$monto=$row->monto;
				$sql='CALL sp_actusal('.$this->db->escape($row->envia).",'$sp_fecha',$monto)";
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'bcaj'); $error++; }

				$sql='CALL sp_actusal('.$this->db->escape($row->recibe).",'$sp_fecha',-$monto)";
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'bcaj'); $error++; }
			}
		}
		$sql="DELETE FROM bcaj WHERE fecha=$dbfecha AND concep2='AUTOTRANFER'";
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }
		return ($error==0)? true : false;
	}

	function _autotranfer($fecha,$efectivo=0,$tarjeta=0,$gastos=0,$valores=0){
		//$cajas=$this->config->item('cajas');
		$envia=$this->cajas['cobranzas'];
		$arr=array(
			'efectivo'=>$this->cajas['efectivo'],
			'tarjeta' =>$this->cajas['tarjetas'],
			'gastos'  =>$this->cajas['gastos'],
			'valores' =>$this->cajas['valores']
		);
		$rt=true;
		foreach($arr as $monto=>$recibe){
			if(!$this->_transferencaj($fecha,$$monto,$envia,$recibe,true))
				$rt=false;
		}
		return $rt;
	}

	function _transferencaj($fecha,$monto,$envia,$recibe,$auto=false){
		if($monto<=0) return true;
		$numero = $this->datasis->fprox_numero('nbcaj');
		$transac= $this->datasis->fprox_numero('ntransa');
		$numeroe= $this->datasis->banprox($envia);
		$numeror= $this->datasis->banprox($recibe);
		$sp_fecha= str_replace('-','',$fecha);
		$error  = 0;

		$mSQL='SELECT codbanc,numcuent,tbanco,banco,saldo FROM banc WHERE codbanc IN ('.$this->db->escape($envia).','.$this->db->escape($recibe).')';
		$query = $this->db->query($mSQL);
		$infbanc=array();
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$infbanc[$row->codbanc]['numcuent']=$row->numcuent;
				$infbanc[$row->codbanc]['tbanco']  =$row->tbanco;
				$infbanc[$row->codbanc]['banco']   =$row->banco;
				$infbanc[$row->codbanc]['saldo']   =$row->banco;
			}
		}

		$data=array(
			'tipo'    => 'TR',
			'fecha'   => $fecha,
			'numero'  => $numero,
			'transac' => $transac,
			'usuario' => $this->session->userdata('usuario'),
			'envia'   => $envia,
			'tipoe'   => 'ND',
			'numeroe' => $numeroe,
			'bancoe'  => $infbanc[$envia]['banco'],
			'recibe'  => $recibe,
			'tipor'   => 'NC',
			'numeror' => $numeror,
			'bancor'  => $infbanc[$recibe]['banco'],
			'concepto'=> 'TRANSFERENCIA ENTRE CAJA '.$envia.' A '.$recibe,
			'concep2' => ($auto)? 'AUTOTRANFER' : '',
			'benefi'  => '',
			'boleta'  => '',
			'precinto'=> '',
			'comprob' => '',
			'totcant' => '',
			'status'  => '',
			'estampa' => date('Ymd'),
			'hora'    => date('H:i:s'),
			'deldia'  => $fecha,
			'tarjeta' => 0,
			'tdebito' => 0,
			'cheques' => 0,
			'efectivo'=> $monto,
			'comision'=> 0,
			'islr'    => 0,
			'monto'   => $monto,
		);
		$sql=$this->db->insert_string('bcaj', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }

		//Crea el egreso en el banco
		$mSQL='CALL sp_actusal('.$this->db->escape($envia).",'$sp_fecha',-$monto)";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'bcaj'); $error++; }

		$data=array();
		$data['codbanc']  = $envia;
		$data['numcuent'] = $infbanc[$envia]['numcuent'];
		$data['banco']    = $infbanc[$envia]['banco'];
		$data['saldo']    = $infbanc[$envia]['saldo'];
		$data['tipo_op']  = 'ND';
		$data['numero']   = $numeroe;
		$data['fecha']    = $fecha;
		$data['clipro']   = 'O';
		$data['codcp']    = 'TRANS';
		$data['monto']    = $monto;
		$data['concepto'] = 'TRANSFERENCIAS ENTRE CAJA '.$envia.' A '.$recibe;
		$data['concep2']  = '';
		$data['transac']  = $transac;
		$data['usuario']  = $this->session->userdata('usuario');
		$data['estampa']  = date('Ymd');
		$data['hora']     = date('H:i:s');
		$data['benefi']   = '-';
		$sql=$this->db->insert_string('bmov', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }

		//Crea el ingreso la otra caja
		$mSQL='CALL sp_actusal('.$this->db->escape($recibe).",'$sp_fecha',$monto)";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'bcaj'); $error++; }

		$data=array();
		$data['codbanc']  = $recibe;
		$data['numcuent'] = $infbanc[$recibe]['numcuent'];
		$data['banco']    = $infbanc[$recibe]['banco'];
		$data['saldo']    = $infbanc[$recibe]['saldo'];
		$data['tipo_op']  = 'NC';
		$data['numero']   = $numeror;
		$data['fecha']    = $fecha;
		$data['clipro']   = 'O';
		$data['codcp']    = 'TRANS';
		$data['monto']    = $monto;
		$data['concepto'] = 'TRANSFERENCIAS ENTRE CAJA '.$envia.' A '.$recibe;
		$data['concep2']  = '';
		$data['transac']  = $transac;
		$data['usuario']  = $this->session->userdata('usuario');
		$data['estampa']  = date('Ymd');
		$data['hora']     = date('H:i:s');
		$data['benefi']   = '-';
		$sql=$this->db->insert_string('bmov', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }
		logusu('bcaj',"Transferencia de caja $numero creada");

		return ($error==0) ? true : false;
	}

	function _transferendepefe($fecha,$efectivo,$cheque,$envia,$recibe,$numeror){
		$monto=$efectivo+$cheque;
		if($monto<=0) return true;
		$numero = $this->datasis->fprox_numero('nbcaj');
		$transac= $this->datasis->fprox_numero('ntransa');
		$numeroe= $this->datasis->banprox($envia);
		//$numeror= $this->datasis->banprox($recibe);
		$sp_fecha= str_replace('-','',$fecha);
		$error  = 0;

		$mSQL='SELECT codbanc,numcuent,tbanco,banco,saldo FROM banc WHERE codbanc IN ('.$this->db->escape($envia).','.$this->db->escape($recibe).')';
		$query = $this->db->query($mSQL);
		$infbanc=array();
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$infbanc[$row->codbanc]['numcuent']=$row->numcuent;
				$infbanc[$row->codbanc]['tbanco']  =$row->tbanco;
				$infbanc[$row->codbanc]['banco']   =$row->banco;
				$infbanc[$row->codbanc]['saldo']   =$row->banco;
			}
		}

		$data=array(
			'tipo'    => 'DE',
			'fecha'   => $fecha,
			'numero'  => $numero,
			'transac' => $transac,
			'usuario' => $this->session->userdata('usuario'),
			'envia'   => $envia,
			'tipoe'   => 'ND',
			'numeroe' => $numeroe,
			'bancoe'  => $infbanc[$envia]['banco'],
			'recibe'  => $recibe,
			'tipor'   => 'DE',
			'numeror' => $numeror,
			'bancor'  => $infbanc[$recibe]['banco'],
			'concepto'=> 'DEPOSITO ENTRE CAJA '.$envia.' A BANCO '.$recibe,
			'concep2' => ($auto)? 'AUTOTRANFER' : '',
			'benefi'  => '',
			'boleta'  => '',
			'precinto'=> '',
			'comprob' => '',
			'totcant' => '',
			'status'  => '',
			'estampa' => date('Ymd'),
			'hora'    => date('H:i:s'),
			'deldia'  => $fecha,
			'tarjeta' => 0,
			'tdebito' => 0,
			'cheques' => $cheque,
			'efectivo'=> $efectivo,
			'comision'=> 0,
			'islr'    => 0,
			'monto'   => $monto,
		);
		$sql=$this->db->insert_string('bcaj', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }

		//Crea el egreso en el banco
		$mSQL='CALL sp_actusal('.$this->db->escape($envia).",'$sp_fecha',-$monto)";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'bcaj'); $error++; }

		$data=array();
		$data['codbanc']  = $envia;
		$data['numcuent'] = $infbanc[$envia]['numcuent'];
		$data['banco']    = $infbanc[$envia]['banco'];
		$data['saldo']    = $infbanc[$envia]['saldo'];
		$data['tipo_op']  = 'ND';
		$data['numero']   = $numeroe;
		$data['fecha']    = $fecha;
		$data['clipro']   = 'O';
		$data['codcp']    = 'TRANS';
		$data['monto']    = $monto;
		$data['concepto'] = 'TRANSFERENCIAS ENTRE CAJA '.$envia.' A '.$recibe;
		$data['concep2']  = '';
		$data['transac']  = $transac;
		$data['usuario']  = $this->session->userdata('usuario');
		$data['estampa']  = date('Ymd');
		$data['hora']     = date('H:i:s');
		$data['benefi']   = '-';
		$sql=$this->db->insert_string('bmov', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }

		//Crea el ingreso la otra caja
		$mSQL='CALL sp_actusal('.$this->db->escape($recibe).",'$sp_fecha',$monto)";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'bcaj'); $error++; }

		$data=array();
		$data['codbanc']  = $recibe;
		$data['numcuent'] = $infbanc[$recibe]['numcuent'];
		$data['banco']    = $infbanc[$recibe]['banco'];
		$data['saldo']    = $infbanc[$recibe]['saldo'];
		$data['tipo_op']  = 'NC';
		$data['numero']   = $numeror;
		$data['fecha']    = $fecha;
		$data['clipro']   = 'O';
		$data['codcp']    = 'TRANS';
		$data['monto']    = $monto;
		$data['concepto'] = 'TRANSFERENCIAS ENTRE CAJA '.$envia.' A '.$recibe;
		$data['concep2']  = '';
		$data['transac']  = $transac;
		$data['usuario']  = $this->session->userdata('usuario');
		$data['estampa']  = date('Ymd');
		$data['hora']     = date('H:i:s');
		$data['benefi']   = '-';
		$sql=$this->db->insert_string('bmov', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }
		logusu("Transferencia de caja $numero creada");

		return ($error==0) ? true : false;
	}


	function _transferendeptar($fecha,$tarjeta,$tdebito,$comision,$islr,$envia,$recibe,$numeror,$tipo){
		$monto=$tarjeta+$tdebito;
		if($monto<=0) return true;
		$numero  = $this->datasis->fprox_numero('nbcaj');
		$transac = $this->datasis->fprox_numero('ntransa');
		$numeroe = $this->datasis->banprox($envia);
		$dbrecibe= $this->db->escape($recibe);
		$sp_fecha= str_replace('-','',$fecha);
		$error   = 0;

		$mSQL="SELECT a.tipotra ,a.formaca FROM tban AS a JOIN banc AS b ON a.cod_banc=b.tbanco WHERE a.cod_banc=$dbrecibe";
		$parr=$this->datasis->damerow($mSQL);
		$formaca=(empty($parr['formaca']) OR $parr['formaca']=='NETA')? 'NETA': 'BRUTA';

		$mSQL='SELECT codbanc,numcuent,tbanco,banco,saldo,codprv,gastocom,depto FROM banc WHERE codbanc IN ('.$this->db->escape($envia).','.$this->db->escape($recibe).')';
		$query = $this->db->query($mSQL);
		$infbanc=array();
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$infbanc[$row->codbanc]['numcuent' ]=$row->numcuent;
				$infbanc[$row->codbanc]['tbanco']   =$row->tbanco;
				$infbanc[$row->codbanc]['banco']    =$row->banco;
				$infbanc[$row->codbanc]['saldo']    =$row->banco;
				$infbanc[$row->codbanc]['codprv']   =$row->codprv;
				$infbanc[$row->codbanc]['gastocom'] =$row->gastocom;
				$infbanc[$row->codbanc]['depto']    =$row->depto;
			}
		}

		$data=array(
			'tipo'    => 'DE',
			'fecha'   => $fecha,
			'numero'  => $numero,
			'transac' => $transac,
			'usuario' => $this->session->userdata('usuario'),
			'envia'   => $envia,
			'tipoe'   => 'ND',
			'numeroe' => $numeroe,
			'bancoe'  => $infbanc[$envia]['banco'],
			'recibe'  => $recibe,
			'tipor'   => $tipo,
			'numeror' => $numeror,
			'bancor'  => $infbanc[$recibe]['banco'],
			'concepto'=> 'DEP/TARJETAS DE '.$envia.' A BANCO '.$recibe,
			'concep2' => '',
			'benefi'  => $this->datasis->traevalor('TITULO1'),
			'boleta'  => '',
			'precinto'=> '',
			'comprob' => '',
			'totcant' => 0,
			'status'  => '',
			'estampa' => date('Ymd'),
			'hora'    => date('H:i:s'),
			'deldia'  => $fecha,
			'tarjeta' => $tarjeta,
			'tdebito' => $tdebito,
			'cheques' => 0,
			'efectivo'=> 0,
			'comision'=> $comision,
			'islr'    => $islr,
			'monto'   => $tarjeta+$tdebito-$comision-$islr,
		);
		$sql=$this->db->insert_string('bcaj', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }

		//Crea el egreso en el banco
		$data=array();
		$data['codbanc']  = $envia;
		$data['numcuent'] = $infbanc[$envia]['numcuent'];
		$data['banco']    = $infbanc[$envia]['banco'];
		$data['saldo']    = $infbanc[$envia]['saldo'];
		$data['tipo_op']  = 'ND';
		$data['numero']   = $numeroe;
		$data['fecha']    = $fecha;
		$data['clipro']   = 'O';
		$data['codcp']    = 'CAJAS';
		$data['nombre']   = 'DEPOSITO DESDE CAJA';
		$data['monto']    = $tarjeta+$tdebito;
		$data['concepto'] = 'DEP/TARJETAS DE '.$envia.' A '.$recibe;
		$data['concep2']  = '';
		$data['comprob']  = $numero;
		$data['transac']  = $transac;
		$data['usuario']  = $this->session->userdata('usuario');
		$data['estampa']  = date('Ymd');
		$data['hora']     = date('H:i:s');
		$sql=$this->db->insert_string('bmov', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }

		$mSQL='CALL sp_actusal('.$this->db->escape($envia).",'$sp_fecha',-$data[monto])";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'bcaj'); $error++; }

		//Crea el ingreso la otra caja

		$data=array();
		$data['codbanc']  = $recibe;
		$data['numcuent'] = $infbanc[$recibe]['numcuent'];
		$data['banco']    = $infbanc[$recibe]['banco'];
		$data['saldo']    = $infbanc[$recibe]['saldo'];
		$data['tipo_op']  = 'NC';
		$data['numero']   = $numeror;
		$data['fecha']    = $fecha;
		$data['clipro']   = 'O';
		$data['codcp']    = 'CAJAS';
		$data['comision'] = $comision;
		$data['impuesto'] = $islr;
		$data['monto']    = ($formaca=='NETA')?  $tarjeta+$tdebito-$islr-$comision : $tarjeta+$tdebito ;
		$data['nombre']   = 'DEPOSITO DESDE CAJA';
		$data['concepto'] = 'DEP/TARJETAS DE '.$envia.' A '.$recibe;;
		$data['concep2']  = '';
		$data['bruto']    = $tarjeta;
		$data['comprob']  = $numero;
		$data['documen']  = $numero;
		$data['transac']  = $transac;
		$data['usuario']  = $this->session->userdata('usuario');
		$data['estampa']  = date('Ymd');
		$data['hora']     = date('H:i:s');
		$sql=$this->db->insert_string('bmov', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }

		$mSQL='CALL sp_actusal('.$this->db->escape($recibe).",'$sp_fecha',$data[monto])";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'bcaj'); $error++; }


		if($comision>0){
			if($formaca=='BRUTA'){
				$data=array();
				$data['codbanc']  = $recibe;
				$data['numcuent'] = $infbanc[$recibe]['numcuent'];
				$data['banco']    = $infbanc[$recibe]['banco'];
				$data['saldo']    = $infbanc[$recibe]['saldo'];
				$data['tipo_op']  = 'ND';
				$data['numero']   = 'C'.substr($numeror,1);
				$data['fecha']    = $fecha;
				$data['clipro']   = 'O';
				$data['codcp']    = 'CAJAS';
				$data['comision'] = $comision;
				$data['impuesto'] = $islr;
				$data['monto']    = $comision;
				$data['nombre']   = 'COMISION POR TC/TD';
				$data['concepto'] = 'COMISION POR TC/TD';
				$data['concep2']  = '';
				$data['bruto']    = $tarjeta;
				$data['comprob']  = $numero;
				$data['documen']  = $numero;
				$data['transac']  = $transac;
				$data['usuario']  = $this->session->userdata('usuario');
				$data['estampa']  = date('Ymd');
				$data['hora']     = date('H:i:s');
				$sql=$this->db->insert_string('bmov', $data);
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'bcaj'); $error++; }
			}

			$data=array();
			$data['fecha']    = $fecha;
			$data['numero']   = 'CTC'.substr($numero,-5);
			$data['proveed']  = $infbanc[$recibe]['codprv'];
			$data['nombre']   = $this->datasis->dameval('SELECT nombre FROM sprv WHERE proveed='.$this->db->escape($infbanc[$recibe]['codprv']));
			$data['vence']    = $fecha;
			$data['totpre']   = $comision;
			$data['totiva']   = 0;
			$data['totbruto'] = $comision;
			$data['reten']    = 0;
			$data['totneto']  = $comision;
			$data['codb1']    = $envia;
			$data['cheque1']  = $numeroe;
			$data['tipo1']    = 'D';
			$data['monto1']   = $comision;
			$data['codb2']    = '';
			$data['tipo2']    = '';
			$data['cheque2']  = '';
			$data['comprob2'] = '';
			$data['monto2']   = 0;
			$data['codb3']    = '';
			$data['tipo3']    = '';
			$data['cheque3']  = '';
			$data['comprob3'] = '';
			$data['monto3']   = 0;
			$data['credito']  = 0;
			$data['anticipo'] = 0;
			$data['orden']    = '';
			$data['tipo_doc'] = 'FC';
			$data['transac']  = $transac;
			$data['usuario']  = $this->session->userdata('usuario');
			$data['estampa']  = date('Ymd');
			$data['hora']     = date('H:i:s');
			$sql=$this->db->insert_string('gser', $data);
			$ban=$this->db->simple_query($sql);
			if($ban==false){ memowrite($sql,'bcaj'); $error++; }

			$data=array();
			$data['fecha']    = $fecha;
			$data['numero']   = 'CTC'.substr($numero,-5);
			$data['proveed']  = $infbanc[$recibe]['codprv'];
			$data['codigo']   = $infbanc[$recibe]['gastocom'];
			$data['descrip']  = 'COMISION POR TARJETAS '.$infbanc[$recibe]['banco'];
			$data['precio']   = $comision;
			$data['iva']      = 0;
			$data['importe']  = $comision;
			$data['unidades'] = 0;
			$data['fraccion'] = 0;
			$data['almacen']  = '';
			$data['departa']  = $infbanc[$recibe]['depto'];
			$data['sucursal'] = ' ';
			$data['transac']  = $transac;
			$data['usuario']  = $this->session->userdata('usuario');
			$data['estampa']  = date('Ymd');
			$data['hora']     = date('H:i:s');
			$sql=$this->db->insert_string('gitser', $data);
			$ban=$this->db->simple_query($sql);
			if($ban==false){ memowrite($sql,'bcaj'); $error++; }
		}

		if($islr>0){
			if($formaca=='BRUTA'){
				$data=array();
				$data['codbanc']  = $recibe;
				$data['numcuent'] = $infbanc[$recibe]['numcuent'];
				$data['banco']    = $infbanc[$recibe]['banco'];
				$data['saldo']    = $infbanc[$recibe]['saldo'];
				$data['tipo_op']  = 'ND';
				$data['numero']   = 'R'.substr($numeror,1);
				$data['fecha']    = $fecha;
				$data['clipro']   = 'O';
				$data['codcp']    = 'CAJAS';
				$data['comision'] = $comision;
				$data['impuesto'] = $islr;
				$data['monto']    = $islr;
				$data['nombre']   = 'RETENCION DE ISLR POR TC';
				$data['concepto'] = 'RETENCION DE ISLR POR TC';
				$data['concep2']  = '';
				$data['bruto']    = $tarjeta;
				$data['comprob']  = $numero;
				$data['documen']  = $numero;
				$data['transac']  = $transac;
				$data['usuario']  = $this->session->userdata('usuario');
				$data['estampa']  = date('Ymd');
				$data['hora']     = date('H:i:s');
				$sql=$this->db->insert_string('bmov', $data);
				$ban=$this->db->simple_query($sql);
				if($ban==false){ memowrite($sql,'bcaj'); $error++; }
			}
			$nccli = $this->datasis->fprox_numero('nccli');
			$nsmov = $this->datasis->fprox_numero('nsmov');
			$ff    = str_replace('-','',$fecha);
			$udia  = days_in_month(substr($ff,0,4),substr($ff,4,2));

			$data=array();
			$data['cod_cli']  = 'RETED';
			$data['nombre']   = 'RETENCION I.S.L.R. TDC/BANCOS';
			$data['tipo_doc'] = 'ND';
			$data['numero']   = $nccli;
			$data['fecha']    = $fecha;
			$data['monto']    = $islr;
			$data['impuesto'] = 0;
			$data['vence']    = substr($ff,0,6).$udia;
			$data['tipo_ref'] = 'DC';
			$data['num_ref']  = '';
			$data['observa1'] = 'RET/ISLR TC POR DEP '.$infbanc[$recibe]['banco'];
			$data['observa2'] = '';
			$data['control']  = $nsmov;
			$data['transac']  = $transac;
			$data['usuario']  = $this->session->userdata('usuario');
			$data['estampa']  = date('Ymd');
			$data['hora']     = date('H:i:s');
			$sql=$this->db->insert_string('smov', $data);
			$ban=$this->db->simple_query($sql);
			if($ban==false){ memowrite($sql,'bcaj'); $error++; }
		}

		logusu('bcaj',"Transferencia de caja $numero creada");

		return ($error==0) ? true : false;
	}


	//Metodo para las tranferencias por deposito
	function _transferendep($fecha,$tarjeta,$tdebito,$cheque,$efectivo,$comision,$islr,$envia,$recibe){
		if($monto<=0) return true;
		$numero  = $this->datasis->fprox_numero('nbcaj');
		$transac = $this->datasis->fprox_numero('ntransa');
		$numeroe = $this->datasis->banprox($envia);
		$numeror = $this->datasis->banprox($recibe);
		$sp_fecha= str_replace('-','',$fecha);
		$error   = 0;

		$mSQL='SELECT codbanc,numcuent,tbanco,banco,saldo FROM banc WHERE codbanc IN ('.$this->db->escape($envia).','.$this->db->escape($recibe).')';
		$query = $this->db->query($mSQL);
		$infbanc=array();
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$infbanc[$row->codbanc]['numcuent']=$row->numcuent;
				$infbanc[$row->codbanc]['tbanco']  =$row->tbanco;
				$infbanc[$row->codbanc]['banco']   =$row->banco;
				$infbanc[$row->codbanc]['saldo']   =$row->banco;
			}
		}

		$monto = $tarjeta+$tdebito+$cheques-$comision-$islr;
		$data=array(
			'tipo'    => 'DE',
			'fecha'   => $fecha,
			'numero'  => $numero,
			'transac' => $transac,
			'usuario' => $this->session->userdata('usuario'),
			'envia'   => $edit->envia->newValue,
			'tipoe'   => 'ND',
			'numeroe' => $numeroe,
			'bancoe'  => $infbanc[$envia]['banco'],
			'recibe'  => $edit->recibe->newValue,
			'tipor'   => 'NC',
			'numeror' => $numeror,
			'bancor'  => $infbanc[$recibe]['banco'],
			'concepto'=> 'DEPOSITO ENTRE '.$envia.' A '.$recibe,
			'concep2' => '',
			'benefi'  => '',
			'boleta'  => '',
			'precinto'=> '',
			'comprob' => '',
			'totcant' => '',
			'status'  => '',
			'deldia'  => $fecha,
			'tarjeta' => $edit->tarjeta->newValue,
			'tdebito' => $edit->tdebito->newValue,
			'cheques' => $edit->cheques->newValue,
			'efectivo'=> $edit->efectivo->newValue,
			'comision'=> $edit->comision->newValue,
			'islr'    => $edit->islr->newValue,
			'monto'   => $monto,
			'estampa' => date('Ymd'),
			'hora'    => date('H:i:s'),
		);

		$sql=$this->db->insert_string('bcaj', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }

		//Crea el egreso en el banco
		$mSQL='CALL sp_actusal('.$this->db->escape($envia).",'$sp_fecha',-$monto)";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'bcaj'); $error++; }

		$data=array();
		$data['codbanc']  = $envia;
		$data['numcuent'] = $infbanc[$envia]['numcuent'];
		$data['banco']    = $infbanc[$envia]['banco'];
		$data['saldo']    = $infbanc[$envia]['saldo'];
		$data['tipo_op']  = 'ND';
		$data['numero']   = $numeroe;
		$data['fecha']    = $fecha;
		$data['clipro']   = 'O';
		$data['codcp']    = 'TRANS';
		$data['monto']    = $monto;
		$data['concepto'] = 'DEPOSITO ENTRE CAJA '.$envia.' A '.$recibe;
		$data['concep2']  = '';
		$data['transac']  = $transac;
		$data['usuario']  = $this->session->userdata('usuario');
		$data['estampa']  = date('Ymd');
		$data['hora']     = date('H:i:s');
		$data['benefi']   = '-';
		$sql=$this->db->insert_string('bmov', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }

		//Crea el ingreso la otra caja
		
		$mSQL='CALL sp_actusal('.$this->db->escape($recibe).",'$sp_fecha',$monto)";
		$ban=$this->db->simple_query($mSQL);
		if($ban==false){ memowrite($mSQL,'bcaj'); $error++; }

		$data=array();
		$data['codbanc']  = $recibe;
		$data['numcuent'] = $infbanc[$recibe]['numcuent'];
		$data['banco']    = $infbanc[$recibe]['banco'];
		$data['saldo']    = $infbanc[$recibe]['saldo'];
		$data['tipo_op']  = 'NC';
		$data['numero']   = $numeror;
		$data['fecha']    = $fecha;
		$data['clipro']   = 'O';
		$data['codcp']    = 'TRANS';
		$data['monto']    = $monto;
		$data['concepto'] = 'TRANSFERENCIAS ENTRE CAJA '.$envia.' A '.$recibe;
		$data['concep2']  = '';
		$data['transac']  = $transac;
		$data['usuario']  = $this->session->userdata('usuario');
		$data['estampa']  = date('Ymd');
		$data['hora']     = date('H:i:s');
		$data['benefi']   = '-';
		$sql=$this->db->insert_string('bmov', $data);
		$ban=$this->db->simple_query($sql);
		if($ban==false){ memowrite($sql,'bcaj'); $error++; }
		return ($error==0) ? true : false;
	}

	function _montoautotranf($caja,$fecha){
		$dbfecha=$this->db->escape($fecha);
		$dbcaja =$this->db->escape($caja);
		$mSQL="SELECT SUM(if(tipo_op IN ('NC','DE'),1,-1)*monto) AS monto FROM bmov WHERE codbanc=$dbcaja AND fecha=$dbfecha";
		$monto=$this->datasis->dameval($mSQL);

		return (empty($monto))? 0 : $monto;
	}

	function chtotal($monto){
		$monto =0;
		$monto+=floatval($this->input->post('efectivo'));
		$monto+=floatval($this->input->post('tarjeta' ));
		$monto+=floatval($this->input->post('gastos'  ));
		$monto+=floatval($this->input->post('valores' ));

		if($monto>0){
			return true;
		}else{
			$this->validation->set_message('chtotal', 'No puede guardar una transferencia en 0');
			return false;
		}
	}

	//Transferencia entre cajas
	function tranferencaj(){
		$this->rapyd->load('dataform');
		$desca='CONCAT_WS(\'-\',codbanc,banco) AS desca';

		$edit = new DataForm('finanzas/bcaj/tranferencaj/process');
		$edit->title='Transferencia entre cajas';

		$edit->back_url = site_url('finanzas/bcaj/index');

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule = 'chfecha|required';
		$edit->fecha->dbformat='Y-m-d';
		$edit->fecha->size=10;

		$edit->envia = new dropdownField('Envia','envia');
		$edit->envia->option('','Seleccionar');
		$edit->envia->options("SELECT  codbanc,$desca FROM banc WHERE tbanco='CAJ'");
		$edit->envia->style = 'width:180px';
		$edit->envia->rule  = 'required';

		$edit->recibe = new dropdownField('Recibe','recibe');
		$edit->recibe->option('','Seleccionar');
		$edit->recibe->options("SELECT  codbanc,$desca FROM banc WHERE tbanco='CAJ'");
		$edit->recibe->style = 'width:180px';
		$edit->recibe->rule  = 'required';

		$edit->monto = new inputField('Monto', 'monto');
		$edit->monto->css_class='inputnum';
		$edit->monto->rule='trim|numeric|required';
		$edit->monto->maxlength =15;
		$edit->monto->size = 20;
		$edit->monto->autocomplete=false;

		$edit->submit('btnsubmit','Guardar');
		$edit->build_form();
		$salida=$edit->output;

		if ($edit->on_success()){
			$fecha  = $edit->fecha->newValue;
			$monto  = $edit->monto->newValue;
			$envia  = $edit->envia->newValue;
			$recibe = $edit->recibe->newValue;
			$this->_transferencaj($fecha,$monto,$envia,$recibe);
			redirect('/finanzas/bcaj/listo');
		}

		$data=array();
		$this->rapyd->jquery[]='$(".inputnum").numeric(".");';
		$data['content'] = $salida;
		$data['title']   = '<h1>Transferencias entre cajas</h1>';
		$data['head']    = $this->rapyd->get_head().phpscript('nformat.js');
		$this->load->view('view_ventanas', $data);
	}

	function listo($error=null){
		if(empty($error)){
			$data['content'] = 'Transacci&oacute;n completada '.anchor('finanzas/bcaj/index','Regresar');
		}else{
			$data['content'] = 'Lo siento pero hubo alg&uacute;n error en la transacci&oacute;n, se genero un centinela '.anchor('finanzas/bcaj/index','Regresar');
		}
		$data['title']   = '<h1>Transferencias entre cajas</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function get_trrecibe(){
		$codigo=$this->input->post('envia');
		echo "<option value=''>Seleccionar</option>";

		if($codigo!==false){
			$tipo= $this->_traetipo($codigo);

			if(!empty($tipo)){
				$ww=($tipo=='CAJ') ? 'AND tbanco="CAJ"' : '';
				$desca='CONCAT_WS(\'-\',codbanc,banco) AS desca';
				$mSQL=$this->db->query("SELECT codbanc,$desca FROM banc WHERE codbanc<>".$this->db->escape($codigo)." $ww ORDER BY banco");
				if($mSQL){
					foreach($mSQL->result() AS $fila )
						echo "<option value='".$fila->codbanc."'>".$fila->desca."</option>";
				}
			}
		}
	}

	function _traetipo($codigo){
		$sql='SELECT tbanco FROM banc WHERE codbanc='.$this->db->escape($codigo);
		return $this->datasis->dameval($sql);
	}

	function chtr(){
		$recibe=$this->input->post('recibe');
	}
}