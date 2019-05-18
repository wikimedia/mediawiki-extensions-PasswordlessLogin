( function () {
	console.log('test');
	var loadingDiv = $('<div />')
		.addClass('login-loading');

	$("[name='wploginattempt']")
		.css('display', 'none')
		.before(loadingDiv);

	setTimeout(function () {
		$("[name='userlogin']").submit();
	}, 3000)
}() );
