<?php

class Buscar extends Controller
{
	// Tabla a consultar
	var $tabla;
	// Columnas array('Campo de la tabla'=>'Titulo de la columna')
	var $columnas;
	// Filtro de busqueda array('Campo de la tabla'=>'Titulo del campo','Campo de la tabla'=>array('Valor desde', 'Valor hasta'))
	var $filtro;
	// Valores a retornar array('Campo de la tabla'=>'Id del objeto que recibe')
	var $retornar;
	// Titulo
	var $titulo;
	// Usar varibles proveniente del uri en en este formato array(segmento=>'<#xxxx#>'), Ej array( 3=>'<#i#>')
	//por ahora solo definido para los campo de retorno
	var $p_uri=false;
	//Where adicional para la consulta
	var $where='';
	//Funciones javasrip que se ejecutaran en el targer despues del paso
	var $script=array();
	//parametros para los join en las consultas
	var $join=array();
	//Parametros para agupar
	var $groupby='';
	//parametro que define el grupo de base de datos a usar
	var $dbgroup='';
	var $order_by='';
	//asc o desc
	var $direction='asc';

	function Buscar(){
		parent::Controller();
		$this->load->library('rapyd');
	}

	function index(){
		$this->rapyd->load('datafilter2','datagrid');
		$this->_db2prop();
		if(!empty($this->dbgroup)){
			$this->rapyd->set_connection($this->dbgroup);
			$this->rapyd->load_db();
		}

		$join=false;
		//extrae las varibles provenientes de las uris
		if($this->p_uri){
			$uris=array();
			foreach($this->p_uri as $segment=>$nombre){
				$valor=$this->uri->segment($segment);
				$uris[$nombre]=$valor;
			}
		}

		$tablas=$this->db->query('show tables');
		$tablas=$tablas->result_array();
		$tables=array();
		foreach($tablas as $row)
			foreach($row as $k=>$v)
				$tables[]=$v;

		//Filtro
		$filtros=array();
		foreach($this->filtro as $k=>$v){
			if(count(explode('.',$k))==2)
				if(in_array(substr($k,0,strpos($k,'.')),$tables))
					$filtros[substr($k,0,strpos($k,'.'))][substr($k,strpos($k,'.')+1)]=$v;
				else
				$filtros[$this->tabla][$k]=$v;
			else
			$filtros[$this->tabla][$k]=$v;
		}

		$campos=array();
		foreach($filtros as $k=>$v){
			$mSQL="SHOW FIELDS FROM $k WHERE Field IN ('".implode('\',\'',array_keys($v)).'\')';
			$query = $this->db->query($mSQL);
			$temp=$query->result_array();
			foreach($temp as $z=>$y){
				$temp[$z]['titulo'] =$v[$temp[$z]['Field']];
				$temp[$z]['db_name']=$k.'.'.$temp[$z]['Field'];
			}
			$campos=array_merge($campos,$temp);
		}

		$r=array();
		$rk=array();
		if(!isset($this->retornar[0])){
			foreach($this->retornar as $k=>$v){
				$r[]=array($k=>$v);
				$rk[]=$k;
			}
		}else{
			$r=$this->retornar;
			foreach($this->retornar as $k=>$v)
				foreach($v as $kk=>$vv)
					$rk[]=$kk;
		}
		$prev=array_keys($this->columnas);
		foreach($r as $k=>$v)
			$prev= array_merge($prev, array_keys($v));

		$prev2= array_unique($prev);
		foreach($prev2 AS $ddata){
				$ddata=$ddata;
				$select[]=$ddata;
		}

		$filter = new DataFilter2('Par&aacute;metros de B&uacute;squeda');
		$filter->db->select($select);
		$filter->db->from($this->tabla);

		if (!empty($this->groupby)) $filter->db->groupby($this->groupby);

		foreach($this->join as $row){
			if(count($row)==3){
				$join=true;
				$filter->db->join($row[0],$row[1],$row[2]);
			}
		}
		if(count($this->join)==3){
			$join=true;
			$filter->db->join($this->join[0],$this->join[1],$this->join[2]);
		}

		foreach($campos as $fila){
			$campo  =$fila['Field'];
			$campodb=$fila['db_name'];
			$titulo =$fila['titulo'];
			$type   =$fila['Type'];
			if(strncasecmp($type,'date', 4)==0){
				if(is_array ($titulo)){
					$filter->$campo = new dateField($titulo[0],$campo,'Y/m/d');
					$filter->$campo->clause='where';
					$filter->$campo->operator='>=';
					$campo2=$campo.'2';
					$filter->$campo2 = new dateField($titulo[1],$campo2,'Y/m/d');
					$filter->$campo2->db_name=$campodb;
					$filter->$campo2->clause='where';
					$filter->$campo2->operator='<=';
				}else{
					$filter->$campo = new dateField($titulo,$campo,'Y/m/d');
					$filter->$campo->clause='where';
					$filter->$campo->operator='=';
				}
			}else{
				if(is_array ($titulo)){
					$filter->$campo = new inputField($titulo[0],$campo);
					$filter->$campo->clause='where';
					$filter->$campo->operator='>=';
					$campo2=$campo.'2';
					$filter->$campo2 = new inputField($titulo[1],$campo2);
					$filter->$campo2->db_name=$campodb;
					$filter->$campo2->db_name=$campodb;
					$filter->$campo2->clause='where';
					$filter->$campo2->operator='<=';
				}else{
					$nobj=$campo.'_CDROPDOWN';
					$filter->$nobj = new dropdownField($titulo, $nobj);
					$filter->$nobj->clause='';
					$filter->$nobj->style='width:120px';
					$filter->$nobj->option('both'  ,'Contiene');
					$filter->$nobj->option('after' ,'Comienza con');
					$filter->$nobj->option('before','Termina con' );
					$side=$filter->getval($nobj);
					$filter->$campo = new inputField($titulo,$campo);
					$filter->$campo->in=$nobj;
					if($side!==FALSE){
						$filter->$campo->like_side=$side;
					}
				}
			}
			$filter->$campo->db_name=$campodb;
		}

		if (!empty($this->where)) {
			if(isset($uris)){
				$valores=array_values($uris);
				for($i=0;$i<count($valores);$i++)
					$valores[$i]=$this->db->escape($valores[$i]);
				$where=str_replace(array_keys($uris),$valores,$this->where);
			}else{
				$where=$this->where;
			}
			$filter->db->where($where);
		};
		$filter->buttons('reset','search');
		$filter->build();

		//Tabla
		function j_escape($parr){
			$search[] = '\''; $replace[] = '\'+String.fromCharCode(39)+\'';
			$search[] = '"';  $replace[] = '\'+String.fromCharCode(34)+\'';
			$search[] = "\n"; $replace[] = '\\n';
			$search[] = "\r"; $replace[] = '\\r';

			$pattern = str_replace($search, $replace, $parr);
			return '\''.$pattern.'\'';
		}

		$rk2=array();
		foreach($rk as $k=>$v){
			$a=explode('.',$v);
			if(count($a)==2){
				if(array_key_exists($a[0],$filtros)){
					$rk[$k]=substr($v,strpos($v,'.')+1);
				}else{
					$rk[$k]=$v;
				}
			}else{
				$rk[$k]=$v;
			}
		}

		$link='<j_escape><#'.implode("#></j_escape>,<j_escape><#",$rk).'#></j_escape>';
		//$link='\'<#'.implode("#>','<#",array_keys($this->retornar)).'#>\'';
		$link = "javascript:pasar($link);";
		$grid = new DataGrid("Resultados");
		$grid->use_function('j_escape');
		$grid->per_page = 10;
		if (!empty($this->order_by)) $grid->order_by($this->order_by,$this->direction);
		$i=0;
		foreach ($this->columnas as $campo => $titulo){
			if ($i==0){
				$cp1=strrchr($campo, '.');
				if($cp1)$campo=str_replace('.','',$cp1);

				//if(empty($this->order_by))
				$grid->column_orderby($titulo,"<a href=\"$link\"><#".substr($campo,strpos($campo,'.'))."#></a>", $campo);
			}else{
				if(count(explode('.',$campo))==2)
					if(in_array(substr($campo,0,strpos($campo,'.')),$tables))
						$grid->column_orderby($titulo,substr($campo,strpos($campo,'.')+1),$campo);
					else
						$grid->column_orderby($titulo,$campo,$campo);
				else
					$grid->column_orderby($titulo,$campo,$campo);
			}
			$i++;
		}
		$grid->build();
		//echo $grid->db->last_query();
		$i=0; $pjs1='';$pjs2='';
		foreach($r as $k=>$v){
			//print_r($v);
			foreach($v as $campo => $id){
				if ($this->p_uri)
					$id = str_replace(array_keys($uris),array_values($uris),$id);
				if($i==0) $pjs1.="p$i";
				else
				$pjs1.=",p$i";

				$pjs2.="
				if(window.opener.document.getElementById('$id').nodeName=='SPAN')
				window.opener.document.getElementById('$id').innerHTML = p$i;
				else
				window.opener.document.getElementById('$id').value = p$i;
				\n";
				$i++;
			}
		}

		$jscript ="<SCRIPT LANGUAGE=\"JavaScript\">\n";
		$jscript.="function pasar($pjs1){\n";
		$jscript.=" if (window.opener && !window.opener.closed){\n";
		$jscript.=$pjs2;
		$jscript.="   window.close();\n";
		foreach($this->script AS $funcion){
			$funcion = (isset($uris) ) ? str_replace(array_keys($uris),array_values($uris),$funcion) : $funcion;
			$jscript.=" window.opener.$funcion;\n";
		}
		$jscript.="}\n}\n</SCRIPT>";

		//echo $grid->db->last_query();
		$data['crud']         = $filter->output . $grid->output;
		$data['titulo']       = '';
		$data['encab']        = $this->titulo;
		$content['content']   = $this->load->view('rapyd/crud', $data, true);
		$content['rapyd_head']= $jscript.$this->rapyd->get_head();
		$content['code']      = '';
		//$content['titulo']  = $this->titulo;
		$content['lista']     = '';
		//$content['charset']   = (stripos($this->db->char_set,'latin')!==false)? 'ISO-8859-1': null;
		$content['charset']   = null;

		$this->load->view('rapyd/modbus', $content);

		//echo $filter->db->last_query();
	}

