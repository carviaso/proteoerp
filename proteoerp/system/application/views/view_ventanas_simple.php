<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<meta http-equiv="Content-type" content="text/html; charset=<?=$this->config->item('charset'); ?>" >
<title>ProteoERP<?php if(isset($title)) echo ':'.preg_replace('/<[^>]*>/', '', $title); ?></title>
<?php //echo style("ventanas.css");?>
<?php if (isset($style))  echo $style; ?>


<?php if( !isset($tabla) )   $tabla=''; ?>
<?php if (isset($head))   echo $head;   ?>
<?php if (isset($script)) echo $script; ?>

</head>
<body>
<?php
echo $form_scripts;
echo $form_begin;
?>
<?php if (isset($content)) echo $content; ?>
</div>
<?php echo $form_end; ?>
</body>
</html>
