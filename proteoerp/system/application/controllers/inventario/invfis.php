<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Invfis extends Controller {

	var $url = 'inventario/invfis/';

	function Invfis(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id(319,1);
		if (!extension_loaded('sysvsem')) {
			show_error('La extension sysvsem no esta cargada, debe cargarla para poder usar estas opciones');
		}
	}

	function index(){
		redirect($this->url.'define');
	}

	function define(){
		$this->rapyd->load('dataform');
		$this->rapyd->config->set_item('theme','clean');

		$titusize=1.5;
		$form0 = new DataForm('inventario/invfis/define/process/crear');
		$form0->title('<span style="font-size:'.$titusize.'em;">1-Crear un nuevo Inventario F&iacute;sico</span>');
		$form0->explica1 = new containerField('',"<p style='color:blue;background-color:C6DAF6;align:center'>Esta secci&oacute;n crea una tabla de inventario vacia de el Almac&eacute;n seleccionado donde se ingresa los valores resultantes del conteo de Inventario, en caso de haber creado un inventario f&iacute;sico previamente puede saltarse al paso 2.</p>");
		$form0->alma = new dropdownField('<span style="font-size:1.2em;color:#000000">Almac&eacute;n</span>', 'alma');
		$form0->alma->options("SELECT TRIM(ubica),CONCAT_WS('-',TRIM(ubides),TRIM(ubica)) AS desca FROM caub WHERE gasto='N' AND invfis='N' ORDER BY ubides");
		$form0->alma->rule='required';
		$form0->explica2 = new containerField('',"<p style='color:blue;background-color:C6DAF6;align:center'>La fecha es <b>muy importante</b>, si el conteo f&iacute;sico se realizo en la ma&ntilde;ana antes de abrir debe colocar la fecha de hoy, de lo contrario si el conteo se hizo en la tarde al final de la jornada debe colocar la fecha de Ma&ntilde;ana.</p>");

		$form0->fecha = new dateonlyField('<span style="font-size:1.2em;color:#000000">Fecha</span>', 'fecha');
		$form0->fecha->rule='required|chfecha';
		$form0->fecha->insertValue = date('Y-m-d');
		$form0->fecha->size=12;
		$form0->explica3 = new containerField('',"<p style='color:blue;background-color:C6DAF6;align:center'>Finalmente si observo las indicaciones anteriores presione el siguiente bot&oacute;n:</p> ");
		$form0->submit('btnsubmit','Crear Inventario F&iacute;sico');

		$form1 = new DataForm('inventario/invfis/define/process/contar');
		$form1->explica1 = new containerField('',"<p style='color:blue;background-color:C6DAF6;align:center'>Luego de haber creado un inventario f&iacute;sico nuevo en el paso anterior, en esta secci&oacute;n podra transcribir el resultado del conteo f&iacute;sico al sistema.</p>");
		$form1->title('<span style="font-size:'.$titusize.'em;">2-Introducir Resultados del Conteo de Inventario F&iacute;sico</span>');
		$form1->inv = new dropdownField('<span style="font-size:1.2em;color:#000000">Inventario F&iacute;sico</span>', 'inv');
		$form1->inv->rule = 'required';
		$form1->inv->style = 'width:400px';
		$form1->submit('btnsubmit','Introducir Conteo F&iacute;sico');

		$titusize=1.3;
		$form2 = new DataForm('inventario/invfis/define/process/cerrar');
		$form2->title('<span style="font-size:'.$titusize.'em;">3A-Cierre del Inventario F&iacute;sico</span>');
		$form2->explica1 = new containerField('',"<p style='color:blue;background-color:F6DAC6;align:center'>Finalmente si todo el inventario esta pasado puede cerrar con el siguiente bot&oacute;n y asi los montos introducidos se cargar&aacute;n en el almac&eacute;n respectivo. Tenga en cuenta que <b>Luego de cerrarlo no se podr&aacute; modificar</b>.</p>");
		$form2->inv = new dropdownField('<span style="font-size:1.2em;color:#000000">Inventario F&iacute;sico</span>', 'inv2');
		$form2->inv->rule = 'required';
		$form2->inv->style = 'width:400px';

		$form3 = new DataForm('inventario/invfis/define/process/descartar');
		$form3->title('<span style="font-size:'.$titusize.'em;">3B-Descarte del Inventario F&iacute;sico</span>');
		$form3->explica1 = new containerField('',"<p style='color:red;background-color:F6DAC6;align:center'>Esta opci&oacute;n eliminara el inventario f&iacute;sico seleccionado, se perder&aacute;n los conteos realizados sobre ese inventario y no se cargar&aacute;n en los almacenes. Luego de descartarlo <b>no se podr&aacute; recuperar</b>.</p>");
		$form3->inv = new dropdownField('<span style="font-size:1.2em;color:#000000">Inventario F&iacute;sico</span>', 'inv3');
		$form3->inv->rule = 'required';
		$form3->inv->style = 'width:400px';
		$form3->submit('btnDELETE' ,'Descartar Inventario');

		$mSQL=$this->db->query("SHOW TABLES LIKE 'INV%________'");
		foreach($mSQL->result_array() AS $row){
			foreach($row AS $key=>$value){
				if(preg_match('/^INV[a-zA-Z0-9]+\d{8}$/', $value)){
					$vval='Almacen:'.$this->datasis->dameval("SELECT ubides FROM caub WHERE ubica ='".substr($value,3,-8)."'").' de Fecha '.dbdate_to_human(substr($value,-8));
					$form3->inv->option($value,$vval);
					$form2->inv->option($value,$vval);
					$form1->inv->option($value,$vval);
				}
			}
		}
		$form2->submit('btnSiCero' ,'Cerrar asumiendo existencia cero para los no contados');
		$form2->submit('btnNoCero' ,'Cerrar (Pasa solo los contados)');

		$form0->build_form();
		$form1->build_form();
		$form2->build_form();
		$form3->build_form();

		$error='';
		//crea un nuevo inventario
		if ($form0->on_success()){
			$alma  = $form0->alma->newValue;
			$fecha = $form0->fecha->newValue;
			$error.=$this->_crear($alma,$fecha);
			if(strlen($error)==0)
				redirect($this->url.'define');
		}

		//entra en conteo
		if ($form1->on_success()){
			$inv=$form1->inv->newValue;
			redirect($this->url.'inven/'.$inv);
		}


		//cierra el inventario
		if ($form2->on_success()){
			$tabla=$form2->inv->newValue;
			if($this->input->post('btnSiCero')!==false){
				$error.=$this->_cerrar($tabla,false); //asume existencias en cero
			}elseif($this->input->post('btnNoCero')!==false){
				$error.=$this->_cerrar($tabla,true);
			}
			if(strlen($error)==0)
				redirect($this->url.'define');
		}

		//Descarte inventario
		if ($form3->on_success()){
			$tabla=$form3->inv->newValue;
			$error=$this->_descarte($tabla);
			if(strlen($error)==0)
				redirect($this->url.'define');
		}

		$titusize=1.5;
		$data['content']  = "<div class='alert'>${error}</div>";
		$data['content'] .= '<span style="font-size:1.3em;">Para realizar un inventario f&iacute;sico siga los siguientes pasos. ponga atenci&oacute;n a las notas.</span>';
		$data['content'] .= '<div style="background-color:#DED3FF;">'.$form0->output.'</div>';
		$data['content'] .= '<div>'.$form1->output.'</div>';
		$data['content'] .= '<div style="background-color:#FFFF6D;padding:10px"><span class="mainheader" style="font-size:'.$titusize.'em;">3-Cierre o descarte de Inventario F&iacute;sico</span>';
		$data['content'] .= '<div style="background-color:#A9FF8C;">'.$form2->output.'</div>';
		$data['content'] .= '<div style="background-color:#FFD582;">'.$form3->output.'</div>';
		$data['content'] .= '</div>';
		$data['title']   = '<h1>Inventario F&iacute;sico</h1>';
		$data['head']    = $this->rapyd->get_head().script('jquery.js').script("plugins/jquery.numeric.pack.js").script("plugins/jquery.json.min.js");
		$this->load->view('view_ventanas', $data);
	}

	function inven($tabla){
		if(!$this->db->table_exists($tabla)) show_error('Inventario f&iacute;sico no exite');
		$tifecha  =dbdate_to_human(substr($tabla,-8));
		$tialmacen=substr($tabla,3,-8);

		$this->rapyd->load('datagrid','dataobject','fields','datafilter2');

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

		$filter = new DataFilter2('');
		$filter->db->select("e.existen,e.modificado,e.contado,e.agregar,e.quitar,e.sustituir,a.tipo AS tipo,a.id,e.codigo,a.descrip,precio1,precio2,precio3,precio4,b.nom_grup AS nom_grup,a.barras,b.grupo AS grupoid,c.descrip AS nom_linea,c.linea AS linea,d.descrip AS nom_depto,d.depto AS depto,e.id AS idfis,e.despacha");
		$filter->db->from("${tabla} AS e");
		$filter->db->join('sinv AS a','a.codigo=e.codigo');
		$filter->db->join('grup AS b','a.grupo=b.grupo');
		$filter->db->join('line AS c','b.linea=c.linea');
		$filter->db->join('dpto AS d','c.depto=d.depto');
		$filter->db->where('activo','S');
		$filter->db->where('actualizado IS NULL','',false);
		//$filter->db->order_by("d.depto,c.linea,b.grupo,a.descrip");
		$filter->script($script);

		$filter->codigo = new inputField('C&oacute;digo', 'codigo');
		$filter->codigo ->db_name = 'e.codigo';
		$filter->codigo -> size=25;

		$filter->descrip = new inputField('Descripci&oacute;n', 'descrip');
		$filter->descrip->db_name='CONCAT_WS(" ",a.descrip,a.descrip2)';
		$filter->descrip -> size=25;

		$filter->tipo = new dropdownField('Tipo', 'tipo');
		$filter->tipo->db_name=('a.tipo');
		$filter->tipo->option('','Todos');
		$filter->tipo->option('Articulo' ,'Art&iacute;culo');
		$filter->tipo->option('Servicio' ,'Servicio');
		$filter->tipo->option('Descartar','Descartar');
		$filter->tipo->option('Consumo'  ,'Consumo');
		$filter->tipo->option('Fraccion' ,'Fracci&oacute;n');
		$filter->tipo ->style='width:220px;';

		$filter->clave = new inputField('Clave', 'clave');
		$filter->clave -> size=25;

		$filter->activo = new dropdownField('Activo', 'activo');
		$filter->activo->option('','Todos');
		$filter->activo->option('S','Si');
		$filter->activo->option('N','No');
		$filter->activo ->style='width:220px;';

		$filter->proveed = new inputField('Proveedor', 'proveed');
		$filter->proveed->append($bSPRV);
		$filter->proveed->db_name='CONCAT_WS("_", a.prov1, a.prov2, a.prov3)';
		$filter->proveed->size=25;

		$filter->depto2 = new inputField('Departamento', 'nom_depto');
		$filter->depto2->db_name='d.descrip';
		$filter->depto2->size=10;

		$filter->depto = new dropdownField("Departamento","depto");
		$filter->depto->db_name="d.depto";
		$filter->depto->option('',"Seleccione un Departamento");
		$filter->depto->options("SELECT depto, descrip FROM dpto WHERE tipo='I' ORDER BY depto");
		$filter->depto->in="depto2";

		$filter->linea = new inputField("Linea", "nom_linea");
		$filter->linea->db_name="c.descrip";
		$filter->linea->size=10;

		$filter->linea2 = new dropdownField("L&iacute;nea","linea");
		$filter->linea2->db_name='c.linea';
		$filter->linea2->option('','Seleccione un Departamento primero');
		$filter->linea2->in='linea';
		$depto=$filter->getval('depto');
		if($depto !== false){
			$filter->linea2->options("SELECT linea, descrip FROM line WHERE depto='$depto' ORDER BY descrip");
		}else{
			$filter->linea2->option('','Seleccione un Departamento primero');
		}

		$filter->grupo2 = new inputField('Grupo', 'nom_grupo');
		$filter->grupo2->db_name='b.nom_grup';
		$filter->grupo2 -> size=10;

		$filter->grupo = new dropdownField('Grupo', 'grupo');
		$filter->grupo->db_name='b.grupo';
		$filter->grupo->option('','Seleccione una L&iacute;nea primero');
		$filter->grupo->in='grupo2';
		$linea=$filter->getval('linea2');
		if($linea !== false){
			$filter->grupo->options("SELECT grupo, nom_grup FROM grup WHERE linea='$linea' ORDER BY nom_grup");
		}else{
			$filter->grupo->option('','Seleccione un Departamento primero');
		}

		$filter->marca = new dropdownField('Marca', 'marca');
		$filter->marca->option('','Todas');
		$filter->marca->options("SELECT TRIM(marca) AS clave, TRIM(marca) AS valor FROM marc ORDER BY marca");
		$filter->marca -> style='width:220px;';

		$filter->buttons('reset','search');
		$filter->build();

		function caja($campo2,$valor,$codigo,$readonly=false,$fila,$desp,$ccana){
			$campo = new inputField2('Title', $campo2);
			$campo->status   = 'create';
			$campo->css_class='inputnum';
			$campo->size=5;
			$campo->insertValue=$valor;
			$campo->readonly = $readonly;
			if($campo2=='c') $campo->type = 'inputhidden';
			$campo->name = $codigo;
			//$campo->name = 'I'.$campo2.'_'.$codigo;
			$campo->id   = 'I'.$campo2.'_'.$codigo;
			$campo->tabindex = $desp*$ccana+$fila;
			$campo->build();
			return $campo->output;
		}

		function pinta($modifi,$cont,$idfis,$pos){
			$iidfis="${idfis}class";
			if(empty($modifi)){
				$rt='<span class="'.$iidfis.'">'.$cont.'</span>';
			}else{
				$rt='<span class="'.$iidfis.'" style="color : red;">'.$cont.'</span>';
			}
			return $rt;
		}

		$atts = array(
			'width'      => '800',
			'height'     => '600',
			'scrollbars' => 'yes',
			'status'     => 'yes',
			'resizable'  => 'yes',
			'screenx'    => '0',
			'screeny'    => '0');

		$titulo1 = anchor_popup("reportes/ver/INVFIS/${tabla}",'Conteo',$atts);
		$titulo2 = anchor_popup('reportes/ver/SINVFIS','Hoja de trabajo',$atts);

		$cana=100;
		$grid = new DataGrid('Reportes -->'.$titulo1.' '.$titulo2 );
		$grid->table_id = 'conteotabla';
		$grid->per_page  = $cana;
		$grid->use_function('caja','pinta');
		$action = "javascript:window.location='".site_url($this->url)."'";
		$grid->button('btn_regresa', 'Regresar', $action, 'TR');

		$grid->column_orderby('Dpto',              'depto'                                                 ,'d.depto' ,'align=center');
		$grid->column_orderby('Linea',             'linea'                                                 ,'c.linea' ,'align=center');
		$grid->column_orderby('Grupo',             'grupoid'                                               ,'b.grupo' ,'align=center');
		$grid->column_orderby('Barras',            'barras'                                               ,'b.grupo' ,'align=center');
		$grid->column_orderby('Codigo',            '<pinta><#modificado#>|<#codigo#>|<#idfis#>|a</pinta>'  ,'codigo'  ,'align=center');
		$grid->column_orderby('Descripci&oacute;n','<pinta><#modificado#>|<#descrip#>|<#idfis#>|b</pinta>' ,'descrip' );
		$grid->column_orderby('P.Desp.',           '<pinta><#modificado#>|<#despacha#>|<#idfis#>|d</pinta>','despacha','align=right');
		$grid->column_orderby('Anterior',          '<pinta><#modificado#>|<#existen#>|<#idfis#>|d</pinta>' ,'existen' ,'align=right');
		$grid->column_orderby('Contado',           '<caja>c|<#contado#>|<#idfis#>|true|<#dg_row_id#>|0|'.$cana.'</caja>','contado','align=right');
		$grid->column('Agregar',                   '<caja>a|<#agregar#>|<#idfis#>|false|<#dg_row_id#>|1|'.$cana.'</caja>');
		$grid->column('Quitar',                    '<caja>q|<#quitar#>|<#idfis#>|false|<#dg_row_id#>|2|'.$cana.'</caja>');
		$grid->column('Sustituir',                 '<caja>s||<#idfis#>|false|<#dg_row_id#>|3|'.$cana.'</caja>');
		$grid->build();
		//echo $grid->db->last_query();

		$data['script']  ='<script language="javascript" type="text/javascript">

		function traer(cod){
			$.post("'.site_url($this->url.'traer').'",{ codigo:cod,tabla:"'.$tabla.'" },function(data){
				$("#Ic_"+cod).val(data);
				$("#Ic_"+cod+"_val").text(data);
			})
		}

		$(function(){
			$(".inputnum").numeric(".");

			$("input[id^=\'I\']").focus(function(){
				var cod =$(this).attr("name");
				traer(cod);
				$(this).select();
			});

			$("input[id^=\'I\']").click(function(){
				$(this).select();
			});

			$("input[id^=\'Ia_\']").change(function(){
				var cod =$(this).attr("name");
				var val = $("#Ia_"+cod).val();
				var con = $("#Ic_"+cod).val();
				$.post("'.site_url($this->url.'agregar').'",{ codigo:cod,valor:val,tabla:"'.$tabla.'",contado:con },function(data){
					$("#Ia_"+cod).val(0);
					if(data.length>0){
						alert(data);
					}else{
						$("."+cod+"class").attr("style","color:orange;");
					}
					traer(cod);
				});
			});

			$("input[id^=\'Iq_\']").change(function(){
				var cod =$(this).attr("name");
				var val = $("#Iq_"+cod).val();
				var con = $("#Ic_"+cod).val();
				$.post("'.site_url($this->url.'quitar').'",{ codigo:cod,valor:val,tabla:"'.$tabla.'",contado:con },function(data){
					$("#Iq_"+cod).val(0);
					if(data.length>0){
						alert(data);
					}else{
						$("."+cod+"class").attr("style","color:orange;");
					}
					traer(cod);
				});

			});

			$("input[id^=\'Is_\']").change(function(){
				var cod =$(this).attr("name");
				var val = $("#Is_"+cod).val();
				var con = $("#Ic_"+cod).val();
				$.post("'.site_url($this->url.'sustituir').'",{ codigo:cod,valor:val,tabla:"'.$tabla.'",contado:con },function(data){
					$("#Is_"+cod).val("");
					if(data.length>0){
						alert(data);
					}else{
						$("."+cod+"class").attr("style","color:orange;");
					}
					traer(cod);
				});
			});

		});
		</script>
		<style type="text/css">#conteotabla tr:hover { background-color: #ffff99; }</style>';

		$leyenda ='<span style="color:orange">Reci&eacute;n contado</span> ';
		$leyenda.='<span style="color:red">Ya contado</span> ';
		$leyenda.='<span style="color:black">No se ha contado</span> ';

		$salida=anchor($this->url,'Regresar');
		$data['content'] = $grid->output.$leyenda;
		$data['filtro']  = $filter->output;
		$data['title']   = heading("Conteo de inventario, fecha ${tifecha}, almac&eacute;n ${tialmacen}");
		$data['head']    = script('jquery.js');
		$data['head']   .= script('plugins/jquery.numeric.pack.js').$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function agregar(){
		$this->_actualiza('+');
	}

	function quitar(){
		$this->_actualiza('-');
	}

	function sustituir(){
		$this->_actualiza('=');
	}

	function traer(){
		$codigo = $this->db->escape($this->input->post('codigo'));
		$tabla  = $this->input->post('tabla');
		echo $this->datasis->dameval("SELECT contado FROM $tabla WHERE id=$codigo");
	}

	function _crear($alma,$fecha){
		$tabla='INV'.$alma.$fecha;
		$error='';

		if(!$this->db->table_exists($tabla)){
			$dbalma=$this->db->escape($alma);
			$mSQL="CREATE TABLE $tabla (  `id` int(11) NOT NULL AUTO_INCREMENT,
				`codigo` varchar(15) NOT NULL DEFAULT '',
				`grupo` varchar(4) DEFAULT NULL,
				`alma` varchar(4) DEFAULT 'DECA',
				`despacha` decimal(13,2) DEFAULT '0.00',
				`existen` decimal(13,2) DEFAULT '0.00',
				`contado` decimal(10,2) NOT NULL DEFAULT '0.0',
				`agregar` decimal(10,2) NOT NULL DEFAULT '0.0',
				`quitar` decimal(10,2) NOT NULL DEFAULT '0.0',
				`sustituir` decimal(10,2) NOT NULL DEFAULT '0.0',
				`fecha` datetime NOT NULL DEFAULT '0000-00-00 00:00:00',
				`modificado` datetime DEFAULT '0000-00-00 00:00:00',
				`actualizado` datetime DEFAULT '0000-00-00 00:00:00',
				`pond` decimal(10,1) NOT NULL DEFAULT '0.0',
				PRIMARY KEY (`id`),
				UNIQUE KEY `codigo` (`codigo`)
			)
			COLLATE=".$this->db->dbcollat."
			ENGINE=MyISAM
			ROW_FORMAT=DEFAULT";

			$ban=$this->db->simple_query($mSQL);
			if(!$ban){ $error.='Error creando la tabla parcial '; memowrite($mSQL,'INVFIS');  }

			$mSQL = "INSERT IGNORE INTO `${tabla}`
			(`codigo`,`grupo`,`existen`,`contado`,`agregar`,`quitar`,`sustituir`,`fecha`,`modificado`,`actualizado`,`pond`)
			SELECT TRIM(a.codigo),TRIM(a.grupo),IFNULL(b.existen,0) AS existen,0 contado,0 agregar,0 quitar,0 sustituir, NOW() fecha,CAST(NULL AS DATE ) modificado, CAST(NULL AS DATE) actualizado,a.pond
			FROM sinv a
			LEFT JOIN itsinv b ON a.codigo=b.codigo AND b.alma=${dbalma} WHERE MID(a.tipo,1,1)='A'";
			$ban=$this->db->simple_query($mSQL);
			if(!$ban){ $error.='Error llenando la tabla parcial '; memowrite($mSQL,'INVFIS'); }

			$mSQL="UPDATE sitems SET despacha='S' WHERE tipoa='D'";
			$ban=$this->db->simple_query($mSQL);
			if(!$ban){ $error.='Error colocando las devoluciones como despachados '; memowrite($mSQL,'INVFIS'); }

			$mSQL="CREATE TEMPORARY TABLE `tem${tabla}` SELECT c.codigoa,SUM(IF(c.tipoa='D',-1,1)*c.cana) AS cana
			FROM sitems AS c
			JOIN sfac AS d ON c.tipoa=d.tipo_doc AND c.numa=d.numero
			WHERE c.despacha<>'S' AND d.almacen=${dbalma}
			GROUP BY c.codigoa";
			$ban=$this->db->simple_query($mSQL);
			if(!$ban){ $error.='Error llenando la tabla de despachos '; memowrite($mSQL,'INVFIS'); }

			$mSQL="UPDATE `${tabla}` AS a JOIN `tem${tabla}` AS b ON `a`.`codigo`= `b`.`codigoa` SET `a`.`despacha`=`b`.`cana`";
			$ban=$this->db->simple_query($mSQL);
			if(!$ban){ $error.='Error llenando la tabla de despachos '; memowrite($mSQL,'INVFIS'); }
		}else{
			$error.="Ya existe un inventario creado para el almac&eacute;n $alma y la fecha seleccionada";
		}
		return $error;
	}

	function _cerrar($tabla,$tipo){

		$estampa= date('Y-m-d');
		$hora   = date('H:i:s');
		$usr    = $this->secu->usuario();
		$dbusr  = $this->db->escape($usr);
		$fecha  = substr($tabla,-8);
		$nstra  = $this->db->escape($this->datasis->fprox_numero('nstra'));
		$alma   = substr($tabla,3,strlen($tabla)-11);
		$alma   = $this->db->escape($alma);
		$error  ='';

		if($tipo)
			$where='a.modificado IS NOT NULL AND a.actualizado IS NULL'; //no asume ceros
		else
			$where='a.actualizado IS NULL'; //asume ceros
		$fromwhere="FROM $tabla a JOIN sinv b ON a.codigo=b.codigo WHERE $where";

		$cana=$this->datasis->dameval("SELECT COUNT(*) $fromwhere");

		if($cana>0){
			$id=$this->_idsem($tabla);
			$seg=sem_get($id,1,0666,-1);
			sem_acquire($seg);

			$mSQL="INSERT INTO itstra (`numero`,`codigo`,`descrip`,`cantidad`,`anteri`)
				SELECT ${nstra},a.codigo,CONCAT_WS(' ',b.descrip,b.descrip2)descrip,IF(a.modificado IS NULL,-1*a.existen,a.contado-a.existen),a.existen ${fromwhere}";

			$ban = $this->db->simple_query($mSQL);
			if(!$ban){$error.="No se pudo crear el registro en stra"; memowrite($mSQL,'INVFIS');}

			$mSQL="INSERT INTO stra (`numero`,`fecha`,`envia`,`recibe`,`observ1`,`usuario`,`estampa`,`hora`)
				VALUES (${nstra},'${fecha}','INFI',${alma},'INVENTARIO FISICO',${dbusr},'${estampa}','${hora}')";

			$ban = $this->db->simple_query($mSQL);
			if(!$ban) {$error.="No se pudo crear el registro en stra "; memowrite($mSQL,'INVFIS'); }

			if(strlen($error)==0){
				$mSQL="DROP TABLE $tabla";
				$ban = $this->db->simple_query($mSQL);
				if(!$ban) {$error.="No se pudo limpiar la base de datos"; memowrite($mSQL,'INVFIS');}
				sem_release($seg);
				sem_remove($seg);
				logusu('INVFIS',"Se creo transferencia de Inventario Fisico $nstra");
			}else{
				sem_release($seg);
			}
			logusu('invfis',"Inventario ${nstra} guardado");
		}else{
			$error='No hay productos contados para cerrar el inventario';
		}
		return $error;
	}

	function _descarte($tabla){
		$error='';
		$id=$this->_idsem($tabla);
		$seg=sem_get($id,1,0666,-1);
		sem_acquire($seg);
		if($this->db->table_exists($tabla)){
			$mSQL="DROP table $tabla";
			$rt=$this->db->simple_query($mSQL);
			if(!$rt){
				memowrite('invfis',$mSQL);
				$error='No se pudo descartar el inventario';
			}
		}
		sem_release($seg);
		sem_remove($seg);
		return $error;
	}

	function _actualiza($ac){
		// ipcs -a Ver :: ipcrm -s <semid> borra el semaforo
		$codigo = $this->db->escape($this->input->post('codigo'));
		$valor  = $this->input->post('valor');
		$tabla  = $this->input->post('tabla');
		$contado= $this->input->post('contado');
		$id     = $this->_idsem($tabla);
		$error  = 'Oops lo siento!! pero hubo un problema actualizando el registro, por favor comuniquese con soporte';

		if(is_numeric($valor)){
			$seg=sem_get($id,1,0666,-1);
			if($seg!==false){
				sem_acquire($seg);
				if($this->db->table_exists($tabla)){
					$condb  = $this->datasis->damerow("SELECT contado,actualizado FROM ${tabla} WHERE id=${codigo}");
					if(array_key_exists('contado',$condb) && array_key_exists('actualizado',$condb)){
						if($condb['actualizado']!=null){
							echo "Advertencia: El productos ya fue actualizado, no lo puede modificar";
						}elseif(round($contado,2) != round($condb['contado'],2)){
							echo "Advertencia: El valor Contado (${contado}) se modifico a ($condb[contado]) mientras usted modificaba el valor";
						}else{
							switch($ac){
								case '=':
									$mSQL="UPDATE ${tabla} SET contado=${valor},modificado=now() WHERE id=${codigo}";
								break;
								case '+':
									$mSQL="UPDATE ${tabla} SET contado=contado+${valor},modificado=now() WHERE id=${codigo}";
								break;
								case '-':
									$mSQL="UPDATE ${tabla} SET contado=contado-${valor},modificado=now() WHERE id=${codigo}";
								break;
							}
							$ban=$this->db->simple_query($mSQL);
							if(!$ban){ memowrite($mSQL,'INVFIS'); echo $error; }
						}
					}else{
						echo $error.' 1';
					}
					sem_release($seg);
				}else{
					sem_release($seg);
					sem_remove($seg);
					echo 'Advertencia: Este inventario ya fue cerrado, no puede modificar mas nada';
				}
			}
		}else{
			echo $error.' 2';
		}
	}

	function _idsem($var){
		$len=strlen($var);
		$int=0;
		for($i=0;$i<$len;$i++){
			$int+=ord($var[$i]);
		}
		return $int;
	}
}
