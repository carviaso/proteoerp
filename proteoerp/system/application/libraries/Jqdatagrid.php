<?php
if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/**
 * Manage datagrid class
 * @author Victor Manuel Agudelo
 * @since 16-may-2010
 * @version 1.1
 *
 * url's
 * formats:
 * http://www.trirand.com/jqgridwiki/doku.php?id=wiki:predefined_formatter&s[]=date
 * edittype:
 * text, textarea, select, checkbox, password, button, image and file
 * http://www.trirand.com/jqgridwiki/doku.php?id=wiki:common_rules&s[]=date
 *
 * Rules
 * http://www.trirand.com/jqgridwiki/doku.php?id=wiki:common_rules
 *
 * example:
 *
 *  $this->load->library('datagrid');
    $grid  = $this->datagrid;

    $grid->addField('id');
    $grid->label('ID')->validators (array('integer' => "{thousandsSeparator: ' ', defaultValue: '0'}"));
    $grid->params(array('align' => "'center'", 'width' => 50,'editable' => 'false','editoptions' => '{readonly:true,size:10}'));

    $grid->addField('descripcion');
    $grid->label('descripcion');
    $grid->params(array('width' => 300,'editable' => 'true','edittype' => "'textarea'", 'editrules' => '{required:true}'));

    $grid->addField('created_by');
    $grid->label('created_by');
    $grid->params(array('width' => 300,'editable' => 'true','edittype' => "'text'",'editrules' => '{date:true}'));

    #GET url
    $grid->setUrlget(site_url('welcome/getData/'));

    #Set url
    $grid->setUrlput(site_url('welcome/setData/'));

    #show paginator
    $grid->showpager(true);
    #titulo de la tabla
    $grid->setTitle('Prueba');

    #show/hide navigations buttonss
    $grid->setAdd(true);
    $grid->setEdit(true);
    $grid->setDelete(true);
    $grid->setSearch(true);

    $param['grid'] = $grid->deploy();
    $this->load->view('crud',$param);
 *
 *
 * controller
 /**
 *
 #Get data result as json

public function getData()
{
    $this->load->library('datagrid');
    $grid             = $this->datagrid;
    $response         = $grid->getData('crud_test', array(array('table' => 'crud_relation', 'join' => 'crud_relation.id = crud_test.relation_id', 'fields' => array('description'))),array(),false);
    $rs = $grid->jsonresult( $response);
    echo $rs;
}


#Put information

public function setData()
{
    $this->load->library('datagrid');
    $grid             = $this->datagrid;
    $response         = $grid->operations('crud_test','id');
}

 *
 */

class Jqdatagrid
{
	private $CI;
	public $_field;

	private $Wbotones = array();

	private $fieldtemp = '';
	#botones de acciones
	private $_buttons  = array('add'    => 'true',
                               'edit'   => 'true',
                               'delete' => 'true',
                               'search' => 'true',
                               'view'   => 'true'
                              );
	private $_export  = array('csv'     => false,
                              'pdf'     => false,
                              'excel'   => false,
                              'print'   => false,
                              'xml'     => false,
                              );
	private $_exportpdf = array();
	#Ordenacion de campos
	private $sortname  = '';

	private $sorttype  = 'desc';
	#urls de envio de informacion
	private $url_put   = '';

	#url de consulta
	private $url_get   = '';

	#url de edicion
	private $url_edit  = '';

	#url de eliminacion
	private $url_del   = '';

	private $datatype  = 'json';

	#numero de registros por pagina
	private $rowNum    = 20;

	#ancho de la pagina
	private $width;

	#Ancho automatico
	private $autowidth = 'true';

	#muestra la barra de paginacion
	private $showpager = true;

	# Ajustar al tamano
	private $shrinkToFit = 'true';

	#alto de la pagina
	private $height  = 150;

	private $return  = array('table' => '', 'pager' => '');

	private $title    = '';

	private $_gridname;

	private $grouping = 'false';

	private $groupingView = '';

	private $filterToolbar = false;

	private $Toolbar = '';

	private $FormOptionsE = '-';

	private $FormOptionsA = '-';

	private $viewrecords = true;

	private $rowList = "[20,30,50,100]";

	private $hiddengrid = false;

	private $afterSubmit = '-';

	private $afterPager  = '';

	private $loadComplete  = '';

	private $gridComplete  = '';


/*
	private $beforeShow = '';
	private $afterShow = '';
*/

	private $multiSelect = false;

	private $onSelectRow = '';

	private $afterInsertRow = '';

	private $ondblClickRow = 'i';

	private $onClick = '';

	private $BarOptions = '';

	private $wpadicional = '';

	public $footerrow=false;

	function __construct ()
	{
		$this->CI =& get_instance();
		if(!isset($this->CI->session)) $this->CI->load->library('session');
		if(empty($this->_gridname)){
			$this->_gridname = '_' .  rand(1122,99999999);
		}
	}

	/**
	* Add fields to draw
	* @param String $field Field name
	* @return void
	*/
	public function addField($field)
	{
		$this->fieldtemp = $field;
		$this->_field['field'][$field] = trim($field);
	}


	/**
	* Crea una clase con sus metodos
	* @param string $name nombre clase
	* @param <type> $args elementos que recibe la clase
	* @return void
	*
	*/
	function __call ($name, $args)
	{

		if ( substr(strtolower($name), 0, 3) == 'set' || substr(strtolower($name), 0, 3) == 'add' ) {
			$name = substr($name, 3);
			$name[0] = strtolower($name[0]);
		}

		$this->_field[$name][$this->fieldtemp] = $args[0];
		return $this;
	}

	function setgridName($name){
		$this->_gridname = '_' . $name;
	}


	/**
	*Show or not, the Add button
	* @param bool $element
	* @return void
	*/
	public function setAdd($element)
	{
		$this->_buttons['add'] = "'{$element}'";
	}

	/**
	*Show or not, the Edit button
	* @param bool $element
	* @return void
	*/
	public function setEdit($element)
	{
		$this->_buttons['edit'] = "'{$element}'";
	}

	/**
	*Show or not, the Delete button
	* @param bool $element
	* @return void
	*/
	public function setDelete($element)
	{
		$this->_buttons['delete'] = "'{$element}'";
	}

	/**
	* Show or not, search button
	* @param bool $element
	* @return void
	*/
	public function setSearch($element)
	{
		$this->_buttons['search'] = "'{$element}'";
	}

	/**
	* Show record summary
	* @param bool $element
	* @return void
	*/
	public function setViewRecords($element)
	{
		$this->viewrecords = $element;
	}


	/**
	* Adiciona al wp
	*
	*/
	public function setWpAdicional($element)
	{
		$this->wpadicional = $element;
	}


	/**
	* Show record summary
	* @param bool $element
	* @return void
	*/
	public function setMultiSelect($element)
	{
		$this->multiSelect = $element;
	}


	/**
	* Show record summary
	* @param bool $element
	* @return void
	*/
	public function setHiddenGrid($element)
	{
		$this->hiddengrid = $element;
	}


	/**
	* Show record summary
	* @param text $element
	* @return void
	*/
	public function setRowList($element)
	{
		$this->rowList = $element;
	}

	/**
	* Show or not, search options
	* @param text $element
	* @return void
	*/
	public function setFormOptionsA($element)
	{
		if ($element == '')
			$this->FormOptionsA = "";
		elseif($element == '-')
			$this->FormOptionsA = "-";
		else
			$this->FormOptionsA = ",{".$element."}";
	}

