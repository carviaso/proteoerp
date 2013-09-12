<?php
class Poscuadre extends Controller {

	function Poscuadre(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('114',1);
	}

	function index(){
		$this->rapyd->load('datagrid2');
		$this->rapyd->load('datafilter');

		$diai =$this->uri->segment(4);
		$mesi =$this->uri->segment(5);
		$anoi =$this->uri->segment(6);

		if($diai===FALSE or $mesi===FALSE or $anoi===FALSE){
			$fechai = date('Y/m/d');
			$qfechai= date('Ymd');
		}else{
			$fechai ="$anoi/$mesi/$diai";
			$qfechai=$anoi.$mesi.$diai;
		}
 		$filter = new DataForm('supermercado/poscuadre/index');
 		$filter->title('Filtro de cajas');
		$filter->fechai = new dateField("Fecha","fechai","d/m/Y");
		$filter->fechai->insertValue=$fechai;
		$filter->fechai->size=10;
		$filter->button("btnsubmit", "Buscar", form2uri(site_url('supermercado/poscuadre/index'),'fechai'), $position="BL");
		$filter->build_form();

		$grid = new DataGrid2();
		$select=array('a.caja caja',"IFNULL(b.nombre,'N/A') nombre",'a.cajero AS cajero','SUM(a.gtotal) AS monto');

		$cupon = floatval($this->datasis->traevalor('FMAYCUPON'));
		if($cupon>0) {
			$select[] = "SUM(TRUNCATE(a.gtotal/${cupon},0)) AS cupones";
		}else{
			$select[] = '(0) AS  cupones';
		}
		$grid->db->select($select);
		$grid->db->from('posfact AS a');
		$grid->db->where('SUBSTRING(numero,1,1)!=','X');
		$grid->db->where('fecha',$qfechai);
		$grid->db->join('scaj b','a.cajero=b.cajero','LEFT');
		$grid->db->groupby('a.caja,a.cajero');

		$link=anchor("supermercado/poscuadre/concaja/<#caja#>/<#cajero#>/$qfechai",'<#caja#>');
		$grid->column('Caja'   , $link);
		$grid->column('Nombre' , 'nombre' );
		$grid->column('Cajero' , 'cajero' ,'align="center"');
		$grid->column('Cupones', 'cupones','align="center"');
		$grid->column('Monto'  , '<nformat><#monto#></nformat>'  ,'align="right"');
		$grid->totalizar('monto');
		$grid->build();
		//echo $grid->db->last_query();

		$consul = new DataForm('supermercado/buscafac/index/search/osp');
 		$consul->title('Buscar Factura');
		$consul->fechad = new dateField("Desde","fechad","d/m/Y");
		$consul->fechah = new dateField("Hasta","fechah","d/m/Y");
		$consul->nombre = new inputField("Nombre", "nombre");
		$consul->cedula = new inputField("C&eacute;dula/RIF", "cedula");
		$consul->fechad->insertValue = $consul->fechah->insertValue=date("Y/m/d");
		$consul->fechah->size=$consul->fechad->size=10;
		$consul->submit("btn_submit","Buscar");
		$consul->build_form();

		$data['content'] = $filter->output.$grid->output.$consul->output;
		$data['title']   = '<h1>Consulta de Cajas</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function concaja() {
		$this->rapyd->load("datagrid");
		$caja   = $this->uri->segment(4);
		$cajero = $this->uri->segment(5);
		$fecha  = $this->uri->segment(6);
		$menvia="$caja/$cajero/$fecha";

		$data['content']  = "<table class='bordetabla' width='40%' align='center'>\n<tr>\n";
		$data['content'] .= "<td><A href='".base_url()."supermercado/poscuadre/detfact/$menvia'>Facturas</a></td>\n";
		$data['content'] .= "<td><A href='".base_url()."supermercado/poscuadre/detsfpa/$menvia'>Pagos</a></td>\n";
		$data['content'] .= "<td><A href='".base_url()."supermercado/poscuadre/detitfact/$menvia'>Articulo</a></td></tr>\n</table>\n";

    $q1="SELECT COUNT(*) FROM positfact WHERE cantidad<0 AND fecha=$fecha AND cajero='$cajero' AND caja='$caja'";
    $q2="SELECT abs(sum(monto)) FROM positfact WHERE cantidad<0 AND fecha=$fecha AND cajero='$cajero' AND caja='$caja'";

		$grid = new DataGrid('Resumen de caja');

		$select=array("FORMAT(sum((gtotal-impuesto)*(SUBSTRING(numero,1,1)<>'X')),2) base",
		                   "FORMAT(sum(impuesto*(SUBSTRING(numero,1,1)<>'X')),2) impuesto",
		                   "FORMAT(sum(gtotal*(SUBSTRING(numero,1,1)<>'X')),2) total", "($q1) dv1", "FORMAT(($q2),2) dv2",
		                   "sum(gtotal*(SUBSTRING(numero,1,1)<>'X' AND gtotal<0) ) devol",
		                   "sum(1*(SUBSTRING(numero,1,1)='X')) nulos",
		                   "FORMAT(sum(gtotal*(SUBSTRING(numero,1,1)='X')),2) nulas",
		                   "count(*) trans",
		                   "sum((SUBSTRING(numero,1,1)<>'X' AND gtotal<0)) nose",
		                   "max(SUBSTRING(numero,2,7)) final",
		                   "min(SUBSTRING(numero,2,7)) inicial");
		$grid->db->select($select);
		$grid->db->from("posfact");
		$grid->db->where("fecha",$fecha);
		$grid->db->where("cajero",$cajero);
		$grid->db->where("caja",$caja);
		$grid->db->groupby('caja');
		$grid->column("Sub Total","base"     ,'align="right"');
		$grid->column("Impuesto" ,"impuesto" ,'align="right"');
		$grid->column("Total"    ,"total"    ,'align="right"');
		$grid->column("Devuelto" ,"<#dv1#> x <#dv2#>" ,'align="right"');
		$grid->column("Nulo"     ,"<#nulos#> x <#nulas#>"    ,'align="right"');
		$grid->column("Transferencia"   ,"trans"    ,'align="center"');
		$grid->build();
		$arreglo=$grid->recordSet[0];
		$data['content'] .= $grid->output.'<b class="mainheader">Factura Inicial: '.$arreglo['inicial'].' Factura Final: '.$arreglo['final'].'</b>';
		//echo $grid->db->last_query();

		$grid2 = new DataGrid('Detalles del impuesto');

		$select=array("impuesto tasa","FORMAT(sum(ROUND(monto*100/(impuesto+100),2)),2) AS base","FORMAT(sum(monto-ROUND(monto*100/(impuesto+100),2)),2) AS iva","FORMAT(sum(monto),2) AS total");
		$grid2->db->select($select);
		$grid2->db->from("positfact");
		$grid2->db->where("fecha",$fecha);
		$grid2->db->where("cajero",$cajero);
		$grid2->db->where("caja",$caja);
		$grid2->db->where("SUBSTRING(numero,1,1)!=",'X');
		$grid2->db->groupby('impuesto');
		$grid2->column("Tasa %"         ,"tasa" ,'align="right"');
		$grid2->column("Base Imponible" ,"base" ,'align="right"');
		$grid2->column("Impuesto"       ,"iva"  ,'align="right"');
		$grid2->column("Total"          ,"total",'align="right"');
		$grid2->build();

		$data['content'] .= $grid2->output;

		$select=array("a.tipo","a.banco","count(*) tran","FORMAT(sum((a.monto)*(SUBSTRING(a.numero,1,1)<>'X')),2) monto","b.nombre","c.descrip");
		$grid3 = new DataGrid('Res&uacute;men de formas de Pago');
		$grid3->db->select($select);
		$grid3->db->from("possfpa a");
		$grid3->db->join("tarjeta b","a.tipo=b.tipo");
		$grid3->db->join("tardet c","a.banco=c.concepto AND a.tipo=c.tarjeta","LEFT");
		$grid3->db->where("fecha",$fecha);
		$grid3->db->where("cajero",$cajero);
		$grid3->db->where("a.caja",$caja);
		$grid3->db->groupby('a.tipo');
		$grid3->column("Tipo"    ,"<#tipo#> <#nombre#>");
		$grid3->column("Cantidad","tran"  ,'align="center"');
		$grid3->column("Monto"   ,"monto" ,'align="right"');
		$grid3->build();

		if($grid3->recordCount>0) $data['content'] .= $grid3->output; else $data['content'] .='<p class="mainheader">No se encontrar&oacute;n resultados.</p>';

		$grid4 = new DataGrid('Detalle de caja');
		$grid4->db->select($select);
		$grid4->db->from("possfpa a");
		$grid4->db->join("tarjeta b","a.tipo=b.tipo");
		$grid4->db->join("tardet c","a.banco=c.concepto AND a.tipo=c.tarjeta","LEFT");
		$grid4->db->where("fecha",$fecha);
		$grid4->db->where("cajero",$cajero);
		$grid4->db->where("a.caja",$caja);
		$grid4->db->groupby('a.tipo,a.banco');
		$grid4->column("Tipo"    ,"<#tipo#> <#nombre#>");
		$grid4->column("Concepto","<#banco#> <#descrip#>"  );
		$grid4->column("Cantidad","tran"  ,'align="center"');
		$grid4->column("Monto"   ,"monto" ,'align="right"');
		$grid4->build();

		$data['content'] .= $grid4->output;
		$data['title']   = "<h1>Resultado cajero ".$cajero." caja ".$caja." fecha ".dbdate_to_human($fecha)."</h1>\n";;
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function detfact() {
		$this->rapyd->load("datagrid");
		$caja   = $this->uri->segment(4) ;
		$cajero = $this->uri->segment(5);
		$fecha  = $this->uri->segment(6);
		$menvia=site_url("supermercado/poscuadre/factura/$caja/$cajero/$fecha/<#numero#>");
		function colum($numero,$gtotal) {
			if ($gtotal < 0)
				return ('<b style="color:red;">'.$numero.'</b>');
			else
				return ($numero);
		}
		$grid = new DataGrid('Venta por facturas');
		$grid->use_function('colum');
		$grid->per_page = 20;

		$select=array("a.tipo","a.numero","DATE_FORMAT(a.fecha, '%d/%m/%Y') fecha","if(b.nombres IS NULL","a.nombres","concat(b.nombres,' ',b.apellidos)) nombres","FORMAT(a.impuesto,2) impuesto", "FORMAT(a.gtotal,2) gtotal", "a.hora");
		$grid->db->select($select);
		$grid->db->from("posfact a");
		$grid->db->join("club b","a.cliente=b.cod_tar","LEFT");
		$grid->db->where("fecha",$fecha);
		$grid->db->where("cajero",$cajero);
		$grid->db->where("caja",$caja);
		$grid->db->orderby('numero');
		$grid->column("Tipo"    ,"tipo"    );
		$grid->column("Numero"  ,"<a href='$menvia'><colum><#numero#>|<#gtotal#></colum></a>"  );
		$grid->column("Fecha"   ,"fecha"   );
		$grid->column("Nombres" ,"nombres" );
		$grid->column("Impuesto","impuesto",'align="right"');
		$grid->column("Total"   ,"gtotal"  ,'align="right"');
		$grid->build();

		if($grid->recordCount>0)
			$data['content'] = $grid->output;
		else
			$data['content'] ='<p class="mainheader">No se encontrar&oacute;n resultados.</p>';

		$data['content'] .= "<a href='".site_url("supermercado/poscuadre/concaja/$caja/$cajero/$fecha")."'>Regresar</a>";
		$data['title']   = "<h1>Facturas cajero ".$cajero." caja ".$caja." fecha ".dbdate_to_human($fecha)."</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function factura() {
		$this->rapyd->load("datagrid");
		$caja   = $this->uri->segment(4);
		$cajero = $this->uri->segment(5);
		$fecha  = $this->uri->segment(6);
		$numero = $this->uri->segment(7);
		$menvia=site_url("supermercado/poscuadre/detfact/$caja/$cajero/$fecha");

		$mSQL = "SELECT cliente, cedula, nombres, direc1, direc2
		         FROM posfact
		         WHERE fecha=$fecha AND cajero='$cajero' AND caja='$caja' AND numero='$numero'";
		$query = $this->db->query($mSQL);
		//echo $mSQL;
		$row = $query->row();
		$data['content']  = "NOMBRE: ".$row->nombres."<br>\n";
		$data['content'] .= "CEDULA: ".$row->cedula."<br>\n";
		$data['content'] .= "TARJETA: ".$row->cliente."<BR>";
		$data['content'] .= "DIRECCION: ".$row->direc1." ". $row->direc2."<br>";

		$mSQL = "SELECT if(referen='',codigo,referen) codigo, descrip, cantidad, precio, monto, impuesto
		         FROM positfact
		         WHERE fecha=$fecha AND cajero='$cajero' AND caja='$caja' AND numero='$numero'";

		$query = $this->db->query($mSQL);

		$data['content'] .= "<table border = 1 valign=center width=100%>\n";
		$data['content'] .= " <tr bgcolor=\"#c0c0c0\">\n";
		$data['content'] .= "  <td><center>Codigo</center></td>\n";
		$data['content'] .= "  <td><center>Descripcion</center></td>\n";
		$data['content'] .= "  <td><center>Cantidad</center></td>\n";
		$data['content'] .= "  <td><center>Precio</center>\n";
		$data['content'] .= "  <td><center>Total</center>\n";
		$data['content'] .= "  <td><center>IVA</center>\n";
		$data['content'] .= " </tr>\n";

		foreach ($query->result() as $row){
			$data['content'] .= "<tr>";
			$data['content'] .= "<td>".$row->codigo."</td>";
			$data['content'] .= "<td>".$row->descrip."</td>";
			$data['content'] .= "<td align=right>".$row->cantidad."</td>";
			$data['content'] .= "<td align=right>".number_format($row->precio,2)." </td>";
			$data['content'] .= "<td align=right>".number_format($row->monto,2)." </td>";
			$data['content'] .= "<td align=right>".$row->impuesto."</td>";
			$data['content'] .= "</tr>";
		}
		$data['content'] .= "</table>\n<br>\n";

		$data['rapyd_head']='';

		$mSQL = "SELECT a.tipo, a.fecha, a.monto, b.descrip
		         FROM possfpa AS a LEFT JOIN tardet AS b ON a.banco=b.concepto AND a.tipo=b.tarjeta
		         WHERE a.fecha=$fecha AND a.cajero='$cajero' AND a.caja='$caja' AND a.numero='$numero'
		         ORDER BY a.tipo";

		$query = $this->db->query($mSQL);

		$data['content'] .= "<h3>FORMA DE PAGO</H3>\n";
		$data['content'] .= "<table border = 1 valign=center width=100%>\n";
		$data['content'] .= "  <tr bgcolor=\"#c0c0c0\">\n";
		$data['content'] .= "  <td><center>Tipo</center></td>\n";
		$data['content'] .= "  <td><center>Fecha</center></td>\n";
		$data['content'] .= "  <td><center>Monto</center>\n";
		$data['content'] .= "  <td><center>Referencia</center></td>\n";
		$data['content'] .= "</tr>\n";

		foreach ($query->result() as $row){
			$data['content'] .= "<tr>";
			$data['content'] .= "<td align=center >".$row->tipo."</td>\n";
			$data['content'] .= "<td align=left>".$row->fecha."</td>\n";
			$data['content'] .= "<td align=right>".number_format($row->monto,2)." </td>\n";
			$data['content'] .= "<td align=left>".$row->descrip."</td>\n";
			$data['content'] .= "</tr>\n";
		}

		$data['content'] .= "<a href='".site_url("supermercado/poscuadre/concaja/$caja/$cajero/$fecha")."'>Regresar</a>";
		$data['title']   = "<h1>Factura ".$numero." caja ".$caja." fecha ".dbdate_to_human($fecha)."</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function detsfpa() {
		$this->rapyd->load("datagrid");
		$caja   = $this->uri->segment(4);
		$cajero = $this->uri->segment(5);
		$fecha  = $this->uri->segment(6);
		$menvia=site_url("supermercado/poscuadre/concaja/$caja/$cajero/$fecha");

		function colum($numero,$gtotal) {
			if ($gtotal < 0)
				return ('<b style="color:red;">'.$numero.'</b>');
			else
				return ($numero);
		}

		$grid = new DataGrid('Formas de pago');
		$grid->use_function('colum');
		$grid->per_page = 20;

		$select=array("a.tipo","a.numero","DATE_FORMAT(a.fecha, '%d/%m/%Y') fecha","a.num_ref","FORMAT(a.monto,2) monto","b.descrip");
		$grid->db->select($select);
		$grid->db->from("possfpa a");
		$grid->db->join("tardet b","a.banco=b.concepto AND a.tipo=b.tarjeta","LEFT");
		$grid->db->where("fecha",$fecha);
		$grid->db->where("cajero",$cajero);
		$grid->db->where("caja",$caja);
		$grid->db->orderby('a.tipo');
		$grid->column("Tipo"     ,"tipo"    );
		$grid->column("Numero"   ,"<colum><#numero#>|<#monto#></colum>"  );
		$grid->column("Fecha"    ,"fecha"   );
		$grid->column("Monto"    ,"monto"   ,'align="right"');
		$grid->column("Referencia","descrip");
		$grid->build();

		if($grid->recordCount>0)
			$data['content'] = $grid->output;
		else
			$data['content'] ='<p class="mainheader">No se encontrar&oacute;n resultados.</p>';

		$data['content'] .= "<a href='$menvia'>Regresar</a>";
		$data['title']   = "<h1>Pagos a cajero ".$cajero." caja ".$caja." fecha ".dbdate_to_human($fecha)."</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function detitfact() {
		$this->rapyd->load("datagrid");
		$caja   = $this->uri->segment(4);
		$cajero = $this->uri->segment(5);
		$fecha  = $this->uri->segment(6);
		$menvia=site_url("supermercado/poscuadre/concaja/$caja/$cajero/$fecha");

		$grid = new DataGrid('Ventas por art&iacute;culos');
		$grid->per_page = 20;
		$select=array("codigo","descrip","SUM(cantidad) cantidad","FORMAT(sum(monto),2) monto","FORMAT(sum(impuesto),2) impuesto","referen");
		$grid->db->select($select);
		$grid->db->from("positfact");
		$grid->db->where("fecha",$fecha);
		$grid->db->where("cajero",$cajero);
		$grid->db->where("caja",$caja);
		$grid->db->groupby('codigo');
		$grid->column("C&oacute;digo"     ,"codigo"  );
		$grid->column("Descripci&oacute;n","descrip" );
		$grid->column("Cantidad"          ,"cantidad",'align="right"');
		$grid->column("Monto"             ,"monto"   ,'align="right"');
		$grid->column("Referencia"        ,"referen" );
		$grid->build();

		if($grid->recordCount>0)
			$data['content'] = $grid->output;
		else
			$data['content'] ='<p class="mainheader">No se encontrar&oacute;n resultados.</p>';

		$data['content'] .= "<a href='$menvia'>Regresar</a>";
		$data['title']   = "<h1>Ventas por articulo ".$cajero." caja ".$caja." fecha ".dbdate_to_human($fecha)."</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
}
