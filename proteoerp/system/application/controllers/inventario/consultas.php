<?php
class Consultas extends Controller {
	function Consultas(){
		parent::Controller();
		$this->load->library('rapyd');
		$sinv= ($this->db->table_exists('sinv')) ? $this->datasis->dameval('SELECT COUNT(*) FROM sinv'): 0;
		$maes= ($this->db->table_exists('maes')) ? $this->datasis->dameval('SELECT COUNT(*) FROM maes'): 0;
		if(is_null($sinv)) $sinv=0;
		if(is_null($maes)) $maes=0;

		$this->tipo=($maes>$sinv) ? 'maes' : 'sinv';
	}

	function index(){
		redirect('inventario/consultas/preciosgeneral');
	}

	function preciosgeneral(){
		$this->rapyd->load('dataform','datatable');
		$cod=($this->uri->segment(4)==false) ? $this->input->post('codigo') : $this->uri->segment(4);

		$script='<script type="text/javascript" charset=ISO-8859-1">
		$(document).ready(function() {
			$("#codigo").attr("value", "");
			$("#codigo").focus();
		});
		function dbuscar(){
			$("form").submit();
		}
		</script>';

		$barras = array(
			'name'      => 'codigo',
			'id'        => 'codigo',
			'value'     => '',
			'size'      => '16',
			);
		if($this->tipo=='sinv'){
			$modbus=array('tabla'   =>'sinv',
				'columnas'=>array(
					'codigo' =>'C&oacute;digo',
					'barras' =>'C&oacute;digo barras',
					'descrip'=>'Descripci&oacute;n',
					'existen'=>'Existencia'),
				'filtro'  =>array('descrip'=>'Descripci&oacute;n'),
				'retornar'=>array('codigo' =>'codigo'),
				'where'   =>'activo = "S"',
				'titulo'  =>'Buscar Art&iacute;culo',
				'script'  => array('dbuscar()'));
		}else{
			$modbus=array('tabla'   =>'maes',
				'columnas'=>array('codigo' =>'C&oacute;digo',
				'descrip'=>'descrip'),
				'filtro'  =>array('descrip'=>'descrip'),
				'retornar'=>array('codigo'=>'codigo'),
				'titulo'  =>'Buscar Articulo',
				'script'  => array('dbuscar()'));
		}
		$boton=$this->datasis->modbus($modbus);

		$out  = '<h1>'.form_open('inventario/consultas/preciosgeneral');
		$out .= 'Introduzca un C&oacute;digo ';
		$out .= form_input($barras).$boton;
		$out .= form_submit('btnsubmit','Consultar').form_close().'</h1>';

		$contenido = $out;
		if(!empty($cod)){
			$data2=$this->rprecios($cod);
			if($data2!==false){
				$contenido .=$this->load->view('view_rprecios', $data2,true);
			}else{
				$t=array();
				$t[1][1]='<b>PRODUCTO NO CODIFICADO</b>';
				$t[2][1]='';
				$t[3][1]='<b>Por Favor consulte con el personal de pasillo</b>';

				$table = new DataTable(null,$t);
				$table->cell_attributes = 'style="vertical-align:middle; text-align: center;"';
				$table->per_row  = 1;
				$table->cell_attributes = '';
				$table->cell_template = "<div style='color:red;' align='center'><#1#></div></br>";
				$table->build();
				$contenido .=$table->output;
			}
		}

		$data['content'] = $contenido;
		$data['head']    = script('jquery.js').style('ventanas.css').style('estilos.css').$this->rapyd->get_head().$script;
		$this->load->view('view_ventanas', $data);
	}

