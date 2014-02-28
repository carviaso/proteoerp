<?php
/** 
 * ProteoERP 
 * 
 * @autor    Andres Hocevar 
 * @license  GNU GPL v3
*/
class Consulcubo extends Controller {
	
	function Consulcubo(){
		parent::Controller();
		//$this->olap=$this->load->database('olap',TRUE);
		//$this->load->library("rapyd");
		//$this->rapyd->set_connection('olap');
		//$this->load->database('olap',TRUE);
	}
	
	function index(){
		$this->load->library('Xmlarequest');
		$MDXQuery = 	'SELECT NON EMPTY 
					{[Measures].[Factura], 
					 [Measures].[Forma de Pago],
					 [Measures].[Total General]} ON columns, 
				NON EMPTY 
					{[Cliente].Children} ON rows 
				FROM [Ventas] 
				WHERE 
					([Producto.Departamentos].[All Producto.Departamentoss].[TECNOLOGIA])';
		
		$Request =  new XMLARequest();
		$Request->dataSource = 'Provider=Mondrian;DataSource=DatasisCubo;';
		$Request->xmlaProvider = 'http://192.168.0.143:8080/mondrian/xmla.jsp';
		$Request->setQueryFormat('Tabular');
		$Request->Catalog = 'DatasisVentas';
		$Request->mdxQuery($MDXQuery);
		
		$xmla = $Request->getMondrianResponse();
		
		$xmlaRows = XMLARequest::getXMLASetOfRows($xmla);
		print_r($xmlaRows);
		
	}
}
?>