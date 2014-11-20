<!DOCTYPE html>
<html>
	<head>
		<meta charset="utf-8" />
		<meta name="format-detection" content="telephone=no" />
        <meta name="viewport" content="user-scalable=yes, initial-scale=1, maximum-scale=1, minimum-scale=1, width=device-width, height=device-height, target-densitydpi=device-dpi" />
		<title>ProteoERP <?php if(isset($title)) echo ':'.preg_replace('/<[^>]*>/', '', $title); ?></title>
		<?php echo style('jqm/jquery.mobile.css'); ?>
		<?php echo script('jqm/jquery.js'); ?>
		<?php echo script('jqm/jquery.mobile.min.js'); ?>
		<?php echo phpscript('nformat.js'); ?>
	</head>
	<body>
		<div data-role="page" id='mainpage'>
			<?php echo (isset($content))? $content:''; ?>
		</div>
	</body>
</html>
