<?php
class Common extends controller {
	function get_depto(){//usado por sinv
		$mSQL=$this->db->query("SELECT depto, CONCAT(descrip,' (',depto,')')  as valor FROM dpto WHERE tipo='I' ORDER BY valor");
		echo "<option value=''></option>";
		if($mSQL){
			foreach($mSQL->result() AS $fila ){
				echo "<option value='".$fila->depto."'>".$fila->valor."</option>";
			}
		}
	}

	function add_depto(){//usado por sinv
		if(isset($_POST['valor'])){
			$valor=$_POST['valor'];
			$existe=$this->datasis->dameval("SELECT COUNT(descrip) FROM dpto WHERE descrip='$valor'");
			if($existe>0){
				echo "Y.a-Existe";
			}else{
				$depto=$this->sug('dpto');
				$agrego=$this->db->query("INSERT INTO dpto (depto,tipo,descrip) VALUES ('$depto','I','$valor')");
				if($agrego)echo $depto;
				else echo "N.o-SeAgrego";
			}
		}
	}

	function add_linea()//usado por sinv
	{
		if(isset($_POST['valor']) && isset($_POST['valor2'])){
			$valor=$_POST['valor'];
			$valor2=$_POST['valor2'];
			$existe=$this->datasis->dameval("SELECT COUNT(descrip) FROM line WHERE descrip='$valor' AND depto='$valor2'");
			if($existe>0){
				echo "Y.a-Existe";
			}else{
				$linea=$this->sug('line');
				$agrego=$this->db->query("INSERT INTO line (linea,depto,descrip) VALUES ('$linea','$valor2','$valor')");
				if($agrego)echo $linea;
				else echo "N.o-SeAgrego";
			}
		}
	}

	function get_linea(){//usado por sinv
		echo "<option value=''>Seleccione un Departamento</option>";
		$depto=$this->input->post('depto');
		if(!empty($depto)){
			$mSQL=$this->db->query("SELECT linea,CONCAT_WS(' ',linea,descrip) AS descrip FROM line WHERE depto ='$depto'");
			if($mSQL){
				foreach($mSQL->result() AS $fila ){
					echo "<option value='".$fila->linea."'>".$fila->descrip."</option>";
				}
			}
		}
	}


	function add_fami()//usado por maes
	{
		if(isset($_POST['valor']) && isset($_POST['valor2'])){
			$valor=$_POST['valor'];
			$valor2=$_POST['valor2'];
			$existe=$this->datasis->dameval("SELECT COUNT(descrip) FROM fami WHERE descrip='$valor' AND depto='$valor2'");
			if($existe>0){
				echo "Y.a-Existe";
			}else{
				$fami=$this->sug('fami');
				$agrego=$this->db->query("INSERT INTO fami (familia,depto,descrip) VALUES ('$fami','$valor2','$valor')");
				if($agrego)echo $fami;
				else echo "N.o-SeAgrego";
			}
		}
	}

	function get_familia(){
	    $this->get_fami();

	}

	function get_fami(){//usado por sinv
		echo "<option value=''>Seleccione un Departamento</option>";
		$depto=$this->input->post('depto');
		if(!empty($depto)){
			$mSQL=$this->db->query("SELECT familia, CONCAT(descrip,' (',familia,')') descrip FROM fami WHERE depto ='$depto'");
			if($mSQL){
				foreach($mSQL->result() AS $fila ){
					echo "<option value='".$fila->familia."'>".$fila->descrip."</option>";
				}
			}
		}
	}

	function get_zona(){//usado por sclifyco
		echo "<option value=''>Seleccione un Pais</option>";
		$pais=$this->input->post('pais');
		if(!empty($pais)){
			$mSQL=$this->db->query("SELECT codigo, nombre FROM zona WHERE pais='$pais'ORDER BY codigo");
			if($mSQL){
				foreach($mSQL->result() AS $fila ){
					echo "<option value='".$fila->codigo."'>".$fila->nombre."</option>";
				}
			}
		}
	}
	function get_estados(){//usado por sclifyco
		echo "<option value=''>Seleccione una Zona</option>";
		$zona=$this->input->post('zona');
		if(!empty($zona)){
			$mSQL=$this->db->query("SELECT codigo, nombre FROM estado WHERE zona='$zona' ORDER BY codigo");
			if($mSQL){
				foreach($mSQL->result() AS $fila ){
					echo "<option value='".$fila->codigo."'>".$fila->nombre."</option>";
				}
			}
		}
	}
	function get_municipios(){//usado por sclifyco
		echo "<option value=''>Seleccione un Estado</option>";
		$estados=$this->input->post('estados');
		if(!empty($estados)){
			$mSQL=$this->db->query("SELECT codigo, nombre FROM municipio WHERE estado='$estados' ORDER BY codigo");
			if($mSQL){
				foreach($mSQL->result() AS $fila ){
					echo "<option value='".$fila->codigo."'>".$fila->nombre."</option>";
				}
			}
		}
	}
	function get_grupo(){//usado por sinv
		$linea=$this->input->post('linea');
		if(!empty($linea)){
			$mSQL=$this->db->query("SELECT grupo,CONCAT_WS(' ',grupo,nom_grup) AS nom_grup FROM grup WHERE linea ='$linea' ORDER BY grupo");
			if($mSQL){
				echo "<option value=''>Seleccione una L&iacute;nea</option>";
				foreach($mSQL->result() AS $fila ){
					echo "<option value='".$fila->grupo."'>".$fila->nom_grup."</option>";
				}
			}
		}else{
			echo "<option value=''>Seleccione una L&iacute;nea primero</option>";
		}
	}

