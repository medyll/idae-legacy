# cf_module inventory

Inventaire des appels `skelMdl::cf_module(...)` trouves dans le code PHP.

- `[-]` = inutile / a supprimer de la cible BP
- `[+]` = a promouvoir explicitement dans `cf_module_bp.md`
- `[x]` = couvert par une famille de rationalisation dans `cf_module_bp.md`
- `[ ]` = non traite / a qualifier
- `non resolu` = le path appele n'a pas ete retrouve tel quel sous `idae\\web\\mdl`, souvent a cause d'un alias legacy ou d'une resolution dynamique

## Modules resolus/litteraux tries par status

### [-] Inutiles

| Status | Module | Vars requises | Role | Notes |
| --- | --- | --- | --- | --- |
| [-] | `app/app_liste_table` | defer, groupBy, nbRows, page, scope, search, sortBy, sortDir, table, table_value, vars, vars_date | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_liste_table.php` |
| [-] | `app/app_liste_tr` | className, moduleTag, scope | Module include / view fragment | non resolu |
| [-] | `app/app_mail/app_mail_attach` | uniqid | Mail UI | non resolu |
| [-] | `app/app_mail/app_mail_boxes` | defer | Mail UI | `idae-legacy\idae\web\mdl\app\app_mail\app_mail_boxes.php` |
| [-] | `app/app_component/app_component_fiche_info` | moduleTag, table, table_value | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_component\app_component_fiche_info.php` |
| [-] | `app/app_component/app_component_info_bar_vert` | moduleTag, table, table_value | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_component\app_component_info_bar_vert.php` |
| [-] | `app/app_component/app_component_table_pin` | table, table_value | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_component\app_component_table_pin.php` |
| [-] | `app/app_conge/app_conge_reload` | date, defer, heureDebutConge, maxJours, sd, tableparent | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_conge\app_conge_reload.php` |
| [-] | `app/app_custom/client/client_espace_left` | idclient, table | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_custom\client\client_espace_left.php` |
| [-] | `app/app_custom/client/client_espace_right` | idclient | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_custom\client\client_espace_right.php` |
| [-] | `app/app_custom/mail/mail_compose_attach` | mail_tmp, scope | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_custom\mail\mail_compose_attach.php` |
| [-] | `app/app_custom/mail/mail_compose_contact` | mail_tmp | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_custom\mail\mail_compose_contact.php` |
| [-] | `app/app_custom/mail/mail_compose_contact_cc` | mail_tmp | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_custom\mail\mail_compose_contact_cc.php` |
| [-] | `app/app_custom/produit/produit_main` | idproduit | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_custom\produit\produit_main.php` |
| [-] | `app/app_custom/tache/tache_periode` | - | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_custom\tache\tache_periode.php` |
| [-] | `app/app_document/app_document_nav` | base, collection, defer, document, scope, tag, vars | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_document\app_document_nav.php` |
| [-] | `app/app_document/app_document_tag_liste` | base, collection, vars | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_document\app_document_tag_liste.php` |
| [-] | `app/app_document/document_liste` | base, collection, vars | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_document\document_liste.php` |
| [-] | `app_document/app_document_liste_drop` | document, moduleTag, scope | Module include / view fragment | non resolu |
| [-] | `app_document/app_document_liste_tbody` | className, document, emptyModule, moduleTag, scope | Module include / view fragment | non resolu |
| [-] | `app_document/app_document_preview` | - | Module include / view fragment | non resolu |
| [-] | `app_document/document_spy` | document | Module include / view fragment | non resolu |
| [-] | `app/app_prod/app_prod_liste_menu` | groupBy, sortBy, sortDir, table, vars | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_prod\app_prod_liste_menu.php` |
| [-] | `app/app_prod/app_prod_liste_menu_export` | - | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_prod\app_prod_liste_menu_export.php` |
| [-] | `app/app_prod/app_prod_liste_menu_search` | MODULE, table, uniqid, vars | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_prod\app_prod_liste_menu_search.php` |
| [-] | `app/app_prod/app_prod_nav_arbo` | mainscope_app, table, vars | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_prod\app_prod_nav_arbo.php` |
| [-] | `app/app_prod/app_prod_nav_date` | groupBy, scope, table, vars | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_prod\app_prod_nav_date.php` |
| [-] | `app/app_prod/app_prod_nav_fk` | sort_fk, table, vars | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_prod\app_prod_nav_fk.php` |
| [-] | `app/app_prod/app_prod_nav_group` | scope, table, vars | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_prod\app_prod_nav_group.php` |

