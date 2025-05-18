<? 
if(file_exists('../conf.inc.php')) include_once('../conf.inc.php');
if(file_exists('../../conf.inc.php')) include_once('../../conf.inc.php'); 
$time = time();
	return;
$idfournisseur= (int)$_POST['idfournisseur'];
$arr = skelMongo::connect('fournisseur')->findOne(array('idfournisseur'=>(int)$idfournisseur)) ; 
?>
<div class="contextualMenu">
  <div class="titre">
    <?=idioma('Fournisseur')?> <?=$idfournisseur?>
  </div> 
  <div class="applink applinkblock"> 
     <a onclick="<?=fonctionsJs::fournisseur_fiche($idfournisseur);?>">
<img src="images/icones/edit16.png" />&nbsp;<?=idioma('Fiche fournisseur')?>
</a>
    <a onclick="ajaxInMdl('production/fournisseurpresentation/fournisseur_presentation','fournisseur<?=$arr["idfournisseur"]?>','idfournisseur=<?=$arr["idfournisseur"]?>',{onglet:'Présentations <?=$arr['nomFournisseur']?>'})"><img src="images/icones/edit16.png" />&nbsp;présentation</a>
    <a onclick="ajaxInMdl('production/fournisseurclause/fournisseurClause','fournisseurclause<?=$arr["idfournisseur"]?>','idfournisseur=<?=$arr["idfournisseur"]?>',{onglet:'Clauses <?=$arr['nomFournisseur']?>'})"><img src="images/icones/edit16.png" />&nbsp;<?=idioma('clauses générales')?></a>
    <hr />
     <a onclick="<?=fonctionsJs::note_create(array('idfournisseur'=>$idfournisseur))?>"> <img src="<?=ICONPATH?>note16.png" />&nbsp;
    <?=idioma('Creer une agent_note')?>
    </a> 
        <?=skelMdl::cf_module('gui/gui_tile_click',array('idfournisseur'=>$idfournisseur,'moduleTag'=>'span'),$idfournisseur);?>
  </div>
</div>