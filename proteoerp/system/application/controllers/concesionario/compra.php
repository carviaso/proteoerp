<?php
/** 
 * ProteoERP 
 * 
 * @autor    Andres Hocevar 
 * @license  GNU GPL v3
*/
require_once(BASEPATH.'application/controllers/compras/scst.php');
class compra extends scst {

	var $titp='Veh&iacute;culos';
	var $tits='Compra de Veh&iacute;culos';
	var $url ='concesionario/compra/';

	function compra(){
		parent::Controller();
		$this->back_dataedit='compras/scst/datafilter';
		$this->load->library('rapyd');
		$this->datasis->modulo_id(210,1);
	}

	function index(){
		$this->rapyd->load('dataedit');
		$iva  = $this->datasis->ivaplica();

		$sprvbus=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo Proveedor',
				'nombre'=>'Nombre',
				'rif'=>'RIF'),
			'filtro'  => array('proveed'=>'C&oacute;digo Proveedor','nombre'=>'Nombre'),
			'retornar'=> array('proveed'=>'proveed', 'nombre'=>'nombre'),
			'script'  => array('post_modbus_sprv()'),
			'titulo'  =>'Buscar Proveedor');

		$modbus=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'codigo_0','descrip'=>'descrip_0','peso'=>'peso'),
			'script'  => array('post_modbus_sinv()'),
			'titulo'  =>'Buscar Art&iacute;culo',
			'where'   =>'activo = "S"');

		$jsc='function calcula(){
			if($("#vh_tasa").val().length>0){
				tasa=parseFloat($("#vh_tasa").val());
				if($("#vh_base").val().length>0) base=parseFloat($("#vh_base").val()); else base=0;
				$("#montoiva").val(roundNumber(base*(tasa/100),2));
			}
		}

		function calculaiva(){
			if($("#vh_tasa").val().length>0){
				tasa=parseFloat($("#vh_tasa").val());
				if($("#vh_montoiva").val().length>0) montoiva=parseFloat($("#montoiva").val()); else montoiva=0;
				$("#vh_base").val(roundNumber(montoiva*100/tasa,2));
			}
		}

		function post_modbus_sinv(){
			$("#descrip_0_val").text($("#descrip_0").val());
		}

		function post_modbus_sprv(){
			$("#nombre_val").text($("#nombre").val());
		}
		';

		$edit = new DataForm($this->url.'compra/index/insert');

		$edit->back_url = site_url($this->url.'filteredgrid');

		$edit->proveed = new inputField('Proveedor', 'proveed');
		$edit->proveed->size     = 7;
		$edit->proveed->maxlength= 5;
		$edit->proveed->autocomplete=false;
		$edit->proveed->rule     = 'required';
		$edit->proveed->append($this->datasis->modbus($sprvbus));
		$edit->proveed->group = 'Datos de la factura';

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->size = 50;
		$edit->nombre->maxlength=40;
		$edit->nombre->type  = 'inputhidden';
		$edit->nombre->group = 'Datos de la factura';
		$edit->nombre->in = 'proveed';

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size = 10;
		$edit->fecha->rule ='required';
		$edit->fecha->group = 'Datos de la factura';

		$edit->vence = new DateonlyField('Vence', 'vence','d/m/Y');
		$edit->vence->insertValue = date('Y-m-d');
		$edit->vence->size = 10;
		$edit->vence->rule ='required';
		$edit->vence->group = 'Datos de la factura';

		$edit->serie = new inputField('N&uacute;mero de factura', 'serie');
		$edit->serie->size = 15;
		$edit->serie->autocomplete=false;
		$edit->serie->rule = 'required';
		$edit->serie->mode = 'autohide';
		$edit->serie->maxlength=12;
		$edit->serie->group = 'Datos de la factura';

		$edit->cfis = new inputField('Control f&iacute;scal', 'nfiscal');
		$edit->cfis->size = 15;
		$edit->cfis->autocomplete=false;
		$edit->cfis->rule = 'required';
		$edit->cfis->maxlength=12;
		$edit->cfis->group = 'Datos de la factura';

		$edit->almacen = new  dropdownField ('Almac&eacute;n', 'depo');
		$edit->almacen->options('SELECT ubica, CONCAT(ubica,\' \',ubides) nombre FROM caub ORDER BY ubica');
		$edit->almacen->rule = 'required';
		$edit->almacen->style='width:145px;';
		$edit->almacen->group = 'Datos de la factura';

		$edit->codigo = new inputField('C&oacute;digo', 'codigo_0');
		$edit->codigo->size=10;
		$edit->codigo->db_name='codigo';
		$edit->codigo->append($this->datasis->modbus($modbus));
		$edit->codigo->autocomplete=false;
		$edit->codigo->db_name  = 'codigo';
		$edit->codigo->rule     = 'required|callback_chcodigoa';
		$edit->codigo->group = 'Datos del veh&iacute;culo';

		$edit->descrip = new inputField('Modelo', 'descrip_0');
		$edit->descrip->size     = 30;
		$edit->descrip->db_name  = 'descrip';
		$edit->descrip->type     = 'inputhidden';
		$edit->descrip->group    = 'Datos del veh&iacute;culo';
		$edit->descrip->in='codigo';

		$edit->anio = new inputField('A&ntildeo','vh_anio');
		$edit->anio->rule='exact_length[4]|numeric|required';
		$edit->anio->size =5;
		$edit->anio->maxlength =4;
		$edit->anio->insertValue=date('Y');
		$edit->anio->autocomplete=false;
		$edit->anio->group = 'Datos del veh&iacute;culo';

		$edit->color = new inputField('Color','vh_color');
		$edit->color->rule='max_length[50]|strtoupper|required';
		$edit->color->size =52;
		$edit->color->maxlength =50;
		$edit->color->autocomplete=false;
		$edit->color->group = 'Datos del veh&iacute;culo';

		$edit->motor = new inputField('Serial de Motor','vh_motor');
		$edit->motor->rule='max_length[50]|strtoupper|callback_chrepetido[motor]|required';
		$edit->motor->size =52;
		$edit->motor->maxlength =50;
		$edit->motor->autocomplete=false;
		$edit->motor->group = 'Datos del veh&iacute;culo';

		$edit->carroceria = new inputField('Serial de Carrocer&iacute;a','vh_carroceria');
		$edit->carroceria->rule='max_length[50]|strtoupper|callback_chrepetido[carroceria]|required';
		$edit->carroceria->size =52;
		$edit->carroceria->maxlength =50;
		$edit->carroceria->autocomplete=false;
		$edit->carroceria->group = 'Datos del veh&iacute;culo';

		$edit->uso = new  dropdownField('Tipo de uso','vh_uso');
		$edit->uso->option('P','Particular');
		$edit->uso->option('T','Trabajo');
		$edit->uso->option('C','Carga');
		$edit->uso->style='width:200px;';
		$edit->uso->size = 6;
		$edit->uso->rule='required';
		$edit->uso->group = 'Datos del veh&iacute;culo';

		$edit->tipo = new  dropdownField('Tipo','vh_tipo');
		$edit->tipo->option('UTILITARIO'        ,'Utilitario');
		$edit->tipo->option('CHASIS'            ,'Chasis');
		$edit->tipo->option('CAVA REFRIGERADA'  ,'Cava Refrigerada');
		$edit->tipo->option('CAVA TERMINA'      ,'Cava Termina');
		$edit->tipo->option('CAVA SECA'         ,'Cava Seca');
		$edit->tipo->option('PLATAFORMA'        ,'Plataforma');
		$edit->tipo->option('PLATAFORMA GRUA'   ,'Plataforma Grua');
		$edit->tipo->option('PLATAFORMA BARANDA','Plataforma Barandas');
		$edit->tipo->option('AUTOBUS'           ,'Autobus');
		$edit->tipo->option('VOLTEO'            ,'Volteo');
		$edit->tipo->option('CUADRILLERO'       ,'Cuadrillero');
		$edit->tipo->option('CHUTO'             ,'Chuto');
		$edit->tipo->option('TANQUE'            ,'Tanque');
		$edit->tipo->option('JAULA GANADERA'    ,'Jaula Ganadera');
		$edit->tipo->option('FERRETERO'         ,'Ferretero');
		$edit->tipo->option('AMBULACIA'         ,'Ambulacia');

		$edit->tipo->style='width:200px;';
		$edit->tipo->size = 6;
		$edit->tipo->rule='required';
		$edit->tipo->group = 'Datos del veh&iacute;culo';

		$edit->clase = new  dropdownField('Clase','vh_clase');
		$edit->clase->option('AUTOMOVIL','Automovil');
		$edit->clase->option('CAMIONETA','Camioneta');
		$edit->clase->option('CAMION'   ,'Camion');
		$edit->clase->style='width:200px;';
		$edit->clase->size = 6;
		$edit->clase->rule='required';
		$edit->clase->group = 'Datos del veh&iacute;culo';

		$edit->transmision = new  dropdownField('Transmisi&oacute;n','vh_transmision');
		$edit->transmision->option('','Seleccionar');
		$edit->transmision->option('AUTOMATICO','Automatico');
		$edit->transmision->option('MANUAL'    ,'Manual');
		$edit->transmision->option('ZF'     ,'ZF');
		$edit->transmision->option('ZF-ITON','ZF-ITON');
		$edit->transmision->style='width:200px;';
		$edit->transmision->size = 6;
		$edit->transmision->rule='required';
		$edit->transmision->group = 'Datos del veh&iacute;culo';

		$edit->neumaticos = new inputField('Cantidad de neum&aacute;ticos','vh_neumaticos');
		$edit->neumaticos->rule='max_length[50]|numeric|required';
		$edit->neumaticos->size =15;
		$edit->neumaticos->maxlength =50;
		$edit->neumaticos->autocomplete=false;
		$edit->neumaticos->group = 'Datos del veh&iacute;culo';

		$edit->tiponeumatucos = new inputField('Tipo de neum&aacute;ticos','vh_tiponeumaticos');
		$edit->tiponeumatucos->rule='max_length[50]|required';
		$edit->tiponeumatucos->size =15;
		$edit->tiponeumatucos->maxlength =50;
		$edit->tiponeumatucos->autocomplete=false;
		$edit->tiponeumatucos->group = 'Datos del veh&iacute;culo';

		$edit->distanciaeje = new inputField('Distancia entre ejes','vh_distanciaeje');
		$edit->distanciaeje->rule='max_length[50]|numeric|required';
		$edit->distanciaeje->size =15;
		$edit->distanciaeje->maxlength =50;
		$edit->distanciaeje->autocomplete=false;
		$edit->distanciaeje->group = 'Datos del veh&iacute;culo';

		$edit->peso = new inputField('Peso Kg.','peso');
		$edit->peso->rule='max_length[10]|numeric|required';
		$edit->peso->css_class='inputnum';
		$edit->peso->size =12;
		$edit->peso->maxlength =12;
		$edit->peso->autocomplete=false;
		$edit->peso->group = 'Datos del veh&iacute;culo';

		$edit->placa = new inputField('Placa','vh_placa');
		$edit->placa->rule='max_length[50]|strtoupper|callback_chrepetido[placa]|required';
		$edit->placa->size =10;
		$edit->placa->maxlength =50;
		$edit->placa->autocomplete=false;
		$edit->placa->group = 'Datos del veh&iacute;culo';

		$edit->precioplaca = new inputField('Precio placa.','vh_precioplaca');
		$edit->precioplaca->rule='max_length[10]|numeric|required';
		$edit->precioplaca->css_class='inputnum';
		$edit->precioplaca->size =12;
		$edit->precioplaca->maxlength =12;
		$edit->precioplaca->autocomplete=false;
		$edit->precioplaca->group = 'Datos del financieros';

		$edit->base = new inputField('Base imponible','vh_base');
		$edit->base->rule= 'required|numeric';
		$edit->base->size = 12;
		$edit->base->css_class='inputnum';
		$edit->base->autocomplete= false;
		$edit->base->group = 'Datos del financieros';

		$edit->tasa =  new dropdownField('Tasa %','vh_tasa');
		$edit->tasa->rule  = 'required|numeric';
		$edit->tasa->style = 'width:100px';
		$edit->tasa->mode  = 'autohide';
		$edit->tasa->insertValue=$iva['tasa'];
		$edit->tasa->group = 'Datos del financieros';
		foreach($iva AS $nom=>$val) $edit->tasa->option($val,nformat($val).'%');

		$edit->montoiva = new inputField('IVA ','montoiva');
		$edit->montoiva->rule= 'required|numeric';
		$edit->montoiva->size = 10;
		$edit->montoiva->autocomplete= false;
		$edit->montoiva->css_class='inputnum';
		$edit->montoiva->in = 'tasa';
		$edit->montoiva->autocomplete=false;
		$edit->montoiva->group = 'Datos del financieros';

		$accion="javascript:window.location='".site_url('concesionario/inicio')."'";
		$edit->button('btn_cargar','Regresar',$accion,'BL');

		$edit->submit('btnsubmit','Realizar compra');
		$edit->build_form();

		if($edit->on_success()){
			$this->genesal=false;

			$_POST['btn_submit'] = 'Guardar';
			//$_POST['fecha']      = '08/08/2012';
			//$_POST['vence']      = '08/08/2012';
			//$_POST['serie']      = '';
			//$_POST['nfiscal']    = '';
			//$_POST['depo']       = '0001';
			//$_POST['proveed']    = '';
			//$_POST['nombre']     = '';
			//$_POST['peso']       = 0;
			//$_POST['montoiva']   = 290.61;
			//$_POST['codigo_0']   = '';
			//$_POST['descrip_0']  = '';

			$_POST['tipo_doc']   = 'FC';
			$_POST['orden']      = '';

			$_POST['cantidad_0'] = 1;
			$_POST['costo_0']    = $_POST['vh_base'] ;
			$_POST['importe_0']  = $_POST['vh_base'] ;
			$_POST['sinvpeso_0'] = $_POST['peso'] ;
			$_POST['iva_0']      = $_POST['vh_tasa'] ;

			$_POST['codigo_1']   = 'PLACA';
			$_POST['descrip_1']  = 'PLACA '.$edit->placa->newValue;
			$_POST['cantidad_1'] = 1;
			$_POST['costo_1']    = $edit->precioplaca->newValue;
			$_POST['importe_1']  = $edit->precioplaca->newValue;
			$_POST['sinvpeso_1'] = 0.000;
			$_POST['iva_1']      = 0;

			$_POST['reten']      = 0;
			$_POST['anticipo']   = 0;
			$_POST['montotot']   = $edit->precioplaca->newValue+$edit->base->newValue;
			$_POST['reteiva']    = 0;
			$_POST['inicial']    = 0;
			$_POST['mdolar']     = '';
			$_POST['credito']    = '';
			$_POST['montonet']   = $_POST['montotot']+$edit->montoiva->newValue;

			$_POST['observa1']   = 'COMPRA DE VEHICULO';
			$_POST['observa2']   = '';
			$_POST['observa3']   = '';

			$rt=$this->dataedit();
			if($rt=='Compra Guardada'){
				$this->_actualizar($this->claves['control'], 'S');

				$data=array();
				$data['id_scst']        = $this->claves['id'];
				$data['codigo_sinv']    = $edit->codigo->newValue;
				$data['modelo']         = $edit->descrip->newValue;
				$data['color']          = $edit->color->newValue;
				$data['motor']          = $edit->motor->newValue;
				$data['carroceria']     = $edit->carroceria->newValue;
				$data['uso']            = $edit->uso->newValue;
				$data['tipo']           = $edit->tipo->newValue;
				$data['clase']          = $edit->clase->newValue;
				$data['anio']           = $edit->anio->newValue;
				$data['peso']           = $edit->peso->newValue;
				$data['transmision']    = $edit->transmision->newValue;
				$data['placa']          = $edit->placa->newValue;
				$data['precioplaca']    = $edit->precioplaca->newValue;
				$data['neumaticos']     = $edit->neumaticos->newValue;
				$data['tipo_neumatico'] = $edit->tiponeumatucos->newValue;
				$data['distanciaeje']   = $edit->distanciaeje->newValue;

				$mSQL = $this->db->insert_string('sinvehiculo', $data);
				$this->db->simple_query($mSQL);

				$content = $rt.br();
				$content.= anchor('formatos/ver/COMPRA/'.$this->claves['id'],'Descargar compra').br();
				$content.= anchor($this->url,'Regresar');

				$data['content'] = $content;
				$data['script']  = script('jquery.js');
				$data['script'] .= script('jquery-ui.js');
				$data['script'] .= script('plugins/jquery.numeric.pack.js');
				$data['script'] .= script('plugins/jquery.floatnumber.js');
				$data['script'] .= script('plugins/jquery.ui.autocomplete.autoSelectOne.js');
				$data['script'] .= phpscript('nformat.js');
				$data['head']    = $this->rapyd->get_head();
				$data['head']   .= style('redmond/jquery-ui-1.8.1.custom.css');
				$data['title']   = heading($this->tits);
				$this->load->view('view_ventanas', $data);
				return;
			}
		}

		$script= '<script type="text/javascript" >
		$(function() {
			$(".inputnum").numeric(".");
			$(".inputonlynum").numeric();
			$("#vh_tasa").change(function() { calcula(); });
			$("#vh_base").bind("keyup",function() { calcula(); });
			$("#vh_montoiva").bind("keyup",function() { calculaiva(); });
			$("#proveed").autocomplete({
				source: function( req, add){
					$.ajax({
						url:  "'.site_url('ajax/buscasprv').'",
						type: "POST",
						dataType: "json",
						data: "q="+req.term,
						success:
							function(data){
								var sugiere = [];
								if(data.length==0){
									$("#proveed").val("");
									$("#nombre").val("");
									$("#nombre_val").text("");
								}else{

									$.each(data,
										function(i, val){
											sugiere.push( val );
										}
									);
								}
								add(sugiere);
							},
					})
				},
				minLength: 2,
				select: function( event, ui ) {
					$("#proveed").attr("readonly", "readonly");

					$("#nombre").val(ui.item.nombre);
					$("#nombre_val").text(ui.item.nombre);
					$("#proveed").val(ui.item.proveed);

					setTimeout(function() {  $("#proveed").removeAttr("readonly"); }, 1500);
				}
			});

			$("#codigo_0").autocomplete({
				source: function( req, add){
					$.ajax({
						url:  "'.site_url('ajax/buscascstart').'",
						type: "POST",
						dataType: "json",
						data: "q="+req.term,
						success:
							function(data){
								var sugiere = [];

								if(data.length==0){
									$("#proveed").val("");
									$("#nombre").val("");
									$("#nombre_val").text("");
								}else{
									$.each(data,
										function(i, val){
											sugiere.push( val );
										}
									);
								}
								add(sugiere);
							},
					})
				},
				minLength: 2,
				select: function( event, ui ) {
					$("#codigo_0").attr("readonly", "readonly");

					$("#codigo_0").val(ui.item.codigo);
					$("#descrip_0").val(ui.item.descrip);
					//$("#tasa").val(ui.item.iva);
					$("#peso").val(ui.item.peso);
					post_modbus_sinv();
					setTimeout(function() {  $("#codigo_0").removeAttr("readonly"); }, 1500);
				}
			});
		});
		'.$jsc.'
		</script>';

		$data['content'] = $edit->output;
		$data['script']  = script('jquery.js');
		$data['script'] .= script('jquery-ui.js');
		$data['script'] .= script('plugins/jquery.numeric.pack.js');
		$data['script'] .= script('plugins/jquery.floatnumber.js');
		$data['script'] .= script('plugins/jquery.ui.autocomplete.autoSelectOne.js');
		$data['script'] .= phpscript('nformat.js');
		$data['head']    = $this->rapyd->get_head();
		$data['head']   .= style('redmond/jquery-ui-1.8.1.custom.css');
		$data['script'] .= $script;
		$data['title']   = heading($this->tits);
		$this->load->view('view_ventanas', $data);
	}

	function chrepetido($valor,$campo){
		$this->db->where($campo,$valor);
		$cana=$this->db->count_all_results('sinvehiculo');
		if($cana>0){
			$this->validation->set_message('chrepetido', "Ya existe un veh&iacute;culo con el mismo $campo registrado.");
			return false;
		}
		return true;
	}

	function instalar(){
		if (!$this->db->table_exists('sinvehiculo')) {
			$mSQL="CREATE TABLE `sinvehiculo` (
				`id` INT(10) NOT NULL AUTO_INCREMENT,
				`id_sfac` INT(10) NULL DEFAULT NULL,
				`id_scst` INT(10) NULL DEFAULT NULL,
				`codigo_sinv` VARCHAR(15) NULL DEFAULT '0',
				`modelo` VARCHAR(50) NULL DEFAULT '0',
				`color` VARCHAR(50) NULL DEFAULT '0',
				`motor` VARCHAR(50) NULL DEFAULT '0',
				`carroceria` VARCHAR(50) NULL DEFAULT '0',
				`uso` VARCHAR(50) NULL DEFAULT '0',
				`anio` VARCHAR(50) NULL DEFAULT '0',
				`peso` DECIMAL(10,2) NULL DEFAULT '0.00',
				`transmision` VARCHAR(50) NULL DEFAULT '0.00',
				`placa` VARCHAR(10) NULL DEFAULT NULL,
				`precioplaca` DECIMAL(10,2) NULL DEFAULT NULL,
				`tasa` DECIMAL(10,2) NULL DEFAULT NULL,
				PRIMARY KEY (`id`)
			)
			COMMENT='Vehiculos a la venta'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}
	}
}
