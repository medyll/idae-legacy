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
			<input type="button" class="validButton" value="Importer" onclick="$('frame_xmltes').loadModule('app/app_admin/app_csv_contact','run=1')"  >
			<input type="reset" value="Fermer" class="cancelClose" >
		</div>
		<div style="width:100%;max-height:350px;border:none;overflow:auto;" id="frame_xmltes" scrolling="auto"></div>
	</div>
<?
	return;
endif;

ini_set('display_errors',55);
ini_set('memory_limit','2G');
set_time_limit(0);

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


	$app_contact      = $APP->plug('sitebase_base','contact');

// SSC => contrat
$progress_vars = array('progress_name' => 'csv_job', 'progress_message' =>  'Importation fichier en cours ...', 'progress_value' =>  0, 'progress_max' => 100);
skelMdl::send_cmd('act_progress', $progress_vars, session_id());
		
$importedCSVFile = APPPATH.'web/csv_contact.csv';
$assocSSC = csv2array($importedCSVFile);
$max = sizeof($assocSSC);
$debugArr = array();

$progress_vars = array('progress_name' => 'csv_job', 'progress_message' =>  'Traitement en cours ...', 'progress_value' => 50, 'progress_max' => 100);
skelMdl::send_cmd('act_progress', $progress_vars, session_id());

	//
	// Titre;Nom;Prénom;Tél 1;Tél 2;Mobile 1;Mobile 2;Mail 1;Mail 2;Fax;Adresse 1;Adresse 2;Adresse 3;Adresse 4;CP;Ville;Compte d'accès;Fonction;Localisation;Usage;Dispo;UO;UO de UO;Site;Commentaire Site;Site adresse 1;Site adresse 2;Site adresse 3;Site adresse 4;Site CP;Site ville;Site Tél 1;Site Tél 2;Site Fax;Site Mail;Org.;Raison sociale;Org. adresse 1;Org. adresse 2;Org. adresse 3;Org. adresse 4;Org. CP;Org. ville;Org. Tél 1;Org. Tél 2;Org. Fax;Org. Mail

	foreach ($assocSSC as $key => $line) {
    $i++;
    if (($i % 100) == 0) {
        $progress_vars = array('progress_name' => 'csv_job', 'progress_value' =>  $i, 'progress_max' => $max);
        skelMdl::send_cmd('act_progress', $progress_vars, session_id());
    }
//if($line['org.']=='CWTT0045') {
    	$debugArr[$i]=$line;
//};

    //  LINE
   	// $line = fonctionsProduction::cleanPostMongo($line, true); 
    unset($idclient,$idcontact);
	//        
    if(!empty($line['org.'])):
        // $app_ent
        $code = trim(noSpace(strtoupper($line['org.'])));
        $rstest  = $app_cli->find(array('codeClient'=>$code));
        $arrCli = array();
        
        if($rstest->count()==0){ 
	        continue;
        }else{
	        $arr_test = $rstest->getNext();
	        $arrCli['idclient']         =   (int)$arr_test['idclient'];
        }   
        //
        $arrCli['codeClient']       =   $code;
        $arrCli['nomClient']        =   $line['raison-sociale'];
        //
        // IDENTITE
        $idclient = $arrCli['idclient'] ;
    // echo $idclient.' '.$line['raison-sociale'].'<br>';
        
    else:
	    continue;
    endif;
	if(empty($idclient)) continue;

	if(!empty($line['nom'])):
		// CONTACT
		$code = noSpace(strtoupper($line['nom'].$line['prenom']));
		$rstest  = $app_contact->find(array('code'=>$code,'identite'=>$idclient));
		$arrCli = array();
		if($rstest->count()==0){
			$arrCli['idcontact']         =   (int)$APP->getNext('idcontact');
		}else{
			$arr_test = $rstest->getNext();
			$arrCli['idcontact']         =   (int)$arr_test['idcontact'];
		}
		//
		$arrCli['idclient']     =   $idclient;
		//$arrCli['idclient_categorie']       =   $idclient_categorie;
		//$arrCli['idclient_type']        =   $idclient_type;
		$arrCli['codeContact']               =   $code;
		$arrCli['titreContact']               =   $line['titre'];
		$arrCli['nomContact']                =   $line['nom'];
		$arrCli['prenomContact']                =   $line['prenom'];
		$arrCli['adresseContact']        =   $line['adresse-1'];
		$arrCli['adresse2Contact']   =   $line['adresse-2'].' '.$line['adresse-3'].' '.$line['adresse-4'];
		$arrCli['codePostalContact'] =   $line['cp'];
		$arrCli['villeContact']      =   $line['ville'];
		$arrCli['telephoneContact']  =   $line['tel-1'];
		$arrCli['telephone2Contact'] =   $line['tel-2'];
		$arrCli['mobileContact']     =   $line['mobile-1'];
		$arrCli['mobile2Contact']    =   $line['mobile-2'];
		$arrCli['emailContact']       =   $line['mail-1'];
		$arrCli['email2Contact']      =   $line['mail-2'];
		// MAJ
		$app_contact->update(array('idcontact'=>$arrCli['idcontact']),array('$set'=>$arrCli),array('upsert'=>true));
		// IDCLIENT
		$idcontact = $arrCli['idcontact'] ;
		//

	endif;

    
    
     
    // ssc => contrat
    //echo $line['code-ssc'].' '.$line['code-ssp'].' '.$line['type-de-client'].' '.$line['libelle-du-bien'].' '.$line['ref.-modele'].' '.$line['libelle-modele'].' '.$line['date-installation-du-bien']; 

    flush();ob_flush();
    // client  
}
$progress_vars = array('progress_name' => 'csv_job', 'progress_message' => 'Terminé !',  'progress_value' => $max, 'progress_max' => $max);
        skelMdl::send_cmd('act_progress', $progress_vars, session_id());
//
vardump($debugArr[3]);


function csv2array($importedCSVFile){
    $assocData = array();
    
    if( ($handle = fopen( $importedCSVFile, "r")) !== FALSE) {
        $rowCounter = 0;
        while (($rowData = fgetcsv($handle, 0, ";")) !== FALSE) {
            if( 0 === $rowCounter) {
                $headerRecord = $rowData;
            } else {
                foreach( $rowData as $key => $value) {
                    $key = niceUrl(utf8_encode($headerRecord[$key]));
                    $assocData[ $rowCounter - 1][ $key ] = utf8_encode($value);  
                }
            }
            $rowCounter++;
			//
		  

        }
        fclose($handle);
    }
    
    return $assocData;
}

	