	/**
	* Show or not, search options
	* @param text $element
	* @return void
	*/
	public function setFormOptionsE($element)
	{
		if ($element == '')
			$this->FormOptionsE = "";
		elseif($element == '-')
			$this->FormOptionsE = "-";
			//$this->FormOptionsA = "-";
		else
			$this->FormOptionsE = ",{".$element."}";

	}

	/**
	* After Submit Function
	* @param text $element
	* @return void
	*/
	public function setAfterSubmit($element)
	{
		$this->afterSubmit = $element;
	}

	//******************************************************************
	//
	public function setBarOptions($element)
	{
		$this->BarOptions = $element;
	}


	//******************************************************************
	//
	public function setLoadComplete($element)
	{
		$this->loadComplete = $element;
	}

	//******************************************************************
	//
	public function setGridComplete($element)
	{
		$this->gridComplete = $element;
	}

	/**
	* After Pager
	* @param text $element
	* @return void
	*/
	public function setAfterPager($element)
	{
		$this->afterPager = $element;
	}

/*
	*
	* After Show Form
	* @param text $element
	* @return void
	*
	public function setAfterShow($element)
	{
		$this->afterShow = $element;
	}

	**
	* Before Show Form
	* @param text $element
	* @return void
	*
	public function setBeforeShow($element)
	{
		$this->beforeShow = $element;
	}
*/

	/**
	* Set ON Selection
	* @param text $element
	* @return void
	*/
	public function setOnSelectRow($element)
	{
		$this->onSelectRow = $element;
	}


	/**
	* Set After Insert
	* @param text $element
	* @return void
	*/
	public function setAfterInsertRow($element)
	{
		$this->afterInsertRow = $element;
	}


	/**
	* Show or not, view data button
	* @param bool $element
	* @return void
	*/
	public function setView($element)
	{
		$this->_buttons['view'] = "'{$element}'";
	}

	/**
	* Show or not excel icon to export
	* @param bool $element
	* @return void
	*/
	public function setExcel($element)
	{
		$this->_export['excel'] = "{$element}";
	}


	/**
	* Show or not pdf icon to export
	* @param bool $element
	* @param array $params pdf properties array('exclude' => array('field1','fields'), // fields to exclude to show
	*                                           'title' => title, // pdf title
	*                                           'orientation' => portrait/landscape, //paper orientation
	*                                           'stream' => true/false, // download or save (./temp/filename.pdf)
	* @return void
	*/
	public function setPdf($element,$params = array())
	{
		$this->_export['pdf']       = "{$element}";
		$this->_exportpdf['params'] = $params; //isset($params['title'])?$params['title']:'';
	}

	/**
	* Show or not Csv icon to export
	* @param bool $element
	* @return void
	*/
	public function setCsv($element)
	{
		$this->_export['csv'] = "{$element}";
	}

	/**
	* Show or not Csv icon to export
	* @param bool $element
	* @return void
	*/
	public function setXml($element)
	{
		$this->_export['xml'] = "{$element}";
	}

	/**
	* Show or not print icon to export
	* @param bool $element
	* @return void
	*/
	public function setPrint($element)
	{
		$this->_export['print'] = "{$element}";
	}

	/**
	* Ordenacion de campos
	* @param String $field campo por el cual va a ordenar
	* @param String $order Orden del campo, asc/desc
	*/
	public function setSortname( $field, $order = 'desc')
	{
		$this->sortname = $field;
		$this->sorttype = $order;
	}

	/**
	* Establece la URL de consulta
	* @param String $url url donde va a recibir la informacion
	*/
	public function setUrlget($url)
	{
		$this->url_get = $url;
	}

	/**
	* Establece la URL de envio de informacion
	* @param String $url url donde va a enviar la informacion
	*/
	public function setUrlput($url)
	{
		$this->url_put = $url;
	}

	/**
	*Devuelve la URL de donde trae la informacion
	* @return String url, url de consulta
	*/
	public function getUrlget()
	{
		return $this->url_get;
	}


	/**
	*Tipo de datos de consulta
	* @param String $datatype json/xml
	*
	*/
	public function setDataType($datatype = 'json')
	{
		$this->datatype = $datatype;
	}

	/**
	* Register per page
	* @param Int $rowNum registres
	*/
	public function setRowNum($rowNum)
	{
		$this->rowNum = $rowNum;
	}

	/**
	* Shrink to Fit
	* @param text $shrink registres
	*/
	public function setShrinkToFit($shrink)
	{
		$this->shrinkToFit = $shrink;
	}

	/**
	* Filter Tool bar
	* @param text $activa registres
	*/
	public function setfilterToolbar($activa)
	{
		$this->filterToolbar = $activa;
	}

	/**
	* Tool bar
	* @param text $arreglo registres
	*/
	public function setToolbar($arreglo)
	{
		$this->Toolbar = $arreglo;
	}


	/**
	* On Double Click
	* @param text $arreglo registres
	*/
	public function setOndblClickRow($element)
	{
		$this->ondblClickRow = $element;
	}


	/**
	* On Double Click
	* @param text $arreglo registres
	*/
	public function setOnClick($element)
	{
		$this->onClick = $element;
	}


	/**
	* Grouping
	* @param text $shrink registres
	*/
	public function setGrouping($grupo)
	{
		$grupo = strtolower($grupo);

		if ( empty($grupo) ){
			$this->grouping = 'false';
		} else {
			$this->grouping = 'true';
		}
		if ( $this->grouping == 'true' ){
			$this->grouping = 'true';
			$this->groupingView = "{ groupField : ['".$grupo."'], groupColumnShow : [false], groupCollapse: false, groupText: ['<b>{0}</b>'] }";
		} else {
			$this->grouping = 'false';
			$this->groupingView = "";
		}
		//"groupField : ['name'], groupDataSorted : true ";
	}

	/**
	* Width table
	* @param Int $width Ancho de la pagina
	*/
	public function setWidth($width)
	{
		$this->width = $width;
	}

	/**
	*Height table
	* @param Int $height Height table
	*/
	public function setHeight($height)
	{
		$this->height = $height;
	}

	/**
	* Table title
	* @param String $title page title
	* @return void
	*/
	public function setTitle($title)
	{
		$this->title = $title;
	}

