(function($) {

	$(document).on('ready', function() {

		var source = $('#emojipinions__adminHtml').html();
		var template = Handlebars.compile(source);


		var actions = {

			addEmoji: function(e) {
				e.preventDefault();
				var $this = $(this);
				var $appendTarget = $($this.data('append'));
				var html = template({});
				$appendTarget.append(html);
				$appendTarget.find('input[name="_emoji[]"]').emojiPicker({
					height: '200px',
					width: '250px'
				});
			},

			removeEmoji: function(e) {
				e.preventDefault();
				var $this = $(this);
				var $target = $this.closest('tr');
				$target.remove();
			}

		};

		$('body').on('click', '[data-action]', function() {
			var action = $(this).data('action');
			if (action in actions) {
				actions[action].apply(this, arguments);
			}
		});

		$('input[name="_emoji[]"]').emojiPicker({
			height: '200px',
			width: '250px'
		});

	});

})(jQuery);