<?
	include_once($_SERVER['CONF_INC']);
	$APP          = new App('promo_zone');
	$uniqid       = uniqid();
	$_POST        = fonctionsProduction::cleanPostMongo($_POST, 1);
	$idpromo_zone = $_POST['idpromo_zone'];

	$arr = $APP->query_one(['idpromo_zone' => (int)$idpromo_zone]);
	//titre
	$nomPromo_zone = $arr['nomPromo_zone'];
	//
	$arrSize     = [1 => '750', 2 => '375', 3 => '250'];
	$grilleBlock = empty($arr['grilleBlock']) ? [] : $arr['grilleBlock'];
	vardump($grilleBlock);
?>
<div class="relative padding" style="max-width: 900px;">
	<?
		foreach ($grilleBlock as $key => $value):
			$uid_grille_block   = $value['uid_grille_block'];
			$nomPromo_zone_item = $value['nomPromo_zone_item'];
			$type               = $value['type'];
			?>
			<div class="relative margin padding " style="overflow:hidden;">
				<div class="relative" style="width:780px;" mdl="uidblock" value="<?= $uid_grille_block ?>">
					<div class="applink titre_entete borderb uppercase bold">
						nom : <?= $nomPromo_zone_item ?>
					</div>
					<div class="applink  sstitre_entete alignright">
						<a class="bold" onclick="ajaxMdl('app/app_promo_zone/app_promo_zone_build_module_block_delete','','idpromo_zone=<?= $idpromo_zone ?>&uid_grille_block=<?= $uid_grille_block ?>')"><i
								class="fa fa-times textrouge"></i> Supprimer ce block
						</a>
					</div>
					<table style="width:100%;table-layout:fixed;">
						<tr>
							<?
								foreach ($value['vignette'] as $key2 => $value2) :
									$uid_grille_mdl = $value2['uid_grille_mdl'];
									$size           = sizeof($value['vignette']);
									echo $mdl = skelMdl::cf_module('app/app_promo_zone/app_promo_zone_build_module_block_vignette', ['moduleTag' => 'td', 'scope' => 'uid_grille_mdl', 'idpromo_zone' => $idpromo_zone, 'uid_grille_block' => $uid_grille_block, 'uid_grille_mdl' => $uid_grille_mdl], $uid_grille_mdl, 'draggable="true"');
								endforeach;
							?>
						</tr>
					</table>
				</div>
			</div>
			<?
		endforeach;
	?>
</div>
