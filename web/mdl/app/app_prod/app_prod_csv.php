<?php
	/**
	 * Created by PhpStorm.
	 * User: Mydde
	 * Date: 14/09/2015
	 * Time: 10:17
	 */
	ob_start();
	include_once($_SERVER['CONF_INC']);

	$_POST = array_merge($_GET, $_POST);
	$APP = new App();

	ob_end_clean();
	if (empty($_POST['run'])):

		$db_tmp = $APP->plug('sitebase_tmp', 'tmp');
		$arr = $db_tmp->findOne(['uniqid' => $_POST['uniqid']]);
		$TITLES = $arr['data'][0];

		?>
		<div class="enteteFor" >
			<div class="ms-font-m-plus fond_noir color_fond_noir padding " ><?= idioma('Téléchargement de fichier csv'); ?></div >
			<div class="borderb ededed" >
				<progress value="0" id="auto_progress_csv" style="display:none;height:40px;" ></progress >
			</div >
		</div >
		<div class="padding blanc" style="width:450px;" >

			<form class="Form" target="frm_csv" action="<?= HTTPCUSTOMERSITE . $_SERVER['PHP_SELF'] ?>?run=1&<?= http_build_query($_POST) ?>" method="post" >
				<input type="hidden" name="uniqid" value="<?=$_POST['uniqid']?>" ?>
				<div class="ms-TextField margin padding">
					<label class="ms-Label">Nom du fichier</label>
					<input type="text" class="ms-TextField-field" value="<?=$arr['table']?>" name="filename" required>
				</div>
				<div class="ms-TextField margin padding">
				<label class="ms-Label"><?= idioma('Choix des colonnes'); ?></label>
					</div>
				<div class="margin ms-TextField margin padding ededed border4" style="max-height:250px;overflow:auto;" >
					<?
						foreach ($TITLES as $key_title => $line_title) {
							if (substr($key_title, 0, 2) == 'id') continue;
							if (substr($key_title, 0, 2) == '_i') continue;
							?>
							<div class="borderb" ><label >
									<input type="checkbox" name="row[<?= $key_title ?>]" checked="checked" > <?= $key_title ?>
								</label >
							</div >
							<?
						}
					?>
				</div >
				<div class="buttonZone" >
					<input type="submit" value="<?= idioma('Créer le fichier csv') ?>" >
				</div >
			</form >
		</div >
		<iframe name="frm_csv" style="display:none;" ></iframe >
		<?
	else:
		// clean ids ...
		ini_set('display_errors', 55);
		// output headers so that the file is downloaded rather than displayed
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename=data.csv');

		// create a file pointer connected to the output stream
		$output = fopen('php://output', 'w');

		$db_tmp = $APP->plug('sitebase_tmp', 'tmp');
		$arr = $db_tmp->findOne(['uniqid' => $_POST['uniqid']]);

		$TITLES = $_POST['row'];
		foreach ($TITLES as $key => $title):
			if (substr($title, 0, 2) == 'id') unset($TITLES[$key]);
		endforeach;
		unset($TITLES['_id']);
		unset($TITLES['N_ID']);
		// output the column headings
		fputcsv($output, array_keys($TITLES), ';');

		// fetch the data
		foreach ($arr['data'] as $key => $line) {
			$i++;
			$final_line = [];
			foreach ($TITLES as $key_title => $line_title) {
				$final_line[] = $line[$key_title];
			}
			//
			fputcsv($output, $final_line, ';');
			//
			$progress_vars = ['progress_message'=>$final_line[0],'progress_name' => 'progress_csv', 'progress_value' => $i,  'progress_max' => sizeof($arr['data'])];
			skelMdl::send_cmd('act_progress', $progress_vars, session_id());
		}
		$progress_vars = ['progress_message'=>'Opération terminée','progress_name' => 'progress_csv', 'progress_value' =>100, 'progress_max' => 100];
		skelMdl::send_cmd('act_progress', $progress_vars, session_id());
		// suppression ligne
		$db_tmp->remove(['uniqid' => $_POST['uniqid']]);
	endif;

