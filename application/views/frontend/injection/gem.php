<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
	<div class="container">
		<div class="card z-depth-1">
			<div class="card-header ch-alt">
				<h2>Gem</h2>
			</div>

			<div class="card-body card-padding">

				<p class="f-16 m-b-15">Select Gem</p>
				<div id="div_multiply">
					<label class="radio radio-inline m-r-20">
						<input type="radio" name="multiply" value="2" checked>
						<i class="input-helper"></i>
						100
					</label>
					<label class="radio radio-inline m-r-20">
						<input type="radio" name="multiply" value="20">
						<i class="input-helper"></i>
						1,000
					</label>
					<label class="radio radio-inline m-r-20">
						<input type="radio" name="multiply" value="40">
						<i class="input-helper"></i>
						2,000
					</label>
					<label class="radio radio-inline m-r-20">
						<input type="radio" name="multiply" value="60">
						<i class="input-helper"></i>
						3,000
					</label>
					<label class="radio radio-inline m-r-20">
						<input type="radio" name="multiply" value="100">
						<i class="input-helper"></i>
						5,000
					</label>
					<label class="radio radio-inline m-r-20">
						<input type="radio" name="multiply" value="200">
						<i class="input-helper"></i>
						10,000
					</label>
				</div>


				<p class="f-16 m-t-20 m-b-5">How many you want more ?</p>
				<div class="form-group fg-line m-b-15">
					<input id="how_many" type="text" class="form-control input-lg" placeholder="Enter Amount">
				</div>

				<button id="run_button" onclick="doInjection();" type="submit" class="btn btn-primary btn-block">Run</button>
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

		});

		function doInjection() {
			let url = '{post_doInjection}';
			let multiply = $('input[name=multiply]:checked', '#div_multiply').val();
			let quest_name = $('#quest_id :selected').text();

			let param = {
				'id': '{id}',
				'platform': $.cookie('platform'),
				'token': '{token}',
				'multiply': multiply,
			};

			function nextCompose() {

				$.ajax({
					type: "POST",
					url: url,
					data: param,
					cache: false,
					async: true,
					contentType: "application/x-www-form-urlencoded",
					beforeSend: function() {
						DisButton('run_button', 'Injecting...');
					},
					success: function(res) {

						try {
							var res_quest = resultQuest(res);
							if (res_quest == 'success') {
								notify('success', 'Inject Success');
							} else if (res_quest == 'wrong') {
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
								if (res.body.player.mail_count != undefined) {

									let mailCount = res.body.player.mail_count;
									let page = Math.round(mailCount / 20);
									if(page == 0) page++;
									// console.log('mail count '+mailCount);
									// console.log('page '+page);
									checkGemInMail(page);

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
								$('#text_log').prepend('<h4 class="bgm-red">' + text + '</h4>');
							}

						}

					}
				});

			}

			nextCompose();

			// end function
		}

		var mailId = [];
		var lastPage = 1;

		function checkGemInMail(page) {
			let url = '{post_checkGemInMail}';

			let currentPage = 1;

			mailId = [];

			function nextCompose() {

				let param = {
					'id': '{id}',
					'platform': $.cookie('platform'),
					'token': '{token}',
					'page': currentPage
				};

				$.ajax({
					type: "POST",
					url: url,
					data: param,
					cache: false,
					async: true,
					contentType: "application/x-www-form-urlencoded",
					beforeSend: function() {
						DisButton('run_button', 'Checking...');
					},
					success: function(res) {

						try {
							var res_quest = resultQuest(res);
							if (res_quest == 'success') {
							} else if (res_quest == 'wrong') {
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
								if (res != undefined) {
									res.forEach(mid => {
										mailId.push(mid);
									});
									// console.log('check page '+lastPage);
									if (lastPage >= page) {
										notify('success', 'Check Gem Success ' + lastPage + ' Page');
										lastPage = 1;
										getGemInMail();
									}
									lastPage++;
									return 'success';
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
								$('#text_log').prepend('<h4 class="bgm-red">' + text + '</h4>');
							}

						}

					}
				});

			}

			while (currentPage <= page) {
				nextCompose();
				// console.log('send '+currentPage);
				currentPage++;
			}

			// end function
		}

		var gem_total_done = 0;

		function getGemInMail() {
			let url = '{post_getGemInMail}';

			// console.log('count ' +mailId.length);
			mailId = JSON.stringify(mailId);

			let how_many_you_want_more = $('#how_many').val();
			let round = 1;

			function nextCompose() {

				let param = {
					'id': '{id}',
					'platform': $.cookie('platform'),
					'token': '{token}',
					'mail_id': mailId
				};

				$.ajax({
					type: "POST",
					url: url,
					data: param,
					cache: false,
					contentType: "application/x-www-form-urlencoded",
					beforeSend: function() {
						DisButton('run_button', 'Getting...');
					},
					success: function(res) {

						try {
							var res_quest = resultQuest(res);
							// ทำเควสเดียวตามจำนวนครั้ง
							// ถ้าเควสผ่าน
							if (res_quest == 'success') {
								if (gem_total_done < how_many_you_want_more) {
									// ส่งเควสถัดไป
									doInjection();
								} else {
									// ทำเควสครบแล้ว
									gem_total_done = 0;
									EnButton('run_button', 'Run');
								}
							} else if (res_quest == 'wrong') {
								// สั่งรหัสเควสเริ่ม = สิ้นสุดทันทีเพื่อหยุด
								how_many_you_want_more = gem_total_done;
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
								if (res.body.processed != undefined) {
									let count_gem = 0;
									res.body.processed.forEach(mails => {
										count_gem = count_gem + mails.gifts[1]['coin'];
									});
									gem_total_done = gem_total_done + count_gem;

									let percent = Math.round(gem_total_done * 100) / how_many_you_want_more;
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

									mailId = [];
									lastPage = 1;

									notify('success', 'Receive Gem ' + gem_total_done);

									$('#text_log').prepend(
										`<h4><img class="lgi-img" src="http://cdn.alchemistcodedb.com/images/items/icons/IT_COIN.png"> Total Free Gems - ${numberWithCommas(res.body.player.coin.free)} | Receive - ${numberWithCommas(gem_total_done)}</h4>`
									);
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
								$('#text_log').prepend('<h4 class="bgm-red">' + text + '</h4>');
							}

						}

					}
				});

			}

			nextCompose();

			// end function
		}
	</script>