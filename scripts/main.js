(function($) {

	$(document).on('ready', function() {

		var actions = {

			addEmoji: function(e) {
				e.preventDefault();
				var $this = $(this);
				var $appendTarget = $($this.data('append'));
				var $target = $appendTarget.find('tr:last-of-type');
				var $clone = $target.clone();
				$clone.find('input[type="text"]').val('');
				$clone.find('input[type="number"]').val('1');
				$appendTarget.append($clone);
				$clone.removeAttr('id');
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