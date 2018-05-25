/*! 
 * Master Slider WordPress Panel 
 * Copyright © 2017 All Rights Reserved. 
 *
 * @author Averta [www.averta.net]
 * @version 2.50.3
 * @date May 2017
 */


/* ================== src/js/mspanel/MSpanel.js =================== */
/*
 * @overview  Master Slider Wordpress Panel
 * @copyright Copyright 2014 Averta Ltd.
 * @version   2.50.3
 * http://www.averta.net
 */

window.MSPanel = Ember.Application.create({	rootElement : "#msp-root" });
MSPanel.version = '2.50.3';
MSPanel.SliderID = parseQueryString(window.location.search).slider_id || __MSP_SLIDER_ID || '100';
MSPanel.SliderSlug = __MSP_SLIDER_ALIAS || ('ms-' + MSPanel.SliderID);
MSPanel.dependedControllers = [];

//window.__MSP_TYPE = 'post'; window.__MSP_TYPE || 'custom';
if( __MSP_TYPE === 'flickr' || __MSP_TYPE === 'post' || __MSP_TYPE === 'facebook' || __MSP_TYPE === 'wc-product') {
	MSPanel.dynamicTags = [];
}
/**
 * Adds new function to String object 'jfmt' it's like Ember.fmt but first replaces '%s' or '%d' to '%@'
 * @example 'Hi, %s'.jfmt('John');
 */
String.prototype.jfmt = function(){ return ''.fmt.apply(this.replace(/%s|%d/, '%@') ,arguments); };

window.$ = jQuery.noConflict();
jQuery.ui.dialog.prototype._focusTabbable = function(){};

// Setup Application Router
MSPanel.Router.map(function() {
	this.resource('settings' );
	this.resource('slides', {path: '/'});
	this.resource('controls');
	this.resource('callbacks');
	this.resource('error');

	if( __MSP_TYPE === 'flickr' ){
		this.resource('flickr');
	}

	if( __MSP_TYPE === 'facebook' ){
		this.resource('facebook');
	}

	if( __MSP_TYPE === 'post' ){
		this.resource('post');
	}

	if( __MSP_TYPE === 'wc-product' ){
		this.resource('wcproduct');
	}
});
MSPanel.Router.reopen({ location: 'none' });

// Application route
MSPanel.ApplicationRoute = Ember.Route.extend({
	model: function() {
		var setting = MSPanel.Settings.find();
		if( setting.get('length') === 0){
			 MSPanel.Settings.create().save();
		}
	}
});

MSPanel.SettingsRoute = Ember.Route.extend({
	model: function() {
		return MSPanel.Settings.find(1);
	},
	setupController: function(controller, model) {
		controller.set('model', model);
		controller.setup();
	}
});

// Flickr slider type route
if( __MSP_TYPE === 'flickr' ){
	MSPanel.FlickrRoute = Ember.Route.extend({
		/*activate: function() {
			this.controllerFor('flickr').onActivate();
		},*/
  		deactivate: function() {
			this.controllerFor('flickr').onDeactivate();
  		}
	});

	// register flickr controller setup in depended controllers which will be runned by Slides route
	MSPanel.dependedControllers.push(
		function(){
			this.controllerFor('flickr').set('model', MSPanel.Settings.find(1));
			this.controllerFor('flickr').setup();
		}
	);
}

// Facebook slider type route
if( __MSP_TYPE === 'facebook' ){
	MSPanel.FacebookRoute = Ember.Route.extend({
		/*activate: function() {
			this.controllerFor'(facebook').onActivate();
		},*/
  		deactivate: function() {
			this.controllerFor('facebook').onDeactivate();
  		}
	});

	// register facebook controller setup in depended controllers which will be runned by Slides route
	MSPanel.dependedControllers.push(
		function(){
			this.controllerFor('facebook').set('model', MSPanel.Settings.find(1));
			this.controllerFor('facebook').setup();
		}
	);
}

// Posts slider type route
if( __MSP_TYPE === 'post' ){
	MSPanel.PostRoute = Ember.Route.extend({
		/*activate: function() {
			this.controllerFor'(post').onActivate();
		},*/
  		deactivate: function() {
			this.controllerFor('post').onDeactivate();
  		}
	});

	// register post controller setup in depended controllers which will be runned by Slides route
	MSPanel.dependedControllers.push(
		function(){
			this.controllerFor('post').set('model', MSPanel.Settings.find(1));
			this.controllerFor('post').setup();
		}
	);
}

// Woocommerce type route
if( __MSP_TYPE === 'wc-product' ){
	MSPanel.WcproductRoute = Ember.Route.extend({
		/*activate: function() {
			this.controllerFor'(post').onActivate();
		},*/
  		deactivate: function() {
			this.controllerFor('wcproduct').onDeactivate();
  		}
	});

	// register post controller setup in depended controllers which will be runned by Slides route
	MSPanel.dependedControllers.push(
		function(){
			this.controllerFor('wcproduct').set('model', MSPanel.Settings.find(1));
			this.controllerFor('wcproduct').setup();
		}
	);
}
MSPanel.SlidesRoute = Ember.Route.extend({
	model: function(){
		return MSPanel.Slide.find();
	},

	setupController: function(controller, model) {
		controller.set('model', model);
		controller.set('sliderSettings' , MSPanel.Settings.find(1));

		// creates new style controller and add it in slides controller
		var stylesController = MSPanel.StylesController.create({
			model: MSPanel.Style.find(),//this.store.find('style'),
			presetStyles: MSPanel.PresetStyle.find(),
			parent: controller
		 // store: controller.store
		});

		// creates new effect controller and add it in slides controller
		var effectsController = MSPanel.EffectsController.create({
			model: MSPanel.Effect.find(),//this.store.find('effect'),
			presetEffects: MSPanel.PresetEffect.find(),
			parent: controller,
			container: MSPanel.__container__

		 // store: controller.store
		});

		// creates new button style controller and add it in slides controller
		var buttonsController = MSPanel.ButtonsController.create({
			model: MSPanel.ButtonStyle.find(),//this.store.find('effect'),
			parent: controller,
			container: MSPanel.__container__
		 // store: controller.store
		});

		controller.set('stylesController' , stylesController);
		controller.set('effectsController' , effectsController);
		controller.set('buttonsController' , buttonsController);
		controller.setup();
		if( MSPanel.dependedControllers.length > 0 ) {
			Ember.run.scheduleOnce('afterRender', this, this.setupDependedControllers);
		}
	},

	setupDependedControllers: function(){
		for(var i=0,l=MSPanel.dependedControllers.length; i!==l; i++){
			MSPanel.dependedControllers[i].call(this);
		}
	}
});

MSPanel.ControlsRoute = Ember.Route.extend({
	model: function() {
		return MSPanel.Control.find();
	},

	setupController: function(controller, model) {
		controller.set('model', model);
		controller.setup();
		this.activate();
	},

	activate: function() {
		var controller = this.get('controller');
		if(controller){
			controller.set('controlOptions', 'empty-template')
		}
	}

});

MSPanel.CallbacksRoute = Ember.Route.extend({
	model: function() {
		return MSPanel.Callback.find();
	},

	setupController: function(controller, model) {
		controller.set('model', model);
		controller.setup();
	}
});

/* ================== src/js/mspanel/models/SliderModel.js =================== */
/**
 * Master Slider Panel Model
 * @package MSPanel
 * @author averta
 */

;(function(){

	var attr = Ember.attr,
		hasMany = Ember.hasMany,
		belongsTo = Ember.belongsTo;

	// custom data type, converts absolute paths to relative
	var regp = /https\:|http\:/;
	var WPPath = {

		// convert to relative
		serialize: function(path){
			if ( path == undefined ){
				return path;
			}

			if( regp.test(path) ) { // is it absolute?
				return path.replace(__MS.upload_dir, '');
			} else {
				return path.replace('/wp-content/uploads', '');
			}
		},

		// covert to absolute
		deserialize: function(path){
			if ( path == undefined ) {
				return path;
			}

			if( regp.test(path) ) { // is it absolute?
				return path;
			} else {
				return __MS.upload_dir + path;
			}
		}

	};

	var defaults = window.__MSP_DEF_OPTIONS || {};

	/**
	 * Slider Settings Model
	 */
	MSPanel.Settings = Ember.Model.extend({

		/*	Internal Options */
		// -------------------------------------------------------------------
		id 				: attr('number'), 	// settings record id (not slider id)
		snapping    	: attr('boolean', {defaultValue : true}),
		bgImageThumb	: attr(WPPath),
		disableControls : attr('boolean', {defaultValue: false}),
		// -------------------------------------------------------------------

		// General
        name             : attr('string' , {defaultValue: __MSP_LAN.sm_001}),
		slug 			 : attr('string'),
		width			 : attr('number' , {defaultValue: defaults.width  || 1000}),
		height 			 : attr('number' , {defaultValue: defaults.height || 500}),
		wrapperWidth 	 : attr('number'),
		minHeight 		 : attr('number'),
		wrapperWidthUnit : attr('string' , {defaultValue: 'px'}),
		autoCrop		 : attr('boolean', {defaultValue: defaults.autoCrop || false}),
		type 			 : attr('string'),
		sliderId 	 	 : attr('string'),
		autofillTarget	 : attr('string'),

        enableOverlayLayers : attr('boolean', {defaultValue:defaults.enableOverlayLayers || true }),

		/**
		 * Slider sizing methods
		 * Values:
		 * 		boxed,
		 * 		fullwidth,
		 * 		fullscreen,
		 * 		fillwidth,
		 * 		autofill,
		 * 		partialview
		 */
		layout		: attr('string' , {defaultValue: defaults.layout || 'boxed'}),
		autoHeight	: attr('boolean', {defaultValue: defaults.autoHeight || false}),

		// navigation and appearance
		trView		: attr('string' , {defaultValue: defaults.transition || 'basic'}),
		speed		: attr('number' , {defaultValue: defaults.speed || 20}),
		space 		: attr('number' , {defaultValue: defaults.space || 0}),
		start		: attr('number' , {defaultValue: defaults.start}),
		grabCursor	: attr('boolean', {defaultValue: defaults.grabCursor /*true*/}),
		swipe		: attr('boolean', {defaultValue: defaults.swipe /*true*/}),
		mouse		: attr('boolean', {defaultValue: defaults.mouse /*true*/}),
		wheel		: attr('boolean', {defaultValue: defaults.wheel /*false*/}),
		keyboard 	: attr('boolean', {defaultValue: defaults.keyboard /*false*/}),
        autoplay    : attr('boolean', {defaultValue: defaults.autoplay /*false*/}),
        loop        : attr('boolean', {defaultValue: defaults.loop /*false*/}),
        shuffle     : attr('boolean', {defaultValue: defaults.shuffle /*false*/}),
        preload     : attr('string' , {defaultValue: defaults.preload /*'-1'*/}),
        overPause   : attr('boolean', {defaultValue: defaults.overPause /*true*/}),
        endPause    : attr('boolean', {defaultValue: defaults.endPause /*false*/}),
        hideLayers  : attr('boolean', {defaultValue: defaults.hideLayers /*false*/}),
        dir         : attr('string' , {defaultValue: defaults.dir /*'h'*/}),
        parallaxMode: attr('srting' , {defaultValue: defaults.parallaxMode /*'swipe'*/}),
        useDeepLink : attr('string' , {defaultValue: false}),
        deepLink    : attr('string'),
        deepLinkType: attr('string', {defaultValue: 'path'}),

		mobileBGVideo : attr('boolean', {defaultValue: defaults.mobileBGVideo /*false*/}),

		startOnAppear		: attr('boolean', {defaultValue: defaults.startOnAppear /*false*/}),

		scrollParallax 		: attr('boolean'),
		scrollParallaxMove  : attr('number' , {defaultValue: 30}),
		scrollParallaxBGMove: attr('number' , {defaultValue: 50}),
		scrollParallaxFade 	: attr('boolean', {defaultValue: true}),

		centerControls		: attr('boolean', {defaultValue:defaults.centerControls /*true*/}),
		instantShowLayers	: attr('boolean', {defaultValue:defaults.instantShowLayers /*false*/}),
		fullscreenMargin	: attr('number'),

		// misc
		inlineStyle	: attr('string'),
		className	: attr('string', {defaultValue:defaults.className /*true*/}),
		bgColor		: attr('string'),
		bgImage		: attr(WPPath),
		customStyle : attr('string'),

		skin			: attr('string' , {defaultValue: defaults.skin /*'ms-skin-default'*/}),
		msTemplate		: attr('string' , {defaultValue: 'custom'}),
		msTemplateClass	: attr('string' , {defaultValue: ''}),
		usedFonts 		: attr('string'),

		// Flickr/Facebook Settings
        fbtoken         : attr('string'),
		apiKey 			: attr('string'),
		setId 			: attr('string'),
		setType 		: attr('string'),
		imgCount	 	: attr('number'),
		thumbSize 		: attr('srting'),
		imgSize			: attr('string'),

		// Posts Settings
		postType 		: attr('string'),
		postCats		: attr(Array),
		postTags		: attr(Array),
		postCount		: attr('number'),
		postImageType	: attr('string'),
		postOrder 		: attr('string'),
		postOrderDir 	: attr('string'),
		postExcerptLen	: attr('number'),
        postExcludeIds  : attr('string'),
		postExcludeNoImg: attr('boolean'),
		postIncludeIds	: attr('string'),
		postOffset		: attr('number'),
		postLinkSlide	: attr('boolean'),
		postLinkTarget 	: attr('string'),
		postSlideBg		: attr('string'),
		postSlideBgthumb: attr('string'), // internal

		// woocommmerce settings
		wcOnlyInstock   : attr('boolean'),
  		wcOnlyFeatured  : attr('boolean'),
  		wcOnlyOnsale    : attr('boolean')
	})

	/**
	 * Slider Slide Model
	 */
	MSPanel.Slide = Ember.Model.extend({

		/*	Internal Options */
		// -------------------------------------------------------------------
		id 			: attr('number'),
		timeline_h	: attr('number' , {defaultValue: 200}),
		bgThumb 	: attr(WPPath),
		thumbOrginal : attr(WPPath),
		// -------------------------------------------------------------------

        isOverlayLayers : attr('boolean', {defaultValue: false}),

		// General
		order			: attr('number'),
		ishide			: attr('boolean'),
		bg				: attr(WPPath),
		duration		: attr('number', {defaultValue : defaults.duration || 3}),
        msId            : attr('string'), // slide id

		fillMode		: attr('string', {defaultValue : defaults.slideFillMode || 'fill'}),
		thumb			: attr(WPPath),
		info			: attr('string'),
		link			: attr('string'),
		linkTarget		: attr('string'),
		linkTitle		: attr('string'),
		linkRel 		: attr('string'),
		linkClass		: attr('string'),
		linkId 			: attr('string'),
 		video			: attr('string'),
		bgColor 		: attr('string'),
		autoplayVideo 	: attr('boolean'),

		pattern			: attr('string'),
		colorOverlay 	: attr('string'),

		bgv_mp4			: attr('string'),
		bgv_ogg			: attr('string'),
		bgv_webm		: attr('string'),
		bgv_fillmode	: attr('string' , {defaultValue: defaults.sliderVideoFillMode || 'fill'}),

		bgv_loop		: attr('boolean', {defaultValue:defaults.slideVideoLoop /*true*/}),
		bgv_mute		: attr('boolean', {defaultValue:defaults.slideVideoMute /*true*/}),
		bgv_autopause 	: attr('boolean', {defaultValue:defaults.slideVideoAutopause /*false*/}),

		cssId			: attr('string'),
		cssClass 		: attr('string'),
		bgAlt 			: attr('string'),
        bgTitle         : attr('string'),
		/**
		 * Slide Layers
		 * Object format: layer_ids:[1,2,3,...]
		 */
		layers 		: hasMany('MSPanel.Layer', {key: 'layer_ids'})

	});

	/**
	 * Slide Layer Model
	 */
	MSPanel.Layer = Ember.Model.extend({

		/*	Internal Options */
		// -------------------------------------------------------------------
		id 				: attr('number'),
		name 			: attr('string'),
		isLocked		: attr('boolean' , {defaultValue: false}),
		isHided			: attr('boolean' , {defaultValue: false}),
		isSoloed		: attr('boolean' , {defaultValue: false}),
		slide 			: belongsTo('MSPanel.Slide', {key: 'slide'}),
		styleModel		: belongsTo('MSPanel.Style', {key: 'styleModel', embedded:false}),

		showEffect 		: belongsTo('MSPanel.Effect', {key: 'showEffect', embedded:false} ),
		showTransform 	: attr('string' , {defaultValue: ''}), // tranform style
		showOrigin		: attr('string' , {defaultValue: ''}), // transform origin
		showFade		: attr('boolean', {defaultValue: true}),

		hideEffect		: belongsTo('MSPanel.Effect', {key: 'hideEffect', embedded:false}),
		hideTransform 	: attr('string' , {defaultValue: ''}), // transform style
		hideOrigin		: attr('string' , {defaultValue: ''}),
		hideFade		: attr('boolean', {defaultValue: true}),
		imgThumb 		: attr(WPPath),

		stageOffsetX 	: attr('number', {defaultValue: 0}),
		stageOffsetY	: attr('number', {defaultValue: 0}),


        // -------------------------------------------------------------------

        // General
        order       : attr('number'),
        type        : attr('string'), // values: text, video, image, hotspot
        position    : attr('string', {defaultValue:'normal'}),
        msId        : attr('string'), // layer id

        // misc
        cssClass    : attr('string'), // custom css class name
        cssId       : attr('string'), // custom css id
        title       : attr('string'), // title attribute
        rel         : attr('string'), // rel attribute
        noSwipe     : attr('string', {defaultValue: false}),

        // layer content
        content     : attr('string' , {defaultValue : defaults.layerContent || 'Lorem Ipsum'}), // for text, hotspot
        img         : attr(WPPath), // for image and video
        imgAlt      : attr('string'),
        video       : attr('string', {defaultValue: 'http://player.vimeo.com/video/11721242'}), // video iframe path
        align       : attr('string', {defaultValue: 'top'}),

        useAction   : attr('boolean', {defaultValue: false}),
        action      : attr('string'),
        toSlide     : attr('number'), // gotoSlide action parameter
        link        : attr('string'),
        linkTarget  : attr('string'),

        scrollDuration  : attr('number', {defaultValue: 2}), // scrollToEnd action parameter
        scrollTarget    : attr('string'), // scrollTo action parameter

        actionTargetLayer : attr('string'), // the target layer id

        // Position
        offsetX     : attr('number' , {defaultValue : 0}),
        offsetY     : attr('number' , {defaultValue : 0}),
        width       : attr('number'),
        height      : attr('number'),
        resize      : attr('boolean', {defaultValue : true}),
        fixed       : attr('boolean', {defaultValue : false}),
        widthlimit  : attr('number' , {defaultValue : '0'}),
        origin      : attr('string' , {defaultValue : 'tl'}),

        stayHover   : attr('boolean', {defaultValue: true}), // hotspot only

        // layer style class name
        className   : attr('string'),

        // layer parallax effect
        parallax    : attr('string'),

        // wait for action trigger
        wait        : attr('boolean', {defaultValue: defaults.layerWait /* false */ }),

        // mask
        masked          : attr('boolean'),
        maskCustomSize  : attr('boolean'),
        maskWidth       : attr('number'), // data-mask-width
        maskHeight      : attr('number'), // data-mask-height

        // only for overlay layers, show or hide the layer over the specified slides
        overlayTargetSlides : attr('string'),
        overlayTargetSlidesAction : attr('string', {defaultValue: 'show'}),

		// Show Effect
		showDuration	: attr('number' , {defaultValue : 1}),
		showDelay		: attr('number' , {defaultValue : 0}),
		showEase		: attr('string' , {defaultValue : 'easeOutQuint'}),
		showEffFunc		: attr('string'), // used by master slider

		// Hide Effect
		useHide 		: attr('boolean', {defaultValue : false}),
		hideDuration	: attr('number' , {defaultValue : 1}),
		hideDelay		: attr('number' , {defaultValue : 1}),
		hideEase		: attr('string' , {defaultValue : 'easeOutQuint'}),
		hideEffFunc		: attr('string'), // used by master slider

		// btn layer only
		btnClass		: attr('string', {defaultValue : 'ms-btn ms-default-btn'}),

		autoplayVideo	: attr('boolean') // video layer only


		//style 		: attr('string' , {defaultValue: ''}),
	});

	/**
	 * Layer Styles Model
	 */
	MSPanel.Style = Ember.Model.extend({

		/*	Internal Options */
		// -------------------------------------------------------------------
		id 				: attr('number'),
		name 			: attr('string'),
		// -------------------------------------------------------------------

		/**
		 * style type
		 * values:
		 * 		preset,  preset style
		 * 		copy,    on copy of preset style used in mspanel
		 * 		custom,  layer custom style
		 */
		type 			: attr('string'),

		/**
		 * style class name
		 * format:
		 * 		preset->  msp-preset-{{presetID}}
		 * 		custom->  msp-cn-{{sliderID}}-{{layer-ID}}
		 */
		className		: attr('string'),
		//css 			: attr('string'),

		backgroundColor	: attr('string'),

		// padding
		paddingTop		: attr('number'),
		paddingRight	: attr('number'),
		paddingBottom	: attr('number'),
		paddingLeft 	: attr('number'),

		// border
		borderTop		: attr('number'),
		borderRight		: attr('number'),
		borderBottom	: attr('number'),
		borderLeft 		: attr('number'),

		borderColor		: attr('string'),
		borderRadius	: attr('number'),
		borderStyle		: attr('string'),

		//Typography
		fontFamily		: attr('string'),
		fontWeight		: attr('string' , {defaultValue: 'normal'}),
		fontSize		: attr('number'),

		textAlign		: attr('string'),
		letterSpacing	: attr('number'),
		lineHeight		: attr('string' , {defaultValue: 'normal'}),
		whiteSpace		: attr('string'),
		color			: attr('string'),

		// custom style
		custom				: attr('string')
	});

	MSPanel.PresetStyle = MSPanel.Style.extend({});

	/**
	 * Layer Effect Model
	 */
	MSPanel.Effect = Ember.Model.extend({

		/*	Internal Options */
		// -------------------------------------------------------------------
		id 				: attr('number'),
		name 			: attr('string'),
		// -------------------------------------------------------------------

		type 			: attr('string'), // preset or null

		fade 			: attr('boolean', {defaultValue: true}),

		translateX		: attr('number'),
		translateY		: attr('number'),
		translateZ		: attr('number'),

		scaleX			: attr('number'),
		scaleY			: attr('number'),

		rotate			: attr('number'),
		rotateX			: attr('number'),
		rotateY			: attr('number'),
		rotateZ			: attr('number'),

		skewX			: attr('number'),
		skewY			: attr('number'),

		originX			: attr('number'),
		originY			: attr('number'),
		originZ			: attr('number')

		// effect function for master slider
		//msEffect		: attr('string'),

	});

	MSPanel.PresetEffect = MSPanel.Effect.extend({});

	/**
	 * Slider control model
	 */
	MSPanel.Control = Ember.Model.extend({

		/*	Internal Options */
		// -------------------------------------------------------------------
		id 			: attr('number'),
		label		: attr('string'),
		// -------------------------------------------------------------------

		// general
		name 		: attr('string'),

		autoHide	: attr('boolean', {defaultValue: true}), // in JS autohide
		overVideo	: attr('boolean', {defaultValue: true}),

		// misc
		cssClass	: attr('string'),
		cssId		: attr('string'),

		// align and margin
		//align 		: attr('string'), // values : t, r, b, l \ tl,tr,bl,br (for circle timer)
		//inset 		: attr('boolean'), // in slider or out of slider
		margin		: attr('number'), // element margin from top ,...

		// used for bullets, scrollbar and thumbs/tabs
		dir			: attr('string'), // h or v

		// circle timer options
		color 		: attr('string'), // also scrollbar | timebar
		radius 		: attr('number'),
		stroke 		: attr('number'),

		// thumbs/tabs
		speed		: attr('number'),
		space 		: attr('number'),
		type 		: attr('string'), // tab or thumb
		insertThumb : attr('boolean'),
		arrows 		: attr('boolean'),
		hoverChange : attr('boolean'),

		width 		: attr('number'), /// thumblist | scrollbar | timebar
 		height  	: attr('number'), // thumbelist

 		align 		: attr('string'), // thumblist | scrollbar | bullets | timebar | slideinfo
 		inset 		: attr('boolean'), // thumblist | scrollbar | timebar | slideinfo

 		size 		: attr('number'), // slide info

 		hideUnder 	: attr('number'),

 		fillMode 	: attr('string')

	});

	/**
	 * Slider Callback functions
	 */
	MSPanel.Callback = Ember.Model.extend({

		/*	Internal Options */
		// -------------------------------------------------------------------
		id 			: attr('number'),
		label		: attr('string'),
		// -------------------------------------------------------------------

		name 		: attr('string'),
		content 	: attr('string', {defaultValue: 'function(event){\n  var api = event.target;\n}'})

	});

	/**
	 * Button Class Names
	 * @since 1.9.0
	 */

	 MSPanel.ButtonStyle = Ember.Model.extend({

	 	/*	Internal Options */
		// -------------------------------------------------------------
		id 			: attr('number'),
		// -------------------------------------------------------------

		className 	: attr('string'),
		normal		: attr('string'),
		hover 		: attr('string'),
		active		: attr('string'),
		style 		: attr('string', {defaultValue: 'ms-btn-box'}),
		size 		: attr('string', {defaultValue: 'ms-btn-n'})

	 });


	var decodeFix = function(str){
		var decoded = B64.decode(str);
		return decoded.slice(0, decoded.lastIndexOf('}')+1);
	}

	MSPanel.data   = __MSP_DATA ? JSON.parse(decodeFix(__MSP_DATA)) : {meta:{}};
	MSPanel.PSData = __MSP_PRESET_STYLE  ? JSON.parse(decodeFix(__MSP_PRESET_STYLE))  : {meta:{}};
	MSPanel.PEData = __MSP_PRESET_EFFECT ? JSON.parse(decodeFix(__MSP_PRESET_EFFECT)) : {meta:{}};
	MSPanel.PBData = __MSP_PRESET_BUTTON ? JSON.parse(decodeFix(__MSP_PRESET_BUTTON)) : {meta:{}};

	MSPanel.Settings.adapter = Ember.OfflineAdapter.create({applicationData:MSPanel.data});
	MSPanel.Slide.adapter = Ember.OfflineAdapter.create({applicationData:MSPanel.data});
	MSPanel.Layer.adapter = Ember.OfflineAdapter.create({applicationData:MSPanel.data});
	MSPanel.Style.adapter = Ember.OfflineAdapter.create({applicationData:MSPanel.data});
	MSPanel.Effect.adapter = Ember.OfflineAdapter.create({applicationData:MSPanel.data});
	MSPanel.Control.adapter = Ember.OfflineAdapter.create({applicationData:MSPanel.data});
	MSPanel.Callback.adapter = Ember.OfflineAdapter.create({applicationData:MSPanel.data});
	MSPanel.PresetStyle.adapter = Ember.OfflineAdapter.create({applicationData:MSPanel.PSData});
	MSPanel.PresetEffect.adapter = Ember.OfflineAdapter.create({applicationData:MSPanel.PEData});
	MSPanel.ButtonStyle.adapter = Ember.OfflineAdapter.create({applicationData:MSPanel.PBData});

})();

