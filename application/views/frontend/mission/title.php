<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
	<div class="container">
		<div class="card z-depth-1">
			<div class="card-header ch-alt">
				<h2>Mission Title</h2>
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

				<button id="run_button" onclick="doQuestNormalHard();" type="submit" class="btn btn-primary btn-block">Run</button>
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

				html = html + `<span>${quest.reward[0]}</span>`;
				html = html + `<span class="item-name hidden">${quest.reward[0]}</span>`;

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

		function doQuestNormalHard() {

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
					'date_buy_bot': '{date_buy_bot}',
					'bot_day': '{bot_day}',
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
				'{"AWARD_RECORD_00001_00002":{"iname":"AWARD_RECORD_00001_00002","name":"Starting Title Gift! [1]","reward":["AmateurAlchemist","Titleawardedtobeginnerplayers</p><p>CharacterLover","Titleawardedtoplayerswholovetheircharacters"]},"AWARD_RECORD_00003_00004":{"iname":"AWARD_RECORD_00003_00004","name":"Starting Title Gift! [2]","reward":["BattleLover","Titleawardedtoplayerswholovebattling</p><p>HighCaliberStrategist","Titleawardedtoplayerswhocleardifficultquests"]},"AWARD_RECORD_02001":{"iname":"AWARD_RECORD_02001","name":"Raise any unit to Lv60","reward":["JourneymanAlchemist","Titleawardedtoplayerswhohaveraisedaunit&#39;slevelto60"]},"AWARD_RECORD_02002":{"iname":"AWARD_RECORD_02002","name":"Raise a unit to Lv75","reward":["CompetentAlchemist","Titleawardedtoplayerswhohaveraisedaunit&#39;slevelto75"]},"AWARD_RECORD_02003":{"iname":"AWARD_RECORD_02003","name":"Raise a unit to Lv85","reward":["UpandComingAlchemist","Titleawardedtoplayerswhohaveraisedaunit&#39;slevelto85"]},"AWARD_RECORD_03001":{"iname":"AWARD_RECORD_03001","name":"Evolve gear 10 times","reward":["ApprenticeBlacksmith","Titleawardedtoplayerswhohaveevolvedequipment10times"]},"AWARD_RECORD_03002":{"iname":"AWARD_RECORD_03002","name":"Evolve gear 30 times","reward":["NoviceBlacksmith","Titleawardedtoplayerswhohaveevolvedequipment30times"]},"AWARD_RECORD_03003":{"iname":"AWARD_RECORD_03003","name":"Evolve gear 50 times","reward":["VeteranBlacksmith","Titleawardedtoplayerswhohaveevolvedequipment50times"]},"AWARD_RECORD_04001":{"iname":"AWARD_RECORD_04001","name":"Clear Chapter 1: Episode 5 [3-10]","reward":["ConnectorofHearts","TitleawardedtoplayerswhohaveclearedChapter1:Episode5[3-10]"]},"AWARD_RECORD_04002":{"iname":"AWARD_RECORD_04002","name":"Clear Chapter 2: Episode 4 [3-10]","reward":["BuddyofDandies","TitleawardedtoplayerswhohaveclearedChapter2:Episode4[3-10]"]},"AWARD_RECORD_04003":{"iname":"AWARD_RECORD_04003","name":"Clear Story Quests 100 times","reward":["StoryRepeater","TitleawardedtoplayerswhohaveclearedStoryQuests100times"]},"AWARD_RECORD_04004":{"iname":"AWARD_RECORD_04004","name":"Clear Story Quests 500 times","reward":["StoryManiac","TitleawardedtoplayerswhohaveclearedStoryQuests500times"]},"AWARD_RECORD_04005":{"iname":"AWARD_RECORD_04005","name":"Clear Story Quests 1000 times","reward":["Storyholic","TitleawardedtoplayerswhohaveclearedStoryQuests1000times"]},"AWARD_RECORD_04006":{"iname":"AWARD_RECORD_04006","name":"Clear Story Quests 5000 times","reward":["StoryMaster","TitleawardedtoplayerswhohaveclearedStoryQuests5000times"]},"AWARD_RECORD_04007":{"iname":"AWARD_RECORD_04007","name":"Clear Hard Quests 100 times","reward":["HardWalker","TitleawardedtoplayerswhohaveclearedHardQuests100times"]},"AWARD_RECORD_04008":{"iname":"AWARD_RECORD_04008","name":"Clear Hard Quests 500 times","reward":["HardJogger","TitleawardedtoplayerswhohaveclearedHardQuests500times"]},"AWARD_RECORD_04009":{"iname":"AWARD_RECORD_04009","name":"Clear Hard Quests 1000 times","reward":["HardRunner","TitleawardedtoplayerswhohaveclearedHardQuests1000times"]},"AWARD_RECORD_04010":{"iname":"AWARD_RECORD_04010","name":"Clear Hard Quests 5000 times","reward":["HardAthlete","TitleawardedtoplayerswhohaveclearedHardQuests5000times"]},"AWARD_RECORD_05001":{"iname":"AWARD_RECORD_05001","name":"Clear Event Quests 100 times","reward":["AmateurAdventurer","TitleawardedtoplayerswhohaveclearedEventQuests100times"]},"AWARD_RECORD_05002":{"iname":"AWARD_RECORD_05002","name":"Clear Event Quests 500 times","reward":["UpcomingAdventurer","TitleawardedtoplayerswhohaveclearedEventQuests500times"]},"AWARD_RECORD_05003":{"iname":"AWARD_RECORD_05003","name":"Clear Event Quests 1000 times","reward":["HardenedAdventurer","TitleawardedtoplayerswhohaveclearedEventQuests1000times"]},"AWARD_RECORD_05004":{"iname":"AWARD_RECORD_05004","name":"Clear Event Quests 5000 times","reward":["FamousAdventurer","TitleawardedtoplayerswhohaveclearedEventQuests5000times"]},"AWARD_RECORD_06001":{"iname":"AWARD_RECORD_06001","name":"Clear Multiplay Quests 100 times","reward":["MultiplaySergeant","TitleawardedtoplayerswhohaveclearedMultiplayQuests50times"]},"AWARD_RECORD_06002":{"iname":"AWARD_RECORD_06002","name":"Clear Multiplay Quests 500 times","reward":["MultiplayOfficer","TitleawardedtoplayerswhohaveclearedMultiplayQuests100times"]},"AWARD_RECORD_06003":{"iname":"AWARD_RECORD_06003","name":"Clear Multiplay Quests 1000 times","reward":["MultiplayCaptain","TitleawardedtoplayerswhohaveclearedMultiplayQuests500times"]},"AWARD_RECORD_06004":{"iname":"AWARD_RECORD_06004","name":"Clear Multiplay Quests 5000 times","reward":["MultiplayGeneral","TitleawardedtoplayerswhohaveclearedMultiplayQuests1000times"]},"AWARD_RECORD_07001":{"iname":"AWARD_RECORD_07001","name":"Win 50 battles in the Arena","reward":["ArenaRisingStar","Titleawardedtoplayerswhohavewon50battlesintheArena"]},"AWARD_RECORD_07002":{"iname":"AWARD_RECORD_07002","name":"Win 100 battles in the Arena","reward":["ArenaHotShot","Titleawardedtoplayerswhohavewon100battlesintheArena"]},"AWARD_RECORD_07003":{"iname":"AWARD_RECORD_07003","name":"Win 500 battles in the Arena","reward":["ArenaFavorite","Titleawardedtoplayerswhohavewon500battlesintheArena"]},"AWARD_RECORD_07004":{"iname":"AWARD_RECORD_07004","name":"Win 1000 battles in the Arena","reward":["ArenaHero","Titleawardedtoplayerswhohavewon1000battlesintheArena"]}}';

			return questListJson;
		}
	</script>