	/**
	* Generathe the HTML code
	*/
	public function deploy()
	{
		/**
		* check if we must export result
		*/
		$this->export();
		$margen = "\t\t";

		$html      = '';
		$loadbutton = false;

		if(false == empty($this->url_get)){
			$html .= ",url:'{$this->url_get}/'\r\n";
			$post = (false == empty($this->url_put)) ? $this->url_put : $this->url_get;
			$html .=  $margen.",editurl:'{$post}/'\r\n";
		}

		$html     .=  $margen.",datatype:'{$this->datatype}'\r\n";
		$html     .=  $margen.",rowNum:'{$this->rowNum}'\r\n";
		$html     .=  $margen.",shrinkToFit: $this->shrinkToFit \r\n";
		$html     .=  $margen.",scrollrows: true \r\n";
		if($this->footerrow)
			$html     .=  $margen.",footerrow: true \r\n";

		if ( $this->grouping == 'true' ) {
			$html  .=  $margen.",grouping: true \r\n";
			$html  .=  $margen.",groupingView: ".$this->groupingView." \r\n";
		}

		if(false == empty($this->width)){
			$html  .= $margen.",width:'$this->width'\r\n";
		}else{
			$html  .= $margen.",autowidth:$this->autowidth\r\n";
		}

		if(false == empty($this->sortname)){
			$html .= $margen.",sortname: '$this->sortname'\r\n";
			$html .= $margen.",sortorder: '$this->sorttype'\r\n";
		}

		if($this->height){
			$html  .= $margen.",height:'$this->height'\r\n";
		} else {
			$html  .= $margen.",height:'100%'\r\n";
		}

		if($this->showpager){
			$html .= $margen.",pager: '#pnewapi{$this->_gridname}'\r\n";
		}

		if($this->title){
			$html .= $margen.",caption:'{$this->title}'\r\n";
		}

		if($this->viewrecords == false ){
			$html .= $margen.",viewrecords: false\r\n";
			$html .= $margen.",pgbuttons: false\r\n";
			$html .= $margen.",rowList: []\r\n";
			$html .= $margen.",pgtext: null\r\n";
		} else
			$html .= $margen.",rowList: ".$this->rowList."\r\n";

		if ($this->onSelectRow)
			$html .= $margen.",onSelectRow: ".$this->onSelectRow."\r\n";

		if ($this->afterInsertRow)
			$html .= $margen.",afterInsertRow: ".$this->afterInsertRow."\r\n";

		if ($this->loadComplete)
			$html .= $margen.",loadComplete: ".$this->loadComplete."\r\n";

		if ($this->gridComplete)
			$html .= $margen.",gridComplete: ".$this->gridComplete."\r\n";

		if($this->multiSelect == true ){
			//$html .= $margen.",gridComplete: function() { $(this).jqGrid('hideCol', 'cb');}";
			$html .= $margen.",multiselect: true\r\n";
			$html .= $margen.",multiboxonly: true\r\n";
		}

		if($this->hiddengrid == true )
			$html .= $margen.",hiddengrid: true\r\n";


		if ( strlen($this->Toolbar)>2 ){
			$html .= $margen.",toolbar: [$this->Toolbar]\r\n";
		}

		$querydata = array( 'dtgFields' => $this->_field );
		$this->CI->session->set_userdata($querydata);

		//calendario
		$calendar = "
			size: 12,
			maxlengh: 12,
			dataInit: function(element) {
				$(element).datepicker({
					dateFormat: 'yy-mm-dd',
					changeMonth: true,
					changeYear: true,
					yearRange: '" . (date('Y',time()) - 30) .":" .(date('Y',time()) + 10) ."',
					onSelect: function(){
						if(this.id.substr(0, 3) === 'gs_'){
							//in case of searching toolbar
							setTimeout(function(){ jQuery('#newapi".$this->_gridname."')[0].triggerToolbar(); },50);
						}else{
							//refresh the filter in case of searching dialog
							//$(this).trigger('change');
						}
					}
				});
			}";

		$html     .=  $margen.',colModel:[' . "\r";
		$fieldname = '';
		if(false == empty($this->_field)){

			#recorre la parametrizacion
			foreach($this->_field as $row => $value){
				if($row == 'field'){
					#recorre c/u de los campos
					foreach($value as $field){
						$alias        = $field;
						$validators   = '';
						$params       = '';
						$isdate       = false;
						$editoptions  = false;
						if(isset($this->_field['label'][$field])){
							$alias = $this->_field['label'][$field];
						}
						if(isset($this->_field['validators'][$field])){
							$validators = ',formatter:{';
							foreach($this->_field['validators'][$field] as $param => $paramval){
								if(empty($params)){
									$params = $param . ':' . $paramval . '';
								}else{
									$params .=   ',' . $param . ':' . $paramval . '';
								}
							}
							$validators .= $params . '}';
						}
						$params = '';
						$calc   = '';
						if(isset($this->_field['params'][$field])){
							//$alias = $this->_field['label'][$field];
							foreach($this->_field['params'][$field] as $param => $paramval){
								if(empty($params)){
									$params = ",{$param}:$paramval";
								} else {
									$params .= ",{$param}:$paramval";
								}
								$addcal = '';
								if (!$isdate)  $calc   = '';
								if($param == 'editrules'){
									#verifica si tiene un campo date
									if((isset($this->_field['params'][$field]['editoptions']) && strpos($params,'date:') > 0)){
										$isdate = true;
										$pos = strpos($params,'editoptions:{');
										$addcal = substr($params, $pos +  13);
										if(false == empty($addcal)){
											$calc = ",editoptions: {{$calendar} ${addcal},searchoptions: {{$calendar}}";
										} else {
											$calc = ",editoptions: {{$calendar}},searchoptions: {{$calendar}}";
										}
									} else {
										if(strpos($params,'date:') > 0){
											$isdate = true;
											if(false == empty($addcal)){
						 						$calc = ",editoptions: {{$calendar} $addcal ,searchoptions: {{$calendar}}";
											} else {
												$calc = ",editoptions: {{$calendar}, defaultValue:'".date('Y-m-d')."'}, searchoptions: {{$calendar}}";
											}
										}
									}
								}
							}
							if( empty($fieldname)) {
								$fieldname   = $margen."	{name:'{$field}',index:'{$field}',label:'{$alias}' {$params} {$validators} {$calc}}\n";
							} else {
								$fieldname .= $margen. "	,{name:'{$field}',index:'{$field}',label:'{$alias}' {$params} {$validators} {$calc}}\n";
							}
						}
					}
				}
			}
		}


        $html .= $fieldname.$margen."]\r";
        $this->return['table']       = $html;
        $this->return['gridname']    = $this->_gridname;
        $this->return['export']      = $this->_export;
        $this->return['querystring'] = ($this->CI->config->item('enable_query_strings') == FALSE)?'\'false\'':'\'true\'';


	if ( $this->ondblClickRow == 'i'){
		$this->ondblClickRow = ',ondblClickRow: function(id){
			var gridwidth = jQuery("#newapi'.$this->_gridname.'").width();
			gridwidth = gridwidth/2;
			jQuery("#newapi'.$this->_gridname.'").editGridRow(id, {closeAfterEdit:true,mtype:\'POST\'});
			return;
		}';
	}

	$this->return['ondblClickRow'] = $this->ondblClickRow;

	$this->return['onClick'] = $this->onClick;


        #paginador
		if($this->showpager){
			$bar   = '';
			$bar  .= "
	$(\"#newapi{$this->_gridname}\").jqGrid('navGrid', '#pnewapi{$this->_gridname}', {
		view:   {$this->_buttons['view']},
		edit:   {$this->_buttons['edit']},
		add:    {$this->_buttons['add']},
		del:    {$this->_buttons['delete']},
		search: {$this->_buttons['search']},\n";

			$bar  .= $margen.$this->BarOptions;

			$bar  .= "
	} \n";

			if ( $this->FormOptionsE=='' ){
				$bar  .= "	,{closeAfterEdit:true, mtype: 'POST'}\r\n"; // edit options
			} elseif($this->FormOptionsE=='-') {
				$bar  .= "";
			} else {
				$bar  .= "	".$this->FormOptionsE."\n";
			}

			if ( $this->FormOptionsA=='' ){
				$bar  .= "	,{closeAfterAdd:true, mtype: 'POST'}\r\n"; //add options
			} elseif($this->FormOptionsA=='-') {
				$bar  .= "";
			} else {
				$bar  .= "	".$this->FormOptionsA."\n";
			}

			if ( $this->afterSubmit =='' ) {
				$bar   .= "	,{mtype: 'POST',	afterSubmit: function(a,b){ ";
				$bar   .= "if (a.responseText.length > 0) alert(a.responseText); return [true, a ];";
				$bar   .= "}}\r\n";
			} elseif($this->afterSubmit=='-') {
				$bar  .= "";
			} else {
				$bar   .= "	,{mtype: 'POST',	afterSubmit: function(a,b){ ";
				$bar   .= $this->afterSubmit;
				$bar   .= "}}\r\n";
			}


			$bar   .= "	,{sopt:['eq','cn','ge','le'], overlay:false, mtype: 'POST', multipleSearch:true }"; //search options
			//$bar   .= "	,{ multipleSearch:true , multipleGroup:true}"; //search options
			if ( $this->afterPager == '' )
				$bar .= "\r\n";
			else
				$bar   .= ",\r\n".$this->afterPager;
			$bar   .= ")";

		$key = array_search('true', $this->_export); // find if we must show the export button;
		if($key)
		{
			$bar  .= ".navButtonAdd('#pnewapi{$this->_gridname}',
					{ caption:'', buttonicon:'ui-icon-extlink', onClickButton:dtgOpenExportdata, position: 'last', title:'Export data', cursor: 'pointer'}
				)";
			$loadbutton = true;
		}

		$bar     .= ";\r\n";
		if($loadbutton){
			$bar .= "dtgLoadButton();\r\n";
		}

		if ($this->filterToolbar){
			$bar .= "\t$(\"#newapi{$this->_gridname}\").jqGrid('filterToolbar');\r\n";
			$this->return['menosalto'] = 115;
		} else $this->return['menosalto'] = 90;
			//$this->_buttons['excel'];
			$this->return['pager'] = $bar;
		}
		return $this->return;
	}

