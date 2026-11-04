<? if ($site_set['menu']): ?>
	<? // Тапсырыс түрлері базадан (retail_orders_type), компанияда өшірулі түр (company.delivery / yourself / service / coffee) шықпайды
		$pm_types = [
			'delivery' => ['sort' => 'new', 'menu' => 'car', 'ico' => 'fa-car', 'kz' => 'Жеткізу', 'ru' => 'Доставка'],
			'yourself' => ['sort' => 'myself', 'menu' => 'user', 'ico' => 'fa-walking', 'kz' => 'Өзімен', 'ru' => 'Собой'],
			'service' => ['sort' => 'service', 'menu' => 'service', 'ico' => 'fa-backpack', 'kz' => 'Сервис', 'ru' => 'Сервис'],
			'coffee' => ['sort' => 'coffee', 'menu' => 'coffee', 'ico' => 'fa-utensils', 'kz' => 'Кафе', 'ru' => 'Кафе'],
		];
		$pm_q = db::query("select nms from retail_orders_type order by number asc"); ?>
	<div class="pmenu">
		<div class="pmenu_c">
			<span class="pmenu_sl"></span>
			<? while ($pm_t = mysqli_fetch_assoc($pm_q)): $pm = @$pm_types[$pm_t['nms']]; ?>
				<? if ($pm && !empty($company_d[$pm_t['nms']])): ?>
					<a class="pmenu_i <?=($menu_name == $pm['menu'] ? 'pmenu_i_act' : '')?>" href="/orders/?sort=<?=$pm['sort']?>">
						<i class="far <?=$pm['ico']?>"></i>
						<span><?=t::w($pm['kz'], $pm['ru'])?></span>
					</a>
				<? endif ?>
			<? endwhile ?>
			<a class="pmenu_i <?=($menu_name=='acc' || $menu_name=='users' || $menu_name=='none'?'pmenu_i_act':'')?>" href="/acc/">
				<i class="far fa-user"></i>
				<span><?=t::w('Аккаунт', 'Аккаунт')?></span>
			</a>
		</div>
	</div>

<? endif ?>
