var afterAjaxCall = function (div) {

	if (!$(div)) {
		return;
	}
	$(div).identify();
	mdlDiv = ('["' + $(div).id + '"]').replace('/', '","', 'gi');
	var frm = eval(mdlDiv).last();
	if (!$(frm)) return;
	$(frm).observe('dom:close', function (event) {
		//$(frm).hide()
		//Event.stop(event);
	})
	$(frm).observe('click', function (event) {
		var element = Event.element(event);
		if ($(element).hasClassName('cancelClose')) {
			setTimeout(function(){
				$(element).up().fire('dom:close');
				//Event.stop(event);
			}.bind(this),350);
		}
		if ($(element).hasClassName('cancelClean')) {
			$(frm).update()
			Event.stop(event);
		}
		if ($(element).hasClassName('cancelRemove')) {
			$(frm).update()
			Event.stop(event);
		}
		if ($(element).hasClassName('cancelHide')) {
			$(frm).hide()
			Event.stop(event);
		}
		if ($(element).hasClassName('cancelButton')) {
			$(Event.element(event)).up().fire('dom:close');
			Event.stop(event);
		}
		if ($(element).hasClassName('cancelToggle')) {
			$(frm).unToggleContent();
		}
		if ($(element).hasClassName('cancelFade')) {
			$(frm).fade({afterFinish: function () {
				$(frm).remove()
			}})
			Event.stop(event);
		}
		if ($(element).hasClassName('cancelRemove')) {
			$(frm).remove();
			Event.stop(event);
		}
		if ($(element).match('input[type=submit]')) {
			$(element).blur()
		}
	},false)

	$A($(frm).querySelectorAll('input[type=text]')).each(function (element, i) {
		$(element).observe('focus', function (event, element) {
			element = Event.element(event);
			$(element).activate();
		}.bind(this), true);
	}.bind(this))

	return $(frm);
};
