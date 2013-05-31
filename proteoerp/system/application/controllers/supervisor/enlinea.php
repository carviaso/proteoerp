<?php
class Enlinea extends Controller{
	
	function Enlinea(){
		parent::Controller();
		$this->load->library('rapyd');
	}

	function index(){
		redirect('supervisor/enlinea/pasillo');
	}

	function pasillo(){
		//$this->datasis->modulo_id(926,1);
		$this->rapyd->load('datagrid');
		$uri = anchor_popup('http://<#url#>/<#proteo#>','<#sucursal#>');

		function linea($url,$proteoerp){
			$CI =& get_instance();
			$CI->load->library('xmlrpc');
			$CI->xmlrpc->xmlrpc_defencoding=$CI->config->item('charset');

			$server_url = reduce_double_slashes("http://$url"."/$proteoerp/".'rpcserver');
			$CI->xmlrpc->server($server_url , 80);
			$CI->xmlrpc->method('ventanainf');
			$request = array('a');
			$CI->xmlrpc->request($request);

			if(!$CI->xmlrpc->send_request())
				return 'No Disponible';
			else{
				$ima="http://".$url."/".$proteoerp."/supervisor/enlinea/ventana";
			return "<iframe src='$ima' width='500px' height='110px'/></iframe>";
			}
		}

		$grid = new DataGrid();
		$grid->db->from('sucumon');
		$grid->order_by('codigo','desc');
		$grid->per_page=15;
		$grid->use_function('linea','ver');

		$grid->column('Sucursal', $uri                                );
		$grid->column('En Linea', '<linea><#url#>|<#proteo#></linea>' );

		$grid->column('Actualizar Proteo' ,'<a href="http://<#url#>/<#proteo#>/supervisor/mantenimiento/actualizaproteo" />Actualizar</a>');

		$grid->build();

		$data['content'] = $grid->output;
		$data['title']   = heading('Informacion de Sucursales');
		$data["head"]    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function ventana(){
		$ukardex=$this->datasis->dameval("SELECT max(fecha) valor FROM costos");
		$ventas =$this->datasis->damerow("SELECT SUM(totals*IF(tipo_doc='D',-1,1)) AS valor,
						 (SUM(totals*IF(tipo_doc='D',-1,1)*(referen='E'))) efectivo,
						 (SUM(totals*IF(tipo_doc='D',-1,1)*(referen='C'))) credito FROM sfac WHERE fecha=(SELECT MAX( fecha) FROM sfac) AND tipo_doc<>'X' AND MID(numero,1,1)<>'_' ");

		$fsfac  =$this->datasis->dameval("SELECT MAX( fecha) FROM sfac");

		$conten['vendido' ]  =$ventas['valor'];
		$conten['efectivo']  =$ventas['efectivo'];
		$conten['credito' ]  =$ventas['credito'];
		$conten['ukardex' ]  =$ukardex;
		$conten['fsfac' ]    =$fsfac;
		$data['content']     = $this->load->view('view_enlinea', $conten,true);
		$data['head']        = $this->rapyd->get_head();
		$this->load->view('view_ventanas_sola', $data);
	}
}
