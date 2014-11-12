<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class ccli extends Controller {
	var $titp='Cobro a clientes';
	var $tits='Cobro a cliente';
	var $url ='finanzas/ccli/';

	function ccli(){
		parent::Controller();
	}

	function index(){
		exit('Este modulo fue deshabilitado, debe emitir los pagos por finanzas -> movimientos de clientes.');
	}
}
