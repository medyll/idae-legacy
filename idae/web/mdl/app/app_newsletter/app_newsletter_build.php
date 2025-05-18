<?
	include_once($_SERVER['CONF_INC']);
	$APP = new App('newsletter');
	$APP->init_scheme('sitebase_newsletter', 'newsletter_item');
	$APP->init_scheme('sitebase_newsletter', 'newsletter_block');
	$APP->init_scheme('sitebase_newsletter', 'newsletter_item_type');
	ini_set('display_errors', 55);
	$uniqid       = uniqid();
	$table = 'newsletter';
	$Table = ucfirst($table);
	$table_value = $idnewsletter = (int)$_POST['idnewsletter'];
	$arr          = $APP->findOne(array('idnewsletter' => (int)$table_value));
?>
<div class="blanc relative" style="overflow:hidden;width:100%;height:100%;" app_gui_explorer>
	<div class="flex_v" style="height:100%;overflow:hidden;">
		<div class="relative" syle="z-index:10">
			<div class="titre_entete ededed uppercase bold applink">
				<a expl_html_title
				   onclick="reloadModule('app/app_newsletter/app_newsletter_build','*')">
					<i class="fa fa-refresh"></i>
					Newsletter
					"<?= $arr['nomNewsletter'] ?>"
				</a>
			</div>
		</div>
		<div class="flex_main" style="position:relative;overflow:hidden;">
			<div class="flex_h" style="height:100%;overflow: hidden;">
				<div class="frmCol1 flex_v">
					<div class="applink applinkblock flex_h toggler borderb aligncenter">
						<div class="borderr flex_main">
							<a class="autoToggle active" act_target="ffds56fds"><i class="fa fa-home"></i>

								<br>
								Home
							</a>
						</div>
						<div class="borderr flex_main">
							<a class="autoToggle" act_target="gdgfdsgfsd">
								<i class="fa fa-wrench"></i><br><?= idioma('Composer') ?></a>
						</div>
						<div class="flex_main">
							<a class="autoToggle" act_target="whatyd"><i class="fa fa-cloud-upload"></i>

								<br>
								Fin
							</a>
						</div>
					</div>
					<div class="flex_main" style="height:100%;overflow:hidden;;">
						<div id="ffds56fds"  data-act_target_toggle="true" data-table="newsletter" data-table_value="<?= $idnewsletter ?>">
							<br><br>
							<div class="applinkblock applink" main_auto_tree>
								<? $arr_edit = ['nom'=>'Titre','dateDebut'=>'Date d\'envoi','color'=>'Couleur','bgcolor'=>'Couleur de fond'];
								foreach($arr_edit  as $key=>$value) {

									?>
									<div auto_tree class="padding bold"><div><?= idioma($value) ?></div></div>
									<div class="retrait relative">
										<a act_target="news_field_edit_<?= $key ?>" mdl="app/app_field_update" vars="table=<?= $table ?>&table_value=<?= $idnewsletter ?>&field_name_raw=<?= $key ?>">
											<?= $APP->draw_field(['field_name_raw' => $key, 'table' => $table, 'field_value' => $arr[$key . $Table]]) ?>&nbsp;</a>
										<div class="" style="position: absolute;display:none;top:0;min-width:100%;min-height:100%;z-index:300" id="news_field_edit_<?= $key ?>"></div>
									</div>
									<?
								}
								?>
								<div auto_tree class="padding bold"><div><?= idioma('Image') ?></div></div>
								<div class="retrait">
									<div class="aligncenter" act_defer mdl="app/app_img/image_dyn"
									     vars="table=newsletter&table_value=<?= $idnewsletter ?>&codeTailleImage=small&show" scope="app_img"
									     value="newsletter-small-<?= $idnewsletter ?>"></div>
								</div>
							</div>
						</div>
						<div id="whatyd" data-act_target_toggle="true" class="applink applinkblock applinkbig" style="height:100%;display:none;">
							<a onclick="$('news_zoom_main').loadModule('app/app_newsletter/app_newsletter_build_source','idnewsletter=<?= $idnewsletter ?>')"><i class="fa fa-code"></i> <?=idioma('Code source html')?></a>
							<a onclick="ajaxMdl('app/app_custom/mail/mail_send','Mail : <?= $arr['emailDevis'] ?>','idnewsletter=<?= $idnewsletter ?>')"><i class="fa fa-mail"></i> Envoyer un mail Bat</a>
							<a onclick="$('news_zoom_main').loadModule('app/app_liste/app_liste','table=newsletter_contact')"><i class="fa fa-mail"></i>Liste des contacts</a>
						</div>
						<div id="gdgfdsgfsd" data-act_target_toggle="true" style="height:100%;display:none;">
							<div  class="applink applinkbig applinkblock borderb">
								<a onclick="$('news_zoom_main').loadModule('app/app_newsletter/app_newsletter_build_item','idnewsletter=<?= $idnewsletter ?>')"><i class="fa fa-wrench"></i>Construire</a>

								<a onclick="$('news_zoom').toggle();"><i class="fa fa-eye"></i> Visualiser</a>
							</div>
							<div><?= skelMdl::cf_module('app/app_newsletter/app_newsletter_build_mini_liste', array('idnewsletter' => $idnewsletter), $idnewsletter); ?></div>
						</div>
					</div>
				</div>
				<div class="flex_main" style="overflow:hidden;">
					<div id="news_zoom_main"  style="height: 100%;overflow:hidden;">

					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="news_zoom" class="absolute blanc" style="height:100%;z-index: 100;overflow:hidden;min-width:50%;right:0;top:0;display:none;">
		<div class="flex_v">
			<div class="padding applink ">
				<div class="inline padding borderb">
					<a onclick="runModule('mdl/dyn/dyn_newsletter_html','idnewsletter=<?= $idnewsletter ?>')"><i class="fa fa-cogs"></i> Reconstruire</a>
				</div>
			</div>
			<div class="flex_main" act_defer mdl="app/app_newsletter/app_newsletter_preview"
			     vars="idnewsletter=<?= $idnewsletter ?>" value="<?= $idnewsletter ?>" style="overflow:auto;">
			</div>
		</div>
	</div>
</div>
<script>
	load_table_in_zone('table=newsletter_block&vars[idnewsletter]=<?=$idnewsletter?>', 'dyn_pr');
</script>
<style>
	#django {
		height: 5px;
		margin-right: 5px;
		margin-left: 5px;
		position: relative;
		background-color: #333;
	}

	.info_entete {
		margin: 0.5em;
		padding: 0.5em;
	}
</style>
