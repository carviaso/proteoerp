<?php
class Analisis extends Controller {

	function Analisis(){
		parent::Controller();
		$this->load->helper('text');
		$this->load->library("rapyd");
		$this->load->library('calendar');
		$this->datasis->modulo_id(101,1);
		$this->rapyd->config->set_item("theme","repo");
	}
	function index() {
		$this->rapyd->load("datagrid","dataform");
		$this->load->helper('openflash');

		$anio=$this->uri->segment(4);
		if (empty($anio))$anio=date("Y");

		$script ='
			$(function() {
				$(".inputnum").numeric(".");
			});
			$("#df1").submit(function() {
					valor=$("#anio").attr("value");
					location.href="'.site_url('ventas/analisis/index').'/"+valor;
					return false;
				});
			';
			
		function blanco($num){
			if(empty($num)||$num==0){
			 return '';
			}else{
				return number_format($num,2,',','.');
			}
		}

		$filter = new DataForm();
		$filter->title('Filtro de An&aacute;lisis ventas');
		$filter->script($script, "create");
		$filter->script($script, "modify");
		
		$filter->anio = new inputField("A&ntilde;o", "anio");
		$filter->anio->size=4;
		$filter->anio->insertValue=$anio;
		$filter->anio->maxlength=4;	
		$filter->anio->rule = "trim";
		$filter->anio->css_class='inputnum';

		$filter->button("btnsubmit", "Buscar", form2uri(site_url('/ventas/analisis/index'),array('anio')), $position="BL");
		$filter->build_form(); 

		$link="ventas/analisis/departamento/<#depto#>/$anio";
		$select=array('IFNULL(e.descrip, e.depto) dpto','e.depto');//, a.linea, a.grupo, a.codigo

		$grid = new DataGrid('Ventas por Departamentos');
		$grid->use_function('blanco');
		$grid->use_function('number_format');
		$grid->column("Departamento", anchor($link, '<#dpto#>'),'nowrap=yes');

		for($i=1;$i<=12;$i++){
			$nmes=$this->calendar->get_month_name(str_pad($i, 2, "0", STR_PAD_LEFT));
			$grid->column($nmes, "<blanco><#m$i#>|2|,|.</blanco>" ,'align=right');
			$select[]="sum(a.tota*(month(a.fecha)=$i)*IF(f.tipo_doc='F',1,-1))  AS m$i";
		}

		$grid->db->select($select);  
		$grid->db->from('sfac AS f');
		$grid->db->join('sitems AS a','f.numero=a.numa AND f.tipo_doc=a.tipoa','LEFT');
		$grid->db->join('sinv AS b','a.codigoa=b.codigo','LEFT');
		$grid->db->join('grup AS c','b.grupo=c.grupo','LEFT');
		$grid->db->join('line AS d','c.linea=d.linea','LEFT');
		$grid->db->join('dpto AS e','d.depto=e.depto','LEFT');	
		$grid->db->where('YEAR(f.fecha)',$anio);
		$grid->db->where("f.tipo_doc <>",'X');
		//$grid->db->groupby('EXTRACT(YEAR_MONTH FROM f.fecha)');
		$grid->db->groupby('e.depto');

		$grid->build();
		//echo  $grid->db->last_query();

		$data['content'] = $filter->output.'<div style="overflow: auto; width: 100%;">'.$grid->output.'</div>';
		$data['title']   = "<h1>An&aacute;lisis de ventas</h1>";
		$data["head"]    = script("jquery.pack.js").script("plugins/jquery.numeric.pack.js").script("plugins/jquery.floatnumber.js").$this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
	
	function departamento() {
		$this->rapyd->load("datagrid","dataform");
		$this->load->helper('openflash');

		$depto=$this->uri->segment(4);
		if ($depto===FALSE) redirect("ventas/analisis");                    
		$anio=$this->uri->segment(5);
		if ($anio===FALSE)$anio=date("Y");
		
		//$data['lista'] = open_flash_chart_object(800,300, site_url("ventas/mensuales/grafico/$mes/$anio"));
  
		$script ='
		$(function() {
			$(".inputnum").numeric(".");
		});
		';
   
		$link="ventas/analisis/linea/$depto/<#linea#>/$anio";
		$select='a.depto, a.linea, a.grupo, a.codigo, b.descrip';
		
		$depto2=$this->datasis->dameval("SELECT IFNULL(descrip, depto) dpto FROM dpto WHERE depto='$depto'");		
		
		$grid = new DataGrid("Ventas por l&iacute;nea del departamento $depto2 del a&ntilde;o $anio");
		$grid->use_function('number_format');
		$grid->column("L&iacute;neas", anchor($link, '<#descrip#>'),'nowrap=yes');
		for($i=1;$i<=12;$i++){
			$nmes=$this->calendar->get_month_name(str_pad($i, 2, "0", STR_PAD_LEFT));
			$grid->column($nmes, "<number_format><#m$i#>|0|,|.</number_format>" ,'align=right');
			$select.=",sum(a.tota*(month(a.fecha)=$i)) AS m$i ";
		}		
		$grid->db->select($select);  
		$grid->db->from('eventas a');
		$grid->db->join('line b','a.linea=b.linea','LEFT');
		$grid->db->where('year(a.fecha)',$anio);
		$grid->db->where('a.depto',$depto);
		$grid->db->groupby('linea');
		$grid->build();
		
		$at=array('title'=>"Regresar a Ventas por Departamentos del a&ntilde;o $anio");
		$salida=anchor("ventas/analisis/index/$anio","$anio",$at);
		
		$data['content'] = '<div style="overflow: auto; width: 100%;">'.$salida.$grid->output.'</div>';
		$data['title']   = "<h1>An&aacute;lisis de ventas</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);	
	}
	
  function linea() {
		$this->rapyd->load("datagrid","dataform");
		$this->load->helper('openflash');
		//$this->rapyd->uri->keep_persistence();

		$depto=$this->uri->segment(4);
		if ($depto===FALSE) redirect("ventas/analisis");
		$linea=$this->uri->segment(5);
		if ($linea===FALSE) redirect("ventas/analisis/departamento/$depto");                   
		$anio=$this->uri->segment(6);
		if ($anio===FALSE)$anio=date("Y");
		
		//$data['lista'] = open_flash_chart_object(800,300, site_url("ventas/mensuales/grafico/$mes/$anio")); 
		
		$depto2=$this->datasis->dameval("SELECT IFNULL(descrip, depto) dpto FROM dpto WHERE depto='$depto'");
		$linea2=$this->datasis->dameval("SELECT IFNULL(descrip, linea) linea FROM line WHERE linea='$linea'");
   
		$link="ventas/analisis/grupo/$depto/$linea/<#grupo#>/$anio";
		$select='a.depto, a.linea, a.grupo, a.codigo,b.nom_grup';
		
		$grid = new DataGrid("Ventas por grupos de el departamento $depto2 y l&iacute;nea $linea2");
		$grid->use_function('number_format');
		$grid->column("Grupo", anchor($link, '<#nom_grup#>'),'nowrap=yes');
		for($i=1;$i<=12;$i++){
			$nmes=$this->calendar->get_month_name(str_pad($i, 2, "0", STR_PAD_LEFT));
			$grid->column($nmes, "<number_format><#m$i#>|0|,|.</number_format>" ,'align=right');
			$select.=",sum(a.tota*(month(a.fecha)=$i)) AS m$i ";
		}
		$grid->db->select($select);  
		$grid->db->from('eventas a');
		$grid->db->join('grup b','a.grupo=b.grupo','LEFT');
		$grid->db->where('year(a.fecha)',$anio);
		$grid->db->where('a.depto',$depto);
		$grid->db->where('a.linea',$linea);
		$grid->db->groupby('grupo');
		$grid->build();
		
		$at=array('title'=>"Regresar a Ventas por Departamento del a&ntilde;o $anio");
		$salida=anchor("ventas/analisis/index/$anio","$anio",$at);
		$salida.=" ";
		$at=array('title'=>"Regresar a Ventas por linea del departamento $depto2");
		$salida.=anchor("ventas/analisis/departamento/$depto/$anio","$depto2",$at);
		$data['content'] = '<div style="overflow: auto; width: 100%;">'.$salida.$grid->output.'</div>';
		$data['title']   = "<h1>An&aacute;lisis de ventas</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}	
	function grupo() {
		$this->rapyd->load("datagrid","dataform");
		$this->load->helper('openflash');
		//$this->rapyd->uri->keep_persistence();
		$atts = array(
              'width'     =>'800',
              'height'    =>'600',
              'scrollbars'=>'yes',
              'status'    =>'yes',
              'resizable' =>'yes',
              'screenx'   =>'5',
              'screeny'   =>'5');

		$depto=$this->uri->segment(4);
		if ($depto===FALSE) redirect("ventas/analisis");
		$linea=$this->uri->segment(5);
		if ($linea===FALSE) redirect("ventas/analisis/departamento/$depto"); 
		$grupo=$this->uri->segment(6);
		if ($grupo===FALSE) redirect("ventas/analisis/departamento/$depto/$linea");                   
		$anio=$this->uri->segment(7);
		if ($anio===FALSE)$anio=date("Y");
		
		//$data['lista'] = open_flash_chart_object(800,300, site_url("ventas/mensuales/grafico/$mes/$anio")); 
   
		//$link="ventas/analisis/grupo/$depto/$linea/<#grupo#>/$anio";
		
		$depto2=$this->datasis->dameval("SELECT IFNULL(descrip, depto) dpto FROM dpto WHERE depto='$depto'");
		$linea2=$this->datasis->dameval("SELECT IFNULL(descrip, linea) linea FROM line WHERE linea='$linea'");
		$grup2=$this->datasis->dameval("SELECT IFNULL(nom_grup, grupo) grupo FROM grup WHERE grupo='$grupo'");
		
		$select='a.depto, a.linea, a.grupo, a.codigo,b.descrip';
		
		$link='inventario/sinvshow/dataedit/show/<#id#>';
		
		$grid = new DataGrid("Ventas por c&oacute;digo de el departamento $depto2, l&iacute;nea $linea2 y grupo $grup2");
		$grid->use_function('number_format');
		$grid->column("C&oacute;digo",anchor_popup($link,'<#codigo#>',$atts),'nowrap=yes');
		$grid->column("Descripci&oacute;n",'descrip','nowrap=yes');
		for($i=1;$i<=12;$i++){
			$nmes=$this->calendar->get_month_name(str_pad($i, 2, "0", STR_PAD_LEFT));
			$grid->column($nmes, "<number_format><#m$i#>|0|,|.</number_format>" ,'align=right');
			$grid->column('Cant.', "<number_format><#c$i#>|0|,|.</number_format>" ,'align=right');
			$select.=",sum(a.tota*(month(a.fecha)=$i)) AS m$i,sum(a.cana*(month(a.fecha)=$i)) AS c$i ";
		}
		$select.=",b.id AS id";
		$grid->db->select($select);  
		$grid->db->from('eventas a');
		$grid->db->join('sinv b','a.codigo=b.codigo','LEFT');
		$grid->db->where('year(a.fecha)',$anio);
		$grid->db->where('a.depto',$depto);
		$grid->db->where('a.linea',$linea);
		$grid->db->where('a.grupo',$grupo);
		$grid->db->groupby('codigo');
		$grid->build();
		
		$at=array('title'=>"Regresar a Ventas por Departamento del a&ntilde;o $anio");
		$salida=anchor("ventas/analisis/index/$anio","$anio",$at);
		$salida.=" ";
		$at=array('title'=>"Regresar a Ventas por linea del departamento $depto2");
		$salida.=anchor("ventas/analisis/departamento/$depto/$anio","$depto2",$at);
		$salida.=" ";
		$at=array('title'=>"Regresar a Ventas por grupo del departamento $depto2 y linea $linea2");
		$salida.=anchor("ventas/analisis/linea/$depto/$linea/$anio","$linea2",$at);
		
		$data['content'] = $salida.'<div style="overflow: auto; width: 850px; height: 400px;">'.$grid->output.'</div>';// 
		$data['title']   = "<h1>An&aacute;lisis de ventas</h1>";
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}
}
?>
