<? 

	class fun {
		
		function __construct() {}


		

		// retail_order
		public static function retailo($id) {
			$sql = db::query("select * from retail_orders where id = '$id'");
			return mysqli_fetch_array($sql);
		}
		public static function retailo_quantity($id) {
			$sql = db::query("select * from retail_orders_products where order_id = '$id'"); $q = 0;
			if (mysqli_num_rows($sql)) { while ($res = mysqli_fetch_array($sql)) $q = $q + $res['quantity']; }
			return $q;
		}

		// retail_orders_products
		public static function retailop($id) {
			$sql = db::query("select * from retail_orders_products where id = '$id'");
			return mysqli_fetch_array($sql);
		}


		

		
		// retail_order
		public static function retail_returns($id) {
			$sql = db::query("select * from retail_returns where id = '$id'");
			return mysqli_fetch_array($sql);
		}
		public static function retail_returns_total($id) {
			$sql = db::query("select * from retail_returns_products where return_id = '$id'"); $q = 0;
			if (mysqli_num_rows($sql)) { while ($res = mysqli_fetch_array($sql)) $q = $q + ($res['quantity'] * $res['price']); }
			return $q;
		}
		public static function retail_returns_quantity($id) {
			$sql = db::query("select * from retail_returns_products where return_id = '$id'"); $q = 0;
			if (mysqli_num_rows($sql)) { while ($res = mysqli_fetch_array($sql)) $q = $q + $res['quantity']; }
			return $q;
		}

		// retail_orders_products
		public static function retail_returnsp($id) {
			$sql = db::query("select * from retail_returns_products where id = '$id'");
			return mysqli_fetch_array($sql);
		}
















		// user management
		public static function branch($id) {
			$sql = db::query("select * from company_branch where id = '$id'");
			if (mysqli_num_rows($sql)) return mysqli_fetch_array($sql); else return 0;
		}


		// user
		public static function user($id) {
			$sql = db::query("select * from user where id = '$id'");
			if (mysqli_num_rows($sql)) return mysqli_fetch_array($sql); else return 0;
		}
		public static function user_phone($phone) {
			$sql = db::query("select * from user where phone = '$phone'");
			if (mysqli_num_rows($sql)) return mysqli_fetch_array($sql); else return 0;
		}

		// user management
		public static function user_staffw($id) {
			$sql = db::query("select * from company_staff where user_id = '$id' limit 1");
			if (mysqli_num_rows($sql)) return mysqli_fetch_array($sql); else return 0;
		}


		// user staff
		public static function user_staff($id) {
			$sql = db::query("select * from company_staff where id = '$id' limit 1");
			if (mysqli_num_rows($sql)) return mysqli_fetch_array($sql); else return 0;
		}
		public static function user_staff_name($id) {
			$sql = db::query("select * from company_staff where id = '$id'");
			return (mysqli_fetch_array($sql))['name_ru'];
		}
		public static function user_staff_positions($id) {
			$sql = db::query("select * from company_staff_positions where id = '$id'");
			if (mysqli_num_rows($sql)) return mysqli_fetch_array($sql); else return 0;
		}

		// autor
		// public static function autor($id) {
		// 	$sql = db::query("select * from u_autor where id = '$id'");
		// 	return mysqli_fetch_array($sql);
		// }











		//  chat
		public static function chat($id) {
			$sql = db::query("select * from h_chat where user_id = '$id'");
			if (mysqli_num_rows($sql)) return mysqli_fetch_array($sql); else return 0;
		}
		public static function chat2($id) {
			$sql = db::query("select * from h_chat where id = '$id'");
			if (mysqli_num_rows($sql)) return mysqli_fetch_array($sql); else return 0;
		}
		public static function chat_txt_end($id) {
			$sql = db::query("select * from h_chat_item where chat_id = '$id' order by ins_dt desc limit 1");
			if (mysqli_num_rows($sql)) return mysqli_fetch_array($sql); else return 0;
		}
		public static function chat_view_sum() {
			$sql = db::query("select * from h_chat where view is null");
			return mysqli_num_rows($sql);
		}
		public static function chat_item_view_sum($id) {
			$sql = db::query("select * from h_chat_item where chat_id = '$id' and user_id is null and view is null");
			return mysqli_num_rows($sql);
		}
		public static function chat_view_sum2($id) {
			$sql = db::query("select * from h_chat where user_id = '$id'");
			if (mysqli_num_rows($sql)) {
				$chat_d = mysqli_fetch_array($sql); 
				$chat_id = $chat_d['id'];
				$chad_i = db::query("select * from h_chat_item where chat_id = '$chat_id' and user_id is not null and view is null");
				return mysqli_num_rows($chad_i);
			} else return 0;
		}




		






		







		// mall send
		public static function send_mail($mail, $txt) {
			$from = "info@aruacademy.kz";
			$subject = "Aru Academy";
			$headers = "From:" . $from. "\r\n";
			$headers .= "Content-type:text/html;charset=UTF-8";
		$mess = "<html>
							<head><title>$subject</title></head>
							<body>
								<div><b>$txt<b></div>
							</body>
						</html>";
			return mail($mail, $subject, $mess, $headers);
		}

















	
		// Карта: курьердің негізгі картасы (company_staff.map_app), әдепкісі 2GIS
		public static $cities = ['shymkent' => 'Шымкент', 'aktobe' => 'Актобе', 'astana' => 'Астана', 'almaty' => 'Алматы', 'kostanai' => 'Костанай', 'karaganda' => 'Караганда', 'oral' => 'Уральск'];

		public static function map_app($staff) {
			return (@$staff['map_app'] == 'yandex') ? 'yandex' : '2gis';
		}

		// Бір адресті іздеу
		public static function map_url($app, $city, $address) {
			$address = trim(str_replace('~', ' ', (string)$address));
			if ($app == 'yandex') {
				$c = self::$cities[$city] ?? '';
				return 'https://yandex.kz/maps/?text='.rawurlencode(($c ? $c.', ' : '').$address);
			}
			return 'https://2gis.kz/'.rawurlencode((string)$city).'/search/'.rawurlencode($address);
		}

		// Бірнеше адреске маршрут (курьердің тұрған жерінен). 2GIS мәтінмен маршрут құрмайды, сондықтан әрқашан Яндекс
		// $start — точканың адресі (company_branch.address); бос болса маршрут курьердің тұрған жерінен
		public static function route_url($city, $addresses, $start = '', $ret = false) {
			$c = self::$cities[$city] ?? '';
			$pts = [];
			foreach ($addresses as $a) {
				$a = trim(str_replace('~', ' ', (string)$a));
				if ($a !== '') $pts[] = ($c ? $c.', ' : '').$a;
			}
			$start = trim(str_replace('~', ' ', (string)$start));
			$from = $start !== '' ? ($c ? $c.', ' : '').$start : '';
			if ($ret && $from !== '') $pts[] = $from; // соңында базаға қайту
			return 'https://yandex.kz/maps/?rtext='.rawurlencode($from.'~'.implode('~', $pts)).'&rtt=auto';
		}

	
		// Предоплата мен курьерге қалған сома (кассадағыдай: pos.berik.c/orders/card.php)
		// prepay_method бар болса — предоплата pay_prepay-де (QR, аударым, нал, RED); бос болса — ескі тапсырыс, предоплата pay_qr-да
		public static function prepay($o) {
			$new = (string)@$o['prepay_method'] !== '';
			$pre = $new ? (int)$o['pay_prepay'] : (int)$o['pay_qr'];
			$rest = $new ? max(0, (int)$o['total'] - $pre) : (int)$o['pay_cash'];
			return [$pre, $rest];
		}

	
		// Уақыт блогы кассадағыдай: үлкен уақыт + предзаказ (қызыл), астында күні мен филиал
		public static function order_time($o, $branch_name = '') {
			$ts = strtotime($o['ins_dt']); $pre = trim((string)@$o['preorder_dt']);
			$h = '<div class="or_tm_r"><b class="or_time">'.date('H:i', $ts).'</b>';
			if ($pre !== '') {
				$pt = strtotime($pre);
				$h .= '<span class="or_pre_tm" title="Предзаказ"><i class="far fa-stopwatch"></i>'.($pt ? date('H:i', $pt).(date('Y-m-d', $pt) != date('Y-m-d', $ts) ? '<small>'.date('d.m', $pt).'</small>' : '') : htmlspecialchars($pre)).'</span>';
			}
			$h .= '</div><span class="or_date">'.date('d-m-Y', $ts).($branch_name !== '' ? ' · '.htmlspecialchars($branch_name) : '').'</span>';
			return $h;
		}

	
		// Маршрут соңында базаға қайту: әдепкі қосулы (баған жоқ болса да қосулы)
		public static function route_return($staff) {
			return !is_array($staff) || !array_key_exists('route_return', $staff) || (int)$staff['route_return'] === 1;
		}

	}