	/**
	* Show or not the pagination bar
	* @param bool $action true/false, si muestra o no la barra de paginacion
	* @return $return['pager'] codigo html de paginacion
	*/
	public function showpager($action)
	{
		$this->showpager = $action;
	}

	/**
	* Return an Array with table information:
	* Example:
	* $this->load->library('datagrid');
	* $grid = $this->datagrid;
	* Join with more than one table:
	* $grid->getData($table,array(array(array('table' => 'table_related', 'join' => 'table_related.primary_key = $table.foreignkey', 'fields' => 'field1,field2','type' => 'left')),array('table' => 'table_related2', 'join' => 'table_related2.primary_key = $table.foreignkey',direction)));//direction = Options are: left, right, outer, inner, left outer, and right outer
	* Query without relations
	* $grid->getData($table);
	* @param String $table table name
	* @param String/Array $joinmodel Join tables
	* @param Array $fields Field list to show
	* @param bool $prefix indicate si put prefix at recordset fields
	* @return Array $response
	*/
	public function getData($table, $joinmodel = array(), $fields = array(), $prefix = true, $mwhere='', $orden='', $orddire=''){
		$limit      = intval($this->CI->input->get_post('rows'));
		$limitstart = $this->CI->input->get_post('limitstart');
		$filter     = $this->CI->input->get_post('searchField');
		$filtertext = $this->CI->input->get_post('searchString');
		$oper       = $this->CI->input->get_post('searchOper');

		$sortby     = $this->CI->input->get_post('sidx');
		$sortdir    = $this->CI->input->get_post('sord');

		$page       = intval($this->CI->input->get_post('page'));
		$filters    = $this->CI->input->get_post('filters');

		$comodin = $this->CI->datasis->traevalor('COMODIN');

		if(empty($sortby)){
			$sortby  = $orden;
			$sortdir = $orddire;
		} else {
				$sortby = str_replace(' asc,','',$sortby);
		}

		$fields2    = array();

		$response = array();
		//$this->CI->db->select('count(1) as rows');
		//$this->CI->db->from($table);
        //
		//if( false == empty($filter) ) {
		//	switch ($oper) {
		//	case 'cn':
		//		$this->CI->db->like( $table .'.' . $filter, $filtertext );
		//		break;
		//	case 'eq':
		//		$this->CI->db->where( $table .'.' . $filter , $filtertext );
		//		break;
		//	case 'ge':
		//		$this->CI->db->where( "${table}.{$filter} >=", $filtertext );
		//		break;
		//	case 'le':
		//		$this->CI->db->where( "${table}.{$filter} <=", $filtertext );
		//		break;
		//	default:
		//		$this->CI->db->like( $table .'.' . $filter, $filtertext );
		//		break;
		//	}
		//}
        //
		//$total = $this->CI->db->get()->row();
		//$count = $total->rows;
		//if( $count >0 ) {
		//	$total_pages = ceil($count/$limit);
		//} else {
		//	$total_pages = 0;
		//}
        //
		//$response['records']  = $count;
		//$response['total']    = $total_pages;
		//$response['page']     = $page;
		//if($page > $total_pages){
		//	$page=$total_pages;
		//}

		$limitstart = $limit*$page - $limit; // do not put $limit*($page - 1)
		$limitstart = ($limitstart < 0)?0:$limitstart;

		if(false == empty($filter)){
			switch ($oper) {
			case 'cn':
				$this->CI->db->like( $table .'.' . $filter, trim($filtertext) );
				break;
			case 'eq':
				$this->CI->db->where( $table .'.' . $filter, trim($filtertext) );
				break;
			case 'ge':
				$this->CI->db->where( "${table}.{$filter} >=", $filtertext );
				break;
			case 'le':
				$this->CI->db->where( "${table}.{$filter} <=", $filtertext );
				break;
			default:
				$this->CI->db->like( $table .'.' . $filter, trim($filtertext) );
				break;
			}
		}

		if(!empty($mwhere)){
			foreach($mwhere as $busca){
				if(trim(strtoupper($busca[0]))== 'LIKE'){
					$this->CI->db->like( $busca[1], trim(str_replace($comodin,'%', $busca[2])), $busca[3] );
				}elseif(trim(strtoupper($busca[0]))== 'IN'){
					$this->CI->db->where_in( $busca[1], $busca[2] );
				}else{
					if (in_array($busca[0], array('>','<')) || in_array($busca[0],array('<>','>=','<=','!=')) ){
						$this->CI->db->where( $busca[1].' '.$busca[0], trim($busca[2]));
					}else{
						//Eliminado para poder buscar campos numericos en cero
						if($busca[2]==='' || is_null($busca[2])){
							$this->CI->db->where($busca[1]);
						}else{
							if(is_array($busca[2])){
								$this->CI->db->where_in($busca[1], $busca[2]);
							}else{
								$this->CI->db->where( $busca[1], trim($busca[2]) );
							}
						}
					}
				}
			}
		}


		if(!empty($filters)){
			$mQUERY = $this->constructWhere($filters);
			foreach($mQUERY as $busca){
				if(trim(strtoupper($busca[0])) == 'LIKE'){
					if(strtoupper($busca[4])=='OR'){
						$this->CI->db->or_like( $busca[1], str_replace($comodin,'%', $busca[2]), $busca[3] );
					}else{
						$this->CI->db->like( $busca[1], str_replace($comodin,'%', $busca[2]), $busca[3] );
					}
				}else{
					if(strtoupper($busca[4])=='OR'){
						$this->CI->db->or_where( $busca[1], $busca[2]);
					}else{
						$this->CI->db->where( $busca[1], $busca[2]);
					}
				}
			}
		}

		if(empty($sortdir)){
			$sortdir = 'asc';
		}

		if(!empty($sortby)){
			$this->CI->db->order_by( $sortby, $sortdir );
	    }

		if(!isset($limitstart) || $limitstart == ''){
			$limitstart = 0;
		}

		if(isset($limitstart) && !empty($limit)){
			$this->CI->db->limit( $limit, $limitstart);
		}
		if(!empty($joinmodel)){
			if(is_array($joinmodel)){
				foreach($joinmodel as $model){
					if(isset($model['table']) && isset($model['join'])){
						$this->CI->db->join($model['table']/*tablename*/, $model['join']/*join fields*/, (isset($model['type']))?$model['type']:'inner'/*join type*/);
						if(isset($model['fields']) && false == empty($model['fields']) && $prefix){
							foreach($model['fields'] as $field){
								$fields2[] = "{$model['table']}.{$field} AS {$model['table']}_{$field}";
							}
						}else{
							if(isset($model['fields']) && false == empty($model['fields'])){
								foreach($model['fields'] as $field){
									$fields2[] = "{$model['table']}.{$field}";
								}
							}
						}
					}
				}
			}

			if(empty($fields) && $prefix){
				$fieldstable = $this->CI->db->list_fields($table);
				foreach($fieldstable as $field){
					$fields[] = "{$table}.{$field} AS {$table}_{$field}";
				}
			}else{
				if(empty($fields)){
					$fieldstable = $this->CI->db->list_fields($table);
					foreach($fieldstable as $field){
						$fields[] = "{$table}.{$field}";
					}
				}
			}

			$fields = array_merge($fields, $fields2);

			$this->CI->db->select($fields);
			$this->CI->db->from($table);
		}
		$qq = $this->CI->db->get();
		$rs = $this->CI->datasis->codificautf8($qq->result_array());
		$queryString = $this->CI->db->last_query();

		$mSQL = preg_replace('/SELECT((.*)\n*)FROM/i', 'SELECT  COUNT(*) AS cana FROM', $queryString);
		$pos  = stripos($mSQL, 'ORDER BY');
		if($pos !== false){
			$mSQL=substr($mSQL,0,$pos);
		}else{
			$pos = stripos($mSQL, 'LIMIT');
			if($pos !== false){
				$mSQL=substr($mSQL,0,$pos);
			}else{

			}
		}

		$response['records']  = intval($this->CI->datasis->dameval($mSQL));
		$response['total']    = ceil($response['records']/$limit);
		$response['page']     = $page;

		//if($qq->num_rows()<$limit){
		//	if($qq->num_rows()>0 ) {
		//		$total_pages = ceil($qq->num_rows()/$limit);
		//	} else {
		//		$total_pages = 0;
		//	}
        //
		//	if($page > $total_pages){
		//		$page=$total_pages;
		//	}
        //
		//	$response['records']  = $qq->num_rows();
		//	$response['total']    = $total_pages;
		//	$response['page']     = $page;
        //
		//	$limitstart = $limit*$page - $limit; // do not put $limit*($page - 1)
		//	$limitstart = ($limitstart < 0)?0:$limitstart;
		//}


		//INTENTA ver si el Problema es el escape de %
		if(empty($rs)){
			$lq = str_replace('\%','%',$this->CI->db->last_query());
			$rs = $this->CI->datasis->codificautf8($this->CI->db->query($lq)->result_array());
		}

		$querydata = array( 'dtgQuery' => $this->CI->db->last_query() );

		$this->CI->session->set_userdata($querydata);

		$response['data'] = $rs;
		return $response;
	}


