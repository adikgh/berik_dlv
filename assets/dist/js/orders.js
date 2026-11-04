// start jquery
$(document).ready(function() {

	// btn_return_sn
	$('.btn_return_sn').on('click', function () {
		btn = $(this)
      $.ajax({
         url: "/orders/get.php?return_sn",
         type: "POST",
         dataType: "html",
         data: ({ id: btn.data('id'), }),
         success: function(data){ 
            if (data == 'yes') location.reload();
            console.log(data);
         },
         beforeSend: function(){ },
         error: function(data){ }
      })
	})



	// 
	$('.on_delete').on('click', function () {
		btn = $(this)
      $.ajax({
         url: "/orders/get.php?delete",
         type: "POST",
         dataType: "html",
         data: ({ id: btn.data('id'), }),
         success: function(data){ 
            if (data == 'yes') location.reload();
            console.log(data);
         },
         beforeSend: function(){ },
         error: function(data){ }
      })
	})

	// 
	// Кафе: Столда
	$('html').on('click', '.on_table', function () {
		var btn = $(this)
		$.ajax({
			url: "/orders/get.php?table", type: "POST", dataType: "html",
			data: ({ id: btn.data('id') }),
			success: function(data){ if (data == 'yes') btn.remove(); location.reload() }
		})
	})

	$('.on_check').on('click', function () {
		btn = $(this)
      $.ajax({
         url: "/orders/get.php?check",
         type: "POST",
         dataType: "html",
         data: ({ id: btn.data('id'), }),
         success: function(data){ 
            if (data == 'yes') {
               btn.parents('.uc_ui').remove()
            }
            console.log(data);
         },
         beforeSend: function(){ },
         error: function(data){ }
      })
	})


   // 
	$('.on_staff').on('change', function () {
		btn = $(this)
      text = btn.children('option:selected').html()

      $.ajax({
         url: "/orders/get.php?change_staff",
         type: "POST",
         dataType: "html",
         data: ({ 
            id: btn.children('option:selected').attr('data-id'),
            order_id: btn.attr('data-order-id'),
         }),
         success: function(data){ 
            if (data == 'yes') {
               // location.reload();
               btn.parents('.uc_uil2_sel').siblings('.uc_uil2_mi').children('.uc_uil2_mi2').html(text)
            }
            console.log(data);
         },
         beforeSend: function(){ },
         error: function(data){ }
      })
	})


   // 
	$('.on_status').on('change', function () {
      // id = $(this).children('option:selected').attr('data-id')
		btn = $(this)
      $.ajax({
         url: "/orders/get.php?change_status",
         type: "POST",
         dataType: "html",
         data: ({ 
            id: btn.children('option:selected').attr('data-id'),
            order_id: btn.attr('data-order-id'),
         }),
         success: function(data){ 
            // if (data == 'yes') location.reload();
            console.log(data);
         },
         beforeSend: function(){ },
         error: function(data){ }
      })
	})
   
   
   
   // 
	$('.on_sort_status').on('change', function () {
      var val = $(this).children('option:selected').attr('data-id');
      const url = new URL(window.location);
      url.searchParams.set('status', val); 
      history.pushState(null, null, url);
      location.reload();
	})
   
   // 
	$('.on_sort_staff').on('change', function () {
      var val = $(this).children('option:selected').attr('data-id');
      const url = new URL(window.location);
      url.searchParams.set('staff', val); 
      history.pushState(null, null, url);
      location.reload();
	})
   
   // 
	$('.on_sort_branch').on('click', function () {
      var val = $(this).attr('data-id');
      const url = new URL(window.location);
      url.searchParams.set('branch', val); 
      history.pushState(null, null, url);
      location.reload();
	})




	$('.on_sort_time').on('change', function () {
      var val = $(this).val();
      const url = new URL(window.location);
      url.searchParams.set('time', val);
      history.pushState(null, null, url);
      location.reload();
	})














	$('.on_print').on('click', function () {
      window.open("/orders/" + "order_print.php?" + "&orderID=" + $(this).attr('data-id'), "mywin","width=570,height=570,left=250,top=50");
	})
	// $('.on_print').on('click', function () {

	// })














	// cashbox_pay
	$('.cashbox_pay').click(function(){
		$('.cashbox_pay_block').addClass('pop_bl_act');
		$('#html').addClass('ovr_h');
	})
	$('.cashbox_pay_back').click(function(){
		$('.cashbox_pay_block').removeClass('pop_bl_act');
		$('#html').removeClass('ovr_h');
	})

   // cashbox_pay
	$('.cashbox_pay2').on('click', function () {
		btn = $(this)
      $.ajax({
         url: "/orders/get.php?cashbox_pay",
         type: "POST",
         dataType: "html",
         data: ({ 
            number: $('.order_number_sel').attr('data-val'),
            total: $('.btype_totol').attr('data-val'),
            delivery: $('.btype_delivery').attr('data-val'),
            qr: $('.btype_qr').attr('data-val'),
            branch: btn.attr('data-branch'),
         }),
         success: function(data){
            if (btn.attr('data-type') == 'check') {
               
            } else {
               if (data == 'yes') location.reload();
               else if (data == 0) mess('Вам необходимо заполнить все поля')
            }
            console.log(data);
         },
         beforeSend: function(){ },
         error: function(data){ }
      })
	})


}) // end jquery

