function nl2br(str, is_xhtml) {

    return (str + '').replace(/([^>\r\n]?)(\r\n|\n\r|\r|\n)/g, '$1<br>$2');
}

var ResizingTextArea = Class.create();
var resizeInput = Class.create();

resizeInput.prototype = {
    initialize: function (element, options) {

        this.options = Object.extend({ }, options || {});
        this.element = $(element);
        this.wrapper_holder = this.element.parentNode;

        this.span = new Element('span', {style: 'position:absolute;z-index:-1;display:inline-block;right:0;top:-1500px;visibility:hidden;padding:0.5em;padding-right:2.5em;min-width:80px;'});
        this.span.update(this.element.value);
        this.element.style.width = this.span.getWidth() + 'px';
        this.element.style.minWidth =  '80px';
        this.wrapper_holder.appendChild(this.span);
        $(this.element).on('keydown', function (event, node) {
            this.span.update(this.element.value);
            this.element.style.width = this.span.getWidth() + 'px';
        }.bind(this));
    }

}

ResizingTextArea.prototype = {
    defaultRows: 1,

    initialize: function (field, options) {
        this.options = Object.extend({
            layout: true,
            height: true,
            width: true
        }, options || {});


        this.field = $(field);
        this.parent = $(field).parentNode

        if ($(field).readAttribute('layout')) {
            this.options.layout = $(field).readAttribute('layout');
        }
        this.defaultRows = Math.max(field.rows, 1);
        this.tplhref = '<a onclick="#{value}">#{value}</a>';
        this.hrefsyntax = /http:(.*?)\s+/;
        this.syntax = /\lien\((.*?)\)/;
        this.datesyntax = /(\d{2})\/(\d{2})\/(\d{4})/;

        this.display = new Element('div')

        $(this.display).addClassName($(this.field).classNames());
        if (this.options.layout == true) {
            this.parent.appendChild(this.display);
            this.field.observe('blur', this.deactivate.bind(this));
            this.display.update(nl2br(field.value));
            //this.field.setStyle({'width': this.display.getWidth()+'px' });

            this.deactivate();
        }

        this.resizeNeeded();
        this.field.observe("click", function (event) {
            this.resizeNeeded();
        }.bind(this));
        this.field.observe("keyup", function (event) {
            this.resizeNeeded();
        }.bind(this));
        this.display.observe('click', function (event) {
            this.activate();
        }.bind(this));
    },
    activate: function (event) {
        if (event) {
            if (Event.element(event).match('a')) {/*Event.stop(event);*/
                return;
            }
        }

        $(this.field).toggleContent().focus();
    },
    deactivate: function () {
        this.display.toggleContent();
    },
    resizeNeeded: function () {
        lineHeight = this.field.getStyle('lineHeight')
        this.display.setStyle({'line-height': lineHeight });
        this.display.style.fontSize = this.field.getStyle('fontSize');
        if (this.field.getHeight() == 0) {
            height = '50'
        } else {
            height = this.field.getHeight()
        }

        if (this.options.height == true) {
            var t = this.field;
            var lines = t.value.split('\n');
            var newRows = lines.length + 1;
            this.field.rows = newRows;
            this.display.setStyle({height: newRows * lineHeight + 'px'});
        }
        if (this.options.width == true) {
            this.field.setStyle({'width': 'auto' });
            this.field.setStyle({'width': this.field.scrollWidth + 10 + 'px' });
        }
        toPrint = this.field.value.sub(this.syntax, function (match) {
            return   '<a onclick="fctquickPaste(this.innerHTML,$(this))">' + match[1] + '</a>';
        })
        /*toPrint  =  toPrint.sub(this.datesyntax, function(match) {
         return   '<a onclick="ajaxMdl(\'calendrier/mdlCalendrierDesk\',\'Date et heure\',\'date='+match[1]+'/'+match[2]+'/'+match[3]+'\', {buttonClose:false,inTask: false,parent:\'desktop\',hasHandle: false});return false;">'+match[1]+'/'+match[2]+'/'+match[3]+'</a>' ;
         })*/
        //toPrint  = this.field.value.gsub(syntax,'<a onclick="fctquickPaste(this.innerHTML,$(this))">');
        //syntax = /\:(.*?)/
        //toPrint  = toPrint.gsub(this.hrefsyntax,'<a href="#{0}" target="_blank">#{0}</a>');
        //dlink = 'http://www.destinationsreve.com/trains-de-luxe/pt2/nos-voyages.html et des choses';
        //console.log(dlink.gsub(/http:(.*?)\s+/,'<a href="#{0}">#{0}</a>'));
        this.display.update(nl2br(toPrint))
    }
}