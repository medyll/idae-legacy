<?
	include_once($_SERVER['CONF_INC']);

	$_POST['table_value'] = (int)$_POST['idville'];
	$_POST['table']       = 'ville';

	$table      = $_POST['table'];
	$Table      = ucfirst($table);
	$name_id    = "id$table";
	$value_id   = (int)$_POST['table_value'];
	$field_name_table = "nom$Table";

	$APP = new App($table);

	$uniqid  = 't' . uniqid();
	$idville = (int)$_POST['idville'];

	$lat_field = "latitude$Table";
	$lng_field = "longitude$Table";
	//
	$arrV = $APP->findOne([$name_id => (int)$value_id]);

	$zoom = 5;
	if (empty($arrV[$lat_field]) || empty($arrV[$lng_field])) {
		$arrV[$lat_field] = $arrV[$lng_field] = 0;
		$zoom             = 2;
	}
?>
<style>
	#map_canvas {
		display : block !important;
		height  : 450px;
	}
</style>
<div style="width:750px;position:relative;">
	<div act_defer
	     mdl="app/app/app_menu"
	     vars="act_from=map&table=<?= $table ?>&table_value=<?= $value_id ?>"></div>
	<div class="padding">
		<form id="<?= $uniqid ?>"
		      onsubmit="ajaxFormValidation(this);return false;"
		      action="<?= ACTIONMDL ?>app/actions.php"
		      auto_close="auto_close">
			<input type="hidden"
			       name="F_action"
			       value="app_update"/>
			<input type="hidden" name="table" value="<?= $table ?>">
			<input type="hidden" name="table_value" value="<?= $value_id ?>">
			<input id="gps<?=$Table?>_lat" type="hidden" name="vars[gps<?=$Table?>][lat]" value="<?= $arrV[$lat_field] ?>">
			<input id="gps<?=$Table?>_lng" type="hidden" name="vars[gps<?=$Table?>][lng]" value="<?= $arrV[$lng_field] ?>">


			<input id="gps<?=$Table?>_lng" type="hidden" name="vars[gps<?=$Table?>][type]" value="Point">
			<input id="gps<?=$Table?>_lng" type="hidden" name="vars[gps<?=$Table?>][coords][]" value="<?= $arrV[$lat_field] ?>">
			<input id="gps<?=$Table?>_lng" type="hidden" name="vars[gps<?=$Table?>][coords][]" value="<?= $arrV[$lng_field] ?>">


			<table class="table_form">
				<tr>
					<td>
						<?= idioma('Nom de ') . $APP->nomAppscheme ?>
					</td>
					<td colspan="3">
						<input id="z<?= $uniqid ?>"
						       type="text"
						       class="inputLarge"
						       value="<?= $arrV[$field_name_table] . ' ' . $arrV["nomPays"] ?>"/>
					</td>
					<td>
						<button type="button"
						        class="validButton"
						        value="Situer"
						        onclick="searchMap($(z<?= $uniqid ?>).value)">
							<i class="fa fa-map-marker"></i> <?= idioma('Situer') ?></button>
					</td>
				</tr>
				<tr>
					<td>
						<?= idioma('latitude') ?>
					</td>
					<td>
						<input type="text"
						       class="inputSmall"
						       value="<?= $arrV[$lat_field] ?>"
						       name="vars[<?= $lat_field ?>]"
						       id="<?= $lat_field ?><?= $uniqid ?>">
					</td>
					<td class="label">
						<?= idioma('longitude') ?>
					</td>
					<td>
						<input type="text"
						       class="inputSmall"
						       value="<?= $arrV[$lng_field] ?>"
						       name="vars[<?= $lng_field ?>]"
						       id="<?= $lng_field ?><?= $uniqid ?>">
					</td>
					<td></td>
				</tr>
			</table>
			&nbsp;&nbsp;
			<div id="map_canvas"
			     class="relative flowDown"
			     style="width:100%;position:relative;overflow:hidden"></div>
			<div class="buttonZone">
				<input type="submit"
				       value="<?= idioma('Valider') ?>"/>
				<input type="button"
				       class="cancelClose"
				       value="<?= idioma('Fermer') ?>">
			</div>
		</form>
	</div>
	<div class="spacer"></div>
</div>
<script type="text/javascript">
	initializeSearch = function () {

	}
	initializeMap = function () {
		var geocoder  = new google.maps.Geocoder ();
		var markers   = new Array ();
		var i         = 0;
		var originlat = new google.maps.LatLng (<?=$arrV[$lat_field]?>, <?=$arrV[$lng_field]?>);

		var mapOptions = {
			zoom      : 9,
			center    : new google.maps.LatLng (<?=$arrV[$lat_field]?>, <?=$arrV[$lng_field]?>),
			mapTypeId : google.maps.MapTypeId.ROADMAP
		}
		var map        = new google.maps.Map ($ ("map_canvas"), mapOptions);
		var marker     = new google.maps.Marker ({
			position : originlat,
			map      : map
		});
		markers.push (marker);

		//
		google.maps.event.addListener (map, 'click', function (event) {

			$ ('<?=$lat_field?><?=$uniqid?>').value = event.latLng.lat ();
			$ ('<?=$lng_field?><?=$uniqid?>').value = event.latLng.lng ();

			$ ('gps<?=$Table?>_lat').value = event.latLng.lat ();
			$ ('gps<?=$Table?>_lng').value = event.latLng.lng ();



			placeMarker (event.latLng);
		}.bind (this));

		placeMarker = function (location) {
			markers.each (function (node, index) {
				markers[index].setMap (null);
			})
			marker = new google.maps.Marker ({
				position : location,
				map      : map
			});
			markers.push (marker);
			map.setCenter (location);
		}
		searchMap   = function (value) {
			geocoder.geocode (
				{ 'address' : value },
				function (results, status) {
					if ( status == google.maps.GeocoderStatus.OK ) {

						var loc                                 = results[0].geometry.location;
						placeMarker (loc)
						$ ('<?=$lat_field?><?=$uniqid?>').value = loc.lat ();
						$ ('<?=$lng_field?><?=$uniqid?>').value = loc.lng ();

						$ ('gps<?=$Table?>_lat').value = loc.lat ();
						$ ('gps<?=$Table?>_lng').value = loc.lng ();

					}
					else {
						alert ("Non trouvé: " + status);
					}
				}
			);
		};
	}
	loadScriptMap = function () {
		if ( $ ('script_map') ) {
			initializeMap ();
			initializeSearch ();
			return;
		}
		var script  = document.createElement ("script");
		script.type = "text/javascript";
		script.id   = 'script_map';
		script.src  = "http://maps.googleapis.com/maps/api/js?sensor=true&callback=initializeMap";
		document.body.appendChild (script);
	}
	loadScriptMap ();
</script>
