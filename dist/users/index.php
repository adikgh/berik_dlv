<? include "../config/core.php";
	
	// Қолданушыны тексеру
	if (!$user_id) header('location: /');


	$number = '';


	// Сайттың баптаулары
	$menu_name = 'users';
	$site_set['utop_nm'] = t::w('Курьерлер', 'Курьеры');
	$css = ['item', 'user2'];
	$js = ['admin'];

?>
<? include "../block/header.php"; ?>

	<? $staff = db::query("select * from company_staff where positions_id = 6 and company_id = '".(int)$company."' order by ins_dt desc"); $st_cnt = mysqli_num_rows($staff); ?>
	<div class="bp_page">

		<div class="bp_head">
			<div class="bl_c">
				<div class="bp_head_t">
					<div>
						<a class="bp_back" href="/acc/"><i class="far fa-arrow-left"></i> <?=t::w('Аккаунт', 'Аккаунт')?></a>
						<h1 class="bp_head_h"><?=t::w('Курьерлер', 'Курьеры')?></h1>
					</div>
					<div class="bp_cnt">
						<span><?=t::w('Барлығы', 'Всего')?></span>
						<b><?=$st_cnt?></b>
					</div>
				</div>
				<div class="bp_tools">
					<button class="bp_add add_user_btn" type="button"><i class="far fa-plus"></i> <span><?=t::w('Қосу', 'Добавить')?></span></button>
				</div>
			</div>
		</div>

		<div class="bl_c">
			<div class="bp_users">
				<? if ($st_cnt): ?>
					<? while ($buy_d = mysqli_fetch_assoc($staff)): ?>
						<? $user_d = fun::user($buy_d['user_id']); if (!$user_d) continue; $u_nm = trim($user_d['name'].' '.$user_d['surname']); ?>
						<div class="bp_user" data-q="<?=htmlspecialchars(mb_strtolower($u_nm.' '.$user_d['phone']))?>">
							<div class="bp_user_m edit_user_btn" data-id="<?=$user_d['id']?>">
								<div class="bp_user_av lazy_img" <?=($user_d['img'] ? 'data-src="/assets/uploads/users/'.htmlspecialchars($user_d['img']).'"' : '')?>><? if (!$user_d['img']): ?><?=htmlspecialchars(mb_strtoupper(mb_substr($u_nm, 0, 1)))?><? endif ?></div>
								<div class="bp_user_t">
									<div class="bp_user_n"><?=htmlspecialchars($u_nm)?></div>
									<div class="bp_user_p fr_phone"><?=htmlspecialchars((string)$user_d['phone'])?></div>
								</div>
							</div>
							<a class="bp_user_b bp_user_call" href="tel:8<?=htmlspecialchars((string)$user_d['phone'])?>" title="<?=t::h('Қоңырау', 'Звонок')?>"><i class="far fa-phone-alt"></i></a>
							<div class="bp_user_b bp_user_del user_del" data-id="<?=$buy_d['id']?>" data-confirm="<?=t::h('Курьерді тізімнен өшіру керек пе?', 'Удалить курьера из списка?')?>" title="<?=t::h('Өшіру', 'Удалить')?>"><i class="far fa-trash-alt"></i></div>
						</div>
					<? endwhile ?>
				<? else: ?>
					<div class="ds_nr"><i class="fal fa-users"></i><p><?=t::w('Әзірге курьер жоқ', 'Курьеров пока нет')?></p></div>
				<? endif ?>
			</div>
		</div>

	</div>


<? include "../block/footer.php"; ?>

	
	<!-- user plus -->
	<div class="pop_bl pop_bl2 add_user">
		<div class="pop_bl_a add_user_back"></div>
		<div class="pop_bl_c">
			<div class="head_c">
				<h4><?=t::w('Курьер қосу', 'Добавить курьера')?></h4>
				<div class="btn btn_dd add_user_back"><i class="fal fa-times"></i></div>
			</div>
			<div class="pop_bl_cl">
				<div class="form_c">
					<div class="form_im ">
						<div class="form_span"><?=t::w('Аты', 'Имя')?>:</div>
						<input type="text" class="form_txt name" placeholder="" data-lenght="2">
						<i class="fal fa-text form_icon"></i>
					</div>
					<div class="form_im ">
						<div class="form_span"><?=t::w('Телефон нөмірі', 'Номер телефона')?>:</div>
						<input type="tel" class="form_txt phone fr_phone" placeholder="8 (000) 000-00-00" data-lenght="11">
						<i class="fal fa-mobile form_icon"></i>
					</div>
					<div class="form_im form_im_bn">
						<div class="btn add_user_send" data-cours-id="<?=$cours_id?>">
							<span><?=t::w('Қосу', 'Добавить')?></span>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	
	<!-- user plus -->
	<div class="pop_bl pop_bl2 edit_user">
		<div class="pop_bl_a edit_user_back"></div>
		<div class="pop_bl_c">
			<div class="head_c">
				<h4><?=t::w('Курьерді өзгерту', 'Изменить курьера')?></h4>
				<div class="btn btn_dd edit_user_back"><i class="fal fa-times"></i></div>
			</div>
			<div class="pop_bl_cl edit_clp">
				
			</div>
		</div>
	</div>
