/*! elementor - v2.0.11 - 09-05-2018 */
/*! elementor - v2.0.10 - 09-05-2018 */
/*! elementor - v2.0.10 - 08-05-2018 */
(function(){function r(e,n,t){function o(i,f){if(!n[i]){if(!e[i]){var c="function"==typeof require&&require;if(!f&&c)return c(i,!0);if(u)return u(i,!0);var a=new Error("Cannot find module '"+i+"'");throw a.code="MODULE_NOT_FOUND",a}var p=n[i]={exports:{}};e[i][0].call(p.exports,function(r){var n=e[i][1][r];return o(n||r)},p,p.exports,r,e,n,t)}return n[i].exports}for(var u="function"==typeof require&&require,i=0;i<t.length;i++)o(t[i]);return o}return r})()({1:[function(require,module,exports){
/* global jQuery, ElementorGutenbergSettings */
( function( $ ) {
	'use strict';

	var ElementorGutenbergApp = {

		cacheElements: function() {
			this.isElementorMode = '1' === ElementorGutenbergSettings.isElementorMode;

			this.cache = {};

			this.cache.$gutenberg = $( '#editor' );
			this.cache.$switchMode = $( $( '#elementor-gutenberg-button-switch-mode' ).html() );

			this.cache.$gutenberg.find( '.edit-post-header-toolbar' ).append( this.cache.$switchMode );
			this.cache.$switchModeButton = this.cache.$switchMode.find( '#elementor-switch-mode-button' );

			this.cache.$editorPanel = $( $( '#elementor-gutenberg-panel' ).html() );

			this.cache.$gurenbergBlockList = this.cache.$gutenberg.find( '.editor-block-list__layout' );
			this.cache.$gurenbergBlockList.after( this.cache.$editorPanel );

			this.cache.$editorPanelButton = this.cache.$editorPanel.find( '#elementor-go-to-edit-page-link' );

			this.toggleStatus();
		},

		bindEvents: function() {
			var self = this;

			self.cache.$switchModeButton.on( 'click', function() {
				self.isElementorMode = ! self.isElementorMode;

				self.toggleStatus();

				if ( self.isElementorMode ) {
					self.cache.$editorPanelButton.trigger( 'click' );
				} else {
					var wpEditor = wp.data.dispatch( 'core/editor' );

					wpEditor.editPost( { gutenberg_elementor_mode: false } );
					wpEditor.savePost();
				}
			} );

			self.cache.$editorPanelButton.on( 'click', function( event ) {
				event.preventDefault();

				self.animateLoader();

				wp.data.dispatch( 'core/editor' ).savePost();
				self.redirectWhenSave();
			} );
		},

		redirectWhenSave: function() {
			var self = this;

			setTimeout( function() {
				if ( wp.data.select( 'core/editor' ).isSavingPost() ) {
					self.redirectWhenSave();
				} else {
					location.href = ElementorGutenbergSettings.editLink;
				}
			}, 300 );
		},

		animateLoader: function() {
			this.cache.$editorPanelButton.addClass( 'elementor-animate' );
		},

		toggleStatus: function() {
			jQuery( 'body' )
				.toggleClass( 'elementor-editor-active', this.isElementorMode )
				.toggleClass( 'elementor-editor-inactive', ! this.isElementorMode );
		},

		init: function() {
			this.cacheElements();
			this.bindEvents();
		}
	};

	$( function() {
		ElementorGutenbergApp.init();
	} );

}( jQuery ) );

},{}]},{},[1])
//# sourceMappingURL=data:application/json;charset=utf-8;base64,eyJ2ZXJzaW9uIjozLCJzb3VyY2VzIjpbIm5vZGVfbW9kdWxlcy9icm93c2VyLXBhY2svX3ByZWx1ZGUuanMiLCJhc3NldHMvZGV2L2pzL2FkbWluL2d1dGVuYmVyZy5qcyJdLCJuYW1lcyI6W10sIm1hcHBpbmdzIjoiQUFBQTtBQ0FBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0E7QUFDQTtBQUNBO0FBQ0EiLCJmaWxlIjoiZ2VuZXJhdGVkLmpzIiwic291cmNlUm9vdCI6IiIsInNvdXJjZXNDb250ZW50IjpbIihmdW5jdGlvbigpe2Z1bmN0aW9uIHIoZSxuLHQpe2Z1bmN0aW9uIG8oaSxmKXtpZighbltpXSl7aWYoIWVbaV0pe3ZhciBjPVwiZnVuY3Rpb25cIj09dHlwZW9mIHJlcXVpcmUmJnJlcXVpcmU7aWYoIWYmJmMpcmV0dXJuIGMoaSwhMCk7aWYodSlyZXR1cm4gdShpLCEwKTt2YXIgYT1uZXcgRXJyb3IoXCJDYW5ub3QgZmluZCBtb2R1bGUgJ1wiK2krXCInXCIpO3Rocm93IGEuY29kZT1cIk1PRFVMRV9OT1RfRk9VTkRcIixhfXZhciBwPW5baV09e2V4cG9ydHM6e319O2VbaV1bMF0uY2FsbChwLmV4cG9ydHMsZnVuY3Rpb24ocil7dmFyIG49ZVtpXVsxXVtyXTtyZXR1cm4gbyhufHxyKX0scCxwLmV4cG9ydHMscixlLG4sdCl9cmV0dXJuIG5baV0uZXhwb3J0c31mb3IodmFyIHU9XCJmdW5jdGlvblwiPT10eXBlb2YgcmVxdWlyZSYmcmVxdWlyZSxpPTA7aTx0Lmxlbmd0aDtpKyspbyh0W2ldKTtyZXR1cm4gb31yZXR1cm4gcn0pKCkiLCIvKiBnbG9iYWwgalF1ZXJ5LCBFbGVtZW50b3JHdXRlbmJlcmdTZXR0aW5ncyAqL1xuKCBmdW5jdGlvbiggJCApIHtcblx0J3VzZSBzdHJpY3QnO1xuXG5cdHZhciBFbGVtZW50b3JHdXRlbmJlcmdBcHAgPSB7XG5cblx0XHRjYWNoZUVsZW1lbnRzOiBmdW5jdGlvbigpIHtcblx0XHRcdHRoaXMuaXNFbGVtZW50b3JNb2RlID0gJzEnID09PSBFbGVtZW50b3JHdXRlbmJlcmdTZXR0aW5ncy5pc0VsZW1lbnRvck1vZGU7XG5cblx0XHRcdHRoaXMuY2FjaGUgPSB7fTtcblxuXHRcdFx0dGhpcy5jYWNoZS4kZ3V0ZW5iZXJnID0gJCggJyNlZGl0b3InICk7XG5cdFx0XHR0aGlzLmNhY2hlLiRzd2l0Y2hNb2RlID0gJCggJCggJyNlbGVtZW50b3ItZ3V0ZW5iZXJnLWJ1dHRvbi1zd2l0Y2gtbW9kZScgKS5odG1sKCkgKTtcblxuXHRcdFx0dGhpcy5jYWNoZS4kZ3V0ZW5iZXJnLmZpbmQoICcuZWRpdC1wb3N0LWhlYWRlci10b29sYmFyJyApLmFwcGVuZCggdGhpcy5jYWNoZS4kc3dpdGNoTW9kZSApO1xuXHRcdFx0dGhpcy5jYWNoZS4kc3dpdGNoTW9kZUJ1dHRvbiA9IHRoaXMuY2FjaGUuJHN3aXRjaE1vZGUuZmluZCggJyNlbGVtZW50b3Itc3dpdGNoLW1vZGUtYnV0dG9uJyApO1xuXG5cdFx0XHR0aGlzLmNhY2hlLiRlZGl0b3JQYW5lbCA9ICQoICQoICcjZWxlbWVudG9yLWd1dGVuYmVyZy1wYW5lbCcgKS5odG1sKCkgKTtcblxuXHRcdFx0dGhpcy5jYWNoZS4kZ3VyZW5iZXJnQmxvY2tMaXN0ID0gdGhpcy5jYWNoZS4kZ3V0ZW5iZXJnLmZpbmQoICcuZWRpdG9yLWJsb2NrLWxpc3RfX2xheW91dCcgKTtcblx0XHRcdHRoaXMuY2FjaGUuJGd1cmVuYmVyZ0Jsb2NrTGlzdC5hZnRlciggdGhpcy5jYWNoZS4kZWRpdG9yUGFuZWwgKTtcblxuXHRcdFx0dGhpcy5jYWNoZS4kZWRpdG9yUGFuZWxCdXR0b24gPSB0aGlzLmNhY2hlLiRlZGl0b3JQYW5lbC5maW5kKCAnI2VsZW1lbnRvci1nby10by1lZGl0LXBhZ2UtbGluaycgKTtcblxuXHRcdFx0dGhpcy50b2dnbGVTdGF0dXMoKTtcblx0XHR9LFxuXG5cdFx0YmluZEV2ZW50czogZnVuY3Rpb24oKSB7XG5cdFx0XHR2YXIgc2VsZiA9IHRoaXM7XG5cblx0XHRcdHNlbGYuY2FjaGUuJHN3aXRjaE1vZGVCdXR0b24ub24oICdjbGljaycsIGZ1bmN0aW9uKCkge1xuXHRcdFx0XHRzZWxmLmlzRWxlbWVudG9yTW9kZSA9ICEgc2VsZi5pc0VsZW1lbnRvck1vZGU7XG5cblx0XHRcdFx0c2VsZi50b2dnbGVTdGF0dXMoKTtcblxuXHRcdFx0XHRpZiAoIHNlbGYuaXNFbGVtZW50b3JNb2RlICkge1xuXHRcdFx0XHRcdHNlbGYuY2FjaGUuJGVkaXRvclBhbmVsQnV0dG9uLnRyaWdnZXIoICdjbGljaycgKTtcblx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHR2YXIgd3BFZGl0b3IgPSB3cC5kYXRhLmRpc3BhdGNoKCAnY29yZS9lZGl0b3InICk7XG5cblx0XHRcdFx0XHR3cEVkaXRvci5lZGl0UG9zdCggeyBndXRlbmJlcmdfZWxlbWVudG9yX21vZGU6IGZhbHNlIH0gKTtcblx0XHRcdFx0XHR3cEVkaXRvci5zYXZlUG9zdCgpO1xuXHRcdFx0XHR9XG5cdFx0XHR9ICk7XG5cblx0XHRcdHNlbGYuY2FjaGUuJGVkaXRvclBhbmVsQnV0dG9uLm9uKCAnY2xpY2snLCBmdW5jdGlvbiggZXZlbnQgKSB7XG5cdFx0XHRcdGV2ZW50LnByZXZlbnREZWZhdWx0KCk7XG5cblx0XHRcdFx0c2VsZi5hbmltYXRlTG9hZGVyKCk7XG5cblx0XHRcdFx0d3AuZGF0YS5kaXNwYXRjaCggJ2NvcmUvZWRpdG9yJyApLnNhdmVQb3N0KCk7XG5cdFx0XHRcdHNlbGYucmVkaXJlY3RXaGVuU2F2ZSgpO1xuXHRcdFx0fSApO1xuXHRcdH0sXG5cblx0XHRyZWRpcmVjdFdoZW5TYXZlOiBmdW5jdGlvbigpIHtcblx0XHRcdHZhciBzZWxmID0gdGhpcztcblxuXHRcdFx0c2V0VGltZW91dCggZnVuY3Rpb24oKSB7XG5cdFx0XHRcdGlmICggd3AuZGF0YS5zZWxlY3QoICdjb3JlL2VkaXRvcicgKS5pc1NhdmluZ1Bvc3QoKSApIHtcblx0XHRcdFx0XHRzZWxmLnJlZGlyZWN0V2hlblNhdmUoKTtcblx0XHRcdFx0fSBlbHNlIHtcblx0XHRcdFx0XHRsb2NhdGlvbi5ocmVmID0gRWxlbWVudG9yR3V0ZW5iZXJnU2V0dGluZ3MuZWRpdExpbms7XG5cdFx0XHRcdH1cblx0XHRcdH0sIDMwMCApO1xuXHRcdH0sXG5cblx0XHRhbmltYXRlTG9hZGVyOiBmdW5jdGlvbigpIHtcblx0XHRcdHRoaXMuY2FjaGUuJGVkaXRvclBhbmVsQnV0dG9uLmFkZENsYXNzKCAnZWxlbWVudG9yLWFuaW1hdGUnICk7XG5cdFx0fSxcblxuXHRcdHRvZ2dsZVN0YXR1czogZnVuY3Rpb24oKSB7XG5cdFx0XHRqUXVlcnkoICdib2R5JyApXG5cdFx0XHRcdC50b2dnbGVDbGFzcyggJ2VsZW1lbnRvci1lZGl0b3ItYWN0aXZlJywgdGhpcy5pc0VsZW1lbnRvck1vZGUgKVxuXHRcdFx0XHQudG9nZ2xlQ2xhc3MoICdlbGVtZW50b3ItZWRpdG9yLWluYWN0aXZlJywgISB0aGlzLmlzRWxlbWVudG9yTW9kZSApO1xuXHRcdH0sXG5cblx0XHRpbml0OiBmdW5jdGlvbigpIHtcblx0XHRcdHRoaXMuY2FjaGVFbGVtZW50cygpO1xuXHRcdFx0dGhpcy5iaW5kRXZlbnRzKCk7XG5cdFx0fVxuXHR9O1xuXG5cdCQoIGZ1bmN0aW9uKCkge1xuXHRcdEVsZW1lbnRvckd1dGVuYmVyZ0FwcC5pbml0KCk7XG5cdH0gKTtcblxufSggalF1ZXJ5ICkgKTtcbiJdfQ==
