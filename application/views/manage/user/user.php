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

					.user-list {
						max-height: 300px;
						overflow-x: hidden !important;
					}
				</style>

				<div id="user_list">
					<div class="form-group fg-line m-b-10">
						<input id="search_user" type="text" class="fuzzy-search form-control input-lg" placeholder="Search... User">
					</div>
					<div class="user-list c-overflow m-b-15"></div>
				</div>
			</div>
		</div>

		<div class="card z-depth-1">
			<div class="card-header ch-alt">
				<h2>
					Edit
				</h2>
			</div>

			<div class="card-body card-padding">
				<form class="row" role="form">
					<div class="col-sm-4">
						<div class="form-group fg-line">
							<label for="edit_email">Email</label>
							<input type="hidden" class="form-control input-sm" id="edit_id" placeholder="Email">
							<input type="text" class="form-control input-sm" id="edit_email" placeholder="Email">
						</div>
					</div>

					<div class="col-sm-4">
						<div class="form-group fg-line">
							<label for="edit_device_id">Id</label>
							<input type="text" class="form-control input-sm" id="edit_device_id" placeholder="Id">
						</div>
					</div>

					<div class="col-sm-4">
						<div class="form-group fg-line">
							<label for="edit_secret_key">Key</label>
							<input type="text" class="form-control input-sm" id="edit_secret_key" placeholder="Key">
						</div>
					</div>
				</form>

				<form class="row" role="form">
					<div class="col-sm-4">
						<div class="form-group fg-line">
							<label for="edit_device_id_ap">Id AP</label>
							<input type="text" class="form-control input-sm" id="edit_device_id_ap" placeholder="Id AP">
						</div>
					</div>

					<div class="col-sm-4">
						<div class="form-group fg-line">
							<label for="edit_secret_key_ap">Key AP</label>
							<input type="text" class="form-control input-sm" id="edit_secret_key_ap" placeholder="Key AP">
						</div>
					</div>
				</form>

				<div class="form-group">
					<div class="fg-line">
						<textarea class="form-control" rows="5" id="edit_owner" placeholder="ข้อมูลเจ้าของไอดี"></textarea>
					</div>
				</div>

				<form class="row" role="form">
					<div class="col-sm-3">
						<div class="form-group fg-line">
							<label for="edit_bot_day">Bot Day</label>
							<input type="text" class="form-control input-sm" id="edit_bot_day" placeholder="Bot Day">
						</div>
					</div>
					<div class="col-sm-3">
						<div class="form-group fg-line">
							<label for="edit_date_buy_bot">Date Buy Bot</label>
							<input type="text" class="form-control input-sm" id="edit_date_buy_bot" placeholder="Date Buy Bot">
						</div>
					</div>

					<div class="col-sm-2">
						<button type="button" class="btn btn-default btn-sm" onclick="setTimeNow();">Set Time Now</button>
					</div>
				</form>

				<div class="form-group">
					<label class="radio radio-inline m-r-20">
						<input type="radio" name="edit_platform" value="android">
						<i class="input-helper"></i>
						Android
					</label>
					<label class="radio radio-inline m-r-20">
						<input type="radio" name="edit_platform" value="ios">
						<i class="input-helper"></i>
						iOS
					</label>
					<label class="checkbox checkbox-inline m-r-20">
						<input type="checkbox" id="edit_gem">
						<i class="input-helper"></i>
						Gem
					</label>
				</div>
				<button id="save_edit_button" onclick="updateUser();" type="button" class="btn btn-success m-t-10">Save Edit</button>
				<button id="delete_button" onclick="deleteUser();" type="button" class="btn btn-danger m-t-10">Delete</button>
			</div>
		</div>

		<div class="card z-depth-1">
			<div class="card-header ch-alt">
				<h2>
					Add New User
				</h2>
			</div>

			<div class="card-body card-padding">
				<form class="row" role="form">
					<div class="col-sm-4">
						<div class="form-group fg-line">
							<label for="email">Email</label>
							<input type="text" class="form-control input-sm" id="email" placeholder="Email">
						</div>
					</div>

					<div class="col-sm-4">
						<div class="form-group fg-line">
							<label for="device_id">Id</label>
							<input type="text" class="form-control input-sm" id="device_id" placeholder="device_id">
						</div>
					</div>

					<div class="col-sm-4">
						<div class="form-group fg-line">
							<label for="secret_key">Key</label>
							<input type="text" class="form-control input-sm" id="secret_key" placeholder="secret_key">
						</div>
					</div>
				</form>
				<div class="form-group">
					<div class="fg-line">
						<textarea class="form-control" id="owner" rows="5" placeholder="ข้อมูลเจ้าของไอดี"></textarea>
					</div>
				</div>
				<div class="form-group">
					<label class="radio radio-inline m-r-20">
						<input type="radio" name="platform" value="android">
						<i class="input-helper"></i>
						Android
					</label>
					<label class="radio radio-inline m-r-20">
						<input type="radio" name="platform" value="ios">
						<i class="input-helper"></i>
						iOS
					</label>
				</div>
				<button id="save_button" onclick="addUser();" type="button" class="btn btn-primary btn-block m-t-10">Save</button>
			</div>
		</div>

	</div>

	</div>

	<script>
		$(document).ready(function() {

			const userListArray = JSON.parse(userListJson());

			if (userListArray != '') {
				let html = '';
				html = html + '<ul class="list p-l-0 p-t-5">';
				$.each(userListArray, function(i, user) {

					let dataJson = JSON.stringify(user);

					html = html + `<li class="radio m-t-0"><label class="test-data">`;
					html = html +
						`<input onclick='setEditData(${dataJson});' name="user" id="${user.id}" value="${user.id}" type="radio">`;
					html = html + '<i class="input-helper"></i>';
					html = html +
						`<h4 class="user">${user.id} - ${user.user} | ${user.last_login}</h4>`;
					html = html + '</label></li>';

				});
				html = html + '</ul>';

				$('#mCSB_2_container').append(html);

				var monkeyList = new List('user_list', {
					valueNames: [
						'user',
					],
					fuzzySearch: {
						searchClass: "fuzzy-search",
						location: 0,
						distance: 200,
						threshold: 0.4,
						multiSearch: true
					}
				});
			}


		});

		function setEditData(data) {
			let dataValue = {
				'edit_id': data.id,
				'edit_email': data.user,
				'edit_device_id': data.device_id,
				'edit_secret_key': data.secret_key,
				'edit_device_id_ap': data.device_id_ap,
				'edit_secret_key_ap': data.secret_key_ap,
				'edit_owner': data.owner.replace(/<br\s*[\/]?>/gi, "\n"),
				'edit_bot_day': data.bot_day,
				'edit_date_buy_bot': data.date_buy_bot,
			}
			setHtmlByVal(dataValue);

			if (data.gem == 1) {
				$('input[id="edit_gem"]').prop('checked', true);
			} else {
				$('input[id="edit_gem"]').prop('checked', false);
			}

			if (data.platform == 'android') {
				$('input[name="edit_platform"][value="android"]').prop('checked', true);
			} else {
				$('input[name="edit_platform"][value="ios"]').prop('checked', true);
			}
		}

		function setTimeNow() {
			let timeNow = {
				'edit_date_buy_bot': moment().format("YYYY-MM-DD HH:mm:ss"),
			}
			setHtmlByVal(timeNow);
		}

		function updateUser() {

			let url = '{post_updateUser}';

			let platform = '';
			if ($('input[name="edit_platform"][value="android"]').prop('checked') == true) {
				platform = 'android';
			} else if ($('input[name="edit_platform"][value="ios"]').prop('checked') == true) {
				platform = 'ios';
			}

			let gem = 0;
			if ($('input[id="edit_gem"]').prop('checked') == true) {
				gem = 1;
			} else {
				gem = 0;
			}

			let param = {
				'id': $('#edit_id').val(),
				'user': $('#edit_email').val(),
				'platform': platform,
				'gem': gem,
				'device_id': $('#edit_device_id').val(),
				'secret_key': $('#edit_secret_key').val(),
				'device_id_ap': $('#edit_device_id_ap').val(),
				'secret_key_ap': $('#edit_secret_key_ap').val(),
				'owner': $('#edit_owner').val(),
				'bot_day': $('#edit_bot_day').val(),
				'date_buy_bot': $('#edit_date_buy_bot').val(),
			};

			if ($('#edit_id').val() != '') {
				$.ajax({
					type: "POST",
					url: url,
					data: param,
					cache: false,
					contentType: "application/x-www-form-urlencoded",
					beforeSend: function() {
						DisButton('save_edit_button', 'Loading...');
					},
					success: function(res) {

						try {

							if (res.msg == true) {
								notify('success', 'Update => ' + $('#edit_email').val());
								// EnButton('save_edit_button', 'Save Edit');
								setTimeout(location.reload.bind(location), 1000);
							} else {
								notify('error', 'Not have any change => ' + $('#edit_email').val());
								EnButton('save_edit_button', 'Save Edit');
							}

						} catch (error) {
							notify('error', 'Program Error => ' + error);
							EnButton('save_edit_button', 'Save Edit');
						}

					}
				});
			} else {
				notify('error', 'Please Select User');
				EnButton('save_edit_button', 'Save Edit');
			}

		}

		function addUser() {

			let url = '{post_addUser}';

			let platform = '';
			if ($('input[name="platform"][value="android"]').prop('checked') == true) {
				platform = 'android';
			} else if ($('input[name="platform"][value="ios"]').prop('checked') == true) {
				platform = 'ios';
			}

			let param = {
				'user': $('#email').val(),
				'platform': platform,
				'device_id': $('#device_id').val(),
				'secret_key': $('#secret_key').val(),
				'owner': $('#owner').val(),
			};

			if ($('#email').val() != '') {
				$.ajax({
					type: "POST",
					url: url,
					data: param,
					cache: false,
					contentType: "application/x-www-form-urlencoded",
					beforeSend: function() {
						DisButton('save_button', 'Loading...');
					},
					success: function(res) {

						try {

							if (res.msg == true) {
								notify('success', 'Insert => ' + res.id);
								// EnButton('save_button', 'Save');
								setTimeout(location.reload.bind(location), 1000);
							} else {
								notify('error', 'Can\'t Add New User');
								EnButton('save_button', 'Save');
							}

						} catch (error) {
							notify('error', 'Program Error => ' + error);
							EnButton('save_button', 'Save');
						}

					}
				});
			} else {
				notify('error', 'Please Insert Data');
				EnButton('save_button', 'Save');
			}

		}

		function deleteUser() {

			let url = '{post_deleteUser}';

			let param = {
				'id': $('#edit_id').val(),
			};

			if ($('#edit_id').val() != '') {
				$.ajax({
					type: "POST",
					url: url,
					data: param,
					cache: false,
					contentType: "application/x-www-form-urlencoded",
					beforeSend: function() {
						DisButton('delete_button', 'Loading...');
					},
					success: function(res) {

						try {

							if (res.msg == true) {
								notify('success', 'Delete => ' + $('#edit_id').val());
								// EnButton('delete_button', 'Delete');
								setTimeout(location.reload.bind(location), 1000);
							} else {
								notify('error', 'Can\'t Delete => ' + $('#edit_id').val());
								EnButton('delete_button', 'Delete');
							}

						} catch (error) {
							notify('error', 'Program Error => ' + error);
							EnButton('delete_button', 'Delete');
						}

					}
				});
			} else {
				notify('error', 'Please Select User');
				EnButton('delete_button', 'Delete');
			}

		}

		function userListJson() {
			let userListJson =
				'<?php echo $user_list; ?>';

			return userListJson;
		}
	</script>