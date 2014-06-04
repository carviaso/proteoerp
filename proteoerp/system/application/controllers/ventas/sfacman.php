<?php
require_once(BASEPATH.'application/controllers/validaciones.php');
//require_once(BASEPATH.'application/controllers/ventas/sfac');
class sfacman extends validaciones {

	var $titp='Facturaci&oacute;n por Mandatarios';
	var $tits='Facturaci&oacute;n';
	var $url ='ventas/sfacman/';

	function sfacman(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('13E',1);
		$this->instalar();
	}

	function index(){
		redirect($this->url.'filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
		);

		$scli=array(
		'tabla'   =>'scli',
		'columnas'=>array(
		'cliente' =>'C&oacute;digo Cliente',
		'nombre'=>'Nombre',
		'contacto'=>'Contacto'),
		'filtro'  =>array('cliente'=>'C&oacute;digo Cliente','nombre'=>'Nombre'),
		'retornar'=>array('cliente'=>'cod_cli'),
		'titulo'  =>'Buscar Cliente');
		$boton=$this->datasis->modbus($scli);

		$filter = new DataFilter('Filtro de Facturas');
		$filter->db->select(array('fecha','numero','cod_cli','nombre','totals','totalg','iva','tipo_doc','exento', 'IF(referen="C","Credito",IF(referen="E","Contado","Pendiente")) referen','IF(tipo_doc="X","N","S") nulo','almacen','vd','usuario', 'hora', 'estampa','nfiscal','cajero', 'transac','maqfiscal', 'factura' ,'id'));
		$filter->db->where('tipo_doc','T');
		$filter->db->from('sfac');

		$filter->fechad = new dateonlyField('Desde', 'fechad','d/m/Y');
		$filter->fechad->clause  = 'where';
		$filter->fechad->db_name = 'fecha';
		//$filter->fechad->insertValue = date('Y-m-d');
		$filter->fechad->size=10;
		$filter->fechad->operator='>=';
		$filter->fechad->group = '1';

		$filter->fechah = new dateonlyField('Hasta', 'fechah','d/m/Y');
		$filter->fechah->clause = 'where';
		$filter->fechah->db_name='fecha';
		//$filter->fechah->insertValue = date('Y-m-d');
		$filter->fechah->size=10;
		$filter->fechah->operator='<=';
		$filter->fechah->group = '1';

		$filter->referen = new  dropdownField ('Condici&oacute;n', 'referen');
		$filter->referen->option('' ,'Todos');
		$filter->referen->option('E','Contado');
		$filter->referen->option('C','Cr&eacute;dito');
		$filter->referen->style='width:150px;';
		$filter->referen->operator='=';
		$filter->referen->clause  = 'where';
		$filter->referen->group = '1';

		$filter->numero = new inputField('N&uacute;mero', 'numero');
		$filter->numero->size = 20;
		$filter->numero->group = '2';

		$filter->cliente = new inputField('Cliente', 'cod_cli');
		$filter->cliente->size = 20;
		$filter->cliente->append($boton);
		$filter->cliente->group = '2';

		$filter->buttons('reset','search');
		$filter->build('dataformfiltro');

		$uri = anchor($this->url.'dataedit/show/<#id#>','<#tipo_doc#><#numero#>');
		$uri2  = anchor($this->url.'dataedit/show/<#id#>',img(array('src'=>'images/editar.png','border'=>'0','alt'=>'Editar')));
		$uri2 .= "&nbsp;";
		$uri2 .= anchor('formatos/ver2/FACTURA/<#tipo_doc#>/<#numero#>',img(array('src'=>'images/pdf_logo.gif','border'=>'0','alt'=>'PDF')));
		$uri2 .= "&nbsp;";
		$uri2 .= anchor('formatos/verhtml/FACTURA/<#tipo_doc#>/<#numero#>',img(array('src'=>'images/html_icon.gif','border'=>'0','alt'=>'HTML')));
		$uri2 .= "&nbsp;";
		$uri2 .= img(array('src'=>'images/<#nulo#>.gif','border'=>'0','alt'=>'Estado','title'=>'Estado'));

		$uri_3  = "<a href='javascript:void(0);' onclick='javascript:nfiscal(\"<#id#>\")'>";
		$propiedad = array('src' => 'images/engrana.png', 'alt' => 'Modifica Nro de Control', 'title' => 'Modifica Nro. de Control','border'=>'0','height'=>'12');
		$uri_3 .= img($propiedad);
		$uri_3 .= "</a>";

		$mtool  = "<table background='#554455'><tr>";
		$mtool .= "<td>&nbsp;</td>";

		$mtool .= "<td>&nbsp;<a href='".base_url().$this->url."dataedit/create'>";
		$mtool .= img(array('src' => 'images/agregar.jpg', 'alt' => 'Agregar Registro', 'title' => 'Agregar Registro','border'=>'0','height'=>'32'));
		$mtool .= "</a>&nbsp;</td>";

		$mtool .= "<td>&nbsp;<a href='javascript:void(0);' ";
		$mtool .= 'onclick="window.open(\''.base_url()."reportes/index/sfac', '_blank', 'width=800, height=600, scrollbars=Yes, status=Yes, resizable=Yes, screenx='+((screen.availWidth/2)-400)+',screeny='+((screen.availHeight/2)-300)+'');".'" heigth="600" width="900" '.'>';
		$mtool .= img(array('src' => 'images/reportes.gif', 'alt' => 'Reportes', 'title' => 'Reportes','border'=>'0','height'=>'32'));
		$mtool .= "</a>&nbsp;</td>";

		$mtool .= "</tr></table>";

		$grid = new DataGrid($mtool);
		$grid->order_by('fecha','desc');
		$grid->per_page = 50;

		$grid->column('Acciones',$uri2);
		$grid->column_orderby('N&uacute;mero',$uri,'numero');
		$grid->column_orderby('Fecha',    '<dbdate_to_human><#fecha#></dbdate_to_human>','fecha','align=\'center\'');
		$grid->column_orderby('Cliente',  'cod_cli',  'cod_cli');
		$grid->column_orderby('Nombre',   'nombre',   'nombre');
		$grid->column_orderby('Almacen',  'almacen',  'almacen');
		$grid->column_orderby('Sub.Total','<nformat><#totals#></nformat>','totals','align=\'right\'');
		$grid->column_orderby('IVA',      '<nformat><#iva#></nformat>'   ,'iva',   'align=\'right\'');
		$grid->column_orderby('Total',    '<nformat><#totalg#></nformat>','totalg','align=\'right\'');
		$grid->column_orderby('Exento',   '<nformat><#exento#></nformat>','totalg','align=\'right\'');
		$grid->column_orderby('Tipo',     'referen',  'referen','align=\'left\'');
		$grid->column_orderby('N.Fiscal',  $uri_3.'<#nfiscal#>', 'nfiscal' );
		$grid->column_orderby('M.Fiscal', 'maqfiscal','maqfiscal','align=\'left\'');
		$grid->column_orderby('Vende',    'vd',       'vd');
		$grid->column_orderby('Cajero',   'cajero',   'cajero');
		$grid->column_orderby('Usuario',  'usuario',  'nfiscal','align=\'left\'');
		$grid->column_orderby('Hora',     'hora',     'hora',   'align=\'center\'');
		$grid->column_orderby('Transac',  'transac',  'transac','align=\'left\'');
		$grid->column_orderby('Afecta',   'factura',  'factura','align=\'left\'');
		$grid->column_orderby('I.D.',     'id',       'id',     'align=\'right\'');

		$grid->build('datagridST');
		//echo $grid->db->last_query();

		// Para usar SuperTable
		$extras = '
		<script type="text/javascript">
		//<![CDATA[
		(function() {
			var mySt = new superTable("demoTable", {
			cssSkin : "sSky",
			fixedCols : 1,
				headerRows : 1,
				onStart : function () {
				this.start = new Date();
				},
				onFinish : function () {
				document.getElementById("testDiv").innerHTML += "Finished...<br>" + ((new Date()) - this.start) + "ms.<br>";
				}
			});
		})();
		//]]>
		</script>';

		$style ='<style type="text/css">
		.fakeContainer { /* The parent container */
			margin: 5px;
			padding: 0px;
			border: none;
			width: 640px; /* Required to set */
			height: 320px; /* Required to set */
			overflow: hidden; /* Required to set */
		}
		</style>';

		$script ='
		<script type="text/javascript">
		function nfiscal(mid){
			jPrompt("Numero de Serie","" ,"Cambio de Nro.Fiscal", function(mserie){
				if( mserie==null){
					jAlert("Cancelado","Informacion");
				} else {
					$.ajax({ url: "'.site_url().'ventas/sfac/nfiscal/"+mid+"/"+mserie,
						success: function(msg){
							jAlert("Cambio Finalizado "+msg,"Informacion");
							location.reload();
							}
					});
				}
			})
		}
		</script>';


		$sigma = "";

		//$data['content']  = $mtool;
		$data['content'] = $grid->output;

		$data['filtro']  = $filter->output;

		$data['script']  = script('jquery.js');
		$data['script'] .= script('jquery.alerts.js');
		$data['script'] .= script('superTables.js');
		$data['script'] .= $script;

		$data['style']   = $style;
		$data['style']  .= style('superTables.css');
		$data['style']  .= style('jquery.alerts.css');

		$data['extras']  = $extras;

		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading($this->titp);
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->load('dataobject','datadetails');

		$do = new DataObject('sfac');
		$do->rel_one_to_many('sitems', 'sitems', array('id'=>'id_sfac'));
		$do->pointer('scli'  ,'scli.cliente=sfac.cod_cli','scli.tipo AS sclitipo','left');
		$do->pointer('scli AS manda' ,'manda.cliente=sfac.mandatario','manda.nombre AS mandanombre, manda.rifci AS mandarif, manda.dire11 AS mandadirec','left');
		$do->rel_pointer('sitems','sinv','sitems.codigoa=sinv.codigo','sinv.descrip AS sinvdescrip, sinv.base1 AS sinvprecio1, sinv.base2 AS sinvprecio2, sinv.base3 AS sinvprecio3, sinv.base4 AS sinvprecio4, sinv.iva AS sinviva, sinv.peso AS sinvpeso,sinv.tipo AS sinvtipo');

		$edit = new DataDetails('Facturas', $do);
		$edit->back_url = site_url($this->url.'filteredgrid');
		$edit->set_rel_title('sitems','Producto <#o#>');

		$edit->pre_process( 'insert','_pre_insert' );
		$edit->pre_process( 'update','_pre_update' );
		$edit->post_process('insert','_post_insert');
		$edit->post_process('update','_post_update');
		$edit->post_process('delete','_post_delete');

		$edit->sclitipo = new hiddenField('', 'sclitipo');
		$edit->sclitipo->db_name     = 'sclitipo';
		$edit->sclitipo->pointer     = true;
		$edit->sclitipo->insertValue = 1;

		$edit->mandatario = new inputField('C&oacute;digo','mandatario');
		$edit->mandatario->size = 6;
		$edit->mandatario->maxlength=5;
		$edit->mandatario->rule='required|existescli';

		$edit->mandanombre = new inputField('Nombre', 'mandanombre');
		$edit->mandanombre->db_name     = 'mandanombre';
		$edit->mandanombre->pointer     = true;
		$edit->mandanombre->type        = 'inputhidden';
		$edit->mandanombre->maxlength   = 40;
		$edit->mandanombre->size        = 25;
		$edit->mandanombre->readonly =true;

		$edit->mandarif = new inputField('RIF', 'mandarif');
		$edit->mandarif->db_name     = 'mandarif';
		$edit->mandarif->pointer     = true;
		$edit->mandarif->type        = 'inputhidden';
		$edit->mandarif->autocomplete= false;
		$edit->mandarif->size        = 15;
		$edit->mandarif->readonly    = true;

		$edit->mandadirec= new inputField('Direcci&oacute;n', 'mandadirec');
		$edit->mandadirec->type        = 'inputhidden';
		$edit->mandadirec->db_name     = 'mandadirec';
		$edit->mandadirec->pointer     = true;
		$edit->mandadirec->size        = 40;
		$edit->mandadirec->readonly    = true;

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->rule = 'required';
		$edit->fecha->mode = 'autohide';
		$edit->fecha->size = 10;

		$edit->vence = new DateonlyField('Vence', 'vence','d/m/Y');
		$edit->vence->insertValue = date('Y-m-d');
		$edit->vence->rule = 'required';
		$edit->vence->mode = 'autohide';
		$edit->vence->size = 10;

		$edit->vd = new  dropdownField ('Vendedor', 'vd');
		$edit->vd->options('SELECT vendedor, CONCAT(vendedor,\' \',nombre) nombre FROM vend ORDER BY vendedor');
		$edit->vd->style='width:200px;';
		$edit->vd->size = 5;

		$edit->numero = new inputField('Referencia', 'numero');
		$edit->numero->size = 10;
		$edit->numero->maxlength=8;
		//$edit->numero->rule='required';
		$edit->numero->when=array('show');

		$edit->nfiscal = new inputField('No.Fiscal', 'nfiscal');
		$edit->nfiscal->size = 10;
		$edit->nfiscal->maxlength=20;
		$edit->nfiscal->autocomplete=false;
		$edit->nfiscal->rule='required|callback_chnumero';

		$edit->peso = new inputField('Peso', 'peso');
		$edit->peso->css_class = 'inputnum';
		$edit->peso->readonly  = true;
		$edit->peso->size      = 10;

		$edit->cliente = new inputField('Cliente','cod_cli');
		$edit->cliente->size = 6;
		$edit->cliente->maxlength=5;
		$edit->cliente->autocomplete=false;
		$edit->cliente->rule='required|existescli';

		$edit->nombre = new hiddenField('Nombre', 'nombre');
		$edit->nombre->size = 25;
		$edit->nombre->maxlength=40;
		$edit->nombre->readonly =true;
		$edit->nombre->autocomplete=false;
		$edit->nombre->rule= 'required';

		$edit->rifci   = new hiddenField('RIF/CI','rifci');
		$edit->rifci->autocomplete=false;
		$edit->rifci->readonly =true;
		$edit->rifci->size = 15;

		$edit->direc = new hiddenField('Direcci&oacute;n','direc');
		$edit->direc->readonly =true;
		$edit->direc->size = 40;

		//***********************************
		//  Campos para el detalle 1 sitems
		//***********************************
		$edit->codigoa = new inputField('C&oacute;digo <#o#>', 'codigoa_<#i#>');
		$edit->codigoa->size     = 12;
		$edit->codigoa->db_name  = 'codigoa';
		$edit->codigoa->rel_id   = 'sitems';
		$edit->codigoa->rule     = 'required|existesinv';

		$edit->desca = new inputField('Descripci&oacute;n <#o#>', 'desca_<#i#>');
		$edit->desca->size=36;
		$edit->desca->db_name='desca';
		$edit->desca->maxlength=50;
		$edit->desca->readonly  = true;
		$edit->desca->rel_id='sitems';

		$edit->cana = new inputField('Cantidad <#o#>', 'cana_<#i#>');
		$edit->cana->db_name  = 'cana';
		$edit->cana->css_class= 'inputnum';
		$edit->cana->rel_id   = 'sitems';
		$edit->cana->maxlength= 10;
		$edit->cana->size     = 6;
		$edit->cana->rule     = 'required|positive';
		$edit->cana->autocomplete=false;
		$edit->cana->onkeyup  ='importe(<#i#>)';

		$edit->preca = new inputField('Precio <#o#>', 'preca_<#i#>');
		$edit->preca->db_name   = 'preca';
		$edit->preca->css_class = 'inputnum';
		$edit->preca->rel_id    = 'sitems';
		$edit->preca->size      = 10;
		$edit->preca->rule      = 'required|positive';
		$edit->preca->readonly  = true;

		$edit->detalle = new hiddenField('', 'detalle_<#i#>');
		$edit->detalle->db_name  = 'detalle';
		$edit->detalle->rel_id   = 'sitems';

		$edit->tota = new inputField('Importe <#o#>', 'tota_<#i#>');
		$edit->tota->db_name='tota';
		$edit->tota->size=10;
		$edit->tota->css_class='inputnum';
		$edit->tota->rel_id   ='sitems';

		for($i=1;$i<4;$i++){
			$obj='precio'.$i;
			$edit->$obj = new hiddenField('Precio <#o#>', $obj.'_<#i#>');
			$edit->$obj->db_name   = 'sinv'.$obj;
			$edit->$obj->rel_id    = 'sitems';
			$edit->$obj->pointer   = true;
		}

		$edit->precio4 = new hiddenField('', 'precio4_<#i#>');
		$edit->precio4->db_name   = 'precio4';
		$edit->precio4->rel_id    = 'sitems';

		$edit->itiva = new hiddenField('', 'itiva_<#i#>');
		$edit->itiva->db_name  = 'iva';
		$edit->itiva->rel_id   = 'sitems';

		$edit->sinvpeso = new hiddenField('', 'sinvpeso_<#i#>');
		$edit->sinvpeso->db_name   = 'sinvpeso';
		$edit->sinvpeso->rel_id    = 'sitems';
		$edit->sinvpeso->pointer   = true;

		$edit->sinvtipo = new hiddenField('', 'sinvtipo_<#i#>');
		$edit->sinvtipo->db_name   = 'sinvtipo';
		$edit->sinvtipo->rel_id    = 'sitems';
		$edit->sinvtipo->pointer   = true;

		$edit->ivat = new hiddenField('I.V.A', 'iva');
		$edit->ivat->css_class ='inputnum';
		$edit->ivat->readonly  =true;
		$edit->ivat->size      = 10;

		$edit->totals = new hiddenField('Sub-Total', 'totals');
		$edit->totals->css_class ='inputnum';
		$edit->totals->readonly  =true;
		$edit->totals->size      = 10;

		$edit->totalg = new hiddenField('Total', 'totalg');
		$edit->totalg->css_class ='inputnum';
		$edit->totalg->readonly  =true;
		$edit->totalg->size      = 10;

		$edit->observa   = new inputField('Observacion', 'observa');
		$edit->observ1   = new inputField('Observacion', 'observ1');
		$edit->zona      = new inputField('Zona', 'zona');
		$edit->ciudad    = new inputField('Ciudad', 'ciudad');
		$edit->exento    = new inputField('Exento', 'exento');
		$edit->maqfiscal = new inputField('Mq.Fiscal', 'maqfiscal');
		$edit->cajero    = new inputField('Cajero', 'cajero');
		$edit->referen   = new inputField('Referencia', 'referen');

		$edit->reiva     = new inputField('Retencion de IVA', 'reiva');
		$edit->creiva    = new inputField('Comprobante', 'creiva');
		$edit->freiva    = new inputField('Fecha', 'freiva');
		$edit->ereiva    = new inputField('Emision', 'ereiva');

		$edit->usuario  = new autoUpdateField('usuario',$this->session->userdata('usuario'),$this->session->userdata('usuario'));
		$edit->estampa  = new autoUpdateField('estampa' ,date('Ymd'), date('Ymd'));
		$edit->hora     = new autoUpdateField('hora',date('H:i:s'), date('H:i:s'));
		$edit->tipo_doc = new autoUpdateField('tipo_doc','T', 'T');

		$edit->buttons('save', 'back','add_rel','add');
		$edit->build();

		$conten['form']  =&  $edit;

		$data['style']   = style('redmond/jquery-ui.css');
		$data['style']  .= style('gt_grid.css');
		$data['style']  .= style('impromptu.css');

		$data['script']  = script('jquery.js');
		$data['script'] .= script('jquery-ui.js');
		$data['script'] .= script('plugins/jquery.ui.autocomplete.autoSelectOne.js');
		$data['script'] .= script('jquery-impromptu.js');
		$data['script'] .= script('plugins/jquery.blockUI.js');
		$data['script'] .= script('plugins/jquery.numeric.pack.js');
		$data['script'] .= phpscript('nformat.js');
		$data['script'] .= script('plugins/jquery.floatnumber.js');
		$data['script'] .= script('gt_msg_en.js');
		$data['script'] .= script('gt_grid_all.js');
		$data['content'] = $this->load->view('view_sfacman', $conten,true);
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading($this->titp);
		$this->load->view('view_ventanas', $data);
	}

