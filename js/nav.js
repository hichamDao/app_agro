/**
 * Navigation principale Foodmax
 * - menu burger (mobile)
 * - sous-menu Products : ouverture au clic, fermeture au clic exterieur
 * - barre de recherche : ouverture/fermeture + icone qui change d'etat
 *
 * Deux precautions :
 *  1. jQuery peut etre charge APRES ce script (gallery.php le charge en fin de
 *     <body>) : on interroge window.jQuery au moment de demarrer, jamais a la
 *     closure, et on retente au DOMContentLoaded / load.
 *  2. DOMContentLoaded peut ne jamais survenir si une feuille de style externe
 *     (police Google) ne repond pas : si #fmNav existe deja, on demarre
 *     immediatement sans passer par ready.
 */
(function () {
	'use strict';

	function init() {
		var $ = window.jQuery;
		if (!$) { return; }
		var $nav = $('#fmNav');
		if (!$nav.length) { return; }

		/* Une seule initialisation. Quand jQuery arrive apres ce script
		   (gallery.php le charge en fin de <body>), boot() echoue une premiere
		   fois puis arme un rappel sur DOMContentLoaded ET un autre sur load :
		   sans ce garde-fou init() tourne deux fois, chaque gestionnaire est
		   enregistre deux fois et le clic sur le burger s'annule tout seul. */
		if (init.done) { return true; }
		init.done = true;

		var $burger = $('#fmBurger', $nav);
		var $menu = $('#fmMenu', $nav);
		var $toggle = $('#searchtoggl', $nav);
		var $search = $('#searchbar', $nav);
		var $input = $('#s', $search);

		/* ------------------------------------------------ menu burger */
		$burger.on('click', function (e) {
			e.preventDefault();
			var open = $nav.hasClass('is-open');
			$nav.toggleClass('is-open', !open);
			$(this).attr('aria-expanded', open ? 'false' : 'true');
		});

		/* ------------------------------------------- sous-menu produits */
		/* Ce bouton n'est jamais une navigation : la liste se deroule toujours.
		   Sur grand ecran elle est deja visible au survol (CSS :hover), sur petit
		   ecran le deroulage au toucher est le seul moyen de la faire apparaitre.
		   Le test ne doit pas porter sur $nav.hasClass('is-open') : en mobile ce
		   drapeau est vrai des que le burger est ouvert, donc ilpiait toujours,
		   laissait passer le lien et quittait la page sans derouler la liste.
		   La navigation reste possible via l'entree "All products". */
		$('.fm-sub-toggle', $nav).on('click', function (e) {
			e.preventDefault();
			var $item = $(this).parent();
			var open = $item.hasClass('is-open');
			$('.has-sub', $nav).removeClass('is-open');
			$item.toggleClass('is-open', !open);
			$(this).attr('aria-expanded', open ? 'false' : 'true');
		});

		/* ------------------------------------------- barre de recherche */
		function setSearch(open) {
			$search.toggleClass('is-open', open);
			$toggle.attr('aria-expanded', open ? 'true' : 'false');
			$toggle.find('i')
				.toggleClass('fa-search-minus', open)
				.toggleClass('fa-search', !open);
			if (!open) { return; }
			/* Sur telephone, on ne donne pas le focus : le clavier virtuel
			   recouvrirait la barre et ferait scroller la page. L visiteur
			   tape lui-meme dans le champ, ce qui ouvre le clavier au bon
			   endroit. Sur bureau (souris presente) le focus est agreable et
			   sans risque. */
			if (window.matchMedia && window.matchMedia('(hover: hover)').matches) {
				$input.trigger('focus');
			}
		}

		$toggle.on('click', function (e) {
			e.preventDefault();
			setSearch(!$search.hasClass('is-open'));
		});

		/* --------------------- fermeture au clic hors menu / recherche */
		$(document).on('click', function (e) {
			if (!$(e.target).closest('#fmNav').length) {
				$nav.removeClass('is-open');
				$('.has-sub', $nav).removeClass('is-open');
				$burger.attr('aria-expanded', 'false');
				if ($search.hasClass('is-open')) { setSearch(false); }
			}
		});

		$(document).on('keyup', function (e) {
			if (e.key === 'Escape' || e.keyCode === 27) {
				$nav.removeClass('is-open');
				$('.has-sub', $nav).removeClass('is-open');
				if ($search.hasClass('is-open')) { setSearch(false); }
			}
		});

		/* navigation clavier dans le sous-menu */
		$('.fm-sub a', $nav).on('keydown', function (e) {
			if (e.key === 'Escape' || e.keyCode === 27) {
				$('.has-sub', $nav).removeClass('is-open');
				$('.fm-sub-toggle', $nav).trigger('focus');
			}
		});

		/* referme le menu mobile apres un clic sur un lien */
		$('.fm-nav-list a', $nav).on('click', function (e) {
			/* Le bouton du sous-menu est dans .fm-nav-list mais ne navigate pas :
			   sans cette garde, le menu burger se refermerait juste apres le
			   deroulage et la liste fraichement ouverte disparaitrait aussi. */
			if ($(e.target).closest('.fm-sub-toggle').length) { return; }
			if ($nav.hasClass('is-open')) {
				$nav.removeClass('is-open');
				$burger.attr('aria-expanded', 'false');
			}
		});
	}

	/* Demarrage tolérant : jQuery peut etre charge apres ce script
	   (gallery.php le charge en fin de <body>). On attend le DOM si besoin. */
	function boot() {
		var $ = window.jQuery;
		if (!$ || !$.fn) { return false; }
		if ($('#fmNav').length) { init(); } else { $(init); }
		return true;
	}
	if (!boot()) {
		document.addEventListener('DOMContentLoaded', boot);
		window.addEventListener('load', boot);
	}
})();
