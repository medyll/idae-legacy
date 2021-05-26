<?
include_once($_SERVER['CONF_INC']);  
$time=time(); 
$uniqid = uniqid(); 
$mail_tmp = empty($_POST['mail_tmp'])? 'mail_tmp'.uniqid() : $_POST['mail_tmp']; 

$idagent = (int)$_SESSION['idagent']; 
 
$arrA  	= 	skelMongo::connect('agent','sitebase_base')->findOne(array('idagent'=>$idagent)); 
$rsA    =   skelMongo::connect('agent','sitebase_base')->find(array('estActifAgent'=>1)); 
//
$selectA = fonctionsProduction::getSelectMongo('emailFrom',$rsA,'idagent','prenomAgent',$idagent);

$insert= $after =''; 
if(!empty($_POST['uniqid'])):
	$uniqid = 	$_POST['uniqid'];
	$test 	=	skelMongo::connect('email','sitebase_email')->findOne(array('uniqid'=>$uniqid)); 
	$insert	=	$test['html'];
endif; 
if(!empty($_POST['idnewsletter'])):
    $idnewsletter =   (int)$_POST['idnewsletter'];
    $test   =   skelMongo::connect('newsletter','sitebase_newsletter')->findOne(array('idnewsletter'=>$idnewsletter)); 
    $after =   $test['html_newsletter'];
endif; 
$subject 	= 	(empty($_POST['subject']))? '' : $_POST['subject'] ; 
$sign 		= 	'<br><br><br>Cordialement,<br>'.$arrA['prenomAgent'].' '.$arrA['nomAgent'].'<br>'.$arrA['emailAgent'];?>

<div style="width:950px">
  <div class="ededed">
    <form    id="form<?=$uniqid?>" action="mdl/mail/actions.php" onsubmit="ajaxFormValidation(this);return false" auto_close="auto_close" >
      <input type="hidden" name="F_action" value="sendMail" />
      <input type="hidden" name="mail_tmp" value="<?=$mail_tmp?>" />
      <input type="hidden" name="afterAction[app/app_mail/app_mail_send]" value="cloddse" />
      <input type="hidden" class="inputMedium" name="emailFrom" value="<?=$arrAgent['emailAgent']?>" />
      <input type="hidden" class="inputMedium" name="emailFromName" value="<?=$arrAgent['prenomAgent']?> <?=$arrAgent['nomAgent']?>" />
      <div class="titre_entete fond_noir color_fond_noir borderb">
        <div  class="table" style="width:100%">
          <div class="cell">
            <li class="fa fa-envelope"></li>
            &nbsp;Envoyer un mail</div>
          <div class="cell" style="width:150px;">
             <?=$selectA?>
          </div>
          <div class="cell aligncenter " style="width:130px;">
            <button  style="width:120px;" type="submit" class="cursor" onclick="ajaxFormValidation($('form<?=$uniqid?>'));"  >
            <li class="fa fa-envelope"></li>
            Envoyer</button>
          </div>
        </div>
      </div>
      <div id="drag<?=$uniqid?>" class="margin relative padding blanc">
        <table style="width:100%; table-layout:auto" class="tablemiddle">
          <tr>
            <td style="width:80px;"><label>A ...</label></td>
            <td id="email<?=$uniqid?>" style="vertical-align:middle;"><div class="fauxInput" >

               <input datalist_input_name="emailInfo" datalist="app/app_mail/app_mail_contact_select" populate  name="email" class="inline" />

                <div   id="contact<?=$uniqid?>" class="inline" >
                  <?=skelMdl::cf_module('app/app_mail/app_mail_compose_contact',array('mail_tmp'=>$mail_tmp),$mail_tmp)?>
                </div>
              </div></td>
          </tr>
          <tr>
            <td style="width:80px;"><label>Cc ...</label></td>
            <td id="email_cc<?=$uniqid?>" style="vertical-align:middle;"><div class="fauxInput" >
				<input datalist_input_name="emailInfoCC" datalist="app/app_mail/app_mail_contact_select" populate  name="email" class="inline" />
                <div id="contact_cc<?=$uniqid?>" class="inline">
                  <?=skelMdl::cf_module('app/app_mail/app_mail_compose_contact_cc',array('mail_tmp'=>$mail_tmp),$mail_tmp)?>
                </div>
              </div></td>
          </tr>
          <tr>
            <td><label>Objet</label></td>
            <td ><input type="text"  style="width:100%" name="objet" value="<?=$subject?>" required="required" /></td>
          </tr>
          <tr>
            <td><label>Fichiers</label></td>
            <td ><div class="inline applink borderr relative" style="overflow:hidden;vertical-align:middle;">
                <li class="fa fa-files-o"></li>
                <a>Ajouter</a>
                <input class="cursor" type="file" multiple="multiple" style="opacity:0;position:absolute;left:0;top:0;" />
              </div>
              <div class="inline" id="attach<?=$uniqid?>">
                <?=skelMdl::cf_module('app/app_mail/app_mail_compose_attach',$_POST+array('scope'=>'mail_tmp','mail_tmp'=>$mail_tmp),$mail_tmp)?>
              </div>
              <div id="listing<?=$uniqid?>" ></div></td>
          </tr>
        </table>
      </div>
      <div class="margin relative padding blanc">
        <table style="width:100%; table-layout:auto" class="tablemiddle">
          <tr>
            <td colspan="2"><textarea ext_mce_textarea class="required" style="width:100%;height:400px" name="texteMail" ><?=$insert?><?=$sign?><?=$after?>
