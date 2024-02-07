
function initialize() {
	var myLatlng = new google.maps.LatLng(7.873053999999999,80.77179699999999);
	var myOptions = {
		zoom: 8,
		center: myLatlng,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	}

    console.log(document.getElementById('map-canvas'));

	var map = new google.maps.Map(document.getElementById('map-canvas'), myOptions);

	// Create the search box and link it to the UI element.
	var input = document.getElementById('pac-input');
	var searchBox = new google.maps.places.SearchBox(input);
	// map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

	var marker = new google.maps.Marker({
		draggable:true,
		position: myLatlng,
		map: map
	});

	// Bias the SearchBox results towards current map's viewport.
	map.addListener('bounds_changed', function() {
		searchBox.setBounds(map.getBounds());
	});

	google.maps.event.addListener(marker, 'dragend', function (event) {
		var poiLat = this.getPosition().lat();
		var poiLon = this.getPosition().lng();
		$('#latitude').val(poiLat);
		$('#longitude').val(poiLon);
	});

	var markers = [];
	// Listen for the event fired when the user selects a prediction and retrieve
	// more details for that place.
	searchBox.addListener('places_changed', function() {
		var places = searchBox.getPlaces();

		if (places.length == 0) {
			return;
		}

		// Clear out the old markers.
		markers.forEach(function(marker) {
			marker.setMap(null);
		});
		markers = [];

		// For each place, get the icon, name and location.
		var bounds = new google.maps.LatLngBounds();
		places.forEach(function(place) {
			if (!place.geometry) {
				console.log("Returned place contains no geometry");
				return;
			}

			var icon = {
				url: place.icon,
				size: new google.maps.Size(71, 71),
				origin: new google.maps.Point(0, 0),
				anchor: new google.maps.Point(17, 34),
				scaledSize: new google.maps.Size(25, 25)
			};

			// Create a marker for each place.
			marker.setPosition( place.geometry.location );
			map.panTo( place.geometry.location );
			$('#latitude').val(place.geometry.location.lat());
			$('#longitude').val(place.geometry.location.lng());

			if (place.geometry.viewport) {
			// Only geocodes have viewport.
				bounds.union(place.geometry.viewport);
			} else {
				bounds.extend(place.geometry.location);
			}
		});

		map.fitBounds(bounds);
	});
}

