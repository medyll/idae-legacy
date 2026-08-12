/**
 * Modified: 2026-08-10 — migrated off the PrototypeJS compatibility shims
 * (BE_PLAN.md phase 5). Drag-and-drop / file-input upload widget. Behaviour
 * unchanged. The dead branch after the unconditional `return;` inside
 * UploadFile (found already-dead in commit 496f371, migrating the Effect.*
 * calls) is left exactly as-is — it never executes, no reason to touch it.
 */
(function (global) {

	/* ------------------------------------------------------------------ *
	 * DOM helpers — file-local, same rationale as the other migrated       *
	 * files (see BE_PLAN.md phase 5).                                      *
	 * ------------------------------------------------------------------ */

	function ma_el(ref) {
		return typeof ref === 'string' ? document.getElementById(ref) : ref;
	}

	function ma_qsa(root, selector) {
		if (!root) return [];
		return Array.prototype.slice.call(root.querySelectorAll(selector));
	}

	function ma_identify(node) {
		if (!node.id) node.id = uniqid('anonymous_element');
		return node.id;
	}

	function ma_show(node) {
		if (node) node.style.display = '';
		return node;
	}

	function ma_hide(node) {
		if (node) node.style.display = 'none';
		return node;
	}

	function ma_remove(node) {
		if (node && node.parentNode) node.parentNode.removeChild(node);
		return node;
	}

	/* ------------------------------------------------------------------ */

	var myddeAttach = function () {
		this.initialize.apply(this, arguments);
	};

	myddeAttach.prototype = {
		initialize: function (element, options) {

			this.element = ma_el(element)
			this.options = Object.assign({
				preview_zone: false,
				autoSubmit: false,
				form: null,
				action: null,
				data: null,
				onChange: function () {}
			}, options || {});
			this.imagetype = {
				'image/png': true,
				'image/jpeg': true,
				'image/gif': true
			}
			this.timer = 0;
			this.form = ma_el(this.options.form);
			this.upload_zone = this.options.upload_zone || this.element;
			this.progressHolder = document.createElement('div');
			this.progressHolder.id = 'progressHolder';
			this.progressHolder.className = 'progressHolder';

			// Prototype's insert({top: node}): prepend as the first child.
			ma_hide(this.progressHolder);
			this.element.insertBefore(this.progressHolder, this.element.firstChild);

			if (this.form != null && this.options.action == null) {
				this.options.action = this.form.getAttribute('action')
				this.form.addEventListener("submit", function(){this.submitForm()}.bind(this), false);
				this.form.addEventListener("dom:submit", function(){this.submitForm()}.bind(this), false);
				// this.form.addEventListener("dom:submit", this.submitForm.bind(this), false);
			}
			//
			document.body.addEventListener("dragover", this.dragOver.bind(this), false)
			document.body.addEventListener("dragenter", this.dragEnter.bind(this), false)
			document.body.addEventListener("dragend", function (event) {
				this.dragEnd(event);
				// Was Scriptaculous' Effect.Fade via the shim; fadeElement
				// (engine/methods.js) is the native replacement, same contract.
				if (ma_el(this.zone)){fadeElement(ma_el(this.zone));}
			}.bind(this))
			/*document.body.addEventListener("dragexit", function (event) {
				this.dragEnd(event);
				if (ma_el(this.zone)){ma_el(this.zone).fade();}
			}.bind(this));*/
			var fileInputs = ma_qsa(this.element, 'input[type=file]');
			if (fileInputs.length != 0) {
				this.inputTypeFile = fileInputs[0];
				this.inputTypeFile.addEventListener('change', function (event) {
					// Event.stop(event);
					this.fileChange();
					this.preview();
				}.bind(this));
			}
			this.element.addEventListener("drop", function (event) {
				event.stopPropagation();
				event.preventDefault();
				if (ma_el(this.zone)){ma_hide(ma_el(this.zone));}
				// if (ma_el(this.zone)){ma_el(this.zone).kill() ; delete this.zone;}
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
			    if (ma_el(this.zone)){ma_hide(ma_el(this.zone));}
			    if(this.options.show_hide){ma_hide(this.element);}
		    }
	    },
		makeDropArea: function () {
			var frm = this.element;
			ma_identify(frm);
			if (!ma_el(this.zone)){
				this.zone = create_element_in('div',frm);
				ma_el(this.zone).innerHTML = '<div id="load' + ma_el(frm).id + '"   class="drop_area flex_h flex_align_middle"><div class="aligncenter flex_main"><i class="padding margin border4 fond_noir color_fond_noir fa fa-upload fa-3x"></i> </div></div>';
			}

			ma_show(ma_el(this.zone)).makeOnTop();
			if(this.options.show_hide){ma_show(this.element);}

		},
	    makeProgress : function(file, index) {
	        ma_show(this.progressHolder);
	        this.Progress[index] = this.progressHolder.appendChild(document.createElement('progress'));
	        this.Progress[index].id = 'progress_' + index;
	        this.Progress[index].value = 0;
	        this.Progress[index].min = 0;
	        this.Progress[index].max = 100;
	    },
	    preview: function(){
	        if(this.options.preview_zone){
	            ma_el(this.options.preview_zone).innerHTML = '';
	            ma_el(this.options.preview_zone).makeLoading();
	            for (var i = 0; i < this.files.length; i++) {
	                filem = this.files[0];
	                var oFReader = new FileReader();
	                oFReader.readAsDataURL(filem);
	                oFReader.onload = function (oFREvent) {
	                    loader_img = document.createElement('img');
	                    loader_img.className = 'just_uploaded';
	                    loader_img.src = oFREvent.target.result;
	                    ma_el(this.options.preview_zone).appendChild(loader_img);
	                    ma_el(this.options.preview_zone).undoLoading();
	                }.bind(this);
	            }


	        }

	    },
	    //
	    FileSelectHandler : function(event) {
		    console.log('FileSelectHandler')
		    this.dropped = true;
	        ma_qsa(this.element, '.disinput').forEach(function (n) { n.classList.add('enabled'); });
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
	        ma_qsa(this.element, '.disinput').forEach(function (n) { n.classList.add('enabled'); });
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
	            ser = "?" + engine_formSerialize(this.form)
	        }
	        postbody = this.options.action + ser + '&filename=' + file.name + '&filesize=' + file.size + '&filetype=' + file.type;
	        //

		    var xhr = new XMLHttpRequest();
		    xhr.open("POST", postbody, true);


		    xhr.onload = function() {
			    this.Progress[index].value = this.Progress[index].innerHTML = 100;
			    // Was Scriptaculous' Effect.Fade via the shim; fadeElement
			    // (engine/methods.js) is the native replacement, same contract.
			    fadeElement(this.Progress[index], {
				    afterFinish : function() {
					    ma_remove(this.Progress[index]);
				    }.bind(this)
			    });
			    if(index == eval(this.total-1)){
				    this.dropped = false;
				    if(this.options.show_hide){fadeElement(this.element);}
			    }
		    }.bind(this);
		    xhr.onloadend = function(event) {
			    content = xhr.responseText;
			    // Was String#evalScripts.bind(content).defer() via the shim
			    // (indirect eval on a 10ms defer, Prototype's Function#defer);
			    // engine_evalScripts (engine/engine.js) is the native
			    // equivalent, run on the same defer.
			    setTimeout(function () { engine_evalScripts(content); }, 10);
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
	            // Same dead-since-2026-08-09 `.fade()` as below. fadeElement
	            // takes the same options object, afterFinish included.
	            fadeElement(this.Progress[index], {
	                afterFinish : function() {
	                    var p = this.Progress[index];
	                    if (p && p.parentNode) p.parentNode.removeChild(p);
	                }.bind(this)
	            });
		        if(index == eval(this.total-1)){
			        this.dropped = false;
			        // Was `$(this.element).fade()`. shim-effects.js was deleted on
			        // 2026-08-09 once the last *known* Effect caller migrated; this
			        // one was missed, so the upload panel has thrown "fade is not a
			        // function" on every completed upload with show_hide set since.
			        // fadeElement is the native replacement in engine/methods.js.
			        if(this.options.show_hide){fadeElement(this.element);}
		        }
		        console.log('onload for index ',index,act_chrome_gui);
	        }.bind(this);
	        this.xhrArr[index].onloadend = function(event) {
	            content = this.xhrArr[index].responseText;
	            // Was `content.evalScripts.bind(content).defer()` — String#evalScripts
            // plus Function#defer, both from shim-enumerable. engine_evalScripts
            // (engine/engine.js) is the native port already used by app_keepon,
            // app_live_data and app_menu, and setTimeout(.., 10) is exactly what
            // defer() did.
            setTimeout(function () { engine_evalScripts(content); }, 10);
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

	global.myddeAttach = myddeAttach;

})(window);
