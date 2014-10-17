<?php
class compras extends Controller {

	function compras(){
		parent::Controller();
		$this->load->library("rapyd");
		//$this->datasis->modulo_id(604,1);
	}

	function index() {		
		$this->rapyd->load("datagrid","datafilter");
		
		$modbus=array(
			'tabla'   =>'sprv',
			'columnas'=>array(
				'proveed' =>'C&oacute;digo Proveedor',
				'nombre'=>'Nombre',
				'rif'=>'RIF'),
			'filtro'  =>array('proveed'=>'C&oacute;digo Proveedor','nombre'=>'Nombre'),
			'retornar'=>array('proveed'=>'proveed'),
			'titulo'  =>'Buscar Proveedor');
		
		$boton=$this->datasis->modbus($modbus);
		
		$filter = new DataFilter("Filtro de Compras");
		$filter->db->select('numero,fecha,vence,nombre,montoiva,montonet,proveed,control');
		$filter->db->from('scst');
		
		$filter->fechad = new dateonlyField("Desde", "fechad",'d/m/Y');
		$filter->fechah = new dateonlyField("Hasta", "fechah",'d/m/Y');
		$filter->fechad->clause  =$filter->fechah->clause="where";
		$filter->fechad->db_name =$filter->fechah->db_name="fecha";
		$filter->fechad->insertValue = date("Y-m-d"); 
		$filter->fechah->insertValue = date("Y-m-d"); 
		$filter->fechah->size=$filter->fechad->size=10;
		$filter->fechad->operator=">="; 
		$filter->fechah->operator="<=";
		
		$filter->numero = new inputField("N&uacute;mero", "numero");
		$filter->numero->size=20;

		$filter->proveedor = new inputField("Proveedor", "proveed");
		$filter->proveedor->append($boton);
		$filter->proveedor->db_name = "proveed";
		$filter->proveedor->size=20;

		$filter->buttons("reset","search");
		$filter->build();
    
		$uri = anchor('finanzas/compras/dataedit/show/<#control#>','<#control#>');
    
		$grid = new DataGrid();
		$grid->order_by("numero","desc");
		$grid->per_page = 15;
		
		$grid->column("N&uacute;mero",$uri);
		$grid->column("Fecha","<dbdate_to_human><#fecha#></dbdate_to_human>","align='center'");
		$grid->column("Vence","<dbdate_to_human><#vence#></dbdate_to_human>","align='center'");
		$grid->column("Nombre","nombre");
		$grid->column("IVA"  , "<number_format><#montoiva#>|2|,|.</number_format>",'align=right');
		$grid->column("Monto" , "<number_format><#montonet#>|2|,|.</number_format>",'align=right');
			
		$grid->add("finanzas/compras/dataedit/create");
		$grid->build();
		//echo $grid->db->last_query();
		
		$data['content'] =$filter->output.$grid->output;
		$data["head"]    = $this->rapyd->get_head();
		$data['title']   ='<h1>Compras</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function dataedit(){
 		$this->rapyd->load("dataedit","datadetalle","fields","datagrid");
 		
 		$formato=$this->datasis->dameval('SELECT formato FROM cemp LIMIT 0,1');
 		$qformato='%';
 		for($i=1;$i<substr_count($formato, '.')+1;$i++) $qformato.='.%';
 		$this->qformato=$qformato;
 		
 		 	$modbus=array(
			'tabla'   =>'sinv',
			'columnas'=>array(
				'codigo' =>'C&oacute;digo',
				'descrip'=>'descrip'),
			'filtro'  =>array('codigo' =>'C&oacute;digo','descrip'=>'descrip'),
			'retornar'=>array('codigo'=>'codigo<#i#>','precio1'=>'precio1<#i#>','precio2'=>'precio2<#i#>','precio3'=>'precio3<#i#>','precio4'=>'precio4<#i#>','iva'=>'iva<#i#>','pond'=>'costo<#i#>'),
			'p_uri'=>array(4=>'<#i#>'),
			'titulo'  =>'Buscar Articulo');
 		
 		//Script necesario para totalizar los detalles
 		
		$fdepar = new dropdownField("ccosto", "ccosto");    
		$fdepar->options("SELECT depto,descrip FROM dpto WHERE tipo='G' ORDER BY descrip");
		$fdepar->status='create';
		$fdepar->build();
		$dpto=$fdepar->output;
		
		$dpto=trim($dpto);
		$dpto=preg_replace('/\n/i', '', $dpto);
 		
 		$uri=site_url("/contabilidad/casi/dpto/");

 		$script='
 		function totalizar(){
 			monto=debe=haber=0;
 			amonto=$$(\'input[id^="monto"]\');
			for(var i=0; i<amonto.length; i++) {
    		valor=parseFloat(amonto[i].value);
    		if (isNaN(valor))
					valor=0.0;
				if (valor>0)
    			haber=haber+valor;
    		else{
    			valor=valor*(-1);
    			debe=debe+valor;
    		}
				$("haber").value=haber;
    		$("debe").value=debe;
				$("total").value=haber-debe;
			}
		}
		function departa(i){
			ccosto=$F(\'ccosto\'+i.toString())
			if (ccosto==\'S\'){
				//alert("come una matina");
				departamen=window.open("'.$uri.'/"+i.toString(),"buscardeparta","width=500,height=200,scrollbars=Yes,status=Yes,resizable=Yes,screenx=5,screeny=5,top="+ ((screen.height - 200) / 2) + ",left=" + ((screen.width - 500) / 2)); 
				departamen.focus();
				//new Insertion.Before(\'departa\'+i.toString(), \''.$dpto.'\')
			}
		}
		';
 		
		$edit = new DataEdit("Compras","scst");
		
/*		
		$edit->_dataobject->db->set('transac', 'MANUAL');
		$edit->_dataobject->db->set('origen' , 'MANUAL');
		$edit->_dataobject->db->set('usuario', $this->session->userdata('usuario'));
		$edit->_dataobject->db->set('hora'   , 'CURRENT_TIME()', FALSE);
		$edit->_dataobject->db->set('estampa', 'NOW()', FALSE);
		
		$edit->post_process("insert","_guarda_detalle");
		$edit->post_process("update","_actualiza_detalle");
		$edit->post_process("delete","_borra_detalle");
		$edit->pre_process('delete','_pre_del');
*/
		
		$edit->back_url = "finanzas/compras";
		
		$edit->fecha = new DateonlyField("Fecha", "fecha","d/m/Y");
		$edit->fecha->insertValue = date("Y-m-d");
		$edit->fecha->mode="autohide";
		$edit->fecha->size = 10;
		
		$edit->vence = new DateonlyField("Vence", "vence","d/m/Y");
		$edit->vence->insertValue = date("Y-m-d");
		$edit->vence->size = 10;
			
		$edit->numero = new inputField("N&uacute;mero", "numero");
		$edit->numero->size = 10;
		$edit->numero->rule= "required";
		$edit->numero->mode="autohide";
		$edit->numero->maxlength=8;
		
		$edit->proveedor = new inputField("Proveedor", "proveed");
		$edit->proveedor->size = 10;        
		$edit->proveedor->maxlength=5;
		
		$edit->nombre = new inputField("Nombre", "nombre");
		$edit->nombre->size = 50;
		$edit->nombre->maxlength=40;   
		
		$edit->cfis = new inputField("C.fis", "nfiscal");
		$edit->cfis->size = 15;
		$edit->cfis->maxlength=8;
		
		$edit->almacen = new inputField("Almance", "depo");
		$edit->almacen->size = 15;
		$edit->almacen->maxlength=8;
		
		$edit->tipo = new dropdownField("Tipo", "tipo_doc");  
		$edit->tipo->option("FC","Factura");  
		$edit->tipo->option("NC","Nota Credito");
		$edit->tipo->option("NE","Nota de Entrega");
		$edit->tipo->rule = "required";  
		$edit->tipo->size = 20;  
		$edit->tipo->style='width:150px;';
	
		$edit->peso  = new inputField2("Peso", "peso");
		$edit->peso->size = 20;
		$edit->peso->css_class='inputnum';
		
		$edit->orden  = new inputField("Orden", "orden");
		$edit->orden->size = 15;
		
		$edit->credito  = new inputField("Credito", "credito");
		$edit->credito->size = 20;
		$edit->credito->css_class='inputnum';
		
		$edit->subt  = new inputField("Subt", "montotot");
		$edit->subt->size = 20;
		$edit->subt->css_class='inputnum';
		
		$edit->iva  = new inputField("IVA", "montoiva");
		$edit->iva->size = 20;
		$edit->iva->css_class='inputnum';
		
		$edit->total  = new inputField("Total", "montonet");
		$edit->total->size = 20;
		$edit->total->css_class='inputnum';
		
		$edit->anticipo  = new inputField("Anticipo", "anticipo");
		$edit->anticipo->size = 20;
		$edit->anticipo->css_class='inputnum';
		
		$edit->contado  = new inputField("Contado", "inicial");
		$edit->contado->size = 20;
		$edit->contado->css_class='inputnum';
				
		$edit->rislr  = new inputField("R.ISLR", "flete");
		$edit->rislr->size = 20;
		$edit->rislr->css_class='inputnum';
		
		$edit->riva  = new inputField("R.IVA", "reteiva");
		$edit->riva->size = 20;
		$edit->riva->css_class='inputnum';
		
		$edit->monto  = new inputField("Monto$", "mdolar");
		$edit->monto->size = 20;
		$edit->monto->css_class='inputnum';
				
		$numero=$edit->_dataobject->get('numero');
		
		$detalle = new DataDetalle($edit->_status);
		
		//Campos para el detalle
				
		$detalle->db->select('codigo,descrip,cantidad,costo,importe');
		$detalle->db->from('itscst');
		$detalle->db->where("numero='$numero'");
		
		$detalle->codigo = new inputField("Codigo", "codigo<#i#>");
		$detalle->codigo->size=10;
		$detalle->codigo->db_name='codigo';
		$detalle->codigo->append($this->datasis->p_modbus($modbus,'<#i#>'));
		$detalle->codigo->readonly=TRUE;
		
		$detalle->descripcion = new inputField("Descripci&oacute;n", "descrip<#i#>");
		$detalle->descripcion->size=30;
		$detalle->descripcion->db_name='descrip';
		$detalle->descripcion->maxlength=12;
		
		$detalle->cantidad = new inputField("Cantidad", "cantidad<#i#>");
		$detalle->cantidad->size=10;
		$detalle->cantidad->db_name='cantidad';
		$detalle->cantidad->maxlength=60;
		$detalle->cantidad->css_class='inputnum';
		
		$detalle->precio = new inputField("Precio", "costo<#i#>");
		$detalle->precio->css_class='inputnum';
		$detalle->precio->onchange='totalizar()';
		$detalle->precio->size=20;
		$detalle->precio->db_name='costo';
		
		$detalle->importe = new inputField2("Importe", "importe<#i#>");
		$detalle->importe->db_name='importe';
		$detalle->importe->css_class='inputnum';
		$detalle->importe->size=20;

		//fin de campos para detalle
		
		$detalle->onDelete('totalizar()');
		$detalle->onAdd('totalizar()');
		$detalle->script($script);
		$detalle->style="width:110px";
		
		//Columnas del detalle
		$detalle->column("Codigo"    ,  "<#codigo#>");
		$detalle->column("Descripci&oacute;n", "<#descripcion#>");
		$detalle->column("Cantidad"  ,  "<#cantidad#>");
		$detalle->column("Precio"     , "<#precio#>");
		$detalle->column("Importe"    , "<#importe#>");
	
		$detalle->build();	
		$conten["detalle"] = $detalle->output;
		
		$edit->detalle=new freeField("detalle", 'detalle',$detalle->output);

		$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();
		
		$conten["form"]  =&  $edit;
		$data['content'] = $this->load->view('view_compras', $conten,true); 
		$data["head"]    = script("tabber.js").script("prototype.js").$this->rapyd->get_head().script("scriptaculous.js").script("effects.js");
		$data['title']   = '<h1>Compras</h1>';
		$this->load->view('view_ventanas', $data);
	}
	
	function dpto() {		
		$this->rapyd->load("dataform");
		$campo='ccosto'.$this->uri->segment(4);
 		$script='
 		function pasar(){
			if($F("departa")!="-!-"){
				window.opener.document.getElementById("'.$campo.'").value = $F("departa");
				window.close();
			}else{
				alert("Debe elegir un departamento");
			}
		}';
		
		$form = new DataForm('');
		$form->script($script);
		$form->fdepar = new dropdownField("Departamento", "departa");
		$form->fdepar->option('-!-','Seleccion un departamento');
		$form->fdepar->options("SELECT depto,descrip FROM dpto WHERE tipo='G' ORDER BY descrip");
		$form->fdepar->onchange='pasar()';
		$form->build_form();
		
		$data['content'] =$form->output;
		$data["head"]    =script('prototype.js').$this->rapyd->get_head();
		$data['title']   ='<h1>Seleccione un departamento</h1>';
		$this->load->view('view_detalle', $data);
	}

	function _guarda_detalle($do) {
		$cant=$this->input->post('cant_0');
		$i=$o=0;
		while($o<$cant){
			if (isset($_POST["cuenta$i"])){
				if($this->input->post("cuenta$i")){
					$ccosto=$this->input->post("ccosto$i");
					if ($ccosto!='N')
						$ccosto="'$ccosto'";
					else
						$ccosto="NULL";
						
					$sql = "INSERT INTO itcasi (fecha,comprob,cuenta,referen,concepto,debe,haber,origen,ccosto) VALUES(?,?,?,?,?,?,?,'MANUAL',$ccosto)";
					$debe =($this->input->post("monto$i") > 0)? $this->input->post("monto$i") : 0;
					$haber=($this->input->post("monto$i") < 0)? $this->input->post("monto$i")*(-1) : 0;
					$llena=array(
							0=>$do->get('fecha'),
							1=>$do->get('comprob'),
							2=>$this->input->post("cuenta$i"),
							3=>$this->input->post("referen$i"),
							4=>$this->input->post("concepto$i"),
							5=>$debe,
							6=>$haber);
					$this->db->query($sql,$llena);
				}
				$o++;
			}
			$i++;
		}
	}
	function _actualiza_detalle($do){
		$this->_borra_detalle($do);
		$this->_guarda_detalle($do);
	}
	function _borra_detalle($do){
		$comprob=$do->get('comprob');
		$sql = "DELETE FROM itcasi WHERE comprob='$comprob'";
		$this->db->query($sql);
	}
	function _pre_del($do) {
		$codigo=$do->get('comprob');
		$check =   $this->datasis->dameval("SELECT COUNT(*) FROM cpla WHERE codigo LIKE '$codigo.%'");
		$check +=  $this->datasis->dameval("SELECT COUNT(*) FROM itcasi WHERE cuenta='$codigo'");
		
		if ($check > 0){
			$do->error_message_ar['pre_del'] = $do->error_message_ar['delete']='Plan de Cuenta tiene derivados o movimientos';
			return False;
		}
		return True;
	}
}
?>
