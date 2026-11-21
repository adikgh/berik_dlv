<? include "../config/core.php";
	if ($dist) { require __DIR__.'/dist.php'; exit; } // Раздача аккаунты

	// Қолданушыны тексеру
	if (!$user_id) { header('location: /'); exit; }

	// Сайттың баптаулары
	$menu_name = 'acc';
	$site_set['utop_nm'] = t::w('More menu', 'Тағы');
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
				<label class="up_logo_upd" title="<?=t::h('Upload photo', 'Фото жүктеу')?>"><i class="fal fa-camera"></i><input type="file" class="on_acc_img" accept="image/jpeg,image/png,image/webp" hidden></label>
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
						<div class="menu_cih"><?=t::w('Personal data', 'Жеке деректер')?></div>
					</div>
					<div class="up_li on_pw_open">
						<div class="menu_cin"><i class="fal fa-key"></i></div>
						<div class="menu_cih"><?=t::w('Change password', 'Құпиясөзді өзгерту')?></div>
					</div>

					<? if ($super == 3 || $user_right['positions_id'] == 2 || $user_right['positions_id'] == 1): ?>
						<a class="up_li" href="/users/">
							<div class="menu_cin"><i class="fal fa-users"></i></div>
							<div class="menu_cih"><?=t::w('Couriers', 'Курьерлер')?></div>
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
								<div class="menu_cih"><?=t::w('Become admin', 'Админ болу')?></div>
							</div>
						<? else: ?>
							<div class="up_li on_superv3" data-id="<?=$user_right['id']?>" href="/users/">
								<div class="menu_cin"><i class="fal fa-plus"></i></div>
								<div class="menu_cih"><?=t::w('Become courier', 'Курьер болу')?></div>
							</div>
						<? endif ?>
					<? endif ?>

				</div>

				<? $map_app = fun::map_app($user_right); ?>
				<div class="up_lt">
					<div class="up_li map_app_h">
						<div class="menu_cin"><i class="fal fa-map-marked-alt"></i></div>
						<div class="menu_cih"><?=t::w('Main map', 'Негізгі карта')?></div>
					</div>
					<div class="map_app">
						<div class="btn on_map_app <?=($map_app == '2gis' ? 'map_app_act' : '')?>" data-app="2gis">2GIS</div>
						<div class="btn on_map_app <?=($map_app == 'yandex' ? 'map_app_act' : '')?>" data-app="yandex"><?=t::w('Yandex Maps', 'Яндекс карта')?></div>
					</div>
					<label class="rt_ret">
						<span><b><?=t::w('Return to base at route end', 'Маршрут соңында базаға қайту')?></b><small><?=t::w('Route returns to the branch after the last address', 'Соңғы адрестен кейін маршрут точкаға қайтып оралады')?></small></span>
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

				<div class="up_lt">
					<div class="up_li">
						<div class="menu_cin"><i class="fal fa-globe"></i></div>
						<div class="menu_cih"><?=t::w('Language', 'Тіл')?></div>
					</div>
					<div class="map_app">
						<? foreach ($langs_short as $lk => $lv): ?>
							<a class="btn <?=($ui_lang == $lk ? 'map_app_act' : '')?>" href="?lang=<?=$lk?>"><?=$lv?></a>
						<? endforeach ?>
					</div>
				</div>

				<div class="up_exit">
					<a class="btn btn_back2" href="/exit.php">
						<i class="far fa-sign-out"></i>
						<span><?=t::w('Log out', 'Шығу')?></span>
					</a>
				</div>

			</div>
			<div class="up_cg">
				<a href="https://gprog.kz" target="_blank" class="gprog_bl">
					<span>#gprog-та</span>
					<div class="gprog_heart"><i class="fas fa-heart"></i></div>
					<span><?=t::w('made', 'жасалған')?></span>
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
				<h4 id="acc_drw_t"><?=t::w('Personal data', 'Жеке деректер')?></h4>
				<div class="btn btn_cl on_acc_close" title="<?=t::h('Close', 'Жабу')?>"><i class="far fa-times"></i></div>
			</div>
			<form class="acc_form">
				<label class="acc_fl"><span><?=t::w('First name', 'Аты')?></span><input class="acc_inp" name="name" value="<?=e($user['name'])?>" maxlength="60" autocomplete="given-name" required></label>
				<label class="acc_fl"><span><?=t::w('Surname', 'Тегі')?></span><input class="acc_inp" name="surname" value="<?=e($user['surname'])?>" maxlength="60" autocomplete="family-name"></label>
				<label class="acc_fl"><span><?=t::w('Phone (label)', 'Телефон')?></span><input class="acc_inp fr_phone" name="phone" type="tel" inputmode="numeric" value="<?=e($user['phone'])?>" placeholder="8 (700) 000-00-00" autocomplete="tel" required></label>
				<div class="acc_msg"></div>
				<button class="btn acc_save" type="submit"><?=t::w('Save', 'Сақтау')?></button>
			</form>
		</div>
	</div>

	<!-- Құпиясөзді өзгерту -->
	<div class="acc_drw pw_drw" aria-hidden="true">
		<div class="acc_drw_a on_pw_close"></div>
		<div class="acc_drw_c" role="dialog" aria-labelledby="pw_drw_t">
			<div class="acc_drw_grab"></div>
			<div class="acc_drw_hd">
				<h4 id="pw_drw_t"><?=t::w('Change password', 'Құпиясөзді өзгерту')?></h4>
				<div class="btn btn_cl on_pw_close" title="<?=t::h('Close', 'Жабу')?>"><i class="far fa-times"></i></div>
			</div>
			<form class="acc_form pw_form">
				<label class="acc_fl"><span><?=t::w('New password', 'Жаңа құпиясөз')?></span><input class="acc_inp" name="pass" type="password" minlength="6" maxlength="60" autocomplete="new-password" required></label>
				<div class="acc_msg"></div>
				<button class="btn acc_save" type="submit"><?=t::w('Save', 'Сақтау')?></button>
			</form>
		</div>
	</div>

<? include "../block/footer.php"; ?>