/* ================== src/js/mspanel/models/SliderTemplates.js =================== */
MSPanel.SliderTemplates = [
	
	{
		name:'Custom Template',
		value:'custom',
		className: '',
		img: __MSP_PATH + 'images/templates/custom.gif',
		controls: null
	},
	
	{	
		name:'3D Flow Carousel',
		value:'3d-flow-carousel',
		className:'ms-caro3d-template',
		img: __MSP_PATH + 'images/templates/3d-flow-carousel.png',
		settings: {
			space:0,
			loop:true,
			trView:'flow',
			layout:'partialview',
			dir:'h',
			wheel:false
		},
		controls: null
	},

	{	
		name:'3D Wave Carousel',
		value:'3d-wave-carousel',
		className:'ms-caro3d-template',
		img: __MSP_PATH + 'images/templates/3d-wave-carousel.png',
		settings: {
			space:0,
			loop:true,
			trView:'flow',
			layout:'partialview',
			dir:'h',
			wheel:false
		},
		controls: null
	},

	{	
		name:'Image Gallery with Thumbs',
		value:'image-gallery',
		className:'ms-gallery-template',
		img: __MSP_PATH + 'images/templates/image-gallery.png',
		settings: {
			space:0,
			trView:'basic',
			skin:'ms-skin-black-2 round-skin'
		},
		controls: null,
		disableControls: true
	},

	{	
		name:'Slider with Bottom Aligned Thumbs',
		value:'slider-horizontal-thumbs',
		className:'ms-thumbs-template',
		img: __MSP_PATH + 'images/templates/slider-bottom-thumbs.png',
		settings: {
			trView:'scale',
			space:0
		},
		controls: {
			arrows: {},
			scrollbar: {dir:'h'},
			thumblist: {autohide:false ,dir:'h',arrows:false, align:'bottom', width:127, height:137, margin:5, space:5}
		}
	},

	{	
		name:'Slider with Top Aligned Thumbs',
		value:'slider-top-thumbs',
		className:'ms-thumbs-template',
		img: __MSP_PATH + 'images/templates/slider-top-thumbs.png',
		settings: {
			trView:'scale',
			space:0
		},
		controls: {
			arrows: {},
			scrollbar: {dir:'h'},
			thumblist: {autohide:false ,dir:'h',arrows:false, align:'top', width:127, height:137, margin:5, space:5}
		}
	},

	{	
		name:'Slider with Right Aligned Thumbs',
		value:'slider-vertical-thumbs',
		className:'ms-thumbs-template',
		img: __MSP_PATH + 'images/templates/slider-right-thumbs.png',
		settings: null,
		controls: {
			arrows: {},
			scrollbar: {dir:'v'},
			thumblist: {autohide:false ,dir:'v',arrows:false, align:'right', width:127, height:137, margin:5, space:5}
		}
	},

	{	
		name:'Slider with Left Aligned Thumbs',
		value:'slider-left-thumbs',
		className:'ms-thumbs-template',
		img: __MSP_PATH + 'images/templates/slider-left-thumbs.png',
		settings: null,
		controls: {
			arrows: {},
			scrollbar: {dir:'v'},
			thumblist: {autohide:false ,dir:'v',arrows:false, align:'left', width:127, height:137, margin:5, space:5}
		}
	},

	{	
		name:'Slider with Horizontal Tabs',
		value:'slider-horizontal-tabs',
		className:'ms-tabs-template',
		img: __MSP_PATH + 'images/templates/slider-horizontal-tabs.png',
		settings: null,
		controls: {
			arrows: {},
			circletimer: {color:"#FFFFFF" , stroke:9},
			thumblist: {autohide:false ,dir:'h', type:'tabs',width:240,height:120, align:'bottom', space:0 , margin:-12, hideUnder:400}
		}
	},

	{	
		name:'Slider with Vertical Tabs',
		value:'slider-vertical-tabs',
		className:'ms-tabs-template',
		img: __MSP_PATH + 'images/templates/slider-vertical-tabs.png',
		settings: null,
		controls: {
			arrows: {},
			circletimer: {color:"#FFFFFF" , stroke:9},
			thumblist: {autohide:false ,dir:'v', type:'tabs', align:'right', margin:-12, space:0, width:229, height:100, hideUnder:550}
		}
	},

	{	
		name:'Partial View Slider V1',
		value:'partial-1',
		className:'ms-partialview-template',
		img: __MSP_PATH + 'images/templates/partial-1.png',
		settings: {
			space:10,
	        loop:true,
	        trView:'partialWave',
	        layout:'partialview',
	        dir:'h'
		},
		controls: {
			arrows: {},
			circletimer: {color:"#FFFFFF" , stroke:9},
			slideinfo: {autohide:false, align:'bottom', size:160}
		}
	},

	{	
		name:'Partial View Slider V2',
		value:'partial-2',
		className:'ms-partialview-template',
		img: __MSP_PATH + 'images/templates/partial-2.png',
		settings: {
			space:10,
	        loop:true,
	        trView:'fadeWave',
	        layout:'partialview',
	        dir:'h'
		},
		controls: {
			arrows: {},
			circletimer: {color:"#FFFFFF" , stroke:9},
			slideinfo: {autohide:false, align:'bottom', size:160}
		}
	},

	{	
		name:'Partial View Slider V3',
		value:'partial-3',
		className:'ms-partialview-template',
		img: __MSP_PATH + 'images/templates/partial-3.png',
		settings: {
			space:10,
	        loop:true,
	        trView:'fadeFlow',
	        layout:'partialview',
	        dir:'h'
		},
		controls: {
			arrows: {},
			circletimer: {color:"#FFFFFF" , stroke:9},
			slideinfo: {autohide:false, align:'bottom', size:160}
		}
	},

	{	
		name:'Slider in Display',
		value:'display',
		className:'ms-display-template',
		img: __MSP_PATH + 'images/templates/display.png',
		settings: {
			width:507,
	        height:286,
	        speed:20,
	        space:2,
	        trView:'flow',
	        dir:'h',
	        layout:'boxed'
		},
		controls: {
			arrows: {},
			circletimer: {color:"#FFFFFF" , stroke:9},
			bullets: {autohide:false}
		},
		disableControls: true
	},

	{	
		name:'Slider in Flat Display',
		value:'flat-display',
		className:'ms-display-template',
		img: __MSP_PATH + 'images/templates/flat-display.png',
		settings: {
			width:507,
	        height:286,
	        speed:20,
	        space:2,
	        trView:'flow',
	        dir:'h',
	        layout:'boxed'
		},
		controls: {
			arrows: {},
			circletimer: {color:"#FFFFFF" , stroke:9},
			bullets: {autohide:false}
		},
		disableControls: true
	},

	{	
		name:'Slider in Laptop',
		value:'laptop',
		className:'ms-laptop-template',
		img: __MSP_PATH + 'images/templates/laptop.png',
		settings: {
			width:492,
	        height:309,
	        speed:20,
	        space:2,
	        trView:'mask',
	        dir:'h',
	        layout:'boxed'
		},
		controls: {
			arrows: {},
			circletimer: {color:"#FFFFFF" , stroke:9},
			bullets: {autohide:false}
		},
		disableControls: true
	},

	{	
		name:'Slider in Flat Laptop',
		value:'flat-laptop',
		className:'ms-laptop-template',
		img: __MSP_PATH + 'images/templates/flat-laptop.png',
		settings: {
			width:492,
	        height:309,
	        speed:20,
	        space:2,
	        trView:'mask',
	        dir:'h',
	        layout:'boxed'
		},
		controls: {
			arrows: {},
			circletimer: {color:"#FFFFFF" , stroke:9},
			bullets: {autohide:false}
		},
		disableControls: true
	},

	{	
		name:'Slider in Tablet',
		value:'tablet',
		className:'ms-tablet-template',
		img: __MSP_PATH + 'images/templates/tablet.png',
		settings: {
			width:400,
	        height:534,
	        speed:20,
	        space:2,
	        trView:'wave',
	        dir:'h',
	        layout:'boxed'
		},
		controls: {
			arrows: {},
			circletimer: {color:"#FFFFFF" , stroke:9},
			bullets: {autohide:false}
		},
		disableControls: true
	},

	{	
		name:'Slider in Flat Tablet',
		value:'flat-tablet',
		className:'ms-tablet-template',
		img: __MSP_PATH + 'images/templates/flat-tablet.png',
		settings: {
			width:400,
	        height:534,
	        speed:20,
	        space:2,
	        trView:'basic',
	        dir:'h',
	        layout:'boxed'
		},
		controls: {
			arrows: {},
			circletimer: {color:"#FFFFFF" , stroke:9},
			bullets: {autohide:false}
		},
		disableControls: true
	},

	{	
		name:'Slider in Landscape Tablet',
		value:'tablet-land',
		className:'ms-tablet-template ms-tablet-land',
		img: __MSP_PATH + 'images/templates/tablet-land.png',
		settings: {
			width:632,
	        height:476,
	        speed:20,
	        space:2,
	        trView:'mask',
	        dir:'h',
	        layout:'boxed'
		},
		controls: {
			arrows: {},
			circletimer: {color:"#FFFFFF" , stroke:9},
			bullets: {autohide:false}
		},
		disableControls: true
	},

	{	
		name:'Slider in Flat Landscape Tablet',
		value:'flat-tablet-land',
		className:'ms-tablet-template ms-tablet-land',
		img: __MSP_PATH + 'images/templates/flat-tablet-land.png',
		settings: {
			width:632,
	        height:476,
	        speed:20,
	        space:2,
	        trView:'mask',
	        dir:'h',
	        layout:'boxed'
		},
		controls: {
			arrows: {},
			circletimer: {color:"#FFFFFF" , stroke:9},
			bullets: {autohide:false}
		},
		disableControls: true
	},

	{	
		name:'Slider in Smart Phone',
		value:'phone',
		className:'ms-phone-template',
		img: __MSP_PATH + 'images/templates/phone.png',
		settings: {
			width:258,
	        height:456,
	        speed:20,
	        space:2,
	        trView:'wave',
	        dir:'h',
	        layout:'boxed'
		},
		controls: {
			arrows: {},
			circletimer: {color:"#FFFFFF" , stroke:9},
			bullets: {autohide:false}
		},
		disableControls: true
	},

	{	
		name:'Slider in Flat Smart Phone',
		value:'flat-phone',
		className:'ms-phone-template',
		img: __MSP_PATH + 'images/templates/flat-phone.png',
		settings: {
			width:258,
	        height:456,
	        speed:20,
	        space:2,
	        trView:'basic',
	        dir:'h',
	        layout:'boxed'
		},
		controls: {
			arrows: {},
			circletimer: {color:"#FFFFFF" , stroke:9},
			bullets: {autohide:false}
		},
		disableControls: true
	},

	{	
		name:'Slider in Landscape Smart Phone',
		value:'phone-land',
		className:'ms-phone-template ms-phone-land',
		img: __MSP_PATH + 'images/templates/phone-land.png',
		settings: {
			width:456,
	        height:258,
	        speed:20,
	        space:2,
	        trView:'mask',
	        dir:'h',
	        layout:'boxed'
		},
		controls: {
			arrows: {},
			circletimer: {color:"#FFFFFF" , stroke:9},
			bullets: {autohide:false}
		},
		disableControls: true
	},

	{	
		name:'Slider in Flat Landscape Smart Phone',
		value:'flat-phone-land',
		className:'ms-phone-template ms-phone-land',
		img: __MSP_PATH + 'images/templates/flat-phone-land.png',
		settings: {
			width:456,
	        height:258,
	        speed:20,
	        space:2,
	        trView:'mask',
	        dir:'h',
	        layout:'boxed'
		},
		controls: {
			arrows: {},
			bullets: {autohide:false}
		},
		disableControls: true
	},

	{	
		name:'Vertical Slider',
		value:'vertical-slider',
		className:'ms-vertical-template',
		img: __MSP_PATH + 'images/templates/vertical-slider.png',
		settings: {
			space:5,
	        dir:'v'
		},
		controls: {
			arrows: {},
			scrollbar: {dir:'v'},
			circletimer: {color:"#FFFFFF" , stroke:9},
			thumblist : {autohide:false ,dir:'v',space:5,margin:5,align:'right'}
		}
	},

	{	
		name:'Staff Carousel V1',
		value:'staff-1',
		className:'ms-staff-carousel',
		img: __MSP_PATH + 'images/templates/staff-1.png',
		settings: {
			loop:true,
	        width:240,
	        height:240,
	        speed:20,
	        trView:'focus',
	        layout:'partialview',
	        space:0,
	        wheel:true,
	        dir:'h'
		},
		controls: {
			arrows: {},
			slideinfo: {autohide:false, align:'bottom', size:160}
		}
	},

	{	
		name:'Staff Carousel V2',
		value:'staff-2',
		className:'ms-staff-carousel',
		img: __MSP_PATH + 'images/templates/staff-2.png',
		settings: {
			loop:true,
	        width:240,
	        height:240,
	        speed:20,
	        trView:'fadeBasic',
	        layout:'partialview',
	        space:0,
	        dir:'h'
		},
		controls: {
			arrows: {},
			slideinfo: {autohide:false, align:'bottom', size:160}
		}
	},

	{	
		name:'Staff Carousel V3',
		value:'staff-3',
		className:'ms-staff-carousel ms-round',
		img: __MSP_PATH + 'images/templates/staff-3.png',
		settings: {
			loop:true,
	        width:240,
	        height:240,
	        speed:20,
	        trView:'focus',
	        layout:'partialview',
	        space:0,
	        space:35,
	        dir:'h'
		},
		controls: {
			arrows: {},
			slideinfo: {autohide:false, align:'bottom', size:160}
		}
	},

	{	
		name:'Staff Carousel V4',
		value:'staff-4',
		className:'ms-staff-carousel ms-round',
		img: __MSP_PATH + 'images/templates/staff-4.png',
		settings: {
			loop:true,
	        width:240,
	        height:240,
	        speed:20,
	        trView:'fadeBasic',
	        layout:'partialview',
	        space:0,
	        space:45,
	        dir:'h'
		},
		controls: {
			arrows: {},
			slideinfo: {autohide:false, align:'bottom', size:160}
		}
	},

	{	
		name:'Staff Carousel V5',
		value:'staff-5',
		className:'ms-staff-carousel',
		img: __MSP_PATH + 'images/templates/staff-5.png',
		settings: {
			loop:true,
	        width:240,
	        height:240,
	        speed:20,
	        trView:'wave',
	        layout:'partialview',
	        space:0,
	        wheel:true,
	        dir:'h'
		},
		controls: {
			arrows: {},
			slideinfo: {autohide:false, align:'bottom', size:160}
		}
	},

	{	
		name:'Staff Carousel V6',
		value:'staff-6',
		className:'ms-staff-carousel',
		img: __MSP_PATH + 'images/templates/staff-6.png',
		settings: {
			loop:true,
	        width:240,
	        height:240,
	        speed:20,
	        trView:'flow',
	        layout:'partialview',
	        space:0,
	        wheel:true,
	        dir:'h'
		},
		controls: {
			arrows: {},
			slideinfo: {autohide:false, align:'bottom', size:160}
		}
	},
];

/* ================== src/js/mspanel/models/FixtureData.js =================== */
MSPanel.Settings.FIXTURES = [{
	id 		: 1,
	name 	: 'slider 1',
}];

MSPanel.Slide.FIXTURES = [];

MSPanel.Layer.FIXTURES = [];
MSPanel.Style.FIXTURES = [];

/* ================== src/js/mspanel/views/UIViews.js =================== */
/* ---------------------------------------------------------
                        Slideframe
------------------------------------------------------------*/
MSPanel.SlideFrame = Ember.View.extend({
    classNames  : ['msp-slideframe'],
    classNameBindings: ['selected:active'],
    selected    : false,
    thumb_src   : '',
    showbtnclass : 'msp-ico msp-ico-whitehide',

    template    : Ember.Handlebars.compile('<div class="msp-img-cont">'+
                                                '{{#if view.hasImg}}'+
                                                    '<div class="msp-imgselect-preview" {{bind-attr style=view.preview}}></div>'+
                                                '{{/if}}'+
                                            '</div>'+
                                            '<span class="msp-frame-slideorder">#{{view.order}}</span>'+
                                          '<div class="msp-framehandle">'+
                                            '<ul>'+
                                              '<li><a title="'+__MSP_LAN.ui_001+'" href="#" {{action "hideswitch" target=view}}><span {{bind-attr class=view.showbtnclass}}></span></a></li>'+
                                              '<li><a title="'+__MSP_LAN.ui_002+'" href="#" {{action "duplicate" target=view}}><span class="msp-ico msp-ico-whiteduplicate"></span></a></li>'+
                                              '<li><a title="'+__MSP_LAN.ui_003+'" href="#" {{action "remove" target=view}}><span class="msp-ico msp-ico-whiteremove"></span></a></li>'+
                                            '</ul>'+
                                          '</div>'),

    click : function(event){
        this.get('controller').send('select' , this.get('slide'));
    },

    onValueChanged: function(){
        var hasImg = !Ember.isEmpty(this.get('slide.bg'));
        var hasThumb = !Ember.isEmpty(this.get('slide.thumb'));
        this.beginPropertyChanges();
        this.set('hasImg', hasImg || hasThumb);
        if(hasImg){
            this.set('preview', 'background-image:url(' + this.get('slide.bgThumb') + ');');
        } else if (hasThumb) {
            this.set('preview', 'background-image:url('+ this.get('slide.thumb')+');');
        }
        this.endPropertyChanges();
    }.observes('slide.bg','slide.thumb').on('didInsertElement'),

    onSelect : function(){
        var slide = this.get('slide');

        this.set('selected' , slide === this.get('controller.currentSlide'));

    }.observes('controller.currentSlide').on('init'),

    hideChange : function(){
        if(this.get('slide.ishide'))
            this.set('showbtnclass' , 'msp-ico msp-ico-whitehide msp-ico-whiteshow');
        else
            this.set('showbtnclass' , 'msp-ico msp-ico-whitehide');

    }.observes('slide.ishide').on('init'),

    order: function(){
        return this.get('slide.order') + 1;
    }.property('slide.order'),

    actions : {
        duplicate : function(){
            this.get('controller').duplicateSlide(this.get('slide'));
        },

        hideswitch : function(){
            this.set('slide.ishide' , !this.get('slide.ishide'));
        },

        remove : function(){
            if(confirm(__MSP_LAN.ui_004))
                 this.get('controller').removeSlide(this.get('slide'));
        }
    }
});

/* ---------------------------------------------------------
                        SlideList
------------------------------------------------------------*/
MSPanel.SlideList = Ember.View.extend({
    tagName : 'div',
    classNames : ['msp-slides-container'],

    template : Ember.Handlebars.compile(
            '<div {{bind-attr class="view.overlaySelected:active :msp-slideframe :msp-overlay-layers "}} {{action "switchToOverlays" target=view}}>'+
            '   <div class="msp-img-cont">'+
            '   </div>'+
            '   <span class="msp-frame-slideorder">'+ ( __MSP_LAN.ui_044 || 'Overlay Layers' ) +'</span>'+
            ' <div class="msp-framehandle"></div>'+
            '</div> <div class="msp-slide-spliter"></div>'+
            '<ul class="msp-slides sortable">' +
            '{{#each item in controller}}'+
                '{{#if item.isOverlayLayers}}{{else}}'+
                    '<li class="msp-slideframe-item" {{bind-attr data-id=item.id}}>{{view MSPanel.SlideFrame slide=item}}</li>'+
                '{{/if}}'+
            '{{/each}}'+
            '<li class="msp-addslide-cont">'+
              '<div class="msp-addslide" {{action "addSlides"}}>'+
                    '<span class="msp-ico msp-ico-grayaddlarge"></span>'+
                    '<span class="msp-addslide-label">Add Slide</span>'+
              '</div>'+
           '</li> </ul>'),

    didInsertElement : function(){

        var that = this;

        this.$().find('.sortable').sortable({
            placeholder: "msp-frames-srtplaceholder",
            items: ">li:not(.msp-addslide-cont)",
            delay: 100,
            update : function(event , ui){that.updateSort();},
            create: function(event, ui){that.updateSort();}
        });

    },

    updateSort: function(){
        var indexes = {};
        $('.msp-slideframe-item').each(function(index) {
          indexes[$(this).data('id')] = index;
        });
        this.$().find('.sortable').sortable('cancel');
        this.get('controller').updateSlidesSort(indexes);
    },

    onSelect : function(){
        this.set('overlaySelected', this.get('controller.currentSlide.isOverlayLayers'));
    }.observes('controller.currentSlide').on('init'),

    actions : {
        switchToOverlays : function(){
            if ( !this.get('controller.currentSlide.isOverlayLayers') ) {
                this.set('controller.currentSlide', this.get('controller.overlayLayersSlide'));
            }
        }
    }
});


/* ---------------------------------------------------------
                        ImgSelect
------------------------------------------------------------*/


/*
var frame; // to store already used upload frame

$upload_btn.on( 'click', function() {
    var $this  = $(this);
    // get input field (the image src field)
    var $input = $this.siblings('input[type="text"]');

    // If the frame already exists, re-open it.
    if ( frame ) {
        frame.open();
        return;
    }

    var frame = wp.media.frames.frame = wp.media({
        title: "Select Image", // the select button label in media uploader
        multiple: false,       // use single image upload or multiple?
        frame: 'select',
        library: { type: 'image' },
        button : { text : 'Add Image' }
    });

    // on "Add Image" button clicked in media uploader
    frame.on( 'select', function() {
        var attachment = frame.state().get('selection').first().toJSON();
        $input.val(attachment.url).trigger('change'); // insert attachment url in our input field
    });

    // open media uploader
    frame.open();
});
 */


MSPanel.ImgSelect = Ember.View.extend({
    classNames : ['msp-imgselect'],
    value : '',
    hasImg : false,
    frame: null,
    slideBg: false,
    template : Ember.Handlebars.compile('<div class="msp-img-cont">'+
                                            '{{#if view.hasImg}}'+
                                                '<div class="msp-imgselect-preview" {{bind-attr style=view.preview}})"></div>'+
                                            '{{/if}}'+
                                        '</div>'+
                                        '{{#if view.hasImg}}'+
                                            '<button {{action removeImg target="view"}} class="msp-img-btn"><span class="msp-ico msp-ico-grayremove"></span></button>'+
                                        '{{else}}'+
                                            '<button {{action addImg target="view"}} class="msp-img-btn"><span class="msp-ico msp-ico-grayadd"></span></button>'+
                                        '{{/if}}'),

    willDestroyElement: function(){
        var frame = this.get('frame');

        if(frame){
            frame.detach();
            frame.remove();
            frame = null;
            this.set('frame', null);
        }
    },

    onValueChanged: function(){
        this.beginPropertyChanges();
        this.set('hasImg' , !Ember.isEmpty(this.get('value')));
        this.set('preview', 'background-image:url(' + this.get('thumb') + ');') ;
        this.endPropertyChanges();
    }.observes('value').on('didInsertElement'),

    actions : {
        removeImg : function(){
            this.beginPropertyChanges();
            this.set('value' , undefined);
            this.set('thumb' , undefined);
            this.endPropertyChanges();
        },

        addImg : function(){
            if( typeof wp === 'undefined'){
                return;
            }

            var that = this,
                frame = this.get('frame');

            if ( frame ) {
                frame.open();

                return;
            }

            var frame = wp.media.frames.frame = wp.media({
                title: "Select Image", // the select button label in media uploader
                multiple: false,       // use single image upload or multiple?
                frame: 'select',
                library: { type: 'image' },
                button : { text : 'Add Image' }
            });

            // on "Add Image" button clicked in media uploader
            frame.on( 'select', function() {
                var attachment = frame.state().get('selection').first().toJSON();
                //console.log(attachment)
                //console.log(attachment)
                that.set('thumb', (attachment.sizes.thumbnail || attachment.sizes.full).url);
                that.set('value', attachment.url);
            });

            // open media uploader
            frame.open();
            this.set('frame', frame);
        }
    }
});

/* ---------------------------------------------------------
                        Selectbox
------------------------------------------------------------*/

MSPanel.Select = Ember.Select.extend({
    tagName: 'div',
    classNames: ['msp-ddlist'],
    layout: Ember.Handlebars.compile('<select>{{yield}}</select>'),
    value: null,
    width: 100,

    didInsertElement: function(){
        var that = this;
        this.$('select').on('change', function(){
            var option = that.$('select option:selected');
            that.set('value', option.attr('value'));
        }).width(this.get('width'));

        this.onValueChanged();
    },

    onValueChanged: function(){
        if( !Ember.isEmpty(this.get('value')) ){
            this.$('select').val(this.get('value'));
        }
    }.observes('value')

    /*classNames:['msp-selectbox'],
    tagName:'div',
    layout: Ember.Handlebars.compile('<select>{{yield}}</select>'),
    width:100,
    didInsertElement: function() {
        var that = this,
            isFirst = true;
        var ddslick = this.$('select').ddslick({width:this.get('width') , onSelected: function(selectedData){
                !isFirst && that.set('value' , selectedData.selectedData.value);
                isFirst = false;
        } });
        this.onValueChanged();
    },

    onValueChanged: function(){
        var that = this,
            cindex = 0;
        this.$('.dd-option-value').each(function(){
            var $this = $(this);

            if( $this.attr('value') === that.get('value') ){
                that.$('.dd-container').ddslick('select' , {index:cindex});
                return false;
            }
            cindex ++;
        });

    }.observes('value')*/
});


/* ---------------------------------------------------------
                        URLTarget
------------------------------------------------------------*/
MSPanel.URLTarget = MSPanel.Select.extend({

    onInit : function(){
        var contents = [{lable:__MSP_LAN.ui_005 , value:"_self"},
                        {lable:__MSP_LAN.ui_006 , value:"_blank"},
                        {lable:__MSP_LAN.ui_007 , value:"_parent"},
                        {lable:__MSP_LAN.ui_008 , value:"_top"}];

        this.set('content' , contents);
        this.set('optionValuePath' , "content.value");
        this.set('optionLabelPath' , "content.lable");

        this.set('width' , 200);

    }.on('init')
/*
    didInsertElement: function(){
        //this.$().css('vertical-align', 'top');
        this._super();
    }*/
});


/* ---------------------------------------------------------
                        Fillmode
------------------------------------------------------------*/
MSPanel.Fillmode = Ember.View.extend({
    classNames : ['msp-fill-dd'],
    type : 'slide', // video
    value: 'fill',
    index: 1,
    template   : Ember.Handlebars.compile('<select>{{#each item in view.contents}}'+
                                            '<option {{bind-attr value=item.value data-imagesrc=item.img}}>{{item.text}}</option>'+
                                         '{{/each}}</select>'),
    didInsertElement : function(){
        var that = this,
            isFirst = true;
        this.$('select').ddslick({width:154 , onSelected: function(selected){
            !isFirst && that.set('value' , selected.selectedData.value);
            isFirst = false;
        } });

        this.onValueChanged();
    },

    onValueChanged : function(){
        if( Ember.isEmpty(this.get('value')) ){
            return;
        }
        this.$('.dd-container').ddslick('select', {index:this.get('valuedic')[this.get('value')]});
    }.observes('value'),

    onInit : function(){
        var contents , valuedic;
        if(this.get('type') === 'slide'){

            contents = [{value:'fill'    , text:__MSP_LAN.ui_009 , img: __MSP_PATH + 'images/fill.png'      },
                        {value:'fit'     , text:__MSP_LAN.ui_010 , img: __MSP_PATH + 'images/fit.png'       },
                        {value:'center'  , text:__MSP_LAN.ui_011 , img: __MSP_PATH + 'images/center.png'    },
                        {value:'stretch' , text:__MSP_LAN.ui_012 , img: __MSP_PATH + 'images/stretch.png'   },
                        {value:'tile'    , text:__MSP_LAN.ui_013 , img: __MSP_PATH + 'images/tile.png'      }];

            valuedic = {fill:0 , fit:1 , center:2 , stretch:3 , tile:4};

        }else if(this.get('type') === 'video'){

            contents = [{value:'fill'    , text:__MSP_LAN.ui_009 , img: __MSP_PATH + 'images/fill.png'      },
                        {value:'fit'     , text:__MSP_LAN.ui_010 , img: __MSP_PATH + 'images/fit.png'       }
                        //{value:'none'  , text:__MSP_LAN.ui_013 , img:'images/none.png'        }
                        ];

            valuedic = {fill:0 , fit:1 , none:2};
        }

        this.set('contents' , contents);
        this.set('valuedic' , valuedic);
    }.on('init')

});

/* ---------------------------------------------------------
                        AddLayer
------------------------------------------------------------*/
MSPanel.AddLayer = Ember.View.extend({
    classNames : ['msp-addlayer'],

    template : Ember.Handlebars.compile('<button {{action newLayer view.value}} class="msp-add-btn msp-addlayer-btn"><span class="msp-ico msp-ico-whiteadd"></span></button>'+
                                        '<div class="msp-addlayer-dd"><select>{{#each item in view.layertypes}}'+
                                          '<option {{bind-attr value=item.value data-imagesrc=item.img}}>{{item.lable}}</option>'+
                                        '{{/each}}</select></div>'),

    didInsertElement : function(){
        var that = this;
        this.$().find('select').ddslick({width:154 , onSelected: function(selected){
            that.set('value' , selected.selectedData.value);
        } });
    },

/*  onValueChanged : function(){
        //this.$().find('.dd-container').ddslick('select', {index:this.get('valuedic')[this.get('value')]});
    }.observes('value'),*/

    onInit : function(){
        var layertypes = [];
        var clt = this.get('controller.layertypes');

        for(var i = 0 , l = clt.length; i !== l ; i++)
            layertypes.push({value:clt[i].value , lable:clt[i].lable , img: __MSP_PATH + 'images/layertypes/'+clt[i].value+'.png'});

        this.set('layertypes' , layertypes);

    }.on('init')

});

/* ---------------------------------------------------------
                        AlignBtns
------------------------------------------------------------*/
MSPanel.AlignBtns = Ember.View.extend({
    classNames : ['msp-align-btns'],
    target : null,
    template : Ember.Handlebars.compile('<button title="'+__MSP_LAN.ui_015+'" {{action "alignLayer" "top" target=view.target}} class="msp-align-btn"><span class="msp-ico msp-ico-altop"></span></button>'+
                                        '<button title="'+__MSP_LAN.ui_016+'" {{action "alignLayer" "mid" target=view.target}} class="msp-align-btn"><span class="msp-ico msp-ico-almid"></span></button>'+
                                        '<button title="'+__MSP_LAN.ui_017+'" {{action "alignLayer" "bot" target=view.target}} class="msp-align-btn"><span class="msp-ico msp-ico-albot"></span></button>'+
                                        '<div class="msp-btn-space"></div>'+
                                        '<button title="'+__MSP_LAN.ui_018+'" {{action "alignLayer" "left" target=view.target}} class="msp-align-btn"><span class="msp-ico msp-ico-alleft"></span></button>'+
                                        '<button title="'+__MSP_LAN.ui_019+'" {{action "alignLayer" "center" target=view.target}} class="msp-align-btn"><span class="msp-ico msp-ico-alcenter"></span></button>'+
                                        '<button title="'+__MSP_LAN.ui_020+'" {{action "alignLayer" "right" target=view.target}} class="msp-align-btn"><span class="msp-ico msp-ico-alright"></span></button>')

});

/* ------------------------------------------------------- *\
                    Position Origin
\* --------------------------------------------------------*/

MSPanel.PositionOrigin = Ember.View.extend({
    classNames: ['msp-origin-control'],
    layer: null,
    selectedNode: null,
    template: Ember.Handlebars.compile('<table><tbody>'+
                                            '<tr>'+
                                                '<td title="Top left" class="msp-origin-btn msp-origin-tl" data-origin="tl"></td>'+
                                                '<td title="Top center" class="msp-origin-btn msp-origin-tc" data-origin="tc"></td>'+
                                                '<td title="Top right" class="msp-origin-btn msp-origin-tr" data-origin="tr"></td>'+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td title="Middle left" class="msp-origin-btn msp-origin-ml" data-origin="ml"></td>'+
                                                '<td title="Middle center" class="msp-origin-btn msp-origin-mc" data-origin="mc"></td>'+
                                                '<td title="Middle right" class="msp-origin-btn msp-origin-mr" data-origin="mr"></td>'+
                                            '</tr>'+
                                            '<tr>'+
                                                '<td title="Bottom left" class="msp-origin-btn msp-origin-bl" data-origin="bl"></td>'+
                                                '<td title="Bottom center" class="msp-origin-btn msp-origin-bc" data-origin="bc"></td>'+
                                                '<td title="Bottom right" class="msp-origin-btn msp-origin-br" data-origin="br"></td>'+
                                            '</tr>'+
                                       '</tbody></table>'),
    didInsertElement: function(){
        var that = this;
        this.$('.msp-origin-btn').click(function(){
            if ( !Ember.isEmpty( that.get('layer') ) ) {
                that.set('layer.origin', $(this).data('origin'));
            }
        });

        this.onValueChanged();
    },

    onValueChanged: function(){
        var selectedNode = this.get('selectedNode');

        if ( !Ember.isEmpty(selectedNode) ) {
            selectedNode.removeClass('msp-origin-btn-selected');
        }

        if ( Ember.isEmpty( this.get('layer') ) ) {
            this.$().addClass('msp-origin-control-disabled')
            return;
        }

        this.$().removeClass('msp-origin-control-disabled')

        var value = this.get('layer.origin'),
            newNode = this.$('.msp-origin-' + value).addClass('msp-origin-btn-selected');

        this.set('selectedNode', newNode);

    }.observes('layer', 'layer.origin')

});

/* ------------------------------------------------------- *\
                    Buttons List
\* --------------------------------------------------------*/
MSPanel.ButtonsList = Ember.View.extend({
    classNames: ['msp-buttons-container'],

    layer: null,

    template: Ember.Handlebars.compile('{{#each button in controller.buttonClasses}}'+
                                            '<div class="msp-button-container" {{action "selectButton" button target=view}}>'+
                                                '<div class="msp-button-cell">'+
                                                    '<span {{bind-attr class=":ms-btn button.style button.size button.className"}}>Button</span>'+
                                                '</div>'+
                                            '</div>'+
                                        '{{/each}}'),
    didInsertElement: function(){
        var that = this;
        this.onValueChanged();
    },

    onValueChanged: function(){
        var className = this.get('layer.btnClass'),
            lastSelected = this.get('lastSelected');

        if( !Ember.isEmpty(className) ) {
            className = className.split(' ').pop();
            if( !Ember.isEmpty(lastSelected) ) {
                this.$('.' + lastSelected).parent().removeClass('active');
            }

            this.$('.' + className).parent().addClass('active');

            this.set('lastSelected', className);
        }

    }.observes('layer', 'layer.btnClass'),

    actions: {
        selectButton: function(button) {
            this.set('layer.btnClass', 'ms-btn ' + button.get('style') + ' ' + button.get('size') + ' ' + button.get('className'));
        }
    }

});

