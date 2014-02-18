<?php require_once(APPPATH.'/controllers/inventario/consultas.php');
class Scst extends Controller {

	function scst(){
		parent::Controller();
		$this->load->library('rapyd');
		//$this->datasis->modulo_id(201,1);
	}

	function index() {
		redirect('farmacia/scst/datafilter');
	}

	function datafilter(){
		$this->rapyd->set_connection('farmax');
		$this->rapyd->load_db();

		$this->rapyd->load('datagrid','datafilter');
		$this->rapyd->uri->keep_persistence();

		$atts = array(
				'width'      => '800',
				'height'     => '600',
				'scrollbars' => 'yes',
				'status'     => 'yes',
				'resizable'  => 'yes',
				'screenx'    => '0',
				'screeny'    => '0'
			);

		$modbus=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo Proveedor',
				'nombre'=>'Nombre',
				'rif'=>'RIF'),
			'filtro'  =>array('proveed'=>'C&oacute;digo Proveedor','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed'),
			'titulo'  =>'Buscar Proveedor');

		$boton=$this->datasis->modbus($modbus);

		$filter = new DataFilter('Filtro de Compras');
		$filter->db->select=array('numero','fecha','vence','nombre','montoiva','montonet','proveed','control');
		$filter->db->from('scst');

		$filter->fechad = new dateonlyField('Desde', 'fechad','d/m/Y');
		$filter->fechah = new dateonlyField('Hasta', 'fechah','d/m/Y');
		$filter->fechad->clause  =$filter->fechah->clause='where';
		$filter->fechad->db_name =$filter->fechah->db_name='fecha';
		$filter->fechah->size=$filter->fechad->size=10;
		$filter->fechad->operator='>=';
		$filter->fechah->operator='<=';

		$filter->numero = new inputField('Factura', 'numero');
		$filter->numero->size=20;

		$filter->proveedor = new inputField('Proveedor', 'proveed');
		$filter->proveedor->append($boton);
		$filter->proveedor->db_name = 'proveed';
		$filter->proveedor->size=20;

		$filter->buttons('reset','search');
		$filter->build();

		$uri = anchor('farmacia/scst/dataedit/show/<#control#>','<#numero#>');
		$uri2 = anchor_popup('formatos/verhtml/COMPRA/<#control#>','Ver HTML',$atts);

		$grid = new DataGrid();
		$grid->order_by('fecha','desc');
		$grid->per_page = 15;

		$grid->column_orderby('Factura',$uri,'control');
		$grid->column_orderby('Fecha'  ,'<dbdate_to_human><#fecha#></dbdate_to_human>','fecha',"align='center'");
		$grid->column_orderby('Vence'  ,'<dbdate_to_human><#vence#></dbdate_to_human>','vence',"align='center'");
		$grid->column_orderby('Nombre' ,'nombre','nombre');
		$grid->column_orderby('IVA'    ,'<nformat><#montoiva#></nformat>' ,'montoiva',"align='right'");
		$grid->column_orderby('Monto'  ,'<nformat><#montonet#></nformat>' ,'montonet',"align='right'");
		$grid->column_orderby('Control','pcontrol' ,'pcontrol',"align='right'");

		//$grid->add('compras/agregar');
		$grid->build();
		//echo $grid->db->last_query();

		$data['content'] = $filter->output.$grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Compras a droguerias');
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
		$this->rapyd->set_connection('farmax');
		$this->rapyd->load_db();

		$this->rapyd->load('dataedit','datadetalle','fields','datagrid');
		$this->rapyd->uri->keep_persistence();

		function exissinv($cen,$id=0){
			if(empty($cen)){
				$id--;
				$rt =form_button('create' ,'Crear','onclick="pcrear('.$id.');" title="Crear como nuevo producto"');
				$rt.=form_button('asignar','Asig.','onclick="pasig('.$id.');" title="Asociar con un producto ya existente"');
			}else{
				$attr = array(
					'width'      => '800',
					'height'     => '600',
					'scrollbars' => 'yes',
					'status'     => 'yes',
					'resizable'  => 'yes',
					'title'      => 'Consultar precio actual',
					'screenx'    => "'+((screen.availWidth/2)-400)+'",
					'screeny'    => "'+((screen.availHeight/2)-300)+'"
				);

				$llink=anchor_popup('inventario/consultas/preciosgeneral/'.raencode(trim($cen)), $cen, $attr);
				$rt=$llink;
			}
			return $rt;
		}

		$edit = new DataEdit('Compras','scst');
		$edit->back_url = 'farmacia/scst/datafilter/';

		$edit->fecha = new DateonlyField('Fecha', 'fecha','d/m/Y');
		$edit->fecha->insertValue = date('Y-m-d');
		$edit->fecha->mode='autohide';
		$edit->fecha->size = 10;

		$edit->numero = new inputField('N&uacute;mero', 'numero');
		$edit->numero->size = 15;
		$edit->numero->rule= 'required';
		$edit->numero->mode= 'autohide';
		$edit->numero->maxlength=8;

		$edit->proveedor = new inputField('Proveedor', 'proveed');
		$edit->proveedor->size = 10;
		$edit->proveedor->maxlength=5;

		$edit->nombre = new inputField('Nombre', 'nombre');
		$edit->nombre->size = 50;
		$edit->nombre->maxlength=40;

		$edit->almacen = new inputField('Almac&eacute;n', 'depo');
		$edit->almacen->size = 15;
		$edit->almacen->maxlength=8;

		$edit->tipo = new dropdownField('Tipo', 'tipo_doc');
		$edit->tipo->option('FC','FC');
		$edit->tipo->rule = 'required';
		$edit->tipo->size = 20;
		$edit->tipo->style='width:150px;';

		$edit->subt  = new inputField('Sub-total', 'montotot');
		$edit->subt->size = 20;
		$edit->subt->css_class='inputnum';

		$edit->iva  = new inputField('Impuesto', 'montoiva');
		$edit->iva->size = 20;
		$edit->iva->css_class='inputnum';

		$edit->total  = new inputField('Total global', 'montonet');
		$edit->total->size = 20;
		$edit->total->css_class='inputnum';

		$edit->pcontrol  = new inputField('Control', 'pcontrol');
		$edit->pcontrol->size = 12;

		$numero =$edit->_dataobject->get('control');
		$proveed=$this->db->escape($edit->_dataobject->get('proveed'));

		$atts = array(
			'width'     => '250',
			'height'    => '340',
			'scrollbars'=> 'no',
			'status'    => 'no',
			'resizable' => 'no',
			'title'     => 'Cambiar PVP',
			'screenx'   => "'+((screen.availWidth/2)-175)+'",
			'screeny'   => "'+((screen.availHeight/2)-175)+'"
		);
		$llink=anchor_popup('farmacia/scst/reasignaprecio/modify/<#id#>', '<b><nformat><#precio1#></nformat></b>', $atts);

		function similar($st1,$st2,$id){
			$st1 =trim(strtoupper($st1));
			$st2 =trim(strtoupper($st2));

			$rt=similar_text($st1,$st2,$por);

			if($por>65){
				$class = 'bien';
			}elseif($por<35){
				$class = 'malo';
			}else{
				$class = 'regu';
			}

			$atts = array(
				'width'     => '550',
				'height'    => '300',
				'scrollbars'=> 'no',
				'status'    => 'no',
				'resizable' => 'no',
				'class'     => $class,
				'title'     => 'Cambiar Asociaci&oacute;n',
				'screenx'   => "'+((screen.availWidth/2)-275)+'",
				'screeny'   => "'+((screen.availHeight/2)-150)+'"
			);

			$llink=anchor_popup('farmacia/scst/asignardataedit/scst/modify/'.$id, nformat($por).'%' , $atts);

			return $llink;
		}

		//Indica si el producto tiene una oferta
		function ofertas($sinv,$margen,$pvp,$ultimo){
			if(empty($sinv)) return '';
			$CI =& get_instance();
			$mSQL='SELECT id,margen FROM sinvpromo WHERE codigo='.$CI->db->escape($sinv);
			$query = $CI->db->query($mSQL);
			$atts = array(
				'width'      => '800',
				'height'     => '600',
				'scrollbars' => 'yes',
				'status'     => 'yes',
				'resizable'  => 'yes',
				'screenx'    => '0',
				'screeny'    => '0',
				'title'      => 'Agregar oferta'
			);

			if ($query->num_rows() > 0){
				$row = $query->row();
				$m   = $row->margen/100;
				$val = nformat($row->margen).'%';
				$link= anchor_popup('inventario/sinvpromo/dataeditexpress/'.raencode($sinv).'/show/'.$row->id,$val, $atts);
			}else{
				$m   = $margen/100;
				$val = nformat($margen).'%';
				$link= anchor_popup('inventario/sinvpromo/dataeditexpress/'.raencode($sinv).'/create/',$val, $atts);
			}
			$precio=$pvp*(1-$m);
			if($precio<=floatval($ultimo)){
				$tprecio='<b style="color:red" title="Precio por debajo del costo" >'.nformat($precio).'</b>';
			}else{
				$tprecio=nformat($precio);
			}

			return $tprecio.' '.$link;
		}

		//Campos para el detalle
		$this->_autoasignar($numero);
		$this->_autoprecios($numero);
		$tabla=$this->db->database;
		$detalle = new DataGrid('');
		$detalle->use_function('similar','ofertas');
		$select=array('a.*','a.codigo AS barras','COALESCE(b.descrip, d.descrip) AS sinvdesc','a.costo AS pond','COALESCE( b.codigo , c.abarras) AS sinv','c.id AS farmaid','e.margen');
		$detalle->db->select($select);
		$detalle->db->from('itscst AS a');
		$detalle->db->where('a.control',$numero);
		$detalle->db->join($tabla.'.sinv AS b','a.codigo=b.codigo','LEFT');
		$detalle->db->join($tabla.'.farmaxasig AS c',"a.codigo=c.barras AND c.proveed=${proveed}",'LEFT');
		$detalle->db->join($tabla.'.sinv AS d','d.codigo=c.abarras','LEFT');
		$detalle->db->join($tabla.'.grup AS e','e.grupo=COALESCE(b.grupo,d.grupo)','LEFT');
		$detalle->db->order_by('a.id');
		$detalle->use_function('exissinv');
		$detalle->column('Barras'             ,'<#codigo#>' );
		$detalle->column('Semejanza% -Descripci&oacute;n' ,'<similar><#descrip#>|<#sinvdesc#>|<#farmaid#></similar> - <#descrip#>');
		$detalle->column('Cantidad'           ,'<nformat><#cantidad#></nformat>','align=\'right\'');
		$detalle->column('PVP'                ,$llink  ,'align=\'right\'');
		$detalle->column('Costo'              ,'<nformat><#ultimo#></nformat>'  ,'align=\'right\'');
		$detalle->column('Importe'            ,'<nformat><#importe#></nformat>' ,'align=\'right\'');
		$detalle->column('C&oacute;digo local','<exissinv><#sinv#>|<#dg_row_id#></exissinv>',"bgcolor='#D7F7D7' align='center'");
		$detalle->column('Desc.'              ,'<ofertas><#sinv#>|<#margen#>|<#precio1#>|<#ultimo#></ofertas>' ,'align=\'right\'');
		$detalle->build();
		//echo $detalle->db->last_query();
		$c_articulos=$detalle->recordCount;

		$query = $this->rapyd->db->query('SELECT SUM(a.cantidad) AS cant FROM itscst AS a WHERE a.control='.$this->db->escape($numero));
		if ($query->num_rows() > 0){
			$row = $query->row_array();
			$unid = $row['cant'];
		}else{
			$unid = 0;
		}
		$edit->unidades = new freeField('Unidades','unidades',nformat($unid));

		$script='
		function pcrear(id){
			var pasar=["barras","descrip","ultimo","iva","codigo","pond","precio1","precio2","precio3","precio4"];
			var url  = "'.site_url('farmacia/sinv/dataedit/create').'";
			form_virtual(pasar,id,url);
		}

		function pasig(id){
			var pasar=["barras","proveed","descrip"];
			var url  = "'.site_url('farmacia/scst/asignardataedit/scst/create').'";
			form_virtual(pasar,id,url);
		}

		function form_virtual(pasar,id,url){
			var data='.json_encode($detalle->data).';
			var w = window.open("'.site_url('farmacia/scst/dummy').'","asignar","width=800,height=600,scrollbars=Yes,status=Yes,resizable=Yes,screenx="+((screen.availWidth/2)-400)+",screeny="+((screen.availHeight/2)-300)+"");

			var fform  = document.createElement("form");
			fform.setAttribute("target", "asignar");
			fform.setAttribute("action", url );
			fform.setAttribute("method", "post");

			for(i=0;i<pasar.length;i++){
				Val=eval("data[id]."+pasar[i]);
				iinput = document.createElement("input");
				iinput.setAttribute("type", "hidden");
				iinput.setAttribute("name", pasar[i]);
				iinput.setAttribute("value", Val);
				fform.appendChild(iinput);
			}

			var cuerpo = document.getElementsByTagName("body")[0];
			cuerpo.appendChild(fform);
			fform.submit();
			w.focus();
			cuerpo.removeChild(fform);
		}';

		$edit->detalle=new freeField('detalle', 'detalle',$detalle->output);
		$accion="javascript:window.location='".site_url('farmacia/scst/cargar'.$edit->pk_URI())."'";
		$pcontrol=$edit->_dataobject->get('pcontrol');
		if($this->_btn_cargar($pcontrol)){
			$edit->button_status('btn_cargar','Cargar',$accion,'TR','show');
		}
		$edit->buttons('save','undo','back');

		$edit->script($script,'show');
		$edit->build();

		$this->rapyd->jquery[]='$("#dialog").dialog({
			autoOpen: false,
			show: "blind",
			hide: "explode"
		});

		$( "#opener" ).click(function() {
			$( "#dialog" ).dialog( "open" );
			return false;
		});';

		$conten['form']  =&  $edit;
		$conten['carti'] = $c_articulos;
		$data['content'] = $this->load->view('view_farmax_compras', $conten,true);
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = '<h1>Compras Descargadas</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function _btn_cargar($pcontrol){
		if(is_null($pcontrol)){
			return true;
		}else{
			$dbpcontrol=$this->db->escape($pcontrol);
			$cana=$this->datasis->dameval('SELECT * FROM scst WHERE control='.$dbpcontrol);
			if($cana==0) return true; else return false;
		}
	}

	function reasignaprecio(){
		$this->rapyd->set_connection('farmax');
		$this->rapyd->load('dataedit');
		$edit = new DataEdit('Cambios de precios','itscst');
		$edit->back_save   = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save   = true;
		$edit->back_cancel_delete = true;
		$edit->back_url = site_url('ajax/reccierraventana');

		$edit->pre_process( 'update','_pre_update');

		$edit->descrip  = new inputField('Descripci&oacute;n', 'descrip');
		$edit->descrip->mode = 'autohide';

		for($i=1;$i<5;$i++){
			$obj='precio'.$i;
			$edit->$obj = new inputField('Precio '.$i, $obj);
			$edit->$obj->css_class='inputnum';
			$edit->$obj->rule ='numeric';
			$edit->$obj->size = 10;
		}

		$edit->pmanual = new autoUpdateField('pmanual','S','S');
		$edit->buttons('modify','save');
		$edit->build();
		//$this->rapyd->jquery[]='$(window).unload(function() { window.opener.location.reload(); });';
		$data['content'] =$edit->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   ='';
		$this->load->view('view_ventanas_sola', $data);
	}

	function _pre_update($do){
		for($i=1;$i<5;$i++){
			$prec='precio'.$i;
			$$prec=round($do->get($prec),2); //optenemos el precio
		}

		if($precio1>=$precio2 && $precio2>=$precio3 && $precio3>=$precio4){
			return true;
		}else{
			$do->error_message_ar['pre_upd'] = 'Los precios deben cumplir con:<br> Precio 1 mayor o igual al Precio 2 mayor o igual al  Precio 3 mayor o igual al Precio 4';
			return false;
		}
	}

	function asignarfiltro(){
		$this->rapyd->load('datagrid','datafilter');
		$this->rapyd->uri->keep_persistence();

		$modbus=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo Proveedor',
				'nombre'=>'Nombre',
				'rif'=>'RIF'),
			'filtro'  =>array('proveed'=>'C&oacute;digo Proveedor','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed'),
			'titulo'  =>'Buscar Proveedor');

		$boton=$this->datasis->modbus($modbus);

		$filter = new DataFilter('Filtro de asignaci&oacute;n de productos');
		$select=array('a.proveed','a.abarras','a.barras','c.nombre','b.descrip','a.id','b.codigo');
		$filter->db->select($select);
		$filter->db->from('farmaxasig AS a');
		$filter->db->join('sinv AS b','a.abarras=b.codigo','LEFT');
		$filter->db->join('sprv AS c','a.proveed=c.proveed');

		$filter->proveedor = new inputField('Proveedor', 'proveed');
		$filter->proveedor->db_name='a.proveed';
		$filter->proveedor->append($boton);
		$filter->proveedor->db_name = 'a.proveed';
		$filter->proveedor->size=20;

		$filter->barras = new inputField('C&oacute;digo seg&uacute;n proveedor', 'barras');
		$filter->barras->db_name='a.barras';
		$filter->barras->append('C&oacute;digo del producto seg&uacute;n el proveedor');

		$filter->abarras = new inputField('C&oacute;digo local', 'abarras');
		$filter->abarras->db_name='a.abarras';

		$filter->buttons('reset','search');
		$filter->build();

		$grid = new DataGrid();
		$grid->order_by('id','desc');
		$grid->per_page = 15;

		$uri=anchor('farmacia/scst/asignardataedit/show/<#id#>','<#barras#>');
		$grid->column_orderby('Proveedor','(<#proveed#>) <#nombre#>' ,'proveed');
		$grid->column_orderby('C&oacute;digo seg&uacute;n proveedor' ,$uri,'barras');
		$grid->column_orderby('Mapeado a','(<#abarras#>) <#descrip#>','abarras');

		$grid->add('farmacia/scst/asignardataedit/create');
		$grid->build();
		//echo $grid->db->last_query();

		$data['content'] = $filter->output.$grid->output;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   ='<h1>Reasignar C&oacute;digo</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function _autoprecios($control){

		$esstd=$this->datasis->traevalor('SCSTSD','Precios en carga de compras por droguerias S P.standard, I margenes de inventario, D sugerido por drogueria, F Respeta los margenes al momento de actualizar');

		if(!empty($control)){
			$dbcontrol=$this->db->escape($control);
			$dbfarmax = $this->load->database('farmax', TRUE);
			$dbfarmax->simple_query("ALTER IGNORE TABLE `itscst` ADD COLUMN `pmanual` CHAR(1) NOT NULL DEFAULT 'N' COMMENT 'Si cambio o no el precio manual'");

			if($esstd=='S'){ //USa el precio standar de inventario
				$query = $dbfarmax->query('SELECT proveed FROM scst WHERE control='.$dbcontrol);
				if ($query->num_rows() > 0){
					$row = $query->row_array();
					$proveed=$row['proveed'];
				}
				$dbproveed=$this->db->escape($proveed);

				$tabla    = $dbfarmax->database;

				$mSQL="SELECT COALESCE( b.codigo , c.abarras) AS sinv, a.cstandard, COALESCE(e.margen,f.margen) AS margen, a.id
				FROM (${tabla}.`itscst`  AS a)
				LEFT JOIN `sinv`       AS b ON `a`.`codigo`=`b`.`codigo`
				LEFT JOIN `farmaxasig` AS c ON `a`.`codigo`=`c`.`barras` AND c.proveed=${dbproveed}
				LEFT JOIN `sinv`       AS d ON `d`.`codigo`=`c`.`abarras`
				LEFT JOIN `grup`       AS e ON b.grupo=e.grupo
				LEFT JOIN `grup`       AS f ON d.grupo=f.grupo
				WHERE `a`.`control` = ${dbcontrol}";

				$query = $this->db->query($mSQL);
				if ($query->num_rows() > 0){
					foreach ($query->result() as $row){
						if(empty($row->sinv) || empty($row->cstandard)) continue;

						$data = array();
						$data['precio1']=round(($row->cstandard*100)/(100-$row->margen),2);
						$data['precio2']=$row->cstandard;
						$data['precio3']=$row->cstandard;
						$data['precio4']=$row->cstandard;
						$where = 'id = '.$row->id;
						$sql = $dbfarmax->update_string('itscst', $data, $where);
						$dbfarmax->simple_query($sql);
						//echo $sql.br();
					}
				}
			}elseif($esstd=='I'){ //usa los margenes de inventario
				$query = $dbfarmax->query('SELECT proveed FROM scst WHERE control='.$dbcontrol);
				if ($query->num_rows() > 0){
					$row = $query->row_array();
					$proveed=$row['proveed'];
				}
				$dbproveed=$this->db->escape($proveed);

				$tabla    = $dbfarmax->database;

				$mSQL="SELECT
				COALESCE(b.margen1,d.margen1) AS margen1,
				COALESCE(b.margen2,d.margen2) AS margen2,
				COALESCE(b.margen3,d.margen3) AS margen3,
				COALESCE(b.margen4,d.margen4) AS margen4,
				a.costo,a.iva,a.id
				FROM (${tabla}.`itscst`  AS a)
				LEFT JOIN `sinv`       AS b ON `a`.`codigo`=`b`.`codigo`
				LEFT JOIN `farmaxasig` AS c ON `a`.`codigo`=`c`.`barras` AND c.proveed=${dbproveed}
				LEFT JOIN `sinv`       AS d ON `d`.`codigo`=`c`.`abarras`
				WHERE `a`.`control` = ${dbcontrol} AND `a`.`pmanual`='N'";

				$query = $this->db->query($mSQL);
				if ($query->num_rows() > 0){
					foreach ($query->result() as $row){
						$data=array();
						$iva = floatval($row->iva);
						for($i=1;$i<5;$i++){
							$obj='margen'.$i;
							if($row->$obj>0 && $row->$obj<100){
								$ind = 'precio'.$i;
								$data[$ind] = round(($row->costo*100/(100-$row->$obj))*(1+($iva/100)),2);
							}
						}
						if(count($data)>0){
							//Arregla el precio4
							if(isset($data['precio3']) && isset($data['precio4']) && $data['precio3']==$data['precio4']){
								$data['precio4']=$data['precio4']/1.01;
							}
							$where = 'id = '.$row->id;
							$sql = $dbfarmax->update_string('itscst', $data, $where);
							$dbfarmax->simple_query($sql);
						}
					}
				}
			}else{
				//usa el sugerido
			}
		}
	}

	function _autoasignar($control=null){
		if(!empty($control)){
			$dbcontrol=$this->db->escape($control);

			//Limpia la tabla farmaxasig
			$mmSQL="DELETE farmaxasig FROM farmaxasig LEFT JOIN sinv ON farmaxasig.abarras=sinv.codigo WHERE sinv.codigo IS NULL";
			$this->db->simple_query($mmSQL);

			$tabla    = $this->db->database;
			$dbfarmax = $this->load->database('farmax', TRUE);

			$query = $dbfarmax->query('SELECT proveed FROM scst WHERE control='.$dbcontrol);
			if($query->num_rows() > 0){
				$row = $query->row_array();
				$proveed=$row['proveed'];
			}
			$dbproveed=$this->db->escape($proveed);

			$mSQL="SELECT `a`.`codigo` AS barras FROM (`itscst` AS a) WHERE `a`.`control` = ${dbcontrol}";
			$query = $dbfarmax->query($mSQL);
			if ($query->num_rows() > 0){
				foreach ($query->result() as $row){
					$qquery=consultas::_gconsul('SELECT codigo  FROM sinv',$row->barras,array('codigo','barras','alterno'),null,'Articulo',false);
					if($qquery!==false){
						$rrow   = $qquery->row_array();
						$codigo = $rrow['codigo'];
						$data = array('proveed' => $proveed, 'abarras' =>trim($rrow['codigo']) , 'barras' => trim($row->barras));

						$str = $this->db->insert_string('farmaxasig', $data);
						$str = str_replace('INSERT','INSERT IGNORE',$str);
						$this->db->simple_query($str);
					}
				}
			}
		}
	}

	function asignardataedit($origen){
		$this->rapyd->uri->keep_persistence();
		$this->rapyd->load('dataedit','datagrid');

		$modbus=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'barras' =>'C&oacute;digo barras',
				'descrip'=>'Descripci&oacute;n'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo' =>'abarras','descrip'=>'sinvdescrip'),
			'titulo'  =>'Buscar Art&iacute;culo');
		$boton=$this->datasis->modbus($modbus);

		$do = new DataObject('farmaxasig');
		$do->pointer('sinv','sinv.codigo=farmaxasig.abarras' , 'descrip AS sinvdescrip', 'left');
		$do->pointer('sprv','sprv.proveed=farmaxasig.proveed', 'nombre', 'left');

		$js='function pasacod(val,desc) { $("#abarras").val(val); $("#sinvdescrip").val(desc); }';
		$edit = new DataEdit('Reasignaciones de c&oacute;digo',$do);
		if($origen='scst'){
			$edit->back_save   = true;
			$edit->back_cancel = true;
			$edit->back_cancel_save   = true;
			$edit->back_cancel_delete = true;
			$edit->back_url = site_url('ajax/reccierraventana');
		}else{
			$edit->back_url = 'farmacia/scst/asignarfiltro';
		}

		$edit->proveedor = new inputField('Proveedor','proveed');
		$edit->proveedor->rule = 'trim|callback_sprvexits|required';
		$edit->proveedor->mode = 'autohide';
		$edit->proveedor->size = 10;
		$edit->proveedor->maxlength=50;

		$edit->nombre = new inputField('Nombre del proveedor','nombre');
		$edit->nombre->pointer=true;
		$edit->nombre->mode = 'autohide';
		$edit->nombre->when=array('show','modify');

		$edit->barras = new inputField('Barras en el proveedor','barras');
		$edit->barras->rule = 'required|trim|callback_fueasignado|callback_noexiste';
		$edit->barras->mode = 'autohide';
		$edit->barras->size = 20;
		$edit->barras->maxlength=250;

		$edit->abarras = new inputField('Producto en sistema','abarras');
		$edit->abarras->rule = 'required|trim|callback_siexiste';
		$edit->abarras->size = 20;
		$edit->abarras->maxlength=250;
		$edit->abarras->append($boton);

		$edit->sinvdescrip = new inputField('Descripcion en el sistema','sinvdescrip');
		$edit->sinvdescrip->pointer=true;
		$edit->sinvdescrip->in='abarras';
		$edit->sinvdescrip->readonly=true;

		$edit->buttons('modify','save','delete','undo','back');

		$describus=$this->input->post('descrip');
		if($describus!==false){
			//print_r($patrones);
			$grid = new DataGrid('Sugerencia de productos similares a <b>'.$describus.'</b>');
			$grid->per_page = 10;
			$grid->db->select(array('codigo','descrip','precio1'));
			$grid->db->from('sinv');
			$grid->paged=false;

			$sstr='';
			$patrones = preg_split("/[\s,\-]+/", $describus);
			foreach($patrones AS $pat){
				if(strlen($pat)>3){
					$sstr.=$pat.' ';
					//$grid->db->like('descrip',$pat);
				}
			}

			$sstr=$this->db->escape($sstr);
			$grid->use_function('str_replace');
			$grid->db->where("MATCH(descrip) AGAINST ($sstr)");
			$grid->db->limit(10);
			$url='<a onclick=\'pasacod("<#codigo#>","<str_replace>"| |<#descrip#></str_replace>")\'  href=\'#\'><#codigo#></a>';

			$grid->column('C&oacute;digo'     ,$url);
			$grid->column('Descripci&oacute;n','descrip');
			$grid->column('PVP'          ,'<nformat><#precio1#></nformat>' ,"align='right'");

			$grid->build();
			//echo $grid->db->last_query();
			$tabla=($grid->recordCount>0)? $grid->output : 'No existe descripci&oacute;n semejante a <b>'.$describus.'</b>';

			$edit->script($js,'create');
			$edit->script($js,'modify');
		}else{
			$tabla='';
		}
		$edit->build();

		$this->rapyd->jquery[]='$(window).unload(function() { window.opener.location.reload(); });';
		$data['content'] = $edit->output.$tabla;
		$data['head']    = $this->rapyd->get_head();
		$data['title']   = heading('Reasignar c&oacute;digo');
		$this->load->view('view_ventanas', $data);
	}

	function sprvexits($proveed){
		$mSQL='SELECT COUNT(*) FROM sprv WHERE proveed='.$this->db->escape($proveed);
		$cana=$this->datasis->dameval($mSQL);
		if($cana==0){
			$error="El proveedor dado no exite";
			$this->validation->set_message('sprvexits',$error);
			return false;
		}
		return true;
	}

	function noexiste($barras){
		$mSQL='SELECT COUNT(*) FROM sinv WHERE codigo='.$this->db->escape($barras);
		$cana=$this->datasis->dameval($mSQL);
		if($cana!=0){
			$error="El c&oacute;digo de barras '$barras' existe en el iventario, la equivalencia se debe aplicar en un producto que no exista";
			$this->validation->set_message('noexiste',$error);
			return false;
		}
		return true;
	}

	function siexiste($barras){
		$mSQL='SELECT COUNT(*) FROM sinv WHERE codigo='.$this->db->escape($barras);
		$cana=$this->datasis->dameval($mSQL);
		if($cana==0){
			$error="El c&oacute;digo de barras '$barras' no existe en el iventario";
			$this->validation->set_message('siexiste',$error);
			return false;
		}
		return true;
	}

	function fueasignado($barras){
		$proveed=$this->db->escape($this->input->post('proveed'));
		$mSQL='SELECT COUNT(*) FROM farmaxasig WHERE barras='.$this->db->escape($barras).' AND proveed='.$proveed;
		$cana=$this->datasis->dameval($mSQL);
		if($cana>0){
			$error="El c&oacute;digo de barras '${barras}' ya fue asignado a otro producto";
			$this->validation->set_message('fueasignado',$error);
			return false;
		}
		return true;
	}


	function cargar($control){
		$this->rapyd->uri->keep_persistence();
		$this->rapyd->load('dataform');

		$dbcontrol= $this->db->escape($control);
		$farmaxDB = $this->load->database('farmax',TRUE);
		$farmaxdb = $farmaxDB->database;
		$localdb  = $this->db->database;

		$msj='';
		$block=trim($this->datasis->traevalor('SCSTACTIVABLOQUEO','Activa la bloque al cargar compras, P:por promedio,M:por valor maximo'));

		if($block=='P' || $block=='M'){
			$mSQL ="SELECT COALESCE(c.abarras,b.codigo) AS codigo,a.descrip, a.cantidad
			FROM ${farmaxdb}.itscst AS a
			LEFT JOIN ${localdb}.sinv AS b ON a.codigo=b.codigo
			LEFT JOIN ${localdb}.farmaxasig AS c ON a.codigo=c.barras AND c.proveed=a.proveed
			WHERE a.control=$dbcontrol";
			$query = $this->db->query($mSQL);
			foreach ($query->result() as $row){
				if(empty($row->codigo)) continue;
				$cana=intval($row->cantidad);
				$fdesde=date('Ymd', mktime(0, 0, 0, date('n')-2, 1, date('Y')));
				$fhasta=date('Ymd', mktime(0, 0, 0, date('n'), 0, date('Y')));

				if($block=='P'){
					$sql   = "SELECT SUM(cana) AS cana FROM sitems WHERE fecha BETWEEN ${fdesde} AND ${fhasta} AND codigoa=".$this->db->escape($row->codigo);
					$venta = $this->datasis->dameval($sql);
					if(empty($venta)) $venta=0; else $venta = ceil($venta/2);

					$fdesde=date('Ymd', mktime(0, 0, 0, date('n'), 1, date('Y')));
					$fhasta=date('Ymd');
					$sql = "SELECT SUM(IF(b.tipo_doc IN ('FC','NE'),1,-1)*a.cantidad) AS cana FROM itscst AS a JOIN scst AS b ON a.control=b.control  WHERE b.recep BETWEEN $fdesde AND $fhasta AND a.codigo=".$this->db->escape($row->codigo);

					$compra = $this->datasis->dameval($sql);
					if(empty($compra)) $compra=0;
					$lim    = $cana+$compra;

					if($lim > $venta) $msj.='&nbsp;&nbsp;-'.$row->codigo.'-'.$row->descrip.', Cantidad: <b>'.nformat($cana).'</b>, Compra en el mes: <b>'.nformat($compra).'</b>, L&iacute;mite: <b>'.nformat($venta).'</b>'.br();
				}else{
					$sql = "SELECT COUNT(*) FROM sinv WHERE existen+$cana > exmax AND codigo=".$this->db->escape($row->codigo);
					$ch = $this->datasis->dameval($sql);
					if($ch>0) $msj.=$row->codigo.'-'.$row->descrip.', Se esta comprando mas del m&aacute;ximo establecido.'.br();
				}
			}
		}

		$form = new DataForm("farmacia/scst/cargar/$control/process");
		$form->title('Carga de compra proveniente de droguer&iacute;a');

		$form->nfiscal = new inputField('Control F&iacute;scal', 'nfiscal');
		$form->nfiscal->rule = 'required|strtoupper';
		$form->nfiscal->autocomplete=false;
		$form->nfiscal->rows = 10;

		$cana=$this->datasis->dameval("SELECT COUNT(*) AS val FROM caub WHERE gasto='N' and invfis='N'");
		$form->almacen = new  dropdownField ('Almac&eacute;n', 'almacen');
		if($cana>1)$form->almacen->option('','Seleccionar');
		$form->almacen->options("SELECT ubica,CONCAT_WS('-',ubica,ubides) AS val FROM caub WHERE gasto='N' and invfis='N' ORDER BY ubides");
		$form->almacen->insertValue=$this->datasis->traevalor('ALMACEN');
		$form->almacen->rule = 'required';

		$proveed=$this->_traesprv($control);
		$dias=$this->datasis->dameval('SELECT b.dcredito FROM sprv AS b WHERE b.proveed='.$this->db->escape($proveed));

		$form->dias = new inputField('D&iacute;as de cr&eacute;dito', 'dias');
		$form->dias->insertValue = ($dias>0)? $dias :  21;
		$form->dias->rule = 'required|integer';
		$form->dias->css_class= 'inputnum';
		$form->dias->size = 5;

		if(strlen($msj)>0){
			$form->free  = new containerField('','<p class="alert">Se ha detectado sobre existencia en los siguientes productos:'.br().$msj.'Es necesaria la clave de aprobaci&oacute;n para continuar</p>');

			$form->clavef = new inputField('Clave de aprobaci&oacute;n', 'clavef');
			$form->clavef->rule= 'callback_chclavef|required';
			$form->clavef->size= 10;
			$form->clavef->autocomplete=false;
			$form->clavef->type='password';
		}

		$action = "javascript:window.location='".site_url('farmacia/scst/dataedit/show/'.$control)."'";
		$form->button('btn_regresa', 'Regresar', $action, 'TR');

		$form->submit('btnsubmit','Guardar');
		$form->build_form();

		if($form->on_success()){
			$nfiscal= $form->nfiscal->newValue;
			$almacen= $form->almacen->newValue;
			$dias   = $form->dias->newValue;

			$data['content'] = $this->_cargar($control,$nfiscal,$almacen,$dias).br().anchor('farmacia/scst/dataedit/show/'.$control,'Regresar');
		}else{
			$data['content'] = $form->output;
		}

		$script= '<script type="text/javascript" >
		$(function() {
		    $(".inputnum").numeric(".");
		});
		</script>';

		$data['script'] = $script;
		$data['head']   = $this->rapyd->get_head().script('jquery.js').script('plugins/jquery.numeric.pack.js');
		$data['title']  = '<h1>Cargar compra '.$control.'</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function chclavef($valor){
		$this->validation->set_message('chclavef', 'Clave de aprobaci&oacute;n inv&aacute;lida');
		$clave=trim($this->datasis->traevalor('SCSTCLAVEEXMAX','Clave de aprobacion para compras por encima del maximo'));
		if(empty($clave)){
			$rif  = str_replace('-','',$this->datasis->traevalor('RIF'));
			$clave= substr($rif,-4);
			$this->datasis->ponevalor('SCSTCLAVEEXMAX',$clave);
		}
		if($clave==$valor) return true;
		return false;
	}

	function _traesprv($control){
		$farmaxDB=$this->load->database('farmax',TRUE);
		$query = $farmaxDB->query("SELECT proveed FROM scst WHERE control=".$this->db->escape($control));
		if ($query->num_rows() > 0){
			$row = $query->row_array();
			return $row['proveed'];
		}
		return '';
	}

	function _cargar($control,$nfiscal,$almacen,$dias){
		$control =$this->db->escape($control);
		$farmaxDB=$this->load->database('farmax',true);
		$farmaxdb=$farmaxDB->database;
		$localdb =$this->db->database;
		$retorna ='';

		$sql ="SELECT COUNT(*) AS cana
		  FROM ${farmaxdb}.itscst AS a
		  LEFT JOIN ${localdb}.sinv AS b ON a.codigo=b.codigo
		  LEFT JOIN ${localdb}.farmaxasig AS c ON a.codigo=c.barras AND c.proveed=a.proveed
		WHERE a.control=$control AND b.codigo IS NULL AND c.abarras IS NULL";

		$query=$this->db->query($sql);
		if($query->num_rows()>0){
			$row=$query->row_array();
			if($row['cana']==0){
				//$query=$farmaxDB->query("SELECT * FROM scst WHERE control=$control AND pcontrol IS NULL");
				$query=$farmaxDB->query("SELECT * FROM scst WHERE control=${control}");

				if ($query->num_rows()==1){

					$row=$query->row_array();
					$pcontrol=$row['pcontrol'];
					if($this->_btn_cargar($pcontrol)){

						$lcontrol=$this->datasis->fprox_numero('nscst');
						$transac =$this->datasis->fprox_numero('ntransa');
						$contribu=$this->datasis->traevalor('CONTRIBUYENTE');
						$rif     =$this->datasis->traevalor('RIF');
						$estampa =date('Ymd');
						$hora    =date('H:i:s');

						$numero=$row['numero'];
						$proveed=$row['proveed'];
						$row['serie']   =$numero;
						$row['numero']  =substr($numero,-8);
						$row['control'] =$lcontrol;
						$row['transac'] =$transac;
						$row['nfiscal'] =$nfiscal;
						$row['credito'] =$row['montonet'];
						$row['anticipo']=0;
						$row['inicial'] =0;
						$row['estampa'] =$estampa;
						$row['hora']    =$hora;
						$row['usuario'] =$this->session->userdata('usuario');
						$row['depo']    =$almacen;
						$cd             =strtotime($row['fecha']);
						$row['vence']   =date('Y-m-d', mktime(0,0,0,date('m',$cd),date('d',$cd)+$dias,date('Y',$cd)));

						$mmsql="SELECT iva,SUM(montoiva) AS monto,SUM(importe) AS base FROM itscst WHERE control=${control} GROUP BY iva";
						$m_iva=$farmaxDB->query($mmsql);
						$ivas=$this->datasis->ivaplica($row['fecha']);
						$tasa=$redutasa=$sobretasa=$exento=$basetasa=$baseredu=$baseadicio=0;

						foreach ($m_iva->result_array() as $ivarow){
							if($ivarow['iva']==$ivas['redutasa']){
								$redutasa  +=$ivarow['monto'];
								$baseredu  +=$ivarow['base'];
							}elseif($ivarow['iva']==$ivas['tasa']){
								$tasa      +=$ivarow['monto'];
								$basetasa  +=$ivarow['base'];
							}elseif($ivarow['iva']==$ivas['sobretasa']){
								$sobretasa +=$ivarow['monto'];
								$baseadicio+=$ivarow['base'];
							}elseif($ivarow['iva']==0){
								$exento    +=$ivarow['base'];
							}
						}
						$row['reducida'] =$redutasa;
						$row['tasa']     =$tasa;
						$row['sobretasa']=$sobretasa;
						$row['monredu']  =$redutasa;
						$row['montasa']  =$tasa;
						$row['monadic']  =$sobretasa;

						$row['ctotal']   =$row['montonet'];
						$row['cstotal']  =$row['montotot'];
						$row['cexento']  =$exento;
						$row['cimpuesto']=$redutasa+$tasa+$sobretasa;
						$row['cgenera']  =$basetasa;
						$row['civagen']  =$tasa;
						$row['cadicio']  =$baseadicio;
						$row['civaadi']  =$sobretasa;
						$row['creduci']  =$baseredu;
						$row['civared']  =$redutasa;

						unset($row['pcontrol']);
						if($contribu=='ESPECIAL' && strtoupper($rif[0])!='V'){
							$por_rete=$this->datasis->dameval('SELECT reteiva FROM sprv WHERE proveed='.$this->db->escape($row['proveed']));
							if($por_rete!=100){
								$por_rete=0.75;
							}else{
								$por_rete=$por_rete/100;
							}
							$row['reteiva']=round($row['montoiva']*$por_rete,2);
						}

						$mSQL[]=$this->db->insert_string('scst', $row);

						$itquery = $farmaxDB->query("SELECT * FROM itscst WHERE control=${control}");
						//echo "SELECT * FROM itscst WHERE control=$control";
						$esstd=$this->datasis->traevalor('SCSTSD');
						foreach ($itquery->result_array() as $itrow){
							$codigo=$this->datasis->dameval('SELECT abarras FROM farmaxasig WHERE barras='.$this->db->escape($itrow['codigo']).' AND proveed='.$this->db->escape($proveed));
							$itrow['codigo']  = $codigo;
							$itrow['control'] = $lcontrol;
							$itrow['usuario'] = $this->session->userdata('usuario');
							$itrow['estampa'] = $estampa;
							$itrow['hora']    = $hora;
							if($esstd=='F') $itrow['rmargen'] = 'S';

							unset($itrow['id']);
							if(isset($itrow['pmanual'])) unset($itrow['pmanual']);
							$mSQL[]=$this->db->insert_string('itscst', $itrow);
						}

						foreach($mSQL AS $sql){
							$rt=$this->db->simple_query($sql);
							if(!$rt){ memowrite($sql,'scstfarma');}
						}
						$sql="UPDATE scst SET pcontrol='${lcontrol}' WHERE control=${control}";
						$rt=$farmaxDB->simple_query($sql);
						if(!$rt) memowrite($sql,'farmaejec');

						/*$mSQL="UPDATE
						  ${localdb}.itscst AS a
						  JOIN ${localdb}.farmaxasig AS b ON a.codigo=b.barras AND a.proveed=b.proveed
						  SET a.codigo=b.abarras
						WHERE a.control='$lcontrol'";
						$rt=$this->db->simple_query($mSQL);
						if(!$rt){ memowrite('farmaejec1',$sql);}*/
						logusu('scst',"Compra ${numero} control ${lcontrol} CARGADA DESDE FarmaSIS");
						$retorna='Compra guardada con el control '.$lcontrol;//.anchor("compras/scst/dataedit/show/${lcontrol}",$lcontrol);
					}else{
						$retorna='Al parecer la factura fue ya pasada';
					}
				}else{
					$retorna='Control no existe';
				}
			}else{
				$retorna='No se puede pasar porque hay productos que no existen en inventario';
			}
		}else{
			$retorna='Error en la consulta';
		}
		return $retorna;
	}

	function dummy(){
		echo "<p aling='center'>Redirigiendo la p&aacute;gina</p>";
	}

	function instalar(){
		$mSQL="CREATE TABLE IF NOT EXISTS `farmaxasig` (
		`id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
		`proveed` VARCHAR(5) NOT NULL,
		`barras` VARCHAR(20) NOT NULL,
		`abarras` VARCHAR(12) NOT NULL,
		PRIMARY KEY (`id`),
		UNIQUE INDEX `proveed` (`proveed`, `barras`)
		)
		COMMENT='Tabla de equivalencias de productos'
		ENGINE=MyISAM
		ROW_FORMAT=DEFAULT";
		var_dump($this->db->simple_query($mSQL));

		$mSQL="ALTER TABLE `farmaxasig`  CHANGE COLUMN `barras` `barras` VARCHAR(20) NOT NULL COLLATE 'latin1_general_ci' AFTER `proveed`";
		var_dump($this->db->simple_query($mSQL));
		$mSQL="ALTER TABLE `farmaxasig`  CHANGE COLUMN `abarras` `abarras` VARCHAR(20) NOT NULL COLLATE 'latin1_general_ci' AFTER `barras`";
		var_dump($this->db->simple_query($mSQL));
		$mSQL="ALTER TABLE `farmaxasig`  CHANGE COLUMN `proveed` `proveed` VARCHAR(5) NOT NULL COLLATE 'utf8_unicode_ci' AFTER `id`";
		var_dump($this->db->simple_query($mSQL));
		$mSQL="ALTER TABLE `farmaxasig`  COLLATE='latin1_general_ci',  CONVERT TO CHARSET latin1";
		var_dump($this->db->simple_query($mSQL));
	}
}
