<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
	<section id="main">
		<aside id="sidebar" class="sidebar c-overflow z-depth-1">
			<ul class="main-menu">
				<li class="mm-profile sub-menu toggled">
					<a href="" data-ma-action="submenu-toggle" class="media">
						<img class="pull-left" src="<?php echo base_url(); ?>theme/custom/img/1.png">
						<div class="media-body">
							{user}
						</div>
					</a>

					<ul style="display: block;">
						<li class="<?=active_menu('frontend/account');?>">
							<a href="{account_menu_link}">Account</a>
						</li>
						<li>
							<a href="<?php echo base_url(); ?>">Logout</a>
						</li>
					</ul>
				</li>
				<?php 
                if ($gem == 1) {
                    ?>
				<li class="sub-menu">
					<a href="" data-ma-action="submenu-toggle">
						<i class="zmdi zmdi-menu"></i> Injection
					</a>

					<ul style="display: block;">
						<li class="<?=active_menu('frontend/injection/gem'); ?>">
							<a href="{gem_injection_menu_link}">Gem</a>
						</li>
					</ul>
				</li>
				<?php
                }
                ?>

					<li class="sub-menu">
						<a href="" data-ma-action="submenu-toggle">
							<i class="zmdi zmdi-menu"></i> Quest</a>

						<ul style="display: block;">
							<li class="<?=active_menu('frontend/quest/normal');?>">
								<a href="{quest_normal_menu_link}">Normal</a>
							</li>
							<li class="<?=active_menu('frontend/quest/hard');?>">
								<a href="{quest_hard_menu_link}">Hard</a>
							</li>
							<li class="<?=active_menu('frontend/quest/multiplayer');?>">
								<a href="{quest_multiplayer_menu_link}">Multiplayer</a>
							</li>
							<li class="<?=active_menu('frontend/quest/event');?>">
								<a href="{quest_event_menu_link}">Event</a>
							</li>
						</ul>
					</li>

					<li class="sub-menu">
						<a href="" data-ma-action="submenu-toggle">
							<i class="zmdi zmdi-menu"></i> Mission</a>

						<ul style="display: block;">
							<li class="<?=active_menu('frontend/mission/daily');?>">
								<a href="{mission_daily_menu_link}">Daily</a>
							</li>
							<li class="<?=active_menu('frontend/mission/story');?>">
								<a href="{mission_story_menu_link}">Story</a>
							</li>
							<li class="<?=active_menu('frontend/mission/event');?>">
								<a href="{mission_event_menu_link}">All Event</a>
							</li>
							<li class="<?=active_menu('frontend/mission/title');?>">
								<a href="{mission_title_menu_link}">Title</a>
							</li>
							<li class="<?=active_menu('frontend/mission/challenge');?>">
								<a href="{mission_challenge_menu_link}">Challenge</a>
							</li>
						</ul>
					</li>

					<li class="sub-menu">
						<a href="" data-ma-action="submenu-toggle">
							<i class="zmdi zmdi-menu"></i> Summon Banner</a>

						<ul style="display: block;">
							<li class="<?=active_menu('frontend/summon/normal');?>">
								<a href="{summon_normal_menu_link}">Normal</a>
							</li>
							<li class="<?=active_menu('frontend/summon/unit');?>">
								<a href="{summon_unit_menu_link}">Rare Unit</a>
							</li>
							<li class="<?=active_menu('frontend/summon/gear');?>">
								<a href="{summon_gear_menu_link}">Rare Gear</a>
							</li>
						</ul>
					</li>
			</ul>
		</aside>

		<section id="content" style="max-width: 1280px;margin: 0 auto;">