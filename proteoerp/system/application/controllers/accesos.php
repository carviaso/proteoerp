<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
class Accesos extends Controller{
	function Accesos(){
		parent::Controller();
		$this->instalar();
	}

	function index(){
		$this->session->set_userdata('panel', 9);
		$this->datasis->modulo_id(904,1);
		$this->datasis->modintramenu( 950, 540, 'accesos' );
		$this->instalar();
		redirect($this->url.'accesos/crear');

	}

	//******************************************************************
	// Administra los accesos
	//
	function crear(){
		$this->datasis->modulo_id(904,1);

		if (isset($_POST['usuario']))
			$usuario = $_POST['usuario'];
		else
			$usuario = $this->uri->segment(3);

		if(isset($_POST['copia']))
			$copia=$_POST['copia'];
		else
			$copia='';


		$script = '';
		$mSQL='SELECT us_codigo, CONCAT( us_codigo,\' - \' ,us_nombre ) FROM usuario WHERE us_nombre != \'SUPERVISOR\' ORDER BY us_codigo';
		$dropdown = $this->datasis->consularray($mSQL);

		$salida  = '';
		$salida .= '<table width="100%"><tr>';
		$salida .= '<td align="right"><label>Usuario:                  </label></td><td>'.form_dropdown('usuario',$dropdown,$usuario,'id="usuario" style="font-size:12px;"').'</td>';
		$salida .= '<td align="right"><a href="#" onclick="copiaac()">Copiar</a> accesos de: </td><td>'.form_dropdown('copia',  $dropdown,$copia,  'id="copia"   style="font-size:12px;"').'</td>';
		$salida .= '</tr></table>';

		$query = $this->db->query("SELECT us_nombre FROM usuario WHERE us_codigo='$usuario'");

		if(!empty($copia))
			$acceso = $copia;
		else
			$acceso = $usuario;

		// Opciones del Menu
		$mSQL="
		SELECT a.modulo,a.titulo FROM intramenu AS a
		WHERE MID(a.modulo,1,1)!='0' AND LENGTH(a.modulo) = 1
		ORDER BY a.modulo";
		$mc1 = $this->db->query($mSQL);
		//$salida .= form_open('accesos/guardar').form_hidden('usuario',$usuario);
		$salida .='<table width="100%"><tr><td>
		<div id=\'ContenedoresTitulo\' style=\'width:140px;\'>
			<table width=100% cellspacing="0">
			<tr><th>Grupos</th></tr>';

		foreach( $mc1->result() as $row ){
			$salida .= '<tr><td><a href="#" onclick="traeitem( $(\'#usuario\').val() ,\''.$row->modulo.'\')">'.$row->titulo.'</a></td></tr>';
			$panel = '';
		}
		$salida .= "</table>\n";
		$salida .= "</div>\n";
		$salida .= "</td><td>\n";

		$salida .= '
		<div id=\'ContenedoresDedata\' style=\'width:270px;\'>
			<p style="font: bold 18px;">Seleccione un Grupo del panel izquierdo</p>
		</div>';
		$salida .= "</td><td>\n";

		// Opciones del Modulos
		$mSQL = "
		SELECT CONCAT(substr(a.modulo,1,4), replace(replace(substr(a.modulo,5,16),'OTR',''),'LIS','')) modulo, b.nombre
		FROM tmenus a JOIN modulos b ON TRIM(a.modulo)=TRIM(b.modulo)
		WHERE (a.modulo <> 'MENUINT') AND a.modulo regexp '^[^0-9]+$'
		GROUP BY concat(substr(a.modulo,1,4),replace(replace(substr(a.modulo,5,16),'OTR',''),'LIS',''))
		ORDER BY modulo,secu";

		$mc1 = $this->db->query($mSQL);
		//$salida .= form_open('accesos/guardar').form_hidden('usuario',$usuario);
		$salida .='<table width="100%"><tr><td>
		<div id=\'ContenedoresModulo\' style=\'width:260px;\'>
			<table width=100% cellspacing="0">
			<tr><th colspan="2">Modulos</th></tr>';

		foreach( $mc1->result() as $row ){
			$salida .= '<tr><td>'.$row->modulo.'</td><td><a style="font:10px;" href="#" onclick="traetmenu( $(\'#usuario\').val() ,\''.$row->modulo.'\')">'.$row->nombre.'</a></td></tr>';
			$panel = '';
		}
		$salida .= "</table>\n";
		$salida .= "</div>\n";
		$salida .= "</td><td>\n";

		$salida .= '
		<div id=\'ContenedoresDatasis\' style=\'width:230px;\'>
			<p style="font: bold 18px;">Seleccione un Modulo del panel izquierdo</p>
		</div>';

		$salida .= '</td></tr></table>';
		//$salida .= anchor('/accesos','Regresar');;

		$script = '
		<style>
			#ContenedoresDedata {overflow: auto; height: 400px;border: thin solid #E4E4E4;}
			#ContenedoresDedata table td{ border-bottom: thin solid #E4E4E4;}
			#ContenedoresDedata a:link, a:visited{text-decoration: none;font-weight: bold;color: #2E4B70;}
			#ContenedoresDedata a:hover{text-decoration: none;font-weight: bold;color: #0066FF;}
			#ContenedoresDedata table th{background:url(../assets/default/css/accor_tbg.gif);}

			#ContenedoresModulo {overflow: auto; height: 400px;border: thin solid #E4E4E4;}
			#ContenedoresModulo table td{ border-bottom: thin solid #E4E4E4; font-size:11px}
			#ContenedoresModulo a:link, a:visited{text-decoration: none;font-weight: bold;color: #2E4B70;}
			#ContenedoresModulo a:hover{text-decoration: none;font-weight: bold;color: #0066FF;}
			#ContenedoresModulo table th{background:url(../assets/default/css/accor_tbg.gif);}

			#ContenedoresTitulo {overflow: auto; height: 400px;border: thin solid #E4E4E4;}
			#ContenedoresTitulo table td{ border-bottom: thin solid #E4E4E4;}
			#ContenedoresTitulo a:link, a:visited{text-decoration: none;font-weight: bold;color: #2E4B70;}
			#ContenedoresTitulo a:hover{text-decoration: none;font-weight: bold;color: #0066FF;}
			#ContenedoresTitulo table th{background:url(../assets/default/css/accor_tbg.gif);}

			#ContenedoresDatasis {overflow: auto; height: 400px;border: thin solid #E4E4E4;}
			#ContenedoresDatasis table td{ border-bottom: thin solid #E4E4E4;}
			#ContenedoresDatasis a:link, a:visited{text-decoration: none;font-weight: bold;color: #2E4B70;}
			#ContenedoresDatasis a:hover{text-decoration: none;font-weight: bold;color: #0066FF;}
			#ContenedoresDatasis table th{background:url(../assets/default/css/accor_tbg.gif);}

			.overflow { height: 400px; font-size:11px; }
			.select { width:350px;}
			.ui-selectmenu-text { font-size:11px;}
		</style>
		<script>
		$(function() {
			//$("#usuario").selectmenu({select: function( event, ui ) { limpia();}})
			//.selectmenu("menuWidget").addClass("overflow");
			//$("#copia"  ).selectmenu().selectmenu("menuWidget").addClass("overflow");
		});
		function traeitem( usuario, modulo ){
			$.post( "'.site_url("accesos/traeitem/").'", { usuario: usuario, modulo: modulo })
			.done(function( data ) {
				$("#ContenedoresDedata").html(data);
			});
		}
		function traetmenu( usuario, modulo ){
			$.post( "'.site_url("accesos/traetmenu/").'", { usuario: usuario, modulo: modulo })
			.done(function( data ) {
				$("#ContenedoresDatasis").html(data);
			});
		}
		function guardainter(usuario, modulo){
			$.post( "'.site_url("accesos/guardainter/").'", { usuario: usuario, modulo: modulo });
		}
		function guardatmenu(usuario, modulo){
			$.post( "'.site_url("accesos/guardatmenu/").'", { usuario: usuario, modulo: modulo });
		}

		function copiaac(){
			var temp = {
			state0: {
				html:"<h1>Desea copiar los acceso "+$("#copia").val()+" a "+$("#usuario").val()+" </h1>",
				buttons: { Cancelar: false, Copiar: true },
				focus: 1,
				submit:function(e,v,m,f){
					if(v){
						e.preventDefault();
						$.post("'.site_url("accesos/copiaac/").'", { usuario: $("#usuario").val(), copia: $("#copia").val() })
						.done(function(data){
							$.prompt.getStateContent("state1").find("#resultado").html(data);
						});
						limpia();
						$.prompt.goToState("state1");
						return false;
					}
					$.prompt.close();
				}
			},
			state1: {
				html:"<span id=\'resultado\'></span>",
				buttons: { Salir: 0 },
				focus: 1,
				submit:function(e,v,m,f){
					e.preventDefault();
					if(v==0)
						$.prompt.close();
					}
				}
			};
			$.prompt(temp);
		}

		function limpia(){
			$("#ContenedoresDedata").html("Seleccione un Grupo");
			$("#ContenedoresDatasis").html("Seleccione un Modulo");
		}
		</script>
		';

		$data['script']  = $script;
		$data['content'] = $salida;
		$data['head']    = script('jquery-min.js');
		$data['head']   .= script('jquery-ui.min.js');
		$data['head']   .= script('jquery-impromptu.js');
		$data['head']   .= style('jquery-ui.min.css');
		$data['head']   .= style('themes/proteo/proteo.css');
		$data['head']   .= style('estilos.css');
		$data['head']   .= style('impromptu/default.css');


		$data['title']   = '<h1>Accesos del usuario:</h1>';
		$data['title']   = "<h1>Administraci&oacute;n de accesos, usuario <b>$usuario</b></h1>";
		$this->load->view('view_ventanas', $data);
	}

