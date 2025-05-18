<?
	include_once($_SERVER['CONF_INC']);
	$APP      = new App('appscheme');
	$arr_tbl  = ['client', 'prospect', 'contact', 'tache', 'affaire', 'financement', 'contrat', 'opportunite', 'intervention', 'materiel'];
	$RSSCHEME = $APP->get_schemes(['codeAppscheme_base' => 'sitebase_base'], '', 370);
	$RSSCHEME = $APP->find()->sort(['nomAppscheme' => 1]); // ['codeAppscheme_base' => 'sitebase_base']

?>
<div class="padding">
	<div class="padding alignright bold"><span class="inline border4 ededed padding"><i class="fa fa-caret-down textgrisfonce"></i> <?= idioma('Accéder à mes listes') ?></span></div>
	<div class="  flex_v " style="height:100%;width: 100%;">
		<div class=" toggler   flex_h flex_wrap ">
			<?
				foreach ($RSSCHEME as $arr_dist):
					//
					$table  = $arr_dist['codeAppscheme'];
					$table_name = $arr_dist['nomAppscheme'];
					// if ($APP->get_settings($_SESSION['idagent'], 'app_menu_create_' . $table) != 'true') continue;
					if (!droit_table($_SESSION['idagent'], 'L', $table)) continue;
					$APP_TMP = new App($table);
					if (!$APP_TMP->has_agent()) continue;
					$RS_TMP = $APP_TMP->find(['idagent' => (int)$_SESSION['idagent']]);
					if ($RS_TMP->count() == 0) continue;
					?>
					<div class="demi   ">
						<div class="appmetro  autoToggle aligncenter" onclick="<?= fonctionsJs::app_liste($table, $table_value, ['vars' => ['idagent' => $_SESSION['idagent']]]); ?>">
							<div class="aligncenter padding inline border4 ededed" style="border-color:<?= $APP_TMP->colorAppscheme ?>"><i class="fa fa-<?= $APP_TMP->iconAppscheme ?> bold"></i></div>
							<div class="aligncenter padding ellipsis">
								<div class="inline padding bordert"><?= ucfirst(idioma($table_name)) ?> (<?= $RS_TMP->count() ?>)</div>
							</div>
						</div>
					</div>
					<?
				endforeach; ?>
		</div>
	</div>
</div>