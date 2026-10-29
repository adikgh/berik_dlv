<? include "../config/core.php";

	// 
	if (!$user_id) header('location: /');
	if ($user['password'] == '123456') header('location: /pass.php');


	$company_d = mysqli_fetch_assoc(db::query("SELECT * FROM company WHERE `id` = '$company'"));

	$city = $company_d['city'];

   	$type = @$_GET['type'];
   	$sort = 'new'; 
	if (@$_GET['sort']) $sort = @$_GET['sort'];
	if (@$_GET['branch']) $branch = @$_GET['branch'];
	

	if (@$_GET['time']) {
		$time_sort = $_GET['time'];
		$start_cdate = date('Y-m-d 06:00:00', strtotime("$time_sort"));
		$end_cdate = date('Y-m-d 06:00:00', strtotime("$start_cdate +1 day"));
	}
	

	// $start_cdate = '2025-01-10';

	if ($sort == 'new') {
		$orders = db::query("select * from retail_orders where ins_dt BETWEEN '$start_cdate' and '$end_cdate' and `order_type` = 1 and `order_status` in(1, 8, 2, 11, 3) and `сourier_id` = '$user_id' and company_id = '$company' order by number asc");
	} else {
		if ($branch != 0) $orders = db::query("select * from retail_orders where ins_dt BETWEEN '$start_cdate' and '$end_cdate' and `order_type` = 1 and `order_status` in(4, 6) and `сourier_id` = '$user_id' and company_id = '$company' and `branch_id` = '$branch' order by number desc");
		else $orders = db::query("select * from retail_orders where ins_dt BETWEEN '$start_cdate' and '$end_cdate' and `order_type` = 1 and `order_status` in(4, 6) and `сourier_id` = '$user_id' and company_id = '$company' order by number desc");
	}



	$take_ids = []; // «Барлығын қабылдау» үшін
	$route_addr = []; $route_ids = []; // «Маршрут құру» үшін
	$st_clr = []; $stq = db::query("SELECT id, clr FROM retail_orders_status"); while ($sq = mysqli_fetch_assoc($stq)) $st_clr[$sq['id']] = $sq['clr'];
	$map_app = fun::map_app($user_right);
	if (!function_exists('e')) { function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES); } }
	$allorder['number'] = 0;
	$pay_delivery = 0;
	$allorder['pay_delivery'] = 0;
	$allorder['pay_qr'] = 0;
	$allorder['pay_cash'] = 0;


	// site setting
	$menu_name = 'new'; 
	if ($sort == 'history') $menu_name = 'history';
	$pod_menu_name = 'main';
	$css = ['orders'];
	$js = ['orders'];