</textarea></td>
          </tr>
        </table>
      </div>
    </form>
  </div>
</div>
<form id="formdrag<?=$uniqid?>" action="mdl/mail/actions.php" onsubmit=";return false"  >
  <input type="hidden" name="F_action" value="addFichier" />
  <input type="hidden" name="mail_tmp" value="<?=$mail_tmp?>" />
  <input type="hidden" name="reloadModule[app/app_mail/app_mail_compose_attach]" value="<?=$mail_tmp?>" />
</form>
<script>

	$('attach<?=$uniqid?>').on('click','a[filename]',function(event,node){
		filename = $(node).readAttribute('filename');
		ajaxValidation('deleteAttach','mdl/mail/','scope=mail_tmp&mail_tmp=<?=$mail_tmp?>&idagent=<?=$_SESSION['idagent']?>&filename='+filename);
		});
	$('attach<?=$uniqid?>').on('click','a[deleteFichier]',function(event,node){
		filename = $(node).readAttribute('deleteFichier');
		ajaxValidation('deleteFichier','mdl/mail/','scope=mail_tmp&mail_tmp=<?=$mail_tmp?>&idagent=<?=$_SESSION['idagent']?>&filename='+filename);
		});
	$('contact<?=$uniqid?>').on('click','a[email]',function(event,node){
		email = $(node).readAttribute('email');
		ajaxValidation('deleteContact','mdl/mail/','scope=mail_tmp&mail_tmp=<?=$mail_tmp?>&idagent=<?=$_SESSION['idagent']?>&email='+email+'&reloadModule[app/app_mail/app_mail_compose_contact]=<?=$mail_tmp?>');
		});
	$('contact_cc<?=$uniqid?>').on('click','a[email]',function(event,node){
		email = $(node).readAttribute('email');
		ajaxValidation('deleteContactCC','mdl/mail/','scope=mail_tmp&mail_tmp=<?=$mail_tmp?>&idagent=<?=$_SESSION['idagent']?>&email='+email+'&reloadModule[app/app_mail/app_mail_compose_contact_cc]=<?=$mail_tmp?>');
		});
</script> 
<script>
var input_contact 		= $(document.body.querySelector('[datalist_input_name=emailInfo]'));
var input_contact_cc 	= $(document.body.querySelector('[datalist_input_name=emailInfoCC]'));
	// Add contact
$(input_contact).observe('dom:act_change',function(event){
    var email	=	event.memo.value;
	var meta	=	event.memo.meta || 'meta[nom]='+email+'&meta[email]='+email;
    ajaxValidation('addContact','mdl/mail/','mail_tmp=<?=$mail_tmp?>&email='+email+'&reloadModule[app/app_mail/app_mail_compose_contact]=<?=$mail_tmp?>&'+meta);
	$(input_contact).value = '';
}.bind(this))
	// addcontact CC
$(input_contact_cc).observe('dom:act_change',function(event){
	var email	=	event.memo.value;
	var meta	=	event.memo.meta || 'meta[nom]='+email+'&meta[email]='+email;
	ajaxValidation('addContactCC','mdl/mail/','mail_tmp=<?=$mail_tmp?>&email='+email+'&reloadModule[app/app_mail/app_mail_compose_contact]=<?=$mail_tmp?>&'+meta);
	input_contact_cc.value = '';
}.bind(this))


	
// mce_area("textarea#texteMail<?=$time?>");
//
new myddeAttach($('drag<?=$uniqid?>'),{form:'formdrag<?=$uniqid?>',autoSubmit:true}); 
</script> 