	function chnumero($numero){
		$mandatario=$this->input->post('mandatario');
		$this->db->from('sfac');
		$this->db->where('nfiscal'   ,$numero);
		$this->db->where('mandatario',$mandatario);
		$this->db->where('tipo_doc','T');
		$cana=$this->db->count_all_results();
		if($cana>0){
			$this->validation->set_message('chnumero', 'Ya existe una factura con el mismo n&uacute;mero para el mismo mandatario registrada en el sistema');
			return false;
		}
		return true;
	}

	function _pre_insert($do){
		$numero  = $this->datasis->fprox_numero('nsfacman');
		$transac = $this->datasis->fprox_numero('ntransa');
		$do->set('numero' ,$numero);
		$do->set('transac',$transac);
		$con=$this->db->query('SELECT tasa,redutasa,sobretasa FROM civa ORDER BY fecha desc LIMIT 1');
		$t=$con->row('tasa');$rt=$con->row('redutasa');$st=$con->row('sobretasa');

		$fecha =$do->get('fecha');
		$vd    =$do->get('vendedor');
		$tipoa =$do->get('tipo_doc');
		$numero=$do->get('numero');

		$iva=$totals=0;
		$tasa=$montasa=$reducida=$monredu=$sobretasa=$monadic=$exento=0;
		$cana=$do->count_rel('sitems');
		for($i=0;$i<$cana;$i++){
			$itcana    = $do->get_rel('sitems','cana',$i);
			$itpreca   = $do->get_rel('sitems','preca',$i);
			$itiva     = $do->get_rel('sitems','iva',$i);
			$itimporte = $itpreca*$itcana;
			$do->set_rel('sitems','tota'    ,$itimporte,$i);
			$do->set_rel('sitems','mostrado',$itimporte*(1+($itiva/100)),$i);

			$iiva    = $itimporte*($itiva/100);
			$iva    += $iiva;
			$totals += $itimporte;

			if($itiva-$t==0) {
				$tasa   +=$iva;
				$montasa+=$itimporte;
			}elseif($itiva-$rt==0) {
				$reducida+=$iva;
				$monredu +=$itimporte;
			}elseif($itiva-$st==0) {
				$sobretasa+=$iva;
				$monadic  +=$itimporte;
			}else{
				$exento+=$itimporte;
			}

			$do->set_rel('sitems','numa'    ,$numero ,$i);
			$do->set_rel('sitems','tipoa'   ,$tipoa  ,$i);
			$do->set_rel('sitems','transac' ,$transac,$i);
			$do->set_rel('sitems','fecha'   ,$fecha  ,$i);
			$do->set_rel('sitems','vendedor',$vd     ,$i);
		}
		$totalg = $totals+$iva;

		$do->set('exento'   ,$exento   );
		$do->set('tasa'     ,$tasa     );
		$do->set('reducida' ,$reducida );
		$do->set('sobretasa',$sobretasa);
		$do->set('montasa'  ,$montasa  );
		$do->set('monredu'  ,$monredu  );
		$do->set('monadic'  ,$monadic  );

		$do->set('inicial',0 );
		$do->set('totals' ,round($totals ,2));
		$do->set('totalg' ,round($totalg ,2));
		$do->set('iva'    ,round($iva    ,2));
		$do->set('referen','C');

		return true;
	}

