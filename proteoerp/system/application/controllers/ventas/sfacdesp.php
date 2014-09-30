<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class sfacdesp extends Controller {

	function sfacdesp(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id(111,1);
	}

	function index(){
		$this->rapyd->uri->keep_persistence();
		$data['content'] = '<p style="text-align:center;"><div style="margin-left: auto;margin-right: auto;" id="maso" >';

		$data['content'].= '<div class="box" style="width:230px;height:255px;background-color: #F9F7F9;"><h3>Despacho Masivo</h3>'.br();
		$data['content'].= '<a href="'.site_url('ventas/sfacdesp/filterexpress').'"><img border=0 src="'.base_url().'images/despachoexp1.png'.'"></a>'.br();
		$data['content'].= '<p>Seleccione esta opci&oacute;n para despachos masivos de <b>varios d&iacute;as</b> en forma r&aacute;pida y eficiente</p>.'.br();
		$data['content'].= '</div>';

		$data['content'].= '<div class="box" style="width:230px;height:255px;background-color: #F9F7F9;"><h3>Despacho Diario o Parcial</h3>'.br();
		$data['content'].= '<a href="'.site_url('ventas/sfacdesp/filteredgrid').'"><img border=0 src="'.base_url().'images/despachoexp2.png'.'"></a>'.br();
		$data['content'].= '<p>Seleccione esta opci&oacute;n para hacer despachos de <b>un solo d&iacute;a espec&iacute;fico, permite despachos parciales</b>.</p>'.br();
		$data['content'].= '</div>';

		$data['content'].= '<div class="box" style="width:230px;height:255px;background-color: #F9F7F9;"><h3>Reverso de despachos</h3>'.br();
		$data['content'].= '<a href="'.site_url('ventas/sfacdesp/filteredrev').'"><img border=0 src="'.base_url().'images/despachoexp3.png'.'"></a>'.br();
		$data['content'].= '<p>Seleccione esta opci&oacute;n para <b>reversar despachos hechos por error</b>.</p>'.br();
		$data['content'].= '</div>';

		$data['content'].= '</div></p>';

		$data['title']   = heading('Despacho Express');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas_masonry', $data);
	}

	//Despacho unitario
	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');
		$this->rapyd->uri->keep_persistence();
		$this->load->library('encrypt');

		$filter = new DataFilter('');
		$select=array('a.cod_cli AS cliente','a.fecha','if(a.referen=\'C\',\'Cred\',\'Cont\') referen','a.numero','a.nombre','a.totalg AS total','a.vd','d.nombre AS vendedor');
		$select[]='GROUP_CONCAT(e.despacha) LIKE \'%S%\' AS parcial';
		$filter->db->select($select);
		$filter->db->from('sitems AS e');
		$filter->db->join('itsnot AS c' ,'c.factura=e.numa AND c.codigo=e.codigoa','LEFT');
		$filter->db->join('sfac AS a'   ,'e.numa=a.numero AND e.tipoa=a.tipo_doc');
		$filter->db->join('sfac AS g'   ,'a.numero=g.factura AND g.tipo_doc=\'D\'','LEFT');
		$filter->db->join('vend AS d'   ,'a.vd=d.vendedor');
		//$filter->db->join('snot AS c' ,'a.numero=c.factura','LEFT');
		//$filter->db->join('sfac AS f','a.numero=e.factura','LEFT');
		$filter->db->groupby('e.numa');
		$filter->db->where('a.fdespacha IS NULL');
		$filter->db->where('g.factura IS NULL');
		$filter->db->where('a.tipo_doc','F');
		$filter->db->where('c.factura IS NULL');
		$filter->db->where('MID(a.numero,1,1) <> "_"');
		$filter->db->where('a.referen <> "P"');
		$filter->db->orderby('a.fecha DESC, a.numero');
		$filter->db->_escape_char='';
		$filter->db->_protect_identifiers=false;

		$filter->container = new containerField('info','<p style=\'color:blue;\'>Use esta opci&oacute;n si desea despachos de <b>un solo d&iacute;a espec&iacute;fico, o despachos parciales</b>.</p>');
		$filter->container->clause='';

		$filter->fechad = new dateonlyField('Fecha', 'fechad');
		$filter->fechad->clause = 'where';
		$filter->fechad->db_name = 'a.fecha';
		$filter->fechad->size = 12;
		$filter->fechad->operator = '=';
		$filter->fechad->rule='required|chfecha';
		$filter->fechad->insertValue=date('Y-m-d');

		$filter->numero = new inputField('N&uacute;mero de factura', 'numero');
		$filter->numero->db_name = 'a.numero';
		$filter->numero->rule = 'existefac|callback_chnodesp';
		$filter->numero->size = 20;

		$action = "javascript:window.location='".site_url('ventas/sfacdesp/index')."'";
		$filter->button('btn_regresa', 'Regresar', $action, 'BR');

		$filter->buttons('reset','search');
		$filter->build();

		$uri = 'ventas/cajeros/dataedit/show/<#cajero#>';

		if(!$this->rapyd->uri->is_set('search')) $filter->db->where('a.fecha','CURDATE()');

		function descheck($numero){
			$data = array(
				'name'    => 'despacha[]',
				'id'      => $numero,
				'value'   => $numero,
				'title' => 'Tildar para marcar como despachada la factura y presionar el boton de "Despachar Facturas Marcadas"',
				'checked' => false);
			return form_checkbox($data);
		}

		$seltodos='Seleccionar <a id="todos" href=# >Todos</a> <a id="nada" href=# >Ninguno</a> <a id="alter" href=# >Invertir</a>';

		function colum($tipo_doc) {
			if ($tipo_doc=='Anulada')
				return ('<b style="color:red;">'.$tipo_doc.'</b>');
			else
				return ($tipo_doc);
		}
		function parcial($parcial) {
			if ($parcial)
				return '*';
			else
				return '';
		}

		if($filter->is_valid()){
			$grid = new DataGrid($seltodos);
			$grid->use_function('descheck');
			$grid->use_function('colum');
			$grid->use_function('parcial');

			$link=anchor('ventas/sfacdesp/parcial/<#numero#>','<#numero#>','title="Haga click para despachos parciales"');
			$grid->column('Fecha'        ,'<dbdate_to_human><#fecha#></dbdate_to_human>');
			$grid->column('Tipo'         ,'referen');
			$grid->column('N&uacute;mero','<parcial><#parcial#></parcial>'.$link);
			$grid->column('Cliente'      ,'cliente');
			$grid->column('Nombre'       ,'nombre');
			$grid->column('Total'        ,'<nformat><#total#></nformat>'   ,"align='right'");
			$grid->column('Vendedor'     ,'(<#vd#>) <#vendedor#>'          ,"align='center'");
			$grid->column('Despachar'    ,'<descheck><#numero#></descheck>',"align='center'");

			$action = "javascript:if(confirm('Seguro que deseas marcar despachadas las facturas seleccionadas?')){ $('#adespacha').submit(); }";
			$grid->button('btn_submit', 'Despachar Facturas Marcadas', $action, 'BR');

			$grid->build();

			$cana=$grid->recordCount;
			$js='';
		}else{
			if($this->rapyd->uri->is_set('search'))
				$filter->build_form();
			$cana=0;
		}

		$script ='<script type="text/javascript">
		$(document).ready(function() {
			$("#todos").click(function() { $("#adespacha").checkCheckboxes();   });
			$("#nada").click(function()  { $("#adespacha").unCheckCheckboxes(); });
			$("#alter").click(function() { $("#adespacha").toggleCheckboxes();  });
		});
		</script>';

		$attributes = array('id' => 'adespacha');
		$data['content'] =  '<table width="100%"><tr><td>'.img(array('src'=>'images/despachoexp2.png','align'=>'left')).'</td><td>'.$filter->output.'</td></tr></table>';
		if($cana>0)
			$data['content'] .=form_open('ventas/sfacdesp/procesar/E',$attributes).$grid->output.form_close().$script;
		$data['title']   =  heading('Despacho Express Diario o Parcial');
		$data['head']    =  script('jquery-1.2.6.pack.js');
		$data['head']    .= script("plugins/jquery.checkboxes.pack.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	//Verifica si la factura fue despachada
	function chdespacha($numero){
		$dbnumero = $this->db->escape($numero);
		$fdespacha= $this->datasis->dameval("SELECT fdespacha FROM sfac WHERE numero=${dbnumero}");
		if(empty($fdespacha)){
			$this->validation->set_message('chdespacha', "La factura ${numero} no esta marcada como despachada.");
			return false;
		}
		return true;
	}

	//Verifica si la factura no fue despachada
	function chnodesp($numero){
		$dbnumero = $this->db->escape($numero);
		$fdespacha= $this->datasis->dameval("SELECT fdespacha FROM sfac WHERE numero=${dbnumero}");
		if(empty($fdespacha)){
			return true;
		}
		$this->validation->set_message('chnodesp', "La factura ${numero} ya esta marcada como despachada el día ".dbdate_to_human($fdespacha).".");
		return false;
	}

	function filteredrev(){
		$this->rapyd->load('dataform');

		$script = '$("#numero").autocomplete({
			delay: 600,
			autoFocus: true,
			source: function( req, add){
				$.ajax({
					url:  "'.site_url('ajax/buscasfacdev').'",
					type: "POST",
					dataType: "json",
					data: {"q":req.term},
					success:
						function(data){
							var sugiere = [];
							if(data.length==0){
								$("#numero").val("");
								$("#nombre").text("");
							}else{
								$.each(data,
									function(i, val){
										sugiere.push( val );
									}
								);
								add(sugiere);
							}
						},
				})
			},
			minLength: 2,
			select: function( event, ui ) {
				$("#numero").attr("readonly", "readonly");
				$("#numero").val(ui.item.codigo);
				$("#nombre").text(ui.item.nombre+" Total: "+ui.item.totalg);
				setTimeout(function() {  $("#numero").removeAttr("readonly"); }, 1500);
			}
		});';

		$filter = new DataForm('ventas/sfacdesp/filteredrev/process');
		$filter->script($script);
		//$filter->title('Reverso de despacho Express');
		$filter->container = new containerField('info','<p style=\'color:blue;\'>Coloque el n&uacute;mero de la factura que desea reversarle el despacho (Solo aplica para facturas despachadas por cualquiera de los m&oacute;dulos de  despacho express, las que fueron despachadas por nota de despacho seguir&aacute;n iguales).</p>');
		$filter->container->clause='';

		$filter->numero = new inputField('N&uacute;mero de factura', 'numero');
		$filter->numero->rule = 'required|existefac|callback_chdespacha';
		$filter->numero->size = 15;

		$filter->nombre = new containerField('nombre','<span id="nombre"></span>');
		$filter->nombre->in='numero';

		$action = "javascript:window.location='".site_url('ventas/sfacdesp/index')."'";
		$filter->button('btn_regresa', 'Regresar', $action, 'BR');

		$filter->submit('btnsubmit','Reversar despacho');
		$filter->build_form();

		$rt='';
		if($filter->on_success()){
			$numero   = $filter->numero->newValue;
			$dbnumero = $this->db->escape($numero);
			$mSQL="UPDATE sitems SET despacha='I', fdespacha=NULL, udespacha=NULL WHERE numa=${dbnumero} AND tipoa='F'";
			$this->db->simple_query($mSQL);
			$mSQL="UPDATE sfac SET fdespacha=NULL, udespacha=NULL WHERE numero=${dbnumero} AND tipo_doc='F'";
			$this->db->simple_query($mSQL);
			logusu('SFACDESP',"REVERSO DE DESPACHO EXPRESS FACTURA ${numero}");
			$rt='<p style="text-align:center;color:green;font-weight:bold;">Despacho Express de factura '.$numero.' reversado.</p>';
		}

		$data['content'] =  '<table width="100%"><tr><td>'.img(array('src'=>'images/despachoexp3.png','align'=>'left')).'</td><td>'.$filter->output.$rt.'</td></tr></table>';
		$data['title']   = heading('Reverso de despacho Express');
		$data['script']  = script('jquery.js');
		$data['script'] .= script('jquery-ui.js');
		$data['script'] .= script('plugins/jquery.numeric.pack.js');
		$data['script'] .= script('plugins/jquery.floatnumber.js');
		$data['script'] .= phpscript('nformat.js');
		$data['head']    = $this->rapyd->get_head();
		$data['head']   .= style('redmond/jquery-ui-1.8.1.custom.css');
		$this->load->view('view_ventanas', $data);
	}

	//Masivo
	function filterexpress(){
		$this->rapyd->load('datafilter','datagrid');
		$this->rapyd->uri->keep_persistence();
		$this->load->library('encrypt');

		$filter = new DataFilter('');
		$select=array("IF(a.tipo_doc='F','Activa',IF(a.tipo_doc='D','Devolucion',IF(a.tipo_doc='X','Anulada','Otro'))) AS tipo_doc",
		'a.cod_cli AS cliente','a.fecha',"IF(a.referen='C','Cred','Cont') referen",'a.numero','a.nombre','a.totalg AS total','a.vd',
		'g.numero AS devo','a.id');
		$filter->db->select($select);
		$filter->db->from('sfac AS a');
		$filter->db->join('snot AS c' ,'a.numero=c.factura','LEFT');
		$filter->db->join('sfac AS g'   ,'a.numero=g.factura AND g.tipo_doc=\'D\'','LEFT');
		$filter->db->groupby('a.numero,a.tipo_doc');
		$filter->db->where('a.fdespacha IS NULL');
		$filter->db->where('a.tipo_doc','F');
		$filter->db->where('c.factura IS NULL');
		$filter->db->where('MID(a.numero,1,1) <> "_"');
		$filter->db->where('a.referen <> "P"');

		$filter->db->orderby('a.fecha DESC, a.numero');
		$filter->db->_escape_char='';
		$filter->db->_protect_identifiers=false;

		$filter->container = new containerField('info','<p style=\'color:blue;\'>Use esta opci&oacute;n si necesita despachos masivos de <b>varios d&iacute;as</b> en forma r&aacute;pida y eficiente.</p>');
		$filter->container->clause='';

		$filter->fechad = new dateonlyField('Desde', 'fechad');
		$filter->fechah = new dateonlyField('Hasta', 'fechah');
		$filter->fechad->clause  =$filter->fechah->clause ='where';
		$filter->fechad->db_name =$filter->fechah->db_name='a.fecha';
		$filter->fechad->insertValue = date('Y-m-d');
		$filter->fechah->insertValue = date('Y-m-d');
		$filter->fechad->rule=$filter->fechah->rule='required';
		$filter->fechad->operator='>=';
		$filter->fechah->operator='<=';
		$filter->fechah->group = $filter->fechad->group ='Rango de fecha';
		$filter->fechah->size  = $filter->fechad->size = 12;

		//$filter->numero = new inputField('N&uacute;mero de factura', 'numero');
		//$filter->numero->db_name='a.numero';
		//$filter->numero->size = 20;

		$action = "javascript:window.location='".site_url('ventas/sfacdesp/index')."'";
		$filter->button('btn_regresa', 'Regresar', $action, 'BR');

		$filter->buttons('reset','search');
		$filter->build();

		$uri = 'ventas/cajeros/dataedit/show/<#cajero#>';

		if(!$this->rapyd->uri->is_set('search')) $filter->db->where('a.fecha','CURDATE()');

		function descheck($numero,$devolu=null){
			$data = array(
			  'name'    => 'despacha[]',
			  'id'      => $numero,
			  'value'   => $numero,
			  'title' => 'Tildar para marcar como despachada la factura y presionar el boton de "Despachar Facturas Marcadas"',
			  'checked' => false);

			if(!empty($devolu)){
				$data['title']='Factura con devolucion '.$devolu;
				$style='style="background-color:red;" title=\'Factura con devolucion '.$devolu.'\'';
			}else{
				$style='';
			}
			return "<div ${style}>".form_checkbox($data).'</div>';
		}

		function verfact($numero,$id){
			$atts = array(
				'width'      => '800',
				'height'     => '600',
				'scrollbars' => 'yes',
				'status'     => 'yes',
				'resizable'  => 'yes',
				'screenx'    => '0',
				'screeny'    => '0'
			);

			return anchor_popup('formatos/verhtml/FACTURA/'.$id, $numero, $atts);
		}

		$seltodos='Seleccionar <a id="todos" href=# >Todos</a> <a id="nada" href=# >Ninguno</a> <a id="alter" href=# >Invertir</a>';

		if($filter->is_valid()){
			$grid = new DataGrid($seltodos);
			$grid->use_function('descheck','verfact');

			$grid->column('Fecha'        ,'<dbdate_to_human><#fecha#></dbdate_to_human>');
			$grid->column('Tipo'         ,'referen');
			$grid->column('N&uacute;mero','<verfact><#numero#>|<#id#></verfact>');
			$grid->column('Cliente'      ,'cliente');
			$grid->column('Nombre'       ,'nombre');
			$grid->column('Total'        ,'<nformat><#total#></nformat>',"align=right");
			$grid->column('Vendedor'     ,'vd',"align=center");
			$grid->column('Despachar'    ,'<descheck><#numero#>|<#devo#></descheck>',"align=center");

			$action = "javascript:$('#aexcel').submit();";
			$grid->button('btn_excel', 'Descargar a Excel', $action, 'BL');

			$action = "javascript:if(confirm('Seguro que deseas marcar despachadas las facturas seleccionadas?')){ $('#adespacha').submit(); }";
			$grid->button('btn_submit', 'Despachar Facturas  Marcadas', $action, 'BR');

			$grid->build();

			//echo $grid->db->last_query();
			$cana=$grid->recordCount;

			$consulta = $grid->db->last_query();
			$mSQL     = $this->encrypt->encode($consulta);
			$campo="<form id='aexcel' action='/../../proteoerp/xlsauto/repoauto2/' method='post'><input size='100' type='hidden' name='mSQL' value='${mSQL}'></form>";
		}else{
			if($this->rapyd->uri->is_set('search'))
				$filter->build_form();
			$campos='';
			$cana=0;
		}

		$script ='<script type="text/javascript">
		$(document).ready(function() {
			$("#todos").click(function() { $("#adespacha").checkCheckboxes();   });
			$("#nada").click(function()  { $("#adespacha").unCheckCheckboxes(); });
			$("#alter").click(function() { $("#adespacha").toggleCheckboxes();  });
		});
		</script>';

		//$ggrid.=form_hidden('mSQL', $mSQL);

		$attributes = array('id' => 'adespacha');
			$data['content'] =  '<table width="100%"><tr><td>'.img(array('src'=>'images/despachoexp1.png','align'=>'left')).'</td><td>'.$filter->output.'</td></tr></table>';
		if($cana>0)
			$data['content'] .=form_open('ventas/sfacdesp/procesar/M',$attributes).$grid->output.form_close().$campo.$script;
		$data['title']   =  heading('Despacho Express Masivo');
		$data['head']    =  script('jquery-1.2.6.pack.js');
		$data['head']    .= script('plugins/jquery.checkboxes.pack.js').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	//Despacho masivo
	function procesar($tipo='M'){
		$this->rapyd->uri->keep_persistence();
		if($tipo=='M'){
			$metodo='filterexpress';
		}else{
			$metodo='filteredgrid';
		}

		$persistence = $this->rapyd->session->get_persistence('ventas/sfacdesp/'.$metodo, $this->rapyd->uri->gfid);
		$back= (isset($persistence['back_uri'])) ?$persistence['back_uri'] : 'ventas/sfacdesp/'.$metodo.'/search/osp';

		foreach($_POST['despacha'] as $fila){
			$dbusuario = $this->db->escape($this->session->userdata('usuario'));
			$dbfila    = $this->db->escape($fila);
			$mSQL="UPDATE sitems SET despacha='S', fdespacha=CURDATE(), udespacha=${dbusuario} WHERE numa=${dbfila} AND tipoa='F' ";
			$this->db->simple_query($mSQL);
			$mSQL="UPDATE sfac SET fdespacha=CURDATE(), udespacha=${dbusuario} WHERE numero=${dbfila} AND tipo_doc='F' ";
			$this->db->simple_query($mSQL);
		}
		redirect($back);
	}

	//Despacho parcial
	function activar(){
		$numero  = $this->db->escape($this->input->post('numa'));
		$codigo  = $this->db->escape($this->input->post('codigoa'));
		$usuario = $this->db->escape($this->session->userdata('usuario'));

		$mSQL="UPDATE sitems SET despacha=IF(despacha='S','I','S'), fdespacha=IF(despacha='S',CURDATE(),NULL), udespacha=${usuario} WHERE codigoa=${codigo} AND numa=${numero} AND tipoa='F' ";
		$a   = $this->db->simple_query($mSQL);
		$mSQL="SELECT COUNT(*) FROM sitems AS a LEFT JOIN `itsnot` AS b ON `b`.`factura`=`a`.`numa` AND b.codigo=a.codigoa WHERE numa=${numero} AND tipoa='F'  AND b.codigo IS NULL AND despacha<>'S'";
		$can = $this->datasis->dameval($mSQL);
		if($can==0){
			$mSQL="UPDATE sfac SET fdespacha=CURDATE(), udespacha=${usuario} WHERE numero=${numero} AND tipo_doc='F'";
			$this->db->simple_query($mSQL);
		}
		//$mSQL="UPDATE sfac SET fdespacha=CURDATE(), udespacha='$usuario' WHERE numero='$numero' AND tipo_doc='F' ";
		//$b=$this->db->simple_query($mSQL);
	}

	function parcial($numero){

		$this->rapyd->uri->keep_persistence();

		$persistence = $this->rapyd->session->get_persistence('ventas/sfacdesp/filteredgrid', $this->rapyd->uri->gfid);
		$back= (isset($persistence['back_uri'])) ?$persistence['back_uri'] : 'ventas/sfacdesp/filteredgrid/';


		$this->rapyd->load('datafilter','datagrid');

		function ractivo($despacha,$numero,$codigoa){
		 $retorna= array(
			'name'  => $numero,
			'id'    => $codigoa,
			'value' => 'accept',
			'title' => 'Presionar para marcar o desmarcar como despachado'
			);
			if($despacha=='S'){
				$retorna['checked']= true;
			}else{
				$retorna['checked']= false;
			}
			return form_checkbox($retorna);
		}

		function colum($tipo_doc) {
			if ($tipo_doc=='Anulada')
				return ('<b style="color:red;">'.$tipo_doc.'</b>');
			else
				return ($tipo_doc);
		}

		$grid = new DataGrid("Despacho parcial, factura nro.: ${numero}");
		$grid->db->_escape_char='';
		$grid->db->_protect_identifiers=false;

		$grid->db->select(array('a.codigoa','a.desca','a.cana','a.preca','a.tota','a.despacha','a.numa'));
		$grid->db->from('sitems AS a');
		$grid->db->join('itsnot AS b','a.codigoa=b.codigo AND a.numa=b.factura','LEFT');
		$grid->db->where('a.tipoa'   ,'F');
		$grid->db->where('a.numa'    ,$numero);
		$grid->db->where('b.factura IS NULL');

		$grid->use_function('ractivo');
		$grid->use_function('colum');

		$grid->column('C&oacute;digo'     ,'codigoa');
		$grid->column('Descripci&oacute;n','desca');
		$grid->column('Cantidad'   ,'<nformat><#cana#></nformat>' ,'align=\'right\'');
		$grid->column('Precio'     ,'<nformat><#preca#></nformat>','align=\'right\'');
		$grid->column('Total'      ,'<nformat><#tota#></nformat>' ,'align=\'right\'');
		$grid->column('Despachado' ,'<ractivo><#despacha#>|<#numa#>|<#codigoa#></ractivo>','align=\'center\'');

		$action = "javascript:window.location='".site_url($back)."'";
		$grid->button('btn_regresa', 'Regresar', $action, 'BR');


		$grid->build();
		$tabla=$grid->output;
		//echo $grid->db->last_query();

		$script='';
		$url=site_url('ventas/sfacdesp/activar');
		$data['script']='<script type="text/javascript">
			$(document).ready(function() {
				$("form :checkbox").click(function () {
						$.ajax({
						  type: "POST",
						  url: "'.$url.'",
						  data: "numa="+this.name+"&codigoa="+this.id,
						  success: function(msg){
						  //alert(msg);
						  }
						});
					}).change();
			});
			</script>';

		$attributes = array('id' => 'adespacha');
		$data['content'] =  '';
		//if($grid->recordCount>0)
		//$atras=anchor('ventas/sfacdesp/filteredgrid/search/osp','Regresar');
		$data['content'] .=form_open('').$grid->output.form_close().$script;
		$data['title']   =  heading('Despacho Parcial');
		$data['head']    =  script('jquery-1.2.6.pack.js');
		$data['head']    .= script('plugins/jquery.checkboxes.pack.js').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
}
