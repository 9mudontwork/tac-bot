<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
	<div class="container">
		<div class="card z-depth-1">
			<div class="card-header ch-alt">
				<h2>Quest Hard</h2>
			</div>

			<div class="card-body card-padding">
				<p class="f-16 m-b-5">Select Quest</p>
				<select id="quest_id" class="chosen-image" style="display: none;">
				</select>

				<div class="checkbox m-t-15">
					<label>
						<input id="continue_checkbox" onclick="continueQuest();" type="checkbox">
						<i class="input-helper"></i>
						Continute to this Quest
					</label>
				</div>

				<div class="m-t-15" id="field_quest_target_select" style="display:none;">
					<select id="quest_id_target" class="chosen-image">
					</select>
				</div>

				<p class="f-16 m-t-20 m-b-5">Repeat / 1 Quest</p>
				<div class="form-group fg-line m-b-10">
					<input id="repeat_amount" value="1" type="text" class="form-control input-lg" placeholder="Enter Repeat Amount">
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
				<div id="text_log" class="lgi-heading m-b-5">
				</div>
			</div>

		</div>

	</div>

	<script>
		$(document).ready(function() {

			const questListArray = JSON.parse(questListJson());

			let html = '';
			$.each(questListArray, function(i, quest) {


				html = html + '<option data-keywords="';

				$.each(quest.item_reward, function(i, item) {
					html = html + item.name + ',';
				});

				html = html + `" quest-data='${JSON.stringify(quest.item_reward)}'`
				html = html + `value="${quest.iname}">${quest.name}</option>`;
			});

			$('#quest_id').append(html);
			$("#quest_id").trigger("chosen:updated");

			$('#quest_id_target').append(html);
			$("#quest_id_target").trigger("chosen:updated");

		});

		let continue_checkbox = true;

		function continueQuest() {
			if (continue_checkbox == true) {
				continue_checkbox = false;
				$("#field_quest_target_select").css("display", "block");
			} else {
				continue_checkbox = true;
				$("#field_quest_target_select").css("display", "none");
			}
		}

		function doQuestNormalHard() {

			let item_current_total_done = 0;
			let round = 1;

			function nextCompose() {

				let url = '{post_doQuestMultiplayer}';
				let quest_id = $('#quest_id').val();
				let quest_id_target = $('#quest_id_target').val();
				let quest_name = $('#quest_id :selected').text();
				let repeat_amount = $('#repeat_amount').val();

				let param = {
					'id': '{id}',
					'platform': $.cookie('platform'),
					'quest_id': quest_id,
					'token': '{token}',
					'token_ap': '{token_ap}',
					'btlid': '{btlid}',
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

							if ($("#continue_checkbox").is(':checked')) {

								// ถ้าเควสผ่าน
								if (res_quest == 'success') {
									// ถ้าเควสยังไม่เท่ากับเป้าหมาย
									if (quest_id != quest_id_target) {
										// ถ้ารอบปัจจุบันของเควสที่ทำ น้อยกว่าที่ตั้งไว้
										round++;
										if (round <= repeat_amount) {
											// ส่งเควสเดิม ทำซ้ำรอบต่อไป
											nextCompose();
										} else {
											// ถ้าเควสปัจจุบันรอบครบแล้ว
											// เลื่อนเควสถัดไป
											$('#quest_id option:selected').next().attr('selected', 'selected');
											quest_id = $("#quest_id").val();
											quest_name = $("#quest_id :selected").text();

											// รีเซ็ตรอบเควส ทำเควสต่อไป
											round = 1;
											nextCompose();
										}
									} else if (quest_id == quest_id_target) {
										// ถ้ารอบปัจจุบันของเควสที่ทำ น้อยกว่าที่ตั้งไว้
										round++;
										if (round <= repeat_amount) {
											// ส่งเควสเดิม ทำซ้ำรอบต่อไป
											nextCompose();
										} else {
											EnButton('run_button', 'Run');
										}
									} else {

										EnButton('run_button', 'Run');
									}
								}
							} else {
								// ทำเควสเดียวตามจำนวนครั้ง
								// ถ้าเควสผ่าน
								if (res_quest == 'success') {
									round++;
									if (round <= repeat_amount) {
										// ส่งเควสถัดไป
										nextCompose();
									} else {
										// ทำเควสครบแล้ว
										EnButton('run_button', 'Run');
									}
								} else if (res_quest == 'wrong') {
									// สั่งรหัสเควสเริ่ม = สิ้นสุดทันทีเพื่อหยุด
									round = repeat_amount;
									// ปลดบล็อคปุ่ม
									EnButton('run_button', 'Run');
								} else {
									EnButton('run_button', 'Run');
								}
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

							try {
								if (res.body.player.selected_award == "" || res.body.player.selected_award) {

									// แสดงไอเท็มดรอป
									let html_log = '';
									html_log += `<p class="m-b-0">${round} | ${quest_name} | Receive `;
									res.drops.forEach(items => {
										if (items.iname) {
											var name_unit_search = items.iname;
											var name_unit_search = name_unit_search.replace('UN_V2_', 'IT_PI_')

											// ค้นหายูนิตใน listUnitJson
											let questListArray = JSON.parse(questListJson());
											$.each(questListArray[quest_id].item_reward, function(i, item) {
												if (name_unit_search == item.iname) {
													html_log = html_log +
														`<img style="heigth:35px; width:35px;" src="http://cdn.alchemistcodedb.com/images/items/icons/${item.url}">`;
													html_log = html_log + `x ${items.num}`;
												}
											});
										}
									});
									html_log = html_log + `</p>`;
									$('#text_log').prepend(html_log);
									return 'success';
								} else {
									renderErrorLog(res.message);
									toast('error', res.message);
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
				'{"QE_ST_HA_010001":{"iname":"QE_ST_HA_010001","item_reward":[{"iname":"IT_PI_LOGI","name":"Logi Soul Shard","url":"IT_PI_LOGI.png"},{"iname":"IT_EQ_ATK_WOODENSWORD","name":"Wooden Sword","url":"IT_EQ_ATK_WOODENSWORD.png"},{"iname":"IT_EQ_MAG_BRANCH","name":"Branch","url":"IT_EQ_MAG_BRANCH.png"}],"name":"[Hard] Ch 1: Ep 1 [1-1] A Turbulent Beginning"},"QE_ST_HA_010002":{"iname":"QE_ST_HA_010002","item_reward":[{"iname":"IT_PI_RIGARUTO","name":"Rigalt Soul Shard","url":"IT_PI_RIGARUTO.png"},{"iname":"IT_EQ_ATK_MACE","name":"Mace","url":"IT_EQ_ATK_MACE.png"},{"iname":"IT_EQ_MAG_WAND","name":"Wand","url":"IT_EQ_MAG_WAND.png"}],"name":"[Hard] Ch 1: Ep 1 [1-10] Saving the Rebels"},"QE_ST_HA_010003":{"iname":"QE_ST_HA_010003","item_reward":[{"iname":"IT_PI_ALAIA","name":"Alaia Soul Shard","url":"IT_PI_ALAIA.png"},{"iname":"IT_EQ_DIAGRAM_GROVE","name":"Gloves Diagram","url":"IT_EQ_DIAGRAM_GROVE.png"},{"iname":"IT_EQ_DIAGRAM_EARRING","name":"Earrings Diagram","url":"IT_EQ_DIAGRAM_EARRING.png"}],"name":"[Hard] Ch 1: Ep 1 [2-4] In the Woods at Dusk"},"QE_ST_HA_010004":{"iname":"QE_ST_HA_010004","item_reward":[{"iname":"IT_PI_RINN","name":"Rin Soul Shard","url":"IT_PI_RINN.png"},{"iname":"IT_EQ_HP_CAP","name":"Thick Hat","url":"IT_EQ_HP_CAP.png"},{"iname":"IT_EQ_DEX_TRIANGLE","name":"Set Square","url":"IT_EQ_DEX_TRIANGLE.png"}],"name":"[Hard] Ch 1: Ep 1 [2-5] Encounter with Sabareta"},"QE_ST_HA_010005":{"iname":"QE_ST_HA_010005","item_reward":[{"iname":"IT_PI_LUCIDO","name":"Lucido Soul Shard","url":"IT_PI_LUCIDO.png"},{"iname":"IT_EQ_GEM_SPOON","name":"Silver Spoon","url":"IT_EQ_GEM_SPOON.png"},{"iname":"IT_EQ_DIAGRAM_KNIFE","name":"Ogre Knife Diagram","url":"IT_EQ_DIAGRAM_KNIFE.png"}],"name":"[Hard] Ch 1: Ep 1 [2-9] Journey to the Ruins"},"QE_ST_HA_010006":{"iname":"QE_ST_HA_010006","item_reward":[{"iname":"IT_PI_ALEXIS","name":"Alexis Soul Shard","url":"IT_PI_ALEXIS.png"},{"iname":"IT_EQ_LUK_SILVERCOIN","name":"Fortune Coin","url":"IT_EQ_LUK_SILVERCOIN.png"},{"iname":"IT_EQ_ATK_MACE","name":"Mace","url":"IT_EQ_ATK_MACE.png"}],"name":"[Hard] Ch 1: Ep 1 [2-10] Decisive Battle at the Gilrack Ruins"},"QE_ST_HA_010007":{"iname":"QE_ST_HA_010007","item_reward":[{"iname":"IT_PI_POLLIN","name":"Polin Soul Shard","url":"IT_PI_POLLIN.png"},{"iname":"IT_EQ_ATK_WOODENSWORD","name":"Wooden Sword","url":"IT_EQ_ATK_WOODENSWORD.png"},{"iname":"IT_EQ_MAG_WAND","name":"Wand","url":"IT_EQ_MAG_WAND.png"}],"name":"[Hard] Ch 1: Ep 1 [3-1] Bringer of Demise"},"QE_ST_HA_010008":{"iname":"QE_ST_HA_010008","item_reward":[{"iname":"IT_PI_GAIN","name":"Gyan Soul Shard","url":"IT_PI_GAIN.png"},{"iname":"IT_EQ_DIAGRAM_BROOM","name":"Broom Diagram","url":"IT_EQ_DIAGRAM_BROOM.png"},{"iname":"IT_EQ_DEF_BANGLE","name":"Copper Bangle","url":"IT_EQ_DEF_BANGLE.png"}],"name":"[Hard] Ch 1: Ep 1 [3-2] A Quiet Castle"},"QE_ST_HA_010009":{"iname":"QE_ST_HA_010009","item_reward":[{"iname":"IT_PI_MELIA","name":"Melia Soul Shard","url":"IT_PI_MELIA.png"},{"iname":"IT_EQ_ATK_WOODENSWORD","name":"Wooden Sword","url":"IT_EQ_ATK_WOODENSWORD.png"},{"iname":"IT_EQ_DEX_TRIANGLE","name":"Set Square","url":"IT_EQ_DEX_TRIANGLE.png"}],"name":"[Hard] Ch 1: Ep 1 [3-9] Proud Knight of Wrath"},"QE_ST_HA_010010":{"iname":"QE_ST_HA_010010","item_reward":[{"iname":"IT_PI_RUNBELL","name":"Lambert Soul Shard","url":"IT_PI_RUNBELL.png"},{"iname":"IT_EQ_MAG_BRANCH","name":"Branch","url":"IT_EQ_MAG_BRANCH.png"},{"iname":"IT_EQ_PIECE_JACKET","name":"Cloth Jacket Piece","url":"IT_EQ_HP_JACKET.png"}],"name":"[Hard] Ch 1: Ep 1 [3-10] No Lies - No Matter What"},"QE_ST_HA_010011":{"iname":"QE_ST_HA_010011","item_reward":[{"iname":"IT_PI_DIOS","name":"Dias Soul Shard","url":"IT_PI_DIOS.png"},{"iname":"IT_EQ_DIAGRAM_KNIFE","name":"Ogre Knife Diagram","url":"IT_EQ_DIAGRAM_KNIFE.png"},{"iname":"IT_EQ_PIECE_FURO","name":"Stamp of Certain Delivery Piece","url":"IT_EQ_DEX_FURO.png"}],"name":"[Hard] Ch 1: Ep 2 [1-1] How to Capture Two Fortresses"},"QE_ST_HA_010012":{"iname":"QE_ST_HA_010012","item_reward":[{"iname":"IT_PI_GATH","name":"Gato Soul Shard","url":"IT_PI_GATH.png"},{"iname":"IT_EQ_MND_CHARM","name":"Charm","url":"IT_EQ_MND_CHARM.png"},{"iname":"IT_EQ_PIECE_SHELM","name":"Cross Helmet Piece","url":"IT_EQ_DEF_SHELM.png"}],"name":"[Hard] Ch 1: Ep 2 [1-10] Soul-Devouring Black Knight"},"QE_ST_HA_010013":{"iname":"QE_ST_HA_010013","item_reward":[{"iname":"IT_PI_ALFRED","name":"Alfred Soul Shard","url":"IT_PI_ALFRED.png"},{"iname":"IT_EQ_DEX_TRIANGLE","name":"Set Square","url":"IT_EQ_DEX_TRIANGLE.png"},{"iname":"IT_EQ_PIECE_FURO","name":"Stamp of Certain Delivery Piece","url":"IT_EQ_DEX_FURO.png"}],"name":"[Hard] Ch 1: Ep 2 [2-4] A New Threat"},"QE_ST_HA_010014":{"iname":"QE_ST_HA_010014","item_reward":[{"iname":"IT_PI_MEILY","name":"Meily Soul Shard","url":"IT_PI_MEILY.png"},{"iname":"IT_EQ_ATK_WOODENSWORD","name":"Wooden Sword","url":"IT_EQ_ATK_WOODENSWORD.png"},{"iname":"IT_EQ_CRI_DAGGER","name":"Dagger","url":"IT_EQ_CRI_DAGGER.png"}],"name":"[Hard] Ch 1: Ep 2 [2-5] To the Ruins of Ancient Alchemy"},"QE_ST_HA_010015":{"iname":"QE_ST_HA_010015","item_reward":[{"iname":"IT_PI_ISHUNA","name":"Ishuna Soul Shard","url":"IT_PI_ISHUNA.png"},{"iname":"IT_EQ_DIAGRAM_GROVE","name":"Gloves Diagram","url":"IT_EQ_DIAGRAM_GROVE.png"},{"iname":"IT_EQ_DIAGRAM_RABBIT","name":"Rabbit&#39;s Foot Diagram","url":"IT_EQ_DIAGRAM_RABBIT.png"}],"name":"[Hard] Ch 1: Ep 2 [2-9] Return of the Black Knight"},"QE_ST_HA_010016":{"iname":"QE_ST_HA_010016","item_reward":[{"iname":"IT_PI_RIGARUTO","name":"Rigalt Soul Shard","url":"IT_PI_RIGARUTO.png"},{"iname":"IT_EQ_GEM_SPOON","name":"Silver Spoon","url":"IT_EQ_GEM_SPOON.png"},{"iname":"IT_EQ_PIECE_TRUMPET","name":"Trumpet Piece","url":"IT_EQ_GEM_TRUMPET.png"}],"name":"[Hard] Ch 1: Ep 2 [2-10] The Beginning of Disaster"},"QE_ST_HA_010017":{"iname":"QE_ST_HA_010017","item_reward":[{"iname":"IT_PI_ALAIA","name":"Alaia Soul Shard","url":"IT_PI_ALAIA.png"},{"iname":"IT_EQ_DIAGRAM_KNIFE","name":"Ogre Knife Diagram","url":"IT_EQ_DIAGRAM_KNIFE.png"},{"iname":"IT_EQ_PIECE_SABER","name":"Saber Piece","url":"IT_EQ_ATK_SABER.png"},{"iname":"IT_EQ_PIECE_ROD","name":"Skull Rod Piece","url":"IT_EQ_MAG_ROD.png"}],"name":"[Hard] Ch 1: Ep 2 [3-1] A Vulnerable Heart"},"QE_ST_HA_010018":{"iname":"QE_ST_HA_010018","item_reward":[{"iname":"IT_PI_RINN","name":"Rin Soul Shard","url":"IT_PI_RINN.png"},{"iname":"IT_EQ_ATK_MACE","name":"Mace","url":"IT_EQ_ATK_MACE.png"},{"iname":"IT_EQ_PIECE_SHELM","name":"Cross Helmet Piece","url":"IT_EQ_DEF_SHELM.png"},{"iname":"IT_EQ_PIECE_CAPE","name":"Magic Cape Piece","url":"IT_EQ_MND_CAPE.png"}],"name":"[Hard] Ch 1: Ep 2 [3-2] The Minister&#39;s Judgement"},"QE_ST_HA_010019":{"iname":"QE_ST_HA_010019","item_reward":[{"iname":"IT_PI_LUCIDO","name":"Lucido Soul Shard","url":"IT_PI_LUCIDO.png"},{"iname":"IT_EQ_DEX_TRIANGLE","name":"Set Square","url":"IT_EQ_DEX_TRIANGLE.png"},{"iname":"IT_EQ_PIECE_FURO","name":"Stamp of Certain Delivery Piece","url":"IT_EQ_DEX_FURO.png"},{"iname":"IT_EQ_DIAGRAMPIECE_CIRCLET","name":"Circlet Diagram Piece","url":"IT_EQ_DIAGRAM_CIRCLET.png"}],"name":"[Hard] Ch 1: Ep 2 [3-9] Rampage of Disaster"},"QE_ST_HA_010020":{"iname":"QE_ST_HA_010020","item_reward":[{"iname":"IT_PI_ALEXIS","name":"Alexis Soul Shard","url":"IT_PI_ALEXIS.png"},{"iname":"IT_EQ_CRI_DAGGER","name":"Dagger","url":"IT_EQ_CRI_DAGGER.png"},{"iname":"IT_EQ_DIAGRAM_GETA","name":"Single-Tooth Geta Diagram","url":"IT_EQ_DIAGRAM_GETA.png"},{"iname":"IT_EQ_DIAGRAMPIECE_LOUPE","name":"Magnifying Glass Diagram Piece","url":"IT_EQ_DIAGRAM_LOUPE.png"}],"name":"[Hard] Ch 1: Ep 2 [3-10] Beyond Undelivered Feelings"},"QE_ST_HA_010021":{"iname":"QE_ST_HA_010021","item_reward":[{"iname":"IT_PI_LOGI","name":"Logi Soul Shard","url":"IT_PI_LOGI.png"},{"iname":"IT_EQ_PIECE_ROD","name":"Skull Rod Piece","url":"IT_EQ_MAG_ROD.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MIRROR","name":"Crystal Mirror Diagram Piece","url":"IT_EQ_DIAGRAM_MIRROR.png"},{"iname":"IT_EQ_PIECE_STAFF","name":"Star Staff Piece","url":"IT_EQ_MAG_STAFF.png"}],"name":"[Hard] Ch 1: Ep 3 [1-6] Determination of the Lizard Brigade"},"QE_ST_HA_010022":{"iname":"QE_ST_HA_010022","item_reward":[{"iname":"IT_PI_POLLIN","name":"Polin Soul Shard","url":"IT_PI_POLLIN.png"},{"iname":"IT_EQ_PIECE_SHELM","name":"Cross Helmet Piece","url":"IT_EQ_DEF_SHELM.png"},{"iname":"IT_EQ_DIAGRAMPIECE_CIRCLET","name":"Circlet Diagram Piece","url":"IT_EQ_DIAGRAM_CIRCLET.png"},{"iname":"IT_EQ_PIECE_SHIELD","name":"Lion Monarch Shield Piece","url":"IT_EQ_DEF_SHIELD.png"}],"name":"[Hard] Ch 1: Ep 3 [1-7] Logi&#39;s Resolution"},"QE_ST_HA_010023":{"iname":"QE_ST_HA_010023","item_reward":[{"iname":"IT_PI_GAIN","name":"Gyan Soul Shard","url":"IT_PI_GAIN.png"},{"iname":"IT_EQ_MAG_WAND","name":"Wand","url":"IT_EQ_MAG_WAND.png"},{"iname":"IT_EQ_DIAGRAMPIECE_JAVELIN","name":"Javelin Diagram Piece","url":"IT_EQ_DIAGRAM_JAVELIN.png"},{"iname":"IT_EQ_PIECE_COAT","name":"Noble Coat Piece","url":"IT_EQ_HP_COAT.png"}],"name":"[Hard] Ch 1: Ep 3 [1-9] Final Hope"},"QE_ST_HA_010024":{"iname":"QE_ST_HA_010024","item_reward":[{"iname":"IT_PI_MELIA","name":"Melia Soul Shard","url":"IT_PI_MELIA.png"},{"iname":"IT_EQ_PIECE_JACKET","name":"Cloth Jacket Piece","url":"IT_EQ_HP_JACKET.png"},{"iname":"IT_EQ_DIAGRAMPIECE_SHINOBI","name":"Shinobi Shoes Diagram Piece","url":"IT_EQ_DIAGRAM_SHINOBI.png"},{"iname":"IT_EQ_PIECE_NOTEBOOK","name":"Research Notebook Piece","url":"IT_EQ_DEX_NOTEBOOK.png"}],"name":"[Hard] Ch 1: Ep 3 [1-10] Agatha&#39;s Suggestion"},"QE_ST_HA_010025":{"iname":"QE_ST_HA_010025","item_reward":[{"iname":"IT_PI_RUNBELL","name":"Lambert Soul Shard","url":"IT_PI_RUNBELL.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MAS","name":"Drunkard&#39;s Masu Cup Diagram Piece","url":"IT_EQ_DIAGRAM_MAS.png"},{"iname":"IT_EQ_PIECE_NOTEBOOK","name":"Research Notebook Piece","url":"IT_EQ_DEX_NOTEBOOK.png"},{"iname":"IT_EQ_PIECE_KATAR","name":"Platinum Katar Piece","url":"IT_EQ_CRI_KATAR.png"}],"name":"[Hard] Ch 1: Ep 3 [2-9] Ardent Wish of Wratharis"},"QE_ST_HA_010026":{"iname":"QE_ST_HA_010026","item_reward":[{"iname":"IT_PI_GATH","name":"Gato Soul Shard","url":"IT_PI_GATH.png"},{"iname":"IT_EQ_PIECE_MACHINEWING","name":"Mechanical Wing Piece","url":"IT_EQ_SPD_MACHINEWING.png"},{"iname":"IT_EQ_PIECE_DICE","name":"Dice of Fate Piece","url":"IT_EQ_LUK_DICE.png"},{"iname":"IT_EQ_PIECE_CHIKUONKI","name":"Gramophone Piece","url":"IT_EQ_GEM_CHIKUONKI.png"}],"name":"[Hard] Ch 1: Ep 3 [2-10] Story of Dreams"},"QE_ST_HA_010027":{"iname":"QE_ST_HA_010027","item_reward":[{"iname":"IT_PI_ALFRED","name":"Alfred Soul Shard","url":"IT_PI_ALFRED.png"},{"iname":"IT_EQ_MAG_BRANCH","name":"Branch","url":"IT_EQ_MAG_BRANCH.png"},{"iname":"IT_EQ_DIAGRAMPIECE_TURBAN","name":"Turban Diagram Piece","url":"IT_EQ_DIAGRAM_TURBAN.png"},{"iname":"IT_EQ_PIECE_LANCE","name":"Great Lance Piece","url":"IT_EQ_ATK_LANCE.png"}],"name":"[Hard] Ch 1: Ep 3 [3-2] Good Old Daily Life"},"QE_ST_HA_010028":{"iname":"QE_ST_HA_010028","item_reward":[{"iname":"IT_PI_MEILY","name":"Meily Soul Shard","url":"IT_PI_MEILY.png"},{"iname":"IT_EQ_DIAGRAM_GROVE","name":"Gloves Diagram","url":"IT_EQ_DIAGRAM_GROVE.png"},{"iname":"IT_EQ_DIAGRAMPIECE_BELL","name":"Silver Bell Diagram Piece","url":"IT_EQ_DIAGRAM_BELL.png"},{"iname":"IT_EQ_DIAGRAM_CHOKER","name":"Elemental Belt Diagram","url":"IT_EQ_DIAGRAM_CHOKER.png"}],"name":"[Hard] Ch 1: Ep 3 [3-7] Back to Father"},"QE_ST_HA_010029":{"iname":"QE_ST_HA_010029","item_reward":[{"iname":"IT_PI_ISHUNA","name":"Ishuna Soul Shard","url":"IT_PI_ISHUNA.png"},{"iname":"IT_EQ_HP_CAP","name":"Thick Hat","url":"IT_EQ_HP_CAP.png"},{"iname":"IT_EQ_DIAGRAMPIECE_TURBAN","name":"Turban Diagram Piece","url":"IT_EQ_DIAGRAM_TURBAN.png"},{"iname":"IT_EQ_PIECE_COAT","name":"Noble Coat Piece","url":"IT_EQ_HP_COAT.png"}],"name":"[Hard] Ch 1: Ep 3 [3-9] Unsaveable Heart"},"QE_ST_HA_010030":{"iname":"QE_ST_HA_010030","item_reward":[{"iname":"IT_PI_RIGARUTO","name":"Rigalt Soul Shard","url":"IT_PI_RIGARUTO.png"},{"iname":"IT_EQ_DIAGRAM_GETA","name":"Single-Tooth Geta Diagram","url":"IT_EQ_DIAGRAM_GETA.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MIRROR","name":"Crystal Mirror Diagram Piece","url":"IT_EQ_DIAGRAM_MIRROR.png"},{"iname":"IT_EQ_DIAGRAM_CHOKER","name":"Elemental Belt Diagram","url":"IT_EQ_DIAGRAM_CHOKER.png"}],"name":"[Hard] Ch 1: Ep 3 [3-10] Future put in Motion"},"QE_ST_HA_010031":{"iname":"QE_ST_HA_010031","item_reward":[{"iname":"IT_PI_DIOS","name":"Dias Soul Shard","url":"IT_PI_DIOS.png"},{"iname":"IT_EQ_DIAGRAM_RABBIT","name":"Rabbit&#39;s Foot Diagram","url":"IT_EQ_DIAGRAM_RABBIT.png"},{"iname":"IT_EQ_DIAGRAMPIECE_SHINOBI","name":"Shinobi Shoes Diagram Piece","url":"IT_EQ_DIAGRAM_SHINOBI.png"},{"iname":"IT_EQ_PIECE_MACHINEWING","name":"Mechanical Wing Piece","url":"IT_EQ_SPD_MACHINEWING.png"}],"name":"[Hard] Ch 1: Ep 4 [1-4] Limits of Hopes"},"QE_ST_HA_010032":{"iname":"QE_ST_HA_010032","item_reward":[{"iname":"IT_PI_ALAIA","name":"Alaia Soul Shard","url":"IT_PI_ALAIA.png"},{"iname":"IT_EQ_GEM_SPOON","name":"Silver Spoon","url":"IT_EQ_GEM_SPOON.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MAS","name":"Drunkard&#39;s Masu Cup Diagram Piece","url":"IT_EQ_DIAGRAM_MAS.png"},{"iname":"IT_EQ_PIECE_CHIKUONKI","name":"Gramophone Piece","url":"IT_EQ_GEM_CHIKUONKI.png"}],"name":"[Hard] Ch 1: Ep 4 [1-6] Father&#39;s True Motives"},"QE_ST_HA_010033":{"iname":"QE_ST_HA_010033","item_reward":[{"iname":"IT_PI_RINN","name":"Rin Soul Shard","url":"IT_PI_RINN.png"},{"iname":"IT_EQ_MAG_BRANCH","name":"Branch","url":"IT_EQ_MAG_BRANCH.png"},{"iname":"IT_EQ_PIECE_NOTEBOOK","name":"Research Notebook Piece","url":"IT_EQ_DEX_NOTEBOOK.png"},{"iname":"IT_EQ_PIECE_KATAR","name":"Platinum Katar Piece","url":"IT_EQ_CRI_KATAR.png"}],"name":"[Hard] Ch 1: Ep 4 [1-9] Agitation"},"QE_ST_HA_010034":{"iname":"QE_ST_HA_010034","item_reward":[{"iname":"IT_PI_LUCIDO","name":"Lucido Soul Shard","url":"IT_PI_LUCIDO.png"},{"iname":"IT_EQ_HP_CAP","name":"Thick Hat","url":"IT_EQ_HP_CAP.png"},{"iname":"IT_EQ_PIECE_ROD","name":"Skull Rod Piece","url":"IT_EQ_MAG_ROD.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MIRROR","name":"Crystal Mirror Diagram Piece","url":"IT_EQ_DIAGRAM_MIRROR.png"},{"iname":"IT_EQ_PIECE_COAT","name":"Noble Coat Piece","url":"IT_EQ_HP_COAT.png"}],"name":"[Hard] Ch 1: Ep 4 [2-5] Riddle in the Eyes"},"QE_ST_HA_010035":{"iname":"QE_ST_HA_010035","item_reward":[{"iname":"IT_PI_ALEXIS","name":"Alexis Soul Shard","url":"IT_PI_ALEXIS.png"},{"iname":"IT_EQ_MAG_WAND","name":"Wand","url":"IT_EQ_MAG_WAND.png"},{"iname":"IT_EQ_DIAGRAM_SMALL","name":"Picmy Figurine Diagram","url":"IT_EQ_DIAGRAM_SMALL.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MAS","name":"Drunkard&#39;s Masu Cup Diagram Piece","url":"IT_EQ_DIAGRAM_MAS.png"},{"iname":"IT_EQ_PIECE_MACHINEWING","name":"Mechanical Wing Piece","url":"IT_EQ_SPD_MACHINEWING.png"}],"name":"[Hard] Ch 1: Ep 4 [2-8] We Fall - We Stand Back up"},"QE_ST_HA_010036":{"iname":"QE_ST_HA_010036","item_reward":[{"iname":"IT_PI_POLLIN","name":"Polin Soul Shard","url":"IT_PI_POLLIN.png"},{"iname":"IT_EQ_PIECE_GOGGLES","name":"Goggles Piece","url":"IT_EQ_CRI_GOGGLES.png"},{"iname":"IT_EQ_PIECE_HOURGLASS","name":"Rainbow Hourglass Piece","url":"IT_EQ_SPD_HOURGLASS.png"},{"iname":"IT_EQ_DIAGRAMPIECE_SHINOBI","name":"Shinobi Shoes Diagram Piece","url":"IT_EQ_DIAGRAM_SHINOBI.png"},{"iname":"IT_EQ_PIECE_DICE","name":"Dice of Fate Piece","url":"IT_EQ_LUK_DICE.png"}],"name":"[Hard] Ch 1: Ep 4 [2-9] Hesitation and Weakness"},"QE_ST_HA_010037":{"iname":"QE_ST_HA_010037","item_reward":[{"iname":"IT_PI_GAIN","name":"Gyan Soul Shard","url":"IT_PI_GAIN.png"},{"iname":"IT_EQ_CRI_DAGGER","name":"Dagger","url":"IT_EQ_CRI_DAGGER.png"},{"iname":"IT_EQ_DIAGRAM_SMALL","name":"Picmy Figurine Diagram","url":"IT_EQ_DIAGRAM_SMALL.png"},{"iname":"IT_EQ_PIECE_CHIKUONKI","name":"Gramophone Piece","url":"IT_EQ_GEM_CHIKUONKI.png"},{"iname":"IT_EQ_DIAGRAMPIECE_TENBIN","name":"Gem Seller&#39;s Scales Diagram Piece","url":"IT_EQ_DIAGRAM_TENBIN.png"}],"name":"[Hard] Ch 1: Ep 4 [2-10] Farewell"},"QE_ST_HA_010038":{"iname":"QE_ST_HA_010038","item_reward":[{"iname":"IT_PI_MELIA","name":"Melia Soul Shard","url":"IT_PI_MELIA.png"},{"iname":"IT_EQ_DEF_BANGLE","name":"Copper Bangle","url":"IT_EQ_DEF_BANGLE.png"},{"iname":"IT_EQ_DIAGRAM_CHOKER","name":"Elemental Belt Diagram","url":"IT_EQ_DIAGRAM_CHOKER.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MIRROR","name":"Crystal Mirror Diagram Piece","url":"IT_EQ_DIAGRAM_MIRROR.png"},{"iname":"IT_EQ_DIAGRAMPIECE_BELT","name":"Black Belt Diagram Piece","url":"IT_EQ_DIAGRAM_BELT.png"}],"name":"[Hard] Ch 1: Ep 4 [3-4] One Future - Two Swords"},"QE_ST_HA_010039":{"iname":"QE_ST_HA_010039","item_reward":[{"iname":"IT_PI_RUNBELL","name":"Lambert Soul Shard","url":"IT_PI_RUNBELL.png"},{"iname":"IT_EQ_SPD_FEATHER","name":"Blue Feather","url":"IT_EQ_SPD_FEATHER.png"},{"iname":"IT_EQ_DIAGRAMPIECE_CIRCLET","name":"Circlet Diagram Piece","url":"IT_EQ_DIAGRAM_CIRCLET.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MAS","name":"Drunkard&#39;s Masu Cup Diagram Piece","url":"IT_EQ_DIAGRAM_MAS.png"},{"iname":"IT_EQ_PIECE_PARKA","name":"Magus Hood Piece","url":"IT_EQ_MND_PARKA.png"}],"name":"[Hard] Ch 1: Ep 4 [3-6] Temporary Retreat"},"QE_ST_HA_010040":{"iname":"QE_ST_HA_010040","item_reward":[{"iname":"IT_PI_GATH","name":"Gato Soul Shard","url":"IT_PI_GATH.png"},{"iname":"IT_EQ_PIECE_FURO","name":"Stamp of Certain Delivery Piece","url":"IT_EQ_DEX_FURO.png"},{"iname":"IT_EQ_DIAGRAM_SMALL","name":"Picmy Figurine Diagram","url":"IT_EQ_DIAGRAM_SMALL.png"},{"iname":"IT_EQ_DIAGRAM_CHOKER","name":"Elemental Belt Diagram","url":"IT_EQ_DIAGRAM_CHOKER.png"},{"iname":"IT_EQ_DIAGRAMPIECE_TENBIN","name":"Gem Seller&#39;s Scales Diagram Piece","url":"IT_EQ_DIAGRAM_TENBIN.png"}],"name":"[Hard] Ch 1: Ep 4 [3-10] Father and Son"},"QE_ST_HA_010041":{"iname":"QE_ST_HA_010041","item_reward":[{"iname":"IT_PI_ALFRED","name":"Alfred Soul Shard","url":"IT_PI_ALFRED.png"},{"iname":"IT_EQ_PIECE_GEARROD","name":"Gear Rod Piece","url":"IT_EQ_MAG_GEARROD.png"},{"iname":"IT_EQ_PIECE_ARMOR","name":"Flame Emperor Armor Piece","url":"IT_EQ_DEF_ARMOR.png"},{"iname":"IT_EQ_DIAGRAMPIECE_HEELHAT","name":"Hat of Tranquility Diagram Piece","url":"IT_EQ_DIAGRAM_HEELHAT.png"},{"iname":"IT_EQ_DIAGRAMPIECE_HARP","name":"Goddess Harp Diagram Piece","url":"IT_EQ_DIAGRAM_HARP.png"}],"name":"[Hard] Ch 1: Ep 5 [1-4] Words of Magic"},"QE_ST_HA_010042":{"iname":"QE_ST_HA_010042","item_reward":[{"iname":"IT_PI_MEILY","name":"Meily Soul Shard","url":"IT_PI_MEILY.png"},{"iname":"IT_EQ_DIAGRAMPIECE_NECRO","name":"Necronomicon Diagram Piece","url":"IT_EQ_DIAGRAM_NECRO.png"},{"iname":"IT_EQ_PIECE_ROBE","name":"Saint&#39;s Robe Piece","url":"IT_EQ_MND_ROBE.png"},{"iname":"IT_EQ_DIAGRAMPIECE_HOOK","name":"Steel Hook Diagram Piece","url":"IT_EQ_DIAGRAM_HOOK.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MAGICBREATH","name":"Magic Bracelet Diagram Piece","url":"IT_EQ_DIAGRAM_MAGICBREATH.png"}],"name":"[Hard] Ch 1: Ep 5 [1-5] Time Falsified"},"QE_ST_HA_010043":{"iname":"QE_ST_HA_010043","item_reward":[{"iname":"IT_PI_ISHUNA","name":"Ishuna Soul Shard","url":"IT_PI_ISHUNA.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MND_CAP","name":"Mage&#39;s Hat Diagram Piece","url":"IT_EQ_DIAGRAM_MND_CAP.png"},{"iname":"IT_EQ_PIECE_WATCH","name":"Old Pocket Watch Piece","url":"IT_EQ_SPD_WATCH.png"},{"iname":"IT_EQ_DIAGRAMPIECE_ANGELA","name":"Rod of Recovery Diagram Piece","url":"IT_EQ_DIAGRAM_ANGELA.png"},{"iname":"IT_EQ_DIAGRAMPIECE_CRANE","name":"Prayer Crane Diagram Piece","url":"IT_EQ_DIAGRAM_CRANE.png"}],"name":"[Hard] Ch 1: Ep 5 [1-8] Battle at the Ruins"},"QE_ST_HA_010044":{"iname":"QE_ST_HA_010044","item_reward":[{"iname":"IT_PI_RIGARUTO","name":"Rigalt Soul Shard","url":"IT_PI_RIGARUTO.png"},{"iname":"IT_EQ_DIAGRAMPIECE_CHESS","name":"Knight Chess Piece Diagram Piece","url":"IT_EQ_DIAGRAM_CHESS.png"},{"iname":"IT_EQ_PIECE_TWOSWORDS","name":"Orichalcum Blades Piece","url":"IT_EQ_CRI_TWOSWORDS.png"},{"iname":"IT_EQ_DIAGRAMPIECE_SUZU","name":"Shrine Maiden&#39;s Bells Diagram Piece","url":"IT_EQ_DIAGRAM_SUZU.png"},{"iname":"IT_EQ_DIAGRAMPIECE_DRAGON","name":"Dragon Sphere Diagram Piece","url":"IT_EQ_DIAGRAM_DRAGON.png"}],"name":"[Hard] Ch 1: Ep 5 [2-4] Sacred Stone Satna"},"QE_ST_HA_010045":{"iname":"QE_ST_HA_010045","item_reward":[{"iname":"IT_PI_ALAIA","name":"Alaia Soul Shard","url":"IT_PI_ALAIA.png"},{"iname":"IT_EQ_DIAGRAMPIECE_AX","name":"Great Axe Diagram Piece","url":"IT_EQ_DIAGRAM_AX.png"},{"iname":"IT_EQ_PIECE_SWORD","name":"Sacred Lion Blade Piece","url":"IT_EQ_ATK_SWORD.png"},{"iname":"IT_EQ_DIAGRAMPIECE_ARMSWORD","name":"Brave Sword Diagram Piece","url":"IT_EQ_DIAGRAM_ARMSWORD.png"},{"iname":"IT_EQ_DIAGRAMPIECE_HOOK","name":"Steel Hook Diagram Piece","url":"IT_EQ_DIAGRAM_HOOK.png"}],"name":"[Hard] Ch 1: Ep 5 [2-7] Frontal Breakthrough"},"QE_ST_HA_010046":{"iname":"QE_ST_HA_010046","item_reward":[{"iname":"IT_PI_RINN","name":"Rin Soul Shard","url":"IT_PI_RINN.png"},{"iname":"IT_EQ_PIECE_RABBITDOLL","name":"Lucky Rabbit Figurine Piece","url":"IT_EQ_LUK_RABBITDOLL.png"},{"iname":"IT_EQ_PIECE_ROBE","name":"Saint&#39;s Robe Piece","url":"IT_EQ_MND_ROBE.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MUCHI","name":"Briar-Whip of Discipline Diagram Piece","url":"IT_EQ_DIAGRAM_MUCHI.png"},{"iname":"IT_EQ_DIAGRAMPIECE_HARP","name":"Goddess Harp Diagram Piece","url":"IT_EQ_DIAGRAM_HARP.png"}],"name":"[Hard] Ch 1: Ep 5 [2-10] Unexpected Blade"},"QE_ST_HA_010047":{"iname":"QE_ST_HA_010047","item_reward":[{"iname":"IT_PI_LUCIDO","name":"Lucido Soul Shard","url":"IT_PI_LUCIDO.png"},{"iname":"IT_EQ_DIAGRAMPIECE_BELT","name":"Black Belt Diagram Piece","url":"IT_EQ_DIAGRAM_BELT.png"},{"iname":"IT_EQ_PIECE_FURCOAT","name":"Sacred Fur Coat Piece","url":"IT_EQ_HP_FURCOAT.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MAGICALLAMP","name":"Magical Lamp Diagram Piece","url":"IT_EQ_DIAGRAM_ARABIANLAMP.png"},{"iname":"IT_EQ_DIAGRAMPIECE_ZUKIN","name":"Master Ninja&#39;s Hood Diagram Piece","url":"IT_EQ_DIAGRAM_ZUKIN.png"}],"name":"[Hard] Ch 1: Ep 5 [3-1] Minor Business"},"QE_ST_HA_010048":{"iname":"QE_ST_HA_010048","item_reward":[{"iname":"IT_PI_ALEXIS","name":"Alexis Soul Shard","url":"IT_PI_ALEXIS.png"},{"iname":"IT_EQ_PIECE_RADIO","name":"Alchemic Radio Piece","url":"IT_EQ_GEM_RADIO.png"},{"iname":"IT_EQ_PIECE_GLASSPEN","name":"Glass Pen Piece","url":"IT_EQ_DEX_GLASSPEN.png"},{"iname":"IT_EQ_DIAGRAMPIECE_VISIONMASK","name":"Entrancing Mask Diagram Piece","url":"IT_EQ_DIAGRAM_EYE.png"},{"iname":"IT_EQ_DIAGRAMPIECE_ORB","name":"Stellar Orb Diagram Piece","url":"IT_EQ_DIAGRAM_ORB.png"}],"name":"[Hard] Ch 1: Ep 5 [3-2] Battle for the Castle Walls"},"QE_ST_HA_010049":{"iname":"QE_ST_HA_010049","item_reward":[{"iname":"IT_PI_POLLIN","name":"Polin Soul Shard","url":"IT_PI_POLLIN.png"},{"iname":"IT_EQ_DIAGRAMPIECE_COMPASS","name":"Compass Diagram Piece","url":"IT_EQ_DIAGRAM_COMPASS.png"},{"iname":"IT_EQ_PIECE_GEARROD","name":"Gear Rod Piece","url":"IT_EQ_MAG_GEARROD.png"},{"iname":"IT_EQ_DIAGRAMPIECE_COWBOY","name":"Gunner&#39;s Hat Diagram Piece","url":"IT_EQ_DIAGRAM_COWBOY.png"},{"iname":"IT_EQ_DIAGRAMPIECE_CRANE","name":"Prayer Crane Diagram Piece","url":"IT_EQ_DIAGRAM_CRANE.png"}],"name":"[Hard] Ch 1: Ep 5 [3-4] Riot in the Capital"},"QE_ST_HA_010050":{"iname":"QE_ST_HA_010050","item_reward":[{"iname":"IT_PI_GAIN","name":"Gyan Soul Shard","url":"IT_PI_GAIN.png"},{"iname":"IT_EQ_PIECE_TWOSWORDS","name":"Orichalcum Blades Piece","url":"IT_EQ_CRI_TWOSWORDS.png"},{"iname":"IT_EQ_PIECE_RADIO","name":"Alchemic Radio Piece","url":"IT_EQ_GEM_RADIO.png"},{"iname":"IT_EQ_DIAGRAMPIECE_SHOULDER","name":"Laboratory Flasks Diagram Piece","url":"IT_EQ_DIAGRAM_SHOULDER.png"},{"iname":"IT_EQ_DIAGRAMPIECE_WAISTCLOTH","name":"Holy Brawler&#39;s Sash Diagram Piece","url":"IT_EQ_DIAGRAM_WAISTCLOTH.png"}],"name":"[Hard] Ch 1: Ep 5 [3-10] For Whom..."},"QE_ST_HA_020001":{"iname":"QE_ST_HA_020001","item_reward":[{"iname":"IT_PI_EDGAR","name":"Edgar Soul Shard","url":"IT_PI_EDGAR.png"},{"iname":"IT_EQ_DIAGRAMPIECE_DARKKABUTO","name":"Sinister Helmet Diagram Piece","url":"IT_EQ_DIAGRAM_DARKKABUTO.png"},{"iname":"IT_EQ_ATK_WOODENSWORD","name":"Wooden Sword","url":"IT_EQ_ATK_WOODENSWORD.png"}],"name":"[Hard] Ch 2: Ep 1 [1-1] Get past the Cliff"},"QE_ST_HA_020002":{"iname":"QE_ST_HA_020002","item_reward":[{"iname":"IT_PI_ANJOU","name":"Ange Soul Shard","url":"IT_PI_ANJOU.png"},{"iname":"IT_EQ_DIAGRAMPIECE_ARMSWORD","name":"Brave Sword Diagram Piece","url":"IT_EQ_DIAGRAM_ARMSWORD.png"},{"iname":"IT_EQ_DEF_BANGLE","name":"Copper Bangle","url":"IT_EQ_DEF_BANGLE.png"}],"name":"[Hard] Ch 2: Ep 1 [1-10] Opponents Blessed by the Goddess"},"QE_ST_HA_020003":{"iname":"QE_ST_HA_020003","item_reward":[{"iname":"IT_PI_CIEL","name":"Ciel Soul Shard","url":"IT_PI_CIEL.png"},{"iname":"IT_EQ_PIECE_WATCH","name":"Old Pocket Watch Piece","url":"IT_EQ_SPD_WATCH.png"},{"iname":"IT_EQ_MND_CHARM","name":"Charm","url":"IT_EQ_MND_CHARM.png"}],"name":"[Hard] Ch 2: Ep 1 [2-4] Collision of Light and Darkness"},"QE_ST_HA_020004":{"iname":"QE_ST_HA_020004","item_reward":[{"iname":"IT_PI_MELIA","name":"Melia Soul Shard","url":"IT_PI_MELIA.png"},{"iname":"IT_EQ_DIAGRAMPIECE_COMPASS","name":"Compass Diagram Piece","url":"IT_EQ_DIAGRAM_COMPASS.png"},{"iname":"IT_EQ_PIECE_CAPE","name":"Magic Cape Piece","url":"IT_EQ_MND_CAPE.png"}],"name":"[Hard] Ch 2: Ep 1 [2-5] The Necessity of Evil"},"QE_ST_HA_020005":{"iname":"QE_ST_HA_020005","item_reward":[{"iname":"IT_PI_RUNBELL","name":"Lambert Soul Shard","url":"IT_PI_RUNBELL.png"},{"iname":"IT_EQ_DIAGRAMPIECE_HARP","name":"Goddess Harp Diagram Piece","url":"IT_EQ_DIAGRAM_HARP.png"},{"iname":"IT_EQ_CRI_DAGGER","name":"Dagger","url":"IT_EQ_CRI_DAGGER.png"}],"name":"[Hard] Ch 2: Ep 1 [2-9] The Rogues&#39; Motivating Power"},"QE_ST_HA_020006":{"iname":"QE_ST_HA_020006","item_reward":[{"iname":"IT_PI_GATH","name":"Gato Soul Shard","url":"IT_PI_GATH.png"},{"iname":"IT_EQ_PIECE_GLASSPEN","name":"Glass Pen Piece","url":"IT_EQ_DEX_GLASSPEN.png"},{"iname":"IT_EQ_DIAGRAMPIECE_CHESS","name":"Knight Chess Piece Diagram Piece","url":"IT_EQ_DIAGRAM_CHESS.png"}],"name":"[Hard] Ch 2: Ep 1 [2-10] Reclaim the Captured Princess"},"QE_ST_HA_020007":{"iname":"QE_ST_HA_020007","item_reward":[{"iname":"IT_PI_ALFRED","name":"Alfred Soul Shard","url":"IT_PI_ALFRED.png"},{"iname":"IT_EQ_PIECE_LANCE","name":"Great Lance Piece","url":"IT_EQ_ATK_LANCE.png"},{"iname":"IT_EQ_DIAGRAM_PEN","name":"Mage&#39;s Fountain Pen Diagram","url":"IT_EQ_DIAGRAM_PEN.png"}],"name":"[Hard] Ch 2: Ep 1 [3-1] Whereabouts of the Sacred Stone"},"QE_ST_HA_020008":{"iname":"QE_ST_HA_020008","item_reward":[{"iname":"IT_PI_MEILY","name":"Meily Soul Shard","url":"IT_PI_MEILY.png"},{"iname":"IT_EQ_DIAGRAMPIECE_HOOK","name":"Steel Hook Diagram Piece","url":"IT_EQ_DIAGRAM_HOOK.png"},{"iname":"IT_EQ_DIAGRAM_GETA","name":"Single-Tooth Geta Diagram","url":"IT_EQ_DIAGRAM_GETA.png"}],"name":"[Hard] Ch 2: Ep 1 [3-2] A Woman&#39;s Experience of the World"},"QE_ST_HA_020009":{"iname":"QE_ST_HA_020009","item_reward":[{"iname":"IT_PI_ISHUNA","name":"Ishuna Soul Shard","url":"IT_PI_ISHUNA.png"},{"iname":"IT_EQ_PIECE_RADIO","name":"Alchemic Radio Piece","url":"IT_EQ_GEM_RADIO.png"},{"iname":"IT_EQ_ATK_MACE","name":"Mace","url":"IT_EQ_ATK_MACE.png"}],"name":"[Hard] Ch 2: Ep 1 [3-9] Coat of Arms"},"QE_ST_HA_020010":{"iname":"QE_ST_HA_020010","item_reward":[{"iname":"IT_PI_RIGARUTO","name":"Rigalt Soul Shard","url":"IT_PI_RIGARUTO.png"},{"iname":"IT_EQ_PIECE_SHIELD","name":"Lion Monarch Shield Piece","url":"IT_EQ_DEF_SHIELD.png"},{"iname":"IT_EQ_PIECE_SABER","name":"Saber Piece","url":"IT_EQ_ATK_SABER.png"}],"name":"[Hard] Ch 2: Ep 1 [3-10] Guard of the Clock Tower"},"QE_ST_HA_020011":{"iname":"QE_ST_HA_020011","item_reward":[{"iname":"IT_PI_EDGAR","name":"Edgar Soul Shard","url":"IT_PI_EDGAR.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MAGICBREATH","name":"Magic Bracelet Diagram Piece","url":"IT_EQ_DIAGRAM_MAGICBREATH.png"},{"iname":"IT_EQ_ATK_WOODENSWORD","name":"Wooden Sword","url":"IT_EQ_ATK_WOODENSWORD.png"}],"name":"[Hard] Ch 2: Ep 2 [1-1] Timekeeper in Peril"},"QE_ST_HA_020012":{"iname":"QE_ST_HA_020012","item_reward":[{"iname":"IT_PI_GAYN","name":"Gane Soul Shard","url":"IT_PI_GAYN.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MAGICALLAMP","name":"Magical Lamp Diagram Piece","url":"IT_EQ_DIAGRAM_ARABIANLAMP.png"},{"iname":"IT_EQ_DEF_BANGLE","name":"Copper Bangle","url":"IT_EQ_DEF_BANGLE.png"}],"name":"[Hard] Ch 2: Ep 2 [1-10] Invisible Weapons"},"QE_ST_HA_020013":{"iname":"QE_ST_HA_020013","item_reward":[{"iname":"IT_PI_ALAIA","name":"Alaia Soul Shard","url":"IT_PI_ALAIA.png"},{"iname":"IT_EQ_PIECE_WATCH","name":"Old Pocket Watch Piece","url":"IT_EQ_SPD_WATCH.png"},{"iname":"IT_EQ_DIAGRAMPIECE_CRANE","name":"Prayer Crane Diagram Piece","url":"IT_EQ_DIAGRAM_CRANE.png"},{"iname":"IT_EQ_PIECE_CAPE","name":"Magic Cape Piece","url":"IT_EQ_MND_CAPE.png"}],"name":"[Hard] Ch 2: Ep 2 [2-4] Shorten the Required Time!"},"QE_ST_HA_020014":{"iname":"QE_ST_HA_020014","item_reward":[{"iname":"IT_PI_RINN","name":"Rin Soul Shard","url":"IT_PI_RINN.png"},{"iname":"IT_EQ_DIAGRAMPIECE_ZUKIN","name":"Master Ninja&#39;s Hood Diagram Piece","url":"IT_EQ_DIAGRAM_ZUKIN.png"},{"iname":"IT_EQ_DIAGRAMPIECE_CHESS","name":"Knight Chess Piece Diagram Piece","url":"IT_EQ_DIAGRAM_CHESS.png"},{"iname":"IT_EQ_HP_CAP","name":"Thick Hat","url":"IT_EQ_HP_CAP.png"}],"name":"[Hard] Ch 2: Ep 2 [2-5] Road to Recovering the Stone"},"QE_ST_HA_020015":{"iname":"QE_ST_HA_020015","item_reward":[{"iname":"IT_PI_LUCIDO","name":"Lucido Soul Shard","url":"IT_PI_LUCIDO.png"},{"iname":"IT_EQ_DIAGRAMPIECE_COWBOY","name":"Gunner&#39;s Hat Diagram Piece","url":"IT_EQ_DIAGRAM_COWBOY.png"},{"iname":"IT_EQ_PIECE_STAFF","name":"Star Staff Piece","url":"IT_EQ_MAG_STAFF.png"},{"iname":"IT_EQ_DIAGRAM_GETA","name":"Single-Tooth Geta Diagram","url":"IT_EQ_DIAGRAM_GETA.png"}],"name":"[Hard] Ch 2: Ep 2 [2-9] Through the Pitch-Black Darkness"},"QE_ST_HA_020016":{"iname":"QE_ST_HA_020016","item_reward":[{"iname":"IT_PI_ALEXIS","name":"Alexis Soul Shard","url":"IT_PI_ALEXIS.png"},{"iname":"IT_EQ_DIAGRAMPIECE_MEDICINE","name":"Medical Tome Diagram Piece","url":"IT_EQ_DIAGRAM_MEDICINE.png"},{"iname":"IT_EQ_DIAGRAMPIECE_BELT","name":"Black Belt Diagram Piece","url":"IT_EQ_DIAGRAM_BELT.png"},{"iname":"IT_EQ_PIECE_HOURGLASS","name":"Rainbow Hourglass Piece","url":"IT_EQ_SPD_HOURGLASS.png"}],"name":"[Hard] Ch 2: Ep 2 [2-10] Resounding Steam Whistle"},"QE_ST_HA_020017":{"iname":"QE_ST_HA_020017","item_reward":[{"iname":"IT_PI_POLLIN","name":"Polin Soul Shard","url":"IT_PI_POLLIN.png"},{"iname":"IT_EQ_PIECE_ROBE","name":"Saint&#39;s Robe Piece","url":"IT_EQ_MND_ROBE.png"},{"iname":"IT_EQ_PIECE_SHIELD","name":"Lion Monarch Shield Piece","url":"IT_EQ_DEF_SHIELD.png"},{"iname":"IT_EQ_SPD_FEATHER","name":"Blue Feather","url":"IT_EQ_SPD_FEATHER.png"}],"name":"[Hard] Ch 2: Ep 2 [3-1] Ride Towards the Execution Grounds"},"QE_ST_HA_020018":{"iname":"QE_ST_HA_020018","item_reward":[{"iname":"IT_PI_GAIN","name":"Gyan Soul Shard","url":"IT_PI_GAIN.png"},{"iname":"IT_EQ_DIAGRAMPIECE_GAUNTLET","name":"Lion Gauntlet Diagram Piece","url":"IT_EQ_DIAGRAM_GAUNTLET.png"},{"iname":"IT_EQ_DIAGRAMPIECE_WAIST","name":"Engineer&#39;s Waist Pouch Diagram Piece","url":"IT_EQ_DIAGRAM_WAIST.png"},{"iname":"IT_EQ_DIAGRAMPIECE_CIRCLET","name":"Circlet Diagram Piece","url":"IT_EQ_DIAGRAM_CIRCLET.png"}],"name":"[Hard] Ch 2: Ep 2 [3-2] Get Through the Hills"},"QE_ST_HA_020019":{"iname":"QE_ST_HA_020019","item_reward":[{"iname":"IT_PI_ANJOU","name":"Ange Soul Shard","url":"IT_PI_ANJOU.png"},{"iname":"IT_EQ_DIAGRAMPIECE_DRAGON","name":"Dragon Sphere Diagram Piece","url":"IT_EQ_DIAGRAM_DRAGON.png"},{"iname":"IT_EQ_PIECE_NOTEBOOK","name":"Research Notebook Piece","url":"IT_EQ_DEX_NOTEBOOK.png"},{"iname":"IT_EQ_MAG_WAND","name":"Wand","url":"IT_EQ_MAG_WAND.png"}],"name":"[Hard] Ch 2: Ep 2 [3-9] Steampunk Gunman"},"QE_ST_HA_020020":{"iname":"QE_ST_HA_020020","item_reward":[{"iname":"IT_PI_CIEL","name":"Ciel Soul Shard","url":"IT_PI_CIEL.png"},{"iname":"IT_EQ_DIAGRAMPIECE_RIFLE","name":"Nocturne Rifle Diagram Piece","url":"IT_EQ_DIAGRAM_RIFLE.png"},{"iname":"IT_EQ_PIECE_GEARROD","name":"Gear Rod Piece","url":"IT_EQ_MAG_GEARROD.png"},{"iname":"IT_EQ_PIECE_TRUMPET","name":"Trumpet Piece","url":"IT_EQ_GEM_TRUMPET.png"}],"name":"[Hard] Ch 2: Ep 2 [3-10] Utter Defeat"}}';

			return questListJson;
		}
	</script>