(function () {
	"use strict";

	const config = window.FabricamosNative || {};

	function debounce(callback, delay) {
		let timeoutId = null;

		return function () {
			const args = arguments;
			window.clearTimeout(timeoutId);
			timeoutId = window.setTimeout(function () {
				callback.apply(null, args);
			}, delay);
		};
	}

	function hideSuggestions(container) {
		if (!container) {
			return;
		}

		container.innerHTML = "";
		container.classList.remove("is-visible");
	}

	function requestSuggestions(urlBase, term, options) {
		const requestOptions = options || {};
		const url = new URL(urlBase, window.location.origin);
		url.searchParams.set("search", term);

		return fetch(url.toString(), {
			cache: "no-store",
			credentials: "include",
			signal: requestOptions.signal
		}).then(function (response) {
			return response.ok ? response.json() : [];
		});
	}

	function bindAutocomplete(options) {
		const input = document.querySelector(options.inputSelector);
		const suggestions = document.querySelector(options.suggestionsSelector);

		if (!input || !suggestions || !options.url) {
			return;
		}

		let requestToken = 0;
		let activeController = null;

		const loadSuggestions = debounce(function (term) {
			requestToken += 1;
			const currentToken = requestToken;

			if (activeController) {
				activeController.abort();
			}

			activeController = new AbortController();

			requestSuggestions(options.url, term, { signal: activeController.signal })
				.then(function (items) {
					if (currentToken !== requestToken || input.value.trim() !== term) {
						return;
					}

					if (!Array.isArray(items) || !items.length) {
						hideSuggestions(suggestions);
						return;
					}

					suggestions.innerHTML = "";

					items.forEach(function (item) {
						const button = document.createElement("button");
						button.type = "button";
						button.className = "fab-suggestion";
						button.innerHTML = "<strong></strong><span></span>";
						button.querySelector("strong").textContent = item.title || "";
						const meta = options.metaText(item);
						const metaNode = button.querySelector("span");
						if (meta) {
							metaNode.textContent = meta;
						} else {
							metaNode.remove();
						}
						button.addEventListener("click", function () {
							if (typeof options.onSelect === "function") {
								options.onSelect(item, input, suggestions);
							} else {
								input.value = options.selectValue(item);
								hideSuggestions(suggestions);
								input.focus();
							}
						});
						suggestions.appendChild(button);
					});

					suggestions.classList.add("is-visible");
				})
				.catch(function (error) {
					if (error && error.name === "AbortError") {
						return;
					}

					hideSuggestions(suggestions);
				});
		}, 220);

		function handleInputChange() {
			const term = input.value.trim();

			if (term.length < (options.minLength || 1)) {
				if (activeController) {
					activeController.abort();
					activeController = null;
				}
				hideSuggestions(suggestions);
				return;
			}

			loadSuggestions(term);
		}

		input.addEventListener("input", handleInputChange);
		input.addEventListener("search", handleInputChange);
		input.addEventListener("focus", function () {
			if (suggestions.children.length) {
				suggestions.classList.add("is-visible");
			}
		});

		document.addEventListener("click", function (event) {
			if (!suggestions.contains(event.target) && event.target !== input) {
				hideSuggestions(suggestions);
			}
		});
	}

	function initModal() {
		const modal = document.querySelector(".fab-modal[data-open='1']");
		if (!modal) {
			return;
		}

		const button = modal.querySelector("[data-modal-catalog]");
		if (button) {
			button.addEventListener("click", function () {
				window.location.href = config.catalogUrl || "/";
			});
		}
	}

	function initHeaderUserMenu() {
		const menu = document.querySelector(".jupiterx-header .dsf-user-menu");
		const headerMenu = config.headerMenu || null;

		if (!menu || !headerMenu || !headerMenu.label) {
			return;
		}

		const label = menu.querySelector(".dsf-user-label span");
		if (label) {
			label.textContent = headerMenu.label;
		}

		const trigger = menu.querySelector(".dsf-user-menu__trigger");
		const dropdown = menu.querySelector(".dsf-user-dropdown") || document.createElement("div");
		dropdown.className = "dsf-user-dropdown";
		dropdown.innerHTML = "";

		if (Array.isArray(headerMenu.items) && headerMenu.items.length) {
			headerMenu.items.forEach(function (item) {
				if (!item || !item.url || !item.title) {
					return;
				}

				const link = document.createElement("a");
				link.href = item.url;
				link.textContent = item.title;
				dropdown.appendChild(link);
			});

			if (!dropdown.parentNode) {
				menu.appendChild(dropdown);
			}

			if (trigger) {
				trigger.setAttribute("aria-expanded", "false");
			}
		} else {
			if (dropdown.parentNode) {
				dropdown.parentNode.removeChild(dropdown);
			}
		}
	}

	function createChip(list, id, title, payload) {
		if (!list || !id || list.querySelector("[data-chip-id='" + id + "']")) {
			return;
		}

		const chip = document.createElement("span");
		chip.className = "fab-chip";
		chip.setAttribute("data-chip-id", id);
		chip.innerHTML =
			"<span></span>" +
			'<button type="button" class="fab-chip__remove" aria-label="Remover substância">×</button>' +
			'<input type="hidden" name="fab_substance_payload[]" />';

		chip.querySelector("span").textContent = title;
		chip.querySelector("input").value = JSON.stringify(payload || { display_name: title });
		list.appendChild(chip);
	}

	function initSubstancePicker() {
		const chips = document.querySelector("[data-fab-chip-list]");

		if (!chips || !config.ajaxUrl) {
			return;
		}

		chips.addEventListener("click", function (event) {
			const button = event.target.closest(".fab-chip__remove");
			if (!button) {
				return;
			}

			const chip = button.closest(".fab-chip");
			if (chip) {
				chip.remove();
			}
		});

		bindAutocomplete({
			inputSelector: "[data-fab-substance-search]",
			suggestionsSelector: "[data-fab-suggestions]",
			url: config.ajaxUrl,
			metaText: function (item) {
				return item.meta && item.meta.dcb ? "DCB: " + item.meta.dcb : "Substância DSF";
			},
			onSelect: function (item, input, suggestions) {
				createChip(chips, String(item.id), item.title, item.payload);
				input.value = "";
				hideSuggestions(suggestions);
				input.focus();
			}
		});
	}

	function initCatalogFiltersAutocomplete() {
		bindAutocomplete({
			inputSelector: "[data-fab-company-search]",
			suggestionsSelector: "[data-fab-company-suggestions]",
			url: config.manufacturersUrl,
			metaText: function (item) {
				return "";
			},
			selectValue: function (item) {
				return item.title || "";
			},
			minLength: 1
		});

		bindAutocomplete({
			inputSelector: "[data-fab-catalog-substance-search]",
			suggestionsSelector: "[data-fab-catalog-substance-suggestions]",
			url: config.ajaxUrl,
			metaText: function (item) {
				if (!item.meta) {
					return "Substância DSF";
				}

				const parts = [];

				if (item.meta.dcb) {
					parts.push("DCB: " + item.meta.dcb);
				}

				if (item.meta.inn) {
					parts.push("INN: " + item.meta.inn);
				}

				if (item.meta.cas) {
					parts.push("CAS: " + item.meta.cas);
				}

				return parts.join(" | ");
			},
			selectValue: function (item) {
				return item.title || "";
			},
			minLength: 1
		});
	}

	function initSubstanceAccordion() {
		const items = document.querySelectorAll(".fab-substance-item");
		if (!items.length) {
			return;
		}

		items.forEach(function (item) {
			const button = item.querySelector(".fab-substance-toggle");
			const content = item.querySelector(".fab-substance-content");

			if (!button || !content) {
				return;
			}

			button.addEventListener("click", function () {
				const isOpen = item.classList.contains("is-open");

				items.forEach(function (otherItem) {
					const otherButton = otherItem.querySelector(".fab-substance-toggle");
					const otherContent = otherItem.querySelector(".fab-substance-content");

					otherItem.classList.remove("is-open");
					if (otherButton) {
						otherButton.setAttribute("aria-expanded", "false");
					}
					if (otherContent) {
						otherContent.hidden = true;
					}
				});

				if (!isOpen) {
					item.classList.add("is-open");
					button.setAttribute("aria-expanded", "true");
					content.hidden = false;
				}
			});
		});
	}

	function initProfileImageEditors() {
		const editors = document.querySelectorAll("[data-fab-image-field]");
		if (!editors.length) {
			return;
		}

		editors.forEach(function (editor) {
			let input = editor.querySelector("[data-fab-image-input]");
			const preview = editor.querySelector("[data-fab-image-preview]");
			const trigger = editor.querySelector("[data-fab-image-trigger]");
			const clear = editor.querySelector("[data-fab-image-clear]");
			const removeField = editor.querySelector("[data-fab-image-remove]");

			if (!input || !preview || !trigger || !clear || !removeField) {
				return;
			}

			const defaultSrc = preview.getAttribute("data-default-src") || preview.getAttribute("src") || "";
			const placeholderSrc = preview.getAttribute("data-placeholder-src") || defaultSrc;
			let objectUrl = null;

			function setPreview(src, isPlaceholder) {
				preview.src = src;
				preview.classList.toggle("is-placeholder", !!isPlaceholder);
				editor.classList.toggle("is-empty", !!isPlaceholder);
			}

			function releaseObjectUrl() {
				if (objectUrl) {
					URL.revokeObjectURL(objectUrl);
					objectUrl = null;
				}
			}

			function applyFile(file) {
				if (!file) {
					return;
				}

				releaseObjectUrl();
				objectUrl = URL.createObjectURL(file);
				removeField.value = "0";
				setPreview(objectUrl, false);
			}

			function bindInputChange() {
				input.addEventListener("change", function () {
					const file = input.files && input.files[0] ? input.files[0] : null;
					if (!file) {
						return;
					}

					applyFile(file);
				});
			}

			function resetInput() {
				const replacement = input.cloneNode();
				input.parentNode.replaceChild(replacement, input);
				input = replacement;
				bindInputChange();
			}

			trigger.addEventListener("click", function () {
				input.click();
			});

			preview.addEventListener("click", function () {
				input.click();
			});

			bindInputChange();

			clear.addEventListener("click", function () {
				releaseObjectUrl();
				resetInput();
				removeField.value = "1";
				setPreview(placeholderSrc, true);
			});

			["dragenter", "dragover"].forEach(function (eventName) {
				editor.addEventListener(eventName, function (event) {
					event.preventDefault();
					editor.classList.add("is-dragover");
				});
			});

			["dragleave", "dragend", "drop"].forEach(function (eventName) {
				editor.addEventListener(eventName, function (event) {
					event.preventDefault();
					editor.classList.remove("is-dragover");
				});
			});

			editor.addEventListener("drop", function (event) {
				const files = event.dataTransfer && event.dataTransfer.files;
				const file = files && files[0] ? files[0] : null;
				if (!file || !file.type || file.type.indexOf("image/") !== 0) {
					return;
				}

				if (typeof DataTransfer !== "undefined") {
					const dataTransfer = new DataTransfer();
					dataTransfer.items.add(file);
					input.files = dataTransfer.files;
				}

				applyFile(file);
			});

			setPreview(preview.getAttribute("src") || placeholderSrc, preview.classList.contains("is-placeholder"));
		});
	}

	function initDeleteModal() {
		const modal = document.querySelector("[data-fab-delete-modal]");
		if (!modal) {
			return;
		}

		const nameNode = modal.querySelector("[data-fab-delete-name]");
		const idNode = modal.querySelector("[data-fab-delete-id]");
		const closeButtons = modal.querySelectorAll("[data-fab-delete-close]");
		const openButtons = document.querySelectorAll("[data-fab-delete-open]");

		function closeModal() {
			modal.classList.remove("is-visible");
		}

		openButtons.forEach(function (button) {
			button.addEventListener("click", function () {
				if (nameNode) {
					nameNode.textContent = button.getAttribute("data-manufacturer-name") || "este fabricante";
				}

				if (idNode) {
					idNode.value = button.getAttribute("data-manufacturer-id") || "";
				}

				modal.classList.add("is-visible");
			});
		});

		closeButtons.forEach(function (button) {
			button.addEventListener("click", closeModal);
		});

		modal.addEventListener("click", function (event) {
			if (event.target === modal) {
				closeModal();
			}
		});
	}

	function initPanelPasswordToggles() {
		const toggles = document.querySelectorAll("[data-fab-password-toggle]");
		if (!toggles.length) {
			return;
		}

		toggles.forEach(function (toggle) {
			toggle.addEventListener("click", function () {
				const wrapper = toggle.closest(".fab-password-field");
				const valueNode = wrapper ? wrapper.querySelector("[data-fab-password-value]") : null;

				if (!valueNode) {
					return;
				}

				const masked = valueNode.getAttribute("data-masked") || "••••••";
				const plain = valueNode.getAttribute("data-plain") || "";
				const expanded = toggle.getAttribute("data-expanded") === "true";

				if (!plain) {
					return;
				}

				if (expanded) {
					valueNode.textContent = masked;
					toggle.textContent = "Exibir";
					toggle.setAttribute("data-expanded", "false");
					return;
				}

				valueNode.textContent = plain;
				toggle.textContent = "Ocultar";
				toggle.setAttribute("data-expanded", "true");
			});
		});
	}

	function initPanelFormValidation() {
		const form = document.querySelector(".fab-panel-form");
		if (!form) {
			return;
		}

		form.setAttribute("novalidate", "novalidate");

		const phoneFields = Array.from(form.querySelectorAll("input[type='tel']"));
		const substancesInput = form.querySelector("[data-fab-substance-search]");
		const chipList = form.querySelector("[data-fab-chip-list]");
		const fields = Array.from(
			form.querySelectorAll("input:not([type='hidden']):not([type='file']), textarea, select")
		);
		let hasAttemptedSubmit = false;

		function getFieldWrap(field) {
			return field.closest(".fab-form-stack") || field.parentElement;
		}

		function getFieldLabel(field) {
			const wrap = getFieldWrap(field);
			const label = wrap ? wrap.querySelector(".fab-label-strong, label") : null;
			if (!label) {
				return "este campo";
			}

			return (label.textContent || "")
				.replace(/\s+/g, " ")
				.replace(/\*+/g, "")
				.trim()
				.toLowerCase();
		}

		function getErrorNode(field) {
			const wrap = getFieldWrap(field);
			if (!wrap) {
				return null;
			}

			let errorNode = wrap.querySelector(".fab-field-error");
			if (!errorNode) {
				errorNode = document.createElement("div");
				errorNode.className = "fab-field-error";
				errorNode.setAttribute("aria-live", "polite");
				wrap.appendChild(errorNode);
			}

			return errorNode;
		}

		function setFieldError(field, message) {
			const wrap = getFieldWrap(field);
			const errorNode = getErrorNode(field);

			field.classList.add("is-invalid");
			field.setAttribute("aria-invalid", "true");

			if (wrap) {
				wrap.classList.add("is-invalid");
			}

			if (errorNode) {
				errorNode.textContent = message;
			}
		}

		function clearFieldError(field) {
			const wrap = getFieldWrap(field);
			const errorNode = getErrorNode(field);

			field.classList.remove("is-invalid");
			field.removeAttribute("aria-invalid");

			if (wrap) {
				wrap.classList.remove("is-invalid");
			}

			if (errorNode) {
				errorNode.textContent = "";
			}
		}

		function getFieldMessage(field) {
			if (field.validity.valueMissing) {
				return "Você precisa preencher " + getFieldLabel(field) + " para continuar.";
			}

			if (field.validity.typeMismatch) {
				if (field.type === "email") {
					return "Informe um e-mail válido para continuar.";
				}

				if (field.type === "url") {
					return "Informe um site válido para continuar.";
				}
			}

			if (field.validity.tooShort) {
				return "O campo " + getFieldLabel(field) + " está incompleto.";
			}

			if (field.validity.customError) {
				return field.validationMessage || "Revise este campo para continuar.";
			}

			return "";
		}

		function validateField(field, options) {
			const validationOptions = options || {};
			const shouldShowError =
				typeof validationOptions.showError === "boolean" ? validationOptions.showError : hasAttemptedSubmit;

			if (!field || field.disabled) {
				return true;
			}

			if (phoneFields.indexOf(field) !== -1) {
				validatePhoneField(field);
			}

			if (field === substancesInput) {
				validateSubstances();
			}

			const valid = field.checkValidity();
			if (valid) {
				clearFieldError(field);
				return true;
			}

			if (shouldShowError) {
				setFieldError(field, getFieldMessage(field));
			} else {
				clearFieldError(field);
			}

			return false;
		}

		function substanceCount() {
			return chipList ? chipList.querySelectorAll("input[name='fab_substance_payload[]'], input[name='fab_substances[]']").length : 0;
		}

		function validatePhoneField(field) {
			if (!field) {
				return true;
			}

			const digits = (field.value || "").replace(/\D+/g, "");
			const valid = digits.length === 0 ? !field.required : digits.length >= 10 && digits.length <= 13;

			field.setCustomValidity(valid ? "" : "Informe um telefone completo com DDD.");
			return valid;
		}

		function validateSubstances() {
			if (!substancesInput) {
				return true;
			}

			const valid = substanceCount() > 0;
			substancesInput.setCustomValidity(valid ? "" : "Selecione ao menos uma substância.");
			return valid;
		}

		phoneFields.forEach(function (field) {
			field.addEventListener("input", function () {
				if (hasAttemptedSubmit) {
					validateField(field);
				}
			});
			field.addEventListener("blur", function () {
				if (hasAttemptedSubmit) {
					validateField(field);
				}
			});
		});

		if (substancesInput) {
			substancesInput.addEventListener("input", function () {
				if (hasAttemptedSubmit) {
					validateField(substancesInput);
				}
			});
		}

		if (chipList) {
			chipList.addEventListener("click", function () {
				if (hasAttemptedSubmit) {
					window.setTimeout(function () {
						validateField(substancesInput);
					}, 0);
				}
			});
		}

		fields.forEach(function (field) {
			if (phoneFields.indexOf(field) !== -1 || field === substancesInput) {
				return;
			}

			field.addEventListener("input", function () {
				if (hasAttemptedSubmit) {
					validateField(field);
				}
			});

			field.addEventListener("blur", function () {
				if (hasAttemptedSubmit) {
					validateField(field);
				}
			});
		});

		form.addEventListener("submit", function (event) {
			hasAttemptedSubmit = true;

			const invalidFields = fields.filter(function (field) {
				return !validateField(field, { showError: true });
			});

			if (invalidFields.length) {
				event.preventDefault();
				invalidFields[0].focus();
			}
		});
	}

	document.addEventListener("DOMContentLoaded", function () {
		initHeaderUserMenu();
		initModal();
		initSubstancePicker();
		initCatalogFiltersAutocomplete();
		initSubstanceAccordion();
		initProfileImageEditors();
		initDeleteModal();
		initPanelPasswordToggles();
		initPanelFormValidation();
	});
})();
