<?
	include_once($_SERVER['CONF_INC']);
	$APP = new App();
	$uniqid = uniqid();
	$_POST = fonctionsProduction::cleanPostMongo($_POST, 1);
	$tag = empty($_POST['tag']) ? 'notag' : $_POST['tag'];
	$vars = empty($_POST['vars']) ? array() : $_POST['vars'];
	$base = empty($_POST['base']) ? 'sitebase_ged' : $_POST['base'];
	$collection = empty($_POST['collection']) ? 'ged_client' : $_POST['collection'];
	$baseFS = skelMongo::connectBase($base);
	$fs = $baseFS->getGridFs($collection);
	if ($tag != 'notag'):
		$vars['metatag'] = array('$in' => array($tag));
	else:
		// $vars['$or']	=	array(array('metatag'=>array('$exists'=>0)));
	endif;

	$vars['metatag'] = array('$in' => array($tag));

	$rs = $fs->find($vars)->sort(array('uploadDate' => -1));

	while ($file = $rs->getNext()) {
		$arr      = $file->file;
		$dragvars = 'drop[_id]=' . $arr['_id'] . '&drop[collection]=' . $collection . '&drop[base]=' . $base;
		$vars     = 'uid=' . $arr['_id'] . '&collection=' . $collection . '&base=' . $base;
		$meta_client = empty($arr['metadata']['idclient'])? '' :$arr['metadata']['idclient'] ;
		$meta_devis = empty($arr['metadata']['iddevis'])? '' :$arr['metadata']['iddevis'] ;
		$meta_prestataire = empty($arr['metadata']['idprestataire'])? '' :$arr['metadata']['idprestataire'] ;
		?>
		<tr act_preview_mdl = "document/document_detail"
		    scope = "document"
		    draggable = "true"
		    class = "autoToggle applink"
		    mdl = 'trfilename'
		    value = "<?= $arr['_id'] ?>"
		    base = "<?= $base ?>"
		    collection = "<?= $collection ?>"
		    dragvars = "<?= $dragvars ?>"
		    vars = "<?= $vars ?>"
		    _id[]="<?=$arr['_id']?>" >
  <td class = "aligncenter"><input type = "checkbox"
                                   value = "<?= $arr['_id'] ?>"
                                   name = "_id[]"/></td>
  <td title = "<?= $arr['filename'] ?>"><?= $arr['filename'] ?></td>
  <td  data-contextual="table=client&table_value=<?=$meta_client?>"><?=$APP->get_titre_vars(['idclient'=>(int)$meta_client])?></td>
  <td  data-contextual="table=devis&table_value=<?=$meta_devis?>"><?=$meta_devis?></td>
  <td><?=$APP->get_titre_vars(['idprestataire'=>(int)$meta_prestataire])?></td>
  <td><?= date_fr($arr['metadata']['date']) . ' ' . maskHeure($arr['metadata']['heure']) ?></td>
  <td><?= pathinfo($arr['filename'], PATHINFO_EXTENSION); ?></td>
  <td class = "alignright"><?= maskSize($arr['length']) ?></td>
  <td class = "aligncenter"><a deleteFile = "<?= $arr['_id'] ?>">
		  <li class = "fa fa-times"></li>
	  </a></td>
</tr>
<? } ?>
<input type = "hidden"
       name = "base"
       value = "<?= $base ?>"/>
<input type = "hidden"
       name = "collection"
       value = "<?= $collection ?>"/>