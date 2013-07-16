<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
		<?php echo script('jquery.js');    ?>
		<?php echo script('jquery-ui.js'); ?>
		<?php echo script('plugins/jquery.easing.js'); ?>
		<?php echo script('plugins/jquery.fancybox.pack.js'); ?>
		<?php echo style('le-frog/jquery-ui-1.7.2.custom.css'); ?>
		<?php echo style('catalogover.css'); ?>
		<?php echo style('fancybox/jquery.fancybox.css'); ?>

		<title>Catalogo</title>
		<script type="text/javascript">
			var form='';

			function act_catalogo(){

				$(".pagenav a").click(function(){
					link=$(this).attr("href");
					filtro(link);
					return false;
				});
			}

			function filtro(link){

				if((!link)||(link=='aed')){

					link="<?php echo $site_url.'inventario/catalogover/filter'?>";

				}
				$.ajax({
					type: "POST",
					url: link,
					data:"depto="+$("#depto").val()+"&&linea="+$("#linea").val()+"&&grupo="+$("#grupo").val()+"&&descrip="+$("#descrip").val()+"&&titulo="+$("#titulo").val(),
					success: function(msg){
						if(msg){
							$("#html").hide();
							$("#portada").hide();
							$("#html2").hide();

							a=msg.indexOf('<div class="mainbackground"><div class="pagenav">');
							b=msg.indexOf('</div></div>',a);
							c=msg.substring(a,12+b);
							$("#navega").html(c);
							d=msg.substring(0,a);
							e=msg.substring(b,10000);
							$("#articulos").html(d+e);
							$("#articulos").show();
							act_catalogo();

						}
						else{
							$("#articulos").html("");
						}
					}
				});
			}

			function navega(link){
				if((!link)){
					link="<?php echo $site_url.'inventario/catalogover/filter'?>";
				}
				var a;
				$.ajax({
					type: "POST",
					url: link,
					data:"depto="+$("#depto").val()+"&&linea="+$("#linea").val()+"&&grupo="+$("#grupo").val()+"&&descrip="+$("#descrip").val()+"&&titulo="+$("#titulo").val(),
					success: function(msg){
						if(msg){
							a=msg.indexOf('<div class="mainbackground"><div class="pagenav">');
							b=msg.indexOf('</div></div>',a);
							c=msg.substring(a,12+b);
							d="<a href='"+"<?=$site_url.'inventario/catalogover/filter/osp/0'?>"+"'>1</a>";
							c=c.replace('<b>1</b>',d);
							$("#navega").html(c);
							act_catalogo();
						}
						else{

						}
					}
				});
			}

			function bportada(){
				$("#html").hide();
				$("#html2").hide();
				$("#articulos").hide();
				$("#portada").show();
			}

			function barticulos(){
				$("#html").hide();
				$("#html2").hide();
				$("#portada").hide();
				$("#articulos").show();
			}

			function bhtml(){
				$("#html2").hide();
				$("#portada").hide();
				$("#articulos").hide();
				$("#html").show();
			}

			function bhtml2(){
				$("#portada").hide();
				$("#articulos").hide();
				$("#html").hide();
				$("#html2").show();
			}

			function html2(codigo){

				$.ajax({
					type: "POST",
					url: "<?php echo $site_url.'inventario/catalogover/html2/'?>",
					data:"codigo="+codigo,
					success: function(msg){
						if(msg){
							$("#html2").html(msg);
							$("#portada").hide();
							$("#html").hide();
							$("#articulos").hide();
							$("#html2").show();
						}
						else{
							$("#html").html("");
						}
					}
				});

			}

			function html($sinv_id,$nombre,$comentario){
				$.ajax({
					type: "POST",
					url: "<?php echo $site_url.'inventario/catalogover/html/'.$format?>",
					data:"sinv_id="+$sinv_id+"&&nombre="+$nombre+"&&comentario="+$comentario,
					success: function(msg){
						if(msg){
							$("#html").html(msg);
							$("#articulos").hide();
							$("#portada").hide();
							$("#html2").hide();
							$("#html").show();
						}
						else{
							$("#html").html("");
						}
					}
				});
			}

			$(function(){

				$("#verfiltro").click(function(event){
					event.preventDefault();
					$("#filter").slideToggle();
				});

				$("#submit").click(function (){
					$("#contenido").val('<div id="articulos" class="articulos">'+$("#articulos").html()+'</div>');
					$("#foot").val('<div id="footer" class="footer">'+$("#footer").html()+'</div>');
					$("#submit").click();
				});

				$(".portadaa").click(function(){
					link=$(this).attr("href");

					html2(link);
					return false;
				});

			});

			$(document).ready(function(){
				navega();

				$("#articulos").hide();
				$("#html").hide();
				$("#html2").hide();
				$.post("<? echo $site_url.'inventario/common/get_depto' ?>",{ linea:"" },function(data){$("#depto").html(data);})
				$("#depto").change(function(){
					$.post("<? echo $site_url.'inventario/common/get_linea' ?>",{ depto:$(this).val() },function(data){$("#linea").html(data);})
					$.post("<? echo $site_url.'inventario/common/get_grupo' ?>",{ linea:"" },function(data){$("#grupo").html(data);})
				});
				$("#linea").change(function(){
					$.post("<?php echo $site_url.'inventario/common/get_grupo' ?>",{ linea:$(this).val() },function(data){$("#grupo").html(data);})
				});

			});
		</script>
		<style type="text/css">
		</style>
		</head>
		<body background='<?php echo $conf['styles']; ?>le-frog/images/<? echo $img_fondo?>'>
			<div class="filter">
				<form name="filter" id="filter" action="javascript:filtro();">
				<table align="center">
					<tr>
						<td>
							Departamento
						</td>
						<td >
							<select name="depto" id="depto" style="width:190px;" class="select">
							<option value=""></option>
							</select>
						</td>
						<td>
							L&iacute;nea
						</td>
						<td>
							<select name="linea" id="linea" style="width:190px;" class="select">
							<option value=""></option>
							</select>
						</td>
						<td>
							Grupo
						</td>
						<td>
							<select name="grupo" id="grupo" style="width:190px;" class="select">
								<option value=""></option>
							</select>
						</td>
					</tr>
					<tr>
						<td colspan="6" align="center">
							Descripci&oacute;n
							<input type="text" name="descrip" id="descrip" size="25">
							Titulo
							<input type="text" name="titulo" id="titulo" size="25">
							<input type="button" name="busca" id="busca" value="Buscar" onclick="filtro('aed')" />

						</td>
					</tr>
				</table>
				</form>
			</div>
			<div id="borde" class="borde" >
			</div>
		</body>
</html>
