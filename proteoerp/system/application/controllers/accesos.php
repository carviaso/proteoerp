<?php
class Accesos extends Controller{
	function Accesos(){
		parent::Controller();
		$this->instalar();
	}

	function index(){
		$this->session->set_userdata('panel', 9);
		$this->datasis->modulo_id(904,1);

		$mSQL='SELECT us_codigo, CONCAT( us_codigo,\' - \' ,us_nombre ) FROM usuario WHERE us_nombre != \'SUPERVISOR\' ORDER BY us_codigo';
		$dropdown=$this->datasis->consularray($mSQL);
		$data['content']  = form_open('accesos/crear');
		$data['content'] .= form_dropdown('usuario',$dropdown);
		$data['content'] .= form_submit('pasa','Aceptar');
		$data['content'] .= form_close();
		$data['head']    = '';
		$data['title']   = '<h1>Administraci&oacute;n de accesos</h1>';
		$this->load->view('view_ventanas', $data);
	}

	function crear(){
		$this->datasis->modulo_id(904,1);

		if (isset($_POST['usuario']))
			$usuario = $_POST['usuario'];
		else
			$usuario = $this->uri->segment(3);
		if (empty($usuario)) 
			redirect('/accesos');
		if(isset($_POST['copia']))
			$copia=$_POST['copia'];
		else
			$copia='';

		$mSQL='SELECT us_codigo, CONCAT( us_codigo,\' - \' ,us_nombre ) FROM usuario WHERE us_nombre != \'SUPERVISOR\' ORDER BY us_codigo';
		$dropdown=$this->datasis->consularray($mSQL);
		$data['title'] = '<h1>Accesos del usuario: '.$usuario.'</h1>';
		$data['content']  = form_open('accesos/crear');
		$data['content'] .= 'Copiar de: '.form_dropdown('copia',$dropdown,$copia);
		$data['content'] .= form_submit('pasa','Copiar');
		$data['content'] .= form_hidden('usuario',$usuario).form_close();

		$query = $this->db->query("SELECT us_nombre FROM usuario WHERE us_codigo='$usuario'");
		if($query->num_rows() == 1){
			if(!empty($copia))
				$acceso=$copia;
			else
				$acceso=$usuario;

			$mSQL="SELECT aa.modulo,aa.titulo, aa.acceso,bb.panel FROM
			(SELECT a.modulo,a.titulo, IFNULL(b.acceso,'N') AS acceso ,a.panel 
			FROM intramenu AS a
			LEFT JOIN intrasida AS b ON a.modulo=b.modulo AND b.usuario=".$this->db->escape($acceso)."
			WHERE MID(a.modulo,1,1)!='0') AS aa
			JOIN intramenu AS bb ON MID(aa.modulo,1,3)=bb.modulo
			ORDER BY MID(aa.modulo,1,1), IF(LENGTH(aa.modulo)=1,0,1),bb.panel,MID(aa.modulo,2,2), MID(aa.modulo,2)";

			$mc = $this->db->query($mSQL);
			$data['content'].=form_open('accesos/guardar').form_hidden('usuario',$usuario).'<div id=\'ContenedoresDeData\'><table width=100% cellspacing="0">';
			$i=0;
			$panel = '';
			foreach( $mc->result() as $row ){
				if($row->acceso=='S') $row->acceso=TRUE; else $row->acceso=FALSE;
				
				if(strlen($row->modulo)==1) {
					$data['content'] .= '<tr><th colspan=2>'.$row->titulo.'</th></tr>';
					$panel = '';
				}elseif( strlen($row->modulo)==3 ) {
					if ($panel <> $row->panel ) {
						$data['content'] .= '<tr><td colspan=2 bgcolor="#CCDDCC">'.$row->panel.'</td></tr>';
						$panel = $row->panel ;
					};

					$data['content'] .= '<tr><td>'.$row->modulo.'-'.$row->titulo.'</td><td>'.form_checkbox('accesos['.$i.']',$row->modulo,$row->acceso).'</td></tr>';
					$i++;
				}else{
					$data['content'] .= '<tr><td><b>&nbsp;&nbsp;-&nbsp;</b>'.$row->titulo.'</td><td>'.form_checkbox('accesos['.$i.']',$row->modulo,$row->acceso).'</td></tr>';
					$i++;
				}
			}
			$data['content'].='</table></div>';
			$data['content'].=form_hidden('usuario',$usuario).form_submit('pasa','Guardar').form_close().anchor('/accesos','Regresar');;     
		}else
			$data['content']='Usuario no V&aacute;lido, por favor selecione un usuario correcto.';

		$data['head']    = style('estilos.css');
		$data['title']   = "<h1>Administraci&oacute;n de accesos, usuario <b>$usuario</b></h1>";
		$this->load->view('view_ventanas', $data);
	}

