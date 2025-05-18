<?
	include_once($_SERVER['CONF_INC']);

	$idtransport = (int)$_POST['idtransport'];
	$table = 'transport';
	$table_value = (int)$_POST['idtransport'];
	$id = 'id'.$table;
	$APP = new App('transport');

	$arr = $APP->query_one(array( $id => (int)$table_value ));
?>

<div class = "relative" >
	<?= skelMdl::cf_module('app/app_menu' , array('table'=>'transport','table_value'=>$idtransport)); ?>
</div >

<form action = "<?= ACTIONMDL ?>app/actions.php" onsubmit = "ajaxFormValidation(this);return false;" auto_close = "auto_close" >
	<input type = "hidden" name = "F_action" value = "app_update" />
	<input type = "hidden" name = "table" value = "<?= $table ?>" />
	<input type = "hidden" name = "table_value" value = "<?= $table_value ?>" />
	<input type = "hidden" name = "scope" value = "<?= $id ?>" />
	<input type = "hidden" name = "<?= $id ?>" value = "<?= $table_value ?>" />
<table class = "tabletop" >
	<tr >
		<td ><label >
				<?= idioma("date de mise en service") ?>
			</label ></td >
		<td ><input name = "vars[dateServiceTransport]" type = "text" value = "<?= date_fr($arr['dateServiceTransport']) ?>" /></td >
	</tr >
	<tr >
		<td ><label >
				<?= idioma("Passagers") ?> nb
			</label ></td >
		<td ><input name = "vars[passagerTransport]" type = "text" value = "<?= $arr['passagerTransport'] ?>" /></td >
	</tr >
	<tr >
		<td ><label >
				<?= idioma("Equipage") ?> nb
			</label ></td >
		<td ><input name = "vars[equipageTransport]" type = "text" value = "<?= $arr['equipageTransport'] ?>" /></td >
	</tr >
	<tr >
		<td ><label >
				<?= idioma("Tonnage") ?>
			</label ></td >
		<td ><input name = "vars[tonnageTransport]" type = "text" value = "<?= $arr['tonnageTransport'] ?>" /></td >
	</tr >
	<tr >
		<td ><label >
				<?= idioma("Largeur") ?>
			</label ></td >
		<td ><input name = "vars[largeurTransport]" type = "text" value = "<?= $arr['largeurTransport'] ?>" /></td >
	</tr >
	<tr >
		<td ><label >
				<?= idioma("Longueur") ?>
			</label ></td >
		<td ><input name = "vars[longueurTransport]" type = "text" value = "<?= $arr['longueurTransport'] ?>" /></td >
	</tr >
	<tr >
		<td ><label >
				<?= idioma("Vitesse") ?> noeuds
			</label ></td >
		<td ><input name = "vars[vitesseTransport]" type = "text" value = "<?= $arr['vitesseTransport'] ?>" /></td >
	</tr >
</table >
<div class = "buttonZone" >
	<input type = "submit" value = "Valider" >
	<input type = "reset" value = "Annuler" class = "cancelClose" >
</div ></form>
