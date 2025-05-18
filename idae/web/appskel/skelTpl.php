<?   
class skelTpl
{ 
	function _construct(){ 
		 
	} 
	static function cf_template($tpl,$datas = array(),$block=''){
		$a = get_defined_constants(true);
		//var_dump($a);
		$datas += $a['user'];

		$tempPost = $_POST; 
		if (trim($tpl=='')){return '';}		 
		ob_start();   
	 //	echo '<tpl tpl="$tpl.$block"></tpl>';
		if(file_exists(APPTPL.'/'.$tpl.'.html')){   
				$fxlt = new fxl_template(APPTPL.'/'.$tpl.'.html');
				if(!empty($block)) {
					if(strpos($block,'/')===false) {
						$fxlt_block = $fxlt->get_block($block); 
						$fxlt_block->assign($datas);
						$fxlt_block->display();
					}else{ 
						$arrBloc = explode('/',$block); 
						$fxlt_row = $fxlt->get_block($arrBloc[0]);
						$fxlt_cell = $fxlt_row->get_block($arrBloc[1]);
						$fxlt_cell->assign($datas);
						$fxlt_cell->display(); 
						}
				}else{
				$fxlt->assign($datas);
				$fxlt->display();
				}
		}else{
			echo "missing template ".APPTPL.'/'.$tpl.'.html';
		}	 
 		$final = ob_get_contents();
		ob_end_clean();  
 		flush();
		ob_flush(); 
		
		return stripslashes($final);
	}
}  ?>