	function get_grupo_tipo(){//usado con el tipo
		$tipo=$this->input->post('tipo');
		if(!empty($tipo)){
			$mSQL=$this->db->query("SELECT grupo,nom_grup FROM grup WHERE tipo ='$tipo'");
			if($mSQL){
				echo "<option value=''>Seleccione una L&iacute;nea</option>";
				foreach($mSQL->result() AS $fila ){
					echo "<option value='".$fila->grupo."'>".$fila->nom_grup."</option>";
				}
			}
		}else{
			echo "<option value=''>Seleccione un tipo primero</option>";
		}
	}

	function get_grupo_m(){//usado por maes
		$fami  = $this->input->post('fami');
		$depto = $this->input->post('depto');

		if(!empty($fami)){
			$mSQL=$this->db->query("SELECT grupo, CONCAT( nom_grup,' (',grupo,')') nom_grup FROM grup WHERE familia ='$fami' AND depto='$depto' ORDER BY nom_grup");
			if($mSQL){
				echo "<option value=''>Seleccione una familia</option>";
				foreach($mSQL->result() AS $fila ){
					echo "<option value='".$fila->grupo."'>".$fila->nom_grup."</option>";
				}
			}
		}else{
			echo "<option value=''>Seleccione una familia primero</option>";
		}
	}

	function add_grupo()//usado por sinv
	{
		if(isset($_POST['valor']) && isset($_POST['valor2']) && isset($_POST['valor3'])){
			$valor=$_POST['valor'];
			$valor2=$_POST['valor2'];
			$valor3=$_POST['valor3'];
			$existe=$this->datasis->dameval("SELECT COUNT(nom_grup) FROM grup WHERE nom_grup='$valor' AND linea='$valor2' AND depto='$valor3'");
			if($existe>0){
				echo "Y.a-Existe";
			}else{
				$grupo=$this->sug('grup');
				$agrego=$this->db->query("INSERT INTO grup (grupo,linea,nom_grup,tipo,depto) VALUES ('$grupo','$valor2','$valor','I','$valor3')");
				if($agrego)echo $grupo;
				else echo "N.o-SeAgrego";
			}
		}
	}

	function get_marca(){//usado por sinv
		$mSQL=$this->db->query("SELECT marca as codigo, marca FROM marc ORDER BY marca");
		if($mSQL){
			foreach($mSQL->result() AS $fila ){
				echo "<option value='".$fila->codigo."'>".$fila->marca."</option>";
			}
		}
	}

	function add_marc(){//usado por sinv
		if(isset($_POST['valor'])){
			$valor=$_POST['valor'];
			$agrego=$this->db->query("INSERT INTO marc (marca) VALUES ('$valor')ON DUPLICATE KEY UPDATE marca='$valor'");
			if($agrego)echo "s.i";
		}
	}

	function get_unidad(){//usado por sinv
		$mSQL=$this->db->query("SELECT unidades, unidades as valor FROM unidad ORDER BY unidades");
		echo "<option value=''></option>";
		if($mSQL){
			foreach($mSQL->result() AS $fila ){
				echo "<option value='".$fila->unidades."'>".$fila->valor."</option>";
			}
		}
	}

	function add_unidad(){//usado por sinv
		if(isset($_POST['valor'])){
			$valor=$_POST['valor'];
			$agrego=$this->db->query("INSERT INTO unidad (unidades) VALUES ('$valor')ON DUPLICATE KEY UPDATE unidades='$valor'");
			if($agrego)echo "s.i";
		}
	}

	function sugerir_dpto(){
		$ultimo=$this->datasis->dameval("SELECT LPAD(hexa,2,0) FROM serie LEFT JOIN dpto ON LPAD(depto,2,0)=LPAD(hexa,2,0) WHERE valor<255 AND depto IS NULL LIMIT 1");
		echo $ultimo;
	}