	//******************************************************************
	// Copia los accesso
	//
	function copiaac(){
		$usuario = $this->input->post('usuario');
		$copia   = $this->input->post('copia');

		if ( $usuario <> $copia && $usuario<>'' && $copia<>'' ){
			$mSQL="DELETE FROM intrasida WHERE usuario=".$this->db->escape($usuario);
			$this->db->query($mSQL);

			//Copia desde el usuario copia
			$mSQL = "INSERT INTO intrasida (usuario, modulo, acceso) SELECT ? usuario, modulo, acceso FROM intrasida WHERE usuario=? ";
			$this->db->query($mSQL, array($usuario, $copia ));
			echo "Insertados Nuevos Accesos <br>";

			//Borra los del usuario
			$mSQL = "DELETE FROM sida WHERE usuario=? ";
			$this->db->query($mSQL, array($usuario));
			echo "Eliminado Accesos anteriores<br>";

			//Porsia Agrega desde tmenus
			$mSQL = "INSERT IGNORE INTO sida (usuario, modulo, acceso) SELECT ? usuario, codigo modulo, 'N' FROM tmenus ";
			$this->db->query($mSQL, array($copia));
			echo 'Insertados Accesos Faltantes<br>';

			//Copia desde el usuario copia
			$mSQL = "INSERT INTO sida (usuario, modulo, acceso) SELECT ? usuario, modulo, acceso FROM sida WHERE usuario=? ";
			$this->db->query($mSQL, array($usuario, $copia ));
			echo "Insertados Nuevos Accesos <br>";

			echo "<h1>El Usuario ${usuario} ahora tiene los accesos de ${copia}</h1>";
			logusu('SIDA',"Copiado accesos de ${usuario} a  ${copia}");
		} else {
			echo "<h1>Usuarios origen y destino iguales ${usuario} = ${copia}</h1>";
		}

	}


