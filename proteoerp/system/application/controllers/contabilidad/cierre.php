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
		$this->load->library('rapyd');
		$this->datasis->modulo_id(606,1);
	}

	function index(){
		$this->instalar();
		$this->rapyd->load('datagrid','dataform');

		$fecha=$this->uri->segment(4);
		$form = new DataForm();
		$form->title('Fecha para la ejecuci&oacute;n');
		$form->fecha = new dateonlyField('Fecha de Cierre', 'fecha','d/m/Y');
		$form->fecha->size = 10;
		$form->fecha->insertValue = ($fecha ? $fecha : date('Ymd', mktime  (0, 0, 0,12,31,date('Y')-1 )));
		$form->submit('btnsubmit','Cerrar');
		$form->build_form();
		$link=site_url('contabilidad/cierre/ejecutar');

		$data['script']="<script type='text/javascript'>
		function generar(){
			$('#preloader').show();
			$('#contenido').hide();
			$.ajax({
				type: 'POST',
				url: '$link',
				data: $('input,select').serialize(),
				success: function(msg){
					$('#preloader').hide();
					$('#contenido').show('slow');
					alert(msg);
				}
			});
		}

		$(document).ready(function(){
			$('#preloader').hide();
			$('form').submit(function() {
				generar();
				return false;
			});
			$('#preloader').hide();
		});
		</script>";

		$data['extras']="<div id='preloader' style='position:absolute; left:40%; top:40%; font-family:Verdana, Arial, Helvetica, sans-serif;'>
			<center>".image('loading4.gif').'<br>'.image('loadingBarra.gif')."<br>
			<b>Generando . . . </b>
			</center>
		</div>";
		$data['content'] =$form->output;
		$data['head']    = script('jquery.js').$this->rapyd->get_head();
		$data['title']   = heading('Cierre Contable');
		$this->load->view('view_ventanas', $data);
	}

	function ejecutar(){
		$error=false;
		$mfinal  =$this->input->post('fecha');
		//echo $mfinal;
		//$mfinal='31/12/2009';
		if($mfinal==false) redirect('contabilidad/cierre');

		$mfinal  = date('Ymd',timestampFromInputDate($mfinal));
		$anio    = substr($mfinal,2,2);
		$annio   = substr($mfinal,0,4);
		$comprob = "ZIERRE${anio}";

		$this->db->simple_query("DELETE FROM itcasi WHERE comprob='${comprob}'");
		$this->db->simple_query("DELETE FROM casi   WHERE comprob='${comprob}'");

		$mSQL = "INSERT INTO casi SET comprob='${comprob}', fecha=${mfinal}, descrip='ASIENTO DE CIERRE DEL EJERCICIO', total = 0, debe=0, haber=0, estampa=NOW(),tipo='INDETERMIN',status='A',origen='MANUAL'";
		$centinela=$this->db->simple_query($mSQL);
		if($centinela==false){ memowrite($mSQL,'casi'); $error=true; }

		$mSQL = "INSERT INTO itcasi (fecha,comprob,origen,cuenta,referen,concepto,debe,haber,ccosto,sucursal)
		    SELECT $mfinal fecha,
		    '${comprob}' comp, 'MANUAL' origen,
		    cuenta, 'CIERRE ".$anio."' referen,
		    'CIERRE DE CUENTAS DE RESULTADO EJERCICIO ".$anio."' concepto,
		    SUM(haber) debe, SUM(debe) haber, 0 ccosto, 0 sucu
		    FROM itcasi WHERE cuenta>='4' AND fecha<=${mfinal} AND fecha>=${annio}0101
		    GROUP BY cuenta";
		$centinela=$this->db->simple_query($mSQL);
		if($centinela==false){ memowrite($mSQL,'itcasi'); $error=true; }

		$mSQL = "INSERT INTO itcasi (fecha,comprob,origen,cuenta,referen,concepto,debe,haber,ccosto,sucursal)
		SELECT fecha, comprob, origen,
		    (SELECT resultado FROM cemp limit 1) AS cuenta,
		    referen,concepto,
		    IF(SUM(debe-haber)>0,0,SUM(haber-debe)) AS debe,
		    IF(SUM(debe-haber)>0,SUM(debe-haber),0) AS haber, 0 AS ccosto, 0 AS sucu
		    FROM itcasi WHERE comprob='${comprob}' GROUP BY comprob ";
		$centinela=$this->db->simple_query($mSQL);
		if($centinela==false){ memowrite($mSQL,'itcasi'); $error=true; }
		$centinela=$this->db->simple_query("DELETE FROM itcasi WHERE debe=haber AND comprob='${comprob}'");
		if($centinela==false){ memowrite($mSQL,'itcasi'); $error=true; }
		$centinela=$this->db->simple_query("UPDATE casi SET
		    debe =(SELECT SUM(debe)  FROM itcasi WHERE comprob='${comprob}'),
		    haber=(SELECT SUM(haber) FROM itcasi WHERE comprob='${comprob}')
		    WHERE comprob='${comprob}'");
		if($centinela==false){ memowrite($mSQL,'itcasi'); $error=true; }

		if($error)
			echo 'Hubo algunos errores, se generaron centinelas, favor comunicarse con servicio tecnico';
		else
			echo "Cierre realizado ${comprob}";
	}

	function instalar(){
		$mSQL="CREATE TABLE IF NOT EXISTS `cplacierre` (
		  `id` int(20) unsigned NOT NULL AUTO_INCREMENT,
		  `anno` int(10) DEFAULT NULL,
		  `cuenta` varchar(250) DEFAULT NULL,
		  `descrip` varchar(250) DEFAULT NULL,
		  `monto` decimal(15,2) DEFAULT NULL,
		  PRIMARY KEY (`id`),
		  UNIQUE KEY `ac` (`anno`,`cuenta`)
		) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Cierres contables'";
		$this->db->simple_query($mSQL);
	}
}
