var SITE = (function($) {

	function init() {

	}

	function anotherOne() {

	}

	return {
		init: 	init,
		anotherOne: anotherOne
	}

}(jQuery));

$(document).ready(function() {
	SITE.init();
	SITE.anotherOne();
});
