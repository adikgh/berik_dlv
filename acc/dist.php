<? // Раздача аккаунты. acc/index.php-ден шақырылады ($dist). Тікелей ашылса — аккаунтқа.
	if (empty($dist)) { header('location: /acc/'); exit; }

	// Қолданушыны тексеру
	if (!$user_id) { header('location: /'); exit(); }

	// Сайттың баптаулары
	$menu_name = 'acc';
	$site_set['utop_nm'] = t::w('More menu', 'Тағы');
	$css = ['acc'];
	$js = ['admin'];
	$pos_nm = [1 => t::w('Creator', 'Создатель'), 2 => t::w('Director', 'Директор'), 3 => t::w('Manager', 'Басқарушы'), 8 => t::w('Dispatch', 'Раздача')];
?>
<? include "../block/header.php"; ?>

	<div class="bp_page bp_acc">
		<div class="bl_c">

			<div class="bp_prof">
				<div class="bp_prof_av lazy_img" <?=(@$user['img'] ? 'data-src="/assets/uploads/users/'.htmlspecialchars($user['img']).'"' : '')?>><? if (!@$user['img']): ?><?=htmlspecialchars(mb_strtoupper(mb_substr(trim((string)$user['name']), 0, 1)))?><? endif ?></div>
				<div class="bp_prof_t">
					<div class="bp_prof_n"><?=htmlspecialchars(trim($user['name'].' '.$user['surname']))?></div>
					<div class="bp_prof_p fr_phone"><?=htmlspecialchars((string)$user['phone'])?></div>
					<div class="bp_prof_r">
						<? if (isset($pos_nm[(int)@$user_right['positions_id']])): ?><span><?=$pos_nm[(int)$user_right['positions_id']]?></span><? endif ?>
						<? if (@$company_d['name']): ?><span><?=htmlspecialchars($company_d['name'])?></span><? endif ?>
					</div>
				</div>
			</div>

			<div class="bp_box">
				<div class="bp_box_h"><i class="far fa-globe"></i> <?=t::w('Language', 'Тіл')?></div>
				<div class="bp_lang">
					<? foreach ($langs_short as $lk => $lv): ?>
						<a class="bp_lang_i <?=($ui_lang == $lk ? 'bp_lang_act' : '')?>" href="?lang=<?=$lk?>"><?=$lv?></a>
					<? endforeach ?>
				</div>
			</div>

			<? // «Тағы»: тапсырыс бөлімдері бөлек блок, басқа бөлімдер (Оплата т.б.) аккаунт блогында
				$pm_ord = []; $pm_oth = [];
				foreach ($pm_more ?? [] as $p) { if ($p[1] == '/acc/') continue; if (strpos($p[1], '/orders/') === 0) $pm_ord[] = $p; else $pm_oth[] = $p; }
				$pm_ord[] = [false, '/orders/?sort=none', 'fa-ban', t::w('Refusals', 'Бас тарту')]; ?>
			<div class="bp_box bp_list">
				<div class="bp_box_h"><i class="far fa-receipt"></i> <?=t::w('Orders', 'Тапсырыстар')?></div>
				<? foreach ($pm_ord as $p): ?>
					<a class="bp_li" href="<?=$p[1]?>">
						<span class="bp_li_ic"><i class="far <?=$p[2]?>"></i></span>
						<span class="bp_li_t"><?=$p[3]?></span>
						<i class="far fa-chevron-right bp_li_ar"></i>
					</a>
				<? endforeach ?>
			</div>

			<div class="bp_box bp_list">
				<div class="bp_box_h"><i class="far fa-user-friends"></i> <?=t::w('Staff', 'Жұмысшылар')?></div>
				<? // Бүгінгі смена: бүгін касса сменасын ашқан, тапсырыс статусын өзгерткен немесе тапсырыс алған курьер
					$c = (int)$company; $d0 = date('Y-m-d 00:00:00'); $d1 = date('Y-m-d 00:00:00', strtotime('-1 day'));
					$sh_q = db::query("SELECT u.id, u.name, u.surname, MIN(x.t) t, MAX(x.op) op,
							(SELECT s.positions_id FROM company_staff s WHERE s.user_id = u.id AND s.company_id = '$c' ORDER BY s.id LIMIT 1) pos
						FROM (
							SELECT user_id uid, ins_dt t, (close_dt IS NULL) op FROM retail_shifts WHERE company_id = '$c' AND (ins_dt >= '$d0' OR close_dt IS NULL)
							UNION ALL SELECT l.user_id, l.ins_dt, 0 FROM retail_orders o JOIN retail_orders_log l ON l.order_id = o.id WHERE o.company_id = '$c' AND o.ins_dt >= '$d1' AND l.ins_dt >= '$d0'
							UNION ALL SELECT `сourier_id`, ins_dt, 0 FROM retail_orders WHERE company_id = '$c' AND ins_dt >= '$d0' AND `сourier_id` > 0
						) x JOIN user u ON u.id = x.uid GROUP BY u.id ORDER BY t");
					$sh_pos = [];
					$pq = db::query("SELECT id, name_kz, name_ru FROM company_staff_positions");
					while ($pr = mysqli_fetch_assoc($pq)) $sh_pos[$pr['id']] = ($ui_lang == 'kz' ? $pr['name_kz'] : '') ?: $pr['name_ru']; ?>
				<div class="bp_sub"><?=t::w('Today shift', 'Бүгінгі смена')?><? if (mysqli_num_rows($sh_q)): ?> <b><?=mysqli_num_rows($sh_q)?></b><? endif ?></div>
				<? while ($sh = mysqli_fetch_assoc($sh_q)): $sh_n = trim($sh['name'].' '.$sh['surname']); ?>
					<div class="bp_li bp_sh">
						<span class="bp_li_ic"><?=htmlspecialchars(mb_strtoupper(mb_substr($sh_n, 0, 1)))?></span>
						<span class="bp_li_t"><?=htmlspecialchars($sh_n)?><small><?=htmlspecialchars((string)@$sh_pos[$sh['pos']])?> · <?=date('H:i', strtotime($sh['t']))?></small></span>
						<? if ($sh['op']): ?><span class="bp_sh_on" title="<?=t::h('Shift open', 'Смена ашық')?>"></span><? endif ?>
					</div>
				<? endwhile ?>
				<? if (!mysqli_num_rows($sh_q)): ?><div class="bp_li bp_sh bp_sh_e"><span class="bp_li_t"><small><?=t::w('Nobody on shift yet', 'Бүгін әзірге ешкім жоқ')?></small></span></div><? endif ?>
				<a class="bp_li" href="/users/">
					<span class="bp_li_ic"><i class="far fa-users"></i></span>
					<span class="bp_li_t"><?=t::w('Couriers', 'Курьерлер')?></span>
					<i class="far fa-chevron-right bp_li_ar"></i>
				</a>
			</div>

			<div class="bp_box bp_list">
				<? foreach ($pm_oth as $p): ?>
					<a class="bp_li" href="<?=$p[1]?>">
						<span class="bp_li_ic"><i class="far <?=$p[2]?>"></i></span>
						<span class="bp_li_t"><?=$p[3]?></span>
						<i class="far fa-chevron-right bp_li_ar"></i>
					</a>
				<? endforeach ?>
				<div class="bp_li pass_ubd_btn">
					<span class="bp_li_ic"><i class="far fa-key"></i></span>
					<span class="bp_li_t"><?=t::w('Change password', 'Құпиясөзді өзгерту')?></span>
					<i class="far fa-chevron-right bp_li_ar"></i>
				</div>
				<? if (@$site['whatsapp']): ?>
					<a class="bp_li" href="https://wa.me/<?=htmlspecialchars($site['whatsapp'])?>" target="_blank" rel="noopener">
						<span class="bp_li_ic"><i class="fab fa-whatsapp"></i></span>
						<span class="bp_li_t"><?=t::w('Help (WhatsApp)', 'Көмек (WhatsApp)')?></span>
						<i class="far fa-chevron-right bp_li_ar"></i>
					</a>
				<? endif ?>
			</div>

			<a class="bp_exit" href="/exit.php">
				<i class="far fa-sign-out"></i>
				<span><?=t::w('Log out', 'Шығу')?></span>
			</a>

			<div class="up_cg">
				<a href="https://gprog.kz" target="_blank" rel="noopener" class="gprog_bl">
					<span>#gprog-та</span>
					<div class="gprog_heart"><i class="fas fa-heart"></i></div>
					<span><?=t::w('made', 'жасалған')?></span>
				</a>
				<div class="up_brand">Berik POS</div>
			</div>

		</div>
	</div>

<? include "../block/footer.php"; ?>

	<!-- Құпиясөзді өзгерту -->
	<div class="pop_bl pop_bl2 pass_ubd">
		<div class="pop_bl_a pass_ubd_back"></div>
		<div class="pop_bl_c">
			<div class="head_c">
				<h4><?=t::w('Change password', 'Құпиясөзді өзгерту')?></h4>
				<div class="btn btn_dd pass_ubd_back"><i class="fal fa-times"></i></div>
			</div>
			<div class="pop_bl_cl">
				<div class="form_c">
					<div class="form_im ">
						<div class="form_span"><?=t::w('Password', 'Құпиясөз')?>:</div>
						<input type="text" class="form_txt pass" placeholder="<?=t::h('New password', 'Жаңа құпиясөз')?>" data-lenght="6">
						<i class="fal fa-key form_icon"></i>
					</div>
					<div class="form_im form_im_bn">
						<div class="btn pass_ubd_send" data-id="<?=$user_id?>"><?=t::w('Save', 'Сақтау')?></div>
					</div>
				</div>
			</div>
		</div>
	</div>
