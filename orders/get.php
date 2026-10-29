<? include "../config/core.php";
   
   // returns
	if(isset($_GET['return_sn'])) {
		$id = strip_tags($_POST['id']);
      $order_d = fun::retailo($id);
      $total = $order_d['total'];
      $quantity_all = $order_d['quantity'];

      $return_id = (mysqli_fetch_assoc(db::query("SELECT * FROM `retail_returns` order by id desc")))['id'] + 1;
      $ins = db::query("INSERT INTO `retail_returns`(`id`, `user_id`, `returns`, `total`, `quantity`) VALUES ('$return_id', '$user_id', 1, '$total', '$quantity_all')");
      $upd = db::query("UPDATE retail_orders SET returned = 1 WHERE id = '$id'");

      $op = db::query("select * from retail_orders_products where order_id = '$id'");
		if (mysqli_num_rows($op)) {
         while ($op_d = mysqli_fetch_array($op)) {
            $product_id = $op_d['product_id'];
            $item_id = $op_d['product_item_id'];
            $quantity = $op_d['quantity'];
            $price = $op_d['price'];
            $ins = db::query("INSERT INTO `retail_returns_products`(`return_id`, `product_id`, `product_item_id`, `quantity`, `price`) VALUES ('$return_id', '$product_id', '$item_id', '$quantity', '$price')");
            
            $pq_id = (mysqli_fetch_array(db::query("select * from product_item_quantity where item_id = '$item_id' limit 1")))['id'];
            $upd = db::query("UPDATE `product_item_quantity` SET quantity = quantity + '$quantity' WHERE id = '$pq_id'");
         
         }

         $arr = array(
            'Номер возврата: ' => $return_id,
            'Сумма: ' => $total.' тг',
            'Количество: '	=> $quantity_all.' шт',
            'Продавец: ' => $user['name'],
         );
         foreach($arr as $key => $value) {$txt .= "<b>".$key."</b> ".$value."%0A";};
         $sendToTelegram = fopen("https://api.telegram.org/bot{$token}/sendMessage?chat_id={$chat_id}&parse_mode=html&text={$txt}","r");
         // if ($sendToTelegram) echo "yes"; else echo "error";

         echo 'yes';
      } else echo 'none';

      exit();
	}
   
   
   
   // 
	if(isset($_GET['change_staff'])) {
		$id = strip_tags($_POST['id']);
		$order_id = strip_tags($_POST['order_id']);

      if ($id == 'off') {
         $upd = db::query("UPDATE `retail_orders` SET сourier_id = null WHERE id = '$order_id'");
      } else {
         $upd = db::query("UPDATE `retail_orders` SET сourier_id = '$id' WHERE id = '$order_id'");
      }

      echo 'yes';

      exit();
	}

   // «Алдым»: курьер тапсырысты алды → Жолда (3)
	if(isset($_GET['take'])) {
		$id = (int)@$_POST['id'];
		$o = mysqli_fetch_assoc(db::query("select order_status from retail_orders where id = '$id' and `сourier_id` = '".(int)$user_id."' limit 1"));
		$cur = $o ? (int)$o['order_status'] : 0;
		if (!in_array($cur, [1, 8, 2, 11])) { echo 'none'; exit(); }
		db::query("UPDATE `retail_orders` SET order_status = 3, upd_dt = '$datetime' WHERE id = '$id'");
		db::query("INSERT INTO `retail_orders_log` (order_id, field, old_value, new_value, user_id, ins_dt) VALUES ('$id', 'status', '$cur', '3', '".(int)$user_id."', '$datetime')");
		echo 'yes';
		exit();
	}

   // «Барлығын қабылдау»: экранда көрінген тапсырыстардың бәрін бірден → Жолда (3)
	if(isset($_GET['take_all'])) {
		$ids = array_filter(array_map('intval', (array)@$_POST['ids']));
		$n = 0;
		foreach ($ids as $id) {
			$o = mysqli_fetch_assoc(db::query("select order_status from retail_orders where id = '$id' and `сourier_id` = '".(int)$user_id."' limit 1"));
			$cur = $o ? (int)$o['order_status'] : 0;
			if (!in_array($cur, [1, 8, 2, 11])) continue;
			db::query("UPDATE `retail_orders` SET order_status = 3, upd_dt = '$datetime' WHERE id = '$id'");
			db::query("INSERT INTO `retail_orders_log` (order_id, field, old_value, new_value, user_id, ins_dt) VALUES ('$id', 'status', '$cur', '3', '".(int)$user_id."', '$datetime')");
			$n++;
		}
		echo $n ? 'yes' : 'none';
		exit();
	}

   // Отказ при получение (6): тек курьер, бізде қалған ақша (kept_sum) есепке кіреді
	if(isset($_GET['refuse'])) {
		$id = (int)@$_POST['id']; $kept = max(0, (int)@$_POST['kept']);
		$reason = mb_substr(trim(strip_tags((string)@$_POST['reason'])), 0, 255);
		if ($reason === '') { echo 'need_reason'; exit(); } // отказ себебі міндетті
		$rs = mysqli_real_escape_string(db::$connect, $reason);
		$o = mysqli_fetch_assoc(db::query("select order_status, kept_sum from retail_orders where id = '$id' and `сourier_id` = '".(int)$user_id."' limit 1"));
		if (!$o || (int)$o['order_status'] != 3) { echo 'none'; exit(); }
		db::query("UPDATE `retail_orders` SET order_status = 6, kept_sum = '$kept', refuse_reason = '$rs', upd_dt = '$datetime' WHERE id = '$id'");
		db::query("INSERT INTO `retail_orders_log` (order_id, field, old_value, new_value, user_id, ins_dt) VALUES ('$id', 'status', '3', '6', '".(int)$user_id."', '$datetime'), ('$id', 'kept_sum', '".(int)$o['kept_sum']."', '$kept', '".(int)$user_id."', '$datetime'), ('$id', 'refuse_reason', NULL, '$rs', '".(int)$user_id."', '$datetime')");
		echo 'yes';
		exit();
	}

   //
	if(isset($_GET['check'])) {
		$id = (int)@$_POST['id'];
      $o = mysqli_fetch_assoc(db::query("select order_status from retail_orders where id = '$id' and `сourier_id` = '".(int)$user_id."' limit 1"));
      if (!$o || (int)$o['order_status'] != 3) { echo 'none'; exit(); } // тек өз «Жолда» тапсырысы
      // Немен алды: cash (қолма-қол) / kaspi / mix (аралас). Тек предоплатадан кейін қалған сома (админдегі courier_pay сияқты)
      $od = mysqli_fetch_assoc(db::query("select * from retail_orders where id = '$id' limit 1"));
      [$pre_sum, $rest] = fun::prepay($od);
      $method = (string)@$_POST['method']; $c = 0; $k = 0;
      if ($rest > 0) {
         if ($method == 'cash') { $c = $rest; }
         elseif ($method == 'kaspi') { $k = $rest; }
         elseif ($method == 'mix') { $c = max(0, min($rest, (int)@$_POST['cash'])); $k = $rest - $c; }
         else { echo 'method'; exit(); }
      } else $method = '';
      $m = $method !== '' ? "'$method'" : "NULL";
      $upd = db::query("UPDATE `retail_orders` SET `order_status` = 4, courier_method = $m, courier_cash = '$c', courier_kaspi = '$k', upd_dt = '$datetime' WHERE id = '$id'");
      db::query("INSERT INTO `retail_orders_log` (order_id, field, old_value, new_value, user_id, ins_dt) VALUES ('$id', 'status', '3', '4', '".(int)$user_id."', '$datetime')".($method !== '' ? ", ('$id', 'courier_pay', NULL, '".$method.":".$c.":".$k."', '".(int)$user_id."', '$datetime')" : ''));
      echo 'yes';

      exit();
	}

   // 
	if(isset($_GET['delete'])) {
      $id = strip_tags($_POST['id']);
      $del = db::query("DELETE FROM `retail_orders` where id = '$id'");
      if ($del) echo 'yes';

      exit();
	}



   // 
	if(isset($_GET['change_status'])) {
		$id = strip_tags($_POST['id']);
		$order_id = strip_tags($_POST['order_id']);

      $upd = db::query("UPDATE `retail_orders` SET order_status = '$id' WHERE id = '$order_id'");
      echo 'yes';

      exit();
	}















   // cashbox_pay
	if(isset($_GET['cashbox_pay'])) {
		$cashbox_number = strip_tags($_POST['number']);
		$total = strip_tags($_POST['total']);
		$delivery = strip_tags($_POST['delivery']);
		$qr = strip_tags($_POST['qr']);
		$cash = $total - $qr;
		$branch = strip_tags($_POST['branch']);

      if ($delivery) $ins = db::query("INSERT INTO `retail_orders`(`user_id`, `number`, `paid`, `total`, `pay_qr`, `pay_cash`, `branch_id`, `pay_delivery`, `upd_dt`) VALUES (1, '$cashbox_number', 1, '$total', '$qr', '$cash', '$branch', '$delivery', '$datetime')");
      else $ins = db::query("INSERT INTO `retail_orders`(`user_id`, `number`, `paid`, `total`, `pay_qr`, `pay_cash`, `order_status`, `branch_id`, `upd_dt`) VALUES (1, '$cashbox_number', 1, '$total', '$qr', '$cash', 2, '$branch', '$datetime')");
      if ($ins) echo 'yes';
      else echo 'none';

      exit();
	}