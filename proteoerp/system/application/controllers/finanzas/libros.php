<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
if (!defined('BASEPATH')) exit('No direct script access allowed');
class Libros extends Controller {

	function Libros() {
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id(504,1);
		$this->load->helper('date');
	}

	function index() {
		$this->instalar();
		$this->rapyd->load('datagrid');
		$this->load->helper('fecha');

		for($i=1;$i<=12;$i++) $mmes[ str_pad($i, 2, "0", STR_PAD_LEFT)]=mesLetra($i);
		for($i=date('Y'); $i>=date('Y')-6;$i--) $anhos[$i]=$i;

		$descarga=$genera=array();
		$query = $this->db->query("SELECT * FROM libros WHERE activo='S'");
		foreach ($query->result() as $row){
			if($row->tipo=='D')
				$descarga[]=array('accion'=>$row->metodo, 'nombre' => $row->nombre);
			else
				$genera[]=array('accion'=>$row->metodo, 'nombre' => $row->nombre,'estampa'=>$row->estampa,'fgenera'=>$row->fgenera);
		}

		$mk=mktime(0, 0 , 0, date('n')-1,date('j'), date('Y'));
		$checkbox="<input type='checkbox' name='generar[]' value='<#accion#>' title='<#accion#>' /> ";
		$submit= form_submit('<#accion#>', 'Generar');
		$sanio = form_dropdown('year',$anhos,date('Y',$mk));
		$smes  = form_dropdown('mes' ,$mmes ,date('m',$mk));

		function obser($gene,$estampa,$metodo){
			if (empty($gene) or empty($estampa)) return "<span id='obs_${metodo}'>Niguna</span>";
			$hestampa=dbdate_to_human($estampa,'d/m/Y h:i a');
			$hgene=substr($gene,4).'/'.substr($gene,0,4);
			return "<span id='obs_${metodo}'>Generado el <b>${hestampa}</b> para el mes <b>${hgene}</b></span>";
		}

		$gene = new DataGrid("Documento para el mes ${smes} del a&ntilde;o ${sanio}",$genera);
		$gene->use_function('obser');
		$gene->per_page = count($genera);
		$gene->column('Generar'  ,$checkbox);
		$gene->column('Documento','nombre');
		$gene->column('Observaciones','<obser><#fgenera#>|<#estampa#>|<#accion#></obser>');
		$gene->submit('enviar', 'Generar');
		$gene->build();

		$link='<a href="javascript:void(0);" title="<#accion#>" onclick="descarga(\'<#accion#>\');">Descargar</a>';
		$desca = new DataGrid("Descarga de documentos",$descarga);
		$desca->per_page = count($descarga);
		$desca->column('Descargar'  ,$link);
		$desca->column('Documento','nombre');
		$desca->build();

		$link=site_url('finanzas/libros/generar');

		$data['script']='<script type="text/javascript">
		$(document).ready(function(){
			$("form").submit(function() {
				geneDoc();
				return false;
			});

			$("#preloader").ready(function() {
				$("#preloader").hide();
			});
		});

		function geneDoc(){
			$("#preloader").fadeIn("slow");
			$("#contenido").fadeOut("slow");
			var url = "'.$link.'";
			$.ajax({
				type: "POST",
				url: url,
				data: $("form").serialize(),
				success: function(msg){
					$("#preloader").fadeOut("slow");
					$("#contenido").fadeIn("slow");
					objs=$(":checked");
					jQuery.each(objs, function() {
						$("#obs_"+this.value).text("Acaba de ser generado");
					});
				}
			});
		}

		function descarga(nombre){
			param=$("select[name=\'year\']").val()+$("select[name=\'mes\']").val();
			window.location="'.site_url('finanzas/libros').'/"+nombre+"/"+param;
			//alert(param);
		}
		</script>';

		if($this->secu->essuper()){
			$conf=anchor('finanzas/libros/configurar','Configurar');
		}else{
			$conf='';
		}

		$form=  form_open('/finanzas/libros');
		$data['content'] = $form.$gene->output.form_close().$desca->output.$conf;
		$data['title']   = heading('Generar libros Contables');
		$data['head']    = script('jquery.js').$this->rapyd->get_head();
		$data['extras']  = $this->load->view('view_preloader',array(),true);
		$this->load->view('view_ventanas', $data);
	}