	function sugerir_grup(){
		$ultimo=$this->datasis->dameval("SELECT LPAD(hexa,4,0) FROM serie LEFT JOIN grup ON LPAD(grupo,4,0)=LPAD(hexa,4,0) WHERE valor<65535 AND grupo IS NULL LIMIT 1");
		echo $ultimo;
	}

	function sugerir_line(){
		$ultimo=$this->datasis->dameval("SELECT LPAD(hexa,2,0) FROM serie LEFT JOIN line ON LPAD(linea,2,0)=LPAD(hexa,2,0) WHERE valor<255 AND linea IS NULL LIMIT 1");
		echo $ultimo;
	}

	function sug($tabla=''){
		if($tabla=='dpto'){
			$valor=$this->datasis->dameval("SELECT LPAD(hexa,2,0) FROM serie LEFT JOIN dpto ON LPAD(depto,2,0)=LPAD(hexa,2,0) WHERE valor<255 AND depto IS NULL LIMIT 1");
		}elseif($tabla=='line'){
			$valor=$this->datasis->dameval("SELECT LPAD(hexa,2,0) FROM serie LEFT JOIN line ON LPAD(linea,2,0)=LPAD(hexa,2,0) WHERE valor<255 AND linea IS NULL LIMIT 1");
		}elseif($tabla=='grup'){
			$valor=$this->datasis->dameval("SELECT LPAD(hexa,4,0) FROM serie LEFT JOIN grup ON LPAD(grupo,4,0)=LPAD(hexa,4,0) WHERE valor<65535 AND grupo IS NULL LIMIT 1");
		}elseif($tabla=='fami'){
			$valor=$this->datasis->dameval("SELECT LPAD(hexa,2,0) FROM serie LEFT JOIN fami ON LPAD(familia,2,0)=LPAD(hexa,4,0) WHERE valor<255 AND familia IS NULL LIMIT 1");
		}
		return $valor;
	}

	function _descufijo($codigo,$aplica){
		$dbcodigo=$this->db->escape($codigo);
		if($aplica=='sinv'){
			if ($this->db->field_exists('descufijo', 'sinv')){
				$mSQL='SELECT descufijo FROM sinv WHERE codigo='.$dbcodigo;
				$descu=$this->datasis->dameval($mSQL);
			}else{
				$descu=0;
			}
			if(empty($descu) || $descu==0){
				if($this->db->table_exists('sinvpromo')){
					$descufijo=$this->datasis->dameval('SELECT margen FROM sinvpromo WHERE codigo='.$dbcodigo);
					$descurazon='Descuento promocional';
					if(empty($descufijo)){
						if($this->db->field_exists('margen','grup')){
							$mSQL ='SELECT grupo FROM sinv WHERE codigo='.$dbcodigo;
							$grupo=$this->datasis->dameval($mSQL);
							$descufijo=$this->datasis->dameval('SELECT margen FROM grup WHERE grupo='.$this->db->escape($grupo));
							$descurazon='Descuento por grupo';
						}else{
							$descufijo=0;
						}
					}
				}else{
					$descufijo=0;
				}
			}else{
				$descufijo=$descu;
				$descurazon='Descuento por producto';
			}
		}else{
			$descufijo=0;
		}
		return $descufijo;
	}

	function _gconsul($mSQL_p,$cod_bar,$busca,$suple=null){
		if(!empty($suple) AND $this->db->table_exists('suple')){
			$mSQL  ="SELECT codigo FROM suple WHERE suplemen='${cod_bar}' LIMIT 1";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() != 0){
				$row = $query->row();
				$busca  =array($suple);
				$cod_bar=$row->codigo;
			}
		}

