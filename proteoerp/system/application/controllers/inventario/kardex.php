<?php
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
				case '6C': return(anchor_popup($link,'Conversion'          ,$atts)); break;
				case '5C': return(anchor_popup($link,'Ajuste de inventario',$atts)); break;
				case '5D': return(anchor_popup($link,'Consignacion'        ,$atts)); break;
				case '0F': return(anchor_popup($link,'Inventario'          ,$atts)); break;
				case '9F': return(anchor_popup($link,'Inventario'          ,$atts)); break;
				//case '0F': return('Inventario'); break;
				//case '9F': return('Inventario'); break;
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

		$filter = new DataFilter('Kardex de Inventario');
		$filter->codigo = new inputField('C&oacute;digo ', 'codigo');
		$filter->codigo->db_name ='a.codigo';
		$filter->codigo->rule = 'required';
		$filter->codigo->operator='=';
		$filter->codigo->size    = 10;
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

		$data['filtro'] =  $filter->output;

		$code=$this->input->post('codigo');

		if($code){
			$mSQL="SELECT CONCAT_WS(' ',TRIM(descrip),TRIM(descrip2)) descrip FROM sinv WHERE codigo='$code'";
			$query = $this->db->query($mSQL);
			$descrip='';
			if ($query->num_rows() > 0){
				$row = $query->row();
				$descrip=$row->descrip;
			}

			$link="/inventario/kardex/grid/<#origen#>/<dbdate_to_human><#fecha#>|Ymd</dbdate_to_human>/<raencode><#codigo#></raencode>/<raencode><#ubica#></raencode>";
			$grid = new DataGrid2("Producto: ($code) $descrip");
			$grid->agrupar(' ', 'almacen');
			$grid->use_function('convierte','str_replace');
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

			$grid->column('Or&iacute;gen','<convierte><#origen#>|'.$link.'</convierte>','align=\'left\'' );
			$grid->column('Fecha'        ,'<dbdate_to_human><#fecha#></dbdate_to_human>');
			$grid->column('Cantidad'     ,'<nformat><#cantidad#></nformat>'  ,'align=\'right\'');
			$grid->column('Acumulado'    ,'<b><nformat><#salcant#></nformat></b>'   ,'align=\'right\'');
			$grid->column('Monto'        ,'<nformat><#monto#></nformat>'     ,'align=\'right\'');
			$grid->column('Saldo'        ,'<nformat><#saldo#></nformat>'     ,'align=\'right\'');
			$grid->column('Costo Prom.'  ,'<nformat><#promedio#></nformat>'  ,'align=\'right\'');
			$grid->column('Ventas'       ,'<nformat><#venta#></nformat>'     ,'align=\'right\'');
			$grid->column('Precio Prom.' ,'<nformat><#vpromedio#></nformat>' ,'align=\'right\'');
			$grid->column('Margen %'     ,'<nformat><#vmargen#></nformat>'   ,'align=\'right\'');
			$grid->column('Margen Bs.'   ,'<nformat><#vutil#></nformat>'     ,'align=\'right\'');

			$grid->build();
			$data['content'] = $grid->output;
			//echo $grid->db->last_query();
		}
		$data['forma'] ='';

		$data['script']  = script('jquery.js');

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

		$grid = new DataGrid();
		$grid->order_by('numero','desc');
		$grid->per_page = 50;

		//img(array('src' =>'images/pdf_logo.gif','height' => 18, 'alt' => 'Imprimir', 'title' => 'Imprimir', 'border'=>'0'))
		if($tipo=='3I' || $tipo=='3M'){  //ventas de caja
			$fields = $this->db->field_data('sfac');
			$ppk=array();
			$select=array('a.numa','a.tipoa','a.numa','CONCAT("(",b.cod_cli,") ",b.nombre) cliente','a.cana','a.fecha','a.vendedor','a.preca','a.tota','b.tipo_doc');
			foreach ($fields as $field){
				if($field->primary_key==1){
					$ppk[]='<#'.$field->name.'#>';
					$pknombre='b.'.$field->name;
					if(array_search($pknombre, $select)===false){
						$select[]=$pknombre;
					}
				}
			}

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
			$grid->db->from('sitems a');
			$grid->db->join('sfac b','b.numero=a.numa  AND b.tipo_doc=a.tipoa');
			$grid->db->where('a.fecha',$fecha);
			$grid->db->where('a.codigoa',$codigo);
			$grid->db->where('a.tipoa !=','X');
			$grid->db->where('b.almacen',$almacen);
			$grid->build();
			$gridout=$grid->output;
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
			$select=array('a.numero','a.fecha','a.nombre','b.cana','b.precio','b.importe');
			foreach ($fields as $field){
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
			$grid->title('Notas de Entrega');
			$grid->column('N&uacute;mero',$link);
			$grid->column('Fecha'    ,'<dbdate_to_human><#fecha#></dbdate_to_human>','align=center');
			$grid->column('Cliente','Nombre');
			$grid->column('Cantidad' ,'<nformat><#cana#></nformat>'   ,'align=\'right\'');
			$grid->column('Costo'    ,'<nformat><#precio#></nformat>' ,'align=\'right\'');
			$grid->column('Importe'  ,'<nformat><#importe#></nformat>','align=\'right\'');
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
			$grid->column('Salida'   ,'<nformat><#salida#> </nformat>','align=right');
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