/* ------------------------------------------------------- *\
                    Action List
\* --------------------------------------------------------*/
MSPanel.ActionList = Ember.View.extend({
    classNames: ['msp-action-list'],

    layer: null,
    showSlideNum: null,
    showDuration: null,

    template: Ember.Handlebars.compile('{{#dropdwon-List value=view.layer.action width=180}}'+
                                            '<option value="next">'         + ( __MSP_LAN.ui_021 || 'Goto next slide' ) +'</option>'+
                                            '<option value="previous">'     + ( __MSP_LAN.ui_022 || 'Goto previous slide' ) +'</option>'+
                                            '<option value="gotoSlide">'    + ( __MSP_LAN.ui_025 || 'Goto slide' ) +'</option>'+
                                            '<option value="pause">'        + ( __MSP_LAN.ui_023 || 'Pause timer' ) +'</option>'+
                                            '<option value="resume">'       + ( __MSP_LAN.ui_024 || 'Resume timer' ) +'</option>'+
                                            '<option value="scrollToEnd">'  + ( __MSP_LAN.ui_028 || 'Scroll to bottom of slider' ) +'</option>'+
                                            '<option value="scrollTo">'     + ( __MSP_LAN.ui_030 || 'Scroll to an element in page' ) +'</option>'+
                                            '<option value="showLayer">'    + ( __MSP_LAN.ui_040 || 'Show layer' ) +'</option>'+
                                            '<option value="hideLayer">'    + ( __MSP_LAN.ui_041 || 'Hide layer' ) +'</option>'+
                                            '<option value="toggleLayer">'  + ( __MSP_LAN.ui_042 || 'Toggle layer' ) +'</option>'+
                                        '{{/dropdwon-List}}'+
                                        '{{#if view.showSlideNum}}'+
                                            '<div class="msp-form-space-med"></div>'+ ( __MSP_LAN.ui_026 || 'Slide number : ' ) +' {{number-input value=view.layer.toSlide}}'+
                                        '{{/if}}'+
                                        '{{#if view.showDuration}}'+
                                            '<div class="msp-form-space-med"></div>'+ ( __MSP_LAN.ui_029 || 'Scroll animation duration : ' ) +' {{number-input value=view.layer.scrollDuration}} s'+
                                        '{{/if}}'+
                                        '{{#if view.showTarget}}'+
                                            '<div class="msp-form-space-med"></div>'+ ( __MSP_LAN.ui_029 || 'Scroll animation duration : ' ) +' {{number-input value=view.layer.scrollDuration}} s'+
                                            '<div class="msp-form-space-med"></div>'+ ( __MSP_LAN.ui_031 || 'Target element : ' ) +' {{input value=view.layer.scrollTarget}}'+
                                        '{{/if}}'
                                        +'{{#if view.showTargetLayer}}'+
                                            '<div class="msp-form-space-med"></div>'+ ( __MSP_LAN.ui_043 || 'Target layers id : ' ) +' {{input value=view.layer.actionTargetLayer}}'+
                                            '<span style="margin-left:0.5em;">'+ ( __MSP_LAN.ui_045 || 'Add multiple layers ids separated by "|".' ) +'</span>'+
                                        '{{/if}}'
                                        ),
    onValueChanged: function(){
        var value = this.get('layer.action');
        this.set('showSlideNum', value === 'gotoSlide');
        this.set('showDuration', value === 'scrollToEnd');
        this.set('showTarget', value === 'scrollTo');
        this.set('showTargetLayer', value === 'showLayer' || value === 'hideLayer' || value === 'toggleLayer' );
    }.observes('layer', 'layer.action').on('init')

});

/* ------------------------------------------------------- *\
                    SimpleCodeBlock
\* --------------------------------------------------------*/
MSPanel.SimpleCodeBlock = Ember.View.extend({
    classNames: ['msp-shortcode-box'],
    template: Ember.Handlebars.compile('<input type="text" readonly {{bind-attr value=view.value}}>' ),
    width:150,
    didInsertElement: function(){
        this.$('input').on('click',function(){
            $(this).select();
        }).width(this.get('width'));
    }
});

/* ================== src/js/mspanel/views/SettingsView.js =================== */
/**
 * Settings Page View
 * @package MSPanel
 * @extends {Ember.View}
 */

MSPanel.SettingsView = Ember.View.extend({
	didInsertElement: function(){
		this.set('controller.mainView' , this);
	}
});

/* ================== src/js/mspanel/views/SlidesView.js =================== */
/**
 * Slides Page View
 * @package MSPanel
 * @extends {Ember.View}
 */

MSPanel.SlidesView = Ember.View.extend({
	didInsertElement: function(){
		this.set('controller.mainView' , this);
	}
});

/* ================== src/js/mspanel/views/TimelineView.js =================== */
/**
 * Master Slider Panel Timeline View
 * @version 1.0a
 * @author Averta
 */

;(function(){

	"use strict";

	/**
	 * Default one second width (pixels) in timeline and maximum time of timeline
	 * @define {number} 
	 */
	var ONE_SEC = 80, // pixels
		TIMELINE_WIDTH = 300; // seconds

	/**
	 * Timeline View
	 * 
	 * @package MSPanel
	 * @extends {Ember.View}
	 */
	MSPanel.Timeline = Ember.View.extend({
		classNames : ['msp-timeline-cont'],
		goLockAll : true,
		goSoloAll : true,
		goHideAll : true,

		template : Ember.Handlebars.compile(' <div class="msp-tl-headbar">'+
								                '<div class="msp-tl-controls">'+
								                  '<ul>'+
								                    '<li><a title="'+__MSP_LAN.tl_001+'" href="#" {{action "hideAll" target=view}}><span class="msp-ico msp-ico-blackhide"></span></a></li>'+
								                    '<li><a title="'+__MSP_LAN.tl_002+'" href="#" {{action "soloAll" target=view}}><span class="msp-ico msp-ico-blackiso"></span></a></li>'+
								                    '<li><a title="'+__MSP_LAN.tl_003+'" href="#" {{action "lockAll" target=view}}><span class="msp-ico msp-ico-blacklock"></span></a></li>'+
								                  '</ul>'+
								                  '<div class="msp-tl-current-time">{{view MSPanel.TimelineTime}}</div>'+
								                '</div>'+
								                '<div class="msp-tl-timeruler-cont">'+
								                  '{{view MSPanel.TimelineRuler timeline=view}}'+
								                '</div>'+
								               '</div>'+
								               '<div class="msp-tl-layars-cont">'+
							                	  '{{view MSPanel.TimelineLayersList timeline=view}}'+
							                	  '{{view MSPanel.TimelineFrames timeline=view}}'+
								               '</div><div id="msp-resize-handle" class="msp-tl-resizehandle">...</div>'),

		/**
		 * Setup timeline resize handle after insert elmenet by Ember.
		 */
		didInsertElement : function(){

			// resizing height of timeline
			var that = this;
			this.$('#msp-resize-handle').mousedown(function(event){
				var startH = that.get('controller.slide.timeline_h'),
					start_pos = event.pageY,
					down = true;

				$(document).mousemove(function(event) {
					if(!down) return;
					var h =  Math.max(100 , startH + event.pageY - start_pos);
 					that.set('controller.slide.timeline_h' , h);
 					that.$('.msp-tl-layars-cont').height(h);
 					that.set('scrlY' , 0);
 					event.preventDefault();
				}).mouseup(function(event){
					down = false;
				});
			});

			// update height of timeline after slide changed.
			this.onSlideChange(); 
		//	console.log(this.get('controller'))
		},

		willDestroyElement: function(){
			// save layers
			///console.log('layers')
		//	this.get('controller.model').save();
		},

		/**
		 * Update height of slider just after slide changes.
		 */
		onSlideChange : function(){
			this.$('.msp-tl-layars-cont').height(this.get('controller.slide.timeline_h'));
		}.observes('controller.slide'),

		actions : {

			// Go lock all layers.
			lockAll : function(){
				var la = this.get('goLockAll');
				this.get('controller').forEach(function(layer){
					layer.set('isLocked' , la);
				});
				this.set('goLockAll' , !la);
			},

			// Go solo all layers
			soloAll : function(){
				var sa = this.get('goSoloAll');
				this.get('controller').forEach(function(layer){
					layer.set('isSoloed' , sa);
				});
				this.set('goSoloAll' , !sa);
			},

			// Go hide of all layers
			hideAll : function(){
				var ha = this.get('goHideAll');
				this.get('controller').forEach(function(layer){
					layer.set('isHided' , ha);
				});
				this.set('goHideAll' , !ha);
			}

		}

	});

	/**
	 * View helper for formating current time of animation in timline
	 * @example 00:00.00
	 */
	MSPanel.TimelineTime = Ember.View.extend({
		tagName: 'span',
		time: '00:00.00',
		template: Ember.Handlebars.compile('{{view.time}}'),

		update : function(){
			var currentTime = this.get('controller.timelinePos'),
				mins = Math.floor(currentTime/60),
				seconds = Math.abs(mins * 60 - Math.floor(currentTime)),
				ms = Math.floor((currentTime - Math.floor(currentTime)) * 100);
				 
			this.set('time' , (mins < 10 ? '0' + mins : mins) + ':' + (seconds < 10 ? '0' + seconds : seconds) + '.' + (ms < 10 ? '0' + ms : ms));

		}.observes('controller.timelinePos')
	});


	/**
	 * Slide edit and preview mode switch btn
	 */
	MSPanel.PreviewSlideBtn = Ember.View.extend({
		classNames : ['msp-preview-slide'],
		template : Ember.Handlebars.compile('{{#if controller.isPlaying}}<a href="#" {{action "pause"}} class="msp-preview-btn msp-pause-btn"><span class="msp-ico msp-ico-whitepause"></span></a>'+
											'{{else}}<a href="#" {{action "enterPreviewMode"}} class="msp-preview-btn msp-play-btn"><span class="msp-ico msp-ico-whiteplay"></span></a>{{/if}}'+
	             						    '{{#if controller.isPreviewMode}}<a href="#" {{action "exitPreviewMode"}} class="msp-preview-btn-text msp-exit-preview">'+__MSP_LAN.tl_004+'</a>'+
	             						    '{{else}}<a href="#" {{action "enterPreviewMode"}} class="msp-preview-btn-text">'+__MSP_LAN.tl_005+'</a>{{/if}}')
	});


	//--------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

	/**
	 * Timeline Layers View, list of all layers in left side of timeline.
	 * @extends {Ember.View}
	 */
	MSPanel.TimelineLayersList = Ember.View.extend({
		classNames : ['msp-tl-layers-list'],

		template : Ember.Handlebars.compile('<ul>{{#each layer in controller}}'+
												'<li class="msp-slidelayer-item" {{bind-attr data-id=layer.id}}> {{view MSPanel.LayerRow layer=layer}} </li>'+	  
											'{{/each}}</ul><div class="msp-layer-botspace"></div>'),

		didInsertElement : function(){
			var self = this,
				$ele = this.$();

			/**
			 * Scrolls layers list while dragging one of them like auto scroll in jQUery UI Sortable.
			 * @param  {object} event jQuery event object.
			 */
			var scrolling = function(event){
				var overflowOffset = self.$().offset(),
					scrollSensitivity = 1,
					scrollSpeed = 5,
					scrollParent = $ele,
					targetScroll = scrollParent[0].scrollTop;

				if((overflowOffset.top + scrollParent[0].offsetHeight) - event.pageY < scrollSensitivity) 
					targetScroll = scrollParent[0].scrollTop + scrollSpeed;
				else if(event.pageY - overflowOffset.top < scrollSensitivity) 
					targetScroll = scrollParent[0].scrollTop - scrollSpeed;
				
				self.set('timeline.scrlY' , targetScroll);
			};

			// jQuery UI Sortable used for list of layers in timline.
			var soratableList = this.$('>ul').sortable({
					placeholder: "msp-layers-srtplaceholder",
					helper: 'clone'	,
					axis: 'y',
					zIndex: 1002,
					opacity: 0.6,
					delay: 100,
					
					start: function (e, ui) {
					 	ui.item.show();
					 	ui.helper.bind('mousemove' , scrolling);
					},

					end: function(e , ui){
						ui.helper.unbind('mousemove' , scrolling);
					},

					update : function(event , ui){
						var indexes = {},
							layers = $(this).find('.msp-slidelayer-item'),
							len = layers.length,
							$this = $(this);

						// stores new order of layers in "indexes" object
						layers.each(function(index) {
				          indexes[$(this).data('id')] = len - index;
				        });

						// We cancel the sortable, because Ember array controller will re-render template based on new order.
						$this.sortable('cancel');

						// call the controller for update order of layers
						self.get('controller').updateLayersSort(indexes);
					}
			});

			// wheel scroll event for layers list
			// TODO: It has some problems while scrolling
			var wheelScroll = function(event){
				var st = $ele[0].scrollTop,
					delta = event.deltaY * 30;

				if((st === 0 && delta > 0) || ( delta < 0 && st >= $ele[0].scrollHeight - $ele[0].clientHeight - 10)) 
					return;

				self.set('timeline.scrlY' , st - delta);
				event.preventDefault();
			};

			$ele.mouseenter(function(event) {
				$ele.bind('mousewheel', wheelScroll);
			}).mouseleave(function(event){
				$ele.unbind('mousewheel', wheelScroll);
			});

			this.set('soratableList' , soratableList);
		},

		/**
		 * Enable sortable in Editmode and disable sortable in Previewmode.
		 */
		onPlayingMode: function(){
			if(this.get('controller.isPreviewMode'))
				this.get('soratableList').sortable('disable');
			else
				this.get('soratableList').sortable('enable');
		}.observes('controller.isPreviewMode'),

		/**
		 * Updates scroll position of layers list while scrolling frames. 
		 */
		onScroll : function(){
			var scrl = this.get('timeline.scrlY');
			this.$()[0].scrollTop = parseInt(scrl);
		}.observes('timeline.scrlY'),

		willDestroyElement : function(){
			// remove sortable object from View.
			this.set('soratableList' , null);
		}

	});

	// ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------

	MSPanel.LayerRow = Ember.View.extend({
		classNames : ['msp-layer-row'],
		classNameBindings : ['selected:active'],
		selected : false,
		isEditing : false,
		template : Ember.Handlebars.compile('<ul>'+
	                    				     '<li><a href="#" title="'+__MSP_LAN.tl_006+'" {{action "toggleHide" target=view bubbles=false}}><span {{bind-attr class=view.hideClass}}></span></a></li>'+//class="msp-ico msp-ico-graypoint"
	                    				     '<li><a href="#" title="'+__MSP_LAN.tl_007+'" {{action "toggleSolo" target=view bubbles=false}}><span {{bind-attr class=view.soloClass}}></span></a></li>'+
	                    				     '<li><a href="#" title="'+__MSP_LAN.tl_008+'" {{action "toggleLock" target=view bubbles=false}}><span {{bind-attr class=view.lockClass}}></span></a></li>'+
	                    				   '</ul>'+

	                    				   '<div class="msp-layer-label"><img {{bind-attr src=view.layerImg}}>'+
	                    				  // '{{layer.order}}'+
	                    				   '{{#if view.isEditing}}'+
	                    				   		'{{view MSPanel.RenameLayer class="msp-lt-layer-rename" value=layer.name isEditingBinding="view.isEditing"}}'+
	                    				   '{{else}}'+
	                    				   		'<span {{action "renameLayer" on="doubleClick" target=view}} class="msp-layer-labeltext">{{layer.name}}</span>'+
	                    				   '{{/if}}'+
	                    				   '</div>'+
	                    				   '<div class="msp-layer-controls">'+
	                    				   	'<a href="#" title="Duplicate" {{action "duplicateLayer" target=view bubbles=false}}><span class="msp-ico msp-ico-grayduplicate"></span></a>'+
	                    				   	'<a href="#" title="Remove" {{action "removeLayer" target=view bubbles=false}}><span class="msp-ico msp-ico-grayremove"></span></a>'+
	                    				   '</div>'),

		mouseDown : function(){
			if(this.get('controller.isPreviewMode')) return;
			this.get('controller').send('selectLayer' , this.get('layer'));
		},

		onInit : function(){
			this.set('layerImg' , __MSP_PATH + 'images/layertypes/' + this.get('layer.type') + '.png');
			this.updateLayerstate();
		}.on('init'),

		onSelect : function(){
			if(this.get('controller.currentLayer') === null){
				if(this.get('selected')) this.set('selected' , false);
				return;
			}
			var layer = this.get('layer');
			this.set('selected' , layer.get('id') === this.get('controller.currentLayer.id'));
		}.observes('controller.currentLayer').on('init'),

		updateLayerstate : function(){
			this.set('hideClass' , 'msp-ico ' + (this.get('layer.isHided') ? 'msp-ico-blackhide' : 'msp-ico-graypoint'));
			this.set('soloClass' , 'msp-ico ' + (this.get('layer.isSoloed') ? 'msp-ico-blackiso'  : 'msp-ico-graypoint'));
			this.set('lockClass' , 'msp-ico ' + (this.get('layer.isLocked') ? 'msp-ico-blacklock' : 'msp-ico-graypoint'));
		}.observes('layer.isHided' , 'layer.isLocked' , 'layer.isSoloed'),

		actions : {
			duplicateLayer : function(){
				if(this.get('controller.isPreviewMode')) return;
				this.get('controller').duplicateLayer(this.get('layer'));
			},

			removeLayer : function(){
				if(this.get('controller.isPreviewMode')) return;
				if(confirm(__MSP_LAN.tl_009))
					this.get('controller').removeLayer(this.get('layer'));
			},

			renameLayer : function(){
				this.set('isEditing' , true);
			},

			toggleLock : function(){
				this.set('layer.isLocked' , !this.get('layer.isLocked'));
			},

			toggleSolo : function(){
				this.set('layer.isSoloed' , !this.get('layer.isSoloed'));
			},

			toggleHide : function(){
				this.set('layer.isHided' , !this.get('layer.isHided'));
			}
		}
	});


	MSPanel.RenameLayer = Ember.TextField.extend({
		didInsertElement : function(){
			this.$().focus();
		},

		focusOut: function(evt) {
		    this.set('isEditing', false);
		    if(Ember.isEmpty(this.get('value')))
		    	this.set('value' , 'layer');
		},

		insertNewline : function(evt){
			this.set('isEditing', false);
			if(Ember.isEmpty(this.get('value')))
		    	this.set('value' , 'layer');
		}

	});

/* ---------------------------------------------------------
						TimelineRuler
------------------------------------------------------------*/

	MSPanel.TimelineRuler = Ember.View.extend({
		classNames : ['msp-tl-ruler'],
		attributeBindings: ['style'],

		template : Ember.Handlebars.compile('<div id="ruler-frame-indicator" class="msp-tl-ruler-frameindicator"></div>'+
											'<div id="ruler-delay-indicator" class="msp-tl-delayindicator"></div>'+
											'{{#each time in view.timeList}}<span class="mps-tl-lable">{{time}}</span>{{/each}}'),

		setup : function(){
			var time = TIMELINE_WIDTH + 1,
				timeList = [], width;

			for(var i = 0; i !== time ; i++)
				timeList.push(i + 's');

			width = time*ONE_SEC;
			this.set('timeline.rulerWidth' , width);

			this.set('style' , 'width:' + width + 'px');
			this.set('timeList' , timeList);

		}/*.observes('controller.slide.duration')*/.on('init'),

		didInsertElement : function(){
			
			var that = this;
			var fi = this.$('#ruler-frame-indicator').slider({
				slide: function( event, ui ) {
					var timeline = that.get('controller.tweenTimeline');
				  	that.get('controller').send('pause');
					timeline.position(Math.min(0.998, ui.value / (300 * ONE_SEC)) * timeline.duration);
				 },
				 max: 300 * ONE_SEC
			});
			if(!this.get('controller.isPreviewMode')) fi.css('display' , 'none');
		},

		onPlaying : function(){
			var isPreviewMode = this.get('controller.isPreviewMode'),
				fi = this.$('#ruler-frame-indicator');

			if(isPreviewMode){
				fi.css('width' , this.get('controller.tweenTimeline').duration * ONE_SEC).css('display' , '');
			}else{
				fi.css('display' , 'none').slider('option' , 'value' , 0);
			}

		}.observes('controller.isPreviewMode'),

		updateFrameIndicator : function(){
			this.$('#ruler-frame-indicator').slider('option' , 'value' , this.get('controller.timelinePos') *  (300 * ONE_SEC) / this.get('controller.tweenTimeline').duration);
			this.onScroll();
		}.observes('controller.timelinePos'),

		onScroll : function(){
			var scrl = this.get('timeline.scrlX');
			this.$().parent()[0].scrollLeft = parseInt(scrl);
		}.observes('timeline.scrlX'),

		updateDelayIndicator : function(){
			this.$('#ruler-delay-indicator')[0].style.left = this.get('controller.slide.duration') * ONE_SEC + 20 + 'px'; // 20px margin left
		}.observes('controller.slide.duration').on('didInsertElement')

	});


/* ---------------------------------------------------------
				Timeline Frames contariner
------------------------------------------------------------*/

	MSPanel.TimelineFrames = Ember.View.extend({
		classNames : ['msp-tl-frames-cont'],

		template : Ember.Handlebars.compile('<ul>{{#each layer in controller}}'+
												'<li> {{view MSPanel.FramesRow layer=layer}} </li>'+	  
											'{{/each}}</ul><div id="delay-indicator" class="msp-tl-delayindicator"></div><div id="frame-indicator" class="msp-tl-frameindicator"></div>'),

		didInsertElement : function(){
			this.$().jScrollPane({hideFocus: true , forceReinit : true});
			$(window).resize(function(event) {
				api.reinitialise();
			});
			
			var api = this.$().data('jsp');
			var that = this;

			this.$().bind('jsp-scroll-y' , function(event, scrollPositionY, isAtTop, isAtBottom)	{
				that.set('timeline.scrlY' , scrollPositionY);
			}).bind('jsp-scroll-x' , function(event, scrollPositionX, isAtLeft, isAtRight) {
				that.set('timeline.scrlX' , scrollPositionX);
			})	
		},

		updatejsp : function(){
			Ember.run.scheduleOnce('afterRender', this, this.applyScroll);
		}.observes('controller','controller.@each' , 'timeline.rulerWidth' , 'controller.slide.timeline_h'),

		applyScroll : function(){
			var api = this.$().data('jsp');
			api.reinitialise();
		},

		scrollTo : function(){
			this.$().data('jsp').scrollToY(this.get('timeline.scrlY'));
		}.observes('timeline.scrlY'),

		updateDelayIndicator : function(){
			this.$('#delay-indicator')[0].style.left = this.get('controller.slide.duration') * ONE_SEC + 20 + 'px'; // 20px margin left
		}.observes('controller.slide.duration').on('didInsertElement'),

		updateFrameIndicator : function(){
			this.$('#frame-indicator')[0].style.left = this.get('controller.timelinePos') * ONE_SEC + 20 + 'px'; // 20px margin left
		}.observes('controller.timelinePos'),

		onPlaying : function(){
			if(this.get('controller.isPreviewMode')){
				 this.$('#frame-indicator').css('display' , '');
			}else{
				 this.$('#frame-indicator').css('display' , 'none');
			}
		}.observes('controller.isPreviewMode').on('didInsertElement')

	});

/* ---------------------------------------------------------
				Timeline Frames row
------------------------------------------------------------*/

	MSPanel.FramesRow = Ember.View.extend({
		classNames : ['msp-frames-row'],
		attributeBindings: ['style'],
		classNameBindings: ['selected:active'],
		selected 	: false,
		rangeSlider : null,

		didInsertElement : function() {
			var rangeSlider = new averta.rangeSlider();
			var parentView = this.get('parentView');
			var that = this;

			this.$().append(rangeSlider.$element.addClass('msp-timeline-range'));

			var updateTTposition = function(tt){
				if(tt.offset().left < parentView.$().offset().left + 10)
					tt.css('left' , parentView.$().offset().left + 10);
			}

			var showDelay = rangeSlider.addRange('startDelay' , this.get('layer.showDelay') * ONE_SEC , 'msp-range msp-range-delay' , function(range){
				that.set('layer.showDelay' , range.value / ONE_SEC);
				updateTTposition(range.tt);
				return __MSP_LAN.tl_010 + ' ' + Math.round(range.value/ONE_SEC * 100)/100 + 's';
			});

			var showDuration = rangeSlider.addRange('showDuration' , this.get('layer.showDuration') * ONE_SEC , 'msp-range msp-range-show' , function(range){
				that.set('layer.showDuration' , range.value / ONE_SEC);
				updateTTposition(range.tt);
				return __MSP_LAN.tl_011 + ' ' + Math.round(range.value/ONE_SEC * 100)/100 + 's';
			});

			var hideDelay = rangeSlider.addRange('hideDelay' , this.get('layer.hideDelay') * ONE_SEC , 'msp-range msp-range-wating' , function(range){
				that.set('layer.hideDelay' , range.value / ONE_SEC);
				updateTTposition(range.tt);
				return __MSP_LAN.tl_012 + ' ' + Math.round(range.value/ONE_SEC * 100)/100 + 's';
			});

			var hideDuration = rangeSlider.addRange('hideDuration' , this.get('layer.hideDuration') * ONE_SEC , 'msp-range msp-range-hide' , function(range){
				that.set('layer.hideDuration' , range.value / ONE_SEC);
				updateTTposition(range.tt);
				return __MSP_LAN.tl_013 + ' ' + Math.round(range.value/ONE_SEC * 100)/100 + 's';
			});

			this.set('hideDurationRange' , hideDuration);
			this.set('hideDelayRange' , hideDelay);
			this.set('showDurationRange' , showDuration);
			this.set('showDelayRange' , showDelay);

			this.set('rangeSlider' , rangeSlider);

			this.onHideStateChange();
			this.onLayerPositionTypeChange();
		},

		onLayerPositionTypeChange : function () {

			var pos = this.get('layer.position');
			if ( pos === 'static' ) {
				this.$('.msp-timeline-range').css('display', 'none');
				this.$().append('<p class="msp-static-notice">' + (__MSP_LAN.tl_014 || 'Static layer doesn\'t support transitions.') + '</p>');
			} else {
				this.$('.msp-timeline-range').css('display', '');
				this.$('.msp-static-notice').remove();
			}

		}.observes('layer.position'),

		onValuesChanged : function(){

			var rangeSlider = this.get('rangeSlider');

			rangeSlider.setValue(this.get('showDelayRange')    , this.get('layer.showDelay') * ONE_SEC);
			rangeSlider.setValue(this.get('showDurationRange') , this.get('layer.showDuration') * ONE_SEC);

			if(this.get('layer.useHide')){
				rangeSlider.setValue(this.get('hideDelayRange')    , this.get('layer.hideDelay') * ONE_SEC);
				rangeSlider.setValue(this.get('hideDurationRange') , this.get('layer.hideDuration') * ONE_SEC);
			}

		}.observes('layer.showDelay' , 'layer.showDuration' , 'layer.hideDelay' , 'layer.hideDuration'),

		onHideStateChange : function(){
			var useHide = this.get('layer.useHide'),
				hideDuration = this.get('hideDurationRange'),
				hideDelay = this.get('hideDelayRange'),
				rangeSlider = this.get('rangeSlider');

			if(useHide){
				rangeSlider.showRange(hideDelay);
				rangeSlider.showRange(hideDuration);
			}else{
				rangeSlider.hideRange(hideDelay);
				rangeSlider.hideRange(hideDuration);
			}

		}.observes('layer.useHide'),

		onPlayingMode:function(){
			var isPreviewMode = this.get('controller.isPreviewMode'),
				rangeSlider = this.get('rangeSlider');

			if(isPreviewMode)
				rangeSlider.disable();
			else
				rangeSlider.enable();

		}.observes('controller.isPreviewMode'),

		onSelect : function(){
			if(this.get('controller.currentLayer') === null){
				if(this.get('selected')) this.set('selected' , false);
				return;
			}
			var layer = this.get('layer');
			this.set('selected' , layer.get('id') === this.get('controller.currentLayer.id'));
		}.observes('controller.currentLayer').on('init'),

		willDestroyElement : function(){
			this.get('rangeSlider').remove();
		},

		update : function(){
			var width = this.get('parentView.timeline.rulerWidth');
			this.set('style' , 'width:' + width + 'px');
		}.observes('parentView.timeline.rulerWidth').on('init')

	});

})();

/* ================== src/js/mspanel/views/StageView.js =================== */
/* ---------------------------------------------------------
						Stage View
------------------------------------------------------------*/
MSPanel.StageArea = Ember.View.extend({
	classNames : ['msp-stage-area'],
	selectedLayer : null,
	zoom : 100,
	template : Ember.Handlebars.compile('<div class="msp-metabox-row"><div class="msp-stage-top-toolbar">'+
											'<label>'+__MSP_LAN.sv_001+' </label> {{view MSPanel.AlignBtns target=view}}'+
											'<span class="msp-form-space"></span>'+
											'<label>'+__MSP_LAN.sv_002+' </label> {{switch-box value=sliderSettings.snapping}}'+
											'<span class="msp-form-space"></span>'+
											'<label>'+ (__MSP_LAN.sv_010 || 'Layer position origin : ') + ' </label> {{view MSPanel.PositionOrigin layer=view.layer}}'+
											'<span class="msp-form-space"></span>'+
											'<label>'+__MSP_LAN.sv_003+' </label> {{number-input value=view.zoom}} %'+
										'</div></div>'+
										'<hr class="msp-metabox-hr" style="margin-top:10px;">'+
										'{{view MSPanel.Stage}}'+
										'{{#if noticeMsg}}<div class="msp-stage-msg"><span class="msp-ico msp-ico-notice"></span>{{{noticeMsg}}}</div>{{/if}}'),

	onSelectedLayerChanged: function(){
		this.set('layer', this.get('selectedLayer.layer'));
	}.observes('selectedLayer'),

	actions : {
		alignLayer : function(align){
			if(this.get('selectedLayer'))
				this.get('selectedLayer').align(align);
		}
	}
});