	function generar(){
		if(empty($_POST['generar'])) return;
		foreach($_POST['generar'] AS $gene){
			$this->$gene($_POST['year'].$_POST['mes']);
			$mSQL = "UPDATE libros SET estampa=NOW(), fgenera=$_POST[year]$_POST[mes] WHERE metodo = '$gene'";
			$this->db->simple_query($mSQL);
			echo "Generado $gene";
		}
	}

	//***********************************************
	// LIBROS Excell
	//***********************************************
	function wlcexcel($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('compras');
		compras::wlcexcel($mes);
	}
	// libros ventas fiscal
	function wlcexcel3($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('compras');
		compras::wlcexcel($mes);
	}

	//libro de ventas basado en cierres z
	function wlvcierrez($mes=null){
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventas');
		ventas::wlvcierrez($mes);
	}

	//Libro de compras para supermercado
	function wlcsexcel($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('comprassuper');
		comprassuper::wlcsexcel($mes);
	}

	//Libro de ventas con pto de ventas contribuyente normal
	function wlvexcelpdv1($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventassuper');
		ventassuper::wlvexcelpdv1($mes);
	}

	function invresu($mes=null){
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('inventario');
		inventario::invresu($mes);
	}

	function wlvexcel($mes=null){
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventas');
		ventas::_wlvexcel($mes,false);
	}

	function wlvexcelfiscal($mes=null){
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventas');
		ventas::_wlvexcel($mes,true);
	}

	//Libro de ventas no agrupados
	function wlvexcel2($mes=null){
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventas');
		ventas::wlvexcel2($mes);
	}

	//Libro de ventas fiscal
	function wlvexcel3($mes=null){
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventas');
		ventas::wlvexcel3($mes);
	}
	//Libro de ventas separado por sucursal
	function wlvexcelsucu($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventas');
		ventas::wlvexcelfiscal($mes);
	}

	//Libro de ventas no agrupado
	function wlvexcel1($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventas');
		ventas::wlvexcel1($mes);
	}

	function wlvexcelpdv($mes=null,$modalidad='M'){
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventassuper');
		ventassuper::wlvexcelpdv($mes,$modalidad);
	}

	//libro de ventas con punto de ventas primera quincena
	function wlvexcelpdvq1($mes=null){
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventassuper');
		ventassuper::wlvexcelpdv($mes,'Q1');
	}

	//libro de ventas con punto de ventas Segunda quincena
	function wlvexcelpdvq2($mes=null){
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventassuper');
		ventassuper::wlvexcelpdv($mes,'Q2');
	}

	//libro de ventas con punto de ventas fiscal
	function wlvexcelpdvfiscal($mes=null,$modalidad='M'){
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventassuper');
		ventassuper::wlvexcelpdvfiscal($mes,$modalidad);
	}

	//libro de ventas con punto de ventas primera quincena
	function wlvexcelpdvfiscalq1($mes=null){
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventassuper');
		ventassuper::wlvexcelpdvfiscal($mes,'Q1');
	}

	//libro de ventas con punto de ventas Segunda quincena
	function wlvexcelpdvfiscalq2($mes=null){
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventassuper');
		ventassuper::wlvexcelpdvfiscal($mes,'Q2');
	}

	function wlvexcelpdvfiscal2($mes=null,$modalidad='M'){
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventassuper');
		ventassuper::wlvexcelpdvfiscal2($mes,$modalidad);
	}

	//Libro de ventas personalizado
	function wlvpersonal($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('personal');
		personal::wlv($mes);
	}

