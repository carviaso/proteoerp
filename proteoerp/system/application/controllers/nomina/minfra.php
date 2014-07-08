<?php
class Minfra extends Controller {

	function minfra(){
		parent::Controller();
		//$this->load->library('rapyd');
		$this->load->helper('download');
		$this->load->helper('file');
	}

	function index(){
		redirect ('nomina/minfra/crear');
	}

	function crear($fechad='',$fechah=''){
		$this->load->dbutil();
		if(empty($fechad) || empty($fechah)){
			$dbfechad=$this->db->escape($fechad);
			$dbfechah=$this->db->escape($fechah);
			$mSQL="SELECT nombre,apellido,nacional,cedula,sexo,DATE_FORMAT(nacimi,'%d%m%Y') as nacimi,cargo,IF(status='A',DATE_FORMAT(ingreso,'%d%m%Y'),DATE_FORMAT(retiro,'%d%m%Y'))as ingreso,status,IF(tipo='B',sueldo*2,IF(tipo='Q',sueldo*2,IF(tipo='M',sueldo*1,IF(tipo='S',sueldo*4,sueldo*0))))AS sueldo,contrato FROM pers WHERE retiro>=$dbfechad AND retiro<=$dbfechah OR status='A'";
			$query=$this->db->query($mSQL);
		}
		$line='1ER_NOMBRE;2DO_NOMBRE;1ER_APELLIDO;2DO_APELLIDO;NACIONALIDAD;CEDULA;SEXO;FECHA_NACIMIENTO;CARGO;TIPO_TRABAJADOR;FECHA_INGRESO;ESTADO_EMPLEADO;SALARIO';
		$line.="\r\n";
		foreach($query->result_array() as $row){
			$temp=preg_replace('/\s\s+/', ' ', trim($row['nombre']));
			$temp=str_replace('¥','&ntilde;',$temp);
			$temp=explode(' ',$temp);
			$ban =true;
			$nombre1=$nombre2='';
			foreach($temp AS $token){
				if($ban){
					if (preg_match("/^([lL][aeoAEO][sS]|[Dd][aieoAIEO]|[dD]?[eE][lL]|[lL][aA])$/",$token)>0){
						$nombre1.=$token.' ';
					}else{
						$nombre1.=$token;
						$ban=false;
					}
				}else{
					$nombre2.=$token.' ';
				}
			}
			$line.=rtrim($nombre1).';'.rtrim($nombre2).';';

			$temp=preg_replace('/\s\s+/', ' ', trim($row['apellido']));
			$temp=str_replace('¥','&ntilde;',$temp);
			$temp=explode(' ',$temp);
			$ban=true;
			$nombre1=$nombre2='';
			foreach($temp AS $token){
				if($ban){
					if (preg_match("/^([lL][aeoAEO][sS]|[Dd][aieoAIEO]|[dD]?[eE][lL]|[lL][aA])$/",$token)>0){
						$nombre1.=$token.' ';
					}else{
						$nombre1.=$token;
						$ban=false;
					}
				}else{
					$nombre2.=$token.' ';
				}
			}
			$line.=rtrim($nombre1).';'.rtrim($nombre2).';';

			//$temp=split(' ',trim($row["nombre"]));
			//if(count($temp)==1)$line.=$temp[0].";;";
			//if(count($temp)==2)$line.=$temp[0].";".$temp[1].";";

			//$temp=split(' ',trim($row["apellido"]));
			//if(count($temp)==1)$line.=$temp[0].";;";
			//if(count($temp)==2)$line.=$temp[0].";".$temp[1].";";

			if(trim($row['nacional'])=='V')$line.='1;';
			if(trim($row['nacional'])=='E')$line.='2;';

			$line.=trim($row['cedula']).';';

			if(trim($row['sexo'])=='M')$line.='1;';
			if(trim($row['sexo'])=='F')$line.='2;';

			$line.=$row['nacimi'].';';

			$temp = $this->db->escape(trim($row['cargo']));
			$cargo= $this->datasis->dameval("SELECT descrip FROM carg WHERE cargo=$temp");
			$carg = str_replace(' ','',$cargo);
			$carg1= str_replace('¥','&ntilde;',$carg);
			$line.= $carg1.';';

			$temp=$this->db->escape($row['contrato']);
			$temp=$this->datasis->dameval("SELECT tipo FROM noco WHERE codigo=$temp");
			if((trim($temp)=='Q')||(trim($temp)=='M'))$line.='1;';
			if((trim($temp)=='S')||(trim($temp)=='B'))$line.='2;';

			$line.=$row['ingreso'].';';
			if(trim($row['status'])=='R') $line.='2;'; else $line.='1;';
			$line.=number_format($row['sueldo'],2,'','');
			$line.="\r\n";
		}

		$name = 'MINFRA.txt';
		force_download($name,$line);
	}

