<?
	include_once($_SERVER['CONF_INC']);
	$APP           = new App('newsletter');
	$APP_ITEM      = new App('newsletter_item');
	$APP_BLOCK     = new App('newsletter_block');
	$APP_ITEM_TYPE = new App('newsletter_item_type');

	$uniqid        = uniqid();
	$nomNewsletter = '';
	$idnewsletter  = (int)$_POST['idnewsletter'];
	$rsType        = $APP_ITEM_TYPE->find()->sort(['ordreItem_type']);
	$arr           = $APP->findOne(array('idnewsletter' => (int)$idnewsletter));
	$nomNewsletter = $arr['nomNewsletter'];

	unset($_SESSION['blockid']);
	// $idnewsletter = (!empty($idnewsletter))? $idnewsletter : rand(1,1000).time() ;
	$action = (!empty($idnewsletter)) ? 'updateNewsletters' : 'createNewsletters';
	$titre  = (!empty($idnewsletter)) ? 'Mise a jour' : 'Cr&eacute;ation';
	//
	$arrTag = array('idproduit' => 'mdl_idproduit', 'titre' => 'mdl_titre', 'Atout' => 'mdl_sstitre', 'Description' => 'mdl_description', 'prix' => 'mdl_prix', 'url' => 'mdl_url');
	//
	$formSearch = 'u' . uniqid();
?>
<div class="applink applinkblock flex_v" style="overflow:hidden;height:100%;">
	<div class="flex_main ">
		<div class="applink applinkbig applinkblock" onclick="$(this).next().toggle();" >
			<a  >
				<i class="fa fa-angle-double-down"></i><?= idioma('Ajouter élément(s) ')  ?>
			</a>
		</div>
		<div class="padding border4 " style="display:none">
			<? while ($arrT = $rsType->getNext()) {
				$idnewsletter_item_type = $arrT['idnewsletter_item_type'];
				$valeur = $arrT['quantiteNewsletter_item_type'];
				?>
				<div class="margin flex_h flex_align_middle ededed  ">
					<div class="flex_main flex_h flex_align_middle">
						<? for ($i = 0; $i < $valeur; $i++) { ?>
							<div class="flex_main   aligncenter margin border4" style="line-height:3em;">
								<i class="fa fa-image"></i>
							</div>
						<? } ?>
					</div>
					<div class="">
						<a onclick="ajaxValidation('app_multi_create','mdl/app/','occurence=<?=$valeur?>&table=newsletter_item&vars[idnewsletter_item_type]=<?=$idnewsletter_item_type?>&vars[idnewsletter]=<?=$idnewsletter?>')"><i class="fa fa-plus"></i> </a>
					</div>
				</div>
			<? } ?>
		</div>
	</div>
	<div class="relative" style="min-height:200px;overflow: auto">
		<div class="applinkbig applink applinkbig" onclick="$(this).next().toggle();" >
			<a><i class="fa fa-sort"></i> <?= idioma('Ordonner'); ?></a>
		</div>
		<div  data-table="newsletter_block"  class="  padding margin" style="position:relative;" id="<?= $formSearch ?>" sort_zone_drag="true">
			<? if (!empty($idnewsletter)) {

				$rs_block = $APP_BLOCK->query(array('idnewsletter' => $idnewsletter))->sort(['ordreNewsletter_block' => 1]);
				while ($arr_block = $rs_block->getNext()):
					$idnewsletter_block  = (int)$arr_block['idnewsletter_block'];
					$nomNewsletter_block = $arr_block['nomNewsletter_block'];
					$rs_item = $APP_ITEM->find(['idnewsletter_block' => $idnewsletter_block])->sort(['ordreNewsletter_item'=>1]);
					?>
					<div draggable="true" data-sort_element="true" data-table="newsletter_block" data-table_value="<?= $idnewsletter_block ?>" data-contextual="table=newsletter_block&table_value=<?= $idnewsletter_block ?>" class="flex_h flex_align_middle borderb margin" style="width:100%;position:relative;">
						<div>
							<div class="relative flex_h" style="width: 160px;">
								<? for ($i = 0; $i < $rs_item->count(); $i++) { ?>
									<div class="flex_main fond_noir aligncenter margin boxshadow" style="line-height:3em;">
										 ss
									</div>
								<? } ?>
							</div>
						</div>
						<div class="alignright" style="width:40px;vertical-align:top;">
							<div class="padding">
								<i class="fa fa-chevron-up sortprevious"></i>
								<i class="fa fa-chevron-down sortnext"></i>
							</div>
						</div>
					</div>
					<?
				endwhile;

			} ?>
		</div>
	</div>
</div>