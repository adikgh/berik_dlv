<?

   // Бір код, екі сайт: хост атауы режимді шешеді (кассадағы posm сияқты).
   // dist.* / dst.* (жергілікті, ноутбук) / dstb.* (live) — Раздача. Қалғаны — Доставка (курьер).
   $dist = (bool)preg_match('~^(dist|dst|dstb)\.~i', (string)@$_SERVER['HTTP_HOST']);
