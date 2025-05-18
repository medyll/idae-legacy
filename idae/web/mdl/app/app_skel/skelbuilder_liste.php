<?
	include_once($_SERVER['CONF_INC']);



	$APP = new App();
	$APP_FIELD = new App('appscheme_field');
	$APP_HAS_FIELD = new App('appscheme_has_field');
	$APP_FIELD_GROUP = new App('appscheme_field_group');
	$APP_FIELD_TYPE = new App('appscheme_field_type');
	ini_set('display_errors', 0);
	$arr_f = $APP->app_default_fields;
	/*
	 * foreach ($arr_f as $field => $title):
		echo $field;
		$out = [];
		$out['field_raw'] = $field;
		$out['field_title'] = $title;
		$out['field_group'] = '';
		$out['field_type']  = '';
		$APP->plug('sitebase_app', 'appscheme_field')->insert($out);
	endforeach; */
	/*$rs = $APP_HAS_FIELD->find();
	$out = [];
	while($arr = $rs->getNext()):
		$out['idappscheme_has_field'] =  (int)$arr['idappscheme_has_field'];
		$out['codeAppscheme_has_field'] = $arr['field_raw'];
		$out['nomAppscheme_has_field'] = $arr['field_raw'].ucfirst($arr['collection']);
		$out['ordreAppscheme_has_field'] = (int)$arr['type_ordre'];
		$APP_HAS_FIELD->update(['idappscheme_has_field'=>$out['idappscheme_has_field']],$out);
	endwhile;*/

/*

	$APP_FIELD_TYPE->setNext('idappscheme_field_type',15);
	$rs = $APP_FIELD_GROUP->find()->sort(array('group_ordre' => 1));
	$out = [];
	while($arr = $rs->getNext()):
			$out['idappscheme_field_group'] =  (int)$arr['idappscheme_field_group'];
			$out['codeAppscheme_field_group'] = $arr['group_name'];
			$out['nomAppscheme_field_group'] = $arr['group_name'];
			$out['ordreAppscheme_field_group'] = $arr['group_ordre'];
		$APP_FIELD_GROUP->update(['idappscheme_field_group'=>$out['idappscheme_field_group']],$out);
	endwhile;

	$rs = $APP_FIELD_TYPE->find()->sort(array('group_type' => 1));
	$out = [];
	while($arr = $rs->getNext()):
		$out['idappscheme_field_type'] =  (int)$arr['idappscheme_field_type'];
		$out['codeAppscheme_field_type'] = $arr['type_name'];
		$out['ordreAppscheme_field_type'] = (int)$arr['type_ordre'];
		$APP_FIELD_TYPE->update(['idappscheme_field_type'=>$out['idappscheme_field_type']],$out);
	endwhile;


	$rs = $APP_FIELD->find();
	$out = [];
	while($arr = $rs->getNext()):
		$arrgr = $APP_FIELD_GROUP->findOne(['codeAppscheme_field_group'=>$arr['field_group']]);

		$out['idappscheme_field_group']     =  (int)$arrgr['idappscheme_field_group'];
		$out['codeAppscheme_field_group']   = $arrgr['codeAppscheme_field_group'];
		$out['nomAppscheme_field_group']    = $arrgr['nomAppscheme_field_group'];

		 $APP_FIELD->update(['idappscheme_field'=>(int)$arr['idappscheme_field']],$out);
	endwhile;

	$rs = $APP_FIELD->find();
	$out = [];
	while($arr = $rs->getNext()):
		$arrgr = $APP_FIELD_TYPE->findOne(['codeAppscheme_field_type'=>$arr['field_type']]);

		$out['idappscheme_field_type']     =  (int)$arrgr['idappscheme_field_type'];

		$APP_FIELD->update(['idappscheme_field'=>(int)$arr['idappscheme_field']],$out);
	endwhile;*/

	/*$arr_gr = $APP->plug('sitebase_app', 'appscheme_field')->distinct('field_group');
	foreach ($arr_gr as $field => $title):
		echo $title;
		$out = [];
		$out['group_name'] = $title;
		$out['group_ordre'] = 0;
	 	$APP->plug('sitebase_app', 'appscheme_field_group')->insert($out);
	endforeach;*/

?>

<div   class="flex_v blanc" >
	<div class="titre_entete">

	</div>
	<div class="app_onglet toggler">
		<a class="autoToggle active" onclick="$('inner_skel').loadModule('app/app_skel/skelbuilder_liste_inner');">Collections</a>
		<a class="autoToggle"  onclick="$('inner_skel').loadModule('app/app_skel/skelbuilder_liste_group');">champs groupe</a>
		<a class="autoToggle"  onclick="$('inner_skel').loadModule('app/app_skel/skelbuilder_liste_fields');">champs définis</a>
		<a class="autoToggle"  onclick="$('inner_skel').loadModule('app/app_skel/skelbuilder_liste_collection_fields');">champs att</a>
	</div>
	<div act_defer mdl="app/app_skel/skelbuilder_liste_inner" vars="" class="blanc flex_main " style="overflow:auto" id="inner_skel">

	</div>
</div>
