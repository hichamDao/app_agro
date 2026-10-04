/**
 * Formulaire de contact Foodmax
 * Envoi AJAX vers admin/contact_control.php, sans rechargement de la page.
 *
 * Deux precautions, identiques a celles de slider.js / nav.js :
 *  - jQuery peut etre charge apres ce script : on lit window.jQuery au moment
 *    du demarrage, jamais dans la closure, et on retente au DOMContentLoaded
 *    puis au load.
 *  - DOMContentLoaded peut ne jamais se declencher si une feuille de style
 *    externe (police Google) ne repond pas. Si le formulaire est deja dans le
 *    DOM, on s'y attache immediatement.
 */

// URL de base du site, calculee pendant le chargement synchrone du script
// (ou document.currentScript est encore valide), a partir de l'emplacement
// de ce fichier dans js/. Fonctionne depuis la racine comme depuis un sous-dossier.
var FM_BASE = (function () {
    var s = document.currentScript;
    return (s && s.src) ? s.src.replace(/\/js\/[^\/]*$/, '/') : '/';
})();

(function () {
	'use strict';

	function init() {
		var $ = window.jQuery;
		if (!$ || !$.fn) { return; }

		var $form = $('#contactform');
		if (!$form.length) { return; }

		var $result   = $('#resultContact');
		var $button   = $form.find('button[type="submit"]');
		var $label    = $button.find('.fm-btn-label');
		var $icon     = $button.find('.fa');
		var labelIdle = $label.text();

		/* Les messages renvoyes par contact_control.php utilisent ces classes ;
		   on y ajoute un style commun pour qu'ils restent lisibles dans la
		   nouvelle mise en page. */
		$result.on('click', '#hide-message', function (e) {
			e.preventDefault();
			$result.empty();
		});

		$form.on('submit', function (e) {
			e.preventDefault();

			/* Validation HTML native avant l'appel : si un champ obligatoire
			   est vide, le navigateur affiche son message et on s'arrete. */
			if (this.checkValidity && !this.checkValidity()) {
				if (this.reportValidity) { this.reportValidity(); }
				return;
			}

			var $btn = $button;
			$btn.prop('disabled', true);
			$label.text('Sending\u2026');
			$icon.removeClass('fa-paper-plane').addClass('fa-spinner fa-spin');

			$.ajax({
				url:  FM_BASE + 'admin/contact_control.php',
				type: 'POST',
				data: $form.serialize(),
				dataType: 'html'
			}).done(function (html) {
				$result.html(html);
				/* Le message de succes est signale par check-message : on peut
				   alors vider le formulaire. */
				if (html.indexOf('check-message"') !== -1) {
					$form.find('input[type="text"], input[type="email"], input[type="tel"], textarea').val('');
				}
			}).fail(function () {
				$result.html(
					'<div class="fm-form-alert fm-form-alert-error" id="message">'
					+ '<a href="#" id="hide-message" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></a>'
					+ '<i class="fa fa-exclamation-circle" aria-hidden="true"></i>'
					+ '<span>Sorry, the message could not be sent. Please try again or call us.</span>'
					+ '</div>'
				);
			}).always(function () {
				$btn.prop('disabled', false);
				$label.text(labelIdle);
				$icon.removeClass('fa-spinner fa-spin').addClass('fa-paper-plane');
			});
		});
	}

	function boot() {
		var $ = window.jQuery;
		if (!$ || !$.fn) { return false; }
		if ($('#contactform').length) { init(); } else { $(init); }
		return true;
	}

	if (!boot()) {
		document.addEventListener('DOMContentLoaded', boot);
		window.addEventListener('load', boot);
	}
})();
