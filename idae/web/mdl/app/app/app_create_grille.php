<?
	include_once($_SERVER['CONF_INC']);
	ini_set('display_errors' , 55);
	// POST
	$table = $_POST['table'];
	$Table = ucfirst($_POST['table']);
	$table_value = (int)$_POST['table_value'];
	$vars = empty($_POST['vars']) ? array() : fonctionsProduction::cleanPostMongo($_POST['vars'] , 1);
	$groupBy = empty($_POST['groupBy']) ? '' : $_POST['groupBy'];
	//
	if(file_exists(APPMDL.'/app/app_custom/'.$table.'/'.$table.'_update.php')){
		echo skelMdl::cf_module('/app/app_custom/'.$table.'/'.$table.'_update',$_POST);
		// return;
	}
	//
	$APP = new App($table);
	
	//
	$APP_TABLE = $APP->app_table_one;

	$GRILLE_FK = $APP->get_grille_fk();
	$GRILLE = $APP->get_grille($table);
	
	vardump($GRILLE);
	// 
	$arrFields = $APP->get_basic_fields_nude($table);
	//
	$id = 'id' . $table;
	$ARR = $APP->query_one([ $id => $table_value ]);
	//
	$top = 'estTop' . ucfirst($table);
	$actif = 'estActif' . ucfirst($table);
	$nom = 'nom' . ucfirst($table);



	$arrFieldsBool = $APP->get_array_field_bool();
	// LOG_CODE
	// $APP->set_log($_SESSION['idagent'],$table,$table_value,'FICHE');
	// skelMdl::reloadModule('activity/activityAgent', $_SESSION['idagent']);
?>
<div  class = "relative" >
	<?=skelMdl::cf_module('app/app/app_menu',array('table'=>$table,'table_value'=>$table_value,'act_from'=>'update'),$table_value)?>
	<form action = "<?= ACTIONMDL ?>app/actions.php" onsubmit = "ajaxFormValidation(this);return false;" auto_close = "auto_close" >
		<input type = "hidden" name = "F_action" value = "app_update" />
		<input type = "hidden" name = "table" value = "<?= $table ?>" />
		<input type = "hidden" name = "table_value" value = "<?= $table_value ?>" />
		<input type = "hidden" name = "scope" value = "<?= $id ?>" />
		<input type = "hidden" name = "<?= $id ?>" value = "<?= $table_value ?>" />
		<input type = "hidden" name = "vars[m_mode]" value = "1" />

		<div class = "flex_h" >
			<div class = "padding flex_v" style = "max-width:170px" >
				<? if ( ! empty($APP_TABLE['hasImageScheme']) ): ?>
					<div class = "aligncenter" >
						<div class = " ededed inline aligncenter" style = "width:150px;height:150px;" >
							<img style="max-width:100%;" src="<?=Act::imgApp($table,$table_value,'square')?>">
						</div >
					</div >
				<? endif ?>
				<? if ( ! empty($APP_TABLE['hasBoolScheme']) ): ?>
				<div class = "aligncenter  padding" >
					<? foreach ($arrFieldsBool as $field => $arr_ico):
						$fa         = empty($ARR[$field . ucfirst($table)]) ? 'circle-thin' : 'check-circle';
						$css        = empty($ARR[$field . ucfirst($table)]) ? 'textgris' : 'textvert';
						$input_name = "vars[" . $field . ucfirst($table) . "]";
						?>
						<div class = "inline <?= $css ?>" style = "margin-right:1em;line-height:15px;" >
							<?= ucfirst(idioma($field)) ?>
							&nbsp;<input name = "<?= $input_name ?>" type = "range" min = "0" max = "1" value = "<?= $ARR[$field . ucfirst($table)] ?>" style = "width:40px;height:15px;vertical-align: middle;" />
						</div >
					<? endforeach; ?></div >
				<? endif ?>
			</div >
			<div class = "flex_main" >
				<table class = "table_info" >
					<tr class = "" style = "width:100%;" >
						<td class = "label" >Nom</td >
						<td colspan = "5" ><?= $ARR['nom' . $Table] ?></td >
					</tr >
				</table >
				    
				   
			</div >
			
			<? if(sizeof($R_FK)!=0): ?>
					<div class="bordert">
				<table class="table_info">
					<? foreach ($R_FK as $arr_fk):
						$value_rfk               = $arr_fk['table_value'];
						$table_rfk               = $arr_fk['table'];
						$vars_rfk['vars']        = ['id' . $table => $table_value];
						$vars_rfk['table']       = $table_rfk;
						$vars_rfk['table_value'] = $value_rfk;
						$count                   = $arr_fk['count'];
						?>
						<tr>
							<td><?= $table_rfk ?></td>
							<td><a act_chrome_gui="app/app_liste/app_liste_gui"
							       vars="<?= http_build_query($vars_rfk); ?>">
									<i class="fa fa-<?= $APP -> iconAppscheme ?>"></i> <?=$count . ' ' . $table_rfk . '' . (($count == 0) ? '' : 's')?></a></td>
						</tr>
	
					<? endforeach; ?>
				</table>     </div>
				<? endif; ?>
				

		</div >
		<div class = "padding margin border4 ededed" >
					<? foreach ($GRILLE  as $field):
						$id       = 'id' . $field['table_grille'];
						$nom      = 'nom' . ucfirst($field['table_grille']);
						$arr      = $APP->plug($field['base_grille'] , $field['table_grille'])->findOne([ $field['idtable_grille'] => $ARR[$field['idtable_grille']] ]);
						$dsp_name = $arr['nom' . ucfirst($field['table_grille'])];
						?>
						<div class = "label" ><i class="fa fa-<?= $field['icon_grille'] ?>"></i> <?= ucfirst($field['table_grille']) ?>
							<a onclick="act_chrome_gui('app/app/app_create_grille','table=<?=$table?>&table_value=<?=$table_value?>&table_grille=<?=$field['table_grille']?>')" >ajouter</a>
						</div >
						<div class = "padding" >
							
						</div >
					<? endforeach; ?></div >
		<div class = "buttonZone" >
			<button type = "button" class = "trash_button left" onclick="<?= fonctionsJs::app_delete($table,$table_value)?>" >
				<?= idioma('Supprimer') ?>
			</button >
			<input class = "valid_button" type = "submit" value = "<?= idioma('Valider') ?>" >
			<input type = "button" class = "cancelClose" value = "<?= idioma('Fermer') ?>" ></div >
	</form >
</div >
<div class = "titreFor" >
	<?= idioma('Ajout de ') ?> <?= $table ?> <?= $ARR[$nom] ?>
</div >
<div class = "footerFor" >ici</div >
<style >
	.valid_button { border: 1px solid #0000FF; }

	.trash_button { border: 1px solid #B81900; }

	.trash_button:before {
		content: '\f014';
		font-family: FontAwesome;
		color: #B81900;
		margin: 0 5px;
		}
</style >
