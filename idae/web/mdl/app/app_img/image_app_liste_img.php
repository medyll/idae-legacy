<?
	include_once($_SERVER['CONF_INC']);

	global $buildArr;
	global $IMG_SIZE_ARR;

	ini_set('display_errors',55);

	$table_value = (int)$_POST['table_value'];
	$table = $_POST['table'];
	$id = 'id' . $table;

	$nom = 'nom' . ucfirst($table);
	$APP = new App($table);
	//
	$APP_TABLE       = $APP->app_table_one;
	$arr = $APP->query_one(array( $id => $table_value ));
?>
<div class = "flex_v" >
	<div class = "please_hide relative" >
		<?=  skelMdl::cf_module('app/app/app_menu' , $_POST); ?>
	</div >
	<div class = "applink   relative" style = "overflow: auto;" >
		<div class = "uppercase titre_entete alignright" >
			<div  >
				<?= $arr[$nom] ?>
			</div >
		</div >
		<div class = "flex_h flex_wrap flex_align_top  " >
			<? foreach ($IMG_SIZE_ARR as $key => $value) { ?>
					<?
						if(empty($APP_TABLE['hasImage'.$key.'Scheme'])) continue;
						// nom de l'image
						$mongoImg = $table . '-' . $key . '-' . $table_value;
						$vars = array( 'table'     => $table ,
						               'table_value'     => $table_value ,
						               'codeTailleImage'     => $key,
						               'needResize'    => true   );
						$vars['show_info'] = true;
						//
					?>
					<div class = "margin_more padding_more"  >
						<?= skelMdl::cf_module('app/app_img/image_dyn' , $vars , $mongoImg); ?>
				</div >
			<? } ?>
		</div >
		<?
			if(!empty($APP_TABLE['hasImagePelemeleScheme'])):
				$pmvars = array( 'mongoSize'     => 'large' ,
				                 'mongoTag'      => $table ,
				                 'mongoId'       => $table_value ,
				                 'mongoName'     => niceUrl($nom) ,
				                 'mongoImg'      => $table . '-pelemele-large-' . $table_value ,
				                 'needResize'    => true ,
				                 'sizeImg'       => 650 ,
				                 'sizeHeightImg' => 430 ,
				                 'multiple'      => true ,
				                 'texteImg'      => 'pelemele ' . niceUrl($nom) );//

				?>
				<div class = "titre_entete uppercase fond_noir color_fond_noir relative" >
					Images Pele-mele
				</div >
				<a onClick = "ajaxMdl('app/app_img/app_img_upload','pele mele ','<?= http_build_query($pmvars) ?>')" >
					<i class = "fa fa-plus-circle" ></i > <?= idioma('Ajouter une image pele-mele') ?>
				</a >
				<?
				$reg = "/.*$table-pelemele-large-$table_value-*/";
				$db = $APP->plug_base('sitebase_image'); //$con->sitebase_image;
				$grid = $db->getGridFS();
				$download = $grid->find(array( 'filename' => new MongoRegex($reg) )); // instance of MongoGridFSFile
				$count = $download->count();
				$count ++;
				foreach ($download as $value) {
					$file     = $value->file;
					$mongoImg = $file['filename'];
					$vars     = array( 'mongoSize'     => 'large' ,
					                   'mongoTag'      => $table ,
					                   'mongoId'       => $table_value ,
					                   'mongoName'     => niceUrl($nom) ,
					                   'mongoImg'      => $mongoImg ,
					                   'needResize'    => true ,
					                   'sizeImg'       => 650 ,
					                   'sizeHeightImg' => 430 ,
					                   'texteImg'      => niceUrl($nom) );
					//
					//
					?>
					<div >
						<?= skelMdl::cf_module('app/app_img/image_dyn' , $vars , $mongoImg); ?>
					</div >
					<?
					//}
				}
			endif;
		?>
	</div >
</div >