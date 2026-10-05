/**
 * Slider horizontal des produits (accueil).
 *
 * Le defilement lui-meme est fait par le navigateur (CSS scroll-snap) : le
 * glissement au doigt marche donc nativement sur Android, iPhone et tablette.
 * Ce script ajoute seulement les fleches, les points de pagination et la
 * navigation au clavier. Sans JavaScript, le slider reste utilisable en
 * glissant ou avec la barre de defilement.
 *
 * Utilisation : un conteneur [data-fm-slider] avec
 *   .fm-slider-track (les cartes), .fm-slider-prev, .fm-slider-next,
 *   .fm-slider-dots (rempli ici).
 * Le nombre de cartes visibles se regle uniquement dans le CSS (--fm-n).
 */
(function () {
	'use strict';

	var reduce = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function init(root) {
		var track = root.querySelector('.fm-slider-track');
		var prev  = root.querySelector('.fm-slider-prev');
		var next  = root.querySelector('.fm-slider-next');
		var dots  = root.querySelector('.fm-slider-dots');
		if (!track || !track.children.length) { return; }

		var ticking = false;

		function metrics() {
			var card = track.children[0];
			var cs   = window.getComputedStyle(track);
			var gap  = parseFloat(cs.columnGap || cs.gap) || 0;
			var step = card.getBoundingClientRect().width + gap;      /* une carte */
			var per  = Math.max(1, Math.floor((track.clientWidth + gap) / step));
			var max  = Math.max(0, track.scrollWidth - track.clientWidth);
			return { step: step, page: step * per, max: max };
		}

		function go(dir) {
			var m = metrics();
			track.scrollBy({ left: dir * m.page, behavior: reduce ? 'auto' : 'smooth' });
		}

		function goTo(i) {
			var m = metrics();
			track.scrollTo({ left: Math.min(i * m.page, m.max), behavior: reduce ? 'auto' : 'smooth' });
		}

		function buildDots() {
			if (!dots) { return; }
			var m = metrics();
			var n = m.max > 2 ? Math.ceil(m.max / m.page) + 1 : 1;
			dots.innerHTML = '';
			dots.hidden = n < 2;
			for (var i = 0; i < n; i++) {
				var b = document.createElement('button');
				b.type = 'button';
				b.setAttribute('aria-label', 'Go to page ' + (i + 1));
				b.tabIndex = -1;
				(function (k) { b.addEventListener('click', function () { goTo(k); }); })(i);
				dots.appendChild(b);
			}
			dots.setAttribute('aria-hidden', 'false');
		}

		function update() {
			ticking = false;
			var m = metrics();
			var atStart = track.scrollLeft <= 2;
			var atEnd   = track.scrollLeft >= m.max - 2;
			root.classList.toggle('is-static', m.max <= 2);       /* tout tient : pas de fleches */
			if (prev) { prev.disabled = atStart; }
			if (next) { next.disabled = atEnd; }

			if (dots && dots.children.length) {
				var n = dots.children.length;
				var cur = atEnd ? n - 1 : Math.min(n - 1, Math.round(track.scrollLeft / m.page));
				for (var i = 0; i < n; i++) { dots.children[i].classList.toggle('is-on', i === cur); }
			}
		}

		function onScroll() {
			if (!ticking) { ticking = true; window.requestAnimationFrame(update); }
		}

		if (prev) { prev.addEventListener('click', function () { go(-1); }); }
		if (next) { next.addEventListener('click', function () { go(1); }); }

		track.addEventListener('scroll', onScroll, { passive: true });
		track.addEventListener('keydown', function (e) {
			if (e.key === 'ArrowRight') { e.preventDefault(); go(1); }
			if (e.key === 'ArrowLeft')  { e.preventDefault(); go(-1); }
		});

		var t;
		window.addEventListener('resize', function () {
			clearTimeout(t);
			t = setTimeout(function () { buildDots(); update(); }, 120);
		});

		/* les images changent la largeur utile : on recalcule apres chargement */
		window.addEventListener('load', function () { buildDots(); update(); });

		buildDots();
		update();
	}

	function start() {
		var roots = document.querySelectorAll('[data-fm-slider]');
		for (var i = 0; i < roots.length; i++) { init(roots[i]); }
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', start);
	} else {
		start();
	}
})();
