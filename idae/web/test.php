<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors' , 55);


	$file                 = XMLDIR . 'costa/iti.xml';
	$depth                = 0;
	$tree                 = [ ];
	$tree['name']         = "root";
	$stack[count($stack)] = &$tree;
	$DESTINATION          = '';
	$ITINERARY          = '';
	$my_collect           = [];

	function startElement ($parser , $name , $attrs) {
		global $depth;
		global $stack;
		global $tree;
		global $DESTINATION;
		global $ITINERARY;
		global $my_collect;

		$element         = [ ];
		$element['name'] = $name;
		foreach ($attrs as $key => $value) {
			$element[$key] = $value;
		}
		if ( $element['CODE']){
			// echo $element['CODE'];
		}
		if ( $element['CODE'] && $element['name'] == 'DESTINATION' ) {
			$DESTINATION = $element['DISPLAYNAME'];
			$my_collect['DESTINATION'][$DESTINATION] =  &$element;
		}
		if ( $element['CODE'] && $element['name'] == 'ITINERARY' ) {
			$ITINERARY = $element['DISPLAYNAME'];
			$my_collect['DESTINATION'][$DESTINATION]['ITINERARY'][$ITINERARY] =  &$element;
		}
		if ( $element['name'] && $element['name'] == 'STEPS' ) {
			$STEPS = $element['DISPLAYNAME'];
			$my_collect['DESTINATION'][$DESTINATION]['ITINERARY'][$ITINERARY]['STEPS'][$STEPS] =  &$element;
		}

		$last                   = &$stack[count($stack) - 1];
		$last[count($last) - 1] = &$element;
		$stack[count($stack)]   = &$element;



		$depth ++;
	}

	function endElement ($parser , $name) {
		global $depth;
		global $stack;
		array_pop($stack);
		$depth --;
	}

	$xml_parser = xml_parser_create();
	xml_set_element_handler($xml_parser , "startElement" , "endElement");
	if ( !($fp = fopen($file , "r")) ) {
		die("could not open XML input");
	}

	while ($data = fread($fp , 4096)) {
		if ( !xml_parse($xml_parser , $data , feof($fp)) ) {
			die(sprintf("XML error: %s at line %d" ,
			            xml_error_string(xml_get_error_code($xml_parser)) ,
			            xml_get_current_line_number($xml_parser)));
		}
	}
	xml_parser_free($xml_parser);
	$tree = $stack[0][0];

	echo "<pre>";
	vardump($my_collect);
	vardump($tree);
	echo "</pre>";

	exit;
	$local_rep = XMLDIR . 'costa/';

	$xml      = simplexml_load_file($local_rep . 'catalog.xml');
	$xml_dest = $xml->CostaCruiseCatalog->Destination;
	foreach ($xml_dest as $key => $Destination) {
		$attr_dest       = $Destination->attributes();
		$codeDestination = $attr_dest->Code;
		$nomDestination  = $attr_dest->DisplayName;
		vardump($attr_dest);
		foreach ($Destination->Itinerary as $key_iti => $Itinerary) {
			vardump($Itinerary);
		}
	}
	exit;
	$PATH = 'mdl/business/' . BUSINESS . '/app/app_xml_csv/';
	// vardump_async('tesssst');
	// retry in one minute
	skelMdl::run($PATH . 'ftp/notify' , [ 'file'   => 'terd' ,
	                                      'method' => 'POST' ,
	                                      'delay'  => 5000 ]);

	exit;

	$cddata = <<<XML
	<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:web="http://schemas.costacrociere.com/WebAffiliation">
	   <soapenv:Header><web:Partner><web:Name>IST_MaW</web:Name><web:Password>MaW</web:Password></web:Partner>
	      <web:Agency><web:Code>24514717</web:Code></web:Agency></soapenv:Header>
	   <soapenv:Body><web:ListAllDestinations/></soapenv:Body>
	</soapenv:Envelope>