	//******************************************************************
	// Trae intramenu por ajax
	//
	function traeitem(){
		$usuario = $this->input->post('usuario');
		$modulo  = $this->input->post('modulo');
		$i       = 0;
		$panel   = '';

		$salida = '<table width=100% cellspacing="0" style="font-size:0.8em;">';

		$mSQL="SELECT aa.modulo,aa.titulo, aa.acceso, bb.panel FROM
			(SELECT a.modulo,a.titulo, IFNULL(b.acceso,'N') AS acceso ,a.panel
			FROM intramenu AS a
			LEFT JOIN intrasida AS b ON a.modulo=b.modulo AND b.usuario=".$this->db->escape($usuario)."
			WHERE MID(a.modulo,1,1) = ".$this->db->escape($modulo)." AND LENGTH(TRIM(a.modulo)>1 ) ) AS aa
			JOIN intramenu AS bb ON MID(aa.modulo,1,3)=bb.modulo
			ORDER BY MID(aa.modulo,1,1), IF(LENGTH(aa.modulo)=1,0,1),bb.panel,MID(aa.modulo,2,2), MID(aa.modulo,2)";

		$mc = $this->db->query($mSQL);
		foreach( $mc->result() as $row ){
			if($row->acceso=='S') $row->acceso='checked'; else $row->acceso='';

			if(strlen($row->modulo)==1) {
				$salida .= '<tr><th colspan=2>'.$row->titulo.'</th></tr>';
				$panel = '';
			}elseif( strlen($row->modulo)==3 ) {
				if ($panel <> $row->panel ) {
					$salida .= '<tr><td colspan=2 bgcolor="#CCDDCC">'.$row->panel.'</td></tr>';
					$panel = $row->panel;
				};

				$salida .= '<tr><td>'.$row->modulo.'-'.$row->titulo.'</td><td><input type="checkbox" name="accesos['.$i.']" value="'.$row->modulo.'" '.$row->acceso.' onchange="guardainter(\''.$usuario.'\',\''.$row->modulo.'\')"></td></tr>'."\n";
				$i++;
			}else{
				$salida .= '<tr><td><b>&nbsp;&nbsp;-&nbsp;</b>'.$row->titulo.'</td><td><input type="checkbox" name="accesos['.$i.']" value="'.$row->modulo.'" '.$row->acceso.' onchange=\'guardainter("'.$usuario.'","'.$row->modulo.'")\'></td></tr>'."\n";
				$i++;
			}
		}
		$salida .='</table>';
		echo $salida;
	}

