$(document).ready(function () {
	if ($.cookie('platform') == undefined) {
		$.cookie('platform', 'android', {
			expires: 7,
			path: '/'
		});
		$('input[name="platform"][value="android"]').prop('checked', true);
	} else if ($.cookie('platform') == 'android') {
		$('input[name="platform"][value="android"]').prop('checked', true);
	} else {
		$('input[name="platform"][value="ios"]').prop('checked', true);
	}
});

function numberWithCommas(x) {
	return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
}

function notify(type, text) {
	// set toastr
	toastr.options = {
		"closeButton": false,
		"debug": false,
		"newestOnTop": false,
		"progressBar": false,
		"positionClass": "toast-top-right",
		"preventDuplicates": false,
		"onclick": null,
		"showDuration": "100",
		"hideDuration": "1000",
		"timeOut": "2000",
		"extendedTimeOut": "1000",
		"showEasing": "swing",
		"hideEasing": "linear",
		"showMethod": "fadeIn",
		"hideMethod": "fadeOut"
	}

	return toastr[type](text);
};

function EnButton(id, text) {
	$("#" + id + "").html(text);
	$("#" + id + "").removeClass('btn-icon-text');
	$("#" + id + "").attr('disabled', false);
}

function DisButton(id, text) {
	$("#" + id + "").html('<i class="zmdi zmdi-refresh zmdi-hc-spin"></i> ' + text);
	$("#" + id + "").addClass('btn-icon-text');
	$("#" + id + "").attr('disabled', true);
}

function setHtmlById(data_set_html) {
	for (id in data_set_html) {
		$("#" + id + "").html(data_set_html[id]);
	}
}

function getHtmlById(id) {
	return $("#" + id + "").html();
}

function setHtmlByVal(data_set_val) {
	for (id in data_set_val) {
		$("#" + id + "").val(data_set_val[id]);
	}
}