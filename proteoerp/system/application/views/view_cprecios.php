<?php
/**
 * ProteoERP
 *
 * @autor    Andres Hocevar
 * @license  GNU GPL v3
*/
$format ='CIPRECIOS';
$jqtheme='le-frog';
?><!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" >
		<?php echo script('jquery.js');    ?>
		<?php echo script('jquery-ui.js'); ?>
		<?php echo style("$jqtheme/jquery-ui-1.7.2.custom.css"); ?>
		<title>Consulta de precios</title>
		<script type="text/javascript">
		var c='';
		var propa=false;
		$(document).ready(function() {
			$(document).keydown(function(e){
				if (32 <= e.which && e.which <= 176) {
				  c = c+String.fromCharCode(e.which);
				} else if (e.which == 13) {
					$.ajax({
						type: "POST",
						url: "<?php echo site_url("inventario/consultas/ssprecios/$format") ?>",
						data: {'barras':c},
						success: function(msg){
							$("#ent").html(msg);
							$("#ent").fadeIn("slow");

							if (propa!==false)
								clearTimeout(propa);
							propa=setTimeout(function() { $("#ent").fadeOut("slow"); },5000);
						}
					});
					c='';
				}
				return false;
			});
			$("#cpropa").ajaxError(function() {
				setTimeout('cargapub()',10000);
			});
			$("#_ppro").error(function() {
				alert('Handler for .error() called.')
			});

			$("#ent").hide();
			cargapub();
		});

		function cargapub(){
			actual=$("#_ppro").attr('src');
			if(actual==undefined)
				actual='';
			else
				actual=basename(actual);

			$("#cpropa").hide();
			$("#cpropa").load("<?php echo site_url("supervisor/publicidad/obtener") ?>"+'/'+actual,function(response, status, xhr){
				$("#_ppro").bind('load', function() {
					$("#cpropa").slideDown("slow");
					setTimeout('cargapub()',10000);
				});
			});
		}

		function basename(path) {
				var b = path.replace(/^.*[\/\\]/g, '');
				return b;
		}
		</script>
		<style type="text/css">
			.bgclass {background: #FFFFFF url(<?php echo $conf['styles']; ?>le-frog/images/ui-bg_diagonals-thick_15_444444_40x40.png) 50% 50% repeat;height:100%; width:100%;}
			body{ font: 100% "Trebuchet MS", sans-serif; margin: 50px; }
			h1  {color:#ffffff; }
		</style>
	</head>
	<body background='<?php echo $conf['styles']; ?>le-frog/images/ui-bg_diagonals-thick_15_444444_40x40.png'>

			<h1 class="demoHeaders">Consulta de precios</h1>
				<p style="text-align:center;" id='cpropa'>
					<span id='_ppro'>Cargando Publicidad...</span>
				</p>

			<div style="position: absolute; width: 70%; height: 60%; left: 15%; top: 20%; padding: 10px;" class="ui-widget ui-widget-content ui-corner-all" id='ent'>
				<div class="ui-dialog-content ui-widget-content" style="background: none; border: 0;">
					Consulta de precios
				</div>
			</div>
	</body>
</html>