	function _sess2prop(){
		$id = $this->uri->segment(3);
		$arreglo=$this->session->flashdata('modbus');
		//echo '<pre>';print_r($this->session->userdata);echo '</pre>';
		//echo 'ARREGLO <pre>';print_r($arreglo);echo '</pre>';


		if($arreglo==FALSE or !array_key_exists($id, $arreglo)){
			echo '<pre>';print_r($this->session->userdata);echo '</pre>';
			exit("Error: No se han definido los parametros: $id");
		}
		//$modbus=$this->session->flashdata('modbus'.$id);
		$modbus=$arreglo[$id];
		$this->tabla   =$modbus['tabla'];
		$this->columnas=$modbus['columnas'];
		$this->filtro  =$modbus['filtro'];
		$this->retornar=$modbus['retornar'];
		$this->titulo  =$modbus['titulo'];

	}

	function _db2prop(){
		$id  = $this->uri->segment(3);
		$dbid= $this->db->escape($id);
		$query = $this->db->query("SELECT parametros FROM modbus WHERE id=${dbid}");

		if($query->num_rows() > 0){
			$row = $query->row();

			$para  = $row->parametros;
			$modbus= @unserialize($para);
			if($modbus===false){
				$para = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $para);
				$modbus= @unserialize($para);
			}
			if($modbus===false){
				show_error('No se pudo extraer la informacion.');
			}

			$this->tabla   =$modbus['tabla'];
			$this->columnas=$modbus['columnas'];
			$this->filtro  =$modbus['filtro'];
			$this->retornar=$modbus['retornar'];
			$this->titulo  =$modbus['titulo'];
			if(isset($modbus['p_uri']))   $this->p_uri  =$modbus['p_uri'];
			if(isset($modbus['where']))   $this->where  =$modbus['where'];
			if(isset($modbus['script']))  $this->script =$modbus['script'];
			if(isset($modbus['join']))    $this->join   =$modbus['join'];
			if(isset($modbus['groupby'])) $this->groupby=$modbus['groupby'];
			if(isset($modbus['dbgroup'])) $this->dbgroup=$modbus['dbgroup'];
			if(isset($modbus['orderby']))   $this->order_by  =$modbus['orderby'];
			if(isset($modbus['direction'])) $this->direction =$modbus['direction'];

		}else{
			show_error("id no encontrado ${id}");
		}
		//echo '<pre>';print_r($this->session->userdata);echo '</pre>';
	}

	function instalar(){
		$mSQL="CREATE TABLE IF NOT EXISTS `modbus` (
		  `id` int(10) unsigned NOT NULL auto_increment,
		  `uri` varchar(50) NOT NULL default '',
		  `idm` varchar(50) NOT NULL default '',
		  `parametros` text,
		  PRIMARY KEY  (`id`)
		) ENGINE=MyISAM AUTO_INCREMENT=1 DEFAULT CHARSET=latin1";

		$this->db->simple_query($mSQL);
		$query="ALTER TABLE `stal`  CHANGE COLUMN `nombre` `nombre` TEXT NULL DEFAULT NULL";
		$this->db->simple_query($query);
	}
}
