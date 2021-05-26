<? 
include_once($_SERVER['CONF_INC']);
$time = time();

$idtache = (int)$_POST['idtache'];
$APP = new App('tache');$APP->consolidate_scheme($idtache);
$arrtache = $APP->findOne(array('idtache'=>$idtache)) ;

$time_debut = strtotime($arrtache['dateDebutTache']);
?>
<? if($arrtache['codeTache_statut']!='END'){ ?>
    <a onClick="act_chrome_gui('app/app_custom/tache/tache_move','table=tache&table_value=<?=$idtache?>')">
    <i class="fa fa-calendar-o"></i> <?=idioma('Deplacer')?> <i class="fa fa-caret-left"></i><i class="fa fa-caret-right"></i>
    </a>
<?   } ?>
<? if($time_debut<=time()){ ?>
	<a onClick="act_chrome_gui('app/app_custom/tache/tache_fwd','table=tache&table_value=<?=$idtache?>')">
		<i class="fa fa-rocket textvert"></i><?=idioma('Relancer')?>&nbsp;&nbsp;&nbsp;
    </a>
<? } ?>
	<? if($arrtache['codeTache_statut']!='END'){ ?>
		<a onClick="act_chrome_gui('app/app_custom/tache/tache_close','table=tache&table_value=<?=$idtache?>')">
		    <i class="fa fa-calendar-times-o textrouge"></i> <?=idioma('Clore')?>
	    </a>
	<? } ?>
<hr>