<html>
<head>
<title>Sistemas DataSIS</title>
<?php echo style("ventanas.css");?>
<?php if (isset($head))   echo $head;   ?>
<?php if (isset($script)) echo $script; ?>
</head>
<body>		
	<div id='contenido'>	
	<table width="95%" border=0 align="center">	
		<tr>
		<td >
						
		<td >
			<?php if (isset($title)) echo $title; ?>
		</td>
		<td>

		</td>
		<td >
		
		</td>
		</tr>
	<table>
		
		<table width="95%" border=0 align="center">
			<tr>
				<td valign=top ><?php if (isset($lista)) echo $lista; ?></td>
				<td><?php if (isset($content)) echo $content; ?></td>				
			</tr>
		</table>        
	</div>
	<?php if (isset($extras)) echo $extras; ?>
	
		
	</body>

</html>