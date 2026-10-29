<? include "../config/core.php";

	// Тапсырыстың ішкі беті (курьер тек өз тапсырысын көреді)
	if (!$user_id) { header('location: /'); exit; }
	if ($user['password'] == '123456') { header('location: /pass.php'); exit; }

	$id = (int)@$_GET['id'];
	$st = mysqli_prepare(db::$connect, "SELECT * FROM retail_orders WHERE id = ? AND `сourier_id` = ? AND company_id = ? LIMIT 1");
	mysqli_stmt_bind_param($st, 'iii', $id, $user_id, $company);
	mysqli_stmt_execute($st);
	$order = mysqli_fetch_assoc(mysqli_stmt_get_result($st));
	if (!$order) { header('location: /orders/'); exit; }

	$company_d = mysqli_fetch_assoc(db::query("SELECT * FROM company WHERE id = '".(int)$company."'"));
	$city = (string)@$company_d['city'];
	fun::$company_ya = trim((string)@$company_d['ya_city']); // Яндекс беті (company.ya_city)
	$branch_d = $order['branch_id'] ? fun::branch($order['branch_id']) : [];
	$status_d = mysqli_fetch_assoc(db::query("SELECT * FROM retail_orders_status WHERE id = '".(int)$order['order_status']."'"));

	// Этаж / пәтер: «3 этаж, 12 кв»
	$fa = trim((string)$order['floor_apt']);
	$floor = preg_match('/(\S+) этаж/u', $fa, $m) ? $m[1] : '';
	$apt = preg_match('/(\S+) кв/u', $fa, $m) ? $m[1] : '';

	[$pre_sum, $rest_sum] = fun::prepay($order); // кассадағыдай есеп
	$products = db::query("SELECT * FROM retail_orders_products WHERE order_id = '$id' ORDER BY ins_dt ASC");

	// Статус тарихы
	$log = db::query("SELECT l.new_value, l.ins_dt, s.name_kz FROM retail_orders_log l LEFT JOIN retail_orders_status s ON s.id = l.new_value WHERE l.order_id = '$id' AND l.field = 'status' ORDER BY l.id ASC");

	if (!function_exists('e')) { function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES); } }

	// site setting
	$done = in_array((int)$order['order_status'], [4, 5, 6, 10]); // аяқталған → «Тапсырылған» мәзірі
	$menu_name = $done ? 'history' : 'new';
	$css = ['orders'];
	$js = ['orders'];
