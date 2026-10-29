<? include "config/core.php";

	// sign in phone
	if(isset($_GET['sign'])) {
		$phone = substr(preg_replace('/\D/', '', (string)@$_POST['phone']), -10);
		$password = strip_tags($_POST['password']);
		$user = db::query("SELECT * FROM user WHERE `phone` = '$phone' and `password` = '$password' and `right` = 1");
		if (mysqli_num_rows($user)) {
			$user_d = mysqli_fetch_array($user);
			$user_staff = fun::user_staffw($user_d['id']);
			if ($user_staff['positions_id'] == 6) {
				$_SESSION['uph'] = $phone;
				$_SESSION['ups'] = $password;
				setcookie('uph', $phone, time() + 3600*24*30*6, '/');
				setcookie('ups', $password, time() + 3600*24*30*6, '/');
				echo 'yes';
			} else echo 'none';
		} else echo 'none';
		exit();
	}


	// ubd user
	if(isset($_GET['ubd_pass'])) {
		$pass = strip_tags($_POST['pass']);
		$upd = db::query("UPDATE `user` SET `password`='$pass', `upd_dt`='$datetime' WHERE id = '$user_id'");
		echo "yes";
		exit();
	}



	// ubd user
	if(isset($_GET['ubd_acc'])) {
		$n_name = strip_tags($_POST['n_name']);
		$surname = strip_tags($_POST['surname']);
		$sex = strip_tags($_POST['sex']);
		$age = strip_tags($_POST['age']);
		$mail = strip_tags($_POST['mail']);
		$phone = substr(preg_replace('/\D/', '', (string)@$_POST['phone']), -10);
		$password = strip_tags($_POST['password']);
		
		$upd = db::query("UPDATE `user` SET `name`='$n_name', `surname`='$surname', `sex`='$sex', `age`='$age', `mail`='$mail', `phone`='$phone', `password`='$password', `upd_dt`='$datetime' WHERE id = '$user_id'");

		$_SESSION['uph'] = $phone;
		$_SESSION['upm'] = $mail;
		$_SESSION['ups'] = $password;
		setcookie('uph', $phone, time() + 3600*24*30);
		setcookie('upm', $mail, time() + 3600*24*30);
		setcookie('ups', $password, time() + 3600*24*30);

		echo "yes";
		exit();
	}

	// Негізгі карта: 2gis / yandex
	if(isset($_GET['map_app'])) {
		if (!$user_id) exit('none');
		$app = (@$_POST['app'] == 'yandex') ? 'yandex' : '2gis';
		$st = mysqli_prepare(db::$connect, "UPDATE company_staff SET map_app = ? WHERE user_id = ?");
		mysqli_stmt_bind_param($st, 'si', $app, $user_id);
		mysqli_stmt_execute($st);
		echo 'yes';
		exit();
	}

	// Аккаунт: CSRF тексеру
	function acc_csrf() {
		if (empty($_SESSION['csrf']) || !hash_equals($_SESSION['csrf'], (string)@$_POST['csrf'])) { echo 'csrf'; exit(); }
	}

	// Аккаунт: жеке деректерді сақтау (аты, тегі, телефон)
	if(isset($_GET['acc_save'])) {
		if (!$user_id) exit('none');
		acc_csrf();
		$name = trim(strip_tags((string)@$_POST['name']));
		$surname = trim(strip_tags((string)@$_POST['surname']));
		$phone = preg_replace('/\D/', '', (string)@$_POST['phone']);
		if (strlen($phone) == 11 && ($phone[0] == '7' || $phone[0] == '8')) $phone = substr($phone, 1);
		if ($name === '' || mb_strlen($name) > 60 || mb_strlen($surname) > 60) exit('name');
		if (!preg_match('/^7\d{9}$/', $phone)) exit('phone');
		$st = mysqli_prepare(db::$connect, "SELECT id FROM user WHERE phone = ? AND id <> ? LIMIT 1");
		mysqli_stmt_bind_param($st, 'si', $phone, $user_id); mysqli_stmt_execute($st);
		if (mysqli_fetch_assoc(mysqli_stmt_get_result($st))) exit('phone_busy');
		$st = mysqli_prepare(db::$connect, "UPDATE user SET name = ?, surname = ?, phone = ?, upd_dt = ? WHERE id = ?");
		mysqli_stmt_bind_param($st, 'ssssi', $name, $surname, $phone, $datetime, $user_id); mysqli_stmt_execute($st);
		if ($phone != $user['phone']) { // кіру cookie-і жаңа телефонмен
			$_SESSION['uph'] = $phone;
			setcookie('uph', $phone, time() + 3600*24*30*6, '/');
		}
		echo 'yes';
		exit();
	}

	// Аккаунт: профиль фотосы (тек сурет, 8 МБ дейін, 400x400 шаршы JPG болып сақталады)
	if(isset($_GET['acc_img'])) {
		if (!$user_id) exit('none');
		acc_csrf();
		$f = @$_FILES['img'];
		if (!$f || $f['error'] !== UPLOAD_ERR_OK || !is_uploaded_file($f['tmp_name'])) exit('file');
		if ($f['size'] > 8 * 1024 * 1024) exit('size');
		$info = @getimagesize($f['tmp_name']);
		$types = [IMAGETYPE_JPEG => 'jpg', IMAGETYPE_PNG => 'png', IMAGETYPE_WEBP => 'webp'];
		if (!$info || !isset($types[$info[2]]) || $info[0] < 50 || $info[1] < 50 || $info[0] > 8000 || $info[1] > 8000) exit('type');

		$dir = $_SERVER['DOCUMENT_ROOT'].'/assets/uploads/users/';
		$name = 'u'.(int)$user_id.'_'.bin2hex(random_bytes(6));
		if (function_exists('imagecreatetruecolor')) {
			$src = $info[2] == IMAGETYPE_JPEG ? @imagecreatefromjpeg($f['tmp_name']) : ($info[2] == IMAGETYPE_PNG ? @imagecreatefrompng($f['tmp_name']) : @imagecreatefromwebp($f['tmp_name']));
			if (!$src) exit('type');
			// телефон фотосының бұрылысы (EXIF)
			if ($info[2] == IMAGETYPE_JPEG && function_exists('exif_read_data')) {
				$o = (int)@exif_read_data($f['tmp_name'])['Orientation'];
				if ($o == 3) $src = imagerotate($src, 180, 0); elseif ($o == 6) $src = imagerotate($src, -90, 0); elseif ($o == 8) $src = imagerotate($src, 90, 0);
			}
			$w = imagesx($src); $h = imagesy($src); $side = min($w, $h);
			$dst = imagecreatetruecolor(400, 400);
			imagefill($dst, 0, 0, imagecolorallocate($dst, 255, 255, 255));
			imagecopyresampled($dst, $src, 0, 0, (int)(($w - $side) / 2), (int)(($h - $side) / 2), 400, 400, $side, $side);
			$name .= '.jpg';
			if (!imagejpeg($dst, $dir.$name, 85)) exit('save');
		} else {
			$name .= '.'.$types[$info[2]];
			if (!move_uploaded_file($f['tmp_name'], $dir.$name)) exit('save');
		}

		$old = (string)$user['img'];
		$st = mysqli_prepare(db::$connect, "UPDATE user SET img = ?, upd_dt = ? WHERE id = ?");
		mysqli_stmt_bind_param($st, 'ssi', $name, $datetime, $user_id); mysqli_stmt_execute($st);
		// ескі фотоны тек өзі жүктеген болса өшіреміз
		if ($old !== '' && preg_match('/^u'.(int)$user_id.'_[a-f0-9]{12}\.(jpg|png|webp)$/', $old) && is_file($dir.$old)) @unlink($dir.$old);
		echo json_encode(['ok' => 1, 'img' => '/assets/uploads/users/'.$name]);
		exit();
	}

	// Маршрут соңында базаға (точкаға) қайту: қосу / өшіру
	if(isset($_GET['route_return'])) {
		if (!$user_id) exit('none');
		acc_csrf();
		$on = (int)(@$_POST['on'] == 1);
		$st = mysqli_prepare(db::$connect, "UPDATE company_staff SET route_return = ? WHERE user_id = ?");
		if (!$st) exit('db'); // баған әлі жоқ
		mysqli_stmt_bind_param($st, 'ii', $on, $user_id); mysqli_stmt_execute($st);
		echo 'yes';
		exit();
	}

