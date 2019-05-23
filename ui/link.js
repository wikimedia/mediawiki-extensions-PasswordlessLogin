( function () {
	var server = mw.config.get('wgServer');
	var scriptPath = mw.config.get('wgScriptPath');
	var accountName = mw.config.get('wgSiteName');
	var apiUrl = server + scriptPath + '/api.php';

	var pairToken = $('#mw-input-pairToken');
	pairToken.after(
		$('<div/>')
			.addClass('passwordless-pair-token')
			.qrcode(accountName + ";" + apiUrl + ';' + pairToken.val())
	)
}() );