?>
<? include "../block/header.php"; ?>

	<div class="bl_c ord_page">

		<a class="ord_back" href="/orders/<?=($done ? '?sort=history&time='.date('Y-m-d', strtotime($order['ins_dt']) - 6 * 3600) : '')?>"><i class="far fa-long-arrow-left"></i> Тапсырыстар</a>

		<div class="uc_ui" style="--st: <?=e(@$status_d['clr'] ?: '#10a96a')?>">
			<div class="uc_uil2">

				<div class="uc_uil2_top">
					<div class="uc_uil2_nmb"><?=e($order['number'])?></div>
					<div class="uc_uil2_date">
						<?=fun::order_time($order)?>
					</div>
				</div>

				<? if ($status_d): ?>
					<div class="ord_status" style="background: <?=e($status_d['clr'] ?: '#888')?>"><?=e($status_d['name_kz'])?></div>
				<? endif ?>

				<? if ($order['address']): ?>
					<div class="uc_uil2_raz">
						<div class="ord_lbl">Адрес</div>
						<div class="ord_addr"><?=e($order['address'])?></div>
						<? if ($floor !== '' || $apt !== ''): ?>
							<div class="ord_fa">
								<? if ($floor !== ''): ?><div class="ord_fa_i"><span>Этаж</span><b><?=e($floor)?></b></div><? endif ?>
								<? if ($apt !== ''): ?><div class="ord_fa_i"><span>Пәтер</span><b><?=e($apt)?></b></div><? endif ?>
							</div>
						<? elseif ($fa !== ''): ?>
							<div class="uc_uil2_fa"><i class="far fa-building"></i> <?=e($fa)?></div>
						<? endif ?>
						<div class="uc_uil2_mib uc_uil2_mib1">
							<a class="btn btn_map" href="<?=e(fun::map_url(fun::map_app($user_right), $city, $order['address']))?>" target="_blank"><i class="far fa-map-marked-alt"></i><span>Картадан ашу</span><small><?=(fun::map_app($user_right) == 'yandex' ? 'Яндекс' : '2GIS')?></small></a>
						</div>
					</div>
				<? endif ?>

				<? if ($order['phone']): ?>
					<div class="uc_uil2_raz">
						<div class="ord_lbl">Номер</div>
						<div class="ord_addr fr_phone"><?=e($order['phone'])?></div>
						<div class="uc_uil2_mib">
							<a class="btn btn_phone" href="tel:8<?=e($order['phone'])?>">Званок</a>
							<a class="btn btn_whatsapp" href="https://wa.me/7<?=e($order['phone'])?>" target="_blank">Whatsapp</a>
						</div>
					</div>
				<? endif ?>

				<? if (trim((string)$order['additional']) !== '' || trim((string)$order['comment']) !== ''): ?>
					<div class="uc_uil2_raz">
						<div class="ord_lbl">Ескерту</div>
						<? if (trim((string)$order['additional']) !== ''): ?><div class="ord_note"><?=nl2br(e($order['additional']))?></div><? endif ?>
						<? if (trim((string)$order['comment']) !== ''): ?><div class="ord_note"><?=nl2br(e($order['comment']))?></div><? endif ?>
					</div>
				<? endif ?>

				<div class="uc_uil2_raz">
					<div class="uc_uil2_trt">
						<div class="uc_uil2_trt1">Атауы</div>
						<div class="uc_uil2_trt2">Саны</div>
						<div class="uc_uil2_trt3">Бағасы</div>
					</div>
					<div class="uc_uil2_trc">
						<? $n = 0; while ($p = mysqli_fetch_assoc($products)): $n++; $product_d = product::product($p['product_id']); ?>
							<div class="uc_uil2_trt">
								<div class="uc_uil2_trt1"><?=$n?>. <?=strip_tags((string)@$product_d['name_ru'], '<b><i><span>')?></div>
								<div class="uc_uil2_trt2"><?=e($p['quantity'])?> шт</div>
								<div class="uc_uil2_trt3 fr_price"><?=$p['quantity'] * $p['price']?></div>
							</div>
						<? endwhile ?>
						<div class="uc_uil2_trt">
							<div class="uc_uil2_trt1">Доставка</div>
							<div class="uc_uil2_trt3 fr_price"><?=(int)$order['pay_delivery']?></div>
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

				<? if ((int)$order['order_status'] == 6): ?>
					<div class="uc_uil2_raz">
						<div class="ord_lbl">Алғанда бас тарту</div>
						<div class="ord_note">Бізде қалды: <b class="fr_price"><?=(int)$order['kept_sum']?></b></div>
						<? if (trim((string)$order['refuse_reason']) !== ''): ?><div class="ord_note">Себебі: <?=e($order['refuse_reason'])?></div><? endif ?>
					</div>
				<? endif ?>

				<? if (mysqli_num_rows($log)): ?>
					<div class="uc_uil2_raz">
						<div class="ord_lbl">Тарих</div>
						<? while ($l = mysqli_fetch_assoc($log)): ?>
							<div class="ord_log"><span><?=date("H:i", strtotime($l['ins_dt']))?></span> <?=e($l['name_kz'] ?: $l['new_value'])?></div>
						<? endwhile ?>
					</div>
				<? endif ?>

				<? if (in_array((int)$order['order_status'], [1, 8, 2, 11])): ?>
					<div class="uc_uil2_raz uc_uil2_mib uc_uil2_mib1">
						<div class="btn btn_cl on_take" data-id="<?=$id?>">Алдым</div>
					</div>
				<? elseif ((int)$order['order_status'] == 3): ?>
					<div class="uc_uil2_raz ord_acts">
						<div class="btn on_check" data-id="<?=$id?>" data-num="<?=e($order['number'])?>" data-rest="<?=(int)$rest_sum?>"><i class="far fa-check"></i> Тапсырылды</div>
						<div class="btn btn_cl on_refuse" data-id="<?=$id?>" data-kept="<?=$pre_sum?>"><i class="far fa-times"></i> Отказ</div>
					</div>
				<? endif ?>

			</div>
		</div>

	</div>

<? include "../block/footer.php"; ?>