MSPanel.Stage = Ember.View.extend({
	classNames : ['msp-slide-stage'],
	attributeBindings : ['style'],
	selectedLayer : null,
	overSoloPlane: 0,
	template : Ember.Handlebars.compile('<div id="stage-bg" class="msp-stage-bg">'+
											//'<img {{bind-attr src=slide.bg}}>'+
										'</div>'+
										'<div id="overlaybox" {{bind-attr class=":msp-stage-pattern :ms-pattern controller.slide.pattern"}}></div>'+
										'<div id="snapbox" class="msp-stage-snapbox"></div>'+
										'<div id="solo-plane" class="msp-solo-plane"></div>'+
										'{{#each layer in controller }}{{view MSPanel.StageLayer layer=layer}}{{/each}}'),
	resize : function(){

		var w = this.get('controller.sliderSettings.width'),
			h = this.get('controller.sliderSettings.height');

		this.set('width' , w);
		this.set('height' , h);
		if ( $.browser.mozilla ) {
			this.$().css({
				width  : w,
				height : h,
				'-moz-transform': 'scaleX(' + this.get('parentView.zoom')/100 + ') scaleY(' + this.get('parentView.zoom')/100 + ')',
				'-moz-transform-origin': '0 0'
			}).parent().height(h*this.get('parentView.zoom')/100+75);
		} else {
			this.$().css({
				width  : w,
				height : h,
				zoom   : this.get('parentView.zoom') + '%'
			});
		}
	}.observes('controller.sliderSettings.width' , 'controller.sliderSettings.height' , 'parentView.zoom').on('didInsertElement'),

	didInsertElement : function(){
		var BG = this.$('#stage-bg'),
			BGImage = $('<img/>');

		BGImage.css('visibelity' , 'hidden').each($.jqLoadFix);

		var aligner = new MSAligner(this.get('controller.slide.fillMode') , BG , BGImage);

		this.set('bgAligner' , aligner);
		this.set('bgImg', BGImage);

		$(document).on('keydown' , {that:this} , this.moveLayer);
		this.set('soloPlane' , this.$('#solo-plane').css('display', 'none'));
		this.onBGChange();
	},

	onBGColorChange: function(){

		var color = this.get('controller.slide.bgColor');

		if( !Ember.isEmpty(color) ){
			this.$('#stage-bg').css('background-color', color);
		} else {
			this.$('#stage-bg').css('background-color', '');
		}

	}.observes('controller.slide.bgColor').on('didInsertElement'),

	onColorOverlayChange: function(){

		var color = this.get('controller.slide.colorOverlay');

		if ( !Ember.isEmpty(color) ) {
			this.$('#overlaybox').css('background-color', color);
		} else {
			this.$('#overlaybox').css('background-color','');
		}

	}.observes('controller.slide.colorOverlay').on('didInsertElement'),

	onBGChange: function(){
		var alinger = this.get('bgAligner');
		if(alinger){
			alinger.reset();
		}

		var bg = this.get('controller.slide.bg'),
			bgImg = this.get('bgImg');

		if( !Ember.isEmpty(bg) ){
			var that = this;
			bgImg.appendTo(this.$('#stage-bg'));
			bgImg.preloadImg(bg , function(event) {that._onBGLoad(event);});
			bgImg.attr('src', bg);
			//alinger.align();
		} else {
			bgImg.detach();
		}
	}.observes('controller.slide.bg'),

	_onBGLoad: function(event){
		var aligner = this.get('bgAligner');

		if( !aligner ) {
			return;
		}

		aligner.init(event.width , event.height);
		aligner.align();
		this.get('bgImg').css('visibelity' , '');
	},

	mouseDown: function(event){
		this.get('controller').send('clearSelect');
		this.set('selectedLayer', null);
	},
	onFillModeChanged : function(){
		var aligner = this.get('bgAligner');
		aligner.changeType(this.get('controller.slide.fillMode'));
	}.observes('controller.slide.fillMode'),

	moveLayer : function(event){

		var focused_ele = $(document.activeElement)
		if(focused_ele.length !== 0 && (focused_ele.is('input') || focused_ele.is('textarea'))) return;

		var that = event.data.that,
			selectedLayer = that.get('selectedLayer');
			keyCode = event.which;
		if(selectedLayer && keyCode >= 37 &&  keyCode <= 40){
			selectedLayer.move(keyCode , event.shiftKey);
			event.preventDefault();
		}
	},

	onSelectedLayerChanged : function(){
		this.set('parentView.selectedLayer' , this.get('selectedLayer'));
	}.observes('selectedLayer'),

	showSoloPlane: function(){
		var that = this;
		setTimeout(function(){that.get('soloPlane').css('display', '')},1);
		this.incrementProperty('overSoloPlane');
	},

	hideSoloPlane: function(){

		var overSoloPlane = this.get('overSoloPlane');
		overSoloPlane = Math.max(0 , overSoloPlane - 1);
		this.set('overSoloPlane' , overSoloPlane);

		if( overSoloPlane === 0 ) {
			this.get('soloPlane').css('display', 'none');
		}
	},

	previewMode: function(){
		var isPreviewMode = this.get('controller.isPreviewMode');
		if(isPreviewMode && this.get('overSoloPlane') > 0){
			this.get('soloPlane').css('display', 'none');
		} else if( !isPreviewMode && this.get('overSoloPlane') > 0 ){
			this.get('soloPlane').css('display', '');
		}
	}.observes('controller.isPreviewMode'),
	willDestroyElement: function(){
   		$(document).off('keydown', this.moveLayer);
   		this.set('bgAligner' , null);
 	}
});

/* ---------------------------------------------------------
						Stage Layers
------------------------------------------------------------*/

MSPanel.TextLayerTemplate  = '{{#if view.layer.masked }}'+
                             '<div class="msp-layer-mask" {{bind-attr style=view.maskSizes}}>'+
                                '<div class="msp-layer" {{bind-attr style=view.layerStyle}}>{{{view.layer.content}}}</div>'+
                             '</div>{{else}}'+
                                '<div class="msp-layer" {{bind-attr style=view.layerStyle}}>{{{view.layer.content}}}</div>'+
                             '{{/if}}';

MSPanel.ButtonLayerTemplate  = '<div {{bind-attr style=view.layerStyle class=":ms-btn layer.btnClass"}}>{{{view.layer.content}}}</div>';

// dynamic tags ?
if( MSPanel.dynamicTags ){
    MSPanel.TextLayerTemplate = '<div class="msp-layer" {{bind-attr style=view.layerStyle}}>'+
                                    '{{#if view.layer.dynamicContent}}'+
                                        '{{{view.layer.dynamicContent}}}'+
                                    '{{else}}'+
                                        '{{{view.layer.content}}}'+
                                    '{{/if}}'+
                                '</div>';
}

MSPanel.ImageLayerTemplate = '{{#if view.layer.masked }}<div  class="msp-layer-mask" {{bind-attr style=view.maskSizes}}>'+
                                 '{{#if view.hasImg}}<img class="msp-layer" {{bind-attr src=view.layer.img style=view.layerStyle}}>{{else}}<img src="'+ __MSP_PATH +'images/image-layer.png">{{/if}}'+
                             '</div>{{else}}'+
                                 '{{#if view.hasImg}}<img class="msp-layer" {{bind-attr src=view.layer.img style=view.layerStyle}}>{{else}}<img src="'+ __MSP_PATH +'images/image-layer.png">{{/if}}'+
                             '{{/if}}';


MSPanel.VideoLayerTemplate = '<div {{bind-attr style=view.layerStyle}} class="msp-stage-videolayer msp-layer">'+
								'{{#if view.layer.hasCover}}<img {{bind-attr src=view.layer.coverImg}}>{{/if}}'+
								'<img class="msp-stage-videoicon" src="'+ __MSP_PATH +'images/video-layer.png">'+
							'</div>';

MSPanel.HotspotLayerTemplate = '<div class="msp-stage-hotspot msp-layer"></div>';

MSPanel.StageLayer = Ember.View.extend({
	classNames: ['msp-stage-layer'],
	classNameBindings: ['selected:active'],
	attributeBindings: ['style'],

	// this flag used by image layer. if it has image init it will be true otherwise default value should be loaded
	hasImg: false,

	// last applied style to layer.
	lastStyle: {},

	onInit: function(){

		switch(this.get('layer.type')){
			case 'text':
				this.set('template' , Ember.Handlebars.compile(MSPanel.TextLayerTemplate));
			break;
			case 'image':
				this.set('template' , Ember.Handlebars.compile(MSPanel.ImageLayerTemplate));
			break;
			case 'video':
				this.set('template' , Ember.Handlebars.compile(MSPanel.VideoLayerTemplate));
				if( Ember.isEmpty(this.get('layer.width')) ) {
					this.set('layer.width', 300);
				}

				if( Ember.isEmpty(this.get('layer.height')) ) {
					this.set('layer.height', 150);
				}
			break;
			case 'hotspot':
				this.set('template' , Ember.Handlebars.compile(MSPanel.HotspotLayerTemplate));
			break;
			case 'button':
				this.set('template' , Ember.Handlebars.compile(MSPanel.ButtonLayerTemplate));
		}

		this.set('layer.stageLayer' , this);

	}.on('init'),

	checkImageLayerPath: function(){
		var hasImg = !Ember.isEmpty(this.get('layer.img'));
		this.set('hasImg', hasImg);
		Ember.run.scheduleOnce('afterRender', this, this.afterRenderEvent);
		/*var that = this;
		setTimeout(function(){
			that.afterRenderEvent();
		});*/
	}.observes('layer.img').on('didInsertElement'),

	didInsertElement: function(){
		var that = this;

		this.$().draggable({
					snap: this.get('controller.sliderSettings.snapping') ? '.msp-stage-layer, .msp-stage-snapbox' : false ,
					snapTolerance: 8,
					delay:200,
					drag: function( event, ui ) {
						/*var multi = 1 - that.get('parentView.parentView.zoom') / 100;

						ui.position.top += (ui.position.top - ui.originalPosition.top) * multi;
       					ui.position.left += (ui.position.left - ui.originalPosition.left) * multi;*/

						that.updateLayerPostion(ui.position.left , ui.position.top);
					}
				 });

		// disable links in text layer over stage
		if ( this.get('layer.type') === 'text' ) {
			this.$().click(function(e){
				e.preventDefault();
				e.stopPropagation();
			});
		}

		this.onPlayingMode();
		this.onSoloed();
		// apply style on init
		this.updateLayerStyle(this.get('layer.styleModel').toJSON());
		// apply custom css
		var custom = this.get('layer.styleModel.custom');
		if( !Ember.isEmpty(custom) ){
			this.$().attr('style', this.$().attr('style') + ';' + custom);
		}

		/*if( this.get('layer.type') !== 'image' ){
			that = this;
			// reposition layers after 150 ms delay it lets the browser to render layer before repositioning
			setTimeout(function(){
				that.repositionLayer();
			},150);
		}*/

	},

	// onContentChange: function(){
	// 	// disable links in stage view
	// 	if ( this.get('layer.type') === 'text' ) {
	// 		console.log(this.$('a').css('pointer-events', 'none'))
	// 		;
	// 	}
	// }.observes('layer.content').on('didInsertElement'),

	afterRenderEvent: function(){
		if( this.get('layer.type') === 'image' && !Ember.isEmpty(this.get('layer.img')) ){
			var that = this;
			this.$('img').one('load', function(){
				that.repositionLayer();
			}).each($.jqLoadFix);
		} else {
			this.repositionLayer();
		}
	},

	updateZIndex: function(){
		if(this.get('layer.isSoloed')) {
			this.$().css('z-index' ,  this.get('layer.order') + 501);
		} else {
			this.$().css('z-index' ,  this.get('layer.order'));
		}
	}.observes('layer.order').on('didInsertElement'),

	showAndHide: function(){

 		if(this.get('layer.isHided')){
			this.$().css('display' , 'none');
 		}else{
 			this.$().css('display' , '');
 		}

 	}.observes('layer.isHided').on('didInsertElement'),

	onPlayingMode: function(){
		if(this.get('controller.isPreviewMode') || this.get('layer.isLocked'))
			this.$().draggable('disable');
		else
			this.$().draggable('enable');
	}.observes('controller.isPreviewMode' , 'layer.isLocked'),

	onSoloed: function(){
		if(this.get('layer.isSoloed')){
			this.get('parentView').showSoloPlane();
		} else {
			this.get('parentView').hideSoloPlane();
		}

		this.updateZIndex();
	}.observes('layer.isSoloed').on('didInsertElement'),

	/**
	 * Updates button layer class name
	 * @since 1.9.0
	 */
	/*updateButtonClassName: function(){
		var className = this.get('layer.btnClass');

		this.$('>div').removeClass(this.get('lastbtnClassName'))
					  .addClass(className);
		this.set('lastbtnClassName', className);

	}.observes('layer.btnClass').on('didInsertElement'),*/

	updateLayerPostion: function(x , y){
		this.beginPropertyChanges();

		x = x || this.$().position().left;
		y = y || this.$().position().top;

		var origin = this.get('layer.origin'),
			w = this.get('hasImg') ? Math.max(this.$().outerWidth(), this.$('img').width()) : this.$().outerWidth(),
			h = this.get('hasImg') ? Math.max(this.$().outerHeight(), this.$('img').height()) : this.$().outerHeight();

		switch ( origin.charAt(0) ) {
			case 't':
				this.set('layer.offsetY', y);
				break;
			case 'm':
				this.set('layer.offsetY', y - (this.get('parentView.height') - h) / 2);
				break;
			case 'b':
				this.set('layer.offsetY', this.get('parentView.height') - h - y );
				break;
		}

		switch ( origin.charAt(1) ) {
			case 'l':
				this.set('layer.offsetX', x);
				break;
			case 'c':
				this.set('layer.offsetX', x - (this.get('parentView.width') - w) / 2);
				break;
			case 'r':
				this.set('layer.offsetX', this.get('parentView.width') - w - x);
				break;
		}

		this.set('intranlChange' , true);

		this.endPropertyChanges();
	},

	repositionLayer: function(){
		if(this.get('intranlChange')) {
			this.set('intranlChange' , false);
			return;
		}

		var origin = this.get('layer.origin'),
			x = this.get('layer.offsetX'),
			y = this.get('layer.offsetY'),
			w = this.get('hasImg') ? Math.max(this.$().outerWidth(), this.$('img').width()) : this.$().outerWidth(),
			h = this.get('hasImg') ? Math.max(this.$().outerHeight(), this.$('img').height()) : this.$().outerHeight();

		switch ( origin.charAt(0) ) {
			case 't':
				this.$().css('top', y);
				break;
			case 'm':
				this.$().css('top', y + (this.get('parentView.height') - h) / 2);
				break;
			case 'b':
				this.$().css('top', (this.get('parentView.height') - h) - y );
				break;
		}

		switch ( origin.charAt(1) ) {
			case 'l':
				this.$().css('left', x);
				break;
			case 'c':
				this.$().css('left', x + (this.get('parentView.width') - w) / 2);
				break;
			case 'r':
				this.$().css('left', (this.get('parentView.width') - w) - x);
				break;
		}
	}.observes('layer.offsetX' , 'layer.offsetY'),

	onOriginChange: function(){
		this.updateLayerPostion();
	}.observes('layer.origin'),

	onResize: function(){
		var style = '';
		this.get('layer.width')  != null && (style += 'width:'  + this.get('layer.width')  + 'px;');
		this.get('layer.height') != null && (style += 'height:' + this.get('layer.height') + 'px;');
		this.set('layerStyle' , style);
	}.observes('layer.width' , 'layer.height').on('didInsertElement'),

    onMaskResize: function(){
        var style = '';
        if ( this.get('layer.masked') && this.get('layer.maskCustomSize') ) {
            this.get('layer.maskWidth')  != null && (style += 'width:'  + this.get('layer.maskWidth')  + 'px;');
            this.get('layer.maskHeight') != null && (style += 'height:' + this.get('layer.maskHeight') + 'px;');
        }
        this.set('maskSizes' , style);
    }.observes('layer.maskWidth' , 'layer.maskHeight', 'layer.masked', 'layer.maskCustomSize').on('didInsertElement'),

	snappingStateChange: function(){
		this.$().draggable( "option" , "snap", this.get('controller.sliderSettings.snapping') ? '.msp-stage-layer, .msp-stage-snapbox' : false );
	}.observes('controller.sliderSettings.snapping'),

	onSelectedLayerChanged: function(){

		if(this.get('controller.currentLayer') === null || this.get('layer.isLocked')){
			if(this.get('selected')) this.set('selected' , false);
			this.set('parentView.selectedLayer' , null);
			return;
		}

		var isSelected =  this.get('layer.id') === this.get('controller.currentLayer.id');
		this.set('selected' , isSelected);
		if(isSelected)
			this.set('parentView.selectedLayer' , this);
	}.observes('controller.currentLayer' , 'layer.isLocked'),

	mouseDown: function(event){

		if(this.get('controller.isPreviewMode') || this.get('layer.isLocked')) return;
		this.get('controller').send('selectLayer' , this.get('layer'));

		// prevent event bubbling
		return false;
	},

	move: function(keyCode , shift){
		if(this.get('layer.isHided') || this.get('layer.isLocked')) return;
		var moveUnit = shift ? 10 : 1;

		switch(keyCode){
			case 37: // left
				this.$().css('left' , '-=' + moveUnit);
			break;
			case 38: // up
				this.$().css('top' , '-=' + moveUnit);
			break;
			case 39: // right
				this.$().css('left' , '+=' + moveUnit);
			break;
			case 40: // down
				this.$().css('top' , '+=' + moveUnit);
			break;
		}

		this.updateLayerPostion();
	},

	align: function(align){
		if(this.get('layer.isHided') || this.get('layer.isLocked')) return;

 		switch(align) {
 			case 'top':
 				this.$().css('top' , 0);
 			break;
 			case 'mid':
 				this.$().css('top' , (this.get('parentView.height') - this.$().outerHeight()) / 2);
 			break;
 			case 'bot':
 				this.$().css('top' , this.get('parentView.height') - this.$().outerHeight());
 			break;
 			case 'left':
 				this.$().css('left' , 0);
 			break;
 			case 'center':
 				this.$().css('left' , (this.get('parentView.width') - this.$().outerWidth()) / 2 );
 			break;
 			case 'right':
 				this.$().css('left' , this.get('parentView.width') - this.$().outerWidth());
 			break;
 		}

 		this.updateLayerPostion();
 	},

 	reset: function(tweeenTimeline){

 		tweeenTimeline.removeTween(this.get('showTween'));
 		this.set('showTween', null);

 		if( this.get('layer.useHide') ){
 			tweeenTimeline.removeTween(this.get('hideTween'));
 			this.set('hideTween', null);
 		}

 		// reset transform
 		this.$().css(window._jcsspfx + 'Transform' , '')
 				.css('opacity' , 1);

        if ( this.get('layer.masked') ) {
            this.$().find('.msp-layer').css(window._jcsspfx + 'Transform' , '')
                                       .css('opacity' , 1);
        }
 	},

 	registerTween: function(timeline){

 		// disable transition tween for static layer
 		if ( this.get('layer.position') === 'static' ) {
 			return;
 		}

 		var show_eff = this.get('layer.showTransform'),
 			show_delay = Number(this.get('layer.showDelay')),
 			show_duration = Number(this.get('layer.showDuration')),
 			that = this,
	 		show_origin = this.get('layer.showOrigin'),
	 		$this = this.$();

        var $tweenTarget = this.get('layer.masked') ? $this.find('.msp-layer') : $this;
        // set from style to layer
        $tweenTarget.css(window._jcsspfx + 'Transform' , show_eff);
        if(this.get('layer.showFade')) $tweenTarget.css('opacity' , 0);

 		// add show tween
 		var to_transform = JTween.resetTransform(show_eff);
 		var showTween = new JTween($tweenTarget[0] , show_duration, {transform: to_transform , opacity: 1} , {ease: window.convertEaseName(this.get('layer.showEase')) });

 		timeline.addTween(show_delay , showTween);
 		timeline.addCallback(show_delay, this.updateOrigin, [$tweenTarget[0], show_origin], this.updateOrigin, [$tweenTarget[0], show_origin]);

 		this.set('showTween' , showTween);

 		// hide tween
 		if(!this.get('layer.useHide')) return;

 		var hide_delay =  Number(this.get('layer.hideDelay')),
 			hide_duration =  Number(this.get('layer.hideDuration')),
 			hide_origin = this.get('layer.hideOrigin');

 		var hideTween = new JTween($tweenTarget[0], hide_duration, {transform:this.get('layer.hideTransform'), opacity:(this.get('layer.hideFade') ? 0 : 1)} , {ease: window.convertEaseName(this.get('layer.hideEase')) });

 		timeline.addTween(show_delay + show_duration + hide_delay, hideTween);
		timeline.addCallback(show_delay + show_duration + hide_delay, this.updateOrigin, [$tweenTarget[0], hide_origin], this.updateOrigin, [$tweenTarget[0], show_origin]);

 		this.set('hideTween' ,hideTween);
 	},

 	/**
	 * updates transform origin of layer while animating
	 */
	updateOrigin: function(layer, origin){
		layer.style[window._jcsspfx + 'TransformOrigin'] = origin;
	},

 	/**
 	 * Updates style of layer in stage
 	 */
 	updateLayerStyle: function(style){
 		// remove null values
 		for(var key in style){
 			if( style[key] == null ) {
 				delete style[key];
 			}
 		}

		// remove last applied style from layer
		if( !Ember.isEmpty(this.get('lastStyle')) ){
			var oldStyle = this.get('lastStyle');
			for(key in oldStyle){
				this.$().css(key , '');
			}
		}

		// create a clone from new style as last style
		this.set('lastStyle', jQuery.extend({}, style));


    	// apply new style
        if ( this.get('layer.type') === 'button' ) {
            // apply style to the button element instead of layer element
            this.$('.ms-btn').css(style);
        } else {
        	this.$().css(style);
        }
	},

	willDestroyElement: function(){
		if(this.get('layer.isSoloed')){
			this.get('parentView').hideSoloPlane();
		}
	}

});
;

/* ================== src/js/mspanel/views/StyleEditorView.js =================== */
/**
 * Master Slider Panel - Style Editor 
 *
 * This slider has many preset styles which helps user to style layers faster, also user can create
 * new preset style from this editor. After adding a style to layer if the style was a preset,
 * class name (className) of style adds as "styleClass" in selected layer model otherwise, editor takes 
 * "lstyleClass" from layer which is a unique value and specifies it as "styleClass".
 * 
 * "lstyleClass" value generates and stores in layer model at layer creation.
 * 
 * @version 1.0b
 * @author Averta
 * @package MSPanel
 * @extends {Ember.View}
 * @requires MSPanel popup
 */

MSPanel.StyleEditor = Ember.View.extend({
	classNames: ['mps-style-editor'],
	layer: null,
	template: Ember.Handlebars.compile('<div class="left-box">'+
												'{{view MSPanel.StylePreview layer=view.layer}}'+
												'<div class="msp-section-content">'+
										   			'{{partial "style-properties"}}'+ // partial is defined in app.html (temporary)
										   		'</div>'+
												'<div class="msp-applystyle-cont">'+
													'<button {{action "applyStyle"}} class="msp-blue-btn msp-applystyle">'+__MSP_LAN.se_001+'</button>'+
													'<button {{action "saveAsPreset"}} class="msp-gray-btn msp-savepreset">'+__MSP_LAN.se_002+'</button>'+
												'</div>'+
											'</div>'+
											'{{view MSPanel.StyleList}}'),

	didInsertElement: function() {
		var that = this;

		this.$().dialog({ 
			resizable: false,
			modal: true,
			width: 1000,
			height: 550,
			title: __MSP_LAN.se_006 || 'Style Editor',
			draggable: false,
			show: 'fade',
			dialogClass: 'msp-dialog',
			appendTo: this.get('parentView').$(),
			dialogClass: 'msp-container msp-dialog',
			close: function( event, ui ) {
				//$(this).dialog('destroy')//.remove();
				that.get('controller.parent').closeStyleEditor();
				that.get('controller').send('cancel');
			}
		});
		
		$(window).bind('resize', {ref:this} , this.onWindowResize);
	},

	onWindowResize: function(event){
		var that = event.data.ref;
		that.$().dialog("option", "position", "center");
	},

	willDestroyElement: function(){
		$(window).unbind('resize', this.onWindowResize);
		this.$().dialog('destroy');
	}

});

/**
 * Style Pereview child View
 * @package MSPanel
 * @extends {Ember.View} 
 * @parent MSPanel.StyleEditor
 */

var STAGE_WIDTH = 780,
	STAGE_HEIGHT = 200;

MSPanel.StylePreview = Ember.View.extend({
	classNames: ['msp-section', 'msp-style-preview'],

	template: Ember.Handlebars.compile('<div class="msp-style-preview-cont">'+
									   		'<div class="msp-style-sample" id="sample">Sample Text</div>'+
									   		'<div id="bgToggle" class="bgToggle"></div>'+
									   	'</div>'),

	didInsertElement: function() {
		var sample = this.$('#sample');
		this.set('sample',sample);
		this.update();
		var that = this;
		this.$('#bgToggle').click(function(event) {
			var $this = $(this);
			if( $this.data('isBlack') ){
				that.$().css('background-color', 'white');
				$this.css('background-color', '#222');
				$this.data('isBlack', false);
			}else{
				$this.data('isBlack', true);
				that.$().css('background-color', '#222');
				$this.css('background-color', 'white');
			}
		}).css('background-color', '#222');

	},

	alignCenter: function(){
		var sample = this.get('sample');
		sample.css('left', (STAGE_WIDTH - sample.outerWidth()) / 2)
			  .css('top' , (STAGE_HEIGHT - sample.outerHeight()) / 2);
	},

	update: function(){
		this.get('sample').attr('style', this.get('controller.draftStyleText'));
		this.alignCenter();
	}.observes('controller.draftStyleText')

});

/**
 * Style List child view
 * @package MSPanel
 * @extends {Ember.View}
 * @parent MSPanel.StyleEditor
 */
MSPanel.StyleList = Ember.View.extend({
	classNames: ['msp-section msp-style-list'],
	//presetStyles: null,
	template: Ember.Handlebars.compile('<div class="msp-section-handle"><span class="msp-section-title">'+__MSP_LAN.se_003+'</span></div>'+
										'<div class="msp-section-content">'+
											'<div class="msp-style-list-cont">'+
									   			'{{#each style in presetStyles}}'+
									   				'{{view MSPanel.StyleRow style=style}}'+
									   			'{{/each}}'+ 
									   		'</div>'+
									   		'</div>')/*,

	updatePresetStyles: function(){
		var presetStyles = [];
		this.get('controller').forEach(function(style){
			if( style.get('type') === 'preset' ) {
				presetStyles.push(style);
			}
		});

		this.set('presetStyles', presetStyles);

	}.observes('controller.@each').on('init')*/

});

/**
 * Style Row child view
 * @package MSPanel
 * @extends {Ember.View}
 * @parent MSPanel.StyleList
 */
MSPanel.StyleRow = Ember.View.extend({
	classNames: ['msp-style-row'],
	classNameBindings : ['selected:active'],
	style: null,
	selected: false,

	template: Ember.Handlebars.compile('<span class="msp-style-name">{{style.name}}</span>'+
					   					'<a {{action "removeStyle" style target=view bubbles=false}} class="msp-style-remove">'+
					   						'<span class="msp-ico msp-ico-grayremove"></span>'+
					   					'</a>'),

	/**
	 * Select style row on click
	 * It will change te selected style for selected layer in LayersController
	 */
	click: function(event){
		this.set('controller.currentStyle', this.get('style'));
		this.get('controller').updateDraft(this.get('style'));
	},

	/**
	 * On user select another style in style list
	 */
	onSelectChanged: function(){
		if( this.get('style') === this.get('controller.currentStyle') ) {
			this.set('selected', true);
		} else {
			this.set('selected', false);
		}
	}.observes('controller.currentStyle').on('init'),

	actions: {

		/**
		 * Remove Preset Style
		 * @param {object} styleObject 
		 */
		removeStyle: function(style){
			if( confirm(__MSP_LAN.se_004.jfmt(style.get('name'))) )
				this.get('controller').send('removeStyle' , style);

			//this.get('parentView').updatePresetStyles();
		}
	}

});

/* ================== src/js/mspanel/views/EffectEditorView.js =================== */
MSPanel.EffectEditor = Ember.View.extend({
	classNames: ['mps-effect-editor'],
	layer: null,
	template: Ember.Handlebars.compile('<div class="left-box">'+
												'{{view MSPanel.EffectPreview layer=view.layer}}'+
												'<div class="msp-section-content">'+
										   			'{{partial "effect-properties"}}'+ // partial is defined in app.html (temporary)
										   		'</div>'+
												'<div class="msp-applyeffect-cont">'+
													'<button {{action "applyEffect"}} class="msp-blue-btn msp-applyeffect">'+__MSP_LAN.ee_002+'</button>'+
													'<button {{action "saveAsPreset"}} class="msp-gray-btn msp-savepreset">'+__MSP_LAN.ee_003+'</button>'+
												'</div>'+
											'</div>'+
											'{{view MSPanel.EffectList}}'),

	didInsertElement: function() {
		var that = this;

		this.$().dialog({ 
			resizable: false,
			modal: true,
			width: 1000,
			height: 550,
			title: __MSP_LAN.ee_006 || 'Transition Editor',
			draggable: false,
			show: 'fade',
			dialogClass: 'msp-dialog',
			appendTo: this.get('parentView').$(),
			dialogClass: 'msp-container msp-dialog',
			close: function( event, ui ) {
				//$(this).dialog('destroy')//.remove();
				that.get('controller.parent').closeEffectEditor();
				that.get('controller').send('cancel');
			}
		});
		
		$(window).bind('resize', {ref:this} , this.onWindowResize);
	},

	onWindowResize: function(event){
		var that = event.data.ref;
		that.$().dialog("option", "position", "center");
	},

	willDestroyElement: function(){
		$(window).unbind('resize', this.onWindowResize);
		this.$().dialog('destroy');
	}

});

/**
 * Effect Pereview child View
 * @package MSPanel
 * @extends {Ember.View} 
 * @parent MSPanel.EffectEditor
 */

var EFF_STAGE_WIDTH = 780,
	EFF_STAGE_HEIGHT = 265;

