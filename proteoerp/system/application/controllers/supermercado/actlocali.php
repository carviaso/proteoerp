<?php
/**
 * ProteoERP
 *
 * @autor    Ender Ochoa
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class actlocali extends Controller {

	var $url  ='supermercado/actlocali';
	var $tits ='Actualizar Localizaciones';

	function actlocali(){
		parent::Controller();
		$this->load->library('rapyd');
		$this->datasis->modulo_id('31E',1);
		$this->limite = 100;
	}

	function index(){
		$salida =anchor($this->url.'/locali','Modificar Localizaciones');
		$salida.='</br>';
		$salida.=anchor($this->url.'/mfisicocero','Colocar Productos no contados en Cero(0)');

		$data['content'] = $salida;
		$data['title']   = '<h1>Menu</h1>';
		$data['script']  = '';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);
	}

	function locali(){
		$this->rapyd->load('dataform');
		$form = new DataForm($this->url.'/locali/process');

		/*$form->numero = new inputField('Numero de Inventario Fisico', 'numero');
		$form->numero->rule      ='callback_chinvfis|required';
		$form->numero->size      =10;
		$form->numero->maxlength = 8;
		$form->numero->minlength = 8;*/

		$form->numero = new dropdownField('Inventario F&iacute;sico', 'numero');
		$form->numero->style='width:230px;';
		$form->numero->option('','Seleccionar');
		$form->numero->rule ='callback_chinvfis|required';
		$form->numero->options('SELECT numero,CONCAT_WS(\'-\',numero,ubica,DATE_FORMAT(fecha,\'%d/%m/%Y\')) AS val FROM maesfisico  GROUP BY numero ORDER BY fecha DESC LIMIT '.$this->limite);
		$form->numero->append('N&uacute;mero-Almac&eacute;n-Fecha');

		$form->locali = new inputField('Localizaci&oacute;n a colocar', 'locali');
		$form->locali->size = 10;
		$form->locali->rule = 'required';

		$form->oper = new dropdownField('Tomar en cuenta ','oper');
		$form->oper->option('' ,'Seleccionar');
		$form->oper->option('2','Todos');
		$form->oper->option('1','Solo los que fraccion sea mayor a cero (0)');
		$form->oper->option('0','Solo los que fraccion son igual a cero (0)');
		$form->open->rule = 'required|enum[0,1,2]';

		$back_url=site_url($this->url.'/index');
		$form->button('btn_regre','Regresar',"javascript:window.location='${back_url}'",'BL');
		$form->submit('btnsubmit','Actualizar');
		$form->build_form();

		$salida='';
		if($form->on_success()){
			$numero  =$form->numero->newValue;
			$locali  =$form->locali->newValue;
			$oper    =$form->oper->newValue;
			$dbnumero=$this->db->escape($numero);
			$dblocali=$this->db->escape($locali);
			$dboper  =intval($oper);

			$mSQL="UPDATE
			maesfisico AS a
			JOIN ubic  AS b ON a.codigo = b.codigo AND a.ubica=b.ubica
			SET b.locali=${dblocali}
			WHERE a.numero=${dbnumero} AND
			((a.fraccion>0)*(${dboper}=1)+(a.fraccion=0)*(${dboper}=0)+(a.fraccion>=0)*(${dboper}=2))";

			$bool=$this->db->simple_query($mSQL);
			if($bool){
				logusu('actlocali',"Se actualizaron las localizaciones ${numero}");
				$salida='Se actualizo correctamente';
			}else{
				$salida='Hubo problemas con la operaci&oacute;n, consulte soporte t&eacute;cnico';
			}
		}

		$data['content'] = $form->output.'<p style="text-align:center">'.$salida.'</p>';
		$data['title']   = heading($this->tits);
		$data['script']  = '';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);

	}

	function mfisicocero(){
		$this->rapyd->load('dataform');
		$form = new DataForm($this->url.'/mfisicocero/process');

		$form->numero = new dropdownField('Inventario F&iacute;sico', 'numero');
		$form->numero->style='width:230px;';
		$form->numero->option('','Seleccionar');
		$form->numero->rule ='callback_chinvfis|required';
		$form->numero->options('SELECT numero,CONCAT_WS(\'-\',numero,ubica,DATE_FORMAT(fecha,\'%d/%m/%Y\')) AS val FROM maesfisico  GROUP BY numero ORDER BY fecha DESC LIMIT '.$this->limite);
		$form->numero->append('N&uacute;mero-Almac&eacute;n-Fecha');

		$back_url=site_url($this->url.'/index');
		$form->button('btn_regre','Regresar',"javascript:window.location='${back_url}'",'BL');
		$form->submit('btnsubmit','Actualizar');
		$form->build_form();

		$salida='';
		if ($form->on_success()){
			$numero   = $form->numero->newValue;
			$dbnumero = $this->db->escape($numero);

			$row=$this->datasis->damerow("SELECT fecha, ubica FROM maesfisico WHERE numero=${dbnumero} LIMIT 1");
			if(!empty($row)){
				$ubica    = $row['ubica'];
				$fecha    = $row['fecha'];
				$dbubica  = $this->db->escape($ubica);
				$dbfecha  = $this->db->escape($fecha);
				$dbusr    = $this->db->escape($this->secu->usuario());

				$mSQL="INSERT INTO maesfisico
				(id,codigo,ubica,locali,cantidad,fraccion,acantidad,afraccion,fecha,numero,usuario,estanpa,hora)
				SELECT
					NULL AS id,
					a.codigo,
					${ubica}    AS ubica,
					'BLANCO'    AS locali,
					0           AS cantidad,
					0           AS fraccion,
					0           AS acantidad,
					0           AS afraccion,
					${dbfecha}  AS fecha,
					${dbnumero} AS numero,
					${dbusr}    AS usuario,
					CURDATE()   AS fecha,
					CURTIME()   AS hora
				FROM maes AS a
				LEFT JOIN maesfisico AS b ON a.codigo=b.codigo AND a.numero=${dbnumero}
				WHERE b.codigo IS NULL";

				$bool=$this->db->simple_query($mSQL);
				if($bool){
					logusu('actlocali',"Se pasaron los no contados a cero ${numero}");
					$salida='Se colocaron TODOS los productos de inventario <b>NO CONTADOS</b> en cero (0)';
				}else{
					$salida='Hubo problemas con la operaci&oacute;n, consulte soporte t&eacute;cnico';
				}
			}else{
				$salida='Inventario inexistente';
			}
		}

		$data['content'] = $form->output.'<p style="text-align:center">'.$salida.'</p>';
		$data['title']   = heading('Colocar producto no contados en cero (0)');
		$data['script']  = '';
		$data['head']    = $this->rapyd->get_head();
		$this->load->view('view_ventanas', $data);

	}

	function chinvfis($numero){
		$numero= $this->db->escape($numero);
		$cant  = intval($this->datasis->dameval("SELECT COUNT(*) AS cana FROM maesfisico WHERE numero=${numero}"));
		if($cant>0){
			return true;
		}
		$this->validation->set_message('chinvfis', "No existe el numero de inventario indicado ${numero}");
		return false;
	}
}
