<?
	include_once($_SERVER['CONF_INC']);

	//$_POST['table_value'] = (int)$_POST['idville'];
	// $_POST['table']       = 'ville';

	$table            = $_POST['table'];
	$Table            = ucfirst($table);
	$name_id          = "id$table";
	$value_id         = (int)$_POST['table_value'];
	$field_name_table = "nom$Table";

	$APP = new App($table);

	$uniqid       = 't' . uniqid();
	$map_canvasid = "id$uniqid";

	$lat_field = "latitude$Table";
	$lng_field = "longitude$Table";
	$gps_field = "gps$Table";

	$ARR = $APP->findOne([$name_id => (int)$value_id]);
	// vardump($ARR);
	if (empty($ARR[$gps_field])) $ARR[$gps_field] = "[]";
	$zoom = 5;
	if (empty($ARR[$lat_field]) || empty($ARR[$lng_field])) {
		$ARR[$lat_field] = $ARR[$lng_field] = 0;
		$zoom            = 2;
	}

	$input_default_value = (empty($ARR["adresse$Table"])) ? $ARR[$field_name_table] : $ARR["adresse$Table"] . ' ' . $ARR["adresse2$Table"] . ' ' . $ARR["ville$Table"];
?>
<style>
	<?='#'.$map_canvasid?>
	{
		display : block !important
	;
	}
</style>
<div class="flex_v blanc" style="width:100%;height:100%;position:relative;">
	<div act_defer mdl="app/app/app_menu" vars="act_from=map&table=<?= $table ?>&table_value=<?= $value_id ?>"></div>
	<form id="<?= $uniqid ?>" onsubmit="ajaxFormValidation(this);return false;" action="<?= ACTIONMDL ?>app/actions.php" method="post">
		<div class="padding">
			<input type="hidden"
			       name="F_action"
			       value="app_update"/>
			<input type="hidden"
			       class="inputSmall"
			       value="<?= $ARR[$lat_field] ?>"
			       name="vars[<?= $lat_field ?>]"
			       id="<?= $lat_field ?><?= $uniqid ?>">

			<input type="hidden"
			       class="inputSmall"
			       value="<?= $ARR[$lng_field] ?>"
			       name="vars[<?= $lng_field ?>]"
			       id="<?= $lng_field ?><?= $uniqid ?>">
			<input type="hidden" name="table" value="<?= $table ?>">
			<input type="hidden" name="table_value" value="<?= $value_id ?>">
			<input type="hidden" name="vars[dezdezdez]" value="dfezdezdez">
			<input type="hidden" name="vars[<?= $gps_field ?>][type]" value="Polygon">
			<input id="gps<?= $Table ?>_coordinates" type="hidden" name="vars[<?= $gps_field ?>]" value="<?= $ARR[$gps_field]?>">
			<div class="flex_h flex_align_middle">
				<div class="flex_main">
					<table class="table_form">
						<tr>
							<td>
								<?= $APP->nomAppscheme ?>
							</td>
							<td colspan="3">
								<input id="z<?= $uniqid ?>"
								       type="text"
								       class="inputLarge"
								       value="<?= $input_default_value ?>"/>
							</td>
							<td>
								<button type="button"
								        class="validButton"
								        value="Situer"
								        onclick="searchMapZone($(z<?= $uniqid ?>).value)">
									<i class="fa fa-map-marker"></i> <?= idioma('Situer') ?></button>
							</td>
						</tr>




					</table>
				</div>
				<div class="padding aligncenter fond_noir color_fond_noir">
					<input type="submit" value="<?= idioma('Valider') ?>">
				</div>
			</div>
		</div>
	</form>
	<div class="relative flex_main ">
		<button style="display:none; " id="delete-button"><i class="fa fa-ban textered"></i> annuler</button>
		<div class="flex_v">
			<div class="flex_main borderb" id="<?= $map_canvasid ?>" style="width:100%;position:relative;overflow:hidden">
			</div>
		</div>
	</div>
</div>
<style>
	#delete-button {
		position    : absolute;
		top         : 5px;
		margin-left : 50%;
		left        : 10px;
		z-index     : 5000;
	}
