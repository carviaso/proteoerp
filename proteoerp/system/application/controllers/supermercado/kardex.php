<?php
class Kardex extends Controller {

	function Kardex(){
		parent::Controller();
		$this->load->helper('text');
		$this->load->library('rapyd');
	}

	function index(){
		$this->datasis->modulo_id(300,1);
		redirect('supermercado/kardex/filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid2');
		$atts = array(
			'width'     =>'800',
			'height'    =>'600',
			'scrollbars'=>'yes',
			'status'    =>'yes',
			'resizable' =>'yes',
			'screenx'   =>'5',
			'screeny'   =>'5'
		);

		function convierte($par,$link){
			$atts = array(
				'width'     =>'800',
				'height'    =>'600',
				'scrollbars'=>'yes',
				'status'    =>'yes',
				'resizable' =>'yes',
				'screenx'   =>'5',
				'screeny'   =>'5'
			);

			switch ($par) {
				case '3I': return(anchor_popup($link,'Ventas Caja'   ,$atts)); break;
				case '3M': return(anchor_popup($link,'Ventas Mayor'  ,$atts)); break;
				case '1T': return(anchor_popup($link,'Transferencias',$atts)); break;
				case '2C': return(anchor_popup($link,'Compras'       ,$atts)); break;
				case '4N': return(anchor_popup($link,'Nota/Entrega'  ,$atts)); break;
				case '6C': return('Conversiones'); break;
				case '5A': return('Ajustes'); break;
				case '0F': return('Inventario'); break;
				case '9F': return('Inventario'); break;
				default:   return($par);
			};
		}

		$modbus=array(
			'tabla'   =>'maes',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n',
				'precio1'=>'Precio 1',
				'precio2'=>'Precio 2',
				'precio3'=>'Precio 3',
				'precio4'=>'Precio 4',
				'precio5'=>'Precio 5'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'codigo'),
			'titulo'  =>'Buscar en inventario');

		$boton=$this->datasis->modbus($modbus);

		$filter = new DataFilter('Kardex de Inventario ('.anchor_popup('/supermercado/lfisico','Resumen de inventarios',$atts).')');
		$filter->codigo = new inputField('C&oacute;digo de Producto', 'codigo');
		$filter->codigo->size = '10';
		$filter->codigo->append($boton);
		$filter->codigo->group = 'UNO';

		$filter->ubica = new dropdownField('Almac&eacute;n', 'ubica');
		$filter->ubica->option('','Todos');
		$filter->ubica->db_name='a.ubica';
		$filter->ubica->options("SELECT ubica,CONCAT(ubica,' ',ubides) descrip FROM caub WHERE gasto='N' ");
		$filter->ubica->operator='=';
		$filter->ubica->clause='where';
		$filter->ubica->group = 'UNO';

		$filter->fechad = new dateonlyField('Desde', 'fecha','d/m/Y');
		$filter->fechad->operator='>=';
		$filter->fechad->insertValue = date('Y-m-d',mktime(0, 0, 0, date('m'), date('d')-30,   date('Y')));
		$filter->fechad->group = 'DOS';

		$filter->fechah = new dateonlyField('Hasta', 'fechah','d/m/Y');
		$filter->fechah->db_name='fecha';
		$filter->fechah->operator='<=';
		$filter->fechah->insertValue = date('Y-m-d');
		$filter->fechah->group = 'DOS';

		$filter->fechah->clause=$filter->fechad->clause=$filter->codigo->clause='where';
		$filter->fechah->size=$filter->fechad->size=10;

		$filter->buttons('reset','search');
		$filter->build('dataformfiltro');

