<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
	<div class="container">
		<div class="card z-depth-1">
			<div class="card-header ch-alt">
				<h2>Mission Daily</h2>
			</div>

			<div class="card-body card-padding">

				<style>
					.chosen-container-active .chosen-choices li.search-field input[type="text"] {
						color: #ffffff !important;
					}

					.chosen-container-multi .chosen-drop {
						top: unset !important;
					}

					.quest-list {
						max-height: 300px;
						overflow-x: hidden !important;
					}
				</style>

				<div id="quest_id_checkbox_list">
					<div class="form-group fg-line m-b-10">
						<input id="search_quest" type="text" class="fuzzy-search form-control input-lg" placeholder="Search... Quest Name, Item Name">
					</div>
					<div class="quest-list c-overflow m-b-15"></div>
				</div>

				<div class="my-btn-group m-b-20">
					<button onclick="selectQuestSelect();" type="button" class="btn btn-default">Select All</button>
					<button onclick="clearQuestSelect();" type="button" class="btn btn-default">Clear Selection</button>
				</div>

				<button id="run_button" onclick="doMission();" type="submit" class="btn btn-primary btn-block">Run</button>
			</div>
		</div>

		<div class="card z-depth-1">
			<div class="card-header ch-alt">
				<h2>Log
				</h2>
			</div>

			<div class="card-body card-padding">
				<div id="text_log" class="m-b-5">
				</div>
			</div>

		</div>

	</div>

	<script>
		$(document).ready(function() {

			const questListArray = JSON.parse(questListJson());

			let html = '';
			html = html + '<ul class="list p-l-0">';
			$.each(questListArray, function(i, quest) {

				html = html + '<li class="checkbox m-t-0"><label class="test-data">';
				html = html + `<input name="quest_id[]" id="${quest.iname}" value="${quest.iname}" type="checkbox">`;
				html = html + '<i class="input-helper"></i>';
				html = html + `<span class="quest-name">${quest.name}</span> = `;

				html = html + '<span>';
				$.each(quest.reward, function(i, reward) {
					html = html + reward + ' ';
				});
				html = html + '</span>';

				html = html + '<span class="item-name hidden">';
				$.each(quest.reward, function(i, reward) {
					html = html + reward + ' ';
				});
				if (quest.item_reward != undefined) {
					$.each(quest.item_reward, function(i, item) {
						html = html + item.name + ' ';
					});
				}
				html = html + '</span>';

				$.each(quest.item_reward, function(i, item) {
					html = html +
						`<img style="heigth:35px; width:35px;" src="http://cdn.alchemistcodedb.com/images/items/icons/${item.url}">`;
				});
				html = html + '</label></li>';

			});
			html = html + '</ul>';

			$('#mCSB_2_container').append(html);

			var monkeyList = new List('quest_id_checkbox_list', {
				valueNames: [
					'item-name',
					'quest-name',
				],
				fuzzySearch: {
					searchClass: "fuzzy-search",
					location: 0,
					distance: 200,
					threshold: 0.4,
					multiSearch: true
				}
			});

		});

		function clearQuestSelect() {
			$('input[name*="quest_id"]:checked').map(function() {
				this.checked = false;
			});
		}

		function selectQuestSelect() {
			$('input[name*="quest_id"]').map(function() {
				this.checked = true;
			});
		}

		function doMission() {

			let quest_id = [];
			quest_id = $('input[name*="quest_id"]:checked').map(function() {
				return this.value;
			}).get();

			let current_quest = 0;
			let count_quest = quest_id.length;


			function nextCompose() {

				let url = '{post_doMission}';
				let repeat_amount = $('#repeat_amount').val();

				let param = {
					'id': '{id}',
					'platform': $.cookie('platform'),
					'quest_id': quest_id[current_quest],
					'token': '{token}',
				};

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
							// ถ้าเควสผ่าน
							if (res_quest == 'success') {
								// ถ้าเควสยังไม่เท่ากับเป้าหมาย
								if (current_quest <= count_quest - 1) {
									current_quest++;

									if (current_quest <= count_quest - 1) {
										nextCompose();
									} else {
										EnButton('run_button', 'Run');
									}
								} else {

									EnButton('run_button', 'Run');
								}
							} else {
								EnButton('run_button', 'Run');
							}
						} catch (error) {
							if (res.message) {
								notify('error', res.message);
								EnButton('run_button', 'Run');
							} else {
								notify('error', 'Program Error => ' + error);
								EnButton('run_button', 'Run');
							}
						}

						function resultQuest(res) {

							let questListArray = JSON.parse(questListJson());
							try {
								if (res.stat == 0) {
									let html_log = `<p class="m-b-0">Success | ${questListArray[quest_id[current_quest]].name}</p>`;
									$('#text_log').prepend(html_log);
									return 'success';
								} else {
									notify('error', res.message);
									renderErrorLog(res.message + ' - ' + questListArray[quest_id[current_quest]].name);
									return 'wrong';
								}
							} catch (error) {
								if (res.message) {
									notify('error', res.message);
									renderErrorLog(res.message + ' - ' + questListArray[quest_id[current_quest]].name);
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

			if (quest_id == '') {
				notify('error', 'Please Select Quest');
				EnButton('run_button', 'Run');
				return false;
			} else {
				nextCompose();
			}


			// end function
		}

		function questListJson() {
			let questListJson =
				'{"DAILY_CARD_001":{"iname":"DAILY_CARD_001","item_reward":[{"count":"×3","iname":"IT_US_TICKET","name":"Skip Ticket","url":"IT_US_TICKET.png"}],"name":"Alchemist&#39;s Pact Daily Bonus","reward":["Gems:100"]},"DAILY_GL2017OCT_2_01":{"iname":"DAILY_GL2017OCT_2_01","name":"[Daily] Play a Quest [1]","reward":["AP:50","PlayerEXP:150"]},"DAILY_GL2017OCT_2_02":{"iname":"DAILY_GL2017OCT_2_02","name":"[Daily] Play a Quest [2]","reward":["AP:100","PlayerEXP:200"]},"DAILY_GL2017OCT_2_03":{"iname":"DAILY_GL2017OCT_2_03","name":"[Daily] Play a Quest [3]","reward":["Gems:20"]},"DAILY_GL2017OCT_2_04":{"iname":"DAILY_GL2017OCT_2_04","item_reward":[{"count":"×1","iname":"IT_UG_APPLE4","name":"Forbidden Apple","url":"IT_UG_APPLE4.png"}],"name":"[Daily] Play a Hard Quest [1]","reward":["AP:50"]},"DAILY_GL2017OCT_2_05":{"iname":"DAILY_GL2017OCT_2_05","item_reward":[{"count":"×1","iname":"IT_UG_APPLE4","name":"Forbidden Apple","url":"IT_UG_APPLE4.png"}],"name":"[Daily] Play a Hard Quest [2]","reward":["AP:100"]},"DAILY_GL2017OCT_2_06":{"iname":"DAILY_GL2017OCT_2_06","name":"[Daily] Play a Hard Quest [3]","reward":["Gems:20"]},"DAILY_GL2017OCT_2_07":{"iname":"DAILY_GL2017OCT_2_07","name":"[Daily] Fight in the Arena [1]","reward":["AP:50","PlayerEXP:150"]},"DAILY_GL2017OCT_2_08":{"iname":"DAILY_GL2017OCT_2_08","item_reward":[{"count":"×1","iname":"IT_CV_GOLD3","name":"Gold Ingot","url":"IT_CV_GOLD3.png"}],"name":"[Daily] Fight in the Arena [2]","reward":["AP:100","PlayerEXP:200"]},"DAILY_GL2017OCT_2_09":{"iname":"DAILY_GL2017OCT_2_09","item_reward":[{"count":"×1","iname":"IT_UG_APPLE4","name":"Forbidden Apple","url":"IT_UG_APPLE4.png"}],"name":"[Daily] Clear an Event Quest [1]"},"DAILY_GL2017OCT_2_10":{"iname":"DAILY_GL2017OCT_2_10","name":"[Daily] Clear an Event Quest [2]","reward":["Gems:10"]},"DAILY_GL2017OCT_2_11":{"iname":"DAILY_GL2017OCT_2_11","name":"[Daily] Play a Multiplay Quest","reward":["AP:250","Gems:10"]},"DAILY_GL2017OCT_2_12":{"iname":"DAILY_GL2017OCT_2_12","item_reward":[{"count":"×1","iname":"IT_CV_GOLD3","name":"Gold Ingot","url":"IT_CV_GOLD3.png"}],"name":"[Daily] Use Summoning"},"DAILY_GLCLEARALL_OCT2017":{"iname":"DAILY_GLCLEARALL_OCT2017","item_reward":[{"count":"×1","iname":"IT_KEY_QUEST_APPLE","name":"Garden Key","url":"IT_KEY_QUEST_APPLE.png"},{"count":"×1","iname":"IT_KEY_QUEST_GOLD","name":"Gold Key","url":"IT_KEY_QUEST_GOLD.png"},{"count":"×1","iname":"IT_KEY_QUEST_ORE","name":"Mine Key","url":"IT_KEY_QUEST_ORE.png"}],"name":"Clear All Daily Missions","reward":["AP:250"]},"GL_BF_DAILY_01":{"iname":"GL_BF_DAILY_01","item_reward":[{"count":"×2","iname":"IT_UG_APPLE_MGOD","name":"Metal God Apple","url":"IT_UG_APPLE_MGOD.png"}],"name":"[Daily] Brave Frontier - Level up any Unit"},"GL_BF_DAILY_02":{"iname":"GL_BF_DAILY_02","item_reward":[{"count":"×1","iname":"IT_CV_GOLD3","name":"Gold Ingot","url":"IT_CV_GOLD3.png"}],"name":"[Daily] Brave Frontier - Level up any Ability"},"GL_BF_DAILY_03":{"iname":"GL_BF_DAILY_03","name":"[Daily] Brave Frontier - Clear Ultra EXP Crossover Event","reward":["AP:250","PlayerEXP:250"]},"GL_BF_DAILY_04":{"iname":"GL_BF_DAILY_04","item_reward":[{"count":"×3","iname":"IT_PI_VARGAS","name":"Vargas Soul Shard","url":"IT_PI_VARGAS.png"}],"name":"[Daily] Brave Frontier - Defeat Vargas 3 times in Brave Frontier - Episode 2"},"GL_BF_DAILY_05":{"iname":"GL_BF_DAILY_05","name":"[Daily] Brave Frontier - Play Multi Quest 3 times","reward":["Gems:10"]},"GL_POK_DAILY_01":{"iname":"GL_POK_DAILY_01","name":"[Daily] [Phantom of the Kill] Defeat three Soldier Eldritches","reward":["PlayerEXP:100"]},"GL_POK_DAILY_02":{"iname":"GL_POK_DAILY_02","item_reward":[{"count":"×1","iname":"IT_US_GL_5STAR_SOUL_TICKET_POTK","name":"PotK 5★ Soul Shard Summon Ticket","url":"IT_US_GL_5STAR_SOUL_TICKET_POTK.png"}],"name":"[Daily] [Phantom of the Kill] Defeat five Armored Eldritches","reward":["AP:100"]},"GL_POK_DAILY_03":{"iname":"GL_POK_DAILY_03","item_reward":[{"count":"×1","iname":"IT_UG_APPLE4","name":"Forbidden Apple","url":"IT_UG_APPLE4.png"}],"name":"[Daily] [Phantom of the Kill] Clear an Event Quest once"},"GL_POK_DAILY_04":{"iname":"GL_POK_DAILY_04","item_reward":[{"count":"×1","iname":"IT_CV_GOLD3","name":"Gold Ingot","url":"IT_CV_GOLD3.png"}],"name":"[Daily] [Phantom of the Kill] Enhance an ability"},"GL_POK_DAILY_05":{"iname":"GL_POK_DAILY_05","name":"[Daily] [Phantom of the Kill] Clear Multiplay Quests once","reward":["Gems:20"]},"GL_VALENTINE_DAILY_01":{"iname":"GL_VALENTINE_DAILY_01","name":"[Daily] [Valentine&#39;s Special]  Buy any item from any Shop","reward":["AP:100","PlayerEXP:100"]},"GL_VALENTINE_DAILY_02":{"iname":"GL_VALENTINE_DAILY_02","name":"[Daily] [Valentine&#39;s Special] Raise any unit&#39;s Job Level once","reward":["AP:100","Gems:10"]},"GL_WINTERHOLIDAY_DAILY_01":{"iname":"GL_WINTERHOLIDAY_DAILY_01","name":"[Daily] Winter Holiday - Buy any item from any normal Shop","reward":["AP:100","PlayerEXP:100"]},"GL_WINTERHOLIDAY_DAILY_02":{"iname":"GL_WINTERHOLIDAY_DAILY_02","name":"[Daily] Winter Holiday - Raise any unit&#39;s Job Level","reward":["AP:100","Gems:10"]}}';

			return questListJson;
		}
	</script>