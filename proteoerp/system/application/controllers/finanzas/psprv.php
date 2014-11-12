<?php include('common.php');
class psprv extends Controller {
	var $titp='Pago a proveedores';
	var $tits='Pago a proveedor';
	var $url ='finanzas/psprv/';

	function psprv(){
		parent::Controller();
	}

	function index(){
		exit('Este modulo fue deshabilitado, debe emitir los pagos por finanzas -> movimientos de proveedor.');
	}

}
