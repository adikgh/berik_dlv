<?php

// Тіл жүйесі кассадағыдай (pos.berik.c/config/t.php): `site_word` кестесі — name (кілт), lang (kz/en/uz/ky/ru), txt (мәтін).
// Кодта: t::w('English key', 'Қазақша'). Аударма базада болмаса — config/words.php-дан, ол да болмаса — қазақшасы.
// Курьер (dlv) мен Раздача (dist) бір код, сөздері де ортақ.
class t {

	public static $lang = 'kz';   // интерфейс тілі (core.php қояды)
	public static $words = null;
	public static $ok = null;
	public static $st = null;

	public function __construct() { }

	public static function lang() {
		global $ui_lang;
		if (!empty($ui_lang)) return $ui_lang;
		return self::$lang ?: 'kz';
	}

	// Ағымдағы тіл + қазақша сөздер: базадан бір сұраумен, жетпегені config/words.php-дан
	public static function load() {
		if (self::$words !== null) return self::$words;
		self::$words = ['kz' => [], 'cur' => []];
		$lang = self::lang();
		$q = db::query("SHOW TABLES LIKE 'site_word'");
		self::$ok = $q && mysqli_num_rows($q) > 0;
		if (self::$ok) {
			$q = db::query("SHOW COLUMNS FROM `site_word` LIKE 'lang'");
			self::$ok = $q && mysqli_num_rows($q) > 0;
		}
		if (self::$ok) {
			$l = preg_replace('/[^a-z]/', '', $lang);
			$q = db::query("select `name`, `lang`, `txt` from `site_word` where `lang` in ('kz', '$l')");
			if ($q) while ($r = mysqli_fetch_assoc($q)) {
				if ($r['lang'] == 'kz') self::$words['kz'][$r['name']] = $r['txt'];
				if ($r['lang'] == $l) self::$words['cur'][$r['name']] = $r['txt'];
			}
		}
		$file = is_file(__DIR__.'/words.php') ? require __DIR__.'/words.php' : [];
		foreach ($file as $k => $v) {
			if (!isset(self::$words['kz'][$k]) || self::$words['kz'][$k] === '') self::$words['kz'][$k] = $v['kz'];
			if ($lang != 'kz' && (!isset(self::$words['cur'][$k]) || self::$words['cur'][$k] === '') && !empty($v[$lang])) self::$words['cur'][$k] = $v[$lang];
		}
		return self::$words;
	}

	// t::w('Orders', 'Тапсырыстар') → таңдалған тілдегі мәтін. Жоқ болса қазақшасы, ол да жоқ болса $def.
	// Жаңа кілт (базада да, words.php-да да жоқ) базаға қазақшасымен өзі жазылады — кассадағыдай.
	public static function w($name, $def = null) {
		$w = self::load();
		if (isset($w['cur'][$name]) && $w['cur'][$name] !== '') return $w['cur'][$name];
		if (isset($w['kz'][$name]) && $w['kz'][$name] !== '') return $w['kz'][$name];
		if ($def !== null && self::$ok && !isset($w['kz'][$name])) {
			$n = mysqli_real_escape_string(db::$connect, $name);
			$d = mysqli_real_escape_string(db::$connect, $def);
			db::query("INSERT IGNORE INTO `site_word` (`name`, `lang`, `txt`) VALUES ('$n', 'kz', '$d')");
			self::$words['kz'][$name] = $def;
		}
		return $def !== null ? $def : $name;
	}

	// HTML атрибутына қауіпсіз мәтін
	public static function h($name, $def = null) {
		return htmlspecialchars(self::w($name, $def), ENT_QUOTES);
	}

	// Статус аты: кассадағы кілт 'st:'.name_ru (retail_orders_status), жоқ болса осындағы аударма
	public static function st($id) {
		$id = (int)$id;
		if (self::$st === null) {
			self::$st = [];
			$q = db::query("select id, name_ru from retail_orders_status");
			if ($q) while ($r = mysqli_fetch_assoc($q)) self::$st[(int)$r['id']] = trim((string)$r['name_ru']);
		}
		$def = [
			1 => ['kz' => 'Жаңа', 'ru' => 'Новый', 'en' => 'New', 'uz' => 'Yangi', 'ky' => 'Жаңы'],
			2 => ['kz' => 'Дайын', 'ru' => 'Готов', 'en' => 'Ready', 'uz' => 'Tayyor', 'ky' => 'Даяр'],
			3 => ['kz' => 'Жолда', 'ru' => 'В пути', 'en' => 'On the way', 'uz' => 'Yo‘lda', 'ky' => 'Жолдо'],
			4 => ['kz' => 'Аяқталды', 'ru' => 'Завершён', 'en' => 'Completed', 'uz' => 'Yakunlandi', 'ky' => 'Аякталды'],
			5 => ['kz' => 'Бас тарту', 'ru' => 'Отказ', 'en' => 'Refused', 'uz' => 'Rad etildi', 'ky' => 'Баш тартуу'],
			6 => ['kz' => 'Алғанда бас тарту', 'ru' => 'Отказ при получении', 'en' => 'Refused on delivery', 'uz' => 'Olishda rad etildi', 'ky' => 'Алууда баш тартуу'],
			7 => ['kz' => 'Сұрақ', 'ru' => 'Вопрос', 'en' => 'Question', 'uz' => 'Savol', 'ky' => 'Суроо'],
			8 => ['kz' => 'Дайындалуда', 'ru' => 'Готовится', 'en' => 'Cooking', 'uz' => 'Tayyorlanmoqda', 'ky' => 'Даярдалууда'],
			9 => ['kz' => 'Столда', 'ru' => 'На столе', 'en' => 'Served', 'uz' => 'Stolda', 'ky' => 'Столдо'],
			10 => ['kz' => 'Сыртта бас тарту', 'ru' => 'Отказ вне', 'en' => 'Refused outside', 'uz' => 'Tashqarida rad etildi', 'ky' => 'Сыртта баш тартуу'],
			11 => ['kz' => 'Курьер тағайындалды', 'ru' => 'Курьер назначен', 'en' => 'Courier assigned', 'uz' => 'Kuryer tayinlandi', 'ky' => 'Курьер дайындалды'],
		][$id] ?? null;
		$ru = @self::$st[$id];
		if ($ru === null || $ru === '') return $def ? (@$def[self::lang()] ?: $def['kz']) : (string)$id;
		$key = 'st:'.$ru;
		$w = self::load();
		if (isset($w['cur'][$key]) && $w['cur'][$key] !== '') return $w['cur'][$key];
		if ($def && !empty($def[self::lang()])) return $def[self::lang()];
		if (isset($w['kz'][$key]) && $w['kz'][$key] !== '') return $w['kz'][$key];
		return $def ? $def['kz'] : $ru;
	}

	// JS үшін: барлық сөздер таңдалған тілде { name: txt }
	public static function all() {
		$w = self::load();
		return array_merge(array_filter($w['kz'], 'strlen'), array_filter($w['cur'], 'strlen'));
	}

	// word block
	public static function wb($l) {
		$sql = mysqli_fetch_array(db::query("select * from `word_block` where txt = '$l'"));
		return @$sql['txt_'.(self::lang() == 'ru' ? 'ru' : 'kz')];
	}

	// info
	public static function info($id) {
		$sql = mysqli_fetch_array(db::query("select * from `info` where id = '$id'"));
		return @$sql['name_'.(self::lang() == 'ru' ? 'ru' : 'kz')];
	}

}
