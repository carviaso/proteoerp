 <?php
class ccont extends Controller {

	function ccont(){
		parent::Controller();
		$this->load->library('rapyd');
		//$this->datasis->modulo_id(604,1);
	}

	function index() {
		$this->rapyd->load('datagrid','datafilter');
		$this->rapyd->uri->keep_persistence();

		$mccont=array(
		'tabla'   =>'ccont',
		'columnas'=>array(
		'numero' =>'Número de Contrato',
		'titulo'=>'Titulo',
		'cod_prv'=>'Proveedor'),
		'filtro'  =>array('numero' =>'Número de Contrato','titulo'=>'Titulo'),
		'retornar'=>array('numero'=>'subccont'),
		'titulo'  =>'Buscar Contrato');
		$bccont =$this->datasis->modbus($mccont);

		$mSPRV=array(
		'tabla'   =>'sprv',
		'columnas'=>array(
		'proveed' =>'C&oacute;digo Proveedor',
		'nombre'=>'Nombre',
		'rif'=>'RIF'),
		'filtro'  =>array('proveed' =>'C&oacute;digo Proveedor','nombre'=>'Nombre','rif'=>'RIF'),
		'retornar'=>array('proveed'=>'cod_prv'),
		'titulo'  =>'Buscar Proveedor');
		$bsprv =$this->datasis->modbus($mSPRV);

		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});';

		$filter = new DataFilter("Filtro de Contratos");
		$select=array("numero","obrap","fecha_inicio","fecha_final","detalle","cod_prv","nombre","direccion","usuario","base","impuesto","tota");
		$filter->db->select($select);
		$filter->db->from('ccont AS a');
		$filter->script($script, "create");
		$filter->script($script, "modify");

		$filter->ifechad = new dateonlyField("Fecha Desde", "fechad",'d/m/Y');
		$filter->ifechah = new dateonlyField("Fecha Hasta", "fechah",'d/m/Y');
		$filter->ifechad->clause  =$filter->ifechah->clause="where";
		$filter->ifechad->db_name =$filter->ifechah->db_name="fecha_inicio";
		//$filter->ifechad->insertValue = date("Y-m-d");
		//$filter->ifechah->insertValue = date("Y-m-d");
		$filter->ifechah->size=$filter->ifechad->size=10;
		$filter->ifechad->operator=">=";
		$filter->ifechah->operator="<=";
		$filter->ifechah->group=$filter->ifechad->group="Fecha de Inicio";

		$filter->ffechad = new dateonlyField('Fecha Desde', "fechad",'d/m/Y');
		$filter->ffechah = new dateonlyField('Fecha Hasta', "fechah",'d/m/Y');
		$filter->ffechad->clause  =$filter->ffechah->clause='where';
		$filter->ffechad->db_name =$filter->ffechah->db_name="fecha_final";
		//$filter->ffechad->insertValue = date("Y-m-d");
		//$filter->ffechah->insertValue = date("Y-m-d");
		$filter->ffechah->size=$filter->ffechad->size=10;
		$filter->ffechad->operator='>=';
		$filter->ffechah->operator='<=';
		$filter->ffechah->group=$filter->ffechad->group='Fecha de Terminaci&oacute;n';

		$filter->numero = new inputField('Contrato Nº','numero');
		$filter->numero->size=10;
		$filter->numero->css_class='inputnum';

		$filter->cod_prv = new inputField('Proveedor', 'cod_prv');
		$filter->cod_prv->size=12;
		$filter->cod_prv->maxlength=5;
		$filter->cod_prv->append($bsprv);

		$filter->nombre = new inputField('Nombre o Raz&oacute;n Social','nombre');
		$filter->nombre->size=30;

		$filter->obrap = new inputField('Obra Principal', 'obrap');
		$filter->obrap->size=12;
		$filter->obrap->maxlength=20;
		$filter->obrap->append($bccont);

		$filter->buttons('reset','search');
		$filter->build();

		$uri  = anchor('finanzas/ccont/dataedit/show/<#numero#>','<#numero#>');
		$uri1 = anchor('finanzas/ccont/observa/create/<#numero#>','Agregar');
		$uri2 = anchor('finanzas/ccont/ver/show/<#numero#>','Agregar');
		$uri3 = anchor('finanzas/ccont/ver/show/<#numero#>','Mostrar');

		$grid = new DataGrid();
		$grid->order_by("numero","desc");
		$grid->per_page = 15;

		$grid->column_orderby('Contrato Nº'   ,$uri   ,"numero","align='left'NOWRAP");
		$grid->column_orderby('Obra Principal','obrap',"obrap","align='left'NOWRAP");
		$grid->column_orderby("Fecha Inicial" ,"<dbdate_to_human><#fecha_inicio#></dbdate_to_human>","fecha_inicio","align='center'NOWRAP");
		$grid->column_orderby("Fecha Final"   ,"<dbdate_to_human><#fecha_final#></dbdate_to_human>","fecha_final","align='center'NOWRAP");
		//$grid->column_orderby("Descripción"  ,"descrip","descrip","align='left'NOWRAP");
		$grid->column_orderby("Nombre o Razón Social" ,"nombre","titulo","align='left'NOWRAP");
		$grid->column('Sub.Total'    ,"<number_format><#base#>|2</number_format>","align=right");
		$grid->column('IVA'          ,"<number_format><#impuesto#>|2</number_format>","align=right");
		$grid->column('Total'        ,"<number_format><#tota#>|2</number_format>","align=right");
		$grid->column('Certificación',$uri2,"align='center'");
		$grid->column("Todas las Certificaciones",$uri3,"align='center'");
		$grid->column('Observaciones',$uri1,"align='center'");
		//$grid->column('Ver',$uri2,"align='center'");

		$grid->add('finanzas/ccont/dataedit/create');
		$grid->build();
		//echo $grid->db->last_query();

		$data['content'] =$filter->output.$grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   ='<h1>Contratos</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');

		$modbus=array(
		'tabla'   =>'obpa',
		'columnas'=>array(
		'codigo'  =>'C&oacute;digo',
		'descrip' =>'descrip'),
		'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'descrip'),
		//'retornar'=>array('codigo'=>'codigo<#i#>','precio1'=>'precio1<#i#>','precio2'=>'precio2<#i#>','precio3'=>'precio3<#i#>','precio4'=>'precio4<#i#>','iva'=>'iva<#i#>','pond'=>'costo<#i#>'),
		'retornar'=>array('codigo'=>'partida_<#i#>','descrip'=>'descrip_<#i#>'),
		'p_uri'   =>array(4=>'<#i#>'),
		'titulo'  =>'Buscar Partidas');

		$mSPRV=array(
		'tabla'   =>'sprv',
		'columnas'=>array(
		'proveed' =>'C&oacute;digo Proveedor',
		'nombre'  =>'Nombre',
		'rif'     =>'RIF',
		'telefono'=>'Telefono',
		'email'   =>'Email'),
		'filtro'  =>array('proveed' =>'C&oacute;digo Proveedor','nombre'=>'Nombre','rif'=>'RIF'),
		'retornar'=>array('proveed'=>'cod_prv','nombre'=>'nombre','rif'=>'rif','telefono'=>'telefono'),
		'titulo'  =>'Buscar Proveedor');
		$bsprv =$this->datasis->modbus($mSPRV);

		$mccont=array(
		'tabla'   =>'ccont',
		'columnas'=>array(
		'numero'  =>'Número de Contrato',
		'cod_prv' =>'Proveedor',
		'nombre'  =>'Nombre'),
		'filtro'  =>array('numero' =>'Número de Contrato','titulo'=>'Titulo'),
		'retornar'=>array('numero'=>'obrap'),
		'titulo'  =>'Buscar Contrato');
		$bccont =$this->datasis->modbus($mccont);


		$script="
		function post_add_itccont(id){
			$('#cantidad_'+id).numeric(".");
			return true;
		}";

		$do = new DataObject('ccont');
		$do->rel_one_to_many('itccont', 'itccont', 'numero');

		$edit = new DataDetails('Contratos', $do);
		$edit->back_url = site_url('finanzas/ccont');
		$edit->set_rel_title('itccont','Partida <#o#>');

		$edit->script($script,'create');
		$edit->script($script,'modify');

		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->obrap = new inputField('Obra Principal', 'obrap');
		$edit->obrap->size=12;
		$edit->obrap->maxlength=20;
		$edit->obrap->append($bccont);
		$edit->obrap->readonly=true;
		//$edit->obrap->rule = 'required';

		$edit->numero = new inputField("Contrato Nº", "numero");
		$edit->numero->when = array("show");
		$edit->numero->size = 10;
		$edit->numero->maxlength=20;
		$edit->numero->readonly=true;

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->size = 12;
		$edit->fecha->rule='chfecha';
		$edit->fecha->rule = 'required';

		$edit->tipo = new dropdownField('Tipo', 'tipo');
		$edit->tipo->option('','Seleccione');
		$edit->tipo->options("SELECT codigo,descrip  FROM tipoc ORDER BY descrip");
		$edit->tipo->rule = 'required';

		$edit->cod_prv = new inputField('Proveedor', 'cod_prv');
		$edit->cod_prv->size=12;
		$edit->cod_prv->maxlength=20;
		$edit->cod_prv->append($bsprv);
		$edit->cod_prv->readonly=true;
		//$edit->cod_prv->rule = 'required';

		$lriffis='<a href="javascript:consulrif();" title="Consultar RIF en el SENIAT" onclick=""> Consultar RIF en el SENIAT</a>';
		$edit->rif =  new inputField('R.I.F.', 'rif');
		//$edit->rif->mode='autohide';
		$edit->rif->rule = 'strtoupper|callback_chci';
		$edit->rif->append($lriffis);
		$edit->rif->maxlength=20;
		$edit->rif->size =18;

		$edit->telefono = new inputField('Tel&eacute;fono', 'telefono');
		$edit->telefono->size = 30;
		$edit->telefono->maxlength =100;

		$edit->email = new inputField('E-mail', 'email');
		$edit->email->rule = 'trim|valid_email';
		$edit->email->size =30;
		$edit->email->maxlength =50;

		$edit->nombre = new inputField('Nombre o Raz&oacute;n Social',"nombre");
		$edit->nombre->size=50;
		$edit->nombre->maxlength =100;
		$edit->nombre->rule = 'required|strtoupper';

		$edit->direccion = new textareaField('Direcci&oacute;n o Domicilio','direccion');
		$edit->direccion->rows = 3;
		$edit->direccion->cols = 60;
		$edit->direccion->rule = 'required|strtoupper';

		$edit->detalles = new textareaField('Detalle del Objeto Contractual','detalle');
		$edit->detalles->size=50;
		$edit->detalles->rows = 3;
		$edit->detalles->cols = 60;
		$edit->detalles->rule = 'required|strtoupper';

		$edit->base  = new inputField('Sub Total sin IVA', 'base');
		$edit->base->size = 20;
		$edit->base->css_class='inputnum';
		$edit->base->rule='numeric';
		$edit->base->group="Totales";
		//$edit->base->rule = 'required';
		$edit->base->readonly=true;

		$edit->impuesto  = new inputField("IVA", "impuesto");
		$edit->impuesto->size = 20;
		$edit->impuesto->css_class='inputnum';
		$edit->impuesto->rule='numeric';
		$edit->impuesto->group="Totales";
		//$edit->impuesto->rule = 'required';
		$edit->impuesto->readonly=true;

		$edit->tota  = new inputField("Total Bs", "tota");
		$edit->tota->size = 20;
		$edit->tota->css_class='inputnum';
		$edit->tota->rule='numeric';
		$edit->tota->group="Totales";
		//$edit->tota->rule = 'required';
		$edit->tota->readonly=true;

		$edit->retencion  = new inputField("Retención por Garantia", "retencion");
		$edit->retencion->size =10;
		$edit->retencion->css_class='inputnum';
		$edit->retencion->rule='numeric';
		$edit->retencion->append(' %');

		$edit->fecha_inicio = new DateonlyField("Fecha de Inicio", "fecha_inicio","d/m/Y");
		$edit->fecha_inicio->insertValue = date("Y-m-d");
		$edit->fecha_inicio->size = 12;
		$edit->fecha_inicio->rule='chfecha';
		$edit->fecha_inicio->rule = 'required';

		$edit->fecha_final = new DateonlyField("Fecha de Terminación", "fecha_final","d/m/Y");
		$edit->fecha_final->insertValue = date("Y-m-d");
		$edit->fecha_final->size = 12;
		$edit->fecha_final->rule='required|chfecha';

		$numero=$edit->_dataobject->get("numero");

		$edit->itccont = new containerField('numero',$this->_detalle($numero));
		$edit->itccont->when = array("show","modify");
		$edit->itccont->group = "Totales";

		//Campos para el detalle

		$edit->partida = new inputField("Partida", "partida_<#i#>");
		$edit->partida->size=18;
		$edit->partida->db_name='partida';
		$edit->partida->rel_id='itccont';
		$edit->partida->append($this->datasis->p_modbus($modbus,'<#i#>'));
		$edit->partida->readonly=TRUE;

		$edit->descrip = new inputField("Descripci&oacute;n", "descrip_<#i#>");
		$edit->descrip->size=30;
		$edit->descrip->db_name='descrip';
		$edit->descrip->rel_id='itccont';
		$edit->descrip->readonly=TRUE;

		$edit->unidad  = new inputField("Unidad de Medida", "unidad_<#i#>");
		$edit->unidad->size=12;
		$edit->unidad->rel_id='itccont';
		$edit->unidad->db_name='unidad';

		$edit->cantidad = new inputField("Cantidad", "cantidad_<#i#>");
		$edit->cantidad->size=10;
		$edit->cantidad->db_name='cantidad';
		$edit->cantidad->maxlength=7;
		$edit->cantidad->rel_id='itccont';
		$edit->cantidad->css_class='inputnum';
		$edit->cantidad->onchange ='cal_monto(<#i#>);';

		$edit->precio = new inputField("Precio Unitario", "precio_<#i#>");
		$edit->precio->css_class='inputnum';
		$edit->precio->size=15;
		$edit->precio->rel_id='itccont';
		$edit->precio->db_name='precio';
		$edit->precio->onchange ='cal_monto(<#i#>);';

		$edit->monto = new inputField("Importe Total Bs", "monto_<#i#>");
		$edit->monto->db_name='monto';
		$edit->monto->size=15;
		$edit->monto->rel_id='itccont';
		$edit->monto->css_class='inputnum';
		$edit->monto->readonly=true;

		$edit->buttons("modify", "save", "undo", "delete", "back","add_rel");
		$edit->build();

		$conten['form']  =&  $edit;
		$data['content'] = $this->load->view('view_ccont', $conten,true);
		$data['head']    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js");
		$data['title']   = '<h1>Contrato</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function _detalle($numero){
		$salida='No hay Observaciones';
		if(!empty($numero)){
			$this->rapyd->load('datagrid');

			$grid = new DataGrid('Lista de Observaciones');
			$select=array('id','numero','contenido','fecha','usuario');
			$grid->db->select($select);
			$grid->db->from('itccontb');
			$grid->db->where('numero',$numero);

			$grid->order_by('id','asc');
			$grid->per_page = 10;

			$grid->column('Fecha'      ,'<dbdate_to_human><#fecha#></dbdate_to_human>',"align='center'");
			$grid->column('Contenido'  ,'contenido',"align='left'");

			$grid->build();
			$salida=$grid->output;
			//Echo $grid->db->last_query();
		}
		return $salida;
	}

	function observa($estado='',$numero=''){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit(' ','itccont');
		$edit->back_url = site_url('finanzas/ccont/');

		$edit->post_process('insert','_post_insert2');
		$edit->post_process('update','_post_update2');
		$edit->post_process('delete','_post_delete2');

		$edit->id = new inputField2('N&uacute;mero de Observación', 'id');
		$edit->id->when = array('show');

		$edit->numero = new inputField2('N&uacute;mero de Contrato', 'numero');
		$edit->numero->when = array("show","create");
		$edit->numero->readonly=TRUE;
		$edit->numero->insertValue ="$numero";
		$edit->numero->size = 12;

		$edit->fecha = new DateonlyField("Fecha ", "fecha","d/m/Y");
		$edit->fecha->insertValue = date("Y-m-d");
		$edit->fecha->size = 12;
		$edit->fecha->rule='required|chfecha';

		$edit->contenido = new textareaField("Contenido","contenido");
		$edit->contenido->rows = 3;
		$edit->contenido->cols = 60;
		$edit->contenido->rule = 'required|strtoupper';

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		//$smenu['link']=barra_menu('230');
		//$data['smenu']   = $this->load->view('view_sub_menu', $smenu,true);
		$data['content'] = $edit->output;
		$data['title']   = "<h1>Observaciones</h1>";
		$data['head']    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function _guarda_detalle($do) {
		$cant=$this->input->post('cant_0');
		$i=$o=0;
		while($o<$cant){
			if(isset($_POST["partida$i"])){
				if($this->input->post("partida$i")){

					$sql = "INSERT INTO itccont (numero,partida,descrip,unidad,cantidad,precio,monto) VALUES(?,?,?,?,?,?,?)";
					$llena=array(
							0 =>$do->get('numero'),
							1 =>$this->input->post("partida$i"),
							2 =>$this->input->post("descrip$i"),
							3 =>$this->input->post("unidad$i"),
							4 =>$this->input->post("cantidad$i"),
							5 =>$this->input->post("precio$i"),
							6 =>$this->input->post("monto$i"));

					$this->db->query($sql,$llena);
				}
				$o++;
			}
			$i++;
		}
	}

	function _actualiza_detalle($do){
		$this->_borra_detalle($do);
		$this->_guarda_detalle($do);
	}

	function _borra_detalle($do){
		$numero=$do->get('numero');
		$sql = "DELETE FROM itccont WHERE numero='$numero'";
		$this->db->query($sql);
	}

	function _post_insert($do){
		$numero=$do->get('numero');
		$nombre=$do->get('nombre');
		$cod_prv=$do->get('cod_prv');
		$usuario=$this->session->userdata['usuario'];
		$Sql=$this->db->query("UPDATE ccont SET usuario=$usuario WHERE numero='$numero'");
		logusu('ccont',"CONTRATO $numero $nombre $cod_prv CREADO");
	}

	function _post_update($do){
		$numero=$do->get('numero');
		$nombre=$do->get('nombre');
		$cod_prv=$do->get('cod_prv');
		logusu('ccont',"CONTRATO $numero $nombre $cod_prv MODIFICADO");
	}

	function _post_delete($do){
		$numero=$do->get('numero');
		$nombre=$do->get('nombre');
		$cod_prv=$do->get('cod_prv');
		logusu('ccont',"CONTRATO $numero $nombre  $cod_prv ELIMINADO");
	}

	function chexiste(){
		$cod_prv=$this->input->post('cod_prv');
		$chek=$this->datasis->dameval("SELECT COUNT(*) FROM sprv WHERE proveed='$cod_prv'");
		if ($chek > 0){
			return true;
		}else{
			$this->validation->set_message('chexiste',"El Proveedor $cod_prv no se encuentra registrado");
			return false;
		}
	}
}
