<?
	include_once($_SERVER['CONF_INC']);
	$APP   = new App('feed_header');
	$col_F = new App('fournisseur');
	$rsApp = $APP->find(array('estActifFeed_header' => 1))->sort(array('nomFeed_header' => 1));
?>
<div class="flex_v" style="height:100%;overflow:hidden;">
	<div class="titre_entete">
		<progress value="0" id="auto_xml_job"></progress>
	</div>
	<div class="padding retrait applink flex_h flex_align_middle borderb">
		<div><i class="fa fa-refresh fa-2x"></i></div>
		<div>
			<a onclick="runModule('mdl/business/<?= BUSINESS ?>/app/app_xml/xml_parse')"><?= idioma('Lancer tout les éléments') ?> </a>
		</div>
	</div>
	<div>
		<br>
		<br>
	</div>
	<div class="retrait flex_main toggler" style="overflow: auto;">
		<?
			//
			$PROG_MSG = '';
			$zi       = 0;
			while ($arrApp = $rsApp->getNext()):
				$CODE_FOURNISSEUR = $arrApp['codeFeed_header'];
				$idfournisseur    = (int)$arrApp['idfournisseur'];
				$CRUISELINE       = $arrApp['codeFeed_header'];
				$arr_F            = $col_F->findOne(array('idfournisseur' => $idfournisseur));
				$nomFournisseur   = $arr_F['nomFournisseur'];
				?>
				<div class="flex_h flex_align_middle borderb">
					<div class="padding margin    " style="width:150px;">
						<div class="ellipsis"> <?= $nomFournisseur ?> </div>
						<?= $CRUISELINE ?>
					</div>
					<div class="borderl padding applink">
						<a class="autoToggle" onclick="runModule('mdl/business/<?= BUSINESS ?>/app/app_xml/xml_parse','vars[idfournisseur]=<?= $idfournisseur ?>')"><?= idioma('Lancer') ?> </a>
					</div>
					<div class="flex_main" style="max-height:450px;overflow:auto;">
						<progress style="display: none;" id="auto_xml_job_<?= $CRUISELINE ?>"></progress>
					</div>
				</div>
				<?
			endwhile;
		?>
	</div>
</div>