	function faovtxt($fechad='',$fechah=''){
		$this->load->dbutil();
		$dbfechad=$this->db->escape($fechad);
		$dbfechah=$this->db->escape($fechah);

		$mSQL="SELECT a.codigo, a.concepto, a.monto, SUM(a.valor*(a.tipo='A' AND MID(a.concepto,1,1)<>'9')) asignacion,
		 SUM(a.valor*(a.concepto IN ('620', '621' ))) retencion,
		 SUM(a.valor*(a.concepto IN ('920', '921' ))) aporte,
		 SUM(a.valor*(a.concepto IN ('620', '621' ))) +SUM(a.valor*(a.concepto IN ('920', '921' ))) as total,
		 CONCAT(RTRIM(b.nombre), ' ',RTRIM(b.apellido)) nombre, c.descrip,
		 a.fecha, a.contrato, d.nombre contnom, b.sexo,
		 b.nacional,b.cedula,b.nombre,b.apellido,b.sueldo,DATE_FORMAT(b.ingreso,'%d%m%Y')AS ingreso,DATE_FORMAT(b.retiro,'%d%m%Y')AS retiro
		 FROM (nomina a) JOIN pers as b ON a.codigo=b.codigo
		 JOIN conc as c ON a.concepto=c.concepto
		 LEFT JOIN noco d ON a.contrato=d.codigo
		 WHERE a.valor<>0 AND a.fecha >= $dbfechad AND a.fecha <= $dbfechah
		 GROUP BY EXTRACT( YEAR_MONTH FROM a.fecha ), a.codigo
		 HAVING retencion<>0";

		$query=$this->db->query($mSQL);
		$line=$error='';
		//$line='NACIONAL;CEDULA;1ER_NOMBRE;2DO_NOMBRE;1ER_APELLIDO;2DO_APELLIDO;SALARIO;INGRESO;RETIRO';
		//$line.="\r\n";
		if ($query->num_rows() > 0){
			$rem=array('.','-');
			foreach($query->result_array() as $row){
				$line.=trim($row['nacional']).',';
				$line.=trim($row['cedula']).',';

				$temp=preg_replace('/\s\s+/', ' ', trim($row['nombre']));

				$temp=str_replace('¥','&ntilde;',$temp);
				$temp=explode(' ',$temp);
				$ban=true;
				$nombre1=$nombre2='';
				foreach($temp AS $token){
					if($ban){
						if (preg_match("/^([lL][aeoAEO][sS]|[Dd][aieoAIEO]|[dD]?[eE][lL]|[lL][aA])$/",$token)>0){
							$nombre1.=$token.' ';
						}else{
							$nombre1.=$token;
							$ban=false;
						}
					}else{
						$nombre2.=$token.' ';
					}
				}
				$line.=trim($nombre1).','.trim($nombre2).',';

				$temp=preg_replace('/\s\s+/', ' ', trim($row['apellido']));
				$temp=str_replace('¥','&ntilde;',$temp);
				$temp=explode(' ',$temp);
				$ban=true;
				$nombre1=$nombre2='';
				foreach($temp AS $token){
					if($ban){
						if(preg_match("/^([lL][aeoAEO][sS]|[Dd][aieoAIEO]|[dD]?[eE][lL]|[lL][aA])$/",$token)>0){
							$nombre1.=$token.' ';
						}else{
							$nombre1.=$token;
							$ban=false;
						}
					}else{
						$nombre2.=$token.' ';
					}
				}
				$line.=rtrim($nombre1).','.rtrim($nombre2).',';
				$line.=number_format($row['asignacion'],2,'','').",";
				$line.=$row['ingreso'].',';
				if ($row['retiro'] != '00000000' )
				    $line.=$row['retiro'];
				$line.="\r\n";
			}
			//$line.= $this->datasis->traevalor('CODIGOFAOV').substr($fechad,4,2).substr($fechad,0,4);
			//$line.="\r\n";
		}
		$name = $this->datasis->traevalor('CODIGOFAOV').substr($fechad,4,2).substr($fechad,0,4).'.txt';
		force_download($name,$line);
	}



	function islrtxt($fechad='',$fechah=''){
		$this->load->dbutil();
		$dbfechad=$this->db->escape($fechad);
		$dbfechah=$this->db->escape($fechah);

		$mSQL="SELECT a.codigo, a.monto,
			SUM(a.valor*(a.concepto IN ('010' ))) sueldo,
			SUM(a.valor*(a.concepto IN ('920' ))) retencion,
			'N/A' control, 0.00 reten,
			a.fecha, a.contrato, d.nombre contnom, '0000000000' factura, '001' codcon,
			CONCAT(b.nacional,b.cedula) cedula , 0 AS ingreso,DATE_FORMAT(b.retiro,'%d%m%Y')AS retiro
		FROM (nomina a) JOIN pers as b ON a.codigo=b.codigo
			JOIN conc as c ON a.concepto=c.concepto
			LEFT JOIN noco d ON a.contrato=d.codigo
		WHERE a.valor<>0 AND a.fecha >= $dbfechad AND a.fecha <= $dbfechah
		GROUP BY EXTRACT( YEAR_MONTH FROM a.fecha ), a.codigo";

		$query=$this->db->query($mSQL);
		$line=$error='';
		if ($query->num_rows() > 0){

			$line .= '<?xml version="1.0" encoding="ISO-8859-1"?>';
			$line .= "\r\n";
			$line .= '<RelacionRetencionesISLR RifAgente="'.$this->datasis->traevalor('RIF').'" Periodo="'.substr($fechad,0,6).'">';
			$line .= "\r\n";

			$rem=array('.','-');
			foreach($query->result_array() as $row){
				$line .= "\t".'<DetalleRetencion>'."\r\n";
				$line .= "\t\t".'<RifRetenido>'.$row['cedula'].'</RifRetenido>'."\r\n";
				$line .= "\t\t".'<NumeroFactura>'.$row['factura'].'</NumeroFactura>'."\r\n";
				$line .= "\t\t".'<NumeroControl>'.$row['control'].'</NumeroControl>'."\r\n";
				$line .= "\t\t".'<CodigoConcepto>'.$row['codcon'].'</CodigoConcepto>'."\r\n";
				$line .= "\t\t".'<MontoOperacion>'.$row['sueldo'].'</MontoOperacion>'."\r\n";
				$line .= "\t\t".'<PorcentajeRetencion>'.$row['reten'].'</PorcentajeRetencion>'."\r\n";
				$line .= "\t".'<DetalleRetencion>'."\r\n";
			}
		}
		$name = 'relislr.txt';
		force_download($name,$line);
	}


}
