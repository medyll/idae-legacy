<?
include_once($_SERVER['CONF_INC']);
ini_set('display_errors', 55);
$APP = new App('promo_zone');
$_POST = fonctionsProduction::cleanPostMongo($_POST, 1);
$key_tag = empty($_POST['key_tag']) ? 'vide' : $_POST['key_tag'];
$key_mdl = empty($_POST['key_mdl']) ? 'vide' : $_POST['key_mdl'];
$idpromo_zone = $_POST['idpromo_zone'];
$uid_grille_block = $_POST['uid_grille_block'];
$uid_grille_mdl = $_POST['uid_grille_mdl'];

$arr = $APP->query_one(array('idpromo_zone' => (int)$idpromo_zone));

$nomPromo_zone = $arr['nomPromo_zone'];

$rs = $APP->query_one(array('idpromo_zone' => $idpromo_zone, 'grilleBlock.uid_grille_block' => $uid_grille_block), array('grilleBlock.$' => 1));
$grilleBlock = $rs['grilleBlock'][0];
//
$to_alter = $grilleBlock['vignette'];
foreach ($grilleBlock['vignette'] as $key => $arrVign) :
	if ($arrVign['uid_grille_mdl'] == $uid_grille_mdl) :
		$key_mdl;
		$arr = $grilleBlock['vignette'][$key];
	endif;
endforeach;
//
$nb = sizeof($to_alter);
$from = 'thb_' . $nb;
$width = (int)(750 / sizeof($to_alter));
$height = 240;
$img_size = array(3 => array('mongoSize' => 'thb_3', 'sizeImg' => 220, 'sizeHeightImg' => 120), // 250
					2 => array('mongoSize' => 'thb_2', 'sizeImg' => 345, 'sizeHeightImg' => 180), // 375
					1 => array('mongoSize' => 'thb_1', 'sizeImg' => 720, 'sizeHeightImg' => 220), // 375
					'squary' => array('from' => 'thb_1', 'sizeImg' => 50, 'sizeHeightImg' => 50)); // squary

//(int)($width/2.2);
$arrTag = array('idproduit' => 'mdl_idproduit', 'titre' => 'mdl_titre', 'sous titre' => 'mdl_sstitre', 'descriptif' => 'mdl_description', 'prix' => 'mdl_prix', 'url' => 'mdl_url');
// nom de l'image
$img_name = 'promo-zone-' . $idpromo_zone . '-thb_' . $nb . '-' . $uid_grille_mdl;
// $varimg = array('mongoSize' => 'thb_1', 'mongoImg' => $img_name, 'sizeImg' => 720, 'sizeHeightImg' => 220, 'needResize' => true);
// $varimg['build'] = array('thb_3' => array('from' => 'thb_1', 'width' => 220, 'height' => 120), // 250 
//							'thb_2' => array('from' => 'thb_1', 'width' => 345, 'height' => 180), // 375
//							'squary' => array('from' => 'thb_1', 'width' => 50, 'height' => 50));
// 


$varimg = array('mongoSize' => $img_size[$nb]['mongoSize'], 'mongoImg' => $img_name, 'sizeImg' => $img_size[$nb]['sizeImg'], 'sizeHeightImg' => $img_size[$nb]['sizeHeightImg'], 'needResize' => true);
$varimg['build'] = array('tiny' => array('from' => $img_size[$nb]['mongoSize'], 'width' => 135, 'height' => 68));

$varimg['show'] = 'tiny';
// base de données image et collection
$varimg['base'] = 'sitebase_image';
$varimg['collection'] = 'fs';
?>
<div style="min-width:<?= $width ?>px;" class="borderl margin padding">
	<table class="table_info">
		<?
		foreach ($arrTag as $key_tag => $key_mdl):
			$val = $arr[$key_mdl];

			?>
			<tr>
			<td class="applink"> <a onclick="ajaxMdl('app/app_promo_zone/app_promo_zone_build_module_update','Mise à jour vignette <?=$uid_grille_mdl?>','idpromo_zone=<?=$idpromo_zone?>&uid_grille_mdl=<?=$uid_grille_mdl?>&uid_grille_block=<?=$uid_grille_block?>&key_tag=<?=$key_tag?>&key_mdl=<?=$key_mdl?>');">
				 <? if(!empty($value2[$key_mdl])){ ?><i class="fa fa-check"></i><? } ?>

					<?= $key_tag ?></a></td>
			<td><?= $arr[$key_mdl] ?></td>

			</tr><?
		endforeach;
		?>
		<tr>
			<td class="label"><?= idioma('image') ?></td>
			<td><?//= skelMdl::cf_module('app/app_img/image_dyn', $varimg, $img_name); ?></td>

		</tr>
	</table>
</div>