	//Libro de ventas personalizado
	function wlcpersonal($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('personal');
		personal::wlc($mes);
	}

	function prorrata($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('prorratas');
		prorrata::prorrata($mes);
	}

	function prorrata1($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('prorratas');
		prorrata::prorrata1($mes);
	}

	//***********************************************
	// GENERACION
	//***********************************************
	function genecompras($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('compras');
		compras::genecompras($mes);
	}

	function geneventasfiscalpdv($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventassuper');
		ventassuper::geneventasfiscalpdv($mes);
	}

	function genesfmay($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventassuper');
		ventassuper::genesfmay($mes);
	}

	//Genera libro de ventas basado en sfacfiscal para supermercado
	function geneventassfacfiscal($mes=null){
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventassuper');
		ventassuper::genesfacfiscalpdv($mes,'Q1');
	}

	function geneventasfiscal($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventas');
		ventas::geneventasfiscal($mes);
	}

	function genegastos($mes=null){
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('gastosycxp');
		gastosycxp::genegastos($mes);
	}

	function genecxp($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('gastosycxp');
		gastosycxp::genecxp($mes);
	}

	function genesfac($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventas');
		ventas::_genesfac($mes,false);
	}

	function genesfacfiscal($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventas');
		ventas::_genesfac($mes,true);
	}

	function genesfaccierrez($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ventas');
		ventas::genesfaccierrez($mes);
	}

	function genesmov($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ingresos');
		ingresos::genesmov($mes);
	}

	function geneotin($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('ingresos');
		ingresos::geneotin($mes);
	}

	function generest($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('hoteleria');
		hoteleria::generest($mes);
	}

	function genehotel($mes=null) {
		if(!$this->_checkfecha($mes)) show_error('Parametro inv&aacute;lido');
		$this->_telefono('hoteleria');
		hoteleria::genehotel($mes);
	}

	//***********************************************
	// METODOS PARA PROBAR
	//***********************************************
	function depura($par=null){
		$libro=$gene=array();
		$par=(empty($par)) ? date('Ym',mktime(0, 0 , 0, date('n')-1,date('j'), date('Y'))) : $par;
		$reflection = new ReflectionClass('Libros');
		$aMethods = $reflection->getMethods();
		foreach($aMethods as $method){
			if($method->name=='generar') continue;
			if (preg_match("/^wl.*$/i",$method->name)) {
				$libro[]=anchor("finanzas/libros/$method->name/${par}",$method->name);
			}elseif(preg_match("/^gene.*$/i",$method->name)) {
				$gene[]=anchor("finanzas/libros/$method->name/${par}",$method->name);
			}

		}
		$data['content'] = '<table><tr><td>'.ul($libro).'</td><td>'.ul($gene).'</td></tr></table>';
		$data['title']   = heading('Prueba de los libros parametro '.$par);
		$data['head']    = '';
		$this->load->view('view_ventanas', $data);
	}

	//***********************************************
	// METODOS PRIVADOS
	//***********************************************
	// Arregla montasa en scst
	function scstarretasa($mCONTROL) {
		$m         = 1;
		$mTASA     = $mREDUCIDA = $mSOBRETASA=$mMONTASA = $mMONREDU = $mMONADIC = $mATASAS= $mIVA= $mEXENTO = 0;
		$dbcontrol = $this->db->escape($mCONTROL);
		$query = $this->db->query("SELECT * FROM itscst WHERE control=${dbcontrol}");
		foreach ( $query->result() as $row ){
			if($mATASAS==0) $mATASAS = $this->datasis->ivaplica($row->fecha);
			$mIVA  = $row->iva;
			$mTOTA = $row->importe;
			if ( $mIVA == $mATASAS['tasa']) {
				$mTASA    += round($mTOTA*$mIVA/100,2);
				$mMONTASA += $mTOTA;
			} elseif ( $mIVA == $mATASAS['redutasa']) {
				$mREDUCIDA += round($mTOTA*$mIVA/100,2);
				$mMONREDU  += $mTOTA;
			} elseif ( $mIVA == $mATASAS['sobretasa']) {
				$mSOBRETASA += round($mTOTA*$mIVA/100,2);
				$mMONADIC   += $mTOTA;
			} elseif ( $mIVA == 0 ) {
				$mEXENTO += $mTOTA;
			}
		}
		$mSQL = "UPDATE scst SET exento=${mEXENTO}, tasa=${mTASA},montasa=${mMONTASA},reducida=${mREDUCIDA},monredu=${mMONREDU},sobretasa=${mSOBRETASA},monadic=${mMONADIC} WHERE control=${dbcontrol}";
		$this->db->simple_query($mSQL);
	}

