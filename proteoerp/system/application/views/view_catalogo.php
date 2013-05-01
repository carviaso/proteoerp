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
			var form="";
			
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
			
					
			$(document).ready(function()
			{
				
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
			<table align="center" border="0px" cellpadding="0px" cellspacing="0p" height="26px">
				<tr>
					<td align="center" height="26px">
						<div id="verfiltro" class="verfiltro" >
							Opciones de Busqueda				
						</div>
					</td>
				</tr>
			</table>
			
			
			<table align="center">
				<tr>
					<td valign="center" align="center">
						<div id="articulos" class="articulos">							
						</div>
					</td>
				</tr>
			</table>
			
			<div id="html" class="html">
			</div>
			
			<div id="portada" name="portada" class="portada">
				<table align="center" width="800" border="0" cellspacing="0" cellpadding="0">
				  <tr>
				    <td colspan="3"><div align="center"><img src="http://www.materialeslosandes.com/logo_home.jpg" width="233" height="56" /></div></td>
				  </tr>
				  <tr>
				    <td width="223"><div align="center" class="Estilo1">Oferta Navide&ntilde;a </div></td>
				    <td width="243">&nbsp;</td>
				    <td width="334"><span class="Estilo2">para los problemas de electricidad </span></td>
				  </tr>
				  <tr>
				    <td><a class="portadaa" href="ANEMT01"><img src="http://www.uni-her.com/imagenes/tarro-verde.jpg" alt="a" width="94" height="61" /></a>pintura montana </td>
				    
				    <td><img src="http://grupoeinsa.com/images/Planta_Electrica.jpg" width="164" height="113" />tenemos la mejoras marcas y modelos </td>
				  </tr>
				  <tr>
				    <td colspan="3"><div align="center" class="Estilo3">
				      <p>para que no se quede sin agua con que hacer las hallacas esta navidad </p>
				      <p><img src="http://www.tankes.com.uy/tanques/1000-lt.gif" alt="a" width="61" height="95" /><img src="http://www.tankes.com.uy/tanques/200-tanques.gif" alt="d" width="92" height="82" /></p>
				    </div></td>
				  </tr>
				</table>
			</div>
			
			
			<table align="center">
				<tr>
					<td>
						<div id="html2" name="html2"></div>
					</td>
				</tr>
			</table>
			
			<table>
				<tr>
					<td>
						&nbsp;
					</td>
				</tr>
				<tr>
					<td>
						&nbsp;
					</td>
				</tr>
			</table>
			
			<table align="center" border="0px" cellpadding="0px" cellspacing="0p" height="26px">
				<tr>
					<td align="center">
						
					</td>
				</tr>
			</table>
			
			
				
			
			<div id="menuf" name="menuf" class="menuf">
				<table align="center" cellpadding="0" cellspacing="0">
					<tr>
						<td>ir a
							<a href="javascript:bportada();" id="bportada"> Portada </a>		
						</td>
						<td>
							<a href="javascript:barticulos();" id="barticulos"> Catalogo </a>		
						</td>
						<td>
							&nbsp;
						</td>
						<td>
							<div id="navega" name="navega" class="navega">
							</div>
						</td>
						<td>
							&nbsp;
						</td>	
						<td>
							<form id="foo" name="foo" action="<? echo $site_url ?>inventario/catalogover/descargar" target="_blank" method="post" enctype="multipart/form-data" >
								<input type="hidden" name="contenido" id="contenido">
								<input type="hidden" name="foot" id="foot">
								<input type="submit" name="submit" id="submit" value="imprimir" value="imprimir">
							</form>
						</td>
					</tr>
				</table>
			</div>
			<div id="footer"  class="footer">
				<div id="f" class="f">
					<table align="center" cellpadding="0" cellspacing="0">
					  <tr>
					    <td width="100px" rowspan="3"><img width="100px" src="<?php echo $this->_direccion.'/images/hiperdata.png'; ?>"> </td>
					    <td><strong>HiperData C.A</strong></td>
					    <td width="100px" rowspan="3"><img width="100px" src="<?php echo $this->_direccion.'/images/proteo.png'; ?>"> </td>
					  </tr>
					  <tr>
					    <td>Sistemas Administrativos y Contables, Impresoras fiscales, Todo en computaci&oacute;n y Redes. Desarrollo de Software</td>
					  </tr>
					  <tr>
					    <td>Av.Andres Bello, CC Alto Chama, piso 1, Local 213. Telf: 58 (0274) 271.19.22 M&eacute;rida - Venezuela</td>
					  </tr>
					</table>
				</div>
			</div>
		</body>
</html>