	function rprecios($cod_bar=NULL){
		if(!$cod_bar)$cod_bar=$this->input->post('barras');

		//$sinv= ($this->db->table_exists('sinv')) ? $this->datasis->dameval('SELECT COUNT(*) FROM sinv'): 0;
		//$maes= ($this->db->table_exists('maes')) ? $this->datasis->dameval('SELECT COUNT(*) FROM maes'): 0;

		if($this->tipo=='maes'){
			$mSQL_p = 'SELECT precio1, precio2, precio3, precio4,codigo, referen, barras, descrip, corta, codigo, marca,  dvolum1, dvolum2, existen, mempaq, dempaq,unidad,iva, 0 AS id FROM maes';
			$bbus   = array('codigo','barras','referen');
			$suple  = 'codigo';
			$aplica = 'maes';
		}else{
			$fiedesc= ($this->db->field_exists('descufijo', 'sinv')) ? 'descufijo':'0 AS descufijo';
			$mSQL_p = 'SELECT precio1,base1,precio2,precio3, barras,existen, CONCAT_WS(" ",descrip ,descrip2) AS descrip, codigo,marca,alterno,id,modelo,iva,unidad,'.$fiedesc.',grupo FROM sinv';
			$bbus   = array('codigo','barras','alterno');
			$aplica = 'sinv';
			$suple  = null;
		}

		$query=$this->_gconsul($mSQL_p,$cod_bar,$bbus,$suple);
		if($query!==false){
			$row = $query->row();
			//Vemos si aplica descuento solo farmacias sinv
			if($aplica=='sinv'){
				if($row->descufijo==0){
					if($this->db->table_exists('sinvpromo')){
						$descufijo=$this->datasis->dameval('SELECT margen FROM sinvpromo WHERE codigo='.$this->db->escape($row->codigo));
						$descurazon='Descuento promocional';
						if(empty($descufijo)){
							if($this->db->field_exists('margen','grup')){
								$descufijo=$this->datasis->dameval('SELECT margen FROM grup WHERE grupo='.$this->db->escape($row->grupo));
								$descurazon='Descuento por grupo';
							}else{
								$descufijo=0;
							}
						}
					}else{
						$descufijo=0;
					}
				}else{
					$descufijo=$row->descufijo;
					$descurazon='Descuento por producto';
				}
			}else{
				$descufijo=0;
			}

			$data['precio1']   = nformat($row->precio1);
			$data['pdescu']    = ($descufijo !=0) ? nformat($row->precio1-($row->precio1*$descufijo/100)): 0;
			$data['precio2']   = nformat($row->precio2);
			$data['precio3']   = nformat($row->precio3);
			$data['descrip']   = $row->descrip;
			$data['codigo']    = $row->codigo;
			$data['unidad']    = $row->unidad;
			$data['descufijo'] = nformat($descufijo);
			$data['corta']     = (isset($row->corta)) ?$row->corta : '';
			$data['descurazon']=(isset($descurazon)) ? $descurazon: '';
			$data['marca']     = $row->marca;
			$data['existen']   = nformat($row->existen);
			$data['barras']    = $row->barras;
			$data['iva']       = nformat($row->iva);
			$data['referen']   = (isset($row->referen)) ? $row->referen : 'No disponible';
			$data['moneda']    = 'Bs.F.';

			if($aplica=='maes'){
				$posdescu = $this->datasis->traevalor('POSDESCU','DESCUENTOS EN LOS PUNTOS DE VENTA Si o No');
				if($posdescu=='S'){
					$data['dvolum1']   = $row->dvolum1;
					$data['dvolum2']   = $row->dvolum2;
				}
				$data['precio4']   = nformat($row->precio4);
				$data['corta']     = $row->corta;
				$data['referen']   = $row->referen;
				$data['existen']   = $this->datasis->dameval("SELECT SUM(a.cantidad*b.fracxuni+a.fraccion) FROM ubic a JOIN maes b ON a.codigo=b.codigo WHERE a.codigo='".$row->codigo."' AND a.ubica IN ('DE00','DE01')");
			}else{
				$data['alterno']   = $row->alterno;
				$data['base1']     = nformat($row->base1);
				$data['modelo']    = $row->modelo;
				$data['iva2']      = nformat($row->base1*($row->iva/100));
			}

			$fotos=$this->datasis->dameval('SELECT COUNT(*) AS cana FROM sinvfot WHERE sinv_id='.$row->id);
			if($fotos>0){
				$data['img'] = img('inventario/fotos/thumbnail/'.$row->id);
			}
			return $data;
		}
		return false;
	}

	//Trae el descuento
	function _desc_precio($codigo,$aplica='sinv'){
		$dbcodigo =$this->db->escape($codigo);
		if($aplica=='sinv'){
			//$this->db->select(array('a.descufijo','b.margen'));
			//$this->db->from('sinv AS a');
			//$this->db->join('grup AS b','a.grupo=b.grupo','left');
			//$this->db->where('codigo',$codigo);
			//$query = $this->db->get();

			$dbcodigo=$this->db->escape($codigo);
			$sql   = "SELECT a.descufijo , b.margen
			FROM sinv AS a
			LEFT JOIN grup AS b ON a.grupo=b.grupo
			WHERE a.codigo=${dbcodigo}";
			$query = $this->db->query($sql);

			if ($query->num_rows() > 0){
				$row = $query->row();
				if(empty($row->descufijo) || $row->descufijo==0.0){
					$descufijo  = $this->datasis->dameval('SELECT margen FROM sinvpromo WHERE codigo='.$dbcodigo);
					$descurazon = 'Descuento promocional';

					if(empty($descufijo)){
						$descufijo  = $row->margen;
						$descurazon = 'Descuento por grupo';
					}
				}else{
					$descufijo  = $row->descufijo;
					$descurazon = 'Descuento por producto';
				}
			}else{
				$descufijo  = 0;
				$descurazon = '';
			}
		}else{
			$descufijo  = 0;
			$descurazon = '';
		}
		return $descufijo;
	}