		$data['content'] =  $filter->output;
		if(isset($_POST['codigo'])){
			$code   = $_POST['codigo'];
			$dbcode = $this->db->escape($code);
			$mSQL   = "SELECT descrip FROM maes WHERE codigo=${dbcode}";
			$query  = $this->db->query($mSQL);
			$descrip= '';
			if($query->num_rows() > 0){
				$row = $query->row();
				$descrip=trim($row->descrip);
				//$activo =trim($row->activo);

				$actual = $this->datasis->dameval("SELECT SUM(cantidad) AS cana FROM ubic WHERE codigo=${dbcode}");
				$fracci = $this->datasis->dameval("SELECT SUM(fraccion) AS cana FROM ubic WHERE codigo=${dbcode}");

			}else{
				//$activo ='';
			}

			$link="/supermercado/kardex/grid/<#origen#>/<dbdate_to_human><#fecha#>|Ymd</dbdate_to_human>/<str_replace>/|:slach:|<#codigo#></str_replace>/<#ubica#>";
			$grid = new DataGrid2("($code) $descrip");
			$grid->agrupar('Almac&eacute;n: ', 'almacen');
			$grid->use_function('convierte','number_format','str_replace');
			$grid->db->select("IFNULL( b.ubides , a.ubica ) almacen,a.ubica ,a.fecha, a.venta, a.cantidad, a.saldo, a.monto, a.salcant, a.codigo, a.origen, a.promedio");
			$grid->db->from('costos a');
			$grid->db->join('caub b ','b.ubica=a.ubica','LEFT');
			$grid->db->orderby('almacen, fecha, origen');
			$grid->per_page = 20;
			$grid->column('Fecha'        ,'<dbdate_to_human><#fecha#></dbdate_to_human>');
			$grid->column('Or&iacute;gen',"<convierte><#origen#>|$link</convierte>",'align=left');
			$grid->column('Cantidad'     ,'<nformat><#cantidad#></nformat>','align=right');
			$grid->column('Acumulado'    ,'<nformat><#salcant#></nformat>' ,'align=right');
			$grid->column('Monto'        ,'<nformat><#monto#></nformat>'   ,'align=right');
			$grid->column('Saldo'        ,'<nformat><#saldo#></nformat>'   ,'align=right');
			$grid->column('Costo Prom.'  ,'<nformat><#promedio#></nformat>','align=right');
			$grid->column('Ventas'       ,'<nformat><#venta#></nformat>'   ,'align=right');
			$grid->build();

			//echo $grid->db->last_query();
		}

		$ffinal  = $this->datasis->dameval("SELECT MAX(fecha) AS fecha FROM costos WHERE codigo=${dbcode}");
		if(!empty($ffinal)){
			$dbfinal = $this->db->escape($ffinal);
			$mayor   = $this->datasis->dameval("SELECT SUM(cantidad) AS cana FROM itfmay WHERE codigo=${dbcode} AND fecha>${dbfinal}");
			$ventas  = $this->datasis->dameval("SELECT SUM(cantidad) AS cana FROM positfact WHERE codigo=${dbcode} AND fecha>${dbfinal}");
			$compras = $this->datasis->dameval("SELECT SUM(IF(b.tipo_doc='ND',-1,1)*a.cantidad) AS cana FROM itscst AS a JOIN scst AS b ON a.control=b.control WHERE a.codigo=${dbcode} AND b.actuali>=b.fecha AND b.actuali>${dbfinal}");
			$nentreg = $this->datasis->dameval("SELECT SUM(a.cana) AS cana FROM itsnte AS a JOIN snte AS b ON a.numero=b.numero WHERE a.codigo=${dbcode} AND b.fecha>${dbfinal}");
			$ajustes = $this->datasis->dameval("SELECT SUM(IF(b.tipo='E',1,-1)*cantidad) AS cana FROM itssal AS a JOIN ssal AS b ON a.numero = b.numero WHERE a.codigo = ${dbcode} AND b.fecha>${dbfinal}");
			$conver  = $this->datasis->dameval("SELECT SUM(a.entrada-a.salida) AS cana FROM itconv AS a JOIN conv AS b ON a.numero = b.numero WHERE a.codigo = ${dbcode} AND b.fecha>${dbfinal}");


			$ventas  = (empty($ventas ))? htmlnformat(0) : htmlnformat($ventas );
			$compras = (empty($compras))? htmlnformat(0) : htmlnformat($compras);
			$nentreg = (empty($nentreg))? htmlnformat(0) : htmlnformat($nentreg);
			$actual  = (empty($actual ))? htmlnformat(0) : htmlnformat($actual );
			$ajustes = (empty($ajustes))? htmlnformat(0) : htmlnformat($ajustes);
			$conver  = (empty($conver ))? htmlnformat(0) : htmlnformat($conver );
			$consi   = (empty($consi  ))? htmlnformat(0) : htmlnformat($consi  );
			$mayor   = (empty($mayor  ))? htmlnformat(0) : htmlnformat($mayor  );

			//if($activo=='S'){
			//	$sactivo='<span style=\'font-size:0.7em;color:green;\'>ACTIVO</span>';
			//}else{
			//	$sactivo='<span style=\'font-size:0.7em;color:red;\'>INACTIVO</span>';
			//}

			$optadd = '<div style="">';
			$optadd.= ' <table>';
			$optadd.= '  <tr><td colspan=\'5\'><b style="text-size:1.4em;font-weight:bold;">Movimientos posteriores a la fecha '.dbdate_to_human($ffinal).' para todos los almacenes</b></td></tr>';
			$optadd.= "  <tr><td>Ventas   </td><td style='text-align:right' ><b> ${ventas}</b></td><td style='padding-left:50px;'>Ajustes      </td><td style='text-align:right' ><b>${ajustes}</b></td><td rowspan='3' style='text-align:center'>Existencia actual <p style='font-size:2em;padding:0 0 0 0;margin: 0 0 0 0;font-weight:bold;'>${actual} / ${fracci}</p></td></tr>";
			$optadd.= "  <tr><td>Compras  </td><td style='text-align:right' ><b>${compras}</b></td><td style='padding-left:50px;'>Conversiones </td><td style='text-align:right' ><b> ${conver}</b></td></tr>";
			$optadd.= "  <tr><td>N.Entrega</td><td style='text-align:right' ><b>${nentreg}</b></td><td style='padding-left:50px;'>Mayor        </td><td style='text-align:right' ><b>  ${mayor}</b></td></tr>";
			$optadd.= ' </table>';
			$optadd.= '</div>';
		}else{
			$optadd='';
		}

