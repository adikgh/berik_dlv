// Berik: курьер сайтының ортақ скрипттері (админ мен кассадағы компоненттер)
$(function () {

	// ---------- Дата: ← күн → , Бүгін, өз күнтізбеміз (админдегідей) ----------
	var months = ['қаңтар','ақпан','наурыз','сәуір','мамыр','маусым','шілде','тамыз','қыркүйек','қазан','қараша','желтоқсан'];
	var days = ['жексенбі','дүйсенбі','сейсенбі','сәрсенбі','бейсенбі','жұма','сенбі'];
	var wd_short = ['Дс','Сс','Ср','Бс','Жм','Сб','Жс'];
	function ymd(d) { return d.getFullYear() + '-' + ('0' + (d.getMonth() + 1)).slice(-2) + '-' + ('0' + d.getDate()).slice(-2) }
	function parse(v) { var p = (v || '').split('-'); return p.length == 3 ? new Date(+p[0], +p[1] - 1, +p[2]) : null }
	function workToday() { return ymd(new Date(Date.now() - 6 * 3600 * 1000)) } // жұмыс күні 06:00-де басталады
	function nice(v) { var d = parse(v); return d ? d.getDate() + ' ' + months[d.getMonth()] + ', ' + days[d.getDay()] : v }
	function mname(m) { return months[m].charAt(0).toUpperCase() + months[m].slice(1) }
	function bcal_close() { $('.bcal').removeClass('pop_on'); $('.bdt').removeClass('bdt_open') }
	$(document).on('click', bcal_close);
	$(document).on('keydown', function (e) { if (e.key == 'Escape') bcal_close() });
	$(window).on('resize', bcal_close);

	$('input.on_sort_time').each(function () {
		var inp = $(this), today = workToday(), sel = inp.val() || today, vm = null;
		var box = $('<div class="bdt"></div>');
		var main = $('<label class="bdt_main"><i class="fal fa-calendar-alt"></i><span class="bdt_txt"></span></label>');
		inp.before(box);
		box.append('<div class="bdt_arr bdt_prev" title="Алдыңғы күн"><i class="far fa-angle-left"></i></div>').append(main);
		main.append(inp);
		box.append('<div class="bdt_arr bdt_next" title="Келесі күн"><i class="far fa-angle-right"></i></div>');
		var is_cur = sel == today;
		if (!is_cur) box.append('<div class="bdt_today">Бүгін</div>'); else box.addClass('bdt_is_today');
		main.find('.bdt_txt').text((is_cur ? 'Бүгін · ' : '') + nice(sel));

		var cal = $('<div class="bcal"></div>');
		$('body').append(cal);
		function draw() {
			var y = vm.getFullYear(), m = vm.getMonth();
			var h = '<div class="bcal_head"><div class="bcal_nav bcal_pm"><i class="far fa-angle-left"></i></div><b>' + mname(m) + ' ' + y + '</b><div class="bcal_nav bcal_nm"><i class="far fa-angle-right"></i></div></div><div class="bcal_grid">';
			$.each(wd_short, function (i, n) { h += '<span class="bcal_wd' + (i > 4 ? ' bcal_we' : '') + '">' + n + '</span>' });
			var start = new Date(y, m, 1); start.setDate(1 - (start.getDay() + 6) % 7);
			for (var i = 0; i < 42; i++) {
				var d = new Date(start); d.setDate(start.getDate() + i);
				var v = ymd(d), c = 'bcal_d';
				if (d.getMonth() != m) c += ' bcal_out';
				if (v == today) c += ' bcal_tod';
				if (v == sel) c += ' bcal_sel';
				h += '<div class="' + c + '" data-v="' + v + '">' + d.getDate() + '</div>';
				if (i == 34 && d.getMonth() != m) break;
			}
			cal.html(h + '</div><div class="bcal_foot"><div class="bcal_today">Бүгін</div></div>');
		}
		function place() {
			var r = main[0].getBoundingClientRect(), cw = cal.outerWidth();
			var b = box[0].getBoundingClientRect(); // күнтізбе дата жолағының ортасында
			cal.css({ top: b.bottom + 8, left: Math.max(8, Math.min(b.left + (b.width - cw) / 2, window.innerWidth - cw - 8)) });
		}
		function go(v) { inp.val(v).trigger('change') }
		main.on('click', function (e) {
			e.preventDefault(); e.stopPropagation();
			var open = cal.hasClass('pop_on'); bcal_close();
			if (!open) { vm = parse(sel) || new Date(); vm.setDate(1); draw(); place(); cal.addClass('pop_on'); box.addClass('bdt_open') }
		});
		cal.on('click', function (e) { e.stopPropagation() });
		cal.on('click', '.bcal_pm, .bcal_nm', function () { vm.setMonth(vm.getMonth() + ($(this).hasClass('bcal_nm') ? 1 : -1)); draw() });
		cal.on('click', '.bcal_d', function () { bcal_close(); go($(this).attr('data-v')) });
		cal.on('click', '.bcal_today', function () { bcal_close(); go(today) });
		box.on('click', '.bdt_prev, .bdt_next', function () {
			var d = parse(inp.val()) || new Date(); d.setDate(d.getDate() + ($(this).hasClass('bdt_next') ? 1 : -1)); go(ymd(d));
		});
		box.on('click', '.bdt_today', function () { go(today) });
	});


	// ---------- Аккаунт ----------
	var csrf = $('.up').attr('data-csrf');

	// Жеке деректер: попап ашу / жабу
	function acc_drw(open) {
		$('.acc_drw').toggleClass('acc_drw_act', open).attr('aria-hidden', open ? 'false' : 'true');
		$('body').toggleClass('acc_drw_lock', open);
		if (open) { $('.acc_form .acc_msg').removeClass('acc_msg_ok acc_msg_err').text(''); setTimeout(function () { $('.acc_form [name=name]').trigger('focus') }, 260) }
	}
	$('.on_acc_toggle').on('click', function () { acc_drw(true) });
	$('.on_acc_close').on('click', function () { acc_drw(false) });
	$(document).on('keydown', function (e) { if (e.key == 'Escape' && $('.acc_drw_act').length) acc_drw(false) });
	var acc_err = { name: 'Атыңызды жазыңыз', phone: 'Телефон 10 цифр болуы керек: 7001234567', phone_busy: 'Бұл телефон басқа қызметкерде тіркелген', csrf: 'Бетті жаңартып, қайта көріңіз', none: 'Қайта кіріңіз' };
	$('.acc_form').on('submit', function (e) {
		e.preventDefault();
		var f = $(this), btn = f.find('.acc_save'), msg = f.find('.acc_msg');
		if (btn.hasClass('btn_wait')) return;
		btn.addClass('btn_wait'); msg.removeClass('acc_msg_ok acc_msg_err').text('');
		$.ajax({ url: '/get.php?acc_save', type: 'POST', dataType: 'html',
			data: { csrf: csrf, name: f.find('[name=name]').val(), surname: f.find('[name=surname]').val(), phone: f.find('[name=phone]').val() },
			success: function (d) {
				d = $.trim(d);
				if (d == 'yes') {
					msg.addClass('acc_msg_ok').text('Сақталды');
					setTimeout(function () { acc_drw(false) }, 700);
					$('.up_name').text($.trim(f.find('[name=name]').val() + ' ' + f.find('[name=surname]').val()));
					$('.up_phone').text(f.find('[name=phone]').val().replace(/\D/g, '').slice(-10));
				} else msg.addClass('acc_msg_err').text(acc_err[d] || 'Сақталмады, қайта көріңіз');
			},
			error: function () { msg.addClass('acc_msg_err').text('Байланыс жоқ, қайта көріңіз') },
			complete: function () { btn.removeClass('btn_wait') }
		});
	});

	// Профиль фотосы: таңдау → тексеру → жүктеу → бірден көрсету
	var img_err = { file: 'Файл жүктелмеді', size: 'Фото 8 МБ-тан аспауы керек', type: 'Тек JPG, PNG немесе WEBP фото', save: 'Сақталмады, қайта көріңіз', csrf: 'Бетті жаңартып, қайта көріңіз', none: 'Қайта кіріңіз' };
	$('.on_acc_img').on('change', function () {
		var inp = this, f = inp.files && inp.files[0], logo = $('.up_logo');
		if (!f) return;
		if (!/^image\/(jpeg|png|webp)$/.test(f.type)) { alert(img_err.type); inp.value = ''; return }
		if (f.size > 8 * 1024 * 1024) { alert(img_err.size); inp.value = ''; return }
		var fd = new FormData(); fd.append('img', f); fd.append('csrf', csrf);
		logo.addClass('up_logo_wait');
		$.ajax({ url: '/get.php?acc_img', type: 'POST', data: fd, processData: false, contentType: false, dataType: 'text',
			success: function (d) {
				var r = null; try { r = JSON.parse(d) } catch (e) {}
				if (r && r.ok) logo.html('<div class="up_logo_c" style="background-image:url(\'' + r.img + '\')"></div>');
				else alert(img_err[$.trim(d)] || img_err.save);
			},
			error: function () { alert('Байланыс жоқ, қайта көріңіз') },
			complete: function () { logo.removeClass('up_logo_wait'); inp.value = '' }
		});
	});

})