	function _checkfecha($mes){
		return ($mes>190000 AND $mes <999999) ? true : false;
	}

	//Funcion que llama a las clases asociadas
	function _telefono($clase){
		require_once(APPPATH.'/controllers/finanzas/libros/'.$clase.'.php');
	}

	function _tasas($mes) {
		$msql  = "SELECT tasa, redutasa, sobretasa FROM civa WHERE fecha<=".$mes."01 ORDER BY fecha DESC LIMIT 1";
		$mivas = $this->db->query($msql);
		$mt    = $mivas->row();
		$mtasa['general']   = $mt->tasa;
		$mtasa['reducida']  = $mt->redutasa;
		$mtasa['adicional'] = $mt->sobretasa;
		return $mtasa;
	}

	function _arreglatasa($mTRANSAC){
		$mTASA =$mREDUCIDA=$mSOBRETASA = $mMONTASA =$mMONREDU = $mMONADIC = $mEXENTO= $mIVA= 0;
		$mATASAS    = '';
		$dbtransac  = $this->db->escape($mTRANSAC);

		$query = $this->db->query("SELECT * FROM sitems WHERE transac=${dbtransac} AND tipoa<>'X'");
		foreach ( $query->result() as $row ){
			if (empty($mATASAS)) $mATASAS = $this->datasis->ivaplica($row->fecha);
			$mIVA    = $row->iva;
			$mTOTA   = $row->tota;
			if ( $mIVA == $mATASAS['tasa']) {
				$mTASA    += round($mTOTA*$mIVA/100,2);
				$mMONTASA += $mTOTA;
			} elseif ($mIVA == $mATASAS['redutasa']) {
				$mREDUCIDA += round($mTOTA*$mIVA/100,2);
				$mMONREDU  += $mTOTA;
			} elseif ($mIVA == $mATASAS['sobretasa']) {
				$mSOBRETASA += round($mTOTA*$mIVA/100,2);
				$mMONADIC   += $mTOTA;
			} elseif ($mIVA == 0 ) {
				$mEXENTO += $mTOTA;
			}
		}
		$mSQL = "UPDATE sfac SET exento=${mEXENTO}, tasa=${mTASA}, montasa=${mMONTASA},reducida=${mREDUCIDA}, monredu=${mMONREDU}, sobretasa=${mSOBRETASA}, monadic=${mMONADIC} WHERE transac=${dbtransac}";
		$this->db->simple_query($mSQL);
	}

	// Ajusta al valor
	function _ajustainv($mes, $cambia){
		$cambia = str_replace(',','', $cambia );
		$mSQL = "SELECT SUM(minicial), SUM(mcompras), SUM(mventas), SUM(mfinal) FROM invresu WHERE mes=${mes}";
		$mC   = damecur($mSQL);
		$row  = mysql_fetch_row($mC);
		$difer = $row[3]-$cambia;
		$factor = ( $row[2] + $difer ) / $row[2];
		//echo "$cambia  $difer  $factor";
		ejecutasql("UPDATE invresu SET mventas=mventas*".$factor." WHERE mes=${mes}");
		saldofinal($mes);
	}

