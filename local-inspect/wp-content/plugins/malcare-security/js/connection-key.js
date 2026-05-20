(function() {
	document.addEventListener('DOMContentLoaded', function() {
		var section = document.getElementById('mc-show-connection-key');
		var toggleLink = document.getElementById('mc-show-connection-key-link');
		var keyField = document.getElementById('mc-connection-key');
		var viewButton = document.getElementById('mc-view-connection-key');
		var copyButton = document.getElementById('mc-copy-connection-key');
		if (!section || !keyField || !viewButton || !copyButton) {
			return;
		}

		if (toggleLink) {
			toggleLink.addEventListener('click', function(event) {
				event.preventDefault();
				section.style.display = 'block';
				toggleLink.style.display = 'none';
			});
		}

		viewButton.addEventListener('click', function() {
			if (keyField.type === 'password') {
				keyField.type = 'text';
				viewButton.textContent = 'Hide Key';
			} else {
				keyField.type = 'password';
				viewButton.textContent = 'View Key';
			}
		});

		copyButton.addEventListener('click', function() {
			var previousType = keyField.type;
			keyField.type = 'text';

			var updateCopyState = function() {
				copyButton.textContent = 'Copied!';
				setTimeout(function() {
					copyButton.textContent = 'Copy Key';
				}, 2000);
			};

			var restoreField = function() {
				keyField.type = previousType;
			};

			if (navigator.clipboard && navigator.clipboard.writeText) {
				navigator.clipboard.writeText(keyField.value).then(function() {
					updateCopyState();
				}).finally(function() {
					restoreField();
				});
				return;
			}

			keyField.select();
			document.execCommand('copy');
			updateCopyState();
			restoreField();
		});
	});
})();