### [+] A promouvoir dans le blueprint BP

| Status | Module | Vars requises | Role | Notes |
| --- | --- | --- | --- | --- |
| [+] | `app/app_field_update` | field_name, field_name_raw, field_value, table, table_value, vars | Editable field renderer | `idae-legacy\idae\web\mdl\app\app_field_update.php` |
| [+] | `app/app_liste/app_liste_pager` | groupBy, sortBy, sortDir, table, vars | List/explorer rendering | `idae-legacy\idae\web\mdl\app\app_liste\app_liste_pager.php` |
| [+] | `app/app_gui/app_gui_menu` | scope, table | UI tile / GUI fragment | `idae-legacy\idae\web\mdl\app\app_gui\app_gui_menu.php` |
| [+] | `app/app_gui/app_gui_panel_list` | scope, table, vars | UI tile / GUI fragment | `idae-legacy\idae\web\mdl\app\app_gui\app_gui_panel_list.php` |
| [+] | `app/app_gui/app_gui_start_menu` | code, scope, table | UI tile / GUI fragment | `idae-legacy\idae\web\mdl\app\app_gui\app_gui_start_menu.php` |
| [+] | `app/app_gui/app_gui_tile_click` | moduleTag, table, table_value | UI tile / GUI fragment | `idae-legacy\idae\web\mdl\app\app_gui\app_gui_tile_click.php` |
| [+] | `app/app_gui/app_gui_tile_table_click` | table, table_value | UI tile / GUI fragment | `idae-legacy\idae\web\mdl\app\app_gui\app_gui_tile_table_click.php` |
| [+] | `app/app_gui/app_gui_tile_user` | code, css, moduleTag, text | UI tile / GUI fragment | `idae-legacy\idae\web\mdl\app\app_gui\app_gui_tile_user.php` |
| [+] | `app/app_gui/app_gui_today` | - | UI tile / GUI fragment | `idae-legacy\idae\web\mdl\app\app_gui\app_gui_today.php` |
| [+] | `app/app_gui/app_gui_today_create` | scope, table | UI tile / GUI fragment | `idae-legacy\idae\web\mdl\app\app_gui\app_gui_today_create.php` |
| [+] | `app/app_gui/app_gui_today_link` | scope, table, table_value | UI tile / GUI fragment | `idae-legacy\idae\web\mdl\app\app_gui\app_gui_today_link.php` |

### [x] Couverts par le blueprint BP

