<?php
class Kardex extends Controller {

	function Kardex(){
		parent::Controller(); 
		$this->load->helper('text');
		$this->load->library("rapyd");
		//$this->rapyd->load_db();
	}

	function index(){
		$this->datasis->modulo_id(317,1);
		redirect("inventario/kardex/filteredgrid");
	}

	function filteredgrid(){
		$this->rapyd->load('datafilter','datagrid2');
		function convierte($par,$link){
			$atts = array(
				'width'     =>'800',
				'height'    =>'600',
				'scrollbars'=>'yes',
				'status'    =>'yes',
				'resizable' =>'yes',
				'screenx'   =>'5',
				'screeny'   =>'5');

		 switch ($par) {
			case '3I': return(anchor_popup($link,'Ventas Caja'       ,$atts)); break;
			case '3R': return(anchor_popup($link,'Ventas Restaurante',$atts)); break;
			case '3M': return(anchor_popup($link,'Ventas Mayor'      ,$atts)); break;
			case '1T': return(anchor_popup($link,'Transferencias'    ,$atts)); break;
			case '2C': return(anchor_popup($link,'Compras'           ,$atts)); break;
			case '4N': return(anchor_popup($link,'Nota/Entrega'      ,$atts)); break;
			case '6C': return(anchor_popup($link,'Conversion'        ,$atts)); break;
			case '5C': return('Ajuste de inventario'); break;
			case '0F': return('Inventario'); break;
			case '9F': return('Inventario'); break;
			default:   return($par); };	
		}

		$modbus=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'Descripci&oacute;n',
				'precio1'=>'Precio 1',
				'precio2'=>'Precio 2',
				'precio3'=>'Precio 3',
				'precio4'=>'Precio 4'),
			'filtro'  =>array('codigo'=>'C&oacute;digo','descrip'=>'Descripci&oacute;n'),
			'retornar'=>array('codigo'=>'codigo'),
			'titulo'  =>'Buscar en inventario');

		$boton=$this->datasis->modbus($modbus);

		$filter = new DataFilter("Kardex de Inventario");
		$filter->codigo = new inputField("C&oacute;digo De Producto", "codigo");
		$filter->codigo->rule = "required";  
		$filter->codigo->append($boton);  

		$filter->ubica = new dropdownField("Almacen", "ubica");  
		$filter->ubica->option("","Todos");
		$filter->ubica->db_name='a.ubica';
		$filter->ubica->options("SELECT ubica,CONCAT(ubica,' ',ubides) descrip FROM caub WHERE gasto='N' ");
		$filter->ubica->operator="=";
		$filter->ubica->clause="where";

		$filter->fechad = new dateonlyField("Desde", "fecha","d/m/Y");
		$filter->fechad->operator=">=";
		$filter->fechad->insertValue = date("Y-m-d",mktime(0, 0, 0, date("m"), date("d")-30,   date("Y")));

		$filter->fechah = new dateonlyField("Hasta", "fechah","d/m/Y");
		$filter->fechah->db_name='fecha';
		$filter->fechah->operator="<=";
		$filter->fechah->insertValue = date("Y-m-d");

		$filter->fechah->clause=$filter->fechad->clause=$filter->codigo->clause="where";
		$filter->fechah->size=$filter->fechad->size=10;
		
		$filter->buttons("reset","search");
		$filter->build();

		$data['content'] =  $filter->output;
		$code=$this->input->post('codigo');
		if($code){

			$mSQL="SELECT CONCAT_WS(' ',TRIM(descrip),TRIM(descrip2)) descrip FROM sinv WHERE codigo='$code'";
			$query = $this->db->query($mSQL);
			$descrip='';
			if ($query->num_rows() > 0){
				$row = $query->row();
				$descrip=$row->descrip;
			}

			$link="/inventario/kardex/grid/<#origen#>/<dbdate_to_human><#fecha#>|Ymd</dbdate_to_human>/<str_replace>/|:slach:|<#codigo#></str_replace>/<#ubica#>";
			$grid = new DataGrid2("($code) $descrip");
			$grid->agrupar('Almacen: ', 'almacen');
			$grid->use_function('convierte','str_replace');
			$grid->db->select(array('IFNULL( b.ubides , a.ubica ) almacen','a.ubica','a.fecha','a.venta','a.cantidad', 'a.saldo', 'a.monto', 'a.salcant', 'a.codigo', 'a.origen', 'a.promedio','a.venta / a.cantidad AS vpromedio'));
			$grid->db->from('costos a');
			$grid->db->join('caub b ','b.ubica=a.ubica','LEFT');
			$grid->db->orderby('almacen, fecha, origen');
			$grid->per_page = 20;
			$grid->column("Fecha"        ,"<dbdate_to_human><#fecha#></dbdate_to_human>");
			$grid->column("Or&iacute;gen","<convierte><#origen#>|$link</convierte>",'align=left' );
			$grid->column("Cantidad"     ,"<nformat><#cantidad#></nformat>",'align=right');
			$grid->column("Acumulado"    ,"<nformat><#salcant#></nformat>" ,'align=right');
			$grid->column("Monto"        ,"<nformat><#monto#></nformat>"   ,'align=right');
			$grid->column("Saldo"        ,"<nformat><#saldo#></nformat>"   ,'align=right');
			$grid->column("Costo Prom."  ,"<nformat><#promedio#></nformat>",'align=right');
			$grid->column("Ventas"       ,"<nformat><#venta#></nformat>"   ,'align=right');
			$grid->column("Precio Prom." ,"<nformat><#vpromedio#></nformat>",'align=right');
			$grid->build();
			$data['content'] .= $grid->output;
			//echo $grid->db->last_query();
		}
		$data['forma'] ='';
		
		$data['title'] = "<h1>Kardex de Inventario</h1>";
		$data['head']  = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);

	}

	function grid(){
		$tipo   =$this->uri->segment(4);
		$fecha  =$this->uri->segment(5);
		$codigo =$this->uri->segment(6);
		$almacen=$this->uri->segment(7);
		if($fecha===FALSE or $codigo===FALSE or $tipo===FALSE or $almacen===FALSE) redirect("inventario/kardex");
		$this->rapyd->load('datagrid');

		$grid = new DataGrid();
		$grid->use_function('number_format');
		$grid->order_by("numero","desc");
		$grid->per_page = 20;

		if($tipo=='3I' or $tipo=='3M'){  //ventas de caja
			$grid->title('Facturas');
			$link=anchor("ventas/factura/dataedit/show/<#tipo_doc#>/<#numa#>","<#numero#>");
			$grid->column("N&uacute;mero",$link);
			$grid->column("Cliente"      ,"cliente" );
			$grid->column("Cantidad"     ,"<nformat><#cana#></format>",'align=right');
			$grid->column("Fecha"        ,"<dbdate_to_human><#fecha#></dbdate_to_human>"   ,'align=center');
			$grid->column("vendedor"     ,"vendedor",'align=center');
			$grid->column("Precio"       ,"<nformat><#preca#>|2|,|.</nformat>",'align=right');
			$grid->column("Total"        ,"<nformat><#tota#>|2|,|.</nformat>" ,'align=right');
			$grid->db->select(array('a.numa','CONCAT(a.tipoa,a.numa) numero','CONCAT("(",b.cod_cli,") ",b.nombre) cliente','a.cana','a.fecha','a.vendedor','a.preca','a.tota','tipo_doc'));
			$grid->db->from('sitems a');
			$grid->db->join('sfac b','b.numero=a.numa  AND b.tipo_doc=a.tipoa');
			$grid->db->where('a.fecha',$fecha);
			$grid->db->where('a.codigoa',$codigo);
			$grid->db->where('a.tipoa !=','X');
			$grid->db->where('b.almacen',$almacen);
		}elseif($tipo=='3R'){ //ventas de Restaurante
			$grid->title('Facturas');
			$link=anchor("hospitalidad/factura/dataedit/show/<#numa#>","<#numero#>");
			$grid->column("N&uacute;mero",'numero');
			$grid->column("Cliente"      ,"cliente" );
			$grid->column("Cantidad"     ,"<nformat><#cantidad#></nformat>",'align=right');
			$grid->column("Fecha"        ,"<dbdate_to_human><#fecha#></dbdate_to_human>"   ,'align=center');
			$grid->column("Mesonero"     ,"mesonero",'align=center');
			$grid->column("Precio"       ,"<nformat><#precio#></nformat>",'align=right');
			$grid->column("Total"        ,"<nformat><#importe#></nformat>" ,'align=right');
			$grid->db->select(array('a.numero','CONCAT("(",b.cod_cli,") ",b.nombre) cliente','c.cantidad','a.fecha', 'a.mesonero','a.precio','a.importe'));
			$grid->db->from('ritems a');
			$grid->db->join('rfac b','b.numero=a.numero');
			$grid->db->join('itrece c','c.menu=a.codigo');
			$grid->db->where('a.fecha' ,$fecha );
			$grid->db->where('c.codigo',$codigo);
		}elseif($tipo=='1T'){ //Transferencias
			$link=anchor("/inventario/stra/dataedit/show/<#numero#>","<#numero#>");
			$grid->title('Tranferencias');
			$grid->column("N&uacute;mero",$link);
			$grid->column("Env&iacute;a"      ,"envia" );
			$grid->column("Recibe"            ,"recibe");
			$grid->column("Cantidad"          ,"<nformat><#cantidad#></nformat>",'align=right');
			$grid->column("Fecha"             ,"<dbdate_to_human><#fecha#></dbdate_to_human>",'align=center');
			$grid->column("Observaci&oacute;n","observ1");
			//$grid->column("Costo"             ,"<nformat><#costo#></nformat>",'align=right');
			$grid->db->select(array('a.numero','b.envia','b.recibe','a.cantidad','b.fecha','b.observ1','a.costo'));
			$grid->db->from('itstra a');
			$grid->db->join('stra b','a.numero=b.numero','LEFT');
			$grid->db->where('b.fecha' ,$fecha );
			$grid->db->where('a.codigo',$codigo);
		}elseif($tipo=='2C'){ //compras
			$link=anchor("compras/scst/dataedit/show/<#control#>","<#numero#>");
			$grid->title('Compras');
			$grid->column("N&uacute;mero",$link);
			$grid->column("Fecha"    ,"<dbdate_to_human><#fecha#></dbdate_to_human>",'align=center');
			$grid->column("Proveedor","proveed" );
			$grid->column("Deposito" ,"depo");
			$grid->column("Cantidad" ,"<nformat><#cantidad#></nformat>",'align=right');
			$grid->column("Costo"    ,"<nformat><#costo#></nformat>",'align=right');
			$grid->column("Importe"  ,"<nformat><#importe#></nformat>",'align=right');
			$grid->db->select('a.numero, a.fecha, a.proveed, a.depo, a.cantidad, a.costo, a.importe,a.control');
			$grid->db->from('itscst a');
			$grid->db->join('scst b','a.control=b.control');
			$grid->db->where('a.codigo',$codigo);
			$grid->db->where('b.recep',$fecha);
			$grid->db->where('b.actuali >= b.fecha');
		}elseif($tipo=='4N'){ //Nota de entrega
			$link=anchor("ventas/snte/dataedit/show/<#numero#>","<#numero#>");
			$grid->title('Notas de Entrega');
			$grid->column("N&uacute;mero",$link);
			$grid->column("Fecha"    ,"<dbdate_to_human><#fecha#></dbdate_to_human>",'align=center');
			$grid->column("Proveedor","Nombre");
			$grid->column("Cantidad" ,"<nformat><#cana#>|2|,|.   </nformat>",'align=right');
			$grid->column("Costo"    ,"<nformat><#precio#>|2|,|. </nformat>",'align=right');
			$grid->column("Importe"  ,"<nformat><#importe#>|2|,|.</nformat>",'align=right');
			$grid->db->select(array('a.numero','a.fecha','a.nombre','b.cana','b.precio','b.importe'));
			$grid->db->from('snte a');
			$grid->db->join('itsnte b','a.numero=b.numero');
			$grid->db->where('b.codigo',$codigo);
			$grid->db->where('a.fecha',$fecha);
		}elseif($tipo=='6C'){ //Conversiones
			$link=anchor("inventario/conversiones/dataedit/show/<#numero#>","<#numero#>");
			$grid->title('Conversiones');
			$grid->column("N&uacute;mero",$link);
			$grid->column("Fecha"    ,"<dbdate_to_human><#estampa#></dbdate_to_human>",'align=center');
			$grid->column("Entrada"  ,"<nformat><#entrada#>|2|,|.</nformat>",'align=right');
			$grid->column("Salida"   ,"<nformat><#salida#>|2|,|. </nformat>",'align=right');
			$grid->db->select(array('a.numero','a.estampa','b.entrada','b.salida','b.codigo'));
			$grid->db->from('conv AS a');
			$grid->db->join('itconv AS b','a.numero=b.numero');
			$grid->db->where('b.codigo' ,$codigo);
			$grid->db->where('a.estampa',$fecha);
		}
		$grid->build();
		//echo $grid->db->last_query();
		
		$data['content'] = $grid->output;
		$data['title']   = "<h1>Transacciones del producto $codigo </h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function factura(){
		$tipo   =$this->uri->segment(4);
		$fecha  =$this->uri->segment(5);
		$codigo =$this->uri->segment(6);
		$almacen=$this->uri->segment(7);

		$data["crud"]   = $grid->output;
		$data["titulo"] = '';
		$content["content"]    = $this->load->view('rapyd/crud', $data, true);   
		$content["rapyd_head"] = $this->rapyd->get_head();
		$this->load->view('view_kardex', $content);
	}
}
?>