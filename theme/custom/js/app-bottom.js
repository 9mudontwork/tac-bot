$(document).ready(function () {
	$('body').on('click', '[data-ma-action]', function (e) {
		e.preventDefault();

		var $this = $(this);
		var action = $(this).data('ma-action');

		switch (action) {

			/*-------------------------------------------
			    Sidebar & Chat Open/Close
			---------------------------------------------*/
			case 'sidebar-open':
				var target = $this.data('ma-target');
				var backdrop = '<div data-ma-action="sidebar-close" class="ma-backdrop" />';

				$('body').addClass('sidebar-toggled');
				$('#header, #header-alt, #main').append(backdrop);
				$this.addClass('toggled');
				$(target).addClass('toggled');

				break;

			case 'sidebar-close':
				$('body').removeClass('sidebar-toggled');
				$('.ma-backdrop').remove();
				$('.sidebar, .ma-trigger').removeClass('toggled')

				break;


				/*-------------------------------------------
				    Mainmenu Submenu Toggle
				---------------------------------------------*/
			case 'submenu-toggle':
				$this.next().slideToggle(200);
				$this.parent().toggleClass('toggled');

				break;


				/*-------------------------------------------
				    Action Header Open/Close
				---------------------------------------------*/
				//Open
			case 'action-header-open':
				ahParent = $this.closest('.action-header').find('.ah-search');

				ahParent.fadeIn(300);
				ahParent.find('.ahs-input').focus();

				break;

				//Close
			case 'action-header-close':
				ahParent.fadeOut(300);
				setTimeout(function () {
					ahParent.find('.ahs-input').val('');
				}, 350);

				break;
		}
	});

});

$(document).ready(function () {
	/*-------------------------------------------
			Easy Pie Charts
	---------------------------------------------*/
	function easyPieChart(id, trackColor, scaleColor, barColor, lineWidth, lineCap, size) {
		$('.' + id).easyPieChart({
			trackColor: trackColor,
			scaleColor: scaleColor,
			barColor: barColor,
			lineWidth: lineWidth,
			lineCap: lineCap,
			size: size,
		});
	}

	// Main Pie Chart
	if ($('.main-pie')[0]) {
		easyPieChart('main-pie', 'rgba(0,0,0,0.2)', 'rgba(255,255,255,0)', 'rgba(255,255,255,0.7)', 2, 'butt', 148);
	}
});

