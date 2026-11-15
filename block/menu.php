<? if ($site_set['menu']): ?>
<? if ($dist): // Раздача: тапсырыс түрлері + аккаунт ?>
	<? // Тапсырыс түрлері базадан (retail_orders_type), компанияда өшірулі түр (company.delivery / yourself / service / coffee) шықпайды
		$pm_types = [
			'delivery' => ['sort' => 'new', 'menu' => 'car', 'ico' => 'fa-car', 'key' => 'Delivery', 'kz' => 'Жеткізу'],
			'yourself' => ['sort' => 'myself', 'menu' => 'user', 'ico' => 'fa-walking', 'key' => 'Pickup', 'kz' => 'Өзімен'],
			'service' => ['sort' => 'service', 'menu' => 'service', 'ico' => 'fa-backpack', 'key' => 'Service', 'kz' => 'Сервис'],
			'coffee' => ['sort' => 'coffee', 'menu' => 'coffee', 'ico' => 'fa-utensils', 'key' => 'Cafe', 'kz' => 'Кафе'],
		];
		$pm_q = db::query("select nms from retail_orders_type order by number asc"); ?>
	<div class="pmenu">
		<div class="pmenu_c">
			<span class="pmenu_sl"></span>
			<? while ($pm_t = mysqli_fetch_assoc($pm_q)): $pm = @$pm_types[$pm_t['nms']]; ?>
				<? if ($pm && !empty($company_d[$pm_t['nms']])): ?>
					<a class="pmenu_i <?=($menu_name == $pm['menu'] ? 'pmenu_i_act' : '')?>" href="/orders/?sort=<?=$pm['sort']?>">
						<i class="far <?=$pm['ico']?>"></i>
						<span><?=t::w($pm['key'], $pm['kz'])?></span>
					</a>
				<? endif ?>
			<? endwhile ?>
			<a class="pmenu_i <?=($menu_name=='acc' || $menu_name=='users' || $menu_name=='none'?'pmenu_i_act':'')?>" href="/acc/">
				<i class="far fa-user"></i>
				<span><?=t::w('Account', 'Аккаунт')?></span>
			</a>
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