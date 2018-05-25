(function( $ ) {
	'use strict';

	$('#delete-stores').on('click', function(e) {
		return confirm("Are you sure you want to delete all stores?");
	});

	// Create the defaults once
	var pluginName = "storeLocatorAdmin",
		defaults = {
			map_container: "#wordpress-store-locator-map-container",
			map_min_height: 300,
			earthRadi: {
				mi: 3963.1676,
				km: 6378.1,
				ft: 20925524.9,
				mt: 6378100,
				"in": 251106299,
				yd: 6975174.98,
				fa: 3487587.49,
				na: 3443.89849,
				ch: 317053.408,
				rd: 1268213.63,
				fr: 31705.3408
			},
		};

	// The actual plugin constructor
	function Plugin ( element, options ) {
		this.element = element;
		this.settings = $.extend( {}, defaults, options );
		this._defaults = defaults;

		this.settings.lat = $('#wordpress-store-locator-lat');
		this.settings.lng = $('#wordpress-store-locator-lng');
		
		this.settings.defaultLat = $(element).data('lat');
		this.settings.defaultLng = $(element).data('lng');

		this._name = pluginName;
		this.init();
	}

	// Avoid Plugin.prototype conflicts
	$.extend( Plugin.prototype, {
		init: function() {
			var that = this;
			this.window = $(window);
			this.documentHeight = $( document ).height();
			this.windowHeight = this.window.height();
			this.settings.mapDefaultZoom = parseInt(that.settings.mapDefaultZoom);
			this.templateCache = {};

			that.initStoreLocatorAdmin();
		},
		initStoreLocatorAdmin: function() {
			var that = this;

			that.initMap(function(){
				that.createMarker();  

			});
		},
		initMap: function(callback) {
			var mapContainer = $(this.settings.map_container);
		    var mapDefaultZoom = this.settings.mapDefaultZoom;
		    var mapDefaultLat = Number(this.settings.defaultLat);
		    var mapDefaultLng = Number(this.settings.defaultLng);

		    // Construct Map
		   	this.map = new google.maps.Map(mapContainer[0], {
				zoom: 12,
				center: new google.maps.LatLng(mapDefaultLat, mapDefaultLng),
				scrollwheel: false,
		    });
		   	this.geocoder = new google.maps.Geocoder();
		    callback();
		},
		createMarker: function() {
		   	var store = {};
		   	var that = this;

			store.map = this.map;
			store.position = new google.maps.LatLng(this.settings.defaultLat, this.settings.defaultLng);
			store.icon = this.settings.mapDefaultIcon;
			store.draggable = true;

		    that.marker = new google.maps.Marker(store);
		    that.marker.setMap(this.map);

			google.maps.event.addListener( that.marker, 'drag', function ( event ) {
				that.updateCoordinate( event.latLng );
			} );
			that.findAddress();
		},
		findAddress : function () {
			var that = this;

			$('input[name="wordpress_store_locator_address1"], input[name="wordpress_store_locator_address2"], input[name="wordpress_store_locator_zip"],' + 
			'input[name="wordpress_store_locator_city"], input[name="wordpress_store_locator_region"], select[name="wordpress_store_locator_country"]')
			.on('focusout', function(e)
			{
				that.geocodeAddress();
			});

			$('#wordpress-store-locator-get-position').on( 'click', function () {
				that.geocodeAddress();
				return false;
			} );
		},
		// Update coordinate to input field
		updateCoordinate: function ( latLng ) {
			var that = this;

			that.settings.lat.val(latLng.lat());
			that.settings.lng.val(latLng.lng());
		},
		// Find coordinates by address
		geocodeAddress: function () {

			var that = this;
			var address = '';
			console.log($('select[name="wordpress_store_locator_country"] option:checked').val());

			address += $('input[name="wordpress_store_locator_address1"]').val();
			address += ' ' + $('input[name="wordpress_store_locator_city"]').val();
			address += ' ' + $('input[name="wordpress_store_locator_zip"]').val();
			address += ' ' + $('select[name="wordpress_store_locator_country"] option:selected').val();

			if ( address ) {
				this.geocoder.geocode( {'address': address}, function ( results, status ) {
					if ( status === google.maps.GeocoderStatus.OK ) {
						that.map.setCenter( results[0].geometry.location );
						that.marker.setPosition( results[0].geometry.location );
						that.updateCoordinate( results[0].geometry.location );
					} else {
						console.log('Google Geocoder Status: ' + status);
					}
				} );
			} else {
				console.log('Address Empty');
			}
		},
		//////////////////////
		///Helper Functions///
		//////////////////////
		isEmpty: function(obj) {

		    if (obj == null)		return true;
		    if (obj.length > 0)		return false;
		    if (obj.length === 0)	return true;

		    for (var key in obj) {
		        if (hasOwnProperty.call(obj, key)) return false;
		    }

		    return true;
		},
		sprintf: function parse(str) {
		    var args = [].slice.call(arguments, 1),
		        i = 0;

		    return str.replace(/%s/g, function() {
		        return args[i++];
		    });
		},
		getCookie: function(cname) {
		    var name = cname + "=";
		    var ca = document.cookie.split(';');
		    for(var i=0; i<ca.length; i++) {
		        var c = ca[i];
		        while (c.charAt(0)==' ') c = c.substring(1);
		        if (c.indexOf(name) === 0) return c.substring(name.length, c.length);
		    }
		    return "";
		},
	} );

	// Constructor wrapper
	$.fn[ pluginName ] = function( options ) {
		return this.each( function() {
			if ( !$.data( this, "plugin_" + pluginName ) ) {
				$.data( this, "plugin_" +
					pluginName, new Plugin( this, options ) );
			}
		} );
	};

	$(document).ready(function() {

		$( ".wordpress-store-locator-map" ).storeLocatorAdmin();

	} );


})( jQuery );