<?php  if (!defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!doctype html>
<?php //UBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"  "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd"> ?>
<html>
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" />
<meta http-equiv="Content-Language" content="en-us" />
<meta name="ROBOTS" content="NONE" />
<meta name="MSSmartTagsPreventParsing" content="true" />
<meta name="Keywords"                  content="<?php echo property('app_keywords');   ?>" />
<meta name="Description"               content="<?php echo property('app_description');?>" />
<meta name="Copyright"                 content="<?php echo property('app_copyright');  ?>" />
<title><?php echo property('app_title')?></title>
<?php echo style('estilos.css');  ?>
<?php echo style('menutab.css');  ?>
<?php echo style('acordeon.css'); ?>
<?php echo style('masonry.css'); ?>
<?php echo style("themes/proteo/proteo.css"); ?>

<?php echo script('jquery-min.js'); ?>
<?php echo script('jquery-migrate-min.js'); ?>
<?php echo script('jquery-ui.custom.min.js'); ?>
<?php echo script('plugins/myAccordion.js'); ?>
<?php echo script('plugins/jquery.masonry.min.js'); ?>
<?php echo script("jquery.dialogextend.min.js"); ?>

<?php echo "\n<!-- Impromptu -->\n"; ?>
<?php echo script('jquery-impromptu.js'); ?>
<?php echo style('impromptu/default.css'); ?>

<style>
html { height: 100%; }
.ui-dialog .ui-dialog-titlebar{
	height: 18px;
	font-size:0.8em;
}
<?php $this->load->view('loadstyle'); ?>
</style>

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

<?php $this->load->view('loadready'); ?>

});

<?php $this->load->view('loadscript'); ?>

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
				<table cellpadding='0' cellspacing='0' border='0'>
					<tr>
						<td>
						<?php
							if ( $this->secu->es_logeado() )
								$this->load->view('chat/chat');
						?>
						</td>
					</tr>
					<tr>
						<td>
						<?php
							if ( $this->secu->es_logeado() )
								$this->load->view('ultlog');
						?>
						</td>
					</tr>
					<tr>
						<td><div id='menup_repo' name='menup_repo'></div>
						</td>
					</tr>
				</table>
				</td>
			</tr>
		</table>
		<br>
		<div id="pie">
		<table class='pie' width="100%" border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td width='188px' valign='top'>
					<div>
						<table style="font-size:10px;color:white;width:100%;" border='0'>
							<tr>
								<td><?php echo "Conectado a: <b>".strtoupper($this->db->database); ?></b></td>
						<?php
						if(isset($_SERVER['REMOTE_ADDR'])){
							echo "\t\t\t\t\t\t\t\t</tr><tr>\n";
							echo "\t\t\t\t\t\t\t\t\t<td>IP: <b>".$_SERVER['REMOTE_ADDR']."</b></td>\n";
						}

						if ( $this->secu->es_logeado() ){
							echo "\t\t\t\t\t\t\t\t</tr><tr>\n";
							echo "\t\t\t\t\t\t\t\t\t<td><div><b onclick='camclave()' style='cursor:pointer;'>Cambio de Clave</b></div></td>";
						}
					?>
							</tr><tr>
								<td>Build: <?php echo $this->datasis->traevalor('SVNVER','Version svn de proteo'); ?></td>
							</tr>
						</table>
					</div>
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
