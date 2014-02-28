<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Cierre extends Controller {

	function Cierre(){
		parent::Controller();
		$this->load->helper('text');
		$this->load->library("rapyd");
		$this->datasis->modulo_id('108',1);
		//$this->rapyd->set_connection('supermer');
		//$this->load->database('supermer',TRUE);
	}

	function index() {
		$this->rapyd->load("datagrid");
		$this->rapyd->load("datafilter");
		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0');
		$titulo  = anchor_popup('supermercado/lresumen', ' Ver Resumen de caja',$atts)." <---> ";
		$titulo .= anchor_popup('supermercado/lresumen/indext',' Ver Resumen de todas las cajas',$atts);

		$filter = new DataFilter($titulo);
		$filter->fecha = new dateonlyField("Fecha","fecha","d/m/Y");
		$filter->fecha->size =11;
		$filter->fecha->clause="where";
		$filter->fecha->operator="=";
		$filter->fecha->insertValue=date("Y-m-d");
		//$filter->fecha->append();
		$filter->buttons("reset","search");
		$filter->build();
		$data['content'] = $filter->output;

		function iconcaja($caja,$cajero,$fecha){
			$CI =& get_instance();
			$cerrado = $CI->datasis->dameval("SELECT numero FROM dine WHERE caja='$caja' AND cajero='$cajero' AND fecha='$fecha' ");
			$atts=array('align'=>'LEFT','border'=>'0');
			$fecha=str_replace('-','',$fecha);
			$atRI = array(
				'width'     => '800','height' => '600',
				'scrollbars'=> 'yes','status' => 'yes',
				'resizable' => 'yes','screenx'=> '0',
				'screeny'   => '0');
			if (!empty($cerrado))
				return image('caja_cerrada.gif',"Caja Cerrada: $caja",$atts)."<h3>Caja: $caja</h3><br><center>".anchor_popup("/supermercado/cierre/doccierre/$cerrado",'Ver Cuadre',$atRI).' '.anchor_popup("/supermercado/cierre/ventasdia/$fecha/$caja",'Detalle de Ventas',$atRI).'</center>';
			else
				return image('caja_abierta.gif',"Caja Abierta: $caja",$atts)."<h3>Caja: $caja</h3>".'<center><a href='.site_url("supermercado/cierre/forcierre/$caja/$cajero/$fecha").'>Cerrar Cajero</a></center>';
		}
		$data['forma'] ='';
		if($this->rapyd->uri->is_set("search") AND !empty($filter->fecha->value)){
			$fecha=$filter->fecha->value;
			$grid = new DataGrid('Cierre de cajas para la fecha: '.$filter->fecha->value);
			$select=array('viefac.caja', 'viefac.cajero', 'fecha as qfecha' ,'SUM(viefac.gtotal) monto', 'scaj.nombre as name', 'sum(TRUNCATE(gtotal/60000,0)) cupon');
			$grid->db->select($select);
			$grid->db->from('viefac');
			$grid->db->join('scaj','scaj.cajero=viefac.cajero','LEFT');
			$grid->db->groupby("viefac.caja,viefac.cajero");
			$grid->use_function('iconcaja','number_format');
			$grid->column("Status/Caja"     ,"<iconcaja><#caja#>|<#cajero#>|<#qfecha#></iconcaja>"  ,'align="RIGHT"');
			$grid->column("Cajero"   ,"cajero",'align="RIGHT"');
			$grid->column("Nombre"   ,"name");
			$grid->column("Ventas Bs","<number_format><#monto#>|2|,|.</number_format>",'align="RIGHT"');
			$grid->column("Cupones"  ,"<number_format><#cupon#>|0|,|.</number_format>",'align="RIGHT"');
			$grid->build();
			$data['content'] .= $grid->output;
			//echo $grid->db->last_query();
		}

		$data['title']   = "<h1>Cierre de Caja</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);

		//$data['titulo'] = $this->rapyd->get_head()."<center><h2>CIERRE DE CAJA </h2></center>\n";
		//$this->layout->buildPage('ventas/view_ventas', $data);
	}

	function forcierre() {
		$this->rapyd->load("datagrid2");
		$this->rapyd->load("datagrid");
		$this->rapyd->load("fields");

		$caja    = $this->uri->segment(4);
		$cajero  = $this->uri->segment(5);
		$qfecha  = $this->uri->segment(6);
		if (!$caja or !$cajero or !$qfecha ) redirect('/supermercado/cierre');

		//$data['forma'] =script('nformat.js');

		$efectivo=array();
		$atts    =array('class'=>'inputnum','size'=>'15','align'=>'right');
		$attsr   =array('class'=>'inputnum','size'=>'15','align'=>'right','readonly'=>'readonly');
		$catts   =array('class'=>'inputnum','size'=>'6' ,'align'=>'right');

		$query = $this->db->query('SELECT a.tipo,a.denomina,b.cambiobs*a.denomina equivalencia,a.nombre FROM monebillet a JOIN mone b ON a.moneda=b.moneda ORDER BY a.tipo,a.moneda,a.denomina DESC');
		$i=0;
		foreach ($query->result() as $row){
			$valor=$row->equivalencia;
			$catts['name']=$catts['id']=$row->tipo.$row->denomina;

			$catts['name']=$catts['id']='EFE'.$i;
			$catts['onKeyDown']=$catts['onKeyUp']="opera($valor,'".$catts['name']."');";
			$cantidad = form_input($catts);

			//cantidad sistema
			$attsr['name']=$attsr['id']='T'.$catts['name'];
			$attsr['value']='0,00';
			$total = form_input($attsr);

			$pasa=array();
			$pasa['denom']  =$row->nombre;
			$pasa['cant']   =$cantidad;
			$pasa['sistema']=$total;

			if($row->tipo=='MO')     $pasa['tipo']   ='Monedas';
			elseif($row->tipo=='BI') $pasa['tipo']   ='Billetes';
			else                     $pasa['tipo']   =$row->tipo;

			$efectivo[]=$pasa;
			$i++;
		}$efe_filas=$i;
		$query->free_result();

		$atts['name']=$atts['id']='TOTRAS';
		$atts['onChange']="montosolo('".$atts['name']."');tefectivo();";
		$total = form_input($atts);
		$pasa['denom']  ='Monto';
		$pasa['cant']   ='';
		$pasa['sistema']=$total;
		$pasa['tipo']   ='Otras Denominaciones';
		$efectivo[]=$pasa;

		$atts['name']=$atts['id']='TFONDO';
		$total = form_input($atts);

		$pasa['sistema']=$total;
		$pasa['tipo']   ='Fondo de caja (-)';
		$efectivo[]=$pasa;

		$attsr['name']=$attsr['id']='EFETOTAL';
		$total = form_input($attsr);

		$pasa['denom']  ='TOTAL';
		$pasa['sistema']=$total;
		$pasa['tipo']   ='Total Efectivo';
		$efectivo[]=$pasa;

		$msistema=$this->datasis->dameval("SELECT sum(monto) FROM viepag WHERE caja=$caja AND cajero=$cajero AND fecha=$qfecha AND tipo IN ('EF','DP') AND SUBSTRING(numero,1,1)<>'X' ");
		$attsr['value']=number_format($msistema,'2',',','.');
		$attsr['name']=$attsr['id']='EFESISTEMA';
		$total = form_input($attsr);
		$pasa['denom']  ='SISTEMA';
		$pasa['sistema']=$total;
		$pasa['tipo']   ='Total Efectivo';
		$efectivo[]=$pasa;
		$attsr['value']='';
		$query->free_result();
		//fin efectivo

		$mSQL="SELECT a.concepto, a.descrip, sum(b.monto) sistema
		FROM tardet a JOIN viepag b ON b.banco=a.concepto
		WHERE a.tarjeta='CT' AND b.tipo='CT' AND b.caja='$caja' AND b.cajero='$cajero' AND b.fecha=$qfecha AND SUBSTRING(b.numero,1,1)<>'X'
		GROUP BY a.concepto UNION
		SELECT concepto, descrip, '0' sistema FROM tardet WHERE tarjeta='CT' AND concepto NOT IN (
		SELECT a.concepto FROM tardet a JOIN viepag b ON b.banco=a.concepto
		WHERE a.tarjeta='CT' AND b.tipo='CT' AND b.caja='$caja' AND b.cajero='$cajero' AND b.fecha=$qfecha  AND SUBSTRING(b.numero,1,1)<>'X'
		GROUP BY a.concepto)
		ORDER BY concepto";
		$i=0;
		$ctsistema=0;
		$js_cesArray=array();
		$cestatiket=array();
		$query = $this->db->query($mSQL);
		foreach ($query->result() as $row){
			$catts['name']=$catts['id']='CCESTA'.$row->concepto;
			$cantidad = form_input($catts);

			$atts['name']=$atts['id']='TCESTA'.$row->concepto;
			$js_cesArray[]=$row->concepto;
			$atts['onChange']="montosolo('".$atts['name']."'); tcestatiket();";
			$monto = form_input($atts).form_hidden('TCESTA_T'.$i,$row->concepto);

			$attsr['name']=$attsr['id']='SCESTA'.$row->concepto;
			$attsr['value']=number_format($row->sistema,'2',',','.');
			$sistema = form_input($attsr);
			$attsr['value']='';

			$pasa=array();
			$pasa['descrip']='<b>'.$row->concepto.'</b> '.$row->descrip;
			$pasa['cant']   =$cantidad;
			$pasa['monto']  =$monto;
			$pasa['sistema']=$sistema;
			$i++;
			$cestatiket[]=$pasa;
			$ctsistema+=$row->sistema;
		}$ces_filas=$i;
		$query->free_result();

		$mSQL="SELECT a.tipo,a.nombre, sum(b.monto) sistema
		FROM tarjeta a JOIN viepag b ON a.tipo=b.tipo
		WHERE a.tipo NOT IN ('EF','CT','NC','ND', 'DE','IR','DP') AND b.caja='$caja' AND b.cajero='$cajero' AND b.fecha=$qfecha AND SUBSTRING(b.numero,1,1)<>'X'
		GROUP BY a.tipo UNION SELECT tipo,nombre, '0' sistema  FROM tarjeta
		WHERE tipo NOT IN (SELECT tipo FROM  viepag  WHERE caja='$caja' AND cajero='$cajero' AND fecha=$qfecha GROUP BY tipo)
		AND tipo NOT IN ('EF','CT','NC','ND', 'DE','IR','DP') ORDER BY tipo";
		$i=0;
		$fpsistema=0;
		$tarjetas=array();
		$js_ofpArray=array();
		$query = $this->db->query($mSQL);
		foreach ($query->result() as $row){
			$catts['name']=$catts['id']='COFP'.$row->tipo;
			$cantidad = form_input($catts);

			$atts['name']=$atts['id']='TOFP'.$row->tipo;
			$atts['values']=0;
			$js_ofpArray[]=$row->tipo;
			$atts['onChange']="montosolo('".$atts['name']."'); totrasfpa();";
			$monto = form_input($atts).form_hidden('TOFP_T'.$i,$row->tipo);

			$attsr['name']=$attsr['id']='SOFP'.$row->tipo;
			$attsr['value']=number_format($row->sistema,'2',',','.');
			$sistema = form_input($attsr);
			$attsr['value']='';

			$pasa=array();
			$pasa['descrip']='<b>'.$row->tipo.'</b> '.$row->nombre;
			$pasa['cant']   =$cantidad;
			$pasa['monto']  =$monto;
			$pasa['sistema']=$sistema;
			$fpsistema+=$row->sistema;
			$tarjetas[]=$pasa;
			$i++;
		}$otr_filas=$i;
		$query->free_result();

		$attsr   =array('class'=>'inputnum','size'=>'20','align'=>'right','readonly'=>'readonly');

		$attRecEfe=$attSisEfe=$attDifEfe=$attsr;
		$attRecEfe['name']=$attRecEfe['id']='EFERECI';
		$attSisEfe['name']=$attSisEfe['id']='EFESIST';
		$attDifEfe['name']=$attDifEfe['id']='EFEDIFE';
		$attSisEfe['value']=number_format($msistema,'2',',','.');

		$attRecCtk=$attSisCtk=$attDifCtk=$attsr;
		$attRecCtk['name']=$attRecCtk['id']='CTKRECI';
		$attSisCtk['name']=$attSisCtk['id']='CTKSIST';
		$attDifCtk['name']=$attDifCtk['id']='CTKDIFE';
		$attSisCtk['value']=number_format($ctsistema,'2',',','.');

		$attRecOtr=$attSisOtr=$attDifOtr=$attsr;
		$attRecOtr['name']=$attRecOtr['id']='OTRRECI';
		$attSisOtr['name']=$attSisOtr['id']='OTRSIST';
		$attDifOtr['name']=$attDifOtr['id']='OTRDIFE';
		$attSisOtr['value']=number_format($fpsistema,'2',',','.');

		$attRecRec=$attSisRec=$attDifRec=$attsr;
		$attRecRec['name']=$attRecRec['id']='RECRECI';
		$attSisRec['name']=$attSisRec['id']='RECSIST';
		$attDifRec['name']=$attDifRec['id']='RECDIFE';
		$attSisRec['value']=number_format($fpsistema+$ctsistema+$msistema,'2',',','.');

		$resumen=array(
			0 => array(
				'descrip' =>'Efectivo',
				'recibido'=>form_input($attRecEfe).form_hidden('QEFEGLOBAL'),
				'sistema' =>form_input($attSisEfe).form_hidden('QEFESISTEMA',$msistema),
				'diferen' =>form_input($attDifEfe),
				),
			1 => array(
				'descrip' =>'Cesta Tickets',
				'recibido'=>form_input($attRecCtk),
				'sistema' =>form_input($attSisCtk).form_hidden('QCESSISTEMA',$ctsistema),
				'diferen' =>form_input($attDifCtk),
				),
			3 => array(
				'descrip' =>'Otros',
				'recibido'=>form_input($attRecOtr),
				'sistema' =>form_input($attSisOtr).form_hidden('QOTRSISTEMA',$fpsistema),
				'diferen' =>form_input($attDifOtr),
				),
			4 => array(
				'descrip' =>'Recibido',
				'recibido'=>form_input($attRecRec),
				'sistema' =>form_input($attSisRec).form_hidden('QTOTSISTEMA',$fpsistema+$ctsistema+$msistema),
				'diferen' =>form_input($attDifRec),
				)
		);

		$script="<script type='text/javascript'>
		function caldiferencia() {
			var pre=new Array('EFE','CTK','OTR','REC');
			var i=0,acumulador=0;
			for(i=0;i<3;i++){
				recibid=des_number_format(document.getElementById(pre[i]+'RECI').value,'.',',');
				sistema=des_number_format(document.getElementById(pre[i]+'SIST').value,'.',',');
				document.getElementById(pre[i]+'DIFE').value=number_format(recibid-sistema,'.',',');
				acumulador=acumulador+recibid;
			}
			document.getElementById(pre[i]+'RECI').value=number_format(acumulador,'.',',');
			sistema=des_number_format(document.getElementById(pre[i]+'SIST').value,'.',',');
			document.getElementById(pre[i]+'DIFE').value=number_format(acumulador-sistema,'.',',');
		}

		function opera(valor,traeid) {
			cantidad=document.getElementById(traeid);
			recibe  =document.getElementById('T'+traeid)
			recibe.value= number_format(cantidad.value*valor,'.',',');
			tefectivo();
		}
		function tefectivo() {
			var i=0,acumulador=0;
			for(i=0;i<$efe_filas;i++){
				valor=des_number_format(document.getElementById('TEFE'+i).value,'.',',');
				acumulador=acumulador+valor;
			}
			totras=des_number_format(document.getElementById('TOTRAS').value,'.',',');
			tfondo=des_number_format(document.getElementById('TFONDO').value,'.',',');
			total=document.getElementById('EFETOTAL');
			total.value=number_format(acumulador+totras-tfondo,'.',',');
			recibido=document.getElementById('EFERECI');
			recibido.value=number_format(acumulador+totras-tfondo,'.',',');
			caldiferencia();
		}
		function montosolo(traeid){
			elemento=document.getElementById(traeid);
			valor=des_number_format(elemento.value,'.',',');
			elemento.value=number_format(valor,'.',',');
		}
		function tcestatiket() {
			var pre=new Array('".join("','",$js_cesArray)."');
			var i=0,acumulador=0;
			for(i=0;i<$ces_filas;i++){
				valor=des_number_format(document.getElementById('TCESTA'+pre[i]).value,'.',',');
				acumulador=acumulador+valor;
			}
			recibido=document.getElementById('CTKRECI');
			recibido.value=number_format(acumulador,'.',',');
			caldiferencia();
		}
		function totrasfpa() {
			var pre=new Array('".join("','",$js_ofpArray)."');
			var i=0,acumulador=0;
			for(i=0;i<$otr_filas;i++){
				valor=des_number_format(document.getElementById('TOFP'+pre[i]).value,'.',',');
				acumulador=acumulador+valor;
			}
			recibido=document.getElementById('OTRRECI');
			recibido.value=number_format(acumulador,'.',',');
			caldiferencia();
		}
		</script>";

		$att_pcf=array('class'=>'input','size'=>'30','align'=>'right');;
		$att_pcf['name']='controlp';
		$pcfiscal=form_input($att_pcf);

		$att_ucf=array('class'=>'input','size'=>'30','align'=>'right');;
		$att_ucf['name']='controlf';
		$ucfiscal=form_input($att_ucf);

		$att_obs=array('class'=>'input','rows'=>'2','align'=>'right');;
		$att_obs['name']='observaciones';
		$observa=form_textarea($att_obs);

		$efegrid = new DataGrid2('Efectivo',$efectivo);
		$efegrid->agrupar(' ', 'tipo');
		$efegrid->per_page = count($efectivo);
		$efegrid->column("Denominacion","denom"      ,'align="RIGHT"');
		$efegrid->column("Cantidad"    ,"<#cant#>"   ,'align="RIGHT"');
		$efegrid->column("Sub-total"   ,"<#sistema#>",'align="RIGHT"');
		$efegrid->build();
		$data['listai'] = $efegrid->output;

		$targrid = new DataGrid('Otras Formas de Pago',$tarjetas);
		$targrid->per_page = count($tarjetas);
		$targrid->column("Descripcion","<#descrip#>");
		$targrid->column("Cantidad"   ,"<#cant#>"   ,'align="RIGHT"');
		$targrid->column("Monto"      ,"<#monto#>"  ,'align="RIGHT"');
		$targrid->column("Sistema"    ,"<#sistema#>",'align="RIGHT"');
		$targrid->build();
		$data['listad'] = $targrid->output;

		$cestagrid = new DataGrid('Cesta Tiket',$cestatiket);
		$cestagrid->per_page = count($cestatiket);
		$cestagrid->column("Descripcion","<#descrip#>");
		$cestagrid->column("Cantidad"   ,"<#cant#>",'align="RIGHT"');
		$cestagrid->column("Monto"      ,"<#monto#>"   ,'align="RIGHT"');
		$cestagrid->column("Sistema"    ,"<#sistema#>" ,'align="RIGHT"');
		$cestagrid->build();
		$data['listad'] .= $cestagrid->output;

		$resugrid = new DataGrid('Resumen',$resumen);
		$resugrid->per_page = count($resumen);
		$resugrid->column("Descripcion","<#descrip#>");
		$resugrid->column("Recibido"   ,"<#recibido#>",'align="RIGHT"');
		$resugrid->column("Sistema"    ,"<#sistema#>" ,'align="RIGHT"');
		$resugrid->column("Diferencia" ,"<#diferen#>" ,'align="RIGHT"');
		$resugrid->build();
		$data['listab'] = $resugrid->output.'Primer control Fiscal '.$pcfiscal.'<br>Ultimo control Fiscal '.$ucfiscal.'<br>Observaciones<br>'.$observa;

		$data['submit']=form_submit('mysubmit', 'Guardar');

		$atts=array('onsubmit'=>"return confirm('Seguro que desea Guardar')");

		$hidden = array('otr_filas' => $otr_filas, 'ces_filas' => $ces_filas);
		//$data['form']=form_open("supermercado/cierre/guardar/$caja/$cajero/$qfecha",$atts,$hidden);

		//$data['titulo'] = $script.$this->rapyd->get_head()."<center><h2>Cierre de Caja $caja Cajero $cajero</h2></center>\n";
		//$this->layout->buildPage('supermercado/view_cierre', $data);

		$ddata['content'] = form_open("supermercado/cierre/guardar/$caja/$cajero/$qfecha",$atts,$hidden);
		$ddata['content'].= $this->load->view('view_cierre', $data, true);
		$ddata['content'].= '<center>'.form_submit('mysubmit', 'Guardar').'</center>';
		$ddata['content'].= form_close();
		$ddata['title']   = "<h1>Cierre de caja $caja Cajero $cajero</h1>";
		$ddata["head"]    = $this->rapyd->get_head().script('nformat.js').$script;
		$this->load->view('view_ventanas', $ddata);
	}

	function guardar(){
		//$this->load->database('supermer',TRUE);
		$caja    = $this->uri->segment(4);
		$cajero  = $this->uri->segment(5);
		$qfecha  = $this->uri->segment(6);

		$data['content']='<center>';
		$mSQL = "SELECT COUNT(*) FROM dine WHERE caja='$caja' AND cajero='$cajero' AND fecha=$qfecha";
		if ($this->datasis->dameval($mSQL) == 0)
			$data['content'] .= $this->_guardar($caja,$cajero,$qfecha);
		else
			$data['content'] .= "<BR><H2>CIERRE YA GUARDADO</H2><BR><BR>\n";

		//$data['titulo']=$data['form']=$data['listai']=$data['listad']=$data['listab']=$data['submit']='';

		//$this->layout->buildPage('supermercado/view_cierre', $data);

		$data['title']   = '';
		$data["head"]    = '';
		$this->load->view('view_ventanas', $data);
	}

	function _guardar($caja,$cajero,$qfecha) {
		$fecha    = strtotime($qfecha);
		$numero   = str_pad($this->datasis->prox_sql("ndine"),   8, "0", STR_PAD_LEFT) ;
		$transac  = str_pad($this->datasis->prox_sql("ntransa"), 8, "0", STR_PAD_LEFT) ;
		$nrocaja  = "CAJAPOS".$caja{0};
		$banco    = $this->datasis->dameval("SELECT valor FROM valores WHERE nombre='$nrocaja'");
		$almacen  = $this->datasis->dameval("SELECT almacen FROM caja WHERE caja='$caja'");
		$_POST['EFERECI']=(empty($_POST['EFERECI'])) ? 0 : $_POST['EFERECI'];
		$efgloval = floatval(str_replace(",",'.',str_replace(".","",$_POST['EFERECI'])));
		$sistema  = (empty($_POST['QEFESISTEMA'])) ? 0 : floatval($_POST['QEFESISTEMA']);

		// Crea las cajas por si acaso
		$this->db->query("INSERT IGNORE INTO banc SET codbanc='DF', tbanco='CAJ', banco='DIFERENCIA EN CAJA', moneda='Bs', saldo=0");
		$this->db->query("INSERT IGNORE INTO banc SET codbanc='$banco', tbanco='CAJ', moneda='Bs', saldo=0");

		// Las crea en bsal
		$this->db->query("INSERT IGNORE INTO bsal SET codbanc='$banco', ano='" . substr($qfecha,0,4) ."' ");
		$this->db->query("INSERT IGNORE INTO bsal SET codbanc='DF',     ano='" . substr($qfecha,0,4) ."' ");

		// Ahora Carga el efectivo
		$mSQL = "INSERT INTO itdine ( numero,    tipo,  concepto, referen, cantidad, denomi, compuca, compumo, total )
	           VALUES  (?, 'EF',' ','BILLETES Y MONEDAS', 1, ?, 1, ?, ?) ";
		$this->db->query($mSQL,array($numero,$efgloval,$sistema,$efgloval));
		$billete = $efgloval;

		// Carga Cesta Tickets
		$cesta = 0;
		$query = $this->db->query("SELECT concepto, descrip FROM tardet WHERE tarjeta='CT'");
		foreach ($query->result() as $row){
			//$mmonto    = (empty($_POST["TCESTA".$row->concepto])) ? 0 : $_POST["TCESTA".$row->concepto];
			//$mmonto    = $_POST["TCESTA".$row->concepto];
			$mmonto    = $this->__post("TCESTA".$row->concepto); //$_POST["TCESTA".$row->concepto];
			$msistema  = $_POST["SCESTA".$row->concepto];
			$can       = intval($_POST["CCESTA".$row->concepto]);
			$mmonto    = floatval(str_replace(",",".",str_replace(".","",$mmonto)));
			$msistema  = floatval(str_replace(",",".",str_replace(".","",$msistema)));

			if ($mmonto<>0 or $msistema<>0) {
				$query="INSERT INTO itdine ( numero, tipo, concepto, referen, cantidad, denomi, compuca, compumo,  total )
				        VALUES   (?, 'CT', ? , ? , ?, ?, 1, ?, ?)";
				$this->db->query($query,array($numero,$row->concepto,$row->descrip,$can,$mmonto,$msistema,$mmonto));
				$mSQL = "UPDATE tardet SET saldo=saldo+".$mmonto." WHERE tarjeta='CT' AND concepto= ?";
				$this->db->query($mSQL,array($row->concepto));
				$cesta += $mmonto;
			}
		};
		//$query->free_result();

		// Otras formas de Pago
		$mSQL = 'SELECT tipo,nombre FROM tarjeta WHERE tipo NOT IN ("EF","CT","NC","ND", "DE", "IR","DP") ORDER BY tipo';
		$query = $this->db->query($mSQL);
		$efectos = 0;
		foreach ( $query->result() as $row ){
			//$ind= ($mod==0) ? ($mes>12 OR $mes==0)? 12: $mes : $mod;

			$mmonto    = (empty($_POST["TOFP".$row->tipo])) ? 0 : $_POST["TOFP".$row->tipo];
			$msistema  = (empty($_POST["SOFP".$row->tipo])) ? 0 : $_POST["SOFP".$row->tipo];
			$can       = (empty($_POST["COFP".$row->tipo])) ? 0 : $_POST["COFP".$row->tipo];
			$can       = intval($can);
			$mmonto    = floatval(str_replace(",",".",str_replace(".","",$mmonto)));
			$msistema  = floatval(str_replace(",",".",str_replace(".","",$msistema)));

			if ($mmonto<>0 or $msistema<>0 ){
				$mSQL = "INSERT INTO itdine ( numero, tipo, concepto, referen, cantidad, denomi, compuca, compumo, total )
				         VALUES (?, ? ,'   ', ? , ?, ?, 1 , ? , ?)";
				$this->db->query($mSQL,array($numero,$row->tipo,$row->nombre,$can,$mmonto,$msistema,$mmonto));
				$efectos += $mmonto;
			}
		}
		//$query->free_result();

		$msistema = floatval($_POST['QTOTSISTEMA']);
		//$this->datasis->agregacol("dine","observa","TEXT");

		if (empty($_POST['TFONDO'])) $fondo=0; else $fondo=$_POST['TFONDO'];

		$mmonto1=$billete+$efectos+$cesta;
		$mmonto2=$mmonto1-$msistema;
		$mSQL = "INSERT INTO dine ( numero, fecha, cajero, caja, corte, monedas, parcial,trecibe, recibido, computa, diferen, usuario, codbanc, fondo, nfiscal, estampa, hora, transac, observa ,ifiscal)
		         VALUES(?,?,?,?,'F',?,0,?,?,?,?,?, ?, ?,?, NOW(),'".date("h:m:s")."', ?, ?, ? )";

		$this->db->query($mSQL,array(
			$numero,$qfecha,$cajero,$caja,$billete,$mmonto1,$mmonto1,$msistema,$mmonto2,
			$this->secu->usuario(),$banco,$fondo,$_POST['controlf'],$transac,$_POST['observaciones'],$_POST['controlp']));

		$mSQL = "INSERT INTO bmov (codbanc, moneda, numcuent, banco, saldo, tipo_op, numero, fecha, clipro,
	             codcp, nombre, monto, concepto, enlace, liable, transac, usuario, estampa,
	             hora, anulado )
	            VALUES ( ?, 'Bs', 'CAJA POS','CAJA POS',0, 'NC',?,?,'O','VENT',
	            ?,?,'INGRESO ARQUEO CAJA $caja CAJERO $cajero FECHA $fecha',
	            '$cajero','S','$transac', '".$this->secu->usuario()."', NOW(), '".date("h:m:s")."','N' ) ";
		$this->db->query($mSQL,array($banco,"ARCA${numero}",$qfecha,"INGRESOS CUADRE DE CAJA ${caja}",$mmonto1));
		$this->db->query("UPDATE banc SET saldo=saldo+".$mmonto1." WHERE codbanc='$banco'");
		$this->db->query("UPDATE bsal SET saldo".substr($qfecha,4,2) ."= saldo".substr($qfecha,4,2)."+".$mmonto1." WHERE codbanc='$banco' AND ano='" . substr($qfecha,0,4) ."' ");
		if ($mmonto2 < 0){
			$mSQL = "INSERT INTO bmov
			          (codbanc, moneda, numcuent, banco, saldo, tipo_op, numero, fecha, clipro,
			          codcp, nombre, monto, concepto, enlace, liable, transac, usuario, estampa,
			          hora, anulado )
			         VALUES ( 'DF', 'Bs', 'DIFERENCIA EN CAJA','DIFERENCIA EN CAJA',0, 'NC','ARDF$numero',$qfecha,'O','VENT',
			           'INGRESOS CUADRE DE CAJA $caja',".($msistema-$mmonto1).",
			           'FALTANTE ARQUEO DE CAJA $caja CAJERO $cajero FECHA $fecha',
			           '$cajero','S','$transac', '".$this->secu->usuario()."', NOW(),
			           '".date("h:m:s")."','N' ) ";
			$this->db->query($mSQL);
			$this->db->query("UPDATE banc SET saldo=saldo+".($msistema-$mmonto1)." WHERE codbanc='DF'");
			$this->db->query("UPDATE bsal SET saldo".substr($qfecha,4,2)."= saldo".substr($qfecha,4,2) ."+". ($msistema-$mmonto1) . " WHERE codbanc='DF' AND ano='" . substr($qfecha,0,4) ."' ");
		}elseif (($mmonto1-$msistema)>0) {
			$mSQL = "INSERT INTO bmov
	  	          (codbanc, moneda, numcuent, banco, saldo, tipo_op, numero, fecha, clipro,
	  	          codcp, nombre, monto, concepto, enlace, liable, transac, usuario, estampa,
	  	          hora, anulado )
	  	         VALUES ('DF', 'Bs', 'DIFERENCIA EN CAJA','DIFERENCIA EN CAJA',0, 'ND','ARDF$numero',$qfecha,'O',
	  	          'VENT','INGRESOS CUADRE DE CAJA $caja',". ($mmonto1-$msistema) .",
	  	          'SOBRANTE ARQUEO DE CAJA $caja CAJERO $cajero FECHA $fecha',
	  	          '$cajero','S','$transac', '".$this->secu->usuario()."', now(),'".date("h:m:s")."','N' ) ";
			$this->db->query($mSQL);
			$this->db->query("UPDATE banc SET saldo=saldo-" . ($mmonto1-$msistema) . " WHERE codbanc='DF'");
			$this->db->query("UPDATE bsal SET saldo" . substr($qfecha,4,2) ."= saldo" . substr($qfecha,4,2) ."+". ($mmonto1-$msistema) . " WHERE codbanc='DF' AND ano='" . substr($qfecha,0,4) ."' ");
		}
		$atRI = array(
       'width'     => '800','height' => '600',
       'scrollbars'=> 'yes','status' => 'yes',
       'resizable' => 'yes','screenx'=> '0',
       'screeny'   => '0');
		$salida ="<center><h2>CIERRE GUARDADO $numero</h2><br>Para Imprimir haga click ".anchor_popup("/supermercado/cierre/doccierre/$numero",'AQUI',$atRI).'<br>';
		$salida.=anchor('supermercado/cierre/index/search/osp','Regresar').'</center>';
		logusu('cierrep',"Cierre guardado por proteo de la caja ${caja} numero ${numero}");
		return $salida;
	}

	function _reverso($numero){
		$dbnumero = $this->db->escape($numero);
		$cana     = $this->datasis->dameval("SELECT COUNT(*) FROM dine WHERE numero=${dbnumero}");

		if($cana==1){
			$mSQL=$this->datasis->dameval("SELECT fecha, cajero, caja, corte, monedas, parcial,trecibe, recibido, computa, diferen,transac FROM dine WHERE numero=${dbnumero}");
			$row=$this->datasis->damerow($mSQL);

			$transac=$row->transac;

			$mSQL="DELETE FROM dine WHERE numero=${dbnumero}";
			$mSQL="DELETE FROM itdine WHERE numero=${dbnumero}";

		}else{
			return false;
		}

	}


	function doccierre() {
		$numero = $this->uri->segment(4);
		$titulo = $this->datasis->traevalor('TITULO1');
		$rif    = $this->datasis->traevalor('RIF');

		$mSQL   = "SELECT * FROM dine WHERE numero=$numero ";
		$query = $this->db->query($mSQL);
		$row   = $query->row();
		$observa = $row->observa;

		$this->load->library('fpdf');
		$esta = & $this->fpdf;
		$esta->AddPage();
		$esta->image($_SERVER['DOCUMENT_ROOT'].base_url().'images/logotipo.jpg',10,8,40);
		$esta->ln(5);
		$esta->SetFont('Arial','B',16);
		$esta->cell(0,10,"ARQUEO  DE  CAJA  Nro. $numero", 0, 2, 'C');
		$esta->ln(1);
		$esta->SetFont('Arial','B',10);
		$esta->cell(0,5,"  RIF: ".$rif, 0, 2, 'L');
		$esta->ln(11);

		$esta->SetFont('Arial','',10);
		$esta->cell(13,5,"Caja", 1, 0, 'C');
		$esta->cell(58,5,"Cajero Entrega(".$row->cajero.")", 1, 0, 'C');
		$esta->cell(58,5,"C.Principal", 1, 0, 'C');
		$esta->cell(20,5,"Fecha", 1, 0, 'C');
		$esta->cell(20,5,"Realizado", 1, 0, 'C');
		$esta->cell(21,5,"N. Fiscal", 1, 2, 'C');

		$nomcaja = $this->datasis->dameval("SELECT nombre FROM scaj WHERE cajero='".$row->cajero."'");
		$usuario = $this->datasis->dameval("SELECT us_nombre FROM usuario WHERE us_codigo='".$row->usuario."'");
		$esta->ln(0);
		$esta->SetFont('Arial','B',10);
		$esta->cell(13,7,$row->caja, 1, 0, 'C');
		$esta->cell(58,7,substr($nomcaja,0,22), 1, 0, 'C');
		$esta->cell(58,7,substr($usuario,0,22), 1, 0, 'C');
		$esta->cell(20,7,dbdate_to_human($row->fecha), 1, 0, 'C');
		$esta->cell(20,7,dbdate_to_human($row->estampa), 1, 0, 'C');
		$esta->cell(21,7,$row->nfiscal, 1, 2, 'C');
		$esta->ln(3);

		// NC y Anulaciones
		$devol  = $this->datasis->dameval("SELECT sum(gtotal) FROM viefac WHERE caja='".$row->caja."' AND fecha='".$row->fecha."' AND MID(numero,1,2)='NC' AND cajero='".$row->cajero."' ");
		$devolc = $this->datasis->dameval("SELECT COUNT(*) FROM viefac WHERE caja='".$row->caja."' AND fecha='".$row->fecha."' AND MID(numero,1,2)='NC' AND cajero='".$row->cajero."' ");
		$anul   = $this->datasis->dameval("SELECT sum(gtotal) FROM viefac WHERE caja='".$row->caja."' AND fecha='".$row->fecha."' AND MID(numero,1,1)='X' AND cajero='".$row->cajero."' ");
		$anulc  = $this->datasis->dameval("SELECT COUNT(*) FROM viefac WHERE caja='".$row->caja."' AND fecha='".$row->fecha."' AND MID(numero,1,1)='X' AND cajero='".$row->cajero."' ");

		$esta->cell(30,7,"Devoluciones", "TBL", 0, 'C');
		$esta->cell(12,7,number_format($devolc,0), "TB", 0, 'R');
		$esta->cell(10,7,"Bs.:", "TB", 0, 'C');
		$esta->cell(25,7,number_format($devol,2), "TBR", 0, 'R');
		$esta->cell(36,7,"", 0, 0, 'C');
		$esta->cell(30,7,"Anulaciones", "TBL", 0, 'C');
		$esta->cell(12,7,number_format($anulc,0), "TB", 0, 'R');
		$esta->cell(10,7,"Bs.:", "TB", 0, 'C');
		$esta->cell(25,7,number_format($anul,2), "TBR", 2, 'R');

		$esta->ln(5);
		$esta->SetFont('Arial','B',12);
		$esta->cell(0,10,"RESULTADO:", "T", 0, 'L');

		$esta->ln(6);
		$esta->cell(30,10,'  ',0,0);
		$esta->SetFont('Arial','',12);
		$esta->cell(60,10,"Recaudado segun Sistema (a): ", 0, 0, 'R');
		$esta->SetFont('Arial','B',14);
		$esta->cell(40,10,number_format($row->computa,2), 0, 0, 'R');

		$esta->ln(6);
		$esta->cell(30,10,'  ',0,0);
		$esta->SetFont('Arial','',12);
		$esta->cell(60,10,"Entregado por el Cajero (b): ", 0, 0, 'R');
		$esta->SetFont('Arial','B',14);
		$esta->cell(40,10,number_format($row->recibido,2), 0, 0, 'R');

		$esta->ln(8);
		$esta->cell(90,1,'',0,0);
		$esta->cell(40,1,'','B',0);

		$esta->ln(0);
		$esta->cell(30,10,'  ',0,0);
		$esta->SetFont('Arial','',12);
		$esta->cell(60,10,"Diferencia (a-b): ", 0, 0, 'R');
		$esta->SetFont('Arial','B',14);
		$esta->cell(40,10,number_format($row->computa-$row->recibido,2), 0, 0, 'R');

		$esta->ln(15);
		$esta->SetFont('Arial','B',12);
		$esta->cell(0,10,"DETALLE DE LO RECAUDADO:", "T", 0, 'L');
		$esta->ln(10);

		$esta->cell(20,5,'  ',0,0);
		$esta->SetFont('Arial','BI',10);
		$esta->cell(50,5,"FORMAS DE PAGO", "B", 0, 'R');
		$esta->cell(30,5,"Sistema", "B", 0, 'R');
		$esta->cell(30,5,"Recibido", "B", 0, 'R');
		$esta->cell(30,5,"Diferencia", "B", 0, 'R');
		$esta->ln(5);

		$mSQL = "SELECT referen,sum(compumo) compumo, sum(total) total FROM itdine WHERE numero=$numero AND tipo<>'CT' GROUP BY tipo  ORDER BY referen";
		$query->free_result();
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$esta->cell(20,5,'  ',0,0);
				$esta->SetFont('Arial','',10);
				$esta->cell(50,5,$row->referen, 0, 0, 'R');
				$esta->SetFont('Arial','B',10);
				$esta->cell(30,5,number_format($row->compumo,2), 0, 0, 'R');
				$esta->cell(30,5,number_format($row->total,2), 0, 0, 'R');
				$esta->cell(30,5,number_format($row->compumo-$row->total,2), 0, 0, 'R');
				$esta->ln(5);
			}
		}
		$esta->ln(3);
		$esta->cell(20,5,'  ',0,0);
		$esta->SetFont('Arial','BI',10);
		$esta->cell(50,5,"CESTA TICKETS", "B", 0, 'R');
		$esta->cell(30,5,"Sistema", "B", 0, 'R');
		$esta->cell(30,5,"Recibido", "B", 0, 'R');
		$esta->cell(30,5,"Diferencia", "B", 0, 'R');
		$esta->ln(5);

		$mSQL = "SELECT referen,sum(compumo) compumo, sum(total) total FROM itdine WHERE numero=$numero AND tipo='CT' GROUP BY referen ";
		$query->free_result();
		$query = $this->db->query($mSQL);
		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				$esta->cell(20,5,'',0,0, 'R');
				$esta->SetFont('Arial','',10);
				$esta->cell(50,5,$row->referen, 0, 0, 'R');
				$esta->SetFont('Arial','B',10);
				$esta->cell(30,5,number_format($row->compumo,2), 0, 0, 'R');
				$esta->cell(30,5,number_format($row->total,2), 0, 0, 'R');
				$esta->cell(30,5,number_format($row->compumo-$row->total,2), 0, 0, 'R');
				$esta->ln(5);
			}
		}

		$esta->ln(15);
		$esta->SetFont('Arial','B',12);
		$esta->cell(0,10,"OBSERVACIONES:", "T", 0, 'L');
		$esta->ln(10);
		$esta->SetFont('Arial','',10);
		$esta->cell(20,5,'',0,0, 'R');
		$esta->multicell(130,5,$observa,0);
		$esta->ln(5);

		$esta->SetFont('Arial','B',12);
		$esta->cell(0,10,"FIRMAS:", 0, 0, 'L');

		$esta->ln(30);
		$esta->SetFont('Arial','',8);
		$esta->cell(15,5,'',0,0, 'R');
		$esta->cell(60,5,$nomcaja,"T", 0, 'C');
		$esta->cell(10,5,'',0,0, 'R');
		$esta->cell(60,5,$usuario,"T", 0, 'C');
		$esta->Output();
	}

	function ventasdia() {
		$fecha  = $this->uri->segment(4);
		$caja   = $this->uri->segment(5);

		$titulo = $this->datasis->traevalor('TITULO1');
		$rif    = $this->datasis->traevalor('RIF');

		$this->load->library('lpdf',array('caja'=>$caja, 'fecha'=>$fecha));
		$esta = & $this->lpdf;

		$esta->AliasNbPages();
		$esta->AddPage();
		$esta->SetFont('Times','',12);
		$esta->caja = $caja;
		$esta->fecha = $fecha;
		$esta->SetFont('Arial','B',10);

		$mSQL = "SELECT SUM(a.monto*(a.impuesto=0)) AS exento, b.numero, b.tipo ,
			    b.fecha, b.cajero, b.caja, b.cliente, concat(rtrim(c.nombres),' ',
			    rtrim(c.apellidos))  AS nombre, b.impuesto,b.gtotal, c.cedula, c.cod_tar,
			    IF(d.tipo='RI' , d.monto,0)  AS ivarete , d.tipo
			FROM vieite AS a
			LEFT JOIN viefac AS b ON a.numero = b.numero AND a.caja = b.caja AND a.fecha=b.fecha
			LEFT JOIN club AS c ON b.cliente = c.cod_tar
			LEFT JOIN viepag AS d ON a.numero=d.numero AND d.tipo='RI' AND a.fecha=d.fecha AND a.caja=b.caja
			WHERE b.fecha = $fecha AND b.caja=$caja
			GROUP BY a.numero
			ORDER BY  b.caja, b.numero  ";

		$query = $this->db->query($mSQL);
		$i=$exento=$base=$impuesto=$ivarete=$gtotal=0;

		if ($query->num_rows() > 0){
			foreach ($query->result() as $row){
				if ($i) {
					$esta->SetFillColor(255,255,255);
					$i--;
				}else{
					$esta->SetFillColor(230,255,230);
					$i++;
				}

			$esta->SetFont('Arial','',8);
			$esta->cell(15,3,$row->numero, 0, 0, 'L',1);
			$esta->cell(18,3,$row->cedula, 0, 0, 'L',1);
			$esta->cell(45,3,$row->nombre, 0, 0, 'L',1);
			$esta->cell(24,3,number_format($row->exento,2), 0, 0, 'R',1);
			$esta->cell(24,3,number_format($row->gtotal-$row->impuesto,2), 0, 0, 'R',1);
			$esta->cell(24,3,number_format($row->impuesto,2), 0, 0, 'R',1);
			$esta->cell(20,3,number_format($row->ivarete,2), 0, 0, 'R',1);
			$esta->cell(24,3,number_format($row->gtotal,2), 0, 0, 'R',1);
			$esta->ln(3);

			$exento   += $row->exento;
			$base     += $row->gtotal-$row->impuesto;
			$impuesto += $row->impuesto;
			$ivarete  += $row->ivarete;
			$gtotal   += $row->gtotal;
			}
		}
		$esta->SetFillColor(230,230,230);

		$esta->SetFont('Arial','B',10);
		$esta->cell(53,5,"TOTALES ", "LT", 0, 'R',1);
		$esta->cell(29,5,"Exento", "LT", 0, 'C',1);
		$esta->cell(29,5,"Base", "LT", 0, 'C',1);
		$esta->cell(29,5,"Impuesto", "LT", 0, 'C',1);
		$esta->cell(25,5,"Ret. IVA", "LT", 0, 'C',1);
		$esta->cell(29,5,"Total", "LTR", 0, 'C',1);
		$esta->ln(5);

		$esta->cell(53,5,"", 'LB', 0, 'R',1);
		$esta->cell(29,5,number_format($exento,2), "LB", 0, 'R',1);
		$esta->cell(29,5,number_format($base,2), "LB", 0, 'R',1);
		$esta->cell(29,5,number_format($impuesto,2), "LB", 0, 'R',1);
		$esta->cell(25,5,number_format($ivarete,2), "LB", 0, 'R',1);
		$esta->cell(29,5,number_format($gtotal,2), "LBR", 0, 'R',1);
		$esta->ln(10);

		$esta->SetFont('Arial','B',14);
		$esta->cell(45,7,"CONTROL FISCAL", 0, 0, 'R');
		$esta->cell(25,7,"INICIAL", 0, 0, 'R');
		$esta->cell(40,7,"", 1, 0, 'R');
		$esta->cell(30,7,"FINAL", 0, 0, 'R');
		$esta->cell(40,7,"", 1, 0, 'R');

		$esta->Output();
	}

	function __post($nom){
		$pivo=($this->input->post($nom)===FALSE) ? 0 : $this->input->post($nom);
		return (empty($pivo)) ? 0 : $pivo;
	}
}
