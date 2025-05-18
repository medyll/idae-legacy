<?

	include_once($_SERVER['CONF_INC']);

	$APP = new App();
	ignore_user_abort(true);
	$time = time();
	$vars = array( 'notify' => 'Début mise en production' );

	$db                     = $APP->plug_base('sitebase_production');
	$collection             = $db->produit;
	$collection_tarif       = $db->produit_tarif;
	$collection_tarif_gamme = $db->produit_tarif_gamme;

?>
<?
	if(empty($_POST['run'])):
		$ct_old = $collection_tarif->find(['dateDebutProduit_tarif'=>['$lte'=>date('Y-m-d')]]);
		$ct_tar =  $ct_old->count();
		// dateDebutProduit_tarif
?>
	<div style="width:350px;">
		<table class="tabletop">
			<tr>
				<td style="width:90px;text-align:center"><br>
					<img src="<?=ICONPATH?>alert32.png" /></td>
				<td class="texterouge"><br>
					Voulez vous lancer un import ?<br>  
				</td>
			</tr>
		</table>
		<div class="padding"><progress value="0" id="auto_csv_job"></progress></div>
		<div class="buttonZone">
			<input type="button" class="validButton" value="Importer" onclick="$('frame_xmlte').loadModule('app/app_admin/app_csv','run=1')"  >
			<input type="reset" value="Fermer" class="cancelClose" >
		</div>
		<div style="width:100%;max-height:350px;border:none;overflow:auto;" id="frame_xmlte" scrolling="auto"></div>
	</div>
<?
	return;
endif;

ini_set('display_errors',55);
ini_set('memory_limit','2G');
set_time_limit(0);

$app_agent  = $APP->plug('sitebase_base','agent');
$app_localisation  = $APP->plug('sitebase_base','localisation');
$app_adresse  = $APP->plug('sitebase_base','adresse');
$app_ent        = $APP->plug('sitebase_base','entite');
$app_cli        = $APP->plug('sitebase_base','client');
$app_cli_cat    = $APP->plug('sitebase_base','client_categorie');
$app_cli_act    = $APP->plug('sitebase_base','client_type');
$app_contr      = $APP->plug('sitebase_base','contrat');
$app_contr_ligne        = $APP->plug('sitebase_base','contrat_ligne');
$app_crit_contr = $APP->plug('sitebase_base','contrat_critere');
$app_matos      = $APP->plug('sitebase_base','materiel');
$app_matos_compteur = $APP->plug('sitebase_base','materiel_compteur');
$app_prod       = $APP->plug('sitebase_base','produit');
$app_categorie  = $APP->plug('sitebase_base','produit_categorie');
$app_gamme      = $APP->plug('sitebase_base','produit_gamme');

// SSC => contrat
$progress_vars = array('progress_name' => 'csv_job', 'progress_message' =>  'Importation fichier en cours ...', 'progress_value' =>  0, 'progress_max' => 100);
skelMdl::send_cmd('act_progress', $progress_vars, session_id());
		
$importedCSVFile = APPPATH.'web/ssc_csv_big.csv'; 
$assocSSC = csv2array($importedCSVFile);
$max = sizeof($assocSSC);
$debugArr = array();

