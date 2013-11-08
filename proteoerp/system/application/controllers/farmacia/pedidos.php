<?php require_once(APPPATH.'/controllers/inventario/consultas.php');
class Pedidos extends Controller {
	var $mModulo='FALLAPED';
	var $titp='Pedido de Fallas Diarias';
	var $tits='Pedido de Fallas Diarias';
	var $url ='farmacia/pedidos/';


	function Pedidos(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->load->library('jqdatagrid');
		$this->datasis->modulo_id('20E',1);
	}

	function index() {
		redirect('farmacia/pedidos/datafilter');
	}

	function datafilter(){
		$this->rapyd->load('datagrid','datafilter');
		$this->rapyd->uri->keep_persistence();

		$content_id = md5(uniqid(time()));
		$ttabla = 'fsisu_'.$content_id;

		$columnas = array('b.codigo AS codigoa', 'd.barras', 'b.descrip AS desca', 'b.existen', 'b.exmin', 'b.exmax', 
			'SUM(c.cana*(IF(c.tipoa=\'F\',1,-1))) AS trimestral',
			'b.exmax-IF(existen<0,0,b.existen) AS pedir'
		);
		$filter = new DataFilter('Fallas de productos vendidos en el d&iacute;a');

		$filter->db->select($columnas);
		$filter->db->from('sinv      AS b');
		$filter->db->join('sitems    AS c','b.codigo=c.codigoa AND c.tipoa="F" AND c.fecha >= DATE_ADD(CURDATE(), INTERVAL -90 DAY)','left');
		$filter->db->join($ttabla.'  AS d','b.codigo=d.abarras');
		$filter->db->where('b.existen <= b.exmin');

		$filter->db->groupby('b.codigo');
		$filter->db->having('pedir > 0');

		$filter->fecha = new dateonlyField('Fecha', 'fecha');
		$filter->fecha->clause  ='';
		$filter->fecha->db_name ='a.fecha';
		$filter->fecha->size    =12;
		$filter->fecha->operator='=';
		$filter->fecha->rule='required';
		$filter->fecha->insertValue=date('Y-m-d');

		$action = "javascript:window.location='".site_url($this->url.'especialxls')."'";
		$filter->button('btn_especial', 'Pedido Especial', $action, 'BR','show');

		$filter->buttons('reset','search');
		//$filter->submit('btn_cambio_2', 'Mandar pedido FarmaSIS', 'BR');
		$filter->build();

		function descheck($numero,$pedir){
			$data = array(
				'name'    => 'apedir[]',
				'id'      => $numero,
				'value'   => $numero.'#'.$pedir,
				'checked' => true);

			return form_checkbox($data);
		}

		function pinta($cana){
			$ncana=nformat($cana);
			return ($cana<0)? "<b style='color:red'>$ncana</b>": $ncana;
		}

		function divi($dividendo,$divisor){
			if($divisor>0){
				return ceil($dividendo/$divisor);
			}else{
				return 0;
			}
		}

		$seltod='Seleccionar <a id="todos" href=# >Todos</a> <a id="nada" href=# >Ninguno</a> <a id="alter" href=# >Invertir</a>';

		$bfecha=$filter->fecha->newValue;
		if(!empty($bfecha)) $dbbfecha=$this->db->escape($bfecha); else $dbbfecha='CURDATE()';

		$mSQL  = "CREATE TEMPORARY TABLE ${ttabla} (abarras VARCHAR(15)  NOT NULL, PRIMARY KEY (abarras))
		SELECT a.barras,a.abarras 
		FROM farmaxasig AS a
		JOIN sitems AS b ON a.abarras=b.codigoa
		WHERE  b.fecha=${dbbfecha}
		GROUP BY abarras";
		$this->db->simple_query($mSQL);

		$grid = new DataGrid($seltod);
		$grid->use_function('descheck','pinta','divi');
		$grid->order_by('desca','asc');
		$grid->per_page = 400;

		$grid->column('Pedir' ,'<descheck><#barras#>|<#pedir#></descheck>');
		$grid->column('C&oacute;digo'  ,'<span title="<#barras#>"><#codigoa#></span');
		$grid->column_orderby('Descripci&oacute;n'   ,'desca','desca');
		$grid->column('Trim.'   , '<nformat><#trimestral#>|0</nformat>','align=\'right\'' );
		$grid->column('Mens.'   , '<nformat><divi><#trimestral#>|3</divi>|0</nformat>' , 'align=\'right\'' );
		$grid->column('Quin.'   , '<nformat><divi><#trimestral#>|6</divi>|0</nformat>' , 'align=\'right\'' );
		$grid->column('Sema.'   , '<b><nformat><divi><#trimestral#>|12</divi>|0</nformat></b>', 'align=\'right\'' );
		$grid->column('Actual'  , '<pinta><#existen#>|0</pinta>',       'align=\'right\'' );
		$grid->column('Min-Max' , '<nformat><#exmin#>|0</nformat>-<nformat><#exmax#>|0</nformat>',     'align=\'center\'');
		$grid->column('<b>Sugerido</b>', '<b style="color:green"><nformat><#pedir#>|0</nformat></b>','align=\'right\'');
		$grid->build();
		//$grid->column('Rango' ,'[<nformat><#exmin#></nformat>-<nformat><#exmax#></nformat>]' ,'align=\'center\'');

		if($grid->recordCount>0){
			$tabla=$grid->output.form_submit('mysubmit', 'Mandar pedido a FarmaSIS');
			//echo $grid->db->last_query();
		}else{
			$tabla='';
		}

		$script ='<script type="text/javascript">
		$(document).ready(function() {
			$("#todos").click(function() { $("#apedir").checkCheckboxes();   });
			$("#nada").click(function()  { $("#apedir").unCheckCheckboxes(); });
			$("#alter").click(function() { $("#apedir").toggleCheckboxes();  });

			$(\'input[name="btn_cambio_2"]\').click(function() {
				var md = document.getElementById("apedir");
				md.submit();
			});
		});</script>';

