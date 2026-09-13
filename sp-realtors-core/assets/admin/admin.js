/**
 * SP Realtors Core — admin scripts.
 *
 * Modules (each runs only if its markup exists):
 *  1. Tabbed "Property Details" meta box (keyboard accessible)
 *  2. Gallery field (Media Library, drag sort + move buttons)
 *  3. Location term image picker
 *  4. Live price preview (₹ Cr / L)
 *  5. Featured ★ toggle in the properties list
 */
( function () {
	'use strict';

	var data = window.sprAdmin || { i18n: {} };
	var i18n = data.i18n || {};

	function ready( fn ) {
		if ( document.readyState !== 'loading' ) {
			fn();
		} else {
			document.addEventListener( 'DOMContentLoaded', fn );
		}
	}

	/* 1. Tabs ------------------------------------------------------------ */
	function initTabs() {
		document.querySelectorAll( '[data-spr-tabs]' ).forEach( function ( box ) {
			var tablist = box.querySelector( '[role="tablist"]' );
			if ( ! tablist ) {
				return;
			}
			var tabs = Array.prototype.slice.call( tablist.querySelectorAll( '[role="tab"]' ) );
			var panels = tabs.map( function ( tab ) {
				return document.getElementById( tab.getAttribute( 'aria-controls' ) );
			} );

			function select( index, focus ) {
				tabs.forEach( function ( tab, i ) {
					var active = i === index;
					tab.setAttribute( 'aria-selected', active ? 'true' : 'false' );
					tab.tabIndex = active ? 0 : -1;
					if ( panels[ i ] ) {
						panels[ i ].hidden = ! active;
					}
				} );
				if ( focus ) {
					tabs[ index ].focus();
				}
			}

			tablist.hidden = false;
			box.classList.add( 'spr-tabs-ready' );
			select( 0, false );

			tabs.forEach( function ( tab, index ) {
				tab.addEventListener( 'click', function () {
					select( index, false );
				} );
				tab.addEventListener( 'keydown', function ( e ) {
					var next = null;
					if ( e.key === 'ArrowRight' ) {
						next = ( index + 1 ) % tabs.length;
					} else if ( e.key === 'ArrowLeft' ) {
						next = ( index - 1 + tabs.length ) % tabs.length;
					} else if ( e.key === 'Home' ) {
						next = 0;
					} else if ( e.key === 'End' ) {
						next = tabs.length - 1;
					}
					if ( next !== null ) {
						e.preventDefault();
						select( next, true );
					}
				} );
			} );

			// If the browser flags an invalid field inside a hidden panel, reveal that panel.
			box.addEventListener(
				'invalid',
				function ( e ) {
					var panel = e.target.closest( '[role="tabpanel"]' );
					var idx = panels.indexOf( panel );
					if ( idx > -1 ) {
						select( idx, false );
					}
				},
				true
			);
		} );
	}

	/* 2. Gallery --------------------------------------------------------- */
	function initGalleries() {
		if ( ! window.wp || ! wp.media ) {
			return;
		}

		document.querySelectorAll( '[data-spr-gallery]' ).forEach( function ( wrap ) {
			var input = wrap.querySelector( '[data-spr-gallery-input]' );
			var list = wrap.querySelector( '[data-spr-gallery-list]' );
			var addBtn = wrap.querySelector( '[data-spr-gallery-add]' );
			var frame = null;

			function sync() {
				var ids = Array.prototype.map.call( list.querySelectorAll( '.spr-gallery__item' ), function ( li ) {
					return li.getAttribute( 'data-id' );
				} );
				input.value = ids.join( ',' );
			}

			function iconButton( move, icon, label, extraClass ) {
				var btn = document.createElement( 'button' );
				btn.type = 'button';
				btn.className = 'spr-gallery__btn' + ( extraClass ? ' ' + extraClass : '' );
				btn.setAttribute( 'aria-label', label );
				if ( move === 'remove' ) {
					btn.setAttribute( 'data-spr-remove', '' );
				} else {
					btn.setAttribute( 'data-spr-move', move );
				}
				var span = document.createElement( 'span' );
				span.className = 'dashicons ' + icon;
				span.setAttribute( 'aria-hidden', 'true' );
				btn.appendChild( span );
				return btn;
			}

			function buildItem( attachment ) {
				var li = document.createElement( 'li' );
				li.className = 'spr-gallery__item';
				li.setAttribute( 'data-id', String( attachment.id ) );

				var img = document.createElement( 'img' );
				var sizes = attachment.sizes || {};
				img.src = ( sizes.thumbnail || sizes.medium || sizes.full || {} ).url || attachment.url;
				img.alt = attachment.alt || '';
				img.draggable = false;
				li.appendChild( img );

				var actions = document.createElement( 'div' );
				actions.className = 'spr-gallery__actions';
				actions.appendChild( iconButton( '-1', 'dashicons-arrow-left-alt2', i18n.moveLeft ) );
				actions.appendChild( iconButton( '1', 'dashicons-arrow-right-alt2', i18n.moveRight ) );
				actions.appendChild( iconButton( 'remove', 'dashicons-no-alt', i18n.remove, 'spr-gallery__btn--remove' ) );
				li.appendChild( actions );
				return li;
			}

			addBtn.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				if ( frame ) {
					frame.open();
					return;
				}
				frame = wp.media( {
					title: i18n.galleryTitle,
					button: { text: i18n.galleryButton },
					library: { type: 'image' },
					multiple: 'add',
				} );
				frame.on( 'select', function () {
					var existing = input.value ? input.value.split( ',' ) : [];
					frame
						.state()
						.get( 'selection' )
						.each( function ( model ) {
							var att = model.toJSON();
							if ( existing.indexOf( String( att.id ) ) === -1 ) {
								list.appendChild( buildItem( att ) );
								existing.push( String( att.id ) );
							}
						} );
					sync();
				} );
				frame.open();
			} );

			list.addEventListener( 'click', function ( e ) {
				var btn = e.target.closest( 'button' );
				if ( ! btn ) {
					return;
				}
				var li = btn.closest( '.spr-gallery__item' );
				if ( btn.hasAttribute( 'data-spr-remove' ) ) {
					var nextFocus = li.nextElementSibling || li.previousElementSibling;
					li.remove();
					sync();
					if ( nextFocus ) {
						nextFocus.querySelector( '[data-spr-remove]' ).focus();
					} else {
						addBtn.focus();
					}
					return;
				}
				var move = parseInt( btn.getAttribute( 'data-spr-move' ), 10 );
				if ( move === -1 && li.previousElementSibling ) {
					list.insertBefore( li, li.previousElementSibling );
				} else if ( move === 1 && li.nextElementSibling ) {
					list.insertBefore( li.nextElementSibling, li );
				}
				btn.focus();
				sync();
			} );

			if ( window.jQuery && jQuery.fn.sortable ) {
				jQuery( list ).sortable( {
					items: '.spr-gallery__item',
					placeholder: 'spr-gallery__placeholder',
					tolerance: 'pointer',
					update: sync,
				} );
			}
		} );
	}

	/* 3. Term image picker ---------------------------------------------- */
	function initImagePickers() {
		if ( ! window.wp || ! wp.media ) {
			return;
		}
		document.querySelectorAll( '[data-spr-image-picker]' ).forEach( function ( wrap ) {
			var input = wrap.querySelector( '[data-spr-image-input]' );
			var preview = wrap.querySelector( '[data-spr-image-preview]' );
			var removeBtn = wrap.querySelector( '[data-spr-image-remove]' );
			var frame = null;

			wrap.querySelector( '[data-spr-image-select]' ).addEventListener( 'click', function ( e ) {
				e.preventDefault();
				if ( ! frame ) {
					frame = wp.media( {
						title: i18n.imageTitle,
						button: { text: i18n.imageButton },
						library: { type: 'image' },
						multiple: false,
					} );
					frame.on( 'select', function () {
						var att = frame.state().get( 'selection' ).first().toJSON();
						var sizes = att.sizes || {};
						var img = document.createElement( 'img' );
						img.src = ( sizes.thumbnail || sizes.full || {} ).url || att.url;
						img.alt = '';
						preview.innerHTML = '';
						preview.appendChild( img );
						input.value = String( att.id );
						removeBtn.hidden = false;
					} );
				}
				frame.open();
			} );

			removeBtn.addEventListener( 'click', function ( e ) {
				e.preventDefault();
				input.value = '';
				preview.innerHTML = '';
				removeBtn.hidden = true;
			} );

			// Core clears the add-term form via AJAX; reset our picker too.
			if ( window.jQuery ) {
				jQuery( document ).ajaxSuccess( function ( event, xhr, settings ) {
					if ( settings && typeof settings.data === 'string' && settings.data.indexOf( 'action=add-tag' ) !== -1 ) {
						input.value = '';
						preview.innerHTML = '';
						removeBtn.hidden = true;
					}
				} );
			}
		} );
	}

	/* 4. Price preview --------------------------------------------------- */
	function trimDecimals( value ) {
		return String( Math.round( value * 100 ) / 100 );
	}

	function initPricePreview() {
		var output = document.querySelector( '[data-spr-price-preview]' );
		if ( ! output ) {
			return;
		}
		var input = output.parentNode.querySelector( 'input[type="number"]' );
		function update() {
			var n = parseInt( input.value, 10 );
			if ( ! n || n < 0 ) {
				output.textContent = '';
				return;
			}
			var text;
			if ( n >= 10000000 ) {
				text = '₹' + trimDecimals( n / 10000000 ) + ' ' + i18n.crore;
			} else if ( n >= 100000 ) {
				text = '₹' + trimDecimals( n / 100000 ) + ' ' + i18n.lakh;
			} else {
				text = '₹' + n.toLocaleString( 'en-IN' );
			}
			output.textContent = i18n.preview + ' ' + text;
		}
		input.addEventListener( 'input', update );
		update();
	}

	/* 5. Featured toggle ------------------------------------------------- */
	function initFeaturedToggle() {
		document.addEventListener( 'click', function ( e ) {
			var btn = e.target.closest( '[data-spr-toggle-featured]' );
			if ( ! btn ) {
				return;
			}
			e.preventDefault();
			btn.classList.add( 'is-busy' );

			var body = new FormData();
			body.append( 'action', 'spr_toggle_featured' );
			body.append( 'nonce', data.toggleNonce );
			body.append( 'post_id', btn.getAttribute( 'data-spr-toggle-featured' ) );

			fetch( data.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body } )
				.then( function ( r ) {
					return r.json();
				} )
				.then( function ( json ) {
					if ( ! json || ! json.success ) {
						throw new Error( 'failed' );
					}
					var on = !! json.data.featured;
					btn.classList.toggle( 'is-featured', on );
					btn.setAttribute( 'aria-pressed', on ? 'true' : 'false' );
					var icon = btn.querySelector( '.dashicons' );
					icon.classList.toggle( 'dashicons-star-filled', on );
					icon.classList.toggle( 'dashicons-star-empty', ! on );
				} )
				.catch( function () {
					window.alert( i18n.error );
				} )
				.finally( function () {
					btn.classList.remove( 'is-busy' );
				} );
		} );
	}

	ready( function () {
		initTabs();
		initGalleries();
		initImagePickers();
		initPricePreview();
		initFeaturedToggle();
	} );
} )();
