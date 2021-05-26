<?
	include_once($_SERVER['CONF_INC']);
	$uniqid     = uniqid();
	$_POST      = fonctionsProduction::cleanPostMongo($_POST, 1);
	$vars       = empty($_POST['vars']) ? array() : $_POST['vars'];
	$tag        = empty($_POST['tag']) ? '' : $_POST['tag'];
	$base       = empty($_POST['base']) ? 'sitebase_ged' : $_POST['base'];
	$collection = empty($_POST['collection']) ? 'ged_bin' : $_POST['collection'];

	/*$base 	 	= skelMongo::connectBase($base);
	$fs 	 	= $base->getGridFs($collection);
	$rs 	 	= $fs->find($vars)->sort(array('uploadDate'=>-1)); */
?>
<div class="barre_entete">
	<div class="table" style="width:100%;">
		<div class="cell">
			<div class="fauxInput applink">
				<a>
					<i class="fa fa-caret-right"></i>
					&nbsp;
					<?= idioma('Documents') ?>
				</a>
				<a>
					<i class="fa fa-caret-right"></i>
					&nbsp;
					<?= $base ?>
				</a>
				<a>
					<i class="fa fa-caret-right"></i>
					&nbsp;
					<?= $collection ?>
				</a>
				<a>
					<i class="fa fa-caret-right"></i>
					&nbsp;
					<?= $tag ?>
				</a>
			</div>
		</div>
		<div class="cell aligncenter" style="width:15px;">&nbsp;</div>
		<div class="cell" style="width:200px;">
			<div class="fauxInput inline">
				<input autofocus="autofocus" autocomplete="off" expl_search_button="expl_search_button" type="text" class="noborder" style="width:200px;"/>
			</div>
		</div>
	</div>
</div> 
