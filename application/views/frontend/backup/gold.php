<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
	<div class="container">
		<div class="card z-depth-1">
			<div class="card-header ch-alt">
				<h2>Gold</h2>
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
					html = html + '<div class="row">';
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
				'{"GL_WA_CLOE_03_02":{"iname":"GL_WA_CLOE_03_02","name":"Call of the Scarlet Flame - Episode 3 [3\/15]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d71","url":"IT_CV_GOLD3.png"}]},"GL_WA_CLOE_04_05":{"iname":"GL_WA_CLOE_04_05","name":"Call of the Scarlet Flame [Hard] [10\/25]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d75","url":"IT_CV_GOLD3.png"}]},"GL_WA_CLOE_04_01":{"iname":"GL_WA_CLOE_04_01","name":"Call of the Scarlet Flame [Hard] [1\/25]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d73","url":"IT_CV_GOLD3.png"}]},"GL_BF_DAILY_02":{"iname":"GL_BF_DAILY_02","name":"[Daily] Brave Frontier - Level up any Ability","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d71","url":"IT_CV_GOLD3.png"}]},"GL_POK_DAILY_04":{"iname":"GL_POK_DAILY_04","name":"[Daily] [Phantom of the Kill] Enhance an ability","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d71","url":"IT_CV_GOLD3.png"}]},"DAILY_GL2017OCT_2_12":{"iname":"DAILY_GL2017OCT_2_12","name":"[Daily] Use Summoning","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d71","url":"IT_CV_GOLD3.png"}]},"GL_SO_RETZ_LV3_05":{"iname":"GL_SO_RETZ_LV3_05","name":"Encounter with Retzius [Adv] [10\/25]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d75","url":"IT_CV_GOLD3.png"}]},"GL_SO_RETZ_LV3_01":{"iname":"GL_SO_RETZ_LV3_01","name":"Encounter with Retzius [Adv] [1\/25]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d73","url":"IT_CV_GOLD3.png"}]},"GL_SO_RETZ_LV2_01":{"iname":"GL_SO_RETZ_LV2_01","name":"Encounter with Retzius [Int] [1\/10]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d71","url":"IT_CV_GOLD3.png"}]},"GL_HARVEST_3_01":{"iname":"GL_HARVEST_3_01","name":"Harvest Revelry! [Adv] [1\/10]","item_reward":[{"iname":"IT_SG_POT3","name":"Gold Alchemia Pot","count":"\u00d75","url":"IT_SG_POT3.png"}]},"GL_HARVEST_2_02":{"iname":"GL_HARVEST_2_02","name":"Harvest Revelry! [Int] [5\/10]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d73","url":"IT_CV_GOLD3.png"}]},"GL_SO_SABA_LV3_04":{"iname":"GL_SO_SABA_LV3_04","name":"Lizards of the Lost Kingdom - Episode 2 [7\/20]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d72","url":"IT_CV_GOLD3.png"}]},"GL_SO_SABA_LV5_06":{"iname":"GL_SO_SABA_LV5_06","name":"Lizards of the Lost Kingdom - Episode 3 [Hard] [15\/30]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d75","url":"IT_CV_GOLD3.png"}]},"GL_SO_SABA_LV5_02":{"iname":"GL_SO_SABA_LV5_02","name":"Lizards of the Lost Kingdom - Episode 3 [Hard] [3\/30]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d73","url":"IT_CV_GOLD3.png"}]},"GL_MULTI_HARVEST_3_01":{"iname":"GL_MULTI_HARVEST_3_01","name":"[Multiplay] Harvest Revelry! [Adv] [1\/5]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d73","url":"IT_CV_GOLD3.png"},{"iname":"IT_SG_POT3","name":"Gold Alchemia Pot","count":"\u00d73","url":"IT_SG_POT3.png"}]},"GL_SO_MIAN_MULTI_LV2_01":{"iname":"GL_SO_MIAN_MULTI_LV2_01","name":"[Multiplay] Soul Encounter - Mianne Edition [Adv] [1\/10]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d72","url":"IT_CV_GOLD3.png"}]},"GL_SO_MIAN_MULTI_LV2_03":{"iname":"GL_SO_MIAN_MULTI_LV2_03","name":"[Multiplay] Soul Encounter - Mianne Edition [Adv] [5\/10]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d73","url":"IT_CV_GOLD3.png"}]},"GL_SO_MIAN_MULTI_LV0_01":{"iname":"GL_SO_MIAN_MULTI_LV0_01","name":"[Multiplay] Soul Encounter - Mianne Edition [Bgn] [1\/5]","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d71","url":"IT_CV_GOLD3.png"}]},"GL_CB_POK_MASA_05":{"iname":"GL_CB_POK_MASA_05","name":"[Phantom of the Kill] Change Masamune&#39;s ability setup","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d75","url":"IT_CV_GOLD3.png"}]},"GL_CB_POK_TYR_05":{"iname":"GL_CB_POK_TYR_05","name":"[Phantom of the Kill] Change Tyrfing&#39;s ability setup","item_reward":[{"iname":"IT_CV_GOLD3","name":"Gold Ingot","count":"\u00d75","url":"IT_CV_GOLD3.png"}]}}';

			return questListJson;
		}
	</script>