MSPanel.EffectPreview = Ember.View.extend({
	classNames: ['msp-section', 'msp-effect-preview'],
	isPlaying: false,
	isPlayingMode: false,
	template: Ember.Handlebars.compile('<div class="msp-effect-preview-cont">'+
									   		'<div class="msp-effect-sample" id="sample"></div>'+
									   		'<div class="msp-effect-guide" id="guide"></div>'+
									   		//'<div class="msp-reset-generate">'+
									   		//	'<a href="#" class="msp-eff-random" title="'+__MSP_LAN.ee_006+'" {{action "generateRandom"}}>'+__MSP_LAN.ee_004+'</a> | '+
									   		//	'<a href="#" class="msp-eff-reset" title="'+__MSP_LAN.ee_007+'" {{action "resetForm"}}>'+__MSP_LAN.ee_005+'</a>'+
									   		//'</div>'+
									   		'<div class="msp-preview-controls">'+
									   			'{{#if view.isPlaying}}'+
									   				'<a {{action "pause" target=view}} class="msp-effect-review-btn msp-preview-btn msp-pause-btn"><span class="msp-ico msp-ico-whitepause"></span></a>'+
									   			'{{else}}'+
									   				'<a {{action "play" target=view}} class="msp-effect-review-btn msp-preview-btn msp-play-btn"><span class="msp-ico msp-ico-whiteplay"></span></a>'+
									   			'{{/if}}'+
									   			'<div class="msp-effect-timeline-slider msp-ui-slider" id="timeline-slider"></div>'+
									   		'</div>'+
									   	'</div>'),

	didInsertElement: function() {
		var sample = this.$('#sample'),
			guide = this.$('#guide'),
			timelineSlider = this.$('#timeline-slider').slider()

		this.set('sample',sample);
		this.set('guide', guide);
		this.set('timelineSlider', timelineSlider);

		var that = this;
		timelineSlider.on('slide', function( event, ui ) {
			var timeline = that.get('timeline');
			that.send('pause');
			timeline.position(Math.min(0.998, ui.value / 100) * timeline.duration);
		}).css('display', 'none');

		this.alignCenter();
		this.update();

		this.set('controller.preview', this);		
	},

	willDestroyElement: function(){
		this.set('controller.preview', null);
		this.send('pause');
		this.send('exitPlaying');
		this.get('timelineSlider').remove();
	},

	/**
	 * align guide and sample to center of preview
	 */
	alignCenter: function(){
		var sample = this.get('sample'),
			guilde = this.get('guide');

		sample.css('left', (EFF_STAGE_WIDTH - sample.outerWidth()) / 2)
			  .css('top' , (EFF_STAGE_HEIGHT - sample.outerHeight()) / 2);

		guilde.css('left', (EFF_STAGE_WIDTH - guilde.outerWidth()) / 2)
		  .css('top' , (EFF_STAGE_HEIGHT - guilde.outerHeight()) / 2);
	},

	/**
	 * called when a property of draft effect changes
	 */
	update: function(){
		if( this.get('isPlayingMode') ){
			this.send('exitPlaying');
		}

		var guide = this.get('guide')[0],
			sample = this.get('sample')[0],
			origin = this.get('controller.origin'),
			transform = 'perspective(2000px) ' + this.get('controller.draftEffectText'),
			originStyle;

		guide.style[window._jcsspfx + 'Transform'] = transform;

		this.set('transform', transform);

		if( !Ember.isEmpty(origin) ){
			originStyle = (origin.x != null? origin.x : '50') + '% ' +
						  (origin.y != null? origin.y : '50') + '% ' +
						  (origin.z != null? origin.z : '0') + 'px';

			guide.style[window._jcsspfx + 'TransformOrigin'] = originStyle;
			sample.style[window._jcsspfx + 'TransformOrigin'] = originStyle;
		} else {
			guide.style[window._jcsspfx + 'TransformOrigin'] = '';
			sample.style[window._jcsspfx + 'TransformOrigin'] = '';
		}

	}.observes('controller.draftEffectText', 'controller.origin', 'controller.ease', 'controller.duration'),

	actions: {
		play: function(){

			// already played just paused by slider.
			if( this.get('isPlayingMode') ){
				this.get('timeline').paused(false);
				this.set('isPlaying', true);
				return;
			}

			var sample = this.get('sample'),
				guide = this.get('guide'),
				transform = this.get('transform'),
				timelineSlider = this.get('timelineSlider'),
				ease, tween;
				
			// create timeline
			var timeline =  new JTweenTimeline(null,0,null,{repeatCount:0});
			this.set('timeline', timeline);

			// hide guide
			guide.css('display', 'none');
			timelineSlider.css('display', '');

			if( this.get('controller.type') === 'show' ) {
				// add from
				sample.css(window._jcsspfx + 'Transform', transform);
 				if( this.get('controller.draftEffect.fade') ){
 					sample.css('opacity', 0); 
 				} 
 				ease = window.convertEaseName(this.get('controller.ease'));
 				tween = new JTween(sample[0], Number(this.get('controller.duration')), {transform:JTween.resetTransform(transform), opacity:1}, {ease:ease});
			} else {
				ease = window.convertEaseName(this.get('controller.ease'));
 				tween = new JTween(sample[0], Number(this.get('controller.duration')), {transform:transform, opacity:(this.get('controller.draftEffect.fade')? 0:1)}, {ease:ease});
			}

			timeline.addTween(0, tween);
			
			timeline.calculateDuration();
			timeline.paused(false);

			this.set('isPlaying', true);
			this.set('isPlayingMode', true);

			var that = this;
			timeline.onChange = function(){
				timelineSlider.slider('value', timeline.calculatedPosition * 100 / timeline.duration);
			};

		},

		pause: function(){
			if( !this.get('isPlaying') ) {
				return;
			}

			this.get('timeline').paused(true);
			this.set('isPlaying', false);
		},

		exitPlaying: function(){
			if( !this.get('isPlayingMode') ) {
				return;
			}

			this.set('isPlayingMode', false);
			this.send('pause');

			var timeline = this.get('timeline'),
				tween = this.get('tween'),
				sample = this.get('sample'),
				guide = this.get('guide'),
				timelineSlider = this.get('timelineSlider');
			
			timeline.removeTween(tween);
			timeline.paused(true);
			timeline.onChange = null;
			timeline = null;
			tween = null;

			this.set('tween', null);
			this.set('timeline', null);

			timelineSlider.css('display', 'none');
			sample.css('opacity', '').css(window._jcsspfx + 'Transform', '');
			guide.css('display', '');
		}
	}

});

/**
 * Effect List child view
 * @package MSPanel
 * @extends {Ember.View}
 * @parent MSPanel.EffectEditor
 */
MSPanel.EffectList = Ember.View.extend({
	classNames: ['msp-section msp-effect-list'],
	//presetEfffects: null,
	template: Ember.Handlebars.compile('<div class="msp-section-handle"><span class="msp-section-title">'+__MSP_LAN.ee_001+'</span></div>'+
										'<div class="msp-section-content">'+
											'<div class="msp-effect-list-cont">'+
									   			'{{#each effect in presetEffects}}'+
									   				'{{view MSPanel.EffectRow effect=effect}}'+
									   			'{{/each}}'+ 
									   		'</div>'+
									   		'</div>')/*,

	updatePresetEffects: function(){
		var presetEffects = [];
		this.get('controller').forEach(function(effect){
			if( effect.get('type') === 'preset' ) {
				presetEffects.push(effect);
			}
		});

		this.set('presetEffects', presetEffects);

	}.observes('controller.@each').on('init')*/

});

/**
 * Effects Row child view
 * @package MSPanel
 * @extends {Ember.View}
 * @parent MSPanel.EffectList
 */
MSPanel.EffectRow = Ember.View.extend({
	classNames: ['msp-effect-row'],
	classNameBindings : ['selected:active'],
	effect: null,
	selected: false,

	template: Ember.Handlebars.compile('<span class="msp-effect-name">{{effect.name}}</span>'+
					   					'<a {{action "removeEffect" effect target=view bubbles=false}} class="msp-effect-remove">'+
					   						'<span class="msp-ico msp-ico-grayremove"></span>'+
					   					'</a>'),

	/**
	 * Select effect row on click
	 * It will change te selected effect for selected layer in LayersController
	 */
	click: function(event){
		this.set('controller.currentEffect', this.get('effect'));
		this.get('controller').updateDraft(this.get('effect'));
	},

	/**
	 * On user select another effect in effect list
	 */
	onSelectChanged: function(){
		if( this.get('effect') === this.get('controller.currentEffect') ) {
			this.set('selected', true);
		} else {
			this.set('selected', false);
		}
	}.observes('controller.currentEffect').on('init'),

	actions: {

		/**
		 * Remove Preset Effect
		 * @param {object} effectObject 
		 */
		removeEffect: function(effect){
			if( confirm('Are you sure want to delete "'+ effect.get('name') + '"?') )
				this.get('controller').send('removeEffect' , effect);

			this.get('parentView').updatePresetEffects();
		}
	}

});

/* ================== src/js/mspanel/views/ButtonEditorView.js =================== */
;(function($){
	MSPanel.ButtonEditor = Ember.View.extend({
		classNames: ['mps-button-editor'],
		layer: null,
		template: Ember.Handlebars.compile('<div class="left-box">'+
													'{{view MSPanel.ButtonPreview layer=view.layer}}'+
													'<div class="msp-section-content">'+
											   			'{{partial "button-properties"}}'+ // partial is defined in app.html (temporary)
											   		'</div>'+
													'<div class="msp-applystyle-cont">'+
														'<button {{action "applyStyle"}} class="msp-blue-btn msp-applystyle">'+(__MSP_LAN.be_001 || 'Update Button Style') +'</button>'+
														'<button {{action "saveAsPreset"}} class="msp-gray-btn msp-savepreset">'+(__MSP_LAN.be_002 || 'Save As New Button') +'</button>'+
													'</div>'+
												'</div>'+
												'{{view MSPanel.ButtonList}}'),

		didInsertElement: function() {
			var that = this;

			this.$().dialog({ 
				resizable: false,
				modal: true,
				width: 1000,
				height: 660,
				title: __MSP_LAN.be_005 || 'Button Editor',
				draggable: false,
				show: 'fade',
				dialogClass: 'msp-dialog',
				appendTo: this.get('parentView').$(),
				dialogClass: 'msp-container msp-dialog',
				close: function( event, ui ) {
					//$(this).dialog('destroy')//.remove();
					that.get('controller').send('cancel');
					that.get('controller.parent').closeButtonEditor();
				}
			});
			
			$(window).bind('resize', {ref:this} , this.onWindowResize);
		},

		onWindowResize: function(event){
			var that = event.data.ref;
			that.$().dialog("option", "position", "center");
		},

		willDestroyElement: function(){
			$(window).unbind('resize', this.onWindowResize);
			this.$().dialog('destroy');
		}

	});

	/**
	 * Style Pereview child View
	 * @package MSPanel
	 * @extends {Ember.View} 
	 * @parent MSPanel.ButtonEditor
	 */

	var STAGE_WIDTH = 780,
		STAGE_HEIGHT = 150;

	MSPanel.ButtonPreview = Ember.View.extend({
		classNames: ['msp-section', 'msp-style-preview'],

		template: Ember.Handlebars.compile('<div class="msp-style-preview-cont">'+
										   		'<div class="msp-style-sample" id="sample"><a href="#" {{bind-attr class=":ms-btn controller.draftBtnStyle controller.draftBtnSize"}} id="btn">Button</a></div>'+
										   		'<div id="bgToggle" class="bgToggle"></div>'+
										   	'</div>'),

		didInsertElement: function() {
			var sample = this.$('#sample'),
				btn = this.$('#btn');
			this.set('sample',sample);

			var that = this;

			this.$('#bgToggle').click(function(event) {
				var $this = $(this);
				if( $this.data('isBlack') ){
					that.$().css('background-color', 'white');
					$this.css('background-color', '#222');
					$this.data('isBlack', false);
				}else{
					$this.data('isBlack', true);
					that.$().css('background-color', '#222');
					$this.css('background-color', 'white');
				}
			}).css('background-color', '#222');

			sample.on({
				mouseover: function(){
					btn.attr('style', that.get('controller.draftNormal') + ';' + that.get('controller.draftHover'));
				},
				
				mouseout: function(){
					btn.attr('style', that.get('controller.draftNormal'));
				},

				mousedown: function(){
					btn.attr('style', that.get('controller.draftNormal') + ';' + that.get('controller.draftHover') + ';' + that.get('controller.draftActive'));
				},

				mouseup: function(){
					btn.trigger('mouseover');
				},

				click: function(e){
					e.preventDefault();
				}
			})

			this.update();
		},

		alignCenter: function(){
			var sample = this.get('sample');
			sample.css('left', (STAGE_WIDTH - sample.outerWidth()) / 2)
				  .css('top' , (STAGE_HEIGHT - sample.outerHeight()) / 2);
		},

		update: function(){
			this.get('sample').trigger('mouseout');
			var that = this;
			setTimeout(function(){that.alignCenter()}, 10);
		}.observes('controller.draftNormal', 'controller.draftBtnStyle', 'controller.draftBtnSize')

	});

	/**
	 * Style List child view
	 * @package MSPanel
	 * @extends {Ember.View}
	 * @parent MSPanel.ButtonEditor
	 */
	MSPanel.ButtonList = Ember.View.extend({
		classNames: ['msp-section msp-style-list'],
		//presetStyles: null,
		template: Ember.Handlebars.compile('<div class="msp-section-handle"><span class="msp-section-title">'+(__MSP_LAN.be_004 || 'Buttons')+'</span></div>'+
											'<div class="msp-section-content">'+
												'<div class="msp-style-list-cont">'+
										   			'{{#each button in controller}}'+
										   				'{{view MSPanel.ButtonRow button=button}}'+
										   			'{{/each}}'+ 
										   		'</div>'+
										   		'</div>')
	});

	/**
	 * Button Row child view
	 * @package MSPanel
	 * @extends {Ember.View}
	 * @parent MSPanel.ButtonList
	 */
	MSPanel.ButtonRow = Ember.View.extend({
		classNames: ['msp-style-row', 'msp-be-btn-row'],
		classNameBindings : ['selected:active'],
		button: null,
		selected: false,

		template: Ember.Handlebars.compile('<span {{bind-attr class=":ms-btn button.style button.size button.className"}}>Button</span>'+
						   					'<a {{action "removeButton" button target=view bubbles=false}} class="msp-style-remove">'+
						   						'<span class="msp-ico msp-ico-grayremove"></span>'+
						   					'</a>'),

		/**
		 * Select style row on click
		 * It will change te selected style for selected layer in LayersController
		 */
		click: function(event){
			this.set('controller.currentButton', this.get('button'));
		},

		/**
		 * On user select another button in button list
		 */
		onSelectChanged: function(){
			if( this.get('button') === this.get('controller.currentButton') ) {
				this.set('selected', true);
			} else {
				this.set('selected', false);
			}
		}.observes('controller.currentButton').on('didInsertElement'),

		actions: {

			/**
			 * Remove Preset Button
			 * @param {object} styleObject 
			 */
			removeButton: function(button){
				if( confirm(__MSP_LAN.be_003 || 'Are you sure you want to delete this button?') ) {
					this.get('controller').send('removeButton' , button);
				}

				//this.get('parentView').updatePresetStyles();
			}
		}

	});
})(jQuery);

/* ================== src/js/mspanel/views/ControlsView.js =================== */
/*MSPanel.ControlsView = Ember.View.extend({
	didInsertElement: function(){
		this.get('controller').send('showControlOptions');
	}
});
*/
MSPanel.ControlBtn = Ember.View.extend({
	control: null,
	tagName: 'div',
	active:false,
	classNames: ['msp-control-btn'],
	classNameBindings: ['active:msp-blue-btn'],

	template : Ember.Handlebars.compile('<span class="msp-control-label">{{view.control.label}}</span>'+
										'<a href="#" {{action "removeControl" target=view bubbles=false}}><span class="msp-control-removes msp-ico msp-ico-whiteremove"></span></a>'),

	
	didInsertElement: function() {
		
	},

	onActiveChange: function(){
		this.set('active', this.get('controller.currentControl') === this.get('control'));
		
		if( this.get('active') ){
			this.get('controller').send('showControlOptions');
		}

	}.observes('controller.currentControl').on('init'),

	click: function(){
		if( this.get('active') ) {
			return;
		}
		this.set('controller.currentControl', this.get('control'));
		//this.get('controller').send('showControlOptions');
	},

	actions: {
		removeControl: function(){
			if( confirm('Are you sure want to remove "' + this.get('control.label') + '" control?')){
				this.get('controller').send('removeControl', this.get('control'));
			}
		}
	}

});

/* ================== src/js/mspanel/views/TemplatesView.js =================== */
/**
 * Master Slider Panel Tempaltes Modal View
 * @package MSPanel
 * @author Averta
 * @version 1.0
 */

MSPanel.TemplatesView = Ember.View.extend({

	templateName: 'TemplatesModal',

	didInsertElement: function() {
		var that = this;

		this.$().dialog({ 
			resizable: false,
			modal: true,
			width: 925,
			height: Math.max(350,window.innerHeight - 100),
			title: __MSP_LAN.tv_001,
			draggable: false,
			show: 'fade',
			appendTo: MSPanel.rootElement,
			dialogClass: 'msp-container msp-dialog',
			close: function( event, ui ) {
				//$(this).dialog('destroy')
				that.get('controller').send('closeTemplates');
			}
		});

		$(window).bind('resize', {ref:this} , this.onWindowResize);
	},

	onWindowResize: function(event){
		var that = event.data.ref;
		that.$().dialog('option', 'height', Math.max(350,window.innerHeight - 100))
				.dialog("option", "position", "center")
	},

	willDestroyElement: function(){
		$(window).unbind('resize', this.onWindowResize);
		this.$().dialog('destroy');
	}
});

MSPanel.TemplateFigure = Ember.View.extend({
	msTemplate: null,
	selected: false,
	classNames: ['msp-template-figure'],
	classNameBindings: ['selected:selected'],

	template: Ember.Handlebars.compile('{{#if view.selected}} <div class="msp-templte-selected"></div> {{/if}}'+
									   '<img {{bind-attr src=view.msTemplate.img}}>'+
									   '<div class="msp-template-caption">'+
									   		'<span>{{view.msTemplate.name}}</span>'+
									   '</div>'),

	click: function(){
		this.set('controller.draftMSTemplate', this.get('msTemplate.value'));
	},

	templateChange: function(){
		this.set('selected', this.get('msTemplate.value') === this.get('controller.draftMSTemplate'));
	}.observes('controller.draftMSTemplate').on('init')

});

/* ================== src/js/mspanel/components/UIComponents.js =================== */
/**
	MSPanel UI Components
	Version 1.0b
*/

(function($){

	/* ---------------------------------------------------------
							Metabox
	------------------------------------------------------------*/

	MSPanel.MetaBoxComponent = Ember.Component.extend({
		tagName: 'div',
		classNames: ['msp-metabox'],
		layout: Ember.Handlebars.compile('<div class="msp-metabox-handle">'+
										 	'<h3 class="msp-metabox-title">{{title}}</h3>'+
											'<div class="msp-metabox-toggle"></div>'+
										'</div>'+
										'{{yield}}'+
				 						'<div class="clear"> </div>')
	});


	/* ---------------------------------------------------------
							Tabs
	------------------------------------------------------------*/

	Ember.TEMPLATES['components/tabs-panel'] =	Ember.Handlebars.compile('{{yield}}');
	MSPanel.TabsPanelComponent = Ember.Component.extend({
		tagName: 'div',
		attributeBindings: ['id'],
		classNames: ['msp-metabox msp-metabox-tabs'],
		didInsertElement: function() {
			this.$().avertaLiveTabs();
		}
	});


	/* ---------------------------------------------------------
							Switchbox
	------------------------------------------------------------*/

	MSPanel.SwitchBoxComponent = Ember.Component.extend({
		classNames	: ['msp-switchbox'],
		offlable	: 'OFF',
		onlable 	: 'ON',
		value	: false,

		layout	: Ember.Handlebars.compile('<div class="msp-switch-cont">'+
													'<span class="msp-switch-off">{{view.offlable}}</span>'+
													'<div class="msp-switch-handle"></div>'+
													'<span class="msp-switch-on">{{view.onlable}}</span>'+
												'</div>'),
		
		click:function(){
			var that = this;
			that.set('value' , !that.get('value'));
		},
		
		update: function(){

			if(this.get('value')) 	this.$().addClass('switched');
			else 		 				this.$().removeClass('switched');

		}.observes('value').on('didInsertElement')

	});

	/* ---------------------------------------------------------
							Add Dynamic Tag
	------------------------------------------------------------*/
	MSPanel.AddDynamicTag = Ember.View.extend({
		classNames: ['msp-add-dynamic-tags'],
		editorId: null,
		template: Ember.Handlebars.compile('<button {{action "addTag" target=view}} class="msp-add-btn"><span class="msp-ico msp-ico-whiteadd"></span></button>'+
											'<div class="msp-ddlist"></div>'),

		didInsertElement: function(){
			var select = $('<select></select>').appendTo(this.$('.msp-ddlist')).width(220);

			for( var i=0, l=MSPanel.dynamicTags.length; i!==l; i++ ){
				select.append('<option value="' + MSPanel.dynamicTags[i].tag + '">' + MSPanel.dynamicTags[i].name + '</option>');
			}

			this.set('select', select);
		},

		actions: {
			addTag: function(){
				var id = this.get('editorId');
				if( tinymce && $('#wp-' + id + '-wrap').hasClass('tmce-active') ){
					tinymce.get(id).execCommand('insertHTML', false, this.get('select').val());
				}else{
					$('#' + id).insertAtCaret(this.get('select').val());
				}
			}
		}
	});

	/* ---------------------------------------------------------
							WP TinyMCE Editor
	------------------------------------------------------------*/
	var hiddenEditor = jQuery('#mspHiddenEditor')[0].outerHTML;
	function WPEditorTemplate(id){
		var newEditor = $(hiddenEditor);
		newEditor.find('link').remove(); // remove all css files init
		return newEditor.html().replace(/msp-hidden/g, id);
	}
	var __tmc_msp_id = 0;

	MSPanel.WPEditor = Ember.View.extend({
		classNames : ['msp-wp-editor'],
		_id : null,
		template : null, 
		tab: null,
		tabs: null,

		onInit: function(){
			var id = 'msp-wpeditor-' + __tmc_msp_id;
			this.set('_id', id );
			this.set('template', Ember.Handlebars.compile( WPEditorTemplate(id) ));

			__tmc_msp_id++;

		}.on('init'),

		didInsertElement: function(){
			var tabs = this.get('tabs');
			if( Ember.isEmpty(tabs) ) {
				this.createEditor();
				return;
			}

			// is in tabs
			$('#'+tabs).bind('avtTabChange', {that:this}, this.refreshEditor);
		}, 

		refreshEditor: function(event , tab){
			var that = event.data.that;

			if( that.get('tab') === tab ){
				that.createEditor();
			} 
		},

		createEditor: function(){
			if( this.get('inited') === true ){
				return;
			}

			this.set('inited', true);
			var id = this.get('_id'),
				that = this;

			// tinymce
			if( window.tinymce ){
				var settings = $.extend({}, window.tinyMCEPreInit.mceInit['msp-hidden'] || {});
				settings.forced_root_block = ""; 
				settings.force_br_newlines = true;
				settings.force_p_newlines = false;
				settings.wpautop = false;

				if( tinyMCE.majorVersion == '3' ){
					
					settings.body_class = settings.elements = id;

					settings.setup = function (ed) {
				        ed.onInit.add(function(ed) {
				        	that.initEditor(tinyMCE.getInstanceById(id));
				        });
				    };

					tinymce.init(settings);	
				/*	setTimeout(function(){
						that.initEditor(tinyMCE.getInstanceById(id));
					}, 150);*/

				} else if ( tinyMCE.majorVersion == '4' ){
					settings.body_class = "content post-type-post post-status-auto-draft post-format-standard";
					settings.selector = '#'+id;

					settings.setup = function (ed) {
				        ed.on('init', function(args) {
				            that.initEditor(tinyMCE.get(id));
				        });
				    };

					tinymce.init(settings);	

				}

				/*settings.setup = function(ed) {
					//that.initEditor(ed);
				}*/
			}

			var qtagSettings = $.extend({}, window.tinyMCEPreInit.qtInit['msp-hidden'] || {}),
				qtags;

			qtagSettings.id = id;
			
			if ( typeof(QTags) === 'function' ) {
				qtags = quicktags(qtagSettings);
				QTags.buttonsInitDone = false;
				QTags._buttonsInit();
				that.set('qtags', qtags );

				// if tinymce enabled
				if ( window.tinymce ) {
					switchEditors.go(id, 'html');
				} else {
					this.onValueChanged();
				}

				this.$('textarea#'+this.get('_id')).on('change keyup paste', function(e){
				///	that.set('internalChange', true);
					that.set('value', $(this).val());
				//	that.set('internalChange', false);
				});
			}
		},

		// initialize mce editor
		initEditor: function(mce){
			var id = this.get('_id'),
				value = this.get('value'),
				that = this;
			
			this.$('.wp-editor-wrap').on('mousedown', function(){
				wpActiveEditor = id;
			});

		/*	function updateValue(ed,e){	
				that.set('value', mce.getContent());
			}*/

			function internalUpdate(ed,e){
				that.set('internalChange', true);
				that.set('value', mce.getContent());
				that.set('internalChange', false);
			}

			// register events
			if( tinyMCE.majorVersion == '3' ){
				mce.onChange.add(internalUpdate);
				mce.onKeyUp.add(internalUpdate);
			} else if ( tinyMCE.majorVersion == '4' ){
				mce.on('change', internalUpdate);
				mce.on('keyup', internalUpdate);
			}

			this.$().click(internalUpdate);
			
			setTimeout(function(){
				switchEditors.go(id, 'html');
				switchEditors.go(id, 'tmce');
			}, 100);

			this.set('mce', mce);

			this.onValueChanged();
		},

		onValueChanged: function(){

			if( !this.get('inited') ){
				return;
			}

			var value = this.get('value');
			
			this.$('textarea#'+this.get('_id')).val(value);

			if( this.get('internalChange') ){
				this.set('internalChange', false);
				return;
			}

			// if tinymce enabled
			if ( window.tinymce ) {
				var mce = this.get('mce');
				if( !Ember.isEmpty(mce) && value != null){
					mce.setContent(value);
				} else if( value == null ){
					mce.setContent(' ');
				}
			}

		}.observes('value'),

		willDestroyElement: function(){
			if( !this.get('inited') ){
				return;
			}

			if( window.tinymce ){
				tinymce.remove(this.get('_id'));	
			} 

			var qtags = this.get('qtags');
			if( qtags ){
				$(qtags.toolbar).remove();
				qtags.toolbar = null;
				qtags = null;

				if( QTags.instances[this.get('_id')] ) {
					delete QTags.instances[this.get('_id')];
				}

				this.$('textarea#'+this.get('_id')).remove();
			}

			var tabs = this.get('tabs');
			if( !Ember.isEmpty(tabs) ){
				$('#' + tabs).unbind('avtTabChange', this.refreshEditor);
			}
		}
	});


	/* ---------------------------------------------------------
							CKEditor
	------------------------------------------------------------*/
	/*MSPanel.HTMLTextArea = Ember.TextArea.extend({
		didInsertElement: function() {
			this._super();
			var that = this;

			var cke = CKEDITOR.replace( that.get('elementId'), {	
				uiColor: '#f1f1f1',
				removeButtons: 'Underline,Subscript,Superscript',
				entities  : false,
				htmlEncodeOutput: true,
				forcePasteAsPlainText: true,
				enterMode : CKEDITOR.ENTER_BR,
				shiftEnterMode: CKEDITOR.ENTER_P ,
				toolbarGroups : [
				    { name: 'clipboard',   groups: [ 'clipboard', 'undo' ] },
				    { name: 'editing',     groups: [ 'find', 'selection', 'spellchecker' ] },
				    { name: 'links' },
				    { name: 'insert' },
				     { name: 'tools' },
				    
				    { name: 'document',    groups: [ 'mode', 'document', 'doctools' ] },
				    '/',
				    { name: 'styles' },
				    { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
				    { name: 'paragraph',   groups: [ 'blocks', 'align','list', 'indent'  ] },
				   { name: 'others' }
				    
				]

			});
			
			var update = function(e){
				//if (e.editor.checkDirty()) {
					that.set('internalChange' , true);
					that.set('value', cke.getData());
					//console.log('changes', that.get('value'));

				//}
			}

			//cke.on( 'contentDom', function() {
			//    var editable = cke.editable();

			//    editable.attachListener( editable, 'keyup', function() {
			    //    console.log( 'Editable has been clicked' );
			//        update();
			//    });
			//});

			cke.on('key', update);
			cke.on('blur', update);
			cke.on('paste', update);

			this.set('cke' , cke);
		},

		willDestroyElement: function(){
			this.get('cke').destroy();
			//CKEDITOR.remove(this.get('cke'));
			this.set('cke', null);
		},

		onValueChanged : function(){
			if(this.get('internalChange')){
				this.set('internalChange' , false);
				return;
			}

			var cke = this.get('cke');
			cke.setData(this.get('value'));
		}.observes('value')
	});*/

	/* ---------------------------------------------------------
						Number Input
	------------------------------------------------------------*/

	/* Fixed jQuery UI Spinner changing value without focus bug.  */
	if( jQuery.ui && jQuery.ui.spinner ){
		jQuery.ui.spinner.prototype._events.mousewheel = function ( event, delta ) {

			if ( !delta || !this.element.is(':focus') ) {
				return;
			}
			if ( !this.spinning && !this._start( event ) ) {
				return false;
			}

			this._spin( (delta > 0 ? 1 : -1) * this.options.step, event );
			clearTimeout( this.mousewheelTimer );
			this.mousewheelTimer = this._delay(function() {
				if ( this.spinning ) {
					this._stop( event );
				}
			}, 100 );
			event.preventDefault();
		} 
	}

	MSPanel.NumberInputView = Ember.View.extend({
		step : 1,
		min: 0,
		tagName: 'input',
		attributeBindings:['type'],
		lastValue: null,
		type: 'text',

		didInsertElement : function(){

			var that = this,
				input = this.$();
			var updateValue = function(event, ui){
				var value = input.spinner('value');
				that.set('internalChange', true);

				if( isNaN(value) || value == null  ){
					that.set('value', undefined);
				}else{
					that.set('value', parseFloat(value));
				}
			}

			input.on('change',updateValue).spinner({ 
				step: this.get('step'),
				numberFormat: "n",
				min:this.get('min'),
				max:this.get('max'),
				spin: updateValue,
				stop: updateValue
			}).spinner('value', this.get('value'));
			
		},

		onValueChanged : function(){
			//console.log(this.get('value'));
			if(this.get('internalChange')){
				this.set('internalChange', false);
				//return;
			}

			//this.$().val(this.get('value'));

			this.$().spinner('value',  this.get('value'));


			/*if(this.get('internalChange')){
				this.set('internalChange', false);
				return;
			}*/
			//var value = Number(this.get('value'));
				
				//this.$().val(value);

			/*
			if(value == this.get('lastValue')){
				return;
			}
			// convert to number always 		
			if(value === '' || isNaN(value)){
				this.set('value', undefined);
				return;
			}

			if( typeof value !== 'number') {
				this.set('value', Number(this.get('value')));
			}
			
			if(!Ember.isEmpty(value) && value < this.get('min')){
				value = this.get('min');
				this.set('value' , value);
			}

			

			*/
		}.observes('value')

	});

	Ember.Handlebars.helper('number-input' , MSPanel.NumberInputView);

	/**
	 * Color Picker
	 * @package MSPanel
	 * @requires spectrum color picker
	 */
	MSPanel.ColorPickerComponent = Ember.Component.extend({
		tagName: 'input',
		classNames: 'msp-color-picker',
		value: null,

		didInsertElement: function(){
			var that = this;
			this.$().spectrum({
				color: this.get('value'),
			    allowEmpty:true,
			    showInput: true,
			    showAlpha: true,
			    clickoutFiresChange:true,
			    preferredFormat: "hex6",
			    change: function(color) {
			        if( color === null) {
			        	that.set('value' , null);
			        } else {
			        	that.set('value', color.toString());
			        }
			    }
			})
		},

		willDestroyElement: function(){
			this.$().spectrum("destroy");
		},
		onValueChanged: function(){
			this.$().spectrum("set", this.get('value'));
		}.observes('value')

	});

	/**
	* Dropdwon list 
	* @package MSPanel
	*/
	MSPanel.DropdwonListComponent = Ember.Component.extend({
		tagName: 'div',
		classNames: ['msp-ddlist'],
		layout: Ember.Handlebars.compile('<select>{{yield}}</select>'),
		value: null,
		width: 100,

		didInsertElement: function(){
			var that = this;
			this.$('select').on('change', function(){
				var option = that.$('select option:selected');
				that.set('value', option.attr('value'));
			}).width(this.get('width'));

			this.onValueChanged();
		},

		onValueChanged: function(){
			if( !Ember.isEmpty(this.get('value')) ){
				this.$('select').val(this.get('value'));	
			}
		}.observes('value')
	});


	/**
	 * Multiple dropdown list
	 */
	MSPanel.MultiDropdwonListComponent = Ember.Component.extend({
		tagName: 'div',
		classNames: ['msp-ddlist', 'msp-ddlist-multiselect'],
		layout: Ember.Handlebars.compile('<select {{bind-attr size=view.size}} multiple>{{yield}}</select>'),
		value: null,
		width: 100,
		size: 7,

		didInsertElement: function(){
			var that = this;
			this.$('select').on('change', function(){
				that.set('value', $(this).val());
			}).width(this.get('width'));

			this.onValueChanged();
		},

		onValueChanged: function(){
			if( !Ember.isEmpty(this.get('value')) ){
				this.$('select').val(this.get('value'));	
			}
		}.observes('value')
	});


	/**
	 * Google fonts combobox
	 * @package MSPanel
	 * @requires averta GFonts
	 */
	MSPanel.GoogleFontsComponent = Ember.Component.extend({
		tagName: 'div',
		classNames: ['msp-ddlist','msp-gfonts-select'],
		defaultTemplate: Ember.Handlebars.compile('<select><option value="--" selected>Loading fonts..</option></select>'),
		value: null,
		variants: null,
		width: 210,

		didInsertElement: function(){
			var that = this;

			GFonts.getList(function(){
				that.$('select').html('<option value="--" selected>-- select --</option>' + GFonts.generateSelectList());
				that.onValueChanged();
				that.$('select').trigger('change');
			});
			
			this.$('select').on('change', function(){
				var option = that.$('select option:selected');
				if( option.val() === '--' ){
					that.set('value', undefined);
					that.set('variants', undefined);
				} else {
					that.set('value', option.attr('value'));
					that.set('variants', option.attr('data-variants'));
				}
			}).width(this.get('width'));

			that.onValueChanged();
		},

		onValueChanged: function(){
			if( Ember.isEmpty(this.get('value')) ){
				this.$('select').val('--'); // select
			} else {
				this.$('select').val(this.get('value'));
			}
		}.observes('value')
	});

	/**
	 * Google fonts combobox
	 * @package MSPanel
	 * @requires averta GFonts
	 */
	MSPanel.GoogleFontWeightsComponent = Ember.Component.extend({
		tagName: 'div',
		classNames: ['msp-ddlist','msp-gfonts-select msp-gfonts-weight'],
		defaultTemplate: Ember.Handlebars.compile('<select></select>'),
		variants: null,
		value: null,
		width: 120,
		didInsertElement: function(){
			var that = this;
			
			this.$('select').on('change', function(){
				var option = that.$('select option:selected');
				
				if( option.length === 0 ){ 
					that.$('select').val('normal');
					return;
				}

				if( option.val() === '--' ){
					that.set('value', null);
				} else {
					that.set('value', option.attr('value'));
				}

				//that.set('internalChange', true);

			}).width(this.get('width'));

			that.updateVariants();
		},

		updateVariants: function(){
			var variants = this.get('variants');
			if( Ember.isEmpty(variants) ){
				this.$('select').html('<option>Select font</option>')
			} else {
				var options = '',
					value = this.get('value');
				variants = variants.split(',');
				for(var i=0, l=variants.length; i!==l; i++){
					if( variants[i].indexOf('italic') === -1 ){
						if( variants[i] === 'regular' ){
							options += '<option value="normal"' + (value === 'normal' ? 'selected' : '') + '>Normal</option>';
						} else {
							options += '<option value="' + variants[i] + '"' + (value === variants[i] ? 'selected' : '') + '>' + variants[i] + '</option>';
						}
					}
				}

				this.$('select').html(options).trigger('change');
			}

			//this.onValueChanged();

		}.observes('variants')

		/*onValueChanged: function(){
			if( this.get('internalChange') ){
				this.set('internalChange', false);
				return;
			}

			if( Ember.isEmpty(this.get('value')) ){
				this.$('select').val('normal'); // select
			} else {
				this.$('select').val(this.get('value'));
			}
		}.observes('value')*/

	});

	/**
	 * CodeMirror Component
	 * @package MSPanel
	 * @requires Codemirror
	 */
	MSPanel.CodeMirrorComponent = Ember.Component.extend({
		classNames: ['msp-codemirror'],
		width: 250,
		height: 200,
		mode: 'css',
		tab: null,
		tabs: null,
		layout: Ember.Handlebars.compile('<textarea>{{yield}}</textarea>'),

		didInsertElement: function(){

			this.$().width(this.get('width'))
					.height(this.get('height'));

			var that = this,
				editor = CodeMirror.fromTextArea(this.$('>textarea')[0], {
				lineNumbers:true,
				mode:this.get('mode')
			});

			editor.on('change', function(){ 
				that.set('internalChange', true);
				that.set('value', editor.getValue());
			})

			this.set('editor', editor);

			var value = this.get('value');
			if( !Ember.isEmpty(value) ) {
				editor.setValue(value);
			}

			// is in tabs
			var tabs = this.get('tabs');
			if( !Ember.isEmpty(tabs) ){
				$('#'+tabs).bind('avtTabChange', {that:this}, this.refreshEditor);
			}
		},

		onValueChanged: function(){	
			if( this.get('internalChange') === true) {
				this.set('internalChange', false);
				return;
			}
			
			this.get('editor').setValue(this.get('value'));
			this.set('internalChange', false);
			
		}.observes('value'),

		refreshEditor: function(event , tab){
			var that = event.data.that;

			if( that.get('tab') === tab ) {
				that.get('editor').refresh();
			}
		},

		willDestroyElement: function(){
			var tabs = this.get('tabs');
			if( !Ember.isEmpty(tabs) ){
				$('#' + tabs).unbind('avtTabChange', this.refreshEditor);
			}

			var editor = this.get('editor');
			editor.toTextArea();
			editor = null;
			this.set('editor', null);
		}

	});


	/**
	 * Pattern Picker Component
	 * @package MSPanel
	 * @requires spectrum color picker for styling
	 */

	var patterns = '<div class="msp-pattern-prev ms-pattern"></div>';
	for(var i=1; i<=30; i++){
		patterns += '<div class="msp-pattern-prev ms-pattern ms-patt-'+i+'" data-pattern="ms-patt-'+i+'" style="' + (i > 15 ? 'background-color:black;' : '') + '"></div>'
	}

	MSPanel.PatternPickerComponent = Ember.Component.extend({
	  	tagName: 'div',
	  	classNames: ['msp-pattern-picker'],
	  	value: null,
	  	patternBoxIsOpen: false,
	  	layout: Ember.Handlebars.compile('<div class="msp-pattern-control sp-replacer sp-light">'+
	  										'<div {{bind-attr class=":ms-pattern-preview :ms-pattern view.value"}}></div>'+
	  									 	'<div class="sp-dd">▼</div>'+
	  									 '</div>'+
	  									 '<div class="msp-patterns-box">'+patterns+'</div>'),
	  	didInsertElement: function() {
	  		var that = this;
			
	  		this.$('.msp-pattern-control').on('click', {that:this} , this.togglePatternbox);
	  		$(document).on('click', {that:this}, this.closePatternbox);

	  		this.$('.msp-patterns-box').css('display', 'none')
	  			.on('click', function(e){e.stopPropagation();})
				.find('.ms-pattern').on('click', {that:this}, this.selectPattern);
	  	},

	  	togglePatternbox: function(e) {
	  		var that = e.data.that;
			e.stopPropagation();
	  		
	  		if( that.get('patternBoxIsOpen') ) {
	  			that.closePatternbox(e);
	  		}

	  		that.$('.msp-patterns-box').css('display', '');
	  		that.set('patternBoxIsOpen', true);
	  	},

	  	closePatternbox: function(e){
	  		var that = e.data.that;
	  		if( !that.get('patternBoxIsOpen') ) {
	  			return;
	  		}

	  		that.$('.msp-patterns-box').css('display', 'none');
	  		that.set('patternBoxIsOpen', false);
	  	},

	  	selectPattern: function(e) {
	  		e.stopPropagation();
	  		var that = e.data.that;
	  		that.set('value', $(this).data('pattern'));
	  	},

	  	willDestroyElement: function(){
			this.$('.msp-patterns-box').off('click', this.togglePatternbox).find('.ms-pattern').off('click'); 
			$(document).off('click', this.closePatternbox);
	  	}

	});

})(jQuery);