	function _saldofinal($mes){
		// Calcula Saldo Final
		$this->db->simple_query("UPDATE invresu SET final=inicial+compras-ventas-notas+trans+fisico, mfinal=minicial+mcompras-mventas-mnotas+mtrans+mfisico WHERE mes=$mes ");
	}

	function _restames($mes){
		$ano = substr($mes,0,4);
		$mes = substr($mes,5,2);
		$mes = $mes-1;
		if ( $mes == 0 ){
			$mes = '12';
			$ano = $ano - 1 ;
		}
		return "$ano$mes";
	}

	// Calcula el Inventario
	function _invresum($mes){
		$udia=days_in_month(substr($mes,4),substr($mes,0,4));
		$fdesde=$mes.'01';
		$fhasta=$mes.$udia;
		//BETWEEN $fdesde AND $fhasta
		$mesa = _restames($mes);
		$this->db->simple_query("DELETE FROM invresu WHERE mes=$mes");

		$mSQL = "INSERT INTO invresu
			SELECT
			EXTRACT(YEAR_MONTH FROM a.fecha) AS mes,
			a.codigo, b.descrip, 0 AS inicial,
			SUM(a.cantidad*(a.origen IN ('2C','2D'))*IF(a.origen='2D',-1,1)) AS compras,
			SUM(a.cantidad*(a.origen IN ('3I','3M') )) AS ventas,
			SUM(a.cantidad*(a.origen='1T')) AS trans,
			SUM((a.cantidad-a.anteri)*(a.origen IN ('0F','8F'))) AS fisico,
			SUM(a.cantidad*(a.origen='4N')) AS notas,
			0 AS final,  0 AS minicial,
			SUM(a.monto*(a.origen IN ('2C','2D'))*IF(a.origen='2D',-1,1)) AS mcompras,
			SUM(a.cantidad*a.promedio*(a.origen IN ('3I','3M'))) AS mventas,
			SUM(a.cantidad*a.promedio*(a.origen='1T')) AS mtrans,
			SUM((a.cantidad-a.anteri)*a.promedio*(a.origen IN ('0F','8F'))) AS mfisico,
			SUM(a.cantidad*a.promedio*(a.origen='4N')) AS mnotas,
			0 AS mfinal, SUM(venta)
			FROM costos AS a LEFT JOIN sinv AS b ON a.codigo=b.codigo
			WHERE a.fecha BETWEEN ${fdesde} AND ${fhasta}  AND MID(b.tipo,1,1)!='S'
			GROUP BY mes,a.codigo";
		$this->db->simple_query($mSQL);

		//Insertamos los del mes pasado que no tienen movimiento este mes
		$this->db->simple_query("INSERT IGNORE INTO invresu (mes, codigo,descrip, inicial, compras,ventas, trans, fisico,notas,final, minicial, mcompras, mventas, mtrans, mfisico, mnotas, mfinal ) SELECT $mes, codigo, descrip, 0, 0,0,0,0,0,0,0,0,0,0,0,0,0 FROM invresu WHERE mes=$mesa");

		//Eliminar notas y transferencias
		$this->db->simple_query("UPDATE invresu SET ventas=ventas+notas, mventas=mventas+mnotas, notas=0, mnotas=0 WHERE mes=${mes} ");
		$this->db->simple_query("UPDATE invresu SET ventas=ventas-trans, mventas=mventas-mtrans, trans=0, mtrans=0 WHERE mes=${mes} ");
		$this->db->simple_query("UPDATE invresu SET fisico=0, mfisico=0 WHERE mes=${mes} ");

		// Busca Saldo Inicial
		$this->db->simple_query("UPDATE invresu a JOIN invresu b ON a.codigo=b.codigo AND a.mes=${mesa} AND b.mes=$mes SET b.minicial=a.mfinal, b.inicial=a.final ");

		// Calcula Saldo Final
		$this->_saldofinal($mes);

		// Elimina Negativos  quita la venta en exeso
		$this->db->simple_query("UPDATE invresu SET ventas=inicial+compras, mventas=minicial+mcompras WHERE mes=${mes} AND final<0 ");
		$this->_saldofinal($mes);
		$calcular='Consultar';
	}

