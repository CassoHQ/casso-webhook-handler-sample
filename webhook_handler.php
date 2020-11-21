<?php

	//GIÁ TIỀN TỔNG CỘNG CỦA ĐƠN HÀNG GIẢ ĐỊNH.
	$ORDER_MONEY = 100000;

	//Số tiền chuyển thiếu tối đa mà hệ thống vẫn chấp nhận để xác nhận đã thanh toán
	$ACCEPTABLE_DIFFERENCE = 10000;

	//Tiền tố điền trước mã đơn hàng để tạo mã cho khách hàng chuyển tiền
	$MEMO_PREFIX = 'DH ';

	//Key bảo mật đã cấu hình bên Casso để chứng thực request
	$HEADER_SECURE_TOKEN = 'w3bkulq38wbqdSYd';

	payment_handler();

	function payment_handler(){
		global $ORDER_MONEY,$ACCEPTABLE_DIFFERENCE,$MEMO_PREFIX,$HEADER_SECURE_TOKEN;
		$txtBody = file_get_contents('php://input');
		$jsonBody = json_decode($txtBody); //convert JSON into array
		if (!$txtBody || !$jsonBody){
			echo "Request thiếu body";
			die();
		}
		if ($jsonBody->error != 0){
			echo "Có lỗi xay ra ở phía Casso";
			die();
		}

		$headers = getHeader();

		if ( $headers['Secure-Token'] != $HEADER_SECURE_TOKEN ) {
			echo("Thiếu Secure Token hoặc secure token không khớp");
			die(); 
		}

		foreach ($jsonBody->data as $key => $transaction) {
			$des = $transaction ->description;
			$order_id =parse_order_id($des);

			if (is_null($order_id)) {
				echo ("<div>Không nhận dạng được order_id từ nội dung chuyển tiền : " . $transaction ->description. "</div>");
				continue;
			}
			echo ("<div>Nhận dạng order_id là " . $order_id. "</div>");

			$paid = $transaction->amount;
			$total=number_format($transaction->amount, 0);
			$order_note = "Casso thông báo nhận <b>{$total}</b> VND, nội dung <B>{$des}</B> chuyển vào <b>STK {$transaction->bank_sub_acc_id}</b>";
			$ACCEPTABLE_DIFFERENCE = abs($ACCEPTABLE_DIFFERENCE);

			if ( $paid < $ORDER_MONEY  - $ACCEPTABLE_DIFFERENCE ){
				echo($order_note.'. Trạng thái đơn hàng đã được chuyển từ Tạm giữ sang Thanh toán thiếu.');

			} else if ($paid <= $ORDER_MONEY + $ACCEPTABLE_DIFFERENCE){
				// $order->payment_complete();//
				// wc_reduce_stock_levels($order_id);
				// $order->update_status('paid', $order_note); // order note is optional, if you want to  add a note to order
				echo($order_note.'. Trạng thái đơn hàng đã được chuyển từ Tạm giữ sang Đã thanh toán.');

			} else {
				echo($order_note.'. Trạng thái đơn hàng đã được chuyển từ Tạm giữ sang Thanh toán dư.');
				// $order->payment_complete();
				// wc_reduce_stock_levels($order_id);//final
				// $order->update_status('overpaid', $order_note); // order note is optional, if you want to  add a note to order

			}
		}
		echo "<div>Xử lý hoàn tất</div>";
		die();
	}

	function parse_order_id($des){
		global $MEMO_PREFIX;
		$re = '/'.$MEMO_PREFIX.'\d+/m';
		preg_match_all($re, $des, $matches, PREG_SET_ORDER, 0);

		if (count($matches) == 0 )
			return null;
		// Print the entire match result
		$orderCode = $matches[0][0];
		
		$prefixLength = strlen($MEMO_PREFIX);

		$orderId = intval(substr($orderCode, $prefixLength ));
		return $orderId ;

	}
	function getHeader(){
		$headers = array();

        $copy_server = array(
            'CONTENT_TYPE'   => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5'    => 'Content-Md5',
        );

        foreach ($_SERVER as $key => $value) {
            if (substr($key, 0, 5) === 'HTTP_') {
                $key = substr($key, 5);
                if (!isset($copy_server[$key]) || !isset($_SERVER[$key])) {
                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));
                    $headers[$key] = $value;
                }
            } elseif (isset($copy_server[$key])) {
                $headers[$copy_server[$key]] = $value;
            }
        }

        if (!isset($headers['Authorization'])) {
            if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {
                $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['PHP_AUTH_USER'])) {
                $basic_pass = isset($_SERVER['PHP_AUTH_PW']) ? $_SERVER['PHP_AUTH_PW'] : '';
                $headers['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basic_pass);
            } elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {
                $headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
            }
        }

        return $headers;
	}

?>