<?
	include_once($_SERVER['CONF_INC']);
	// echéancier, trace les dates de fin et les dates de début ( rouge , vert )
	$APP = new App();

	$dateStart = new DateTime();

	$DADATE = $dateStart->format('Y-m-d');
	$jourEnCours = $dateStart->format('d');
	$moisEnCours = $dateStart->format('m');
	$anneeEnCours = $dateStart->format('Y');
	$numeroSemaineEnCours = $dateStart->format('W');

	$ce_lundi = clone $dateStart->modify('monday this week');
	$fin_semaine = clone $dateStart->modify('last day next week');
	$prochain_lundi = clone $dateStart->modify('next monday this month');
	$dernier_jour_mois = clone $dateStart->modify('last day of this month');
	$premier_jour_prochain_mois = clone $dateStart->modify('first day of next month');
	$dernier_jour_prochain_mois = clone $dateStart->modify('last day of next month');

	// 	cette semaine
	// 	le reste du mois
	//	le mois prochain
	//  on prend les timestamp. puis boucle :
	$periode['Cette semaine'] = [$ce_lundi, $fin_semaine];
	$periode['Fin du mois'] = [$prochain_lundi, $dernier_jour_mois];
	$periode['Le mois prochain'] = [$premier_jour_prochain_mois, $dernier_jour_prochain_mois];

	// Les schemes avec dates :
	$rs_sc = $APP->get_schemes();// ['hasDateScheme' => 1]
	$arr_sc = iterator_to_array($rs_sc);

?>
<div main_auto_tree style="height:100%;overflow-x:auto;overflow-y:hidden;width:9000px;" class="blanc flex_h flex_nowrap" >
	<? foreach ($periode as $key => $value) {
		// $value[0];
		// $value[1];
	?>
		<? } ?>
	<? foreach ($periode as $key => $value) {
		// $value[0];
		// $value[1];
		?>
		<div style="height:100%;overflow:auto; "  >
			<div auto_tree class="borderb padding ededed" style="width:100%;" ><?= $key ?>
				Du <?= $value[0]->format('d/m/Y') ?>
				Au <?= $value[1]->format('d/m/Y') ?>
			</div >
			<div class="retrait padding" >
				<?
					$begin = $value[0];
					$end = $value[1];

					for ($i = $begin; $begin <= $end; $i->modify('+1 day')) {
						$dadate = $i->format("Y-m-d");
						?>
						<div auto_tree class="padding borderb" ><?= fonctionsProduction::date_fr($dadate) ?></div >
						<div class="retrait padding flex_h" >
							<? foreach ($arr_sc as $key => $value) {
								// dateDebut
								if($APP->has_field('dateDebut'));
								//
								$table = $value['nomAppscheme'];
								$Table = ucfirst($table);
								$APP_tmp = new App($value['nomAppscheme']);
								$rs_tmp_deb = $APP_tmp->find(['dateDebut' . $Table => $dadate]);
								$rs_tmp_fin = $APP_tmp->find(['dateFin' . $Table => $dadate]);
								if ($rs_tmp_deb->count() == 0 && $rs_tmp_fin->count() == 0) continue;
								?>
								<div class="flex_main ededed" style="border-bottom:red;" >
									<?= $value['nomAppscheme'] ?>
									<div class="flex_h flex_wrap" >
										<? while ($arr_tmp_deb = $rs_tmp_deb->getNext()) {
											$value_id = (int)$arr_tmp_deb['id' . $table];
											?>
											<div style="max-width: 25%;" class="padding margin border4 inline" act_defer mdl="app/app/app_fiche_mini" vars="table=<?= $table ?>&table_value=<?= $value_id ?>"></div ><?
										} ?>
										<? while ($arr_tmp_fin = $rs_tmp_fin->getNext()) {
											$value_id = (int)$arr_tmp_fin['id' . $table];
											?>
											<div  style="max-width: 25%;" class="padding margin border4  inline" act_defer mdl="app/app/app_fiche_mini" vars="table=<?= $table ?>&table_value=<?= $value_id ?>"></div ><?
										} ?>
									</div >
								</div >
								<?
							} ?>
						</div >
					<? } ?>
			</div >
		</div >

	<? } ?>
</div >