$progress_vars = array('progress_name' => 'csv_job', 'progress_message' =>  'Traitement en cours ...', 'progress_value' => 50, 'progress_max' => 100);
skelMdl::send_cmd('act_progress', $progress_vars, session_id());

   
foreach ($assocSSC as $key => $line) {
    if (($i % 100) == 0 || $i==0) {
        $progress_vars = array('progress_name' => 'csv_job', 'progress_value' =>  $i, 'progress_max' => $max);
        skelMdl::send_cmd('act_progress', $progress_vars, session_id());
    }
	 $i++;
    if($line['code-client']=='C0000322') {
    	// $debugArr[]=$line;
	};
    	$debugArr[$line['charge-affaire-ssc']]=$line['nom-signataire-ssc'].' '.$line['prenom-signataire-ssc'];
    //  LINE
   	// 	$line = fonctionsProduction::cleanPostMongo($line, true); 
    unset($idclient_categorie,$idclient_type,$idclient,$idcontrat,$idcontrat_critere,$idproduit_categorie,$idproduit_gamme,$idproduit,$idmateriel,$idagent);
	
	//  AGENT
	if(!empty($line['charge-affaire-ssc'])):
		 $arrtest  = $app_agent->findOne(array('codeAgent'=>$line['charge-affaire-ssc']));
		 if(!empty($arrtest['idagent'])):
		 	$idagent = (int)$arrtest['idagent'];
		 endif;
	endif;
		
    if(!empty($line['organisation-interne'])):
        // $app_ent
        $code = noSpace(strtoupper($line['organisation-interne']));
        $rstest  = $app_ent->find(array('codeEntite'=>$code));
        $arrCli = array();
        
        if($rstest->count()==0){
            $arrCli['identite'] =   $APP->getNext('identite');
        }else{
            $arr_test = $rstest->getNext();
            $arrCli['identite']         =   $arr_test['identite'];
        }   
        //
        $arrCli['codeEntite']       =   $code;
        $arrCli['nomEntite']        =   $line['organisation-interne'];
        // 
        // MAJ
        $app_ent->update(array('identite'=>$arrCli['identite']),array('$set'=>$arrCli),array('upsert'=>true));  
        // IDENTITE
        $identite = $arrCli['identite'] ; 
        
    endif;
    
    if(!empty($line['categories-clients'])):
        // CLIENT_CATEGORIE  // categories-clients
        $code = noSpace(strtoupper($line['categories-clients']));
        $rstest  = $app_cli_cat->find(array('codeClient_categorie'=>$code));
        $arrCli = array();
        
        if($rstest->count()==0){
            $arrCli['idclient_categorie'] =     $APP->getNext('idclient_categorie');
        }else{
            $arr_test = $rstest->getNext();
            $arrCli['idclient_categorie']           =   $arr_test['idclient_categorie'];
        }   
        //
        $arrCli['codeClient_categorie']     =   $code;
        $arrCli['nomClient_categorie']      =   $line['categories-clients'];
        // 
        // MAJ
        $app_cli_cat->update(array('idclient_categorie'=>$arrCli['idclient_categorie']),array('$set'=>$arrCli),array('upsert'=>true));  
        // IDCLIENT_CATEGORIE
        $idclient_categorie = $arrCli['idclient_categorie'] ;
    endif;  
    
    if(!empty($line['activite-client'])):
        // CLIENT_ACTIVITE  // activite-client
        $code = noSpace(strtoupper($line['activite-client']));
        $rstest  = $app_cli_act->find(array('codeClient_type'=>$code));
        $arrCli = array();
        
        if($rstest->count()==0){
            $arrCli['idclient_type'] =  $APP->getNext('idclient_type');
        }else{
            $arr_test = $rstest->getNext();
            $arrCli['idclient_type']            =   $arr_test['idclient_type'];
        }   
        //
        $arrCli['codeClient_type']      =   $code;
        $arrCli['nomClient_type']       =   $line['activite-client'];
        // 
        // MAJ
        $app_cli_act->update(array('idclient_type'=>$arrCli['idclient_type']),array('$set'=>$arrCli),array('upsert'=>true));    
        // IDCLIENT_ACTIVITE
        $idclient_type = $arrCli['idclient_type']   ;
    endif;
    
    if(!empty($line['code-client'])):
        // CLIENT
        $rstest  = $app_cli->find(array('codeClient'=>$line['code-client']));
            $arrCli = array();
        if($rstest->count()==0){
            $arrCli['idclient']         =   $APP->getNext('idclient');
        }else{
            $arr_test = $rstest->getNext();
            $arrCli['idclient']         =   $arr_test['idclient'];
        }   
        //
        $arrCli['identite']     =   $identite;
        $arrCli['idclient_categorie']       =   $idclient_categorie;
        $arrCli['idclient_type']        =   $idclient_type;
        $arrCli['idagent']        =   $idagent;
        $arrCli['codeClient']               =   $line['code-client'];
        $arrCli['nomClient']                =   $line['raison-sociale-client'];
        $arrCli['adresseClient']        =   $line['client-adresse-1'];
        $arrCli['adresse2Client']   =   $line['client-adresse-2'].' '.$line['client-adresse-3'].' '.$line['client-adresse-4'];
        $arrCli['codePostalClient'] =   $line['client-cp'];
        $arrCli['villeClient']      =   $line['client-ville'];
        $arrCli['telephoneClient']  =   $line['client-tel-1'];
        $arrCli['telephone2Client'] =   $line['client-tel-2'];
        $arrCli['mobileClient']     =   $line['client-mobile-1'];
        $arrCli['mobile2Client']    =   $line['client-mobile-2'];
        $arrCli['emailClient']       =   $line['client-mail-1'];
        $arrCli['email2Client']      =   $line['client-mail-2'];
        // MAJ
        $app_cli->update(array('idclient'=>$arrCli['idclient']),array('$set'=>$arrCli),array('upsert'=>true));  
        // IDCLIENT
        $idclient = $arrCli['idclient'] ;
        //
    	// RECUPERER LES ADRESSES => autonomes
    	
    endif;
	// Adresses
    if(!empty($line['client-adresse-1'])):
		$rstest  	= $app_adresse->find(array('codeAdresse'=>$line['code-client'].$line['client-adresse-1']));
	 	// 
	 	$arrCli 	= array();
        if($rstest->count()==0){
            $arrCli['idadresse']         =   $APP->getNext('idadresse');
			// push to client
			$app_cli->update(array('idclient'=>$idclient),array('$push'=>array('idadresse'=>$arrCli['idadresse'])),array('upsert'=>true));  
			// 
        }else{
            $arr_test = $rstest->getNext();
            $arrCli['idadresse']         =   $arr_test['idadresse'];
        }   
		//
		$arrCli['adresseAdresse']       =   $line['client-adresse-1'];
		$arrCli['adresse2Adresse']   	=   $line['client-adresse-2'];
		$arrCli['adresse3Adresse']   	=   $line['client-adresse-3'].' '.$line['client-adresse-4'];
		$arrCli['codePostalAdresse'] 	=   $line['client-cp'];
		$arrCli['villeAdresse']      	=   $line['client-ville'];
		$arrCli['nomAdresse']      		=   $line['client-cp'].' '.$line['client-ville'];
		
		// MAJ
        $app_adresse->update(array('idadresse'=>$arrCli['idadresse']),array('$set'=>$arrCli),array('upsert'=>true));  
        // IDADRESSE
        $idadresse = $arrCli['idadresse'] ;
		// push to client 
		
	endif;
	
    if(!empty($line['code-ssc']) && $line['statut-ssc']!="Fermée"  && $line['statut-ssc']!="Arrêtée"): 
        // CONTRAT
        $rstest  = $app_contr->find(array('codeContrat'=>$line['code-ssc']));
        $arrCli = array();
        if($rstest->count()==0){
            $arrCli['idcontrat']            =   $APP->getNext('idcontrat');
        }else{
            $arr_test = $rstest->getNext();
            $arrCli['idcontrat']            = $arr_test['idcontrat'];   
        }  
        //
        $arrCli['idclient']         =   $idclient;
        $arrCli['codeContrat']      =   $line['code-ssc'];
        $arrCli['nomContrat']       =   $line['libelle-ssc']; 
        $arrCli['descriptionContrat']   = $line['commentaire-ssc']; 
        $arrCli['dateCreationContrat']  =   date_mysql($line['date-acceptation-ssc']);
        $arrCli['dateDebutContrat']     =   date_mysql($line['date-debut-ssc']);
        $arrCli['dateFinContrat']   	=   date_mysql($line['date-fin-ssc']);
        $arrCli['dureeContrat'] =   $line['duree-ssc'];      
		
		// ligne contrat
		// cout-standard-article
		// cout-standard-facture
		
		// cout-depassement-article
		
		
        // MAJ
        $app_contr->update(array('idcontrat'=>$arrCli['idcontrat']),array('$set'=>$arrCli),array('upsert'=>true));
        // IDCONTRAT
        $idcontrat = $arrCli['idcontrat']   ;
    endif; 
        
    if(!empty($line['sfam.'])):
        // CATEGORIE PRODUIT c
        $rstest  = $app_categorie->find(array('codeProduit_categorie'=>$line['sfam']));
        $arrCli = array();
        if($rstest->count()==0){
            $arrCli['idproduit_categorie']  =   $APP->getNext('idproduit_categorie');
        }else{
            $arr_test = $rstest->getNext();
            $arrCli['idproduit_categorie']  =   $arr_test['idproduit_categorie'];   
        }  
        // 
        $arrCli['codeProduit_categorie']=   $line['sfam'];
        $arrCli['nomProduit_categorie'] =   $line['sous-famille'];      
        // MAJ
        $app_categorie->update(array('idproduit_categorie'=>$arrCli['idproduit_categorie']),array('$set'=>$arrCli),array('upsert'=>true));
        // IDCATEGORIE PRODUIT
        $idproduit_categorie = $arrCli['idproduit_categorie']   ;
    endif;
    
    if(!empty($line['fam'])):
        // GAMME_PRODUIT  => SEG
        $rstest  = $app_gamme->find(array('codeProduit_gamme'=>$line['fam']));
        $arrCli = array();
        if($rstest->count()==0){
            $arrCli['idproduit_gamme']  =   $APP->getNext('idproduit_gamme');
        }else{
            $arr_test = $rstest->getNext();
            $arrCli['idproduit_gamme']  =   $arr_test['idproduit_gamme'];   
        }  
        // 
        $arrCli['codeProduit_gamme']=   $line['fam'];
        $arrCli['nomProduit_gamme'] =   $line['famille'];       
        // MAJ
        $app_gamme->update(array('idproduit_gamme'=>$arrCli['idproduit_gamme']),array('$set'=>$arrCli),array('upsert'=>true));
        // IDPRODUIT GAMME 
        $idproduit_gamme = $arrCli['idproduit_gamme']   ;   
    endif;
    
    if(!empty($line['ref-modele'])):
        // PRODUIT --
        $rstest  = $app_prod->find(array('codeProduit'=>$line['ref-modele']));
        $arrCli = array();
        if($rstest->count()==0){
            $arrCli['idproduit']            =   $APP->getNext('idproduit');
        }else{
            $arr_test = $rstest->getNext();
            $arrCli['idproduit']            = $arr_test['idproduit'];   
        }  
        // 
        $arrCli['idproduit_categorie']  =   $idproduit_categorie;
        $arrCli['idproduit_gamme']      =   $idproduit_gamme;
        $arrCli['codeProduit']          =   $line['ref-modele'];
        $arrCli['nomProduit']           =   $line['libelle-modele'];     
        // MAJ
        $app_prod->update(array('idproduit'=>$arrCli['idproduit']),array('$set'=>$arrCli),array('upsert'=>true));
        // IDPRODUIT
        $idproduit = $arrCli['idproduit']   ; 
    endif;
    
    if(!empty($line['identifiant-fabricant'])):
        // MATERIEL --
        $rstest  = $app_matos->find(array('codeMateriel'=>$line['identifiant-fabricant']));
        $arrCli = array();
        if($rstest->count()==0){
            $arrCli['idmateriel']           =   $APP->getNext('idmateriel');
        }else{
            $arr_test = $rstest->getNext();
            $arrCli['idmateriel']           = $arr_test['idmateriel'];  
        }       
        //
        $arrCli['idcontrat']            =   $idcontrat;
        $arrCli['idclient']             =   $idclient;
        $arrCli['idproduit']            =   $idproduit;
        $arrCli['codeMateriel']         =   $line['identifiant-fabricant']; // numero de serie
        $arrCli['nomMateriel']          =   $line['libelle-du-bien'].'-'.$line['identifiant-fabricant'];  
        $arrCli['dateDebutMateriel']    =   date_mysql($line['date-installation-du-bien']); 
        $arrCli['adresseMateriel']      =   $line['site'].' '.$line['localisation'].' '.$line['site-adresse-1']; 
        $arrCli['adresse2Materiel']     =   $line['site-adresse-2'].' '.$line['site-adresse-3'].' '.$line['site-adresse-4'];
        $arrCli['codePostalMateriel']   =   $line['site-cp'];
        $arrCli['villeMateriel']        =   $line['site-ville'];
        $arrCli['telephoneMateriel']    =   $line['site-tel-1'];
        $arrCli['telephone2Materiel']   =   $line['site-tel-2'];
        $arrCli['mobileMateriel']       =   $line['site-mobile-1'];
        $arrCli['mobile2Materiel']      =   $line['site-mobile-2'];
        $arrCli['mailMateriel']         =   $line['site-mail-1'];
        $arrCli['mail2Materiel']        =   $line['site-mail-2'];   
        // MAJ
        $app_matos->update(array('idmateriel'=>$arrCli['idmateriel']),array('$set'=>$arrCli),array('upsert'=>true));
        // IDMATERIEL
        $idmateriel = $arrCli['idmateriel'] ;
		// rajouter ces adresses only //
		
    endif;
    
/*
    if(!empty($line['code-msc'])):   // LIGNES CONTRAT !!  MSC + RFP
     
        $rstest  = $app_contr_ligne->find(array('idcontrat'=>$idcontrat,'codeContrat_ligne'=>$line['code-msc']));
        $arrCli = array();
        if($rstest->count()==0){
            $arrCli['idcontrat_ligne']          =   $APP->getNext('idcontrat_ligne');
        }else{
            $arr_test = $rstest->getNext();
            $arrCli['idcontrat_ligne']          = $arr_test['idcontrat_ligne']; 
        }  
        //
        $arrCli['idcontrat']        =   $idcontrat;
        $arrCli['idmateriel']       =   $idmateriel;
        $arrCli['codeContrat_ligne']        =   $line['code-msc'];
        $arrCli['nomContrat_ligne']     =   $line['nom-msc']; 
        $arrCli['dateDebutContrat_ligne']   =   $line['date-debut-msc'];
        $arrCli['dateFinContrat_ligne'] =   $line['date-fin-msc'];
         
     
        $app_contr_ligne->update(array('idcontrat_ligne'=>$arrCli['idcontrat_ligne']),array('$set'=>$arrCli),array('upsert'=>true));
     
        $idcontrat_critere = $arrCli['idcontrat_critere']   ;
    endif; */


    if(!empty($line['code-msc']) && $line['statut-ssc']=="Validée" ):   // LIGNES CONTRAT !!  MSC + RFP  et ['code-rfp'])); ???
        // LIGNES CONTRAT  $app_crit_contr
        $rstest  = $app_contr_ligne->find(array('idcontrat'=>$idcontrat,'codeContrat_ligne'=>$line['code-msc']));
        $arrCli = array();
        if($rstest->count()==0){
            $arrCli['idcontrat_ligne']          =   $APP->getNext('idcontrat_ligne');
        }else{
            $arr_test = $rstest->getNext();
            $arrCli['idcontrat_ligne']          = $arr_test['idcontrat_ligne']; 
        }  
        //
        $arrCli['idcontrat']        =   $idcontrat;
        $arrCli['idmateriel']       =   $idmateriel;
        $arrCli['codeContrat_ligne']    =   $line['code-msc'];
        $arrCli['nomContrat_ligne']     =   $line['nom-msc']; 
        $arrCli['prixContrat_ligne']        =   str_replace(',','.',  $line['cout-standard-facture']);
        $arrCli['dateDebutContrat_ligne']   =   date_mysql($line['date-debut-msc']);
        $arrCli['dateFinContrat_ligne'] =   date_mysql($line['date-fin-msc']);
         
        // MAJ
        $app_contr_ligne->update(array('idcontrat_ligne'=>$arrCli['idcontrat_ligne']),array('$set'=>$arrCli),array('upsert'=>true));
        // IDCONTRAT
        $idcontrat_ligne = $arrCli['idcontrat_ligne']   ;
    endif; 
        
         
    if(!empty($line['code-de-la-grandeur'])): // => materiel_compteurs !!!
            // CONTRAT LIGNE $app_contr_ligne  => cout standard pour les prix ?
            $rstest  = $app_matos_compteur->find(array('idmateriel'=>$idmateriel,'codeMateriel_compteur'=>$line['code-de-la-grandeur']));
            $arrCli = array();
            if($rstest->count()==0){
                $arrCli['idmateriel_compteur']          =   $APP->getNext('idmateriel_compteur');
            }else{
                $arr_test = $rstest->getNext();
                $arrCli['idmateriel_compteur']          = $arr_test['idmateriel_compteur']; 
            }  
            // 
            $arrCli['idmateriel']       =   $idmateriel;
            $arrCli['codeMateriel_compteur']        =   $line['code-de-la-grandeur'];
            $arrCli['valeurMateriel_compteur']      =   $line['vmm'];// $line['nb-biens---grandeurs'];
			//
            $arrCli['nomMateriel_compteur']         =   $line['nom-de-la-grandeur']; 
            $arrCli['dateDebutMateriel_compteur']   =   date_mysql($line['grandeur-date-debut']);
            $arrCli['dateFinMateriel_compteur']     =   date_mysql($line['grandeur-date-fin']);
            $arrCli['estObligatoireMateriel_compteur']              =   $line['grandeur-saisie-obligatoire'];    
    
            
            
            // MAJ
            $app_matos_compteur->update(array('idmateriel_compteur'=>$arrCli['idmateriel_compteur']),array('$set'=>$arrCli),array('upsert'=>true));
            // IDCONTRATligne
            $idmateriel_compteur = $arrCli['idmateriel_compteur'];
        endif;  
    
    
    
     
    // ssc => contrat
    //echo $line['code-ssc'].' '.$line['code-ssp'].' '.$line['type-de-client'].' '.$line['libelle-du-bien'].' '.$line['ref.-modele'].' '.$line['libelle-modele'].' '.$line['date-installation-du-bien']; 
    flush();ob_flush();
    // client  
}
$progress_vars = array('progress_name' => 'csv_job', 'progress_message' => 'Terminé !',  'progress_value' => $max, 'progress_max' => $max);
        skelMdl::send_cmd('act_progress', $progress_vars, session_id());
//
vardump($debugArr);



function csv2array($importedCSVFile){ // tomongo !!
	//	
	$APP = new App();
	$db_tmp = $APP->plug('sitebase_tmp','tmp_csv');
	$rs_tmp = $db_tmp->find(array('idcsv'=>1));
	
	if($rs_tmp->count()!=0): 
		return $rs_tmp;
		
	endif;

    $assocData = array();
    $assocDataM = array();
    
    if( ($handle = fopen( $importedCSVFile, "r")) !== FALSE) {
        $rowCounter = 0;
        while (($rowData = fgetcsv($handle, 0, ";")) !== FALSE) {
            if( 0 === $rowCounter) {
                $headerRecord = $rowData;
            } else {
            	$assocDataM = array('idcsv'=>1);
                foreach( $rowData as $key => $value) {
                    $key = niceUrl(utf8_encode($headerRecord[$key]));
					$md_key = str_replace('.','',$key);
                    $assocData[ $rowCounter - 1][ $key ] = utf8_encode($value); 
					$assocDataM[ $md_key ] = utf8_encode($value); 
                }
				 $db_tmp->insert($assocDataM);	
		 
            }
			// 
            $rowCounter++;    
        }
		
        fclose($handle);
    }
     
    return $assocData;
}

	