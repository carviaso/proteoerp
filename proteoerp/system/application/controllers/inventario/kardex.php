<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Kardex extends Controller {

	function Kardex(){
		parent::Controller();
		$this->load->helper('text');
		$this->load->library('rapyd');
		//$this->rapyd->load_db();
	}

	function index(){
		$this->datasis->modulo_id(317,1);
		redirect('inventario/kardex/filteredgrid');
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
				case '3I': return(anchor_popup($link,'Ventas Caja'         ,$atts)); break;
				case '3R': return(anchor_popup($link,'Ventas Restaurante'  ,$atts)); break;
				case '3M': return(anchor_popup($link,'Ventas Mayor'        ,$atts)); break;
				case '1T': return(anchor_popup($link,'Transferencias'      ,$atts)); break;
				case '2C': return(anchor_popup($link,'Compras'             ,$atts)); break;
				case '4N': return(anchor_popup($link,'Nota/Entrega'        ,$atts)); break;
				case '6C': return(anchor_popup($link,'Conversión'          ,$atts)); break;
				case '5C': return(anchor_popup($link,'Ajuste'              ,$atts)); break;
				case '5D': return(anchor_popup($link,'Consignación'        ,$atts)); break;
				case '0F': return(anchor_popup($link,'Inventario'          ,$atts)); break;
				case '9F': return(anchor_popup($link,'Inventario'          ,$atts)); break;
			default:   return($par); };
		}

		function colorgal($par){
			switch ($par) {
				case '3I': return '(  0,163,  0, 0.4)'; break;
				case '3R': return '(  0,163,  0, 0.4)'; break;
				case '3M': return '(  0,163,  0, 0.4)'; break;
				case '1T': return '(255, 34,  5, 0.4)'; break;
				case '2C': return '(183, 84, 35, 0.4)'; break;
				case '4N': return '( 17, 19,148, 0.4)'; break;
				case '6C': return '(  0,  0,  0, 0.4)'; break;
				case '5C': return '( 65,185,255, 0.4)'; break;
				case '5D': return '(144,  0,255, 0.4)'; break;
				case '0F': return '(255,221,  0, 0.4)'; break;
				case '9F': return '(255,221,  0, 0.4)'; break;
			default:       return '(255,255,255, 0.4)'; };
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

		$maxfecha = $this->datasis->dameval("SELECT MAX(fecha) AS fecha FROM costos");
		if(!empty($maxfecha)){
			$corte = ' corte: <b>'.dbdate_to_human($maxfecha).'</b>';
		}else{
			$corte = '';
		}


		$filter = new DataFilter('Kardex de Inventario '.$corte);
		//$script= '$(function(){ $("#kardextabla").columnHover(); });';
		//$filter->script($script);
		$filter->codigo = new inputField('C&oacute;digo ', 'codigo');
		$filter->codigo->db_name ='a.codigo';
		$filter->codigo->rule = 'required';
		$filter->codigo->operator='=';
		$filter->codigo->size    = 25;
		$filter->codigo->clause  ='where';
		$filter->codigo->append($boton);
		$filter->codigo->group = 'UNO';

		$filter->ubica = new dropdownField('Almac&eacute;n', 'ubica');
		$filter->ubica->option('','Todos');
		$filter->ubica->db_name='a.ubica';
		$filter->ubica->options("SELECT ubica,CONCAT(ubica,' ',ubides) descrip FROM caub WHERE gasto='N'");
		$filter->ubica->operator='=';
		$filter->ubica->clause  ='where';
		$filter->ubica->group   = 'UNO';

		$filter->origen = new dropdownField('Or&iacute;gen','origen');
		$filter->origen->option('' ,'Todos');
		$filter->origen->option('3I','Ventas Caja'         );
		$filter->origen->option('3R','Ventas Restaurante'  );
		$filter->origen->option('3M','Ventas Mayor'        );
		$filter->origen->option('1T','Transferencias'      );
		$filter->origen->option('2C','Compras'             );
		$filter->origen->option('4N','Nota/Entrega'        );
		$filter->origen->option('6C','Conversión'          );
		$filter->origen->option('5C','Ajuste de inventario');
		$filter->origen->option('5D','Consignación'        );
		$filter->origen->option('0F','Inv. Físico comienzo del día');
		$filter->origen->option('9F','Inv. Físico final del día');
		//$filter->origen->style = 'width:120px';
		$filter->origen->group   = 'UNO';

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
		$filter->fechah->rule=$filter->fechad->rule='required|chfecha';

		$filter->buttons('reset','search');
		$filter->build('dataformfiltro');

		$data['filtro'] =  $filter->output;

		$code=$this->input->post('codigo');
		$data['content'] = '';
		$cana=0;
		if($code  && $filter->is_valid()){
			$dbcode = $this->db->escape($code);
			$mSQL="SELECT CONCAT_WS(' ',TRIM(descrip),TRIM(descrip2)) descrip,existen,activo FROM sinv WHERE codigo=${dbcode}";
			$query = $this->db->query($mSQL);

			if($query->num_rows() > 0){
				$row = $query->row();
				$descrip=$row->descrip;
				$activo =$row->activo;
				$existen=floatval($row->existen);

				$actual  = $this->datasis->dameval("SELECT SUM(existen) AS cana FROM itsinv WHERE codigo=${dbcode}");
				if(floatval($actual) != $existen){
					$this->db->simple_query("UPDATE sinv SET existen=${actual} WHERE codigo=${dbcode}");
				}
			}else{
				$activo ='N';
				$descrip='No encontrado.';
				$existen=0;
			}

			$link="/inventario/kardex/grid/<#origen#>/<dbdate_to_human><#fecha#>|Ymd</dbdate_to_human>/<raencode><#codigo#></raencode>/<raencode><#ubica#></raencode>";
			$grid = new DataGrid2("Producto: (${code}) ${descrip}");
			$grid->table_id = 'kardextabla';
			$grid->agrupar(' ', 'almacen');
			$grid->use_function('convierte','str_replace','colorgal');
			$grid->db->select(array(
						'IFNULL( b.ubides , a.ubica ) almacen',
						'a.ubica','a.fecha',
						'a.venta',
						'a.cantidad',
						'a.saldo',
						'a.monto',
						'a.salcant',
						'TRIM(a.codigo) AS codigo',
						'a.origen',
						'a.promedio',
						'(a.venta/a.cantidad)*(a.cantidad>0) AS vpromedio',
						'ROUND(100-(a.promedio*100/(a.venta/a.cantidad)),2)*(a.origen="3I") AS vmargen',
						'((a.venta/a.cantidad)-a.promedio)*a.cantidad*(a.origen="3I") AS vutil',
						'c.activo',
						'c.grupo'));

			$grid->db->from('costos a');
			$grid->db->join('caub b','b.ubica=a.ubica'  ,'LEFT');
			$grid->db->join('sinv c','a.codigo=c.codigo','LEFT');
			$grid->db->orderby('almacen, fecha, origen');
			$grid->per_page = 60;

			$grid->column('Or&iacute;gen','<p style="background-color: rgba<colorgal><#origen#></colorgal>;font-size:1.3em;font-weight: bold;margin:0px;padding:0px;border:0px;"><convierte><#origen#>|'.$link.'</convierte></p>','align=\'left\'' );
			$grid->column('Fecha'        ,'<dbdate_to_human><#fecha#></dbdate_to_human>');
			$grid->column('Cantidad'     ,'<nformat><#cantidad#></nformat>'  ,'align=\'right\'');
			$grid->column('<b style="color:#FFFFFF">Acumulado</b>','<b style="font-size:1.3em"><nformat><#salcant#></nformat></b>'   ,'align=\'right\'');
			$grid->column('Monto'        ,'<nformat><#monto#></nformat>'     ,'align=\'right\'');
			$grid->column('Saldo'        ,'<nformat><#saldo#></nformat>'     ,'align=\'right\'');
			$grid->column('Costo Prom.'  ,'<nformat><#promedio#></nformat>'  ,'align=\'right\'');
			$grid->column('Ventas'       ,'<nformat><#venta#></nformat>'     ,'align=\'right\'');
			$grid->column('Precio Prom.' ,'<nformat><#vpromedio#></nformat>' ,'align=\'right\'');
			$grid->column('Margen %'     ,'<nformat><#vmargen#></nformat>'   ,'align=\'right\'');
			$grid->column('Margen Bs.'   ,'<nformat><#vutil#></nformat>'     ,'align=\'right\'');

			$grid->build();
			$data['content']  = '<h4 style="text-align:center;padding:0px;margin:0px;">Movimiento comprendido desde la fecha <b style="color:#000000; font-size:1.2em">'.$filter->fechad->value.'</b> Hasta <b style="color:#000000; font-size:1.2em">'.$filter->fechah->value.'</b></h4>';
			$data['content'] .= $grid->output;

			$ffinal  = $this->datasis->dameval("SELECT MAX(fecha) AS fecha FROM costos WHERE codigo=${dbcode}");
			if(!empty($ffinal)){
				$dbfinal = $this->db->escape($ffinal);
				$ventas  = $this->datasis->dameval("SELECT SUM(IF(tipoa='D',-1,1)*cana) AS cana FROM sitems WHERE codigoa=${dbcode} AND fecha>${dbfinal} AND MID(numa,1,1)<>'_' AND tipoa IN ('F','D')");
				$compras = $this->datasis->dameval("SELECT SUM(IF(b.tipo_doc='ND',-1,1)*a.cantidad) AS cana FROM itscst AS a JOIN scst AS b ON a.control=b.control WHERE a.codigo=${dbcode} AND b.actuali>=b.fecha AND b.recep>${dbfinal}");
				$nentreg = $this->datasis->dameval("SELECT SUM(a.cana) AS cana FROM itsnte AS a JOIN snte AS b ON a.numero=b.numero WHERE a.codigo=${dbcode} AND estampa>${dbfinal}");
				$ajustes = $this->datasis->dameval("SELECT SUM(IF(b.tipo='E',1,-1)*cantidad) AS cana FROM itssal AS a JOIN ssal AS b ON a.numero = b.numero WHERE a.codigo = ${dbcode} AND b.fecha>${dbfinal}");
				$conver  = $this->datasis->dameval("SELECT SUM(a.entrada-a.salida) AS cana FROM itconv AS a JOIN conv AS b ON a.numero = b.numero WHERE a.codigo = ${dbcode} AND b.fecha>${dbfinal}");
				if($this->db->table_exists('itscon')){
					$consi   = $this->datasis->dameval("SELECT SUM(IF(tipod='E',-1,1)*a.cana) AS cana FROM itscon AS a JOIN scon AS b ON a.id_scon=b.id WHERE a.codigo = ${dbcode} AND b.fecha>${dbfinal}");
				}else{
					$consi   = 0;
				}
				$ventas  = (empty($ventas ))? htmlnformat(0) : htmlnformat($ventas );
				$compras = (empty($compras))? htmlnformat(0) : htmlnformat($compras);
				$nentreg = (empty($nentreg))? htmlnformat(0) : htmlnformat($nentreg);
				$actual  = (empty($actual ))? htmlnformat(0) : htmlnformat($actual );
				$ajustes = (empty($ajustes))? htmlnformat(0) : htmlnformat($ajustes);
				$conver  = (empty($conver ))? htmlnformat(0) : htmlnformat($conver );
				$consi   = (empty($consi  ))? htmlnformat(0) : htmlnformat($consi  );

				if($activo=='S'){
					$sactivo='<span style=\'font-size:0.7em;color:green;\'>ACTIVO</span>';
				}else{
					$sactivo='<span style=\'font-size:0.7em;color:red;\'>INACTIVO</span>';
				}

				$optadd = '<div style="">';
				$optadd.= ' <table>';
				$optadd.= '  <tr><td colspan=\'5\'><b style="text-size:1.4em;font-weight:bold;">Movimientos posteriores a la fecha '.dbdate_to_human($ffinal).' para todos los almacenes</b></td></tr>';
				$optadd.= "  <tr><td>Ventas   </td><td style='text-align:right' ><b> ${ventas}</b></td><td style='padding-left:50px;'>Ajustes       </td><td style='text-align:right' ><b>${ajustes}</b></td><td rowspan='3' style='text-align:center'>Existencia actual <p style='font-size:2em;padding:0 0 0 0;margin: 0 0 0 0;font-weight:bold;'>${actual}</p>${sactivo}</td></tr>";
				$optadd.= "  <tr><td>Compras  </td><td style='text-align:right' ><b>${compras}</b></td><td style='padding-left:50px;'>Conversiones  </td><td style='text-align:right' ><b> ${conver}</b></td></tr>";
				$optadd.= "  <tr><td>N.Entrega</td><td style='text-align:right' ><b>${nentreg}</b></td><td style='padding-left:50px;'>Consignaciones</td><td style='text-align:right' ><b>  ${consi}</b></td></tr>";
				$optadd.= ' </table>';
				$optadd.= '</div>';
			}else{
				$optadd='';
			}
			$data['content'] .= $optadd;
			//echo $grid->db->last_query();
			$cana=$grid->recordCount;
		}

		if($cana<=0){
			$data['content'] .=  '<script type="text/javascript"> $(function(){ $("#cajafiltro").show(); }); </script>';
		}

		$data['forma'] = '';
		$data['script'] = script('jquery.js').'<style type="text/css">#kardextabla tr:hover { background-color: #ffff99; }</style>';
		//$data['script'].= script('plugins/jquery.columnhover.pack.js');
		$data['title'] = heading('Kardex de Inventario');
		$data['head']  = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);

	}


	function kardexpres() {
		$id = 0;
		$mcodigo = '';
		$mfdesde = 0;
		$mfhasta = 0;
		$id = $this->uri->segment(4);
		if ($id > 0){
			$mcodigo = $this->datasis->dameval("SELECT codigo FROM sinv WHERE id=$id");
			$mfdesde = $this->datasis->dameval("SELECT ADDDATE(MAX(fecha),-30) FROM costos WHERE codigo='".addslashes($mcodigo)."'");
			$mfhasta  = $this->datasis->dameval("SELECT MAX(fecha) FROM costos WHERE codigo='".addslashes($mcodigo)."'");
		}
	}

	function grid(){
		$tipo   =$this->uri->segment(4);
		$fecha  =$this->uri->segment(5);
		$codigo =radecode($this->uri->segment(6));
		$almacen=radecode($this->uri->segment(7));
		if($fecha===FALSE or $codigo===FALSE or $tipo===FALSE or $almacen===FALSE) redirect('inventario/kardex');
		$this->rapyd->load('datagrid','fields');
		$gridout='';

		$attsp = array(
			'width'      => '200',
			'height'     => '200',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
		);

		function bfacts($factura,$codigo){
			//return '';
			$factura=trim($factura);
			if(empty($factura)){
				return 'No encontrado';
			}
			$CI =& get_instance();
			$dbcodigo  = $CI->db->escape($codigo);
			$dbfactura = $CI->db->escape($factura);
			$mSQL="SELECT GROUP_CONCAT( DISTINCT CONCAT(a.id,':',numero)) AS fact
				FROM sfac AS a
				JOIN sitems AS b ON a.numero=b.numa AND a.tipo_doc=b.tipoa
				WHERE b.codigoa=${dbcodigo} AND ${dbfactura} IN (a.numero,a.maestra) AND a.tipo_doc='F'";
			$facts=$CI->datasis->dameval($mSQL);
			$rt ='';
			$lls=array();
			$arr=explode(',',$facts);
			foreach($arr as $fact){
				$parr  = explode(':',$fact);
				$lls[] = anchor('formatos/verhtml/FACTURA/'.$parr[0], $parr[1],array('target'=>'showefect'));
			}
			return implode(', ',$lls);
		}

		$grid = new DataGrid();
		$grid->order_by('numero','desc');
		$grid->per_page = 50;

		//img(array('src' =>'images/pdf_logo.gif','height' => 18, 'alt' => 'Imprimir', 'title' => 'Imprimir', 'border'=>'0'))
		if($tipo=='3I' || $tipo=='3M'){  //ventas de caja
			$fields = $this->db->field_data('sfac');
			$ppk=array();
			$select=array('a.numa','a.tipoa','a.numa','CONCAT("(",b.cod_cli,") ",b.nombre) cliente','a.cana*IF(a.tipoa="D",-1,1) AS cana','a.fecha','a.vendedor','a.preca','a.tota','b.tipo_doc');
			foreach ($fields as $field){
				if($field->primary_key==1){
					$ppk[]='<#'.$field->name.'#>';
					$pknombre='b.'.$field->name;
					if(array_search($pknombre, $select)===false){
						$select[]=$pknombre;
					}
				}
			}

			$gridout='';

			$ll=anchor_popup('formatos/descargar/FACTURA/'.implode('/',$ppk), '(pdf)', $attsp);
			$link=anchor('formatos/verhtml/FACTURA/'.implode('/',$ppk),'<#tipoa#><#numa#> '.$ll,array('target'=>'showefect'));
			$grid->title('Facturas');
			$grid->column('N&uacute;mero',$link);
			$grid->column('Cliente'      ,'cliente' );
			$grid->column('Cantidad'     ,'<nformat><#cana#></nformat>','align=right');
			$grid->column('Fecha'        ,'<dbdate_to_human><#fecha#></dbdate_to_human>'   ,'align=center');
			$grid->column('Vendedor'     ,'vendedor','align=center');
			$grid->column('Precio'       ,'<nformat><#preca#></nformat>','align=\'right\'');
			$grid->column('Total'        ,'<nformat><#tota#></nformat>' ,'align=\'right\'');
			$grid->db->select($select);
			$grid->db->from('sitems AS a');
			$grid->db->join('sfac   AS b','b.numero=a.numa AND b.tipo_doc=a.tipoa');
			$grid->db->where('a.fecha',$fecha);
			$grid->db->where('a.codigoa',$codigo);
			$grid->db->where('a.tipoa !=','X');
			$grid->db->not_like('a.numa','_','after');
			$grid->db->where('b.almacen',$almacen);
			$grid->build();
			if($grid->recordCount > 0){
				$gridout.=$grid->output;
			}

			$fields = $this->db->field_data('snte');
			$ppk=array();
			$select=array('a.numero','a.fecha','a.nombre','b.cana','b.precio','b.importe','a.factura');
			foreach($fields as $field){
				if($field->primary_key==1){
					$ppk[]='<#'.$field->name.'#>';
					$pknombre='a.'.$field->name;
					if(array_search($pknombre, $select)===false){
						$select[]=$pknombre;
					}
				}
			}
			$grid2 = new DataGrid();
			$grid2->use_function('bfacts');
			$ll=anchor_popup('formatos/descargar/SNTE/'.implode('/',$ppk), '(pdf)', $attsp);
			$link=anchor('formatos/verhtml/SNTE/'.implode('/',$ppk),'<#numero#> '.$ll,array('target'=>'showefect'));

			$grid2->title('Notas de Entrega Facturadas');
			$grid2->column('N&uacute;mero',$link);
			$grid2->column('Fecha'    ,'<dbdate_to_human><#fecha#></dbdate_to_human>','align=center');
			$grid2->column('Cliente'  ,'nombre');
			$grid2->column('Cantidad' ,'<nformat><#cana#></nformat>'   ,'align=\'right\'');
			$grid2->column('Costo'    ,'<nformat><#precio#></nformat>' ,'align=\'right\'');
			$grid2->column('Importe'  ,'<nformat><#importe#></nformat>','align=\'right\'');
			//$grid2->column('Factura'  ,'<#factura#>');
			$grid2->column('Fact.(s)' ,"<bfacts><#factura#>|${codigo}</bfacts>");
			$grid2->db->select($select);
			$grid2->db->from('snte   AS a');
			$grid2->db->join('itsnte AS b','a.numero=b.numero');
			$grid2->db->join('sfac   AS c','a.factura=c.numero AND c.tipo_doc=\'F\'');
			$grid2->db->where('b.codigo',$codigo);
			$grid2->db->where('a.fecha' ,$fecha);
			$grid2->build();
			if($grid2->recordCount > 0){
				$gridout.=$grid2->output;
			}


		}elseif($tipo=='3R'){ //ventas de Restaurante
			$grid->title('Facturas');
			//$link=anchor('inventario/kardex/rfac/'.$this->_unionuri().'/show/'.implode('/',$ppk),'<#tipoa#><#numa#>');
			$grid->column('N&uacute;mero','numero');
			$grid->column('Cliente'      ,'cliente' );
			$grid->column('Cantidad'     ,'<nformat><#cantidad#></nformat>','align=right');
			$grid->column('Fecha'        ,'<dbdate_to_human><#fecha#></dbdate_to_human>'   ,'align=center');
			$grid->column('Mesonero'     ,'mesonero','align=center');
			$grid->column('Precio'       ,'<nformat><#precio#></nformat>'  ,'align=right');
			$grid->column('Total'        ,'<nformat><#importe#></nformat>' ,'align=right');
			$grid->db->select(array('a.numero','CONCAT("(",b.cod_cli,") ",b.nombre) cliente','c.cantidad','a.fecha', 'a.mesonero','a.precio','a.importe'));
			$grid->db->from('ritems a');
			$grid->db->join('rfac b','b.numero=a.numero');
			$grid->db->join('itrece c','c.menu=a.codigo');
			$grid->db->where('a.fecha' ,$fecha );
			$grid->db->where('c.codigo',$codigo);
			$grid->build();
			$gridout=$grid->output;
		}elseif($tipo=='1T' || $tipo=='0F' || $tipo=='9F'){ //Transferencias
			$fields = $this->db->field_data('stra');
			$ppk=array();
			$select=array('b.numero','b.envia','b.recibe','a.cantidad','b.fecha','b.observ1','a.costo');
			foreach ($fields as $field){
				if($field->primary_key==1){
					$ppk[]='<#'.$field->name.'#>';
					$pknombre='b.'.$field->name;
					if(array_search($pknombre, $select)===false){
						$select[]=$pknombre;
					}
				}
			}

			$ll=anchor_popup('formatos/descargar/STRA/'.implode('/',$ppk), '(pdf)', $attsp);
			$link=anchor('formatos/verhtml/STRA/'.implode('/',$ppk),'<#numero#> '.$ll,array('target'=>'showefect'));
			$grid->title('Tranferencias');
			$grid->column('N&uacute;mero',$link);
			$grid->column('Env&iacute;a'      ,'envia' );
			$grid->column('Recibe'            ,'recibe');
			$grid->column('Cantidad'          ,'<nformat><#cantidad#></nformat>','align=\'right\'');
			$grid->column('Fecha'             ,'<dbdate_to_human><#fecha#></dbdate_to_human>','align=\'center\'');
			$grid->column('Observaci&oacute;n','observ1');
			$grid->db->select($select);
			$grid->db->from('itstra a');
			$grid->db->join('stra b','a.numero=b.numero');
			$grid->db->where('b.fecha' ,$fecha );
			$grid->db->where('a.codigo',$codigo);
			$grid->build();
			if($grid->recordCount>0){
				$gridout = $grid->output;
			}

			$grid2 = new DataGrid();
			//$grid2->order_by('numero','desc');
			$grid2->per_page = 50;
			$grid2->title('Tranferencias por consumo detallado');
			$grid2->column('C&oacute;digo'     ,'codigo');
			$grid2->column('Consumido'         ,'cantidad','align=\'right\'');
			$grid2->column('Enlace'            ,'enlace');
			$grid2->column('Entrada'           ,'fraccion','align=\'right\'');
			$grid2->db->select(array('a.codigo','b.descrip','a.cantidad','a.fraccion','a.enlace','c.descrip'));
			$grid2->db->from('trafrac AS a');
			$grid2->db->join('sinv AS b','a.codigo=b.codigo');
			$grid2->db->join('sinv AS c','a.enlace=c.codigo');
			$grid2->db->where('a.fecha' ,$fecha );
			$grid2->db->where($this->db->escape($codigo).' IN (`a`.`codigo`,`a`.`enlace`)',null,false);
			$grid2->build();
			if($grid2->recordCount>0){
				$gridout .= $grid2->output;
			}

		}elseif($tipo=='2C'){ //compras
			$fields = $this->db->field_data('scst');
			$ppk=array();
			foreach ($fields as $field){
				if($field->primary_key==1){
					$ppk[]='<#'.$field->name.'#>';
					$pknombre='b.'.$field->name;
					$select=array('a.numero', 'a.fecha','a.proveed', 'a.depo', 'a.cantidad', 'a.costo', 'a.importe','a.control');
					if(array_search($pknombre, $select)===false){
						$select[]=$pknombre;
					}
				}
			}

			$ll=anchor_popup('formatos/descargar/COMPRA/'.implode('/',$ppk), '(pdf)', $attsp);
			$link=anchor('formatos/verhtml/COMPRA/'.implode('/',$ppk),'<#numero#> '.$ll,array('target'=>'showefect'));
			$grid->title('Compras');
			$grid->column('N&uacute;mero',$link);
			$grid->column('Fecha'    ,'<dbdate_to_human><#fecha#></dbdate_to_human>','align=\'center\'');
			$grid->column('Proveedor','proveed' );
			$grid->column('Deposito' ,'depo'    );
			$grid->column('Cantidad' ,'<nformat><#cantidad#></nformat>','align=\'right\'');
			$grid->column('Costo'    ,'<nformat><#costo#></nformat>'   ,'align=\'right\'');
			$grid->column('Importe'  ,'<nformat><#importe#></nformat>' ,'align=\'right\'');
			$grid->db->select($select);
			$grid->db->from('itscst a');
			$grid->db->join('scst b','a.control=b.control');
			$grid->db->where('a.codigo',$codigo);
			$grid->db->where('b.recep',$fecha);
			$grid->db->where('b.actuali >= b.fecha');
			$grid->build();
			$gridout=$grid->output;
		}elseif($tipo=='4N'){ //Nota de entrega
			$fields = $this->db->field_data('snte');
			$ppk=array();
			$select=array('a.numero','a.fecha','a.nombre','b.cana','b.precio','b.importe','a.factura');
			foreach($fields as $field){
				if($field->primary_key==1){
					$ppk[]='<#'.$field->name.'#>';
					$pknombre='a.'.$field->name;
					if(array_search($pknombre, $select)===false){
						$select[]=$pknombre;
					}
				}
			}

			$ll=anchor_popup('formatos/descargar/SNTE/'.implode('/',$ppk), '(pdf)', $attsp);
			$link=anchor('formatos/verhtml/SNTE/'.implode('/',$ppk),'<#numero#> '.$ll,array('target'=>'showefect'));
			$grid->use_function('bfacts');
			$grid->title('Notas de Entrega');
			$grid->column('N&uacute;mero',$link);
			$grid->column('Fecha'    ,'<dbdate_to_human><#fecha#></dbdate_to_human>','align=center');
			$grid->column('Cliente'  ,'nombre');
			$grid->column('Cantidad' ,'<nformat><#cana#></nformat>'   ,'align=\'right\'');
			$grid->column('Costo'    ,'<nformat><#precio#></nformat>' ,'align=\'right\'');
			$grid->column('Importe'  ,'<nformat><#importe#></nformat>','align=\'right\'');
			$grid->column('Fact.(s)' ,"<bfacts><#factura#>|${codigo}</bfacts>");
			$grid->db->select($select);
			$grid->db->from('snte a');
			$grid->db->join('itsnte b','a.numero=b.numero');
			$grid->db->where('b.codigo',$codigo);
			$grid->db->where('a.fecha' ,$fecha);
			$grid->build();
			$gridout=$grid->output;
		}elseif($tipo=='6C'){ //Conversiones
			$fields = $this->db->field_data('conv');
			$ppk=array();
			$select=array('a.numero','a.estampa','b.entrada','b.salida','b.codigo');
			foreach ($fields as $field){
				if($field->primary_key==1){
					$ppk[]='<#'.$field->name.'#>';
					$pknombre='a.'.$field->name;
					if(array_search($pknombre, $select)===false){
						$select[]=$pknombre;
					}
				}
			}
			$ll=anchor_popup('formatos/descargar/CONV/'.implode('/',$ppk), '(pdf)', $attsp);
			$link=anchor('formatos/verhtml/CONV/'.implode('/',$ppk),'<#numero#> '.$ll,array('target'=>'showefect'));
			$grid->title('Conversiones');
			$grid->column('N&uacute;mero',$link);
			$grid->column('Fecha'    ,'<dbdate_to_human><#estampa#></dbdate_to_human>','align=center');
			$grid->column('Entrada'  ,'<nformat><#entrada#></nformat>','align=right');
			$grid->column('Salida'   ,'<nformat><#salida#></nformat>','align=right');
			$grid->db->select($select);
			$grid->db->from('conv AS a');
			$grid->db->join('itconv AS b','a.numero=b.numero');
			$grid->db->where('b.codigo' ,$codigo);
			$grid->db->where('a.estampa',$fecha);
			$grid->build();
			$gridout=$grid->output;
		}elseif($tipo=='5C'){ //Ajustes de inventario
			$fields = $this->db->field_data('ssal');
			$ppk=array();
			$select=array('a.numero','a.fecha','a.almacen','a.motivo','b.descrip','b.cantidad','b.costo');
			foreach ($fields as $field){
				if($field->primary_key==1){
					$ppk[]='<#'.$field->name.'#>';
					$pknombre='a.'.$field->name;
					if(array_search($pknombre, $select)===false){
						$select[]=$pknombre;
					}
				}
			}

			$ll=anchor_popup('formatos/descargar/SSAL/'.implode('/',$ppk), '(pdf)', $attsp);
			$link=anchor('formatos/verhtml/SSAL/'.implode('/',$ppk),'<#numero#> '.$ll,array('target'=>'showefect'));
			$grid->title('Ajustes de inventario');
			$grid->column('N&uacute;mero',$link);
			$grid->column('Descripci&oacute;n','descrip');
			$grid->column('Fecha'    ,'<dbdate_to_human><#fecha#></dbdate_to_human>','align=\'center\'');
			$grid->column('Cantidad' ,'<nformat><#cantidad#></nformat>','align=\'right\'');
			$grid->column('Costo'    ,'<nformat><#costo#></nformat>'   ,'align=\'right\'');
			$grid->db->select($select);
			$grid->db->from('ssal AS a');
			$grid->db->join('itssal AS b','a.numero=b.numero');
			$grid->db->where('b.codigo' ,$codigo);
			$grid->db->where('a.fecha',$fecha);
			$grid->build();
			$gridout=$grid->output;
		}elseif($tipo=='5D'){ //Consignacion
			$fields = $this->db->field_data('scon');
			$ppk=array();
			$select=array('a.numero','a.fecha','b.desca','b.cana','b.precio');
			foreach ($fields as $field){
				if($field->primary_key==1){
					$ppk[]='<#'.$field->name.'#>';
					$pknombre='a.'.$field->name;
					if(array_search($pknombre, $select)===false){
						$select[]=$pknombre;
					}
				}
			}

			$ll=anchor_popup('formatos/descargar/SCON/'.implode('/',$ppk), '(pdf)', $attsp);

			$link=anchor('formatos/verhtml/SCON/'.implode('/',$ppk),'<#numero#> '.$ll,array('target'=>'showefect'));
			$grid->title('Consignaci&oacute;n de inventario');
			$grid->column('N&uacute;mero',$link);
			$grid->column('Descripci&oacute;n','desca');
			$grid->column('Fecha'    ,'<dbdate_to_human><#fecha#></dbdate_to_human>','align=\'center\'');
			$grid->column('Cantidad' ,'<nformat><#cana#></nformat>','align=\'right\'');
			$grid->column('Precio'   ,'<nformat><#precio#></nformat>'   ,'align=\'right\'');
			$grid->db->select($select);
			$grid->db->from('scon AS a');
			$grid->db->join('itscon AS b','a.numero=b.numero');
			$grid->db->where('b.codigo' ,$codigo);
			$grid->db->where('a.fecha',$fecha);
			$grid->build();
			$gridout=$grid->output;
		}
		//echo $grid->db->last_query();

		$iframe = new iframeField('showefect', 'inventario/kardex/showefect' ,"400");
		$iframe->status='show';
		$iframe->build();

		$data['content'] = $gridout.$iframe->output;
		$data['title']   = heading('Transacciones del producto '.$codigo);
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function showefect(){
		echo '';
	}

	/*function stra($tipo,$fecha,$codigo,$almacen){
		$this->back_dataedit='inventario/kardex/grid/'.raencode($tipo).'/'.raencode($fecha).'/'.raencode($codigo).'/'.raencode($almacen);
		stra::dataedit();
	}

	function sfac($tipo,$fecha,$codigo,$almacen){
		$this->back_dataedit='inventario/kardex/grid/'.raencode($tipo).'/'.raencode($fecha).'/'.raencode($codigo).'/'.raencode($almacen);
		sfac::dataedit();
	}

	function snte($tipo,$fecha,$codigo,$almacen){
		$this->back_dataedit='inventario/kardex/grid/'.raencode($tipo).'/'.raencode($fecha).'/'.raencode($codigo).'/'.raencode($almacen);
		snte::dataedit();
	}

	function conv($tipo,$fecha,$codigo,$almacen){
		$this->back_dataedit='inventario/kardex/grid/'.raencode($tipo).'/'.raencode($fecha).'/'.raencode($codigo).'/'.raencode($almacen);
		conv::dataedit();
	}

	function scst($tipo,$fecha,$codigo,$almacen){
		$this->back_dataedit='inventario/kardex/grid/'.raencode($tipo).'/'.raencode($fecha).'/'.raencode($codigo).'/'.raencode($almacen);
		scst::dataedit();
	}

	function ssal($tipo,$fecha,$codigo,$almacen){
		$this->back_dataedit='inventario/kardex/grid/'.raencode($tipo).'/'.raencode($fecha).'/'.raencode($codigo).'/'.raencode($almacen);
		ssal::dataedit();
	}

	function scon($tipo,$fecha,$codigo,$almacen){
		$this->back_dataedit='inventario/kardex/grid/'.raencode($tipo).'/'.raencode($fecha).'/'.raencode($codigo).'/'.raencode($almacen);
		scon::dataedit();
	}*/

	function _unionuri(){
		$tipo   =$this->uri->segment(4);
		$fecha  =$this->uri->segment(5);
		$codigo =$this->uri->segment(6);
		$almacen=$this->uri->segment(7);
		return raencode($tipo).'/'.raencode($fecha).'/'.raencode($codigo).'/'.raencode($almacen);
	}
}

/*
require_once(APPPATH.'/controllers/inventario/stra.php');
require_once(APPPATH.'/controllers/inventario/conv.php');
require_once(APPPATH.'/controllers/inventario/ssal.php');
require_once(APPPATH.'/controllers/inventario/scon.php');
require_once(APPPATH.'/controllers/ventas/sfac.php');
require_once(APPPATH.'/controllers/ventas/snte.php');
require_once(APPPATH.'/controllers/compras/scst.php');
*/
