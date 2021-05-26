myddeAttach = {};
myddeAttach = Class.create();
myddeAttach.prototype = {
	initialize: function (element, options) {

		this.element = $(element)
		this.options = Object.extend({
			preview_zone: false,
			autoSubmit: false,
			form: null,
			action: null,
			data: null,
			onChange: Prototype.emptyFunction
		}, options || {});
		this.imagetype = {
			'image/png': true,
			'image/jpeg': true,
			'image/gif': true
		}
		this.timer = 0;
		this.form = $(this.options.form);
		this.upload_zone = this.options.upload_zone || this.element;
		this.progressHolder = new Element('div', {
			id: 'progressHolder',
			'className': 'progressHolder'
		})
		this.element.insert({
			top: this.progressHolder.hide()
		});

		if (this.form != null && this.options.action == null) {
			this.options.action = this.form.readAttribute('action')
			$(this.form).observe("submit", function(){this.submitForm()}.bind(this), false);
			$(this.form).observe("dom:submit", function(){this.submitForm()}.bind(this), false);
			// $(this.form).observe("dom:submit", this.submitForm.bind(this), false);
		}
		//
		$(document.body).observe("dragover", this.dragOver.bindAsEventListener(this), false)
		$(document.body).observe("dragenter", this.dragEnter.bindAsEventListener(this), false)
		$(document.body).on("dragend", function (event) {
			this.dragEnd(event);
			if ($(this.zone)){$(this.zone).fade();}
		}.bind(this))
		/*$(document.body).on("dragexit", function (event) {
			this.dragEnd(event);
			if ($(this.zone)){$(this.zone).fade();}
		}.bind(this));*/
		if ($(this.element).select('input[type=file]').size() != 0) {
			this.inputTypeFile = $(this.element).select('input[type=file]').first();
			$(this.inputTypeFile).observe('change', function (event) {
				// Event.stop(event);
				this.fileChange();
				this.preview();
			}.bind(this));
		}
		$(this.element).observe("drop", function (event) {
			event.stopPropagation();
			event.preventDefault();
			if ($(this.zone)){$(this.zone).hide();}
			// if ($(this.zone)){$(this.zone).kill() ; delete this.zone;}
			this.FileSelectHandler(event);
			this.preview();
		}.bind(this))

	},
    dragEnter : function(event) {
	    if(event){
		    event.stopPropagation();
		    event.preventDefault();
	    }
        this.makeDropArea();
    },
    dragOver : function(event) {
	    if(event){
		    event.stopPropagation();
		    event.preventDefault();
	    }
	    clearTimeout(this.timer);
	    this.timer = setTimeout(this.dragEnd.bind(this),1250);
    },
    dragEnd : function(event) {
	    if(event){
		    event.stopPropagation();
		    event.preventDefault();
	    }
	    if(!this.dropped){
		    if ($(this.zone)){$(this.zone).hide();}
		    if(this.options.show_hide){$(this.element).hide();}
	    }
    },
	makeDropArea: function () {
		var frm = this.element;
		$(frm).identify();
		if (!$(this.zone)){
			this.zone = create_element_in('div',frm);
			$(this.zone).update('<div id="load' + $(frm).id + '"   class="drop_area flex_h flex_align_middle"><div class="aligncenter flex_main"><i class="padding margin border4 fond_noir color_fond_noir fa fa-upload fa-3x"></i> </div></div>');
		}

		$(this.zone).show().makeOnTop();
		if(this.options.show_hide){$(this.element).show();}

	},
    makeProgress : function(file, index) {
        this.progressHolder.show();
        this.Progress[index] = this.progressHolder.appendChild(new Element('progress', {
            id : 'progress_' + index
        }));
        this.Progress[index].value = 0;
        this.Progress[index].min = 0;
        this.Progress[index].max = 100;
    },
    preview: function(){ 
        if(this.options.preview_zone){ 
            $(this.options.preview_zone).update();
            $(this.options.preview_zone).makeLoading();
            for (var i = 0; i < this.files.length; i++) {
                filem = this.files[0];
                var oFReader = new FileReader();
                oFReader.readAsDataURL(filem);
                oFReader.onload = function (oFREvent) {
                    loader_img = new Element('img',{className:'just_uploaded'}) ;
                    loader_img.src = oFREvent.target.result;
                    $(this.options.preview_zone).appendChild(loader_img);
                    $(this.options.preview_zone).undoLoading();
                }.bind(this);
            }
           
            
        }
        
    },
    //
    FileSelectHandler : function(event) {
	    console.log('FileSelectHandler')
	    this.dropped = true;
        this.element.select('.disinput').invoke('addClassName','enabled');
        this.xhrArr = [];
        this.Progress = [];
        this.files = event.target.files || event.dataTransfer.files;
        //
	    this.total = this.files.length;
        for (var i = 0, f; f = this.files[i]; i++) {
            if (this.options.autoSubmit == true) {
                this.makeProgress(f, i);
                this.UploadFile(f, i);
            }
        }
    },
    fileChange : function() {
	    console.log('fileChange')
	    this.dropped = true;
        this.element.select('.disinput').invoke('addClassName','enabled');
        this.xhrArr = [];
        this.Progress =[];
        i = 0;
        if (!this.inputTypeFile.files)  return;
        this.files = this.inputTypeFile.files;
        if (this.options.autoSubmit == true) {
	        this.total = this.inputTypeFile.files.length;
            while (i < this.inputTypeFile.files.length) {
                this.makeProgress(this.inputTypeFile.files[i], i);
                this.UploadFile(this.inputTypeFile.files[i], i);
                i++;
            }
        }
    },
    submitForm : function() {
	    console.log('submitForm',this)
	    this.dropped = true;
        if (this.files.length != 0) {
	        this.total = this.files.length;

            for (var i = 0, f; f = this.files[i]; i++) {
                this.makeProgress(f, i);
                this.UploadFile(f, i).bind(this);
            }
        }
    },
    //
    UploadFile : function(file, index) {
        if (this.form == null) {
            ser = "&" + this.options.data
        } else {
            ser = "?" + $(this.form).serialize()
        }
        postbody = this.options.action + ser + '&filename=' + file.name + '&filesize=' + file.size + '&filetype=' + file.type;
        //

	    var xhr = new XMLHttpRequest();
	    xhr.open("POST", postbody, true);


	    xhr.onload = function() {
		    this.Progress[index].value = this.Progress[index].innerHTML = 100;
		    this.Progress[index].fade({
			    afterFinish : function() {
				    this.Progress[index].remove();
			    }.bind(this)
		    });
		    if(index == eval(this.total-1)){
			    this.dropped = false;
			    if(this.options.show_hide){$(this.element).fade();}
		    }
	    }.bind(this);
	    xhr.onloadend = function(event) {
		    content = xhr.responseText;
		    content.evalScripts.bind(content).defer();
		    console.log('onloaend  ');
	    }.bind(this);
	    xhr.upload.onprogress = function(event) {
		    if (event.lengthComputable) {
			    var complete = (event.loaded / event.total * 100 | 0);
			    this.Progress[index].value  = complete;
		    }
	    }.bind(this);

	    xhr.open("POST", postbody, true);
	    xhr.setRequestHeader("X_FILENAME", file.name);
	    xhr.setRequestHeader("X-File-Name", file.name);
	    xhr.setRequestHeader("X-File-Size", file.size);
	    xhr.setRequestHeader("X-File-Type", file.type);
	    xhr.send(file);

return;
	    this.xhrArr[index] = new XMLHttpRequest();


        this.xhrArr[index].onload = function() {
            this.Progress[index].value = this.Progress[index].innerHTML = 100;
            this.Progress[index].fade({
                afterFinish : function() {
                    this.Progress[index].remove();
                }.bind(this)
            });
	        if(index == eval(this.total-1)){
		        this.dropped = false;
		        if(this.options.show_hide){$(this.element).fade();}
	        }
	        console.log('onload for index ',index,act_chrome_gui);
        }.bind(this);
        this.xhrArr[index].onloadend = function(event) {
            content = this.xhrArr[index].responseText;
            content.evalScripts.bind(content).defer();
	        console.log('onloaend for index ',index,act_chrome_gui);
        }.bind(this);
        this.xhrArr[index].upload.onprogress = function(event) {
            if (event.lengthComputable) {
                var complete = (event.loaded / event.total * 100 | 0);
                this.Progress[index].value  = complete;
            }
        }.bind(this)
        this.xhrArr[index].open("POST", postbody, true);
        this.xhrArr[index].setRequestHeader("X_FILENAME", file.name);
        this.xhrArr[index].setRequestHeader("X-File-Name", file.name);
        this.xhrArr[index].setRequestHeader("X-File-Size", file.size);
        this.xhrArr[index].setRequestHeader("X-File-Type", file.type);
	    console.log('before send for index ',index,act_chrome_gui);
        this.xhrArr[index].send(file);
	    console.log('send for index ',index,act_chrome_gui);
    }
}