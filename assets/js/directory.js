

	var timer;              
	var doneTypingInterval = 1000;
	var $input = $('input');

	$input.on('keyup', function () {
	clearTimeout(timer);
	timer = setTimeout(doneTyping, doneTypingInterval);
	});

	$input.on('keydown', function () {
	clearTimeout(timer);
	});

	function doneTyping () {
	
		document.getElementById("search-directory").submit();
	}