		foreach($busca AS $b){
			$mSQL  =$mSQL_p." WHERE ${b}='${cod_bar}' LIMIT 1";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() != 0){
				return $query;
			}
		}

		if ($this->db->table_exists('barraspos')) {
			$mSQL  ="SELECT codigo FROM barraspos WHERE suplemen=".$this->db->escape($cod_bar)." LIMIT 1";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() != 0){
				$row = $query->row();
				$cod_bar=$row->codigo;

				$mSQL  =$mSQL_p." WHERE codigo='${cod_bar}' LIMIT 1";
				$query = $this->db->query($mSQL);
				if($query->num_rows() == 0)
					return false;
			}else{
				return false;
			}
		}else{
			return false;
		}
		return $query;
	}

	//Para buscar compra o factura en el autocomplete
	function buscasfacscst(){
		$mid   = $this->input->post('q');
		$qdb   = $this->db->escape('%'.$mid.'%');

		$data = '{[ ]}';
		if($mid !== false){
			$retArray = $retorno = array();

			$mSQL="SELECT
					IF(a.tipo_doc='FC','R','E') AS tipo,
					a.tipo_doc AS tipo_ref,
					a.numero,
					proveed AS clipro,
					a.nombre,
					'C' AS operacion,
					a.fecha,
					'scst' AS origen
				FROM  scst AS a
				WHERE CONCAT(a.tipo_doc,'-',a.numero) LIKE $qdb
				UNION ALL
				SELECT
					IF(b.tipo_doc='F','E','R') AS tipo,
					b.tipo_doc AS tipo_ref,
					b.numero,
					b.cod_cli AS clipro,
					b.nombre,
					'V' AS operacion,
					b.fecha,
					'sfac' AS origen
				FROM sfac AS b
				WHERE CONCAT(b.tipo_doc,'-',b.numero) LIKE $qdb AND b.tipo_doc<>'X' AND MID(b.numero,1,1)<>'_'";

			$mSQL="SELECT * FROM ($mSQL) AS aa ORDER BY aa.fecha desc LIMIT 10";

			$query = $this->db->query($mSQL);
			if ($query->num_rows() > 0){
				foreach( $query->result_array() as  $row ) {
					$retArray['label']    = $row['tipo_ref'].$row['numero'].'-'.utf8_encode($row['nombre']);
					$retArray['value']    = $row['numero'];
					$retArray['tipo']     = $row['tipo'];
					$retArray['origen']   = $row['origen'];
					$retArray['clipro']   = $row['clipro'];
					$retArray['tipo_ref'] = $row['tipo_ref'];
					$retArray['nombre']   = utf8_encode($row['nombre']);

					array_push($retorno, $retArray);
				}
				$data = json_encode($retorno);
			}else{
				$retArray[0]['label']   = 'No se consiguieron facturas';
				$retArray[0]['value']   = '';
				$retArray[0]['tipo']    = '';
				$retArray[0]['origen']  = '';
				$retArray[0]['nombre']  = '';
				$retArray[0]['clipro']  = '';
				$retArray[0]['tipo_ref']= '';
				$data = json_encode($retArray);
			}
		}
		echo $data;
	}

	function get_codigo(){
		$barras =$this->input->post('barras');
		$barrase=$this->db->escape($barras);
		$codigo =$this->datasis->dameval("SELECT codigo FROM sinv WHERE barras=$barrase LIMIT 1");
		if(empty($codigo)){
			$codigo =$this->datasis->dameval("SELECT codigo FROM sinv WHERE codigo=$barrase LIMIT 1");
		}
		echo $codigo;
	}

	function get_prod(){
		$barras =$this->input->post('barras');
		$barrase=$this->db->escape($barras);
		$row=$this->datasis->damerow("SELECT descrip,codigo,serial FROM sinv WHERE barras=$barrase LIMIT 1");
		$pivot=array();
		if(!empty($row)){
			$pivot['cana']   = 1;
			$pivot['descrip']= $row['descrip'];
			$pivot['codigo'] = $row['codigo'] ;
			$pivot['serial'] = $row['serial'] ;
		}else{
			$pivot['cana'] = 0;
		}
		echo json_encode($pivot);
	}

	function get_descrip(){
		$barras =$this->input->post('barras');
		$barrase=$this->db->escape($barras);
		$descrip=$this->datasis->dameval("SELECT descrip FROM sinv WHERE barras=$barrase LIMIT 1");
		if(empty($descrip)){
			$descrip =$this->datasis->dameval("SELECT descrip FROM sinv WHERE codigo=$barrase LIMIT 1");
		}
		echo $descrip;
	}

	function get_cant(){
		$barras =$this->input->post('barras');
		$barrase=$this->db->escape($barras);
		$cana = $this->datasis->dameval("SELECT count(*) FROM sinv WHERE barras=$barrase ");
		if($cana==0 || empty($cana)){
			$cana = $this->datasis->dameval("SELECT count(*) FROM sinv WHERE codigo=$barrase ");
		}
		echo $cana;
	}

	function get_sinv(){
		$barras =$this->input->post('barras');
		$barrase=$this->db->escape('7798134499458');
		$table=$this->db->query("SELECT codigo,descrip FROM sinv WHERE barras=$barrase ");
		$table=$table->result_array();
		echo json_encode($table);
	}

	function _costos($formcal,$costo_pond,$costo_ulti,$costo_stan){
		switch($formcal){
			case 'P':
				$costo=$costo_pond;
				break;
			case 'U':
				$costo=$costo_ulti;
				break;
			case 'S':
				$costo=$costo_stan;
				break;
			default:
				$costo=($costo_pond>$costo_ulti) ? $costo_pond : $costo_ulti;
		}
		return $costo;
	}
}
