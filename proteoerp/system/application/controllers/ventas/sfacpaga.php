<?php
class sfacpaga extends Controller {

	var $url='ventas/sfacpaga/';

	function sfacpaga(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('12C',1);
	}

	function index(){
		redirect($this->url.'principal');
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid');
		$this->load->library('encrypt');
		$this->rapyd->uri->keep_persistence();

		//filter
		$filter = new DataFilter('Filtro');
		$select=array('vd','tipo_doc','numero','fecha','vence','pagada','dias','comision','comical','cod_cli','nombre','sepago');
		#$select[]="GROUP_CONCAT(e.despacha) LIKE '%S%' AS parcial";
		$filter->db->select($select);
		$filter->db->from('sfac');
		$filter->db->where('pagada >'   ,'0');
		$filter->db->where('tipo_doc !=','X');
		$filter->db->orderby('fecha');
		$filter->db->_escape_char='';
		$filter->db->_protect_identifiers=false;

		$filter->fechad = new dateonlyField('Desde', 'fechad');
		$filter->fechah = new dateonlyField('Hasta', 'fechah');
		$filter->fechad->clause  =$filter->fechah->clause='where';
		$filter->fechad->db_name =$filter->fechah->db_name='fecha';
		$filter->fechad->insertValue = date('Y-m-d', mktime(0,0,0,date('n')-1,1));
		$filter->fechah->insertValue = date('Y-m-d', mktime(0,0,0,date('n')  ,0));
		$filter->fechad->rule='required|chfecha';
		$filter->fechah->rule='required|chfecha';
		$filter->fechad->operator='>=';
		$filter->fechah->operator='<=';

		$filter->vd = new dropdownField('Vendedor', 'vd');
		$filter->vd->db_name = 'vd';
		$filter->vd->rule    = 'required';
		$filter->vd->clause  = 'where';
		$filter->vd->operator= '=';
		$filter->vd->options("SELECT vendedor, CONCAT_WS(' ',vendedor,nombre) AS nom FROM vend ORDER BY vendedor");

		$filter->buttons('reset','search');
		$action = "javascript:window.location='".site_url('ventas/sfacpaga/principal')."'";
		$filter->button('btn_regresa', 'Regresar', $action, 'TR');
		$filter->build();

		if(!$this->rapyd->uri->is_set('search')) $filter->db->where('fecha','CURDATE()');

		function descheck($numero,$tipo_doc,$sepago){
		$a=$tipo_doc.'AA'.$numero;
			$data = array(
			  'name'    => 'sepago[]',
			  'id'      => $a,
			  'value'   => $a,
			  'checked' => $sepago=='S' ? true:false,
			);
			return form_checkbox($data);
		}

		$seltodos='Seleccionar <a id="todos" href=# >Todos</a> <a id="nada" href=# >Ninguno</a> <a id="alter" href=# >Invertir</a>';

		$grid = new DataGrid($seltodos);//"$seltodos"
		$grid->use_function('descheck');
		$grid->use_function('colum');
		$grid->use_function('parcial');

		function colum($tipo_doc){
			if($tipo_doc=='Anulada'){
				return ('<b style="color:red;">'.$tipo_doc.'</b>');
			}else{
				return ($tipo_doc);
			}
		}

		function parcial($parcial) {
			if ($parcial)
				return '*';
			else
				return '';
		}

		$link=anchor($this->url.'parcial/<#numero#>','<#numero#>');
		$grid->column('Vendedor' ,'<#vd#>');
		$grid->column('Tipo'     ,'<colum><#tipo_doc#></colum>');
		$grid->column('N&uacute;mero','<#numero#>');
		$grid->column('Fecha'    ,'<dbdate_to_human><#fecha#></dbdate_to_human>');
		$grid->column('Vence'    ,'<dbdate_to_human><#vence#></dbdate_to_human>');
		$grid->column('Pagada'   ,'<dbdate_to_human><#pagada#></dbdate_to_human>');
		$grid->column('Dias'     ,'<nformat><#dias#>|0|,|.</nformat>'        ,"align='right'");
		$grid->column('Comisi&oacute;n','<nformat><#comision#>|2|,|.</nformat>',"align='right'");
		$grid->column('Comisi&oacute;n Calculada','<nformat><#comical#>|2|,|.</nformat>'  ,"align='right'");
		$grid->column('Cliente'  ,'<#cod_cli#>');
		$grid->column('Nombre'   ,'<#nombre#>');
		$grid->column('Pagado'   ,"<descheck><#numero#>|<#tipo_doc#>|<#sepago#></descheck>","align=center");
		$grid->build();
		//echo $grid->db->last_query();

		$script ='<script type="text/javascript">
		$(document).ready(function() {
			$("#todos").click(function() { $("#asepago").checkCheckboxes();   });
			$("#nada").click(function()  { $("#asepago").unCheckCheckboxes(); });
			$("#alter").click(function() { $("#asepago").toggleCheckboxes();  });
		});
		</script>';
		$consulta =$grid->db->last_query();
		$mSQL = $this->encrypt->encode($consulta);

		//$campo="<form action='/../../proteoerp/xlsauto/repoauto2/'; method='post'>
		//<input size='100' type='hidden' name='mSQL' value='${mSQL}'>
		//<input type='submit' value='Descargar a Excel' name='boton'/>
		//</form>";

		$attributes = array('id' => 'asepago');
		$data['content'] =  $filter->output;//.$campo;
		if($grid->recordCount>0)
		$data['content'] .=form_open($this->url.'procesar',$attributes).$grid->output.form_submit('mysubmit', 'Guardar').form_close().$script;
		$data['title']    ='<h1>Marcar Facturas Pagadas</h1>';
		$data['head']     =script('jquery-1.2.6.pack.js');
		$data['head']    .=script('plugins/jquery.checkboxes.pack.js').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function procesar(){
		$this->rapyd->uri->keep_persistence();
		$persistence = $this->rapyd->session->get_persistence($this->url.'filteredgrid', $this->rapyd->uri->gfid);
		$back= (isset($persistence['back_uri'])) ?$persistence['back_uri'] : $this->url.'filteredgrid';

		foreach($_POST['sepago'] as $key){
			$a=explode('AA',$key);
			$dbumero=$this->db->escape($a[1]);
			$dbtipo =$this->db->escape($a[0]);
			$mSQL="UPDATE sfac SET sepago='S' WHERE numero=${dbumero} AND tipo_doc=${dbtipo}";
			$this->db->simple_query($mSQL);
		}

		//redirect('ventas/sfacpaga/filteredgrid/search/osp');
		redirect($back);
	}

	function activar(){
		$numero  = $this->db->escape($this->input->post('numa'));
		$codigo  = $this->db->escape($this->input->post('codigoa'));
		$usuario = $this->db->escape($this->session->userdata('usuario'));

		$mSQL="UPDATE sitems SET despacha=IF(despacha='S','N','S'), fdespacha=if(despacha='S',CURDATE(),NULL), udespacha=$usuario WHERE codigoa=${codigo} AND numa=${numero} AND tipoa='F' ";
		$a   = $this->db->simple_query($mSQL);
		$can = $this->datasis->dameval("SELECT COUNT(*) FROM sitems WHERE numa=${numero} AND tipoa='F' AND despacha='N'");
		if($can==0){
			$mSQL="UPDATE sfac SET fdespacha=CURDATE(), udespacha=${usuario} WHERE numero=${numero} AND tipo_doc='F'";
			$this->db->simple_query($mSQL);
		}
	}

	function parcial($numero){
		$this->rapyd->load('datafilter','datagrid');

		function ractivo($despacha,$numero,$codigoa){
		 $retorna= array(
				'name'  => $numero,
				'id'    => $codigoa,
				'value' => 'accept'
				);
		 if($despacha=='S'){
				$retorna['checked']= TRUE;
			}else{
				$retorna['checked']= FALSE;
			}
			return form_checkbox($retorna);
		}
		function colum($tipo_doc) {
			if ($tipo_doc=='Anulada')
				return ('<b style="color:red;">'.$tipo_doc.'</b>');
			else
				return ($tipo_doc);
		}

		$grid = new DataGrid('Despacho parcial');
		$grid->db->_escape_char='';
		$grid->db->_protect_identifiers=false;

		$grid->db->from('sitems');
		$grid->db->where('tipoa'   ,'F');
		$grid->db->where('numa'    ,$numero);

		$grid->use_function('ractivo');
		$grid->use_function('colum');

		$grid->column('C&oacute;digo'     ,'codigoa');
		$grid->column('Descripci&oacute;n','desca'  );
		$grid->column('Cantidad'  ,'cana',"align=right");
		$grid->column('Precio'    ,'<nformat><#preca#></nformat>');
		$grid->column('Total'     ,'<nformat><#tota#></nformat>',"align=right");
		$grid->column('Despachado','<ractivo><#despacha#>|<#numa#>|<#codigoa#></ractivo>','align="center"');
		$grid->build();
		$tabla=$grid->output;

		$script='';
		$url=site_url('ventas/sfac/activar');
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
		if($grid->recordCount>0)
		$atras=anchor('ventas/sfacpaga/filteredgrid/search/osp','Regresar');
		$data['content'] .=form_open('').$grid->output.form_close().$script.$atras;
		$data['title']   =  '<h1>Despacho Parcial</h1>';
		$data['head']    =  script('jquery-1.2.6.pack.js');
		$data['head']    .= script('plugins/jquery.checkboxes.pack.js').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function principal(){
		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0'
		);

		$salida = "<table width='95%'>
		<tr>
			<td colspan='2'><h1>Pasos para el pago de comisiones</h1></td>
		</tr>
		<tr><td valign='top'>";
		$salida.= "<h2>1-.".anchor($this->url.'filteredgrid','Marcar Facturas')."</h2>";
		$salida.= "<p>En esta secci&oacute;n puede marca las facturas cuyas comisiones ya fueron pagadas en procesos anteriores.</p>";

		$salida.= "</td><td valign='top'>";

		$salida.= "<h2>2-.".anchor($this->url.'call','Calcular Comisiones')."</h2>";
		$salida.= "<p>Ejecuta un procedimiento que calcula las comisiones por cada factura seg&uacute;n los parametros prestablecidos y busca la fecha del &uacute;ltimo pago para calcular los d&iacute;as efectivamente transcurridos entre la emisi&oacute;n del documento y su respectivo pago.</p>";

		$salida.= "</td></tr><tr><td valign='top'>";

		$salida.= "<h2>3-.".anchor_popup('ventas/calcomi','Penalizaci&oacute;n',$atts)."</h2>";
		$salida.= "<p>Permite definir y aplicar una sanciones de acuerdo a la efectividad de la cobranza medida por los d&iacute;as transcurridos para el pago de las mismas.</p>";
		$salida.= "</td><td valign='top'>";

		$salida.= "<h2>4-.".anchor_popup('reportes/ver/SFACCOM/SFAC','Listado de Comisiones',$atts)."</h2>";
		$salida.= "<p>Emite un listado para la verificaci&oacute;n y liquidaci&oacute;n.</p>";
		$salida.= "</td></tr></table>\n";

		$data['content'] = $salida;
		$data['title']   = '<h1>'.img('/assets/default/images/groups.png',array('border'=>'0')).'Comisiones por Ventas y Cobros</h1>';
		$data['head']    = script('jquery-1.2.6.pack.js');
		$data['head']   .= script('plugins/jquery.checkboxes.pack.js').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function call(){
		$bool = $this->db->query('CALL sp_comical()');
		if($bool){
			$salida = 'Se calcularon la comisiones correctamente';
		}else{
			$salida = 'No se pudireon calcular la comisiones correctamente';
		}

		$salida.='</br>';
		$salida.=anchor($this->url.'principal','Regresar');
		$salida.='</br>';

		$data['content'] = $salida;
		$data['title']   = heading('C&aacute;lculo de las comisiones');
		$data['head']    = script('jquery-1.2.6.pack.js');
		$data['head']   .= script('plugins/jquery.checkboxes.pack.js').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
}
