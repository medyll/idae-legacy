<?php
	/**
	 * Created by PhpStorm.
	 * User: lebru_000
	 * Date: 17/02/15
	 * Time: 00:33
	 */
	include_once($_SERVER['CONF_INC']);

	$tpl_path = 'customer/'.CUSTOMERNAME.'/enews/'; ;
	// vardump_async($tpl_path);
	$APP            = new App('newsletter');
	$APP_ITEM       = new App('newsletter_item');
	$APP_ITEM_TYPE  = new App('newsletter_item_type');
	$APP_ITEM_BLOCK = new App('newsletter_block');

	$base            = empty($_POST['base']) ? 'sitebase_image' : $_POST['base'];
	$collection      = empty($_POST['collection']) ? 'fs' : $_POST['collection'];

	$uniqid       = uniqid();
	$_POST        = fonctionsProduction::cleanPostMongo($_POST, 1);
	$idnewsletter = (int)$_POST['idnewsletter'];

	$arr           = $APP->findOne(array('idnewsletter' => (int)$idnewsletter));
	unset($arr['_id']);
	$arr['time']   = time();

	$arr['descriptionNewsletter'] = $arr['descriptionHTMLNewsletter'];
	$arr_data                  = Act::makeTplData('newsletter', $arr['idnewsletter']);
	$arr_data['urlNewsletter'] = Act::lienNewsletter($idnewsletter);
	// img
	$img_name = 'newsletter-small-'.$idnewsletter;
	$grid = $APP->plug_base($base)->getGridFs($collection);
	$test = $grid->find(['filename'=>$img_name.'.jpg']);
	if(!empty($test->count())){
		$arr_data['img'] = '<img style="max-width: 100%;height:auto;" src="'.Act::imgSrc($img_name).'?time='.time().'">';
	}else{$arr_data['img'] = ''; }
	//


	$htmlNewsletter            = skelTpl::cf_template($tpl_path.'enews_ouverture', $arr + $arr_data);
	$nb_item                   = 1;



	skelMdl::reloadModule('app/app_newsletter/app_newsletter_preview', $idnewsletter);

	$rs_item_block = $APP_ITEM_BLOCK->find(array('idnewsletter' => (int)$idnewsletter))->sort(array('ordreNewsletter_block' => 1));
	while ($arr_item_block = $rs_item_block->getNext()):

		$rs_item = $APP_ITEM->find(array('idnewsletter_block' => (int)$arr_item_block['idnewsletter_block']))->sort(array('ordreNewsletter_item' => 1));

		while ($arr_item = $rs_item->getNext()):

			$arr_z = $arr_item;
			unset($arr_z['_id']);
			$i++;
			unset($arr_item['_id']);
			$arr_item['time'] = time();
			// la taille de l'item
			if (!empty($arr_item['idnewsletter_item_type'])):
				$idnewsletter_item_type = (int)$arr_item['idnewsletter_item_type'];
				$arr_type               = $APP_ITEM_TYPE->query_one(['idnewsletter_item_type' => $idnewsletter_item_type]);
				$nb_item                = (int)$arr_type['quantiteNewsletter_item_type'];
			else :
				$nb_item = 1;
			endif;

			$nb_item =$rs_item->count();

			if ($nb_item != $last_nb_item || $i == $nb_item):
				//
				$last_nb_item = $nb_item;
				$i            = 0;
				if (!empty($first)) $htmlNewsletter .= "</tr></table>";
				$htmlNewsletter .= "<table class='table_container' ><tr style='color:".$arr_item_block['colorNewsletter_block'].";background-color: ".$arr_item_block['bgcolorNewsletter_block'].";'>";
			else :

			endif;
			$first = true;
			$pct   = (int)(100 / $nb_item);
			switch ($nb_item):
				case 1:
					$img_size = 'large';
					break;
				case 2:
					$img_size = 'largy';
					break;
				case 3:
					$img_size = 'small';
					break;
				case 4:
					$img_size = 'square';
					break;
			endswitch;

			// le dernier de cette taille ( sort )
			$_Table         = 'Newsletter_item';
			$arr_news_field = ['nom', 'atout', 'descriptionHTML'];
			foreach ($arr_news_field as $key => $field) {
				if (!empty($arr_item[$field . $_Table])) $arr_z[$field] = '<div style="padding:0.5em 0.5em;">' . $arr_item[$field . $_Table] . '</div>';
				else  $arr_z[$field] = '';
			}

			$arr_z['img_size']      = $img_size;
			$bgcolorNewsletter_item = $arr_item['bgcolorNewsletter_item'];
			$colorNewsletter_item = $arr_item['colorNewsletter_item'];
			$str_color = "color:$colorNewsletter_item;background-color:$bgcolorNewsletter_item;";
			// img
			$img_name = 'newsletter_item-' . $img_size . '-' . $arr_item['idnewsletter_item'];
			$grid     = $APP->plug_base($base)->getGridFs($collection);
			$test     = $grid->find(['filename' => $img_name . '.jpg']);
			if (!empty($test->count())) {
				$arr_z['img'] = '<img style="max-width: 100%;height:auto;" src="' . Act::imgSrc($img_name) . '?time=' . time() . '">';
			} else {
				$arr_z['img'] = '';
			}
			//
			$htmlNewsletter .= '<td style="width:' . $pct . '%;vertical-align:top;'.$str_color.'"  >' . skelTpl::cf_template($tpl_path.'enews_block', $arr_z) . "</td>";

			if (!$rs_item->hasNext()):
				$htmlNewsletter .= "</tr></table>";
			endif;

		endwhile;
	endwhile;
	//
	$htmlNewsletter .= skelTpl::cf_template($tpl_path.'enews_footer', $arr + $arr_data);
	$htmlNewsletter = skelTpl::cf_template($tpl_path.'enews_body', $arr + $arr_data + array('body_htmlNewsletter' => $htmlNewsletter));
	$instyle        = new InStyle();
	$htmlNewsletter = $instyle->convert($htmlNewsletter, true);

	$APP->plug('sitebase_newsletter', 'newsletter')->update(array('idnewsletter' => $idnewsletter), array('$set' => array('htmlNewsletter' => $htmlNewsletter)), array('upsert' => true));

	skelMdl::reloadModule('app/app_newsletter/app_newsletter_preview', $idnewsletter);
