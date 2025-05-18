<?
	include_once($_SERVER['CONF_INC']);

?>
<div class = "blanc">
	<?
		$table = str_replace('id' , '' , $_POST['table']);
		$APP = new App($table);
		$TABLE_ONE = $APP->app_table_one;
		$table_value = (int)$_POST['table_value'];
		$act_from = empty($_POST['act_from']) ? '' : $_POST['act_from'];
		$id = 'id' . $table;
		$top = 'estTop' . ucfirst($table);
		$actif = 'estActif' . ucfirst($table);
		$nom = 'nom' . ucfirst($table);
		$arr = $APP->query_one(array( $id => $table_value ));
		//
		$R_FK = $APP->get_reverse_grille_fk($table , $table_value);

	?>
</div>
<div class = "barre_entete applink applinkblock toggler borderb ededed">
	<?= skelMdl::cf_module('app/app_custom/contextuel/' . $table , array( $id => $table_value )) ?>
	<? if ( $act_from != 'fiche' ): ?>
		<a act_chrome_gui = "app/app_fiche" vars = "table=<?= $table ?>&table_value=<?= $table_value ?>" options = "{ident:'fiche_<?= $table . $table_value ?>'}">
			<i class = "fa fa-file-text-o"></i>
			&nbsp;
			<?= idioma('Fiche') . ' ' . coupeChaine($arr[$nom] , 15) ?>
		</a>
	<? endif; ?>
	<? if ( $act_from != 'preview' ): ?>
		<a act_chrome_gui = "app/app_liste_preview_gui" vars = "table=<?= $table ?>&table_value=<?= $table_value ?>" options = "{ident:'prev_<?= $table . $table_value ?>'}">
			<i class = "fa fa-eye"></i>
			&nbsp;
			<?= idioma('Détails') . ' ' . coupeChaine($arr[$nom] , 15) ?>
		</a>
	<? endif; ?>
	<? if ( $act_from != 'update' ): ?>
		<a act_chrome_gui = "app/app/app_update" vars = "table=<?= $table ?>&table_value=<?= $table_value ?>" options = "{ident:'update_<?= $table . $table_value ?>'}">
			<i class = "fa fa-pencil"></i>
			&nbsp;
			<?= idioma('Modifier') ?>
		</a>
	<? endif; ?>
	<? if($TABLE_ONE['hasImageScheme']): ?>
	<a act_chrome_gui = "app/app_img/image_app_liste_img" vars = "table=<?= $table ?>&table_value=<?= $table_value ?>" options = "{ident:'img_<?= $table . $table_value ?>'}">
		<i class = "fa fa-file-image-o"></i>
		&nbsp;
		<?= idioma('Images') ?>
	</a>
	<? endif; ?>
	<!--
	<a act_chrome_gui = "app/app_meta_update" vars = "table=<?/*= $table */?>&table_value=<?/*= $table_value */?>" options = "{ident:'meta_update_<?/*= $table . $table_value */?>'}">
		<i class = "fa fa-code"></i>
		&nbsp;
		<?/*= idioma('web méta') */?>
	</a><a act_chrome_gui = "production/produitliste/produit_liste_gui" vars = "vars[<?/*= $id */?>]=<?/*= $table_value */?>" options = "{ident:'prod_<?/*= $table . $table_value */?>'}">
		<i class = "fa fa-cube"></i>
		&nbsp;
		<?/*= idioma('Textes référencement') */?>
	</a>
	<a act_chrome_gui = "production/produitliste/produit_liste_gui" vars = "vars[<?/*= $id */?>]=<?/*= $table_value */?>" options = "{ident:'prod_<?/*= $table . $table_value */?>'}">
		<i class = "fa fa-cubes"></i>
		&nbsp;
		<?/*= idioma('Production') */?>
	</a>-->
	<? foreach ($R_FK as $arr_fk):
		$nAPP                    = new APP($arr_fk['table']);
		$value_rfk               = $arr_fk['table_value'];
		$table_rfk               = $arr_fk['table'];
		$vars_rfk['vars']        = [ 'id' . $table => $table_value ];
		$vars_rfk['table']       = $table_rfk;
		$vars_rfk['table_value'] = $value_rfk;
		$count                   = $arr_fk['count'];
		if ( ! empty($count) )  :
			?>
			<a act_chrome_gui = "app/app_liste_gui" vars = "<?= http_build_query($vars_rfk); ?>">
				<i class = "fa fa-<?= $APP->iconAppscheme ?>"></i> <?=
					$count . ' ' . $table_rfk . '' . (($count == 0) ? '' : 's') . ' ' . idioma('pour') . ' ' . $arr[$nom]?></a>
		<? endif; endforeach; ?>
	<hr>
	<a onclick = "<?= fonctionsJs::tache_create(array( $id => $table_value )) ?>">
		<i class = "fa fa-calendar-o"></i>
		&nbsp;
		<?= idioma('Creer une tache') ?>
	</a>
	<a onclick = "<?= fonctionsJs::note_create(array( $id => $table_value )) ?>"> <img src = "<?= ICONPATH ?>note16.png"/>&nbsp;
		<?= idioma('Creer une agent_note') ?>
	</a>
	<?= skelMdl::cf_module('app/app_gui/app_gui_tile_click' , $_POST + array( 'moduleTag' => 'span' ) , $table_value); ?>
</div>