| Status | Module | Vars requises | Role | Notes |
| --- | --- | --- | --- | --- |
| [x] | `app/app_calendrier/app_calendrier` | calendar_target, calendarId, date, date_field, sd, table | Calendar navigation or day view | `idae-legacy\idae\web\mdl\app\app_calendrier\app_calendrier.php` |
| [x] | `app/app_calendrier/calendrier_day` | calendarId, date_field, sd, table, vars | Calendar navigation or day view | `idae-legacy\idae\web\mdl\app\app_calendrier\calendrier_day.php` |
| [x] | `app/app_calendrier/calendrier_nav` | calendarId, sd | Calendar navigation or day view | `idae-legacy\idae\web\mdl\app\app_calendrier\calendrier_nav.php` |
| [x] | `app/app_contextual/scheme_contextual` | table | Contextual actions/menu | `idae-legacy\idae\web\mdl\app\app_contextual\scheme_contextual.php` |
| [x] | `app/app_liste/app_liste` | datadsp, groupBy, hide_menu, mdl, module, nbRows, show_search, table, table_value, vars, zone | List/explorer rendering | `idae-legacy\idae\web\mdl\app\app_liste\app_liste.php` |
| [x] | `app/app_liste/app_liste_home` | groupBy, mainscope, page, rppage, search, vars | List/explorer rendering | `idae-legacy\idae\web\mdl\app\app_liste\app_liste_home.php` |
| [x] | `app/app_liste/app_liste_menu` | groupBy, sortBy, sortDir, table, vars | List/explorer rendering | `idae-legacy\idae\web\mdl\app\app_liste\app_liste_menu.php` |
| [x] | `app/app_liste/app_liste_menu_small` | groupBy, sortBy, sortDir, table, table_value, vars | List/explorer rendering | `idae-legacy\idae\web\mdl\app\app_liste\app_liste_menu_small.php` |
| [x] | `app/app_liste/app_liste_mini` | groupBy, nbRows, table, table_value, vars, zone | List/explorer rendering | `idae-legacy\idae\web\mdl\app\app_liste\app_liste_mini.php` |
| [x] | `app/app_liste/app_liste_small` | data_model, data-dsp, data-dsp-className, data-dsp-mdl, groupBy, idclient, mode, nbRows, table, table_value, vars, zone | List/explorer rendering | `idae-legacy\idae\web\mdl\app\app_liste\app_liste_small.php` |
| [x] | `app/app_mail/app_mail_compose_attach` | mail_tmp, scope | Mail UI | non resolu |
| [x] | `app/app_mail/app_mail_compose_contact` | mail_tmp | Mail UI | non resolu |
| [x] | `app/app_mail/app_mail_compose_contact_cc` | mail_tmp | Mail UI | non resolu |
| [x] | `app/app_menu` | table, table_value | Module include / view fragment | non resolu |
| [x] | `app/app_search/search_item_check` | input_name, item, table, table_main, vars | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_search\search_item_check.php` |
| [x] | `app/app_search/search_item_date` | item, vars | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_search\search_item_date.php` |
| [x] | `app/app_search/search_item_select` | input_name, search_type, table, table_from, table_main, vars | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_search\search_item_select.php` |
| [x] | `app/app_search/search_item_text` | input_name, table, table_from, table_main, vars | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_search\search_item_text.php` |
| [x] | `app/app/app_fiche_analogue` | groupBy, moduleTag, table, table_value, vars | Record sheet / preview fragment | `idae-legacy\idae\web\mdl\app\app\app_fiche_analogue.php` |
| [x] | `app/app/app_fiche_entete` | act_from, idclient, table, table_value, vars | Record sheet / preview fragment | `idae-legacy\idae\web\mdl\app\app\app_fiche_entete.php` |
| [x] | `app/app/app_fiche_entete_arbo` | act_from, table, table_value | Record sheet / preview fragment | `idae-legacy\idae\web\mdl\app\app\app_fiche_entete_arbo.php` |
| [x] | `app/app/app_fiche_entete_group` | groupBy, table, table_value, vars | Record sheet / preview fragment | `idae-legacy\idae\web\mdl\app\app\app_fiche_entete_group.php` |
| [x] | `app/app/app_fiche_fk` | act_chrome_gui, mode, table, table_value | Record sheet / preview fragment | `idae-legacy\idae\web\mdl\app\app\app_fiche_fk.php` |
| [x] | `app/app/app_fiche_history` | groupBy, mode, table, table_value, vars | Record sheet / preview fragment | `idae-legacy\idae\web\mdl\app\app\app_fiche_history.php` |
| [x] | `app/app/app_fiche_maxi_entete` | act_from, groupBy, table, table_value, vars | Record sheet / preview fragment | `idae-legacy\idae\web\mdl\app\app\app_fiche_maxi_entete.php` |
| [x] | `app/app/app_fiche_micro` | table, table_value | Record sheet / preview fragment | `idae-legacy\idae\web\mdl\app\app\app_fiche_micro.php` |
| [x] | `app/app/app_fiche_mini` | scope, table, table_value | Record sheet / preview fragment | `idae-legacy\idae\web\mdl\app\app\app_fiche_mini.php` |
| [x] | `app/app/app_fiche_preview` | scope, table, table_value, vars | Record sheet / preview fragment | `idae-legacy\idae\web\mdl\app\app\app_fiche_preview.php` |
| [x] | `app/app/app_fiche_rfk` | act_chrome_gui, idclient, mode, moduleTag, table, table_value, vars | Record sheet / preview fragment | `idae-legacy\idae\web\mdl\app\app\app_fiche_rfk.php` |
| [x] | `app/app/app_fiche_rfk_liste` | act_chrome_gui, nbRows, table, table_value | Record sheet / preview fragment | `idae-legacy\idae\web\mdl\app\app\app_fiche_rfk_liste.php` |
| [x] | `app/app/app_fiche_search` | groupBy, table, table_value, vars | Record sheet / preview fragment | `idae-legacy\idae\web\mdl\app\app\app_fiche_search.php` |
| [x] | `app/app/app_fiche_thumb` | table, table_value, vars | Record sheet / preview fragment | `idae-legacy\idae\web\mdl\app\app\app_fiche_thumb.php` |
| [x] | `app/app/app_menu` | act_from, table, table_value | Record action menu | `idae-legacy\idae\web\mdl\app\app\app_menu.php` |
| [x] | `app/app/app_menu_custom` | act_from, moduleTag, table, table_value | Record action menu | `idae-legacy\idae\web\mdl\app\app\app_menu_custom.php` |
| [x] | `gui/gui_tile_click` | idfournisseur, moduleTag | Module include / view fragment | non resolu |
| [x] | `liveidle/spy` | emptyModule, ONLINE_KEY | Module include / view fragment | non resolu |
| [x] | `liveidle/writer` | ONLINE_KEY | Module include / view fragment | non resolu |