XML;


	$WSDL = 'http://training.costaclick.net/WAWS_1_9/Availability.asmx?WSDL';
	$ns   = 'http://schemas.costacrociere.com/WebAffiliation';

	try {
		$soapClient = new SoapClient($WSDL , [ 'trace' => 1 , 'exceptions' => 0 , 'connection_timeout' => 3600 ]);
	} catch (Exception $e) {
		echo $e->getMessage(); // SoapFault
	}

	$test = $soapClient->__doRequest($cddata , $WSDL , 'http://schemas.costacrociere.com/WebAffiliation/ListAllDestinations' , '1.1');

	$clean_xml = str_ireplace([ 'SOAP-ENV:' , 'SOAP:' ] , '' , $test);
	$xml       = simplexml_load_string($clean_xml);
	$result    = $xml->Body;


	foreach ($result->children() as $child) {
		vardump($child);
	}
	echo "<hr>";
	exit;
	$headerbody_1 = [ 'code' => 24514717 ];
	$headerbody_2 = [ 'Name' => 'IST_MaW' , 'Password' => 'MaW' ];

	$header_1 = new SOAPHeader($ns , 'Agency' , $headerbody_1);
	$header_2 = new SOAPHeader($ns , 'Partner' , $headerbody_2);

	$result      = $soapClient->__soapCall("ListAllDestinations" , [ ] , NULL , [ $header_1 , $header_2 ]);
	$url_catalog = $result->ListAllDestinationsResponse;

	print_r($result); // Errore di autenticazione dell'agenzia

	echo "<hr>";
	$headerbody = [ 'Agency' => $headerbody_1 , 'Partner' => $headerbody_2 ];
	$header     = new SOAPHeader($ns , null , $headerbody);
	$soapClient->__setSoapHeaders($header);
	$result = $soapClient->ListAllDestinations();

	print_r($result);
	exit;
	$ns = 'http://schemas.costacrociere.com/WebAffiliation/';


	$strHeaderComponent_Ei = "<web:Partner><web:Name>IST_MaW</web:Name><web:Password>MaW</web:Password></web:Partner>";
	$strHeaderComponent_E2 = "<web:Agency><web:Code>24514717</web:Code></web:Agency>";

	$objVar_Ei_Inside       = new SoapVar($strHeaderComponent_Ei , XSD_ANYXML , null , null , null);
	$objVar_Ei_Inside_2     = new SoapVar($strHeaderComponent_E2 , XSD_ANYXML , null , null , null);
	$objHeader_Ei_Outside   = new SoapHeader($ns , 'Partner' , $objVar_Ei_Inside);
	$objHeader_Ei_Outside_2 = new SoapHeader($ns , 'Agency' , $objVar_Ei_Inside_2);

	$headers_full = [ $objHeader_Ei_Outside , $objHeader_Ei_Outside_2 ];
	print_r($headers_full);

	exit;
	$APP_TMP    = new App('contact');
	$miniModel  = $APP_TMP->get_field_group_list([ '$nin' => [ '' , '' ] ] , [ 'in_mini_fiche' => 1 ]);
	$keys_nmini = [ 'field_name' , 'field_name_raw' , 'field_name_group' , 'title' , 'iconAppscheme' , 'icon' ];


	$mini_columns = array_reduce(array_column($miniModel , 'field') , 'array_merge' , [ ]);

	foreach ($mini_columns as $key => $ARR_FIELD) {

		$mini_collect[] = [ 'field_name'       => $ARR_FIELD['codeAppscheme_field'] . $Table ,
		                    'field_name_raw'   => $ARR_FIELD['codeAppscheme_field'] ,
		                    'field_name_group' => $ARR_FIELD['codeAppscheme_field_group'] ,
		                    'field_name_type'  => $ARR_FIELD['codeAppscheme_field_type'] ,
		                    'title'            => idioma($ARR_FIELD['nomAppscheme_field']) ,
		                    'iconAppscheme'    => $ARR_FIELD['iconAppscheme_field'] ,
		                    'icon'             => $ARR_FIELD['iconAppscheme_field'] ];
	}
	vardump($mini_collect);

	exit;
	$dateAR = DateTime::createFromFormat('d/m/y' , "12/08/19");
	echo $dateAR->format('Y-m-d');

	exit;
	$APP_X       = new App('xml_destination');
	$rs          = $APP_X->find([ 'idfournisseur' => 21 ]);
	$arr_collect = [ ];
	while ($arr = $rs->getNext()) {
		// same ? doublons
		$rs_ch = $APP_X->distinct_all('idxml_destination' , [ 'idxml_destination'   => [ '$ne' => (int)$arr['idxml_destination'] ] ,
		                                                      'idfournisseur'       => 21 ,
		                                                      'codeXml_destination' => $arr['codeXml_destination'] ]);

		$arr_collect = array_merge($arr_collect , $rs_ch);


	}
	foreach ($arr_collect as $value):
		// vardump($value);
		$APP_X->remove([ 'idxml_destination' => (int)$value ]);
	endforeach;
	vardump($arr_collect);
	exit;

	$APP_X       = new App('xml_ville');
	$rs          = $APP_X->find([ 'idfournisseur' => 21 ]);
	$arr_collect = [ ];
	while ($arr = $rs->getNext()) {
		// same ? doublons
		$rs_ch = $APP_X->distinct_all('idxml_ville' , [ 'idxml_ville' => [ '$ne' => (int)$arr['idxml_ville'] ] , 'idfournisseur' => 21 , 'codeXml_ville' => $arr['codeXml_ville'] ]);

		$arr_collect = array_merge($arr_collect , $rs_ch);


	}
	foreach ($arr_collect as $value):
		// vardump($value);
		$APP_X->remove([ 'idxml_ville' => (int)$value ]);
	endforeach;
	vardump($arr_collect);
	exit;

	/* tac-tac secteur */
	// $APP_C = new App('commune');
	$APP_S = new App('secteur');
	$APP_V = new App('ville');

	$rs = $APP_S->find([ ] , [ '_id' => 0 ]);

	while ($arr = $rs->getNext()) {
		echo $arr['Libelle_acheminement'] . "<br>";
		$nomville = $arr['Libelle_acheminement'];
		if ( !empty($arr['Ligne_5']) ) {
			$nomville .= ' ' . $arr['Ligne_5'];
		}
		$test = $APP_V->findOne([ 'nomVille' => $nomville ]);
		if ( empty($test['idville']) ) {
			$idville = $APP_V->insert([ 'nomVille' => $nomville , 'slugVille' => format_uri($nomville) , 'codeVille' => strtoupper(format_uri($nomville)) ]);

		} else {
			$idville = $test['idville'];
		}
		$APP_S->update([ 'idsecteur' => (int)$arr['idsecteur'] ] , [ 'idville' => (int)$idville ]);
		flush();
		buffer_flush();
	}
	exit;
	$APP_TARIF = new App('produit_tarif');
	$rs        = $APP_TARIF->find();
	/*while($arr = $rs->getNext()){
		if($arr['dateDebutProduit_tarif']!=$arr['dateProduit_tarif']){
			echo $arr['idproduit_tarif'].' : '.$arr['dateProduit_tarif'].' -  '.$arr['dateDebutProduit_tarif']."<br>";
			  $APP_TARIF->update(['idproduit_tarif'=>(int)$arr['idproduit_tarif']],['dateDebutProduit_tarif'=>$arr['dateProduit_tarif']]);
		}
	}*/
	exit;
	$APP             = new App('transport');
	$APP_RS_CAB      = new App('transport_cabine');
	$APP_PROD        = new App('produit');
	$APP_RS          = $APP->plug('sitebase_production' , 'produit_tarif_gamme');
	$APP_RS_DEVIS    = $APP->plug('sitebase_devis' , 'devis');
	$APP_RS_TGAMME   = new App('transport_gamme');
	$APP_TARIF       = new App('produit_tarif');
	$APP_TARIF_GAMME = new App('produit_tarif_gamme');
	$APP_GAMME       = new App('gamme');

	$rs      = $APP->plug('sitebase_production' , 'produit_tarif_gamme')->find();
	$COLLECT = [ ];
	while ($arr = $rs->getNext()) {
		if ( empty($arr['idproduit']) ) {
			continue;
		}
		$APP_RS_CAB->find([ 'idransport' => (int)$arr['idtransport'] ]);
		$arr_P       = $APP_PROD->findOne([ 'idproduit' => (int)$arr['idproduit'] ]);
		$idgamme     = (int)$arr['idgamme'];
		$idtransport = (int)$arr_P['idtransport'];
		$test_cab    = $APP_RS_CAB->find([ 'idtransport' => $idtransport ,
		                                   'idgamme'     => $idgamme ]);
		$arr_gamme   = $APP_GAMME->query_one([ 'idgamme' => $idgamme ]);

		if ( $test_cab->count() == 0 && !empty($arr_P['idproduit']) && !empty($arr_P['idtransport']) && !empty($arr_gamme['codeGamme']) ):
			echo $arr_P['idproduit'] . ' / ' . $arr_gamme['nomGamme'] . ' / ' . $arr_gamme['nomGamme'] . ' / ' . $arr_P['nomProduit'] . ' / ' . $arr_P['nomTransport'] . '<br>';
			$idtransport_cabine = (int)$APP->getNext('idtransport_cabine');

			$APP_RS_CAB->insert([ 'idtransport_cabine'    => $idtransport_cabine ,
			                      'idtransport'           => $idtransport ,
			                      'idgamme'               => $idgamme ,
			                      'nomTransport'          => $arr_P['nomTransport'] ,
			                      'codeTransport_cabine'  => $arr_gamme['codeGamme'] ,
			                      'ordreTransport_cabine' => $arr_gamme['ordreGamme'] ,
			                      'nomGamme'              => $arr_gamme['nomGamme'] ,
			                      'nomTransport_cabine'   => $arr_gamme['nomGamme'] ]);
		endif;

	}
	/*if ($test_cab->count() == 0 && !empty($arr_P['idproduit']) && !empty($arr_P['idtransport']) && !empty($arr_gamme['codeGamme'])):
		echo $arr_gamme['nomGamme'] . ' / ' . $arr_P['nomProduit'] . ' / ' . $arr_P['nomTransport'] . '<br>';
		$idtransport_cabine = (int)$APP->getNext('idtransport_cabine');

		$APP_RS_CAB->insert(['idtransport_cabine'    => $idtransport_cabine,
		                     'idtransport'           => $idtransport,
		                     'idgamme'               => $idgamme,
		                     'nomTransport'          => $arr_P['nomTransport'],
		                     'codeTransport_cabine'  => $arr_gamme['codeGamme'],
		                     'ordreTransport_cabine' => $arr_gamme['ordreGamme'],
		                     'nomGamme'              => $arr_gamme['nomGamme'],
		                     'nomTransport_cabine'   => $arr_gamme['nomGamme']]);
	endif;*/

	exit;
	ini_set('display_errors' , 55);

	echo Act::lienNewsletter(349);
	// debug contact
	/*$APP    = new App('contact');
	$APP_TY = new App('contact_type');
	$rs     = $APP->find()->sort(['nomContact' => 1]);
	while ($arr = $rs->getNext()) {
		if (empty($arr['telephoneContact']) && !empty($arr['telephone2Contact'])) {
			printr($arr);
			$APP->update(['idcontact' => (int)$arr['idcontact']], ['telephoneContact' => $arr['telephone2Contact'], 'telephone2Contact' => '']);
			flush();
			buffer_flush();
		}

	}*/

	exit;
	$APP   = new App('agent');
	$APP_P = new App('prospect');
	$APP_C = new App('contact');

	$rs = $APP_P->find([ 'idagent' => 29 ]);
	while ($arr = $rs->getNext()) {
		echo $arr['nomProspect'] . '<br>>';
		$rs_C = $APP_C->find([ 'idprospect' => (int)$arr['idprospect'] ]);
		while ($arr_c = $rs_C->getNext()) {
			echo $arr_c['nomContact'] . ' ' . $arr_c['idagent'] . '<br>';
			$APP_C->update([ 'idcontact' => (int)$arr_c['idcontact'] ] , [ 'idagent' => 29 ]);
		}
		//  $APP_C->update(['idprospect'=>(int)$arr['idprospect'],'idagent'=>['$ne'=>29]],['idagent'=>29]);

	}

	exit;
	//
	echo Act::imgApp('agent' , 1 , 'smallest');
	exit;
	$mdl = skelMdl::cf_module('app/app_login/app_login_success');//;
	skelMdl::send_cmd('act_notify' , [ 'msg' => $mdl ]); // , 'options'=>['sticky'=>1]

	exit;
	$APP = new App('appscheme');
	$rs  = $APP->find();
	while ($arr = $rs->getNext()) {
		echo $table = $arr['table'];
		$APP->consolidate_app_scheme($table);
	}
	exit;

	use Ddeboer\Imap\Server;

	$APP = new App();
	$APP->init_scheme('sitebase_email' , 'email');
	$APP->init_scheme('sitebase_email' , 'emailbox');
	$APP->init_scheme('sitebase_email' , 'email_mime');

	$APP_EMAIL     = new App('email');
	$APP_EMAILBOX  = new App('emailbox');
	$APP_EMAILMIME = new App('email_mime');

	$server = new Server('mail.croisieres-maw.fr' , // required
	                     '143' , '/novalidate-cert');
