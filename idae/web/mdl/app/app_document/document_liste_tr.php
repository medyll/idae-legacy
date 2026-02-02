<?
	include_once($_SERVER['CONF_INC']);
	$uniqid = uniqid();
	$APP = new App();
	$_POST = fonctionsProduction::cleanPostMongo($_POST, 1);
	$_id = $_POST['uid'];

	$vars = empty($_POST['vars']) ? array() : $_POST['vars'];
	$base = empty($_POST['base']) ? 'sitebase_ged' : $_POST['base'];
	$collection = empty($_POST['collection']) ? 'ged_client' : $_POST['collection'];
	$baseFS = skelMongo::connectBase($base);
	$fs = $baseFS->getGridFs($collection);

	$rs = $fs->find(array('_id' => MongoCompat::toObjectId($_id)))->sort(array('uploadDate' => -1));

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
  <td title = "<?= $arr['filename'] ?>"
      onDblClick = "openDoc('<?= $arr['_id'] ?>','<?= $collection ?>','<?= $base ?>')"><?= $arr['filename'] ?></td>
  <td><?=$APP->get_titre_vars(['idclient'=>(int)$meta_client])?><?=$meta_client?></td>
  <td><?=$meta_devis?></td>
  <td><?=$meta_prestataire?></td>
  <td><?= date_fr($arr['metadata']['date']) . ' ' . maskHeure($arr['metadata']['heure']) ?></td>
  <td><?= pathinfo($arr['filename'], PATHINFO_EXTENSION); ?></td>
  <td class = "alignright"><?= maskSize($arr['length']) ?></td>
  <td class = "aligncenter"><a deleteFile = "<?= $arr['_id'] ?>">
		  <li class = "fa fa-times"></li>
	  </a></td>
</tr>
<? } ?>
<input type = "hidden"
       name = "collection"
       value = "<?= $_id ?>"/>