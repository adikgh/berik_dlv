<? include "../config/core.php";
	if (!$dist) { http_response_code(404); exit; } // қызметкерлер тек Раздачада
	
	// Қолданушыны тексеру
	if (!$user_id) { header('location: /'); exit; }


	$number = '';


	// Сайттың баптаулары
	$menu_name = 'users';
	$site_set['utop_nm'] = t::w('Couriers', 'Курьерлер');
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
						<a class="bp_back" href="/acc/"><i class="far fa-arrow-left"></i> <?=t::w('Account', 'Аккаунт')?></a>
						<h1 class="bp_head_h"><?=t::w('Couriers', 'Курьерлер')?></h1>
					</div>
					<div class="bp_cnt">
						<span><?=t::w('Total count', 'Барлығы')?></span>
						<b><?=$st_cnt?></b>
					</div>
				</div>
				<div class="bp_tools">
					<button class="bp_add add_user_btn" type="button"><i class="far fa-plus"></i> <span><?=t::w('Add', 'Қосу')?></span></button>
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
							<a class="bp_user_b bp_user_call" href="tel:8<?=htmlspecialchars((string)$user_d['phone'])?>" title="<?=t::h('Call', 'Қоңырау')?>"><i class="far fa-phone-alt"></i></a>
							<div class="bp_user_b bp_user_del user_del" data-id="<?=$buy_d['id']?>" data-confirm="<?=t::h('Remove the courier from the list?', 'Курьерді тізімнен өшіру керек пе?')?>" title="<?=t::h('Delete', 'Өшіру')?>"><i class="far fa-trash-alt"></i></div>
						</div>
					<? endwhile ?>
				<? else: ?>
					<div class="ds_nr"><i class="fal fa-users"></i><p><?=t::w('No couriers yet', 'Әзірге курьер жоқ')?></p></div>
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
				<h4><?=t::w('Add courier', 'Курьер қосу')?></h4>
				<div class="btn btn_dd add_user_back"><i class="fal fa-times"></i></div>
			</div>
			<div class="pop_bl_cl">
				<div class="form_c">
					<div class="form_im ">
						<div class="form_span"><?=t::w('First name', 'Аты')?>:</div>
						<input type="text" class="form_txt name" placeholder="" data-lenght="2">
						<i class="fal fa-text form_icon"></i>
					</div>
					<div class="form_im ">
						<div class="form_span"><?=t::w('Phone number', 'Телефон нөмірі')?>:</div>
						<input type="tel" class="form_txt phone fr_phone" placeholder="8 (700) 000-00-00" data-lenght="11">
						<i class="fal fa-mobile form_icon"></i>
					</div>
					<div class="form_im form_im_bn">
						<div class="btn add_user_send" data-cours-id="<?=$cours_id?>">
							<span><?=t::w('Add', 'Қосу')?></span>
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
				<h4><?=t::w('Edit courier', 'Курьерді өзгерту')?></h4>
				<div class="btn btn_dd edit_user_back"><i class="fal fa-times"></i></div>
			</div>
			<div class="pop_bl_cl edit_clp">
				
			</div>
		</div>
	</div>
