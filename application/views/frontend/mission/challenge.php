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
				html = html + `<span class="quest-name">${quest.name}</span>`;
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
									let html_log = `<p class="m-b-0">Success | ${questListArray[current_quest].name}</p>`;
									$('#text_log').prepend(html_log);
									return 'success';
								} else {
									notify('error', res.message);
									renderErrorLog(res.message + ' - ' + questListArray[current_quest].name);
									return 'wrong';
								}
							} catch (error) {
								if (res.message) {
									notify('error', res.message);
									renderErrorLog(res.message + ' - ' + questListArray[current_quest].name);
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
				'[{"iname":"CHALLENGE_01_00","name":"CHALLENGE 1-1"},{"iname":"CHALLENGE_01_01","name":"CHALLENGE 1-2"},{"iname":"CHALLENGE_01_02","name":"CHALLENGE 1-3"},{"iname":"CHALLENGE_01_03","name":"CHALLENGE 1-4"},{"iname":"CHALLENGE_01_04","name":"CHALLENGE 1-5"},{"iname":"CHALLENGE_01_05","name":"CHALLENGE 1-6"},{"iname":"CHALLENGE_01_06","name":"CHALLENGE 1-7"},{"iname":"CHALLENGE_01_07","name":"CHALLENGE 1-8"},{"iname":"CHALLENGE_01_08","name":"CHALLENGE 1-9"},{"iname":"CHALLENGE_02_00","name":"CHALLENGE 2-1"},{"iname":"CHALLENGE_02_01","name":"CHALLENGE 2-2"},{"iname":"CHALLENGE_02_02","name":"CHALLENGE 2-3"},{"iname":"CHALLENGE_02_03","name":"CHALLENGE 2-4"},{"iname":"CHALLENGE_02_04","name":"CHALLENGE 2-5"},{"iname":"CHALLENGE_02_05","name":"CHALLENGE 2-6"},{"iname":"CHALLENGE_02_06","name":"CHALLENGE 2-7"},{"iname":"CHALLENGE_02_07","name":"CHALLENGE 2-8"},{"iname":"CHALLENGE_02_08","name":"CHALLENGE 2-9"},{"iname":"CHALLENGE_03_00","name":"CHALLENGE 3-1"},{"iname":"CHALLENGE_03_01","name":"CHALLENGE 3-2"},{"iname":"CHALLENGE_03_02","name":"CHALLENGE 3-3"},{"iname":"CHALLENGE_03_03","name":"CHALLENGE 3-4"},{"iname":"CHALLENGE_03_04","name":"CHALLENGE 3-5"},{"iname":"CHALLENGE_03_05","name":"CHALLENGE 3-6"},{"iname":"CHALLENGE_03_06","name":"CHALLENGE 3-7"},{"iname":"CHALLENGE_03_07","name":"CHALLENGE 3-8"},{"iname":"CHALLENGE_03_08","name":"CHALLENGE 3-9"},{"iname":"CHALLENGE_04_00","name":"CHALLENGE 4-1"},{"iname":"CHALLENGE_04_01","name":"CHALLENGE 4-2"},{"iname":"CHALLENGE_04_02","name":"CHALLENGE 4-3"},{"iname":"CHALLENGE_04_03","name":"CHALLENGE 4-4"},{"iname":"CHALLENGE_04_04","name":"CHALLENGE 4-5"},{"iname":"CHALLENGE_04_05","name":"CHALLENGE 4-6"},{"iname":"CHALLENGE_04_06","name":"CHALLENGE 4-7"},{"iname":"CHALLENGE_04_07","name":"CHALLENGE 4-8"},{"iname":"CHALLENGE_04_08","name":"CHALLENGE 4-9"},{"iname":"CHALLENGE_05_00","name":"CHALLENGE 5-1"},{"iname":"CHALLENGE_05_01","name":"CHALLENGE 5-2"},{"iname":"CHALLENGE_05_02","name":"CHALLENGE 5-3"},{"iname":"CHALLENGE_05_03","name":"CHALLENGE 5-4"},{"iname":"CHALLENGE_05_04","name":"CHALLENGE 5-5"},{"iname":"CHALLENGE_05_05","name":"CHALLENGE 5-6"},{"iname":"CHALLENGE_05_06","name":"CHALLENGE 5-7"},{"iname":"CHALLENGE_05_07","name":"CHALLENGE 5-8"},{"iname":"CHALLENGE_05_08","name":"CHALLENGE 5-9"}]';

			return questListJson;
		}
	</script>