		$data['content'] = $filter->output;
		$data['content'].= form_open('farmacia/pedidos/guardapedido',array('id'=>'apedir')).$tabla.form_close();
		$data['script']  = $script;
		$data['head']    = script('jquery-1.2.6.pack.js');
		$data['head']   .= script('plugins/jquery.checkboxes.pack.js');
		$data['head']   .= $this->rapyd->get_head();
		$data['title']   = heading('Compras a droguerias');
		$this->load->view('view_ventanas', $data);
	}

	function _guardapedido(){
		$error=$cana=0;
		$farmaxDB=$this->load->database('farmax',true);
		$farmaxDB->simple_query('INSERT INTO npfac VALUES(null, now())');
		$num=$farmaxDB->insert_id();

		$apedir=$this->input->post('apedir');
		foreach($apedir as $pedir){
			$arr=explode('#',$pedir);
			$data= array('barras'=>trim($arr[0]),'numero'=>$num,'cana'=>$arr[1]);
			$sql = $farmaxDB->insert_string('apedir', $data);

			$ban = $farmaxDB->simple_query($sql);
			if($ban==false){ memowrite($sql,'farmaciapedidos'); $error++; }
			$cana++;
		}

		if($cana>0){
			$mSQL='UPDATE inventarios AS a JOIN apedir AS b ON a.barras=b.barras SET b.descrip=a.descrip WHERE b.numero='.$num;
			$ban = $farmaxDB->simple_query($mSQL);
			if($ban==false){ memowrite($mSQL,'farmaciapedidos'); $error++; }
		}

		if($error==0){
			$msj='El pedido ha sido guardado bajo el n&uacute;mero <b>'.$num.'</b> Dirijase al '.anchor($this->_farmaurl(),'FarmaSIS').' para completar el proceso'.br();
		}else{
			$msj='Hubo un error guardando el pedido, se generar&oacute;n centinelas';
		}
		$rt=array('error'=>$error,'msj'=>$msj);
		return $rt;
	}

	function guardapedido(){
		$rt = $this->_guardapedido();
		$data['content'] = $rt['msj'];
		$data['title']   = heading('Enviar pedido a la drogueria');
		$this->load->view('view_ventanas', $data);
	}

	function pedidofalla(){
		$tema = 'proteo';
		$data['head']  = style('themes/'.$tema.'/'.$tema.'.css');
		$data['head'] .= phpscript('nformat.js');
		$data['head'] .= script('jquery-min.js');
		$data['head'] .= script('plugins/jquery.numeric.pack.js');
		$data['head'] .= script('jquery-ui.custom.min.js');
		$data['head'] .= script('jquery-impromptu.js');
		$data['head'] .= style('impromptu/default.css');
		$data['head'] .= style('themes/ui.jqgrid.css');
		$data['head'] .= script('i18n/grid.locale-sp.js');
		$data['head'] .= script('jquery.jqGrid.min.js');
		$data['head'] .= script('datagrid/datagrid.js');
		$data['head'] .= script('jquery.layout.js');

		$script  = '';
		$content = array();

		$data['content'] = $this->load->view('view_farmax_pedido', $content,true);
		$data['content'] = $this->defgrid();
		$data['script']  = $script;
		$data['title']   = heading('Compras a droguerias');
		$this->load->view('view_ventanas', $data);

	}

	function defgrid( $deployed = false ){
		$i      = 1;
		$editar = "false";

		$grid  = new $this->jqdatagrid;

		$grid->addField('id');
		$grid->label('Id');
		$grid->params(array(
			'align'    => "'center'",
			'frozen'   => 'true',
			'width'    => 60,
			'editable' => $editar,
			'search'   => 'false'
		));


		$grid->addField('codigo');
		$grid->label('Codigo');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
		));


		$grid->addField('barras');
		$grid->label('Barras');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 150,
			'edittype'      => "'text'",
		));


		$grid->addField('descrip');
		$grid->label('Descripcion');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'width'         => 200,
			'edittype'      => "'text'",
		));


		$grid->addField('cana');
		$grid->label('Cana');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));


		$grid->addField('ventas');
		$grid->label('Ventas');
		$grid->params(array(
			'search'        => 'true',
			'editable'      => $editar,
			'align'         => "'right'",
			'edittype'      => "'text'",
			'width'         => 100,
			'editrules'     => '{ required:true }',
			'editoptions'   => '{ size:10, maxlength: 10, dataInit: function (elem) { $(elem).numeric(); }  }',
			'formatter'     => "'number'",
			'formatoptions' => '{decimalSeparator:".", thousandsSeparator: ",", decimalPlaces: 0 }'
		));


		$grid->showpager(true);
		$grid->setWidth('');
		$grid->setHeight('290');
		$grid->setTitle($this->titp);
		$grid->setfilterToolbar(true);
		$grid->setToolbar('false', '"top"');

		$grid->setFormOptionsE('closeAfterEdit:true, mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setFormOptionsA('closeAfterAdd:true,  mtype: "POST", width: 520, height:300, closeOnEscape: true, top: 50, left:20, recreateForm:true, afterSubmit: function(a,b){if (a.responseText.length > 0) $.prompt(a.responseText); return [true, a ];} ');
		$grid->setAfterSubmit("$.prompt('Respuesta:'+a.responseText); return [true, a ];");

		#show/hide navigations buttons
		$grid->setAdd(true);
		$grid->setEdit(true);
		$grid->setDelete(true);
		$grid->setSearch(true);
		$grid->setRowNum(30);
		$grid->setShrinkToFit('false');

		#Set url
		$grid->setUrlput(site_url($this->url.'setdata/'));

		#GET url
		$grid->setUrlget(site_url($this->url.'getdata/'));

		if ($deployed) {
			return $grid->deploy();
		} else {
			return $grid;
		}
	}

	function especial(){
		$this->rapyd->load('datafilter2','datagrid','fields');

		$link2=site_url('inventario/common/get_linea');
		$link3=site_url('inventario/common/get_grupo');

		$script='
		$(document).ready(function(){
			$(".inputnum").numeric(".");

			$(\'input[name^="monto"]\').keyup(function(){
				var val=$(this).val();
				alert(val);

			});
		});';

		$filter = new DataFilter2($this->titp);

		$select=array(
			'a.existen','a.marca','a.semestral','a.trimestral','a.mensual','a.quincenal','a.semanal',
			'a.descrip','a.barras',
			'e.cobeca','e.dronena','e.drolanca','e.mafarta',
			'e.cobeca_cana','e.dronena_cana','e.drolanca_cana','e.mafarta_cana'
		);

		$filter->db->select($select);
		$filter->db->from('view_pednegocia AS a');
		$filter->db->join('droguerias.inventarios AS e','a.barras=e.barras');
		$filter->script($script);

		$filter->descrip = new inputField('Descripci&oacute;n', 'descrip');
		$filter->descrip->db_name='a.descrip';
		$filter->descrip-> size=30;

		$filter->marca = new dropdownField('Laboratorio', 'marca');
		$filter->marca->option('','Todas');
		$filter->marca->options('SELECT TRIM(marca) AS clave, TRIM(marca) AS valor FROM view_pednegocia GROUP BY marca');
		$filter->marca->style='width:220px;';

		$filter->buttons('reset', 'search');
		$filter->build();

		$uri = anchor($this->url.'dataedit/show/<raencode><#id#></raencode>','<#id#>');

		function opts($mafarta,$cobeca,$dronena,$drolanca,$mafarta_cana,$cobeca_cana,$dronena_cana,$drolanca_cana){

			$vals=array(
				'cobeca'  => $cobeca,
				'mafarta' => $mafarta,
				'dronena' => $dronena,
				'drolanca'=> $drolanca
			);
			//sort($val,SORT_NUMERIC);

			$val=array(
				'cobeca'  => "Merida   $cobeca_cana   - $cobeca Bs.",
				'mafarta' => "Marta    $mafarta_cana  - $mafarta Bs.",
				'dronena' => "Dronena  $dronena_cana  - $dronena Bs.",
				'drolanca'=> "Drolanca $drolanca_cana - $drolanca Bs."
			);

			return form_dropdown('aa', $val,'','style="width: 250px"');
		}

		$grid = new DataGrid('');
		$grid->use_function('opts');
		$grid->order_by('descrip');
		$grid->per_page = 40;

		$monto = new inputField('Monto', 'monto');
		$monto->grid_name='monto[<#barras#>]';
		$monto->status   ='modify';
		$monto->size     =8;
		$monto->css_class='inputnum';

		$grid->column_orderby('C&oacute;digo'     ,'barras' ,'barras' );
		$grid->column_orderby('Descripci&oacute;n','descrip','descrip');
		$grid->column_orderby('Laboratorio'       ,'marca'  ,'marca'  );
		$grid->column('Existencia' ,'<nformat><#existen#></nformat>' ,'align=\'right\'');

		$grid->column('Cantidad a pedir'      , $monto  ,'align=\'right\'');
		//$grid->column('Droguerias' ,'<opts><#mafarta#>|<#cobeca#>|<#dronena#>|<#drolanca#>|<#mafarta_cana#>|<#cobeca_cana#>|<#dronena_cana#>|<#drolanca_cana#></opts>'  ,'align=\'right\'');

		$grid->column('Semestral'  ,'<nformat><#semestral#></nformat>'  ,'align=\'right\'');
		$grid->column('Trimestral' ,'<nformat><#trimestral#></nformat>' ,'align=\'right\'');
		$grid->column('Mensual'    ,'<nformat><#mensual#></nformat>'    ,'align=\'right\'');
		$grid->column('Semanal'    ,'<nformat><#semanal#></nformat>'    ,'align=\'right\'');

		$grid->column('Merida'     ,'<nformat><#cobeca#></nformat>'  ,'align=\'right\'');
		$grid->column('Mafarta'    ,'<nformat><#mafarta#></nformat>' ,'align=\'right\'');
		$grid->column('Dronena'    ,'<nformat><#dronena#></nformat>' ,'align=\'right\'');
		$grid->column('Drolanca'   ,'<nformat><#drolanca#></nformat>','align=\'right\'');

		$grid->build();

		$data['filtro']  = $filter->output;
		$data['content'] = $grid->output;
		$data['head']    = $this->rapyd->get_head().script('jquery.js');
		$data['title']   = heading($this->titp);
		$this->load->view('view_ventanas', $data);
	}

	function _farmaurl($opt='farmax'){
		$uri='drogueria/pedidos';
		$url=reduce_double_slashes($_SERVER['HTTP_HOST'].'/'.$opt.'/'.$uri);
		$url=prep_url($url);
		return $url;
	}


	function especialxls(){
		$this->rapyd->load('datafilter2','datagrid');

		$droguerias=array(
			'cobeca'  =>'Merida',
			'mafarta' =>'Mafarta',
			'dronena' =>'Dronena',
			'drolanca'=>'Drolanca'
		);

		$filter = new DataFilter2('Pedidos especiales');

		$select=array(
			'a.existen','a.marca','a.semestral','a.trimestral','a.mensual','a.quincenal','a.semanal',
			'a.descrip','a.barras'
		);

		foreach($droguerias AS $id=>$value){
			$select[]='e.'.$id;
			$select[]='e.'.$id.'_cana';
		}

		$filter->db->select($select);
		$filter->db->from('view_pednegocia AS a');
		$filter->db->join('droguerias.inventarios AS e','a.barras=e.barras');

		$filter->descrip = new inputField('Descripci&oacute;n', 'descrip');
		$filter->descrip->db_name='a.descrip';
		$filter->descrip->size=30;

		$filter->marca = new dropdownField('Laboratorio', 'marca');
		$filter->marca->option('','Todas');
		$filter->marca->options('SELECT TRIM(marca) AS clave, TRIM(marca) AS valor FROM marc');
		$filter->marca->style='width:220px;';
		$filter->marca->rule='required';

		$filter->buttons('reset', 'search');
		$filter->build();

		if($filter->is_valid()){

			$mSQL=$this->rapyd->db->_compile_select();

			$fnombre='negociacion.xls';
			$fname = tempnam('/tmp',$fnombre);
			$tot=array();

			$this->load->library('workbook', array('fname'=>$fname));
			$wb = & $this->workbook ;
			$ws = & $wb->addworksheet('Hoja1');

			// ANCHO DE LAS COLUMNAS
			$ws->set_column('A:A',35);
			$ws->set_column('B:B',6);
			$ws->set_column('C:C',10);
			$ws->set_column('D:O',10);
			//$ws->set_column('E:XX',20);

			// FORMATOS
			$h       =& $wb->addformat(array( "bold" => 1, "size" => 14, "align" => 'left'));
			$h0      =& $wb->addformat(array( "bold" => 1, "size" => 10, "align" => 'left'));
			$h1      =& $wb->addformat(array( "bold" => 1, "size" => 11, "align" => 'center'));
			$h2      =& $wb->addformat(array( "bold" => 1, "size" => 14, "align" => 'left', "fg_color" => 'silver'  ));
			$h3      =& $wb->addformat(array( "bold" => 1, "size" => 9 ));
			$h3->set_merge();
			$h4      =& $wb->addformat(array( "bold" => 1, "size" => 9 , "align" => 'right',"num_format" => '#,##0.00'));
			$codesc  =& $wb->addformat(array( "bold" => 0, "size" => 8 , "align" => 'left', "fg_color" => 26  ));
			$codesc->set_border(1);
			$numcer  =& $wb->addformat(array( "bold" => 0, "size" => 8 , "align" => 'right', "fg_color" => 26  ));
			$numcer->set_border(1);
			$numpri  =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 8 , "fg_color" => 44 ));
			$numpri->set_border(1);
			$numseg  =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 8 , "fg_color" => 42 ));
			$numseg->set_border(1);
			$numter  =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 8 , "fg_color" => 41 ));
			$numter->set_border(1);
			$numcua  =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 8 , "fg_color" => 41 ));
			$numcua->set_border(1);
			$numqui  =& $wb->addformat(array( "num_format" => '#,##0.00' , "size" => 8 , "fg_color" => 45 ));
			$numqui->set_border(1);

			$titulo  =& $wb->addformat(array( "bold" => 1, "size" => 8, "merge" => 1, "fg_color" => 'silver', 'align'=>'vcenter' ));
			$titulo->set_text_wrap();
			$titulo->set_text_h_align(2);
			$titulo->set_border(1);
			$titulo->set_merge();

			$titpri  =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 1, "fg_color" => 26 ));
			$titpri->set_text_wrap();
			$titpri->set_border(1);
			$titpri->set_merge();

			$titseg  =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 1, "fg_color" => 44 ));
			$titseg->set_text_wrap();
			$titseg->set_border(1);
			$titseg->set_merge();

			$titter  =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 1, "fg_color" => 42 ));
			$titter->set_text_wrap();
			$titter->set_border(1);
			$titter->set_merge();

			$titcua  =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 1, "fg_color" => 41 ));
			$titcua->set_text_wrap();
			$titcua->set_border(1);
			$titcua->set_merge();

			$titaler  =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 1, "fg_color" => 'red' ));
			$titaler->set_text_wrap();
			$titaler->set_border(1);
			$titaler->set_merge();

			$titqui  =& $wb->addformat(array( "bold" => 1, "size" => 9, "merge" => 1, "fg_color" => 45, 'align'=>'vcenter' ));
			//$titqui->set_text_v_align(6);
			$titqui->set_text_wrap();
			$titqui->set_border(1);
			$titqui->set_merge();

			$cuerpo  =& $wb->addformat(array( 'size' => 9 ));

			$Tnumero =& $wb->addformat(array( 'num_format' => '#,##0.00' , 'size' => 9, 'bold' => 1, 'fg_color' => 'silver' ));
			$Rnumero =& $wb->addformat(array( 'num_format' => '#,##0.00' , 'size' => 9, 'bold' => 1, 'align'    => 'right' ));

			// COMIENZA A ESCRIBIR
			$ws->write(1, 0, $this->datasis->traevalor('TITULO1') , $h );
			$ws->write(2, 0, $this->datasis->traevalor('TITULO2') , $h0 );
			$ws->write(3, 0, 'RIF: '.$this->datasis->traevalor('RIF') , $h0 );

			if(!empty($filter->marca->value)){
				$ws->write(5, 0, 'Laboratorio : '.$filter->marca->value,$h0 );
			}

			$ws->write(1, 8, 'Listado para negocioacion', $h );
			//$ws->write(4, 8, ' ',$h1 );

			// TITULOS
			$mm=9;
			$ws->write_string( $mm,   0, 'Descripción', $titulo );
			$ws->write_string( $mm+1, 0, '', $titulo );

			$ws->write_string( $mm,   1, 'Exist.', $titulo );
			$ws->write_string( $mm+1, 1, '',$titulo );

			$ws->write_string( $mm,   2, 'Cantidad',$titulo );
			$ws->write_string( $mm+1, 2, '', $titulo );

			$col=3;
			foreach($droguerias AS $id=>$value){

				$ws->write_string( $mm,   $col, ucwords($value) ,$titulo );
				$ws->write_string( $mm+1, $col, 'Precio',  $titulo );
				$col++;

				$ws->write_blank(  $mm,   $col, $titulo );
				$ws->write_string( $mm+1, $col, 'Existencia', $titulo );
				$col++;

				$ws->write_blank(  $mm,   $col, $titulo );
				$ws->write_string( $mm+1, $col, 'SubTotal', $titulo );
				$col++;
			}


			$ws->write_string( $mm, $col, 'Ventas', $titulo );
			$ws->write_string( $mm+1, $col, 'Semestral', $titulo );
			$col++;

			$ws->write_blank(  $mm,   $col, $titulo );
			$ws->write_string( $mm+1, $col, 'Trimestral', $titulo );
			$col++;

			$ws->write_blank(  $mm,   $col, $titulo );
			$ws->write_string( $mm+1, $col, 'Mensual'   , $titulo );
			$col++;

			$ws->write_blank(  $mm,   $col, $titulo );
			$ws->write_string( $mm+1, $col, 'Quincenal', $titulo );
			$col++;

			$ws->write_blank(  $mm,   $col, $titulo );
			$ws->write_string( $mm+1, $col, 'Semanal', $titulo );
			$col++;

			$mm=$mm+2;
			$dd=$mm+1;

			$totdrog=array();
			$mc=$this->db->query($mSQL);
			if($mc->num_rows() > 0){
				foreach( $mc->result() as $row ) {
					$ws->write_string( $mm,  0,  $row->descrip        , $codesc );
					$ws->write_number( $mm,  1,  $row->existen        , $numcer );
					$ws->write_number( $mm,  2,  0                    , $numqui );

					$col=3;
					foreach($droguerias as $id=>$value){
						$obj1=$id;
						$ventas=empty($row->$obj1)? 0 : $row->$obj1;
						$ws->write_number( $mm, $col,$ventas, $numpri);
						$col++;

						$obj2=$id.'_cana';
						$existe=empty($row->$obj2)? 0 : $row->$obj2;
						$ws->write_number( $mm, $col,$existe, ($existe<=0)? $titaler: $numpri);
						$col++;

						$ucol= $this->nlet($col-2);
						$umm = $mm+1;
						$ws->write_formula($mm, $col, "=C$umm*$ucol$umm", ($existe<=0)? $titaler: $numter);
						$totdrog[$this->nlet($col)] = $col;
						$col++;
					}

					$ws->write_number( $mm, $col,  $row->semestral   , $codesc ); $col++;
					$ws->write_number( $mm, $col,  $row->trimestral  , $codesc ); $col++;
					$ws->write_number( $mm, $col,  $row->mensual     , $codesc ); $col++;
					$ws->write_number( $mm, $col,  $row->quincenal   , $codesc ); $col++;
					$ws->write_number( $mm, $col,  $row->semanal     , $codesc ); $col++;

					$mm++;
				}
			}
			$celda = $mm+1;
			$totlet=array();

			$ws->write_string( $mm  ,  0, 'Totales...'      ,$Tnumero );
			$ws->write_string( $mm+1,  0, 'Descuento lineal',$Tnumero );

			foreach($totdrog as $ucol=>$col){
				$mo=$mm+2;
				$ws->write_formula($mm, $col, "=SUM(${ucol}12:${ucol}${mm})-(SUM(${ucol}12:${ucol}${mm})*(${ucol}${mo}/100))", $Tnumero );
				$ws->write_number($mm+1, $col, 0, $Tnumero );
			}
			$mm++;

			$wb->close();
			header("Content-type: application/x-msexcel; name=\"$fnombre\"");
			header("Content-Disposition: inline; filename=\"$fnombre\"");
			$fh=fopen($fname,'rb');
			fpassthru($fh);
			unlink($fname);
		}else{
			if($this->input->post('btn_submit') !== false) $filter->build();
			$data['filtro'] = $filter->output;
			$data['titulo'] = heading('Listado para negocioaciones');
			$data['head'] = $this->rapyd->get_head();
			$this->load->view('view_freportes', $data);
		}
	}

	function nlet($i){
		$pivot=ord('A');
		$upivo=ord('Z');
		$val  =$i+$pivot;
		$res  =ceil($val/$upivo)-1;
		$let ='';
		$des = 0;
		for($o=0;$o<$res;$o++){
			$let.=chr($o+$pivot);
			$des+=$upivo-$pivot+1;
		}
		$let.=chr($val-$des);

		return $let;
	}

	function instalar(){
		if(!$this->db->table_exists('fallaped')){
			$mSQL="CREATE TABLE `fallaped` (
				`id` INT(10) NULL AUTO_INCREMENT,
				`codigo` VARCHAR(15) NULL,
				`barras` VARCHAR(15) NULL,
				`descrip` VARCHAR(45) NULL,
				`cana` INT(11) NULL,
				`ventas` INT(11) NULL,
				PRIMARY KEY (`id`),
				UNIQUE INDEX `codigo` (`codigo`)
			)
			COMMENT='pedidos a droguerias por fallas'
			COLLATE='latin1_swedish_ci'
			ENGINE=MyISAM";
			$this->db->simple_query($mSQL);
		}
	}
}