	/**
	* Return an Array with table information:
	* with a given select
	*/
	public function getDataSimple($mSQL)
	{
		$limit      = $this->CI->input->get_post('rows');
		$limitstart = $this->CI->input->get_post('limitstart');
		$filter     = $this->CI->input->get_post('searchField');
		$filtertext = $this->CI->input->get_post('searchString');
		$oper       = $this->CI->input->get_post('searchOper');

		$sortby     = $this->CI->input->get_post('sidx');
		$sortdir    = $this->CI->input->get_post('sord');

		$page       = $this->CI->input->get_post('page');
		$filters    = $this->CI->input->get_post('filters');

		$query = $this->CI->db->query($mSQL);
		$rs = $this->CI->datasis->codificautf8($query->result_array());;

		//echo $this->CI->db->last_query();
		$queryString = $this->CI->db->last_query();

		$querydata = array( 'dtgQuery'  => $this->CI->db->last_query() );

		$this->CI->session->set_userdata($querydata);

		$response['data'] = $rs;
		return $response;
	}




	/***********************************************************************
	* Execute CRUD process
	* @param String $table table name
	* @param String $key primary key
	* @return void
	*/
	public function operations($table,$key = 'id')
	{
		$oper   = $this->CI->input->post('oper');
		//$oper   = $data['oper'];
		if($oper == 'add'){
			$data = $_POST;
			unset($data[$key]);
			unset($data['oper']);
			if(false == empty($data)){
				$this->CI->db->insert($table, $data);
			}
			echo '';
			return;

		} elseif($oper == 'edit'){
			$id   = $this->CI->input->post($key);
			$data = $_POST;
			unset($data['oper']);
			unset($data[$key]);
			$this->CI->db->where($key, $id);
			$this->CI->db->update($table, $data);
			return;

		} elseif($oper == 'del'){
			$id   = $this->CI->input->post($key);
			$this->CI->db->where($key, $id);
			$this->CI->db->delete($table);
			echo '';
			return;
		}
	}

	/***********************************************************************
	* Return data like json
	* @param Array $result
	* @return object json
	*/
	public function jsonresult($result)
	{
		return json_encode($result);
	}


	/***********************************************************************
	*
	*
	*/
	public function constructWhere($s){
		$qwery = "";
		$mWHERE = array();
		//['eq','ne','lt','le','gt','ge','bw','bn','in','ni','ew','en','cn','nc']
		$qopers = array(
						'eq'=>" = ",
						'ne'=>" <> ",
						'lt'=>" < ",
						'le'=>" <= ",
						'gt'=>" > ",
						'ge'=>" >= ",
						'bw'=>" LIKE ",
						'bn'=>" NOT LIKE ",
						'in'=>" IN ",
						'ni'=>" NOT IN ",
						'ew'=>" LIKE ",
						'en'=>" NOT LIKE ",
						'cn'=>" LIKE " ,
						'nc'=>" NOT LIKE " );

		$operador = array(" <> "," < "," <= "," > "," >= ");


		if($s){
			$jsona = json_decode($s,true);
			if(is_array($jsona)){
				$gopr  = $jsona['groupOp'];
				$rules = $jsona['rules'];
				$i =0;
				foreach($rules as $key=>$val) {
					$field = $val['field'];
					$op    = $val['op'];
					$v     = $val['data'];
					if($v && $op){
						if(in_array( $qopers[$op], $operador)){
							$mWHERE[] = array( trim($qopers[$op]), $field.$qopers[$op], $v, $gopr );
						}else{
							$mWHERE[] = array( trim($qopers[$op]), $field, $v, 'both', $gopr );
						}
						$i++;
						// ToSql in this case is absolutley needed
						$v = $this->ToSql($field,$op,$v);
						if($i == 1){
							$qwery = ' AND ';
						}else{
							$qwery .= " ${gopr} ";
						}
						switch ($op) {
							// in need other thing
							case 'in' :
							case 'ni' :
								$qwery .= $field.$qopers[$op]." (${v})";
								break;
							default:
								$qwery .= $field.$qopers[$op].$v;
						}
					}
				}
			}
		}
		return $mWHERE;
	}
	/***********************************************************************
	*
	*
	*
	*/
	public function ToSql ($field, $oper, $val) {
		// we need here more advanced checking using the type of the field - i.e. integer, string, float
		switch ($field) {
			case 'id':
				return intval($val);
				break;
			case 'xxxxxxamount':
			case 'xxxssstax':
			case 'xxsssstotal':
				return floatval($val);
				break;
			default :
				//mysql_real_escape_string is better
				if($oper=='bw' || $oper=='bn') return "'" . addslashes($val) . "%'";
				else if ($oper=='ew' || $oper=='en') return "'%" . addcslashes($val) . "'";
				else if ($oper=='cn' || $oper=='nc') return "'%" . addslashes($val) . "%'";
				else return "'" . addslashes($val) . "'";
		}
	}