/* ================== src/js/mspanel/controllers/ApplicationController.js =================== */
/*
 * Application controller
 * @package MSPanel
 */

MSPanel.pushData = null;
MSPanel.ApplicationController = Ember.Controller.extend({

    isFlickr    : __MSP_TYPE === 'flickr',
    isFacebook  : __MSP_TYPE === 'facebook',
    isPost      : __MSP_TYPE === 'post',
    isWcproduct : __MSP_TYPE === 'wc-product',
    sliderId    : MSPanel.SliderID,

    // if true save button will be disabled
    isSending: false,

    // the status message that appears after save button
    statusMsg: '',

    hasError: false,

    onInit: function(){

        // check for noConflict jquery
        if ( !window.$ ){
            window.$ = jQuery.noConflict();
        }

        // fetch all data

        //setting
        MSPanel.Settings.find();
        // slides
        MSPanel.Slide.find();
        // layer
        MSPanel.Layer.find();
        // style
        MSPanel.Style.find();
        // effect
        MSPanel.Effect.find();
        // style
        MSPanel.PresetStyle.find();
        // effect
        MSPanel.PresetEffect.find();
        //control
        MSPanel.Control.find();
        //callback
        MSPanel.Callback.find();
        //buttonClass
        MSPanel.ButtonStyle.find();

        //this.set('useCustomTemplate', MSPanel.Settings.find(0).get('msTemplate') === 'custom');
        this.set('disableControls', MSPanel.Settings.find(0).get('disableControls'));

        var that = this;
        MSPanel.pushData = function(){
            that.prepareData();
        };

        MSPanel.createButton = this.createButton;

        // redirect if woocommerce not installed
        if ( __MSP_TYPE === 'wc-product' && __MSP_POST == null && __WC_INSTALL_URL != null ){
            this.set('hasError', true);
            this.set('errorTemplate', 'wooc-error');
            this.set('wooLink', __WC_INSTALL_URL);
        }

        // generate buttons style element
        this.generateButtonStyles();

        this.set('shortCode', '[masterslider id="'+this.get('sliderId')+'"]');
        this.set('phpFunction', '<?php masterslider('+this.get('sliderId')+'); ?>');

        jQuery('#panelLoading').remove();

        if ( window._msp_init_timeout ){
            clearTimeout(window._msp_init_timeout);
        }

        // setup sticky save bar
        $(window).scroll(function() {
           if($(window).scrollTop() + $(window).height() >= $(document).height() - 45) {
              $('#saveBar').removeClass('msp-sticky-bar');
              $('#saveBarPlaceHolder').css('display', 'none');
           } else {
              $('#saveBar').addClass('msp-sticky-bar');
              $('#saveBarPlaceHolder').css('display', '');
           }
        }).trigger('scroll');

        $('#timeAgo').timeago();
        setInterval($.proxy(this.updateSavedTime, this), 30000);

        // save shortcut
        $(document).bind('keydown', function( e ) {
            if( ( e.metaKey || e.ctrlKey ) && e.which == 83 ){
                e.preventDefault();
                if ( !that.get('isSending') ) {
                    that.send('saveAll');
                }
                return false;
            }
        });

    }.on('init'),

    updateSliderSlugShortCodes: function () {
        var alias = this.get('sliderSlug');

        if ( Ember.isEmpty(alias) ) {
            alias = MSPanel.SliderSlug
        }

        this.set('shortCodeSlug', '[masterslider alias="'+alias+'"]');
        this.set('phpFunctionSlug', '<?php masterslider("'+alias+'"); ?>');

    }.observes('sliderSlug').on('init'),

    prepareData: function(){
        // Generate used fonts
        var fonts = {},
            font_str = '';
        MSPanel.Style.find().forEach(function(record){
            var font = record.get('fontFamily'),
                weight = record.get('fontWeight');

            if( !Ember.isEmpty(font) ){

                if( !fonts[font] ){
                    fonts[font] = [];
                }

                if( weight === 'normal' ){
                    weight = 'regular';
                }

                if( !Ember.isEmpty(weight) && fonts[font].indexOf(weight) === -1 ) {
                    fonts[font].push(weight);
                }
            }
        });

        for(var font in fonts){
            font_str += font.replace(/\s/, '+') + ':' + fonts[font].join(',') + '|';
        }

        MSPanel.Settings.find(1).set('usedFonts', font_str.slice(0,-1));

        // save all models

        // settings
        this.saveRecords(MSPanel.Settings.find());
        // slides
        this.saveRecords(MSPanel.Slide.find());
        // layer
        this.saveRecords(MSPanel.Layer.find());
        // style
        this.saveRecords(MSPanel.Style.find());
        // effect
        this.saveRecords(MSPanel.Effect.find());
        // preset style
        this.saveRecords(MSPanel.PresetStyle.find());
        // preset effect
        this.saveRecords(MSPanel.PresetEffect.find());
        // control
        this.saveRecords(MSPanel.Control.find());
        // callback functions
        this.saveRecords(MSPanel.Callback.find());
        // button classes
        this.saveRecords(MSPanel.ButtonStyle.find());

        //console.log('saving data');
    },

    generateButtonStyles: function(){
        var styles = MSPanel.ButtonStyle.find(),
            css = '',
            $styleElement = $('#msp-buttons');

        styles.forEach(function(style){
            css += '.' + style.get('className') + ' {'+
                             style.get('normal')+
                    '}\n'+

            '.' + style.get('className') + ':hover {'+
                            style.get('hover')+
                    '}\n'+
            '.' + style.get('className') + ':active {'+
                            style.get('active')+
                    '}\n';
        });

        if( $styleElement.length === 0 ) {
            $styleElement = $('<style id="msp-buttons"></style>').text(css).appendTo($('head'));
        } else {
            $styleElement.text(css);
        }
    },

    actions: {
        saveAll: function(){
            this.prepareData();
            this.sendData();
        },

        showPreview: function(event){
            if(window.lunchMastersliderPreview){
                lunchMastersliderPreview(event);
            }
        }
    },

    saveRecords: function(records){
        records.forEach(function(record){ record.save(); });
    },

    /**
     * Send Data to WP Admin
     * @since  1.0.0
     * @return {null}
     */
    sendData: function(){

        this.set('statusMsg', __MSP_LAN.ap_001);
        this.set('isSending', true);
        this.set('savingStatus', 'msp-saving');
        var that = this;


        jQuery.post(
            __MS.ajax_url,
            {
                action          : 'msp_panel_handler', // the handler
                nonce           : jQuery('#msp-main-wrapper').data('nonce'), // the generated nonce value
                msp_data        : B64.encode(JSON.stringify(MSPanel.data)),
                preset_style    : B64.encode(JSON.stringify(MSPanel.PSData)),
                preset_effect   : B64.encode(JSON.stringify(MSPanel.PEData)),
                buttons         : B64.encode(JSON.stringify(MSPanel.PBData)),
                slider_id       : MSPanel.SliderID,
            },
            function(res){
                that.set('statusMsg', res.message);
                that.set('isSending', false);
                if ( res.success === true ) {
                    that.set('savingStatus', 'msp-save-succeed');
                    that.startAutoHideMsg();
                } else {
                    that.set('savingStatus', 'msp-save-error');
                }

            }
        );
    },

    startAutoHideMsg : function () {
        var timeout = this.get('msgTimeout'),
            that = this;
        if ( !Ember.isEmpty(timeout) ) {
            clearTimeout(timeout);
        }

        this.set('timeout', setTimeout ( function() {
            that.set('savingStatus', 'msp-save-hide msp-save-succeed');
            $('#timeAgo').attr('title', new Date().toISOString())
            that.updateSavedTime();
        }, 2000));
    },

    updateSavedTime : function ( ) {
        var timeEle = $('#timeAgo');
        if ( timeEle.attr('title') ) {
            $('#timeAgo').timeago('updateFromDOM');
        }
    },

    createButton: function(normal, hover, active, style, size){
        var newPreset = MSPanel.ButtonStyle.create({
            normal : normal,
            hover  : hover ,
            active : active,
            size   : size  ,
            style  : style
        });
        newPreset.save();
        newPreset.set('className', 'msp-preset-btn-' + newPreset.get('id'));
        newPreset.save();
    }

});

/* ================== src/js/mspanel/controllers/SettingsController.js =================== */

/**
 * Master Slider Settings Controller
 * @package MSPanel
 * @extends {Ember.Controller}
 */
MSPanel.SettingsController = Ember.ObjectController.extend({

    customSlider    : window.__MSP_TYPE && window.__MSP_TYPE === 'custom',
    templateSlider  : window.__MSP_TYPE && ( window.__MSP_TYPE === 'flickr' || window.__MSP_TYPE === 'post' || window.__MSP_TYPE === 'wc-product' || window.__MSP_TYPE === 'facebook'),
    sliderSkins     : __MSP_SKINS,


    needs: ['application', 'controls'],

    msTemplateName: null,
    msTemplateImg: null,
    draftMSTemplate:null,
    templates: MSPanel.SliderTemplates,
    showAutoHeight: false,
    showNearbyNum: false,
    showWrapperWidth: false,
    preloadMethod: null,

    /**
     * Setup controller init values
     * It called from ember router in MSPanel.js
     */
    setup: function(){
        // read preload valu from model and setup preload select list
        var preload = this.get('preload');
        if( preload === 'all' || preload === '-1' ){
            this.set('preloadMethod' , preload);
        } else {
            this.set('preloadMethod' , 'nearby');
        }

        this.set('draftMSTemplate', this.get('msTemplate'));

        this.updateTemplate(true);

        // check the slider slug value
        this.send('checkSliderSlug');
    },

    /**
     * Remove autoheight option if layout style is fullscreen or autofill
     */
    sliderLayoutChanged: function(){
        var layout = this.get('layout');
        if( layout === 'fullscreen' || layout === 'autofill' ) {
            this.set('showAutoHeight' , false);
            this.set('autoHeight' , false);
        } else {
            this.set('showAutoHeight' , true);
        }

        this.set('showWrapperWidth', layout === 'boxed' || layout === 'partialview');
        this.set('showAutoFillTarget', layout === 'autofill');
        this.set('showMinHeight', layout !== 'fullscreen' && layout !== 'autofill' && !this.get('autoHeight'));


    /*  if( layout === 'boxed' && Ember.isEmpty(this.get('wrapperWidth')) ){
            this.set('wrapperWidth', this.get('width'));
            this.set('wrapperWidthUnit', 'px');
        }

        if( layout === 'partialview' && Ember.isEmpty(this.get('wrapperWidth')) ){
            this.set('wrapperWidth', '100');
            this.set('wrapperWidthUnit', '%');
        }*/

        this.set('showFSMargin', layout === 'fullscreen');

    }.observes('layout','autoHeight').on('setup'),

    /**
     * controll preloading method
     */
    preloadSetup: function(){
        var preloadMethod = this.get('preloadMethod');

        if( preloadMethod === 'nearby' ) {
            this.set('showNearbyNum' , true);
            var preload = this.get('preload');
            if(preload === 'all' || preload === '-1'){
                this.set('preload' , '0');
            }
        } else {
            this.set('showNearbyNum' , false);
            this.set('preload' , preloadMethod);
        }

    }.observes('preloadMethod').on('setup'),
    updateTemplate: function(init){
        var templateObject,
            msTemplate = this.get('msTemplate');

        this.get('templates').forEach(function(template){
            if( template.value === msTemplate ) {
                templateObject = template;
                return;
            }
        });

        if( templateObject ){
            this.set('msTemplateName', templateObject.name);
            this.set('msTemplateImg', templateObject.img);
            this.set('msTemplateClass', templateObject.className);
            this.set('controllers.application.disableControls', templateObject.disableControls );
            this.set('disableControls', templateObject.disableControls );

            if(!init){
                var controllController = this.get('controllers.controls'),
                    controlObj,
                    control;
                // remove added controls
                var controls = MSPanel.Control.find();

                while(controls.get('firstObject')){
                    var control = controls.get('firstObject');

                    controllController.findControlObj(control.get('name')).used = false;
                    control.deleteRecord();
                }

                // create template controls
                for (var controlName in templateObject.controls){
                    controlObj = controllController.findControlObj(controlName);
                    control = MSPanel.Control.create($.extend(true, controllController.getDefaultValues(controlName), templateObject.controls[controlName]));
                    control.set('label', controlObj.label);
                    controlObj.used = true;
                    control.save();
                }

                // update slider settings
                for(var option in templateObject.settings){
                    this.set(option, templateObject.settings[option]);
                }
            }

        } else { // template not found! so lets select custom template
            this.set('draftMSTemplate', 'custom');
            this.updateTemplate();
        }


    },

    onDeepLinkChange: function(){
        var deepLink = this.get('deepLink');

        if( Ember.isEmpty(deepLink) ) {
            this.set('deepLink', 'ms-'+MSPanel.SliderID);
        }

    }.observes('deepLink').on('setup'),
    actions: {

        openTemplates: function(){
            var templatesView = MSPanel.TemplatesView.create({
                controller: this
            });

            this.get('mainView').createChildView(templatesView);
            this.set('templatesView', templatesView);

            templatesView.appendTo(MSPanel.rootElement);
        },

        closeTemplates: function(){
            this.get('templatesView').destroy();

            // rollback to current template
            this.set('draftMSTemplate', this.get('msTemplate'));
        },

        saveTemplate: function(){
            if( this.get('draftMSTemplate') === this.get('msTemplate') ){
                this.send('closeTemplates');
                return;
            }

            if( confirm(__MSP_LAN.tv_002) ){
                // update msTemplate
                this.set('msTemplate', this.get('draftMSTemplate'));
                this.send('closeTemplates');
                this.updateTemplate();
            }
        },

        checkSliderSlug: function() {
            var slug = this.get( 'slug' ), that = this;
            this.set('slugStatus', 'msp-saving');

            if ( Ember.isEmpty(slug) ) {
                // auto generate the slug
                this.set( 'slug', MSPanel.SliderSlug );
                that.set('slugStatus', '');
            } else if ( __MS.ajax_url ) {
                // check wheter it is unique.
                $.ajax({
                    url: __MS.ajax_url,
                    data: {
                        slug: slug,
                        id: MSPanel.SliderID,
                        action : 'ms-slug', // the handler
                        nonce  : jQuery('#msp-main-wrapper').data('nonce') // the generated nonce value
                    },
                }).done(function( data ) {
                    if ( data.success ) {
                        that.set('slug', data.data);
                        that.set('slugStatus', 'msp-save-succeed');
                    } else {
                        that.set('slugStatus', 'msp-save-error');
                    }
                });
            } else {
                that.set('slugStatus', '');
            }

            this.set('controllers.application.sliderSlug', this.get('slug'));
        }
    }
});

/* ================== src/js/mspanel/controllers/SlidesController.js =================== */
MSPanel.SlidesController = Ember.ArrayController.extend({
    needs:['flickr'],
    templateSlider  : window.__MSP_TYPE && ( window.__MSP_TYPE === 'flickr' || window.__MSP_TYPE === 'post' || window.__MSP_TYPE === 'facebook' || window.__MSP_TYPE === 'wc-product' ),
    customSlider    : window.__MSP_TYPE && window.__MSP_TYPE === 'custom',

    _order : -1,

    bgImgSelector: null, // it will be filled by a imgSelect view element.
    sortProperties: ['order'],
    stylesController: null,
    effectsController: null,
    buttonsController: null,
    layersList : [],
    layersController: null, // filled by layer controller init
    mainView: null, // main view object which will be setted by MSPanel.SlidesView
    isFirst:true,
    currentSlide: null,

    setup: function(){

        // slider type
        if( Ember.isEmpty(this.get('sliderSettings.type')) ){
            this.set('sliderSettings.type', __MSP_TYPE);
        }

        this.set('sliderSettings.sliderId', MSPanel.SliderID);

        if( this.get('length') !== 0 ){
            var slide = this.get('firstObject');
            this.set('currentSlide' , slide);
            this.updateLayersList(slide.get('layers'));
            this.updateOrder();
        }
        else if ( this.get('templateSlider') ) { // create one slide for template sliders.
            this.send('newSlide');
            this.updateOrder();
        }

        if ( !this.get('templateSlider') ) {
            this.generateOverlayLayersSlide();
        }
        //this.set('_order', this.get('lastObject.order'));

    },

    // onCurrentSlideChanged : function () {
    //  if ( this.get('length') === 0 ) {
    //      if ( !Ember.isEmpty(this.get('bgSelect')) ) {
    //          this.get('bgSelect').destroy();
    //      }

    //      var that = this;
    //      var bgSelect = MSPanel.ImgSelect.create({controller:this});
    //          bgSelect.reopen({
    //              value: Ember.computed.alias('controller.currentSlide.bg'),
    //              thumb: Ember.computed.alias('controller.currentSlide.bgThumb')
    //          });
    //      this.set('bgSelect', bgSelect);
    //  }
    // }.observes('currentSlide'),


    duplicateSlide : function(slide){
        var slideProp = slide.toJSON();
        delete slideProp.id;
        delete slideProp.layers;
        var newSlide = MSPanel.Slide.create(slideProp);
        slide.get('layers').forEach(function(layer){

            var properties = layer.toJSON(),
                styleProp = layer.get('styleModel').toJSON(),
                showEffProp = layer.get('showEffect').toJSON(),
                hideEffProp = layer.get('hideEffect').toJSON();

            delete properties.id;
            delete styleProp.id;
            delete showEffProp.id;
            delete hideEffProp.id;

            // style
            var styleModel = MSPanel.Style.create(styleProp);
            styleModel.save();
            properties.styleModel = styleModel;

            // show transition effect
            var showEffect = MSPanel.Effect.create(showEffProp);
            showEffect.save();
            properties.showEffect = showEffect;

            // hide transition effect
            var hideEffect = MSPanel.Effect.create(hideEffProp);
            hideEffect.save();
            properties.hideEffect = hideEffect;

            properties.slide = newSlide;

            var newLayer = MSPanel.Layer.create(properties);
            newLayer.save();

            // update class name if it's a custom class
            if( newLayer.get('styleModel.type') === 'custom' ) {
                var newClassName = 'msp-cn-' + MSPanel.SliderID + '-' + newLayer.get('id');
                newLayer.set('styleModel.className', newClassName);
                newLayer.set('className', newClassName);
                styleModel.save();
                newLayer.save();
            }

            newSlide.get('layers').addObject(newLayer);
        });
        // insert after
        newSlide.set('order' , slide.get('order') + 1);

        // update order
        this.forEach(function(_slide){
            var slide_order = _slide.get('order'),
                    nslide_order = newSlide.get('order');

            if(slide_order >= nslide_order && _slide !== newSlide)
                _slide.set('order' , slide_order + 1);
        });

        newSlide.save();
        this.updateOrder();
    },

    updateSlidesSort : function(indexes) {
        this.beginPropertyChanges();

        this.forEach(function(slide) {
            slide.set('order',  indexes[slide.get('id')]);
        }, this);
        this.endPropertyChanges();
        this.set('_order', this.get('lastObject.order'));
    },

    updateOrder: function(){
        var i = 0;
        this.forEach(function(slide){
            if ( !slide.get('isOverlayLayers') ) {
                 slide.set('order', i++);
            }
        });

        this.set('_order', i - 1);
    },

    removeSlide : function(slide){
        slide.get('layers').forEach(function(layer){
            layer.get('styleModel').deleteRecord();
            layer.get('showEffect').deleteRecord();
            layer.get('hideEffect').deleteRecord();
            layer.deleteRecord();
        });
        slide.deleteRecord();

        if(this.get('length') !== 0){
            this.send('select' , this.get('firstObject'));
            this.updateOrder();
        }

    },

    updateLayersList : function(layers){
        /*if( !Ember.isEmpty(this.get('layersList'))) {
            this.get('layersList').save();
        }*/

        this.set('layersList' , layers);
    },


    openStyleEditor: function(forLayer){

        var stylesController = this.get('stylesController');
        stylesController.addLayer(forLayer);

        var editorView = MSPanel.StyleEditor.create({
            controller: stylesController,
        });

        this.get('mainView').createChildView(editorView);

        this.set('styleEditor', editorView);

        editorView.appendTo(MSPanel.rootElement);
    },

    closeStyleEditor: function(){
        var styleView = this.get('styleEditor');
        if( Ember.isEmpty(styleView) ) return
        styleView.destroy();
        this.set('styleView', null);
    },

    openEffectEditor: function(forLayer, type){

        var effectsController = this.get('effectsController');
        effectsController.addLayer(forLayer, type);

        var editorView = MSPanel.EffectEditor.create({
            controller: effectsController,
        });

        this.get('mainView').createChildView(editorView);

        this.set('effectEditor' , editorView);

        editorView.appendTo(MSPanel.rootElement);
    },

    closeEffectEditor: function(){
        var effectView = this.get('effectEditor');
        if( Ember.isEmpty(effectView) ) return
        effectView.destroy();
        this.set('effectView', null);
    },

    // Button Editor
    openButtonEditor: function(forLayer){

        var buttonsController = this.get('buttonsController');
        buttonsController.addLayer(forLayer);

        var editorView = MSPanel.ButtonEditor.create({
            controller: buttonsController,
        });

        this.get('mainView').createChildView(editorView);

        this.set('buttonView' , editorView);

        editorView.appendTo(MSPanel.rootElement);
    },

    closeButtonEditor: function(){
        var buttonView = this.get('buttonView');
        if( Ember.isEmpty(buttonView) ) return
        buttonView.destroy();
        this.set('buttonView', null);
    },
    /**
     * on image select in media uploader
     */
    onImageSelect: function () {
        var uploaderFrame = this.get('uploaderFrame'),
            selection = uploaderFrame.state().get('selection'),
            self = this;

        selection.map(function(attachment) {
            attachment = attachment.toJSON();
            self.generateSlide(attachment, attachment.url, (attachment.sizes.thumbnail || attachment.sizes.full).url)
        });

        // select last slide
        this.send('select', this.get('lastObject'));
    },

    generateSlide: function (attachment, bg, thumb) {
        var slide = MSPanel.Slide.create({
            order: this.get('_order') + 1,
            bg:bg,
            bgThumb:thumb,
            bgAlt:attachment.alt,
            bgTitle:attachment.title
        });

        this.set('_order' , this.get('_order') + 1);
        slide.save();
        this.updateOrder();
    },

    generateOverlayLayersSlide: function() {
        var slide;

        // is it already available?
        this.forEach(function(_slide) {
            if ( _slide.get('isOverlayLayers') ) {
                slide = _slide;
            }
        }, this);

        if ( !slide ) {
            slide = MSPanel.Slide.create({order: -1, isOverlayLayers: true, msId:'overlayLayers'});
        }

        this.set('overlayLayersSlide', slide);
        slide.save();
    },
    actions: {


        addSlides : function () {

            var uploaderFrame = this.get('uploaderFrame');
            // is not created? so create it!
            if ( Ember.isEmpty(uploaderFrame) ) {
                uploaderFrame = wp.media.frames.frame = wp.media({
                    title: __MSP_LAN.slc_001 || 'Select background image for new slide. (Multiple selection is available)', // the select button label in media uploader
                    multiple: true,    // use single image upload or multiple?
                    frame: 'select',
                    library: { type: 'image' },
                    button : { text : __MSP_LAN.slc_002 || 'Create Slide(s)' }
                });
                uploaderFrame.on('select', $.proxy(this.onImageSelect, this));

                this.set('uploaderFrame', uploaderFrame);
            }

            uploaderFrame.open();
        },

        newSlide : function(openMedia){
            var slide = MSPanel.Slide.create({order: this.get('_order') + 1});
            this.set('currentSlide' , slide);
            this.set('_order' , this.get('_order') + 1);
            this.updateLayersList(slide.get('layers'));
            slide.save();
            this.updateOrder();

            // open image select
            // if ( openMedia !== false ) {
            //  this.get('bgSelect').send('addImg');
            // }
        },

        select : function(slide){
            if(slide === this.get('currentSlide')) return;

            this.set('currentSlide' , slide);
            this.updateLayersList(slide.get('layers'));
        }
    }
});

