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
<?php echo style('menutab.css');  ?>
<?php echo style('acordeon.css'); ?>
<?php echo style('masonry.css'); ?>
<?php echo style('estilos.css');  ?>

<?php echo style("themes/proteo/proteo.css"); ?>

<?php echo script('jquery-min.js'); ?>
<?php echo script('jquery-migrate-min.js'); ?>
<?php echo script('jquery-ui.custom.min.js'); ?>
<?php echo script('plugins/myAccordion.js'); ?>
<?php echo script('jquery.layout.js'); ?>
<?php echo script('plugins/jquery.masonry.min.js'); ?>
<?php echo script("jquery.dialogextend.min.js"); ?>

<?php echo "\n<!-- Impromptu -->\n"; ?>
<?php echo script('jquery-impromptu.js'); ?>
<?php echo style('impromptu/default.css'); ?>

<style>
.ui-dialog .ui-dialog-titlebar{
	height: 16px;
	font-size:0.8em;
}
<?php $this->load->view('loadstyle'); ?>
</style>

<script type="text/javascript" charset="<?php echo $this->config->item('charset'); ?>">

	// set EVERY 'state' here so will undo ALL layout changes
	// used by the 'Reset State' button: myLayout.loadState( stateResetSettings )
	var stateResetSettings = {
		north__size:		"auto"
	,	north__initClosed:	false
	,	north__initHidden:	false
	,	south__size:		"auto"
	,	south__initClosed:	false
	,	south__initHidden:	false
	,	west__size:			180
	,	west__initClosed:	false
	,	west__initHidden:	false
	,	east__size:			260
	,	east__initClosed:	false
	,	east__initHidden:	false
	};

	var myLayout;

$(document).ready(function() {

	$("#accordion").myAccordion({
		speed: "fast", 
		defautContent:0
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

	myLayout = $('body').layout({

	//	reference only - these options are NOT required because 'true' is the default
		closable:					true	// pane can open & close
	,	resizable:					true	// when open, pane can be resized 
	,	slidable:					true	// when closed, pane can 'slide' open over other panes - closes on mouse-out
	,	livePaneResizing:			true

	//	some resizing/toggling settings
	,	north__slidable:			false	// OVERRIDE the pane-default of 'slidable=true'
	,	north__togglerLength_closed: '100%'	// toggle-button is full-width of resizer-bar
	,	north__spacing_closed:		20		// big resizer-bar when open (zero height)
	,	south__resizable:			false	// OVERRIDE the pane-default of 'resizable=true'
	,	south__spacing_open:		0		// no resizer-bar when open (zero height)
	,	south__spacing_closed:		20		// big resizer-bar when open (zero height)

	//	some pane-size settings
	,	west__minSize:				100
	,	west__size:					180
	,	east__size:					260
	,	east__minSize:				260
	,	east__maxSize:				.5 // 50% of layout width
	,	center__minWidth:			100

	//	some pane animation settings
	,	west__animatePaneSizing:	false
	,	west__fxSpeed_size:			"fast"	// 'fast' animation when resizing west-pane
	,	west__fxSpeed_open:			1000	// 1-second animation when opening west-pane
	,	west__fxSettings_open:		{ easing: "easeOutBounce" } // 'bounce' effect when opening
	,	west__fxName_close:			"none"	// NO animation when closing west-pane

	//	enable showOverflow on west-pane so CSS popups will overlap north pane
	,	west__showOverflowOnHover:	true

	//	enable state management
	//,	stateManagement__enabled:	true // automatic cookie load & save enabled by default
	,	showDebugMessages:			true // log and/or display messages from debugging & testing code
	});

});

<?php $this->load->view('loadscript'); ?>

</script>
</head>
<body>
<!-- manually attach allowOverflow method to pane -->
<div class="ui-layout-north" onmouseover="myLayout.allowOverflow('north')" onmouseout="myLayout.resetOverflow(this)">
	<div id='header'>
		<?php $this->load->view($data['settings']['default'].'/'.$data['settings']['commons']."header", $data); ?>
	</div>
</div>

<!-- allowOverflow auto-attached by option: west__showOverflowOnHover = true -->
<div class="ui-layout-west">
	<div id='aizquierdo'>
	<?php //Acordeon 
		echo $smenu;
	?>
	</div>
</div>

<div class="ui-layout-south">
<div id='footer'>
	<table width='100%'><tr>
	<td><div id='izquierdo'>
<?php 
	echo "Conexion: <b>".strtoupper($this->db->database)."</b>\n";
	if(isset($_SERVER['REMOTE_ADDR'])){
		echo "IP: <b>".$_SERVER['REMOTE_ADDR']."</b>\n";
	}
?>
	</div></td>


	<td><div id='centro'>
<?php 
	echo 	image('codeigniter.gif').
			image('php-power-micro.png').
			image('jquery-icon.png').
			image('mysqlpowered.png').
			image('buttongnugpl.png'); 
?>
	</div></td>
	<td>
		Version: <b><?php echo $this->datasis->traevalor('SVNVER','Version svn de proteo'); ?></b>
	</td>
	<td>
		<div id='derecho'>
<?php
		if ( $this->secu->es_logeado() ){
			echo "<div id='cambioclave' onclick='camclave()' >Cambio de Clave</div>";
		}
?>
	</div></td>

	</tr></table>
</div>
</div>

<div class="ui-layout-east">
<aside id='aderecha'>
	<?php
		if ( $this->secu->es_logeado() )
			$this->load->view('chat/chat');
		if ( $this->secu->es_logeado() )
			$this->load->view('ultlog');
	?>
</aside>
</div>
<div class="ui-layout-center">
<section id='acentro'>
	<?php $this->load->view($data['settings']['default'].'/'.$data['settings']['content'].$view,  $data); ?>
</section>
</div>
</body>
</html>
