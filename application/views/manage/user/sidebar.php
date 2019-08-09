<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
	<section id="main">
		<aside id="sidebar" class="sidebar c-overflow z-depth-1">
			<ul class="main-menu">
				<li class="sub-menu toggled">
					<a href="" data-ma-action="submenu-toggle">
						<i class="zmdi zmdi-menu"></i> Manage</a>

					<ul style="display: block;">
						<li class="<?=active_menu('manage/user');?>">
							<a href="{manage_url}">User</a>
						</li>
						<li class="<?=active_menu('manage/reroll');?>">
							<a href="{reroll_url}">Reroll</a>
						</li>
						<li class="">
							<a href="{logout_url}">Logout</a>
						</li>
					</ul>
				</li>
			</ul>
		</aside>

		<section id="content" style="max-width: 1280px;margin: 0 auto;">