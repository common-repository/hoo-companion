(function($){
	
	var HooSSEImport = {
		complete: {
			posts: 0,
			media: 0,
			users: 0,
			comments: 0,
			terms: 0,
		},

		updateDelta: function (type, delta) {
			this.complete[ type ] += delta;

			var self = this;
			requestAnimationFrame(function () {
				self.render();
			});
		},
		updateProgress: function ( type, complete, total ) {
			var text = complete + '/' + total;

			if( 'undefined' !== type && 'undefined' !== text ) {
				total = parseInt( total, 10 );
				if ( 0 === total || isNaN( total ) ) {
					total = 1;
				}
				var percent = parseInt( complete, 10 ) / total;
				var progress     = Math.round( percent * 100 ) + '%';
				var progress_bar = percent * 100;
			}
		},
		render: function () {
			var types = Object.keys( this.complete );
			var complete = 0;
			var total = 0;

			for (var i = types.length - 1; i >= 0; i--) {
				var type = types[i];
				this.updateProgress( type, this.complete[ type ], this.data.count[ type ] );

				complete += this.complete[ type ];
				total += this.data.count[ type ];
			}

			this.updateProgress( 'total', complete, total );
		}
	};
	var HooImporter = {
	
		customizer_data : '',
		wxr_url         : '',
		options_data    : '',
		widgets_data    : '',

		init: function()
		{
			this._bind();
		},
		_bind:function(){
			$( document ).on('click' , '.hoo-import-site', HooImporter._importDemo);
			$( document ).on('click' , '.hoo-import-wxr', HooImporter._importPrepareXML);
			$( document ).on('click' , '.hoo-import-options', HooImporter._importSiteOptions);
			$( document ).on('click' , '.hoo-import-widgets', HooImporter._importWidgets);
			$( document ).on('click' , '.hoo-sites-import-done', HooImporter._importEnd);
			
			},
		_importDemo:function(){
			
			if( ! confirm(  hooSiteImporter.i18n.s0 ) ) {
				return;
			}
			var wrap = $('.hoo-import-site');
			HooImporter.wxr_url = wrap.data('site-wxr');
			HooImporter.options_data = wrap.data('site-options');
			HooImporter.customizer_data = wrap.data('site-customizer');
			HooImporter.widgets_data = wrap.data('site-widgets');
			if ( $( '.active .hoo-installable' ).length || $( '.active .hoo-activate' ).length ) {

				HooImporter.checkAndInstallPlugins();
			} else {
				HooImporter._importCustomizerSettings();
			}
			
			},
		/**
		 * 1. Import Customizer Options.
		 */
		_importCustomizerSettings: function( event ) {

			$.ajax({
				url  : hooSiteImporter.ajaxurl,
				type : 'POST',
				dataType: 'json',
				data : {
					action          : 'hoo-sites-import-customizer-settings',
					customizer_data : HooImporter.customizer_data,
				},
				beforeSend: function() {
					$('.hoo-importer-status').append('<p class="import-return-info">'+hooSiteImporter.i18n.s1+'</p>');
					$('.hoo-import-site').text( hooSiteImporter.i18n.s1 );
				},
			})
			.fail(function( jqXHR ){
				$('.hoo-import-site').text( hooSiteImporter.i18n.s2 );
		    })
			.done(function ( customizer_data ) {

				// 1. Fail - Import Customizer Options.
				if( false === customizer_data.success ) {
					$('.hoo-importer-status').append('<p class="import-return-info notice-error">'+customizer_data.data+'</p>');
					$('.hoo-importer-status').append('<p class="import-return-info notice-error">'+hooSiteImporter.i18n.s2+'</p>');
					$('.hoo-import-site').text( hooSiteImporter.i18n.s2 );
				} else {
					
					// 1. Pass - Import Customizer Options.
					$('.hoo-import-site').text( hooSiteImporter.i18n.s3 );
					$('.hoo-importer-status').append('<p class="import-return-info notice-success">'+hooSiteImporter.i18n.s3+'</p>');
					
					$('.hoo-import-site').removeClass( 'hoo-import-site' ).addClass('hoo-import-wxr hoo-sites-import-customizer-settings-done');

					$(document).trigger( 'hoo-sites-import-customizer-settings-done' );
					$( ".hoo-import-wxr" ).trigger( "click" );
				}
			});
		},
		
		/**
		 * 2. Prepare XML Data.
		 */
		_importPrepareXML: function( event ) {

			$.ajax({
				url  : hooSiteImporter.ajaxurl,
				type : 'POST',
				dataType: 'json',
				data : {
					action  : 'hoo-sites-import-wxr',
					wxr_url : HooImporter.wxr_url,
				},
				beforeSend: function() {
					$('.hoo-importer-status').append('<p class="import-return-info">'+hooSiteImporter.i18n.s4+'</p>');
					$('.hoo-import-wxr').text( hooSiteImporter.i18n.s4 );
				},
			})
			.fail(function( jqXHR ){
				
				$('.hoo-importer-status').append('<p class="import-return-info notice-error">'+jqXHR.status + ' ' + jqXHR.responseText+'</p>');
		    })
			.done(function ( xml_data ) {

				// 2. Fail - Prepare XML Data.
				if( false === xml_data.success ) {
					
					$('.hoo-importer-status').append('<p class="import-return-info notice-error">'+hooSiteImporter.i18n.s5+'</p>');
					$('.hoo-importer-status').append('<p class="import-return-info notice-error">'+xml_data.data+'</p>');
					
					
				} 
					
					// 2. Pass - Prepare XML Data.
					// Import XML though Event Source.
					HooSSEImport.data = xml_data.data;
					HooSSEImport.render();
					
					$('.hoo-importer-status').append('<p class="import-return-info">'+hooSiteImporter.i18n.s6_1+'</p>');
					$('.hoo-import-wxr').text( hooSiteImporter.i18n.s6 );
										
					var evtSource = new EventSource( HooSSEImport.data.url );
					evtSource.onmessage = function ( message ) {
						var data = JSON.parse( message.data );
						switch ( data.action ) {
							case 'updateDelta':
									HooSSEImport.updateDelta( data.type, data.delta );
								break;

							case 'complete':
								evtSource.close();

								// 2. Pass - Import XML though "Source Event".
								$('.hoo-import-wxr').text( hooSiteImporter.i18n.s7 );
								$('.hoo-importer-status').append('<p class="import-return-info notice-success">'+hooSiteImporter.i18n.s7+'</p>');
								
								$('.hoo-import-wxr').removeClass( 'hoo-import-wxr' ).addClass('hoo-import-options hoo-sites-import-xml-done');
								
								$(document).trigger( 'hoo-sites-import-xml-done' );
								
								$( ".hoo-import-options" ).trigger( "click" );
								
								

								break;
						}
					};
					evtSource.addEventListener( 'log', function ( message ) {
						var data = JSON.parse( message.data );
						if( data.level !== 'warning' ){
							$('.hoo-importer-status').append( "<p class='import-return-info'>" + data.level + ': ' + data.message + "</p>" );
						}
					});	
					
			});
		},
		
		/**
		 * 3. Import Site Options.
		 */
		_importSiteOptions: function( event ) {

			$.ajax({
				url  : hooSiteImporter.ajaxurl,
				type : 'POST',
				dataType: 'json',
				data : {
					action       : 'hoo-sites-import-options',
					options_data : HooImporter.options_data,
				},
				beforeSend: function() {
					$('.hoo-importer-status').append('<p class="import-return-info">'+hooSiteImporter.i18n.s8+'</p>');
					$('.hoo-import-options').text( hooSiteImporter.i18n.s8 );
				},
			})
			.fail(function( jqXHR ){
				$('.hoo-importer-status').append('<p class="import-return-info notice-error">'+jqXHR.status + ' ' + jqXHR.responseText+'</p>');
				$('.hoo-import-options').text( hooSiteImporter.i18n.s9 );
		    })
			.done(function ( options_data ) {

				// 3. Fail - Import Site Options.
				if( false === options_data.success ) {
					$('.hoo-importer-status').append('<p class="import-return-info notice-error">'+hooSiteImporter.i18n.s9+'</p>');
					$('.hoo-import-options').text( hooSiteImporter.i18n.s9 );

				} else {

					// 3. Pass - Import Site Options.
					$('.hoo-importer-status').append('<p class="import-return-info notice-success">'+ hooSiteImporter.i18n.s10 +'</p>');
					$('.hoo-import-options').text( hooSiteImporter.i18n.s10 );
					
					$('.hoo-import-options').removeClass( 'hoo-import-options' ).addClass('hoo-import-widgets hoo-sites-import-options-done');
					$(document).trigger( 'hoo-sites-import-options-done' );
					$( ".hoo-import-widgets" ).trigger( "click" );
				}
			});
		},
		
		/**
		 * 4. Import Widgets.
		 */
		_importWidgets: function( event ) {

			$.ajax({
				url  : hooSiteImporter.ajaxurl,
				type : 'POST',
				dataType: 'json',
				data : {
					action       : 'hoo-sites-import-widgets',
					widgets_data : HooImporter.widgets_data,
				},
				beforeSend: function() {
					$('.hoo-importer-status').append('<p class="import-return-info">'+hooSiteImporter.i18n.s11+'</p>');
					$('.hoo-import-widgets').text( hooSiteImporter.i18n.s11 );
				},
			})
			.fail(function( jqXHR ){
				//$('.hoo-importer-status').append('<p class="import-return-info">'+hooSiteImporter.i18n.s11+'</p>');
				$('.hoo-importer-status').append('<p class="import-return-info notice-error">'+jqXHR.status + ' ' + jqXHR.responseText+'</p>');
				$('.hoo-import-widgets').text( hooSiteImporter.i18n.s12 );

		    })
			.done(function ( widgets_data ) {

				// 4. Fail - Import Widgets.
				if( false === widgets_data.success ) {
					$('.hoo-import-widgets').text( hooSiteImporter.i18n.s12 );
					$('.hoo-importer-status').append('<p class="import-return-info notice-error">'+widgets_data.data+'</p>');

				} else {
					
					// 4. Pass - Import Widgets.
					$('.hoo-importer-status').append('<p class="import-return-info notice-success">'+hooSiteImporter.i18n.s13+'</p>');
					$('.hoo-import-widgets').removeClass( 'hoo-import-widgets' ).addClass('hoo-sites-import-done hoo-sites-import-widgets-done');
					$(document).trigger( 'hoo-sites-import-widgets-done' );	
					$( ".hoo-sites-import-done" ).trigger( "click" );				
				}
			});
		},
		
		_importEnd: function( event ) {

			$('.hoo-sites-import-done').text( hooSiteImporter.i18n.s14 );
			$('.hoo-importer-status').append('<p class="import-return-info notice-success">'+hooSiteImporter.i18n.s14_1+'</p>');
			$('.hoo-import-button').removeClass( 'hoo-sites-import-done' );
		},
		checkAndInstallPlugins:function () {
		var installable = $( '.hoo-installable' );
		var toActivate = $( '.hoo-activate' );
		if ( installable.length || toActivate.length ) {

			$( installable ).each(
				function () {
					var plugin = $( this );
					$( plugin ).removeClass( 'hoo-installable' ).addClass( 'hoo-installing' );
					$( plugin ).find( 'span.dashicons' ).replaceWith( '<span class="dashicons dashicons-update" style="-webkit-animation: rotation 2s infinite linear; animation: rotation 2s infinite linear; color: #ffb227 "></span>' );
					var slug = $( this ).find( '.hoo-install-plugin' ).attr( 'data-slug' );
					
					if ( wp.updates.shouldRequestFilesystemCredentials && ! wp.updates.ajaxLocked ) {
						  wp.updates.requestFilesystemCredentials( event );
		  
						  $document.on( 'credential-modal-cancel', function() {
							  var $message = $( '.install-now.hoo-installing' );
		  
							  $message
								  .removeClass( 'hoo-installing' )
								  .text( wp.updates.l10n.installNow );
		  
							  wp.a11y.speak( wp.updates.l10n.updateCancel, 'polite' );
						  } );
					  }
					  
					wp.updates.installPlugin(
						{
							slug: slug,
							success: function ( response ) {
								HooImporter.activatePlugin( response.activateUrl, plugin );
							}
						}
					);
				}
			);

			$( toActivate ).each(
				function () {
						var plugin = $( this );
						var activateUrl = $( plugin ).find( '.activate-now' ).attr( 'href' );
					if (typeof activateUrl !== 'undefined') {
						HooImporter.activatePlugin( activateUrl, plugin );
					}
				}
			);
		}
	},

	activatePlugin: function ( activationUrl, plugin ) {
		$.ajax(
			{
				type: 'GET',
				url: activationUrl,
				beforeSend: function() {
					$( plugin ).removeClass( 'hoo-activate' ).addClass( 'hoo-installing' );
					$( plugin ).find( 'span.dashicons' ).replaceWith( '<span class="dashicons dashicons-update" style="-webkit-animation: rotation 2s infinite linear; animation: rotation 2s infinite linear; color: #ffb227 "></span>' );
					$( plugin ).find( '.activate-now' ).removeClass('activate-now  button-primary').addClass('button-activatting button-secondary').text('Activating').attr('href','#');
				},
				success: function () {
					$( plugin ).find( '.dashicons' ).replaceWith( '<span class="dashicons dashicons-yes" style="color: #34a85e"></span>' );
					$( plugin ).find( '.button-activatting' ).text('Activated');
					$( plugin ).removeClass( 'hoo-installing' );
				},
				complete: function() {
					if ( $( '.active .hoo-installing' ).length === 0 ) {
						$( '.hoo-import-site' ).trigger( 'click' );
					}
				}
			}
		);
	}

	}
	
	$(function(){
		HooImporter.init();
	});
	
})(jQuery);