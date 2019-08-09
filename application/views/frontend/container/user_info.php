<?php
defined('BASEPATH') or exit('No direct script access allowed');
?>
	<div class="container">
		<div class="alert alert-danger" role="alert">User has Expire at {expire} (GTM+7)</div>

		<div class="card z-depth-1">
			<div class="card-header" id="platform_list">
				<label class="radio radio-inline m-r-20">
					<input type="radio" name="platform" value="android" onclick="changePlatform('android')">
					<i class="input-helper"></i>
					Android
				</label>
				<label class="radio radio-inline m-r-20">
					<input type="radio" name="platform" value="ios" onclick="changePlatform('ios')">
					<i class="input-helper"></i>
					iOS
				</label>

			</div>

			<div class="card-body">
				<div class="list-group lg-even-white">

					<div class="list-group-item media sv-item">
						<div class="pull-right">
							<div class="stats-bar"></div>
						</div>
						<div class="media-body">
							<small>Token</small>
							<h3 id="token">{token}</h3>

						</div>
					</div>

					<div class="list-group-item media sv-item">
						<div class="pull-right">
							<div class="stats-bar"></div>
						</div>
						<div class="media-body">
							<small>Token AP</small>
							<h3 id="token_ap">{token_ap}</h3>

						</div>
					</div>


					<div class="list-group-item media sv-item">
						<div class="pull-right">
							<div class="stats-bar"></div>
						</div>
						<div class="media-body">
							<small>In Game Name</small>
							<h3 id="in_game_name"></h3>
						</div>
					</div>

					<div class="list-group-item media sv-item">
						<div class="pull-right">
							<div class="stats-bar"></div>
						</div>
						<div class="media-body">
							<small>Free Gem</small>
							<h3 id="free_gem"></h3>
						</div>
					</div>

					<div class="list-group-item media sv-item">
						<div class="pull-right">
							<div class="stats-bar"></div>
						</div>
						<div class="media-body">
							<small>Paid Gem</small>
							<h3 id="paid_gem"></h3>
						</div>
					</div>

					<div class="list-group-item media sv-item">
						<div class="pull-right">
							<div class="stats-bar"></div>
						</div>
						<div class="media-body">
							<small>Gold</small>
							<h3 id="gold"></h3>
						</div>
					</div>

					<div id="btlid_div" class="list-group-item media sv-item hidden">
						<div class="pull-right">
							<div class="stats-bar"></div>
						</div>
						<div class="media-body">
							<small>Btlid</small>
							<h3 id="btlid"></h3>
						</div>
					</div>

				</div>

			</div>

		</div>

		<style lang="css">
			.my-btn-group>.btn {
				margin: 0 5px 10px 0;
			}
		</style>
		<div class="my-btn-group">
			<button id="getToken_Button" onclick="getToken();" type="button" class="btn btn-default">Get Token</button>
			<button id="getTokenAP_Button" onclick="getTokenAP();" type="button" class="btn btn-default waves-effect">Get Token AP</button>
			<button id="checkInfo_Button" onclick="getInfo();" type="button" class="btn btn-default waves-effect">Check Info</button>
		</div>

	</div>

	<script>
		function changePlatform(platform) {
			if (platform == 'android') {
				$.cookie('platform', 'android', {
					expires: 7,
					path: '/'
				});
				notify('success', 'Change to Android');
			} else {
				$.cookie('platform', 'ios', {
					expires: 7,
					path: '/'
				});
				notify('success', 'Change to iOS');
			}
		}

		function getToken() {

			let url = '{post_getToken}';
			let param = {
				'id': '{id}',
				'platform': $.cookie('platform'),
				'device_id': '{device_id}',
				'secret_key': '{secret_key}',
			};

			$.ajax({
				type: "POST",
				url: url,
				data: param,
				cache: false,
				contentType: "application/x-www-form-urlencoded",
				beforeSend: function() {
					DisButton('getToken_Button', 'Loading...');
				},
				success: function(res) {

					try {
						if (res.body.access_token) {
							let data_set_html = {
								'token': res.body.access_token,
							};
							setHtmlById(data_set_html);

							notify('success', 'Get Token Success');
							EnButton('getToken_Button', 'Get Token');
						} else {
							notify('error', res.message);
							EnButton('getToken_Button', 'Get Token');
						}
					} catch (error) {
						if (res.message) {
							notify('error', res.message);
							EnButton('getToken_Button', 'Check Info');
						} else {
							notify('error', 'Program Error => ' + error);
							EnButton('getToken_Button', 'Check Info');
						}
					}

				}
			});
		}

		function getTokenAP() {

			let url = '{post_getTokenAP}';
			let param = {
				'id': '{id}',
				'platform': $.cookie('platform'),
				'device_id_ap': '{device_id_ap}',
				'secret_key_ap': '{secret_key_ap}',
			};

			$.ajax({
				type: "POST",
				url: url,
				data: param,
				cache: false,
				contentType: "application/x-www-form-urlencoded",
				beforeSend: function() {
					DisButton('getTokenAP_Button', 'Loading...');
				},
				success: function(res) {

					// console.log(res);

					try {
						if (res.body.access_token) {
							let data_set_html = {
								'token_ap': res.body.access_token,
							};
							setHtmlById(data_set_html);

							notify('success', 'Get Token AP Success');
							EnButton('getTokenAP_Button', 'Get Token AP');

							if (param.device_id_ap == '' || param.secret_key_ap == '') {
								location.reload();
							}
						} else {
							notify('error', res.message);
							EnButton('getTokenAP_Button', 'Get Token AP');
						}
					} catch (error) {
						if (res.message) {
							notify('error', res.message);
							EnButton('getTokenAP_Button', 'Check Info');
						} else {
							notify('error', 'Program Error => ' + error);
							EnButton('getTokenAP_Button', 'Check Info');
						}
					}

				}
			});
		}


		function getInfo() {

			let url = '{post_getInfo}';
			let param = {
				'id': '{id}',
				'platform': $.cookie('platform'),
				'token': getHtmlById('token'),
			};

			$.ajax({
				type: "POST",
				url: url,
				data: param,
				cache: false,
				contentType: "application/x-www-form-urlencoded",
				beforeSend: function() {
					DisButton('checkInfo_Button', 'Loading...');
				},
				success: function(res) {

					try {
						if (res.body.player) {
							let data_set_html = {
								'in_game_name': res.body.player.name,
								'gold': res.body.player.gold,
								'free_gem': res.body.player.coin.free,
								'paid_gem': res.body.player.coin.paid,
								'btlid': res.body.player.btlid,
							};
							if (res.body.player.btlid) {
								$("#btlid_div").removeClass("hidden");
							}
							setHtmlById(data_set_html);

							notify('success', '[ ' + $.cookie('platform') + ' ] Check Info Success');
							EnButton('checkInfo_Button', 'Check Info');
						} else {
							notify('error', res.message);
							EnButton('checkInfo_Button', 'Check Info');
						}
					} catch (error) {

						if (res.message) {
							notify('error', res.message);
							EnButton('checkInfo_Button', 'Check Info');
						} else {
							notify('error', 'Program Error => ' + error);
							EnButton('checkInfo_Button', 'Check Info');
						}

					}

				}
			});
		}
	</script>