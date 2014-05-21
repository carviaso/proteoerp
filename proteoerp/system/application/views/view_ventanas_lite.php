<html>
<head>
<title>ProteoERP<?php if(isset($title)) echo ':'.trim(preg_replace('/<[^>]*>/', '', $title)); ?></title>
<?php if (isset($head))   echo $head;
if (isset($script)) echo $script; ?>
</head>
<body>
<?php 
if (isset($content)) echo $content; 
if (isset($extras))  echo $extras; 
?>
</body>
</html>
