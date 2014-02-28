<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
//**********************************
// Estadisticas a usar para minimos
//**********************************
function promediot($param){
	if(is_array($param)){
		$data=$param;
	}else{
		$data= func_get_args();
	}

	$valor=array_sum($data)-min($data)-max($data);
	return ceil($valor/(count($data)-2));
}

function promedio($param){
	if(is_array($param)){
		$data=$param;
	}else{
		$data= func_get_args();
	}

	$valor=array_sum($data);
	return ceil($valor/count($data));
}

function maximo($param){
	if(is_array($param)){
		$data=$param;
	}else{
		$data= func_get_args();
	}
	return max($data);
}

function minimo($param){
	if(is_array($param)){
		$data=$param;
	}else{
		$data= func_get_args();
	}
	return min($data);
}

function mediana($param){
	if(is_array($param)){
		$data=$param;
	}else{
		$data= func_get_args();
	}
	sort($data);
	$cana = count($data);
	$ind  = $cana/2;
	if($cana % 2 == 0){
		$rt=($data[$ind]+$data[$ind-1])/2;
	}else{
		$rt=$data[floor($ind)];
	}
	return $rt;
}

//**********************************
// Estadisticas a usar para maximos
//**********************************
function max_dupli($min){
	return $min*2;
}
//Fin de las estadisticas

function divisor($divide,$divisor=1){
	return ceil($divide/$divisor);
}

class Comparativo extends Controller {
	var $id='321';

	function Comparativo(){
		parent::Controller();
		$this->load->library('rapyd');
	}

	function index(){
		redirect('inventario/comparativo/filteredgrid');
	}

