( function () {
	var server = mw.config.get('wgServer');
	var scriptPath = mw.config.get('wgScriptPath');
	var apiUrl = server + scriptPath + '/api.php';

	var pairToken = $('#mw-input-pairToken');
	pairToken.after(
		$('<div/>')
			.addClass('passwordless-pair-token')
			.qrcode(apiUrl + ';' + pairToken.val())
	)
}() );
