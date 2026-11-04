<? include "../config/core.php";
   

   // Статус тарихы (кассадағыдай, тек оқу)
	if(isset($_GET['history'])) {
		require_once __DIR__.'/hist.php';
		$oid = (int)@$_POST['id'];
		$o = mysqli_fetch_assoc(db::query("select * from retail_orders where id = '$oid' and company_id = '".(int)$company."' limit 1"));
		if (!$o || !$user_id) exit();
		echo dist_hist_html($o);
		exit();
	}

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

      $o_st = (int)@mysqli_fetch_assoc(db::query("select order_status from retail_orders where id = '".(int)$order_id."'"))['order_status'];
      if (!in_array($o_st, [1, 8, 2, 11])) { echo 'locked'; exit(); } // Жолда/жабық тапсырыстың курьері ауыспайды
      if ($id == 'off') $upd = db::query("UPDATE `retail_orders` SET сourier_id = null, order_status = 2 WHERE id = '$order_id'");
      else $upd = db::query("UPDATE `retail_orders` SET сourier_id = '$id', order_status = 11 WHERE id = '$order_id'"); // Курьер тағайындалды, «Жолда»-ны курьер қояды
      $n_st = $id == 'off' ? 2 : 11;
      if ($n_st != $o_st) db::query("INSERT INTO `retail_orders_log` (order_id, field, old_value, new_value, user_id, ins_dt) VALUES ('".(int)$order_id."', 'status', '$o_st', '$n_st', '".(int)$user_id."', '$datetime')"); // этап уақыты үшін

      echo 'yes';

      exit();
	}

   // Кафе: клиентке берілді → Столда (9)
	if(isset($_GET['table'])) {
		$id = (int)@$_POST['id'];
		$o = mysqli_fetch_assoc(db::query("select order_status from retail_orders where id = '$id' and order_type = 4 and company_id = '".(int)$company."' limit 1"));
		$cur = $o ? (int)$o['order_status'] : 0;
		if (!in_array($cur, [1, 8, 2])) { echo 'none'; exit(); }
		db::query("UPDATE `retail_orders` SET order_status = 9, upd_dt = '$datetime' WHERE id = '$id'");
		db::query("INSERT INTO `retail_orders_log` (order_id, field, old_value, new_value, user_id, ins_dt) VALUES ('$id', 'status', '$cur', '9', '".(int)$user_id."', '$datetime')");
		echo 'yes';
		exit();
	}

   //
	if(isset($_GET['check'])) {
		$id = (int)@$_POST['id'];
      $o_st = (int)@mysqli_fetch_assoc(db::query("select order_status from retail_orders where id = '$id'"))['order_status'];
      $upd = db::query("UPDATE `retail_orders` SET `order_status` = 4, upd_dt = '$datetime' WHERE id = '$id'");
      if ($o_st != 4) db::query("INSERT INTO `retail_orders_log` (order_id, field, old_value, new_value, user_id, ins_dt) VALUES ('$id', 'status', '$o_st', '4', '".(int)$user_id."', '$datetime')"); // этап уақыты үшін
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