<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Exportar extends Controller {

	function Exportar(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->sucu=$this->datasis->traevalor('NROSUCU');
		$sucu = $this->db->escape($this->sucu);
		$this->prefijo = $this->datasis->dameval("SELECT prefijo FROM sucu WHERE codigo=$sucu");
	}

	function index(){

	}

//***********************
// Interfaces graficas
//***********************
	function ui($metodo=null){
		$obj='_'.$metodo; if(!method_exists($this,$obj)) show_404('page');
		$this->rapyd->load('dataform');

		$form = new DataForm("sincro/exportar/ui/$metodo/process");
		$form->fecha = new dateonlyField("Fecha","fecha");
		$form->fecha->insertValue = date("Y-m-d");
		$form->fecha->rule ="required|chfecha";
		$form->fecha->size =12;
		$form->submit('btnsubmit','Descargar');
		$form->build_form();

		if ($form->on_success()){
			$fecha=$form->fecha->newValue;
			$this->$obj($fecha);
			return 0;
		}

		$data['content'] = $form->output;
		$data['title']   = '<h1>Exportar data a zip ('.$metodo.')</h1>';
		$data['script']  = '';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function uig(){
		$this->rapyd->load('dataform');
		$this->datasis->modulo_id('91D',1);
		$sucu=$this->db->escape($this->sucu);

		$form = new DataForm('sincro/exportar/uig/process');

		$form->qtrae = new dropdownField('Que exportar?', 'qtrae');
		$form->qtrae->rule ='required';
		$form->qtrae->option('','Selecionar');
		$form->qtrae->option('scli'         ,'Clientes');
		$form->qtrae->option('sprv'         ,'Proveedores');
		$form->qtrae->option('sinv'         ,'Inventario');
		$form->qtrae->option('sinvprec'     ,'Inventario solo precios');
		$form->qtrae->option('maes'         ,'Inventario Supermercado');
		$form->qtrae->option('smov'         ,'Movimientos de clientes');
		$form->qtrae->option('transacciones','Facturas y transferencias');
		$form->qtrae->option('supertransa'  ,'Ventas Supermercado');
		$form->qtrae->option('rcaj'         ,'Cierres de cajas');
		$form->qtrae->option('fiscalz'      ,'Cierres Z');
		$form->qtrae->option('sfacfis'      ,'Auditoria Fiscal');

		$form->fecha = new dateonlyField('Fecha','fecha');
		$form->fecha->insertValue = date('Y-m-d');
		$form->fecha->rule ='required|chfecha';
		$form->fecha->size =12;
		$form->submit('btnsubmit','Descargar');
		$form->build_form();

		$exito='';
		if ($form->on_success()){
			$fecha=$form->fecha->newValue;
			$obj='_'.str_replace('_','',$form->qtrae->newValue);
			if(method_exists($this,$obj))
				$rt=$this->$obj($fecha);
			else
				$rt='Metodo no definido ('.$form->qtrae->newValue.')';
			if(strlen($rt)>0){
				$form->error_string=$rt;
				$form->build_form();
			}else{
				$exito='Transferencia &Eacute;xitosa';
			}
		}

		$data['content'] = $form->output.$exito;
		$data['title']   = '<h1>Exportar data de Sucursal</h1>';
		$data['script']  = '';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);

	}

//***********************
//  Interfaces uri
//***********************
	function uri($clave,$metodo,$fecha=null){
		$obj='_'.$metodo;
		if(!method_exists($this,$obj)) show_404('page');
		if($clave!=sha1($this->config->item('encryption_key'))) return false;

		/*$usr=$this->db->escape($usr);
		$pws=$this->db->escape($pws);
		$cursor=$this->db->query("SELECT us_nombre FROM usuario WHERE us_codigo=$usr AND SHA(us_clave)=$pws");
		if($cursor->num_rows()==0) return false;
		$existe = $this->datasis->dameval("SELECT COUNT(*) FROM intrasida WHERE usuario=$usr AND modulo='$id'");
		if ($existe==0 ) return  false;*/

		if (empty($fecha)){
			$dias=3;
			$fecha=date('Ymd',mktime(0,0,0,date('n'),date('j')-$dias,date('Y')));
		}elseif(!$this->__chekfecha($fecha)){
			return false;
		}

		$opcionales=array();
		for($i=7;$i<12;$i++){
			$opt=$this->uri->segment($i);
			if($opt!==false){
				$opcionales[]=$opt;
			}else break;
		}
		$this->$obj($fecha,$opcionales);
	}

//***********************
//  Metodos de Chequeo
//***********************
	function __chekfecha($fecha){
		if(is_numeric($fecha) AND $fecha>10000000){
			$anio=substr($fecha,0,4);
			$mes =substr($fecha,4,2);
			$dia =substr($fecha,6);
			if(checkdate($mes,$dia,$anio))
				return TRUE;
		}
		return FALSE;
	}

//***********************
// Metodos para exportar
//***********************
	function _sinv($fecha,$opt=null){
		set_time_limit(600);
		$dupli_sinv=array('precio1','precio2','precio3','precio4','base1','base2','base3','base4','margen1','margen2','margen3','margen4');
		$this->load->library('sqlinex');
		$data[]=array('table' => 'dpto');
		$data[]=array('table' => 'line');
		$data[]=array('table' => 'grup');
		$data[]=array('table' => 'sinv');
		$data[]=array('table' => 'marc');
		$data[]=array('table' => 'itsinv');
		$fecha=date('d-m-Y');

		$nombre='sinv_'.$fecha.'_'.$this->sucu;
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';

		$this->sqlinex->exportunbufferzip($data,$nombre,$this->sucu);
	}

	/*function _sinvprec($fecha,$opt=null){
		set_time_limit(600);
		$dupli_sinv=array('precio1','precio2','precio3','precio4','base1','base2','base3','base4','margen1','margen2','margen3','margen4');
		$this->load->library('sqlinex');
		$data[]=array('table' => 'dpto','limpiar'=>false,'ignore'=>true);
		$data[]=array('table' => 'line','limpiar'=>false,'ignore'=>true);
		$data[]=array('table' => 'grup','limpiar'=>false,'ignore'=>true);
		$data[]=array('table' => 'marc','limpiar'=>false,'ignore'=>true);
		$data[]=array('table' => 'sinv','dupli'=>$dupli_sinv,'limpiar'=>false);
		$fecha=date('d-m-Y');

		$nombre='sinv_'.$fecha.'_'.$this->sucu;
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';

		$this->sqlinex->exportunbufferzip($data,$nombre,$this->sucu);
	}*/

	function _sinvcontrol($fecha,$opt=null){
		set_time_limit(600);
		$this->load->library('sqlinex');
		$data[]=array('table' => 'sinvcontrol');
		$fecha=date('d-m-Y');

		$nombre='sinvcontrol_'.$fecha.'_'.$this->sucu;
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';

		$this->sqlinex->exportunbufferzip($data,$nombre,$this->sucu);
	}

	function _sinvprec($fecha=null,$opt=null){
		set_time_limit(600);
		$sucu   = '01';
		$sscucu =$this->db->escape($sucu);

		if ($this->db->table_exists('sinvcontrol')){
			$mSQL  = "SELECT a.codigo,a.grupo,a.descrip,a.descrip2,a.unidad,a.ubica,a.tipo,a.clave,a.comision,a.enlace,a.prov1,a.prepro1,a.pfecha1,a.prov2,a.prepro2,a.pfecha2,a.prov3,a.prepro3,a.pfecha3,a.pond,a.ultimo,a.pvp_s,a.pvp_bs,a.pvpprc,a.contbs,a.contprc,a.mayobs,a.mayoprc, 0 AS exmin, 0 AS exord,0 AS exdes,0 AS existen,a.iva,a.fracci,a.codbar,a.barras,0 AS exmax,a.margen1,a.margen2,a.margen3,a.margen4,a.base1,a.base2,a.base3,a.base4,a.precio1,a.precio2,a.precio3,a.precio4,a.serial,a.tdecimal,'N' AS activo,a.dolar,a.redecen,a.formcal,a.fordeci,a.garantia,a.costotal,a.fechac2,a.peso,a.pondcal,a.alterno,a.aumento,a.modelo,a.marca,a.clase,a.oferta,a.fdesde,a.fhasta,a.derivado,a.cantderi,a.ppos1,a.ppos2,a.ppos3,a.ppos4,a.linea,a.depto,a.gasto,a.bonifica,a.bonicant,a.standard,a.modificado,a.descufijo
			FROM sinv AS a LEFT JOIN sinvcontrol AS b ON a.codigo=b.codigo AND b.sucursal=${sscucu}
			WHERE b.precio IS NULL OR b.precio='S'";
		}else{
			$mSQL  = "SELECT a.codigo,a.grupo,a.descrip,a.descrip2,a.unidad,a.ubica,a.tipo,a.clave,a.comision,a.enlace,a.prov1,a.prepro1,a.pfecha1,a.prov2,a.prepro2,a.pfecha2,a.prov3,a.prepro3,a.pfecha3,a.pond,a.ultimo,a.pvp_s,a.pvp_bs,a.pvpprc,a.contbs,a.contprc,a.mayobs,a.mayoprc, 0 AS exmin, 0 AS exord,0 AS exdes,0 AS existen,a.iva,a.fracci,a.codbar,a.barras,0 AS exmax,a.margen1,a.margen2,a.margen3,a.margen4,a.base1,a.base2,a.base3,a.base4,a.precio1,a.precio2,a.precio3,a.precio4,a.serial,a.tdecimal,'N' AS activo,a.dolar,a.redecen,a.formcal,a.fordeci,a.garantia,a.costotal,a.fechac2,a.peso,a.pondcal,a.alterno,a.aumento,a.modelo,a.marca,a.clase,a.oferta,a.fdesde,a.fhasta,a.derivado,a.cantderi,a.ppos1,a.ppos2,a.ppos3,a.ppos4,a.linea,a.depto,a.gasto,a.bonifica,a.bonicant,a.standard,a.modificado,a.descufijo
			FROM sinv AS a";
		}

		$nombre = tempnam('/tmp', 'sinvprec');
		$handle = fopen($nombre, 'w');
		$sql    = '';

		if($this->db->dbdriver=='mysqli'){
			$query= mysqli_query($this->db->conn_id, $mSQL, MYSQLI_USE_RESULT);
			$ff   = 'mysqli_fetch_assoc';
			$fl   = 'mysqli_free_result';
		}else{
			$query= mysql_unbuffered_query($mSQL,$this->db->conn_id);
			$ff   = 'mysql_fetch_assoc';
			$fl   = 'mysql_free_result';
		}

		if ($query!==false){
			while ($row = $ff($query)) {

				$base1=(empty($row['base1']))? 1 : $row['base1'];
				$base2=(empty($row['base2']))? 1 : $row['base2'];
				$base3=(empty($row['base3']))? 1 : $row['base3'];
				$base4=(empty($row['base4']))? 1 : $row['base4'];

				$sql = $this->db->insert_string('sinv', $row);
				$sql.=' ON DUPLICATE KEY UPDATE ';
				$sql.=' `alterno` ='.$this->db->escape($row['alterno']);
				$sql.=',`peso`    ='.$this->db->escape($row['peso']);
				$sql.=',`clase`   ='.$this->db->escape($row['clase']);
				$sql.=',`redecen` ='.$this->db->escape($row['redecen']);
				$sql.=',`precio1` ='.$this->db->escape($row['precio1']);
				$sql.=',`precio2` ='.$this->db->escape($row['precio2']);
				$sql.=',`precio3` ='.$this->db->escape($row['precio3']);
				$sql.=',`precio4` ='.$this->db->escape($row['precio4']);
				$sql.=',`iva`     ='.$this->db->escape($row['iva']);
				$sql.=',`base1`   ='.$this->db->escape($row['base1']);
				$sql.=',`base2`   ='.$this->db->escape($row['base2']);
				$sql.=',`base3`   ='.$this->db->escape($row['base3']);
				$sql.=',`base4`   ='.$this->db->escape($row['base4']);
				$sql.=',`grupo`   ='.$this->db->escape($row['grupo']);
				$sql.=',`linea`   ='.$this->db->escape($row['linea']);
				$sql.=',`depto`   ='.$this->db->escape($row['depto']);
				$sql.=',`descrip` ='.$this->db->escape($row['descrip']);
				//$sql.=',`descrip2`='.$this->db->escape($row['descrip2']);
				$sql.=',`margen1` = ROUND(100-((IF(formcal=\'U\',ultimo,IF(formcal=\'P\',pond,IF(formcal=\'S\',standard,GREATEST(ultimo,pond)))))*100/('.$base1.')),2)';
				$sql.=',`margen2` = ROUND(100-((IF(formcal=\'U\',ultimo,IF(formcal=\'P\',pond,IF(formcal=\'S\',standard,GREATEST(ultimo,pond)))))*100/('.$base2.')),2)';
				$sql.=',`margen3` = ROUND(100-((IF(formcal=\'U\',ultimo,IF(formcal=\'P\',pond,IF(formcal=\'S\',standard,GREATEST(ultimo,pond)))))*100/('.$base3.')),2)';
				$sql.=',`margen4` = ROUND(100-((IF(formcal=\'U\',ultimo,IF(formcal=\'P\',pond,IF(formcal=\'S\',standard,GREATEST(ultimo,pond)))))*100/('.$base4.')),2)';
				$sql.=',`marca`   ='.$this->db->escape($row['marca']);
				$sql.="\n";
				fwrite($handle, $sql);
			}
			$fl($query);
		}

		$ttables=array('grup','line','dpto');
		foreach($ttables AS $ttable){
			$mSQL="SELECT * FROM ${ttable}";
			if($this->db->dbdriver=='mysqli'){
				$query= mysqli_query($this->db->conn_id, $mSQL, MYSQLI_USE_RESULT);
			}else{
				$query= mysql_unbuffered_query($mSQL,$this->db->conn_id);
			}

			if ($query!==false){
				while ($row = $ff($query)) {
					$sql = $this->db->insert_string($ttable, $row);
					$sql = str_replace('INSERT ','INSERT IGNORE ',$sql);
					$sql.="\n";

					fwrite($handle, $sql);
				}
				$fl($query);
			}
		}

		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';
		fclose($handle);
		$firma=md5_file($nombre);
		$this->load->library('encrypt');
		$firma=$this->encrypt->encode($this->sucu.'-#-'.$firma);
		$this->load->library('zip');
		$this->zip->add_data('firma.txt',$firma);
		$this->zip->read_file($nombre);
		$this->zip->download('ssinvpre.zip');
		unlink($nombre);
	}

	function _datacenter($fecha,$opt=null){
		set_time_limit(600);
		$this->load->library('sqlinex');

		$data[]=array('select' =>'tipo_doc,numero,fecha,vence,vd,cod_cli,rifci,nombre,direc,dire1,orden,referen,iva,inicial,totals,totalg,status,observa,observ1,devolu,cajero,almacen,peso,factura,pedido,usuario,estampa,hora,transac,nfiscal,zona,ciudad,comision,pagada,sepago,dias,fpago,comical,exento,tasa,reducida,sobretasa,montasa,monredu,monadic,notcred,fentrega,fpagom,fdespacha,udespacha,numarma,maqfiscal,dmaqfiscal',
				'distinc'=>false,
				'ignore' =>true,
				'limpiar'=>false,
				'table'  =>'sfac',
				'where'  =>"fecha >= ${fecha}");
		$data[]=array('select' =>'tipoa,numa,codigoa,desca,cana,preca,tota,iva,fecha,vendedor,costo,pos,pvp,comision,cajero,mostrado,usuario,estampa,hora,transac,despacha,flote,precio4,detalle,fdespacha,udespacha,combo,descuento',
				'distinc'=>false,
				'ignore' =>true,
				'limpiar'=>false,
				'table'  =>'sitems',
				'where'  =>"fecha >= ${fecha}");
		$data[]=array('select' =>'tipo_doc,numero,tipo,monto,num_ref,clave,fecha,banco,f_factura,cod_cli,vendedor,cobrador,status,cobro,cambio,almacen,transac,usuario,estampa,hora',
				'distinc'=>false,
				'ignore' =>true,
				'limpiar'=>false,
				'table'  =>'sfpa',
				'where'  =>"fecha >= ${fecha}");
		$data[]=array('table' => 'smov',
				'ignore' =>true,
				'where' => "estampa >= ${fecha}");
		$data[]=array('table' => 'costos',
				'ignore' =>false,
				'limpiar'=>true,
				'where'  =>"fecha >=  DATE_SUB(CURDATE(),INTERVAL 365 DAY)");
		$data[]=array('table' => 'sinv',
				'select' =>'codigo,grupo,descrip,descrip2,unidad,ubica,tipo,clave,comision,enlace,prov1,prepro1,pfecha1,prov2,prepro2,pfecha2,prov3,prepro3,pfecha3,pond,ultimo,pvp_s,pvp_bs,pvpprc,contbs,contprc,mayobs,mayoprc,exmin,exord,exdes,existen,fechav,fechac,iva,fracci,codbar,barras,exmax,margen1,margen2,margen3,margen4,base1,base2,base3,base4,precio1,precio2,precio3,precio4,serial,tdecimal,activo,dolar,redecen,formcal,fordeci,garantia,costotal,fechac2,peso,pondcal,alterno,aumento,modelo,marca,clase,oferta,fdesde,fhasta,derivado,cantderi,linea,depto,id,gasto,bonifica,bonicant,standard,descufijo',
				'limpiar'=>true);

		$nombre='ve_'.$fecha.'_'.$this->sucu;
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';
		$this->sqlinex->exportunbufferzip($data,$nombre,$this->sucu);
	}

	function _datacentersinv($fecha,$opt=null){
		set_time_limit(600);
		$this->load->library('sqlinex');

		$data[]=array('table' => 'sinv',
				'select' =>'codigo,grupo,descrip,descrip2,unidad,ubica,tipo,clave,comision,enlace,prov1,prepro1,pfecha1,prov2,prepro2,pfecha2,prov3,prepro3,pfecha3,pond,ultimo,pvp_s,pvp_bs,pvpprc,contbs,contprc,mayobs,mayoprc,exmin,exord,exdes,existen,fechav,fechac,iva,fracci,codbar,barras,exmax,margen1,margen2,margen3,margen4,base1,base2,base3,base4,precio1,precio2,precio3,precio4,serial,tdecimal,activo,dolar,redecen,formcal,fordeci,garantia,costotal,fechac2,peso,pondcal,alterno,aumento,modelo,marca,clase,oferta,fdesde,fhasta,derivado,cantderi,linea,depto,id,gasto,bonifica,bonicant,standard,descufijo',
				'limpiar'=>true);

		$nombre='sinv_'.$fecha.'_'.$this->sucu;
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';
		$this->sqlinex->exportunbufferzip($data,$nombre,$this->sucu);
	}

	function _datacentercostos($fecha,$opt=null){
		set_time_limit(600);
		$this->load->library('sqlinex');

		$data[]=array('table' => 'costos',
				'ignore' =>false,
				'limpiar'=>true);

		$nombre='costos_'.$fecha.'_'.$this->sucu;
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';
		$this->sqlinex->exportunbufferzip($data,$nombre,$this->sucu);
	}

	function _transacciones($fecha,$opt=null){
		set_time_limit(600);
		$this->load->library('sqlinex');

		$sucu     = $this->db->escape($this->sucu);
		$pre_caja = $this->datasis->dameval("SELECT prefijo FROM sucu WHERE codigo=$sucu");
		$cant     = strlen($pre_caja);
		$pre_caja = $this->db->escape($pre_caja);


		$data[]=array('select' =>'tipo_doc,numero,fecha,vence,vd,cod_cli,rifci,nombre,direc,dire1,orden,referen,iva,inicial,totals,totalg,status,observa,observ1,devolu,cajero,almacen,peso,factura,pedido,usuario,estampa,hora,transac,nfiscal,zona,ciudad,comision,pagada,sepago,dias,fpago,comical,exento,tasa,reducida,sobretasa,montasa,monredu,monadic,notcred,fentrega,fpagom,fdespacha,udespacha,numarma,maqfiscal,dmaqfiscal',
				'distinc'=>false,
				'table'  =>'sfac',
				'where'  =>"fecha >= $fecha AND MID(numero,1,$cant)=$pre_caja");
		$data[]=array('select' =>'tipoa,numa,codigoa,desca,cana,preca,tota,iva,fecha,vendedor,costo,pos,pvp,comision,cajero,mostrado,usuario,estampa,hora,transac,despacha,flote,precio4,detalle,fdespacha,udespacha,combo,descuento',
				'distinc'=>false,
				'table'  =>'sitems',
				'where'  =>"fecha >= $fecha AND MID(numa  ,1,$cant)=$pre_caja");
		$data[]=array('select' =>'tipo_doc,numero,tipo,monto,num_ref,clave,fecha,banco,f_factura,cod_cli,vendedor,cobrador,status,cobro,cambio,almacen,transac,usuario,estampa,hora',
				'distinc'=>false,
				'table'  =>'sfpa',
				//'where'  =>"fecha = $fecha AND MID(numero,1,$cant)=$pre_caja AND tipo_doc IN ('FE','DE','AN')");
				'where'  =>"fecha >= $fecha AND MID(transac,1,$cant)=$pre_caja");
		/*$data[]=array('distinc'=>false,
				'table'  =>'fiscalz',
				'where'  =>"fecha >= $fecha");*/

		$data[]=array('select'=>'numero,fecha,envia,recibe,observ1,observ2,totalg,tratot,estampa,hora,usuario,transac,gasto,numeen,numere',
				'distinc'=>false,
				'table'  =>'stra',
				'where'  =>"fecha >= $fecha AND MID(numero,1,$cant)=$pre_caja");
		$data[]=array('select' =>'itstra.numero,itstra.codigo,itstra.descrip,itstra.cantidad,itstra.precio1,itstra.precio2,itstra.precio3,itstra.precio4,itstra.iva,itstra.anteri,itstra.costo',
				'distinc'=>false,
				'table'  =>'itstra',
				'where'  =>"MID(numero,1,$cant)=$pre_caja");
		$data[]=array(
			'distinc'   =>true,
			'select'    =>'itccli.numccli, itccli.tipoccli, itccli.cod_cli, itccli.tipo_doc, itccli.numero, itccli.fecha, itccli.monto, itccli.abono, itccli.ppago, itccli.reten, itccli.cambio, itccli.mora, itccli.transac, itccli.estampa, itccli.hora, itccli.usuario, itccli.reteiva, itccli.nroriva, itccli.emiriva, itccli.recriva',
			'table'     =>'itccli',
			'join'      =>array(
					0 => array(
						'table'=>'smov',
						'on'=>'smov.transac=itccli.transac'),
					1 => array(
						'table'=>'sfpa',
						'on'=>'sfpa.transac=itccli.transac')
					),
			'where'   =>"itccli.fecha>=$fecha AND MID(itccli.transac,1,$cant)='".$this->prefijo."'");
		$data[]=array('table' => 'smov',
				'where' => "estampa >= $fecha AND MID(transac,1,$cant)='".$this->prefijo."'"
		);

		$nombre='ve_'.$fecha.'_'.$this->sucu;
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';
		$this->sqlinex->exportunbufferzip($data,$nombre,$this->sucu);
	}

	function _scli($fecha,$opt=null){
		set_time_limit(600);
		$this->load->library('sqlinex');

		$data[]=array('select' => 'cliente,nombre,grupo,gr_desc,nit,cuenta,formap,tipo,limite,socio,contacto,dire11,dire12,ciudad1,dire21,dire22,ciudad2,telefono,telefon2,zona,pais,email,vendedor,porvend,cobrador,porcobr,repre,cirepre,ciudad,separa,copias,regimen,comisio,porcomi,rifci,observa,fecha1,fecha2,tiva,clave,nomfis,riffis,mensaje,modificado',
				'table'  =>'scli',
				'where'  =>"modificado>=$fecha",
				'limpiar'=>false,
				'ignore' =>true);

		$data[]=array('table' => 'grcl',
				'limpiar'=> false,
				'ignore' =>true);

		$nombre='scli_'.$fecha.'_'.$this->sucu;
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';
		$this->sqlinex->exportunbufferzip($data,$nombre,$this->sucu);
	}

	function _scliser($fecha,$opt=null){
		set_time_limit(600);
		$this->load->library('sqlinex');

		$data[]=array('select' => 'cliente,nombre,grupo,gr_desc,nit,cuenta,formap,tipo,limite,socio,contacto,dire11,dire12,ciudad1,dire21,dire22,ciudad2,telefono,telefon2,zona,pais,email,vendedor,porvend,cobrador,porcobr,repre,cirepre,ciudad,separa,copias,regimen,comisio,porcomi,rifci,observa,fecha1,fecha2,tiva,clave,nomfis,riffis,mensaje,modificado,upago',
				'table'  =>'scli',
				'where'  =>"modificado>=$fecha",
				'limpiar'=>false,
				'ignore' =>true);

		$data[]=array('table' => 'grcl',
				'limpiar'=> false,
				'ignore' =>true);

		$nombre='scli_'.$fecha.'_'.$this->sucu;
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';
		$this->sqlinex->exportunbufferzip($data,$nombre,$this->sucu);
	}


	function _sclilimit($fecha,$opt=null){
		set_time_limit(600);
		$this->load->library('sqlinex');

		$data[]=array('select' => 'cliente,nombre,grupo,gr_desc,nit,cuenta,formap,tipo,0 AS limite,socio,contacto,dire11,dire12,ciudad1,dire21,dire22,ciudad2,telefono,telefon2,zona,pais,email,vendedor,porvend,cobrador,porcobr,repre,cirepre,ciudad,separa,copias,regimen,comisio,porcomi,rifci,observa,fecha1,fecha2,tiva,clave,nomfis,riffis,mensaje,modificado',
				'table'  =>'scli',
				'where'  =>"modificado>=$fecha",
				'limpiar'=>false,
				'dupli'  =>array('nombre','grupo','gr_desc','nit','tipo','dire11','dire12','dire21','dire22','nomfis','riffis','telefono','email','ciudad','modificado'),
				'ignore' =>false);

		$data[]=array('table' => 'grcl',
				'limpiar'=> false,
				'ignore' =>true);

		$nombre='scli_'.$fecha.'_'.$this->sucu;
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';
		$this->sqlinex->exportunbufferzip($data,$nombre,$this->sucu);
	}

	function _smov($fecha,$opt=null){
		set_time_limit(600);
		$this->load->library('sqlinex');
		$cant = strlen($this->prefijo);

		$data[]=array(
			'distinc'   =>true,
			'select'    =>'itccli.numccli, itccli.tipoccli, itccli.cod_cli, itccli.tipo_doc, itccli.numero, itccli.fecha, itccli.monto, itccli.abono, itccli.ppago, itccli.reten, itccli.cambio, itccli.mora, itccli.transac, itccli.estampa, itccli.hora, itccli.usuario, itccli.reteiva, itccli.nroriva, itccli.emiriva, itccli.recriva',
			'table'     =>'itccli',
			'join'      =>array(
					0 => array(
						'table'=>'smov',
						'on'=>'smov.transac=itccli.transac'),
					1 => array(
						'table'=>'sfpa',
						'on'=>'sfpa.transac=itccli.transac')
					),
			'where'   =>"itccli.fecha=$fecha AND MID(itccli.transac,1,$cant)='".$this->prefijo."'"
		);

		$data[]=array(
			'distinc'   =>true,
			'select'    =>'smov.*',
			'table'     =>'smov',
			'join'      =>array(
					0 => array(
						'table'=>'itccli',
						'on'=>'itccli.transac=smov.transac'),
					1 => array(
						'table'=>'sfpa',
						'on'=>'sfpa.transac=smov.transac')
					),
			'where'   =>"smov.fecha=$fecha AND MID(smov.transac,1,$cant)='".$this->prefijo."'"
			//'wherejoin' =>"ittran.recibe=$dbalma"
		);

		$nombre='smov_'.$fecha.'_'.$this->sucu;
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';
		$this->sqlinex->exportunbufferzip($data,$nombre,$this->sucu);
	}

	function _fiscalz($fecha,$opt=null){
		set_time_limit(600);
		$this->load->library('sqlinex');
		//$this->sqlinex->ignore   =TRUE;

		$data[]=array('table' => 'fiscalz',
				'where'  => "fecha >= $fecha",
				'limpiar'=>false,
				'ignore' =>true);

		$nombre='fiscalz_'.$fecha.'_'.$this->sucu;
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';
		$this->sqlinex->exportunbufferzip($data,$nombre,$this->sucu);
	}

	function _sfacfis($fecha,$opt=null){
		set_time_limit(600);
		$this->load->library('sqlinex');
		//$this->sqlinex->ignore   =TRUE;

		$data[]=array('table' => 'sfacfis',
				'where'  => "fecha >= $fecha",
				'limpiar'=>false,
				'ignore' =>true);

		$nombre='sfacfis_'.$fecha.'_'.$this->sucu;
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';
		$this->sqlinex->exportunbufferzip($data,$nombre,$this->sucu);
	}

	//Para supermercado
	function _supertransa($fecha,$opt=null){
		set_time_limit(600);
		$this->load->library('sqlinex');

		$sucu=$this->datasis->traevalor('NROSUCU');
		$pre_caja=$this->prefijo;
		$cant=strlen($pre_caja);
		$pre_caja=$this->db->escape($this->prefijo);

		$this->load->library('sqlinex');

		$data[]=array('distinc'=>false,
		              'table'  =>'viefac',
		              'where'  =>"fecha = $fecha AND MID(caja,1,$cant)=$pre_caja");
		$data[]=array('distinc'=>false,
		              'table'  =>'vieite',
		              'where'  =>"fecha = $fecha AND MID(caja,1,$cant)=$pre_caja");
		$data[]=array('distinc'=>false,
		              'table'  =>'viepag',
		              'where'  =>"f_factura = $fecha AND MID(caja,1,$cant)=$pre_caja");
		$data[]=array('distinc'=>false,
		              'table'  =>'fiscalz',
		              'where'  =>"fecha = $fecha AND MID(caja,1,$cant)=$pre_caja");

		$nombre='supertransa_'.$fecha.'_'.$this->sucu;
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';
		$this->sqlinex->exportunbufferzip($data,$nombre,$this->sucu);
	}

	function _maes($fecha,$opt=null){
		$this->load->library('sqlinex');
		$data[]=array('table'  =>'dpto');
		$data[]=array('table'  =>'fami');
		$data[]=array('table'  =>'grup');
		$data[]=array('table'  =>'maes');
		$data[]=array('table'  =>'ubica');
		$fecha=date('d-m-Y');

		$nombre='maes_'.$fecha.'_'.$this->sucu;
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';
		$this->sqlinex->exportunbufferzip($data,$nombre,$this->sucu);
	}

	function _rcaj($fecha,$opt=null){
		set_time_limit(600);
		//$prefijo=str_pad($this->prefijo,8,'0');
		$prefijo=$this->prefijo;
		$cant=strlen($this->prefijo)+1;
		$this->load->library('sqlinex');
		//$this->sqlinex->ignore   =TRUE;
		$data[]=array('table' => 'rcaj',
		                'select'=>"fecha,cajero,tipo,usuario,caja,recibido,ingreso,parcial,observa, CONCAT('$prefijo',MID(numero,$cant)) AS numero ,transac,estampa,hora",
		                'where' => "fecha >= $fecha",
		                'limpiar'=>false,
		                'ignore' =>true);

		$nombre='rcaj_'.$fecha.'_'.$this->sucu;
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';
		$this->sqlinex->exportunbufferzip($data,$nombre,$this->sucu);
	}

	function _sprv($fecha,$opt=null){
		set_time_limit(600);
		//$prefijo=str_pad($this->prefijo,8,'0');
		$prefijo=$this->prefijo;
		$cant=strlen($this->prefijo)+1;
		$this->load->library('sqlinex');
		//$this->sqlinex->ignore   =TRUE;
		$data[]=array('table' => 'sprv',
		                'select'=>"proveed,nombre,rif,tipo,grupo,cuenta,gr_desc,direc1,direc2,direc3,telefono,contacto,cliente,observa,nit,codigo,email,url,banco1,cuenta1,banco2,cuenta2,tiva,nomfis,reteiva,modificado",
		                'where' => " modificado >= $fecha",
		                'limpiar'=>false,
		                'ignore' =>true);

		if($this->db->table_exists('sinvprov')){
			$data[]=array('table' => 'sinvprov',
			        'limpiar'=>false,
			        'ignore' =>true);
		}

		$nombre='sprv_'.$fecha.'_'.$this->sucu;
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';
		$this->sqlinex->exportunbufferzip($data,$nombre,$this->sucu);
	}
//***********************
// Metodos dependientes
//     del almacen
//***********************
	function _maesalma($fecha,$opt=null){
		if(empty($opt)) return false;
		$this->load->library('sqlinex');
		$dbalma=$this->db->escape($opt[0]);
		$data[]=array('table'  =>'dpto');
		$data[]=array('table'  =>'fami');
		$data[]=array('table'  =>'grup');
		$data[]=array(
			'distinc'   =>true,
			'select'    =>'maes.*',
			'table'     =>'maes',
			'join'      =>array(0 => array('table'=>'ittran','on'=>'ittran.codigo=maes.codigo')),
			//'wherejoin' =>"ittran.fecha>=$fecha AND ittran.recibe=$dbalma"
			'wherejoin' =>"ittran.recibe=$dbalma"
		);
		$fecha=date('d-m-Y');

		$nombre='maesalma_'.$opt[0].'_'.$fecha.'_'.$this->sucu;
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';
		$this->sqlinex->exportunbufferzip($data,$nombre,$this->sucu);
	}

	function _tranalma($fecha,$opt=null){
		if(empty($opt)) return false;
		$this->load->library("sqlinex");
		//$this->sqlinex->ignore   =TRUE;

		$dbalma=$this->db->escape($opt[0]);
		$data[]=array('table'  =>'ittran',
			'where'=>"recibe=$dbalma AND fecha>=$fecha",
			'limpiar'=>false,
			'ignore' =>true
		);
		$data[]=array('table'  =>'tran',
			'where'=>"recibe=$dbalma AND fecha>=$fecha",
			'limpiar'=>false,
			'ignore' =>true
		);

		$nombre='tranalma_'.$opt[0].'_'.$fecha.'_'.$this->sucu;
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';
		$this->sqlinex->exportunbufferzip($data,$nombre,$this->sucu);
	}

	function _ubicalma($fecha,$opt=null){
		if(empty($opt)) return false;
		$this->load->library("sqlinex");
		//$this->sqlinex->ignore   =TRUE;

		$dbalma=$this->db->escape($opt[0]);
		$data[]=array('table'  =>'ubic',
			'where' =>"ubica=$dbalma",
			'ignore'=>true
		);

		$nombre='ubicalma_'.$opt[0].'_'.$fecha.'_'.$this->sucu;
		if(!array_key_exists('HTTP_USER_AGENT', $_SERVER)) $_SERVER['HTTP_USER_AGENT']='curl';
		$this->sqlinex->exportunbufferzip($data,$nombre,$this->sucu);
	}
}
