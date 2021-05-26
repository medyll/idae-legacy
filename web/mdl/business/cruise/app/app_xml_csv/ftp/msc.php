<?
	include_once($_SERVER['CONF_INC']);

	$PATH = 'business/' . BUSINESS . '/app/app_xml_csv/';

	ini_set('display_errors', 55);
//
	$vars = ['notify' => 'HTTP XML MSC'];
	skelMdl::reloadModule('activity/appActivity', '*', $vars);

	$ftp_user_name = "flat_fra";
	$ftp_user_pass = "acwed4";
	$ftp_server    = 'http://' . $ftp_user_name . ':' . $ftp_user_pass . '@www.msconline.com/DownloadArea/';

	$local_rep = XMLDIR . 'msc/';
	$arrFile   = ['FRA/flatact_fra_fra.zip',
		'FRA/flatprices_fra_fra.zip',
		'FRA/itinff_fra_fra.zip',
		'FRA/flatitems_fra_fra.zip',
		'GBL/flatportdetl_fra.zip',
		'GBL/flatcitydetl_fra.zip',
		'FRA/flatfile_fra_air.zip',
		'FRA/flatshpdetl_fra_fra.zip',
		'FRA/flatcabdetl_fra_fra.zip',
		'GBL/flatregion_fra.zip'];

	$i     = 0;
	$total = sizeof($arrFile);

	foreach ($arrFile as $file):
		$i++;

		$real_file_name = explode('/', $file)[1];

		$arr_progr = ['progress_parent'  => 'run_ftp_msc',
		              'progress_name'    => 'xml_job_ftp_msc',
		              'progress_text'    => " XML . $total ftp ",
		              'progress_message' => $i . ' / ' . $total,
		              'progress_max'     => $total,
		              'progress_value'   => $i];
		//
		$arr_progr['progress_log'] = $real_file_name.' - téléchargement';
		//
		skelMdl::send_cmd('act_progress', $arr_progr, session_id());

		// Tentative de téléchargement du fichier $server_file et sauvegarde dans le fichier $local_file
		if ($fp = fopen($ftp_server . $file, "r")) {
			if ($pointer = fopen($local_rep . $real_file_name, "wb+")) {
				while ($buffer = fread($fp, 1024)) {
					if (!fwrite($pointer, $buffer)) {
						// return FALSE;
					}
				}
				fclose($pointer);
			}
		} else {
			// return FALSE;
		}
		fclose($fp);
		//
		$zip = new ZipArchive;
		$zip->open($local_rep . $real_file_name);
		$zip->extractTo(XMLDIR . 'msc/');
		$zip->close();
		//
		unlink($local_rep . $real_file_name);
		//

		$arr_progr = ['progress_parent'  => 'run_ftp_msc',
		              'progress_name'    => 'xml_job_ftp_msc',
		              'progress_text'    => " XML . $total ftp ",
		              'progress_message' => $i . ' / ' . $total,
		              'progress_max'     => $total,
		              'progress_value'   => $i];
		//
		$arr_progr['progress_log'] = $real_file_name.' - terminé';
		//
		skelMdl::send_cmd('act_progress', $arr_progr, session_id());
	endforeach;

	$PATH = 'mdl/business/' . BUSINESS . '/app/app_xml_csv/';

	skelMdl::run($PATH . 'read/readmsc' , [ 'file'  => 'red' ,
	                                             'url'   => '' ,
	                                             'delay' => 10 ,
	                                             'run'   => 1 ]);