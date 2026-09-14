/**
 * SP REALTORS — front-end behaviour. Vanilla JS, no framework.
 * Every module guards its own elements so pages missing a section skip it.
 */

/* Mobile nav drawer ------------------------------------------------------ */
function initNavToggle() {
	const toggle = document.querySelector('[data-nav-toggle]');
	const drawer = document.querySelector('[data-nav-drawer]');
	if (!toggle || !drawer) return;

	const openIcon = toggle.querySelector('[data-nav-icon-open]');
	const closeIcon = toggle.querySelector('[data-nav-icon-close]');
	// "collapse" hides with display (admin sidebar); default slides in (site drawer).
	const collapses = drawer.dataset.navDrawer === 'collapse';
	const hiddenClass = collapses ? 'hidden' : 'translate-x-full';

	const isOpen = () => !drawer.classList.contains(hiddenClass);

	const setState = (open) => {
		drawer.classList.toggle(hiddenClass, !open);
		toggle.setAttribute('aria-expanded', String(open));
		if (!collapses) document.body.style.overflow = open ? 'hidden' : '';
		openIcon?.classList.toggle('hidden', open);
		closeIcon?.classList.toggle('hidden', !open);
	};

	toggle.addEventListener('click', () => setState(!isOpen()));

	document.addEventListener('keydown', (e) => {
		if (e.key === 'Escape' && isOpen()) {
			setState(false);
			toggle.focus();
		}
	});
}

/* Filter drawer (properties listing, mobile) ----------------------------- */
function initFilterDrawer() {
	const toggle = document.querySelector('[data-filter-toggle]');
	const panel = document.querySelector('[data-filter-panel]');
	if (!toggle || !panel) return;

	toggle.addEventListener('click', () => {
		const isHidden = panel.classList.toggle('hidden');
		toggle.setAttribute('aria-expanded', String(!isHidden));
	});
}

/* Auto-submit filter form on change (desktop) ---------------------------- */
function initFilterAutoSubmit() {
	const form = document.querySelector('[data-filter-form]');
	if (!form) return;

	form.addEventListener('change', (e) => {
		if (e.target.matches('input[type="checkbox"], input[type="radio"], select')) {
			form.submit();
		}
	});
}

/* Sort select ------------------------------------------------------------ */
function initSortSelect() {
	document.querySelectorAll('[data-sort-select]').forEach((select) => {
		select.addEventListener('change', () => select.closest('form')?.submit());
	});
}

/* Property gallery ------------------------------------------------------- */
function initGallery() {
	const gallery = document.querySelector('[data-gallery]');
	if (!gallery) return;

	const main = gallery.querySelector('[data-gallery-main]');
	const thumbs = Array.from(gallery.querySelectorAll('[data-gallery-thumb]'));
	const counter = gallery.querySelector('[data-gallery-counter]');
	const prev = gallery.querySelector('[data-gallery-prev]');
	const next = gallery.querySelector('[data-gallery-next]');
	if (!main || thumbs.length === 0) return;

	let index = 0;

	const show = (i) => {
		index = (i + thumbs.length) % thumbs.length;
		const thumb = thumbs[index];
		main.src = thumb.dataset.full;
		main.alt = thumb.dataset.alt || '';
		thumbs.forEach((t, n) => {
			t.classList.toggle('border-blue', n === index);
			t.classList.toggle('border-transparent', n !== index);
			t.setAttribute('aria-current', String(n === index));
		});
		if (counter) counter.textContent = `${index + 1} / ${thumbs.length}`;
	};

	thumbs.forEach((thumb, i) => thumb.addEventListener('click', () => show(i)));
	prev?.addEventListener('click', () => show(index - 1));
	next?.addEventListener('click', () => show(index + 1));

	show(0);
}

/* FAQ accordion ---------------------------------------------------------- */
function initFaq() {
	document.querySelectorAll('[data-faq-question]').forEach((button) => {
		button.addEventListener('click', () => {
			const item = button.closest('[data-faq-item]');
			const answer = item?.querySelector('[data-faq-answer]');
			const toggleIcon = button.querySelector('[data-faq-toggle]');
			if (!item || !answer) return;

			const isOpen = item.classList.toggle('is-open');
			button.setAttribute('aria-expanded', String(isOpen));
			answer.style.maxHeight = isOpen ? `${answer.scrollHeight}px` : '0px';
			answer.classList.toggle('pb-4', isOpen);
			toggleIcon?.classList.toggle('rotate-45', isOpen);
		});
	});
}

/* Share button ----------------------------------------------------------- */
function initShare() {
	document.querySelectorAll('[data-share]').forEach((button) => {
		button.addEventListener('click', async () => {
			const url = window.location.href;
			const title = document.title;

			if (navigator.share) {
				try {
					await navigator.share({ title, url });
				} catch {
					/* user cancelled */
				}
				return;
			}

			try {
				await navigator.clipboard.writeText(url);
				const original = button.getAttribute('aria-label');
				button.setAttribute('aria-label', 'Link copied');
				setTimeout(() => button.setAttribute('aria-label', original || 'Share'), 2000);
			} catch {
				/* clipboard unavailable */
			}
		});
	});
}

document.addEventListener('DOMContentLoaded', () => {
	initNavToggle();
	initFilterDrawer();
	initFilterAutoSubmit();
	initSortSelect();
	initGallery();
	initFaq();
	initShare();
});
