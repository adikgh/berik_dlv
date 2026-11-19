<!DOCTYPE html>
<html lang="<?=($ui_lang == 'kz' ? 'kk' : $ui_lang)?>" id="html" class="html">
<head>

	<? include "head.php"; ?>

</head>
<body id="body" class="body">
	<?=(!empty($user_id) && class_exists('plan', false)) ? plan::banner() : ''?>
	
	<? include "menu.php"; ?>

	<!-- body start -->
	<div class="app">