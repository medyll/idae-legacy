<?
include_once($_SERVER['CONF_INC']);
?>

<div class="minibox">
	<div class="cell first"><img src="images/icones/mail16.png" ></div>
	<div class="cell">
		<a onclick="popopen('https://ssl0.ovh.net/roundcube/?_task=mail', 1600,950,'Mails')">
			Mails RoundCube  Desti reve
		</a>
		<a onclick="popopen('https://37.59.24.227:8080/webmail/', 1600,950,'Mails')">
			Mails RoundCube  destinationsreve
		</a>
		<a onclick="ajaxInMdl('planning/mdlPlanningMain','time','',{onglet:'Mes taches'})">
			Planning taches
		</a>
		<a onclick="ajaxInMdl('agent_note/mdlNoteListe','lis_not','',{title: '<?=idioma('Nouvelle agent_note')?>', hasHandle: false})">
			Liste notes
		</a>
	</div>
</div>