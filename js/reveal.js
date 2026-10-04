/**
 * Apparition douce des blocs au defilement (animations legeres).
 *
 * - Aucun balisage a modifier : les blocs sont designes par leurs classes.
 * - Sans IntersectionObserver, ou si l'utilisateur prefere moins d'animations,
 *   le script ne fait rien et tout reste visible.
 * - La classe d'animation est retiree une fois l'effet termine, pour ne pas
 *   gener les survols (hover) des cartes.
 */
(function () {
	'use strict';

	if (!('IntersectionObserver' in window)) { return; }
	if (window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches) { return; }

	var SELECTOR = [
		'.fm-section-head', '.fm-about-body', '.fm-about-media',
		'.fm-philo', '.fm-cat', '.fm-step', '.fm-commit', '.fm-why',
		'.fm-partner', '.fm-topic', '.fm-quality', '.fm-strategy-item',
		'.fm-morocco-map', '.fm-morocco-body', '.fm-mission-text',
		'.fm-mission-body', '.fm-mission-list', '.fm-cta-inner', '.fm-note'
	].join(',');

	function start() {
		var nodes = document.querySelectorAll(SELECTOR);
		if (!nodes.length) { return; }

		var io = new IntersectionObserver(function (entries) {
			entries.forEach(function (e) {
				if (!e.isIntersecting) { return; }
				var el = e.target;
				io.unobserve(el);
				el.classList.add('in');
				setTimeout(function () { el.classList.remove('fm-rv', 'in'); el.style.transitionDelay = ''; }, 1100);
			});
		}, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

		Array.prototype.forEach.call(nodes, function (el) {
			/* Ce qui est deja a l'ecran au chargement n'est pas masque. */
			var r = el.getBoundingClientRect();
			if (r.top < window.innerHeight * 0.9) { return; }

			/* Decalage leger entre freres d'une meme grille (5 max). */
			var idx = Array.prototype.indexOf.call(el.parentNode.children, el);
			el.style.transitionDelay = (Math.min(idx, 5) * 70) + 'ms';
			el.classList.add('fm-rv');
			io.observe(el);
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', start);
	} else {
		start();
	}
})();