	//******************************************************************
	// Cambia los accesos
	//
	function guardainter(){
		$usuario = $this->input->post('usuario');
		$modulo  = $this->input->post('modulo');
		$dbusr   = $this->db->escape($usuario);
		$dbmodulo= $this->db->escape($modulo);

		$mSQL = "SELECT COUNT(*) AS cana FROM intrasida WHERE usuario=${dbusr} AND modulo=${dbmodulo}";
		$cana = intval($this->datasis->dameval($mSQL));
		if($cana == 0 ){
			$mSQL="INSERT IGNORE INTO intrasida (usuario,modulo,acceso) VALUES(${dbusr}, ${dbmodulo} ,'S')";
			$this->db->query($mSQL);
		}else{
			$mSQL="UPDATE intrasida SET acceso=IF(acceso='N','S','N') WHERE usuario=${dbusr} AND modulo=${dbmodulo}";
			$this->db->query($mSQL);
		}
		$modprin  =$modulo[0];
		$dbmodprin=$this->db->escape($modprin);
		$dbmodprib=$this->db->escape($modprin.'%');
		$mSQL = "SELECT COUNT(*) AS cana FROM intrasida WHERE usuario=${dbusr} AND modulo LIKE ${dbmodprib} AND acceso='S' AND CHAR_LENGTH(modulo)>1";

		$cana = intval($this->datasis->dameval($mSQL));
		if($cana>0){
			$mSQL="REPLACE INTO intrasida (usuario,modulo,acceso) VALUES(${dbusr},${dbmodprin},'S')";
			$this->db->simple_query($mSQL);
		}else{
			$mSQL="REPLACE INTO intrasida (usuario,modulo,acceso) VALUES(${dbusr},${dbmodprin},'N')";
			$this->db->simple_query($mSQL);
		}

	}

