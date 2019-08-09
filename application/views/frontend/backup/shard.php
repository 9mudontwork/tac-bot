<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
	<div class="container">
		<div class="card z-depth-1">
			<div class="card-header ch-alt">
				<h2>Shard</h2>
			</div>

			<div class="card-body card-padding">
				<p class="f-16 m-b-15">Select Quest</p>
				<div id="quest_list">
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
				html = html + '<div class="col-md-1 col-sm-2 col-xs-3">';
				quest.item_reward.forEach(reward => {
					html = html + `<img class="img-responsive"src="http://cdn.alchemistcodedb.com/images/items/icons/${reward.url}">${reward.count} `;
				});
				html = html + `<label class="radio radio-inline">
												<input type="radio" name="quest_id" value="${quest.iname}">
												<i class="input-helper"></i>
											</label>`;
				html = html + '</div>';

				start++;
				if (start == 13) {
					html = html + '</div>';
					start = 1;
				}
			});
			$('#quest_list').append(html);
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
				'{"GL_GEARTUT_01":{"iname":"GL_GEARTUT_01","name":"[Beginner Gear Mission 1] Get Gear Shards!","item_reward":[{"iname":"IT_AF_ACCS_EARRING","name":"Sapphire Earrings Shard","count":"\u00d750","url":"AF_ACCS_EARRING.png"}]},"GL_GEARTUT_03":{"iname":"GL_GEARTUT_03","name":"[Beginner Gear Mission 3] Enhance Your Gear!","item_reward":[{"iname":"IT_AF_ACCS_EARRING","name":"Sapphire Earrings Shard","count":"\u00d750","url":"AF_ACCS_EARRING.png"}]},"GL_CB_BF_GL_HARD_02":{"iname":"GL_CB_BF_GL_HARD_02","name":"Brave Frontier [Hard] [3\/3]","item_reward":[{"iname":"IT_PI_SELENA","name":"Selena Soul Shard","count":"\u00d710","url":"IT_PI_SELENA.png"}]},"GL_WA_CLOE_AF_02":{"iname":"GL_WA_CLOE_AF_02","name":"Call of the Scarlet Flame - Dazzling Blade [2\/3]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d750","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_WA_CLOE_01_01":{"iname":"GL_WA_CLOE_01_01","name":"Call of the Scarlet Flame - Episode 1 [1\/3]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d710","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_WA_CLOE_02_01":{"iname":"GL_WA_CLOE_02_01","name":"Call of the Scarlet Flame - Episode 2 [1\/7]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d715","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_WA_CLOE_02_03":{"iname":"GL_WA_CLOE_02_03","name":"Call of the Scarlet Flame - Episode 2 [5\/7]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d715","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_WA_CLOE_03_05":{"iname":"GL_WA_CLOE_03_05","name":"Call of the Scarlet Flame - Episode 3 [10\/15]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d725","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_WA_CLOE_03_01":{"iname":"GL_WA_CLOE_03_01","name":"Call of the Scarlet Flame - Episode 3 [1\/15]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d725","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_WA_CLOE_03_03":{"iname":"GL_WA_CLOE_03_03","name":"Call of the Scarlet Flame - Episode 3 [5\/15]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d725","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_WA_CLOE_04_06":{"iname":"GL_WA_CLOE_04_06","name":"Call of the Scarlet Flame [Hard] [15\/25]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d745","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_WA_CLOE_04_02":{"iname":"GL_WA_CLOE_04_02","name":"Call of the Scarlet Flame [Hard] [3\/25]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d730","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_WA_CLOE_04_04":{"iname":"GL_WA_CLOE_04_04","name":"Call of the Scarlet Flame [Hard] [7\/25]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d735","url":"AF_ARMS_SWO_CLOE.png"}]},"ZOKUSEI_16_02":{"iname":"ZOKUSEI_16_02","name":"Clear [Dark - Water] Ancient Battles of Six [Adv] [2]","item_reward":[{"iname":"IT_PI_COMMON_WATER","name":"Water Soul Shard","count":"\u00d75","url":"IT_PI_COMMON_WATER.png"}]},"ZOKUSEI_15_01":{"iname":"ZOKUSEI_15_01","name":"Clear [Dark - Water] Ancient Battles of Six [Bgn]","item_reward":[{"iname":"IT_PI_COMMON_DARK","name":"Dark Soul Shard","count":"\u00d71","url":"IT_PI_COMMON_DARK.png"},{"iname":"IT_PI_COMMON_WATER","name":"Water Soul Shard","count":"\u00d71","url":"IT_PI_COMMON_WATER.png"}]},"ZOKUSEI_14_02":{"iname":"ZOKUSEI_14_02","name":"Clear [Fire - Wind] Ancient Battles of Six [Adv] [2]","item_reward":[{"iname":"IT_PI_COMMON_WIND","name":"Wind Soul Shard","count":"\u00d75","url":"IT_PI_COMMON_WIND.png"}]},"ZOKUSEI_13_01":{"iname":"ZOKUSEI_13_01","name":"Clear [Fire - Wind] Ancient Battles of Six [Bgn]","item_reward":[{"iname":"IT_PI_COMMON_FIRE","name":"Fire Soul Shard","count":"\u00d71","url":"IT_PI_COMMON_FIRE.png"},{"iname":"IT_PI_COMMON_WIND","name":"Wind Soul Shard","count":"\u00d71","url":"IT_PI_COMMON_WIND.png"}]},"ZOKUSEI_12_02":{"iname":"ZOKUSEI_12_02","name":"Clear [Light - Thunder] Ancient Battles of Six [Adv] [2]","item_reward":[{"iname":"IT_PI_COMMON_THUNDER","name":"Thunder Soul Shard","count":"\u00d75","url":"IT_PI_COMMON_THUNDER.png"}]},"ZOKUSEI_11_01":{"iname":"ZOKUSEI_11_01","name":"Clear [Light - Thunder] Ancient Battles of Six [Bgn]","item_reward":[{"iname":"IT_PI_COMMON_LIGHT","name":"Light Soul Shard","count":"\u00d71","url":"IT_PI_COMMON_LIGHT.png"},{"iname":"IT_PI_COMMON_THUNDER","name":"Thunder Soul Shard","count":"\u00d71","url":"IT_PI_COMMON_THUNDER.png"}]},"GL_BF_DAILY_04":{"iname":"GL_BF_DAILY_04","name":"[Daily] Brave Frontier - Defeat Vargas 3 times in Brave Frontier - Episode 2","item_reward":[{"iname":"IT_PI_VARGAS","name":"Vargas Soul Shard","count":"\u00d73","url":"IT_PI_VARGAS.png"}]},"GL_SO_RETZ_LV3_06":{"iname":"GL_SO_RETZ_LV3_06","name":"Encounter with Retzius [Adv] [15\/25]","item_reward":[{"iname":"IT_PI_RETZIUS","name":"Retzius Soul Shard","count":"\u00d715","url":"IT_PI_RETZIUS.png"}]},"GL_SO_RETZ_LV3_02":{"iname":"GL_SO_RETZ_LV3_02","name":"Encounter with Retzius [Adv] [3\/25]","item_reward":[{"iname":"IT_PI_RETZIUS","name":"Retzius Soul Shard","count":"\u00d75","url":"IT_PI_RETZIUS.png"}]},"GL_SO_RETZ_LV3_04":{"iname":"GL_SO_RETZ_LV3_04","name":"Encounter with Retzius [Adv] [7\/25]","item_reward":[{"iname":"IT_PI_RETZIUS","name":"Retzius Soul Shard","count":"\u00d710","url":"IT_PI_RETZIUS.png"}]},"GL_SO_RETZ_LV1_02":{"iname":"GL_SO_RETZ_LV1_02","name":"Encounter with Retzius [Bgn] [3\/5]","item_reward":[{"iname":"IT_PI_RETZIUS","name":"Retzius Soul Shard","count":"\u00d75","url":"IT_PI_RETZIUS.png"}]},"GL_SO_RETZ_LV2_02":{"iname":"GL_SO_RETZ_LV2_02","name":"Encounter with Retzius [Int] [3\/10]","item_reward":[{"iname":"IT_PI_RETZIUS","name":"Retzius Soul Shard","count":"\u00d75","url":"IT_PI_RETZIUS.png"}]},"GL_SO_RETZ_LV2_04":{"iname":"GL_SO_RETZ_LV2_04","name":"Encounter with Retzius [Int] [7\/10]","item_reward":[{"iname":"IT_PI_RETZIUS","name":"Retzius Soul Shard","count":"\u00d710","url":"IT_PI_RETZIUS.png"}]},"GL_SO_SABA_LV1_01":{"iname":"GL_SO_SABA_LV1_01","name":"Lizards of the Lost Kingdom - Episode 1 [1\/10]","item_reward":[{"iname":"IT_PI_SABALETA","name":"Sabareta Soul Shard","count":"\u00d73","url":"IT_PI_SABALETA.png"}]},"GL_SO_SABA_LV1_03":{"iname":"GL_SO_SABA_LV1_03","name":"Lizards of the Lost Kingdom - Episode 1 [5\/10]","item_reward":[{"iname":"IT_PI_SABALETA","name":"Sabareta Soul Shard","count":"\u00d75","url":"IT_PI_SABALETA.png"}]},"GL_SO_SABA_LV3_05":{"iname":"GL_SO_SABA_LV3_05","name":"Lizards of the Lost Kingdom - Episode 2 [10\/20]","item_reward":[{"iname":"IT_PI_SABALETA","name":"Sabareta Soul Shard","count":"\u00d710","url":"IT_PI_SABALETA.png"}]},"GL_SO_SABA_LV3_01":{"iname":"GL_SO_SABA_LV3_01","name":"Lizards of the Lost Kingdom - Episode 2 [1\/20]","item_reward":[{"iname":"IT_PI_SABALETA","name":"Sabareta Soul Shard","count":"\u00d75","url":"IT_PI_SABALETA.png"}]},"GL_SO_SABA_LV3_03":{"iname":"GL_SO_SABA_LV3_03","name":"Lizards of the Lost Kingdom - Episode 2 [5\/20]","item_reward":[{"iname":"IT_PI_SABALETA","name":"Sabareta Soul Shard","count":"\u00d75","url":"IT_PI_SABALETA.png"}]},"GL_SO_SABA_LV5_05":{"iname":"GL_SO_SABA_LV5_05","name":"Lizards of the Lost Kingdom - Episode 3 [Hard] [10\/30]","item_reward":[{"iname":"IT_PI_SABALETA","name":"Sabareta Soul Shard","count":"\u00d710","url":"IT_PI_SABALETA.png"}]},"GL_SO_SABA_LV5_01":{"iname":"GL_SO_SABA_LV5_01","name":"Lizards of the Lost Kingdom - Episode 3 [Hard] [1\/30]","item_reward":[{"iname":"IT_PI_SABALETA","name":"Sabareta Soul Shard","count":"\u00d710","url":"IT_PI_SABALETA.png"}]},"GL_SO_SABA_LV5_07":{"iname":"GL_SO_SABA_LV5_07","name":"Lizards of the Lost Kingdom - Episode 3 [Hard] [20\/30]","item_reward":[{"iname":"IT_PI_SABALETA","name":"Sabareta Soul Shard","count":"\u00d715","url":"IT_PI_SABALETA.png"}]},"GL_SO_SABA_LV5_03":{"iname":"GL_SO_SABA_LV5_03","name":"Lizards of the Lost Kingdom - Episode 3 [Hard] [5\/30]","item_reward":[{"iname":"IT_PI_SABALETA","name":"Sabareta Soul Shard","count":"\u00d710","url":"IT_PI_SABALETA.png"}]},"GL_SO_SABA_LV6_2_06":{"iname":"GL_SO_SABA_LV6_2_06","name":"Lizards of the Lost Kingdom [Extra] [15\/100]","item_reward":[{"iname":"IT_AF_ARMO_ARMOR_02","name":"Armor of Wrath Shard","count":"\u00d75","url":"AF_ARMO_ARMOR_02.png"}]},"GL_SO_SABA_LV6_2_08":{"iname":"GL_SO_SABA_LV6_2_08","name":"Lizards of the Lost Kingdom [Extra] [25\/100]","item_reward":[{"iname":"IT_AF_ARMO_ARMOR_02","name":"Armor of Wrath Shard","count":"\u00d710","url":"AF_ARMO_ARMOR_02.png"}]},"GL_SO_SABA_LV6_2_02":{"iname":"GL_SO_SABA_LV6_2_02","name":"Lizards of the Lost Kingdom [Extra] [3\/100]","item_reward":[{"iname":"IT_AF_ARMO_ARMOR_02","name":"Armor of Wrath Shard","count":"\u00d72","url":"AF_ARMO_ARMOR_02.png"}]},"GL_SO_SABA_LV6_2_10":{"iname":"GL_SO_SABA_LV6_2_10","name":"Lizards of the Lost Kingdom [Extra] [40\/100]","item_reward":[{"iname":"IT_AF_ARMO_ARMOR_02","name":"Armor of Wrath Shard","count":"\u00d715","url":"AF_ARMO_ARMOR_02.png"}]},"GL_SO_SABA_LV6_2_04":{"iname":"GL_SO_SABA_LV6_2_04","name":"Lizards of the Lost Kingdom [Extra] [7\/100]","item_reward":[{"iname":"IT_AF_ARMO_ARMOR_02","name":"Armor of Wrath Shard","count":"\u00d73","url":"AF_ARMO_ARMOR_02.png"}]},"GL_SO_SABA_LV6_2_12":{"iname":"GL_SO_SABA_LV6_2_12","name":"Lizards of the Lost Kingdom [Extra] [75\/100]","item_reward":[{"iname":"IT_AF_ARMO_ARMOR_02","name":"Armor of Wrath Shard","count":"\u00d725","url":"AF_ARMO_ARMOR_02.png"}]},"GL_MULTI_BF_LV2_2_01":{"iname":"GL_MULTI_BF_LV2_2_01","name":"[Multiplay] Brave Frontier - Episode 5 [1\/5]","item_reward":[{"iname":"IT_AF_ARMS_SWO_LEQSIDA","name":"Lexida Shard","count":"\u00d710","url":"AF_ARMS_SWO_LEQSIDA.png"}]},"GL_MULTI_BF_LV2_2_02":{"iname":"GL_MULTI_BF_LV2_2_02","name":"[Multiplay] Brave Frontier - Episode 5 [3\/5]","item_reward":[{"iname":"IT_PI_SELENA","name":"Selena Soul Shard","count":"\u00d75","url":"IT_PI_SELENA.png"}]},"GL_MULTI_BF_LV2_3_01":{"iname":"GL_MULTI_BF_LV2_3_01","name":"[Multiplay] Brave Frontier - Episode 6 [1\/10]","item_reward":[{"iname":"IT_AF_ARMS_SWO_LEQSIDA","name":"Lexida Shard","count":"\u00d715","url":"AF_ARMS_SWO_LEQSIDA.png"}]},"GL_MULTI_BF_LV2_3_02":{"iname":"GL_MULTI_BF_LV2_3_02","name":"[Multiplay] Brave Frontier - Episode 6 [3\/10]","item_reward":[{"iname":"IT_PI_SELENA","name":"Selena Soul Shard","count":"\u00d75","url":"IT_PI_SELENA.png"}]},"GL_MULTI_BF_LV2_3_03":{"iname":"GL_MULTI_BF_LV2_3_03","name":"[Multiplay] Brave Frontier - Episode 6 [5\/10]","item_reward":[{"iname":"IT_AF_ARMS_SWO_LEQSIDA","name":"Lexida Shard","count":"\u00d715","url":"AF_ARMS_SWO_LEQSIDA.png"}]},"GL_MULTI_BF_LV2_3_04":{"iname":"GL_MULTI_BF_LV2_3_04","name":"[Multiplay] Brave Frontier - Episode 6 [7\/10]","item_reward":[{"iname":"IT_PI_SELENA","name":"Selena Soul Shard","count":"\u00d710","url":"IT_PI_SELENA.png"}]},"GL_MULTI_WA_CLOE_04_05":{"iname":"GL_MULTI_WA_CLOE_04_05","name":"[Multiplay] Call of the Scarlet Flame [Hard] [10\/50]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d730","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_MULTI_WA_CLOE_04_01":{"iname":"GL_MULTI_WA_CLOE_04_01","name":"[Multiplay] Call of the Scarlet Flame [Hard] [1\/50]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d720","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_MULTI_WA_CLOE_04_06":{"iname":"GL_MULTI_WA_CLOE_04_06","name":"[Multiplay] Call of the Scarlet Flame [Hard] [15\/50]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d730","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_MULTI_WA_CLOE_04_07":{"iname":"GL_MULTI_WA_CLOE_04_07","name":"[Multiplay] Call of the Scarlet Flame [Hard] [25\/50]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d745","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_MULTI_WA_CLOE_04_02":{"iname":"GL_MULTI_WA_CLOE_04_02","name":"[Multiplay] Call of the Scarlet Flame [Hard] [3\/50]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d720","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_MULTI_WA_CLOE_04_03":{"iname":"GL_MULTI_WA_CLOE_04_03","name":"[Multiplay] Call of the Scarlet Flame [Hard] [5\/50]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d720","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_MULTI_WA_CLOE_04_04":{"iname":"GL_MULTI_WA_CLOE_04_04","name":"[Multiplay] Call of the Scarlet Flame [Hard] [7\/50]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CLOE","name":"Dazzling Blade Shard","count":"\u00d720","url":"AF_ARMS_SWO_CLOE.png"}]},"GL_MULTI_SABA_LV3_02":{"iname":"GL_MULTI_SABA_LV3_02","name":"[Multiplay] Lizards of the Lost Kingdom - Extra [3\/10]","item_reward":[{"iname":"IT_AF_ARMO_ARMOR_02","name":"Armor of Wrath Shard","count":"\u00d75","url":"AF_ARMO_ARMOR_02.png"}]},"GL_MULTI_SABA_LV3_04":{"iname":"GL_MULTI_SABA_LV3_04","name":"[Multiplay] Lizards of the Lost Kingdom - Extra [7\/10]","item_reward":[{"iname":"IT_AF_ARMO_ARMOR_02","name":"Armor of Wrath Shard","count":"\u00d710","url":"AF_ARMO_ARMOR_02.png"}]},"GL_SO_MIAN_MULTI_LV2_02":{"iname":"GL_SO_MIAN_MULTI_LV2_02","name":"[Multiplay] Soul Encounter - Mianne Edition [Adv] [3\/10]","item_reward":[{"iname":"IT_PI_MIANNU","name":"Mianne Soul Shard","count":"\u00d75","url":"IT_PI_MIANNU.png"}]},"GL_SO_MIAN_MULTI_LV2_04":{"iname":"GL_SO_MIAN_MULTI_LV2_04","name":"[Multiplay] Soul Encounter - Mianne Edition [Adv] [7\/10]","item_reward":[{"iname":"IT_PI_MIANNU","name":"Mianne Soul Shard","count":"\u00d710","url":"IT_PI_MIANNU.png"}]},"GL_SO_MIAN_MULTI_LV0_02":{"iname":"GL_SO_MIAN_MULTI_LV0_02","name":"[Multiplay] Soul Encounter - Mianne Edition [Bgn] [3\/5]","item_reward":[{"iname":"IT_PI_MIANNU","name":"Mianne Soul Shard","count":"\u00d75","url":"IT_PI_MIANNU.png"}]},"GL_SO_MULTI_POK2_01":{"iname":"GL_SO_MULTI_POK2_01","name":"[Phantom of the Kill] Clear Phantom of the Alchemist 2 [Bgn]","item_reward":[{"iname":"IT_PI_MASAMUNE","name":"Masamune Soul Shard","count":"\u00d750","url":"IT_PI_MASAMUNE.png"}]},"MULTI_SEISEKI_DJ_EX_01":{"iname":"MULTI_SEISEKI_DJ_EX_01","name":"[Sacred Stone Memories] Clear Entrusted Justice [Extra] [Multiplay] [1]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CADANOVA","name":"Guren Blade Shard","count":"\u00d710","url":"AF_ARMS_SWO_CADANOVA.png"}]},"MULTI_SEISEKI_DJ_EX_03":{"iname":"MULTI_SEISEKI_DJ_EX_03","name":"[Sacred Stone Memories] Clear Entrusted Justice [Extra] [Multiplay] [2]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CADANOVA","name":"Guren Blade Shard","count":"\u00d715","url":"AF_ARMS_SWO_CADANOVA.png"}]},"MULTI_SEISEKI_DJ_EX_05":{"iname":"MULTI_SEISEKI_DJ_EX_05","name":"[Sacred Stone Memories] Clear Entrusted Justice [Extra] [Multiplay] [3]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CADANOVA","name":"Guren Blade Shard","count":"\u00d725","url":"AF_ARMS_SWO_CADANOVA.png"}]},"MULTI_SEISEKI_DJ_EX_10":{"iname":"MULTI_SEISEKI_DJ_EX_10","name":"[Sacred Stone Memories] Clear Entrusted Justice [Extra] [Multiplay] [4]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CADANOVA","name":"Guren Blade Shard","count":"\u00d730","url":"AF_ARMS_SWO_CADANOVA.png"}]},"MULTI_SEISEKI_DJ_EX_15":{"iname":"MULTI_SEISEKI_DJ_EX_15","name":"[Sacred Stone Memories] Clear Entrusted Justice [Extra] [Multiplay] [5]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CADANOVA","name":"Guren Blade Shard","count":"\u00d735","url":"AF_ARMS_SWO_CADANOVA.png"}]},"MULTI_SEISEKI_DJ_EX_20":{"iname":"MULTI_SEISEKI_DJ_EX_20","name":"[Sacred Stone Memories] Clear Entrusted Justice [Extra] [Multiplay] [6]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CADANOVA","name":"Guren Blade Shard","count":"\u00d740","url":"AF_ARMS_SWO_CADANOVA.png"}]},"MULTI_SEISEKI_DJ_EX_25":{"iname":"MULTI_SEISEKI_DJ_EX_25","name":"[Sacred Stone Memories] Clear Entrusted Justice [Extra] [Multiplay] [7]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CADANOVA","name":"Guren Blade Shard","count":"\u00d745","url":"AF_ARMS_SWO_CADANOVA.png"}]},"MULTI_SEISEKI_DJ_EX_30":{"iname":"MULTI_SEISEKI_DJ_EX_30","name":"[Sacred Stone Memories] Clear Entrusted Justice [Extra] [Multiplay] [8]","item_reward":[{"iname":"IT_AF_ARMS_SWO_CADANOVA","name":"Guren Blade Shard","count":"\u00d750","url":"AF_ARMS_SWO_CADANOVA.png"}]},"GL_SO_MIAN_LV2_06":{"iname":"GL_SO_MIAN_LV2_06","name":"Soul Encounter - Mianne Edition [Adv] [15\/25]","item_reward":[{"iname":"IT_PI_MIANNU","name":"Mianne Soul Shard","count":"\u00d715","url":"IT_PI_MIANNU.png"}]},"GL_SO_MIAN_LV2_02":{"iname":"GL_SO_MIAN_LV2_02","name":"Soul Encounter - Mianne Edition [Adv] [3\/25]","item_reward":[{"iname":"IT_PI_MIANNU","name":"Mianne Soul Shard","count":"\u00d75","url":"IT_PI_MIANNU.png"}]},"GL_SO_MIAN_LV2_04":{"iname":"GL_SO_MIAN_LV2_04","name":"Soul Encounter - Mianne Edition [Adv] [7\/25]","item_reward":[{"iname":"IT_PI_MIANNU","name":"Mianne Soul Shard","count":"\u00d710","url":"IT_PI_MIANNU.png"}]},"GL_SO_MIAN_LV0_02":{"iname":"GL_SO_MIAN_LV0_02","name":"Soul Encounter - Mianne Edition [Bgn] [3\/5]","item_reward":[{"iname":"IT_PI_MIANNU","name":"Mianne Soul Shard","count":"\u00d75","url":"IT_PI_MIANNU.png"}]},"GL_SO_MIAN_LV1_02":{"iname":"GL_SO_MIAN_LV1_02","name":"Soul Encounter - Mianne Edition [Int] [3\/10]","item_reward":[{"iname":"IT_PI_MIANNU","name":"Mianne Soul Shard","count":"\u00d75","url":"IT_PI_MIANNU.png"}]},"GL_SO_MIAN_LV1_04":{"iname":"GL_SO_MIAN_LV1_04","name":"Soul Encounter - Mianne Edition [Int] [7\/10]","item_reward":[{"iname":"IT_PI_MIANNU","name":"Mianne Soul Shard","count":"\u00d710","url":"IT_PI_MIANNU.png"}]}}';

			return questListJson;
		}
	</script>