<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
	<div class="container">
		<div class="card z-depth-1">
			<div class="card-header ch-alt">
				<h2>Apple</h2>
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
			$.each(questListArray, function(i, item) {
				if (start == 1) {
					html = html + '<div class="row">';
				}
				html = html +
					`
							<div class="col-md-1 col-sm-2 col-xs-3">
									<img class="img-responsive"src="http://cdn.alchemistcodedb.com/images/items/icons/${item.item_reward[0].url}">
									<label class="radio radio-inline">
										<input type="radio" name="quest_id" value="${item.iname}">
										<i class="input-helper"></i>
										${item.item_reward[0].count}
									</label>
							</div>
						`;
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
				'{"GL_WA_CLOE_02_02":{"iname":"GL_WA_CLOE_02_02","name":"Call of the Scarlet Flame - Episode 2 [3\/7]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d71","url":"IT_UG_APPLE4.png"}]},"GL_WA_CLOE_03_04":{"iname":"GL_WA_CLOE_03_04","name":"Call of the Scarlet Flame - Episode 3 [7\/15]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d73","url":"IT_UG_APPLE4.png"}]},"GL_WA_CLOE_04_03":{"iname":"GL_WA_CLOE_04_03","name":"Call of the Scarlet Flame [Hard] [5\/25]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"}]},"GL_BF_DAILY_01":{"iname":"GL_BF_DAILY_01","name":"[Daily] Brave Frontier - Level up any Unit","item_reward":[{"iname":"IT_UG_APPLE_MGOD","name":"Metal God Apple","count":"\u00d72","url":"IT_UG_APPLE_MGOD.png"}]},"DAILY_GL2017OCT_2_09":{"iname":"DAILY_GL2017OCT_2_09","name":"[Daily] Clear an Event Quest [1]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d71","url":"IT_UG_APPLE4.png"}]},"GL_POK_DAILY_03":{"iname":"GL_POK_DAILY_03","name":"[Daily] [Phantom of the Kill] Clear an Event Quest once","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d71","url":"IT_UG_APPLE4.png"}]},"GL_SO_RETZ_LV3_03":{"iname":"GL_SO_RETZ_LV3_03","name":"Encounter with Retzius [Adv] [5\/25]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"}]},"GL_SO_RETZ_LV1_01":{"iname":"GL_SO_RETZ_LV1_01","name":"Encounter with Retzius [Bgn] [1\/5]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d71","url":"IT_UG_APPLE4.png"}]},"GL_SO_RETZ_LV2_03":{"iname":"GL_SO_RETZ_LV2_03","name":"Encounter with Retzius [Int] [5\/10]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d73","url":"IT_UG_APPLE4.png"}]},"GL_SO_SABA_LV1_02":{"iname":"GL_SO_SABA_LV1_02","name":"Lizards of the Lost Kingdom - Episode 1 [3\/10]","item_reward":[{"iname":"IT_UG_APPLE3","name":"Apple of Accomplishment","count":"\u00d75","url":"IT_UG_APPLE3.png"}]},"GL_SO_SABA_LV3_06":{"iname":"GL_SO_SABA_LV3_06","name":"Lizards of the Lost Kingdom - Episode 2 [15\/20]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d73","url":"IT_UG_APPLE4.png"}]},"GL_SO_SABA_LV3_02":{"iname":"GL_SO_SABA_LV3_02","name":"Lizards of the Lost Kingdom - Episode 2 [3\/20]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d72","url":"IT_UG_APPLE4.png"}]},"GL_SO_SABA_LV5_04":{"iname":"GL_SO_SABA_LV5_04","name":"Lizards of the Lost Kingdom - Episode 3 [Hard] [7\/30]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"}]},"GL_CB_POK_MASA_01":{"iname":"GL_CB_POK_MASA_01","name":"[Phantom of the Kill] Raise Masamune&#39;s Level to 10","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d73","url":"IT_UG_APPLE4.png"}]},"GL_CB_POK_MASA_02":{"iname":"GL_CB_POK_MASA_02","name":"[Phantom of the Kill] Raise Masamune&#39;s Level to 30","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d73","url":"IT_UG_APPLE5.png"}]},"GL_CB_POK_TYR_01":{"iname":"GL_CB_POK_TYR_01","name":"[Phantom of the Kill] Raise Tyrfing&#39;s Level to 10","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d73","url":"IT_UG_APPLE4.png"}]},"GL_CB_POK_TYR_02":{"iname":"GL_CB_POK_TYR_02","name":"[Phantom of the Kill] Raise Tyrfing&#39;s Level to 30","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d73","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_01":{"iname":"PLAYERLV_01","name":"Raise Player Level [1]","item_reward":[{"iname":"IT_UG_APPLE1","name":"Apple of Experience","count":"\u00d75","url":"IT_UG_APPLE1.png"}]},"PLAYERLV_10":{"iname":"PLAYERLV_10","name":"Raise Player Level [10]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d74","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_11":{"iname":"PLAYERLV_11","name":"Raise Player Level [11]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d74","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_12":{"iname":"PLAYERLV_12","name":"Raise Player Level [12]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d78","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_13":{"iname":"PLAYERLV_13","name":"Raise Player Level [13]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d78","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_14":{"iname":"PLAYERLV_14","name":"Raise Player Level [14]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d78","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_15":{"iname":"PLAYERLV_15","name":"Raise Player Level [15]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d78","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_16":{"iname":"PLAYERLV_16","name":"Raise Player Level [16]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"}]},"PLAYERLV_17":{"iname":"PLAYERLV_17","name":"Raise Player Level [17]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"}]},"PLAYERLV_18":{"iname":"PLAYERLV_18","name":"Raise Player Level [18]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"}]},"PLAYERLV_19":{"iname":"PLAYERLV_19","name":"Raise Player Level [19]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"}]},"PLAYERLV_02":{"iname":"PLAYERLV_02","name":"Raise Player Level [2]","item_reward":[{"iname":"IT_UG_APPLE1","name":"Apple of Experience","count":"\u00d75","url":"IT_UG_APPLE1.png"}]},"PLAYERLV_20":{"iname":"PLAYERLV_20","name":"Raise Player Level [20]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"}]},"PLAYERLV_21":{"iname":"PLAYERLV_21","name":"Raise Player Level [21]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"}]},"PLAYERLV_22":{"iname":"PLAYERLV_22","name":"Raise Player Level [22]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d75","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_23":{"iname":"PLAYERLV_23","name":"Raise Player Level [23]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d75","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_24":{"iname":"PLAYERLV_24","name":"Raise Player Level [24]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d75","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_25":{"iname":"PLAYERLV_25","name":"Raise Player Level [25]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d75","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_26":{"iname":"PLAYERLV_26","name":"Raise Player Level [26]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d75","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_27":{"iname":"PLAYERLV_27","name":"Raise Player Level [27]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d75","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_28":{"iname":"PLAYERLV_28","name":"Raise Player Level [28]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d75","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_29":{"iname":"PLAYERLV_29","name":"Raise Player Level [29]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d78","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_03":{"iname":"PLAYERLV_03","name":"Raise Player Level [3]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d72","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_30":{"iname":"PLAYERLV_30","name":"Raise Player Level [30]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d78","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_31":{"iname":"PLAYERLV_31","name":"Raise Player Level [31]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d78","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_32":{"iname":"PLAYERLV_32","name":"Raise Player Level [32]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d78","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_33":{"iname":"PLAYERLV_33","name":"Raise Player Level [33]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d78","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_34":{"iname":"PLAYERLV_34","name":"Raise Player Level [34]","item_reward":[{"iname":"IT_UG_APPLE5","name":"Crystal Apple","count":"\u00d710","url":"IT_UG_APPLE5.png"}]},"PLAYERLV_04":{"iname":"PLAYERLV_04","name":"Raise Player Level [4]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d72","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_05":{"iname":"PLAYERLV_05","name":"Raise Player Level [5]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d72","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_06":{"iname":"PLAYERLV_06","name":"Raise Player Level [6]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d72","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_07":{"iname":"PLAYERLV_07","name":"Raise Player Level [7]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d74","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_08":{"iname":"PLAYERLV_08","name":"Raise Player Level [8]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d74","url":"IT_UG_APPLE2.png"}]},"PLAYERLV_09":{"iname":"PLAYERLV_09","name":"Raise Player Level [9]","item_reward":[{"iname":"IT_UG_APPLE2","name":"Apple of Skill","count":"\u00d74","url":"IT_UG_APPLE2.png"}]},"GL_SO_MIAN_LV2_05":{"iname":"GL_SO_MIAN_LV2_05","name":"Soul Encounter - Mianne Edition [Adv] [10\/25]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d710","url":"IT_UG_APPLE4.png"}]},"GL_SO_MIAN_LV2_01":{"iname":"GL_SO_MIAN_LV2_01","name":"Soul Encounter - Mianne Edition [Adv] [1\/25]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d74","url":"IT_UG_APPLE4.png"}]},"GL_SO_MIAN_LV2_03":{"iname":"GL_SO_MIAN_LV2_03","name":"Soul Encounter - Mianne Edition [Adv] [5\/25]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d75","url":"IT_UG_APPLE4.png"}]},"GL_SO_MIAN_LV0_01":{"iname":"GL_SO_MIAN_LV0_01","name":"Soul Encounter - Mianne Edition [Bgn] [1\/5]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d71","url":"IT_UG_APPLE4.png"}]},"GL_SO_MIAN_LV1_01":{"iname":"GL_SO_MIAN_LV1_01","name":"Soul Encounter - Mianne Edition [Int] [1\/10]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d72","url":"IT_UG_APPLE4.png"}]},"GL_SO_MIAN_LV1_03":{"iname":"GL_SO_MIAN_LV1_03","name":"Soul Encounter - Mianne Edition [Int] [5\/10]","item_reward":[{"iname":"IT_UG_APPLE4","name":"Forbidden Apple","count":"\u00d73","url":"IT_UG_APPLE4.png"}]}}';

			return questListJson;
		}
	</script>