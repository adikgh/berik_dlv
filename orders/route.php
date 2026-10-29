<? include "../config/core.php";

	// «Маршрут құру» 2GIS арқылы: адрестерді 2GIS Geocoder-мен координатқа айналдырып,
	// 2GIS немесе Яндекс маршрутын ашады (екеуі де координатпен). Бірінші нүкте — точканың адресі (company_branch.address), бос болса курьердің тұрған жері. Базаға сақталмайды.
	if (!$user_id) { header('location: /'); exit; }

	$company_d = mysqli_fetch_assoc(db::query("SELECT * FROM company WHERE id = '".(int)$company."'"));
	$city = (string)@$company_d['city'];
	fun::$company_ya = trim((string)@$company_d['ya_city']);

	// Тек курьердің өз тапсырыстары, экрандағы ретпен
	$ids = array_values(array_unique(array_filter(array_map('intval', explode(',', (string)@$_GET['ids'])))));
	$ids = array_slice($ids, 0, 10);
	$orders = [];
	if ($ids) {
		$in = implode(',', $ids);
		$q = db::query("SELECT id, address, branch_id FROM retail_orders WHERE id IN ($in) AND `сourier_id` = '".(int)$user_id."' AND company_id = '".(int)$company."'");
		while ($o = mysqli_fetch_assoc($q)) { $orders[$o['id']] = trim((string)$o['address']); $obr[$o['id']] = (int)$o['branch_id']; }
	}
	$addrs = []; $start = '';
	foreach ($ids as $id) if (!empty($orders[$id])) {
		$addrs[] = $orders[$id];
		if ($start === '' && !empty($obr[$id])) { $br = fun::branch($obr[$id]); $start = trim((string)@$br['address']); } // «Откуда» — точканың адресі
	}
	if (!$addrs) { header('location: /orders/'); exit; }

	// 2GIS Geocoder: бір адрес → [lon, lat, id] немесе null
	function geo2gis($q, $key) {
		$url = 'https://catalog.api.2gis.com/3.0/items/geocode?'.http_build_query(['q' => $q, 'fields' => 'items.point', 'key' => $key]);
		if (function_exists('curl_init')) {
			$ch = curl_init($url);
			curl_setopt_array($ch, [CURLOPT_RETURNTRANSFER => true, CURLOPT_TIMEOUT => 4, CURLOPT_CONNECTTIMEOUT => 3]);
			$res = curl_exec($ch); curl_close($ch);
		} else {
			$res = @file_get_contents($url, false, stream_context_create(['http' => ['timeout' => 4]]));
		}
		$j = $res ? json_decode($res, true) : null;
		$it = @$j['result']['items'][0];
		if (!$it || !isset($it['point']['lat'], $it['point']['lon'])) return null;
		return [(float)$it['point']['lon'], (float)$it['point']['lat'], (string)@$it['id']];
	}

	$key = is_file(__DIR__.'/../config/map_key.php') ? (string)(include __DIR__.'/../config/map_key.php') : '';
	$city_name = fun::city($city)['name'];
	$ret = fun::route_return($user_right); // соңында базаға қайту (аккаунттағы баптау)
	$app = fun::map_app($user_right);      // 2gis / yandex
	$cp = $city_name ? $city_name.', ' : '';

	// Әр нүктенің координаты (табылмаса null)
	$g_from = ($key !== '' && $start !== '') ? geo2gis($cp.$start, $key) : null;
	$g_pts = [];
	foreach ($addrs as $a) $g_pts[] = $key !== '' ? geo2gis($cp.$a, $key) : null;
	$found = count(array_filter($g_pts));

	// Яндекс: координатпен (lat,lon) — дәл түседі; координат табылмаған нүкте мәтінмен қалады
	if ($app == 'yandex' || !$found) {
		$y = function ($g, $txt) use ($cp) { return $g ? $g[1].','.$g[0] : $cp.trim(str_replace('~', ' ', $txt)); };
		$parts = [$start !== '' ? $y($g_from, $start) : ''];
		foreach ($addrs as $i => $a) $parts[] = $y($g_pts[$i], $a);
		if ($ret && $start !== '') $parts[] = $y($g_from, $start);
		header('location: https://yandex.kz/maps/?rtext='.rawurlencode(implode('~', $parts)).'&rtt=auto');
		exit;
	}

	// 2GIS: lon,lat;id
	$z = function ($g) { return $g[0].','.$g[1].($g[2] !== '' ? ';'.$g[2] : ''); };
	$from = $g_from ? $z($g_from) : ''; // бос — курьердің тұрған жері
	$pts = [];
	foreach ($g_pts as $g) if ($g) $pts[] = $z($g);
	if ($ret && $from !== '') $pts[] = $from;

	header('location: https://2gis.kz/'.rawurlencode($city).'/directions/points/'.rawurlencode($from.'|'.implode('|', $pts)));
	exit;
