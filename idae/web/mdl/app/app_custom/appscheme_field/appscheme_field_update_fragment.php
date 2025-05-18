<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors', 55);
	$table      = $_POST['table'];
	$Table       = ucfirst($table);
	$APP         = new App($table);
	$idappscheme_field = (int)$_POST['table_value'];
	$arr         = $APP->findOne(['id'.$table => $idappscheme_field]);
	//
	$types = $APP->app_default_fields;



?>
<div class="margin" style="width:250px;">
	 <table class="table_form">
		 <tr>
			 <td>Totaliser</td>
			 <td><?= chkSch('has_total'.$Table, $arr['has_total'.$Table]); ?></td>
		 </tr>
		 <tr>
			 <td>Moyenne</td>
			 <td><?= chkSch('has_moyenne'.$Table, $arr['has_moyenne'.$Table]); ?></td>
		 </tr>
	 </table>
</div>

