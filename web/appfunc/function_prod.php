<?php

	class fonctionsProduction
	{
		function fonctionsProduction()
		{

		}

		function postSearchProduit($arrIn)
		{
			$arrIn = fonctionsProduction::cleanPostMongo($arrIn, 1);
			if (!empty($arrIn['idproduit_type'])) {
				if (is_array($arrIn['idproduit_type'])) $arrPost['idproduit_type'] = ['$in' => (array)$arrIn['idproduit_type']];
			}
			if (!empty($arrIn['dureeJourProduit'])) {
				if (is_array($arrIn['dureeJourProduit'])) $arrPost['dureeJourProduit'] = ['$in' => (array)$arrIn['dureeJourProduit']];
			}
			if (!empty($arrIn['dateDebutProduit_tarif'])) {
				if (is_array($arrIn['dateDebutProduit_tarif'])) {
					foreach ($arrIn['dateDebutProduit_tarif'] as $rdate):
						$date = $rdate . '-01';
						$datemore = $rdate . '-31';
						$arrPost['$or'][]['grilleDateProduit.dateDebutProduit_tarif'] = ['$gte' => $date, '$lte' => $datemore];
					endforeach;
				}
			}
			if (!empty($arrIn['idvilleDepartProduit'])) {
				if (is_array($arrIn['idvilleDepartProduit'])) $arrPost['idvilleDepartProduit'] = ['$in' => (array)$arrIn['idvilleDepartProduit']];
			}
			if (!empty($arrIn['idville'])) {
				if (is_array($arrIn['idville'])) $arrPost['grilleEtapeProduit.idville'] = ['$in' => (array)$arrIn['idville']];
			}
			if (!empty($arrIn['idfournisseur'])) {
				if (is_array($arrIn['idfournisseur'])) $arrPost['idfournisseur'] = ['$in' => (array)$arrIn['idfournisseur']];
			}
			if (!empty($arrIn['idtransport'])) {
				if (is_array($arrIn['idtransport'])) $arrPost['idtransport'] = ['$in' => (array)$arrIn['idtransport']];
			}
			if (!empty($arrIn['iddestination'])):
				if (is_array($arrIn['iddestination'])) $arrPost['grilleDestinationProduit.iddestination'] = ['$all' => (array)$arrIn['iddestination']];
			endif;
			if (!empty($arrIn['idcontinent'])):
				if (is_array($arrIn['idcontinent'])) $arrPost['grilleDestinationProduit.idcontinent'] = ['$in' => (array)$arrIn['idcontinent']];
			endif;
			if (!empty($arrIn['estActifProduit'])):
				if (is_array($arrIn['estActifProduit'])) $arrPost['estActifProduit'] = ['$in' => (array)$arrIn['estActifProduit']];
			endif;
			if (!empty($arrIn['homePageProduit'])):
				if (is_array($arrIn['homePageProduit'])) $arrPost['homePageProduit'] = ['$in' => (array)$arrIn['homePageProduit']];
			endif;
			if (!empty($arrIn['coeurProduit'])):
				if (is_array($arrIn['coeurProduit'])) $arrPost['coeurProduit'] = ['$in' => (array)$arrIn['coeurProduit']];
			endif;
			if (!empty($arrIn['volProduit'])):
				if (is_array($arrIn['volProduit'])) $arrPost['volProduit'] = ['$in' => (array)$arrIn['volProduit']];
			endif;
			if (!empty($arrIn['toutIncluProduit'])):
				if (is_array($arrIn['toutIncluProduit'])) $arrPost['toutIncluProduit'] = ['$in' => (array)$arrIn['toutIncluProduit']];
			endif;
			if (!empty($arrIn['hasDate'])):
				if (is_array($arrIn['hasDate'])) $arrPost['grilleDateProduit'] = ['$size' => 0];
			endif;

			//
			return (array)$arrPost;
		}

		static function getSelectMongo($id, $rs, $value, $cell, $selected = '', $allowempty = false)
		{
			$opt = '';
			if (empty($allowempty)):
				$opt .= '<option ></option>';
			endif;
			while ($rs->hasNext()) {
				$txt = '';
				$arr = $rs->getNext();
				$more = ($selected == $arr[$value]) ? 'selected' : '';
				if (is_array($cell)) {
					foreach ($cell as $k => $v):
						$txt .= ' ' . $arr[$v];
					endforeach;
				} else {
					$txt = $arr[$cell];
				}
				$opt .= '<option value="' . $arr[$value] . '" ' . $more . '>' . $txt . '</option>';
			}

			return '<select id="' . $id . '" name="' . $id . '" >' . $opt . '</select>';
		}
		static function getSelectRadio($id, $rs, $value, $cell, $selected = '', $allowempty = false,$required='')
		{
			$opt = '';
			if (empty($allowempty)):
				$opt .= '<input type="radio" name="' . $id . '" value="" />';
			endif;
			while ($rs->hasNext()) {
				$txt = '';
				$arr = $rs->getNext();
				$more = ($selected == $arr[$value]) ? 'checked="checked"' : '';
				$more .= empty($required) ? '' : 'required="required"';
				if (is_array($cell)) {
					foreach ($cell as $k => $v):
						$txt .= ' ' . $arr[$v];
					endforeach;
				} else {
					$txt = $arr[$cell];
				}
				$opt .= '<label class="flex_align_middle flex_h padding"><span><input type="radio" name="' . $id . '" value="' . $arr[$value] . '" ' . $more . ' /></span><span> '.$txt. '</span> </label>'  ;
			}

			return '<div class="flex_align_middle flex_h" >' . $opt . '</div>';
		}

		function cleanPostDesc($arr)
		{
			if (empty($arr)) return $arr;
			foreach ($arr as $key => $column) {
				$pos = strpos($key, 'description');
				if (is_array($arr[$key])) {
					$arr[$key] = fonctionsProduction::cleanPostMongo($arr[$key]);
				}
				if ($pos === false) {
				} else {
					unset($arr[$key]);
				}
			}

			return $arr;
		}

		static function mois_fr($num)
		{
			$tabmonth = [1 => "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];

			return $tabmonth[(int)$num];
		}

		static function date_fr($date)
		{
			$arrDate = explode('-', $date);
			$tabmonth = [1 => "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];

			return $arrDate[2] . ' ' . $tabmonth[(int)$arrDate[1]] . ' ' . $arrDate[0];
		}

		static function moisDate_fr($date)
		{
			$arrDate = explode('-', $date);
			$tabmonth = [1 => "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];

			return $tabmonth[(int)$arrDate[1]] . ' ' . $arrDate[0];
		}
		static function mois_short_Date_fr($date)
		{
			$arrDate = explode('-', $date);
			$tabmonth = [1 => "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];

			return substr($tabmonth[(int)$arrDate[1]],0,4  ). ' ' . $arrDate[0];
		}

		static function jourMoisDate_fr($date)
		{
			$arrDate = explode('-', $date);
			$tabmonth = [1 => "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
			$tabjour  = array(1=>"Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi","Dimanche");
			$indexjour =  date("w", strtotime($date));
			return  $tabjour[$indexjour].' '.$arrDate[2] . ' ' . $tabmonth[(int)$arrDate[1]] . ' ' . $arrDate[0];
		}
		static function jourMoisDate_fr_short($date)
		{
			$arrDate = explode('-', $date);
			$tabmonth = [1 => "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre"];
			$tabjour  = array(1=>"Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi","Dimanche");
			$indexjour =  date("w", strtotime($date));
			return  $tabjour[$indexjour].' '.$arrDate[2] . ' ' . $tabmonth[(int)$arrDate[1]];
		}

		static function cleanPostMongo($arr, $keepnumerickey = false)
		{

			unset($arr['F_action']);
			unset($arr['mdl']);
			unset($arr['module']);
			unset($arr['reloadModule']);
			unset($arr['afterAction']);
			unset($arr['_id']);
			if (empty($arr)) return $arr;
			foreach ($arr as $key => $column) {
				$pos = strpos($key, 'fake_');
				if ($pos === false) {
				} else {
					unset($arr[$key]);
				}
			}
			$arrClean = [];
			foreach ($arr as $key => $column) {
				if(str_find($key,'code') || str_find($key,'phone')) {
					$arrClean[$key] = $column;
					continue;};
				if ((!is_int($key) || $keepnumerickey == true)) {
					$arrClean[$key] = $arr[$key];
					if ($arr[$key] == 'true') $arrClean[$key] = (bool)true;
					if ($arr[$key] == 'false') $arrClean[$key] = (bool)false;

					if (fonctionsProduction::isTrueFloat($arrClean[$key])) {
						$arrClean[$key] = (float)$arrClean[$key];
					} elseif (is_numeric($arr[$key])) {
						$arrClean[$key] = (int)$arrClean[$key];
					} elseif (is_numeric(str_replace(' ', '', $arr[$key]))) {
						$arrClean[$key] = (int)str_replace(' ', '', $arr[$key]);
					}
					if (is_array($arr[$key])) {
						$arrClean[$key] = fonctionsProduction::cleanPostMongo($arrClean[$key], $keepnumerickey);
					}
				}
			}

			return $arrClean;
		}

		function isTrueFloat($val)
		{
			/*if(is_array($val)) return false;
			$pattern = '/^[+-]?(\d*\.\d+([eE]?[+-]?\d+)?|\d+[eE][+-]?\d+)$/';
			return (!is_bool($val) && (is_float($val) || preg_match($pattern, trim($val))));*/
			//
			if (is_string($val)) $val = trim($val);
			if (is_numeric($val) && (is_float($val) || ((float)$val > (int)$val
						|| strlen($val) != strlen((int)$val)) && (ceil($val)) != 0)
			) {
				return true;
			} else return false;
		}

		function cleanAdodb($arr, $keepnumerickey = false)
		{
			unset($arr['F_action']);
			unset($arr['mdl']);
			unset($arr['module']);
			unset($arr['reloadModule']);
			unset($arr['afterAction']);

			$arrClean = [];
			foreach ($arr as $key => $column) {
				if ((!is_int($key) || $keepnumerickey == true)) {
					$arrClean[$key] = $arr[$key];
					if (is_array($arr[$key])) {
						$arrClean[$key] = fonctionsProduction::cleanAdodb($arrClean[$key], $keepnumerickey);
					}
				}
			}

			return $arrClean;
		}

		function mysqlToMongo($arr, $keepnumerickey = false)
		{
			unset($arr['F_action']);
			unset($arr['mdl']);
			unset($arr['module']);
			unset($arr['reloadModule']);
			unset($arr['afterAction']);
			$arr = fonctionsProduction::cleanPostMongo($arr, $keepnumerickey);
			foreach ($arr as $key => $column) {
				$pos = strpos($key, 'fake_');
				if ($pos === false) {
				} else {
					unset($arr[$key]);
				}
			}
			$arrClean = [];
			foreach ($arr as $key => $column) {
				if ((!is_int($key) || $keepnumerickey == true)) {
					$arrClean[$key] = $arr[$key];
					if (is_array($arr[$key])) {
						$arrClean[$key] = fonctionsProduction::mysqlToMongo($arrClean[$key], $keepnumerickey);
					}
					if (!is_array($arrClean[$key])) {
						$arrID = explode("_id", $key);
						if (sizeof($arrID) == 2) {
							$arrClean['id' . $arrID[1]] = $arrClean[$key];
							unset($arrClean[$key]);
						}
					}
				}
			}

			return $arrClean;
		}

		function andLast($tmparray, $sep = ',', $word = 'et')
		{
			if (sizeof($tmparray) > 1):
				$last = array_pop($tmparray);
				$toadd = implode($sep . ' ', $tmparray) . " $word $last";
			else:
				$toadd = array_pop($tmparray);
			endif;

			return $toadd;
		}

		function buildCodeDevis($iddevis)
		{
			$APP = new App();
			$arrDevis = $APP->plug('sitebase_devis', 'devis')->findOne(['iddevis' => (int)$iddevis]);
			$idproduit = (int)$arrDevis['idproduit'];
			$arr = $APP->plug('sitebase_production', 'produit')->findOne(['idproduit' => (int)$idproduit]);
			$count = $APP->plug('sitebase_devis', 'devis')->find(['dateCreationDevis' => date('Y-m-d')])->count();
			$code = date('dmy') . $count . $arr['codeFournisseur'] . $iddevis;

			return $code;
		}

		function buildCodeClient($idclient)
		{
			$APP = new App();
			$arrDevis = $APP->plug('sitebase_devis', 'client')->findOne(['iddevis' => (int)$idclient]);
			$code = strlen($arrDevis['nomClient']);
			$idproduit = (int)$arrDevis['idproduit'];
			$arr = $APP->plug('sitebase_production', 'produit')->findOne(['idproduit' => (int)$idproduit]);
			$count = $APP->plug('sitebase_devis', 'devis')->find(['dateCreationDevis' => date('Y-m-d')])->count();
			$code = date('dmy') . $count . $arr['codeFournisseur'] . $iddevis;

			return $code;
		}

		function cleanPostSearch($txt)
		{
			$arr = explode('-', $txt);
			$arrClean = [];
			$i = 0;
			foreach ($arr as $key => $value) {
				$i++;
				if (!is_numeric($value)) {
					$arrClean[$key] = $value;
				}
			}
			$out = implode(' ', $arrClean);

			return $out;
		}

		function buildCode($texte, $len = 8, $num = '')
		{
			$texte = strtolower($texte);
			$texte = str_replace(" ", "", $texte);
			$texte = preg_replace('{(.)\1+}', '$1', $texte);
			$texte = str_replace(
				[
					'à', 'â', 'ä', 'á', 'ã', 'å',
					'î', 'ï', 'ì', 'í',
					'ô', 'ö', 'ò', 'ó', 'õ', 'ø',
					'ù', 'û', 'ü', 'ú',
					'é', 'è', 'ê', 'ë', 'ê', '&', strtoupper('&'),
					'ç', 'ÿ', 'ñ', '\'', '"', '_', '!', '?', '\\',
					'(', ')', '/'
				],
				[
					'a', 'a', 'a', 'a', 'a', 'a',
					'i', 'i', 'i', 'i',
					'o', 'o', 'o', 'o', 'o', 'o',
					'u', 'u', 'u', 'u',
					'e', 'e', 'e', 'e', 'e', 'e', 'e',
					'c', 'y', 'n', '-', '-', '-', '-', '-', '-',
					'', '', ''
				], $texte
			);
			if (!empty($num)) {
				$texte = str_replace(
					[
						'a', 'e', 'i', 'o', 'u', 'y'
					],
					[
						'', '', '', '', '', ''
					], $texte
				);
			}
			$testlen = strlen(str_replace(" ", "", $texte));
			$arrt = explode(' ', $texte);
			$dsp = '';//echo "-> ";
			$i = 0;
			$strdone = 0;
			if (strlen($texte) >= $len) {
				//echo sizeof($arrt);//echo "-> ";
				$div = ceil((int)$testlen / sizeof($arrt));//echo "-> ";
				$maxpiece = ceil($len / sizeof($arrt));
				//echo "<br>";
				foreach ($arrt as $value) {
					$i++;
					$strdone += $maxpiece;
					if ($i == sizeof($arrt)) {
						//echo $strdone;echo "-> ";
						$maxpiece = $testlen - $strdone;
						//echo " , ";
					}
					//if(strlen($value)> $div && $testlen-$len>$len) {
					$value = substr($value, 0, $maxpiece);
					//}
					$dsp .= $value;
				}
			} else {
				$dsp = $texte;
			}
			//echo "'".$dsp."'  ";
			// $texte = array_unique($texte);
			$texte = str_replace("-", "", $dsp);
			$texte = str_replace(" ", "", $texte);
			if (strlen($texte) > $len) {
				$texte = substr($texte, 0, $len);
			}
			$texte = stripslashes(strtoupper($texte));

			//echo $texte.'/';
			return $texte;
		}

	}