// Курьер таңдау: астынан шығатын тізім
;(function () {
	var sh = document.querySelector('.bp_staff_sh'), order = 0, busy = false
	if (!sh) return
	document.addEventListener('click', function (e) {
		var b = e.target.closest('.on_pick_staff')
		if (b) {
			order = b.dataset.orderId
			sh.querySelectorAll('.bp_staff_i').forEach(function (i) { i.classList.toggle('bp_li_act', i.dataset.id == b.dataset.cur) })
			sh.querySelector('.bp_staff_off').style.display = b.dataset.cur != '0' ? '' : 'none'
			bpSheet(sh, true); return
		}
		var it = e.target.closest('.bp_staff_i')
		if (!it || !order || busy) return
		busy = true
		$.ajax({ url: "/orders/get.php?change_staff", type: "POST", dataType: "html", data: ({ id: it.dataset.id, order_id: order }),
			success: function (data) { busy = false; if (data == 'locked') { bpSheet(sh, false); location.reload(); return } if (data == 'yes') location.reload() },
			error: function () { busy = false } })
	})
})()

// Курьер сүзгісі: тізім астынан ашылады
document.addEventListener('click', function (e) { if (e.target.closest('.on_pick_flt')) bpSheet(document.querySelector('.bp_flt_sh'), true) })

// «Толығырақ»: тапсырыс тарихы астынан шығатын попапта
document.addEventListener('click', function (e) {
	var b = e.target.closest('.on_hist'); if (!b) return
	var sh = document.querySelector('.bp_hist_sh'); if (!sh) return
	sh.querySelector('.bp_hist_nm').textContent = '№' + b.dataset.nm
	sh.querySelector('.oh_list').innerHTML = '<div class="oh_none">…</div>'
	bpSheet(sh, true)
	$.ajax({ url: "/orders/get.php?history", type: "POST", dataType: "html", data: ({ id: b.dataset.id }),
		success: function (data) { sh.querySelector('.oh_list').innerHTML = data } })
})

// Тағамдар 3-тен көп болса: қалғанын ашу / жасыру
document.addEventListener('click', function (e) {
	var b = e.target.closest('.on_more'); if (!b) return
	var box = b.parentNode, open = box.classList.toggle('bp_more_on')
	b.querySelector('span').textContent = open ? b.dataset.less : b.dataset.more
})

// Тарих: «Статус» / «Өзгерістер» табтары
document.addEventListener('click', function (e) {
	var t = e.target.closest('.oh_tab'); if (!t) return
	var box = t.parentNode.parentNode
	box.querySelectorAll('.oh_tab').forEach(function (x) { x.classList.toggle('oh_tab_act', x === t) })
	box.querySelectorAll('.oh_pane').forEach(function (p) { p.classList.toggle('dsp_n', p.dataset.p != t.dataset.t) })
})
