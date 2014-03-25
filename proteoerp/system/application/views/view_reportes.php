<html>
<head>
	<meta http-equiv="Content-type" content="text/html; charset=<?php echo $this->config->item('charset'); ?>" />
	<?php echo style('reportes.css');?>
	<?php echo $head; ?>
<script type="text/javascript" language="javascript"></script>
</head>
<body>
	<div id='home'>
		<p><?php echo $titulo ?></p>
		<p><?php echo $forma  ?></p>
		<?php foreach($opts as $opt){ ?>
		<div class='<?php echo ($opt['siste']=='D')? 'rconte' : 'pconte'; ?>' tile='Averr'>
			<a href="<?php echo site_url('reportes/ver/'.$opt['nombre'].'/'.$repo); ?>" title="<?php echo 'C&oacute;digo: '.$opt['nombre']; ?>">
			<h3 style="padding:2px;margin:0px;font-size:0.9em"><?php
			$ban = stripos($this->db->char_set,'latin')!==false && $this->config->item('charset')=='UTF-8';
			if($ban){
				echo utf8_encode($opt['titulo']);
			}else{
				echo $opt['titulo'];
			}
			?></h3>
			<p style="padding:2px;margin:0px;font-size:0.7em"><?php
			if($ban){
				echo utf8_encode($opt['mensaje']);
			}else{
				echo $opt['mensaje'];
			}
			?></p>
			</a>
		</div>
		<?php } ?>
	</div>
</body>
</html>