	function filteredgrid(){
		$this->datasis->modulo_id($this->id,1);
		if($this->input->post('btn_cambio_2')!==false){
			unset($_POST['btn_cambio_2']);
			$_POST['btn_submit']='a';
			$_cambiomin=true;
		}else{
			$_cambiomin=false;
		}

		$this->rapyd->load("datafilter2","datagrid");
		$mSPRV=array(
				'tabla'   =>'sprv',
				'columnas'=>array(
				'proveed' =>'C&oacute;odigo',
				'nombre'=>'Nombre',
				'contacto'=>'Contacto'),
				'filtro'  =>array('proveed'=>'C&oacute;digo','nombre'=>'Nombre'),
				'retornar'=>array('proveed'=>'proveed'),
				'titulo'  =>'Buscar Proveedor');

		$bSPRV=$this->datasis->modbus($mSPRV);

		$link2=site_url('inventario/common/get_linea');
		$link3=site_url('inventario/common/get_grupo');

		$script='
		$(document).ready(function(){

			$("#depto").change(function(){
				depto();
				$.post("'.$link2.'",{ depto:$(this).val() },function(data){$("#linea").html(data);})
				$.post("'.$link3.'",{ linea:"" },function(data){$("#grupo").html(data);})
			});
			$("#linea").change(function(){
				linea();
				$.post("'.$link3.'",{ linea:$(this).val() },function(data){$("#grupo").html(data);})
			});

			$("#grupo").change(function(){
				grupo();
			});
			depto();
			linea();
			grupo();
		});

		function depto(){
			if($("#depto").val()!=""){
				$("#nom_depto").attr("disabled","disabled");
			}
			else{
				$("#nom_depto").attr("disabled","");
			}
		}

		function linea(){
			if($("#linea").val()!=""){
				$("#nom_linea").attr("disabled","disabled");
			}
			else{
				$("#nom_linea").attr("disabled","");
			}
		}

		function grupo(){
			if($("#grupo").val()!=""){
				$("#nom_grupo").attr("disabled","disabled");
			}
			else{
				$("#nom_grupo").attr("disabled","");
			}
		}';

		$filter = new DataFilter2("Filtro por Producto");
		$filter->script($script);

		$filter->db->select('a.codigo');
		$filter->db->select('a.descrip');
		$filter->db->select('s.exmin');
		$filter->db->select('s.id');
		$filter->db->from('eventas as a');
		$filter->db->join('grup AS b' ,'a.grupo=b.grupo');
		//$filter->db->join('line AS c' ,'c.linea=b.linea');
		//$filter->db->join('dpto AS d' ,'c.depto=d.depto');
		$filter->db->join('sinv AS s' ,'a.codigo=s.codigo');
		//$filter->db->where('s.activo','S');
		$filter->db->where('s.tipo','Articulo');
		$filter->db->groupby('a.descrip');

		//Agregar proveedor y filtro de activo, columna de activo maximo=doble minu +1 predeterminado, guardar historial de cambios
		//Diferencias porcentual enete el minimo calculado y el minimo actual-

		$filter->fechad = new dateonlyField("Desde", "fechad",'m/Y');
		$filter->fechah = new dateonlyField("Hasta", "fechah",'m/Y');
		$filter->fechad->dbformat='Y-m-';
		$filter->fechah->dbformat='Y-m-';
		$filter->fechah->rule = "required";
		$filter->fechad->rule = "required";
		$filter->fechad->clause  =$filter->fechah->clause='';
		$filter->fechad->insertValue = date("Y-m-d",mktime(0,0,0,date('m')-12,date('j'),date('Y')));
		$filter->fechah->insertValue = date("Y-m-d");
		$filter->fechah->size=$filter->fechad->size=9;

		$filter->depto = new dropdownField("Departamento","depto");
		$filter->depto->db_name="a.depto";
		$filter->depto->option("","Seleccione un Departamento");
		$filter->depto->options("SELECT depto, descrip FROM dpto WHERE tipo='I' ORDER BY depto");

		$filter->linea2 = new dropdownField("L&iacute;nea","linea");
		$filter->linea2->db_name="a.linea";
		$filter->linea2->option("","Seleccione un Departamento primero");
		$depto=$filter->getval('depto');
		if($depto!==FALSE){
			$filter->linea2->options("SELECT linea, descrip FROM line WHERE depto='$depto' ORDER BY descrip");
		}else{
			$filter->linea2->option("","Seleccione un Departamento primero");
		}

		$filter->grupo = new dropdownField("Grupo", "grupo");
		$filter->grupo->db_name='a.grupo';
		$filter->grupo->option("","Seleccione una L&iacute;nea primero");
		$linea=$filter->getval('linea2');
		if($linea!==FALSE){
			$filter->grupo->options("SELECT grupo, nom_grup FROM grup WHERE linea='$linea' ORDER BY nom_grup");
		}else{
			$filter->grupo->option("","Seleccione un Departamento primero");
		}

		$filter->marca = new dropdownField("Marca", "marca");
		$filter->marca->option('','Todas');
		$filter->marca->options("SELECT TRIM(marca) AS clave, TRIM(marca) AS valor FROM marc ORDER BY marca");
		$filter->marca -> style='width:220px;';

		$filter->estadistica = new dropdownField('Estad&iacute;stica a usar', 'estadistica');
		$filter->estadistica->clause='';
		$filter->estadistica->rule = 'required';
		$filter->estadistica->option('promediot','Promedio truncado');
		$filter->estadistica->option('promedio' ,'Promedio');
		$filter->estadistica->option('mediana'  ,'Mediana' );
		$filter->estadistica->option('maximo'   ,'Valor M&aacute;ximo');
		$filter->estadistica->option('minimo'   ,'Valor M&iacute;nimo');
		//$filter->estadistica->option('moda'     ,'Moda');
		$filter->estadistica->group='Configuraci&oacute;n';

		$filter->maximos = new dropdownField('C&aacute;lculo de m&aacute;ximos', 'maximos');
		$filter->maximos->clause='';
		$filter->maximos->option('','No alterar');
		$filter->maximos->option('max_dupli','Doble del m&iacute;nimo');
		$filter->maximos->group='Configuraci&oacute;n';

		$filter->frecuencia = new dropdownField('Frecuencia', 'frecuencia');
		$filter->frecuencia->clause= '';
		$filter->frecuencia->rule  = 'required';
		$filter->frecuencia->option('1' ,'Mensual');
		$filter->frecuencia->option('2' ,'BiMenusal');
		$filter->frecuencia->option('4' ,'Semanal');
		$filter->frecuencia->option('8' ,'BiSemanal');
		$filter->frecuencia->group='Configuraci&oacute;n';

		$filter->buttons('reset','search');

		if($this->rapyd->uri->is_set('search')){
			$filter->submit('btn_cambio_2', 'Actualizar todos', "BR");
		}
		$filter->build();

		$uri = 'inventario/sinv/dataedit/show/<#codigo#>';

		$tabla='';
		if ($filter->is_valid()){
			$estadistica=$filter->estadistica->newValue;
			$maximos    =$filter->maximos->newValue;
			$frecuencia =$filter->frecuencia->newValue;
			$udia=days_in_month(substr($filter->fechah->newValue,4),substr($filter->fechah->newValue,0,4));
			$fechad=$filter->fechad->newValue.'01';
			$fechah=$filter->fechah->newValue.$udia;
			$filter->db->where('a.fecha >=',$fechad);
			$filter->db->where('a.fecha <=',$fechah);

			$datetime1 = new DateTime($fechad);
			$datetime2 = new DateTime($fechah);
			$interval = $datetime1->diff($datetime2);

			$ffechad=explode('-',$fechad);

			$grid = new DataGrid('Lista de Art&iacute;culos');
			$grid->order_by('codigo','asc');
			$grid->use_function('divisor',$estadistica);
			$grid->per_page = 15;

			$grid->column('C&oacute;digo'     ,'codigo' );
			$grid->column('Descripci&oacute;n','descrip');

			$columncal=$ccolumncal=array();
			for($i=0;$i<=$interval->m+1;$i++){
				$mk=mktime(0,0,0,$ffechad[1]+$i,1,$ffechad[0]);
				$udia=days_in_month(date('m',$mk),date('Y',$mk));
				$sqdesde=date("Y-m-d",$mk);
				$sqhasta=date("Y-m-",$mk).$udia;
				$etiq=date("m/Y",$mk);

				$select="SUM(cana*(fecha BETWEEN '$sqdesde' AND '$sqhasta')) AS '$etiq'";
				$filter->db->select($select);
				$grid->column($etiq,"<nformat><#$etiq#></nformat>",'align=right');
				$columncal[] ="<#$etiq#>";
				$ccolumncal[]=$etiq;
			}
			$grid->column('Promedio'     ,'<b style="color:red"><nformat><divisor><'.$estadistica.'>'.implode('|',$columncal).'</'.$estadistica.'>|'.$frecuencia.'</divisor></nformat></b>','align=right');
			$grid->column('M&iacute;nimo','<nformat><#exmin#></nformat>','align=right');
			$grid->column('&nbsp;'       ,'<a href="javascript:actumin(\'<#id#>\',\'<divisor><'.$estadistica.'>'.implode('|',$columncal).'</'.$estadistica.'>|'.$frecuencia.'</divisor>\')" >Actualizar</a>','align=right');

			if($_cambiomin){
				//echo 'Cambios de todos';
				unset($_POST['btn_cambio_2']);
				$_POST['btn_submit']='a';

				$sql=$filter->db->_compile_select();
				$query = $this->db->query($sql);

				if ($query->num_rows() > 0){
					foreach ($query->result() as $row){
						$param=array();
						foreach($ccolumncal AS $obj){
							$param[]=$row->$obj;
						}
						$min=ceil($estadistica($param)/$frecuencia);
						$where = 'codigo ='.$this->db->escape($row->codigo);
						$data=array('exmin' => $min);
						if(!empty($maximos)){
							$data['exmax'] = $maximos($min);
						}
						$sSQL = $this->db->update_string('sinv', $data, $where);
						$this->db->simple_query($sSQL);
					}
				}
			}

			$grid->build();
			$tabla=$grid->output;
		}

		$url=site_url('inventario/comparativo/actumin/').'/';
		$data['script']  ='<script language="javascript" type="text/javascript">
		function actumin(id,val){
			vval = prompt("Actualizar minimo a:",val);
			if(vval)
				$.get("'.$url.'"+id+"/"+vval, function(data) { alert(data); });
		}
		</script>';
		$data['content'] = $filter->output.$tabla;
		$data['title']   = header('Comparativo de M&iacute;nimos de Inventario');
		$data['head']    = script('jquery.pack.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").script("sinvmaes2.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function actumin($id,$exmin){
		$data['exmin']=$exmin;
		$mSQL = $this->db->update_string('sinv', $data, 'id='.$this->db->escape($id));
		if($this->db->simple_query($mSQL)==FALSE){
			echo 'Error actualzando';
		}
		echo 'Listo!!';
	}

	function instalar(){
		/*$mSQL='ALTER TABLE `sinv` DROP PRIMARY KEY';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sinv` ADD UNIQUE `codigo` (`codigo`)';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE sinv ADD id INT AUTO_INCREMENT PRIMARY KEY';
		$this->db->simple_query($mSQL);*/
	}
}
