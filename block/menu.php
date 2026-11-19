<? if ($site_set['menu']): ?>
<? if ($dist): // Раздача: ең көбі 4 батырма — 3 тапсырыс түрі + «Тағы» (қалған түрлер, Оплата, Аккаунт) ?>
	<? // Тапсырыс түрлері базадан (retail_orders_type), компанияда өшірулі түр (company.delivery / yourself / service / coffee) шықпайды
		$pm_types = [
			'delivery' => ['sort' => 'new', 'menu' => 'car', 'ico' => 'fa-car', 'key' => 'Delivery', 'kz' => 'Жеткізу'],
			'yourself' => ['sort' => 'myself', 'menu' => 'user', 'ico' => 'fa-walking', 'key' => 'Pickup', 'kz' => 'Өзімен'],
			'service' => ['sort' => 'service', 'menu' => 'service', 'ico' => 'fa-backpack', 'key' => 'Service', 'kz' => 'Сервис'],
			'coffee' => ['sort' => 'coffee', 'menu' => 'coffee', 'ico' => 'fa-utensils', 'key' => 'Cafe', 'kz' => 'Кафе'],
		];
		$pm_all = []; // [белсенді ме, сілтеме, иконка, атауы]
		$pm_q = db::query("select nms from retail_orders_type order by number asc");
		while ($pm_t = mysqli_fetch_assoc($pm_q)) {
			$pm = @$pm_types[$pm_t['nms']];
			if ($pm && !empty($company_d[$pm_t['nms']])) $pm_all[] = [$menu_name == $pm['menu'], '/orders/?sort=' . $pm['sort'], $pm['ico'], t::w($pm['key'], $pm['kz'])];
		}
		$pm_main = array_slice($pm_all, 0, 3);
		$pm_more = array_slice($pm_all, 3);
		$pm_more[] = [$menu_name == 'pay', '/pay/', 'fa-wallet', t::w('Plan payment', 'Оплата')];
		$pm_more[] = [in_array($menu_name, ['acc', 'users', 'none'], true), '/acc/', 'fa-user', t::w('Account', 'Аккаунт')];
		$pm_in_more = (bool)array_filter(array_column($pm_more, 0)); ?>
	<div class="pmenu">
		<div class="pmenu_c">
			<span class="pmenu_sl"></span>
			<? foreach ($pm_main as $p): ?>
				<a class="pmenu_i <?=($p[0] ? 'pmenu_i_act' : '')?>" href="<?=$p[1]?>">
					<i class="far <?=$p[2]?>"></i>
					<span><?=$p[3]?></span>
				</a>
			<? endforeach ?>
			<a class="pmenu_i pmenu_acc <?=($pm_in_more ? 'pmenu_i_act' : '')?>" href="/acc/">
				<i class="far fa-th-large"></i>
				<span><?=t::w('More menu', 'Тағы')?></span>
			</a>
		</div>
	</div>
	<div class="bp_dr bp_acc_sh">
		<div class="bp_dr_bg"></div>
		<div class="bp_dr_c">
			<div class="bp_dr_grab"></div>
			<div class="bp_sh_h"><span><?=t::w('More menu', 'Тағы')?></span> <span class="bp_dr_x"><i class="far fa-times"></i></span></div>
			<div class="bp_list">
				<? foreach ($pm_more as $p): ?>
					<a class="bp_li <?=($p[0] ? 'bp_li_act' : '')?>" href="<?=$p[1]?>">
						<span class="bp_li_ic"><i class="far <?=$p[2]?>"></i></span>
						<span class="bp_li_t"><?=$p[3]?></span>
						<i class="far fa-chevron-right bp_li_ar"></i>
					</a>
				<? endforeach ?>
			</div>
		</div>
	</div>
<? else: // Доставка (курьер) ?>
   <div class="pmenu">
		<div class="pmenu_c">
			<span class="pmenu_sl"></span>
			<? if ($super == 6): ?>
				<a class="pmenu_i txt_c <?=($menu_name=='new'?'pmenu_i_act':'')?>" href="/orders/?sort=new">
					<i class="far fa-list-ol"></i>
					<span><?=t::w('New', 'Жаңа')?></span>
				</a>
				<a class="pmenu_i <?=($menu_name=='history'?'pmenu_i_act':'')?>" href="/orders/?sort=history">
					<i class="far fa-clipboard-list-check"></i>
					<span><?=t::w('Delivered', 'Тапсырылған')?></span>
				</a>
			<? endif ?>

			<a class="pmenu_i <?=($menu_name=='acc'?'pmenu_i_act':'')?>" href="/acc/">
				<i class="far fa-user"></i>
				<span><?=t::w('Account', 'Аккаунт')?></span>
			</a>
		</div>
   </div>
<? endif ?>
<? endif ?>