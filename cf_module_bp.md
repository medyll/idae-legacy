# cf_module best practices

Objectif: passer d'un inventaire "1 path = 1 module" a une vue plus rationnelle basee sur des familles de modules et des parametres de variation.

## Directive de rationalisation

Quand plusieurs modules ne different que par:
- une densite (`small`, `mini`, `micro`),
- une variante d'affichage (`preview`, `thumb`, `group`, `arbo`),
- une sous-zone (`nav`, `day`, `search`, `export`),
- ou un type d'input (`text`, `select`, `date`, `check`),

alors la cible BP est **un seul module parametrable** plutot qu'un fichier par variante.

## Familles traitees

| Famille cible BP | Modules legacy couverts | Parametre de variation propose | Vars communes / pivots | Role cible |
| --- | --- | --- | --- | --- |
| `app/app_liste/app_liste` | `app/app_liste/app_liste`, `app/app_liste/app_liste_small`, `app/app_liste/app_liste_mini`, `app/app_liste/app_liste_home` | `layout=full|small|mini|home` | `table`, `table_value`, `groupBy`, `vars`, `zone`, `nbRows` | Shell unique de liste/explorer |
| `app/app_liste/app_liste_menu` | `app/app_liste/app_liste_menu`, `app/app_liste/app_liste_menu_small` | `density=default|small` | `table`, `table_value`, `groupBy`, `sortBy`, `sortDir`, `vars` | Menu unique de liste |
| `app/app/app_fiche_header` | `app/app/app_fiche_entete`, `app/app/app_fiche_entete_arbo`, `app/app/app_fiche_entete_group`, `app/app/app_fiche_maxi_entete` | `headerMode=default|arbo|group|maxi` | `table`, `table_value`, `act_from`, `groupBy`, `vars` | Entete unique de fiche |
| `app/app/app_fiche_view` | `app/app/app_fiche_micro`, `app/app/app_fiche_mini`, `app/app/app_fiche_preview`, `app/app/app_fiche_thumb`, `app/app/app_fiche_search` | `view=micro|mini|preview|thumb|search` | `table`, `table_value`, `scope`, `groupBy`, `vars` | Vue compacte/derivee de fiche |
| `app/app/app_fiche_relation` | `app/app/app_fiche_fk`, `app/app/app_fiche_rfk`, `app/app/app_fiche_rfk_liste`, `app/app/app_fiche_history`, `app/app/app_fiche_analogue` | `block=fk|rfk|rfk_list|history|analogue` | `table`, `table_value`, `mode`, `groupBy`, `act_chrome_gui`, `vars` | Bloc relationnel/annexe de fiche |
| `app/app/app_menu` | `app/app/app_menu`, `app/app/app_menu_custom`, `app/app_menu` | `render=default|custom|alias` | `table`, `table_value`, `act_from`, `moduleTag` | Menu d'actions record unique |
| `app/app_gui/app_gui_tile` | `app/app_gui/app_gui_tile_click`, `app/app_gui/app_gui_tile_table_click`, `gui/gui_tile_click` | `target=record|table|custom` | `table`, `table_value`, `moduleTag` | Tuile d'action unique |
| `app/app_gui/app_gui_today` | `app/app_gui/app_gui_today`, `app/app_gui/app_gui_today_create`, `app/app_gui/app_gui_today_link` | `action=show|create|link` | `table`, `table_value`, `scope` | Widget "today" unique |
| `app/app_search/search_item` | `app/app_search/search_item_check`, `app/app_search/search_item_date`, `app/app_search/search_item_select`, `app/app_search/search_item_text`, `'app/app_search/search_item_'.$_POST['search_type']` | `inputType=check|date|select|text` | `input_name`, `item`, `table`, `table_from`, `table_main`, `vars`, `search_type` | Input de recherche parametrable |
| `app/app_calendrier/app_calendrier` | `app/app_calendrier/app_calendrier`, `app/app_calendrier/calendrier_nav`, `app/app_calendrier/calendrier_day` | `section=full|nav|day` | `sd`, `calendarId`, `table`, `date`, `date_field` | Calendrier unique en sous-vues |
| `app/app_contextual/app_contextual` | `app/app_contextual/scheme_contextual`, `'app/app_contextual/' . $table`, `'business/' . BUSINESS . '/app/app_contextual/' . $table`, `'app/app_custom/contextuel/' . $table` | `contextSource=scheme|table|business|custom` | `table`, `table_value` | Point d'entree contextuel unique |
| `liveidle/liveidle` | `liveidle/writer`, `liveidle/spy` | `mode=writer|spy` | `ONLINE_KEY`, `emptyModule` | Presence/realtime unique |

## Modules cibles explicites [+]

| Statut | Module cible BP | Source legacy | Parametre / intention | Vars pivots | Note |
| --- | --- | --- | --- | --- | --- |
| [+] | `app/app_field_update` | `app/app_field_update` | Conserver comme module pivot d'edition de champ | `field_name`, `field_value`, `table`, `table_value`, `vars` | Pas a fusionner: a formaliser comme brique BP stable |
| [+] | `app/app_liste/app_liste_pager` | `app/app_liste/app_liste_pager` | Extraire un pager standard reutilisable | `table`, `groupBy`, `sortBy`, `sortDir`, `vars` | Brique transverse pour toutes les listes |
| [+] | `app/app_gui/*` | `app/app_gui/app_gui_menu`, `app/app_gui/app_gui_panel_list`, `app/app_gui/app_gui_start_menu`, `app/app_gui/app_gui_tile_click`, `app/app_gui/app_gui_tile_table_click`, `app/app_gui/app_gui_tile_user`, `app/app_gui/app_gui_today`, `app/app_gui/app_gui_today_create`, `app/app_gui/app_gui_today_link` | Formaliser une librairie BP GUI avec API coherente | `table`, `table_value`, `scope`, `code`, `moduleTag`, `vars` | Briques transverses a stabiliser avant fusion plus large |

## Notes de migration

| Regle | Application |
| --- | --- |
| Garder le path historique comme facade | Le path legacy peut rester appele mais deleguer vers un module BP avec un parametre fixe. |
| Eviter les doublons `small` / `mini` / `micro` | Ces suffixes deviennent des valeurs de parametre, pas des fichiers. |
| Conserver les vars historiques | Le module BP accepte la superposition legacy, puis normalise vers une API interne unique. |
| Migrer par facade | On peut migrer sans casser l'existant: chaque ancien module devient un wrapper mince vers le module cible. |

## Hors cible BP [-]

- `app_document/*`
- `app/app_document/*`
- `app/app_component/*`
- `app/app_conge/*`
- `app/app_custom/*`
- `app/app_prod/*`

## Portee de ce fichier

`cf_module_bp.md` ne remplace pas `cf_module.md`.

- `cf_module.md` = inventaire brut et suivi de traitement
- `cf_module_bp.md` = cible de rationalisation / blueprint de migration
