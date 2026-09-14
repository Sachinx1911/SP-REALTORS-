/**
 * SP Realtors front-end JS. Vanilla, no jQuery.
 * Each module checks its own elements exist before doing anything, so
 * pages missing a section simply skip that module.
 */
( function () {
	'use strict';

	/**
	 * Mobile nav toggle: hamburger opens a full-screen drawer,
	 * Esc closes it, focus returns to the toggle button.
	 */
	function initNavToggle() {
		var toggle = document.querySelector( '.sp-realtors-nav__toggle' );
		var nav = document.getElementById( 'site-navigation' );

		if ( ! toggle || ! nav ) {
			return;
		}

		function closeNav() {
			nav.classList.remove( 'is-open' );
			toggle.setAttribute( 'aria-expanded', 'false' );
			document.body.classList.remove( 'sp-realtors-nav-open' );
		}

		function openNav() {
			nav.classList.add( 'is-open' );
			toggle.setAttribute( 'aria-expanded', 'true' );
			document.body.classList.add( 'sp-realtors-nav-open' );
		}

		toggle.addEventListener( 'click', function () {
			if ( nav.classList.contains( 'is-open' ) ) {
				closeNav();
			} else {
				openNav();
			}
		} );

		document.addEventListener( 'keydown', function ( event ) {
			if ( 'Escape' === event.key && nav.classList.contains( 'is-open' ) ) {
				closeNav();
				toggle.focus();
			}
		} );

		nav.querySelectorAll( 'a' ).forEach( function ( link ) {
			link.addEventListener( 'click', closeNav );
		} );
	}

	/**
	 * Search card / filter sidebar: Buy/Rent radio tabs swap the visible
	 * budget <optgroup> in the (single) budget <select>.
	 */
	function initPurposeBudgetSwap() {
		document.querySelectorAll( '.spr-search__fields select[name="budget"]' ).forEach( function ( select ) {
			var form = select.closest( 'form' );
			if ( ! form ) {
				return;
			}
			var radios = form.querySelectorAll( 'input[name="purpose"]' );
			if ( ! radios.length ) {
				return;
			}

			function sync() {
				var current = form.querySelector( 'input[name="purpose"]:checked' );
				var purpose = current ? current.value : '';
				select.querySelectorAll( 'optgroup' ).forEach( function ( group ) {
					group.hidden = purpose && group.dataset.purpose !== purpose;
				} );
				var selectedOption = select.options[ select.selectedIndex ];
				if ( selectedOption && selectedOption.closest( 'optgroup' ) && selectedOption.closest( 'optgroup' ).hidden ) {
					select.value = '';
				}
			}

			radios.forEach( function ( radio ) {
				radio.addEventListener( 'change', sync );
			} );
			sync();
		} );
	}

	/**
	 * Property archive filter drawer (mobile) + desktop auto-submit on change.
	 */
	function initFilters() {
		var toggle = document.querySelector( '.spr-filters-toggle' );
		var panel = document.getElementById( 'spr-filters-panel' );
		var overlay = document.querySelector( '.spr-filters-overlay' );
		var closeBtn = panel ? panel.querySelector( '.spr-filters__close' ) : null;

		if ( panel ) {
			function closePanel() {
				panel.classList.remove( 'is-open' );
				if ( overlay ) {
					overlay.hidden = true;
				}
				document.body.classList.remove( 'sp-realtors-nav-open' );
				if ( toggle ) {
					toggle.setAttribute( 'aria-expanded', 'false' );
				}
			}

			function openPanel() {
				panel.classList.add( 'is-open' );
				if ( overlay ) {
					overlay.hidden = false;
				}
				document.body.classList.add( 'sp-realtors-nav-open' );
				if ( toggle ) {
					toggle.setAttribute( 'aria-expanded', 'true' );
				}
				var firstField = panel.querySelector( 'input, select, button' );
				if ( firstField ) {
					firstField.focus();
				}
			}

			if ( toggle ) {
				toggle.setAttribute( 'aria-expanded', 'false' );
				toggle.addEventListener( 'click', openPanel );
			}
			if ( closeBtn ) {
				closeBtn.addEventListener( 'click', closePanel );
			}
			if ( overlay ) {
				overlay.addEventListener( 'click', closePanel );
			}
			document.addEventListener( 'keydown', function ( event ) {
				if ( 'Escape' === event.key && panel.classList.contains( 'is-open' ) ) {
					closePanel();
					if ( toggle ) {
						toggle.focus();
					}
				}
			} );
		}

		var form = document.querySelector( '.spr-filters' );
		if ( form ) {
			form.addEventListener( 'change', function ( event ) {
				if ( 'checkbox' === event.target.type || 'radio' === event.target.type ) {
					if ( window.innerWidth >= 1024 ) {
						form.submit();
					}
				}
			} );
		}
	}

	/**
	 * Archive sort <select>: submits its form on change (works without JS too,
	 * via the <noscript> Apply button).
	 */
	function initSortSelect() {
		var select = document.querySelector( '.spr-archive-toolbar__sort select' );
		if ( ! select ) {
			return;
		}
		select.addEventListener( 'change', function () {
			select.closest( 'form' ).submit();
		} );
	}

	/**
	 * AJAX "Load More" for the property archive/taxonomy grid.
	 */
	function initLoadMore() {
		var button = document.querySelector( '.spr-load-more__button' );
		var grid = document.getElementById( 'spr-property-grid' );
		if ( ! button || ! grid || 'undefined' === typeof sprData || ! sprData.loadMore ) {
			return;
		}

		var config = sprData.loadMore;
		var page = parseInt( button.dataset.page, 10 ) || 1;
		var maxPages = parseInt( button.dataset.maxPages, 10 ) || 1;
		var live = document.createElement( 'div' );
		live.className = 'screen-reader-text';
		live.setAttribute( 'aria-live', 'polite' );
		button.insertAdjacentElement( 'afterend', live );

		function currentFilters() {
			var form = document.querySelector( '.spr-filters' );
			var data = {};
			if ( ! form ) {
				return data;
			}
			new FormData( form ).forEach( function ( value, key ) {
				var cleanKey = key.replace( '[]', '' );
				if ( key.indexOf( '[]' ) !== -1 ) {
					data[ cleanKey ] = data[ cleanKey ] || [];
					data[ cleanKey ].push( value );
				} else {
					data[ cleanKey ] = value;
				}
			} );
			return data;
		}

		button.addEventListener( 'click', function () {
			if ( page >= maxPages ) {
				return;
			}
			page += 1;
			button.disabled = true;
			button.textContent = sprData.i18n.loading;

			var body = new FormData();
			body.append( 'action', config.action );
			body.append( 'nonce', config.nonce );
			body.append( 'page', page );
			if ( config.context && config.context.taxonomy ) {
				body.append( 'context_taxonomy', config.context.taxonomy );
				body.append( 'context_term', config.context.term );
			}
			var filters = currentFilters();
			Object.keys( filters ).forEach( function ( key ) {
				var value = filters[ key ];
				if ( Array.isArray( value ) ) {
					value.forEach( function ( v ) {
						body.append( 'filters[' + key + '][]', v );
					} );
				} else {
					body.append( 'filters[' + key + ']', value );
				}
			} );

			fetch( config.ajaxUrl, { method: 'POST', credentials: 'same-origin', body: body } )
				.then( function ( response ) {
					return response.json();
				} )
				.then( function ( json ) {
					if ( json && json.success ) {
						grid.insertAdjacentHTML( 'beforeend', json.data.html );
						live.textContent = sprData.i18n.loading;
						if ( ! json.data.has_more ) {
							button.remove();
							live.textContent = sprData.i18n.noMore;
						} else {
							button.disabled = false;
							button.textContent = button.dataset.label || button.textContent;
						}
					} else {
						window.location.reload();
					}
				} )
				.catch( function () {
					window.location.reload();
				} );
		} );
	}

	/**
	 * Single property gallery: thumbnail buttons swap the main image,
	 * arrow keys move between thumbnails.
	 */
	function initGallery() {
		var gallery = document.querySelector( '.spr-gallery' );
		if ( ! gallery ) {
			return;
		}
		var main = gallery.querySelector( '.spr-gallery__main img' );
		var thumbs = Array.prototype.slice.call( gallery.querySelectorAll( '.spr-gallery__thumb' ) );
		if ( ! main || ! thumbs.length ) {
			return;
		}

		function activate( index ) {
			var thumb = thumbs[ index ];
			if ( ! thumb ) {
				return;
			}
			main.src = thumb.dataset.full;
			if ( thumb.dataset.srcset ) {
				main.srcset = thumb.dataset.srcset;
			}
			main.alt = thumb.dataset.alt || '';
			thumbs.forEach( function ( t, i ) {
				t.setAttribute( 'aria-current', i === index ? 'true' : 'false' );
			} );
			thumb.focus();
		}

		thumbs.forEach( function ( thumb, index ) {
			thumb.addEventListener( 'click', function () {
				activate( index );
			} );
			thumb.addEventListener( 'keydown', function ( event ) {
				if ( 'ArrowRight' === event.key ) {
					activate( ( index + 1 ) % thumbs.length );
				} else if ( 'ArrowLeft' === event.key ) {
					activate( ( index - 1 + thumbs.length ) % thumbs.length );
				}
			} );
		} );
	}

	document.addEventListener( 'DOMContentLoaded', function () {
		initNavToggle();
		initPurposeBudgetSwap();
		initFilters();
		initSortSelect();
		initLoadMore();
		initGallery();
	} );
} )();
