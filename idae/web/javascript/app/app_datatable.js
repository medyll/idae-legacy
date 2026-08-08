create_element_in = function (tag, node, attributes) {
    var a = document.createElement(tag);
    if (attributes && typeof attributes == 'object') {
        Object.entries(attributes).forEach(function ([name, value]) {
            if (name === 'className') a.className = value;
            else a.setAttribute(name, value);
        });
    }
    // console.log(tag, node, attributes)
    node.appendChild(a);
    return a
}
create_element_of = function (text) {
    var fragment_main = document.createDocumentFragment();
    var fragment_in = document.createDocumentFragment();
    if (text.substr(1, 5) == 'tbody') {
        var element = document.createElement('table')
    } else if (text.substr(1, 2) == 'tr') {
        var element = document.createElement('tbody')
    } else if (text.substr(1, 2) == 'td') {
        var element = document.createElement('tr')
    } else if (text.substr(1, 2) == 'th') {
        var element = document.createElement('tr')
    } else {
        var element = document.createElement('div')
    }
    element.innerHTML = text
    fragment_in.appendChild(element);
    var element_child = element.childNodes[0]
    fragment_main.appendChild(element_child);
    delete fragment_in;
    //
    return element_child;
}
tag_elem_table = function (elem, table_name, table_value) {
    //
    elem.setAttribute('data-table', table_name);
    elem.setAttribute('data-table_value', table_value);
    elem.setAttribute('value', table_value);
    elem.setAttribute('data-contextual', 'table=' + table_name + '&table_value=' + table_value);
    elem.setAttribute('vars', 'table=' + table_name + '&table_value=' + table_value);
    elem.setAttribute('data-vars', 'table=' + table_name + '&table_value=' + table_value);
}
tag_elem_table_field = function (elem, field_name, field_name_raw, field_value, field_className = '') {
    field_value = field_value || '';
    elem.innerHTML = tag_elem_table_value(field_name, field_name_raw, field_value, field_className);
    if (field_className) elem.classList.add(...field_className.split(/\s+/).filter(Boolean));
}
tag_elem_table_value = function (field_name, field_name_raw, field_value = '', field_className = '') {
    return '<div class="' + field_className + '" data-field_name="' + field_name + '" data-field_name_raw="' + field_name_raw + '" style="white-space:nowrap;text-overflow:ellipsis;overflow:hidden;max-width:100%;">' + field_value + '</div>';
}
tag_elem_table_field_labelled = function (elem, field_name, field_name_raw, field_value, field_title, field_className) {
    field_value = field_value || '';
    elem.innerHTML = '<div class="flex_h borderb padding"><div class="label ellipsis textgris" style="width:80px;">' + field_title + '&nbsp;</div><div class="has_data" style="white-space:nowrap;text-overflow: ellipsis;overflow:hidden;">' + field_value + '</div></div>';
    dyn_elem = elem.querySelector('.has_data');
    dyn_elem.setAttribute('data-field_name', field_name);
    dyn_elem.setAttribute('data-field_name_raw', field_name_raw);
    if (field_className) dyn_elem.classList.add(...field_className.split(/\s+/).filter(Boolean));
}
/**
 *
 * @param url_data
 * @param zone
 * @param options
 */

load_table_in_zone = function (url_data, zone, options) {
    var data = Object.fromEntries(new URLSearchParams(url_data.replace('&&', '&')));

    if (typeof data.groupBy != 'string') delete data.groupBy;
    data = array_unique(data)
    var table = data.table;
    var table_groupby = data.groupBy || '';
    var da_options = Object.assign({
        table_name: table,
        groupBy: table_groupby,
        url_data: url_data,
        post_data: data
    }, options || {});
    new BuildTbl(zone, da_options);
}

if (!window.APP) window.APP = {}
if (!window.APP.APPTPL) window.APP.APPTPL = {}
BuildTbl = function () {
    this.initialize.apply(this, arguments);
};
/**
 *
 * @type {{frag_table: string, frag_table_reporter: string, frag_table_footer: string, initialize: Function, set_options: Function, watch_vars: Function, build_bottom: Function, getCount: Function, build_gallery: Function, build_overlay: Function, build_table_div_gallery:  Function, build_scheme_header_table_div: Function, build_table: Function, build_scheme_header: Function, reload_data: Function, load_data: Function, build_data: Function, build_data_thumb: Function, build_data_note: Function, cache_data: Function}}
 */
