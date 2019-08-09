$(document).ready(function () {

	/*----------------------------------------------------------
	    Scrollbar
	-----------------------------------------------------------*/
	function scrollBar(selector, theme, mousewheelaxis) {
		$(selector).mCustomScrollbar({
			theme: theme,
			scrollInertia: 500,
			axis: 'mousewheelaxis',
			mouseWheel: {
				enable: true,
				axis: mousewheelaxis,
				preventDefault: true
			}
		});
	}

	if (!$('html').hasClass('ismobile')) {
		//On Custom Class
		if ($('.c-overflow')[0]) {
			scrollBar('.c-overflow', 'minimal-dark', 'y');
		}
	}
});