	/***********************************************************************
	* Convert result to valid json
	* @param json $json
	*/
	function jqgridSelect($json)
	{
		$selectval = str_replace("[", '', $json);
		$selectval = str_replace("]", '', $selectval);
		$selectval = str_replace("{", '', $selectval);
		$selectval = str_replace("}", '', $selectval);
		$selectval = str_replace('"', '', $selectval);
		$selectval = str_replace(",", ';', $selectval);
		return $selectval;
	}


	/***********************************************************************
	* Genera el codigo java para autocomplete
	*
	*/
	function autocomplete( $link, $name, $id, $html, $despues='', $append='"body"' )
	{
		$salida = '
		"dataInit":function(el){
			setTimeout(function(){
				if(jQuery.ui) {
					if(jQuery.ui.autocomplete){
						jQuery(el).autocomplete({
							"appendTo": '.$append.',
							"disabled":false,
							"delay":300,
							"minLength":1,
							"select": function(event, ui) {
								$("#'.$id.'").remove();
								$("#'.$name.'").after("'.$html.'");'.$despues.'
							},
							"source":function (request, response){
								request.acelem = "'.$name.'";
								request.oper   = "autocomplete";
								request.cargo  = _cargo;
								$.ajax({
									url: "'.$link.'",
									dataType: "json",
									data: request,
									type: "POST",
									error: function(res, status) { $.prompt(res.status+" : "+res.statusText+". Status: "+status);},
									success: function( data ) { response( data );	}
								});
							}
						});
						//jQuery(el).autocomplete("widget").css("font-size","11px");
						jQuery(el).autocomplete("widget").css("z-index", 3000);
					}
				} else { $.prompt("Falta jQuery UI") }
			},200);
		}';
		return $salida;
	}


	/***********************************************************************
	* Returns the where from a table
	*/
	function geneTopWhere($db){
		$mWhere = array();
		if ($this->CI->input->get_post('_search')==true){
			$campos = $this->CI->db->field_data($db);
			foreach($campos as $campo){
				$valor = $this->CI->input->get_post($campo->name);
				if($valor!==false){
					$valor = trim($valor);
					if(in_array($campo->type,array('string',254,253))){
						if(substr($valor,0,1) == '%' || substr($valor,0,1) == '*'){
							$valor = substr($valor,1);
							$mWhere[] = array('like', $campo->name, $valor, 'both' );
						}else{
							$mWhere[] = array('like', $campo->name, $valor, 'after');
						}
					}elseif(in_array($campo->type,array('date','timestamp',10,12,7))){
						$mWhere[] = array('', $campo->name, $valor, '' );
					}elseif(in_array($campo->type,array('real','int',1,2,9,3,8,4,5,246))) {
						$valor= trim($valor);
						if ( in_array(substr($valor,0,2), array('>=','<=','<>','!='))){
							$mWhere[] = array(substr($valor,0,2), $campo->name, floatval(substr($valor,2)), '' );
						}elseif( in_array(substr($valor,0,1), array('>','<') ) ) {
							$mWhere[] = array(substr($valor,0,1), $campo->name, floatval(substr($valor,1)), '' );
						}else{
							$mWhere[] = array('', $campo->name, floatval($valor), '' );
						}
					}elseif( $campo->type == 'blob'){
						$mWhere[] = array('like', $campo->name, $valor, 'both' );
					}else{
						$mWhere[] = array('like', $campo->name, $valor, 'both' );
					}
				}
			}
		}
		return $mWhere;
	}

	/***********************************************************************
	* Returns the where from a select
	*/
	function geneSelWhere($sel,$types=array()){
		$mWhere = array();
		if($this->CI->input->get_post('_search')==true){
			$campos=array();
			foreach($sel as $campo){

				$cc=preg_match('/(?P<name>.+) +AS +(?P<alias>\w+)/', trim($campo), $matches);
				if($cc>0){
					$obj=array(
						'name' => $matches['name'],
						'alias'=> $matches['alias'],
					);
					if(isset($types[$matches['alias']])){
						$obj['type']=$types[$matches['alias']];
					}else{
						$obj['type']='string';
					}
					$campos[]=$obj;
				}
			}
			if(count($campos)<=0){
				return $mWhere;
			}

			foreach($campos as $campo){
				$valor = $this->CI->input->get_post($campo['alias']);
				if($valor!==false){
					$valor = trim($valor);

					if(in_array($campo['type'],array('string',254,253))){
						if(substr($valor,0,1) == '%' || substr($valor,0,1) == '*'){
							$valor = substr($valor,1);
							$mWhere[] = array('like', $campo['name'], $valor, 'both' );
						}else{
							$mWhere[] = array('like', $campo['name'], $valor, 'after');
						}
					}elseif(in_array($campo['type'],array('date','timestamp',10,12,7))){
						$mWhere[] = array('', $campo['name'], $valor, '' );
					}elseif(in_array($campo['type'],array('real','int',1,2,9,3,8,4,5,246))) {
						$valor= trim($valor);
						if(in_array(substr($valor,0,2), array('>=','<=','<>','!='))){
							$mWhere[] = array(substr($valor,0,2), $campo['name'], floatval(substr($valor,2)), '' );
						}elseif( in_array(substr($valor,0,1), array('>','<') ) ) {
							$mWhere[] = array(substr($valor,0,1), $campo['name'], floatval(substr($valor,1)), '' );
						}else{
							$mWhere[] = array('', $campo['name'], floatval($valor), '' );
						}
					}elseif($campo['type'] == 'blob'){
						$mWhere[] = array('like', $campo['name'], $valor, 'both' );
					}else{
						$mWhere[] = array('like', $campo['name'], $valor, 'both' );
					}
				}
			}
		}
		return $mWhere;
	}

	/***********************************************************************
	* Returns the where from a table
	*/
	function geneSqlWhere($tabla, $join ){
		$campost = $this->CI->db->field_data($tabla);

		$campos = array();
		foreach( $campost as $ca ){
			$campos[] = array("tabla"=>$tabla, "name"=>$ca->name, "type"=>$ca->type, "max_length"=> $ca->max_length, "primary_key"=>$ca->primary_key);
		}

		foreach($join as $model){
			$campost = $this->CI->db->field_data($model['table']);
			foreach( $campost as $ca ){
				if (  array_search( $ca->name, $model['fields'] ) !== false ) {
					$campos[] = array("tabla"=>$model['table'], "name"=>$ca->name, "type"=>$ca->type, "max_length"=> $ca->max_length, "primary_key"=>$ca->primary_key);
				}
			}
		}
		$mWhere = array();
		if ($this->CI->input->get_post('_search')==true){
			foreach ( $campos as $campo)
			{
				$valor = $this->CI->input->get_post($campo['name']);
				if ($valor) {
					if ( $campo['type'] == 'string' ){
						if ( substr($valor,0,1) == '%' || substr($valor,0,1) == '*' ) {
							$valor = substr($valor,1);
							$mWhere[] = array('like', $campo['tabla'].'.'.$campo['name'], $valor, 'both' );
						} else
							$mWhere[] = array('like', $campo['tabla'].'.'.$campo['name'], $valor, 'after' );

					} elseif ( $campo['type'] == 'date' || $campo['type'] == 'timestamp' ) {
						$mWhere[] = array('', $campo['tabla'].'.'.$campo['name'], $valor, '' );

					} elseif ( $campo['type'] == 'real' || $campo['type'] == 'int'  ) {
						$valor= trim($valor);
						if ( in_array(substr($valor,0,2), array('>=','<=','<>','!=') ) )  {
							$mWhere[] = array(substr($valor,0,2), $campo['tabla'].'.'.$campo['name'], substr($valor,2), '' );
						} elseif ( in_array(substr($valor,0,1), array('>','<') ) ) {
							$mWhere[] = array(substr($valor,0,1), $campo['tabla'].'.'.$campo['name'], substr($valor,1), '' );
						} else
							$mWhere[] = array('', $campo['tabla'].'.'.$campo['name'], $valor, '' );

					} elseif ( $campo->type == 'blob' ) {
						$mWhere[] = array('like', $campo['tabla'].'.'.$campo['name'], $valor, 'both' );
					} else {
						$mWhere[] = array('like', $campo['tabla'].'.'.$campo['name'], $valor, 'both' );
					}
				}
			}
		}
		return $mWhere;
	}


