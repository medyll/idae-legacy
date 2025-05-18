<?
	include_once($_SERVER['CONF_INC']);


?>
<div class="blanc relative flex_v" style="overflow:hidden;width:100%;height:100%;">
	<div class="titre_entete">
		XML, Gestion de fibos
	</div>
	<div class="flex_h flex_main" style="height: 100%;overflow:hidden;">
		<div class="frmCol1 applink applinkblock applinkbig">
			<div class="flex_h toggler applink applinkblock borderb">
				<div class="flex_main aligncenter borderr">
					<a class="flex_main autoToggle " onclick="$('xml_zone').loadModule('business/<?= BUSINESS ?>/app/app_xml/xml_ft','MDL=xml_ftp');"><i class="fa fa-cloud-download"></i>

						<br>
						télécharger
					</a>
				</div>
				<div class="flex_main aligncenter borderr">
					<a class="flex_main autoToggle" onclick="$('xml_zone').loadModule('business/<?= BUSINESS ?>/app/app_xml/xml_launch','MDL=xml_ftp');"><i class="fa fa-calculator"></i>
						<br>
						parser
					</a>
				</div>
				<div class="flex_main aligncenter">
					<a onclick="$('xml_zone').loadModule('business/<?= BUSINESS ?>/app/app_admin/app_build_pre_prod');">
						<i class="fa fa-upload"></i>

						<br><?= idioma('production') ?>
					</a>
				</div>
			</div>
			<div>
				<div class="padding margin borderb">
				<label><?=idioma('Date de téléchargement')?></label>
				<form  action="<?= ACTIONMDL ?>app/actions.php"  onsubmit="ajaxFormValidation(this);return false;">
					<input type="hidden" name="F_action" value="app_update">
					<input type="hidden" name="table" value="xml_conf">
					<input type="hidden" name="table_value" value="<?=$idxml_conf?>">
					<div class="padding">Du <input type="text" value="<?=date_fr($arr_conf['dateDebutXml_conf'])?>" name="vars[dateDebutXml_conf]" class="validate-date-au" ></div>
					<div class="padding">Au <input type="text" value="<?=date_fr($arr_conf['dateFinXml_conf'])?>"  name="vars[dateFinXml_conf]" class="validate-date-au"  ></div>
					<div class="padding">
						<button type="submit" class="inputSmall">ok</button>
					</div>
				</form>
				</div>
			</div>
		</div>
		<div class="  flex_main" style="height:100%;overflow:hidden;" id="xml_zone"></div>
	</div>
</div>