// $connection is instance of \Ddeboer\Imap\Connection
	$connection = $server->authenticate('meddy@croisieres-maw.fr' , 'malaterre666');

	$mailboxes = $connection->getMailboxes();

	foreach ($mailboxes as $mailbox) {
		$a = sprintf('Mailbox %s has %s messages' , $mailbox->getName() , $mailbox->count());
		skelMdl::send_cmd('act_notify' , [ 'msg' => $a , 'options' => [ 'stiscky' => true ] ]);

		$test_box = $APP_EMAILBOX->findOne([ 'nomEmailbox' => $mailbox->getName() ]);
		if ( empty($test_box['nomEmailbox']) ) {
			$idemailbox = $APP_EMAILBOX->create_update([ 'idagent' => $_SESSION['idagent'] , 'nomEmailbox' => $mailbox->getName() ]);
		} else {
			$idemailbox = (int)$test_box['idemailbox'];
		}

		$messages = $mailbox->getMessages();

		foreach ($messages as $message) {
			// $message is instance of \Ddeboer\Imap\Message
			printr($message);
			/*	echo "<br>getNumber<br>" . $message->getNumber();
				echo "<br>getId<br>" . $message->getId();
				echo "<br>getSubject<br>" . $message->getSubject();
				echo "<br>getFrom<br>" . $message->getFrom();
				echo "<br>getTo<br>" . $message->getTo();
				echo "<br>getDate<br>" . $message->getDate()->format('Y-m-d H:i:s');
				echo "<br>isAnswered<br>" . $message->isAnswered();
				echo "<br>isDeleted<br>" . $message->isDeleted();
				echo "<br>isDraft<br>" . $message->isDraft();
				echo "<br>isSeen<br>" . $message->isSeen();
				echo "<br>getHeaders<br>" . json_encode($message->getHeaders(), JSON_PRETTY_PRINT);
				echo "<br>getBodyText<br>" . $message->keepUnseen()->getBodyText();
				echo "<br>getBodyHtml<br>" . $message->keepUnseen()->getBodyHtml();*/
			//
			skelMdl::send_cmd('act_notify' , [ 'msg' => $message->keepUnseen()->getBodyHtml() , 'options' => [ 'stiscky' => true ] ]);
			//
			$out['referenceEmail'] = $message->getNumber();
			$out['codeEmail']      = $message->getId();
			$out['nomEmail']       = $message->getSubject();
			/*$out['fromEmail']        = $message->getFrom();
			$out['toEmail']          = $message->getTo();*/
			$out['dateEmail']        = $message->getDate()->format('Y-m-d');
			$out['heureEmail']       = $message->getDate()->format('H:i:s');
			$out['estReponduEmail']  = $message->isAnswered();
			$out['estSupprimeEmail'] = $message->isDeleted();
			$out['estBrouillonMail'] = $message->isDraft();
			$out['estVuEmail']       = $message->isSeen();
			$out['descriptionEmail'] = $message->keepUnseen()->getBodyHtml();

			$test_email = $APP_EMAIL->findOne([ 'referenceEMail' => $out['referenceEmail'] ]);
			if ( empty($test_email['referenceEmail']) ) {
				vardump($out);
				$idemail = $APP_EMAIL->create_update([ 'idagent' => (int)$_SESSION['idagent'] , 'referenceEMail' => $out['referenceEmail'] ] , $out);
			} else {

			}

			$attachments = $message->getAttachments();

			foreach ($attachments as $attachment) {
				// $attachment is instance of \Ddeboer\Imap\Message\Attachment
				//echo $attachment->getFilename();
			}
		}

	}

	//skelMdl::send_cmd('act_notify', ['msg' => 'Accés ok ' . $_SESSION['idagent'], 'options' => ['stiscky' => true]]);
	// skelMdl::send_cmd('act_notify' , array( 'msg' => 'Accés ok ' , 'options' => array( 'sticky' => true , 'mdl' => 'app/app/app_fiche_mini' , 'vars' => 'table=produit&table_value=535' ) ) , $session_id);

?>