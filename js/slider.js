/**
 * Slider "Products we export" — page d'accueil
 * - defilement automatique (data-autoplay, en ms ; 0 pour desactiver)
 * - fleches precedent / suivante
 * - pastilles de navigation generees automatiquement
 * - swipe tactile
 * - pause quand l'onglet est en arriere-plan ou au survol de la souris
 *
 * Deux precautions :
 *  - jQuery peut etre charge apres ce script : on lit window.jQuery au moment
 *    de demarrer et on retente au DOMContentLoaded / load.
 *  - DOMContentLoaded peut ne jamais survenir si une feuille de style externe
 *    (police Google) ne repond pas ; le document resterait alors a readyState
 *    "interactive" et tous les callbacks ready seraient ignores. Si l'element
 *    cible est deja dans le DOM, on demarre donc immediatement.
 */
(function () {
	'use strict';

	function init() {
		var $ = window.jQuery;
		if (!$) { return; }
		var $slider = $('#fmSlider .fm-slider');
		if (!$slider.length) { return; }

		var $track = $('.fm-slider-track', $slider);
		var $slides = $track.children('.fm-slide');
		var $dots = $('.fm-slider-dots', $slider);
		var $prev = $('.fm-slider-prev', $slider);
		var $next = $('.fm-slider-next', $slider);
		var count = $slides.length;
		var index = 0;
		var timer = null;
		var delay = parseInt($slider.data('autoplay'), 10) || 0;

		if (count < 2) {
			$prev.add($next).remove();
			return;
		}

		/* ---------------------------------------------------- pastilles */
		$slides.each(function (i) {
			var $b = $('<button type="button" role="tab"></button>')
				.attr('aria-label', 'Produit ' + (i + 1));
			$b.on('click', function () {
				go(i);
				restart();
			});
			$dots.append($b);
		});
		var $dotItems = $dots.children('button');

		/* ------------------------------------------------------ affichage */
		function render() {
			/* La piste fait 100% du cadre (voir CSS) et chaque slide occupe
			   100% de cette piste : -index * 100% decale donc d'exactement
			   un cadre vers la gauche. */
			$track.css('transform', 'translateX(' + (-index * 100) + '%)');

			$slides.removeClass('is-active');
			$slides.eq(index).addClass('is-active');

			$dotItems.removeClass('is-active').attr('aria-selected', 'false');
			$dotItems.eq(index).addClass('is-active').attr('aria-selected', 'true');
		}

		function go(i) {
			index = ((i % count) + count) % count;
			render();
		}

		function next() { go(index + 1); }
		function prev() { go(index - 1); }

		/* -------------------------------------------------- commandes */
		$next.on('click', function () { next(); restart(); });
		$prev.on('click', function () { prev(); restart(); });

		$slider.on('keydown', function (e) {
			if (e.key === 'ArrowRight') { next(); restart(); }
			if (e.key === 'ArrowLeft')  { prev(); restart(); }
		});

		/* ------------------------------------------------------ swipe */
		var x0 = null;
		$track.on('touchstart', function (e) {
			x0 = e.originalEvent.touches[0].clientX;
		});
		$track.on('touchend', function (e) {
			if (x0 === null) { return; }
			var dx = e.originalEvent.changedTouches[0].clientX - x0;
			if (Math.abs(dx) > 45) { if (dx < 0) { next(); } else { prev(); } restart(); }
			x0 = null;
		});

		/* -------------------------------------------------- autoplay */
		function start() {
			if (!delay) { return; }
			timer = setInterval(function () {
				if (!document.hidden) { next(); }
			}, delay);
		}
		function stop() {
			if (timer) { clearInterval(timer); timer = null; }
		}
		function restart() { stop(); start(); }

		$slider.on('mouseenter', stop).on('mouseleave', start);
		$(document).on('visibilitychange', function () {
			if (document.hidden) { stop(); } else { start(); }
		});

		render();
		start();
	}

	function boot() {
		var $ = window.jQuery;
		if (!$ || !$.fn) { return false; }
		if ($('#fmSlider .fm-slider').length) { init(); } else { $(init); }
		return true;
	}
	if (!boot()) {
		document.addEventListener('DOMContentLoaded', boot);
		window.addEventListener('load', boot);
	}
})();
