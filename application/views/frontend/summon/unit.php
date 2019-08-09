<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
	<div class="container">
		<div class="card z-depth-1">
			<div class="card-header ch-alt">
				<h2>Summon Rare Unit Banner (Use 2,500 Gems)</h2>
			</div>

			<div class="card-body card-padding">

				<p class="f-16 m-b-5">Repeat / 1 Summon</p>
				<div class="form-group fg-line m-b-10">
					<input id="repeat_amount" value="1" type="text" class="form-control input-lg" placeholder="Enter Repeat Amount">
				</div>

				<button id="run_button" onclick="doSummon();" type="submit" class="btn btn-primary btn-block">Run</button>
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

		function doSummon() {

			let url = '{post_doSummon}';
			let repeat_amount = $('#repeat_amount').val();
			let round = 1;
			let banner_id = 'Rare_Gacha_10_ii';

			function nextCompose() {

				let param = {
					'id': '{id}',
					'platform': $.cookie('platform'),
					'banner_id': banner_id,
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

							let res_status = resultSummon(res);
							// ถ้าเควสผ่าน
							if (res_status == 'success') {
								// ถ้าเควสยังไม่เท่ากับเป้าหมาย
								let unitListArray = JSON.parse(unitListJson());

								let html_log = '';
								html_log += `<p class="m-b-20">${round} | Free Gems ${numberWithCommas(res.body.player.coin.free)} = `;

								res.body.add.forEach(unit_summon => {

									let name_unit_search = unit_summon.iname.replace('IT_PI_', '');
									name_unit_search = name_unit_search.replace('UN_V2_', '');
									name_unit_search = name_unit_search.replace('ZAHAR', 'SAHAR');
									name_unit_search = (name_unit_search.substring(0, 4));

									console.log(name_unit_search);
									// ค้นหายูนิตใน listUnitJson
									let currentUnit = unitListArray[name_unit_search];
									html_log = html_log +
										`<img style="heigth:35px; width:35px;" src="http://cdn.alchemistcodedb.com/images/units/icons/${currentUnit.url}">`;
									html_log = html_log + ` x ${unit_summon.num} `;
								});

								html_log = html_log + `</p>`;

								$('#text_log').prepend(html_log);
								
								round++;
								if (round <= repeat_amount) {
									nextCompose();
								} else {
									EnButton('run_button', 'Run');
								}
							} else {
								EnButton('run_button', 'Run');
							}
						} catch (error) {
							if (res.message) {
								renderErrorLog(res.message);
								notify('error', res.message);
								EnButton('run_button', 'Run');
							} else {
								notify('error', 'Program Error => ' + error);
								EnButton('run_button', 'Run');
							}
						}

						function resultSummon(res) {

							try {
								if (res.body.player) {
									return 'success';
								} else {
									notify('error', res.message);
									renderErrorLog(res.message);
									return 'wrong';
								}
							} catch (error) {
								if (res.message) {
									notify('error', res.message);
									renderErrorLog(res.message);
									return 'wrong';
								} else {
									renderErrorLog(error);
									notify('error', 'Program Error => ' + error);
									return 'wrong';
								}
							}
						}

						function renderErrorLog(text) {
							$('#text_log').prepend('<h4 class="bgm-red">' + text + '</h4>');
						}

					}
				});

			}

			nextCompose();


			// end function
		}

		function unitListJson() {
			let unitListJson =
				'{"ALAI":{"iname":"ALAI","name":"Alaia","url":"alai.png"},"ALBE":{"iname":"ALBE","name":"Albea","url":"albe.png"},"ALEX":{"iname":"ALEX","name":"Alexis","url":"alex.png"},"ALFR":{"iname":"ALFR","name":"Alfred","url":"alfr.png"},"ALMI":{"iname":"ALMI","name":"Almira","url":"almi.png"},"AYLL":{"iname":"AYLL","name":"Alyu","url":"ayll.png"},"AMIS":{"iname":"AMIS","name":"Amis","url":"amis.png"},"ANAS":{"iname":"ANAS","name":"Anastasia","url":"anas.png"},"ANJO":{"iname":"ANJO","name":"Ange","url":"anjo.png"},"ANNE":{"iname":"ANNE","name":"Annerose","url":"anne.png"},"ARKI":{"iname":"ARKI","name":"Arkil","url":"arki.png"},"BALT":{"iname":"BALT","name":"Balt","url":"balt.png"},"CADA":{"iname":"CADA","name":"Cadanova","url":"cada.png"},"CHAL":{"iname":"CHAL","name":"Caris","url":"chal.png"},"CELI":{"iname":"CELI","name":"Celine","url":"celi.png"},"CHIH":{"iname":"CHIH","name":"Chihaya","url":"chih.png"},"CLOE":{"iname":"CLOE","name":"Chloe","url":"cloe.png"},"CIEL":{"iname":"CIEL","name":"Ciel","url":"ciel.png"},"SITA":{"iname":"SITA","name":"Cita","url":"sita.png"},"DECE":{"iname":"DECE","name":"Decel","url":"dece.png"},"DIOS":{"iname":"DIOS","name":"Dias","url":"dios.png"},"DILG":{"iname":"DILG","name":"Dilga","url":"dilg.png"},"EDGA":{"iname":"EDGA","name":"Edgar","url":"edga.png"},"ELAI":{"iname":"ELAI","name":"Elaine","url":"elai.png"},"ELIZ":{"iname":"ELIZ","name":"Elizabeth","url":"eliz.png"},"ENNI":{"iname":"ENNI","name":"Ennis","url":"enni.png"},"EVE":{"iname":"EVE","name":"Eve","url":"eve.png"},"FLAM":{"iname":"FLAM","name":"Flamel","url":"flam.png"},"FRAI":{"iname":"FRAI","name":"Fraise","url":"frai.png"},"FREE":{"iname":"FREE","name":"Freed","url":"free.png"},"FUNG":{"iname":"FUNG","name":"Fung Liu","url":"fung.png"},"GAYN":{"iname":"GAYN","name":"Gane","url":"gayn.png"},"GATH":{"iname":"GATH","name":"Gato","url":"gath.png"},"GAIN":{"iname":"GAIN","name":"Gyan","url":"gain.png"},"HAZU":{"iname":"HAZU","name":"Hazuki","url":"hazu.png"},"ISHU":{"iname":"ISHU","name":"Ishuna","url":"ishu.png"},"JAKE":{"iname":"JAKE","name":"Jake","url":"jake.png"},"KAGU":{"iname":"KAGU","name":"Kagura","url":"kagu.png"},"CANO":{"iname":"CANO","name":"Kanon","url":"cano.png"},"KAZA":{"iname":"KAZA","name":"Kazahaya","url":"kaza.png"},"KUDH":{"iname":"KUDH","name":"Kudanstein","url":"kudh.png"},"KUON":{"iname":"KUON","name":"Kuon","url":"kuon.png"},"LAEV":{"iname":"LAEV","name":"Laevateinn","url":"laev.png"},"RUNB":{"iname":"RUNB","name":"Lambert","url":"runb.png"},"LAMI":{"iname":"LAMI","name":"Lamia","url":"lami.png"},"RION":{"iname":"RION","name":"Leon","url":"rion.png"},"LIES":{"iname":"LIES","name":"Lisbeth","url":"lies.png"},"ROFI":{"iname":"ROFI","name":"Lofia","url":"rofi.png"},"LOGI":{"iname":"LOGI","name":"Logi","url":"logi.png"},"LUCA":{"iname":"LUCA","name":"Lucian","url":"luca.png"},"LUCI":{"iname":"LUCI","name":"Lucido","url":"luci.png"},"LUCR":{"iname":"LUCR","name":"Lucretia","url":"lucr.png"},"MAGN":{"iname":"MAGN","name":"Magnus","url":"magn.png"},"MASA":{"iname":"MASA","name":"Masamune","url":"masa.png"},"MEGI":{"iname":"MEGI","name":"Megistos","url":"megi.png"},"MEIL":{"iname":"MEIL","name":"Meily","url":"meil.png"},"MERD":{"iname":"MERD","name":"Melda","url":"merd.png"},"MELI":{"iname":"MELI","name":"Melia","url":"meli.png"},"MARE":{"iname":"MARE","name":"Mia","url":"mare.png"},"MIAN":{"iname":"MIAN","name":"Mianne","url":"mian.png"},"MICH":{"iname":"MICH","name":"Michael","url":"mich.png"},"MIEL":{"iname":"MIEL","name":"Mielikki","url":"miel.png"},"MIZU":{"iname":"MIZU","name":"Mizuchi","url":"mizu.png"},"PATT":{"iname":"PATT","name":"Patty","url":"patt.png"},"POLL":{"iname":"POLL","name":"Polin","url":"poll.png"},"RAHU":{"iname":"RAHU","name":"Rahu","url":"rahu.png"},"REGE":{"iname":"REGE","name":"Reagan","url":"rege.png"},"REID":{"iname":"REID","name":"Reida","url":"reid.png"},"REIM":{"iname":"REIM","name":"Reimei","url":"reim.png"},"RETZ":{"iname":"RETZ","name":"Retzius","url":"retz.png"},"RICH":{"iname":"RICH","name":"Richie","url":"rich.png"},"RIGA":{"iname":"RIGA","name":"Rigalt","url":"riga.png"},"RINN":{"iname":"RINN","name":"Rin","url":"rinn.png"},"ROSA":{"iname":"ROSA","name":"Rosa","url":"rosa.png"},"RYLE":{"iname":"RYLE","name":"Ryle","url":"ryle.png"},"SABA":{"iname":"SABA","name":"Sabareta","url":"saba.png"},"SELE":{"iname":"SELE","name":"Selena","url":"sele.png"},"SHAY":{"iname":"SHAY","name":"Shayna","url":"shay.png"},"SHEK":{"iname":"SHEK","name":"Shekinah","url":"shek.png"},"SHEN":{"iname":"SHEN","name":"Shenmei","url":"shen.png"},"SARA":{"iname":"SARA","name":"Strauss","url":"sara.png"},"SUTO":{"iname":"SUTO","name":"Strie","url":"suto.png"},"SUZU":{"iname":"SUZU","name":"Suzuka","url":"suzu.png"},"TYRH":{"iname":"TYRH","name":"Tyrfing","url":"tyrh.png"},"VANE":{"iname":"VANE","name":"Vanekis","url":"vane.png"},"VARG":{"iname":"VARG","name":"Vargas","url":"varg.png"},"VETT":{"iname":"VETT","name":"Vettel","url":"vett.png"},"VINC":{"iname":"VINC","name":"Vincent","url":"vinc.png"},"YOMI":{"iname":"YOMI","name":"Yomi","url":"yomi.png"},"YUAN":{"iname":"YUAN","name":"Yuan","url":"yuan.png"},"SAHA":{"iname":"SAHA","name":"Zahar","url":"saha.png"},"ZANG":{"iname":"ZANG","name":"Zangetsu","url":"zang.png"}}';

			return unitListJson;
		}
	</script>