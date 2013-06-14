(function($) {

	// jQuery plugin definition
	$.fn.timePicker = function(params) {

		// merge default and user parameters
		params = $.extend( {defaultTime: 0, mouseoverClass: 'jquery-timepicker-mouseover'}, params);
		
		// some default cars
		var newHTML = '';
		var $t = $(this);
		
		// calculate the offsets
		var height = this.height()+1;
		var width = this.outerWidth();
		
		// generate our html dropdown
		var timeMargins = ['00', '30'];
		newHTML += ' <select style="position: absolute; left:0;top:' + height + 'px; width: ' + width + 'px;" size="7">'
		for(var h = 0; h <= 23; h++)
		{
			for(var i = 0; i < timeMargins.length; i++)
			{
				var newhour = "" + h;
				var v = (newhour.length == 1 ? '0' : '') + h + ':' + timeMargins[i];
				newHTML += '<option>' + v + '</option>';
			}
		}
		newHTML += '</select>';
		
		var id = this.attr('id');
		var newid = id + '-container';
		
		// wrap the target in the div
		$t.wrap('<div id="' + newid + '" style="position: relative; display: inline;"></div>');
		$t.after(newHTML);
		
		// hide the dropdown now we've injected it
		$("#" + newid + " select").hide();
		
		// 1) Show the dropdown if we focus on the input
		$t.focus(function () {
			$("#" + newid + " select").show();
		});		
		
		// 2) Hide the dropdown if we've clicked it
		/*$("#" + newid + " select").click(function (e) {
			$(this).hide();
		});*/

		// 3) Hide the dropdown if we lose focus
		/*$t.blur(function (e) {
				//$(this).val($("#" + newid + " select").val()); // put the value in the input
				//$("#" + newid + " select").hide(); // hide the select
			$("#" + newid + " select").change();
		});*/
		$("#" + newid + " select").change(function () {
	        $t.val($(this).val());
	        $(this).hide();
		});
		
		// 4) Assign mouseover/mouseout to the options
		$("#" + newid + " select option").mouseover(function () {
			$(this).addClass(params.mouseoverClass);
		});
		$("#" + newid + " select option").mouseout(function () {
			$(this).removeClass(params.mouseoverClass);
		});

		// allow jQuery chaining
		return this;
	};

})(jQuery);