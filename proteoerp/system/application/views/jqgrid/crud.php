<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>

<?php 
if( isset($tema) == false) {
	$tema = 'proteo';
} 
?>

<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title><?php echo $encabeza ?></title>


<!-- ESTILOS -->
<?php
echo style('themes/'.$tema.'/'.$tema.'.css'); 
if ( isset($tema1) ) echo style('themes/'.$tema1.'/'.$tema1.'.css');
if ( isset($anexos) ) echo style('themes/'.$anexos.'/'.$anexos.'.css'); 
?>


<!-- JQUERY -->
<?php
echo phpscript('nformat.js');
echo script('jquery-min.js');
echo script('jquery-migrate-min.js'); //SOLO PARA JQUERY 1.9 - 2.0
echo script('plugins/jquery.numeric.pack.js');
echo script('jquery-ui.custom.min.js')
?>

<!-- Block Out -->
<?php echo script('plugins/jquery.blockUI.js'); ?>

<!-- Impromptu -->
<?php echo script('jquery-impromptu.js') ?>
<?php echo style('impromptu/default.css') ?>

<!-- JQGRID -->
<?php echo style('themes/ui.jqgrid.css') ?>
<?php echo script('i18n/grid.locale-sp.js') ?>
<?php echo script('jquery.jqGrid.min.js')  ?>


<!-- DATAGRID -->
<?php echo script('datagrid/datagrid.js')  ?>
<?php echo style('../datagrid/datagrid.css') ?>

<!-- LAYOUT -->
<?php echo script('jquery.layout.js') ?>

<style>
html, body {margin: 0; padding: 0; overflow: hidden; font-size: 75%;}
/*Splitter style */
#LeftPane  {padding: 2px; overflow: auto;}
#RightPane {padding: 2px; overflow: auto;}
.ui-layout-west .ui-jqgrid tr.jqgrow td { border-bottom: 1px solid;}

</style>

<script type="text/javascript">
var base_url = '<?php echo base_url() ?>';
var site_url = '<?php echo site_url() ?>';
var url = '';
var mGrid = '<?php echo $grid['gridname'] ?>';

$(document).ready(function() {
	var lastsel2;
	var _cargo = "";

<?php
	//Layout por defecto
	if ( isset($readyLayout) ){
		echo $readyLayout;
	} else {?>
	$('body').layout({
		minSize: 30,
		north__size: 60,
		resizerClass: 'ui-state-default',
		<?php echo (isset($WestSize) == true)? "west__size:".$WestSize.", ":"west__size: 212,\n"; ?>
		west__onresize: function (pane, $Pane){jQuery("#west-grid").jqGrid('setGridWidth',$Pane.innerWidth()-2);},
		center__onresize: function (pane, $Pane) {
			jQuery("#newapi<?php echo $grid['gridname'];?>").jqGrid('setGridWidth',$Pane.innerWidth()-6);
			jQuery("#newapi<?php echo $grid['gridname'];?>").jqGrid('setGridHeight',$Pane.innerHeight()-<?php echo $grid['menosalto']?>);
		}
	});
<?php }; 

	echo "\tdtgLoadButton();";
	//Layout por defecto
	if ( isset($grid123) ){
		echo $grid123;
	} else {?>

	// Grid por Defecto
	
	var gridId1 = "#newapi<?php echo $grid['gridname'];?>";
	var grid = jQuery(gridId1).jqGrid({
		ajaxGridOptions : {type:"POST"},
			jsonReader : {
				root:"data",
				repeatitems: false
			}
<?php } ?>
<?php if( !isset($onclick1) ) { ?>

			<?php if(!empty($grid['ondblClickRow'])) echo $grid['ondblClickRow'];?>

<?php } else 
			echo $onclick1;
?>

<?php echo $grid['table'];?>

	})
<?php echo $grid['pager'];?>;

<?php if (isset($grid1)) { ?>
	var gridId2 = "#newapi<?php echo $grid1['gridname'];?>";
	var grid1 = jQuery(gridId2).jqGrid({
		ajaxGridOptions : {type:"POST"},
			jsonReader : {
				root:"data",
				repeatitems: false
			}
			<?php if(!empty($grid1['ondblClickRow'])) echo $grid1['ondblClickRow'];?>
		
			<?php echo $grid1['table'];?>
	})
<?php echo $grid1['pager'];}; 

//grids Detalles
if (isset($detalles)) echo $detalles;


if (isset($grid2)) { ?>
	var grid2 = jQuery("#newapi<?php echo $grid2['gridname'];?>").jqGrid({
		ajaxGridOptions : {type:"POST"},
			jsonReader : {
				root:"data",
				repeatitems: false
			},
			ondblClickRow: function(id){
				var gridwidth = jQuery("#newapi<?php echo $grid2['gridname'];?>").width();
				gridwidth = gridwidth/2;
				grid2.editGridRow(id, {closeAfterEdit:true,mtype:'POST'});
				return;
			}
			<?php echo $grid2['table'];?>
	})
<?php
	echo $grid2['pager'];
}; 

if (isset($listados)) {
	if (!empty($listados)) {?>
	jQuery("#listados").jqGrid({
		datatype: "local",
		height: '200',
		colNames:['','Reporte','Nombre'],
		colModel:[{name:'id',index:'id', width:15},{name:'titulo',index:'titulo', width:165}, {name:'nombre',index:'proteo', hidden:true}],
		multiselect: false,
		hiddengrid: true,
		width: 190,
		caption: "Reportes",
		ondblClickRow: function(id, row, col, e){ 
			var ret = $("#listados").getRowData(id); 
			window.open("<?php echo base_url() ?>reportes/ver/"+ret.nombre, "_blank", "width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400)),screeny=((screen.availWidth/2)-300)");
			}
	});
	<?php echo $listados ?>

	for(var i=0;i<=datalis.length;i++) jQuery("#listados").jqGrid('addRowData',i+1,datalis[i]);

<?php
	}
} 

if (isset($otros)) {
	if (!empty($otros)) {?>
	jQuery("#otros").jqGrid({
		datatype: "local",
		height: 100,
		colNames:['','Funciones','Nombre'],
		colModel:[{name:'id',index:'id', width:15},{name:'titulo',index:'titulo', width:165}, {name:'proteo',index:'proteo', hidden:true}],
		multiselect: false,
		hiddengrid: true,
		width: 190,
		caption: "Funciones",
		ondblClickRow: function(id, row, col, e){ 
			var ret = $("#otros").getRowData(id); 
			window.open("<?php echo base_url() ?>reportes/ver/"+ret.nombre, "_blank", "width=800,height=600,scrollbars=yes,status=yes,resizable=yes,screenx=((screen.availHeight/2)-400)),screeny=((screen.availWidth/2)-300)");
			}
	});

	<?php echo $otros ?>
	
	for(var i=0;i<=dataotr.length;i++) jQuery("#otros").jqGrid('addRowData',i+1,dataotr[i]);

<?php
	}
};

if (isset($funciones))  echo $funciones;

?>

});

