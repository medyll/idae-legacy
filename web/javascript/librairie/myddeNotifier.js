myddeNotifier = Class.create({
	initialize: function (options) {
		this.options = Object.extend({
			location: "tr",
			width: "tr",
			className: 'myddeNotifier',
			vars: ''
		}, options || {});

		this.tpl = '<div id="notifierNotice flex_h"><div class=""></div><div class=""></div></div>'

		if (!$('myddeNotifier')) {
			this.growler = new Element("div", {className: this.options.className, id: "myddeNotifier"});
			this.growler.wrap(document.body);
		} else {
			this.growler = $('myddeNotifier');
		}
		this.growler.setStyle({width: this.options.width, "z-index": "50000"});
		switch (this.options.location) {
			case "br":
				this.growler.setStyle({bottom: 0, right: 0});
				break;
			case "tl":
				this.growler.setStyle({top: 0, left: 0});
				break;
			case "bl":
				this.growler.setStyle({top: 0, right: 0});
				break;
			case "tc":
				this.growler.setStyle({top: 0, left: "25%", width: "50%"});
				break;
			case "bc":
				this.growler.setStyle({bottom: 0, left: "25%", width: "50%"});
				break;
			default:
				//this.growler.setStyle({top: 0, right: 0});
				break;
		}

	}
	, growl: function (msg, options) {
		this.options = Object.extend({
			//location: 			"tr",
			//width: 			"250px",
			//sticky:			false
		}, options || {});
		this.buildNotice(this.growler, msg);
	}
	, buildNotice: function (growler, msg) {
		var notice;
		this.growler.show();
		if (this.options.id) {

			if ($('notice_' + this.options.id)) {
				$('notice_' + this.options.id).insert(msg);
				return $('notice_' + this.options.id);
			}
			notice = new Element("div", {"class": "notifierNotice"}).insert(msg);
			notice.id = 'notice_' + this.options.id;
		} else {
			notice = new Element("div", {"class": "notifierNotice"}).insert(msg);
		}
		if (this.options.sticky) {

		} else {

		}
		this.growler.insert(notice);
		if (this.options.mdl) {
			$(notice).update('<div id="in_' + $(notice).identify() + '"></div>')
			$('in_' + $(notice).identify()).loadModule(this.options.mdl, this.options.vars);
		}
		if (this.options.sticky) {
			notice.insert({top: '<div class="titre_entete alignright closer applink applinblock"><a><i class="fa fa-times"></i> fermer</a></div> '})
			notice.on('click', '.closer', function () {
				this.removeNotice(notice)
			}.bind(this));
		}
		new Effect.Opacity(notice, {to: 0.85, duration: this.options.speedin});
		if (!this.options.sticky) {
			var zz = setTimeout(function () {
				this.removeNotice(notice);
			}.bind(this), 5000)
		}
		

		return notice;
	},
	removeNotice: function (n) {
		n.remove({duration: 0.3});
		if (this.growler.empty()) this.growler.hide();
		;
	}
});
 