/* ================== src/js/mspanel/controllers/LayersController.js =================== */
MSPanel.LayersController = Ember.ArrayController.extend({
 	slide : null,
	currentLayer: null,
	_order : -1,

	sortProperties: ['order'],
	sortAscending : false,
	buttonClasses : null,

	layertypes : [ {lable:__MSP_LAN.lc_001, value:'text'	},
				   {lable:__MSP_LAN.lc_002, value:'image'	},
				   {lable:__MSP_LAN.lc_006 || 'Button Layer', value:'button'},
				   {lable:__MSP_LAN.lc_003, value:'video'	},
				   {lable:__MSP_LAN.lc_004, value:'hotspot' } ],

	onSlideChanged : function(){
		this.set('slide' , this.get('parentController.currentSlide'));

		// Exit preview mode when slide changed
		if( this.get('isPreviewMode') )
			this.send('exitPreviewMode');

		this.send('clearSelect');
		this.updateOrder();

	}.observes('parentController.currentSlide').on('init'),

	onInit : function(){
		this.set('parentController.layersController', this);
		this.set('sliderSettings' , this.get('parentController.sliderSettings'));
		this.set('buttonClasses', MSPanel.ButtonStyle.find());
	}.on('init'),

	onModel: function(){
		this.updateOrder();
	}.observes('model'),

	updateLayersSort : function(indexes){
		this.beginPropertyChanges();
		this.forEach(function(layer) {
			 layer.set('order',	indexes[layer.get('id')]);
		}, this);
		this.endPropertyChanges();
	},

	updateOrder: function(){
		this.beginPropertyChanges();
		var i = this.get('length');
		this.forEach(function(layer){
			layer.set('order', --i);
		});
		this.endPropertyChanges();
		this.set('_order', this.get('length') - 1);
	},

	duplicateLayer : function(layer){
		this.beginPropertyChanges();

		var properties = layer.toJSON(),
			styleProp = layer.get('styleModel').toJSON(),
			showEffProp = layer.get('showEffect').toJSON(),
			hideEffProp = layer.get('hideEffect').toJSON();

		delete properties.id;
		delete styleProp.id;
		delete showEffProp.id;
		delete hideEffProp.id;

		// style
		var styleModel = MSPanel.Style.create(styleProp);
		styleModel.save();
		properties.styleModel = styleModel;

		// show transition effect
		var showEffect = MSPanel.Effect.create(showEffProp);
		showEffect.save();
		properties.showEffect = showEffect;

		// hide transition effect
		var hideEffect = MSPanel.Effect.create(hideEffProp);
		hideEffect.save();
		properties.hideEffect = hideEffect;

		properties.slide = this.get('slide');

		var newLayer = MSPanel.Layer.create(properties);
		newLayer.save();

		// update class name if it's a custom class
		if( newLayer.get('styleModel.type') === 'custom' ) {
			var newClassName = 'msp-cn-' + MSPanel.SliderID + '-' + newLayer.get('id');
			newLayer.set('styleModel.className', newClassName);
			newLayer.set('className', newClassName);
			styleModel.save();
			newLayer.save();
		}

		this.get('slide.layers').addObject(newLayer);

		// update position
		this.forEach(function(_layer){
			var layer_order = _layer.get('order'),
				nlayer_order = newLayer.get('order');

			if(layer_order >= nlayer_order && _layer !== newLayer)
				_layer.set('order' , layer_order + 1);
		});

		newLayer.save();

		this.endPropertyChanges();
	},

	removeLayer : function(layer){
		if(layer === this.get('currentLayer')){
			this.set('currentLayer' , null);
			this.set('layerSettings' , null);
		}

		layer.get('styleModel').deleteRecord();
		layer.get('showEffect').deleteRecord();
		layer.get('hideEffect').deleteRecord();

		this.get('model').removeObject(layer);

		layer.deleteRecord();
		this.updateOrder();
	},

	onLayerSelect : function(){
		if(this.get('currentLayer') === null){
			this.set('layerSettings' , 'empty-template');
		} else {
			this.set('layerSettings' , this.get('currentLayer.type') + '-layer-settings');
		}

        var layerType = this.get('currentLayer.type');
        this.set('maskOptions', layerType ==='image' || layerType === 'text' );
	}.observes('currentLayer'),

	onLayerPositionTypeChange : function () {
		// check layer position
		var pos = this.get('currentLayer.position');
		if ( pos === 'fixed' ) {
			this.set('fixedLayer', true);
			this.set('staticLayer', false);
		} else if ( pos === 'static' ) {
			this.set('fixedLayer', false);
			this.set('staticLayer', true);
		}else {
			this.set('fixedLayer', false);
			this.set('staticLayer', false);
		}

	}.observes('currentLayer.position'),

	willDestroyElement: function(){
		// remove tweentimeline and layers tween if it is at preview mode.
		if( this.get('isPreviewMode') )
			this.send('exitPreviewMode');
	},

	/* ------------------------ Manage Dynamic Tags --------------------------------*/
	/**
	 * Observe content of current layer, and replace dynamic tags if available
	 */
	onLayerContentChanged: function(){
		if( !MSPanel.dynamicTags || this.get('currentLayer.type') !== 'text' ){
			return;
		}
		this.set('currentLayer.dynamicContent', this._replcateDynamicTags(this.get('currentLayer.content')));
	}.observes('currentLayer', 'currentLayer.content'),

	_replcateDynamicTags: function(content){
		var i,l = MSPanel.dynamicTags.length, tagObj;

		return content.replace(/{{[\w-]+}}/g, function(match){
			for(i=0; i!==l; i++){
				tagObj = MSPanel.dynamicTags[i];
				if( match === tagObj.tag ) {
					return tagObj.generator(tagObj);
				}
			}
			return match;
		});
	},

	// calls by an dynamic content controller like Flickr controller
	updateDynamicContent: function(){
		var that = this;
		this.forEach(function(layer){
			if( layer.get('type') === 'text' ){
				layer.set('dynamicContent', that._replcateDynamicTags(layer.get('content')));
			}
		});
	},

	// controller actions
	actions : {

		newLayer : function(type){

			var newLayer = MSPanel.Layer.create({ slide:this.get('slide'), name:'layer', type:type, order:this.get('_order') + 1});

			this.set('_order' , this.get('_order') + 1);

			// setup default style
			var styleModel = MSPanel.Style.create();
			styleModel.save();
			newLayer.set('styleModel', styleModel);

			var showEffect = MSPanel.Effect.create();
			showEffect.save();
			newLayer.set('showEffect', showEffect);

			var hideEffect = MSPanel.Effect.create();
			hideEffect.save();
			newLayer.set('hideEffect', hideEffect);

			newLayer.save();

			this.get('model').addObject(newLayer);
			this.get('model').save();
		},

		selectLayer : function(layer){
			this.set('currentLayer' , layer);
		},

		clearSelect: function(){
			this.set('currentLayer' , null);
		},

		// Enter preview mode, it starts animation automatically
		enterPreviewMode : function(){

			// prevent start animation if there is no layer in slide.
			if(this.get('length') === 0) return;

			if( this.get('isPreviewMode') ){
				this.send('resume'); // resume animation if it's already in preview mode
				return;
			}

			var tweenTimeline = new JTweenTimeline(null,0,null,{repeatCount:0});

			this.forEach(function(layer){
				var stageLayer = layer.get('stageLayer');
				stageLayer.registerTween(tweenTimeline);
			});

			this.set('tweenTimeline', tweenTimeline);

			tweenTimeline.calculateDuration();
			tweenTimeline.position(0);
			this.set('isPreviewMode' , true);
			this.set('isPlaying' , true);
			var that = this;
			tweenTimeline.onChange = function(){
				that.set('timelinePos' , tweenTimeline.calculatedPosition);
			}

			this.set('currentLayer' , null);
		},

		// Exit from preview mode and get back to edit mode
		exitPreviewMode : function() {
			if( !this.get('isPreviewMode') ) return;
			var tweenTimeline = this.get('tweenTimeline');


			// remove all layer tweens from tweenTimeline
			this.forEach(function(layer){
				var stageLayer = layer.get('stageLayer');
				stageLayer.reset(tweenTimeline);
			});

			tweenTimeline.paused(true);
			this.set('tweenTimeline' , null);
			tweenTimeline = null;

			this.set('isPlaying' , false);
			this.set('isPreviewMode' , false);
		},

		// Resume slide animation
		resume: function(){
			if( this.get('isPlaying') ) return;

			this.get('tweenTimeline').paused(false);
			this.set('isPlaying' , true);
		},

		// Pause slide animation
		pause: function(){
			if( !this.get('isPlaying') ) return;

			this.get('tweenTimeline').paused(true);
			this.set('isPlaying' , false);
		},

		openStyleEditor: function(){
			this.get('parentController').openStyleEditor(this.get('currentLayer'));
		},

		openEffectEditor: function(type){
			this.get('parentController').openEffectEditor(this.get('currentLayer'), type);
		},

		openButtonEditor: function(){
			this.get('parentController').openButtonEditor(this.get('currentLayer'));
		}
	}
});

/* ================== src/js/mspanel/controllers/StylesController.js =================== */
/**
 * Master Slider Panel - Style Editor 
 *
 * This slider has many preset styles which helps user to style layers faster, also user can create
 * new preset style from this editor. After adding a style to layer if the style was a preset,
 * class name (className) of style adds as "styleClass" in selected layer model otherwise, editor takes 
 * "lstyleClass" from layer which is a unique value and specifies it as "styleClass".
 * 
 * "lstyleClass" value generates and stores in layer model at layer creation.
 * @package MSPanel
 * @parentController MSPanel.LayersController
 * @extends {Ember.ArrayController}
 */

MSPanel.StylesController = Ember.ArrayController.extend({
	layer: null, // selected layer in MSPanel
 
	// css string from current preset style
	presetCSS: null,
	
	/*
	draft style of layer, it first fills by current style of layer then edited by user or
	replaced by a preset style. 
	 */
	draftStyle: null,
	draftStyleText: null,
	draftClassName: null, 
	wordwrap: true,
	fontVariants: null,
	lineHeight: null,

	/**
	 * Update layer property
	 */
	addLayer: function(layer){
		this.set('layer', layer);

		// fill draftStyle
		var style_data = this.get('layer.styleModel').toJSON();
		delete style_data.id;

		this.set('draftStyle', MSPanel.Style.create(style_data));
		this.set('draftClassName' , this.get('layer.styleClass'));

		// observe all properties of draft style record
		for (var key in style_data){
			this.get('draftStyle').addObserver(key, this, 'onDraftChange');
		}

		this.onDraftChange();
	},

	loadFont: function(){
		var font = this.get('draftStyle.fontFamily');
		if( !Ember.isEmpty(font) ){
			GFonts.load(font, this.get('fontVariants'));
		}	
	}.observes('draftStyle.fontFamily'),

	/**
	 * Update draftStyle and draft class name from preset style
	 */
	updateDraft: function(presetStyle){
		this.beginPropertyChanges();
		var presetStyle = presetStyle.toJSON();
		delete presetStyle.id;
		for(var key in presetStyle){
			if( key !== 'type' ){
				this.set('draftStyle.' + key , presetStyle[key]);
			}
		}

		this.set('presetClassName', presetStyle.className);
		this.set('presetCSS', this.styleObjectToString(presetStyle));
		this.endPropertyChanges();
	},

	/**
	 * controls whitespace property
	 */
	onWordwrap: function(){
		if( !this.get('wordwrap') ) {
			this.set('draftStyle.whiteSpace', 'nowrap');
		} else {
			this.set('draftStyle.whiteSpace', null);
		}
	}.observes('wordwrap'),

	onBorderUpdate: function(){
		var allEmpty =  Ember.isEmpty(this.get('draftStyle.borderTop')) &&
						Ember.isEmpty(this.get('draftStyle.borderBottom')) &&
						Ember.isEmpty(this.get('draftStyle.borderLeft')) &&
						Ember.isEmpty(this.get('draftStyle.borderRight')) &&
						Ember.isEmpty(this.get('draftStyle.borderColor'));

		if( Ember.isEmpty(this.get('draftStyle.borderStyle')) && !allEmpty ){
			this.set('draftStyle.borderStyle', 'solid');
		} else if( allEmpty ){
			this.set('draftStyle.borderStyle', undefined);
		}

	}.observes('draftStyle.borderTop', 'draftStyle.borderBottom', 'draftStyle.borderLeft', 'draftStyle.borderRight', 'draftStyle.borderColor'),	

	lineHeightFix: function(){
		var lineHeight = this.get('lineHeight');
		if( Ember.isEmpty(lineHeight) ){
			this.set('draftStyle.lineHeight', 'normal');
		} else {
			this.set('draftStyle.lineHeight', lineHeight + 'px');
		}
	}.observes('lineHeight'),

	onDraftChange: function(){
		this.set('draftStyleText', this.styleObjectToString(this.get('draftStyle').toJSON()));
	},

	actions: {

		/**
		 * applies style to layer
		 *
		 * Style types:
		 * 		preset, preset style
		 * 		custom, only for layer
		 * 		copy,	one copy of a preset style for layer
		 */
		applyStyle: function(){

			this.beginPropertyChanges();

			var draftStyleObj = this.get('draftStyle').toJSON();
			delete draftStyleObj.id;
			delete draftStyleObj.type;
			delete draftStyleObj.name;

			for(var key in draftStyleObj){
				this.set('layer.styleModel.' + key, draftStyleObj[key]);
			}
			
			var cssStr = this.styleObjectToString(draftStyleObj),
				className;

			this.get('layer.stageLayer').updateLayerStyle(this.cssToObject(cssStr));

			if( cssStr !== this.get('presetCSS') ) {
				className = 'msp-cn-' + MSPanel.SliderID + '-' + this.get('layer.id');
				this.set('layer.styleModel.type','custom'); 
			} else {
				className = this.get('presetClassName');
				this.set('layer.styleModel.type','copy'); 
			}

			//this.set('layer.style', cssStr);
			this.set('layer.styleModel.className', className);
			this.set('layer.className', className);

			this.endPropertyChanges();
		
			this.get('parent').closeStyleEditor();
			this.send('cancel');
			this.set('draftStyleText', null);
		},

		/**
		 * saves current style in style editor as preset
		 */
		saveAsPreset: function(){
			var presetName = prompt(__MSP_LAN.sc_001 ,__MSP_LAN.sc_002 );
			
			if( presetName === '' ) {	
				presetName = __MSP_LAN.sc_002;
			} else if( presetName === null ){
				return;
			}

			this.beginPropertyChanges();
			var data = this.get('draftStyle').toJSON();
			data.type = 'preset';
			data.name = presetName;
			delete data.id;

			var newPreset = MSPanel.PresetStyle.create(data);
			newPreset.save();
			newPreset.set('className', 'msp-preset-' + newPreset.get('id'));
			newPreset.save();
			this.endPropertyChanges();
		},

		/**
		 * calls by closing editor dialog
		 */
		cancel: function(){
			// remove observes to draft style
			for ( var key in this.get('draftStyle').toJSON() ){
				this.get('draftStyle').removeObserver(key, this, 'onDraftChange')
			}
			// remove draft style record
			this.get('draftStyle').deleteRecord();
		},

		/**
		* Remove preset style
		* @param {style record} style
		*/
		removeStyle: function(style){
			style.deleteRecord();
		}

	},

	/**
	 * converts style model to css string
	 * @param  {object} styleModel
	 * @return {string}
	 */
	styleObjectToString: function(styleModel){
		var styleObj = styleModel,
			return_str = '',
			custom = '',
			styleValue;

		// remove none css styles from object
		styleObj.name = undefined;
		styleObj.className = undefined;
		styleObj.css = undefined;
		styleObj.type = undefined;
		styleObj.id = undefined;

		if( !Ember.isEmpty(styleObj.custom) ){
			custom = styleObj.custom.replace(/(\r\n|\n|\r)/gm, ''); // remove all new lines
		}

		styleObj.custom = undefined;

		for(var key in styleObj){
			styleValue = styleObj[key];
			if( styleValue !== undefined ){
				return_str += this.camelToDash(key) + ':' + styleObj[key] + (typeof styleObj[key] === 'number' ? 'px' : '') + ';';
			}
		}

		return return_str + custom;
	},

	/**
 	* Converts css string to an object
 	* @param  {string} css 
 	* @return {object} 
 	*/
 	cssToObject: function(css){
 		var output = {},
 			styles = css.split(';');

 		css = css.replace(/(\r\n|\n|\r)/gm, ''); // remove all new lines

		for(var i=0, l=styles.length; i!==l; i++) {

			var entry = styles[i].split(':');
			if( entry[1] != null ) {
				output[entry[0]] = entry[1];
			}

		}
		return output;
 	},

 	camelToDash: function(str) {
	  return str.replace(/\W+/g, '-')
				.replace(/([a-z\d])([A-Z])/g, '$1-$2').toLowerCase();
   }

});

/* ================== src/js/mspanel/controllers/EffectsController.js =================== */
/**
 * Master Slider Panel - Effect Editor
 *
 * Effects are used in slider to animate layers over slides.
 * User will find many preset effects in Effect Editor which helps to add aniamte effect to layers faster
 * Also this editor makes it possible to create a custom effect and apply it to selected user or save it as andother
 * preset effect.
 *
 * Each effect object will transform to string by editor and will be added in slider markup
 * Output string:
 * 		t(fade,tx,ty,tz,r,rx,ry,rz,scx,scy,skx,sky,ox,oy,oz)
 *
 * @package MSPanel
 * @author Averta
 * @version 1.0b
 */

MSPanel.EffectsController = Ember.ArrayController.extend({
	type: 'show', // or hide
	layer: null, // selected layer
	ease: null, // draft ease
	duration: 5, // draft duration
	origin: null,
	preview: null, // effect preview view 

	/*
	It fills by selected layer then user edits it and efter edit user can save it over layer or save it as preset.
	 */
	draftEffect: null,
	draftTextEffect: null,

	/**
	 * Adds selected layer to editor
	 * @param {DS Record} layer 
	 * @param {string} type  show or hide
	 */
	addLayer: function(layer, type){
		this.set('layer', layer);
		this.set('type', type);

		var eff_data = this.get('layer.' + type + 'Effect').toJSON();
		delete eff_data.id;

		var draftEffect = MSPanel.Effect.create(eff_data);

		//fill DraftEffect
		this.set('draftEffect', draftEffect);
		this.set('ease', this.get('layer.' + type + 'Ease'));
		// add observer to all properties of draftEffect
		for (var key in eff_data){
			this.get('draftEffect').addObserver(key, this, 'onDraftChange');
		}

		this.set('duration', layer.get(type + 'Duration'));
		this.set('ease', layer.get(type + 'Ease'));

		this.onDraftChange();
	},

	/**
	 * Update drafteffect and draft class name from preset effect
	 */
	updateDraft: function(effect){
		this.beginPropertyChanges();
		var currentEffect = effect.toJSON();
		delete currentEffect.id;
		for(var key in currentEffect){
			if( key !== 'type' ){
				this.set('draftEffect.' + key , currentEffect[key]);
			}
		}
		this.endPropertyChanges();
	},


	onDraftChange: function(){
		this.set('draftEffectText', this.createTransform(this.get('draftEffect').toJSON()));
		this.set('origin', {
			x: this.get('draftEffect.originX'),
			y: this.get('draftEffect.originY'),
			z: this.get('draftEffect.originZ')
		});
	}, 

	onEaseUpdate: function(){
		this.set('layer.' + this.get('type') + 'Ease', this.get('ease'));
	}.observes('ease'),

	actions: {

		/**
		 * Applies effect to selected layer
		 */
		applyEffect: function(){
			this.beginPropertyChanges();
			var type = this.get('type'),
				draftEffectObj = this.get('draftEffect').toJSON();

			delete draftEffectObj.id;
			delete draftEffectObj.type;
			delete draftEffectObj.name;

			for(var key in draftEffectObj){
				this.set('layer.' + type + 'Effect.' + key, draftEffectObj[key]);
			}

			var origin = this.get('origin');

			this.set('layer.' + type + 'EffFunc', this.createEffectFunc(draftEffectObj));
			this.set('layer.' + type + 'Ease', this.get('ease'));
			this.set('layer.' + type + 'Fade', this.get('draftEffect.fade'));
			this.set('layer.' + type + 'Duration', this.get('duration'));
			this.set('layer.' + type + 'Transform', 'perspective(2000px) ' + this.get('draftEffectText'));
			this.set('layer.' + type + 'Origin', (origin.x !== undefined? origin.x : '50') + '% ' +
												 (origin.y !== undefined? origin.y : '50') + '% ' +
												 (origin.z !== undefined? origin.z : '0') + 'px') ;

			this.endPropertyChanges();

			this.send('cancel');
			this.get('parent').closeEffectEditor();		
		},

		saveAsPreset: function(){
			var presetName = prompt(__MSP_LAN.ec_001 , __MSP_LAN.ec_002);
			
			if( presetName === '' ) {	
				presetName = __MSP_LAN.ec_002;
			} else if( presetName === null ){
				return;
			}

			this.beginPropertyChanges();
			var data = this.get('draftEffect').toJSON();
			data.type = 'preset';
			data.name = presetName;
			//data.msEffect = this.createEffectFunc(data);
			delete data.id;

			var newPreset = MSPanel.PresetEffect.create(data);
			newPreset.save();
			this.endPropertyChanges();
		},

		cancel: function(){
			// stop preview if is already playing
			var preview = this.get('preview');
			if( !Ember.isEmpty(preview) ){
				preview.send('exitPlaying');
			}

			// remove draft style record
			this.get('draftEffect').deleteRecord();
		},

		/**
		* Remove preset effect
		* @param {effect record} effect
		*/
		removeEffect: function(effect){
			effect.deleteRecord();
			//effect.save();
		}

	},

	/**
	 * Output string:
 	 * 		t(fade,tx,ty,tz,r,rx,ry,rz,scx,scy,skx,sky,ox,oy,oz)
	 * @param  {Object} effect
	 * @return {String} 
	 */
	createEffectFunc: function(effect){
		return 't(' +
				(effect.fade ? 'true': 'false') + ',' +
				(effect.translateX != undefined ? effect.translateX : 'n') + ',' +
				(effect.translateY != undefined ? effect.translateY : 'n') + ',' +
				(effect.translateZ != undefined ? effect.translateZ : 'n') + ',' +
				(effect.rotate != undefined ? effect.rotate : 'n') + ',' +
				(effect.rotateX != undefined ? effect.rotateX : 'n') + ',' +
				(effect.rotateY != undefined ? effect.rotateY : 'n') + ',' +
				(effect.rotateZ != undefined ? effect.rotateZ : 'n') + ',' +
				(effect.scaleX != undefined ? effect.scaleX : 'n') + ',' +
				(effect.scaleY != undefined ? effect.scaleY : 'n') + ',' +
				(effect.skewX != undefined ? effect.skewX : 'n') + ',' +
				(effect.skewY != undefined ? effect.skewY : 'n') + ',' +
				(effect.originX != undefined ? effect.originX : 'n') + ',' +
				(effect.originY != undefined ? effect.originY : 'n') + ',' +
				(effect.originZ != undefined ? effect.originZ : 'n') + ')';
	},

	createTransform: function(effect){
		return  (effect.translateX != undefined ? 'translateX(' + effect.translateX + 'px) ' : '') +	
				(effect.translateY != undefined ? 'translateY(' + effect.translateY + 'px) ' : '') +	
				(effect.translateZ != undefined ? 'translateZ(' + effect.translateZ + 'px)' : '') +
				(effect.rotate != undefined ? 'rotate(' + effect.rotate + 'deg) ' : '') +
				(effect.rotateX != undefined ? 'rotateX(' + effect.rotateX + 'deg) ' : '') +	
				(effect.rotateY != undefined ? 'rotateY(' + effect.rotateY + 'deg) ' : '') +	
				(effect.rotateZ != undefined ? 'rotateZ(' + effect.rotateZ + 'deg) ' : '') +
				(effect.skewX != undefined ? 'skewX(' + effect.skewX + 'deg) ' : '') +	
				(effect.skewY != undefined ? 'skewY(' + effect.skewY + 'deg) ' : '') +			
				(effect.scaleX != undefined ? 'scaleX(' + effect.scaleX + ') ' : '') +	
				(effect.scaleY != undefined ? 'scaleY(' + effect.scaleY + ') ' : '');
	}

});

/* ================== src/js/mspanel/controllers/ButtonsController.js =================== */
/**
 * Master Slider Panel Buttons Controller
 * Used in button editor modal
 * @author averta
 * @since v1.9.0
 */

MSPanel.ButtonsController = Ember.ArrayController.extend({
	needs: 'application',
	layer: null,

	draftNormal: '',
	draftHover: '',
	draftActive: '',
	currentButton: null,

	/**
	 * Update layer property
	 */
	addLayer: function(layer){
		this.set('layer', layer);
		this.onCurrentButtonChange();
	},

	onCurrentButtonChange: function(){
		if ( this.get('length') === 0 ) { 
			return;
		}

		var currentButton = this.get('currentButton');
		if ( Ember.isEmpty(currentButton) ){
			currentButton = this.get('firstObject');
			this.set('currentButton', currentButton);
		} 
		
		this.set('draftNormal', currentButton.get('normal'));
		this.set('draftHover' , currentButton.get('hover'));
		this.set('draftActive', currentButton.get('active'));
		this.set('draftBtnStyle', currentButton.get('style'));
		this.set('draftBtnSize' , currentButton.get('size'));

	}.observes('currentButton').on('init'),

	actions: {

		applyStyle: function(){

			if( this.get('length') !== 0 && !confirm(__MSP_LAN.be_006 || 'By updating a button it will be changed in all of your sliders. Are you sure you want to update this button?') ) {
				return;
			} else if ( this.get('length') === 0 ){
				this.send('saveAsPreset');
				return;
			}

			this.beginPropertyChanges();
			this.set('currentButton.normal', this.get('draftNormal'));
			this.set('currentButton.hover' , this.get('draftHover'));
			this.set('currentButton.active', this.get('draftActive'));
			this.set('currentButton.style', this.get('draftBtnStyle'));
			this.set('currentButton.size', this.get('draftBtnSize'));
			this.get('currentButton').save();
			this.endPropertyChanges();
			this.get('controllers.application').generateButtonStyles();
			this.get('parent').closeButtonEditor();
			this.send('cancel');
		},

		/**
		 * saves current style in style editor as preset button
		 */
		saveAsPreset: function(){
			this.beginPropertyChanges();
			var newPreset = MSPanel.ButtonStyle.create({
				normal : this.get('draftNormal'),
				hover  : this.get('draftHover'),
				active : this.get('draftActive'),
				size   : this.get('draftBtnSize'),
				style  : this.get('draftBtnStyle')
			});
			newPreset.save();
			newPreset.set('className', 'msp-preset-btn-' + newPreset.get('id'));
			newPreset.save();
			this.endPropertyChanges();
			this.get('controllers.application').generateButtonStyles();
			this.get('parent').closeButtonEditor();
			this.send('cancel');
		},

		/**
		* Remove preset button
		* @param {button record} button
		*/
		removeButton: function(button){
			button.deleteRecord();
			this.get('controllers.application').generateButtonStyles();
		},

		/**
		 * calls by closing editor dialog
		 */
		cancel: function(){
			this.set('draftNormal', '');
			this.set('draftHover' , '');
			this.set('draftActive', '');
			this.set('draftBtnSize', null);
			this.set('draftBtnStyle', null);
			this.set('currentButton', null);
		}
	}
});

/* ================== src/js/mspanel/controllers/ControlsController.js =================== */
/**
 * Master Slider Panel, Slider Controls controller
 * @package MSPanel
 * @author Averta
 * @version 1.0b
 */
MSPanel.ControlsController = Ember.ArrayController.extend({

	needs: 'application',

	controls: [
		{used:false, label:__MSP_LAN.cc_001, value:'arrows'},
		{used:false, label:__MSP_LAN.cc_002, value:'timebar'},
		{used:false, label:__MSP_LAN.cc_003, value:'bullets'},
		{used:false, label:__MSP_LAN.cc_004, value:'circletimer'},
		{used:false, label:__MSP_LAN.cc_005, value:'scrollbar'},
		{used:false, label:__MSP_LAN.cc_006, value:'slideinfo'},
		{used:false, label:__MSP_LAN.cc_007, value:'thumblist'}
	],

	selectedControl: null, // selected control in combo box

	availableControls: [], // already added to slider

	noMore: false,

	currentControl: null, // current active control

	setup: function(){
		var that = this;
		this.forEach(function(control){
			that.findControlObj(control.get('name')).used = true;
		});
		this.set('availableControls', this.findAvailableControls());
	},


	// it's useful for tabs
	onInsertThumb: function(){
		var ctr = this.get('currentControl');
		if ( Ember.isEmpty(ctr) ) {
			return;
		}

		if ( this.get('currentControl.type') === 'tabs' ) {
			this.set('isTab', true);
		} else {
			this.set('isTab', false);
		}
	}.observes('currentControl.type').on('didInsertElement'),

	actions: {

		addControl: function(){

			var controlName = this.get('selectedControl'),
				controlObj = this.findControlObj(controlName),
				control;

			// create control object
			control = MSPanel.Control.create(this.getDefaultValues(controlName));
			control.set('label', controlObj.label);

			controlObj.used = true;
			this.set('availableControls', this.findAvailableControls());
			control.save();

			this.set('currentControl', control);
		},

		removeControl: function(control){
			this.findControlObj(control.get('name')).used = false;
			this.set('availableControls', this.findAvailableControls());
			control.deleteRecord();

			this.set('currentControl', this.get('firstObject'));
			this.send('showControlOptions');
		},

		showControlOptions: function(){
			var currentControl = this.get('currentControl');

			if( Ember.isEmpty(currentControl) ){
				this.set('controlOptions', 'empty-template');
			} else {
				this.set('controlOptions', currentControl.get('name') + '-options');
			}
		}
	},

	/**
	 * Find selected control from controls
	 * @param  {string} control 
	 * @return {object} 
	 */
	findControlObj: function(control){
		var controls = this.get('controls');
		for(var i=0,l=controls.length; i!==l; i++){
			if( controls[i].value === control ){
				return controls[i];	
			} 
		}

		return null;
	},

	findAvailableControls: function(){
		var avc = [],
			controls = this.get('controls');
		for(var i=0,l=controls.length; i!==l; i++){
			if( !controls[i].used ){
				avc.push(controls[i]);
			}
		}
		
		this.set('noMore', avc.length === 0);
		this.set('selectedControl', avc[0]?avc[0].value:null);

		return avc;
	},

	/**
	 * creates an object of default values for new control
	 * @param  {Control} control 
	 * @return {Object}         
	 */
	getDefaultValues: function(control){
		var values = {name:control};

		values.inset = !(control === 'slideinfo' || control === 'thumblist');

		switch(control){
			case 'timebar':
				values.align = 'bottom';
				values.color = '#FFFFFF';
				values.autoHide = false;
				values.width = 4;
				break;
			case 'bullets':
				values.align = 'bottom';
				values.dir = 'h';
				values.margin = 10;
				values.space = 6;
				break;
			case 'circletimer':
				//values.align = 'tl';
				values.color = '#A2A2A2';
				values.stroke = 10;
				values.radius = 4;
				values.autoHide = false;
				break;
			case 'scrollbar':
				values.align = 'top';
				values.dir = 'h';
				values.color = '#3D3D3D';
				values.margin = 10;
				values.autoHide = false;
				values.width = 4;
				break;
			case 'slideinfo':
				values.align = 'bottom';
				values.margin = 10;
				values.autoHide = false;
				break;
			case 'thumblist':
				values.align = 'bottom';
				values.space = 5;
				values.width = 100;
				values.height = 80;
				values.margin = 10;
				values.fillMode = 'fill';
				values.autoHide = false;
				break;
		}

		return values;
	}

});

