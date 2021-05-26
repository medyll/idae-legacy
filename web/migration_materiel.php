<?
	include_once('conf.inc.php');
	$APP = new App();
	$APP_MAT = new App('materiel');
	$APP_CON = new App('contrat');
	$BASE_SYNC = $APP->plug_base('sitebase_sync');
	ini_set('display_errors',55);

	$RS_BIEN = $BASE_SYNC->t_bien->find();
	while($ARR_BIEN = $RS_BIEN->getNext()){
		$ARR_COUV = $BASE_SYNC->t_couvbienparserv->findOne(['N_SOURCE_BIEN_ID'=>$ARR_BIEN['N_ID']]);
		if(!empty($ARR_COUV['N_ID'])){
			$ARR_SERV = $BASE_SYNC->t_servclt->findOne(['N_ID'=>$ARR_COUV['N_TARGET_SERVCLT_ID']]);
			if(!empty($ARR_SERV['N_ESTCOUVERTPAR_MODULESERVCLT_ID'])){
				$ARR_MODULE = $BASE_SYNC->t_moduleservclt->findOne(['N_ID'=>$ARR_SERV['N_ESTCOUVERTPAR_MODULESERVCLT_ID']]);
				if(!empty($ARR_MODULE['N_ID'])){
					$ARR_SOLSERVCLT = $BASE_SYNC->t_solservclt->findOne(['N_ID'=>$ARR_MODULE['N_ESTCOMPRISDS_SOLSERVCLT_ID']]);
					if(!empty($ARR_SOLSERVCLT['N_ID'])){
						$ARR_MAT = $APP_MAT->findOne(['codeMateriel'=>$ARR_BIEN['C_IDENTIFIANTFABRICANT_BIENIMMA']]);
						$ARR_CON = $APP_CON->findOne(['codeContrat'=>$ARR_SOLSERVCLT['C_CODE_SOLSERVCLT']]);
						if(!empty($ARR_MAT['idmateriel']) && !empty($ARR_CON['idcontrat']) ){
							$NEW_ROW['idcontrat'] = (int)$ARR_CON['idcontrat'];
							if(!empty($ARR_CON['idclient'])) $NEW_ROW['idclient'] = (int)$ARR_CON['idclient'];

							$APP_MAT->update(['idmateriel'=>(int)$ARR_MAT['idmateriel']],$NEW_ROW);
							echo $ARR_BIEN['C_IDENTIFIANTFABRICANT_BIENIMMA'].' '.$ARR_BIEN['C_LIB_BIEN'].' '.$ARR_SOLSERVCLT['C_CODE_SOLSERVCLT'].' '.$ARR_CON['idclient'];
							echo "<br>";
							flush();
							ob_flush();

						}
					}
				}
			}
		}


	}

?>
FIN