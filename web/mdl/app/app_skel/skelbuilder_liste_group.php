<?
	include_once($_SERVER['CONF_INC']);



	$APP = new App();

	$APP_FIELD = new App('appscheme_field_group');
	ini_set('display_errors', 0);


	$rs = $APP_FIELD->find()->sort(array('ordreAppscheme_field_group' => 1));
?>

<div   class="flex_v blanc"
     style="overflow:hidden;">
	<div class="titre_entete">
		<?= $rs->count(); ?> champs
	</div>
	<div class="titre_entete_menu applink">
		<a onClick="ajaxMdl('app/app_skel/skelbuilder_field_create','Nouveau champ');"><i class="fa fa-plus-circle"></i> Nouveau groupe</a>
	</div>

	<div   class="blanc flex_main "
	     style="overflow:auto">
		<table
		       cellspacing="0"
		       class="explorer" act_sort>
			<thead>
			<tr class="entete">
				<td style="width:40px"></td>
				<td style="width:40px"></td>
				<td style="width:250px">titre</td>
				<td style="width:250px">group</td>
			</tr>
			</thead>
			<tbody  class="toggler">
			<?
				while ($arr = $rs->getNext()) {
				 	 	?>
					<tr class="autoToggle">
						<td><?= $arr['idappscheme_field'] ?></td>
						<td class="aligncenter applink applinkblock">
							<a onclick="<?=fonctionsJs::app_update('appscheme_field_group',$arr['idappscheme_field_group'])?>"><i class="fa fa-pencil"></i></a>
						</td>
						<td>
							<?= $arr['codeAppscheme_field_group'] ?>
						</td>
						<td><?= $arr['nomAppscheme_field_group'] ?></td>
					</tr>
				<? } ?>
			</tbody>
		</table>
	</div>
</div>
