<?

   // Бір код, екі сайт: хост атауы режимді шешеді (кассадағы posm сияқты).
   // dist.* (жергілікті) / dstb.* (live) — Раздача. Қалғаны — Доставка (курьер).
   $dist = (bool)preg_match('~^(dist|dstb)\.~i', (string)@$_SERVER['HTTP_HOST']);