	//******************************************************************
	// Trae intramenu por ajax
	//
	function traetmenu(){
		$usuario = $this->input->post('usuario');
		$modulo  = $this->input->post('modulo');
		$i       = 0;
		$panel   = $modulo;

		$salida = '<table width=100% cellspacing="0" style="font-size:0.8em;">';
		$salida .= '<tr><th colspan=2>'.$modulo.'</th></tr>';

		$mSQL  = "
			SELECT a.codigo, a.modulo, a.secu, a.titulo, b.acceso, b.usuario
			FROM tmenus a LEFT JOIN sida b ON a.codigo = b.modulo
				AND b.usuario=".$this->db->escape($usuario)."
			WHERE a.modulo <> 'MENUINT' AND a.modulo regexp '^[^0-9]+$'
				AND a.modulo IN (".$this->db->escape($modulo).",".$this->db->escape($modulo.'LIS').",".$this->db->escape($modulo.'OTR').")
				AND a.titulo NOT IN ('Prox','Ante','Busca','Tabla')
				AND a.ejecutar!=''
			ORDER BY modulo,secu";

		$mc = $this->db->query($mSQL);
		foreach( $mc->result() as $row ){
			if($row->acceso=='S') $row->acceso='checked'; else $row->acceso='';
			if( $panel != $row->modulo){
				if ( substr($row->modulo,-3) == 'LIS' )
					$salida .= '<tr><td colspan=2 bgcolor="#CCDDCC">REPORTES</td></tr>';
				else
					$salida .= '<tr><td colspan=2 bgcolor="#CCDDCC">FUNCIONES</td></tr>';

				$panel = $row->modulo;
			}
			$salida .= '<tr><td>'.$row->titulo.'</td><td><input type="checkbox" name="accesos['.$i.']" value="'.$row->modulo.'" '.$row->acceso.' onchange="guardatmenu(\''.$usuario.'\',\''.$row->codigo.'\')"></td></tr>'."\n";
			$i++;
		}
		$salida .='</table>';
		echo $salida;
	}

