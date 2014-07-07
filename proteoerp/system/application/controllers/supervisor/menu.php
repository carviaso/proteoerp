<?php
class Menu extends Controller{
	var $niveles;

	function Menu(){
		parent::Controller();
		$this->load->library("rapyd");
		$this->niveles=$this->config->item('niveles_menu');
	}

	function index(){
		$this->datasis->modulo_id(905);
		$this->datasis->targintramenu( 'ajax', 'supervisor/menu' );
		
		if ($this->uri->segment(3) === FALSE) $mod = FALSE; else $mod = $this->uri->segment(3);
		$mSQL='SELECT modulo, titulo FROM intramenu AS a ORDER BY modulo';

		$prop=array('border'=>'0','height'=>'14px');

		$out = '
<div id="arbolito" style="overflow:auto;border:1px solid #9AC8DA;background: #FAFAFA;height:400px;width:320px;font-size:12px;float:left;">
	<div id="sidetreecontrol">
	<table style=""><tr>
		<td><a name="botones" href="?#">Contraer </a></td> 
		<td><a name="botones" href="?#">Expandir </a></td>
		<td><a name="botones" href="?#">Invertir </a></td>
	</tr></table>
	</div>
	<div id="arbol">
	';

		if( strlen($mod)>1) $esde=$mod[0]; else $esde=$mod;

		$out .= "\t".'<a href="#" onclick="creanuevo(\'m\');">'.image('list-add.png','Agregar opcion',$prop).'</a><b>Men&uacute;</b>'."\n";
		$out .= "\t\t".'<ul id="tree">'."\n";
		$out .= "\t\t\t<li>\n";
		$out .= "\t\t\t\t".'<a href="'.site_url('supervisor/menu/dataedit/0/create/').'" >'.image('list-add.png','Agregar',$prop).'</a>0-Libres'."\n";
		$n=1;

		$mc  = $this->db->query($mSQL);
		foreach( $mc->result() as $row ){
			if(strlen($row->modulo)==1){
				if($n==2){
					$out .= "\t\t\t\t\t</li>\n";
					$out .= "\t\t\t\t</ul>\n";
					$out .= "\t\t\t</li>\n";
				}elseif($n==1){
					$out .= "\n\t\t\t</li>\n";
				}elseif($n==3){
					$out .= "\t\t\t</ul>\n";
					$out .= "\t\t\t</li>\n";
					$out .= "\t\t\t</ul>\n";
				}
				$n=1;
				$out .= "\t\t\t<li>\n";

				$out .= "\t\t\t\t";
				$out .= '<a href="#" onclick="modifica(\''.$row->modulo.'\') " >'.image('editor.png',     'Editar',  $prop).'</a>';
				$out .= '<a href="#" onclick="creanuevo(\''.$row->modulo.'\')" >'.image('list-add.png',   'Agregar', $prop).'</a>';
				$out .= '<a href="#" onclick="elimina(\''.$row->modulo.'\')  " >'.image('list-remove.png','Eliminar',$prop).'</a>';
				$out .= $row->modulo.'-'.$row->titulo."\n";

			//nivel 2
			}elseif(strlen($row->modulo)==3){
				if($n==1){
					$out .= "\t\t\t\t<ul>\n";
				}elseif($n==2){
					$out .= "\t\t\t\t\t</li>\n";
				}elseif($n==3){
					$out .= "\t\t\t\t\t\t</ul>\n";
					$out .= "\t\t\t\t\t</li>\n";
				}
				$n=2;
				$out .= "\t\t\t\t\t<li>\n";

				$out .= "\t\t\t\t\t\t";

				$out .= '<a href="#" onclick="modifica(\''.$row->modulo.'\') " >'.image('editor.png',     'Editar',  $prop).'</a>';
				$out .= '<a href="#" onclick="creanuevo(\''.$row->modulo.'\')" >'.image('list-add.png',   'Agregar', $prop).'</a>';
				$out .= '<a href="#" onclick="elimina(\''.$row->modulo.'\')  " >'.image('list-remove.png','Eliminar',$prop).'</a>';


				$out .= $row->modulo.'-'.$row->titulo."\n";

			//nivel 3
			}else{
				if($n==2){
					$out .= "\t\t\t\t\t\t<ul>\n";
				}
				$n=3;
				$out .= "\t\t\t\t\t\t\t<li>\n";

				$out .= "\t\t\t\t\t\t\t\t";

				$out .= '<a href="#" onclick="modifica(\''.$row->modulo.'\') " >'.image('editor.png',     'Editar',  $prop).'</a>';
				$out .= '<a href="#" onclick="elimina(\''.$row->modulo.'\')  " >'.image('list-remove.png','Eliminar',$prop).'</a>';

				$out .= $row->modulo.'-'.$row->titulo."\n";
				$out .= "\t\t\t\t\t\t\t</li>\n";
			}
		}
		if( $n == 1 ){
			$out .= "</li>\n";
		}elseif( $n == 2 ){
			$out .= "\t\t\t\t\t</li>\n";
			$out .= "\t\t\t\t</ul>\n";
			$out .= "\t\t\t</li>\n";
		}elseif( $n == 3 ){
			$out .= "\t\t\t\t\t</ul>\n";
			$out .= "</li>\n";
		}
		$out .= "\t\t</ul>\n";
		$out .= "\t</div>\n";
		$out .= "</div>\n";

		$script  ='<script type="text/javascript">';
		$script .='
		$(function() {
			$.post("'.site_url('supervisor/menu/arbolito').'", function(data){
				$("#arbolito").html(data);
			});
		})';

		$script .='
		function creanuevo(modulo){
			$.post("'.site_url('supervisor/menu/dataedit').'/"+modulo+"/create", function(data){
				$("#dedita").html(data+\'<button onclick="guardanv()">Guardar</button>\');
			});
		}';

		$script .='
		function actuarbol(){
			$.post("'.site_url('supervisor/menu/arbolito').'", function(data){
				$("#arbolito").html(data);
			});
		}';

		$script .='
		function guardanv(){
				var murl = $("#df1").attr("action");
				$.ajax({
					type: "POST", dataType: "html", async: false,
					url: murl,
					data: $("#df1").serialize(),
					success: function(r,s,x){
						try{
							var json = JSON.parse(r);
							if (json.status == "A"){
								alert("Registro Guardado");
								$( "#dedita" ).html( "Registro Guardado" );
								if (json.tipo != "update"){
									actuarbol();
								}
								return true;
							}else{
								alert(json.mensaje);
							}
						}catch(e){
							//$("#dedita").html(r);
						}
					}
				})
		}';

		$script .='
		function modifica(modulo){
			$.post("'.site_url('supervisor/menu/dataedit/modify').'/"+modulo, function(data){
				$("#dedita").html(data+\'<button onclick="guardanv()">Guardar</button>\');
			});
		}';

		$script .='
		function elimina(modulo){
			if(confirm("Eliminar "+modulo+"?")){
				$.post("'.site_url("supervisor/menu/dataedit/do_delete/").'/"+modulo, 
					function(data){
						try {
							var json = JSON.parse(data);
							if (json.status == "A"){
								actuarbol();
								alert("Registro Eliminado");
							} else {
								alert("No se pudo eliminar el Registro");
							} 
						} catch(e){
							alert("Hola");
						}
					}
				)
			}
		}';

		$script .="\n".'</script>'."\n";
		$out = '<div id="arbolito" style="overflow:auto;border:1px solid #9AC8DA;background: #FAFAFA;height:400px;width:320px;font-size:12px;float:left;"></div>';
		$contenido = $out.'<div id="dedita" style="overflow:auto;border:1px solid #9AC8DA;background:#FAFAFA;width:460px;font-size:12px;float:left;"></div>';


		$script   .= script("jquery.treeview.pack.js");
		$style    = style('jquery.treeview.css');

		//$data['title']   = '<h1>Administraci&oacute;n del Men&uacute;</h1>';
		echo $script.$style.$contenido;

		
	}

	//******************************************************************
	//
	//
	function arbolito(){
		$prop=array('border'=>'0','height'=>'14px');
		$mSQL='SELECT modulo,titulo  FROM intramenu AS a ORDER BY modulo';

		$script  ='<script type="text/javascript">';
		
		$script .='
		$(function() {
			$("#tree").treeview({
				collapsed: true,
				animated: "medium",
				control:"#sidetreecontrol",
				persist: "location"
			});
		})';

		$script .="</script>\n";

		$out = '
	<div id="sidetreecontrol">
	<table style=""><tr>
		<td><a name="botones" href="?#">Contraer </a></td> 
		<td><a name="botones" href="?#">Expandir </a></td>
		<td><a name="botones" href="?#">Invertir </a></td>
	</tr></table>
	</div>
	<div id="arbol">
	';

		//if( strlen($mod)>1) $esde=$mod[0]; else $esde=$mod;

		$out .= "\t".'<a href="#" onclick="creanuevo(\'m\');">'.image('list-add.png','Agregar opcion',$prop).'</a><b>Men&uacute;</b>'."\n";
		$out .= "\t\t".'<ul id="tree">'."\n";
		$out .= "\t\t\t<li>\n";
		$out .= "\t\t\t\t".'<a href="'.site_url('supervisor/menu/dataedit/0/create/').'" >'.image('list-add.png','Agregar',$prop).'</a>0-Libres'."\n";
		$n=1;

		$mc  = $this->db->query($mSQL);
		foreach( $mc->result() as $row ){
			if(strlen($row->modulo)==1){
				if($n==2){
					$out .= "\t\t\t\t\t</li>\n";
					$out .= "\t\t\t\t</ul>\n";
					$out .= "\t\t\t</li>\n";
				}elseif($n==1){
					$out .= "\n\t\t\t</li>\n";
				}elseif($n==3){
					$out .= "\t\t\t</ul>\n";
					$out .= "\t\t\t</li>\n";
					$out .= "\t\t\t</ul>\n";
				}
				$n=1;
				$out .= "\t\t\t<li>\n";

				$out .= "\t\t\t\t";
				$out .= '<a href="#" onclick="modifica(\''.$row->modulo.'\') " >'.image('editor.png',     'Editar',  $prop).'</a>';
				$out .= '<a href="#" onclick="creanuevo(\''.$row->modulo.'\')" >'.image('list-add.png',   'Agregar', $prop).'</a>';
				$out .= '<a href="#" onclick="elimina(\''.$row->modulo.'\')  " >'.image('list-remove.png','Eliminar',$prop).'</a>';
				$out .= $row->modulo.'-'.$row->titulo."\n";

			//nivel 2
			}elseif(strlen($row->modulo)==3){
				if($n==1){
					$out .= "\t\t\t\t<ul>\n";
				}elseif($n==2){
					$out .= "\t\t\t\t\t</li>\n";
				}elseif($n==3){
					$out .= "\t\t\t\t\t\t</ul>\n";
					$out .= "\t\t\t\t\t</li>\n";
				}
				$n=2;
				$out .= "\t\t\t\t\t<li>\n";
				$out .= "\t\t\t\t\t\t";
				$out .= '<a href="#" onclick="modifica(\''.$row->modulo.'\') " >'.image('editor.png',     'Editar',  $prop).'</a>';
				$out .= '<a href="#" onclick="creanuevo(\''.$row->modulo.'\')" >'.image('list-add.png',   'Agregar', $prop).'</a>';
				$out .= '<a href="#" onclick="elimina(\''.$row->modulo.'\')  " >'.image('list-remove.png','Eliminar',$prop).'</a>';
				$out .= $row->modulo.'-'.$row->titulo."\n";

			//nivel 3
			}else{
				if($n==2){
					$out .= "\t\t\t\t\t\t<ul>\n";
				}
				$n=3;
				$out .= "\t\t\t\t\t\t\t<li>\n";
				$out .= "\t\t\t\t\t\t\t\t";
				$out .= '<a href="#" onclick="modifica(\''.$row->modulo.'\') " >'.image('editor.png',     'Editar',  $prop).'</a>';
				$out .= '<a href="#" onclick="elimina(\''.$row->modulo.'\')  " >'.image('list-remove.png','Eliminar',$prop).'</a>';
				$out .= $row->modulo.'-'.$row->titulo."\n";
				$out .= "\t\t\t\t\t\t\t</li>\n";
			}
		}
		if( $n == 1 ){
			$out .= "</li>\n";
		}elseif( $n == 2 ){
			$out .= "\t\t\t\t\t</li>\n";
			$out .= "\t\t\t\t</ul>\n";
			$out .= "\t\t\t</li>\n";
		}elseif( $n == 3 ){
			$out .= "\t\t\t\t\t</ul>\n";
			$out .= "</li>\n";
		}
		$out .= "\t\t</ul>\n";
		$out .= "\t</div>\n";


		echo $script.$out;
		
	}

	//******************************************************************
	// Dataedit
	//
	function dataedit($pertenece){
		$this->rapyd->load('dataedit');

		$edit = new DataEdit('', 'intramenu');
		$edit->on_save_redirect=false;
		//$edit->back_url = site_url('supervisor/menu/index');

		$edit->pre_process('insert' ,'_pre_insert');
		$edit->post_process('delete','_pos_del');
		$edit->post_process('insert','_pos_insert');

		if ($pertenece!='m'){
			$edit->pertenece = new inputField2('Deriva de', 'pertenece');
			$edit->pertenece->mode = 'autohide';
			$edit->pertenece->size = 15;
			$edit->pertenece->readonly=TRUE;
			$edit->pertenece->insertValue=$pertenece;
			$edit->pertenece->when = array('create');
		}

		$edit->titulo = new inputField('Titulo', 'titulo');
		$edit->titulo->rule = 'required';
		$edit->titulo->size = 45;

		$edit->mensaje = new inputField("Mensaje", "mensaje");
		$edit->mensaje->size = 45;

		$edit->panel = new inputField("Panel", "panel");
		$edit->panel->size = 45;

		$edit->target= new dropdownField("Objetivo", "target");
		$edit->target->option("tab",    "Abre en un Tab");
		$edit->target->option("popu",   "Link en Popup");
		$edit->target->option("self",   "Link en ventana actual");
		$edit->target->option("javascript","Proceso Javascript"); 
		$edit->target->option("dialogo","Abre en un Dialogo"); 
		$edit->target->option("ajax",   "Abre en un Ajax"); 
		$edit->target->style = 'width:170px';

		$edit->ejecutar = new inputField("Ejecutar", "ejecutar");
		$edit->ejecutar->rule='callback_ejecutar';
		$edit->ejecutar->size = 45;

		$edit->visible = new dropdownField("Visible", "visible");
		$edit->visible->option("S","Si");
		$edit->visible->option("N","No");
		$edit->visible->style = 'width:100px';

		$edit->ancho = new inputField("Ancho", "ancho");
		$edit->ancho->insertValue = '800'; 
		$edit->ancho->css_class   = 'inputnum';
		$edit->ancho->rule        = 'numeric';
		$edit->ancho->group       = 'Dimensiones';
		$edit->ancho->size        = 8;

		$edit->alto = new inputField("Alto", "alto");
		$edit->alto->insertValue = '600';
		$edit->alto->css_class   = 'inputnum';
		$edit->alto->rule        = 'numeric';
		$edit->alto->group       = 'Dimensiones';
		$edit->alto->size        = 8;
		
		//$edit->buttons("modify", "save", "undo", "delete", "back");
		$edit->build();

		if($edit->on_success()){
			$rt=array(
				'status' => 'A',
				'mensaje'=> 'Registro guardado',
				'pk'     => $edit->_dataobject->pk,
				'tipo'   => $edit->_action
			);
			echo json_encode($rt);
		}else{
			echo $edit->output;
		}
	}

	// Devuelve codigo disponible
	function _coddisp($mod=0){
		$dec=hexdec($mod);
		$mSQL="SELECT hexa FROM intramenu AS a RIGHT JOIN serie AS b ON a.modulo=b.hexa WHERE modulo IS NULL AND valor>=$dec LIMIT 1";
		$retorna=$this->datasis->dameval($mSQL);
		return $retorna;
	}

	function ejecutar($ejecutar){
		$resul=stripos($ejecutar,"'");
		if($resul===FALSE){
			return TRUE;
		}else{
			$this->validation->set_message('ejecutar', 'El campo ejecutar no puede tener comillas simples, use comillas dobles');
			return FALSE;
		}
	}

	function _pos_del($do) {
		$codigo = trim($do->get('modulo'));
		$sql = "DELETE FROM intrasida WHERE modulo like '$codigo%'";
		$this->db->query($sql);
		$mSQL="DELETE FROM intramenu WHERE modulo like '$codigo%'";
		$this->db->simple_query($mSQL);

	}

	function _pre_insert($do){
		$mod = $do->get('pertenece');
		if($mod=='0'){
			$mSQL="SELECT hexa FROM intramenu AS a RIGHT JOIN serie AS b ON a.modulo=LPAD(b.hexa,3,'0') WHERE modulo IS NULL LIMIT 1";
			$retorna=$this->datasis->dameval($mSQL);
			if(strlen($retorna)>3){
				$do->error_message_ar['pre_ins']='Se ha alcanzado el l&iacute;mite de opciones';
				return false;
			}
			$modulo =str_pad($retorna,3,'0',STR_PAD_LEFT);
		}else{
			$niveles=explode(',',$this->niveles);
			$acu=0;
			foreach ($niveles AS $level){
				if($acu > strlen($mod))
					break;
				$acu+=$level;
			}
			$mod   =str_pad($mod,$acu,'0');
			$modulo=$this->_coddisp($mod);
			if(strlen($modulo)>$acu){
				$do->error_message_ar['pre_ins']="Se ha alcanzado el l&iacute;mite de opciones";
				return false;
			}
		}
		$do->set('modulo', $modulo);
		$this->session->set_userdata('menu_m', $modulo);
		return true;
	}

	function _pos_insert($do){
		$modulo=$this->session->userdata('menu_m');
		$this->session->unset_userdata('menu_m');
		if($modulo[0] != '0'){
			$usuario=$this->session->userdata('usuario');
			$mSQL="INSERT INTO intrasida (usuario,modulo,acceso) VALUES ('$usuario','$modulo','S')";
			$this->db->simple_query($mSQL);
		}
		return true;
	}

	function instalar(){
		$mSQL="ALTER TABLE `intramenu` ADD COLUMN `orden` TINYINT(4) NULL DEFAULT NULL AFTER `pertenece`";
		echo $this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `intramenu` ADD COLUMN `ancho` INT(10) UNSIGNED NULL DEFAULT '800' AFTER `orden`";
		echo $this->db->simple_query($mSQL);
		$mSQL="ALTER TABLE `intramenu` ADD COLUMN `alto`  INT(10) UNSIGNED NULL DEFAULT '600' AFTER `ancho`";
		echo $this->db->simple_query($mSQL);
	}
}
?>
