<? include "../config/core.php";

	// Тек кірген қызметкер (core.php тек 1,2,3,8 лауазымын қалдырады), тек Раздача хостында
	if (!$dist || !$user_id) exit('none');

	// add user
	if(isset($_GET['add_user'])) {
		$name = mysqli_real_escape_string(db::$connect, trim(strip_tags((string)@$_POST['name'])));
		$phone = substr(preg_replace('/\D/', '', (string)@$_POST['phone']), -10);
		$phone_sms = '7'.$phone;

		if (strlen($phone) != 10) { echo 'phone'; exit(); }
		// user барлық адамға ортақ: клиент ретінде бар болса, сол жазбаны қолданамыз
		$user_d = mysqli_fetch_assoc(db::query("SELECT * FROM `user` WHERE phone = '$phone' limit 1"));
		if (!$user_d) {
			db::query("INSERT INTO `user`(`name`, `phone`, `password`, `right`) VALUES ('$name', '$phone', '".pass_sql('123456')."', 1)");
			$user_d = mysqli_fetch_assoc(db::query("SELECT * FROM `user` WHERE phone = '$phone' limit 1"));
		}
		if (!$user_d) exit();
		$user_id = (int)$user_d['id'];
		$company_id = (int)$company;
		if (mysqli_num_rows(db::query("SELECT id FROM `company_staff` WHERE user_id = '$user_id' and company_id = '$company_id'"))) { echo 'yes'; exit(); }
		if (!$user_d['right'] || !$user_d['password']) db::query("UPDATE `user` SET `right` = 1, `password` = IFNULL(`password`, '".pass_sql('123456')."') WHERE id = '$user_id'");
		db::query("INSERT INTO `company_staff`(`user_id`, `positions_id`, `company_id`) VALUES ('$user_id', 6, '$company_id')");
		echo 'add';

		exit();
	}


	// add user
	if(isset($_GET['edit_user'])) {
		$id = (int)@$_POST['id'];
		$name = mysqli_real_escape_string(db::$connect, trim(strip_tags((string)@$_POST['name'])));
		$phone = substr(preg_replace('/\D/', '', (string)@$_POST['phone']), -10);
		$phone_sms = '7'.$phone;

		// тек өз компаниясының жұмысшысы; нөмір басқа адамда болса, екінші адам жасалмайды
		if (!mysqli_num_rows(db::query("SELECT id FROM `company_staff` WHERE user_id = '$id' and company_id = '".(int)$company."'"))) { echo 'error'; exit(); }
		if ($phone && strlen($phone) != 10) { echo 'phone'; exit(); }
		if ($phone && mysqli_num_rows(db::query("SELECT id FROM `user` WHERE phone = '$phone' and id != '$id'"))) { echo 'phone_busy'; exit(); }
		if ($name) $upd = db::query("UPDATE `user` SET `name` = '$name' WHERE id = '$id'");
		if ($phone) $upd = db::query("UPDATE `user` SET `phone` = '$phone' WHERE id = '$id'");

		echo 'yes';
		exit();
	}

	

   // user delete
	if(isset($_GET['user_del'])) {
		$id = strip_tags($_POST['id']);
		$buy = db::query("delete FROM `company_staff` WHERE id = '$id'");
		echo 'yes';
		exit();
	}


   // user delete
	if(isset($_GET['on_superv'])) {
		// $_SESSION['super'] = $_GET['on_superv'];
		$super = $_GET['on_superv'];
		$id = strip_tags($_POST['id']);
		$buy = db::query("UPDATE `company_staff` SET `positions_id` = '$super' WHERE id = '$id'");

		echo 'yes';
		exit();
	}


	// sms_send
	if(isset($_GET['sms_send'])) {

		$cours_id = strip_tags($_POST['cours_id']);
		$user_id = strip_tags($_POST['user_id']);

		$sub = db::query("SELECT * FROM `c_sub` WHERE cours_id = '$cours_id' and user_id = '$user_id");
		$user = db::query("SELECT * FROM `user` WHERE id = '$user_id'");
		$user_date = mysqli_fetch_assoc($user);
		$phone = $user_date['phone'];
		$code = $user_date['code'];

		$mess = "Иммунити курсы.\nТексеру коды: $code\nСілтеме: https://aruacademy.kz/l/?c=$cours_id";
		list($sms_id, $sms_cnt, $cost, $balance) = send_sms($phone, $mess, 0, 0, 0, 0, false);

		echo 'yes';
		exit();
	}


















		// add user
	if(isset($_GET['pass_ubd'])) {
		$id = (int)@$_POST['id'];
		$pass = trim((string)@$_POST['pass']);
		if (!$user_id) exit('none');
		// Өзгенің паролін тек басшы/раздача, өз компаниясының жұмысшысына ғана өзгерте алады
		if ($id != $user_id && (!in_array((int)@$user_right['positions_id'], [1, 2, 3]) || !mysqli_num_rows(db::query("SELECT id FROM company_staff WHERE user_id = '$id' and company_id = '".(int)$company."'")))) exit('none');

		if (mb_strlen($pass) >= 4) {
			$upd = db::query("UPDATE `user` SET `password` = '".pass_sql($pass)."' WHERE id = '$id'");
			if ($upd) {
				if ($id == $user_id) auth_relogin($user_id);
				echo 'yes';
			}
		}

		exit();
	}