/* Leovian front-end effects. Pages are rendered by WordPress; this only animates them. */
(() => {
	const $ = (s, r = document) => r.querySelector(s);
	const $$ = (s, r = document) => [...r.querySelectorAll(s)];
	const CFG = window.LEO || {};
	const RM = matchMedia('(prefers-reduced-motion: reduce)').matches;
	// Skip background video when the visitor asked for less motion or less data.
	const LITE = RM || Boolean(navigator.connection && navigator.connection.saveData);
	const FINE = !RM && matchMedia('(hover: hover) and (pointer: fine)').matches;
	const P = { x: innerWidth / 2, y: innerHeight / 2 };
	const FX = { px: [], lit: [], steps: [], hs: [] };
	const clamp = (v, a, b) => Math.min(b, Math.max(a, v));
	const pad = n => String(n).padStart(2, '0');
	let lastY = scrollY, ticking = false, heroBg = null, teamIdx = 0;
	try { history.scrollRestoration = 'manual'; } catch (e) { }

	/* ---------- Reveal on scroll ---------- */
	function reveal() {
		const els = $$('.rv, .sw, .mi');
		if (RM || !('IntersectionObserver' in window)) { els.forEach(e => e.classList.add('in')); return; }
		const io = new IntersectionObserver(entries => entries.forEach(x => {
			if (!x.isIntersecting) return;
			x.target.classList.add('in');
			io.unobserve(x.target);
		}), { threshold: .14, rootMargin: '0px 0px -6% 0px' });
		els.forEach(e => io.observe(e));
	}

	// Background videos only load and play while on screen.
	function bgVideos() {
		const vids = $$('video[data-src]');
		if (LITE || !vids.length || !('IntersectionObserver' in window)) return;
		const vio = new IntersectionObserver(entries => entries.forEach(x => {
			const v = x.target;
			if (!x.isIntersecting) { v.pause(); return; }
			if (!v.src) v.src = v.dataset.src;
			v.play().catch(() => { });
		}), { rootMargin: '200px 0px' });
		vids.forEach(v => { v.addEventListener('playing', () => v.classList.add('on'), { once: true }); vio.observe(v); });
	}

	// Hero cycles its clips with a crossfade and a progress bar per clip.
	function heroVideo() {
		const hero = $('.hero');
		if (!hero) return;
		heroBg = $('.bgfx', hero);
		const vids = $$('.hv', hero);
		let clips = [];
		try { clips = JSON.parse(hero.dataset.clips || '[]'); } catch (e) { }
		if (LITE || !vids.length || !clips.length) return;
		const bars = $$('.hc-bars b', hero), small = innerWidth < 900, loc = $('#hloc');
		let cur = 0;
		const show = i => {
			cur = i;
			vids.forEach((v, k) => v.classList.toggle('on', k === i));
			if (loc) loc.textContent = clips[i].place;
			bars.forEach((b, k) => { if (k !== i) b.style.setProperty('--f', 0); });
		};
		vids.forEach((v, i) => {
			v.src = small ? clips[i].sd : clips[i].hd;
			v.addEventListener('timeupdate', () => { if (i === cur && v.duration && bars[i]) bars[i].style.setProperty('--f', (v.currentTime / v.duration).toFixed(3)); });
			v.addEventListener('ended', () => {
				const n = (i + 1) % vids.length;
				vids[n].currentTime = 0;
				vids[n].play().then(() => show(n)).catch(() => { });
			});
			// Buffer only the next clip once the current one starts.
			v.addEventListener('playing', () => { vids[(i + 1) % vids.length].preload = 'auto'; }, { once: true });
		});
		vids[0].preload = 'auto';
		new IntersectionObserver(([x]) => {
			if (x.isIntersecting) vids[cur].play().then(() => show(cur)).catch(() => { });
			else vids[cur].pause();
		}).observe(hero);
	}

	function xpInit() {
		$$('.xp').forEach(xp => {
			const items = $$('.xp-i', xp);
			const act = i => items.forEach((x, k) => x.classList.toggle('on', k === i));
			items.forEach((x, i) => ['mouseenter', 'focus', 'click'].forEach(ev => x.addEventListener(ev, () => act(i))));
			act(0);
		});
	}

	// Pins the investment gallery and scrolls it sideways on wide screens; swipe scroller otherwise.
	function hsInit() {
		$$('.hs').forEach(sec => {
			const track = $('.hs-track', sec);
			const pin = !RM && innerWidth > 900;
			sec.classList.toggle('pinned', pin);
			sec._track = track;
			sec._n = $$('.hcard[id]', track).length;
			sec._count = $('.hs-count b', sec);
			track.style.transform = '';
			sec._pin = pin;
			if (!pin) { sec.style.height = ''; return; }
			sec._dist = Math.max(0, track.scrollWidth - innerWidth);
			sec.style.height = `${innerHeight + sec._dist}px`;
		});
	}

	function collectFx() {
		FX.px = RM ? [] : $$('[data-px]').map(el => { el._s = parseFloat(el.dataset.px) * el.parentElement.offsetHeight; return el; });
		FX.lit = $$('.lit').map(el => { el._w = $$('.lw', el); el._n = -1; if (RM) el._w.forEach(w => w.classList.add('on')); return el; });
		FX.steps = $$('.steps');
		if (RM) FX.steps.forEach(s => { s.style.setProperty('--p', 1); $$('.step', s).forEach(x => x.classList.add('on')); });
		FX.hs = $$('.hs');
	}

	function frame() {
		ticking = false;
		const y = scrollY, vh = innerHeight, max = document.documentElement.scrollHeight - vh;
		document.documentElement.style.setProperty('--sp', max > 0 ? (y / max).toFixed(4) : 0);
		const hdr = $('#hdr');
		hdr.classList.toggle('solid', y > 40);
		if (Math.abs(y - lastY) > 6) {
			const down = y > lastY && y > 420;
			hdr.classList.toggle('hide', down && !document.body.classList.contains('menu') && !hdr.matches(':hover, :focus-within'));
			lastY = y;
		}
		$('#top').classList.toggle('show', y > 600);
		if (RM) return;
		FX.px.forEach(el => {
			const r = el.parentElement.getBoundingClientRect();
			if (r.bottom < -200 || r.top > vh + 200) return;
			const p = (r.top + r.height / 2 - vh / 2) / vh;
			el.style.transform = `translate3d(0,${(p * -el._s).toFixed(1)}px,0)`;
		});
		FX.lit.forEach(el => {
			const r = el.getBoundingClientRect();
			if (r.bottom < -50 || r.top > vh + 50) return;
			const n = Math.round(clamp((vh * .85 - r.top) / (r.height + vh * .35), 0, 1) * el._w.length);
			if (n === el._n) return;
			el._n = n;
			el._w.forEach((w, i) => w.classList.toggle('on', i < n));
		});
		FX.steps.forEach(el => {
			const r = el.getBoundingClientRect();
			const p = clamp((vh * .78 - r.top) / (r.height + vh * .15), 0, 1);
			el.style.setProperty('--p', p.toFixed(3));
			const steps = $$('.step', el);
			steps.forEach((s, i) => s.classList.toggle('on', p >= (i / steps.length) + .02));
		});
		FX.hs.forEach(sec => {
			if (!sec._pin) return;
			const p = clamp(-sec.getBoundingClientRect().top / (sec.offsetHeight - vh), 0, 1);
			sec._track.style.transform = `translate3d(${(-p * sec._dist).toFixed(1)}px,0,0)`;
			sec.style.setProperty('--p', p.toFixed(4));
			const idx = Math.min(sec._n, Math.floor(p * sec._n) + 1);
			if (sec._idx !== idx && sec._count) { sec._idx = idx; sec._count.textContent = pad(idx); }
		});
	}

	function onScroll() {
		if (ticking) return;
		ticking = true;
		requestAnimationFrame(frame);
	}

	// Cursor ring, hover-image preview, magnetic buttons and hero drift on fine-pointer devices.
	function pointerFx() {
		if (!FINE) return;
		document.body.classList.add('has-cur');
		const cur = $('#cur'), hov = $('#hov');
		let cx = P.x, cy = P.y, hx = P.x, hy = P.y, mx = 0, my = 0, row = null, mag = null;
		(function loop() {
			cx += (P.x - cx) * .22; cy += (P.y - cy) * .22;
			hx += (P.x - hx) * .12; hy += (P.y - hy) * .12;
			cur.style.transform = `translate3d(${cx.toFixed(1)}px,${cy.toFixed(1)}px,0)`;
			hov.style.transform = `translate3d(${hx.toFixed(1)}px,${hy.toFixed(1)}px,0)`;
			if (heroBg) {
				mx += ((P.x / innerWidth - .5) - mx) * .05; my += ((P.y / innerHeight - .5) - my) * .05;
				heroBg.style.setProperty('--mx', mx.toFixed(3)); heroBg.style.setProperty('--my', my.toFixed(3));
			}
			requestAnimationFrame(loop);
		})();
		document.addEventListener('mousemove', e => {
			P.x = e.clientX; P.y = e.clientY;
			const m = e.target.closest('.mag');
			if (mag && mag !== m) mag.style.transform = '';
			mag = m;
			if (m) {
				const r = m.getBoundingClientRect();
				m.style.transform = `translate(${((e.clientX - r.left - r.width / 2) * .3).toFixed(1)}px,${((e.clientY - r.top - r.height / 2) * .3).toFixed(1)}px)`;
			}
		}, { passive: true });
		document.addEventListener('mouseover', e => {
			const hot = e.target.closest('a, button, input, select, textarea, label, [data-cursor]');
			const lab = e.target.closest('[data-cursor]');
			cur.classList.toggle('hot', !!hot && !lab);
			cur.classList.toggle('lab', !!lab);
			$('b', cur).textContent = lab ? lab.dataset.cursor : '';
			const r = e.target.closest('[data-img]');
			if (r !== row) {
				row = r;
				if (r && r.dataset.img) { $('img', hov).src = r.dataset.img; hov.classList.add('on'); } else hov.classList.remove('on');
			}
		});
		document.addEventListener('mouseleave', () => cur.classList.add('off'));
		document.addEventListener('mouseenter', () => cur.classList.remove('off'));
	}

	/* ---------- Team carousel ---------- */
	function teamGo(i) {
		const slides = $$('.slide');
		if (!slides.length) return;
		teamIdx = (i + slides.length) % slides.length;
		slides.forEach((s, k) => { s.classList.toggle('on', k === teamIdx); s.inert = k !== teamIdx; });
		$$('.tthumb').forEach((b, k) => b.classList.toggle('on', k === teamIdx));
		const count = $('#tcount');
		if (count) count.innerHTML = `<b>${pad(teamIdx + 1)}</b> / ${pad(slides.length)}`;
		const bar = $('#tbar');
		if (!bar) return;
		bar.classList.remove('run');
		void bar.offsetWidth;
		if (!RM && slides.length > 1) bar.classList.add('run');
	}
	function teamInit() {
		if (!$('.slide')) return;
		teamGo(0);
		const bar = $('#tbar');
		if (bar) bar.addEventListener('animationend', () => teamGo(teamIdx + 1));
	}

	/* ---------- Scrolling helpers ---------- */
	function scrollToId(id, smooth) {
		const el = id && document.getElementById(id);
		if (!el) return false;
		const behavior = smooth && !RM ? 'smooth' : 'auto';
		const hs = el.closest('.hs');
		if (hs && hs._pin) {
			const p = clamp((el.offsetLeft - 40) / Math.max(1, hs._dist), 0, 1);
			scrollTo({ top: hs.getBoundingClientRect().top + scrollY + p * hs._dist, behavior });
		} else if (hs && el !== hs) {
			hs.scrollIntoView({ block: 'start', behavior });
			hs._track.scrollTo({ left: el.offsetLeft - 20, behavior });
		} else {
			el.scrollIntoView({ block: el.hasAttribute('data-flash') ? 'center' : 'start', behavior });
		}
		if (el.hasAttribute('data-flash')) { el.classList.remove('flash'); void el.offsetWidth; el.classList.add('flash'); }
		return true;
	}

	function menu(open) {
		const o = open ?? !document.body.classList.contains('menu');
		document.body.classList.toggle('menu', o);
		document.body.classList.toggle('lock', o);
		const ov = $('#ov');
		if (ov) { ov.setAttribute('aria-hidden', !o); ov.inert = !o; }
		const b = $('.burger');
		if (b) b.setAttribute('aria-expanded', o);
	}

	/* ---------- Event dialog ---------- */
	const esc = s => String(s || '').replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
	function openEvent(card) {
		let e = {};
		try { e = JSON.parse(card.dataset.ev); } catch (err) { return; }
		const d = $('#dlg');
		const reg = ($('.ev-list') && $('.ev-list').dataset.register) || CFG.register;
		d.innerHTML = `<div class="dlg-in">${e.img ? `<div class="mi dlg-media in"><div class="mi-p"><img src="${esc(e.img)}" alt=""></div></div>` : ''}
			<button class="dlg-x" data-close aria-label="${esc(CFG.close)}"><svg class="ic" viewBox="0 0 24 24" aria-hidden="true"><path d="M6 6l12 12M18 6 6 18"/></svg></button>
			<div class="dlg-body"><span class="eyebrow">${esc(e.past ? CFG.pastLabel : CFG.upcoming)}</span><h3 id="dlg-title">${esc(e.t)}</h3>
			<ul class="dlg-meta">${e.date ? `<li>${esc(e.date)}</li>` : ''}${e.loc ? `<li>${esc(e.loc)}</li>` : ''}</ul><p>${esc(e.d)}</p>
			<div class="actions">${e.past ? '' : `<a class="btn solid" href="${esc(CFG.contact)}">${esc(reg)}</a>`}<button class="btn" data-close>${esc(CFG.close)}</button></div></div></div>`;
		d.setAttribute('aria-labelledby', 'dlg-title');
		d.showModal();
	}

	/* ---------- Preloader ---------- */
	function preload(done) {
		const pre = $('#pre');
		if (!pre) { done(); return; }
		let first = true;
		try { first = !sessionStorage.getItem('lv-pre'); sessionStorage.setItem('lv-pre', '1'); } catch (e) { }
		if (RM || !first) { pre.classList.add('gone'); done(); return; }
		const n = $('.pre-n', pre), bar = $('.pre-bar', pre), t0 = performance.now(), dur = 1500;
		let finished = false;
		const finish = () => {
			if (finished) return;
			finished = true;
			pre.classList.add('done');
			setTimeout(done, 380);
			setTimeout(() => pre.classList.add('gone'), 1250);
		};
		const step = now => {
			const k = Math.min(1, (now - t0) / dur), e = 1 - Math.pow(1 - k, 3);
			n.textContent = String(Math.round(e * 100)).padStart(3, '0');
			bar.style.setProperty('--k', e.toFixed(3));
			if (k < 1) requestAnimationFrame(step); else finish();
		};
		requestAnimationFrame(step);
		setTimeout(finish, 3200);
	}

	/* ---------- Clicks ---------- */
	document.addEventListener('click', e => {
		const t = e.target;
		if (t.closest('.burger')) { menu(); return; }
		if (t.closest('[data-close]')) { $('#dlg').close(); return; }
		const team = t.closest('[data-team]');
		if (team) { teamGo(teamIdx + Number(team.dataset.team)); return; }
		const tm = t.closest('[data-tm]');
		if (tm) { teamGo(Number(tm.dataset.tm)); return; }
		const ev = t.closest('[data-ev]');
		if (ev && !t.closest('a')) { openEvent(ev); return; }
		const sc = t.closest('[data-scroll]');
		if (sc) { scrollToId(sc.dataset.scroll, true); return; }
		if (t.closest('#top')) { scrollTo({ top: 0, behavior: RM ? 'auto' : 'smooth' }); return; }

		const a = t.closest('a[href]');
		if (!a || e.defaultPrevented || e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
		const url = new URL(a.href, location.href);
		if (a.target === '_blank' || url.origin !== location.origin || a.hasAttribute('download') || /\/wp-(admin|login)/.test(url.pathname)) return;
		if (url.pathname === location.pathname && url.search === location.search && url.hash) {
			e.preventDefault();
			menu(false);
			if (scrollToId(url.hash.slice(1), true)) history.replaceState(null, '', url.hash);
			return;
		}
		if (RM || a.closest('.lang')) return;
		// Page changes slide a curtain over the screen showing the destination's name.
		e.preventDefault();
		menu(false);
		const w = $('#wipe');
		$('span', w).textContent = (a.textContent || '').trim().slice(0, 48);
		w.className = 'cover';
		setTimeout(() => { location.href = url.href; }, 650);
	});
	addEventListener('pageshow', e => { if (e.persisted) $('#wipe').className = ''; });

	const dlg = $('#dlg');
	if (dlg) dlg.addEventListener('click', e => { if (e.target === e.currentTarget) e.currentTarget.close(); });
	document.addEventListener('keydown', e => { if (e.key === 'Escape') menu(false); });
	matchMedia('(min-width: 1181px)').addEventListener('change', e => { if (e.matches) menu(false); });
	addEventListener('scroll', onScroll, { passive: true });
	let rz;
	addEventListener('resize', () => { clearTimeout(rz); rz = setTimeout(() => { hsInit(); collectFx(); frame(); }, 160); });

	/* ---------- Boot ---------- */
	reveal();
	bgVideos();
	heroVideo();
	xpInit();
	hsInit();
	collectFx();
	teamInit();
	pointerFx();
	frame();
	if (location.hash) addEventListener('load', () => setTimeout(() => scrollToId(location.hash.slice(1), false), 60), { once: true });
	preload(() => { const h = $('.hero'); if (h) setTimeout(() => h.classList.add('go'), 30); });
})();
