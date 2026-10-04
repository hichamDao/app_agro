/**
 * Formulaire newsletter du pied de page.
 * Envoi AJAX vers admin/subscribe_controle.php.
 *
 * Meme demarrage robuste que slider.js / nav.js / contact.js : jQuery peut etre
 * charge apres ce script, et DOMContentLoaded peut ne jamais se declencher si
 * une feuille de style externe (police Google) ne repond pas.
 */
(function () {
	'use strict';

	function init() {
		var $ = window.jQuery;
		if (!$ || !$.fn) { return; }

		var $form   = $('#fmNewsletterForm');
		var $result = $('#fmNewsletterResult');
		if (!$form.length) { return; }

		var $button = $form.find('button[type="submit"]');
		var labelIdle = $button.text().trim();

		$result.on('click', '#hide-message', function (e) {
			e.preventDefault();
			$result.empty();
		});

		$form.on('submit', function (e) {
			e.preventDefault();

			if (this.checkValidity && !this.checkValidity()) {
				if (this.reportValidity) { this.reportValidity(); }
				return;
			}

			$button.prop('disabled', true);
			$button.html('Sending&hellip;');

			$.ajax({
				url:  $form.attr('action'),
				type: 'POST',
				data: $form.serialize(),
				dataType: 'html'
			}).done(function (html) {
				$result.html(html);
				if (html.indexOf('fm-form-alert-ok') !== -1) {
					$form.find('input[type="email"]').val('');
				}
			}).fail(function () {
				$result.html(
					'<div class="fm-form-alert fm-form-alert-error" id="message">'
					+ '<a href="#" id="hide-message" aria-label="Close"><i class="fa fa-times" aria-hidden="true"></i></a>'
					+ '<i class="fa fa-exclamation-circle" aria-hidden="true"></i>'
					+ '<span>Sorry, your subscription could not be saved. Please try again.</span>'
					+ '</div>'
				);
			}).always(function () {
				$button.prop('disabled', false);
				$button.html(labelIdle + ' <i class="fa fa-arrow-right" aria-hidden="true"></i>');
			});
		});
	}

	function boot() {
		var $ = window.jQuery;
		if (!$ || !$.fn) { return false; }
		if ($('#fmNewsletterForm').length) { init(); } else { $(init); }
		return true;
	}

	if (!boot()) {
		document.addEventListener('DOMContentLoaded', boot);
		window.addEventListener('load', boot);
	}
})();
