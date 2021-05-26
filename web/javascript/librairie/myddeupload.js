myddeUpload = {};
myddeUpload = Class.create();
myddeUpload.prototype = {
	initialize: function (element, options) {
		this.element = $(element)
		console.log('uploader - ',this.element);
		this.options = Object.extend({
			listing: '',
			hoverZone: null,
			form: null,
			action: null,
			data: null,
			onChange: Prototype.emptyFunction,
			onEndUpload: Prototype.emptyFunction,
			uploadType: 'img'
		}, options || {});

		this.form = $(this.options.form);
		if (this.form != null && this.options.action == null) {
			this.options.action = this.form.readAttribute('action')
		}
		this.progressBars = [];
		this.uploadType = this.options.uploadType;
		this.listing = $(this.options.listing || this.element.appendChild(new Element('div', {className: 'listing'})));
		this.element.cleanWhitespace();
		this.timy = '';
		this.filereader = new FileReader();
		//
		if (this.element.tagName == 'FORM') {
			$(this.form).observe("drop", this.handleDrop.bindAsEventListener(this), false);
			$(this.form).observe("submit", this.inputUpload.bindAsEventListener(this), false);
			$(this.form).observe("change", this.changeUpload.bindAsEventListener(this), false);
		}
		if (this.element.tagName != 'FORM') {
			$(this.element).observe("dragover", this.dragOver.bindAsEventListener(this), false)
			$(this.element).observe("dragenter", this.dragEnter.bindAsEventListener(this), false)
			$(this.element).observe("drop", this.handleDrop.bindAsEventListener(this), false)
		}

		$(document.body).on("dragenter", function (event) {
			this.dragEnter(event);
		}.bind(this))
		$(this.listing).on("dragover", function (event) {
			this.dragOver(event)
		}.bind(this))
		//	$(this.listing).on("drop",function(event){ this.handleDrop(event)}.bind(this))
		// 	$(this.listing).on("drop",function(event){ this.changeUpload(event)}.bind(this))

	},
	dragEnter: function (event) {
		event.stopPropagation();
		event.preventDefault();
		this.listing.update();
		if (this.options.hoverZone) {
			//	$(this.options.hoverZone).addClassName('hover');
		}
	},
	inputUpload: function (event) {
		elem = $(this.form).down('input[type=file]')
		i = 0;
		while (i < elem.files.length) {
			this.processXHR(elem.files[i], i, this);
			i++;
		}
	},
	changeUpload: function (event) {
		$(this.listing).update();
		this.inputElement = $(Event.element(event))
		//
		i = 0;
		if (!this.inputElement.files) return false;
		while (i < this.inputElement.files.length) {
			this.preProcessXHR(this.inputElement.files[i], i, this);
			i++;
		}
	},
	hideSubmit: function () {
		elem = $(this.form).down('input[type=file]');
		$(elem).hide();
	},
	dragOver: function (event, reddy) {
		/*event.stopPropagation();
		event.preventDefault();*/
		if (this.options.hoverZone) {
			//	$(this.options.hoverZone).addClassName('hover');
		}
	},
	handleDrop: function (event) {
		this.dt = event.dataTransfer
		files = this.dt.files
		count = files.length
console.log(files,count)
		event.stopPropagation();
		event.preventDefault();

		for (var i = 0; i < count; i++) {
			//	if(files[i].fileSize < 83886080) {
			loader_div = new Element('div', {id: 'item_' + i, className: 'loaderItem'})
			loader_link = new Element('div', {className: ' '})
			if (this.uploadType == 'img') {
				try {
					filem = files[i].getAsDataURL();
				} catch (e) {
					filem = '';
				}
				try {
					filem = this.filereader.readAsDataURL(files[i]);
				} catch (e) {
					filem = '';
				}
				loader_img = new Element('img', {className: 'just_uploaded', src: filem})
				if (this.options.onchange) {
				}
				;
				this.preProcessXHR(files[i], i);
			}
			if (this.uploadType == 'video') {
				try {
					filem = this.filereader.readAsDataURL(files[i]);
				} catch (e) {
					filem = '';
				}
				//
				loader_img = new Element('video', {attributes: 'preload controls', width: 130, className: 'border4', type: 'application/x-shockwave-flash', src: filem});
				loader_source = new Element('source', {src: filem});
				loader_img.appendChild(loader_source);
			}
			loader_link.appendChild(loader_img);
			loader_div.appendChild(loader_link);
			this.listing.appendChild(loader_div);

			this.options.onChange(loader_img.getDimensions());
			//this.preProcessXHR(files.item(i), i,this);
			/*} else {
			 alert("Fichier trop volumineux");
			 }*/
		}
	},

	preProcessXHR: function (file, index) {


		// console.log(file);
		loader_img = new Element('img', {className: 'just_uploaded', src: ''})
		loader_div = $('item_' + index) || new Element('div', {id: 'item_' + index, style: 'position:relative;', className: 'loaderItem'})
		loader_link = new Element('div', {className: ' '});
		//
		this.filereader.onloadend = function (event) {
			loader_img.src = event.target.result;
		}.bind(this);
		this.filereader.readAsDataURL(file);
		// loader_img.src = filem ;
		loader_link.appendChild(loader_img);
		loader_div.appendChild(loader_link);
		this.listing.appendChild(loader_div);
	},
	preloadImg: function () {

	},
	processXHR: function (file, index) {
		this.xhr = new XMLHttpRequest(),
			container = this.element,
			fileUpload = this.xhr.upload,
			post = "red";

		this.progressBars[index] = new Element('div', {className: 'progressBar margin ededed'});

		$('item_' + index).appendChild(this.progressBars[index]);

		fileUpload.log = container;
		fileUpload.progressBar = this.progressBars[index];
		fileUpload.addEventListener("progress", this.uploadProgressXHR.bindAsEventListener(this, index), true);
		fileUpload.addEventListener("load", this.loadedXHR.bindAsEventListener(this, index), false);
		fileUpload.addEventListener("error", this.uploadError.bindAsEventListener(this, index), false);
		this.xhr.addEventListener("readystatechange", this.uploadOn.bindAsEventListener(this, index), false);
		//  // console.log(this.form);
		if (this.form == null) {
			ser = "&" + this.options.data
		} else {
			ser = "?" + $(this.form).serialize()
		}
		//
		this.xhr.open("POST", this.options.action + ser);
		this.xhr.overrideMimeType('text/plain; charset=x-user-defined-binary');
		this.xhr.setRequestHeader("If-Modified-Since", "Mon, 26 Jul 1997 05:00:00 GMT");
		this.xhr.setRequestHeader("Cache-Control", "no-cache");
		this.xhr.setRequestHeader("X-Requested-With", "XMLHttpRequest");
		this.xhr.setRequestHeader("X-File-Name", file.name);
		this.xhr.setRequestHeader("X-File-Size", file.size);
		this.xhr.setRequestHeader("Content-Type", "multipart/form-data");

		this.xhr.send(file);
	},
	uploadOn: function (event, index) {
		var status = null;
		try {
			status = event.target.status;
		} catch (e) {
			return;
		}
		if (status == '200' && event.target.responseText) {
			el = new Element('div')
			clearTimeout(this.timy);
			this.timy = setTimeout(function () {
				el.update(event.target.responseText);
				clearTimeout(this.timy);
			}, 1250);
		}

	},
	uploadProgressXHR: function (event, index) {
		if (event.lengthComputable) {
			var percentage = Math.round((event.loaded * 100) / event.total);
			if (percentage < 99) {
				this.progressBars[index].update(percentage + ' %').setStyle({width: (percentage) + "%"});
			}
		}
	},
	loadedXHR: function (event, index) {
		this.progressBars[index].update('ok').setStyle({width: "auto"});
		//	this.progressBars[index].fade(5);
	},
	uploadError: function (error) {
		alert('Une erreur a eu lieu')
	}
}