	function configurar(){
		$this->rapyd->load("datafilter","datagrid");
		$uri = anchor('finanzas/libros/cedit/show/<#metodo#>','<#nombre#>');
		$link=site_url('/finanzas/libros');
		$grid = new DataGrid('Seleccione las opciones que desea activar para el modulo');
		$grid->use_function('form_checkbox');
		$grid->db->select(array('nombre','IF(tipo="D","Descarga","Generar") AS tipo',' (activo="S") AS activo ','metodo'));
		$grid->db->from('libros');
		$grid->order_by('tipo','asc');
		$grid->column('Activo', '<form_checkbox><#metodo#>|<#metodo#>|<#activo#></form_checkbox>','align="center"');
		$grid->column('Nombre',$uri);
		$grid->column('Tipo'  ,'tipo');
		$grid->add('finanzas/libros/cedit/create');
		$grid->button('back',RAPYD_BUTTON_BACK, "javascript:window.location='$link'", "BL");
		$grid->build();
		//echo $grid->db->last_query();

		$link=site_url('/finanzas/libros/activar');
		$data['script']='<script type="text/javascript">
		$(document).ready(function() {
			$(":checkbox").click(function () {
				activar($(this).attr("value"));
			});
		});
		function activar(metodo){
			var url = "'.$link.'"+"/"+metodo;
			$.ajax({
				url: url,
				success: function(msg){
					if(msg=0)
						alert("Error");
				}
			});
		}
		</script>';
		$data['content'] = '<form>'.$grid->output.'</form>';
		$data['title']   = '<h1>Configuraci&oacute;n de libros</h1>';
		$data['head']    = script('jquery-1.2.6.pack.js').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function activar($metodo){
		$mSQL = "UPDATE libros SET activo=IF(activo='S','N','S') WHERE metodo = '$metodo'";
		echo $this->db->simple_query($mSQL);
	}

	function cedit(){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('Edici&oacute;n de caja', 'libros');
		$edit->back_url = site_url('finanzas/libros/configurar');

		$edit->metodo = new inputField('Metodo', 'metodo');
		$edit->metodo->rule = 'required';
		//$edit->metodo->mode = "autohide";

		$edit->tipo = new dropdownField('Tipo', 'tipo');
		$edit->tipo->option('G','Generar' );
		$edit->tipo->option('D','Descarga');

		$edit->activo = new dropdownField('Activo', 'activo');
		$edit->activo->option('S','Si');
		$edit->activo->option('N','No');

		$edit->nombre = new inputField('Nombre', 'nombre');

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		$data['content'] = $edit->output;
		$data['title']   = '<h1>Configuraci&oacute;n de libros</h1>';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function instalar(){
		$mSQL="CREATE TABLE IF NOT EXISTS `libros` (
		  `metodo` varchar(50) NOT NULL default '',
		  `nombre` varchar(150) default NULL,
		  `activo` char(1) default NULL,
		  `tipo` char(1) default NULL,
		  `estampa` timestamp NOT NULL default CURRENT_TIMESTAMP,
		  `fgenera` char(6) default NULL,
		  PRIMARY KEY  (`metodo`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1";
		$this->db->simple_query($mSQL);

		$data[]=array('metodo'=>'wlvexcelpdv'        ,'activo'=>'N','tipo'=>'D' ,'nombre' => 'Libro de Ventas PDV'      );
		$data[]=array('metodo'=>'wlvexcelpdvq1'      ,'activo'=>'N','tipo'=>'D' ,'nombre' => 'Libro de Ventas PDV Quincenta 1');
		$data[]=array('metodo'=>'wlvexcelpdvq2'      ,'activo'=>'N','tipo'=>'D' ,'nombre' => 'Libro de Ventas PDV Quincenta 2');
		$data[]=array('metodo'=>'wlvexcelpdvfiscal'  ,'activo'=>'N','tipo'=>'D' ,'nombre' => 'Libro de Ventas PDV Fiscal');
		$data[]=array('metodo'=>'wlvexcelpdvfiscal2' ,'activo'=>'N','tipo'=>'D' ,'nombre' => 'Libro de Ventas PDV Fiscal V2');
		$data[]=array('metodo'=>'wlvexcelpdvfiscalq1','activo'=>'N','tipo'=>'D' ,'nombre' => 'Libro de Ventas PDV Quincenta 1 Fiscal');
		$data[]=array('metodo'=>'wlvexcelpdvfiscalq2','activo'=>'N','tipo'=>'D' ,'nombre' => 'Libro de Ventas PDV Quincenta 2 Fiscal');
		$data[]=array('metodo'=>'wlvexcel'           ,'activo'=>'S','tipo'=>'D' ,'nombre' => 'Libro de Ventas'          );
		$data[]=array('metodo'=>'wlvexcel2'          ,'activo'=>'N','tipo'=>'D' ,'nombre' => 'Libro de Ventas no Agrupadas');
		$data[]=array('metodo'=>'wlvexcelfiscal'     ,'activo'=>'S','tipo'=>'D' ,'nombre' => 'Libro de Ventas Agrupadas Fiscal');
		$data[]=array('metodo'=>'wlvcierrez'         ,'activo'=>'N','tipo'=>'D' ,'nombre' => 'Libro de Ventas basado en cierre Z');
		$data[]=array('metodo'=>'wlvexcelsucu'       ,'activo'=>'N','tipo'=>'D' ,'nombre' => 'Libro de Ventas por Sucursal');
		$data[]=array('metodo'=>'wlvpersonal'        ,'activo'=>'N','tipo'=>'D' ,'nombre' => 'Libro de Ventas Personalizado'   );
		$data[]=array('metodo'=>'wlcexcel'           ,'activo'=>'S','tipo'=>'D' ,'nombre' => 'Libro de Compras'         );
		$data[]=array('metodo'=>'wlcpersonal'        ,'activo'=>'N','tipo'=>'D' ,'nombre' => 'Libro de Compras Personalizado'   );
		$data[]=array('metodo'=>'wlcsexcel'          ,'activo'=>'N','tipo'=>'D' ,'nombre' => 'Libro de Compras Supermercado');
		$data[]=array('metodo'=>'wlvexcele'          ,'activo'=>'N','tipo'=>'D' ,'nombre' => 'Libro de Ventas  ESPECIAL');
		$data[]=array('metodo'=>'wlcexcele'          ,'activo'=>'N','tipo'=>'D' ,'nombre' => 'Libro de Compras ESPECIAL');
		$data[]=array('metodo'=>'prorrata'           ,'activo'=>'N','tipo'=>'D' ,'nombre' => 'Prorrata'                 );
		$data[]=array('metodo'=>'invresu'            ,'activo'=>'N','tipo'=>'D' ,'nombre' => 'Libro de Inventario'      );

		$data[]=array('metodo'=>'genecompras'         ,'activo'=>'S','tipo'=>'G' ,'nombre' => 'Generar Libro de compras COMPRAS' );
		$data[]=array('metodo'=>'genesfaccierrez'     ,'activo'=>'N','tipo'=>'G' ,'nombre' => 'Generar Libro de ventas basado en cierre Z' );
		$data[]=array('metodo'=>'genegastos'          ,'activo'=>'S','tipo'=>'G' ,'nombre' => 'Generar Libro de compras GASTOS'  );
		$data[]=array('metodo'=>'genecxp'             ,'activo'=>'S','tipo'=>'G' ,'nombre' => 'Generar Libro de compras CXP'     );
		$data[]=array('metodo'=>'genesfac'            ,'activo'=>'S','tipo'=>'G' ,'nombre' => 'Generar Libro de ventas Facturas' );
		$data[]=array('metodo'=>'genesfacfiscal'      ,'activo'=>'N','tipo'=>'G' ,'nombre' => 'Generar Libro de ventas Facturas Fiscal' );
		$data[]=array('metodo'=>'geneventasfiscalpdv' ,'activo'=>'N','tipo'=>'G' ,'nombre' => 'Generar Libro de ventas Fiscal PDV'   );
		$data[]=array('metodo'=>'genesfmay'           ,'activo'=>'N','tipo'=>'G' ,'nombre' => 'Generar Libro de ventas Facturas al mayor' );
		$data[]=array('metodo'=>'genesmov'            ,'activo'=>'N','tipo'=>'G' ,'nombre' => 'Generar Libro de ventas CXC'      );
		$data[]=array('metodo'=>'geneotin'            ,'activo'=>'S','tipo'=>'G' ,'nombre' => 'Generar Libro de ventas O.Ingresos');
		$data[]=array('metodo'=>'generest'            ,'activo'=>'N','tipo'=>'G' ,'nombre'  =>'Generar Libro de ventas Restaurante');
		$data[]=array('metodo'=>'genehotel'           ,'activo'=>'N','tipo'=>'G' ,'nombre'  =>'Generar Libro de ventas Hotel');
		$data[]=array('metodo'=>'geneventassfacfiscal','activo'=>'N','tipo'=>'G' ,'nombre'  =>'Generar Libro de ventas PDV de auditorias');


		foreach($data AS $algo){
			$mSQL = $this->db->insert_string('libros', $algo);
			$this->db->simple_query($mSQL);
		}

		$campos = $this->db->list_fields('siva');
		if (!in_array('serie',$campos)){
			$mSQL="ALTER TABLE `siva`  ADD COLUMN `serie` VARCHAR(20) NULL DEFAULT NULL AFTER `serial`;";
			$this->db->simple_query($mSQL);
		}

		if (!in_array('afecta',$campos)){
			$mSQL="ALTER TABLE `siva`  ADD COLUMN `afecta` VARCHAR(10) NULL DEFAULT NULL AFTER `fafecta`";
			$this->db->simple_query($mSQL);
		}

		if (!in_array('cierrez',$campos)){
			$mSQL="ALTER TABLE `siva` ADD COLUMN `cierrez` VARCHAR(15) NULL DEFAULT NULL AFTER `serial`";
			$this->db->simple_query($mSQL);
		}

		if (!in_array('hora',$campos)){
			$mSQL="ALTER TABLE `siva` ADD `hora` TIME DEFAULT '0' NULL";
			$this->db->simple_query($mSQL);
		}

		if (!in_array('manual',$campos)){
			$mSQL="ALTER TABLE `siva` ADD COLUMN `manual` CHAR(1) NULL DEFAULT 'N' COMMENT 'Indica si el documento fue realizado manual o por sistema'";
			$this->db->simple_query($mSQL);
		}

		$mSQL="ALTER TABLE `siva` CHANGE `numero` `numero` VARCHAR(20) NULL";
		$this->db->simple_query($mSQL);

		$mSQL="ALTER TABLE `siva` ADD `serial` CHAR(20) NULL";
		$this->db->simple_query($mSQL);

		$mSQL="ALTER TABLE `siva`  CHANGE COLUMN `nombre` `nombre` VARCHAR(200) NULL DEFAULT NULL AFTER `clipro`";
		$this->db->simple_query($mSQL);

		//$mSQL="ALTER TABLE `siva`  CHANGE COLUMN `numero` `numero` VARCHAR(20) NOT NULL DEFAULT '' AFTER `fecha`";
		//$this->db->simple_query($mSQL);


		$campos = $this->db->list_fields('sfac');

		if(!in_array('manual'  ,$campos)){
			$this->db->query("ALTER TABLE `sfac` ADD COLUMN `manual` CHAR(50) NULL DEFAULT 'N'");
		}

		//echo $uri = anchor('finanzas/libros/configurar','Configurar');
	}
}
