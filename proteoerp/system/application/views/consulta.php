<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
<head>
<title>Proteo ERP - <?php echo $title;?></title>
<?php

$cintu = '
<table width="100%" bgcolor="#2067B5">
	<tr>
		<td align="left" width="80px"><img src="'.base_url().'assets/default/css/templete_01.jpg" width="120"></td><td align="center"><h1 style="font-size: 20px; color: rgb(255, 255, 255);" onclick="history.back()">'.$encabeza.'</h1></td><td align="left" width="100px" nowrap="nowrap"><font style="color:#FFFFFF;font-size:12px">Usuario: '.$this->secu->usuario().'<br/>'.$this->secu->getnombre().'</font></td><td align="right" width="28px"><img src="'.base_url().'assets/default/images/cerrar.png" alt="Cerrar Ventana" title="Cerrar Ventana" onclick="parent.window.close()" width="25"></td>
	</tr>
</table>
';


echo "<!-- JQUERY -->";
echo phpscript('nformat.js');
echo script('jquery-min.js');
echo script('jquery-migrate-min.js');

echo script('plugins/jquery.numeric.pack.js');
echo script('jquery-ui.custom.min.js');
echo script('jquery.ui.selectmenu.js');
echo script('ui.multiselect.js');
echo script('jquery.contextmenu.js');
echo script('jquery.tablednd.js');
echo "\n";

echo style("themes/redmond/redmond.css");
echo style("ui.multiselect.css");
echo "\n";
echo style('jquery.ui.selectmenu.css');
echo "\n";
echo style('themes/ui.jqgrid.css');
?>
<style>
html, body {margin: 0;padding: 0;overflow: hidden;font-size: 75%;}

//Splitter style 
#LeftPane { overflow: auto; }

// Right-side element of the splitter.
#RightPane {padding: 2px;overflow: auto;}
.ui-tabs-nav li {position: relative;}
.ui-tabs-selected a span {padding-right: 10px;}
.ui-tabs-close {display: none;position: absolute;top: 3px;right: 0px;z-index: 800;width: 16px;height: 14px;font-size: 10px; font-style: normal;cursor: pointer;}
.ui-tabs-selected .ui-tabs-close {display: block;}
.ui-layout-west .ui-jqgrid tr.jqgrow td { border-bottom: 0px none;}
.ui-datepicker {z-index:1200;}
.rotate
    {
        -webkit-transform: rotate(-90deg);
        -moz-transform: rotate(-90deg);
        filter: progid:DXImageTransform.Microsoft.BasicImage(rotation=3);
    }
</style>

<?php
echo "<!-- Block Out -->";
echo script('plugins/jquery.blockUI.js'); 
echo "\n";
echo "<!-- Impromptu -->";
echo style('impromptu/default.css');
echo script('jquery-impromptu.js');
echo "\n";
echo style('apprise.min.css');
echo script('apprise-1.5.min.js');
echo "\n";
echo "<!-- JQGRID -->";
echo script('i18n/grid.locale-sp.js');
echo script('jquery.jqGrid.min.js');
echo "\n";
echo "<!-- DATAGRID -->";
echo script('datagrid/datagrid.js');
echo style('../datagrid/datagrid.css');
echo "\n";
echo "<!-- LAYOUT -->";
echo script('jquery.layout.js');
?>

<script type="text/javascript">
	$.jgrid.no_legacy_api = true;
	$.jgrid.useJSON = true;
</script>

<script type="text/javascript">

jQuery(document).ready(function(){

	$('body').layout({
		resizerClass: 'ui-state-default',
		west__onresize: function (pane, $Pane) {
			jQuery("#west-grid").jqGrid('setGridWidth',$Pane.innerWidth()-2);
		}
	});
	
	$.jgrid.defaults = $.extend($.jgrid.defaults,{loadui:"enable"});
	
	var maintab = $('#tabs','#RightPane').tabs({
		add: function(e, ui) {
			// append close thingy
			$(ui.tab).parents('li:first')
			.append('<span class="ui-tabs-close ui-icon ui-icon-close" title="Close Tab"></span>')
			.find('span.ui-tabs-close')
			.click(function() {
				maintab.tabs('remove', $('li', maintab).index($(this).parents('li:first')[0]));
			});
			// select just added tab
			maintab.tabs('select', '#' + ui.panel.id);
		}
	});
	
	jQuery("#west-grid").jqGrid({
		url: "<?php echo site_url("finanzas/analisisvision/opciones") ?>",
		datatype: "xml",
		height: "auto",
		pager: false,
		loadui: "disable",
		colNames: ["id","Items","url"],
		colModel: [
			{name: "id",width:1,hidden:true, key:true},
			{name: "menu", width:150, resizable: false, sortable:false},
			{name: "url",width:1,hidden:true}
		],
		treeGrid: true,
		caption: "Consultas",
		ExpandColumn: "menu",
		autowidth: true,
		//width: 180,
		rowNum: 200,
		ExpandColClick: true,
		treeIcons: {leaf:'ui-icon-document-b'},
		onSelectRow: function(rowid) {
			var treedata = $("#west-grid").jqGrid('getRowData',rowid);
			if(treedata.isLeaf=="true") {
				//treedata.url
				var st = "#t"+treedata.id;
				if($(st).html() != null ) {
					maintab.tabs('select',st);
				} else {
					maintab.tabs('add',st, treedata.menu);
					//$(st,"#tabs").load(treedata.url);
					$.ajax({
						url: treedata.url,
						type: "GET",
						dataType: "html",
						complete : function (req, err) {
							$(st,"#tabs").append(req.responseText);
						}
					});
				}
			}
		}
	});

// end splitter
});
</script>
</head>
<body>
	<div class="ui-layout-north" ><?php echo $cintu ?></div>
	<div id="LeftPane" class="ui-layout-west ui-widget ui-widget-content">
		<table id="west-grid"></table>
	</div> <!-- #LeftPane -->
	<div id="RightPane" class="ui-layout-center ui-helper-reset ui-widget-content" ><!-- Tabs pane -->
		<div id="switcher"></div>
		<div id="tabs" class="jqgtabs">
			<ul>
				<li><a href="#tabs-1">Principal</a></li>
			</ul>
			<div id="tabs-1" style="font-size:12px;"> Consultas de Datos... <br/>
			<br/>
			<br/>
			<br/>
			</div>
		</div>
	</div> <!-- #RightPane -->
</body>
</html>
