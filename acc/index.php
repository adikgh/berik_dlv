<? include "../config/core.php";

	// Қолданушыны тексеру
	if (!$user_id) header('location: /');

	// Сайттың баптаулары
	$menu_name = 'acc';
	$site_set['utop_nm'] = 'Жеке деректер';
	$css = ['acc'];
	$js = ['admin'];
	if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
	if (!function_exists('e')) { function e($v) { return htmlspecialchars((string)$v, ENT_QUOTES); } }
?>
<? include "../block/header.php"; ?>

	<div class="up" data-csrf="<?=$_SESSION['csrf']?>">

		<div class="up_top">
			<div class="up_lg">
				<div class="up_logo">
					<? if ($user['img']): ?> <div class="up_logo_c" style="background-image: url('/assets/uploads/users/<?=e(rawurlencode($user['img']))?>')"></div>
					<? else: ?> <div class="up_logo_c up_logo_empty"><i class="fal fa-user"></i></div> <? endif ?>
				</div>
				<label class="up_logo_upd" title="Фото жүктеу"><i class="fal fa-camera"></i><input type="file" class="on_acc_img" accept="image/jpeg,image/png,image/webp" hidden></label>
			</div>
			<div class="up_inf">
				<div class="up_name"><?=e($user['name'])?> <?=e($user['surname'])?></div>
				<div class="up_phone fr_phone"><?=e($user['phone'])?></div>
			</div>
		</div>

		<div class="bl_c">
			
			<div class="up_c">

				<div class="up_lt">
					<div class="up_li on_acc_toggle">
						<div class="menu_cin"><i class="fal fa-user-circle"></i></div>
						<div class="menu_cih">Жеке деректер</div>
					</div>

					<? if ($super == 3 || $user_right['positions_id'] == 2 || $user_right['positions_id'] == 1): ?>
						<a class="up_li" href="/users/">
							<div class="menu_cin"><i class="fal fa-users"></i></div>
							<div class="menu_cih">Курьерлер</div>
						</a>
					<? else: ?>
						<!-- <a class="up_li" href="/users/">
							<div class="menu_cin"><i class="fal fa-history"></i></div>
							<div class="menu_cih">Тарих</div>
						</a> -->
					<? endif ?>
					<? if ($user_right['super_v'] == 1): ?>
						<? if ($super == 6): ?>
							<div class="up_li on_superv6" data-id="<?=$user_right['id']?>" href="/users/">
								<div class="menu_cin"><i class="fal fa-plus"></i></div>
								<div class="menu_cih">Админ болу</div>
							</div>
						<? else: ?>
							<div class="up_li on_superv3" data-id="<?=$user_right['id']?>" href="/users/">
								<div class="menu_cin"><i class="fal fa-plus"></i></div>
								<div class="menu_cih">Курьер болу</div>
							</div>
						<? endif ?>
					<? endif ?>

				</div>

				<? $map_app = fun::map_app($user_right); ?>
				<div class="up_lt">
					<div class="up_li map_app_h">
						<div class="menu_cin"><i class="fal fa-map-marked-alt"></i></div>
						<div class="menu_cih">Негізгі карта</div>
					</div>
					<div class="map_app">
						<div class="btn on_map_app <?=($map_app == '2gis' ? 'map_app_act' : '')?>" data-app="2gis">2GIS</div>
						<div class="btn on_map_app <?=($map_app == 'yandex' ? 'map_app_act' : '')?>" data-app="yandex">Яндекс карта</div>
					</div>
					<label class="rt_ret">
						<span><b>Маршрут соңында базаға қайту</b><small>Соңғы адрестен кейін маршрут точкаға қайтып оралады</small></span>
						<input type="checkbox" class="on_route_return" <?=(fun::route_return($user_right) ? 'checked' : '')?>><i class="rt_sw"></i>
					</label>
				</div>

				<!-- <div class="up_lt">
					<a class="up_li" href="#">
						<div class="menu_cin"><i class="fal fa-question-circle"></i></div>
						<div class="menu_cih">Жиі қойылатын сұрақтар</div>
					</a>
					<a class="up_li" href="https://wa.me/<?=$site['whatsapp']?>">
						<div class="menu_cin"><i class="fal fa-comment-dots"></i></div>
						<div class="menu_cih">Көмек (Whatsapp)</div>
					</a>
				</div> -->
				
				<!-- <div class="up_lt">
					<a class="up_li" href="#">
						<div class="menu_cin"><i class="fal fa-award"></i></div>
						<div class="menu_cih">Академия жайлы</div>
					</a>
					<a class="up_li" href="/education/all/offer.php">
						<div class="menu_cin"><i class="fal fa-users-cog"></i></div>
						<div class="menu_cih">Қолдану ережелері</div>
					</a>
					<a class="up_li" href="/education/all/privacy.php">
						<div class="menu_cin"><i class="fal fa-users-cog"></i></div>
						<div class="menu_cih">Авторлық құқық</div>
					</a>
				</div> -->

				<div class="up_exit">
					<a class="btn btn_back2" href="/exit.php">
						<i class="far fa-sign-out"></i>
						<span>Шығу</span>
					</a>
				</div>

			</div>
			<div class="up_cg">
				<a href="https://gprog.kz" target="_blank" class="gprog_bl">
					<span>#gprog-та</span>
					<div class="gprog_heart"><i class="fas fa-heart"></i></div>
					<span>жасалған</span>
				</a>
				<div class="up_brand">Berik POS</div>
			</div>

		</div>
	</div>

	<!-- Жеке деректер: попап (кассадағы acc_drw сияқты; телефонда төменнен шығады) -->
	<div class="acc_drw" aria-hidden="true">
		<div class="acc_drw_a on_acc_close"></div>
		<div class="acc_drw_c" role="dialog" aria-labelledby="acc_drw_t">
			<div class="acc_drw_grab"></div>
			<div class="acc_drw_hd">
				<h4 id="acc_drw_t">Жеке деректер</h4>
				<div class="btn btn_cl on_acc_close" title="Жабу"><i class="far fa-times"></i></div>
			</div>
			<form class="acc_form">
				<label class="acc_fl"><span>Аты</span><input class="acc_inp" name="name" value="<?=e($user['name'])?>" maxlength="60" autocomplete="given-name" required></label>
				<label class="acc_fl"><span>Тегі</span><input class="acc_inp" name="surname" value="<?=e($user['surname'])?>" maxlength="60" autocomplete="family-name"></label>
				<label class="acc_fl"><span>Телефон</span><input class="acc_inp fr_phone" name="phone" type="tel" inputmode="numeric" value="<?=e($user['phone'])?>" placeholder="8 (700) 000-00-00" autocomplete="tel" required></label>
				<div class="acc_msg"></div>
				<button class="btn acc_save" type="submit">Сақтау</button>
			</form>
		</div>
	</div>

<? include "../block/footer.php"; ?>