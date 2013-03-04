<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php // <!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> ?>
<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?=$this->config->item('charset'); ?>" />
<meta http-equiv="Content-Language" content="en-us" />
<meta name="ROBOTS" content="NONE" />
<meta name="MSSmartTagsPreventParsing" content="true" />
<meta name="Keywords"                  content="<?php echo property('app_keywords');   ?>" />
<meta name="Description"               content="<?php echo property('app_description');?>" />
<meta name="Copyright"                 content="<?php echo property('app_copyright');  ?>" />
<title><?php echo property('app_title')?></title>
<?php echo style("estilos.css");  ?>
<?php echo style("menutab.css");  ?>
<?php echo style("acordeon.css"); ?>
<?php echo style("masonry.css"); ?>

<?php echo script("jquery-min.js"); ?>
<?php echo script("jquery-ui.custom.min.js"); ?>
<?php echo script("plugins/myAccordion.js"); ?>
<?php //echo script("plugins/interface.js"); ?>
<?php echo script("plugins/jquery.masonry.min.js"); ?>

<script type="text/javascript" charset="<?php echo $this->config->item('charset'); ?>">
$(document).ready(function() {
	$("#accordion").myAccordion({
		speed: "fast",   // @param : low, medium, fast
		defautContent: 0 // @param : number
	});
	
	$("a[name='_mp']").click(function () {
		$("a[name='_mp']").removeClass("current");
		url=this.href;
		pos=url.lastIndexOf('/');
		carga=url.substring(pos);
		$('#accordion').load('<?php echo site_url('bienvenido/accordion'); ?>'+carga,'',function() {
			$('#accordion').myAccordion({ speed: 'fast', defautContent: 0 });
		});
		$(this).addClass('current');
		$('#tumblelog').load('<?php echo site_url('bienvenido/cargapanel'); ?>'+carga,'' ,function(){
			$('#maso').masonry({ 
				singleMode: true,
				itemSelector: '.box'
			});
		});
		return false;
	});
});
</script>

</head>
<body>
	<div id="container">
	<?php $this->load->view($data['settings']['default'].'/'.$data['settings']['commons']."header", $data); ?>
		<table border='0' cellpadding='0' cellspacing='0' width='100%'>
			<tr>
				<td  valign='top' id='tablemenu'>
					<div id='micelanias'>
					<?php echo $smenu ?>
					</div>
				</td>
				<td valign='top'>
					<?php $this->load->view($data['settings']['default'].'/'.$data['settings']['content'].$view,  $data); ?>
				</td>
				<td valign='top' width='300' align='right'>
				<?php
					if ( $this->secu->es_logeado() )
						$this->load->view('chat/chat'); 
				?>
				</td>
			</tr>
		</table>
		<br>
		<div id="pie">
		<table class='pie' width="100%" border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td width='178px' valign='top'>
					<div><p style="font-size:10px"><?php 
						echo "Conectado a: ".$this->db->database; 
						if(isset($_SERVER['REMOTE_ADDR'])){
							echo br().'Tu ip: '.$_SERVER['REMOTE_ADDR'];
						}
					?></p></div>
				</td>
				<td align='center'>
					<p style="font-size:8px"><?php echo $copyright; ?></p>
					<?php echo image('codeigniter.gif');     ?>
					<?php echo image('php-power-micro.png'); ?>
					<?php echo image('jquery-icon.png');     ?>
					<?php echo image('mysqlpowered.png');    ?>
					<?php echo image('buttongnugpl.png');    ?>
				</td>
				<td width='178px'>
					<div>&nbsp;</div>
				</td>
			</tr>
		</table>
		</div>
	</div>
</body>
</html>