<?php if (isset($postready))  echo $postready; ?>

function esperar(url){
	$.blockUI({
		message: $('#displayBox'), 
		css: { 
			top:  ($(window).height() - 400) /2 + 'px', 
			left: ($(window).width()  - 400) /2 + 'px', 
			width: '300px' 
		}
	});
	$.get(url, function(data) {
		setTimeout($.unblockUI, 2); 
		$.prompt(data);
	});
	return false;
};

</script>

<?php
	$cintu = '<table width="100%" bgcolor="#2067B5"><tr><td align="left" width="80px"><img src="'.base_url().'assets/default/css/templete_01.jpg" width="120"></td><td align="center"><h1 style="font-size: 20px; color: rgb(255, 255, 255);" onclick="history.back()">'.$encabeza.'</h1></td><td align="left" width="100px" nowrap="nowrap"><font style="color:#FFFFFF;font-size:12px">Usuario: '.$this->secu->usuario().'<br/>'.$this->secu->getnombre().'</font></td><td align="right" width="28px"><img src="'.base_url().'assets/default/images/cerrar.png" alt="Cerrar Ventana" title="Cerrar Ventana" onclick="parent.window.close()" width="25"></td></tr></table>';
?>

</head>
<body id="dt_proteo">

<div class="ui-layout-north" ><?php echo $cintu ?></div>

<?php echo (isset($WestPanel) == true)? $WestPanel:''; ?>

<?php 
if(isset($centerpanel) == true) {
	echo $centerpanel;
} else{?>
<div id="RightPane" class="ui-layout-center ui-helper-reset ui-widget-content" align="center"><!-- Tabs pane -->
	<table id="newapi<?php echo $grid['gridname'];?>"></table> 
	<div   id="pnewapi<?php echo $grid['gridname'];?>"></div>
</div> <!-- #RightPane -->

<?php } ?>

<?php echo (isset($SouthPanel) == true)? $SouthPanel:''; ?>


<div id="dtg_dialog" title="Exporta Datos">
    <ul>
       <?php echo (isset($grid['export']['pdf'])   && $grid['export']['pdf']   == true)?"<li><a href='javascript:void(0);' onClick=\"dtgExport('pdf'," . $grid['querystring'] . ");\" >Pdf</a></li>":''; ?>
       <?php echo (isset($grid['export']['csv'])   && $grid['export']['csv']   == true)?"<li><a href='javascript:void(0);' onClick=\"dtgExport('csv'," . $grid['querystring'] . ");\" >Csv</a></li>":''; ?>
       <?php echo (isset($grid['export']['excel']) && $grid['export']['excel'] == true)?"<li><a href=\"#\">Excel</a></li>":''; ?>
       <?php echo (isset($grid['export']['print']) && $grid['export']['print'] == true)?"<li><a href=\"#\">Print</a></li>":''; ?>
       <?php echo (isset($grid['export']['xml'])   && $grid['export']['xml']   == true)?"<li><a href='javascript:void(0);' onClick=\"dtgExport('xml'," . $grid['querystring'] . ");\" >Xml</a></li>":''; ?>
    </ul>
</div>

<?php if(isset($bodyscript)) echo $bodyscript; ?>

<div id="displayBox" style="display:none" ><p>Disculpe por la espere.....</p><img  src="<?php echo base_url() ?>images/doggydig.gif" width="131px" height="79px"/></div>

</body>
</html>