// Негізгі карта таңдау (аккаунт)
$('html').on('click', '.on_map_app', function () {
	var btn = $(this)
	$.ajax({ url: "/get.php?map_app", type: "POST", dataType: "html", data: ({ app: btn.data('app') }),
		success: function (data) { if (data == 'yes') { $('.on_map_app').removeClass('map_app_act'); btn.addClass('map_app_act') } } })
})


// Төменгі мәзір: белсенді бөлімге жылжымалы фон (кассадағыдай)
;(function () {
	var c = document.querySelector('.pmenu_c'); if (!c) return
	var items = [].slice.call(c.querySelectorAll('.pmenu_i')), cur = items.findIndex(function (a) { return a.classList.contains('pmenu_i_act') })
	c.style.setProperty('--n', items.length)
	if (cur < 0) { c.querySelector('.pmenu_sl').style.opacity = 0; return }
	var prev = -1; try { prev = parseInt(sessionStorage.getItem('pm_i')) } catch (e) {}
	if (prev >= 0 && prev < items.length && prev != cur) {
		c.classList.add('pm_noanim'); c.style.setProperty('--i', prev)
		requestAnimationFrame(function () { requestAnimationFrame(function () { c.classList.remove('pm_noanim'); c.style.setProperty('--i', cur) }) })
	} else c.style.setProperty('--i', cur)
	items.forEach(function (a, i) {
		a.addEventListener('click', function () {
			try { sessionStorage.setItem('pm_i', i) } catch (e) {}
			items.forEach(function (b) { b.classList.remove('pmenu_i_act') }); a.classList.add('pmenu_i_act')
			c.style.setProperty('--i', i)
		})
	})
	try { sessionStorage.setItem('pm_i', cur) } catch (e) {}
})()

