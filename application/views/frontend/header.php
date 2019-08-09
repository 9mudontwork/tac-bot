<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
	<!DOCTYPE html>

	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="shortcut icon" href="https://9mu.xyz/tac-global/favicon.ico" />
        <link rel="apple-touch-icon" href="https://9mu.xyz/tac-global/favicon.ico" />
		<title>TAC BOT - i3acksp4ce</title>

		<!-- Vendor CSS -->
		<?php echo link_tag('theme/custom/components/material-design-iconic-font/dist/css/material-design-iconic-font.min.css'); ?>
		<?php echo link_tag('theme/custom/components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.min.css'); ?>
		<?php echo link_tag('theme/custom/components/chosen/chosen.css'); ?>
		<?php echo link_tag('theme/custom/components/chosenImage/chosenImage.css'); ?>
		<?php echo link_tag('//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css'); ?>

		<!-- CSS -->
		<?php echo link_tag('theme/custom/css/app_1.min.css'); ?>
		<?php echo link_tag('theme/custom/css/app_2.min.css'); ?>

		<!-- Javascript Libraries -->
		<?php echo script_tag('theme/custom/components/jquery/dist/jquery.min.js'); ?>
		<?php echo script_tag('theme/custom/components/jquery.easy-pie-chart/dist/jquery.easypiechart.min.js'); ?>
		<?php echo script_tag('theme/custom/components/bootstrap/dist/js/bootstrap.min.js'); ?>
		<?php echo script_tag('theme/custom/components/malihu-custom-scrollbar-plugin/jquery.mCustomScrollbar.concat.min.js'); ?>
		<?php echo script_tag('theme/custom/components/chosen/chosen.jquery.js'); ?>
		<?php echo script_tag('theme/custom/components/chosenImage/chosenImage.jquery.js'); ?>
		<script src="//cdnjs.cloudflare.com/ajax/libs/list.js/1.5.0/list.min.js"></script>
		<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-cookie/1.4.1/jquery.cookie.min.js"></script>

		<?php echo script_tag('theme/custom/js/app-top.js'); ?>

	</head>

	<body>
		<header id="header" class="clearfix">
			<ul class="h-inner">
				<li class="hi-trigger ma-trigger" data-ma-action="sidebar-open" data-ma-target="#sidebar">
					<div class="line-wrap">
						<div class="line top"></div>
						<div class="line center"></div>
						<div class="line bottom"></div>
					</div>
				</li>

				<li class="hi-logo hidden-xs">
					<a href="{account_menu_link}">The Alchemist Code Bot</a>
				</li>
			</ul>

			<!-- Top Search Content -->
			<div class="h-search-wrap">
				<div class="hsw-inner">
					<i class="hsw-close zmdi zmdi-arrow-left" data-ma-action="search-close"></i>
					<input type="text">
				</div>
			</div>
		</header>