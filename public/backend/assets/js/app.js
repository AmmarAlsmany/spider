$(function() {
	"use strict";
	
	// Initialize PerfectScrollbar only if elements exist
	if ($(".header-message-list").length) {
		new PerfectScrollbar(".header-message-list");
	}
	if ($(".header-notifications-list").length) {
		new PerfectScrollbar(".header-notifications-list");
	}

	// Mobile menu toggle
	$(".mobile-toggle-menu, .overlay").on("click", function() {
		$(".wrapper").toggleClass("toggled");
		$(".overlay").toggleClass("show");
	});

	// Desktop sidebar toggle
	$(".toggle-icon").on("click", function() {
		if ($(window).width() >= 1025) {
			$(".wrapper").toggleClass("toggled");
			
			if ($(".wrapper").hasClass("toggled")) {
				$(".sidebar-wrapper").css("width", "70px");
				$(".sidebar-header").css("width", "70px");
				$(".sidebar-wrapper .sidebar-header .logo-text").hide();
			} else {
				$(".sidebar-wrapper").css("width", "250px");
				$(".sidebar-header").css("width", "250px");
				$(".sidebar-wrapper .sidebar-header .logo-text").show();
			}

			// Setup hover effects
			$(".sidebar-wrapper").hover(
				function() {
					if ($(".wrapper").hasClass("toggled")) {
						$(this).css("width", "250px");
						$(".sidebar-header").css("width", "250px");
						$(".sidebar-wrapper .sidebar-header .logo-text").show();
						$(".wrapper").addClass("sidebar-hovered");
					}
				},
				function() {
					if ($(".wrapper").hasClass("toggled")) {
						$(this).css("width", "70px");
						$(".sidebar-header").css("width", "70px");
						$(".sidebar-wrapper .sidebar-header .logo-text").hide();
						$(".wrapper").removeClass("sidebar-hovered");
					}
				}
			);
		}
	});

	// Handle window resize
	$(window).on('resize', function() {
		if ($(window).width() < 1025) {
			$(".sidebar-wrapper").css("width", "");
			$(".sidebar-header").css("width", "");
			$(".sidebar-wrapper .sidebar-header .logo-text").show();
			$(".sidebar-wrapper").off("mouseenter mouseleave");
			$(".overlay").removeClass("show");
		}
	});

	$(".mobile-search-icon").on("click", function() {
		$(".search-bar").addClass("full-search-bar")
	}),

	$(".search-close").on("click", function() {
		$(".search-bar").removeClass("full-search-bar")
	}),

	// Dark mode toggle is now handled in dark-mode.js
	
	$(document).ready(function() {
		// Back to top button
		$(window).on("scroll", function() {
			$(this).scrollTop() > 300 ? $(".back-to-top").fadeIn() : $(".back-to-top").fadeOut()
		}), $(".back-to-top").on("click", function() {
			return $("html, body").animate({
				scrollTop: 0
			}, 600), !1
		})
	}),
	
	$(function() {
		for (var e = window.location, o = $(".metismenu li a").filter(function() {
				return this.href == e
			}).addClass("").parent().addClass("mm-active"); o.is("li");) o = o.parent("").addClass("mm-show").parent("").addClass("mm-active")
	}),
	
	
	$(function() {
		$("#menu").metisMenu()
	}), 
		
	$(".chat-toggle-btn").on("click", function() {
		$(".chat-wrapper").toggleClass("chat-toggled")
	}), $(".chat-toggle-btn-mobile").on("click", function() {
		$(".chat-wrapper").removeClass("chat-toggled")
	}),


	$(".email-toggle-btn").on("click", function() {
		$(".email-wrapper").toggleClass("email-toggled")
	}), $(".email-toggle-btn-mobile").on("click", function() {
		$(".email-wrapper").removeClass("email-toggled")
	}), $(".compose-mail-btn").on("click", function() {
		$(".compose-mail-popup").show()
	}), $(".compose-mail-close").on("click", function() {
		$(".compose-mail-popup").hide()
	}), 
	
	
	$(".switcher-btn").on("click", function() {
		$(".switcher-wrapper").toggleClass("switcher-toggled")
	}), $(".close-switcher").on("click", function() {
		$(".switcher-wrapper").removeClass("switcher-toggled")
	}), $("#lightmode").on("click", function() {
		$("html").attr("class", "light-theme")
		localStorage.setItem("darkMode", "disabled");
	}), $("#darkmode").on("click", function() {
		$("html").attr("class", "dark-theme")
		localStorage.setItem("darkMode", "enabled");
	}), $("#semidark").on("click", function() {
		$("html").attr("class", "semi-dark")
	}), $("#minimaltheme").on("click", function() {
		$("html").attr("class", "minimal-theme")
	}), $("#headercolor1").on("click", function() {
		$("html").addClass("color-header headercolor1"), $("html").removeClass("headercolor2 headercolor3 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8")
	}), $("#headercolor2").on("click", function() {
		$("html").addClass("color-header headercolor2"), $("html").removeClass("headercolor1 headercolor3 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8")
	}), $("#headercolor3").on("click", function() {
		$("html").addClass("color-header headercolor3"), $("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor7 headercolor8")
	}), $("#headercolor4").on("click", function() {
		$("html").addClass("color-header headercolor4"), $("html").removeClass("headercolor1 headercolor2 headercolor3 headercolor5 headercolor6 headercolor7 headercolor8")
	}), $("#headercolor5").on("click", function() {
		$("html").addClass("color-header headercolor5"), $("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor3 headercolor6 headercolor7 headercolor8")
	}), $("#headercolor6").on("click", function() {
		$("html").addClass("color-header headercolor6"), $("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor3 headercolor7 headercolor8")
	}), $("#headercolor7").on("click", function() {
		$("html").addClass("color-header headercolor7"), $("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor3 headercolor8")
	}), $("#headercolor8").on("click", function() {
		$("html").addClass("color-header headercolor8"), $("html").removeClass("headercolor1 headercolor2 headercolor4 headercolor5 headercolor6 headercolor7 headercolor3")
	})
	
	// sidebar colors 
	$('#sidebarcolor1').click(theme1);
	$('#sidebarcolor2').click(theme2);
	$('#sidebarcolor3').click(theme3);
	$('#sidebarcolor4').click(theme4);
	$('#sidebarcolor5').click(theme5);
	$('#sidebarcolor6').click(theme6);
	$('#sidebarcolor7').click(theme7);
	$('#sidebarcolor8').click(theme8);

	function theme1() {
		$('html').attr('class', 'color-sidebar sidebarcolor1');
	}

	function theme2() {
		$('html').attr('class', 'color-sidebar sidebarcolor2');
	}

	function theme3() {
		$('html').attr('class', 'color-sidebar sidebarcolor3');
	}

	function theme4() {
		$('html').attr('class', 'color-sidebar sidebarcolor4');
	}

	function theme5() {
		$('html').attr('class', 'color-sidebar sidebarcolor5');
	}

	function theme6() {
		$('html').attr('class', 'color-sidebar sidebarcolor6');
	}

	function theme7() {
		$('html').attr('class', 'color-sidebar sidebarcolor7');
	}

	function theme8() {
		$('html').attr('class', 'color-sidebar sidebarcolor8');
	}
	
	
});