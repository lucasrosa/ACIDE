$(document).ready(function() {
	$('.sb-right-content').prepend("<hr>");
	$('.sb-right-content').prepend("<a onclick=\"codiad.update.check();\"><span class=\"icon-share bigger-icon\"></span>Update Check</a>");
	$('.sb-right-content').prepend("<a onclick=\"codiad.user.list();\"><span class=\"icon-users bigger-icon\"></span>Users</a>");
	$('.sb-right-content').prepend("<a onclick=\"codiad.plugin_manager.list();\"><span class=\"icon-tag bigger-icon\"></span>Plugins</a>");
});