BuildTbl.prototype = {
    frag_table: '<table  ><thead><tr></tr></thead><tbody class="toggler div_tbody" ></tbody></table>',
    frag_table_footer: '<div ><div class="tbl_footer"></div></div>',
    frag_table_reporter: '<div ><div class="tbl_reporter flex_h"></div></div>',
    frag_table_loading_icon: '<i class="fa fa-spin fa-refresh"></i>',
    frag_table_test: window.APP.APPTPL['table_activity'] || '<div auto_tag="table_activity" table_activity >' +
    '<div  ><div auto_tag="table_activity_icon"><i class="fa fa-spin fa-refresh"></i></div><i class="fa fa-ellipsis-h"></i></div>' +
    '<div auto_tag="table_activity_count" data-menu="data-menu" data-menu_free="data-menu_free" ></div><div auto_tag="table_activity_count_menu" class="applink applinkblock" style="display:none;" ></div>' +
    '<div auto_tag="table_activity_pager_zone"><div auto_tag="table_activity_pager_select"></div><div auto_tag="table_activity_pager" class="toggler"></div><div auto_tag="table_activity_pager_more_trigger" data-menu="data-menu" ><i class="fa fa-plus"></i><i class="fa fa-ellipsis-h"></i></div><div auto_tag="table_activity_pager_more" class="toggler"  style="display:none;"></div>' +
    '</div></div>',
    table_activity: null,
    table_activity_icon: null,
    table_activity_count: null,
    table_activity_count_menu: null,
    table_activity_pager_zone: null,
    table_activity_pager_select: null,
    table_activity_pager: null,
    table_activity_pager_more: null,
    //
    initialize: function (element, options) {
        this.element = typeof element === 'string' ? document.getElementById(element) : element
        this.options = Object.assign({
            table_name: 'produit',
            table_groupby: '',
            url_data: '',
            post_data: '',
            table_scheme: {},
            table_fields: [],
            table_data: {},
            data_model: 'columnModel',
            table_preview: 'app/app/app_fiche_preview'
        }, options || {});
        if (window.APP.APPTPL && window.APP.APPTPL.table_activity) {
            this.frag_table_test = window.APP.APPTPL['table_activity']
        }
        if (window.APP.APPTPL && window.APP.APPTPL.table_line_item) {
            this.frag_table_line_item = window.APP.APPTPL['table_line_item']
        }
        ;
        //
        this.options.Table_name = ucfirst(this.options.table_name);
        this.options.table_scheme = window.APP.APPSCHEMES[this.options.table_name];
        if (this.element.__buildTblAbortController) {
            this.element.__buildTblAbortController.abort();
        }
        this.abortController = new AbortController();
        this.element.__buildTblAbortController = this.abortController;
        if (!this.element.id) this.element.id = uniqid('datatable');
        this.ARR_CACHE_MONGO_KEY = [];
        this.element.setAttribute('app_datatable', 'true');
        if (this.element.getAttribute('data-data_model')) {
            this.options.data_model = this.element.getAttribute('data-data_model');
        } //  DATA_QUERY

        this.DATA_QUERY = [] // url to query params
        //
        if (this.options.url_data) {
            parse_str(this.options.url_data, this.DATA_QUERY)
        } // DATA_QUERY : effacer colonnes deja dans query + garder idclient ou idprospect

        if (!this.options.table_scheme) console.log(' !! ', this.options)
        if (this.options.table_scheme.grilleFK) {
            this.options.table_scheme.grilleFK.forEach(function (node, index) {
                var del_fk = node.table;
                var Del_fk = ucfirst(del_fk);
                if (this.DATA_QUERY.vars && this.DATA_QUERY.vars['id' + del_fk]) {
                    // remove row del_fk
                    var arr_model = [
                        'fieldModel',
                        'defaultModel',
                        'miniModel',
                        this.options.data_model
                    ];
                    var arr_model_delete = [
                        'nom',
                        'prenom',
                        'code',
                        'reference'
                    ];
                    arr_model.forEach(function (node_model, index_modele) {
                        // console.log('node_model ' + node_model , this.options.table_scheme[node_model],this.options.table_scheme[node_model]['field_name']);
                        this.options.table_scheme[node_model].forEach(function (scheme_model, index_delete) {
                            //console.log('scheme_model ',scheme_model);
                            arr_model_delete.forEach(function (node_delete, index) {
                                if (scheme_model.field_name == node_delete + Del_fk) {
                                    // really delete
                                    delete this.options.table_scheme[node_model][index_delete];
                                }
                            }.bind(this))
                        }.bind(this))
                    }.bind(this))
                }
            }.bind(this))
        }
        this.DATA_MODEL_ALL_FIELDS = this.options.table_scheme.fieldModel;
        this.DATA_MODEL_DEFAULT = this.options.table_scheme.defaultModel;
        this.DATA_MODEL = this.options.table_scheme[this.options.data_model] || this.options.table_scheme.defaultModel;
        // rebuild ::
        this.RAW_DATA_MODEL_ALL_FIELDS = [];
        this.DATA_MODEL_ALL_FIELDS.forEach(function (h_node) {
            this.RAW_DATA_MODEL_ALL_FIELDS[h_node.field_name] = h_node;
        }.bind(this));
        //
        this.RAW_DATA_MODEL_DEFAULT = [];
        this.DATA_MODEL_DEFAULT.forEach(function (h_node) {
            this.RAW_DATA_MODEL_DEFAULT[h_node.field_name] = h_node;
        }.bind(this));
        //
        this.RAW_DATA_MODEL = [];
        this.DATA_MODEL.forEach(function (h_node) {
            this.RAW_DATA_MODEL[h_node.field_name] = h_node;
        }.bind(this));
        this.set_observers();
        this.element.setAttribute('data-table', this.options.table_name);
        //
        this.tr_piece = document.createElement('tr');
        this.td_piece = document.createElement('td');
        this.div_piece = document.createElement('div');
        //
        this.uniqid = uniqid(this.options.table_name);
        this.element.setAttribute('data-uniqid', this.uniqid);
        //
        if (!this.element.getAttribute('data-dsp')) {
            this.element.setAttribute('data-dsp', 'table');
        }
        switch (this.element.getAttribute('data-dsp')) {
            case 'table':
            case 'table_icon':
                this.build_table();
                break;
            case 'flex_line':
            case 'icon':
            case 'thumb':
                this.build_gallery();
                this.tbody.classList.add('flex_h', 'flex_wrap', 'flex_align_stretch', 'flex_align_top')
                break;
            case 'image':
                this.build_gallery();
                this.tbody.classList.add('flex_h', 'flex_wrap', 'flex_align_stretch', 'flex_align_middle')
                break;
            case 'conge':
            case 'planning':
                this.build_overlay();
                this.build_overlay();
                break;
            case 'table_div': // AUCUN INTERET
            case 'fields':
                this.build_gallery();
                break;
            default:
                this.build_gallery();
                break;
        }
        this.build_bottom();
        //
        if (this.options.className) this.table.classList.add(...this.options.className.split(/\s+/).filter(Boolean));

        this.load_data();
        this.watch_vars();
        //
    },
    set_observers: function () {
        //
        this.element.addEventListener('dom:load_data', this.load_data_again.bind(this), {
            signal: this.abortController.signal
        })
        this.element.addEventListener('dom:data_reload', function (event) {
            /*console.log('no data_reload');
             return;*/
            var res_tmp = event.memo;
            var tablevalue = res_tmp.table_value;
            //  this.reload_data();
            if (this.element.querySelectorAll('[data-table_value="' + CSS.escape(String(tablevalue)) + '"]').length == 0) {
                this.reload_data();
            } else {
                var objs = {
                    table: this.options.table_name,
                    groupBy: this.options.groupBy,
                    page: this.page,
                    sort: this.options.sort,
                    verify: tablevalue
                }
                var url = new URLSearchParams(Object.entries(objs).filter(function ([, value]) {
                    return value !== undefined && value !== null;
                })).toString() + '&' + this.element.getAttribute('vars');
                objs.url_data = url;
                get_data('json_data_table', objs, function (err) {
                }).then(function (res) {
                    if (res == 'NULL') {
                        this.element.querySelectorAll('[data-table_value="' + CSS.escape(String(tablevalue)) + '"]').forEach(function (node) {
                            node.remove();
                        });
                    }
                }.bind(this));
            }
            ;
        }.bind(this), { signal: this.abortController.signal });
        this.element.addEventListener('dom:stream_chunk', function (event) {

            var res_tmp = event.memo;
            var data = window.register_stream[res_tmp]['data'];
            var data_main = data['data_main'];
            if (!data_main) return;
            this.options.table_data = data_main;

            if (data_main[0]) {
                if (!this.options.groupBy) {
                    // if ( this.options.table_name != data_main[0]['table'] ) return;
                } else {
                    // venir ici un jour
                }
            } else {
                //	console.log(this.options.table_name , data_main[0]['table'])
            }
            if (data_main[0]) if (!this.options.groupBy)  if (this.options.table_name != data_main[0]['table']) {
                return;
            }
            this.build_data(data_main);

        }.bind(this), { signal: this.abortController.signal });
    },
    set_options: function (hash) {
        this.options = Object.assign(hash, this.options || {});
    },
    watch_vars: function () {
        if (!this.old_vars) {
            this.old_vars = this.element.getAttribute('vars')
        }
        this.timer_watch_vars = setTimeout(function () {
            if (this.old_vars == this.element.getAttribute('vars')) {
            } else {
                this.old_vars = this.element.getAttribute('vars');
                var event = new CustomEvent('dom:vars_changed', { bubbles: true, cancelable: true });
                event.memo = {};
                this.element.dispatchEvent(event);
                clearTimeout(this.timer_watch_vars);
            }
        }.bind(this), 1000)
    },
    activate_fragment: function (html_fragment, frag_name) {
        var ret = [];
        this[frag_name] = create_element_of(this[html_fragment]);
        if (this[frag_name].getAttribute('auto_tag')) {
            ret[this[frag_name].getAttribute('auto_tag')] = this[frag_name];
        }
        // new
        this[frag_name].querySelectorAll('[auto_tag]').forEach(function (node, index) {
            ret[node.getAttribute('auto_tag')] = node;
            node.setAttribute(node.getAttribute('auto_tag'), 'true');
        }.bind(this))
        return ret;
    },
    build_bottom: function () {
        this.activity_zone = create_element_of(this.frag_table_test); //  table_activity_icon table_activity_count table_activity_pager_zone table_activity_pager_select table_activity_pager
        // new
        this.activity_zone.querySelectorAll('[auto_tag]').forEach(function (node, index) {
            this[node.getAttribute('auto_tag')] = node;
            node.setAttribute(node.getAttribute('auto_tag'), 'true');
        }.bind(this))
        if (this.tfooter) this.tfooter.prepend(this.activity_zone);
        // return;
        if (this.element.getAttribute('data-dsp') == 'table') {
            this.tfooter.classList.add('flex_h', 'flex_align_middle', 'margin', 'bordert');
            // old
            this.loader_zone = create_element_in('div', this.tfooter, {
                className: 'padding'
            });
            this.count_zone = create_element_in('div', this.tfooter, {
                className: 'padding'
            });
            //this.count_zone.writeAttribute({'data-menu': 'data-menu', 'data-clone': 'data-clone'});
        }
        if (this.element.getAttribute('data-dsp-pager')) {

            this.tbody.after(this.activity_zone)
            if (document.getElementById(this.element.getAttribute('data-dsp-pager'))) {
                /*this.dsp_pager = $(this.element.readAttribute('data-dsp-pager')).update();
                 this.loader_zone = create_element_in('div', this.dsp_pager, {className: 'padding'});
                 this.count_zone = create_element_in('div', this.dsp_pager, {className: 'padding'});
                 this.pager_zone = create_element_in('div', this.dsp_pager, {className: 'padding applink flex_h '});*/
                // this.count_zone.writeAttribute({'data-menu': 'data-menu', 'data-clone': 'data-clone'});
            }
        } else if (this.element.querySelector('[data-dsp-pager]')) {

            this.dsp_pager = this.element.querySelector('[data-dsp-pager]');
            this.dsp_pager.replaceChildren();
            this.loader_zone = create_element_in('div', this.dsp_pager, {
                className: 'padding'
            });
            this.count_zone = create_element_in('div', this.dsp_pager, {
                className: 'padding'
            });
            this.pager_zone = create_element_in('div', this.dsp_pager, {
                className: 'padding applink flex_h  appcontext_menu '
            });
            this.count_zone.setAttribute('data-menu', 'data-menu');
            this.count_zone.setAttribute('data-clone', 'data-clone');
        }
    },
    build_table: function () {
        this.element.innerHTML = '<section class="flex_v relative" style="height:100%;overflow:hidden;"><div main_zone="main_zone" class="_flex_main" style="overflow-y:auto;width:100%;height:100%;">' + this.frag_table + '</div>' + this.frag_table_reporter + this.frag_table_footer + '</section>';
        this.zone = this.element.querySelector('[main_zone]');
        console.log('zone', this.zone)
        this.table = this.element.querySelector('table');
        this.table.classList.add('explorer', 'act_sort', 'ethop');
        if (this.element.getAttribute('data-classname')) {
            this.table.classList.add(...this.element.getAttribute('data-classname').split(/\s+/).filter(Boolean));
            this.table.classList.remove('explorer');
        }
        if (this.DATA_QUERY.groupBy) {
            this.table.classList.add('table_groupe');
            this.table.classList.remove('explorer');
        }

        this.thead = this.table.querySelector('thead');
        this.colgroup = this.table.querySelector('colgroup');
        this.thead_tr = this.thead.querySelector('tr');
        this.tfooter = this.element.querySelector('.tbl_footer');
        this.treporter = this.element.querySelector('.tbl_reporter');
        this.build_scheme_header();
        this.tbody = this.table.querySelector('tbody');
        new autoToggle(this.tbody);
        this.element.setAttribute('data-uniqid', this.uniqid);
    },
    build_gallery: function () {
        this.element.innerHTML = '<div class="div_tbody" main_zone="main_zone" style="overflow:auto;max-height:100%;width:100%;"></div>' + this.frag_table_reporter + this.frag_table_footer;
        this.zone = this.element.querySelector('[main_zone]');
        this.treporter = this.element.querySelector('.tbl_reporter');
        this.tbody = this.element.querySelector('.div_tbody');
        if (this.element.getAttribute('data-sort')) {
            this.tbody.setAttribute('sort_zone_drag', 'true')
        }
        new autoToggle(this.tbody);
    },
    build_overlay: function () {
        this.element.classList.add('div_tbody');
        this.zone = this.element;
        console.log('zone', this.zone)
        this.tbody = this.element;
        this.element.setAttribute('data-uniqid', this.uniqid);
    },
    build_scheme_header: function () {
        if (!this.has_header) {
            var tmp_td = document.createElement('td');//this.td_piece.cloneNode (false);
            tmp_td.classList.add('avoid', 'aligncenter', 'padding', 'chk_show');
            tmp_td.innerHTML = '<input dachk type="checkbox" class="avoid">';
            this.thead_tr.appendChild(tmp_td);
            this.DATA_MODEL.forEach(function (h_node) { //  columnModel // addcheck
                var tmp_td = document.createElement('td');
                tmp_td.innerHTML = h_node.title || '...';
                if (h_node.className) tmp_td.classList.add(...h_node.className.split(/\s+/).filter(Boolean));
                this.thead_tr.appendChild(tmp_td);
            }.bind(this));
            this.has_header = true;
        }
    },
    get_header: function () {
        var export_elem = document.createElement('tr');
        var tmp_td = create_element_of('<td></td>');
        tmp_td.classList.add('avoid', 'aligncenter', 'padding', 'chk_show', 'chk_group');
        tmp_td.innerHTML = '<input dachk type="checkbox" class="avoid">';
        export_elem.appendChild(tmp_td);
        this.DATA_MODEL.forEach((h_node)=> {
            var tmp_td = this.td_piece.cloneNode(false);
            tmp_td.innerHTML = h_node.title || '...';
            if (h_node.className) tmp_td.classList.add(...h_node.className.split(/\s+/).filter(Boolean));
            export_elem.appendChild(tmp_td);
        });

        return export_elem;
    },
    reload_data: function () {
        if (!this.uniqid) return;
        if (this.reloading) {
            // console.log ('reload_data en cours  ');
            return;
        }
        if (this.loading) {
            // console.log ('load_data en cours  ');
            return;
        }
        this.reloading = true;
        if (this.table_activity_icon) {
            var iconPosition = getComputedStyle(this.table_activity_icon).position;
            this.table_activity_icon.hidden = false;
            this.table_activity_icon.style.position = 'absolute';
            Array.from(this.table_activity_icon.parentNode.children).forEach(function (node) {
                if (node !== this.table_activity_icon && !node.classList.contains('avoid')) node.hidden = true;
            }.bind(this));
            this.table_activity_icon.style.position = iconPosition;
        }
        if (!this.element.getAttribute('vars')) {
            this.element.setAttribute('vars', this.options.url_data);
        }
        if (this.element.getAttribute('data-dsp-mdl')) {
            var mdl = '&mdl=' + this.element.getAttribute('data-dsp-mdl');
        } else {
            var mdl = '';
        }
        var objs = {
            stream_to: this.uniqid,
            table: this.options.table_name,
            groupBy: this.options.groupBy,
            page: this.page,
            sort: this.options.sort,
            piece: 'query',
            url_data: this.options.url_data + '&page=' + this.page + mdl
        }
        this.from_cache = false;
        this.setCache([]);
        get_data('json_data_table', objs, {
            stream_to: this.uniqid
        }, function (err) {
        }).then(function (res) {
            this.reloading = false;
            this.getCount();
            if (this.table_activity_icon) {
                this.table_activity_icon.hidden = true;
                Array.from(this.table_activity_icon.parentNode.children).forEach(function (node) {
                    if (node !== this.table_activity_icon && !node.classList.contains('avoid')) node.hidden = false;
                }.bind(this));
            }
            // console.log ('end reload_data '+this.options.table_name);
        }.bind(this));
    },
    load_data_again: function (event, target) {
        newvars = event.memo.url_data;
        srt = [];

        this.tbody.replaceChildren();
        this.element.setAttribute('vars', newvars);
        this.options.url_data = newvars;
        this.load_data();
    },
    load_data: function () {
        if (this.loading) {
            return;
        }

        this.uniqid = uniqid();
        this.cache = [];
        this.loading = true;
        if (!this.page) this.page = 0;
        if (!this.count) this.count = 0;
        if (!this.maxcount) this.maxcount = 0;
        if (!this.element.getAttribute('vars')) {
            this.element.setAttribute('vars', this.options.url_data);
        }
        this.element.setAttribute('data-uniqid', this.uniqid);
        if (this.element.getAttribute('data-dsp-mdl')) {
            var mdl = '&mdl=' + this.element.getAttribute('data-dsp-mdl');
        } else {
            var mdl = '';
        } // extraction vars pour page, nbRows ..

        var data_vars = Object.fromEntries(new URLSearchParams(this.element.getAttribute('vars')));
        // console.log ({ vars : this.element.readAttribute ('vars'), data_vars, data_vars_str });
        this.page = data_vars.page || this.page;
        var url_data = this.options.url_data + '&page=' + this.page + mdl;
        var objs = {
            stream_to: this.uniqid,
            table: this.options.table_name,
            groupBy: this.options.groupBy,
            page: this.page,
            sort: this.options.sort,
            piece: 'query',
            url_data: url_data  // '&stream_to=' + this.uniqid
        };

        if (this.table_activity_icon) {
            var iconPosition = getComputedStyle(this.table_activity_icon).position;
            this.table_activity_icon.hidden = false;
            this.table_activity_icon.style.position = 'absolute';
            Array.from(this.table_activity_icon.parentNode.children).forEach(function (node) {
                if (node !== this.table_activity_icon && !node.classList.contains('avoid')) node.hidden = true;
            }.bind(this));
            this.table_activity_icon.style.position = iconPosition;
        }
        this.app_cache_key = build_cache_key(this.options.table_name, url_data);

        if (!this.options.cache) {
            get_data('json_data_table', objs, {
                stream_to: this.uniqid
            }, function (err) {
            }).then(function (res) {
                this.load_data_end();
            }.bind(this));
            //  alert('red');
            return;
        }
        app_cache.getItem(this.app_cache_key).then((cache_data)=> {
            if (cache_data && typeof cache_data == 'object') {
                //console.log ('get from cache !!', this.app_cache_key, err);
                this.cache = [];
                this.from_cache = true;
                let cache_data_chunk = [];
                for (let index = 0; index < cache_data.length; index += 50) {
                    cache_data_chunk.push(cache_data.slice(index, index + 50));
                }
                for (const data_chunk of cache_data_chunk) {
                    this.build_data(data_chunk);
                }
                //this.build_data (cache_data);
                this.load_data_end();
                this.from_cache = false;
                this.cache = [];
                get_data('json_data_table', objs, {
                    stream_to: this.uniqid
                }, function (err) {
                }).then(function (res) {

                }.bind(this));
            } else {
                this.from_cache = false;
                get_data('json_data_table', objs, {
                    stream_to: this.uniqid
                }, function (err) {
                }).then(function (res) {
                    this.load_data_end();
                }.bind(this));
            }
        })
    },
    load_data_end: function () {
        this.loading = false;
        if (this.loader_zone) this.loader_zone.hidden = true;
        if (this.table_activity_icon) {
            this.table_activity_icon.hidden = true;
            Array.from(this.table_activity_icon.parentNode.children).forEach(function (node) {
                if (node !== this.table_activity_icon && !node.classList.contains('avoid')) node.hidden = false;
            }.bind(this));
        }
        this.getCount();
        this.getSum();
    },
    build_data_row_groupby: function (tr_data, data_dsp = 'table') {
        // créer div pour entete (colspan)
        /*if(this.zone.select(`[sub_zone="red"][groupBy_key="${tr_data.groupBy_key}"]`).size()==0){
         const tr_tmp = create_element_of(`<div class="border4 margin padding_more" groupBy_key="${tr_data.groupBy_key}">${this.frag_table}</div>`)
         this.zone.appendChild(tr_tmp);
         this.zone_current = tr_tmp;
         this.table_zone_current = $ ($ (this.zone_current).querySelector ('table'));
         this.thead_zone_current = $ ($ (this.zone_current).querySelector ('thead'));
         this.tbody_zone_current = $ ($ (this.zone_current).querySelector ('tbody'));
         this.table_zone_current.addClassName ('explorer');
         this.thead_zone_current.update(this.get_header());
         }*/
        if (this.thead) this.thead.hidden = true;
        switch (data_dsp) {
            case 'table_div':
                var col_span_entete = create_element_of(`<div class="css_row"><td class="css_cell" colspan="${col_span_value}">${tr_data.html}</td></tr>`);
                this.tbody.appendChild(col_span_entete);

                var tmp_tr = create_element_in('div', this.tbody);
                //
                tmp_tr.classList.add('css_row', 'css_row_entete');
                this.DATA_MODEL.forEach(function (h_node) {
                    var tmp_td = create_element_in('div', tmp_tr);
                    var tmp_div = create_element_in('div', tmp_td);
                    tmp_div.innerHTML = h_node.title || '...';
                    tmp_td.classList.add('css_cell')
                    if (h_node.className) tmp_td.classList.add(...h_node.className.split(/\s+/).filter(Boolean));
                    tmp_div.classList.add('ellipsis');
                    tmp_tr.appendChild(tmp_td);
                }.bind(this));

                return [col_span_entete, tmp_tr];
                break;
            case 'table_icon':
            case 'table':
                var col_span_value = this.DATA_MODEL.length + 1;

                var col_span_entete = create_element_of(`<tr class="entete_groupe"><td colspan="${col_span_value}">${tr_data.html}</td></tr>`);
                this.tbody.appendChild(col_span_entete);
                var col_head = this.get_header();
                col_head.classList.add('css_row_entete');
                return [col_span_entete, col_head];
                break;
            default :
                var col_span_entete = create_element_of(`<div class="entete_groupe" style="width:100%"><div class="padding_more ededed bordert boxshadowb">${tr_data.html}</div></div>`);
                return [col_span_entete];
                break;
        }
    },
    build_data_row: function (tr_data, data_dsp = 'table') {

        var chk = create_element_of('<input value="' + tr_data.table_value + '" type="checkbox" name="id[]">');
        //console.log({tr_data});
        switch (data_dsp) {
            case 'table':
            case 'table_icon':
                var tmp_tr = create_element_of('<tr act_preview_mdl="' + this.options.table_preview + '"></tr>');
                if (this.has_header) {
                    var tmp_td = create_element_of('<td class="aligncenter chk_show"></td>');
                    tmp_td.appendChild(chk);
                    tmp_tr.appendChild(tmp_td);
                    chk.hidden = false;
                }
                for (var field_key in this.RAW_DATA_MODEL) {
                    if (this.RAW_DATA_MODEL.hasOwnProperty(field_key)) {
                        var model = this.RAW_DATA_MODEL[field_key];
                        var field_name = model.field_name,
                            field_name_raw = model.field_name_raw,
                            field_value = tr_data.html[field_name],
                            field_icon = model.icon || '',
                            field_className = model.className || '';

                        if (data_dsp === 'table_icon' && field_icon != null && field_value != null && field_name_raw != 'icon' && field_name_raw != 'color') {
                            field_value = '<i class="textgris fa fa-' + field_icon + '"></i>' + field_value
                        }
                        var tmp_td = create_element_in('td', tmp_tr);
                        tag_elem_table_field(tmp_td, field_name, field_name_raw, field_value, field_className);
                    }
                }
                tmp_tr.setAttribute('act_preview_mdl', 'app/app/app_fiche_preview');
                return tmp_tr;
                break;
            case 'note':
                var crh_prg = new windowGui('note' + tr_data.value, data_html.nomNote, '', '', {
                    parent: 'desktop',
                    startPosition: 'cascade',
                    ident: 'Note_' + tr_data.value,
                    scope: 'scope_note',
                    className: 'note noSize',
                    inTask: false,
                    onclose: function () {
                        ajaxValidation('app_update', 'mdl/app/', 'vars[estActifNote]=0&table=note&table_value=' + tr_data.value);
                    }.bind(this)
                });
                var tmp_td = this.div_piece.cloneNode(false);
                tag_elem_table_field(tmp_td, 'descriptionNote', 'description', data_html.descriptionNote, 'border4 margin padding');
                var tmp_tr = crh_prg.innerDisp;
                tmp_tr.setAttribute('id', id_tr);
                tmp_tr.replaceChildren(tmp_td);

                return tmp_tr;
                break;
            case 'thumb':
                var tmp_tr = create_element_of('<div class="app_thumb padding_more boxshadow"></div>');
                // tmp_tr.update ('<img height="150" width="150" data-filename="' + this.options.table_name + '-square-' + tr_data.value + '" data-src="http://' + window.document.location.host + '/img_src-' + this.options.table_name + '-square-' + tr_data.value + '.jpg" src="http://' + window.document.location.host + '/img_src-' + this.options.table_name + '-square-' + tr_data.value + '.jpg"/> ')
                for (var field of this.options.table_scheme['miniModel']) {
                    var field_name = field.field_name,
                        field_name_raw = field.field_name_raw,
                        field_value = tr_data.html[field_name] || null,
                        field_icon = field.icon || '',
                        field_className = field.className || '';
                    if (!field_value) continue;
                    var tmp_div = this.div_piece.cloneNode(false);
                    var html_tag = tag_elem_table_value(field_name, field_name_raw, field_value);
                    var html_value = `<div class="flex_h flex_align_middle fiche_field"><div class="label_field_icon"><i class="fa fa-${field_icon}"></i><span>${field_name}</span></div><div class="${field_className}" >${html_tag}</div></div>`;
                    tmp_tr.appendChild(tmp_div);
                    tmp_div.innerHTML = html_value;
                    console.log('html_tag', field_value, html_tag);
                }
                tmp_tr.setAttribute('act_preview_mdl', 'app/app/app_fiche_preview');
                tmp_tr.setAttribute('id', id_tr);
                return tmp_tr;
                break;
            case 'image':
                var tmp_tr = this.div_piece.cloneNode(false);
                tmp_tr.classList.add('autoToggle');

                var data_model = this.DATA_MODEL.filter(function (n) {
                    return n.field_name == 'nom' + ucfirst(this.options.table_name) || n.field_name == 'code' + ucfirst(this.options.table_name);
                }.bind(this));
                tmp_tr.innerHTML = '<div class="tile_image"  >' +
                    '<div class="tile_image_in"><img src="http://' + window.location.host + '/img_src-' + this.options.table_name + '-square-' + tr_data.table_value + '.jpg"></div>' +
                    '<div class="tile_text">' + tr_data.html['nom' + ucfirst(this.options.table_name)] + '</div>' +
                    '</div>';
                tmp_tr.appendChild(chk);
                tmp_tr.setAttribute('act_preview_mdl', 'app/app_img/image_app_liste_img');
                return tmp_tr;
                break;
            case 'fields':
                var tmp_tr = create_element_of('<div class="  padding_more boxshadow"></div>');
                // tmp_tr.update ('<img height="150" width="150" data-filename="' + this.options.table_name + '-square-' + tr_data.value + '" data-src="http://' + window.document.location.host + '/img_src-' + this.options.table_name + '-square-' + tr_data.value + '.jpg" src="http://' + window.document.location.host + '/img_src-' + this.options.table_name + '-square-' + tr_data.value + '.jpg"/> ')
                for (var field_key in this.RAW_DATA_MODEL) {
                    if (this.RAW_DATA_MODEL.hasOwnProperty(field_key)) {
                        var model = this.RAW_DATA_MODEL[field_key];
                        var field_title = model.title,
                            field_name = model.field_name,
                            field_name_raw = model.field_name_raw,
                            field_value = tr_data.html[field_name],
                            field_icon = field_key.icon || '',
                            field_className = model.className || '';

                        var tmp_div = this.div_piece.cloneNode(false);

                        var html_tag = tag_elem_table_value(field_name, field_name_raw, field_value);
                        var html_value = `<div class="flex_h flex_wrap flex_align_middle fiche_field" style="min-width:50%;"><div class="label_field_icon"><i class="fa fa-${field_icon}"></i><span>${field_name}</span></div><div>${html_tag}</div></div>`;
                        tmp_tr.appendChild(tmp_div);
                        tmp_div.innerHTML = html_value;
                    }
                }
                tmp_tr.setAttribute('act_preview_mdl', 'app/app/app_fiche_preview');
                tmp_tr.setAttribute('id', id_tr);
                return tmp_tr;
                break;
        }
        chk.hidden = true;
        var id_tr = this.element.id + '_' + this.options.table_name + '_' + tr_data.table_value + '-' + this.element.id;
        if (document.getElementById(id_tr)) return document.getElementById(id_tr);

    },
    setIndex: function (index) {

    },
    build_data: function (args) {
        if (!this.element.querySelector('[expl_count]')) {
            this.expl_count = document.createElement('div');
            this.expl_count.setAttribute('expl_count', 'true')
            this.element.append(this.expl_count)
            this.expl_count.hidden = true;
        }
        if (args[0]) if (args[0]['maxcount']) this.maxcount = args[0]['maxcount'];
        if (args[0]) if (args[0]['count']) this.count = args[0]['count'];
        if (args[0] && args[0]['chunk'] == 0) this.getCount();
        var data_vars = [];

        for (var property in args) {
            if (args.hasOwnProperty(property)) {
                data_vars[property] = args[property];
            }
        }
        var args = data_vars,
            debug_ct = 0;

        var iter = args || this.options.table_data;

        var DATA_MODEL_DEFAULT = [];
        this.DATA_MODEL_DEFAULT.forEach(function (h_node) {
            DATA_MODEL_DEFAULT[h_node.field_name] = h_node;
        });
        //
        var DATA_MODEL = [];
        this.DATA_MODEL.forEach(function (h_node) {
            DATA_MODEL[h_node.field_name_raw] = h_node;
        });

        var SCHEME_ICON = this.options.table_scheme.iconAppscheme || '';
        var SCHEME_COLOR = this.options.table_scheme.colorAppscheme || '';

        // args       = args.concat ([args]);
        if (this.from_cache !== true) {
            this.cache = this.cache.concat(args);
        }

        iter.forEach(function (data) {
            var tr_data = data
            debug_ct++;

            if (this.table_activity_count) this.table_activity_count.textContent = this.cache.length// nouveau tr si pas groupbyc
            if (!tr_data.groupBy) {
                var data_vars = tr_data.vars;
                if (!data_vars) {
                    console.log('Allerrttte : ', tr_data);
                }
                var data_html = tr_data.html;
                var id_tr = this.element.id + '_' + this.options.table_name + '_' + data_vars.table_value + '-' + this.element.id;


                if (!document.getElementById(id_tr) && data_vars) {

                    var chk = document.createElement('input');
                    chk.setAttribute('type', 'checkbox')
                    chk.setAttribute('name', 'id[]')
                    chk.setAttribute('value', tr_data.table_value)
                    chk.hidden = true;
                    var i_ct = 0;
                    switch (this.element.getAttribute('data-dsp')) {
                        case 'planning':
                            var dateDebut = data_html['dateDebut' + this.options.Table_name],
                                heureDebut = data_html['heureDebut' + this.options.Table_name],
                                heureFin = data_html['heureFin' + this.options.Table_name];
                            var dateSelector = '[data-droptache="dropzone"][dropvalue="' + CSS.escape(String(dateDebut)) + '"]';
                            if (this.tbody.querySelectorAll(dateSelector).length == 0) break;
                            var tmp_tr = this.div_piece.cloneNode(false);
                            var exactSelector = dateSelector + '[heuredebut="' + CSS.escape(String(heureDebut)) + '"]';
                            var morningSelector = dateSelector + '[heuredebut="AM"]';
                            if (this.tbody.querySelectorAll(exactSelector).length != 0) {
                                this.tbody.querySelectorAll(exactSelector).forEach(function (node_parent) {
                                    node_parent.appendChild(tmp_tr);
                                    var resizeEvent = new CustomEvent('dom:resizetache', { bubbles: true, cancelable: true });
                                    resizeEvent.memo = {};
                                    node_parent.dispatchEvent(resizeEvent);
                                }.bind(this));
                            } else if (this.tbody.querySelectorAll(morningSelector).length != 0) {
                                this.tbody.querySelectorAll(morningSelector).forEach(function (node_parent) {
                                    node_parent.appendChild(tmp_tr);
                                    var resizeEvent = new CustomEvent('dom:resizetache', { bubbles: true, cancelable: true });
                                    resizeEvent.memo = {};
                                    node_parent.dispatchEvent(resizeEvent);
                                }.bind(this))
                            } else {
                                this.tbody.querySelectorAll(dateSelector).forEach(function (node_parent) {
                                    node_parent.appendChild(tmp_tr);
                                    var resizeEvent = new CustomEvent('dom:resizetache', { bubbles: true, cancelable: true });
                                    resizeEvent.memo = {};
                                    node_parent.dispatchEvent(resizeEvent);
                                }.bind(this));
                            }
                            ;
                            var attr = {
                                'draggable': 'true',
                                'id': id_tr,
                                'data-dateDebut': dateDebut,
                                'data-heureDebut': heureDebut,
                                'data-dragtache': 'tache',
                                'data-parent': this.tbody.id || (this.tbody.id = uniqid('datatable_body'))
                            }
                            Object.entries(attr).forEach(function ([name, value]) { tmp_tr.setAttribute(name, value); });
                            tmp_tr.innerHTML = '<div style="height:100%;overflow:hidden;" act_defer mdl="app/app_planning/app_planning_tache" vars="idtache=' + tr_data.table_value + '"></div>';
                            tmp_tr.classList.add('absolute', 'dyntache', 'tachehebdo');
                            var resizeEvent = new CustomEvent('dom:resizetache', { bubbles: true, cancelable: true });
                            resizeEvent.memo = {};
                            tmp_tr.dispatchEvent(resizeEvent);
                            break;
                        case 'conge':

                            var idconge = tr_data.html['idconge'];
                            var idagent = tr_data.html['idagent'];
                            var dateDebut = tr_data.html['dateDebut' + this.options.Table_name];
                            var dateFin = tr_data.html['dateFin' + this.options.Table_name];
                            var heureDebut = tr_data.html['heureDebut' + this.options.Table_name];
                            var heureFin = tr_data.html['heureFin' + this.options.Table_name];
                            var duree = tr_data.html['duree' + this.options.Table_name];

                            var tmp_tr = create_element_of('<div style="position:absolute;"></div>')
                            var attr = {
                                /*'draggable'       : 'true',*/
                                'id': id_tr,
                                'data-idagent': idagent,
                                'data-dateDebut': dateDebut,
                                'data-heureDebut': heureDebut,
                                'data-dateFin': dateFin,
                                'data-heureFin': heureFin,
                                'data-dragconge': 'conge',
                                'data-parent': this.tbody.id || (this.tbody.id = uniqid('datatable_body'))
                            }
                            Object.entries(attr).forEach(function ([name, value]) { tmp_tr.setAttribute(name, value); });

                            this.tbody.querySelectorAll('[data-idconge="' + CSS.escape(String(idconge)) + '"]').forEach(function (node) { node.remove(); });
                            tmp_tr.innerHTML = '<div  act_defer mdl="app/app_conge/app_conge_drag" vars="idconge=' + tr_data.table_value + '"></div>';

                            this.tbody.appendChild(tmp_tr);
                            break;
                        case 'mdl':
                            var tmp_tr = this.div_piece.cloneNode(false);
                            tmp_tr.innerHTML = tr_data.mdl;
                            break;
                        case 'table_div':
                            var tmp_tr = document.createElement('div');
                            //	console.log(tmp_tr);
                            tmp_tr.classList.add('css_row', 'autoToggle');
                            tmp_tr.setAttribute('act_preview_mdl', 'app/app/app_fiche_preview');
                            Object.assign(tmp_tr.style, {
                                'width': '350px',
                                overflow: 'hidden'
                            });

                            // tmp_tr.appendChild (chk);

                            for (var field_key in this.DATA_MODEL) {
                                if (this.DATA_MODEL.hasOwnProperty(field_key)) {
                                    var model = this.DATA_MODEL[field_key];
                                    var field_title = model.title,
                                        field_name = model.field_name,
                                        field_name_raw = model.field_name_raw,
                                        field_value = tr_data.html[field_name],
                                        field_icon = model.icon || '',
                                        field_className = model.className || '';
                                    var tmp_td = create_element_in('div', tmp_tr);
                                    tmp_td.classList.add('css_cell', 'padding');
                                    field_value = '<span class="flex_h"><i class="textgris padding fa fa-' + field_icon + ' border"></i><span class="flex_main">' + field_value + '</span></span>';
                                    tag_elem_table_field(tmp_td, field_name, field_name_raw, field_value, field_className);
                                }
                            }
                            break;
                        case 'group_vertical':
                            var tmp_tr = this.div_piece.cloneNode(false);
                            tmp_tr.classList.add('border4', 'margin', 'blanc');
                            tmp_tr.appendChild(chk);
                            tmp_tr.setAttribute('act_preview_mdl', 'app/app/app_fiche_preview');
                            this.DATA_MODEL.forEach(function (h_node) { // opportunites , micro fiches
                                var field_title = h_node.title
                                var field_name = h_node.field_name
                                var field_name_raw = h_node.field_name_raw
                                var field_value = tr_data.html[field_name];
                                var field_className = h_node.className || '';
                                //
                                var tmp_div = this.div_piece.cloneNode(false);
                                tmp_tr.appendChild(tmp_div);
                                tag_elem_table_field_labelled(tmp_div, field_name, field_name_raw, field_value, field_title, field_className);
                            }.bind(this));
                            break;
                        case 'icon':
                            var tmp_tr = this.div_piece.cloneNode(false);
                            tmp_tr.classList.add('autoToggle', 'inline', 'tile');
                            tmp_tr.setAttribute('act_preview_mdl', 'app/app/app_fiche_preview');
                            tmp_tr.innerHTML = '<div class="inline relative padding"  >'
                                + '<i class="fa fa-file-o fa-2x textgris"></i><br>'
                                + '  <div class="inline absolute"  style="top:10px">'
                                + ' <i class="fa fa-' + this.options.table_scheme.icon + ' fa-2x"></i>'
                                + ' </div>'
                                + '</div>'
                                + '<div class="tile_text aligncenter"> <span class=""> ' + this.options.table_name + ' </span></div>';
                            tmp_tr.appendChild(chk);
                            var data_model = this.DATA_MODEL.filter(function (n) {
                                return n.field_name == 'nom' + ucfirst(this.options.table_name) || n.field_name == 'code' + ucfirst(this.options.table_name);
                            });
                            if (data_model.length == 0) data_model = this.DATA_MODEL.filter(function (n) {
                                return n.field_name == 'code' + ucfirst(this.options.table_name);
                            });
                            data_model.forEach(function (h_node) {
                                var field_name = h_node.field_name
                                var field_name_raw = h_node.field_name_raw
                                var field_value = tr_data.html[field_name];
                                var field_className = h_node.className || 'flex_main';
                                //
                                var tmp_div = document.createElement('span');
                                tmp_tr.append(tmp_div);
                                tag_elem_table_field(tmp_div, field_name, field_name_raw, field_value, field_className);
                            }.bind(this));
                            break;
                        case 'flex_line':
                            var tmp_tr = this.div_piece.cloneNode(false);
                            tmp_tr.classList.add('app_line', 'applink', 'applinkblock');
                            Object.assign(tmp_tr.style, {
                                'width': '250px',
                                'max-width': '250px',
                                'overflow': 'hidden'
                            }) // ,'max-width':'50%'
                            var tmp_div_cont = document.createElement('a');
                            tmp_div_cont.classList.add('autoToggle');
                            tmp_tr.appendChild(tmp_div_cont);
                            tmp_div_cont.appendChild(chk);
                            var data_model = this.DATA_MODEL.filter(function (n) {
                                return n.field_name == 'nom' + ucfirst(this.options.table_name);
                            }.bind(this));
                            if (data_model.length == 0) {
                                data_model = this.DATA_MODEL.filter(function (n) {
                                    return n.field_name == 'code' + ucfirst(this.options.table_name);
                                }.bind(this));
                            }
                            var data_model_statut = this.DATA_MODEL.filter(function (n) {
                                return n.field_name == 'color' + ucfirst(this.options.table_name) + '_statut';
                            }.bind(this));
                            data_model_statut.forEach(function (h_node) {
                                var field_name = h_node.field_name
                                var field_name_raw = h_node.field_name_raw
                                var field_value = tr_data.html[field_name];
                                var field_className = h_node.className || 'flex_main';
                                //
                                var tmp_div = create_element_in('div', tmp_div_cont);
                                tag_elem_table_field(tmp_div, field_name, field_name_raw, field_value, field_className);
                            }.bind(this));
                            data_model.forEach(function (h_node) {
                                var field_value = tr_data.html[h_node.field_name];
                                var field_className = h_node.className || 'flex_main';
                                //
                                var tmp_div = create_element_in('div', tmp_div_cont);
                                tmp_div.innerHTML = '<i class="textgrisfonce padding fa fa-' + this.options.table_scheme.icon + '"></i>';
                                var tmp_div = create_element_in('div', tmp_div_cont);
                                Object.assign(tmp_div.style, {
                                    overflow: 'hidden'
                                });
                                tag_elem_table_field(tmp_div, h_node.field_name, h_node.field_name_raw, field_value, field_className);
                            }.bind(this));
                            // data-dsp_fields
                            if (this.element.getAttribute('data-dsp_fields')) {
                                tmp_div_cont.classList.add('flex', 'flex_h', 'flex_align_middle');
                                var add_field_model = explode(';', this.element.getAttribute('data-dsp_fields'));
                                add_field_model.forEach(function (field_key) {
                                    var h_node = DATA_MODEL_DEFAULT[field_key],
                                        field_value = tr_data.html[field_key],
                                        field_className = h_node.className || 'flex_main ellipsis';
                                    //
                                    var tmp_div = create_element_in('div', tmp_div_cont);
                                    Object.assign(tmp_div.style, {
                                        overflow: 'hidden'
                                    });
                                    tmp_div.classList.add('textgrisfonce');
                                    tag_elem_table_field(tmp_div, h_node.field_name, h_node.field_name_raw, field_value, field_className);
                                }.bind(this))
                            }
                            tmp_tr.setAttribute('data-link', true);
                            tmp_tr.setAttribute('data-vars', 'table=' + this.options.table_name + '&table_value=' + data_vars.table_value);
                            break;
                        case 'line':
                            var tmp_tr = this.div_piece.cloneNode(false);
                            var tmp_tr = create_element_of('<div class="app_line applink applinkblock ellipsis" act_preview_mdl="' + this.options.table_preview + '"></div>'); //this.tr_piece.cloneNode(false);

                            //tmp_tr.addClassName ('app_line applink applinkblock ellipsis');
                            // var tmp_div_cont = new Element ('a');
                            var tmp_div_cont = create_element_of('<a class="autoToggle flex_h flex"></a>');
                            //tmp_div_cont.addClassName ('autoToggle flex_h flex');
                            tmp_tr.appendChild(tmp_div_cont);
                            tmp_div_cont.appendChild(chk);

                            var data_model = this.DATA_MODEL.filter(function (n) {
                                return n.field_name == 'nom' + ucfirst(this.options.table_name);
                            }.bind(this));
                            if (data_model.length == 0) {
                                data_model = this.DATA_MODEL.filter(function (n) {
                                    return n.field_name == 'code' + ucfirst(this.options.table_name);
                                }.bind(this));
                            } //

                            // data-dsp_fields
                            if (this.element.getAttribute('data-dsp_fields')) {
                                // console.log(explode(';', this.element.readAttribute('data-dsp_fields')));
                            }

                            data_model.forEach(function (h_node) {
                                var field_name = h_node.field_name
                                var field_name_raw = h_node.field_name_raw
                                var field_value = tr_data.html[field_name];
                                var field_className = h_node.className || 'flex_main';
                                //
                                var tmp_div1 = create_element_in('span', tmp_div_cont);
                                var tmp_div2 = create_element_in('span', tmp_div_cont, {
                                    className: 'flex_main'
                                });

                                tmp_div1.innerHTML = '<i class="padding fa fa-' + SCHEME_ICON + '" style="color:' + SCHEME_COLOR + '"></i>';
                                tag_elem_table_field(tmp_div2, field_name, field_name_raw, field_value, field_className);

                            }.bind(this));
                            tmp_tr.setAttribute('data-link', true);
                            tmp_tr.setAttribute('data-vars', 'table=' + this.options.table_name + '&table_value=' + data_vars.table_value);
                            break;
                        case 'table_line':
                            var tpl_tr = this.activate_fragment('frag_table_line_item', 'table_line_item');
                            var tmp_tr = tpl_tr['table_item'];

                            var named_field = (DATA_MODEL['nom']) ? 'nom' : 'code';
                            if (DATA_MODEL[named_field]) {
                                var field_name = DATA_MODEL[named_field]['field_name'];
                                var field_name_raw = DATA_MODEL[named_field]['field_name_raw'];
                                var field_className = DATA_MODEL[named_field]['className'];

                            }

                            tpl_tr['table_item_main'].classList.add('app_line', 'applink');
                            if (field_className) tpl_tr['table_item_main'].classList.add(...field_className.split(/\s+/).filter(Boolean));
                            var attr_tr = {
                                'data-link': true,
                                'data-vars': 'table=' + this.options.table_name + '&table_value=' + data_vars.table_value
                            }
                            Object.entries(attr_tr).forEach(function ([name, value]) {
                                tpl_tr['table_item_main'].setAttribute(name, value);
                            });

                            if (tr_data.html[field_name]) {
                                var content = '<i class="textbleu padding fa fa-' + this.options.table_scheme.icon + '"></i>' + tr_data.html[field_name] + '&nbsp;';
                            } else {
                                var content = '<i class="textbleu padding fa fa-' + this.options.table_scheme.icon + '"></i>' + '&nbsp;';
                            }
                            // console.log('tr_data',tr_data.html)
                            this.DATA_MODEL.forEach(function (h_node) {
                                if (h_node.field_name_raw != named_field) {
                                    var field_name = h_node.field_name;
                                    var field_name_raw = h_node.field_name_raw;
                                    var field_className = h_node.className || '';
                                    var field_icon = h_node.icon || '';
                                    var str;

                                    // console.log(field_name,field_name_raw,tr_data.html[field_name]);

                                    if (tr_data.html[field_name] && String(tr_data.html[field_name]).replace(/<[^>]*>/g, '').trim() != '') {
                                        if (field_className != 'nb_field') {
                                            if (tr_data.html[field_name]) {
                                                str = '<i class="padding fa fa-' + field_icon + ' textgris"></i>';
                                                str += '<div class="' + field_className + '">';
                                                str += '&nbsp;' + tr_data.html[field_name]
                                                str += '</div>';
                                            } else {
                                                str = '<div>Vide !!! </div>';
                                            }
                                            tpl_tr['table_item_data_fk'].insertAdjacentHTML('beforeend', str);
                                        } else {
                                            var table_fk = field_name_raw.replace('count_', '');
                                            var table_fk_vars = 'table=' + table_fk + '&nbRows=15&vars[id' + this.options.table_name + ']=' + tr_data.html['id' + this.options.table_name];
                                            var str = tr_data.html[field_name] || '';
                                            // console.log( ' table_fk '+ table_fk)
                                            if (String(str).replace(/<[^>]*>/g, '').trim() != '') {

                                                tpl_tr['table_item_count_zone'].insertAdjacentHTML('afterbegin', str);
                                                tpl_tr['table_line_item_more_title'].textContent = table_fk;
                                                tpl_tr['table_line_item_more_fk'].insertAdjacentHTML('afterbegin', '<div class=""><div class="flex_main" data-data_model="' + this.options.data_model + '" data-dsp_liste="dsp_liste" data-vars="' + table_fk_vars + '" data-dsp="table_line">' + field_name + ' </div></div>');

                                            } else {
                                                tpl_tr['table_line_item_more_fk'].insertAdjacentHTML('afterbegin', '<div>Vide !!! </div>');
                                            }

                                        }
                                    }
                                }
                            }.bind(this));

                            tag_elem_table_field(tpl_tr['table_item_main'], field_name, field_name_raw, content, field_className);
                            break;

                            if (this.options.table_scheme.grilleRFK) {
                                this.options.table_scheme.grilleRFK.forEach(function (node, index) {
                                    var del_fk = node.table;
                                    tmp_div_cont.insertAdjacentHTML('beforeend', del_fk);
                                }.bind(this))
                            }

                            break;
                        case 'line_fk':
                            var tmp_tr = this.div_piece.cloneNode(false);
                            tmp_tr.classList.add('app_line', 'applink', 'applinkblock', 'ellipsis');
                            var tmp_div_cont = document.createElement('a');
                            var tmp_div_chk = document.createElement('div');
                            tmp_div_chk.replaceChildren(chk);
                            tmp_div_cont.appendChild(tmp_div_chk);
                            tmp_div_cont.classList.add('autoToggle', 'flex_h', 'flex_inline', 'padding');
                            tmp_tr.appendChild(tmp_div_cont);
                            var data_model = this.DATA_MODEL.filter(function (n) {
                                return n.field_name == 'nom' + ucfirst(this.options.table_name);
                            }.bind(this));
                            if (data_model.length == 0) data_model = this.DATA_MODEL.filter(function (n) {
                                return n.field_name == 'code' + ucfirst(this.options.table_name);
                            }.bind(this));
                            // data_model // this.options.table_scheme.columnModel
                            data_model.forEach(function (h_node) {
                                var field_name = h_node.field_name
                                var field_name_raw = h_node.field_name_raw
                                var field_value = '<div class="ellipsis"><i class="textbleu padding fa fa-' + this.options.table_scheme.icon + '"></i>' + tr_data.html[field_name] + '</div>';
                                var tmp_div = document.createElement('div');
                                tmp_div_cont.appendChild(tmp_div);
                                if (tr_data.html.grille_FK) {
                                    var fk_g = tr_data.html.grille_FK;
                                    for (var property in fk_g) {
                                        if (fk_g.hasOwnProperty(property)) {
                                            if (fk_g[property]) {
                                                var content = '&nbsp;/&nbsp;' + fk_g[property] + '&nbsp;';
                                            } else {
                                                var content = '&nbsp;/&nbsp;';
                                            }
                                            tmp_div_cont.insertAdjacentHTML('beforeend', '<div   style="width:110px;max-width:110px;" class="textgrisfonce"><div class="ellipsis"><i class="fa fa-' + h_node.icon + '"></i>' + content + '</div></div>');
                                        }
                                    }
                                }
                                var field_className = h_node.className || 'flex_main';
                                //
                                tag_elem_table_field(tmp_div, field_name, field_name_raw, field_value, field_className);
                                //
                                Object.assign(tmp_div.style, {
                                    width: '512px',
                                    'max-width': '250px'
                                });
                                tmp_div.classList.add('flex_main');
                            }.bind(this));
                            tmp_tr.setAttribute('data-link', true);
                            tmp_tr.setAttribute('data-vars', 'table=' + this.options.table_name + '&table_value=' + data_vars.table_value);
                            break;
                        case 'image':
                            var tmp_tr = this.build_data_row(tr_data, 'image');
                            break;
                        case 'note':
                            var tmp_tr = this.build_data_row(tr_data, 'note');
                            break;
                        case 'fields':
                            var tmp_tr = this.build_data_row(tr_data, 'fields');
                            break;
                        case 'thumb':
                            var tmp_tr = this.build_data_row(tr_data, 'thumb');
                            break;
                        case 'table_icon':
                            var tmp_tr = this.build_data_row(tr_data, 'table_icon');
                            break;
                        default:
                            var tmp_tr = this.build_data_row(tr_data);
                            break;
                    }
                    if (tmp_tr) {

                        tmp_tr.classList.add('autoToggle');
                        tmp_tr.setAttribute('id', id_tr);
                        tmp_tr.setAttribute('md5', tr_data.md5 || '');
                        tag_elem_table(tmp_tr, this.options.table_name, data_vars.table_value);
                        if (this.element.getAttribute('data-dsp') != 'planning') {
                            /*if ( this.last_inserted_elem && this.last_inserted_elem.parentNode ) {
                             this.last_inserted_elem.parentNode.insertBefore (tmp_tr, this.last_inserted_elem.nextSibling);
                             } else {
                             this.tbody.appendChild (tmp_tr);
                             }*/
                            this.tbody.appendChild(tmp_tr);
                        }
                        if (this.element.getAttribute('data-sort')) {
                            tmp_tr.setAttribute('draggable', 'true');
                            tmp_tr.setAttribute('data-sort_element', 'true');
                        }
                        this.last_inserted_elem = tmp_tr;
                    }
                } else {
                    if (this.element.getAttribute('data-dsp') == 'planning') {
                    }
                    this.last_inserted_elem = document.getElementById(id_tr);
                }
            }
            if (tr_data.groupBy) {

                var id_row_groupBy = this.element.id + '_' + this.options.table_name + '_' + tr_data.table_value + '-' + tr_data.table;
                if (document.getElementById(id_row_groupBy)) {
                    return document.getElementById(id_row_groupBy);
                }
                var tmp_groupBy = this.build_data_row_groupby(tr_data, this.element.getAttribute('data-dsp'));
                if (tmp_groupBy) {
                    for (const tmp_g of tmp_groupBy) {
                        if (this.last_inserted_elem && this.last_inserted_elem.parentNode) {
                            this.last_inserted_elem.parentNode.insertBefore(tmp_g, this.last_inserted_elem.nextSibling);
                        } else {
                            this.tbody.appendChild(tmp_g);
                        }
                        this.last_inserted_elem = tmp_g;
                        this.last_inserted_elem.setAttribute('id', id_row_groupBy);
                    }
                }
                this.last_inserted_elem.setAttribute('id', id_row_groupBy);
            }
            if (tr_data.chunk && tr_data.chunks && (tr_data.chunk == tr_data.chunks) && debug_ct == iter.length) {
                if (this.from_cache !== true) {
                   // this.cache_data();
                    //console.log('end cache '+this.options.table_scheme.nomAppscheme,tr_data.chunk , tr_data.chunks);
                }
            }
        }.bind(this));
        if (this.from_cache === true) {

        }

    },
    getCount: function () {

        if (this.element.querySelector('[expl_count]')) {
            if (this.count == this.maxcount) this.expl_count.textContent = this.count
            else this.expl_count.textContent = this.count + ' / ' + this.maxcount
        } // new

        if (this.table_activity_count) {
            if (this.count == this.maxcount) this.table_activity_count.textContent = this.count + ' résultats'
            else {
                this.table_activity_count.textContent = this.count + ' résultats sur ' + this.maxcount;
                if (this.table_activity_pager_select) {
                    this.table_activity_pager_select.textContent = Math.ceil((this.maxcount / this.count)) + ' pages'
                }
            }

            this.nbPage = Math.ceil((this.maxcount / this.count));
            if (this.nbPage >= 8) {
            }
            if (this.count != this.maxcount && (this.table_activity_pager.children.length != this.nbPage)) {
                this.table_activity_pager.replaceChildren();
                if (this.nbPage <= 8) {
                    this.table_activity_pager_more_trigger.hidden = true;
                } else {
                    this.table_activity_pager_more_trigger.hidden = false;
                }
                add_page = function (page) {
                    var css = (this.page == page) ? 'active' : '';
                    if (page > 8) {
                        if (this.table_activity_pager_more)
                            this.table_activity_pager_more.insertAdjacentHTML('beforeend', '<div class="' + css + ' autoToggle" app_button_scope="app_button_scope" vars="page=' + page + '">' + page + '</div>')
                    } else {
                        this.table_activity_pager.insertAdjacentHTML('beforeend', '<div class="' + css + ' autoToggle" app_button_scope="app_button_scope" vars="page=' + page + '">' + page + '</div>')
                    }
                };
                for (var page = 0; page < this.nbPage; page++) add_page.call(this, page);
            } else {
            }
            if (this.table_activity_count_menu && !this.table_activity_count_menu.textContent.trim()) {
                [
                    10,
                    50,
                    100,
                    200,
                    500,
                    1500,
                    3000
                ].reverse().forEach(function (value) {
                    this.table_activity_count_menu.insertAdjacentHTML('beforeend', '<a class="autoToggle" app_button_scope="app_button_scope" vars="nbRows=' + value + '">' + value + '</a>')
                }.bind(this))
            }
        }
        this.element.setAttribute('data-table_count', this.count);
        this.element.setAttribute('data-table_count_max', this.maxcount);
//
    },
    getSum: function () {
        if (this.element.getAttribute('data-dsp-sum')) {
            if (document.getElementById(this.element.getAttribute('data-dsp-sum'))) {
                this.treporter.replaceChildren();
                this.sum_zone_tmp = create_element_in('div', this.treporter, {
                    className: 'flex_h flex_align_middle padding'
                });
                this.sum_zone_tmp.innerHTML = '<div class="border4"><i class="fa fa-calculator textbold"></i> Total</div><div class="sum_zone flex_h"></div>';
                this.sum_zone = this.element.querySelector('.sum_zone');
                this.moy_zone_tmp = create_element_in('div', this.treporter, {
                    className: 'flex_h flex_align_middle padding'
                });
                this.moy_zone_tmp.innerHTML = '<div><i class="fa fa-calculator textbold"></i> Moyenne</div><div class="moy_zone"></div>';
                this.moy_zone = this.element.querySelector('.treporter')
                this.sum_zone_tmp.hidden = true;
                this.moy_zone_tmp.hidden = true;
                for (var field_key in this.RAW_DATA_MODEL) {
                    if (this.RAW_DATA_MODEL.hasOwnProperty(field_key)) {
                        var model = this.RAW_DATA_MODEL[field_key];
                        // console.log('field_name_raw',model.field_name_raw);
                        var field_name_raw = model.field_name_raw;
                        if (!empty(window.APP.APPFIELDS[field_name_raw])) {
                            if (!empty(window.APP.APPFIELDS[field_name_raw]['has_totalAppscheme_field'])) {
                                var childs = this.element.querySelectorAll('[data-field_name="' + CSS.escape(String(model.field_name)) + '"]');
                                var total_ligne = 0;
                                childs.forEach(function (child) {
                                    var a = child.textContent.replace(/\s|€|\u00a0/g, '');
                                    total_ligne += Number(a) || 0
                                }.bind(this))
                                var totalNode = create_element_in('div', this.sum_zone, {
                                    className: 'padding'
                                });
                                totalNode.textContent = model.title + ' : ' + Math.round(total_ligne, 2);
                                this.sum_zone_tmp.hidden = false;
                            }
                            if (!empty(window.APP.APPFIELDS[field_name_raw]['has_moyenneAppscheme_field'])) {
                                var childs = this.element.querySelectorAll('[data-field_name="' + CSS.escape(String(model.field_name)) + '"]');
                                var moyenne_ligne = 0;
                                childs.forEach(function (child) {
                                    var a = child.textContent.replace(/\s|€|\u00a0/g, '');
                                    moyenne_ligne += Number(a) || 0
                                }.bind(this))
                                moyenne_ligne = moyenne_ligne / childs.length;
                                var averageNode = create_element_in('div', this.moy_zone, {
                                    className: 'padding'
                                });
                                averageNode.textContent = model.title + ' : ' + Math.round(moyenne_ligne, 4);
                                this.moy_zone_tmp.hidden = false;
                            }
                        }
                    }
                }
            }
        }
    },
    cache_data: function () {
        window.app_cache.removeItem(this.app_cache_key, function (err, result) {
            //console.log('cache erased',err,result);
        }).then(()=> {
            window.app_cache.setItem(this.app_cache_key, this.cache, function (err, result) {
            }).then(()=> {
                this.cache_data_verify();
            });
        });
    },
    cache_data_verify: function () {
        // console.log('cache_data_verify '+this.options.table_scheme.nomAppscheme,'grom_cache',this.from_cache);
        this.cache_clone = {}
        for (const cache_index of this.cache) {
            if (cache_index.table && cache_index.table_value) {
                this.cache_clone[cache_index.table + '-' + cache_index.table_value] = cache_index.md5 || 'md5';
            }
        }
        Array.from(this.tbody.children).forEach((node)=> {
            if (node.getAttribute('data-table')) {
                if (node.getAttribute('data-table_value')) {
                    let key = node.getAttribute('data-table') + '-' + node.getAttribute('data-table_value');
                    if (this.cache_clone[key]) {
                        if (this.cache_clone[key] != node.getAttribute('md5')) {
                            console.log('changed ', this.cache_clone[key], node.getAttribute('md5'))
                            //$(node).update('update');
                        }
                    } else {
                        node.remove();
                    }
                }
            }
        })
        this.setCache([]);
    },
    setCache: function (content) {
        this.cache = content;
    },
    getCache: function () {
        return this.cache;
    }
}