	/***********************************************************************
	* Export data to pdf or csv
	* @param String $type pdf/csv
	* @return <type>
	*/
	function export()
	{
		$queryString = $this->CI->config->item('enable_query_strings');
		if($queryString === false){
			$arrFormat = $this->CI->uri->uri_to_assoc();
			if(isset ($arrFormat['_exportto'])){
				$format = $arrFormat['_exportto'];
			}else{
				return false;
			}
		}else{
			$format = $this->CI->input->get('_exportto', TRUE);
			if(empty($format)){
				return false;
			}
		}
		$query  = $this->CI->session->userdata('dtgQuery');
		$fields = $this->CI->session->userdata('dtgFields');
		$sql    = $this->_cleanExportSql($query, $fields);

		if($format == 'csv'){
			$this->CI->load->dbutil();
			$query = $this->CI->db->query($sql);
			$delimiter = ",";
			$newline = "\r\n";
			header ("Content-disposition: attachment; filename=csvoutput_". time(). ".csv") ;
			echo $this->CI->dbutil->csv_from_result($query,$delimiter, $newline);
			exit;
		}elseif($format == 'xml'){
			$this->CI->load->dbutil();
			$query = $this->CI->db->query($sql);
			header ("Content-disposition: attachment;content-type: text/xml; filename=xmloutput_". time(). ".xml") ;
			$config = array (
				'root'    => 'root',
				'element' => 'element',
				'newline' => "\n",
				'tab'     => "\t"
			);

			echo $this->CI->dbutil->xml_from_result($query, $config);
			exit;
		}elseif($format == 'pdf'){
			$this->CI->load->plugin('to_pdf');
			$query  = $this->CI->db->query($sql);
			$params = $this->_exportpdf['params'];
			$exclude     = (isset($params['exclude']))?$params['exclude']:array();
			$orientation = (isset($params['orientation']))?$params['orientation']:'portrait';
			$stream      = (isset($params['stream']))?$params['stream']:true;

			$html   = '';
			$html  .= (isset($params['title']))?'<h3><center>' .$params['title'] . '</center></h3>':'';
			$html  .= html_prepare($query, $exclude);

			//echo $html;
			pdf_create($html, "pdfoutput_". time(),$stream,$orientation);
		}
		//echo "== {$query} ==";
	}


	/***********************************************************************
	* Show report table
	* @param Int $reportid
	*/
	function report($reportid)
	{

	}


	/***********************************************************************
	* Unset the Limit and show only the field data
	* @param String $sql
	* @param Array $fields
	* @return String $sql
	*/
	private function _cleanExportSql($sql, $fields)
	{
		$query  = '';
		$frompos   = strpos($sql, 'FROM');
		$select    = 'SELECT ';

//        if(isset($this->_field['field'])){
//            $i = 0;
//            foreach($this->_field['field'] as $field){
//
//                $select .= ($i== 0)?$field:',' . $field;
//                $i++;
//            }
//
//        }
		//echo $sql. '<hr>';
		$query     = $sql; //substr($sql, $frompos);
		$limitpos  = stripos($query, 'LIMIT ');
		if($limitpos > 0){
			$query     = substr($query, 0,$limitpos);
		}
		//echo  $query;
		return $query;
	}


	//******************************************************************
	// Agrega Boton al panel izquierdo
	//
	function wbotonadd( $boton ){
		$this->Wbotones[] = $boton;
	}

	function deploywestp(){

//<div class="anexos">

		$wlista = '
<div id="LeftPane" class="ui-layout-west ui-widget ui-widget-content">
<table id="west-grid" align="center">
	<tr>
		<td>
			<div class="anexos"><table id="listados"></table></div>
		</td>
	</tr><tr>
		<td>
			<div class="anexos"><table id="otros"></table></div>
		</td>
	</tr>'."\n";
	$wlista .= $this->wpadicional;
	$wlista .='
</table>
<div id="wbotones">'."\n";

//</div>

		$wbotones = "<table id='west-grid' align='center'>\n";
		foreach( $this->Wbotones as $bt  ){
			if ( !isset($bt['height'])) $bt['height'] = 18;
			if ( !isset($bt['tema'])) $bt['tema'] = 'tema1';
			$wbotones .= '
	<tr>
		<td style="vertical-align:top;"><div class="'.$bt["tema"].' botones"><a style="width:190px;text-align:left;vertical-align:top;" href="#" id="'.$bt["id"].'">'.img(array('src' => $bt["img"], 'height' => $bt['height'], 'alt' => $bt["alt"], 'title' => $bt["alt"], 'border'=>'0')).'&nbsp;&nbsp;&nbsp;&nbsp;'.$bt["label"].'</a></div></td>
	</tr>';

		}

		$wbotones .= '
</table>
</div>

<div class="centro-sur" id="ladicional" style="overflow:auto;"></div>
</div> <!-- #LeftPane -->
';

		return $wlista.$wbotones;
	}