/* ================== src/js/mspanel/controllers/CallbacksController.js =================== */
/**
 * Master Slider Panel Callbacks controller
 * @package MSPanel
 * @version 1.0
 * @author Averta
 */

MSPanel.CallbacksController = Ember.ArrayController.extend({

	callbacks: [
		{used: false, label:__MSP_LAN.cb_011, value:'INIT'},
		{used: false, label:__MSP_LAN.cb_001, value:'CHANGE_START'},
		{used: false, label:__MSP_LAN.cb_002, value:'CHANGE_END'},
		{used: false, label:__MSP_LAN.cb_003, value:'WAITING'},
		{used: false, label:__MSP_LAN.cb_004, value:'RESIZE'},
		{used: false, label:__MSP_LAN.cb_005, value:'VIDEO_PLAY'},
		{used: false, label:__MSP_LAN.cb_006, value:'VIDEO_CLOSE'},
		{used: false, label:__MSP_LAN.cb_007, value:'SWIPE_START'},
		{used: false, label:__MSP_LAN.cb_008, value:'SWIPE_MOVE'},
		{used: false, label:__MSP_LAN.cb_009, value:'SWIPE_END'}
	],

	availableCallbacks: [],
	noMore: false,
	selectedCallback: null, // selected callback in combo box

	setup: function(){
		var that = this;
		this.forEach(function(callback){
			that.findCallbackObj(callback.get('name')).used = true;
		});
		this.set('availableCallbacks', this.findAvailableCallbacks());
	},

	actions: {
		addCallback: function(){
			var callbackName = this.get('selectedCallback'),
				callbackObj = this.findCallbackObj(callbackName),
				callback;

			// create callback object
			callback = MSPanel.Callback.create({
				name:callbackObj.value,
				label:callbackObj.label
			});
			
			callbackObj.used = true;
			this.set('availableCallbacks', this.findAvailableCallbacks());
			callback.save();
		},

		removeCallback: function(callback){
			if( confirm(__MSP_LAN.cb_010.jfmt(callback.get('label'))) ){
				this.findCallbackObj(callback.get('name')).used = false;
				this.set('availableCallbacks', this.findAvailableCallbacks());
				callback.deleteRecord();	
			}
		}

	},

	/**
	 * Find selected callback from callbacks
	 * @param  {string} callback 
	 * @return {object} 
	 */
	findCallbackObj: function(callback){
		var callbacks = this.get('callbacks');
		for(var i=0,l=callbacks.length; i!==l; i++){
			if( callbacks[i].value === callback ){
				return callbacks[i];	
			} 
		}
		return null;
	},

	findAvailableCallbacks: function(){
		var avc = [],
			callbacks = this.get('callbacks');
		for(var i=0,l=callbacks.length; i!==l; i++){
			if( !callbacks[i].used ){
				avc.push(callbacks[i]);
			}
		}
		
		this.set('noMore', avc.length === 0);
		this.set('selectedCallback', avc[0]?avc[0].value:null);
		return avc;
	},

});

/* ================== src/js/mspanel/controllers/FlickrController.js =================== */
/**
 * Master Slider WordPress Panel Flickr Controller
 * @author Averta Ltd.
 * @extends {Ember.ObjectController}
 * @package MSPanel
 */
;(function($){

	/**
	 * Generate Flickr photoset url
	 * @param  {String} key   api key
	 * @param  {String} id    photoset id
	 * @param  {Number} count number of images
	 * @return {String}
	 */
	var getPhotosetURL = function(key , id , count){
		return 'https://api.flickr.com/services/rest/?method=flickr.photosets.getPhotos&api_key=' + key + '&photoset_id='+ id +'&per_page='+ count +'&extras=url_o,description,date_taken,owner_name,views&format=json&jsoncallback=?';
	};

	/**
	 * Generate Flickr user public images url
	 * @param  {String} key   api key
	 * @param  {String} id    user id
	 * @param  {Number} count number of images
	 * @return {String}
	 */
	var getUserPublicURL = function(key , id , count){
		return 'https://api.flickr.com/services/rest/?&method=flickr.people.getPublicPhotos&api_key=' + key + '&user_id='+ id +'&per_page='+ count +'&extras=url_o,description,date_taken,owner_name,views&format=json&jsoncallback=?';
	};

	/**
	 * Generates image path
	 * @param  {String} fid    
	 * @param  {String} server 
	 * @param  {String} id     
	 * @param  {String} secret 
	 * @param  {String} size   
	 * @return {String}        
	 */
	var getImageSource = function(fid , server , id , secret , size, data){
		if ( size === 'o' && data ) {
			return data.url_o;
		}
		
		if( size === '-' ){
			size = '';
		} else {
			size = '_' + size;
		}

		return 'https://farm' + fid + '.staticflickr.com/'+ server + '/' + id + '_' + secret + size + '.jpg';
	};

	var registered = false; // dynamic tags register flag
	MSPanel.flickrData = {}; // current data of Flickr
	
	MSPanel.FlickrController = Ember.ObjectController.extend({
		needs:['slides'],

		isPhotoset: null,
		firstSlide: null, // the slide template
		photoData: null,
		needToReload: false,
		ajaxRequest: null,

		setup: function(){
			registerDynamicTags();

			if( Ember.isEmpty(this.get('imgCount')) ){
				this.set('imgCount', 10);
			}

			if( Ember.isEmpty(this.get('setType')) ){
				this.set('setType', 'photos');
			}

			if( Ember.isEmpty(this.get('imgSize')) ){
				this.set('imgSize', 'c');
			}

			if( Ember.isEmpty(this.get('thumbSize')) ){
				this.set('thumbSize', 'q');
			}

			this.set('firstSlide', MSPanel.Slide.find(1));
			this.loadData();	
		},

		/**
		 * Loads data from Flickr Api
		 */
		loadData: function(){
			this.set('needToReload', false);
			this.set('controllers.slides.layersController.noticeMsg', 'Loading data from Flickr...');

			// abort last ajax request
			var ajaxRequest = this.get('ajaxRequest');
			if( !Ember.isEmpty(ajaxRequest) ){
				ajaxRequest.abort();
			}  

			/*if( Ember.isEmpty(this.get('apiKey')) ) {
				return;
			}  */

			var that = this,
				request;
			if(this.get('setType') === 'photoset'){
				request = $.getJSON(getPhotosetURL(this.get('apiKey') , this.get('setId') , 1) , function(data){
					that._photoData(data);
				});
			}else{
				request = $.getJSON(getUserPublicURL(this.get('apiKey') , this.get('setId') , 1) , function(data){
					that._photoData(data);
				});
			}

			this.set('ajaxRequest', request);

		},

		checkForReload: function(){
			this.set('needToReload', true);
		}.observes('setType', 'apiKey', 'id'),

		onActivate: function(){},
		onDeactivate: function(){
			if( this.get('needToReload') ){
				this.loadData();
			}
		},

		/**
		 * Updates slide data
		 * @param  {Object} data 
		 */
		_photoData: function(data){
			if(data.stat === 'fail'){
				this.set('firstSlide.bg', undefined);
				MSPanel.flickrData = {};
				this.get('controllers.slides.layersController').updateDynamicContent();
				this.set('controllers.slides.layersController.noticeMsg', '<span style="color:#FF4700;">Flickr API error #' + data.code + ': ' + data.message + '. Please check Flicker settings.</span>');
				return;
			}
			data = data[this.get('setType')].photo[0];
			MSPanel.flickrData = data;
			this.set('photoData', data);
			this._updateBGImage();

			this.set('controllers.slides.layersController.noticeMsg', undefined);
			this.get('controllers.slides.layersController').updateDynamicContent();
		},

		_updateBGImage: function(){
			var data = this.get('photoData');
			if( Ember.isEmpty(data) ) {
				return;
			}
			this.set('firstSlide.bg', getImageSource(data.farm, data.server, data.id, data.secret, this.get('imgSize'), data));
		}.observes('imgSize'),

		onTypeChange: function(){
			this.set('isPhotoset', this.get('setType') === 'photoset');
		}.observes('setType').on('init'),

	});

	function registerDynamicTags(){
		if( registered ) {
			return;
		}

		registered = true;

		// Register dynamic tags
		if(MSPanel.dynamicTags){
			MSPanel.dynamicTags.push({
				name 		: __MSP_LAN.flk_001 || 'Photo title',
				tag 		: '{{title}}',
				generator	: function(){ return MSPanel.flickrData.title || '{{title}}'; }
			},
			
			{
				name 		: __MSP_LAN.flk_002 || 'Photo owner name',
				tag 		: '{{owner-name}}',
				generator	: function(){ return MSPanel.flickrData.ownername || '{{owner-name}}'; }
			},

			{
				name 		: __MSP_LAN.flk_003 || 'Date taken',
				tag 		: '{{date-taken}}',
				generator	: function(){ return MSPanel.flickrData.datetaken || '{{date-taken}}'; }
			},

			{
				name 		: __MSP_LAN.flk_004 || 'Photo description',
				tag 		: '{{description}}',
				generator	: function(){ return MSPanel.flickrData.description._content || '{{description}}'; }
			});		
		}		
	}

})(jQuery);

/* ================== src/js/mspanel/controllers/FacebookController.js =================== */
/**
 * Master Slider WordPress Panel Facebook Controller
 * @author Averta Ltd.
 * @extends {Ember.ObjectController}
 * @package MSPanel
 */
;(function($){

	/**
	 * Generate Photostream of user url
	 * @param  {String} user  username
	 * @param  {Number} count number of images
	 * @return {String}
	 */
	var getPhotostreamURL = function(user, count, token){
		return 'https://graph.facebook.com/' + user + '/photos/uploaded/?fields=source,name,link,images,from&limit=' + count + '&access_token=' + token;
	};

	/**
	 * Generate Album images url
	 * @param  {String} albumId
	 * @param  {Number} count number of images
	 * @return {String}
	 */
	var getAlbumURL = function(albumId , count, token){
		return 'https://graph.facebook.com/' + albumId + '/photos?fields=source,name,link,images,from&limit=' + count + '&access_token=' + token;
	};

	/**
	 * Generates image path
	 * @param  {Array} images
	 * @param  {String} size
	 * @return {String}
	 */
	var getImageSource = function(images, size){

		if( size === 'orginal' ) {
			return images[0].source;
		}

		for(var i = 0, l = images.length; i !== l; i++){
			if( images[i].source.indexOf(size + 'x' + size) !== -1 )
				return images[i].source;
		}

		return images[l-3].source;
	};

	var registered = false; // dynamic tags register flag
	MSPanel.facebookData = {}; // current data of Facebook

	MSPanel.FacebookController = Ember.ObjectController.extend({
		needs:['slides'],

		isPhotostream: null,
		firstSlide: null, // the slide template
		photoData: null,
		needToReload: false,
		ajaxRequest: null,

		setup: function(){

			registerDynamicTags();

			if( Ember.isEmpty(this.get('imgCount')) ){
				this.set('imgCount', 10);
			}

			if( Ember.isEmpty(this.get('setType')) ){
				this.set('setType', 'album');
			}

			if( Ember.isEmpty(this.get('imgSize')) ){
				this.set('imgSize', 'orginal');
			}

			if( Ember.isEmpty(this.get('thumbSize')) ){
				this.set('thumbSize', '320');
			}

			this.set('firstSlide', MSPanel.Slide.find(1));
			this.loadData();
		},

		/**
		 * Loads data from Flickr Api
		 */
		loadData: function(){
			this.set('needToReload', false);
			this.set('controllers.slides.layersController.noticeMsg', 'Loading data from Facebook...');

			// abort last ajax request
			var ajaxRequest = this.get('ajaxRequest');
			if( !Ember.isEmpty(ajaxRequest) ){
				ajaxRequest.abort();
			}

			/*if( Ember.isEmpty(this.get('apiKey')) ) {
				return;
			}  */

			var that = this,
				request;
			if(this.get('setType') === 'album'){
				request = $.getJSON(getAlbumURL(this.get('setId') , 1, this.get('fbtoken')) , function(data){
					that._photoData(data);
				});
			}else{
				request = $.getJSON(getPhotostreamURL(this.get('setId') , 1 , this.get('fbtoken')) , function(data){
					that._photoData(data);
				});
			}

			this.set('ajaxRequest', request);

		},

		checkForReload: function(){
			this.set('needToReload', true);
		}.observes('setType', 'apiKey', 'id'),

		onActivate: function(){},
		onDeactivate: function(){
			if( this.get('needToReload') ){
				this.loadData();
			}
		},

		/**
		 * Updates slide data
		 * @param  {Object} data
		 */
		_photoData: function(content){
			//console.log(content)
			if(content.error){
				this.set('firstSlide.bg', undefined);
				MSPanel.facebookData = {};
				this.get('controllers.slides.layersController').updateDynamicContent();
				this.set('controllers.slides.layersController.noticeMsg', '<span style="color:#FF4700;">Facebook API ERROR#' + content.error.code + '(' + content.error.type + ')' + ': ' + content.error.message + '. Please check Facebook settings.</span>');
				return;
			}
			content = content.data[0];
			MSPanel.facebookData = content;
			this.set('photoData', content);
			this._updateBGImage();

			this.set('controllers.slides.layersController.noticeMsg', undefined);
			this.get('controllers.slides.layersController').updateDynamicContent();
		},

		_updateBGImage: function(){
			var data = this.get('photoData');
			if( Ember.isEmpty(data) ) {
				return;
			}
			this.set('firstSlide.bg', getImageSource(data.images, this.get('imgSize')));
		}.observes('imgSize'),

		onTypeChange: function(){
			this.set('isPhotostream', this.get('setType') === 'photostream');
		}.observes('setType').on('init'),

	});

	function registerDynamicTags(){
		if( registered ) {
			return;
		}

		registered = true;

		// Register dynamic tags
		if(MSPanel.dynamicTags){

			MSPanel.dynamicTags.push({
				name 		: __MSP_LAN.fb_001 || 'Photo name',
				tag 		: '{{name}}',
				generator	: function(){ return MSPanel.facebookData.name || '{{name}}'; }
			},

			{
				name 		: __MSP_LAN.fb_002 || 'Photo owner name',
				tag 		: '{{owner-name}}',
				generator	: function(){ return MSPanel.facebookData.from.name || '{{owner-name}}'; }
			},

			{
				name 		: __MSP_LAN.fb_003 || 'Phoot link',
				tag 		: '{{link}}',
				generator	: function(){ return MSPanel.facebookData.link || '{{link}}'; }
			});
		}
	}

})(jQuery);

/* ================== src/js/mspanel/controllers/PostController.js =================== */
/**
 * Master Slider Wordpress Panel Post Controller
 * @author Aveta Ltd
 * @extends {Ember.ObjectController}
 * @package MSPanel
 *
 *
 *      Ajax Request:
 *          (string) post_type
 *          (array)  terms      [ "product_tag": "79", "product_tag": "71" ]  (note : 79 is term_id)
 *          (string) orderby    ( date | menu_order | title | ID | rand | comment_count | modified | author )
 *          (string) order     ( DESC | ASC )
 *          (int)    limit      // number of post to show
 *          (int)    offset     // number of post to displace or pass over
 *          (string) slideImage ( auto, featured, first, none )
 *          (int|string) post__not_in    ( 112,82,39 ) (exclude post ids from result)
 *
 *
 */
;(function($){

    MSPanel.wpPostData = {}; // current data of posts

    MSPanel.PostController = Ember.ObjectController.extend({
        needs:['slides'],

        wpData:null, // added object in page by backend

        firstSlide: null, // the slide template
        needToReload: false,
        ajaxRequest: null,

        setup: function(){

            if( Ember.isEmpty(this.get('postType')) ){
                this.set('postType', 'post');
            }

            this.registerDynamicTags();

            if( Ember.isEmpty(this.get('postCount')) ){
                this.set('postCount', 10);
            }

            if( Ember.isEmpty(this.get('postOrder')) ){
                this.set('postOrder', 'date');
            }

            if( Ember.isEmpty(this.get('postOrderDir')) ){
                this.set('postOrderDir', 'DESC');
            }

            if( Ember.isEmpty(this.get('postImageType')) ){
                this.set('postImageType', 'auto');
            }

            // init wp data
            var wpData = __MSP_POST.types_taxs_terms;
            this.set('wpData', wpData);
            this.set('firstSlide', MSPanel.Slide.find(1));
            this.loadData();

            this.onPostTypeChanged(true);
        },

        /**
         * Loads data from Wordpress
         */
        loadData: function(){
            this.set('needToReload', false);

            // abort last ajax request
            var ajaxRequest = this.get('ajaxRequest');
            if( !Ember.isEmpty(ajaxRequest) ){
                ajaxRequest.abort();
            }

            var that = this,
                request,
                data = {},
                terms = [];

            // create ajax data
            data.post_type                  = this.get('postType');
            data.orderby                    = this.get('postOrder');
            data.order                      = this.get('postOrderDir');
            data.limit                      = this.get('postCount');
            data.post__not_in               = this.get('postExcludeIds');
            data.post__in                   = this.get('postIncludeIds');
            data.exclude_post_no_image      = this.get('postExcludeNoImg');
            data.slideImage                 = this.get('postImageType');
            data.offset                     = this.get('postOffset');
            data.excerpt_length             = this.get('postExcerptLen');
            data.action                     = 'post_slider_preview';
            data.nonce                      = jQuery('#msp-main-wrapper').data('nonce');

            if( !Ember.isEmpty(this.get('postCats')) ){
                var cats = this.get('postCats');

                if( cats[0] === '' ){
                    cats.splice(0,1);
                }

                if( cats.length !== 0 ){
                    data.cats = cats.join(',');
                }
            }

            if( !Ember.isEmpty(this.get('postTags')) ){
                var tags = this.get('postTags');
                if( tags[0] === '' ){
                    tags.splice(0,1);
                }

                if( tags.length !== 0 ){
                    data.tags = tags.join(',');
                }
            }

            this.set('previewResults', '<div class="msp-posts-loading">Loading data...</div>');
            this.set('controllers.slides.layersController.noticeMsg', 'Loading posts data...');
            var request = $.post(__MS.ajax_url, data, function(serverData) {
                if( serverData.success ){
                    that.updatePreview(serverData);
                    that.updateWpPostData(serverData);
                } else {
                    that.set('previewResults', 'Error: ' + serverData.message );
                    that.set('controllers.slides.layersController.noticeMsg',  'Error: ' + serverData.message);
                }
            }, "json");

            this.set('ajaxRequest', request);

        },

        onPostTypeChanged: function(keepTaxo){
            var wpData = this.get('wpData'),
                postType = this.get('postType');

            if( Ember.isEmpty(wpData) ){
                return;
            }

            var cats = $.map(wpData.cats[postType], function(index){ return {value:index.term_taxonomy_id, label:index.name }; }),
                tags = $.map(wpData.tags[postType], function(index){ return {value:index.term_taxonomy_id, label:index.name }; });

            this.set('postCatsList', cats);
            this.set('postTagsList', tags);

            if( keepTaxo !== true ){
                this.set('postTags', undefined);
                this.set('postCats', undefined);
            }

        }.observes('postType'),

        onPostImageTypeChanged: function(){
            this.set('useCustomBg', this.get('postImageType') === 'custom');
        }.observes('postImageType'),

        updatePreview: function(serverData){
            console.log(serverData)
            if( serverData.preview_results ) {
                this.set('previewResults', serverData.preview_results);
            }else{
                this.set('previewResults', '<div style="margin:20px">Nothing found. Please try another filter. </div>');
                this.set('controllers.slides.layersController.noticeMsg',  'Nothing found. Please try another filter in posts settings.');
            }
        },

        updateWpPostData: function(serverData){
            var data = serverData.template_tags;

            if( !data ){
                MSPanel.wpPostsData = {};
                this.set('firstSlide.bg', undefined);
                this.set('controllers.slides.layersController.noticeMsg', 'Nothing found. Please try another filter in posts settings.' );
                this.get('controllers.slides.layersController').updateDynamicContent();
                this.set('needToReload', false);

                return;
            }


            if( this.get('postImageType') === 'custom' ){
                this.set('firstSlide.bg', this.get('postSlideBg'));
            } else if( data['image-url'].length > 0 ){
                this.set('firstSlide.bg', data['image-url']);
            } else {
                this.set('firstSlide.bg', undefined);
            }

            MSPanel.wpPostsData = data;
            this.set('controllers.slides.layersController.noticeMsg', undefined);
            this.get('controllers.slides.layersController').updateDynamicContent();
            this.set('needToReload', false);
        },

        checkForReload: function(){
            this.set('needToReload', true);
            var that = this;

            clearTimeout(this.get('reqTo'));
            this.set('reqTo', setTimeout(function(){
                that.loadData();
            },200));

        }.observes('postType', 'postCats', 'postTags', 'postCount', 'postImageType', 'postOrder', 'postOrderDir', 'postExcludeIds', 'postIncludeIds', 'postOffset', 'postExcerptLen'),

        onActivate: function(){},
        onDeactivate: function(){
            if( this.get('needToReload') ){
                clearTimeout(this.get('reqTo'));
                this.loadData();
                this.registerDynamicTags();
            }
        },

        registerDynamicTags: function(){
            // Register dynamic tags
            if(MSPanel.dynamicTags){

                MSPanel.dynamicTags = [];
                // register general tags
                MSPanel.dynamicTags.push.apply(MSPanel.dynamicTags, $.map(__MSP_POST.content_tags.general, registerTag));
                // register post type tags
                var postTags = __MSP_POST.content_tags[this.get('postType')];
                if( postTags && postTags.length > 0 ) {
                    MSPanel.dynamicTags.push.apply(MSPanel.dynamicTags, $.map(postTags, registerTag));
                }
            }
        }
    });

    function registerTag(tag){
        return {
            name: tag.label,
            tag: '{{'+tag.name+'}}',
            tagName: tag.name,
            generator: function(tagObj) { return MSPanel.wpPostsData[tagObj.tagName] || tagObj.tag; }
        };
    }

})(jQuery);

/* ================== src/js/mspanel/controllers/WcproductController.js =================== */
/**
 * Master Slider Wordpress Panel Post Controller
 * @author Aveta Ltd
 * @extends {Ember.ObjectController}
 * @package MSPanel
 *
 *
 *		Ajax Request:
 * 			(string) post_type
 * 			(array)  terms      [ "product_tag": "79", "product_tag": "71" ]  (note : 79 is term_id)
 * 			(string) orderby    ( date | menu_order | title | ID | rand | comment_count | modified | author )
 * 			(string) order     ( DESC | ASC )
 * 			(int)    limit      // number of post to show
 * 			(int)    offset     // number of post to displace or pass over
 * 			(string) slideImage ( auto, featured, first, none )
 * 			(int|string) post__not_in    ( 112,82,39 ) (exclude post ids from result)
 * 
 * 
 */
;(function($){

	MSPanel.wpPostData = {}; // current data of posts
	
	MSPanel.WcproductController = Ember.ObjectController.extend({
		needs:['slides'],

		wpData:null, // added object in page by backend

		firstSlide: null, // the slide template
		needToReload: false,
		ajaxRequest: null,

		setup: function(){

			if( Ember.isEmpty(this.get('postType')) ){
				this.set('postType', 'product');
			}

			this.registerDynamicTags();

			if( Ember.isEmpty(this.get('postCount')) ){
				this.set('postCount', 10);
			}

			if( Ember.isEmpty(this.get('postOrder')) ){
				this.set('postOrder', 'date');
			}

			if( Ember.isEmpty(this.get('postOrderDir')) ){
				this.set('postOrderDir', 'DESC');
			}

			if( Ember.isEmpty(this.get('postImageType')) ){
				this.set('postImageType', 'auto');
			}

			// init wp data
			var wpData = __MSP_POST.types_taxs_terms;
			this.set('wpData', wpData);
			this.set('firstSlide', MSPanel.Slide.find(1));
			this.loadData();

			this.onPostTypeChanged(true);	
		},

		/**
		 * Loads data from Wordpress
		 */
		loadData: function(){
			this.set('needToReload', false);

			// abort last ajax request
			var ajaxRequest = this.get('ajaxRequest');
			if( !Ember.isEmpty(ajaxRequest) ){
				ajaxRequest.abort();
			}  

			var that = this,
				request,
				data = {},
				terms = [];

			// create ajax data
			data.post_type 		= this.get('postType');
			data.orderby 		= this.get('postOrder');
			data.order 			= this.get('postOrderDir');
			data.limit 			= this.get('postCount');
			data.post__not_in 	= this.get('postExcludeIds');
			data.slideImage 	= this.get('postImageType');
			data.offset 		= this.get('postOffset');
			data.excerpt_length	= this.get('postExcerptLen');
			data.only_instock	= this.get('wcOnlyInstock');   	
			data.only_featured	= this.get('wcOnlyFeatured');
			data.only_onsale	= this.get('wcOnlyOnsale');
			data.action 		= 'wc_slider_preview';
			data.nonce     		= jQuery('#msp-main-wrapper').data('nonce');

			if( !Ember.isEmpty(this.get('postCats')) ){
				var cats = this.get('postCats');

				if( cats[0] === '' ){
					cats.splice(0,1);
				}

				if( cats.length !== 0 ){
					data.cats = cats.join(',');
				}
			}

			if( !Ember.isEmpty(this.get('postTags')) ){
				var tags = this.get('postTags');
				if( tags[0] === '' ){
					tags.splice(0,1);
				}

				if( tags.length !== 0 ){
					data.tags = tags.join(',');
				}
			}

			this.set('previewResults', '<div class="msp-posts-loading">Loading data...</div>');
			this.set('controllers.slides.layersController.noticeMsg', 'Loading products data...');
			var request = $.post(__MS.ajax_url, data, function(serverData) {
			  	if( serverData.success ){
					that.updatePreview(serverData);
					that.updateWpPostData(serverData);
				} else {
					that.set('previewResults', 'Error: ' + serverData.message );
					that.set('controllers.slides.layersController.noticeMsg',  'Error: ' + serverData.message);
				}
			}, "json");

			this.set('ajaxRequest', request);

		},

		onPostTypeChanged: function(keepTaxo){
			var wpData = this.get('wpData'),
				postType = this.get('postType');
			
			if( Ember.isEmpty(wpData) ){
				return;
			}

			var cats = $.map(wpData.cats[postType], function(index){ return {value:index.term_taxonomy_id, label:index.name }; }),
			 	tags = $.map(wpData.tags[postType], function(index){ return {value:index.term_taxonomy_id, label:index.name }; });

			this.set('postCatsList', cats);
			this.set('postTagsList', tags);

			if( keepTaxo !== true ){
				this.set('postTags', undefined);
				this.set('postCats', undefined);
			}

		}.observes('postType'),

		onPostImageTypeChanged: function(){
			this.set('useCustomBg', this.get('postImageType') === 'custom');
		}.observes('postImageType'),

		updatePreview: function(serverData){
			if( serverData.preview_results ) {
				this.set('previewResults', serverData.preview_results);
			}else{
				this.set('previewResults', '<div style="margin:20px">Nothing found. Please try another filter. </div>');
				this.set('controllers.slides.layersController.noticeMsg',  'Nothing found. Please try another filter in product slider settings.');
			}
		},

		updateWpPostData: function(serverData){
			var data = serverData.template_tags;
			
			if( !data ){
				MSPanel.wpPostsData = {};
				this.set('firstSlide.bg', undefined);
				this.set('controllers.slides.layersController.noticeMsg', 'Nothing found. Please try another filter in product slider settings.' );
				this.get('controllers.slides.layersController').updateDynamicContent();
				this.set('needToReload', false);

				return;
			}

			if( this.get('postImageType') === 'custom' ){
				this.set('firstSlide.bg', this.get('postSlideBg'));
			} else if( data['image-url'].length > 0 ){
				this.set('firstSlide.bg', data['image-url']);
			} else {
				this.set('firstSlide.bg', undefined);
			}

			MSPanel.wpPostsData = data;
			this.set('controllers.slides.layersController.noticeMsg', undefined);
			this.get('controllers.slides.layersController').updateDynamicContent();
			this.set('needToReload', false);
		},

		checkForReload: function(){
			this.set('needToReload', true);
			var that = this;

			clearTimeout(this.get('reqTo'));
			this.set('reqTo', setTimeout(function(){
				that.loadData();
			},200));

		}.observes('postType', 'postCats', 'postTags', 'postCount', 'postImageType', 'postOrder', 'postOrderDir', 'postExcludeIds', 'postOffset', 'postExcerptLen', 'wcOnlyOnsale', 'wcOnlyFeatured', 'wcOnlyInstock'),

		onActivate: function(){},
		onDeactivate: function(){
			if( this.get('needToReload') ){
				clearTimeout(this.get('reqTo'));
				this.loadData();
				this.registerDynamicTags();
			}
		},

		registerDynamicTags: function(){
			// Register dynamic tags
			if(MSPanel.dynamicTags){

				MSPanel.dynamicTags = [];
				// register general tags
				MSPanel.dynamicTags.push.apply(MSPanel.dynamicTags, $.map(__MSP_POST.content_tags.general, registerTag));
				// register post type tags
				var postTags = __MSP_POST.content_tags[this.get('postType')];
				if( postTags && postTags.length > 0 ) {
					MSPanel.dynamicTags.push.apply(MSPanel.dynamicTags, $.map(postTags, registerTag));
				}
			}		
		}
	});
	
	function registerTag(tag){
		return {
			name: tag.label,
			tag: '{{'+tag.name+'}}',
			tagName: tag.name,
			generator: function(tagObj) { return MSPanel.wpPostsData[tagObj.tagName] || tagObj.tag; }
		};
	}

})(jQuery);