// Маршрут соңында базаға қайту (аккаунт)
$('html').on('change', '.on_route_return', function () {
	var inp = $(this), on = inp.prop('checked') ? 1 : 0
	$.ajax({ url: "/get.php?route_return", type: "POST", dataType: "html", data: ({ on: on, csrf: $('.up').attr('data-csrf') }),
		success: function (d) { if ($.trim(d) != 'yes') { inp.prop('checked', !on); alert('Сақталмады, қайта көріңіз') } },
		error: function () { inp.prop('checked', !on) } })
})


// «Тапсырдым»: немен төлем алғанын сұрайтын попап (предоплатадан кейін қалған сома ғана)
;(function () {
	var fmt = function (n) { return String(n).replace(/\B(?=(\d{3})+(?!\d))/g, ' ') + ' тг' }
	var sh = $('<div class="acc_drw pay_drw" aria-hidden="true"><div class="acc_drw_a pay_close"></div><div class="acc_drw_c" role="dialog">' +
		'<div class="acc_drw_grab"></div><div class="acc_drw_hd"><h4>Тапсырыс <span class="pay_num"></span></h4><div class="btn btn_cl pay_close" title="Жабу"><i class="far fa-times"></i></div></div>' +
		'<div class="pay_sum"><span>Клиенттен алу керек</span><b class="pay_rest"></b></div>' +
		'<div class="pay_lbl">Немен алдыңыз?</div>' +
		'<div class="pay_ms"><div class="pay_m" data-m="cash"><i class="far fa-money-bill-wave"></i><span>Қолма-қол</span></div><div class="pay_m" data-m="kaspi"><i class="far fa-mobile-alt"></i><span>Kaspi</span></div><div class="pay_m" data-m="mix"><i class="far fa-random"></i><span>Аралас</span></div></div>' +
		'<div class="pay_mix" hidden><label class="acc_fl"><span>Қолма-қол қанша алдыңыз?</span><input class="acc_inp pay_cash" type="tel" inputmode="numeric" placeholder="0"></label><div class="pay_kaspi">Kaspi: <b>0 тг</b></div></div>' +
		'<div class="acc_msg pay_msg"></div>' +
		'<button class="btn acc_save pay_ok" type="button"><i class="far fa-check"></i><span>Тапсырдым</span></button>' +
		'</div></div>')
	var cur = null, method = '', rest = 0
	function open(btn) {
		cur = btn; rest = parseInt(btn.attr('data-rest')) || 0; method = ''
		sh.find('.pay_num').text('№' + (btn.attr('data-num') || ''))
		sh.find('.pay_rest').text(fmt(rest))
		sh.find('.pay_m').removeClass('pay_m_act'); sh.find('.pay_mix').prop('hidden', true); sh.find('.pay_cash').val('')
		sh.find('.pay_msg').removeClass('acc_msg_err').text('')
		sh.toggleClass('pay_full', rest <= 0)
		sh.addClass('acc_drw_act').attr('aria-hidden', 'false'); $('body').addClass('acc_drw_lock')
	}
	function close() { sh.removeClass('acc_drw_act').attr('aria-hidden', 'true'); $('body').removeClass('acc_drw_lock') }
	function mix_upd() { var c = Math.min(rest, parseInt(String(sh.find('.pay_cash').val()).replace(/\D/g, '')) || 0); sh.find('.pay_kaspi b').text(fmt(rest - c)) }
	$(function () { $('body').append(sh) })
	// Қалған сома 0 болса (толық предоплата) — попапсыз бірден сақталады
	$(document).on('click', '.on_check', function (e) {
		e.preventDefault(); var b = $(this)
		if ((parseInt(b.attr('data-rest')) || 0) > 0) { open(b); return }
		if (b.hasClass('btn_wait')) return
		b.addClass('btn_wait')
		$.ajax({ url: '/orders/get.php?check', type: 'POST', dataType: 'html', data: { id: b.attr('data-id') },
			success: function (d) {
				if ($.trim(d) != 'yes') { alert('Сақталмады, бетті жаңартып көріңіз'); return }
				if ($('.ord_page').length) location.href = '/orders/'
				else b.parents('.uc_ui').slideUp(200, function () { $(this).remove() })
			},
			complete: function () { b.removeClass('btn_wait') } })
	})
	sh.on('click', '.pay_close', close)
	$(document).on('keydown', function (e) { if (e.key == 'Escape' && sh.hasClass('acc_drw_act')) close() })
	sh.on('click', '.pay_m', function () {
		method = $(this).attr('data-m'); sh.find('.pay_m').removeClass('pay_m_act'); $(this).addClass('pay_m_act')
		sh.find('.pay_mix').prop('hidden', method != 'mix'); if (method == 'mix') { mix_upd(); sh.find('.pay_cash').trigger('focus') }
		sh.find('.pay_msg').text('')
	})
	sh.on('input', '.pay_cash', mix_upd)
	sh.on('click', '.pay_ok', function () {
		var ok = $(this), msg = sh.find('.pay_msg')
		if (rest > 0 && !method) { msg.addClass('acc_msg_err').text('Немен алғаныңызды таңдаңыз'); return }
		if (ok.hasClass('btn_wait')) return
		ok.addClass('btn_wait')
		$.ajax({ url: '/orders/get.php?check', type: 'POST', dataType: 'html',
			data: { id: cur.attr('data-id'), method: method, cash: String(sh.find('.pay_cash').val()).replace(/\D/g, '') },
			success: function (d) {
				if ($.trim(d) == 'yes') {
					close()
					if ($('.ord_page').length) location.href = '/orders/'
					else cur.parents('.uc_ui').slideUp(200, function () { $(this).remove() })
				} else msg.addClass('acc_msg_err').text($.trim(d) == 'method' ? 'Немен алғаныңызды таңдаңыз' : 'Сақталмады, бетті жаңартып көріңіз')
			},
			error: function () { msg.addClass('acc_msg_err').text('Байланыс жоқ, қайта көріңіз') },
			complete: function () { ok.removeClass('btn_wait') }
		})
	})
})()
