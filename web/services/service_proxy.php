<?php
   include_once ($_SERVER['CONF_INC']);
   $_POST = array_merge($_GET, $_POST);
   $mdl = $_POST['mdl'];
   $vars['moduleTag']="none";
   $result = skelMdl::cf_module($mdl,$vars);
   skelMdl::send_cmd('app_mdl_ready',array(''=>$result));
?>