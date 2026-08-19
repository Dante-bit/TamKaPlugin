(function () {
	'use strict';

	document.addEventListener('DOMContentLoaded', function () {
		initScopeCards();
		initCountdown();
		initTimePresets();
		initPercentPreview();
		initProductPicker();
	});

	/* ---------------- Scope cards (Tất cả / Danh mục / Sản phẩm) ---------------- */
	function initScopeCards() {
		var cards = document.querySelectorAll('.wcp-scope-card');
		if (!cards.length) return;

		function refresh() {
			cards.forEach(function (card) {
				var input = card.querySelector('input');
				card.classList.toggle('is-selected', input.checked);
			});
			document.querySelectorAll('.wcp-scope-panel').forEach(function (panel) {
				var checked = document.querySelector('.wcp-scope-card input:checked');
				panel.style.display = checked && panel.dataset.scope === checked.value ? '' : 'none';
			});
		}

		cards.forEach(function (card) {
			card.addEventListener('click', function () {
				var input = card.querySelector('input');
				input.checked = true;
				refresh();
			});
		});

		refresh();
	}

	/* ---------------- Đếm ngược thời gian khuyến mãi ---------------- */
	function initCountdown() {
		var el = document.getElementById('wcp-countdown');
		if (!el) return;

		var end = parseInt(el.dataset.end, 10);
		var start = parseInt(el.dataset.start, 10);

		function tick() {
			var now = Date.now();

			if (start && now < start) {
				el.textContent = 'Chưa bắt đầu';
				el.className = 'wcp-countdown';
				return;
			}
			if (!end || now > end) {
				el.textContent = 'Đã kết thúc';
				el.className = 'wcp-countdown';
				return;
			}

			var diff = Math.max(0, end - now);
			var d = Math.floor(diff / 86400000);
			var h = Math.floor((diff % 86400000) / 3600000);
			var m = Math.floor((diff % 3600000) / 60000);
			var s = Math.floor((diff % 60000) / 1000);

			var parts = [];
			if (d > 0) parts.push(d + 'n');
			parts.push((h < 10 ? '0' : '') + h + 'h');
			parts.push((m < 10 ? '0' : '') + m + 'p');
			parts.push((s < 10 ? '0' : '') + s + 'g');

			el.textContent = parts.join(' ');
		}

		tick();
		setInterval(tick, 1000);
	}

	/* ---------------- Preset nhanh cho ngày giờ ---------------- */
	function initTimePresets() {
		var buttons = document.querySelectorAll('.wcp-pill-btn[data-preset]');
		if (!buttons.length) return;

		buttons.forEach(function (btn) {
			btn.addEventListener('click', function (e) {
				e.preventDefault();
				var preset = btn.dataset.preset;
				var now = new Date();
				var start = new Date(now);
				var end = new Date(now);

				if (preset === '24h') {
					end.setDate(end.getDate() + 1);
				} else if (preset === 'weekend') {
					var day = start.getDay();
					var diffToSat = (6 - day + 7) % 7 || 7;
					start.setDate(start.getDate() + diffToSat);
					start.setHours(0, 0, 0, 0);
					end = new Date(start);
					end.setDate(end.getDate() + 2);
					end.setHours(23, 59, 0, 0);
				} else if (preset === '7d') {
					end.setDate(end.getDate() + 7);
				} else if (preset === '30d') {
					end.setDate(end.getDate() + 30);
				}

				setFieldValue('wcp_promo_start_date', formatDate(start));
				setFieldValue('wcp_promo_start_time', formatTime(start));
				setFieldValue('wcp_promo_end_date', formatDate(end));
				setFieldValue('wcp_promo_end_time', formatTime(end));
			});
		});
	}

	function setFieldValue(name, value) {
		var field = document.querySelector('[name="' + name + '"]');
		if (field) field.value = value;
	}

	function formatDate(d) {
		return d.getFullYear() + '-' + pad(d.getMonth() + 1) + '-' + pad(d.getDate());
	}

	function formatTime(d) {
		return pad(d.getHours()) + ':' + pad(d.getMinutes());
	}

	function pad(n) {
		return (n < 10 ? '0' : '') + n;
	}

	/* ---------------- Preview giá theo % nhập vào ---------------- */
	function initPercentPreview() {
		var input = document.getElementById('wcp_promo_percent');
		if (!input) return;

		function update() {
			var percent = parseFloat(input.value) || 0;
			document.querySelectorAll('[data-regular-price]').forEach(function (el) {
				var regular = parseFloat(el.dataset.regularPrice) || 0;
				var target = el.querySelector('.wcp-new-price');
				if (!target) return;
				if (percent > 0 && regular > 0) {
					var sale = regular - (regular * percent) / 100;
					target.textContent = '→ ' + formatMoney(sale);
					target.style.display = '';
				} else {
					target.style.display = 'none';
				}
			});

			var previewPercent = document.getElementById('wcp-preview-percent');
			if (previewPercent) previewPercent.textContent = percent;
		}

		input.addEventListener('input', update);
		update();
	}

	function formatMoney(value) {
		return Math.round(value).toLocaleString('vi-VN') + 'đ';
	}

	/* ---------------- Bảng chọn sản phẩm: tìm kiếm + lọc danh mục + phân trang ---------------- */
	function initProductPicker() {
		var wrap = document.getElementById('wcp-product-picker');
		if (!wrap) return;

		var searchInput = wrap.querySelector('.wcp-picker-search');
		var catSelect = wrap.querySelector('.wcp-picker-cat-filter');
		var checkAll = wrap.querySelector('.wcp-picker-check-all');
		var rows = Array.prototype.slice.call(wrap.querySelectorAll('tbody tr[data-name]'));
		var countLabel = wrap.querySelector('.wcp-picker-count');
		var pagination = wrap.querySelector('.wcp-pagination');
		var perPage = 12;
		var currentPage = 1;

		function updateCheckedCount() {
			var checked = rows.filter(function (r) {
				return r.querySelector('input[type="checkbox"]').checked;
			}).length;
			if (countLabel) {
				countLabel.innerHTML = '<strong>' + checked + '</strong> sản phẩm đã chọn';
			}
			rows.forEach(function (r) {
				r.classList.toggle('is-checked', r.querySelector('input[type="checkbox"]').checked);
			});
		}

		function getFiltered() {
			var term = (searchInput ? searchInput.value : '').toLowerCase().trim();
			var cat = catSelect ? catSelect.value : '';

			return rows.filter(function (row) {
				var matchesTerm = !term ||
					row.dataset.name.indexOf(term) !== -1 ||
					row.dataset.sku.indexOf(term) !== -1;
				var matchesCat = !cat || (',' + row.dataset.cat + ',').indexOf(',' + cat + ',') !== -1;
				return matchesTerm && matchesCat;
			});
		}

		function render() {
			var filtered = getFiltered();
			var totalPages = Math.max(1, Math.ceil(filtered.length / perPage));
			if (currentPage > totalPages) currentPage = totalPages;

			rows.forEach(function (row) {
				row.style.display = 'none';
			});

			var start = (currentPage - 1) * perPage;
			var pageRows = filtered.slice(start, start + perPage);
			pageRows.forEach(function (row) {
				row.style.display = '';
			});

			var emptyRow = wrap.querySelector('.wcp-empty-row');
			if (emptyRow) {
				emptyRow.style.display = filtered.length ? 'none' : '';
			}

			renderPagination(totalPages);
		}

		function renderPagination(totalPages) {
			if (!pagination) return;
			pagination.innerHTML = '';

			if (totalPages <= 1) return;

			var prev = document.createElement('button');
			prev.type = 'button';
			prev.textContent = '‹';
			prev.disabled = currentPage === 1;
			prev.addEventListener('click', function () {
				currentPage--;
				render();
			});
			pagination.appendChild(prev);

			for (var i = 1; i <= totalPages; i++) {
				(function (page) {
					var btn = document.createElement('button');
					btn.type = 'button';
					btn.textContent = page;
					if (page === currentPage) btn.classList.add('is-active');
					btn.addEventListener('click', function () {
						currentPage = page;
						render();
					});
					pagination.appendChild(btn);
				})(i);
			}

			var next = document.createElement('button');
			next.type = 'button';
			next.textContent = '›';
			next.disabled = currentPage === totalPages;
			next.addEventListener('click', function () {
				currentPage++;
				render();
			});
			pagination.appendChild(next);
		}

		if (searchInput) {
			searchInput.addEventListener('input', function () {
				currentPage = 1;
				render();
			});
		}
		if (catSelect) {
			catSelect.addEventListener('change', function () {
				currentPage = 1;
				render();
			});
		}
		if (checkAll) {
			checkAll.addEventListener('change', function () {
				getFiltered().forEach(function (row) {
					row.querySelector('input[type="checkbox"]').checked = checkAll.checked;
				});
				updateCheckedCount();
			});
		}

		rows.forEach(function (row) {
			var cb = row.querySelector('input[type="checkbox"]');
			cb.addEventListener('change', updateCheckedCount);
			row.addEventListener('click', function (e) {
				if (e.target.tagName.toLowerCase() !== 'input') {
					cb.checked = !cb.checked;
					updateCheckedCount();
				}
			});
		});

		updateCheckedCount();
		render();
	}
})();
