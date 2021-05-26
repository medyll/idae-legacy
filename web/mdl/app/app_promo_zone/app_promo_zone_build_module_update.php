<?
include_once($_SERVER['CONF_INC']); 
   $APP = new App('promo_zone');
$_POST 		= 	fonctionsProduction::cleanPostMongo($_POST,1);
$key_tag 		= empty($_POST['key_tag'])? 'vide' : $_POST['key_tag']; 
$key_mdl 		= empty($_POST['key_mdl'])? 'vide' : $_POST['key_mdl']; 
$idpromo_zone	=	(int)$_POST['idpromo_zone'];
$idpromo_zone_item	=	(int)$_POST['idpromo_zone_item'];
$uid_grille_block		=	$_POST['uid_grille_block'];
$uid_grille_mdl		=		$_POST['uid_grille_mdl']; 

$arr 		= 	$APP->query_one(array('idpromo_zone'=>(int)$idpromo_zone));
 
$nomPromo_zone = $arr['nomPromo_zone'];


$rs = $APP->query_one( array('idpromo_zone'=>$idpromo_zone,'grilleBlock.uid_grille_block'=>$uid_grille_block),
				array('grilleBlock.$'=>1)); 
$grilleBlock = $rs['grilleBlock'][0] ;
//	vardump($grilleBlock);
// on altere vignette de grilleBlock
$to_alter = $grilleBlock['vignette'];
foreach($grilleBlock['vignette'] as $key=>$arrVign) :  
		if($arrVign['uid_grille_mdl'] == $uid_grille_mdl) :  
		$key_mdl;		
		$text =  $grilleBlock['vignette'][$key][$key_mdl]	;	 
		endif; 
endforeach; 

$arrSize = array(1=>'750',2=>'375',3=>'250');
$width = $arrSize[sizeof($to_alter)]; 
?>

<div class="titre_entete uppercase fond_noir color_fond_noir">
  <?=$nomPromo_zone?>
  <br>
  <?=$key_tag?> 
</div>
<div style="width:550px">
  <form action="<?=ACTIONMDL?>app/app_promo_zone/actions.php"  onsubmit="ajaxFormValidation(this);return false;" auto_close="auto_close" >
  
  <input type="hidden" name="F_action" value="update_block_promo_zone" />
  <input type="hidden" name="idpromo_zone" value="<?=$idpromo_zone?>" />
  <input type="hidden" name="idpromo_zone_item" value="<?=$idpromo_zone_item?>" />
  <input type="hidden" name="uid_grille_block" value="<?=$uid_grille_block?>" />
  <input type="hidden" name="uid_grille_mdl" value="<?=$uid_grille_mdl?>" /> 
  <input type="hidden" name="key_mdl" value="<?=$key_mdl?>" />
  <input type="hidden" name="key_tag" value="<?=$key_tag?>" />
 	  <input type="hidden" name="scope" value="uid_grille_mdl" />
 	  <input type="hidden" name="scopse[]" value="idpromo_zone" />
  <div class="padding margin">
    <? 
	switch($key_mdl):
		case  'mdl_idproduit':
		?>
			<input datalist_input_name = "<?=$key_mdl?>"
			       datalist_input_value = "<?=nl2br(empty($text)? 'texte':$text )?>"
			       datalist = "app/app_select"
			       populate
			       vars="table=produit"
			       name = "search"
			       paramName = "search"
			       value = "<?=nl2br(empty($text)? 'texte':$text )?>"
			       type="text"
			       class="inputMedium"
				/><br>

   <label>
		<input type="checkbox" name="insertMore" value="1"> Entrer infos contextuelles ( url, prix , description ... )</label>
    <?
		
		break;
		case  'mdl_description':
		?>
    <textarea ext_mce_textarea id="denfer" name="<?=$key_mdl?>" style="width:<?=$width?>px;max-width:<?=$width?>px;height:250px;" ><?=empty($text)? 'Descriptif':$text?>
</textarea>
    <?
		
		break;
		case  'mdl_prix':
		?>
    <input type="text" name="<?=$key_mdl?>" style="width:100%;" value="<?=nl2br(empty($text)? 'prix':$text )?>" >
    <?
		break;
		default:
		?>
    <input type="text" name="<?=$key_mdl?>" style="width:100%;" value="<?=empty($text)? 'texte' : $text ?>" >
    <?
		break;
	endswitch; ?>
  </div>
  <div class="buttonZone">
    <input type="submit"  value="Valider" />
    <input type="button" class="cancelClose" value="Fermer" />
  </div>
  </form>
</div>
