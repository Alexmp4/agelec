(function( $ ) {
	'use strict';

	// Create the defaults once
	var pluginName = "storeLocatorSingle",
		defaults = {
			map_container: "#store_locator_single_map",
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

		this.settings.lat = $(element).data('lat');
		this.settings.lng = $(element).data('lng');

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

			that.initStoreLocatorSingle();
		},
		initStoreLocatorSingle: function() {
			var that = this;

			that.initMap(function(){
				that.createMarker();  
			});
		},
		initMap: function(callback) {
			var mapContainer = $(this.settings.map_container);
		    var mapDefaultZoom = this.settings.mapDefaultZoom;
		    var mapDefaultType = this.settings.mapDefaultType;
		    var mapDefaultLat = Number(this.settings.lat);
		    var mapDefaultLng = Number(this.settings.lng);

		    var mapStyling = this.settings.mapStyling;
		    if( !this.isEmpty(mapStyling) ) {
		    	mapStyling = JSON.parse(mapStyling);
		    } else {
		    	mapStyling = "";
		    }

		    // Construct Map
		   	this.map = new google.maps.Map(mapContainer[0], {
				zoom: mapDefaultZoom,
				center: new google.maps.LatLng(mapDefaultLat, mapDefaultLng),
				mapTypeId: google.maps.MapTypeId[mapDefaultType],
				scrollwheel: false,
				styles: mapStyling
		    });

		    callback();
		},
		createMarker: function() {
		   	var marker;
		   	var store = {};

			store.map = this.map;
			store.position = new google.maps.LatLng(this.settings.lat, this.settings.lng);
			store.icon = this.settings.mapDefaultIcon;

		    marker = new google.maps.Marker(store);
		    marker.setMap(this.map);
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

		$( "#store_locator_single_map" ).storeLocatorSingle( 
			store_locator_options
		);

	} );

})( jQuery );