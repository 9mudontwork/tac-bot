<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
	<div class="container">
		<div class="card z-depth-1">
			<div class="card-header ch-alt">
				<h2>Other</h2>
			</div>

			<div class="card-body card-padding">
				<p class="f-16 m-b-15">Select Quest</p>
				<style>
					.quest-list {
							max-height: 300px;
							overflow-x: hidden;
					}
					.img-responsive {
						max-width: 48px;
						display: inline-block;
					}
					.col-md-1 {
							width: auto;
							display: -webkit-box;
					}
					.radio-inline {
							vertical-align: text-top;
					}
				</style>
				<div id="quest_list" class="c-overflow quest-list">
				</div>

				<p class="f-16 m-t-20 m-b-15">If not work select some value</p>
				<div id="div_change_ymd">
					<label class="radio radio-inline m-r-20">
						<input type="radio" name="change_ymd" value="1">
						<i class="input-helper"></i>
						1
					</label>
					<label class="radio radio-inline m-r-20">
						<input type="radio" name="change_ymd" value="0" checked>
						<i class="input-helper"></i>
						0
					</label>
					<label class="radio radio-inline m-r-20">
						<input type="radio" name="change_ymd" value="-1">
						<i class="input-helper"></i>
						-1
					</label>
					<label class="radio radio-inline m-r-20">
						<input type="radio" name="change_ymd" value="-2">
						<i class="input-helper"></i>
						-2
					</label>
					<label class="radio radio-inline m-r-20">
						<input type="radio" name="change_ymd" value="-3">
						<i class="input-helper"></i>
						-3
					</label>
				</div>


				<p class="f-16 m-t-20 m-b-15">Multiply</p>
				<div id="div_multiply">
					<label class="radio radio-inline m-r-20">
						<input type="radio" name="multiply" value="1" checked>
						<i class="input-helper"></i>
						1
					</label>
					<label class="radio radio-inline m-r-20">
						<input type="radio" name="multiply" value="10">
						<i class="input-helper"></i>
						10
					</label>
					<label class="radio radio-inline m-r-20">
						<input type="radio" name="multiply" value="50">
						<i class="input-helper"></i>
						50
					</label>
					<label class="radio radio-inline m-r-20">
						<input type="radio" name="multiply" value="100">
						<i class="input-helper"></i>
						100
					</label>
				</div>


				<p class="f-16 m-t-20 m-b-5">How many you want more ?</p>
				<div class="form-group fg-line m-b-15">
					<input id="how_many" type="text" class="form-control input-lg" placeholder="Enter Amount">
				</div>

				<button id="run_button" onclick="doInjection();" type="submit" class="btn btn-lg btn-default m-t-10">Run</button>
			</div>

		</div>

		<div class="card z-depth-1">
			<div class="card-header ch-alt">
				<h2>Log
				</h2>
			</div>

			<div class="card-body card-padding">

				<div id="pie-charts" class="dash-widget-item">

					<div class="text-center">
						<div class="easy-pie main-pie" data-percent="0">
							<div class="percent">0</div>
						</div>
						<div id="text_log" class="lgi-heading m-b-5">
						</div>
					</div>

				</div>



			</div>


		</div>

	</div>

	<script>
		$(document).ready(function() {

			let questListArray = JSON.parse(questListJson());

			let start = 1;
			let html = '';
			$.each(questListArray, function(i, quest) {
				if (start == 1) {
					html = html + '<div class="row m-b-15">';
				}
				html = html + '<div class="col-md-1 col-sm-2 col-xs-3 m-b-15">';

				html = html + `<label class="radio radio-inline">
												<input type="radio" name="quest_id" value="${quest.iname}">
												<i class="input-helper"></i>
											</label>`;
				quest.item_reward.forEach(reward => {
					html = html + `<img class="img-responsive"src="http://cdn.alchemistcodedb.com/images/items/icons/${reward.url}">${reward.count} `;
				});
				
				html = html + '</div>';

				start++;
				if (start == 13) {
					html = html + '</div>';
					start = 1;
				}
			});
			$('#mCSB_2_container').append(html);
		});

		function doInjection() {
			let url = '{post_doInjection}';
			let quest_id = $('input[name=quest_id]:checked', '#quest_list').val();
			let change_ymd = $('input[name=change_ymd]:checked', '#div_change_ymd').val();
			let multiply = $('input[name=multiply]:checked', '#div_multiply').val();

			if(quest_id == '' || quest_id == undefined){
				notify('error', 'Please Select Quest');
				return;
			}

			let param = {
				'id': '{id}',
				'platform': $.cookie('platform'),
				'token': '{token}',
				'date_buy_bot': '{date_buy_bot}',
				'bot_day': '{bot_day}',
				'quest_id': quest_id,
				'change_ymd': change_ymd,
				'multiply': multiply,
			};

			let how_many_you_want_more = $('#how_many').val();
			let item_current_total_done = 0;
			let round = 1;

			function nextCompose() {

				$.ajax({
					type: "POST",
					url: url,
					data: param,
					cache: false,
					contentType: "application/x-www-form-urlencoded",
					beforeSend: function() {
						DisButton('run_button', 'Running...');
					},
					success: function(res) {

						try {
							var res_quest = resultQuest(res);
							// ทำเควสเดียวตามจำนวนครั้ง
							// ถ้าเควสผ่าน
							if (res_quest == 'success') {
								round++
								if (item_current_total_done < how_many_you_want_more) {
									// ส่งเควสถัดไป
									nextCompose();
								} else {
									// ทำเควสครบแล้ว
									EnButton('run_button', 'Run');
								}
							} else if (res_quest == 'wrong') {
								// สั่งรหัสเควสเริ่ม = สิ้นสุดทันทีเพื่อหยุด
								how_many_you_want_more = item_current_total_done;
								// ปลดบล็อคปุ่ม
								EnButton('run_button', 'Run');
							} else {
								EnButton('run_button', 'Run');
							}

						} catch (error) {
							notify('error', 'Program Error => ' + error);
							EnButton('run_button', 'Run');
						}

						function resultQuest(res) {

							try {
								if (res.body.player) {

									// แสดงไอเท็มดรอป
									let html_log = '';
									// html_log += '<div class="columns" >';
									let position = 0;
									res.body.items.forEach(items => {
										if (items.iname) {
											let name_unit_search = items.iname;
											name_unit_search = name_unit_search.replace('UN_V2_', 'IT_PI_')
											// ค้นหายูนิตใน listUnitJson
											let questListArray = JSON.parse(questListJson());
											let itemReward = questListArray[quest_id]['item_reward'];

											itemReward.forEach(item => {
												if (item.iname == name_unit_search) {
													
													if (position == 0) {
														item_current_total_done = item_current_total_done + (parseInt(item.count.replace('×', '') * multiply));
													}

													html_log += '<h4>Round ' + (round) + ' | ' + item.name;
													html_log += `<img class="lgi-img" src="http://cdn.alchemistcodedb.com/images/items/icons/${item.url}">`;
													html_log += 'Total x ' + numberWithCommas(items.num) + ' | Receive x '+ numberWithCommas(item_current_total_done);
												}
											});

										}
										position++;
									});

									$('#text_log').html(html_log);

									let percent = Math.round(item_current_total_done * 100) / how_many_you_want_more;
									if (percent >= 100) {
										percent = 100;
									}
									$('.main-pie').data('easyPieChart').update(percent);

									let current_percent = $('.main-pie').find('.percent').text();

									$('.main-pie').find('.percent').each(function() {
										$(this).prop('Counter', current_percent).animate({
											Counter: percent
										}, {
											duration: 500,
											easing: 'swing',
											step: function(now) {
												if (Math.round(now) >= 100) {
													$(this).text(100);
												} else {
													$(this).text(Math.round(now));
												}
											}
										});
									});

									return 'success';
								} else {
									renderErrorLog(res.message);
									notify('error', res.message);
									return 'wrong';
								}
							} catch (error) {

								if (res.message) {
									renderErrorLog(res.message);
									notify('error', res.message);
									return 'wrong';
								} else {
									renderErrorLog(error);
									notify('error', 'Program Error => ' + error);
									return 'wrong';
								}

							}

							function renderErrorLog(text) {
								$('#text_log').html('<h4 class="bgm-red">' + text + '</h4>');
							}

						}

					}
				});

			}

			nextCompose();

			// end function
		}

		function questListJson() {
			let questListJson =
				'{"WINQUEST_260":{"iname":"WINQUEST_260","name":"2\u7ae04\u8a712-10\u3092\u30af\u30ea\u30a2","item_reward":[{"iname":"IT_US_2016APR_JOB_SOUBI_TICKET","name":"Job-Specific Equipment Summon Ticket","count":"\u00d71","url":"IT_US_2016APR_JOB_SOUBI_TICKET.png"},{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d77","url":"IT_UG_APPLE5.png"},{"iname":"IT_SG_POT3","name":"Gold Alchemia Pot","count":"\u00d71","url":"IT_SG_POT3.png"}]},"WINQUEST_252":{"iname":"WINQUEST_252","name":"2\u7ae04\u8a712-2\u3092\u30af\u30ea\u30a2","item_reward":[{"iname":"IT_PI_EDGAR","name":"Edgar Soul Shard","count":"\u00d75","url":"IT_PI_EDGAR.png"},{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d75","url":"IT_UG_APPLE5.png"}]},"WINQUEST_254":{"iname":"WINQUEST_254","name":"2\u7ae04\u8a712-4\u3092\u30af\u30ea\u30a2","item_reward":[{"iname":"IT_EQ_ATK_AX","name":"Great Axe","count":"\u00d71","url":"IT_EQ_ATK_AX.png"}]},"WINQUEST_256":{"iname":"WINQUEST_256","name":"2\u7ae04\u8a712-6\u3092\u30af\u30ea\u30a2","item_reward":[{"iname":"IT_US_TICKET","name":"Skip Ticket","count":"\u00d710","url":"IT_US_TICKET.png"},{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d75","url":"IT_UG_APPLE5.png"}]},"WINQUEST_258":{"iname":"WINQUEST_258","name":"2\u7ae04\u8a712-8\u3092\u30af\u30ea\u30a2","item_reward":[{"iname":"IT_EQ_MND_ROBE","name":"Saint&#39;s Robe","count":"\u00d71","url":"IT_EQ_MND_ROBE.png"}]},"WINQUEST_270":{"iname":"WINQUEST_270","name":"2\u7ae04\u8a713-10\u3092\u30af\u30ea\u30a2","item_reward":[{"iname":"IT_US_APPLE_10TICKET","name":"Apple 10-Summon Ticket","count":"\u00d71","url":"IT_US_APPLE_10TICKET.png"},{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d77","url":"IT_UG_APPLE5.png"},{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d725","url":"IT_CV_GOLD3.png"}]},"WINQUEST_262":{"iname":"WINQUEST_262","name":"2\u7ae04\u8a713-2\u3092\u30af\u30ea\u30a2","item_reward":[{"iname":"IT_PI_EDGAR","name":"Edgar Soul Shard","count":"\u00d75","url":"IT_PI_EDGAR.png"},{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d75","url":"IT_UG_APPLE5.png"}]},"WINQUEST_264":{"iname":"WINQUEST_264","name":"2\u7ae04\u8a713-4\u3092\u30af\u30ea\u30a2","item_reward":[{"iname":"IT_EQ_CRI_TWOSWORDS","name":"Orichalcum Blades","count":"\u00d71","url":"IT_EQ_CRI_TWOSWORDS.png"}]},"WINQUEST_266":{"iname":"WINQUEST_266","name":"2\u7ae04\u8a713-6\u3092\u30af\u30ea\u30a2","item_reward":[{"iname":"IT_US_TICKET","name":"Skip Ticket","count":"\u00d710","url":"IT_US_TICKET.png"},{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d75","url":"IT_UG_APPLE5.png"}]},"WINQUEST_268":{"iname":"WINQUEST_268","name":"2\u7ae04\u8a713-8\u3092\u30af\u30ea\u30a2","item_reward":[{"iname":"IT_EQ_HP_FURCOAT","name":"Sacred Fur Coat","count":"\u00d71","url":"IT_EQ_HP_FURCOAT.png"},{"iname":"IT_SG_POT3","name":"Gold Alchemia Pot","count":"\u00d71","url":"IT_SG_POT3.png"}]},"GL_GEARTUT_01":{"iname":"GL_GEARTUT_01","name":"[Beginner Gear Mission 1] Get Gear Shards!","item_reward":[{"iname":"IT_AF_ACCS_EARRING","name":"Sapphire Earrings Shard","count":"\u00d750","url":"AF_ACCS_EARRING.png"}]},"GL_GEARTUT_02":{"iname":"GL_GEARTUT_02","name":"[Beginner Gear Mission 2] Transmute Your Gear!","item_reward":[{"iname":"IT_EQUP_MIT","name":"Adamantine Ore","count":"\u00d75","url":"IT_EQUP_MIT.png"}]},"GL_GEARTUT_03":{"iname":"GL_GEARTUT_03","name":"[Beginner Gear Mission 3] Enhance Your Gear!","item_reward":[{"iname":"IT_AF_ACCS_EARRING","name":"Sapphire Earrings Shard","count":"\u00d750","url":"AF_ACCS_EARRING.png"}]},"GL_CB_BF_GL_HARD_01":{"iname":"GL_CB_BF_GL_HARD_01","name":"Brave Frontier [Hard] [1\/3]","item_reward":[{"iname":"IT_EQ_SOUL_GUA","name":"Holy Cavalier Token","count":"\u00d76","url":"IT_EQ_SOUL_GUA.png"}]},"GL_CB_BF_GL_HARD_02":{"iname":"GL_CB_BF_GL_HARD_02","name":"Brave Frontier [Hard] [3\/3]","item_reward":[{"iname":"IT_PI_SELENA","name":"Selena Soul Shard","count":"\u00d710","url":"IT_PI_SELENA.png"}]},"GL_WA_CLOE_AF_01":{"iname":"GL_WA_CLOE_AF_01","name":"Call of the Scarlet Flame - Dazzling Blade [1\/3]","item_reward":[{"iname":"IT_EQUP_MIT","name":"Adamantine Ore","count":"\u00d75","url":"IT_EQUP_MIT.png"}]},"GL_WA_CLOE_AF_02":{"iname":"GL_WA_CLOE_AF_02","name":"Call of the Scarlet Flame - Dazzling Blade [2\/3]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d750","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_WA_CLOE_01_01":{"iname":"GL_WA_CLOE_01_01","name":"Call of the Scarlet Flame - Episode 1 [1\/3]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d710","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_WA_CLOE_02_01":{"iname":"GL_WA_CLOE_02_01","name":"Call of the Scarlet Flame - Episode 2 [1\/7]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d715","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_WA_CLOE_02_02":{"iname":"GL_WA_CLOE_02_02","name":"Call of the Scarlet Flame - Episode 2 [3\/7]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d71","url":"IT_UG_APPLE4.png"}]},"GL_WA_CLOE_02_03":{"iname":"GL_WA_CLOE_02_03","name":"Call of the Scarlet Flame - Episode 2 [5\/7]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d715","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_WA_CLOE_03_05":{"iname":"GL_WA_CLOE_03_05","name":"Call of the Scarlet Flame - Episode 3 [10\/15]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d725","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_WA_CLOE_03_01":{"iname":"GL_WA_CLOE_03_01","name":"Call of the Scarlet Flame - Episode 3 [1\/15]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d725","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_WA_CLOE_03_02":{"iname":"GL_WA_CLOE_03_02","name":"Call of the Scarlet Flame - Episode 3 [3\/15]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d71","url":"IT_CV_GOLD3.png"}]},"GL_WA_CLOE_03_03":{"iname":"GL_WA_CLOE_03_03","name":"Call of the Scarlet Flame - Episode 3 [5\/15]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d725","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_WA_CLOE_03_04":{"iname":"GL_WA_CLOE_03_04","name":"Call of the Scarlet Flame - Episode 3 [7\/15]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d73","url":"IT_UG_APPLE4.png"}]},"GL_WA_CLOE_04_05":{"iname":"GL_WA_CLOE_04_05","name":"Call of the Scarlet Flame [Hard] [10\/25]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d75","url":"IT_CV_GOLD3.png"}]},"GL_WA_CLOE_04_01":{"iname":"GL_WA_CLOE_04_01","name":"Call of the Scarlet Flame [Hard] [1\/25]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d73","url":"IT_CV_GOLD3.png"}]},"GL_WA_CLOE_04_06":{"iname":"GL_WA_CLOE_04_06","name":"Call of the Scarlet Flame [Hard] [15\/25]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d745","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_WA_CLOE_04_02":{"iname":"GL_WA_CLOE_04_02","name":"Call of the Scarlet Flame [Hard] [3\/25]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d730","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_WA_CLOE_04_03":{"iname":"GL_WA_CLOE_04_03","name":"Call of the Scarlet Flame [Hard] [5\/25]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"}]},"GL_WA_CLOE_04_04":{"iname":"GL_WA_CLOE_04_04","name":"Call of the Scarlet Flame [Hard] [7\/25]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d735","url":"AF_ARMS_SWO_CLOE.png"}]},"ZOKUSEI_16_02":{"iname":"ZOKUSEI_16_02","name":"Clear [Dark - Water] Ancient Battles of Six [Adv] [2]","item_reward":[{"iname":"IT_PI_COMMON_WATER","name":"Water Soul Shard","count":"\u00d75","url":"IT_PI_COMMON_WATER.png"}]},"ZOKUSEI_15_01":{"iname":"ZOKUSEI_15_01","name":"Clear [Dark - Water] Ancient Battles of Six [Bgn]","item_reward":[{"iname":"IT_PI_COMMON_DARK","name":"Dark Soul Shard","count":"\u00d71","url":"IT_PI_COMMON_DARK.png"},{"iname":"IT_PI_COMMON_WATER","name":"Water Soul Shard","count":"\u00d71","url":"IT_PI_COMMON_WATER.png"}]},"ZOKUSEI_14_02":{"iname":"ZOKUSEI_14_02","name":"Clear [Fire - Wind] Ancient Battles of Six [Adv] [2]","item_reward":[{"iname":"IT_PI_COMMON_WIND","name":"Wind Soul Shard","count":"\u00d75","url":"IT_PI_COMMON_WIND.png"}]},"ZOKUSEI_13_01":{"iname":"ZOKUSEI_13_01","name":"Clear [Fire - Wind] Ancient Battles of Six [Bgn]","item_reward":[{"iname":"IT_PI_COMMON_FIRE","name":"Fire Soul Shard","count":"\u00d71","url":"IT_PI_COMMON_FIRE.png"},{"iname":"IT_PI_COMMON_WIND","name":"Wind Soul Shard","count":"\u00d71","url":"IT_PI_COMMON_WIND.png"}]},"ZOKUSEI_12_02":{"iname":"ZOKUSEI_12_02","name":"Clear [Light - Thunder] Ancient Battles of Six [Adv] [2]","item_reward":[{"iname":"IT_PI_COMMON_THUNDER","name":"Thunder Soul Shard","count":"\u00d75","url":"IT_PI_COMMON_THUNDER.png"}]},"ZOKUSEI_11_01":{"iname":"ZOKUSEI_11_01","name":"Clear [Light - Thunder] Ancient Battles of Six [Bgn]","item_reward":[{"iname":"IT_PI_COMMON_LIGHT","name":"Light Soul Shard","count":"\u00d71","url":"IT_PI_COMMON_LIGHT.png"},{"iname":"IT_PI_COMMON_THUNDER","name":"Thunder Soul Shard","count":"\u00d71","url":"IT_PI_COMMON_THUNDER.png"}]},"GL_BF_DAILY_04":{"iname":"GL_BF_DAILY_04","name":"[Daily] Brave Frontier - Defeat Vargas 3 times in Brave Frontier - Episode 2","item_reward":[{"iname":"IT_PI_VARGAS","name":"Vargas Soul Shard","count":"\u00d73","url":"IT_PI_VARGAS.png"}]},"GL_BF_DAILY_02":{"iname":"GL_BF_DAILY_02","name":"[Daily] Brave Frontier - Level up any Ability","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d71","url":"IT_CV_GOLD3.png"}]},"GL_BF_DAILY_01":{"iname":"GL_BF_DAILY_01","name":"[Daily] Brave Frontier - Level up any Unit","item_reward":[{"iname":"IT_UG_APPLE_MGOD","name":"Metal God Apple","count":"\u00d72","url":"IT_UG_APPLE_MGOD.png"}]},"DAILY_GL2017OCT_2_09":{"iname":"DAILY_GL2017OCT_2_09","name":"[Daily] Clear an Event Quest [1]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d71","url":"IT_UG_APPLE4.png"}]},"GL_POK_DAILY_03":{"iname":"GL_POK_DAILY_03","name":"[Daily] [Phantom of the Kill] Clear an Event Quest once","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d71","url":"IT_UG_APPLE4.png"}]},"GL_POK_DAILY_04":{"iname":"GL_POK_DAILY_04","name":"[Daily] [Phantom of the Kill] Enhance an ability","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d71","url":"IT_CV_GOLD3.png"}]},"DAILY_GL2017OCT_2_12":{"iname":"DAILY_GL2017OCT_2_12","name":"[Daily] Use Summoning","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d71","url":"IT_CV_GOLD3.png"}]},"GL_SO_RETZ_LV3_05":{"iname":"GL_SO_RETZ_LV3_05","name":"Encounter with Retzius [Adv] [10\/25]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d75","url":"IT_CV_GOLD3.png"}]},"GL_SO_RETZ_LV3_01":{"iname":"GL_SO_RETZ_LV3_01","name":"Encounter with Retzius [Adv] [1\/25]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d73","url":"IT_CV_GOLD3.png"}]},"GL_SO_RETZ_LV3_06":{"iname":"GL_SO_RETZ_LV3_06","name":"Encounter with Retzius [Adv] [15\/25]","item_reward":[{"iname":"IT_PI_RETZIUS","name":"Retzius Soul Shard","count":"\u00d715","url":"IT_PI_RETZIUS.png"}]},"GL_SO_RETZ_LV3_02":{"iname":"GL_SO_RETZ_LV3_02","name":"Encounter with Retzius [Adv] [3\/25]","item_reward":[{"iname":"IT_PI_RETZIUS","name":"Retzius Soul Shard","count":"\u00d75","url":"IT_PI_RETZIUS.png"}]},"GL_SO_RETZ_LV3_03":{"iname":"GL_SO_RETZ_LV3_03","name":"Encounter with Retzius [Adv] [5\/25]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"}]},"GL_SO_RETZ_LV3_04":{"iname":"GL_SO_RETZ_LV3_04","name":"Encounter with Retzius [Adv] [7\/25]","item_reward":[{"iname":"IT_PI_RETZIUS","name":"Retzius Soul Shard","count":"\u00d710","url":"IT_PI_RETZIUS.png"}]},"GL_SO_RETZ_LV1_01":{"iname":"GL_SO_RETZ_LV1_01","name":"Encounter with Retzius [Bgn] [1\/5]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d71","url":"IT_UG_APPLE4.png"}]},"GL_SO_RETZ_LV1_02":{"iname":"GL_SO_RETZ_LV1_02","name":"Encounter with Retzius [Bgn] [3\/5]","item_reward":[{"iname":"IT_PI_RETZIUS","name":"Retzius Soul Shard","count":"\u00d75","url":"IT_PI_RETZIUS.png"}]},"GL_SO_RETZ_LV2_01":{"iname":"GL_SO_RETZ_LV2_01","name":"Encounter with Retzius [Int] [1\/10]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d71","url":"IT_CV_GOLD3.png"}]},"GL_SO_RETZ_LV2_02":{"iname":"GL_SO_RETZ_LV2_02","name":"Encounter with Retzius [Int] [3\/10]","item_reward":[{"iname":"IT_PI_RETZIUS","name":"Retzius Soul Shard","count":"\u00d75","url":"IT_PI_RETZIUS.png"}]},"GL_SO_RETZ_LV2_03":{"iname":"GL_SO_RETZ_LV2_03","name":"Encounter with Retzius [Int] [5\/10]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d73","url":"IT_UG_APPLE4.png"}]},"GL_SO_RETZ_LV2_04":{"iname":"GL_SO_RETZ_LV2_04","name":"Encounter with Retzius [Int] [7\/10]","item_reward":[{"iname":"IT_PI_RETZIUS","name":"Retzius Soul Shard","count":"\u00d710","url":"IT_PI_RETZIUS.png"}]},"GL_HARVEST_3_03":{"iname":"GL_HARVEST_3_03","name":"Harvest Revelry! [Adv] [10\/10]","item_reward":[{"iname":"IT_EQ_DEX_GLASSPEN","name":"Glass Pen","count":"\u00d72","url":"IT_EQ_DEX_GLASSPEN.png"},{"iname":"IT_EQ_MAG_GEARROD","name":"Gear Rod","count":"\u00d72","url":"IT_EQ_MAG_GEARROD.png"},{"iname":"IT_EQ_DEF_ARMOR","name":"Flame Emperor Armor","count":"\u00d72","url":"IT_EQ_DEF_ARMOR.png"}]},"GL_HARVEST_3_01":{"iname":"GL_HARVEST_3_01","name":"Harvest Revelry! [Adv] [1\/10]","item_reward":[{"iname":"IT_SG_POT3","name":"Gold Alchemia Pot","count":"\u00d75","url":"IT_SG_POT3.png"}]},"GL_HARVEST_3_02":{"iname":"GL_HARVEST_3_02","name":"Harvest Revelry! [Adv] [5\/10]","item_reward":[{"iname":"IT_US_SOULTICKET","name":"Soul Encounter Ticket","count":"\u00d71","url":"IT_US_SOULTICKET.png"}]},"GL_HARVEST_HARD_01":{"iname":"GL_HARVEST_HARD_01","name":"Harvest Revelry! [Hard] [1\/3]","item_reward":[{"iname":"IT_US_TICKET","name":"Skip Ticket","count":"\u00d710","url":"IT_US_TICKET.png"}]},"GL_HARVEST_HARD_02":{"iname":"GL_HARVEST_HARD_02","name":"Harvest Revelry! [Hard] [3\/3]","item_reward":[{"iname":"IT_US_SOULTICKET","name":"Soul Encounter Ticket","count":"\u00d73","url":"IT_US_SOULTICKET.png"}]},"GL_HARVEST_2_03":{"iname":"GL_HARVEST_2_03","name":"Harvest Revelry! [Int] [10\/10]","item_reward":[{"iname":"IT_EQ_ATK_LANCE","name":"Great Lance","count":"\u00d72","url":"IT_EQ_ATK_LANCE.png"},{"iname":"IT_EQ_MAG_STAFF","name":"Star Staff","count":"\u00d72","url":"IT_EQ_MAG_STAFF.png"},{"iname":"IT_EQ_LUK_DICE","name":"Dice of Fate","count":"\u00d72","url":"IT_EQ_LUK_DICE.png"}]},"GL_HARVEST_2_01":{"iname":"GL_HARVEST_2_01","name":"Harvest Revelry! [Int] [1\/10]","item_reward":[{"iname":"IT_SG_POT2","name":"Silver Alchemia Pot","count":"\u00d75","url":"IT_SG_POT2.png"}]},"GL_HARVEST_2_02":{"iname":"GL_HARVEST_2_02","name":"Harvest Revelry! [Int] [5\/10]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d73","url":"IT_CV_GOLD3.png"}]},"GL_SO_SABA_LV1_01":{"iname":"GL_SO_SABA_LV1_01","name":"Lizards of the Lost Kingdom - Episode 1 [1\/10]","item_reward":[{"iname":"IT_PI_SABALETA","name":"Sabareta Soul Shard","count":"\u00d73","url":"IT_PI_SABALETA.png"}]},"GL_SO_SABA_LV1_02":{"iname":"GL_SO_SABA_LV1_02","name":"Lizards of the Lost Kingdom - Episode 1 [3\/10]","item_reward":[{"iname":"IT_UG_APPLE3","name":"Apple of Accomplishment","count":"\u00d75","url":"IT_UG_APPLE3.png"}]},"GL_SO_SABA_LV1_03":{"iname":"GL_SO_SABA_LV1_03","name":"Lizards of the Lost Kingdom - Episode 1 [5\/10]","item_reward":[{"iname":"IT_PI_SABALETA","name":"Sabareta Soul Shard","count":"\u00d75","url":"IT_PI_SABALETA.png"}]},"GL_SO_SABA_LV1_04":{"iname":"GL_SO_SABA_LV1_04","name":"Lizards of the Lost Kingdom - Episode 1 [7\/10]","item_reward":[{"iname":"IT_CV_GOLD2","name":"Silver Ingot","count":"\u00d75","url":"IT_CV_GOLD2.png"}]},"GL_SO_SABA_LV3_05":{"iname":"GL_SO_SABA_LV3_05","name":"Lizards of the Lost Kingdom - Episode 2 [10\/20]","item_reward":[{"iname":"IT_PI_SABALETA","name":"Sabareta Soul Shard","count":"\u00d710","url":"IT_PI_SABALETA.png"}]},"GL_SO_SABA_LV3_01":{"iname":"GL_SO_SABA_LV3_01","name":"Lizards of the Lost Kingdom - Episode 2 [1\/20]","item_reward":[{"iname":"IT_PI_SABALETA","name":"Sabareta Soul Shard","count":"\u00d75","url":"IT_PI_SABALETA.png"}]},"GL_SO_SABA_LV3_06":{"iname":"GL_SO_SABA_LV3_06","name":"Lizards of the Lost Kingdom - Episode 2 [15\/20]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d73","url":"IT_UG_APPLE4.png"}]},"GL_SO_SABA_LV3_02":{"iname":"GL_SO_SABA_LV3_02","name":"Lizards of the Lost Kingdom - Episode 2 [3\/20]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d72","url":"IT_UG_APPLE4.png"}]},"GL_SO_SABA_LV3_03":{"iname":"GL_SO_SABA_LV3_03","name":"Lizards of the Lost Kingdom - Episode 2 [5\/20]","item_reward":[{"iname":"IT_PI_SABALETA","name":"Sabareta Soul Shard","count":"\u00d75","url":"IT_PI_SABALETA.png"}]},"GL_SO_SABA_LV3_04":{"iname":"GL_SO_SABA_LV3_04","name":"Lizards of the Lost Kingdom - Episode 2 [7\/20]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d72","url":"IT_CV_GOLD3.png"}]},"GL_SO_SABA_LV5_05":{"iname":"GL_SO_SABA_LV5_05","name":"Lizards of the Lost Kingdom - Episode 3 [Hard] [10\/30]","item_reward":[{"iname":"IT_PI_SABALETA","name":"Sabareta Soul Shard","count":"\u00d710","url":"IT_PI_SABALETA.png"}]},"GL_SO_SABA_LV5_01":{"iname":"GL_SO_SABA_LV5_01","name":"Lizards of the Lost Kingdom - Episode 3 [Hard] [1\/30]","item_reward":[{"iname":"IT_PI_SABALETA","name":"Sabareta Soul Shard","count":"\u00d710","url":"IT_PI_SABALETA.png"}]},"GL_SO_SABA_LV5_06":{"iname":"GL_SO_SABA_LV5_06","name":"Lizards of the Lost Kingdom - Episode 3 [Hard] [15\/30]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d75","url":"IT_CV_GOLD3.png"}]},"GL_SO_SABA_LV5_07":{"iname":"GL_SO_SABA_LV5_07","name":"Lizards of the Lost Kingdom - Episode 3 [Hard] [20\/30]","item_reward":[{"iname":"IT_PI_SABALETA","name":"Sabareta Soul Shard","count":"\u00d715","url":"IT_PI_SABALETA.png"}]},"GL_SO_SABA_LV5_08":{"iname":"GL_SO_SABA_LV5_08","name":"Lizards of the Lost Kingdom - Episode 3 [Hard] [25\/30]","item_reward":[{"iname":"IT_US_SOUBI10TICKET_2","name":"Equipment 10-Summon Ticket","count":"\u00d71","url":"IT_US_SOUBI10TICKET_2.png"}]},"GL_SO_SABA_LV5_02":{"iname":"GL_SO_SABA_LV5_02","name":"Lizards of the Lost Kingdom - Episode 3 [Hard] [3\/30]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d73","url":"IT_CV_GOLD3.png"}]},"GL_SO_SABA_LV5_03":{"iname":"GL_SO_SABA_LV5_03","name":"Lizards of the Lost Kingdom - Episode 3 [Hard] [5\/30]","item_reward":[{"iname":"IT_PI_SABALETA","name":"Sabareta Soul Shard","count":"\u00d710","url":"IT_PI_SABALETA.png"}]},"GL_SO_SABA_LV5_04":{"iname":"GL_SO_SABA_LV5_04","name":"Lizards of the Lost Kingdom - Episode 3 [Hard] [7\/30]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"}]},"GL_SO_SABA_LV6_2_05":{"iname":"GL_SO_SABA_LV6_2_05","name":"Lizards of the Lost Kingdom [Extra] [10\/100]","item_reward":[{"iname":"IT_EQ_ATK_LANCE","name":"Great Lance","count":"\u00d72","url":"IT_EQ_ATK_LANCE.png"}]},"GL_SO_SABA_LV6_2_01":{"iname":"GL_SO_SABA_LV6_2_01","name":"Lizards of the Lost Kingdom [Extra] [1\/100]","item_reward":[{"iname":"IT_EQ_ATK_SABER","name":"Saber","count":"\u00d72","url":"IT_EQ_ATK_SABER.png"}]},"GL_SO_SABA_LV6_2_06":{"iname":"GL_SO_SABA_LV6_2_06","name":"Lizards of the Lost Kingdom [Extra] [15\/100]","item_reward":[{"iname":"IT_AF_ARMO_ARMOR_02","name":"Armor of Wrath Shard","count":"\u00d75","url":"AF_ARMO_ARMOR_02.png"}]},"GL_SO_SABA_LV6_2_07":{"iname":"GL_SO_SABA_LV6_2_07","name":"Lizards of the Lost Kingdom [Extra] [20\/100]","item_reward":[{"iname":"IT_EQ_ATK_SWORD","name":"Sacred Lion Blade","count":"\u00d72","url":"IT_EQ_ATK_SWORD.png"}]},"GL_SO_SABA_LV6_2_08":{"iname":"GL_SO_SABA_LV6_2_08","name":"Lizards of the Lost Kingdom [Extra] [25\/100]","item_reward":[{"iname":"IT_AF_ARMO_ARMOR_02","name":"Armor of Wrath Shard","count":"\u00d710","url":"AF_ARMO_ARMOR_02.png"}]},"GL_SO_SABA_LV6_2_09":{"iname":"GL_SO_SABA_LV6_2_09","name":"Lizards of the Lost Kingdom [Extra] [30\/100]","item_reward":[{"iname":"IT_EQ_HOOK","name":"Steel Hook","count":"\u00d72","url":"IT_EQ_HOOK.png"}]},"GL_SO_SABA_LV6_2_02":{"iname":"GL_SO_SABA_LV6_2_02","name":"Lizards of the Lost Kingdom [Extra] [3\/100]","item_reward":[{"iname":"IT_AF_ARMO_ARMOR_02","name":"Armor of Wrath Shard","count":"\u00d72","url":"AF_ARMO_ARMOR_02.png"}]},"GL_SO_SABA_LV6_2_10":{"iname":"GL_SO_SABA_LV6_2_10","name":"Lizards of the Lost Kingdom [Extra] [40\/100]","item_reward":[{"iname":"IT_AF_ARMO_ARMOR_02","name":"Armor of Wrath Shard","count":"\u00d715","url":"AF_ARMO_ARMOR_02.png"}]},"GL_SO_SABA_LV6_2_11":{"iname":"GL_SO_SABA_LV6_2_11","name":"Lizards of the Lost Kingdom [Extra] [50\/100]","item_reward":[{"iname":"IT_EQ_ATK_SPEAR","name":"Dragon Halberd","count":"\u00d72","url":"IT_EQ_ATK_SPEAR.png"}]},"GL_SO_SABA_LV6_2_03":{"iname":"GL_SO_SABA_LV6_2_03","name":"Lizards of the Lost Kingdom [Extra] [5\/100]","item_reward":[{"iname":"IT_EQ_DEF_SHELM","name":"Cross Helmet","count":"\u00d72","url":"IT_EQ_DEF_SHELM.png"}]},"GL_SO_SABA_LV6_2_04":{"iname":"GL_SO_SABA_LV6_2_04","name":"Lizards of the Lost Kingdom [Extra] [7\/100]","item_reward":[{"iname":"IT_AF_ARMO_ARMOR_02","name":"Armor of Wrath Shard","count":"\u00d73","url":"AF_ARMO_ARMOR_02.png"}]},"GL_SO_SABA_LV6_2_12":{"iname":"GL_SO_SABA_LV6_2_12","name":"Lizards of the Lost Kingdom [Extra] [75\/100]","item_reward":[{"iname":"IT_AF_ARMO_ARMOR_02","name":"Armor of Wrath Shard","count":"\u00d725","url":"AF_ARMO_ARMOR_02.png"}]},"GL_MULTI_BF_LV2_2_01":{"iname":"GL_MULTI_BF_LV2_2_01","name":"[Multiplay] Brave Frontier - Episode 5 [1\/5]","item_reward":[{"iname":"IT_AF_ARMS_SWO_LEQSIDA","name":"Lexida Shard","count":"\u00d710","url":"AF_ARMS_SWO_LEQSIDA.png"}]},"GL_MULTI_BF_LV2_2_02":{"iname":"GL_MULTI_BF_LV2_2_02","name":"[Multiplay] Brave Frontier - Episode 5 [3\/5]","item_reward":[{"iname":"IT_PI_SELENA","name":"Selena Soul Shard","count":"\u00d75","url":"IT_PI_SELENA.png"}]},"GL_MULTI_BF_LV2_3_01":{"iname":"GL_MULTI_BF_LV2_3_01","name":"[Multiplay] Brave Frontier - Episode 6 [1\/10]","item_reward":[{"iname":"IT_AF_ARMS_SWO_LEQSIDA","name":"Lexida Shard","count":"\u00d715","url":"AF_ARMS_SWO_LEQSIDA.png"}]},"GL_MULTI_BF_LV2_3_02":{"iname":"GL_MULTI_BF_LV2_3_02","name":"[Multiplay] Brave Frontier - Episode 6 [3\/10]","item_reward":[{"iname":"IT_PI_SELENA","name":"Selena Soul Shard","count":"\u00d75","url":"IT_PI_SELENA.png"}]},"GL_MULTI_BF_LV2_3_03":{"iname":"GL_MULTI_BF_LV2_3_03","name":"[Multiplay] Brave Frontier - Episode 6 [5\/10]","item_reward":[{"iname":"IT_AF_ARMS_SWO_LEQSIDA","name":"Lexida Shard","count":"\u00d715","url":"AF_ARMS_SWO_LEQSIDA.png"}]},"GL_MULTI_BF_LV2_3_04":{"iname":"GL_MULTI_BF_LV2_3_04","name":"[Multiplay] Brave Frontier - Episode 6 [7\/10]","item_reward":[{"iname":"IT_PI_SELENA","name":"Selena Soul Shard","count":"\u00d710","url":"IT_PI_SELENA.png"}]},"GL_MULTI_WA_CLOE_04_05":{"iname":"GL_MULTI_WA_CLOE_04_05","name":"[Multiplay] Call of the Scarlet Flame [Hard] [10\/50]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d730","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_MULTI_WA_CLOE_04_01":{"iname":"GL_MULTI_WA_CLOE_04_01","name":"[Multiplay] Call of the Scarlet Flame [Hard] [1\/50]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d720","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_MULTI_WA_CLOE_04_06":{"iname":"GL_MULTI_WA_CLOE_04_06","name":"[Multiplay] Call of the Scarlet Flame [Hard] [15\/50]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d730","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_MULTI_WA_CLOE_04_07":{"iname":"GL_MULTI_WA_CLOE_04_07","name":"[Multiplay] Call of the Scarlet Flame [Hard] [25\/50]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d745","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_MULTI_WA_CLOE_04_02":{"iname":"GL_MULTI_WA_CLOE_04_02","name":"[Multiplay] Call of the Scarlet Flame [Hard] [3\/50]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d720","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_MULTI_WA_CLOE_04_03":{"iname":"GL_MULTI_WA_CLOE_04_03","name":"[Multiplay] Call of the Scarlet Flame [Hard] [5\/50]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d720","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_MULTI_WA_CLOE_04_04":{"iname":"GL_MULTI_WA_CLOE_04_04","name":"[Multiplay] Call of the Scarlet Flame [Hard] [7\/50]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d720","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_MULTI_HARVEST_3_01":{"iname":"GL_MULTI_HARVEST_3_01","name":"[Multiplay] Harvest Revelry! [Adv] [1\/5]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d73","url":"IT_CV_GOLD3.png"},{"iname":"IT_SG_POT3","name":"Gold Alchemia Pot","count":"\u00d73","url":"IT_SG_POT3.png"}]},"GL_MULTI_HARVEST_3_02":{"iname":"GL_MULTI_HARVEST_3_02","name":"[Multiplay] Harvest Revelry! [Adv] [3\/5]","item_reward":[{"iname":"IT_US_SOULTICKET","name":"Soul Encounter Ticket","count":"\u00d73","url":"IT_US_SOULTICKET.png"}]},"GL_MULTI_SABA_LV3_01":{"iname":"GL_MULTI_SABA_LV3_01","name":"[Multiplay] Lizards of the Lost Kingdom - Extra [1\/10]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d73","url":"IT_UG_APPLE4.png"},{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d73","url":"IT_CV_GOLD3.png"}]},"GL_MULTI_SABA_LV3_02":{"iname":"GL_MULTI_SABA_LV3_02","name":"[Multiplay] Lizards of the Lost Kingdom - Extra [3\/10]","item_reward":[{"iname":"IT_AF_ARMO_ARMOR_02","name":"Armor of Wrath Shard","count":"\u00d75","url":"AF_ARMO_ARMOR_02.png"}]},"GL_MULTI_SABA_LV3_03":{"iname":"GL_MULTI_SABA_LV3_03","name":"[Multiplay] Lizards of the Lost Kingdom - Extra [5\/10]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"},{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d75","url":"IT_CV_GOLD3.png"}]},"GL_MULTI_SABA_LV3_04":{"iname":"GL_MULTI_SABA_LV3_04","name":"[Multiplay] Lizards of the Lost Kingdom - Extra [7\/10]","item_reward":[{"iname":"IT_AF_ARMO_ARMOR_02","name":"Armor of Wrath Shard","count":"\u00d710","url":"AF_ARMO_ARMOR_02.png"}]},"GL_SO_MIAN_MULTI_LV2_01":{"iname":"GL_SO_MIAN_MULTI_LV2_01","name":"[Multiplay] Soul Encounter - Mianne Edition [Adv] [1\/10]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d72","url":"IT_CV_GOLD3.png"}]},"GL_SO_MIAN_MULTI_LV2_02":{"iname":"GL_SO_MIAN_MULTI_LV2_02","name":"[Multiplay] Soul Encounter - Mianne Edition [Adv] [3\/10]","item_reward":[{"iname":"IT_PI_MIANNU","name":"Mianne Soul Shard","count":"\u00d75","url":"IT_PI_MIANNU.png"}]},"GL_SO_MIAN_MULTI_LV2_03":{"iname":"GL_SO_MIAN_MULTI_LV2_03","name":"[Multiplay] Soul Encounter - Mianne Edition [Adv] [5\/10]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d73","url":"IT_CV_GOLD3.png"}]},"GL_SO_MIAN_MULTI_LV2_04":{"iname":"GL_SO_MIAN_MULTI_LV2_04","name":"[Multiplay] Soul Encounter - Mianne Edition [Adv] [7\/10]","item_reward":[{"iname":"IT_PI_MIANNU","name":"Mianne Soul Shard","count":"\u00d710","url":"IT_PI_MIANNU.png"}]},"GL_SO_MIAN_MULTI_LV0_01":{"iname":"GL_SO_MIAN_MULTI_LV0_01","name":"[Multiplay] Soul Encounter - Mianne Edition [Bgn] [1\/5]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d71","url":"IT_CV_GOLD3.png"}]},"GL_SO_MIAN_MULTI_LV0_02":{"iname":"GL_SO_MIAN_MULTI_LV0_02","name":"[Multiplay] Soul Encounter - Mianne Edition [Bgn] [3\/5]","item_reward":[{"iname":"IT_PI_MIANNU","name":"Mianne Soul Shard","count":"\u00d75","url":"IT_PI_MIANNU.png"}]},"GL_CB_POK_MASA_05":{"iname":"GL_CB_POK_MASA_05","name":"[Phantom of the Kill] Change Masamune&#39;s ability setup","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d75","url":"IT_CV_GOLD3.png"}]},"GL_CB_POK_TYR_05":{"iname":"GL_CB_POK_TYR_05","name":"[Phantom of the Kill] Change Tyrfing&#39;s ability setup","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d75","url":"IT_CV_GOLD3.png"}]},"GL_SO_MULTI_POK2_01":{"iname":"GL_SO_MULTI_POK2_01","name":"[Phantom of the Kill] Clear Phantom of the Alchemist 2 [Bgn]","item_reward":[{"iname":"IT_PI_MASAMUNE","name":"Masamune Soul Shard","count":"\u00d750","url":"IT_PI_MASAMUNE.png"}]},"GL_CB_POK_MASA_01":{"iname":"GL_CB_POK_MASA_01","name":"[Phantom of the Kill] Raise Masamune&#39;s Level to 10","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d73","url":"IT_UG_APPLE4.png"}]},"GL_CB_POK_MASA_02":{"iname":"GL_CB_POK_MASA_02","name":"[Phantom of the Kill] Raise Masamune&#39;s Level to 30","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d73","url":"IT_UG_APPLE5.png"}]},"GL_CB_POK_MASA_03":{"iname":"GL_CB_POK_MASA_03","name":"[Phantom of the Kill] Raise Masamune&#39;s Level to 50","item_reward":[{"iname":"IT_EV_RAINBOW_TSUBO","name":"Rainbow Goddess Pot","count":"\u00d71","url":"IT_EV_RAINBOW_TSUBO.png"}]},"GL_CB_POK_TYR_01":{"iname":"GL_CB_POK_TYR_01","name":"[Phantom of the Kill] Raise Tyrfing&#39;s Level to 10","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d73","url":"IT_UG_APPLE4.png"}]},"GL_CB_POK_TYR_02":{"iname":"GL_CB_POK_TYR_02","name":"[Phantom of the Kill] Raise Tyrfing&#39;s Level to 30","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d73","url":"IT_UG_APPLE5.png"}]},"GL_CB_POK_TYR_03":{"iname":"GL_CB_POK_TYR_03","name":"[Phantom of the Kill] Raise Tyrfing&#39;s Level to 50","item_reward":[{"iname":"IT_EV_RAINBOW_S_GOLEM","name":"Rainbow Soul Golem","count":"\u00d71","url":"IT_EV_RAINBOW_S_GOLEM.png"}]},"PLAYERLV_01":{"iname":"PLAYERLV_01","name":"Raise Player Level [1]","item_reward":[{"iname":"IT_UG_APPLE1","name":"Apple of Experience","count":"\u00d75","url":"IT_UG_APPLE1.png"}]},"PLAYERLV_10":{"iname":"PLAYERLV_10","name":"Raise Player Level [10]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d74","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_11":{"iname":"PLAYERLV_11","name":"Raise Player Level [11]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d74","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_12":{"iname":"PLAYERLV_12","name":"Raise Player Level [12]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d78","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_13":{"iname":"PLAYERLV_13","name":"Raise Player Level [13]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d78","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_14":{"iname":"PLAYERLV_14","name":"Raise Player Level [14]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d78","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_15":{"iname":"PLAYERLV_15","name":"Raise Player Level [15]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d78","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_16":{"iname":"PLAYERLV_16","name":"Raise Player Level [16]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"}]},"PLAYERLV_17":{"iname":"PLAYERLV_17","name":"Raise Player Level [17]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"}]},"PLAYERLV_18":{"iname":"PLAYERLV_18","name":"Raise Player Level [18]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"}]},"PLAYERLV_19":{"iname":"PLAYERLV_19","name":"Raise Player Level [19]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"}]},"PLAYERLV_02":{"iname":"PLAYERLV_02","name":"Raise Player Level [2]","item_reward":[{"iname":"IT_UG_APPLE1","name":"Apple of Experience","count":"\u00d75","url":"IT_UG_APPLE1.png"}]},"PLAYERLV_20":{"iname":"PLAYERLV_20","name":"Raise Player Level [20]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"}]},"PLAYERLV_21":{"iname":"PLAYERLV_21","name":"Raise Player Level [21]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"}]},"PLAYERLV_22":{"iname":"PLAYERLV_22","name":"Raise Player Level [22]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d75","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_23":{"iname":"PLAYERLV_23","name":"Raise Player Level [23]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d75","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_24":{"iname":"PLAYERLV_24","name":"Raise Player Level [24]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d75","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_25":{"iname":"PLAYERLV_25","name":"Raise Player Level [25]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d75","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_26":{"iname":"PLAYERLV_26","name":"Raise Player Level [26]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d75","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_27":{"iname":"PLAYERLV_27","name":"Raise Player Level [27]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d75","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_28":{"iname":"PLAYERLV_28","name":"Raise Player Level [28]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d75","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_29":{"iname":"PLAYERLV_29","name":"Raise Player Level [29]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d78","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_03":{"iname":"PLAYERLV_03","name":"Raise Player Level [3]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d72","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_30":{"iname":"PLAYERLV_30","name":"Raise Player Level [30]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d78","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_31":{"iname":"PLAYERLV_31","name":"Raise Player Level [31]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d78","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_32":{"iname":"PLAYERLV_32","name":"Raise Player Level [32]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d78","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_33":{"iname":"PLAYERLV_33","name":"Raise Player Level [33]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d78","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_34":{"iname":"PLAYERLV_34","name":"Raise Player Level [34]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d710","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_04":{"iname":"PLAYERLV_04","name":"Raise Player Level [4]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d72","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_05":{"iname":"PLAYERLV_05","name":"Raise Player Level [5]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d72","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_06":{"iname":"PLAYERLV_06","name":"Raise Player Level [6]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d72","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_07":{"iname":"PLAYERLV_07","name":"Raise Player Level [7]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d74","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_08":{"iname":"PLAYERLV_08","name":"Raise Player Level [8]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d74","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_09":{"iname":"PLAYERLV_09","name":"Raise Player Level [9]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d74","url":"IT_UG_APPLE2.png"}]},"SEISEKI_DJ_03":{"iname":"SEISEKI_DJ_03","name":"[Sacred Stone Memories] Clear Entrusted Justice - Episode 3","item_reward":[{"iname":"IT_EQ_PIECE_CANO_HAIR_ORNAMENTS","name":"Memento Hairpin Piece","count":"\u00d75","url":"IT_EQ_CANO_HAIR_ORNAMENTS.png"}]},"SEISEKI_DJ_07":{"iname":"SEISEKI_DJ_07","name":"[Sacred Stone Memories] Clear Entrusted Justice - Episode 7","item_reward":[{"iname":"IT_EQ_PIECE_CANO_SHOES","name":"Holy Order Boots Piece","count":"\u00d75","url":"IT_EQ_CANO_SHOES.png"}]},"SEISEKI_DJ_09":{"iname":"SEISEKI_DJ_09","name":"[Sacred Stone Memories] Clear Entrusted Justice - Episode 9","item_reward":[{"iname":"IT_EQ_PIECE_CANO_CLOTHES","name":"Dress of Blessings Piece","count":"\u00d710","url":"IT_EQ_CANO_CLOTHES.png"}]},"MULTI_SEISEKI_DJ_EX_01":{"iname":"MULTI_SEISEKI_DJ_EX_01","name":"[Sacred Stone Memories] Clear Entrusted Justice [Extra] [Multiplay] [1]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CADANOVA","name":"Guren Blade Shard","count":"\u00d710","url":"AF_ARMS_SWO_CADANOVA.png"}]},"MULTI_SEISEKI_DJ_EX_03":{"iname":"MULTI_SEISEKI_DJ_EX_03","name":"[Sacred Stone Memories] Clear Entrusted Justice [Extra] [Multiplay] [2]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CADANOVA","name":"Guren Blade Shard","count":"\u00d715","url":"AF_ARMS_SWO_CADANOVA.png"}]},"MULTI_SEISEKI_DJ_EX_05":{"iname":"MULTI_SEISEKI_DJ_EX_05","name":"[Sacred Stone Memories] Clear Entrusted Justice [Extra] [Multiplay] [3]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CADANOVA","name":"Guren Blade Shard","count":"\u00d725","url":"AF_ARMS_SWO_CADANOVA.png"}]},"MULTI_SEISEKI_DJ_EX_10":{"iname":"MULTI_SEISEKI_DJ_EX_10","name":"[Sacred Stone Memories] Clear Entrusted Justice [Extra] [Multiplay] [4]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CADANOVA","name":"Guren Blade Shard","count":"\u00d730","url":"AF_ARMS_SWO_CADANOVA.png"}]},"MULTI_SEISEKI_DJ_EX_15":{"iname":"MULTI_SEISEKI_DJ_EX_15","name":"[Sacred Stone Memories] Clear Entrusted Justice [Extra] [Multiplay] [5]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CADANOVA","name":"Guren Blade Shard","count":"\u00d735","url":"AF_ARMS_SWO_CADANOVA.png"}]},"MULTI_SEISEKI_DJ_EX_20":{"iname":"MULTI_SEISEKI_DJ_EX_20","name":"[Sacred Stone Memories] Clear Entrusted Justice [Extra] [Multiplay] [6]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CADANOVA","name":"Guren Blade Shard","count":"\u00d740","url":"AF_ARMS_SWO_CADANOVA.png"}]},"MULTI_SEISEKI_DJ_EX_25":{"iname":"MULTI_SEISEKI_DJ_EX_25","name":"[Sacred Stone Memories] Clear Entrusted Justice [Extra] [Multiplay] [7]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CADANOVA","name":"Guren Blade Shard","count":"\u00d745","url":"AF_ARMS_SWO_CADANOVA.png"}]},"MULTI_SEISEKI_DJ_EX_30":{"iname":"MULTI_SEISEKI_DJ_EX_30","name":"[Sacred Stone Memories] Clear Entrusted Justice [Extra] [Multiplay] [8]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CADANOVA","name":"Guren Blade Shard","count":"\u00d750","url":"AF_ARMS_SWO_CADANOVA.png"}]},"GL_SO_MIAN_LV2_05":{"iname":"GL_SO_MIAN_LV2_05","name":"Soul Encounter - Mianne Edition [Adv] [10\/25]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d710","url":"IT_UG_APPLE4.png"}]},"GL_SO_MIAN_LV2_01":{"iname":"GL_SO_MIAN_LV2_01","name":"Soul Encounter - Mianne Edition [Adv] [1\/25]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d74","url":"IT_UG_APPLE4.png"}]},"GL_SO_MIAN_LV2_06":{"iname":"GL_SO_MIAN_LV2_06","name":"Soul Encounter - Mianne Edition [Adv] [15\/25]","item_reward":[{"iname":"IT_PI_MIANNU","name":"Mianne Soul Shard","count":"\u00d715","url":"IT_PI_MIANNU.png"}]},"GL_SO_MIAN_LV2_02":{"iname":"GL_SO_MIAN_LV2_02","name":"Soul Encounter - Mianne Edition [Adv] [3\/25]","item_reward":[{"iname":"IT_PI_MIANNU","name":"Mianne Soul Shard","count":"\u00d75","url":"IT_PI_MIANNU.png"}]},"GL_SO_MIAN_LV2_03":{"iname":"GL_SO_MIAN_LV2_03","name":"Soul Encounter - Mianne Edition [Adv] [5\/25]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"}]},"GL_SO_MIAN_LV2_04":{"iname":"GL_SO_MIAN_LV2_04","name":"Soul Encounter - Mianne Edition [Adv] [7\/25]","item_reward":[{"iname":"IT_PI_MIANNU","name":"Mianne Soul Shard","count":"\u00d710","url":"IT_PI_MIANNU.png"}]},"GL_SO_MIAN_LV0_01":{"iname":"GL_SO_MIAN_LV0_01","name":"Soul Encounter - Mianne Edition [Bgn] [1\/5]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d71","url":"IT_UG_APPLE4.png"}]},"GL_SO_MIAN_LV0_02":{"iname":"GL_SO_MIAN_LV0_02","name":"Soul Encounter - Mianne Edition [Bgn] [3\/5]","item_reward":[{"iname":"IT_PI_MIANNU","name":"Mianne Soul Shard","count":"\u00d75","url":"IT_PI_MIANNU.png"}]},"GL_SO_MIAN_LV1_01":{"iname":"GL_SO_MIAN_LV1_01","name":"Soul Encounter - Mianne Edition [Int] [1\/10]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d72","url":"IT_UG_APPLE4.png"}]},"GL_SO_MIAN_LV1_02":{"iname":"GL_SO_MIAN_LV1_02","name":"Soul Encounter - Mianne Edition [Int] [3\/10]","item_reward":[{"iname":"IT_PI_MIANNU","name":"Mianne Soul Shard","count":"\u00d75","url":"IT_PI_MIANNU.png"}]},"GL_SO_MIAN_LV1_03":{"iname":"GL_SO_MIAN_LV1_03","name":"Soul Encounter - Mianne Edition [Int] [5\/10]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d73","url":"IT_UG_APPLE4.png"}]},"GL_SO_MIAN_LV1_04":{"iname":"GL_SO_MIAN_LV1_04","name":"Soul Encounter - Mianne Edition [Int] [7\/10]","item_reward":[{"iname":"IT_PI_MIANNU","name":"Mianne Soul Shard","count":"\u00d710","url":"IT_PI_MIANNU.png"}]},"RECORD_2016_XMAS_01":{"iname":"RECORD_2016_XMAS_01","name":"[Winter Holiday Special] Clear Winter Holiday Time in Babel - Episode 1 [1]","item_reward":[{"iname":"IT_CV_GOLD1","name":"Bronze Ingot","count":"\u00d71","url":"IT_CV_GOLD1.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MICH_RING","name":"Archmage Ring Diagram Piece","count":"\u00d75","url":"IT_EQ_DIAGRAM_MICH_RING.png"}]},"RECORD_2016_XMAS_02":{"iname":"RECORD_2016_XMAS_02","name":"[Winter Holiday Special] Clear Winter Holiday Time in Babel - Episode 1 [2]","item_reward":[{"iname":"IT_CV_GOLD2","name":"Silver Ingot","count":"\u00d71","url":"IT_CV_GOLD2.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MICH_RING","name":"Archmage Ring Diagram Piece","count":"\u00d710","url":"IT_EQ_DIAGRAM_MICH_RING.png"}]},"RECORD_2016_XMAS_03":{"iname":"RECORD_2016_XMAS_03","name":"[Winter Holiday Special] Clear Winter Holiday Time in Babel - Episode 1 [3]","item_reward":[{"iname":"IT_EVENT_XMAS_WREATH","name":"Ouroboros Wreath","count":"\u00d715","url":"IT_EVENT_XMAS_WREATH.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MICH_RING","name":"Archmage Ring Diagram Piece","count":"\u00d715","url":"IT_EQ_DIAGRAM_MICH_RING.png"}]},"RECORD_2016_XMAS_04":{"iname":"RECORD_2016_XMAS_04","name":"[Winter Holiday Special] Clear Winter Holiday Time in Babel - Episode 2 [1]","item_reward":[{"iname":"IT_CV_GOLD1","name":"Bronze Ingot","count":"\u00d71","url":"IT_CV_GOLD1.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MICH_GLASSES","name":"Archmage Glasses Diagram Piece","count":"\u00d75","url":"IT_EQ_DIAGRAM_MICH_GLASSES.png"}]},"RECORD_2016_XMAS_05":{"iname":"RECORD_2016_XMAS_05","name":"[Winter Holiday Special] Clear Winter Holiday Time in Babel - Episode 2 [2]","item_reward":[{"iname":"IT_CV_GOLD2","name":"Silver Ingot","count":"\u00d71","url":"IT_CV_GOLD2.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MICH_GLASSES","name":"Archmage Glasses Diagram Piece","count":"\u00d710","url":"IT_EQ_DIAGRAM_MICH_GLASSES.png"}]},"RECORD_2016_XMAS_06":{"iname":"RECORD_2016_XMAS_06","name":"[Winter Holiday Special] Clear Winter Holiday Time in Babel - Episode 2 [3]","item_reward":[{"iname":"IT_EVENT_XMAS_WREATH","name":"Ouroboros Wreath","count":"\u00d720","url":"IT_EVENT_XMAS_WREATH.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MICH_GLASSES","name":"Archmage Glasses Diagram Piece","count":"\u00d715","url":"IT_EQ_DIAGRAM_MICH_GLASSES.png"}]},"RECORD_2016_XMAS_07":{"iname":"RECORD_2016_XMAS_07","name":"[Winter Holiday Special] Clear Winter Holiday Time in Babel - Episode 3 [1]","item_reward":[{"iname":"IT_CV_GOLD1","name":"Bronze Ingot","count":"\u00d71","url":"IT_CV_GOLD1.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MICH_HAT","name":"Archmage Hat Diagram Piece","count":"\u00d75","url":"IT_EQ_DIAGRAM_MICH_HAT.png"}]},"RECORD_2016_XMAS_08":{"iname":"RECORD_2016_XMAS_08","name":"[Winter Holiday Special] Clear Winter Holiday Time in Babel - Episode 3 [2]","item_reward":[{"iname":"IT_CV_GOLD2","name":"Silver Ingot","count":"\u00d71","url":"IT_CV_GOLD2.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MICH_HAT","name":"Archmage Hat Diagram Piece","count":"\u00d710","url":"IT_EQ_DIAGRAM_MICH_HAT.png"}]},"RECORD_2016_XMAS_09":{"iname":"RECORD_2016_XMAS_09","name":"[Winter Holiday Special] Clear Winter Holiday Time in Babel - Episode 3 [3]","item_reward":[{"iname":"IT_EVENT_XMAS_WREATH","name":"Ouroboros Wreath","count":"\u00d725","url":"IT_EVENT_XMAS_WREATH.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MICH_HAT","name":"Archmage Hat Diagram Piece","count":"\u00d715","url":"IT_EQ_DIAGRAM_MICH_HAT.png"}]},"RECORD_2016_XMAS_10":{"iname":"RECORD_2016_XMAS_10","name":"[Winter Holiday Special] Clear Winter Holiday Time in Babel - Episode 4 [1]","item_reward":[{"iname":"IT_CV_GOLD1","name":"Bronze Ingot","count":"\u00d71","url":"IT_CV_GOLD1.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MICH_CLOAK","name":"Archmage Cape Diagram Piece","count":"\u00d75","url":"IT_EQ_DIAGRAM_MICH_CLOAK.png"}]},"RECORD_2016_XMAS_11":{"iname":"RECORD_2016_XMAS_11","name":"[Winter Holiday Special] Clear Winter Holiday Time in Babel - Episode 4 [2]","item_reward":[{"iname":"IT_CV_GOLD2","name":"Silver Ingot","count":"\u00d71","url":"IT_CV_GOLD2.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MICH_CLOAK","name":"Archmage Cape Diagram Piece","count":"\u00d710","url":"IT_EQ_DIAGRAM_MICH_CLOAK.png"}]},"RECORD_2016_XMAS_12":{"iname":"RECORD_2016_XMAS_12","name":"[Winter Holiday Special] Clear Winter Holiday Time in Babel - Episode 4 [3]","item_reward":[{"iname":"IT_EVENT_XMAS_WREATH","name":"Ouroboros Wreath","count":"\u00d730","url":"IT_EVENT_XMAS_WREATH.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MICH_CLOAK","name":"Archmage Cape Diagram Piece","count":"\u00d715","url":"IT_EQ_DIAGRAM_MICH_CLOAK.png"}]},"RECORD_2016_XMAS_13":{"iname":"RECORD_2016_XMAS_13","name":"[Winter Holiday Special] Clear Winter Holiday Time in Babel - Episode 5 [1]","item_reward":[{"iname":"IT_CV_GOLD1","name":"Bronze Ingot","count":"\u00d71","url":"IT_CV_GOLD1.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MICH_BOOK","name":"Archmage Book Diagram Piece","count":"\u00d75","url":"IT_EQ_DIAGRAM_MICH_BOOK.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MICH_STAFF","name":"Archmage Staff Diagram Piece","count":"\u00d75","url":"IT_EQ_DIAGRAM_MICH_STAFF.png"}]},"RECORD_2016_XMAS_14":{"iname":"RECORD_2016_XMAS_14","name":"[Winter Holiday Special] Clear Winter Holiday Time in Babel - Episode 5 [2]","item_reward":[{"iname":"IT_CV_GOLD2","name":"Silver Ingot","count":"\u00d71","url":"IT_CV_GOLD2.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MICH_BOOK","name":"Archmage Book Diagram Piece","count":"\u00d710","url":"IT_EQ_DIAGRAM_MICH_BOOK.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MICH_STAFF","name":"Archmage Staff Diagram Piece","count":"\u00d710","url":"IT_EQ_DIAGRAM_MICH_STAFF.png"}]},"RECORD_2016_XMAS_15":{"iname":"RECORD_2016_XMAS_15","name":"[Winter Holiday Special] Clear Winter Holiday Time in Babel - Episode 5 [3]","item_reward":[{"iname":"IT_EVENT_XMAS_WREATH","name":"Ouroboros Wreath","count":"\u00d750","url":"IT_EVENT_XMAS_WREATH.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MICH_BOOK","name":"Archmage Book Diagram Piece","count":"\u00d715","url":"IT_EQ_DIAGRAM_MICH_BOOK.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MICH_STAFF","name":"Archmage Staff Diagram Piece","count":"\u00d715","url":"IT_EQ_DIAGRAM_MICH_STAFF.png"}]},"RECORD_2016_XMAS_DROP04":{"iname":"RECORD_2016_XMAS_DROP04","name":"[Winter Holiday Special] Get 100 Ouroboros Wreaths in Event Quests","item_reward":[{"iname":"IT_CV_GOLD2","name":"Silver Ingot","count":"\u00d75","url":"IT_CV_GOLD2.png"},{"iname":"IT_EQ_ATK_MORNINGSTAR","name":"Morning Star","count":"\u00d73","url":"IT_EQ_ATK_MORNINGSTAR.png"},{"iname":"IT_EQ_DEF_BANGLE","name":"Copper Bangle","count":"\u00d73","url":"IT_EQ_DEF_BANGLE.png"}]},"RECORD_2016_XMAS_DROP01":{"iname":"RECORD_2016_XMAS_DROP01","name":"[Winter Holiday Special] Get 10 Ouroboros Wreaths in Event Quests","item_reward":[{"iname":"IT_CV_GOLD1","name":"Bronze Ingot","count":"\u00d73","url":"IT_CV_GOLD1.png"},{"iname":"IT_EQ_CRI_JAVELIN","name":"Javelin","count":"\u00d73","url":"IT_EQ_CRI_JAVELIN.png"},{"iname":"IT_EQ_DEF_MANTLE","name":"Leather Cape","count":"\u00d73","url":"IT_EQ_DEF_MANTLE.png"}]},"RECORD_2016_XMAS_DROP05":{"iname":"RECORD_2016_XMAS_DROP05","name":"[Winter Holiday Special] Get 200 Ouroboros Wreaths in Event Quests","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d75","url":"IT_CV_GOLD3.png"},{"iname":"IT_EQ_ATTRIBUTE_CHOKER","name":"Elemental Belt","count":"\u00d73","url":"IT_EQ_ATTRIBUTE_CHOKER.png"},{"iname":"IT_EQ_GEM_SPOON","name":"Silver Spoon","count":"\u00d73","url":"IT_EQ_GEM_SPOON.png"}]},"RECORD_2016_XMAS_DROP02":{"iname":"RECORD_2016_XMAS_DROP02","name":"[Winter Holiday Special] Get 25 Ouroboros Wreaths in Event Quests","item_reward":[{"iname":"IT_CV_GOLD1","name":"Bronze Ingot","count":"\u00d75","url":"IT_CV_GOLD1.png"},{"iname":"IT_EQ_STATUS_SMALL","name":"Picmy Figurine","count":"\u00d73","url":"IT_EQ_STATUS_SMALL.png"},{"iname":"IT_EQ_SPD_GETA","name":"Single-Tooth Geta","count":"\u00d73","url":"IT_EQ_SPD_GETA.png"}]},"RECORD_2016_XMAS_DROP03":{"iname":"RECORD_2016_XMAS_DROP03","name":"[Winter Holiday Special] Get 50 Ouroboros Wreaths in Event Quests","item_reward":[{"iname":"IT_CV_GOLD2","name":"Silver Ingot","count":"\u00d73","url":"IT_CV_GOLD2.png"},{"iname":"IT_EQ_MAG_ROD","name":"Skull Rod","count":"\u00d73","url":"IT_EQ_MAG_ROD.png"},{"iname":"IT_EQ_DEX_INK","name":"Ink Jar","count":"\u00d73","url":"IT_EQ_DEX_INK.png"}]},"GL_WINTERHOLIDAY_PRESENT":{"iname":"GL_WINTERHOLIDAY_PRESENT","name":"Winter Holiday Special Present","item_reward":[{"iname":"IT_US_MICH_TICKET","name":"Michael Summon Ticket","count":"\u00d71","url":"IT_US_RARETICKET.png"}]}}';

			return questListJson;
		}
	</script>