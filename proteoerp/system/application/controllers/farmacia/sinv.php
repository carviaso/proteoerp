<?php require_once(BASEPATH.'application/controllers/inventario/common.php');
class sinv extends Controller {

	function sinv(){
		parent::Controller();
		$this->load->library('rapyd');
	}

	function index(){
		//$this->datasis->modulo_id(309,1);
		redirect('farmacia/sinv/filteredgrid');
	}

	function filteredgrid(){
		$this->rapyd->uri->keep_persistence();
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

		//filter
		$filter = new DataFilter2('Filtro por Producto');

		$filter->db->select("a.existen AS existen,a.marca marca,a.tipo AS tipo,id,codigo,a.descrip,precio1,precio2,precio3,precio4,b.nom_grup AS nom_grup,b.grupo AS grupoid,c.descrip AS nom_linea,c.linea AS linea,d.descrip AS nom_depto,d.depto AS depto");
		$filter->db->from('sinv AS a');
		$filter->db->join('grup AS b','a.grupo=b.grupo');
		$filter->db->join('line AS c','b.linea=c.linea');
		$filter->db->join('dpto AS d','c.depto=d.depto');
		$filter->script($script);

		$filter->codigo = new inputField("C&oacute;digo", "codigo");
		$filter->codigo -> size=25;

		$filter->descrip = new inputField("Descripci&oacute;n", "descrip");
		$filter->descrip->db_name='CONCAT_WS(" ",a.descrip,a.descrip2)';
		$filter->descrip -> size=25;

		$filter->tipo = new dropdownField("Tipo", "tipo");
		$filter->tipo->db_name=("a.tipo");
		$filter->tipo->option("","Todos");
		$filter->tipo->option("Articulo","Art&iacute;culo");
		$filter->tipo->option("Servicio","Servicio");
		$filter->tipo->option("Descartar","Descartar");
		$filter->tipo->option("Consumo","Consumo");
		$filter->tipo->option("Fraccion","Fracci&oacute;n");
		$filter->tipo ->style='width:220px;';

		$filter->clave = new inputField("Clave", "clave");
		$filter->clave -> size=25;

		$filter->activo = new dropdownField("Activo", "activo");
		$filter->activo->option("","");
		$filter->activo->option("S","Si");
		$filter->activo->option("N","No");
		$filter->activo ->style='width:220px;';

		$filter->proveed = new inputField("Proveedor", "proveed");
		$filter->proveed->append($bSPRV);
		$filter->proveed->clause ="in";
		$filter->proveed->db_name='( a.prov1, a.prov2, a.prov3 )';
		$filter->proveed -> size=25;

		$filter->depto2 = new inputField("Departamento", "nom_depto");
		$filter->depto2->db_name="d.descrip";
		$filter->depto2 -> size=10;

		$filter->depto = new dropdownField("Departamento","depto");
		$filter->depto->db_name="d.depto";
		$filter->depto->option("","Seleccione un Departamento");
		$filter->depto->options("SELECT depto, descrip FROM dpto WHERE tipo='I' ORDER BY depto");
		$filter->depto->in="depto2";

		$filter->linea = new inputField("Linea", "nom_linea");
		$filter->linea->db_name="c.descrip";
		$filter->linea -> size=10;

		$filter->linea2 = new dropdownField("L&iacute;nea","linea");
		$filter->linea2->db_name="c.linea";
		$filter->linea2->option("","Seleccione un Departamento primero");
		$filter->linea2->in="linea";
		$depto=$filter->getval('depto');
		if($depto!==FALSE){
			$filter->linea2->options("SELECT linea, descrip FROM line WHERE depto='$depto' ORDER BY descrip");
		}else{
			$filter->linea2->option("","Seleccione un Departamento primero");
		}

		$filter->grupo2 = new inputField("Grupo", "nom_grupo");
		$filter->grupo2->db_name="b.nom_grup";
		$filter->grupo2 -> size=10;

		$filter->grupo = new dropdownField("Grupo", "grupo");
		$filter->grupo->db_name="b.grupo";
		$filter->grupo->option("","Seleccione una L&iacute;nea primero");
		$filter->grupo->in="grupo2";
		$linea=$filter->getval('linea2');
		if($linea!==FALSE){
			$filter->grupo->options("SELECT grupo, nom_grup FROM grup WHERE linea='$linea' ORDER BY nom_grup");
		}else{
			$filter->grupo->option("","Seleccione un Departamento primero");
		}

		$filter->marca = new dropdownField("Marca", "marca");
		$filter->marca->option('','Todas');
		$filter->marca->options("SELECT TRIM(marca) AS clave, TRIM(marca) AS valor FROM marc ORDER BY marca");
		$filter->marca->style='width:220px;';
		$filter->marca->rule='required';

		$filter->buttons("reset","search");
		$filter->build();

		$uri = "farmacia/sinv/dataedit/show/<#codigo#>";

		$grid = new DataGrid("Lista de Art&iacute;culos");
		$grid->order_by("codigo","asc");
		$grid->per_page = 15;
		$link  = anchor('farmacia/sinv/dataedit/show/<#id#>','<#codigo#>');
		$uri_2 = anchor('farmacia/sinv/dataedit/create/<#id#>','Duplicar');

		$grid->column_orderby("C&oacute;digo",$link,"codigo");
		//$grid->column("Departamento","<#nom_depto#>"   ,'align=left');
		//$grid->column("L&iacute;nea","<#nom_linea#>"   ,'align=left');
		//$grid->column("Grupo","<#nom_grup#>",'align=left');
		$grid->column_orderby("Descripci&oacute;n","descrip","descrip");
		$grid->column_orderby("Marca","marca","marca");
		$grid->column_orderby("Precio 1","<nformat><#precio1#></nformat>","precio1",'align=right');
		$grid->column_orderby("Precio 2","<nformat><#precio2#></nformat>","precio2",'align=right');
		$grid->column_orderby("Precio 3","<nformat><#precio3#></nformat>","precio3",'align=right');
		$grid->column_orderby("Existencia","<nformat><#existen#></nformat>","existen",'align=right');
		//$grid->column("Precio 4","<nformat><#precio4#></nformat>",'align=right');
		$grid->column("Acci&oacute;n",$uri_2     ,"align='center'");

		$grid->add('inventario/sinv/dataedit/create');
		$grid->build();

		$data['content'] = $filter->output.$grid->output;
		$data['title']   = '<h1>Maestro de Inventario</h1>';
		$data['head']    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").script("sinvmaes2.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function dataedit($status='',$id='' ) {
		$this->rapyd->uri->keep_persistence();
		$this->rapyd->load('dataedit2','dataobject');

		$link  =site_url('inventario/common/add_marc');
		$link4 =site_url('inventario/common/get_marca');
		$link5 =site_url('inventario/common/add_unidad');
		$link6 =site_url('inventario/common/get_unidad');
		$link7 =site_url('inventario/sinv/ultimo');
		$link8 =site_url('inventario/sinv/sugerir');
		$link9 =site_url('inventario/common/add_depto');
		$link10=site_url('inventario/common/get_depto');
		$link11=site_url('inventario/common/add_linea');
		$link12=site_url('inventario/common/get_linea');
		$link13=site_url('inventario/common/add_grupo');
		$link14=site_url('inventario/common/get_grupo');

		$script='
		function dpto_change(){
			$.post("'.$link12.'",{ depto:$("#depto").val() },function(data){$("#linea").html(data);})
			$.post("'.$link14.'",{ linea:"" },function(data){$("#grupo").html(data);})
		}
		$(function(){
			$("#depto").change(function(){dpto_change(); });
			$("#linea").change(function(){ $.post("'.$link14.'",{ linea:$(this).val() },function(data){$("#grupo").html(data);}) });

			$("#tdecimal").change(function(){
				var clase;
				if($(this).attr("value")=="S") clase="inputnum"; else clase="inputonlynum";
				$("#exmin").unbind();$("#exmin").removeClass(); $("#exmin").addClass(clase);
				$("#exmax").unbind();$("#exmax").removeClass(); $("#exmax").addClass(clase);
				$("#exord").unbind();$("#exord").removeClass(); $("#exord").addClass(clase);
				$("#exdes").unbind();$("#exdes").removeClass(); $("#exdes").addClass(clase);

				$(".inputnum").numeric(".");
				$(".inputonlynum").numeric("0");
			});
			cambioprecio("I");
			//requeridos(true);
		});

		function ultimo(){
			$.ajax({
				url: "'.$link7.'",
				success: function(msg){
				  alert( "El &uacute;ltimo c&oacute;digo ingresado fue: " + msg );
				}
			});
		}

		function sugerir(){
			$.ajax({
				url: "'.$link8.'",
				success: function(msg){
					if(msg){
						$("#codigo").val(msg);
					}
					else{
						alert("No es posible generar otra sugerencia. Coloque el c&oacute;digo manualmente");
					}
				}
			});
		}

		function add_marca(){
			marca=prompt("Introduza el nombre de la MARCA a agregar");
			if(marca==null){
			}else{
				$.ajax({
				 type: "POST",
				 processData:false,
					url: "'.$link.'",
					data: "valor="+marca,
					success: function(msg){
						if(msg=="s.i"){
							marca=marca.substr(0,30);
							$.post("'.$link4.'",{ x:"" },function(data){$("#marca").html(data);$("#marca").val(marca);})
						}
						else{
							alert("Disculpe. En este momento no se ha podido agregar la marca, por favor intente mas tarde");
						}
					}
				});
			}
		}

		function add_unidad(){
			unidad=prompt("Introduza el nombre de la UNIDAD a agregar");
			if(unidad==null){
			}else{
				$.ajax({
				 type: "POST",
				 processData:false,
					url: "'.$link5.'",
					data: "valor="+unidad,
					success: function(msg){
						if(msg=="s.i"){
							unidad=unidad.substr(0,8);
							$.post("'.$link6.'",{ x:"" },function(data){$("#unidad").html(data);$("#unidad").val(unidad);})
						}
						else{
							alert("Disculpe. En este momento no se ha podido agregar la unidad, por favor intente mas tarde");
						}
					}
				});
			}
		}

		function add_depto(){
			depto=prompt("Introduza el nombre del DEPARTAMENTO a agregar");
			if(depto==null){
			}else{
				$.ajax({
				 type: "POST",
				 processData:false,
					url: "'.$link9.'",
					data: "valor="+depto,
					success: function(msg){
						if(msg=="Y.a-Existe"){
							alert("Ya existe un Departamento con esa Descripcion");
						}
						else{
							if(msg=="N.o-SeAgrego"){
								alert("Disculpe. En este momento no se ha podido agregar el departamento, por favor intente mas tarde");
							}else{
								$.post("'.$link10.'",{ x:"" },function(data){$("#depto").html(data);$("#depto").val(msg);})
							}
						}
					}
				});
			}
		}

		function add_linea(){
			deptoval=$("#depto").val();
			if(deptoval==""){
				alert("Debe seleccionar un Departamento al cual agregar la linea");
			}else{
				linea=prompt("Introduza el nombre de la LINEA a agregar al DEPARTAMENTO seleccionado");
				if(linea==null){
				}else{
					$.ajax({
					 type: "POST",
					 processData:false,
						url: "'.$link11.'",
						data: "valor="+linea+"&&valor2="+deptoval,
						success: function(msg){
							if(msg=="Y.a-Existe"){
								alert("Ya existe una Linea con esa Descripcion");
							}
							else{
								if(msg=="N.o-SeAgrego"){
									alert("Disculpe. En este momento no se ha podido agregar la linea, por favor intente mas tarde");
								}else{
									$.post("'.$link12.'",{ depto:deptoval },function(data){$("#linea").html(data);$("#linea").val(msg);})
								}
							}
						}
					});
				}
			}
		}

		function add_grupo(){
			lineaval=$("#linea").val();
			deptoval=$("#depto").val();
			if(lineaval==""){
				alert("Debe seleccionar una Linea a la cual agregar el departamento");
			}else{
				grupo=prompt("Introduza el nombre del GRUPO a agregar a la LINEA seleccionada");
				if(grupo==null){
				}else{
					$.ajax({
					 type: "POST",
					 processData:false,
						url: "'.$link13.'",
						data: "valor="+grupo+"&&valor2="+lineaval+"&&valor3="+deptoval,
						success: function(msg){
							if(msg=="Y.a-Existe"){
								alert("Ya existe una Linea con esa Descripcion");
							}
							else{
								if(msg=="N.o-SeAgrego"){
									alert("Disculpe. En este momento no se ha podido agregar la linea, por favor intente mas tarde");
								}else{
									$.post("'.$link14.'",{ linea:lineaval },function(data){$("#grupo").html(data);$("#grupo").val(msg);})
								}
							}
						}
					});
				}
			}
		}';

		$do = new DataObject('sinv');
		if($status=='create' && !empty($id)){
			$do->load($id);
			$do->set('codigo', '');
		}

		$edit = new DataEdit2('Maestro de Inventario', $do);
		$edit->back_save   = true;
		$edit->back_cancel = true;
		$edit->back_cancel_save   = true;
		$edit->back_cancel_delete = true;
		$edit->back_url = site_url('ajax/reccierraventana');

		$edit->pre_process('insert' ,'_pre_insert');
		$edit->post_process('insert','_post_insert');

		$edit->script($script,'create');
		$edit->script($script,'modify');

		/*$edit->codigo = new inputField("C&oacute;digo", "codigo");
		$edit->codigo->size=20;
		$edit->codigo->maxlength=15;
		$edit->codigo->rule = "trim|required|strtoupper|callback_chexiste";
		$edit->codigo->mode="autohide";*/

		$edit->alterno = new inputField("C&oacute;digo Alterno", "alterno");
		$edit->alterno->size=20;
		$edit->alterno->maxlength=15;
		$edit->alterno->rule = "trim|strtoupper|callback_chexiste2";

		$edit->enlace  = new inputField("C&oacute;digo Caja", "enlace");
		$edit->enlace ->size=20;
		$edit->enlace->maxlength=15;
		$edit->enlace->rule = "trim|strtoupper";

		$edit->barras = new inputField("C&oacute;digo Barras", "barras");
		$edit->barras->size=20;
		$edit->barras->maxlength=15;
		$edit->barras->rule = 'trim|unique';

		$edit->tipo = new dropdownField('Tipo', 'tipo');
		$edit->tipo->style='width:180px;';
		$edit->tipo->option('Articulo','Art&iacute;culo' );
		//$edit->tipo->option('Servicio','Servicio');
		//$edit->tipo->option('Descartar','Descartar');
		//$edit->tipo->option('Consumo','Consumo');
		//$edit->tipo->option('Fraccion','Fracci&oacute;n');
		//$edit->tipo->option('Lote','Lote');

		$AddUnidad='<a href="javascript:add_unidad();" title="Haz clic para Agregar una unidad nueva">Agregar Unidad</a>';
		$edit->unidad = new dropdownField("Unidad","unidad");
		$edit->unidad->style='width:180px;';
		$edit->unidad->option("","");
		$edit->unidad->options("SELECT unidades, unidades as valor FROM unidad ORDER BY unidades");
		$edit->unidad->append($AddUnidad);

		$edit->clave = new inputField('Clave', 'clave');
		$edit->clave->size=10;
		$edit->clave->maxlength=8;
		$edit->clave->rule = 'trim|strtoupper';

		$AddDepto='<a href="javascript:add_depto();" title="Haz clic para Agregar un nuevo Departamento">Agregar Departamento</a>';
		$edit->depto = new dropdownField('Departamento', 'depto');
		$edit->depto->rule ='required';
		//$edit->depto->onchange = "get_linea();";
		$edit->depto->option('','Seleccione un Departamento');
		$edit->depto->options("SELECT depto, descrip FROM dpto WHERE tipo='I' ORDER BY depto");
		$edit->depto->append($AddDepto);

		$AddLinea='<a href="javascript:add_linea();" title="Haz clic para Agregar una nueva Linea;">Agregar Linea</a>';
		$edit->linea = new dropdownField('L&iacute;nea','linea');
		$edit->linea->rule ='required';
		$edit->linea->append($AddLinea);
		$depto=$edit->getval('depto');
		if($depto!==false){
			$dbdepto=$this->db->escape($depto);
			$edit->linea->options("SELECT linea, descrip FROM line WHERE depto=${dbdepto} ORDER BY descrip");
		}else{
			$edit->linea->option('','Seleccione un Departamento primero');
		}

		$AddGrupo='<a href="javascript:add_grupo();" title="Haz clic para Agregar un nuevo Grupo;">Agregar Grupo</a>';
		$edit->grupo = new dropdownField("Grupo", "grupo");
		$edit->grupo->rule="required";
		$edit->grupo->append($AddGrupo);
		$linea=$edit->getval('linea');
		if($linea!==false){
			$dblinea = $this->db->escape($linea);
			$edit->grupo->options("SELECT grupo, nom_grup FROM grup WHERE linea=${dblinea} ORDER BY nom_grup");
		}else{
			$edit->grupo->option('','Seleccione un Departamento primero');
		}

		$edit->fracci  = new inputField('Unidad por Caja', 'fracci');
		$edit->fracci ->size=10;
		$edit->fracci->maxlength=4;
		$edit->fracci->css_class='inputnum';
		$edit->fracci->rule='numeric|callback_positivo|trim';

		$edit->activo = new dropdownField('Activo', 'activo');
		$edit->activo->style='width:100px;';
		$edit->activo->option('S','Si' );
		$edit->activo->option('N','No' );

		$edit->serial2 = new freeField('','free','Serial');
		$edit->serial2->in='activo';

		$edit->serial = new dropdownField ('Serial', 'serial');
		$edit->serial->style='width:100px;';
		$edit->serial->option('N','No' );
		$edit->serial->option('S','Si' );
		$edit->serial->in="activo";

		$edit->tdecimal2 = new freeField('','free','Unidad Decimal');
		$edit->tdecimal2->in='activo';

		$edit->tdecimal = new dropdownField('Unidad Decimal', 'tdecimal');
		$edit->tdecimal->style='width:100px;';
		$edit->tdecimal->option('N','No' );
		$edit->tdecimal->option('S','Si' );
		$edit->tdecimal->in="activo";

		$edit->descrip = new inputField('Descripci&oacute;n', 'descrip');
		$edit->descrip->size=50;
		$edit->descrip->maxlength=45;
		$edit->descrip->rule = 'trim|required|strtoupper';

		$edit->descrip2 = new inputField("Descripci&oacute;n", "descrip2");
		$edit->descrip2->size=50;
		$edit->descrip2->maxlength=45;
		$edit->descrip2->rule = "trim|strtoupper";

		$AddMarca='<a href="javascript:add_marca();" title="Haz clic para Agregar una marca nueva">Agregar Marca</a>';
		$edit->marca = new dropdownField('Marca', 'marca');
		$edit->marca->style='width:180px;';
		$edit->marca->option('','Seleccionar');
		$edit->marca->rule='required';
		$edit->marca->options("SELECT marca AS codigo, marca FROM marc ORDER BY marca");
		$edit->marca->append($AddMarca);

		/*$edit->modelo  = new inputField("Modelo", "modelo");
		$edit->modelo->size=20;
		$edit->modelo->maxlength=20;
		$edit->modelo->rule = "trim|strtoupper";*/

		$edit->clase= new dropdownField('Clase', 'clase');
		$edit->clase->style='width:180px;';
		$edit->clase->option('A','Alta Rotacion');
		$edit->clase->option('B','Media Rotacion');
		$edit->clase->option('C','Baja Rotacion');
		$edit->clase->option('I','Importacion Propia');

		$edit->iva = new inputField('IVA', 'iva');
		$edit->iva->css_class='inputnum';
		$edit->iva->size=10;
		$edit->iva->maxlength=6;
		$edit->iva->onchange = 'requeridos();';
		$edit->iva->append('%');
		if($edit->_status=='create'){
			$iva=$this->datasis->dameval("SELECT valor FROM valores WHERE nombre='IVA'");
			$edit->iva->insertValue=($iva);
		}

		$edit->ultimo = new inputField('Ultimo', 'ultimo');
		$edit->ultimo->css_class='inputnum';
		$edit->ultimo->size=10;
		$edit->ultimo->maxlength=13;
		$edit->ultimo->onchange = 'requeridos();';
		$edit->ultimo->rule='required';

		$edit->pond = new inputField('Promedio', 'pond');
		$edit->pond->css_class='inputnum';
		$edit->pond->size=10;
		$edit->pond->maxlength=13;
		$edit->pond->onchange = 'requeridos();';
		$edit->pond->rule='required';

		$edit->formcal = new dropdownField("Base C&aacute;lculo", "formcal");
		$edit->formcal->style='width:100px;';
		//$edit->formcal->rule='required';
		//$edit->formcal->option('','Seleccione' );
		$edit->formcal->option('U','Ultimo'  );
		$edit->formcal->option('P','Promedio');
		$edit->formcal->option('M','Mayor'   );
		$edit->formcal->onchange = "requeridos();calculos('I');";

		$edit->redecen = new dropdownField('Redondear', 'redecen');
		$edit->redecen->style='width:100px;';
		$edit->redecen->option('N','No');
		$edit->redecen->option('F','Fracci&oacute;n');
		$edit->redecen->option('D','Decena' );
		$edit->redecen->option('C','Centena'  );
		//$edit->redecen->onchange = "redon();";

		for($i=1;$i<=4;$i++){
			$objeto="margen$i";
			$edit->$objeto = new inputField("Margen $i", $objeto);
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=10;
			$edit->$objeto->maxlength=6;
			$edit->$objeto->onchange = "calculos('I');";
			$edit->$objeto->rule='required';

			$objeto="Ebase$i";
			$edit->$objeto = new freeField('','',"Precio $i");
			$edit->$objeto->in="margen$i";

			$objeto="base$i";
			$edit->$objeto = new inputField("Base $i", $objeto);
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=10;
			$edit->$objeto->maxlength=13;
			$edit->$objeto->in="margen$i";
			$edit->$objeto->onchange = "cambiobase('I');";
			$edit->$objeto->rule="required";

			$objeto="Eprecio$i";
			$edit->$objeto = new freeField('','',"Precio + I.V.A. $i");
			$edit->$objeto->in="margen$i";

			$objeto="precio$i";
			$edit->$objeto = new inputField("Margen $i", $objeto);
			$edit->$objeto->css_class='inputnum';
			$edit->$objeto->size=10;
			$edit->$objeto->maxlength=6;
			$edit->$objeto->in="margen$i";
			$edit->$objeto->onchange = "cambioprecio('I');";
			$edit->$objeto->rule="required";
		}

		$edit->descufijo = new inputField('Descuento fijo', 'descufijo');
		$edit->descufijo->size=10;
		$edit->descufijo->maxlength=12;
		$edit->descufijo->css_class='inputnum';
		$edit->descufijo->rule='numeric|callback_positivo|callback_chrequerido|trim';

		$edit->exmin = new inputField("Existencia Minima", "exmin");
		$edit->exmin->size=10;
		$edit->exmin->maxlength=12;
		$edit->exmin->css_class='inputonlynum';
		$edit->exmin->rule='numeric|callback_positivo|trim';

		$edit->exmax = new inputField('Existencia Maxima', 'exmax');
		$edit->exmax->size=10;
		$edit->exmax->maxlength=12;
		$edit->exmax->css_class='inputonlynum';
		$edit->exmax->rule='numeric|callback_positivo|trim';

		$edit->exord = new inputField('Existencia Ordenada','exord');
		$edit->exord->when =array("show");

		$edit->exdes = new inputField("Pedido","exdes");
		$edit->exdes->when =array('show');

		$edit->existen = new inputField("Existencia Actual","existen");
		$edit->existen->when =array("show");

		for($i=1;$i<=3;$i++){
			$objeto="pfecha$i";
			$edit->$objeto = new dateField("Fecha $i",$objeto,'d/m/Y');
			$edit->$objeto->when =array('show');
			$edit->$objeto->size=10;

			$objeto="Eprepro$i";
			$edit->$objeto = new freeField('','','Precio');
			$edit->$objeto->in="pfecha$i";
			$edit->$objeto->when =array('show');

			$objeto="prepro$i";
			$edit->$objeto = new inputField('',$objeto);
			$edit->$objeto->when =array('show');
			$edit->$objeto->size=10;
			$edit->$objeto->in="pfecha$i";

			$objeto="Eprov$i";
			$edit->$objeto = new freeField('','','Proveedor');
			$edit->$objeto->in="pfecha$i";
			$edit->$objeto->when =array('show');

			if($edit->_status=='show'){
				$prov  =$edit->_dataobject->get('prov'.$i);
				$dbprov=$this->db->escape($prov);
				$proveed=$this->datasis->dameval("SELECT nombre FROM sprv WHERE proveed=${dbprov} LIMIT 1");
				$objeto="proveed$i";
				$edit->$objeto= new freeField('','',$proveed);
				$edit->$objeto->in="pfecha$i";
			}
		}

		$codigo=$edit->_dataobject->get('codigo');
		$edit->almacenes = new containerField('almacenes',$this->_detalle($codigo));
		$edit->almacenes->when = array('show','modify');

		$edit->buttons('modify', 'save', 'undo', 'delete', 'back');
		$edit->build();

		$this->rapyd->jquery[]='$(window).unload(function() { window.opener.location.reload(); });';

		$data['content'] = $edit->output;
		$data['title']   = heading('Inventario de Farmacia');
		$data['head']    = script('jquery.pack.js').script('plugins/jquery.numeric.pack.js').script("plugins/jquery.floatnumber.js").script("sinvmaes.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function _pre_insert($do){
		$size='6';
		$mSQL="SELECT LPAD(a.hexa,${size},0) AS val FROM serie AS a LEFT JOIN sinv AS b ON b.codigo=LPAD(a.hexa,${size},0) WHERE valor<16777215 AND b.codigo IS NULL LIMIT 1";

		$codigo=$this->datasis->dameval($mSQL);
		if(empty($codigo)){
			$do->error_message_ar['pre_ins']='C&oacute;digos agotados';
			return false;
		}
		$do->set('codigo',$codigo);
		return true;
	}

	function _post_insert($do){
		$codigo=$do->get('codigo');

		$precio1=$do->get('precio1');
		$precio2=$do->get('precio2');
		$precio3=$do->get('precio3');
		$precio4=$do->get('precio4');

		$query = $this->db->query('SELECT ubica FROM caub WHERE gasto=\'N\' AND invfis=\'N\'');
		foreach ($query->result() as $row){
			$sql = $this->db->insert_string('itsinv', array('codigo' => $codigo,'alma'=>$row->ubica, 'existen'=>0));
			$this->db->simple_query($sql);
		}

		logusu('sinv',"Creo ${codigo} farmacia precios: ${precio1}, ${precio2}, ${precio3}, ${precio4}");
	}

	function sug($tabla=''){
		if($tabla=='dpto'){
			$valor=$this->datasis->dameval("SELECT LPAD(hexa,2,0) FROM serie LEFT JOIN dpto ON LPAD(depto,2,0)=LPAD(hexa,2,0) WHERE valor<255 AND depto IS NULL LIMIT 1");
		}elseif($tabla=='line'){
				$valor=$this->datasis->dameval("SELECT LPAD(hexa,2,0) FROM serie LEFT JOIN line ON LPAD(linea,2,0)=LPAD(hexa,2,0) WHERE valor<255 AND linea IS NULL LIMIT 1");
		}elseif($tabla=='grup'){
				$valor=$this->datasis->dameval("SELECT LPAD(hexa,4,0) FROM serie LEFT JOIN grup ON LPAD(grupo,4,0)=LPAD(hexa,4,0) WHERE valor<65535 AND grupo IS NULL LIMIT 1");
		}
		return $valor;
	}

	function ultimo(){
		$ultimo=$this->datasis->dameval("SELECT codigo FROM sinv ORDER BY codigo DESC LIMIT 1");
		echo $ultimo;
	}

	function sugerir(){
		$ultimo=$this->datasis->dameval("SELECT LPAD(hexa,4,0) FROM serie LEFT JOIN sinv ON LPAD(codigo,4,0)=LPAD(hexa,4,0) WHERE valor<65535 AND codigo IS NULL LIMIT 1");
		echo $ultimo;
	}

	function chrequerido($monto){
			//$this->validation->set_message('chrequerido',"El codigo alterno $alterno ya existe para el producto $descrip");
			//return FALSE;
			return TRUE;
	}

	function chexiste($codigo){
		//$codigo=$this->input->post('codigo');
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM sinv WHERE codigo='$codigo'");
		if ($check > 0){
			$descrip=$this->datasis->dameval("SELECT descrip FROM sinv WHERE codigo='$codigo'");
			$this->validation->set_message('chexiste',"El codigo $codigo ya existe para el producto $descrip");
			return FALSE;
		}else {
		 return TRUE;
		}
	}

	function chexiste2($alterno){
		$check=$this->datasis->dameval("SELECT COUNT(*) FROM sinv WHERE alterno='$alterno'");
		if ($check > 0){
			$descrip=$this->datasis->dameval("SELECT descrip FROM sinv WHERE alterno='$alterno'");
			$this->validation->set_message('chexiste',"El codigo alterno $alterno ya existe para el producto $descrip");
			return FALSE;
		}else {
			return TRUE;
		}
	}

	function _detalle($codigo){
	$salida='';

		if(!empty($codigo)){
			$this->rapyd->load('dataedit','datagrid');

			$grid = new DataGrid('Cantidad por almac&eacute;n');
			$grid->db->select(array('b.ubides','a.codigo','a.alma','a.existen',"IF(b.ubides IS NULL,'ALMACEN INCONSISTENTE',b.ubides) AS nombre"));
			$grid->db->from('itsinv AS a');
			$grid->db->join('caub as b','a.alma=b.ubica','LEFT');
			$grid->db->where('codigo',$codigo);

			$grid->column('Almac&eacute;n','alma');
			$grid->column('Nombre'       ,'<#nombre#>');
			$grid->column('Cantidad'      ,'existen','align="RIGHT"');

			$grid->build();
			if($grid->recordCount>0) $salida=$grid->output;
		}
		return $salida;
	}

	function instalar(){
		$mSQL='ALTER TABLE `sinv` DROP PRIMARY KEY';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE `sinv` ADD UNIQUE `codigo` (`codigo`)';
		$this->db->simple_query($mSQL);
		$mSQL='ALTER TABLE sinv ADD id INT AUTO_INCREMENT PRIMARY KEY';
		$this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `sinv`  ADD COLUMN `descufijo` DECIMAL(6,3) NULL DEFAULT '0.000' AFTER `id`";
		$this->db->simple_query($mSQL);

		$mSQL="CREATE TABLE IF NOT EXISTS `sinvcombo` (
		`combo` char(15) NOT NULL,
		`codigo` char(15) NOT NULL default '',
		`descrip` char(30) default NULL,
		`cantidad` decimal(10,3) default NULL,
		`precio` decimal(15,2) default NULL,
		`transac` char(8) default NULL,
		`estampa` date default NULL,
		`hora` char(8) default NULL,
		`usuario` char(12) default NULL,
		`costo` decimal(17,2) default '0.00',
		PRIMARY KEY  (`combo`,`codigo`)
		) ENGINE=MyISAM DEFAULT CHARSET=latin1";
		$this->db->simple_query($mSQL);
	}

}