?>
<? include "../block/header.php"; ?>

	<div class="flex_clm_rev">

		<div class="bl_c">

			<div class="uc_u">

				<? if ($orders != ''): ?>
					<? if (mysqli_num_rows($orders) != 0): ?>
						<? while ($buy_d = mysqli_fetch_assoc($orders)): ?>
							<? if ($buy_d['branch_id']) $branch_d = fun::branch($buy_d['branch_id']); ?>
							<? [$pre_sum, $rest_sum] = fun::prepay($buy_d); ?>

							<div class="uc_ui" style="--st: <?=e(@$st_clr[$buy_d['order_status']] ?: '#10a96a')?>">
								<div class="uc_uil2" >
									<div class="uc_uil2_top">
										<? $item_url = '/orders/item.php?id='.(int)$buy_d['id']; ?>
										<a class="uc_uil2_nmb" href="<?=$item_url?>"><?=$buy_d['number']?></a>
										<a class="uc_uil2_date" href="<?=$item_url?>">
											<?=fun::order_time($buy_d)?>
										</a>
										<? if (in_array((int)$buy_d['order_status'], [1, 8, 2, 11])): // тағайындалды: жеке батырма жоқ, төмендегі «Барлығын қабылдау» ?>
											<? $take_ids[] = (int)$buy_d['id']; ?>
										<? endif ?>
										<? if ($buy_d['order_status'] == 3): // Жолда: Жеткіздім (4) / Отказ при получение (6) ?>
											<div class="uc_uil2_chek">
												<div class="btn on_check uc_chk_btn" data-id="<?=$buy_d['id']?>" data-num="<?=e($buy_d['number'])?>" data-rest="<?=(int)$rest_sum?>"><i class="far fa-check"></i><span>Тапсырдым</span></div>
											</div>
										<? endif ?>
									</div>
									<div class="uc_uil2_raz">
										<div class="uc_uil2_trt">
											<div class="uc_uil2_trt1">Атауы</div>
											<div class="uc_uil2_trt2">Саны</div>
											<div class="uc_uil2_trt3">Бағасы</div>
										</div>
										<div class="uc_uil2_trc">

											<? 	
												$cashbox_id = $buy_d['id'];
												$cashboxp = db::query("select * from retail_orders_products where order_id = '$cashbox_id' order by ins_dt asc");
												$number = 0; $total = 0;
											?>
											<? if (mysqli_num_rows($cashboxp) != 0): ?>
												<? while ($sel_d = mysqli_fetch_assoc($cashboxp)): ?>
													<? 
														$number++; 
														$sum = $sel_d['quantity'] * $sel_d['price']; 
														$total = $total + $sum;
														$product_d = product::product($sel_d['product_id']);
													?>
													<div class="uc_uil2_trt">
														<div class="uc_uil2_trt1"><?=$number?>. <?=$product_d['name_ru']?></div>
														<div class="uc_uil2_trt2"><?=$sel_d['quantity']?> шт</div>
														<!-- <div class=""><?=$sel_d['price']?></div> -->
														<div class="uc_uil2_trt3 fr_price"><?=$sum?></div>
													</div>
												<? endwhile ?>
											<? endif ?>
											
											<div class="uc_uil2_trt">
												<div class="uc_uil2_trt1">Доставка</div>
												<div class="uc_uil2_trt3 fr_price"><?=$buy_d['pay_delivery']?></div>
											</div>
											<div class="uc_uil2_trt">
												<div class="uc_uil2_trt1">Предоплата</div>
												<div class="uc_uil2_trt2">-</div>
												<div class="uc_uil2_trt3 fr_price"><?=$pre_sum?></div>
											</div>
										</div>
										<div class="uc_uil2_trb">
											<div class="uc_uil2_trt1">К оплате</div>
											<div class="uc_uil2_trt2"></div>
											<div class="uc_uil2_trt3 fr_price"><?=$rest_sum?></div>
										</div>
									</div>
									<? if ($buy_d['address']): $route_addr[] = $buy_d['address']; $route_ids[] = (int)$buy_d['id']; if (!isset($route_start)) $route_start = trim((string)@$branch_d['address']); ?>
										<div class="uc_uil2_raz">
											<div class="uc_uil2_mi">
												<div class="uc_uil2_mi1">Адрес:</div>
												<div class="uc_uil2_mi2"><?=htmlspecialchars($buy_d['address'])?></div>
											</div>
											<? $fa = trim((string)$buy_d['floor_apt']); if ($fa !== ''): ?>
												<div class="uc_uil2_fa"><i class="far fa-building"></i> <?=htmlspecialchars(str_replace(', ', ' · ', $fa))?></div>
											<? endif ?>
											<div class="uc_uil2_mib uc_uil2_mib1">
												<a class="btn btn_map" href="<?=e(fun::map_url($map_app, $city, $buy_d['address']))?>" target="_blank"><i class="far fa-map-marked-alt"></i><span>Картадан ашу</span><small><?=($map_app == 'yandex' ? 'Яндекс' : '2GIS')?></small></a>
											</div>
										</div>
									<? endif ?>
									<? if ($buy_d['phone']): ?>
										<div class="uc_uil2_raz">
											<div class="uc_uil2_mi">
												<div class="uc_uil2_mi1">Номер:</div>
												<div class="uc_uil2_mi2 fr_phone"><?=$buy_d['phone']?></div>
											</div>
											<div class="uc_uil2_mib">
												<a class="btn btn_phone" href="tel:8<?=$buy_d['phone']?>">Званок</a>
												<a class="btn btn_whatsapp" href="https://wa.me/7<?=$buy_d['phone']?>" target="_blank">Whatsapp</a>
											</div>
										</div>
									<? endif ?>

									<div class="uc_uil2_raz uc_uil2_mib uc_uil2_mib1">
										<a class="btn btn_cl" href="<?=$item_url?>">Толығырақ</a>
									</div>

									<!-- <div class="uc_uin_other fr_price"><?=$buy_d['pay_qr']?> </div> -->
									<!-- <div class="uc_uin_other fr_price"><?=$buy_d['total'] - $buy_d['pay_qr']?></div> -->
								</div>
							</div>

							<? 
								$allorder['number'] = $allorder['number'] + 1;
								if ($buy_d['order_status'] != 5 && $buy_d['order_status'] != 6) {
									$pay_delivery = $buy_d['pay_delivery'] + $company_d['delivery_price'];

									$allorder['pay_qr'] = $allorder['pay_qr'] + $pre_sum;
									$allorder['pay_delivery'] = $allorder['pay_delivery'] + $pay_delivery;
									// Курьер налды да, Kaspi-ді де өзі қабылдайды, есебін өзі тапсырады — сондықтан қалған сома толық
									$allorder['pay_cash'] = $allorder['pay_cash'] + ($rest_sum - $pay_delivery);
								} elseif ($buy_d['order_status'] == 6) { // Отказ при получение: тек бізде қалған ақша
									$k_pre = min($pre_sum, (int)$buy_d['kept_sum']);
									$allorder['pay_qr'] = $allorder['pay_qr'] + $k_pre;
									$allorder['pay_cash'] = $allorder['pay_cash'] + ((int)$buy_d['kept_sum'] - $k_pre);
								}
							?>

						<? endwhile ?>
					<? else: ?>
						<div class="ds_nr"><i class="fal fa-inbox"></i><p><?=($sort == 'history' ? 'Бұл күні тапсырыс жоқ' : 'Әзірге тапсырыс жоқ')?></p><small><?=($sort == 'history' ? 'Басқа күнді таңдап көріңіз' : 'Жаңа тапсырыс келгенде осы жерде шығады')?></small></div>
					<? endif ?>
				<? else: ?>
					<div class="ds_nr"><i class="fal fa-inbox"></i><p><?=($sort == 'history' ? 'Бұл күні тапсырыс жоқ' : 'Әзірге тапсырыс жоқ')?></p><small><?=($sort == 'history' ? 'Басқа күнді таңдап көріңіз' : 'Жаңа тапсырыс келгенде осы жерде шығады')?></small></div>
				<? endif ?>

			</div>

			<? // Бір орында бір батырма: алдымен «Барлығын қабылдау», бәрі қабылданған соң «Маршрут құру»
				$show_route = $sort == 'new' && empty($take_ids) && count($route_addr) >= 2; ?>
			<? if (!empty($take_ids) || $show_route): ?>
				<div class="take_all_bar">
					<? if (!empty($take_ids)): ?>
						<div class="btn on_take_all" data-ids="<?=implode(',', $take_ids)?>"><i class="far fa-check-double"></i><span>Барлығын қабылдау</span><b class="tab_cnt"><?=count($take_ids)?></b></div>
					<? else: ?>
						<a class="btn on_route" href="<?=e($map_app == '2gis' ? '/orders/route.php?ids='.implode(',', $route_ids) : fun::route_url($city, $route_addr, (string)@$route_start, fun::route_return($user_right)))?>" target="_blank"><i class="far fa-route"></i><span>Маршрут құру</span><b class="tab_cnt"><?=count($route_addr)?></b></a>
					<? endif ?>
				</div>
			<? endif ?>

			<!-- Осы қатарды қосу қажет -->
			<!-- <div class="uc_ukb">
				<div class="btn btn_gr">Барлығы орындалды</div>
			</div> -->

		</div>


		<div class="hil_head">
			<div class="bl_c">

				<div class="hil_headc">
					<? if ($sort == 'history'): ?>
						<h4 class="hil_headc1 txt_c">Орындалған заказдар</h4>
					<? else: ?>
						<h4 class="hil_headc1 txt_c">Жеткізу қажет заказдар</h4>
					<? endif ?>
					<? if ($sort != 'new'): ?>
						<!-- <div class="hil_fr1">
							<div class="hil_fr1c on_sort_branch <?=($branch == 0?'hil_fr1c_act':'')?>" data-id="0">Барлығы</div>
							<div class="hil_fr1c on_sort_branch <?=($branch == 1?'hil_fr1c_act':'')?>" data-id="1">Банзай</div>
							<div class="hil_fr1c on_sort_branch <?=($branch == 2?'hil_fr1c_act':'')?>" data-id="2">Мастер</div>
						</div> -->

						<br>

						<div class="">
							<div class="form_im">
								<input class="form_dt on_sort_time" type="date" name="" id="" value="<?=date('Y-m-d', strtotime("$start_cdate"))?>">
							</div>
						</div>

					<? endif ?>
					<div class="hil_headc2">
						<div class="hil_headc2s">
							<span>Заказ саны:</span>
							<p><?=$allorder['number']?> шт</p>
						</div>
						<div class="hil_headc2s">
							<span>Доставкаға:</span>
							<p class="fr_price"><?=$allorder['pay_delivery']?></p>
						</div>
						<? if ($sort != 'new'): ?>
							<div class="hil_headc2s">
								<span>Предоплата:</span>
								<p class="fr_price"><?=$allorder['pay_qr']?></p>
							</div>
							<? $kas = (int)$allorder['pay_cash']; // минус: касса курьерге қайтарады ?>
							<div class="hil_headc2s">
								<span>Кассаға:</span>
								<p<?=($kas < 0 ? ' class="hil_red"' : '')?>><?=($kas < 0 ? '−' : '').number_format(abs($kas), 0, '', ' ')?> тг</p>
							</div>
						<? endif ?>

					</div>
				</div>

			</div>
		</div>

	</div>

<? include "../block/footer.php"; ?>