	function guardar(){
		$this->datasis->modulo_id(904001);
		$usuario = $this->db->escape($_POST['usuario']);
		$modprin=0;
		$mSQL="DELETE FROM intrasida WHERE usuario=$usuario";
		$this->db->simple_query($mSQL);

		if (isset($_POST['accesos']) > 0 ){
			foreach( $_POST['accesos'] as $codigo ){
				if($modprin != $codigo[0]){
					$modprin=$codigo[0];
					$mSQL="INSERT INTO intrasida (usuario,modulo,acceso) VALUES($usuario,'$modprin' ,'S')";
					$this->db->simple_query($mSQL);
				}
				$mSQL="INSERT INTO intrasida (usuario,modulo,acceso) VALUES($usuario,'$codigo' ,'S')";
				$this->db->simple_query($mSQL);
			}
		}

		$data['head']    = style('estilos.css');
		$data['title']   = heading('Accesos Guardados para el usuario: '.$usuario);
		$data['content'] = anchor('/accesos','Regresar');
		$this->load->view('view_ventanas', $data);
	}

	function instalar(){
		$fields = $this->db->field_data('intrasida','modulo');
		if($fields[1]->type!='string'){
			$mSQL="ALTER TABLE `intrasida`  CHANGE COLUMN `modulo` `modulo` VARCHAR(11) NOT NULL DEFAULT '0' AFTER `usuario`";
			$this->db->simple_query($mSQL);
		}
	}

	function usuarios(){
		$mSQL = "SELECT * FROM usuario ORDER BY us_nombre";
		$query = $this->db->query($mSQL);
		$results = $query->num_rows(); 
		$arr = $this->datasis->codificautf8($query->result_array());
		echo '{success:true, message:"Loaded data" ,results:'. $results.', data:'.json_encode($arr).'}';
	}


	function accextjs(){

		$encabeza = '<table width="100%" bgcolor="#2067B5"><tr><td align="left" width="100px"><img src="'.base_url().'assets/default/css/templete_01.jpg" width="120"></td><td align="center"><h1 style="font-size: 20px; color: rgb(255, 255, 255);" onclick="history.back()">ACCESO DE USUARIOS</h1></td><td align="right" width="100px"><img src="'.base_url().'assets/default/images/cerrar.png" alt="Cerrar Ventana" title="Cerrar Ventana" onclick="parent.window.close()" width="25"></td></tr></table>';
		$modulo = 'usuario';
		$urlajax = 'accesos/';

		$script = "
Ext.define('usuarioMod', {
	extend: 'Ext.data.Model',
	fields: [".$this->datasis->extjscampos("usuario")."],
	proxy: {type: 'ajax',noCache: false,
		api: {	read   : urlApp+'accesos/usuarios',method: 'POST'},
		reader: {type: 'json',root: 'data',successProperty: 'success',messageProperty: 'message',totalProperty: 'results'}}
});

//////////////////////////////////////////////////////////
//
var usuarioCol = [
	{ header: 'Codigo',   width: 80, sortable: true, dataIndex: 'us_codigo',  field: { type: 'textfield' }, filter: { type: 'string' }},
	{ header: 'Nombre',   width:140, sortable: true, dataIndex: 'us_nombre',  field: { type: 'textfield' }, filter: { type: 'string' }},
	{ header: 'Sup.',     width: 30, sortable: true, dataIndex: 'supervisor', field: { type: 'textfield' }, filter: { type: 'string' }},
	{ header: 'Vende',    width: 40, sortable: true, dataIndex: 'vendedor',   field: { type: 'textfield' }, filter: { type: 'string' }},
	{ header: 'Cajero',   width: 40, sortable: true, dataIndex: 'cajero',     field: { type: 'textfield' }, filter: { type: 'string' }},
	{ header: 'Almacen',  width: 40, sortable: true, dataIndex: 'almacen',    field: { type: 'textfield' }, filter: { type: 'string' }},
	{ header: 'Sucursal', width: 40, sortable: true, dataIndex: 'sucursal',   field: { type: 'textfield' }, filter: { type: 'string' }},
	{ header: 'Activo',   width: 30, sortable: true, dataIndex: 'activo',     field: { type: 'textfield' }, filter: { type: 'string' }},
];


// create the Data Store
var usuarioStore = Ext.create('Ext.data.Store', {
	model: 'usuarioMod',
	autoLoad: false,
	autoSync: true,
	method: 'POST'
});



Ext.onReady(function() {

	//
	var usuarioGrid = Ext.create('Ext.grid.Panel', {
		width:   '100%',
		height:  '100%',
		store:   usuarioStore,
		title:   'Usuarios',
		iconCls: 'icon-grid',
		frame:   true,
		features: [ { ftype: 'filters', encode: 'json', local: false } ],
		columns: usuarioCol
	});


	Ext.create('Ext.container.Viewport', {
		layout: 'border',
		items: [
			{
				region: 'north',
				html: '".$encabeza."',
				autoHeight: true,
				border: false,
				margins: '0 0 5 0'
			},
			{
				region: 'west',
				collapsible: true,
				title: 'Usuarios',
				width: 300,
				items: usuarioGrid

			},
			{
				region: 'south',
				title: 'South Panel',
				collapsible: true,
				html: 'Information goes here',
				split: true,
				height: 100,
				minHeight: 100
			},
			{
				region: 'center',
				title:'Listados',
				border:false,
				layout: 'fit',
				html: '<h1>aaa</h1>'
			}
		]
	});
	usuarioStore.load();

});

	";
		$data['encabeza']    = "ACCESOS";
		$data['script']  = $script;
		$data['title']  = heading('Accesos');
		$this->load->view('extjs/ventana',$data);
		
	}

}