	function _pre_update($do){
		return true;
	}

	function _pre_delete($do){
		return false;
	}

	function _post_insert($do){
		$numero  =$do->get('numero');
		$nfiscal =$do->get('nfiscal');
		$fecha   =$do->get('fecha');
		$totneto =$do->get('totalg');
		$impuesto=$do->get('iva');
		$hora    =$do->get('hora');
		$usuario =$do->get('usuario');
		$transac =$do->get('transac');
		$manda   =$do->get('mandatario');
		$nombre  =$this->datasis->dameval('SELECT nombre FROM scli WHERE cliente='.$this->db->escape($manda));
		$cod_cli =$do->get('cod_cli');
		$estampa =$do->get('estampa');
		$error   = 0;

		if($totneto>0){
			//Inserta en smov
			$mnumnc = $this->datasis->fprox_numero('nccli');
			$data=array();
			$data['cod_cli']    = $manda;
			$data['nombre']     = $nombre;
			$data['tipo_doc']   = 'ND';
			$data['numero']     = $mnumnc;
			$data['fecha']      = $fecha;
			$data['monto']      = $totneto;
			$data['impuesto']   = $impuesto;
			$data['abonos']     = 0;
			$data['vence']      = $fecha;
			$data['tipo_ref']   = 'FT';
			$data['num_ref']    = $nfiscal;
			$data['observa1']   = 'FACTURA POR TERCERO A '.$cod_cli.' FC'.$nfiscal;
			$data['estampa']    = $estampa;
			$data['hora']       = $hora;
			$data['transac']    = $transac;
			$data['usuario']    = $usuario;
			$data['codigo']     = 'NOCON';
			$data['descrip']    = 'NOTA DE CONTABILIDAD';

			$sql= $this->db->insert_string('smov', $data);
			$ban=$this->db->simple_query($sql);
			if($ban==false){ memowrite($sql,'sfacman'); $error++;}
		}

		$primary =implode(',',$do->pk);
		logusu($do->table,"Creo $this->tits $primary ");
	}

	function _post_update($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Modifico $this->tits $primary ");
	}

	function _post_delete($do){
		$primary =implode(',',$do->pk);
		logusu($do->table,"Elimino $this->tits $primary ");
	}

	function instalar(){
		if(!$this->datasis->iscampo('sfac','mandatario')){
			$mSQL="ALTER TABLE sfac ADD COLUMN mandatario VARCHAR(5) NULL DEFAULT NULL COMMENT ''";
			$ban=$this->db->simple_query($mSQL);
		}
	}
}
