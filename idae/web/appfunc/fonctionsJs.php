<?php
class fonctionsJs
{
	function fonctionsJs(){

	}
	static function app_gui($gui,$table,$table_value='',$vars=[] ){
		$ident = 'app_gui'.$table.'_'.$table_value;
		$query = is_array($vars) ? http_build_query($vars) : $vars;
		return "act_chrome_gui('app/$gui','table=$table&table_value=$table_value&$query',{ident: '$ident' });";
	}
	static function app_create($table,$vars=array(),$mode='popup'){
		$ident = 'app_create_'.$table;
		if($mode=='popup' || $table !='appscheme')
		 return "act_chrome_gui('app/app/app_create','table=".$table.'&'.http_build_query($vars)."',{ident: '$ident' });return false;";
		else
			return "ajaxInMdl('app/app/app_create','app_mdl$ident','table=$table&".http_build_query($vars)."',{onglet:'$ident'});";
	}
	static function app_update($table,$table_value,$vars=array()){
		$ident = 'app_update_'.$table.'_'.$table_value;
		$query = http_build_query($vars);
		return "act_chrome_gui('app/app/app_update','table=$table&table_value=$table_value&$query',{ident: '$ident' });";
	}
	static function app_duplique($table,$table_value,$vars=array()){
		$ident = 'app_duplique_'.$table.'_'.$table_value;
		$query = http_build_query($vars);
		return "act_chrome_gui('app/app/app_duplique','table=$table&table_value=$table_value&$query',{ident: '$ident' });";
	}

	static function app_delete($table,$table_value,$vars=array()){
		$ident = 'app_delete_'.$table.'_'.$table_value;
		$query = http_build_query($vars);
		return "act_chrome_gui('app/app/app_delete','table=$table&table_value=$table_value&$query',{ident: '$ident' });";
	}

	static function app_sort($table,$table_value='',$vars=array()){
		$ident = 'app_sort_'.$table.'_'.$table_value;
		$query = http_build_query($vars);
		return "act_chrome_gui('app/app/app_sort','table=$table&table_value=$table_value&$query',{ident: '$ident' });";
	}

	static function app_fiche_maxi($table,$table_value='',$vars=array()){
		$APP        = new App($table);
		$ARR_APP    = $APP->query_one(['id'.$table=>(int)$table_value]);
		$ident = 'app_fiche_maxi_'.$table.'_'.$table_value;
		$title = 'Synthese '.niceUrlSpace($ARR_APP['nom'.ucfirst($table)]);
		return "ajaxInMdl('app/app/app_fiche_maxi','app_fiche_maxi_$table','table=$table&table_value=$table_value&".http_build_query($vars)."',{onglet:'$title'});";
	}
	static function app_console($table, $vars=array()){
		$APP        = new App($table);
		return "ajaxInMdl('app/app/app_console','app_fiche_console_$table','table=$table',{onglet:'Console $table'});";
	}

	static function app_fiche($table,$table_value='',$vars=array()){
		$ident = 'app_fiche_'.$table.'_'.$table_value;
		return "act_chrome_gui('app/app/app_fiche','table=$table&table_value=$table_value',{ident: '$ident' });";
	}
	static function app_fiche_preview($table,$table_value=''){
		//
		$ident = 'app_fiche_preview'.$table.'_'.$table_value;
		return "act_chrome_gui('app/app/app_fiche_preview_gui','table=$table&table_value=$table_value',{ident: '$ident' });";
	}

	static function app_liste($table, $vars, $table_value=''){
		$ident = 'app_liste_gui'.$table.'_'.$table_value;
		$query = is_array($vars) ? http_build_query($vars) : $vars;
		return "act_chrome_gui('app/app_liste/app_liste_gui','table=$table&table_value=$table_value&$query',{ident: '$ident' });";
	}
	function client_big_fiche($idclient){
		$APP = new App('client');
		$arr = $APP->findOne(array('idclient'=>(int)$idclient));
		$name = addslashes($arr['nomClient']);
		return "ajaxInMdl('app/app_custom/client/client_espace','tmp_client".$idclient."','idclient=$idclient',{scope:'idclient',value: '$idclient' ,onglet:'$name'});";
	}
	function opportunite(){

		return "ajaxInMdl('app/app_custom/opportunite/opportunite_espace','tmp_opportunitelient','',{onglet:'opportunites'});";
	}
	static function app_explorer($table,$vars=array()){
		return "ajaxInMdl('app/app/app_explorer','app_explorer_$table','table=$table',{onglet:'Espace $table'});";
	}
	static function app_mdl($mdl,$vars=array(),$prop=[]){
		if (file_exists(APPMDL . '/customer/'.CUSTOMERNAME.'/'.$mdl.'.php')) {
			$mdl = 'customer/'.CUSTOMERNAME.'/'.$mdl;
		}elseif (file_exists(APPMDL . '/business/'.BUSINESS.'/'.$mdl.'.php')) {
			$mdl = 'business/'.BUSINESS.'/'.$mdl;
		}
		$query_vars = http_build_query($vars);
		$title = empty($prop['title'])? $mdl : $prop['title'];
		$uniqid = str_replace('&','',str_replace('/','',str_replace('=','',$query_vars)));
		$mdl_value = empty($prop['mdl_value'])? 'app_mdl' : $prop['mdl_value'];

		//  echo APPMDL . '/business/'.BUSINESS.'/'.$mdl.'.php';
		return "ajaxInMdl('$mdl','app_mdl$uniqid','".$query_vars."',{onglet:'$title'});";
	}
}