	function readyLayout2( $west = 212, $south = 220, $grid0, $grid1 = ''){
		$readyLayout = '
		$(\'body\').layout({
			minSize: 30,
			north__size: 60,
			resizerClass: \'ui-state-default\',
			west__size: '.$west.',
			west__onresize: function (pane, $Pane){
				jQuery("#west-grid").jqGrid(\'setGridWidth\',$Pane.innerWidth()-2);
			},
		});';
		if ($grid1 == ''){
			$readyLayout .= '
			$(\'div.ui-layout-center\').layout({
				minSize: 30,
				resizerClass: "ui-state-default",
				center__paneSelector: ".centro-centro",
				south__paneSelector:  ".centro-sur",
				south__size: '.$south.',
				center__onresize: function (pane, $Pane) {
					jQuery("#newapi'.$grid0.'").jqGrid(\'setGridWidth\',$Pane.innerWidth()-6);
					jQuery("#newapi'.$grid0.'").jqGrid(\'setGridHeight\',$Pane.innerHeight()-110);
				}
			});
			';
		} else {

			$readyLayout .= '
			$(\'div.ui-layout-center\').layout({
				minSize: 30,
				resizerClass: "ui-state-default",
				center__paneSelector: ".centro-centro",
				south__paneSelector:  ".centro-sur",
				south__size: '.$south.',
				center__onresize: function (pane, $Pane) {
					winHeight = window.innerHeight;
					jQuery("#newapi'.$grid0.'").jqGrid(\'setGridWidth\', $Pane.innerWidth()-6);
					jQuery("#newapi'.$grid0.'").jqGrid(\'setGridHeight\',$Pane.innerHeight()-100);
					jQuery("#newapi'.$grid1.'").jqGrid(\'setGridWidth\', $Pane.innerWidth()-6);
					jQuery("#newapi'.$grid1.'").jqGrid(\'setGridHeight\',winHeight-($Pane.innerHeight()+150));
				}
			});
			';
		}


		return $readyLayout;
	}

	function centerpanel( $id = "adicional", $grid0, $grid1 = '' ){
		if ( $grid1 == '' ) {
			$centerpanel = '
			<div id="RightPane" class="ui-layout-center">
			<div class="centro-centro">
				<table id="newapi'.$grid0.'"></table>
				<div  id="pnewapi'.$grid0.'"></div>
			</div>
			<div class="centro-sur" id="'.$id.'" style="overflow:auto;">
			</div>
			</div> <!-- #RightPane -->
			';
		} else {
			$centerpanel = '
			<div id="RightPane" class="ui-layout-center">
				<div class="centro-centro">
					<table id="newapi'.$grid0.'"></table>
					<div id="pnewapi'.$grid0.'"></div>
				</div>
				<div class="centro-sur" id="'.$id.'" style="overflow:auto;">
					<table id="newapi'.$grid1.'"></table>
				</div>
			</div>
			<!-- #RightPane -->
			';
		}
		return $centerpanel;
	}

	function SouthPanel( $leyenda = "", $adic = array() ){
		$SouthPanel = '
		<div id="BottomPane" class="ui-layout-south ui-widget ">
			<table cellpadding="0" cellspacing="0" width="100%"><tr><td><span style="font-size:14px;font-weight:bold;">'.$leyenda.'</span></td><td><div id="respuesta"></div></td></tr></table>
		</div> <!-- #BottomPanel -->
		';

		foreach( $adic as $me ){
			$SouthPanel .= "<div id='".$me["id"]."' title='".$me["title"]."'></div>\n";
		}
		return $SouthPanel;
	}



	//******************************************************************
	//  AYUDA PARA BODYSCRIPT
	//
	function bswrapper($ngrid){
		$bodyscript = '
		$(function() {
			$("#dialog:ui-dialog").dialog( "destroy" );
			var mId = 0;
			var grid = jQuery("'.$ngrid.'");
			var s;
			s = grid.getGridParam(\'selarrrow\');
		';
		return $bodyscript;
	}


	//******************************************************************
	// Dialogo fedita
	//
	function bsfedita( $ngrid, $height = "300", $width = "550", $dialogo='fedita', $post='', $botones='' ){
		if ($dialogo == '') $dialogo='fedita';
		$bodyscript = '
		$("#'.$dialogo.'").dialog({
			autoOpen: false, height: '.$height.', width: '.$width.', modal: true,
			buttons: {
				"Guardar": function() {
					var murl = $("#df1").attr("action");
					$.ajax({
						type: "POST", dataType: "html", async: false,
						url: murl,
						data: $("#df1").serialize(),
						success: function(r,s,x){
							try{
								var json = JSON.parse(r);
								if (json.status == "A"){
									$.prompt("<h1>Registro Guardado</h1>",{
										submit: function(e,v,m,f){
											setTimeout(function(){ $("'.$ngrid.'").jqGrid(\'setSelection\',json.pk.id);}, 500);
										}}
									);
									$( "#'.$dialogo.'" ).dialog( "close" );
									grid.trigger("reloadGrid");
									idactual = json.pk.id;'.$post.'
									return true;
								} else {
									$.prompt(json.mensaje);
								}
							} catch(e) {
								$("#'.$dialogo.'").html(r);
							}
						}
					})
				},'.$botones.'
				"Cancelar": function() {
					$("#'.$dialogo.'").html("");
					$( this ).dialog( "close" );
				}
			},
			close: function() {
				$("#'.$dialogo.'").html("");
			}
		});';

		return $bodyscript;
	}


	//******************************************************************
	// Dialogo fshow
	//
	function bsfshow( $height = "500", $width = "700" ){
		$bodyscript = '
		$("#fshow").dialog({
			autoOpen: false, height: '.$height.', width: '.$width.', modal: true,
			buttons: {
				"Aceptar": function() {
					$("#fshow").html("");
					$( this ).dialog( "close" );
				},
			},
			close: function() {
				$("#fshow").html("");
			}
		});';

		return $bodyscript;
	}

	//******************************************************************
	// Dialogo fborra
	//
	function bsfborra( $ngrid, $height = "300", $width = "400" ){
		$bodyscript = '
		$("#fborra").dialog({
			autoOpen: false, height: '.$height.', width: '.$width.', modal: true,
			buttons: {
				"Aceptar": function() {
					$("#fborra").html("");
					jQuery("'.$ngrid.'").trigger("reloadGrid");
					$( this ).dialog( "close" );
				},
			},
			close: function() {
				jQuery("'.$ngrid.'").trigger("reloadGrid");
				$("#fborra").html("");
			}
		});';

		return $bodyscript;
	}


	//******************************************************************
	// Agregar
	//
	function bsadd( $modulo, $url ){
		$bodyscript = '
		function '.$modulo.'add(){
			$.post("'.site_url($url.'dataedit/create').'",
			function(data){
				$("#fedita").html(data);
				$("#fedita").dialog( "open" );
			})
		};';
		return $bodyscript;
	}

	//******************************************************************
	//Editar
	//
	function bsedit( $modulo, $ngrid, $url ){
		$bodyscript = '
		function '.$modulo.'edit(){
			var id = $("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				$.post("'.site_url($url.'dataedit/modify').'/"+id, function(data){
					$("#fedita").html(data);
					$("#fedita").dialog( "open" );
				})
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';
		return $bodyscript;
	}

	//******************************************************************
	// Mostrar
	function bsshow( $modulo, $ngrid, $url ){
		$bodyscript = '
		function '.$modulo.'show(){
			var id  = $("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				var ret = $("'.$ngrid.'").getRowData(id);
				mId = id;
				$.post("'.site_url($url.'dataedit/show').'/"+id, function(data){
					$("#fshow").html(data);
					$("#fshow").dialog( "open" );
				});
			} else {
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';
		return $bodyscript;
	}


	//******************************************************************
	// Borrar
	function bsdel( $modulo, $ngrid, $url ){
		$bodyscript = '
		function '.$modulo.'del() {
			var id = jQuery("'.$ngrid.'").jqGrid(\'getGridParam\',\'selrow\');
			if(id){
				if(confirm(" Seguro desea eliminar el registro?")){
					var ret    = $("'.$ngrid.'").getRowData(id);
					mId = id;
					$.post("'.site_url($url.'dataedit/do_delete').'/"+id, function(data){
						try{
							var json = JSON.parse(data);
							if (json.status == "A"){
								apprise("Registro eliminado");
								jQuery("'.$ngrid.'").trigger("reloadGrid");
							}else{
								apprise("Registro no se puede eliminado");
							}
						}catch(e){
							$("#fborra").html(data);
							$("#fborra").dialog( "open" );
						}
					});
				}
			}else{
				$.prompt("<h1>Por favor Seleccione un Registro</h1>");
			}
		};';
		return $bodyscript;
	}



}
/* End of file datagrid.php */
/* Location: ./system/application/libraries/Datagrid.php */
