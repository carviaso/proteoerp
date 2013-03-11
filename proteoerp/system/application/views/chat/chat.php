<?php
	$mSQL    = "SELECT us_codigo, us_nombre FROM usuario WHERE activo='S' ORDER BY us_nombre ";
	$usuario = $this->datasis->llenaopciones($mSQL, true, 'musuario' );
	$usuario = str_replace('Seleccione','Todos',$usuario);
	// crea la tabla si no existe
	if ( !$this->datasis->istabla('chat') ) {
		$mSQL = '
		CREATE TABLE IF NOT EXISTS chat (
			id      INT(11) NOT NULL AUTO_INCREMENT,
			fecha   DATE        NULL DEFAULT NULL,
			hora    TIME        NULL DEFAULT NULL,
			usuario VARCHAR(20) NULL DEFAULT NULL,
			para    VARCHAR(20) NULL DEFAULT NULL,
			mensaje TEXT        NULL,
			PRIMARY KEY (id)
		)
		COMMENT=\'Tabla del Chat\'
		COLLATE=\'latin1_swedish_ci\'
		ENGINE=MyISAM
		AUTO_INCREMENT=14;';
		$this->db->simple_query($mSQL);
	}
?>
<div id="wrapper" style='font:11px arial; color: #222; text-align:center;'>
						<div id="chatmenu"><table width='100%' cellpadding='0' cellspacing='0'><tr><td><div id='status'><?php echo img('images/msg.png');?></div></td><td>&nbsp;&nbsp;<p class="welcome">MENSAJERIA</p></td></tr></table></div>
						<div id='colap'>
							<div id="chatbox"></div>
							<table width='100%'>
								<tr>
									<td colspan='2'><textarea name="usermsg" id="usermsg" rows="1" cols="30"></textarea></td> 
								</tr><tr>
									<td><span style='color:white;'>Para:</span> <?php echo $usuario; ?></td>
									<td><input name="submitmsg" id="submitmsg" type="button" value="Enviar" /></td>
								</tr>
							</table>
						</div>
					</div>