### [ ] Non traites / a qualifier

| Status | Module | Vars requises | Role | Notes |
| --- | --- | --- | --- | --- |
| [ ] | `app/app_field_add` | add_field, display_mode, field, idagent, idclient, module_value, run, vars | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_field_add.php` |
| [ ] | `app/app_field_fk_update` | field_name, field_name_raw, table, table_value, vars | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_field_fk_update.php` |
| [ ] | `app/app_gui/app_gui_menu` | scope, table | UI tile / GUI fragment | `idae-legacy\idae\web\mdl\app\app_gui\app_gui_menu.php` |
| [ ] | `app/app_gui/app_gui_panel_list` | scope, table, vars | UI tile / GUI fragment | `idae-legacy\idae\web\mdl\app\app_gui\app_gui_panel_list.php` |
| [ ] | `app/app_gui/app_gui_start_menu` | code, scope, table | UI tile / GUI fragment | `idae-legacy\idae\web\mdl\app\app_gui\app_gui_start_menu.php` |
| [ ] | `app/app_gui/app_gui_tile_user` | code, css, moduleTag, text | UI tile / GUI fragment | `idae-legacy\idae\web\mdl\app\app_gui\app_gui_tile_user.php` |
| [ ] | `app/app_img/image_dyn` | base, codeImage, codeTailleImage, collection, csssource, mongoImg, noEdit, reflect, show, show_info, table, table_value | Image/media rendering | `idae-legacy\idae\web\mdl\app\app_img\image_dyn.php` |
| [ ] | `app/app_liste_search_all` | - | Module include / view fragment | non resolu |
| [ ] | `app/app_login/app_login_success` | - | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_login\app_login_success.php` |
| [ ] | `app/app_mail/app_mail_liste_tr` | className, moduleTag, uniqid | Mail UI | non resolu |
| [ ] | `app/app_mail/app_mail_preview` | defer, uniqid | Mail UI | non resolu |
| [ ] | `app/app_newsletter/app_newsletter_build_mini_liste` | idnewsletter | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_newsletter\app_newsletter_build_mini_liste.php` |
| [ ] | `app/app_planning/app_planning_quoti` | date, defer, sd, valueModule | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_planning\app_planning_quoti.php` |
| [ ] | `app/app_planning/app_planning_tache_reload` | date, defer, heureDebutTache, maxJours, sd, tableparent | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_planning\app_planning_tache_reload.php` |
| [ ] | `app/app_promo_zone/app_promo_zone_build_module_block_vignette` | idpromo_zone, key_mdl, key_tag, moduleTag, scope, uid_grille_block, uid_grille_mdl | Promo zone builder/editor | `idae-legacy\idae\web\mdl\app\app_promo_zone\app_promo_zone_build_module_block_vignette.php` |
| [ ] | `app/app_scheme/app_scheme_menu_icon` | table | Schema/table metadata UI | `idae-legacy\idae\web\mdl\app\app_scheme\app_scheme_menu_icon.php` |
| [ ] | `app/app_search/search_item` | input_name, item, search_type, table, table_from, table_main, vars | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_search\search_item.php` |
| [ ] | `app/app_stat/app_stat_periode` | groupBy, scope, table, vars | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_stat\app_stat_periode.php` |
| [ ] | `app/app_stat/statistique_periode` | - | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_stat\statistique_periode.php` |
| [ ] | `app/app_user_pref/app_user_pref_css` | - | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_user_pref\app_user_pref_css.php` |
| [ ] | `app/app_user_pref/app_wallpaper` | - | Module include / view fragment | `idae-legacy\idae\web\mdl\app\app_user_pref\app_wallpaper.php` |
| [ ] | `app/app/app_explorer_entete_rfk` | act_chrome_gui, defer, nbRows, table, table_value, vars | Explorer/search fragment | `idae-legacy\idae\web\mdl\app\app\app_explorer_entete_rfk.php` |
| [ ] | `app/app/app_explorer_search` | table, table_value, vars | Explorer/search fragment | `idae-legacy\idae\web\mdl\app\app\app_explorer_search.php` |
| [ ] | `business/cruise/app/devis/devis_preview_inner` | iddevis, md5Devis, scope | Module include / view fragment | `idae-legacy\idae\web\mdl\business\cruise\app\devis\devis_preview_inner.php` |
| [ ] | `devis_paiement/devis_paiement_liste` | moduleTag, numeroDossierDevis, scope, vars | Module include / view fragment | non resolu |
| [ ] | `devis/devis_prestataire/devis_prestataire_inner` | iddevis | Module include / view fragment | non resolu |
| [ ] | `document/document_nav` | document, scope | Module include / view fragment | non resolu |

