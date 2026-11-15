<? // Кіру формасы: пароль → WhatsApp коды, «Парольді ұмыттым». JS: assets/js/sign.js. admin, delivery, dist, ktch-та бірдей. ?>
<div class="sg_box" data-home="<?=htmlspecialchars($sign_home ?? '/orders/')?>">

	<!-- 1. Телефон + пароль -->
	<div class="usign_cn sg_step sg_login">
		<div class="form_im form_im_ph">
			<i class="far fa-mobile form_icon"></i>
			<input type="tel" class="form_txt fr_phone phone sg_phone" placeholder="8 (700) 000-00-00" data-lenght="11" data-sel="0" autocomplete="tel" />
		</div>
		<div class="form_im form_im_ps">
			<i class="far fa-lock form_icon"></i>
			<input type="password" class="form_txt password sg_pass" placeholder="<?=t::h('Password (field)', 'Пароль')?>" data-lenght="6" data-sel="0" data-eye="0" autocomplete="current-password" />
			<i class="far fa-eye-slash form_icon_pass"></i>
		</div>
		<div class="form_im">
			<button class="btn sg_btn sg_go" type="button"><span><?=t::w('Sign in', 'Кіру')?></span><i class="far fa-long-arrow-right"></i></button>
		</div>
		<div class="form_im si_blc_bn">
			<div class="btn btn_back3 txt_c sg_forgot"><?=t::w('Forgot password?', 'Парольді ұмыттым?')?></div>
		</div>
	</div>

	<!-- 2. Кіру коды -->
	<div class="usign_cn sg_step sg_code" style="display:none">
		<p class="sg_note"><i class="fab fa-whatsapp"></i> <?=t::w('Code sent to WhatsApp:', 'WhatsApp-қа код жіберілді:')?><br><b class="sg_ph"></b></p>
		<p class="sg_test" style="display:none"><?=t::w('Test mode, code:', 'Тест режимі, код:')?> <b></b></p>
		<div class="form_im">
			<input type="text" class="form_txt sg_otp sg_code_in" inputmode="numeric" maxlength="4" placeholder="• • • •" autocomplete="one-time-code" />
		</div>
		<label class="sg_trust"><input type="checkbox" class="sg_trust_in" checked> <span><?=str_replace('%s', '<b class="sg_days">365</b>', t::h('Do not ask on this device for %s days', 'Бұл құрылғыда %s күн сұрамау'))?></span></label>
		<div class="form_im">
			<button class="btn sg_btn sg_code_go" type="button"><span><?=t::w('Confirm', 'Растау')?></span><i class="far fa-check"></i></button>
		</div>
		<div class="sg_links"><span class="sg_link sg_resend" data-key="login"><?=t::w('Resend code', 'Кодты қайта жіберу')?></span><span class="sg_link sg_back"><?=t::w('Back', 'Артқа')?></span></div>
	</div>

	<!-- 3. Парольді ұмыттым: нөмір -->
	<div class="usign_cn sg_step sg_reset" style="display:none">
		<p class="sg_note"><?=t::w('Enter your number, a code will come to WhatsApp', 'Нөміріңізді жазыңыз, WhatsApp-қа код келеді')?></p>
		<div class="form_im form_im_ph">
			<i class="far fa-mobile form_icon"></i>
			<input type="tel" class="form_txt fr_phone sg_rphone" placeholder="8 (700) 000-00-00" data-lenght="11" data-sel="0" />
		</div>
		<div class="form_im">
			<button class="btn sg_btn sg_reset_go" type="button"><span><?=t::w('Get code', 'Код алу')?></span><i class="fab fa-whatsapp"></i></button>
		</div>
		<div class="sg_links"><span class="sg_link sg_back"><?=t::w('Back', 'Артқа')?></span></div>
	</div>

	<!-- 4. Парольді ұмыттым: код -->
	<div class="usign_cn sg_step sg_rcode" style="display:none">
		<p class="sg_note"><i class="fab fa-whatsapp"></i> <?=t::w('Code sent to WhatsApp:', 'WhatsApp-қа код жіберілді:')?><br><b class="sg_ph"></b></p>
		<p class="sg_test" style="display:none"><?=t::w('Test mode, code:', 'Тест режимі, код:')?> <b></b></p>
		<div class="form_im">
			<input type="text" class="form_txt sg_otp sg_rcode_in" inputmode="numeric" maxlength="4" placeholder="• • • •" autocomplete="one-time-code" />
		</div>
		<div class="form_im">
			<button class="btn sg_btn sg_rcode_go" type="button"><span><?=t::w('Confirm', 'Растау')?></span><i class="far fa-check"></i></button>
		</div>
		<div class="sg_links"><span class="sg_link sg_resend" data-key="reset"><?=t::w('Resend code', 'Кодты қайта жіберу')?></span><span class="sg_link sg_back"><?=t::w('Back', 'Артқа')?></span></div>
	</div>

	<!-- 5. Жаңа пароль -->
	<div class="usign_cn sg_step sg_rpass" style="display:none">
		<p class="sg_note"><?=t::w('Create a new password (at least 6 characters)', 'Жаңа пароль ойлап табыңыз (кемінде 6 таңба)')?></p>
		<div class="form_im form_im_ps">
			<i class="far fa-lock form_icon"></i>
			<input type="password" class="form_txt sg_np1" placeholder="<?=t::h('New password', 'Жаңа пароль')?>" autocomplete="new-password" />
		</div>
		<div class="form_im form_im_ps">
			<i class="far fa-lock form_icon"></i>
			<input type="password" class="form_txt sg_np2" placeholder="<?=t::h('Repeat password', 'Парольді қайталаңыз')?>" autocomplete="new-password" />
		</div>
		<div class="form_im">
			<button class="btn sg_btn sg_rpass_go" type="button"><span><?=t::w('Save', 'Сақтау')?></span><i class="far fa-check"></i></button>
		</div>
	</div>

</div>
<style>
	.sg_note{margin:0 0 14px;text-align:center;font-size:15px;line-height:1.45;color:#444}
	.sg_note .fa-whatsapp{color:#25D366}
	.sg_test{margin:0 0 12px;text-align:center;padding:8px 10px;border-radius:10px;background:#FFF6DA;color:#7A5B00;font-size:14px}
	.sg_otp{text-align:center;letter-spacing:10px;font-size:24px !important;font-weight:700}
	.sg_trust{display:flex;align-items:center;gap:8px;margin:2px 0 12px;font-size:14px;color:#555;cursor:pointer}
	.sg_box input[type=checkbox]{-webkit-appearance:auto;appearance:auto;padding:0;margin:0;min-height:0;max-height:none;width:18px;height:18px;border:0;flex:none;accent-color:#0EA768;cursor:pointer}
	.sg_links{display:flex;justify-content:space-between;gap:10px;margin-top:6px}
	.sg_link{font-size:14px;color:#0EA768;cursor:pointer;font-weight:600}
	.sg_link:hover{text-decoration:underline}
	.sg_btn{width:100%;display:flex;align-items:center;justify-content:center;gap:8px}
	.sg_btn.sg_wait{opacity:.6;pointer-events:none}
	.sg_lang{display:flex;justify-content:center;flex-wrap:wrap;gap:6px;margin-top:18px}
	.sg_lang a{padding:5px 10px;border-radius:8px;font-size:13px;font-weight:700;color:#666;background:#f1f3f2;text-decoration:none}
	.sg_lang a.sg_lang_act{background:#0EA768;color:#fff}
</style>