</style>
<script type="text/javascript">
	initMapZone = function (var_lat, var_long) {
		var selectedShape;
		var geocoder  = new google.maps.Geocoder ();
		var markers   = [];
		var originlat = new google.maps.LatLng (<?=$ARR[$lat_field]?>, <?=$ARR[$lng_field]?>);

		var mapOptions = {
			zoom             : 12,
			disableDefaultUI : true,
			center           : new google.maps.LatLng (<?=$ARR[$lat_field]?>, <?=$ARR[$lng_field]?>),
			mapTypeId        : google.maps.MapTypeId.ROADMAP
		}
		var map        = new google.maps.Map ($ ("<?=$map_canvasid?>"), mapOptions);
		var marker     = new google.maps.Marker ({
			position : originlat,
			map      : map
		});
		markers.push (marker);

		var drawingManager = new google.maps.drawing.DrawingManager ({
			drawingMode           : google.maps.drawing.OverlayType.MARKER,
			drawingControl        : true,
			drawingControlOptions : {
				position     : google.maps.ControlPosition.TOP_CENTER,
				drawingModes : ['polygon']
			},
			// markerOptions: {icon: 'https://developers.google.com/maps/documentation/javascript/examples/full/images/beachflag.png'},
			polygonOptions        : {
				fillColor    : 'rgba(255,255,255,0.5)',
				fillOpacity  : 1,
				strokeColor  : 'rgba(0,0,0,0.8)',
				strokeWeight : 3,
				draggable    : true,
				editable     : true,
				zIndex       : 1
			}
		});

		drawingManager.setMap (map);
		//loadPolygons ();

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

		searchMapZone       = function (value) {
			geocoder.geocode (
				{ 'address' : value },
				function (results, status) {
					if ( status == google.maps.GeocoderStatus.OK ) {

						var loc                                 = results[0].geometry.location;
						placeMarker (loc);
						$ ('<?=$lat_field?><?=$uniqid?>').value = loc.lat ();
						$ ('<?=$lng_field?><?=$uniqid?>').value = loc.lng ();

						$ ('gps<?=$Table?>_lat').value = loc.lat ();
						$ ('gps<?=$Table?>_lng').value = loc.lng ();

					}
					else {

					}
				}
			);
		};
		setSelection        = function (shape) {
			clearSelection ();
			selectedShape = shape;
			shape.setEditable (true);
		}
		clearSelection      = function () {
			if ( selectedShape ) {
				selectedShape.setEditable (false);
				selectedShape = null;
			}
		}
		deleteSelectedShape = function () {
			if ( selectedShape ) {
				selectedShape.setMap (null);
				drawingManager.setOptions ({
					drawingControl : true
				});
			}
			document.getElementById ('delete-button').style.display = 'none';
		}
		saveShape           = function () {
			var collection = [];
			//for (var k in this.collection) {
			//	var shape = this.collection[k],
			var types = google.maps.drawing.OverlayType;
			switch (selectedShape.type) {
				case types.POLYGON:
					allpath = []

					var len     = selectedShape.getPath ().getLength ();
					var htmlStr = "";
					for (var i                                             = 0 ; i < len ; i++) {
						allpath.push ([ selectedShape.getPath ().getAt (i).lng (),selectedShape.getPath ().getAt (i).lat ()]);
					}
					allpath.push(allpath[0])
					collection.push ({
						type    : selectedShape.type,
						path    : google.maps.geometry.encoding.encodePath (selectedShape.getPath ()),
						allpath : allpath
					});
					break;
				default:
				// alert ('implement a storage-method for ' + selectedShape.type)
			}
			//}
			//collection is the result
			document.getElementById ('gps<?= $Table ?>_coordinates').value = JSON.stringify (allpath)
		}

		loadPolygons = function () {
			var data = JSON.parse (document.getElementById ('gps<?= $Table ?>_coordinates').value);
			// console.log(data[0])
			var gg_data = data.map( (obj) =>{
				var rObj = {lat:eval(obj[1]),lng:obj[0]};
				return rObj;
			})

			if(data[0] && data[0][0]){
				map.setCenter({lat: data[0][1], lng: data[0][0]});
				map.setZoom(13);
			}
			placeMarker(map.getCenter());
			map.data.add({geometry: new google.maps.Data.Polygon([gg_data])});

		};

		google.maps.event.addListener (drawingManager, 'overlaycomplete', function (e) {
			if ( e.type != google.maps.drawing.OverlayType.MARKER ) {
				// Switch back to non-drawing mode after drawing a shape.
				drawingManager.setDrawingMode (null);
				// To hide:
				drawingManager.setOptions ({
					drawingControl : false
				});
				document.getElementById ('delete-button').style.display = '';
				// Add an event listener that selects the newly-drawn shape when the user
				// mouses down on it.
				var newShape  = e.overlay;
				newShape.type = e.type;

				google.maps.event.addListener (newShape, 'click', function () {
					setSelection (newShape);
				});
				setSelection (newShape);
				saveShape ();
				/*var len     = newShape.getPath ().getLength ();
				 var htmlStr = "";
				 for (var i = 0 ; i < len ; i++) {
				 console.log (newShape.getPath ().getAt (i).toUrlValue (7));
				 }*/

			}
		});
		loadPolygons();
		google.maps.event.addListener (drawingManager, 'drawingmode_changed', clearSelection);
		google.maps.event.addListener (map, 'click', clearSelection);
		google.maps.event.addDomListener (document.getElementById ('delete-button'), 'click', deleteSelectedShape);

	};

	(function () {
		if ( $ ('script_map') ) {
			initMapZone ();
			return;
		}
		var script  = document.createElement ("script");
		script.type = "text/javascript";
		script.id   = 'script_map';
		script.src  = "http://maps.googleapis.com/maps/api/js?key=AIzaSyBtPDWqnwnxO5-9Ic2EOUXbbGdRJTVPSJs&libraries=drawing&callback=initMapZone";
		document.body.appendChild (script);
	}) ()
</script>