## Paths dynamiques / non directement resolus

| Status | Expression module | Vars requises | Role | Notes |
| --- | --- | --- | --- | --- |
| [-] | `'app/app_contextual/' . $table` | table, table_value | Contextual actions/menu | callers: `idae-legacy\idae\web\mdl\app\app_contextual\app_contextual.php:53` |
| [-] | `'app/app_custom/contextuel/' . $table` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\app\app_contextual.php:23` |
| [-] | `'app/app_search/search_item_'.$_POST['search_type']` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\app\app_search\search_item.php:39` |
| [-] | `'business/' . BUSINESS . '/app/app_contextual/' . $table` | table, table_value | Contextual actions/menu | callers: `idae-legacy\idae\web\mdl\app\app_contextual\app_contextual.php:52` |
| [-] | `'/app/app_custom/' . $table . '/' . $table . '_create_fragment'` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\app\app\app_create.php:49` |
| [-] | `'/app/app_custom/' . $table . '/' . $table . '_create'` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\app\app\app_create.php:14` |
| [-] | `'/app/app_custom/' . $table . '/' . $table . '_espace'` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\app\app\app_fiche_maxi_liste_rfk.php:15`, `idae-legacy\idae\web\mdl\app\app\app_fiche_maxi_old.php:15`, `idae-legacy\idae\web\mdl\app\app\app_fiche_maxi.php:11` |
| [-] | `'/app/app_custom/' . $table . '/' . $table . '_fiche_icone'` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\app\app\app_fiche_icone.php:9` |
| [-] | `'/app/app_custom/' . $table . '/' . $table . '_fiche_micro'` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\app\app\app_fiche_micro.php:9`, `idae-legacy\idae\web\mdl\app\app\app_fiche_search.php:13`, `idae-legacy\idae\web\mdl\app\app\app_fiche_thumb_full.php:13`, `idae-legacy\idae\web\mdl\app\app\app_fiche_thumb.php:11` |
| [-] | `'/app/app_custom/' . $table . '/' . $table . '_fiche_mini'` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\app\app\app_fiche_mini.php:13` |
| [-] | `'/app/app_custom/' . $table . '/' . $table . '_fiche'` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\app\app\app_fiche_document.php:20`, `idae-legacy\idae\web\mdl\app\app\app_fiche.php:20` |
| [-] | `'/app/app_custom/' . $table . '/' . $table . '_preview'` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\app\app\app_fiche_preview.php:7` |
| [-] | `'/app/app_custom/' . $table . '/' . $table . '_update_fragment'` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\app\app\app_duplique.php:62`, `idae-legacy\idae\web\mdl\app\app\app_update.php:67` |
| [-] | `'/app/app_custom/' . $table . '/' . $table . '_update'` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\app\app\app_duplique.php:12`, `idae-legacy\idae\web\mdl\app\app\app_update_save.php:10`, `idae-legacy\idae\web\mdl\app\app\app_update.php:22` |
| [-] | `'/app/app_custom/'.$table.'/'.$table.'_update'` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\app\app\app_create_grille.php:12` |
| [-] | `'/business/' . BUSINESS . '/app/' . $table . '/' . $table . '_create'` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\app\app\app_create.php:9` |
| [-] | `'/business/' . BUSINESS . '/app/' . $table . '/' . $table . '_fiche'` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\app\app\app_fiche_document.php:15`, `idae-legacy\idae\web\mdl\app\app\app_fiche.php:15` |
| [-] | `'/business/' . BUSINESS . '/app/' . $table . '/' . $table . '_update'` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\app\app\app_update.php:17` |
| [-] | `'/customer/' . BUSINESS . '/app/' . $table . '/' . $table . '_fiche'` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\app\app\app_fiche_document.php:10`, `idae-legacy\idae\web\mdl\app\app\app_fiche.php:10` |
| [-] | `'/customer/' . BUSINESS . '/app/' . $table . '/' . $table . '_update'` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\app\app\app_update.php:12` |
| [-] | `'app/app_stat/'.$mdl.'_liste'` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\app\app_stat\stat_dispatch.php:11` |
| [-] | `'app/app_stat/'.$mdl.'_stat'` | emptyModule | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\app\app_stat\stat_dispatch.php:18` |
| [-] | `'business/' . BUSINESS . '/app/app_admin/app_admin'` | - | Admin module | callers: `idae-legacy\idae\web\mdl\app\app_admin\app_admin.php:29` |
| [-] | `'customer/' . CUSTOMERNAME . '/app_admin/app_admin'` | - | Admin module | callers: `idae-legacy\idae\web\mdl\app\app_admin\app_admin.php:30` |
| [-] | `'customer/'.CUSTOMERNAME.'/app/app_update'` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\business\cruise\app\devis\devis_service_update.php:19` |
| [-] | `'xml/read'.$four` | moduleTag | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\business\cruise\app\app_xml_csv\xml_thread.php:66` |
| [-] | `'xml/read'.$four.'_iti'` | moduleTag | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\business\cruise\app\app_xml_csv\xml_thread.php:71` |
| [-] | `'xml/xml'.$four` | moduleTag | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\business\cruise\app\app_xml_csv\xml_thread.php:78` |
| [-] | `$_POST['mdl']` | - | Module include / view fragment | callers: `idae-legacy\idae\web\proxy.php:9` |
| [-] | `$file` | - | Module include / view fragment | callers: `idae-legacy\idae\web\server\app_server.php:63`, `idae-legacy\idae\web\server\app_server.php:66` |
| [-] | `$MDL` | table, table_value | Module include / view fragment | callers: `idae-legacy\idae\web\services\json_data_table.php:73`, `idae-legacy\idae\web\services\json_data_table.php:735`, `idae-legacy\idae\web\services\service_proxy.php:6` |
| [-] | `$path_to_devis . 'devis_create_cabine'` | idproduit, idproduit_tarif | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\business\cruise\app\devis\devis_create_make.php:102`, `idae-legacy\idae\web\mdl\business\cruise\app\devis\devis_make_update.php:82` |
| [-] | `$path_to_devis . 'devis_create_date'` | idproduit | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\business\cruise\app\devis\devis_create_make.php:92`, `idae-legacy\idae\web\mdl\business\cruise\app\devis\devis_make_update.php:73` |
| [-] | `$path_to_devis . 'devis_make_nav'` | iddevis, scope | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\business\cruise\app\devis\devis_make.php:98` |
| [-] | `$path_to_devis.'devis_create_wizard'` | uniqid | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\business\cruise\app\devis\devis_create.php:43` |
| [-] | `$PATH.'ftp/'.$four` | moduleTag | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\business\cruise\app\app_xml_csv\xml_thread.php:60` |
| [-] | `$PATH.'read/read'.$four` | moduleTag | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\business\cruise\app\app_xml_csv\xml_thread.php:61` |
| [-] | `$PATH.'xml_bar_info'` | emptyModule | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\business\cruise\app\app_xml_csv\xml_launch.php:20` |
| [-] | `$PATH.'xml_bar'` | emptyModule | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\business\cruise\app\app_xml_csv\xml_launch.php:19` |
| [-] | `mdl_link('app/appsite/appsite_scheme_values')` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\business\cruise\app\appsite\appsite.php:35` |
| [-] | `mdl_link('app/appsite/appsite_scheme')` | - | Module include / view fragment | callers: `idae-legacy\idae\web\mdl\business\cruise\app\appsite\appsite.php:32` |