	//******************************************************************
	// Cambia los accesos
	//
	function guardatmenu(){
		$usuario = $this->input->post('usuario');
		$modulo  = $this->input->post('modulo');

		$mSQL = "SELECT COUNT(*) FROM sida WHERE usuario=".$this->db->escape($usuario)." AND modulo=".$this->db->escape($modulo) ;
		if ( $this->datasis->dameval($mSQL) == 0 ){
			$mSQL="INSERT IGNORE INTO sida (usuario,modulo,acceso) VALUES(".$this->db->escape($usuario).", ".$this->db->escape($modulo)." ,'S')";
			$this->db->query($mSQL);
		} else {
			$mSQL="UPDATE sida SET acceso=IF(acceso='N','S','N') WHERE usuario=".$this->db->escape($usuario)." AND modulo=".$this->db->escape($modulo) ;
			$this->db->query($mSQL);
		}
		echo $mSQL;
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

		//Si no esta la tabla la crea
		$mSQL = "CREATE TABLE IF NOT EXISTS modulos (modulo VARCHAR(20) NOT NULL DEFAULT '', nombre VARCHAR(50) NULL DEFAULT NULL, id INT(11) NOT NULL AUTO_INCREMENT,PRIMARY KEY (id), UNIQUE INDEX modulo (modulo)) COLLATE='latin1_swedish_ci' ENGINE=MyISAM;";
		$this->db->query($mSQL);

		// Crea la entrada en la tabla modulos
		$campos = $this->db->list_fields('modulos');
		if (!in_array('id',$campos)){
			$this->db->query('ALTER TABLE modulos DROP PRIMARY KEY');
			$this->db->query('ALTER TABLE modulos ADD UNIQUE INDEX modulo (modulo)');
			$this->db->query('ALTER TABLE modulos ADD COLUMN id INT(11) NULL AUTO_INCREMENT, ADD PRIMARY KEY (id)');
		}

		$mSQL = "
		INSERT IGNORE INTO modulos (modulo, nombre) VALUES
		('PERS',      'Trabajadores'),
		('SCST',      'Compras de Productos'),
		('SPRV',      'Proveedores'),
		('APAN',      'Aplicacion anticipos'),
		('ARAN',      'Aranceles'),
		('ASIG',      'Asignaciones de Nomina'),
		('AUSU',      'Aumento de Sueldos'),
		('BANC',      'Caja y Bancos'),
		('BCAJ',      'Depositos de caja a bancos'),
		('BCONCI',    'Conciliacion Bancaria'),
		('BMOV',      'Movimiento de Bancos'),
		('BOTR',      'Otros Conceptos de Ingresos y Gastos'),
		('CAJA',      'Parametrizacion de Cajas'),
		('CARG',      'Cargos de trabajadores'),
		('CASI',      'Asientos Contables'),
		('CAUB',      'Almacenes.'),
		('CHOFER',    'Choferes'),
		('CIVA',      'Libros Contables'),
		('CLUB',      'Club de clientes'),
		('CONC',      'Conceptos de Nomina'),
		('CONV',      'Conversion de inventario'),
		('CPLA',      'Plan de Cuentas'),
		('CRUC',      'Cruce de cuentas'),
		('DEPA',      'Departamentos de Nomina'),
		('DIVI',      'Divisiones de Nomina'),
		('DPTO',      'Departamentos de Inventario'),
		('EDGASTO',   'Gastos de Inmobiliaria'),
		('EDINMUE',   'Inmuebles'),
		('EDREC',     'Recibos de cobro'),
		('FLOTA',     'Flota de Vehiculos'),
		('GRCL',      'Grupos de Clientes.'),
		('GRGA',      'Grupos de Gastos.'),
		('GRPR',      'Grupos de Proveedores.'),
		('GRUP',      'Grupos de Inventario.'),
		('GSER',      'Gastos y servicios'),
		('GSERCHI',   'Caja chica'),
		('ICON',      'Conceptos de Ajustes'),
		('INVRESU',   'Libro de Inventario'),
		('LINE',      'Lineas de Inventario.'),
		('MAES',      'Inventario supermercado'),
		('MGAS',      'Maestro de Gastos y Servicios.'),
		('MODPOS',    'POS'),
		('MVCERTI',   'Certificados MV'),
		('NOCO',      'Contratos de Nomina'),
		('NOMI',      'Historico de Nomina'),
		('NOTABU',    'Tabla de Prestaciones'),
		('ORDC',      'Pedido a Proveedores'),
		('ORDS',      'Ordenes de servicio.'),
		('OTIN',      'Otros Ingresos y Notas de Debito Cliente'),
		('PROASISTE', 'Asistencia a Promociones'),
		('PFAC',      'Pedidos de los Clientes.'),
		('PRES',      'Prestamos de Nomina'),
		('PRETAB',    'Prenomina'),
		('PRMO',      'Otros Movimientos de Caja y Bancos'),
		('PROVOCA',   'Proveedores ocacionales'),
		('REPARTO',   'Control de Reparto'),
		('RETE',      'Retenciones de I.S.L.R.'),
		('RIVA',      'Retenciones de IVA a pagos'),
		('RIVC',      'Retenciones de IVA de clientes'),
		('SCAJ',      'Cajeros.'),
		('SCLI',      'Clientes'),
		('SCON',      'Consignaciones'),
		('SFAC',      'Facturacion.'),
		('SINV',      'Inventario y Servicios'),
		('SINVEC',    'Estructura de Costos'),
		('SIVA',      'Movimientos de proveedores.'),
		('SMOV',      'Movimientos de clientes.'),
		('SNOT',      'Notas de despacho'),
		('SNTE',      'Notas de entrega'),
		('SPRE',      'Presupuesto.'),
		('SPREML',    'Presupuesto ML'),
		('SPRM',      'Movimientos de proveedores.'),
		('SSAL',      'Ajustes de Inventario'),
		('STRA',      'Transferencia de Inventarios'),
		('TARDET',    'Detalle de forma de pago'),
		('TARJETA',   'Formas de Pago.'),
		('TBAN',      'Instituciones Bancarias'),
		('TMENUS',    'Menus de Datasis'),
		('TMGA',      'Movimiento de inventarios, activos y suministros'),
		('USERS',     'Usuarios'),
		('USOL',      'Transferencia de Inventarios entre Almacenes'),
		('UTRIBUTA',  'Unidades tributarias'),
		('VEND',      'Vendedores.'),
		('ZONA',      'Zonas de Clientes'),
		('PROPARTI',  'Participantes en Promociones');";
		$this->db->query($mSQL);

	}

	function usuarios(){
		$mSQL = "SELECT * FROM usuario ORDER BY us_nombre";
		$query = $this->db->query($mSQL);
		$results = $query->num_rows();
		$arr = $this->datasis->codificautf8($query->result_array());
		echo '{success:true, message:"Loaded data" ,results:'. $results.', data:'.json_encode($arr).'}';
	}

}
