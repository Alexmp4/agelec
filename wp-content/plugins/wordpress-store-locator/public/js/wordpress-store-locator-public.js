(function( $ ) {
	'use strict';

	// Create the defaults once
	var pluginName = "storeLocator",
		defaults = {
			store_locator: '#store_locator',
			store_modal: "#store_modal",
			store_modal_button: "#store_modal_button",
			store_modal_close: "#store_modal_close",
			map_container: "#store_locator_map",
			map_min_height: 300,
			store_locator_sidebar: '#store_locator_sidebar',
			store_locator_search_box: '#store_locator_search_box',
			result_list: "#store_locator_result_list",
			store_locator_address_field: '#store_locator_address_field',
			store_locator_find_stores_button: '#store_locator_find_stores_button',
			store_locator_loading: '#store_locator_loading',
			store_locator_filter_radius: '#store_locator_filter_radius',
			store_locator_filter_categories: '#store_locator_filter_categories',
			store_locator_filter: '#store_locator_filter',
			store_locator_filter_active_filter: '#store_locator_filter_active_filter',
			store_locator_filter_open_close: '#store_locator_filter_open_close',
			store_locator_filter_content: '#store_locator_filter_content',
			store_locator_filter_checkbox: '.store_locator_filter_checkbox',

			store_locator_form_customer_address: 'input[name="store_locator_form_customer_address"]',
			store_locator_form_store_select: 'select[name="store_locator_form_store_select"]',

			store_locator_embedded_search: '#store_locator_embedded_search',

			store_locator_get_my_position: '#store_locator_get_my_position',
			store_locator_get_all_stores: '#store_locator_get_all_stores',
			store_locator_dragged_button: '#store_locator_dragged_button',
			store_locator_category_icon: '',
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

		this._name = pluginName;
		this.init();
	}

	// Avoid Plugin.prototype conflicts
	$.extend( Plugin.prototype, {
		init: function() {
			var that = this;
			this.window = $(window);
			this.currentURL = window.location.href.split('?')[0];
			this.documentHeight = $( document ).height();
			this.windowHeight = this.window.height();
			this.settings.mapDefaultZoom = parseInt(that.settings.mapDefaultZoom);
			this.templateCache = {};
			this.markers = [];
			this.ownMarker = {};
			this.radiusCircle = {};
			this.categories = {};
			this.filter = {};
			this.address = "";

			this.geocoder = new google.maps.Geocoder();

			if(!this.isEmpty($(this.settings.store_locator))){
				this.setResultListMaxHeight();

				// Check if we have a Modal Button (Product Page)
				if(!this.isEmpty($(this.settings.store_modal_button))){
					this.initModal(function(){
						that.initStoreLocator();
						that.setResultListMaxHeight();
					});
				} else {
					$(this.settings.store_modal_close).hide();
					that.initStoreLocator();
				}
			}
			if(!this.isEmpty($(this.settings.store_locator_form_store_select))){
				that.initForm();
			}
			if(!this.isEmpty($(this.settings.store_locator_embedded_search))){
				that.initEmbeddedSearch();
			}

		},
		setResultListMaxHeight: function() {
			var resultList = $(this.settings.result_list);
			var store_locator_sidebar = $(this.settings.store_locator_sidebar);
			var height = store_locator_sidebar.height() + 100;

			if(this.settings.mapFullHeight == "1") {
				height = this.windowHeight - $(this.settings.store_locator_search_box).height();
			} 
			resultList.css('max-height', height);
		},
		initModal: function(callback) {
			var store_modal = $(this.settings.store_modal);
			var store_modal_button = $(this.settings.store_modal_button);
			var store_modal_close = $(this.settings.store_modal_close);
			var that = this;

		    store_modal_button.on('click', function()
		    {
		    	store_modal.show();
			    store_modal.modal('show');
			    callback();
		    });

		    store_modal_close.on('click', function()
		    {
		    	store_modal.hide();
		    	$('.modal-backdrop').remove();
			    store_modal.modal('hide');
		    });
		},
		initStoreLocator: function() {
			var that = this;

			// Do not load Map again when Modal gets reopened
			if(that.isEmpty(that.map)){
				that.initMap(function(){
					if(that.settings.searchBoxAutocomplete === "1") {
						that.initAutocomplete();
					}
				    that.initStoreLocatorButton();
				    that.initGetCurrentPositionLink();
				    that.initGetAllStoresLink();
				    that.autoHeightMap();
				    that.watchMapDragged();
				    that.watchAddressFieldEmpty();
				    that.watchDraggedButton();

				    that.initFilter();
					if(that.settings.searchBoxAutolocate === "1") {
						if(that.settings.searchBoxSaveAutolocate === "1") {
							that.getCurrentPosition();
						} else {
							that.getCurrentPosition(false);
						}					 
					} else {
						var currentPosition = new google.maps.LatLng(Number(that.settings.mapDefaultLat), Number(that.settings.mapDefaultLng)); 
						that.setCurrentPosition(currentPosition);
					}		    
				});
			}
		},
		initMap: function(callback) {
			var mapContainer = $(this.settings.map_container);
		    var mapDefaultZoom = this.settings.mapDefaultZoom;
		    var mapDefaultType = this.settings.mapDefaultType;
		    var mapDefaultLat = Number(this.settings.mapDefaultLat);
		    var mapDefaultLng = Number(this.settings.mapDefaultLng);

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
		getCurrentPosition: function(useCookie) {
			var that = this;

			var ip_geoservice = "https://geoip.nekudo.com/api/";

			var cookieLat = that.getCookie('store_locator_lat');
			var cookieLng = that.getCookie('store_locator_lng');
			var currentPosition;

			that.maybeShowLoading();

			if (typeof(useCookie)==='undefined') useCookie = true;

			if(cookieLat !== "" && cookieLng !== "" && useCookie === true){
				currentPosition = new google.maps.LatLng(cookieLat, cookieLng);
			}

			if(typeof(currentPosition) == "undefined") {
				if (navigator.geolocation) {

					var options = {
					  enableHighAccuracy: true,
					  timeout: 5000,
					  maximumAge: 0
					};

					navigator.geolocation.getCurrentPosition(function(position) {
					
						var currentPosition = new google.maps.LatLng(position.coords.latitude, position.coords.longitude); 
						document.cookie="store_locator_lat="+position.coords.latitude;
						document.cookie="store_locator_lng="+position.coords.longitude;
						that.setCurrentPosition(currentPosition, true);
					}, function(error) {
						console.log(error);
						console.log('Getting position via IP!');
						$.getJSON(ip_geoservice)
							.done(function( location ) {
								var ipLat = location.location.latitude;
								var ipLng = location.location.longitude;
								if(ipLat == "" || ipLng == "") {
									alert('Could not find your position. Please enter it manually.');
									return;
								}
								var currentPosition = new google.maps.LatLng(ipLat, ipLng); 
								document.cookie="store_locator_lat="+ipLat;
								document.cookie="store_locator_lng="+ipLng;
								that.setCurrentPosition(currentPosition, true);
						});	
					}, options);

				} else {
					console.log('Browser Geolocation not supported! Getting position via IP');

					$.getJSON(ip_geoservice)
						.done(function( location ) {
							var currentPosition = new google.maps.LatLng(location.latitude, location.longitude); 
							document.cookie="store_locator_lat="+location.latitude;
							document.cookie="store_locator_lng="+location.longitude;
							that.setCurrentPosition(currentPosition, true);
					});				
				}

			} else {
				that.setCurrentPosition(currentPosition, true);
			}
		},
		setCurrentPosition: function(latlng, override) {
			var that = this;
			var store_locator_address_field = $(this.settings.store_locator_address_field);

			this.currentPosition = latlng;
			this.lat = latlng.lat();
			this.lng = latlng.lng();

			if(override) {
				that.maybeShowLoading();
			}

			if(store_locator_address_field.val() === "" || override) {

				this.geocodeLatLng(function(address){
					store_locator_address_field.val(address);
				});
			}

			// Delete old marker
			if(!this.isEmpty(this.ownMarker)) {
				this.ownMarker.setMap(null);
			}
			
			this.ownMarker = new google.maps.Marker({
				position: latlng,
				map: this.map,
				title: 'Your Position!',
				icon: this.settings.mapDefaultUserIcon
			});

			this.drawRadiusCircle(true);
			this.getStores();

		},
		drawRadiusCircle: function(inital) {
			var that = this;
			var mapRadius;
			var distanceUnit = this.settings.mapDistanceUnit;
			var earthRadius = this.settings.earthRadi[distanceUnit];
			var selectedRadius = $(this.settings.store_locator_filter_radius).find(":selected").val();

			if(!this.isEmpty(selectedRadius)){
				this.radius = parseFloat(selectedRadius);
			} else {
				this.radius = parseFloat(this.settings.mapRadius);
			}

			if(!this.isEmpty(this.radiusCircle) && typeof(this.radiusCircle.setMap) !== "undefined") {
				this.radiusCircle.setMap(null);
			}

			if(this.settings.mapDrawRadiusCircle === "0"){
				if(this.settings.mapRadiusToZoom === "1") {
					this.map.setZoom(this.radiusToZoom(this.radius));
				}
				return false;
			}

			mapRadius = (this.radius / earthRadius) * this.settings.earthRadi.mt;
			this.radiusCircle = new google.maps.Circle({
				center: this.currentPosition,
				clickable: true,
				draggable: false,
				editable: false,
				fillColor: '#004de8',
				fillOpacity: 0.27,
				map: this.map,
				radius: mapRadius,
				strokeColor: '#004de8',
				strokeOpacity: 0.62,
				strokeWeight: 1
			});

			if(inital !== true) {
				this.map.fitBounds(this.radiusCircle.getBounds());
			}
		},
		initAutocomplete: function() {
			var that = this;
			var addressField = $(this.settings.store_locator_address_field);
			var countryRestrict = this.settings.autocompleteCountryRestrict;
			var type = this.settings.autocompleteType;
			var map = this.map;
			
			if ( !addressField) { return; }

			var autocompleteOptions = {};
			if(!that.isEmpty(countryRestrict)) {
				autocompleteOptions.componentRestrictions = {'country' : countryRestrict };
			}

			if(!that.isEmpty(type)) {
				autocompleteOptions.types = [type];
			} else {
				autocompleteOptions.types = ['geocode'];
			}

			var autocomplete = new google.maps.places.Autocomplete(addressField[0], autocompleteOptions);
			autocomplete.bindTo('bounds', map);

			autocomplete.addListener('place_changed', function(e){
				var place = autocomplete.getPlace();
				if(!that.isEmpty(place.formatted_address)) {
					that.geocodeAddress(place.formatted_address);
				} else {
					that.geocodeAddress(place.name);
				}
			});

			var predefinedAddress = that.getParameterByName('location');
			if(!that.isEmpty(predefinedAddress)) {
				addressField.val(predefinedAddress);
				that.geocodeAddress(predefinedAddress);
			}
		},
		initStoreLocatorButton: function() {
			var that = this;
			var button = $(this.settings.store_locator_find_stores_button);
			var addressField = $(this.settings.store_locator_address_field);
			var currentAddress;

			button.on('click', function(e) {
				e.preventDefault();
				currentAddress = addressField.val();
				that.geocodeAddress(currentAddress);
			});
		},
		watchAddressFieldEmpty : function() {
			var that = this;
			var address_field = $(that.settings.store_locator_address_field);

			address_field.on('keyup', function(e) {
				var $this = $(this);
				var val = $this.val()
				if(val == "") {
					$this.css('border', '2px solid red');
				} else {
					$this.css('border', 'none');
				}
			});
		},
		initGetCurrentPositionLink: function() {
			var that = this;
			var store_locator_get_my_position = $(this.settings.store_locator_get_my_position);
			
			store_locator_get_my_position.on('click', function(e){
				e.preventDefault();
				that.getCurrentPosition(false);
			});
		},
		initGetAllStoresLink: function() {
			var that = this;
			var store_locator_get_all_stores = $(this.settings.store_locator_get_all_stores);
			
			store_locator_get_all_stores.on('click', function(e){
				e.preventDefault();
				that.maybeShowLoading();
				that.getAllStores('', '', function(response) {

					if(!that.isEmpty(that.radiusCircle) && typeof(that.radiusCircle.setMap) !== "undefined") {
						that.radiusCircle.setMap(null);
					}
					that.map.setZoom(parseInt(that.settings.searchBoxShowShowAllStoresZoom));
					var allStoresPosition = new google.maps.LatLng(Number(that.settings.searchBoxShowShowAllStoresLat), Number(that.settings.searchBoxShowShowAllStoresLng)); 
					that.map.setCenter(allStoresPosition);

					that.createMarker(response);
					that.createResultList(response);
					that.maybeShowLoading();
				});
			});
		},
		maybeShowLoading: function() {
			var store_locator_loading = $(this.settings.store_locator_loading);

			if(store_locator_loading.hasClass('store-locator-hidden'))
			{
				store_locator_loading.removeClass('store-locator-hidden');
			} else {
				store_locator_loading.addClass('store-locator-hidden');
			}
		},
		geocodeAddress: function (address) {
			var that = this;

			if ( address ) {
				this.geocoder.geocode( { 'address': address }, function ( results, status ) {
					if ( status === google.maps.GeocoderStatus.OK ) {
						that.setCurrentPosition(results[0].geometry.location);
					}
				} );
			} else {
				$(that.settings.store_locator_address_field).css('border', '2px solid red');
			}
		},
		geocodeLatLng: function (callback) {
			var that = this;
			var latlng = {lat: this.lat, lng: this.lng};

			this.geocoder.geocode({'location': latlng}, function(results, status) {
				if (status === google.maps.GeocoderStatus.OK) {
					if (results[1]) {
						callback(results[1].formatted_address);
					} else {
						window.alert('No results found');
					}
				} else {
					window.alert('Geocoder failed due to: ' + status);
				}
			});
		},
		autoHeightMap: function() {

			var mapContainer = $(this.settings.map_container);
			var store_locator_sidebar = $(this.settings.store_locator_sidebar);
		    var mapHeight = $(store_locator_sidebar).height();

		    if(mapHeight < this.settings.map_min_height) {
		    	mapHeight = this.settings.map_min_height;
		    } 

		    if(this.settings.mapFullHeight == "1") {
		    	mapHeight = this.windowHeight;
		    }

		    mapContainer.css('height', mapHeight);
		    google.maps.event.trigger(this.map, "resize");

		},
		getStores: function() {
			var that = this;
			that.maybeShowLoading();

			jQuery.ajax({
				url: that.settings.ajax_url,
				type: 'post',
				dataType: 'JSON',
				data: {
					action: 'get_stores',
					lat: that.lat,
					lng: that.lng,
					radius: that.radius,
					categories: that.categories,
					filter: that.filter,
				},
				success : function( response ) {
					that.createMarker(response);
					that.createResultList(response);
					that.maybeShowLoading();
					if (window.history.replaceState) {
						that.buildReplaceState();
					}
				},
				error: function(jqXHR, textStatus, errorThrown) {
				    that.maybeShowLoading();
				    alert('An Error Occured: ' + jqXHR.status + ' ' + errorThrown + '! Please contact System Administrator!');
				}
			});
		},
		createMarker: function(stores) {
			var that = this;
		   	var storesLength = Object.keys(stores).length;
		   	var store;
		   	var i = 0;
		   	var marker;
		   	var map = this.map;
		    var infowindow =  new google.maps.InfoWindow({
		        content: ""
		    });

		    // Clean markers
			while(this.markers.length){
			    this.markers.pop().setMap(null);
			}
			// Create Markers
			if(storesLength > 0) {
				for (i; i < storesLength; i++) {  

					store = stores[i];
					store.map = this.map;
					this.store = store;
					store.position = new google.maps.LatLng(store.lat, store.lng);

					if(!that.isEmpty(store.ic)) {
						store.icon = store.ic;
					} else if(!that.isEmpty(that.settings.store_locator_category_icon)) {
						store.icon = that.settings.store_locator_category_icon;
					} else {
						store.icon = that.settings.mapDefaultIcon;
					}

					// Label?
					// store.label = i.toString();
				    marker = new google.maps.Marker(store);
				    this.markers.push(marker);
				    if(this.settings.infowindowEnabled === "1") {
				    	this.createInfowindow(marker, map, infowindow, store);
				    }
				}
			}
		},
		createInfowindow: function(marker, map, infowindow, store) {
			var that = this;

			var infowindowLinkAction = this.settings.infowindowLinkAction;
			store.infowindowAction = store.gu;
			if(infowindowLinkAction == "web") {
				store.infowindowAction = store.we;
			}
			if(infowindowLinkAction == "tel") {
				store.infowindowAction = 'tel:' + store.te;
			}
			if(infowindowLinkAction == "email") {
				store.infowindowAction = 'mailto:' + store.em;
			}

			var content = '<div id="store_locator_infowindow_' + store.ID + '" class="store_locator_infowindow">' +
								'<div class="store-locator-col-sm-' + that.settings.infowindowDetailsColumns + ' store_locator_details">';
								if(this.settings.infowindowLinkAction !== "none") {
									content += '<a href="' + store.infowindowAction + '">' +
													'<h3 class="store_locator_name">' + store.na + ' <i class="fa fa-chevron-right"></i></h3>' +
												'</a>';
									
								} else {
									content += '<h3 class="store_locator_name">' + store.na + '</h3>';
								}
								content += !that.isEmpty(store.dc) ? '<span class="store_locator_distance">' + that.settings.showDistanceText + ': ' + store.dc + '<br/></span>' : '';
								content += '<p class="store_locator_address">';

					if(that.settings.showAddressStyle == "american") {
						content += !that.isEmpty(store.st) ? '<span class="store_locator_street">' + store.st + '<br/></span>' : '';
						content += !that.isEmpty(store.ct) ? '<span class="store_locator_city">' + store.ct + ', </span>' : '';
						content += !that.isEmpty(store.rg) ? '<span class="store_locator_region">' + store.rg + ' </span>' : '';
						content += !that.isEmpty(store.zp) ? '<span class="store_locator_zip">' + store.zp + '<br/></span>' : '';						
						content += !that.isEmpty(store.co) ? '<span class="store_locator_country">' + store.co + '</span>' : '';
					} else {
						content += !that.isEmpty(store.st) ? '<span class="store_locator_street">' + store.st + '<br/></span>' : '';
						content += !that.isEmpty(store.zp) ? '<span class="store_locator_zip">' + store.zp + '</span>' : '';
						content += !that.isEmpty(store.ct) ? '<span class="store_locator_city">' + store.ct + '<br/></span>' : '';
						content += !that.isEmpty(store.rg) ? '<span class="store_locator_region">' + store.rg + '</span>' : '';
						content += !that.isEmpty(store.co) ? '<span class="store_locator_country">' + store.co + '</span>' : '';
					}

			content +=				'</p>' +
									'<p class="store_locator_contact">';
			content += !that.isEmpty(store.te) ? '<span class="store_locator_tel">' + that.settings.showTelephoneText + ': <a href="tel:' + store.te + '">' + store.te + '</a><br/></span>' : '';
			content += !that.isEmpty(store.em) ? '<span class="store_locator_email">' + that.settings.showEmailText + ': <a href="mailto:' + store.em + '">' + store.em + '</a><br/></span>' : '';
			content += !that.isEmpty(store.mo) ? '<span class="store_locator_mobile">' + that.settings.showMobileText + ': <a href="tel:' + store.mo + '">' + store.mo + '</a><br/></span>' : '';
			content += !that.isEmpty(store.fa) ? '<span class="store_locator_fax">' + that.settings.showFaxText + ': ' + store.fa + '<br/></span>' : '';
			content += !that.isEmpty(store.we) ? '<span class="store_locator_website">' + that.settings.showWebsiteText + ': <a href="' + store.we + '" target="_blank">' + store.we + '</a><br/></span>' : '';
			content +=				'</p>' +
									'<p class="store_locator_actions">';

			content += !that.isEmpty(store.lat) ? '<a href="http://maps.google.com/maps?saddr=' + this.lat + ',' + this.lng + '&daddr=' + store.lat + ',' + store.lng + '" class="btn button btn-primary btn-lg store_locator_get_direction" target="_blank"><i class="fa fa-compass"></i> '+that.settings.showGetDirectionText+'</a>' : '';

			content += !that.isEmpty(that.settings.showContactStorePage) ? '<a href="' + that.settings.showContactStorePage + '?store_id=' + store.ID + '&address=' + encodeURIComponent($(that.settings.store_locator_address_field).val()) + '&lat=' + that.lat + '&lng=' + that.lng + '" class="btn button btn-primary btn-lg store_locator_contact_store"><i class="fa fa-mail-forward"></i> '+that.settings.showContactStoreText+'</a>' : '';

			content += !that.isEmpty(store.te) ? '<a href="tel:' + store.te + '" class="btn button btn-primary btn-lg store_locator_call_now"><i class="fa fa-phone"></i> '+that.settings.showCallNowText+'</a>' : '';

			content += !that.isEmpty(store.we) ? '<a href="' + store.we + '" class="btn button btn-primary btn-lg store_locator_visit_website" target="_blank"><i class="fa fa-globe"></i> '+that.settings.showVisitWebsiteText+'</a>' : '';

			content += !that.isEmpty(store.em) ? '<a href="mailto:' + store.em + '" class="btn button btn-primary btn-lg store_locator_write_email"><i class="fa fa-envelope-o"></i> '+that.settings.showWriteEmailText+'</a>' : '';

			content +=				'</p>' +
								'</div>';
								
			content += !that.isEmpty(store.im) ? '<div class="store-locator-col-sm-' + that.settings.infowindowImageColumns + ' store_locator_image"> <img src="' + store.im + '" class="img-responsive" width="'+that.settings.imageDimensions.width+'" height="'+that.settings.imageDimensions.height+'" /></div>' : '';

			if(!that.isEmpty(store.op)) {
				content += 		'<div class="store-locator-col-sm-' + that.settings.infowindowOpeningHoursColumns + ' store_locator_opening_hours">' + 
									that.createOpeningHoursTable(store.op)+
								'</div>';
			}
			content += !that.isEmpty(store.de) ? '<div class="store-locator-col-sm-12 store_locator_description">' +
									'' + store.de + '' +
								'</div>' : '';
			content +=		'</div>';

		    marker.addListener('click', function() {

				if(!that.isEmpty(store.ic)) {
					this.setIcon(store.ic);
				} else if(!that.isEmpty(that.settings.store_locator_category_icon)) {
					this.setIcon(that.settings.store_locator_category_icon);
				} else {
					this.setIcon(that.settings.mapDefaultIconHover);
				}

				infowindow.setContent(content);
		        infowindow.open(map, this);

		        if(that.settings.mapPanToOnHover == "1") {
		        	that.map.panTo(this.getPosition());
	        	}

		     	google.maps.event.addListener(map, 'click', function() {
					infowindow.close();
			    });
		     	google.maps.event.addListener(that.radiusCircle, 'click', function() {
					infowindow.close();
			    });
		    });
		    
		    marker.addListener('mouseover', function() {
		    	if(that.settings.infowindowCheckClosed == "1") {
		    		if(!that.isInfoWindowOpen(infowindow)) {
		        		google.maps.event.trigger(this, 'click');
	        		}
        		} else {
        			google.maps.event.trigger(this, 'click');
        		}
		    });
		    marker.addListener('mouseout', function() {
				if(!that.isEmpty(store.ic)) {
					this.setIcon(store.ic);
				} else if(!that.isEmpty(that.settings.store_locator_category_icon)) {
					this.setIcon(that.settings.store_locator_category_icon);
				} else {
					this.setIcon(that.settings.mapDefaultIcon);
				}
		    });
		},
		createResultList: function(stores)
		{
			var that = this;
		   	var storesLength = Object.keys(stores).length;
		   	var resultList = $(this.settings.result_list);
		   	var resultListIconEnabled = this.settings.resultListIconEnabled;
		   	var resultListIcon = this.settings.resultListIcon;
		   	var resultListIconSize = this.settings.resultListIconSize;
		   	var resultListIconColor = this.settings.resultListIconColor;

		   	var resultListPremiumIconEnabled = this.settings.resultListPremiumIconEnabled;
		   	var resultListPremiumIcon = this.settings.resultListPremiumIcon;
		   	var resultListPremiumIconSize = this.settings.resultListPremiumIconSize;
		   	var resultListPremiumIconColor = this.settings.resultListPremiumIconColor;

		   	var resultListLinkAction = this.settings.resultListLinkAction;
		   	
		   	var store;
		   	var i = 0;
		   	var content;
		   	var filterBadges;

		   	resultList.html('');
		   	if(storesLength > 0) {
				for (i; i < storesLength; i++) {
					store = stores[i];

					content = '';
		   	
					if(resultListIconEnabled === "1") {
						content	+=	'<div class="store-locator-col-sm-2 store_locator_icon store-locator-hidden-sm store-locator-hidden-xs">' +
											'<i style="color: '+ resultListIconColor +';" class="fa '+ resultListIcon +' ' + resultListIconSize +'"></i>' +
										'</div>' +
										'<div class="store-locator-col-sm-10 store_locator_details">';
					} else if(that.settings.showImage == "1" && store.im) {
						
						content	+=	'<div class="store-locator-col-sm-4 store_locator_image_container ' + that.settings.imagePosition + '">';
							content	+=	!that.isEmpty(store.im) ? '<img src="' + store.im + '" class="store-locator-img-responsive" width="'+that.settings.imageDimensions.width+'" height="'+that.settings.imageDimensions.height+'" />' : '';
						content += '</div>';
						content	+=	'<div class="store-locator-col-sm-8 store_locator_details">';
					} else {
						content	+=	'<div class="store-locator-col-sm-12 store_locator_details">';
					}

					var resultListLinkAction = this.settings.resultListLinkAction;

					store.resultListLinkAction = store.gu;
					if(resultListLinkAction == "web") {
						store.resultListLinkAction = store.we;
					}
					if(resultListLinkAction == "tel") {
						store.resultListLinkAction = 'tel:' + store.te;
					}
					if(resultListLinkAction == "email") {
						store.resultListLinkAction = 'mailto:' + store.em;
					}

					if(this.settings.resultListLinkAction !== "none") {
						content += '<a href="' + store.resultListLinkAction + '">' +
										'<h3 class="store_locator_name">' + store.na + ' <i class="fa fa-chevron-right"></i></h3>' +
									'</a>';
						
					} else {
						content += '<h3 class="store_locator_name">' + store.na + '</h3>';
					}

					content += !that.isEmpty(store.dc) ? '<span class="store_locator_distance">' + that.settings.showDistanceText + ': ' + store.dc + '<br/></span>' : '';

					filterBadges = "";
					if(!that.isEmpty(store.fi)) {
						$.each(store.fi, function(i, item){
							filterBadges += that.createBadge(item);
						});
					}

					if(!that.isEmpty(store.ca)) {
						$.each(store.ca, function(i, item){
							filterBadges += that.createBadge(item);
						});
					}

					content += !that.isEmpty(filterBadges) ? '<span className="store_locator_badges">' +
										filterBadges +
									'<br/></span>' : '';

									'<p class="store_locator_address">';

					if(that.settings.showAddressStyle == "american") {
						content += !that.isEmpty(store.st) ? '<span class="store_locator_street">' + store.st + '<br/></span>' : '';
						content += !that.isEmpty(store.ct) ? '<span class="store_locator_city">' + store.ct + ', </span>' : '';
						content += !that.isEmpty(store.rg) ? '<span class="store_locator_region">' + store.rg + ' </span>' : '';
						content += !that.isEmpty(store.zp) ? '<span class="store_locator_zip">' + store.zp + '<br/></span>' : '';						
						content += !that.isEmpty(store.co) ? '<span class="store_locator_country">' + store.co + '</span>' : '';
					} else {
						content += !that.isEmpty(store.st) ? '<span class="store_locator_street">' + store.st + '<br/></span>' : '';
						content += !that.isEmpty(store.zp) ? '<span class="store_locator_zip">' + store.zp + '</span>' : '';
						content += !that.isEmpty(store.ct) ? '<span class="store_locator_city">' + store.ct + '<br/></span>' : '';
						content += !that.isEmpty(store.rg) ? '<span class="store_locator_region">' + store.rg + '</span>' : '';
						content += !that.isEmpty(store.co) ? '<span class="store_locator_country">' + store.co + '</span>' : '';
					}

					content +=				'</p>' +
											'<p class="store_locator_contact">';
					content += !that.isEmpty(store.te) ? '<span class="store_locator_tel">' + that.settings.showTelephoneText + ': <a href="tel:' + store.te + '">' + store.te + '</a><br/></span>' : '';
					content += !that.isEmpty(store.em) ? '<span class="store_locator_email">' + that.settings.showEmailText + ': <a href="mailto:' + store.em + '">' + store.em + '</a><br/></span>' : '';
					content += !that.isEmpty(store.mo) ? '<span class="store_locator_mobile">' + that.settings.showMobileText + ': <a href="tel:' + store.mo + '">' + store.mo + '</a><br/></span>' : '';
					content += !that.isEmpty(store.fa) ? '<span class="store_locator_fax">' + that.settings.showFaxText + ': ' + store.fa + '</span>' : '';
					content += !that.isEmpty(store.dc) ? '<span class="store_locator_distance">' + that.settings.showDistanceText + ': ' + store.dc + '<br/></span>' : '';
					content += !that.isEmpty(store.we) ? '<span class="store_locator_website">' + that.settings.showWebsiteText + ': <a href="' + store.we + '" target="_blank">' + store.we + '</a><br/></span>' : '';
					content +=				'</p>';
					content += !that.isEmpty(store.de) ? '<div class="store-locator-col-sm-12 store_locator_result_list_description">' +
															'<p>' + store.de + '</p>' +
														'</div>' : '';
					content += 				'<p class="store_locator_actions">';

					content += !that.isEmpty(store.lat) ? '<a href="http://maps.google.com/maps?saddr=' + this.lat + ',' + this.lng + '&daddr=' + store.lat + ',' + store.lng + '" class="btn button btn-primary btn-lg store_locator_get_direction" target="_blank"><i class="fa fa-compass"></i> '+that.settings.showGetDirectionText+'</a>' : '';

					content += !that.isEmpty(that.settings.showContactStorePage) ? '<a href="' + that.settings.showContactStorePage + '?store_id=' + store.ID + '&address=' + encodeURIComponent($(that.settings.store_locator_address_field).val()) + '&lat=' + that.lat + '&lng=' + that.lng + '" class="btn button btn-primary btn-lg store_locator_contact_store"><i class="fa fa-mail-forward"></i> '+that.settings.showContactStoreText+'</a>' : '';

					content += !that.isEmpty(store.te) ? '<a href="tel:' + store.te + '" class="btn button btn-primary btn-lg store_locator_call_now"><i class="fa fa-phone"></i> '+that.settings.showCallNowText+'</a>' : '';

					content += !that.isEmpty(store.we) ? '<a href="' + store.we + '" class="btn button btn-primary btn-lg store_locator_visit_website" target="_blank"><i class="fa fa-globe"></i> '+that.settings.showVisitWebsiteText+'</a>' : '';

					content += !that.isEmpty(store.em) ? '<a href="mailto:' + store.em + '" class="btn button btn-primary btn-lg store_locator_write_email"><i class="fa fa-envelope-o"></i> '+that.settings.showWriteEmailText+'</a>' : '';
					
					content += '<a class="btn button btn-primary btn-lg store_locator_show_on_map"><i class="fa fa-map-marker"></i> '+ that.settings.showShowOnMapText + '</a>';

					content += '</div>';

					var render = '';
					render = '<div id="store_locator_result_list_item_' + store.ID + '" class="store_locator_result_list_item">' +
								'<div class="store-locator-row">';
					render += content;
					
					if(resultListPremiumIconEnabled === "1" && store.pr === "1") {
						render	+=	'<i style="color: '+ resultListPremiumIconColor +'; position: absolute; top: 5px; z-index: 999999; right: 10px;" class="fa '+ resultListPremiumIcon +' ' + resultListPremiumIconSize +'"></i>';
					}				

					render +=	'</div>' +
							'</div>';

					resultList.append(render);
				}
			} else {
				if(this.settings.mapExtendRadius === "1") {
					if(!$(this.settings.store_locator_filter_radius + " option:last").is(":selected")) {
						$(this.settings.store_locator_filter_radius + ' option:selected').prop('selected', false).next().prop('selected', 'selected').trigger('change');
					} else {
						this.noResults();
					}
				} else {
					this.noResults();
				}
			}
			this.autoHeightMap();
			this.map.setCenter(this.currentPosition);
			this.window.trigger('resize');

			if(this.settings.resultListHover == "1") {
				this.resultItemHover();
			}

			if(this.settings.showShowOnMap == "1") {
				this.showOnMap();
			}
		},
		createOpeningHoursTable: function(openingHours) {
			var that = this;

			var table = '';
			$.each(openingHours, function(i, item) {
				if(that.isEmpty(item)) {
					return true;
				}

				if(i % 2 === 0) {
					table += '<div class="store-locator-row">';
					table += '<div class="store-locator-col-sm-12">';
				}
				
				if(i % 2 === 0) {
					if(i === "0") { table += that.settings.showOpeningHoursMonday; }
					if(i === "2") { table += that.settings.showOpeningHoursTuesday; }
					if(i === "4") { table += that.settings.showOpeningHoursWednesday; }
					if(i === "6") { table += that.settings.showOpeningHoursThursday; }
					if(i === "8") { table += that.settings.showOpeningHoursFriday; }
					if(i === "10") { table += that.settings.showOpeningHoursSaturday; }
					if(i === "12") { table += that.settings.showOpeningHoursSunday; }

					table += ': ' + item;
				} else {
					table += " - " + item + ' ' + that.settings.showOpeningHoursClock;
				}

				if(i % 2 !== 0) {
					table += '</div>';
					table += '</div>';
				}
				
			});
			if(!that.isEmpty(table)) {
				var title = '<h3 class="store_locator_opening_hours_title">' + that.settings.showOpeningHoursText + '</h3>';
				table = '<div class="store-locator-opening-hours" id="store-locator-opening-hours">' + table + '</div>';
				table = title + table;
			}

			return table;
		},
		createBadge: function(value) {
			var that = this;

			var badgeCSS = that.slugify(value);
			var template = '<span class="store-locator-label store-locator-label-success ' + badgeCSS + '">%s</span> ';

			return that.sprintf(template, value);
		},
		noResults: function() {
		   	var resultList = $(this.settings.result_list);

		   	resultList.html('');
		   	resultList.append('<h4 class="store_locator_no_stores">' + this.settings.resultListNoResultsText + '</h4>');
			this.autoHeightMap();
		},
		resultItemHover: function() {
			var that = this;
			var resultList = $(this.settings.result_list);

			$('.store_locator_result_list_item').each(function(i, item){
				$(item).on('mouseenter', function(){
					google.maps.event.trigger(that.markers[i], 'click');
				});
			});
		},
		showOnMap: function() {
			var that = this;
			var resultList = $(this.settings.result_list);

			$('.store_locator_show_on_map').each(function(i, item){
				$(item).on('click', function(){
					google.maps.event.trigger(that.markers[i], 'click');
				});
			});
		},
		initFilter: function() {
			var that = this;
			var store_locator_filter_open_close = $(this.settings.store_locator_filter_open_close);
			var store_locator_filter_icon = store_locator_filter_open_close.find('i');

			store_locator_filter_open_close.on('click', function(){
				that.maybeShowFilter();
			});
			
		    that.watchRadiusSelection();
		    that.watchCategoriesSelection();
		    that.watchCheckboxFilter();
		    that.updateActiveFilter();
		},
		maybeShowFilter: function() {
			var store_locator_filter_content = $(this.settings.store_locator_filter_content);
			var store_locator_filter_open_close = $(this.settings.store_locator_filter_open_close);
			var store_locator_filter_icon = store_locator_filter_open_close.find('i');

			if(store_locator_filter_content.is(":hidden"))
			{
				store_locator_filter_icon.removeClass('fa-chevron-down');
				store_locator_filter_icon.addClass('fa-chevron-up');
				store_locator_filter_content.fadeIn();
			} else {
				store_locator_filter_icon.addClass('fa-chevron-down');
				store_locator_filter_icon.removeClass('fa-chevron-up');
				store_locator_filter_content.fadeOut();
			}
		},
		watchRadiusSelection: function() {
			var that = this;
			var selectedRadius = $(this.settings.store_locator_filter_radius);

			selectedRadius.on('change', function(){
				that.drawRadiusCircle();

				that.updateActiveFilter();
				that.getStores();
			});

			var predefinedRadius = that.getParameterByName('radius');
			if(!that.isEmpty(predefinedRadius)) {
				var options = selectedRadius.find('option');
				$.each(options, function(i, index) {
					var option = $(this);
					option.prop('selected', false);
					if(option.val() == predefinedRadius) {
						option.prop('selected', true);
					}
				});
			}
		},
		watchCategoriesSelection: function() {
			var that = this;
			var selectedCategories = $(this.settings.store_locator_filter_categories);

			if(selectedCategories.length == 0) {
				var selectedCategories = $('.store_locator_category_filter_image');
				var selectedCategoryID = $('.store_locator_category_filter_image[data-selected="selected"]').data('category');

				if(selectedCategoryID > 0) {
					that.categories = {0: selectedCategoryID };
				}

				selectedCategories.on('click', function(){
					var selected = $(this);
					selectedCategoryID = selected.data('category');

					var categoryIcon = $(selected).data('icon');
					if(!that.isEmpty(categoryIcon)) {
						that.settings.store_locator_category_icon = categoryIcon;
					} else {
						that.settings.store_locator_category_icon = '';
					}
					that.categories = {0: selectedCategoryID };
					that.updateActiveFilter();
					that.getStores();
				});
			} else {

				var selectedCategoryID = selectedCategories.find(':selected').val();
				var predefinedCategory = that.getParameterByName('category');

				if(!that.isEmpty(predefinedCategory) ) {
					var tst = selectedCategories.val(predefinedCategory);
					selectedCategoryID = predefinedCategory;
				}

				that.categories = {0: selectedCategoryID };
				selectedCategories.on('change', function(){
					var selected = selectedCategories.find(':selected');
					selectedCategoryID = selected.val();

					var categoryIcon = $(selected).data('icon');
					if(!that.isEmpty(categoryIcon)) {
						that.settings.store_locator_category_icon = categoryIcon;
					} else {
						that.settings.store_locator_category_icon = '';
					}
					that.categories = {0: selectedCategoryID };
					that.updateActiveFilter();
					that.getStores();
				});
			}
		},
		watchCheckboxFilter: function() {
			var that = this;
			var filterCheckboxes = $(this.settings.store_locator_filter_checkbox);

			var predefinedFilter = that.getQuerystringData('filter[]');

			if(typeof predefinedFilter == 'string' ) {
				predefinedFilter = {0 : predefinedFilter};
			}

			if(typeof predefinedFilter == 'undefined' ) {
				predefinedFilter = {};
			}

		    filterCheckboxes.each(function(i, item) {
		    	var checkbox = $(item);

				if(!that.isEmpty(predefinedFilter)) {
					$.each(predefinedFilter, function(i, index) {
						if(checkbox.prop('name') == index) {
							checkbox.prop('checked', 'checked');
						}
					});
				}

			    var isChecked = checkbox.is(":checked");

			    if(isChecked) {
			    	var filter = checkbox.prop("name");
			    	that.filter[filter] = filter;
			    }
		    });

			$(filterCheckboxes).on('change', function () {
				that.filter = {};
			    filterCheckboxes.each(function(i, item) {
			    	var checkbox = $(item);
				    var isChecked = checkbox.is(":checked");

				    if(isChecked) {
				    	var filter = checkbox.prop("name");
				    	that.filter[filter] = filter;
				    }
			    });
				that.updateActiveFilter();
				that.getStores();
			});
		},
		updateActiveFilter: function()
		{
			var that = this;
			var store_locator_filter = $(this.settings.store_locator_filter);
			var store_locator_filter_active_filter = $(this.settings.store_locator_filter_active_filter);
			var selectedCategories = store_locator_filter.find('select');
			var selectedFilters = store_locator_filter.find('input:checked');
			var template = '<span class="store_locator_filter_active store-locator-label store-locator-label-success %s">%s</span> ';
			var append = "";

			store_locator_filter_active_filter.html('');
			selectedCategories.each(function(i, item){
				var val = $(item).find(':selected').val();
				if(val !== "") {
					var text = $(item).find(':selected').text();
					var slug = that.slugify(text);
					append = append + that.sprintf(template, slug, text);
				}				
			});

			selectedFilters.each(function(i, item) {
				var text = $(item).val();
				var slug = that.slugify(text);
				append = append + that.sprintf(template, slug, text);
			});


			store_locator_filter_active_filter.html(append);

		},
		watchResize: function() {
			var store_locator_sidebar = $(this.settings.store_locator_sidebar);
			var store_modal_close = $(this.settings.store_modal_close);
			var windowWidth = this.window.width();

			var top;
			// if(windowWidth < 769) {
			// 	top = store_locator_sidebar.height() * -1;
			// 	store_modal_close.css('top', top);
			// } else {
			// 	top = 20;
			// 	store_modal_close.css('top', top);
			// }
		},
		watchMapDragged : function() {

			var that = this;
			var map = that.map;
			
			var store_locator_dragged_button = $(that.settings.store_locator_dragged_button);
			store_locator_dragged_button.fadeOut();
			
			google.maps.event.addListener(map, 'dragend', function(e) {
				store_locator_dragged_button.fadeIn();
			} );

		},
		watchDraggedButton : function () {

			var that = this;
			var store_locator_dragged_button = $(that.settings.store_locator_dragged_button);

			store_locator_dragged_button.on('click', function(e) {
				store_locator_dragged_button.fadeOut();

				var coords = that.map.getCenter();

				var currentPosition = new google.maps.LatLng(coords.lat(), coords.lng()); 
				that.setCurrentPosition(currentPosition);
			});
		},
		radiusToZoom: function(radius){
		    return Math.round(14-Math.log(radius)/Math.LN2);
		},
		initForm : function() {
			var that = this;

			var predefinedAddress = that.getParameterByName('address');
			var predefinedLat = that.getParameterByName('lat');
			var predefinedLng = that.getParameterByName('lng');
			var addressField = $(this.settings.store_locator_form_customer_address);
			var address = addressField.val();
			var lat, lng;

			if(!that.isEmpty(predefinedAddress) && that.isEmpty(address)) {
				addressField.val(predefinedAddress);
			}

			if(!that.isEmpty(predefinedLat) && !that.isEmpty(predefinedLng)) {
				lat = predefinedLat;
				lng = predefinedLng;
			}

			that.watchFormSelectField();
			that.watchFormAddressField();
			that.initFormAutocomplete();
			that.loadFormStores(lat, lng);

		},
		loadFormStores : function(lat, lng) {
			var that = this;

			var storeSelectField = $(this.settings.store_locator_form_store_select);
			var addressField = $(this.settings.store_locator_form_customer_address);
			var address = addressField.val();
			var predefinedStoreId = that.getParameterByName('store_id');

			if( (that.isEmpty(lat) || that.isEmpty(lng)) && !that.isEmpty(address)) {
				that.geocoder.geocode( { 'address': address }, function ( results, status ) {
					if ( status === google.maps.GeocoderStatus.OK ) {
						var location = results[0].geometry.location;
						lat = location.lat();
						lng = location.lng();			
						that.getAllStores(lat, lng, that.storesToSelectField, { store_id : predefinedStoreId, that: that});
					} else {
						that.getAllStores(lat, lng, that.storesToSelectField, { store_id : predefinedStoreId, that: that});
					}
				} );
			} else {
				that.getAllStores(lat, lng, that.storesToSelectField, { store_id : predefinedStoreId, that: that});
			}

		},
		watchFormAddressField : function() {
			var that = this;
			var addressField = $(this.settings.store_locator_form_customer_address);

			addressField.on('focusout', function(e) {
				var $this = $(this);
				var val = $this.val()
				
				that.loadFormStores();
			});
		},
		watchFormSelectField : function () {
			var that = this;
			var storeSelectField = $(this.settings.store_locator_form_store_select);
			var dataName, dataValue;
			storeSelectField.on('change', function(e) {
				var $this = $(this);
				var selected = storeSelectField.find(':selected');
				var val = $this.val()

				var possibleData = [
					'name',
					'address',
					'zip',
					'city',
					'country',
					'region',
					'telephone',
					'mobile',
					'fax',
					'email',
					'website',
				]
				
				$(possibleData).each(function(i, index) {
					var dataName = index;
					var dataValue = selected.data(dataName);

					if(typeof dataValue !== 'undefined') {
						dataValue = dataValue.toString();
					}

					if(that.isEmpty(dataValue)) {
						dataValue = "";
					}
					var inputField = $('input[name="store_locator_form_store_' + dataName + '"]');
					if(inputField.length > 0) {
						inputField.val(dataValue);
					}
					
					var infoField = $('.store_locator_store_info_' + dataName);
					if(infoField.length > 0) {
						infoField.html(dataValue);
					}
				});
			});
		},
		getAllStores: function(lat, lng, callback, options) {
			var that = this;

			jQuery.ajax({
				url: that.settings.ajax_url,
				type: 'post',
				dataType: 'JSON',
				data: {
					action: 'get_all_stores',
					lat: lat,
					lng: lng,
				},
				success : function( response ) {
					callback(response, options);
				},
				error: function(jqXHR, textStatus, errorThrown) {
				    alert('An Error Occured: ' + jqXHR.status + ' ' + errorThrown + '! Please contact System Administrator!');
				}
			});
		},
		// Form 
		initFormAutocomplete: function() {
			var that = this;
			var addressField = $(this.settings.store_locator_form_customer_address);
			var countryRestrict = this.settings.autocompleteCountryRestrict;
			var type = this.settings.autocompleteType;
			var map = this.map;
			
			if ( !addressField) { return; }

			var autocompleteOptions = {};
			if(!that.isEmpty(countryRestrict)) {
				autocompleteOptions.componentRestrictions = {'country' : countryRestrict };
			}

			if(!that.isEmpty(type)) {
				autocompleteOptions.types = [type];
			} else {
				autocompleteOptions.types = ['geocode'];
			}

			var autocomplete = new google.maps.places.Autocomplete(addressField[0], autocompleteOptions);

			autocomplete.addListener('place_changed', function(e){
				var place = autocomplete.getPlace();
				if(!that.isEmpty(place.formatted_address)) {
					addressField.val(place.formatted_address).trigger('focusout');
				} else {
					addressField.val(place.name).trigger('focusout');
				}
			});
		},
		storesToSelectField: function(stores, options) {

			var that = options.that;
			var store_id = options.store_id
			var storeSelectField = $(that.settings.store_locator_form_store_select);
			
			var html = '<option value="">Select Store</option>';

			var storesLength = Object.keys(stores).length;
			var i = 0;
			var store;

			var selected;
			var data;
			var disabled;
			if(storesLength > 0) {
				for (i; i < storesLength; i++) { 
					
					store = stores[i];

					selected = "";
					if(store.ID == store_id) {
						selected = 'selected="selected"';
					}

					data = "";
					if(!that.isEmpty(store.na)) {
						data += ' data-name="' + store.na + '"';
						if(!that.isEmpty(store.distance)) {
							store.distance = parseFloat(store.distance).toFixed(2); 
							store.na = store.na + ' (' + store.distance + ' ' + that.settings.mapDistanceUnit + ')';
						}
					}
					if(!that.isEmpty(store.st)) {
						data += ' data-address="' + store.st + '"';
					}
					if(!that.isEmpty(store.zp)) {
						data += ' data-zip="' + store.zp + '"';
					}
					if(!that.isEmpty(store.ct)) {
						data += ' data-city="' + store.ct + '"';
					}
					if(!that.isEmpty(store.co)) {
						data += ' data-country="' + store.co + '"';
					}
					if(!that.isEmpty(store.rg)) {
						data += ' data-region="' + store.rg + '"';
					}
					if(!that.isEmpty(store.te)) {
						data += ' data-telephone="' + store.te + '"';
					}
					if(!that.isEmpty(store.mo)) {
						data += ' data-mobile="' + store.mo + '"';
					}
					if(!that.isEmpty(store.fa)) {
						data += ' data-fax="' + store.fa + '"';
					}
					if(!that.isEmpty(store.em)) {
						data += ' data-email="' + store.em + '"';
					}
					if(!that.isEmpty(store.we)) {
						data += ' data-website="' + store.we + '"';
					}


					html += '<option value="' + store.ID + '" ' + selected + ' ' +  data + '>' + store.na + '</option>';
				}
			}

			storeSelectField.html(html).trigger('change');
		},
		initEmbeddedSearch : function() {
			var that = this;
			var addressField = $(this.settings.store_locator_address_field);
			var countryRestrict = this.settings.autocompleteCountryRestrict;
			var type = this.settings.autocompleteType;
			var map = this.map;
			
			if ( !addressField) { return; }

			var autocompleteOptions = {};
			if(!that.isEmpty(countryRestrict)) {
				autocompleteOptions.componentRestrictions = {'country' : countryRestrict };
			}

			if(!that.isEmpty(type)) {
				autocompleteOptions.types = [type];
			} else {
				autocompleteOptions.types = ['geocode'];
			}

			var autocomplete = new google.maps.places.Autocomplete(addressField[0], autocompleteOptions);

			autocomplete.addListener('place_changed', function(e){
				var place = autocomplete.getPlace();
				if(!that.isEmpty(place.formatted_address)) {
					addressField.val(place.formatted_address).trigger('focusout');
				} else {
					addressField.val(place.name).trigger('focusout');
				}
			});

			var store_locator_get_my_position = $(this.settings.store_locator_get_my_position);
			store_locator_get_my_position.on('click', function(e){
				
				e.preventDefault();

				if (navigator.geolocation) {

					var options = {
					  enableHighAccuracy: true,
					  timeout: 5000,
					  maximumAge: 0
					};

					navigator.geolocation.getCurrentPosition(function(position) {

						if(addressField.val() === "") {
							var currentPosition = new google.maps.LatLng(position.coords.latitude, position.coords.longitude); 

							that.currentPosition = currentPosition;
							that.lat = currentPosition.lat();
							that.lng = currentPosition.lng();

							that.geocodeLatLng(function(address){
								addressField.val(address);
							});
						}
					}, function(error) {
						console.log(error);
					}, options);
				}
			}).trigger('click');
		},
		isInfoWindowOpen : function(infoWindow){
		    var map = infoWindow.getMap();
		    return (map !== null && typeof map !== "undefined");
		},
		buildReplaceState : function() {
			var that = this;
			var address = $(that.settings.store_locator_address_field).val();
			var categories = that.categories;
			var filter = that.filter;
			var radius = that.radius;

			var url = "";
			if(!that.isEmpty(address)) {
				url += '?location=' + address;
			}

			if(categories[0]) {
				if(url == "") {
					url += '?category=' + categories[0];
				} else {
					url += '&category=' + categories[0];
				}
			}
			
			if(radius) {
				if(url == "") {
					url += '?radius=' + radius;
				} else {
					url += '&radius=' + radius;
				}
			}

			if(filter) {
				var filterLoop = 0;
				$.each(filter, function(i, index) {
					if(filterLoop == 0 && url == "") {
						url += '?filter[]=' + index;
					} else {
						url += '&filter[]=' + index;
					}
					filterLoop++;
				});
			}
			

			window.history.replaceState('test', 'Store Locator', that.currentURL + url);
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
		slugify : function (text) {
		  return text.toString().toLowerCase()
		    .replace(/\s+/g, '-')           // Replace spaces with -
		    .replace(/[^\w\-]+/g, '')       // Remove all non-word chars
		    .replace(/\-\-+/g, '-')         // Replace multiple - with single -
		    .replace(/^-+/, '')             // Trim - from start of text
		    .replace(/-+$/, '');            // Trim - from end of text
		},
		getParameterByName : function (name, url) {
		    if (!url) url = window.location.href;
		    name = name.replace(/[\[\]]/g, "\\$&");
		    var regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)"),
		        results = regex.exec(url);
		    if (!results) return null;
		    if (!results[2]) return '';
		    return decodeURIComponent(results[2].replace(/\+/g, " "));
		},
		getQuerystringData : function (name) {
		    var data = { };
		    var parameters = window.location.search.substring(1).split("&");
		    for (var i = 0, j = parameters.length; i < j; i++) {
		        var parameter = parameters[i].split("=");
		        var parameterName = decodeURIComponent(parameter[0]);
		        var parameterValue = typeof parameter[1] === "undefined" ? parameter[1] : decodeURIComponent(parameter[1]);
		        var dataType = typeof data[parameterName];
		        if (dataType === "undefined") {
		            data[parameterName] = parameterValue;
		        } else if (dataType === "array") {
		            data[parameterName].push(parameterValue);
		        } else {
		            data[parameterName] = [data[parameterName]];
		            data[parameterName].push(parameterValue);
		        }
		    }
		    return typeof name === "string" ? data[name] : data;
		}
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

	$.fn.emulateTransitionEnd = function (duration) {
		var called = false
		var $el = this
		$(this).one('bsTransitionEnd', function () { called = true })
		var callback = function () { if (!called) $($el).trigger($.support.transition.end) }
		setTimeout(callback, duration)
		return this
	}

	$(document).ready(function() {

		$( "body" ).storeLocator( 
			store_locator_options
		);

	} );

})( jQuery );