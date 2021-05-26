<?php
/**
 * Created by PhpStorm.
 * User: lebru_000
 * Date: 22/07/15
 * Time: 11:58
 */
	include_once($_SERVER['CONF_INC']);
	$time = time();
	$APP_T = new App('tache');
	/*$arrtache = skelMongo::connect('tache','sitebase_tache')->findOne(array('idtache'=>$idtache)) ;
	$arrstatut = skelMongo::connect('statut_tache','sitebase_tache')->findOne(array('idstatut_tache'=>(int)$arrtache['idstatut_tache'])) ;
	$arrtype = skelMongo::connect('type_tache','sitebase_tache')->findOne(array('idtype_tache'=>(int)$arrtache['idtype_tache'])) ;
	$arragent = skelMongo::connect('agent','sitebase_base')->findOne(array('idagent'=>(int)$arrtache['idagent'])) ;
	$arragentW = skelMongo::connect('agent','sitebase_base')->findOne(array('idagent'=>(int)$arrtache['idagent_writer'])) ; */
	// mise à jour notif
	// skelMongo::connect('tache','sitebase_tache')->update(array('idtache'=>$idtache),array('$set'=>array('notifiedTache'=>1)),array('upsert'=>true))

	$rs = $APP_T->find(array('codeStatut_tache'=>array('$ne'=>'END'),'idagent'=>(int)$_SESSION['idagent'],'dateDebutTache'=>date('Y-m-d')));// a 2 minutes prés
?>

<div style="width:550px;" class="ededed">
	<div>
		<div class="" style="min-height:150px;max-height:25px;overflow:auto;">
			<table class="sortable" style="width:100%" cellspacing="0">
				<thead>
				<tr class="entete">
					<td>Objet</td>
					<td style="width:80px">Echéance</td>
					<td style="width:100px" class="aligncenter">Repousser</td>
					<td style="width:40px;"></td>
				</tr>
				</thead>
				<tbody class="toggler">
				<? while($arr = $rs->getNext()): ?>
					<tr class="autoToggle">
						<td><a onclick="<?=fonctionsJs::app_update('tache',$arrT['idtache']);?>"><?=$arr['nomTache']?> </a>
						</td>
						<td class="aligncenter"><?=calculate_time_span(strtotime($arr['heureDebutTache']))?></td>
						<td>
							<select style="width:75px" class="noborder" onchange="ajaxValidation('updateTache','mdl/tache/','idtache=<?=$arr['idtache']?>&timeDebutTacheSet='+this.value)">
								<option value="" selected="selected">choisir</option>
								<option value="5">5 minutes</option>
								<option value="10">10 minutes</option>
								<option value="15">15 minutes</option>
								<option value="30">30 minutes</option>
								<option value="60">1 h</option>
								<option value="120">2 h</option>
								<option value="180">3 h</option>
								<option value="240">4 h</option>
								<option value="1440">24 h</option>
								<option value="2880">48 h</option>
								<option value="4320">3 jours</option>
								<option value="5760">4 jours</option>
							</select></td>
						<td class="aligncenter ededed" title="Clore la tache">
							<a onclick="ajaxValidation('updateTache','mdl/tache/','idtache=<?=$arr['idtache']?>&codeStatut_tache=END')"><img src="<?=ICONPATH?>delete16.png" /></a>
						</td>
					</tr>
				<? endwhile; ?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<div class="enteteFor">
	<div class="titre_entete">Rappels taches</div>
</div>
<div class="footerFor">
	<div class="buttonZone">
		<input type="button" value="Fermer" class="cancelClose" />
	</div>
</div>