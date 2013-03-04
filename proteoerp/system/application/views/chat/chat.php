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
<script type="text/javascript">
$(document).ready(function(){
	var mostrar = 0 ;
	//loadLog();
	setInterval (loadLog, 3000); 
	$('#colap').hide();
	$('#submitmsg').click(function(event){
		event.preventDefault();
		var muser     = $('#musuario').val();
		var clientmsg = $('#usermsg' ).val();
		$.post('<?php echo site_url('chat/chat/agregar');?>', {mensaje: clientmsg, usuario: muser});
		$('#usermsg').val('');
	});
	$("#chatmenu").click( function(){
		if ( mostrar == 0 ){
			$('#colap').show();
			mostrar = 1 ;
			loadLog(1);
		} else {
			$('#colap').hide();
			mostrar = 0 ;
		}
	});
	function loadLog(){
		var oldscrollHeight = $('#chatbox').attr('scrollHeight') - 10;
		if (mostrar == 1) {
			$.ajax({
				type: "POST",
				url: "<?php echo site_url('/chat/chat/actualizar');?>",
				success: function(msg){
					$('#chatbox').html(msg);
					var newscrollHeight = $('#chatbox').attr('scrollHeight') - 10; //La altura del scroll después del pedido
					if(newscrollHeight > oldscrollHeight){
						$('#chatbox').animate({ scrollTop: newscrollHeight }, 'normal'); //Autoscroll hacia el fondo del div
						//$('#pie').append('<h1>jajaja</h1>');
						//alert('aaaa');
					}
				}
			});
		}
	}
});
</script>
<style>
/* CSS Document */
/*body { font:12px arial; color: #222; text-align:center; padding:35px; }*/

form, p, span { margin:0; padding:0; }
input { font:12px arial; }
a { color:#0000FF; text-decoration:none; }
a:hover { text-decoration:underline; }

#wrapper {
	margin:0 auto;
	padding-bottom:5px;
	background:#7E4335;
	width:280px;
	border:1px solid #7E4335; 
}

#chatbox {
	text-align:left;
	margin:0 auto;
	margin-bottom:5px;
	padding:10px;
	background:#fff;
	height:200px;
	width:250px;
	border:1px solid #7E4335;
	overflow:auto; 
}

#colap {
	text-align:left;
	margin:0 auto;
	margin-bottom:0px;
	padding:0,0,0,0;
	background:#fff;
	border:none;
	width:275px;
	overflow:auto; 
	background:#7E4335;
}

#usermsg { width:250px; border:1px solid #ACD8F0; }
#submit { width: 60px; }
#chatmenu { padding:2.5px 25px 4.0px 20px; height:14px; background-color:#7E4335; }
.welcome { float:left; height:20px; font-size:14px;color:white; font-weight:bold; text-align:center }
.logout { float:right; }
.msgln { margin:0 0 2px 0; }
</style>
<div id="wrapper" style='font:11px arial; color: #222; text-align:center;'>
	<div id="chatmenu"><p class="welcome">MENSAJERIA</p></div>
	<div id='colap'>
		<div id="chatbox"></div>
		<table width='100%'>
			<tr>
				<td colspan='2'><textarea name="usermsg" id="usermsg" rows="2" cols="30"></textarea></td> 
			</tr><tr>
				<td><span style='color:white;'>Para:</span> <?php echo $usuario; ?></td>
				<td><input name="submitmsg" id="submitmsg" type="button" value="Enviar" /></td>
			</tr>
		</table>
	</div>
</div>