$(document).ready(function () {

	/*----------------------------------------------------------
	    Dropdown Menu
	-----------------------------------------------------------*/
	if ($('.dropdown')[0]) {
		//Propagate
		$('body').on('click', '.dropdown.open .dropdown-menu', function (e) {
			e.stopPropagation();
		});

		$('.dropdown').on('shown.bs.dropdown', function (e) {
			if ($(this).attr('data-animation')) {
				$animArray = [];
				$animation = $(this).data('animation');
				$animArray = $animation.split(',');
				$animationIn = 'animated ' + $animArray[0];
				$animationOut = 'animated ' + $animArray[1];
				$animationDuration = ''
				if (!$animArray[2]) {
					$animationDuration = 500; //if duration is not defined, default is set to 500ms
				} else {
					$animationDuration = $animArray[2];
				}

				$(this).find('.dropdown-menu').removeClass($animationOut)
				$(this).find('.dropdown-menu').addClass($animationIn);
			}
		});

		$('.dropdown').on('hide.bs.dropdown', function (e) {
			if ($(this).attr('data-animation')) {
				e.preventDefault();
				$this = $(this);
				$dropdownMenu = $this.find('.dropdown-menu');

				$dropdownMenu.addClass($animationOut);
				setTimeout(function () {
					$this.removeClass('open')

				}, $animationDuration);
			}
		});
	}


	/*----------------------------------------------------------
	    Calendar Widget
	-----------------------------------------------------------*/
	if ($('#calendar-widget')[0]) {
		(function () {
			$('#calendar-widget #cw-body').fullCalendar({
				contentHeight: 'auto',
				theme: true,
				header: {
					right: '',
					center: 'prev, title, next',
					left: ''
				},
				defaultDate: '2014-06-12',
				editable: true,
				events: [{
						title: 'All Day',
						start: '2014-06-01',
					},
					{
						title: 'Long Event',
						start: '2014-06-07',
						end: '2014-06-10',
					},
					{
						id: 999,
						title: 'Repeat',
						start: '2014-06-09',
					},
					{
						id: 999,
						title: 'Repeat',
						start: '2014-06-16',
					},
					{
						title: 'Meet',
						start: '2014-06-12',
						end: '2014-06-12',
					},
					{
						title: 'Lunch',
						start: '2014-06-12',
					},
					{
						title: 'Birthday',
						start: '2014-06-13',
					},
					{
						title: 'Google',
						url: 'http://google.com/',
						start: '2014-06-28',
					}
				]
			});

			//Display Current Date as Calendar widget header
			var mYear = moment().format('YYYY');
			var mDay = moment().format('dddd, MMM D');
			$('#calendar-widget .cwh-year').html(mYear);
			$('#calendar-widget .cwh-day').html(mDay);
		})();
	}

	/*----------------------------------------------------------
	     Auto Size Textare
	-----------------------------------------------------------*/
	if ($('.auto-size')[0]) {
		autosize($('.auto-size'));
	}


	/*----------------------------------------------------------
	    Text Field
	-----------------------------------------------------------*/
	//Add blue animated border and remove with condition when focus and blur
	if ($('.fg-line')[0]) {
		$('body').on('focus', '.fg-line .form-control', function () {
			$(this).closest('.fg-line').addClass('fg-toggled');
		})

		$('body').on('blur', '.form-control', function () {
			var p = $(this).closest('.form-group, .input-group');
			var i = p.find('.form-control').val();

			if (p.hasClass('fg-float')) {
				if (i.length == 0) {
					$(this).closest('.fg-line').removeClass('fg-toggled');
				}
			} else {
				$(this).closest('.fg-line').removeClass('fg-toggled');
			}
		});
	}

	//Add blue border for pre-valued fg-flot text feilds
	if ($('.fg-float')[0]) {
		$('.fg-float .form-control').each(function () {
			var i = $(this).val();

			if (!i.length == 0) {
				$(this).closest('.fg-line').addClass('fg-toggled');
			}

		});
	}

	/*----------------------------------------------------------
	    Chosen
	-----------------------------------------------------------*/
	if ($('.chosen')[0]) {
		$('.chosen').chosen({
			width: '100%',
			allow_single_deselect: true,
			search_contains: true
		});
	}

	if ($('.chosen-image')[0]) {
		$('.chosen-image').chosenImage({
			width: '100%',
			allow_single_deselect: true,
			search_contains: true,
			parser_config: {
				copy_data_attributes: true
			}
		});

		let chosen = $('#quest_id').chosenImage().data('chosen');
		let autoClose = false;
		let chosen_resultSelect_fn = chosen.result_select;
		chosen.result_select = function (evt) {
			let resultHighlight = null;

			if (autoClose == false) {
				evt["metaKey"] = true;
				evt["ctrlKey"] = true;

				resultHighlight = chosen.result_highlight;
			}

			let result = chosen_resultSelect_fn.call(chosen, evt);

			if (autoClose == false && resultHighlight != null)
				resultHighlight.addClass("result-selected");

			return result;
		};
	}




	/*-----------------------------------------------------------
	    Date Time Picker
	-----------------------------------------------------------*/
	//Date Time Picker
	if ($('.date-time-picker')[0]) {
		$('.date-time-picker').datetimepicker();
	}

	//Time
	if ($('.time-picker')[0]) {
		$('.time-picker').datetimepicker({
			format: 'LT'
		});
	}

	//Date
	if ($('.date-picker')[0]) {
		$('.date-picker').datetimepicker({
			format: 'DD/MM/YYYY'
		});
	}

	$('.date-picker').on('dp.hide', function () {
		$(this).closest('.dtp-container').removeClass('fg-toggled');
		$(this).blur();
	})
});