	//***********************************
	// Consulta de precios para el kiosk
	//
	//***********************************
	function sprecios($formato='CPRECIOS'){
		$dbformato = $this->db->escape($formato);
		$data['conf']=$this->layout->settings;

		$query = $this->db->query("SELECT proteo FROM formatos WHERE nombre=${dbformato}");
		if ($query->num_rows() > 0){
			$row = $query->row();
			extract($data);
			ob_start();
				echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', $row->proteo)).'<?php ');
				$_html=ob_get_contents();
			@ob_end_clean();
			echo $_html;
		}else{
			//$reporte=file_get_contents(APPPATH.'views/view_cprecios.php');
			$data['link']=site_url('inventario/consultas/ssprecios');
			$this->load->view('view_cprecios', $data);
		}
	}

	function ssprecios($formato='CIPRECIOS',$cod_bar=NULL){
		$dbformato = $this->db->escape($formato);
		$query = $this->db->query("SELECT proteo FROM formatos WHERE nombre=${dbformato}");
		if ($query->num_rows() > 0){
			$row = $query->row();
			ob_start();
				echo eval('?>'.preg_replace("/;*\s*\?>/", "; ?>", str_replace('<?=', '<?php echo ', $row->proteo)).'<?php ');
				$_html=ob_get_contents();
			@ob_end_clean();
			echo $_html;
		}else{
			echo 'Formato ${formato} no definido';
		}
	}

	function precios(){
		$barras = array(
			'name'      => 'barras',
			'id'        => 'barras',
			'value'     => '',
			'maxlength' => '15',
			'size'      => '16',
			//'style'     => 'display:none;',
		);

		$out  = form_open('inventario/consultas/precios');
		$out .= form_label('Introduzca un C&oacute;digo ');
		$out .= form_input($barras);
		$out .= form_close();

		$link=site_url('inventario/consultas/rprecios');

		$data['script']='
		<script type="text/javascript">
		$(document).ready(function(){
			$("a").fancybox();
			$("#resp").hide();
			$("#barras").attr("value", "");
			$("#barras").focus();
			$("form").submit(function() {
				mostrar();
				return false;
			});
		});

		function mostrar(){
			$("#resp").hide();
			var url = "'.$link.'";
			$.ajax({
				type: "POST",
				url: url,
				data: $("input").serialize(),
				success: function(msg){
					$("#resp").html(msg).fadeIn("slow");
				  $("#barras").attr("value", "");
					$("#barras").focus();
				}
			});
		}
		</script>';
		$data['content'] = '<div id="resp" style=" width: 100%;" ></div>';
		$data['title']   = "<h1><center><a title='ender' href='http://192.168.0.99/proteoerp/assets/shared/images/3_b.jpg'><img src='http://192.168.0.99/proteoerp/assets/shared/images/3_s.jpg' /></a>$out</center></h1>";
		$data['head']    = script('jquery.js').script('plugins/jquery.fancybox.pack.js').script('plugins/jquery.easing.js').style('fancybox/jquery.fancybox.css').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function _gconsul($mSQL_p,$cod_bar,$busca,$suple=null,$tipo=null,$activo=true){
		$tabla=trim(substr($mSQL_p,(strripos($mSQL_p, 'FROM')+4)));
		if($activo) $activo=$this->db->field_exists('activo',$tabla)? 'AND activo=\'S\'' : '';
		if(!empty($tipo)){
			$wtipo = ' AND tipo='.$this->db->escape($tipo);
		}else{
			$wtipo = '';
		}
		$cod_bar=$this->db->escape($cod_bar);
		if(!empty($suple) && $this->db->table_exists('suple')){
			$mSQL  ="SELECT codigo FROM suple WHERE suplemen=${cod_bar} LIMIT 1";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() != 0){
				$row = $query->row();
				$busca  =array($suple);
				$cod_bar=$row->codigo;
			}
		}

		foreach($busca as $b){
			$mSQL  =$mSQL_p." WHERE ${b}=${cod_bar} ${activo} ${wtipo} LIMIT 1";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() != 0){
				return $query;
			}
		}

		if($this->db->table_exists('barraspos')) {
			$mSQL  ="SELECT codigo FROM barraspos WHERE suplemen=${cod_bar} LIMIT 1";
			$query = $this->db->query($mSQL);
			if ($query->num_rows() != 0){
				$row = $query->row();
				$cod_bar=$row->codigo;

				$mSQL  =$mSQL_p." WHERE codigo='${cod_bar}' ${activo} ${wtipo} LIMIT 1";
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
}