		$data['content'] .= $grid->output.$optadd;
		$data['title']   = heading('Kardex de Inventario');
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function grid(){
		$tipo   =$this->uri->segment(4);
		$fecha  =$this->uri->segment(5);
		$codigo =$this->uri->segment(6);
		$almacen=$this->uri->segment(7);
		if($fecha===false || $codigo===false || $tipo===false || $almacen===false) redirect('supermercado/kardex');
		$this->rapyd->load('datagrid','fields');

		$grid = new DataGrid();
		$grid->per_page = 20;

		if($tipo=='3I' or $tipo=='3M'){ //ventas de caja
			$grid->title('Facturas');
			$link=anchor('ventas/factura/dataedit/show/<#tipo_doc#>/<#numa#>','<#numero#>');
			$grid->column('N&uacute;mero','numa');
			$grid->column('Cliente'      ,'cliente' );
			$grid->column('Cantidad'     ,'<nformat><#cantidad#></nformat>','align=right');
			$grid->column('Fecha'        ,'<dbdate_to_human><#fecha#></dbdate_to_human>','align=\'center\'');
			$grid->column('Cajero'       ,'cajero','align=center');
			$grid->column('Precio'       ,'<nformat><#monto#></nformat>','align=right');
			$grid->column('Total'        ,'<nformat><#tota#></nformat>' ,'align=right');
			$grid->db->select(array('a.numero AS numa','CONCAT( "(", b.cliente ,") ", b.nombres ) cliente','a.cantidad','a.fecha', 'a.cajero', 'a.monto','monto * cantidad tota', 'MID( a.numero ,1,2) AS tipo_doc'));
			$grid->db->from('vieite a');
			$grid->db->join('viefac b','b.numero=a.numero  AND b.caja=a.caja AND b.cajero=a.cajero AND a.fecha=b.fecha');
			//$grid->db->where("a.fecha=$fecha AND a.codigo='$codigo' AND a.almacen='$almacen'");
			$grid->db->where('a.fecha'  ,$fecha);
			$grid->db->where('a.codigo' ,$codigo);
			$grid->db->where('a.almacen',$almacen);
			$grid->order_by('a.numero','desc');
		}elseif($tipo=='1T'){ //Transferencias
			//$link=anchor("/supermercado/transferencia/dataedit/show/<#numero#>","<#numero#>");
			$link='numero';
			$grid->title('Tranferencias');
			$grid->column('N&uacute;mero',$link);
			$grid->column('Env&iacute;a'      ,'envia' );
			$grid->column('Recibe'            ,'recibe');
			$grid->column('Cantidad'          ,'<nformat><#cantidad#></nformat>','align=right');
			$grid->column('Fracci&oacute;n'   ,'<nformat><#totcant#></nformat>','align=right');
			$grid->column('Fecha'             ,'<dbdate_to_human><#fecha#></dbdate_to_human>','align=\'center\'');
			$grid->column('Observaci&oacute;n','observ1');
			$grid->column('Costo'             ,'<nformat><#costo#></nformat>','align=right');
			$grid->db->select(array('a.numero','b.envia' , 'b.recibe', 'a.cantidad', 'b.fecha', 'b.observ1', 'a.costo','a.totcant'));
			$grid->db->from('ittran a');
			$grid->db->join('tran b','a.numero=b.numero','LEFT');
			//$grid->db->where("b.fecha=$fecha AND a.codigo='$codigo' ");
			$grid->db->where('b.fecha' ,$fecha);
			$grid->db->where('a.codigo',$codigo);
			$grid->order_by('numero','desc');
		}elseif($tipo=='2C'){ //compras
			$link=anchor("compras/scst/dataedit/show/<#control#>","<#numero#>");
			$grid->title('Compras');
			$grid->column('N&uacute;mero',$link);
			$grid->column('Fecha'    ,'<dbdate_to_human><#fecha#></dbdate_to_human>','align=\'center\'');
			$grid->column('Proveedor','proveed' );
			$grid->column('Deposito' ,'depo');
			$grid->column('Cantidad' ,'<nformat><#cantidad#></nformat>','align=\'right\'');
			$grid->column('Costo'    ,'<nformat><#costo#></nformat>'   ,'align=\'right\'');
			$grid->column('Importe'  ,'<nformat><#importe#></nformat>' ,'align=\'right\'');
			$grid->db->select(array('a.numero', 'a.fecha', 'a.proveed' , 'a.depo','a.cantidad', 'a.costo', 'a.importe','a.control'));
			$grid->db->from('itscst a');
			$grid->db->join('scst b','a.control=b.control');
			//$grid->db->where("a.codigo='$codigo' AND b.recep=$fecha AND b.actuali>=b.fecha");
			$grid->db->where('a.codigo',$codigo);
			$grid->db->where('b.recep',$fecha);
			$grid->db->where('b.actuali >= b.fecha');
			$grid->order_by('numero','desc');
		}elseif($tipo=='4N'){ //Nota de entrega
			$link=anchor("ventas/notaentrega/dataedit/show/<#numero#>","<#numero#>");
			$grid->title('Notas de Entrega');
			$grid->column('N&uacute;mero',$link);
			$grid->column('Fecha'    ,"<dbdate_to_human><#fecha#></dbdate_to_human>",'align=center');
			$grid->column('Proveedor','Nombre');
			$grid->column('Cantidad' ,'<nformat><#cana#></nformat>'   ,'align=\'right\'');
			$grid->column('Costo'    ,'<nformat><#precio#></nformat>' ,'align=\'right\'');
			$grid->column('Importe'  ,'<nformat><#importe#></nformat>','align=\'right\'');
			$grid->db->select(array('a.numero', 'a.fecha', 'a.nombre', 'b.cana', 'b.precio', 'b.importe'));
			$grid->db->from('snte a');
			$grid->db->join('itsnte b','a.numero=b.numero');
			//$grid->db->where("b.codigo='$codigo' AND a.fecha=$fecha ");
			$grid->db->where('a.fecha' ,$fecha);
			$grid->db->where('b.codigo',$codigo);
			$grid->order_by('numero','desc');
		}

		$grid->build();
		//echo $grid->db->last_query();

		$iframe = new iframeField('showefect', 'supermercado/kardex/showefect' ,'400');
		$iframe->status='show';
		$iframe->build();

		$data['content'] = $grid->output.$iframe->output;
		$data['title']   = heading('Transacciones del producto '.$codigo);
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);

	}

	function factura(){
		$tipo   =$this->uri->segment(4);
		$fecha  =$this->uri->segment(5);
		$codigo =$this->uri->segment(6);
		$almacen=$this->uri->segment(7);

		$data['crud']   = $grid->output;
		$data['titulo'] = '';
		$content['content']    = $this->load->view('rapyd/crud', $data, true);
		$content['rapyd_head'] = $this->rapyd->get_head();
		$this->load->view('view_kardex', $content);
	}

	function showefect(){
		echo '';
	}
}
