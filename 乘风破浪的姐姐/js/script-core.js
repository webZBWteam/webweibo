(function($){
	"use strict";

	var ztudiox_display = 'desktop';
	if( typeof(window.matchMedia) == 'function' ){
		$(window).on('resize ztudiox-set-display', function(){
			if( window.matchMedia('(max-width: 419px)').matches ){
				ztudiox_display = 'mobile-portrait';
			}else if( window.matchMedia('(max-width: 767px)').matches ){
				ztudiox_display = 'mobile-landscape'
			}else if( window.matchMedia('(max-width: 959px)').matches ){
				ztudiox_display = 'tablet'
			}else{
				ztudiox_display = 'desktop';
			}
		});
		$(window).trigger('ztudiox-set-display');
	}else{
		$(window).on('resize ztudiox-set-display', function(){
			if( $(window).innerWidth() <= 419 ){
				ztudiox_display = 'mobile-portrait';
			}else if( $(window).innerWidth() <= 767 ){
				ztudiox_display = 'mobile-landscape'
			}else if( $(window).innerWidth() <= 959 ){
				ztudiox_display = 'tablet'
			}else{
				ztudiox_display = 'desktop';
			}
		});
		$(window).trigger('ztudiox-set-display');
	}

	// ref : http://unscriptable.com/2009/03/20/debouncing-javascript-methods/
	// ensure 1 is fired
	var ztudiox_debounce = function(func, threshold, execAsap){
		
		var timeout;

		return function debounced(){
			
			var obj = this, args = arguments;
			
			function delayed(){
				if( !execAsap ){
					func.apply(obj, args);
				}
				timeout = null;
			};

			if( timeout ){
				clearTimeout(timeout);
			}else if( execAsap ){
				func.apply(obj, args);
			}
			timeout = setTimeout(delayed, threshold);
		};
	}	
	
	// reduce the event occurance
	var ztudiox_throttling = function(func, threshold){
		
		var timeout;

		return function throttled(){
			var obj = this, args = arguments;
			
			function delayed(){
				func.apply(obj, args);
				timeout = null;
			};

			if( !timeout ){
				timeout = setTimeout(delayed, threshold);
			}
		};
	}	

	/////////////////////////
	// menu handle function
	/////////////////////////
	var ztudiox_sf_menu = function( menu ){

		if( menu.length == 0 ) return;

		this.main_menu = menu;

		this.slide_bar = this.main_menu.children('.ztudiox-navigation-slide-bar');
		this.slide_bar_offset = '3';

		this.current_menu = this.main_menu.children('.sf-menu').children('.current-menu-item, .current-menu-ancestor').children('a');
		
		this.init();
		
	} // ztudiox_sf_menu

	ztudiox_sf_menu.prototype = {
		
		init: function(){
			
			var t = this;
			
			// sf menu mod
			t.sf_menu_mod();
			
			// init superfish menu
			if(typeof($.fn.superfish) == 'function'){
				t.main_menu.superfish({ delay: 400, speed: 'fast' });	
				
				t.sf_menu_position();
				$(window).resize(ztudiox_debounce(function(){
					t.sf_menu_position();
				}, 300));
			}
			
			// init the slidebar
			if( t.slide_bar.length > 0 ){
				t.init_slidebar();
			}
			
		}, // init
		
		sf_menu_mod: function(){
			
			// create the mega menu script
			this.main_menu.find('.sf-mega > ul').each(function(){	
				var mega_content = $('<div></div>');
				var mega_row = $('<div class="sf-mega-section-wrap" ></div>');
				var mega_column_size = 0;
				
				$(this).children('li').each(function(){
					var column_size = parseInt($(this).attr('data-size'));
					if( mega_column_size + column_size  <= 60 ){
						mega_column_size += column_size;
					}else{	
						mega_column_size = column_size;
						mega_content.append(mega_row);
						mega_row = $('<div class="sf-mega-section-wrap" ></div>');
					}
					
					mega_row.append( $('<div class="sf-mega-section" ></div>')
						.addClass('ztudiox-column-' + column_size)
						.html( $('<div class="sf-mega-section-inner" ></div>')
							.addClass($(this).attr('class'))
							.attr('id', $(this).attr('id'))
							.html($(this).html())
						)
					);
				});
				
				mega_content.append(mega_row);
				$(this).replaceWith(mega_content.html());
			});
			
		}, // sf_menu_mod
		
		sf_menu_position: function(){

			if( ztudiox_display == 'mobile-landscape' || ztudiox_display == 'mobile-portrait' || ztudiox_display == 'tablet' ) return;

			// submenu of normal menu
			var body_wrapper = $('.ztudiox-body-wrapper');
			var sub_normal_menu = this.main_menu.find('.sf-menu > li.ztudiox-normal-menu .sub-menu');
			
			sub_normal_menu.css({display: 'block'}).removeClass('sub-menu-right');
			sub_normal_menu.each(function(){
				if( $(this).offset().left + $(this).width() > body_wrapper.outerWidth() ){
					$(this).addClass('sub-menu-right');
				}
			});
			sub_normal_menu.css({display: 'none'});
			
			// submenu of mega menu
			this.main_menu.find('.sf-menu > li.ztudiox-mega-menu .sf-mega').each(function(){
				if( !$(this).hasClass('sf-mega-full') ){
					
					$(this).css({ display: 'block' });
					
					// set the position
					$(this).css({ right: '', 'margin-left': -(($(this).width() - $(this).parent().outerWidth()) / 2) });
					
					// if exceed the screen
					if( $(this).offset().left + $(this).width() > $(window).width() ){
						$(this).css({ right: 0, 'margin-left': '' });
					}
					
					$(this).css({ display: 'none' });
				}
				
			});
			
		}, // sf_menu_position
		
		init_slidebar: function(){
			
			var t = this;
			
			t.animate_slidebar(t.current_menu);
			$(window).load(function(){ t.animate_slidebar(t.current_menu); });
			
			// animate slidebar 
			t.main_menu.children('.sf-menu').children('li').on({
				mouseenter: function(){
					var nav_element = $(this).children('a');
					t.animate_slidebar(nav_element);
				}, 
				mouseleave: function(){
					t.animate_slidebar(t.current_menu);
				}
			});
			
			// window resize event
			$(window).on('resize', function(){ t.animate_slidebar(t.current_menu); });
			$(window).on('ztudiox-navigation-slider-bar-init', function(){ 
				t.current_menu = t.main_menu.children('.sf-menu').children('.current-menu-item, .current-menu-ancestor').children('a');
				t.animate_slidebar(t.current_menu); 
			});
			$(window).on('ztudiox-navigation-slider-bar-animate', function(){ t.animate_slidebar(t.current_menu); });
			
		}, // init_slidebar

		animate_slidebar: function( target ){
			var t = this;

			if( !target.length ){
				t.slide_bar.css({ width: 0 });
				return;
			}

			t.slide_bar.css({ 
				width: 0, 
				top: target.position().top + (target.height() * 0.6), 
				left: target.position().left + (target.outerWidth() / 2),
				display: 'block'
			});

			t.slide_bar.animate({ 
				width: target.outerWidth() + (2 * t.slide_bar_offset),
				left: target.position().left - t.slide_bar_offset
			}, { duration: 200, queue: false });
		},
		
	}; // ztudiox_sf_menu.prototype
	
	/////////////////////////
	// mobile menu
	/////////////////////////
	$.fn.ztudiox_mobile_menu = function( args ){
		
		var menu_button = $(this).siblings('.ztudiox-mm-menu-button');
		var options = {
			navbar: { title: '<span class="mmenu-custom-close" ></span>' },
			extensions: [ 'pagedim-black' ],

		};
		var extensions = { 
			offCanvas: { pageNodetype: '.ztudiox-body-outer-wrapper' } 
		};

		// remove the wrap for submenu
		$(this).find('a[href="#"]').each(function(){
			var content = $(this).html();
			$('<span class="ztudiox-mm-menu-blank" ></span>').html(content).insertBefore($(this));
			$(this).remove();
		});
		
		if( $(this).attr('data-slide') ){
			var html_class = 'ztudiox-mmenu-' + $(this).attr('data-slide');
			$('html').addClass(html_class);

			options.offCanvas = { position : $(this).attr('data-slide') };
		}		
		
		$(this).mmenu(options, extensions);

		var menu_api = $(this).data('mmenu');
		$(this).find('a').not('.mm-next, .mm-prev').on('click', function(){
			menu_api.close();
		});
		$(this).find('.mmenu-custom-close').on('click', function(){
			menu_api.close();
		});
		$(window).resize(function(){
			menu_api.close();
		});

		// add class active to button
		menu_api.bind('open', function($panel){
			menu_button.addClass('ztudiox-active');
		});
		menu_api.bind('close', function($panel){
			menu_button.removeClass('ztudiox-active');
		});

	}	

	/////////////////////////
	// overlay menu
	/////////////////////////
	var ztudiox_overlay_menu = function( menu ){

		this.menu = menu;
		this.menu_button = menu.children('.ztudiox-overlay-menu-icon');
		this.menu_content = menu.children('.ztudiox-overlay-menu-content');
		this.menu_close = this.menu_content.children('.ztudiox-overlay-menu-close');

		this.init();
	}
	ztudiox_overlay_menu.prototype = {
		
		init: function(){

			var t = this;

			// add transition delay for each menu
			var delay_count = 0;
			t.menu_content.appendTo('body');
			t.menu_content.find('ul.menu > li').each(function(){
				$(this).css('transition-delay', (delay_count * 150) + 'ms');

				delay_count++;
			});

			// bind the menu button
			t.menu_button.on('click', function(){
				$(this).addClass('ztudiox-active');

				t.menu_content.fadeIn(200, function(){
					$(this).addClass('ztudiox-active');
				});

				return false;
			});

			// bind the menu close button
			t.menu_close.on('click', function(){
				t.menu_button.removeClass('ztudiox-active');

				t.menu_content.fadeOut(400, function(){
					$(this).removeClass('ztudiox-active');
				});
				t.menu_content.find('.sub-menu').slideUp(200).removeClass('ztudiox-active');

				return false;
			});

			// menu item click
			t.menu_content.find('a').on('click', function(e){ 
				var sub_menu = $(this).siblings('.sub-menu');
				if( sub_menu.length > 0 ){
					if( !sub_menu.hasClass('ztudiox-active') ){
						var prev_active = sub_menu.closest('li').siblings().find('.sub-menu.ztudiox-active');
						if( prev_active.length > 0 ){
							prev_active.removeClass('ztudiox-active').slideUp(150);
							sub_menu.delay(150).slideDown(400, 'easeOutQuart').addClass('ztudiox-active');
						}else{
							sub_menu.slideDown(400, 'easeOutQuart').addClass('ztudiox-active');
						}

						$(this).addClass('ztudiox-no-preload');
						return false;
					}else{
						$(this).removeClass('ztudiox-no-preload');
					}
				}else{
					t.menu_close.trigger('click');
				}
			});

		}

	}; // ztudiox_overlay_menu.prototype

	/////////////////////////
	// header side navigation
	/////////////////////////
	var ztudiox_header_side_nav = function( side_nav ){

		if( side_nav.length == 0 ) return;

		this.prev_scroll = 0;

		this.side_nav = side_nav;
		this.side_nav_content = side_nav.children();

		this.init();

	} // ztudiox_header_side_nav

	ztudiox_header_side_nav.prototype = {

		init: function(){

			var t = this;

			t.init_nav_bar_element();

			$(window).resize(function(){ 
				t.init_nav_bar_element();
			});
			
			$(window).scroll(function(){

				if( ztudiox_display == 'mobile-landscape' || ztudiox_display == 'mobile-portrait' || ztudiox_display == 'tablet' ) return;

				// if content longer than screen height
				if( t.side_nav.hasClass('ztudiox-allow-slide') ){

					var admin_bar_height = parseInt($('html').css('margin-top'));
					var scroll_down = ($(window).scrollTop() > t.prev_scroll);
					t.prev_scroll = $(window).scrollTop();

					// if scroll down
					if( scroll_down ){

						if( !t.side_nav.hasClass('ztudiox-fix-bottom') ){
							if( t.side_nav.hasClass('ztudiox-fix-top') ){
								t.side_nav.css('top', t.side_nav.offset().top);
								t.side_nav.removeClass('ztudiox-fix-top');

							}else if( $(window).height() + $(window).scrollTop() > t.side_nav_content.offset().top + t.side_nav_content.outerHeight() ){ 
								if( !t.side_nav.hasClass('ztudiox-fix-bottom') ){
									t.side_nav.addClass('ztudiox-fix-bottom');
									t.side_nav.css('top', '');
								}
							}
						}

					// if scroll up
					}else{

						if( !t.side_nav.hasClass('ztudiox-fix-top') ){
							if( t.side_nav.hasClass('ztudiox-fix-bottom') ){
								var top_pos = $(window).scrollTop() + ($(window).height() - admin_bar_height) - t.side_nav_content.outerHeight();
								t.side_nav.css('top', top_pos);
								t.side_nav.removeClass('ztudiox-fix-bottom');

							}else if( $(window).scrollTop() + admin_bar_height < t.side_nav_content.offset().top ){ 
								if( !t.side_nav.hasClass('ztudiox-fix-top') ){
									t.side_nav.addClass('ztudiox-fix-top');
									t.side_nav.css('top', '');
								}
							}
						}
					
					}

				}
			});

		},

		init_nav_bar_element: function(){

			if( ztudiox_display == 'mobile-landscape' || ztudiox_display == 'mobile-portrait' || ztudiox_display == 'tablet' ) return;

			var t = this;
			var middle_pos = t.side_nav_content.children('.ztudiox-pos-middle').addClass('ztudiox-active');
			var bottom_pos = t.side_nav_content.children('.ztudiox-pos-bottom').addClass('ztudiox-active');

			// remove all additional space
			t.side_nav_content.children('.ztudiox-pre-spaces').remove();

			// add class depends on the screen size/content
			if( $(window).height() < t.side_nav_content.height() ){
				t.side_nav.addClass('ztudiox-allow-slide');
			}else{
				t.side_nav.removeClass('ztudiox-allow-slide ztudiox-fix-top ztudiox-fix-bottom').css('top', '');

				// set the middle position
				if( t.side_nav.hasClass('ztudiox-style-middle') ){
					middle_pos.each(function(){
						var top_padding = parseInt($(this).css('padding-top'));
						var prespace = ((t.side_nav.height() - (t.side_nav_content.height() - top_padding)) / 2) - top_padding;

						if( prespace > 0 ){
							$('<div class="ztudiox-pre-spaces" ></div>').css('height', prespace).insertBefore($(this));
						}
					});
				}

				// set the bottom position
				bottom_pos.each(function(){
					var prespace = t.side_nav.height() - t.side_nav_content.height();

					if( prespace > 0 ){
						$('<div class="ztudiox-pre-spaces" ></div>').css('height', prespace).insertBefore($(this));
					}
				});

			}
		}

	}; // ztudiox_sf_menu.prototype

	/////////////////////////
	// anchoring
	/////////////////////////	

	var ztudiox_anchor = function(){

		this.anchor_link = $('a[href*="#"]').not('[href="#"]').filter(function(){

			// for mm-menu plugin
			if( $(this).is('.ztudiox-mm-menu-button, .mm-next, .mm-prev, .mm-title, .gdlr-core-ilightbox') ){
				return false;
			}

			// for additional plugins
			if( $(this).is('.fbx-btn-transition') ){
				return false;
			}

			// for woocommerce
			if( $(this).parent('.description_tab, .reviews_tab').length || $(this).not('[class^="ztudiox"]').closest('.woocommerce').length ){
				return false;
			}

			return true;
		});

		if( this.anchor_link.length ){
			this.menu_anchor = $('#ztudiox-main-menu, #ztudiox-bullet-anchor');
			this.home_anchor = this.menu_anchor.find('ul.sf-menu > li.current-menu-item > a, ul.sf-menu > li.current-menu-ancestor > a, .ztudiox-bullet-anchor-link.current-menu-item');

			this.init();
		}
	}
	ztudiox_anchor.prototype = {

		init: function(){

			var t = this;

			t.animate_anchor();
			t.scroll_section();

			// init bullet anchor height
			t.menu_anchor.filter('#ztudiox-bullet-anchor').each(function(){
				$(this).css('margin-top', - t.menu_anchor.height() / 2).addClass('ztudiox-init');
			});

			// initialize if the page hash exists
			// wait for all element to initialize ( eg. flexslider )
			var url_hash = window.location.hash;
			if( url_hash ){
				setTimeout(function(){

					var current_menu = t.menu_anchor.find('a[href*="' + url_hash + '"]');
					if( !current_menu.is('.current-menu-item, .current-menu-ancestor') ){
						current_menu.addClass('current-menu-item').siblings().removeClass('current-menu-item current-menu-ancestor');

						$(window).trigger('ztudiox-navigation-slider-bar-init');
					}

					t.scroll_to(url_hash, false, 300);
				}, 500);
			}
	
		},

		animate_anchor: function(){

			var t = this;

			// home anchor
			t.home_anchor.on('click', function(){

				if( window.location.href == this.href ){
					$('html, body').animate({ scrollTop: 0 }, { duration: 1500, easing: 'easeOutQuart' });
					return false;
				}
				
			});

			// normal anchor
			t.anchor_link.on('click', function() {
				if( location.hostname == this.hostname && location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') ){
					return t.scroll_to(this.hash, true);
				}
			});

		}, // animate anchor

		scroll_to: function( hash, redirect, duration ){

			// start scrolling
			if( hash == '#ztudiox-top-anchor' ){
				var scroll_position = 0;
			}else{
				
				var target = $(hash);

				if( target.length ){
					var scroll_position = target.offset().top;
				}
			}

			if( typeof(scroll_position) != 'undefined' ){

				// offset for wordpress admin bar
				scroll_position = scroll_position - parseInt($('html').css('margin-top'));

				// offset for fixed nav bar
				if( ztudiox_display == 'mobile-portrait' || ztudiox_display == 'mobile-landscape' ){
					scroll_position = scroll_position - 75;
				}else if( typeof(window.ztudiox_anchor_offset) != 'undefined' ){
					scroll_position = scroll_position - parseInt(window.ztudiox_anchor_offset);
				}

				if( scroll_position < 0 ) scroll_position = 0;

				$('html, body').animate({ scrollTop: scroll_position }, { duration: 1500, easing: 'easeOutQuart', queue: false });

				return false;

			}else if( redirect ){

				window.location.href = ztudiox_script_core.home_url + hash;

				return false;
			}

		}, // scroll to

		scroll_section: function(){

			var t = this;

			// have anchor in anchor menu
			var menu_link_anchor = this.menu_anchor.find('a[href*="#"]').not('[href="#"]');
			if( !menu_link_anchor.length ){ return; }

			// get anchor section
			var home_anchor_section = $('#ztudiox-page-wrapper');
			var anchor_section = home_anchor_section.find('div[id], section[id]');
			if( !anchor_section.length ){ return; }

			// add data for faster query
			menu_link_anchor.each(function(){
				if( $(this).closest('.sub-menu').length == 0 && $(this.hash).length ){
					$(this).attr('data-anchor', this.hash);
				}
			});

			// check section on scroll event
			$(window).scroll(function(){

				if( ztudiox_display == 'mobile-landscape' || ztudiox_display == 'mobile-portrait' || ztudiox_display == 'tablet' ) return;
				
				if( t.home_anchor.length && $(window).scrollTop() <= home_anchor_section.offset().top ){

					t.home_anchor.each(function(){
						if( $(this).hasClass('ztudiox-bullet-anchor-link') ){
							$(this).addClass('current-menu-item').siblings().removeClass('current-menu-item');
							$(this).parent('.ztudiox-bullet-anchor').attr('data-anchor-section', 'ztudiox-home');
						}else if( !$(this).parent('.current-menu-item, .current-menu-ancestor').length ){
							$(this).parent().addClass('current-menu-item').siblings().removeClass('current-menu-item current-menu-ancestor');
							$(window).trigger('ztudiox-navigation-slider-bar-init');
						}			
					});

				}else{
					var section_position = $(window).scrollTop() + ($(window).height() / 2);

					anchor_section.each(function(){
						if( $(this).css('display') == 'none' ) return;
						
						var top_offset_pos = $(this).offset().top;

						if( (section_position > top_offset_pos) && (section_position <  top_offset_pos + $(this).outerHeight()) ){
							var section_id = $(this).attr('id');
							menu_link_anchor.filter('[data-anchor="#' +  section_id + '"]').each(function(){
								if( $(this).hasClass('ztudiox-bullet-anchor-link') ){
									$(this).addClass('current-menu-item').siblings().removeClass('current-menu-item');
									$(this).parent('.ztudiox-bullet-anchor').attr('data-anchor-section', section_id);
								}else if( $(this).parent('li.menu-item').length && !$(this).parent('li.menu-item').is('.current-menu-item, .current-menu-ancestor') ){
									$(this).parent('li.menu-item').addClass('current-menu-item').siblings().removeClass('current-menu-item current-menu-ancestor');
									$(window).trigger('ztudiox-navigation-slider-bar-init');
								}
							});
							
							return false;
						}

					});
				}

			});

		} // scroll section

	};

	var ztudiox_sticky_navigation = function(){

		var t = this;

		// get navigation
		t.sticky_nav = $('.ztudiox-with-sticky-navigation .ztudiox-sticky-navigation');
		t.mobile_menu = $('#ztudiox-mobile-header');

		// set the anchor offset
		if( t.sticky_nav.hasClass('ztudiox-sticky-navigation-height') ){
			window.ztudiox_anchor_offset = t.sticky_nav.outerHeight();
			$(window).resize(function(){
				window.ztudiox_anchor_offset = t.sticky_nav.outerHeight();
			});
		}else if( t.sticky_nav.attr('data-navigation-offset') ){
			window.ztudiox_anchor_offset = parseInt(t.sticky_nav.attr('data-navigation-offset'));
		}else if( t.sticky_nav.length ){
			window.ztudiox_anchor_offset = 75;
		}

		// init the sticky navigation
		if( t.sticky_nav.length ){
			t.init(); 
		}

		if( t.mobile_menu.hasClass('ztudiox-sticky-mobile-navigation') ){
			t.style_mobile_slide();
			$(window).trigger('ztudiox-set-sticky-mobile-navigation'); 
		}

	}
	ztudiox_sticky_navigation.prototype = {
		
		init: function(){

			var t = this;

			if( t.sticky_nav.hasClass('ztudiox-style-fixed') ){
				t.style_fixed();
			}else if( t.sticky_nav.hasClass('ztudiox-style-slide') ){
				t.style_slide();
			}

			$(window).trigger('ztudiox-set-sticky-navigation'); 
		},

		style_fixed: function(){

			var t = this;
			var placeholder = $('<div class="ztudiox-sticky-menu-placeholder" ></div>');

			$(window).on('scroll ztudiox-set-sticky-navigation', function(){

				if( ztudiox_display == 'mobile-landscape' || ztudiox_display == 'mobile-portrait' || ztudiox_display == 'tablet' ) return;

				var page_offset = parseInt($('html').css('margin-top'));

				if( !t.sticky_nav.hasClass('ztudiox-fixed-navigation') ){

					if( $(window).scrollTop() + page_offset > t.sticky_nav.offset().top ){
						if( !t.sticky_nav.hasClass('ztudiox-without-placeholder') ){
							placeholder.height(t.sticky_nav.outerHeight());
						}
						placeholder.insertAfter(t.sticky_nav);
						$('body').append(t.sticky_nav);
						t.sticky_nav.addClass('ztudiox-fixed-navigation');
						
						setTimeout(function(){ t.sticky_nav.addClass('ztudiox-animate-fixed-navigation'); }, 10);	
						setTimeout(function(){ 
							t.sticky_nav.css('height', ''); 
							$(window).trigger('ztudiox-navigation-slider-bar-animate');
						}, 200);
					}
				}else{

					if( $(window).scrollTop() + page_offset <= placeholder.offset().top ){
						if( !t.sticky_nav.hasClass('ztudiox-without-placeholder') ){
							t.sticky_nav.height(placeholder.height());
						}
						t.sticky_nav.insertBefore(placeholder);
						t.sticky_nav.removeClass('ztudiox-fixed-navigation');
						placeholder.remove();

						setTimeout(function(){ t.sticky_nav.removeClass('ztudiox-animate-fixed-navigation'); }, 10);	
						setTimeout(function(){ 
							t.sticky_nav.css('height', ''); 
							$(window).trigger('ztudiox-navigation-slider-bar-animate');
						}, 200);
					}
				}
			});

		}, // style_fixed

		style_slide: function(){

			var t = this;
			var placeholder = $('<div class="ztudiox-sticky-menu-placeholder" ></div>');

			$(window).on('scroll ztudiox-set-sticky-navigation', function(){

				if( ztudiox_display == 'mobile-landscape' || ztudiox_display == 'mobile-portrait' || ztudiox_display == 'tablet' ){ return; }

				var page_offset = parseInt($('html').css('margin-top'));

				if( !t.sticky_nav.hasClass('ztudiox-fixed-navigation') ){

					if( $(window).scrollTop() + page_offset > t.sticky_nav.offset().top + t.sticky_nav.outerHeight() + 200 ){
						
						if( !t.sticky_nav.hasClass('ztudiox-without-placeholder') ){
							placeholder.height(t.sticky_nav.outerHeight());
						}
						placeholder.insertAfter(t.sticky_nav);
						t.sticky_nav.css('display', 'none');

						$('body').append(t.sticky_nav);
						t.sticky_nav.addClass('ztudiox-fixed-navigation ztudiox-animate-fixed-navigation');
						t.sticky_nav.slideDown(200);
						$(window).trigger('ztudiox-navigation-slider-bar-animate');
					}
				}else{

					if( $(window).scrollTop() + page_offset <= placeholder.offset().top + placeholder.height() + 200 ){
						var clone = t.sticky_nav.clone();
						clone.insertAfter(t.sticky_nav);
						clone.slideUp(200, function(){ $(this).remove(); });

						t.sticky_nav.insertBefore(placeholder);
						placeholder.remove();
						t.sticky_nav.removeClass('ztudiox-fixed-navigation ztudiox-animate-fixed-navigation');
						t.sticky_nav.css('display', 'block');

						$(window).trigger('ztudiox-navigation-slider-bar-animate');
					}
				}
			});

		}, // style_slide		

		style_mobile_slide: function(){

			var t = this;
			var placeholder = $('<div class="ztudiox-sticky-mobile-placeholder" ></div>');

			$(window).on('scroll ztudiox-set-sticky-mobile-navigation', function(){

				if( ztudiox_display == 'mobile-landscape' || ztudiox_display == 'mobile-portrait' || ztudiox_display == 'tablet' ){

					var page_offset = parseInt($('html').css('margin-top'));

					if( !t.mobile_menu.hasClass('ztudiox-fixed-navigation') ){
						if( $(window).scrollTop() + page_offset > t.mobile_menu.offset().top + t.mobile_menu.outerHeight() + 200 ){
							placeholder.height(t.mobile_menu.outerHeight()).insertAfter(t.mobile_menu);
							$('body').append(t.mobile_menu);
							t.mobile_menu.addClass('ztudiox-fixed-navigation');
							t.mobile_menu.css('display', 'none').slideDown(200);
						}
					}else{
						if( $(window).scrollTop() + page_offset <= placeholder.offset().top + placeholder.height() + 200 ){
							var clone = t.mobile_menu.clone();
							clone.insertAfter(t.mobile_menu);
							clone.slideUp(200, function(){ $(this).remove(); });

							t.mobile_menu.insertBefore(placeholder);
							placeholder.remove();
							t.mobile_menu.removeClass('ztudiox-fixed-navigation');
							t.mobile_menu.css('display', 'block');
						}
					}
				}

			});

		}, // style_slide

	};

	var ztudiox_font_resize = function(){

		this.heading_font = $('h1, h2, h3, h4, h5, h6');

		this.init();

	}
	ztudiox_font_resize.prototype = {

		init: function(){

			var t = this;

			t.resize();
			$(window).on('resize', ztudiox_throttling(function(){
				t.resize();
			}, 100));

		},

		resize: function(){
			
			var t = this;

			if( ztudiox_display == 'mobile-landscape' || ztudiox_display == 'mobile-portrait' ){

				t.heading_font.each(function(){
					if( parseInt($(this).css('font-size')) > 40 ){
						if( !$(this).attr('data-orig-font') ){
							$(this).attr('data-orig-font', $(this).css('font-size')); 
						}

						$(this).css('font-size', '40px');
					}
				});

			// return font to normal
			}else{

				t.heading_font.filter('[data-orig-font]').each(function(){
					$(this).css('font-size', $(this).attr('data-orig-font'));
				});

			}
		}

	};


	////////////////////////////////
	// starting running the script
	////////////////////////////////
	$(document).ready(function(){
	
		// resize font on mobile
		new ztudiox_font_resize();

		// init main navigation menu
		$('#ztudiox-main-menu, #ztudiox-right-menu, #ztudiox-mobile-menu').each(function(){
			if( $(this).hasClass('ztudiox-overlay-menu') ){
				new ztudiox_overlay_menu( $(this) );
			}else if( $(this).hasClass('ztudiox-mm-menu-wrap') ){
				$(this).ztudiox_mobile_menu();
			}else{
				new ztudiox_sf_menu( $(this) );
			}
		});

		$('#ztudiox-top-search, #ztudiox-mobile-top-search').each(function(){

			var search_wrap = $(this).siblings('.ztudiox-top-search-wrap');
			search_wrap.appendTo('body');

			// bind click button
			$(this).on('click', function(){
				search_wrap.fadeIn(200, function(){
					$(this).addClass('ztudiox-active');
				});
			});

			// bind close button
			search_wrap.find('.ztudiox-top-search-close').on('click', function(){
				search_wrap.fadeOut(200, function(){
					$(this).addClass('ztudiox-active');
				});
			});

			// bind search button
			search_wrap.find('.search-submit').on('click', function(){
				if( search_wrap.find('.search-field').val().length == 0 ){
					return false;
				}
			});
		});

		$('#ztudiox-main-menu-cart, #ztudiox-mobile-menu-cart').each(function(){
			
			$(this).on({
				mouseenter: function(){
					$(this).addClass('ztudiox-active ztudiox-animating');
				}, 
				mouseleave: function(){
					var menu_cart = $(this);
					menu_cart.removeClass('ztudiox-active');
					setTimeout(function(){
						menu_cart.removeClass('ztudiox-animating');
					}, 400)
				}
			});
		});

		// additional space for header transparent
		$('.ztudiox-header-boxed-wrap, .ztudiox-header-background-transparent, .ztudiox-navigation-bar-wrap.ztudiox-style-transparent').each(function(){
			var header_transparent = $(this);
			var header_transparent_sub = $('.ztudiox-header-transparent-substitute');

			header_transparent_sub.height(header_transparent.outerHeight());
			$(window).on('load resize', function(){
				header_transparent_sub.height(header_transparent.outerHeight());
			});
		});

		// full screen for 404 not found
		$('body.error404, body.search-no-results').each(function(){

			var wrap = $(this).find('#ztudiox-full-no-header-wrap');
			var body_wrap_offset = parseInt($(this).children('.ztudiox-body-outer-wrapper').children('.ztudiox-body-wrapper').css('margin-bottom'));
			
			var padding = ($(window).height() - wrap.offset().top - wrap.outerHeight() - body_wrap_offset) / 2;
			if( padding > 0 ){
				wrap.css({ 'padding-top': padding, 'padding-bottom': padding });
			}

			$(window).on('load resize', function(){
				wrap.css({ 'padding-top': 0, 'padding-bottom': 0 });
				padding = ($(window).height() - wrap.offset().top - wrap.outerHeight() - body_wrap_offset) / 2;
				if( padding > 0 ){
					wrap.css({ 'padding-top': padding, 'padding-bottom': padding });
				}
			});
		});

		// back to top button
		var back_to_top = $('#ztudiox-footer-back-to-top-button');
		if( back_to_top.length ){ 
			$(window).on('scroll', function(){
				if( $(window).scrollTop() > 300 ){
					back_to_top.addClass('ztudiox-scrolled');
				}else{
					back_to_top.removeClass('ztudiox-scrolled');
				}
			});
		}

		// page preload
		$('body').children('#ztudiox-page-preload').each(function(){
			var page_preload = $(this);
			var animation_time = parseInt(page_preload.attr('data-animation-time'));
			
			$('a[href]').not('[href^="#"], [target="_blank"], .gdlr-core-js, .strip').on('click', function(e){
				if( e.which != 1 || $(this).hasClass('ztudiox-no-preload') ) return;
				
				if( window.location.href != this.href ){
					page_preload.addClass('ztudiox-out').fadeIn(animation_time);
				}
			});
			
			$(window).load(function(){
				page_preload.fadeOut(animation_time);
			});
		});

		// single nav style 2 sync height
		$('body.ztudiox-blog-style-2 .ztudiox-single-nav-area').each(function(){
			var max_height;
			var single_nav_2 = $(this).children();

			max_height = 0;
			single_nav_2.css('min-height', '0px');
			single_nav_2.each(function(){
				if( max_height < $(this).outerHeight() ){
					max_height = $(this).outerHeight();
				}
			});
			single_nav_2.css('min-height', max_height);

			$(window).resize(function(){
				max_height = 0;
				single_nav_2.css('min-height', '0px');
				single_nav_2.each(function(){
					if( max_height < $(this).outerHeight() ){
						max_height = $(this).outerHeight();
					}
				});
				single_nav_2.css('min-height', max_height);
			});
		});
		
	});

	// fix back button for preload
	$(window).on('pageshow', function(event) {
	    if( event.originalEvent.persisted ){
	        $('body').children('#ztudiox-page-preload').each(function(){
				$(this).fadeOut(400);
			});
	    }
	});
	// $(window).on('beforeunload', function(){
	// 	$('body').children('#ztudiox-page-preload').each(function(){
	// 		$(this).fadeOut(400);
	// 	});
	// });

	$(window).load(function(){

		// fixed footer
		$('#ztudiox-fixed-footer').each(function(){
			var fixed_footer = $(this);
			var placeholder = $('<div class="ztudiox-fixed-footer-placeholder" ></div>');

			placeholder.insertBefore(fixed_footer);
			placeholder.height(fixed_footer.outerHeight());
			$('body').css('min-height', $(window).height() - parseInt($('html').css('margin-top'))); // for safari
			$(window).resize(function(){ 
				placeholder.height(fixed_footer.outerHeight()); 
				$('body').css('min-height', $(window).height() - parseInt($('html').css('margin-top')));
			});
		});

		// side navigation bar
		new ztudiox_header_side_nav( $('#ztudiox-header-side-nav') );

		// sticky navigation
		new ztudiox_sticky_navigation();

		// anchoring
		new ztudiox_anchor();

	});

})(jQuery);