BuildSearch = function () {
    this.initialize.apply(this, arguments);
};
BuildSearch.prototype = {
    apptpl_table: '<table ><thead><tr></tr></thead><tbody class="toggler div_tbody" ></tbody></table>',
    apptpl_table_footer: '<div class="bordert"><div class="padding"></div></div>',
    initialize: function (element, options) {
        this.element = typeof element === 'string' ? document.getElementById(element) : element
        this.options = Object.assign({
            url_data: '',
            table_scheme: {},
            nbRows: 10
        }, options || {});
//
// this.options.table_scheme = window.APP.APPSCHEMES[this.options.table_name];
        if (!this.element.id) this.element.id = uniqid('search');
        this.tbody = document.createElement('div');
        this.tbody.classList.add('relative');
        this.div_piece = document.createElement('div');
        this.element.replaceChildren(this.tbody);
        this.element.addEventListener('dom:stream_chunk', function (event) {
            var res_tmp = event.memo;
            var data = window.register_stream[res_tmp]['data'];
            var data_main = data['data_main'];
            this.options.table_data = data_main;
            this.build_data(data_main);
        }.bind(this));
    },
    load_data: function (args) {
        search_args = Object.fromEntries(new URLSearchParams(args));
        this.tbody.replaceChildren();
        if (!this.page) this.page = 0;
        if (!this.count) this.count = 0;
        if (!this.maxcount) this.maxcount = 0;
        this.uniqid = uniqid();
        this.element.setAttribute('data-uniqid', this.uniqid);
        get_data('json_data_search', {
            nbRows: this.options.nbRows,
            groupBy: this.options.groupBy,
            page: this.page,
            sort: this.options.sort,
            search: search_args.search,
            url_data: this.options.url_data + '&' + args
        }, {
            stream_to: this.uniqid
        }, function (err) {
        }).then(function (res) {
            /* this.page++;
             res = JSON.parse(res);
             this.count += this.options.nbRows;
             this.options.table_data = res.data_main;
             this.build_data();*/
        }.bind(this));
    },
    build_data: function (args) {
        data_vars = [];
        for (var property in args) {
            if (args.hasOwnProperty(property)) {
                data_vars[property] = args[property];
            }
        }
        args = data_vars;
        var iter = args || this.options.table_data;
        this.tbody.classList.add('flex_v')
        iter.forEach(function (tr_data) {
// nouveau tr si pas groupby
            if (!tr_data.groupBy) {
                var data_html = tr_data.html;
                var id_tr = this.element.id + '-' + tr_data.name_id + '_' + tr_data.value;
                if (!document.getElementById(id_tr)) {
                    var tmp_tr = this.div_piece.cloneNode(false);
                    var i_ct = 0;
                    var field_name = tr_data.name_id
                    var field_name_raw = tr_data.table
                    var val_data = data_html.nom + '' + data_html.nom_fk;
// tds
                    var tmp_div = this.div_piece.cloneNode(false);
                    tmp_div.innerHTML = val_data || '';
                    tmp_div.setAttribute('field_name', field_name);
                    tmp_div.setAttribute('field_name_raw', field_name_raw);
                    tmp_tr.appendChild(tmp_div);
// tmp_tr.appendChild(tmp_div_fk);
                    tmp_tr.setAttribute('data-groupIn', tr_data.table);
                    tmp_tr.classList.add('animated', 'bounce');
//
                    tag_elem_table_field(tmp_div, field_name, field_name_raw, val_data);
                    tag_elem_table(tmp_tr, tr_data.table, tr_data.value);
                    if (this.tbody.querySelector('[data-groupBy="' + CSS.escape(String(tr_data.table)) + '"]')) {
                        this.tbody.querySelector('[data-groupBy="' + CSS.escape(String(tr_data.table)) + '"]').appendChild(tmp_tr);
                    } else {
                        this.tbody.appendChild(tmp_tr);
                    }
                } else {
                    this.last_inserted_elem = document.getElementById(id_tr);
                }
            }
            if (tr_data.groupBy) {
                var tmp_tr = this.div_piece.cloneNode(false);
                var tmp_td = this.div_piece.cloneNode(false);
                tmp_td.innerHTML = tr_data.html;
                tmp_td.setAttribute('data-groupByTitre', true);
                tmp_tr.setAttribute('data-groupBy', tr_data.groupBy);
                tmp_tr.appendChild(tmp_td);
                this.tbody.appendChild(tmp_tr);
//console.log('please reorder');
                this.reposition();
            }
        }.bind(this))
        this.reposition();
//
    },
    reposition: function () {
        this.tbody.querySelectorAll('[data-groupBy]').forEach(function (node) {
// node.setStyle({position: 'absolute',width:'100%'});
        }.bind(this))
        this.sortedRows = Array.from(this.tbody.querySelectorAll('[data-groupBy]')).sort(function (nodeA, nodeB) {
            return nodeA.childNodes.length - nodeB.childNodes.length;
        }.bind(this))
        var y = 0;
        this.sortedRows.forEach(function (node, i) {
//  this.tbody.appendChild(node) ;
            node.style.order = i;
            node.style.top = y + 'px';
//  node.style.order=i;
            y += node.getBoundingClientRect().height;
        }.bind(this));
        this.tbody.querySelectorAll('[data-groupBy="appscheme"]').forEach(function (node) {
            node.style.order = -1;
        });
        this.tbody.querySelectorAll('[data-groupBy]').forEach(function (node) {
//  node.setStyle({position:'relative',top:''});
        }.bind(this))
    }
}
