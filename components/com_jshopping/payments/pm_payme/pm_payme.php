<?php
defined('_JEXEC') or die();
class pm_payme extends PaymentRoot
{	
	public function showPaymentForm($params, $pmconfigs)
	{
		include(dirname(__FILE__) . "/paymentform.php");
	}
	public function loadLanguageFile()
	{
		$lang    = JFactory::getLanguage();
		$langtag = $lang->getTag();
		if(file_exists(JPATH_ROOT . '/components/com_jshopping/payments/pm_payme/lang/' . $langtag . '.php')) {
			require_once(JPATH_ROOT . '/components/com_jshopping/payments/pm_payme/lang/' . $langtag . '.php');
		}else{
			require_once(JPATH_ROOT . '/components/com_jshopping/payments/pm_payme/lang/ru-RU.php'); //если языковый файл не найден, то подключаем en-GB.php
		}
	}
	public function showAdminFormParams($params){
		$array_params = [	'payme_merchant_id',
							'payme_secret_key',
							'payme_method_id',
							'payme_db_host',
							'payme_db_user',
							'payme_db_name',
							'payme_db_pass',
							'transaction_end_status',
							'transaction_pending_status',
							'transaction_confirm_status',
							'transaction_refunded_status',
							'transaction_cancel_status' ]; 
		foreach ($array_params as $key){
			if (!isset($params[$key])){
				$params[$key] = '';
			}
		}
		$orders = JModelLegacy::getInstance('orders', 'JshoppingModel');
		$this->loadLanguageFile();
		include(dirname(__FILE__) . '/adminparamsform.php');
	}
	public function getUrlParams($pmconfigs){
		if (!function_exists('getallheaders')) {
      function getallheaders(){
			$headers = '';
			foreach ($_SERVER as $name => $value){
				if (substr($name, 0, 5) == 'HTTP_'){
					$headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
				}
			}
        return $headers;
		}
        }
		function respond($response, $die=true){
			if (!headers_sent()) {
				header('Content-Type: application/json; charset=UTF-8');
			}
			echo json_encode($response);
			if ($die) die();
		} 
		function Error_Authorization($payload){
			$response = ["error" => ["code"=>-32504,"message"=>[
													"ru"=>"Ошибка при авторизации",
													"uz"=>"Avtorizatsiyada xatolik",
													"en"=>"Error during authorization"],
									"data"=>null],"result"=>null,"id" => $payload['id']];
			return $response;
		}
		function Success_Change_Password($payload, $pmconfigs){
			$mysqli=new mysqli($pmconfigs['payme_db_host'], $pmconfigs['payme_db_user'], $pmconfigs['payme_db_pass'], $pmconfigs['payme_db_name'] );
			$mysqli->query("SET NAMES 'utf-8'");
          	$table=$pmconfigs['payme_db_suf']."_jshopping_payment_method";
			$result=$mysqli->query ("SELECT `payment_params` FROM $table WHERE `payment_id`=7");
			$row=$result->fetch_assoc();
			$replase_key=str_replace($pmconfigs['payme_secret_key'],$payload['params']['password'],$row['payment_params']);
			$payment_id=$pmconfigs['payme_method_id'];
			$mysqli->query("UPDATE $table SET `payment_params` = '$replase_key' WHERE `payment_id` = '$payment_id'");
			$mysqli->close();
			$response = ["id" => $payload['id'],"result" => ["success" => true]];
			return $response;
		}
		function Error_OrderID($payload){
		$response = ["error" => ["code"=>-31099,"message"=>[
												"ru"=>"Номер заказа не найден",
												"uz"=>"Buyurtma raqami topilmadi",
												"en"=>"Order number not found"],
								"data"=>"order"],"result"=>null,"id" => $payload['id']];
			return $response;
		}
		function Error_Transaction($payload){
			$response = ["error" => ["code"=>-31003,"message"=>[
													"ru"=>"Номер транзакции не верен",
													"uz"=>"Tranzaksiya raqami xato",
													"en"=>"Transaction number is wrong"],
									"data"=>null],"result"=>null,"id" => $payload['id']];
			return $response;
		}
		function Error_Amount($payload){
			$response = ["error" => ["code"=>-31001,"message"=>[
													"ru"=>"Неверная сумма заказа",
													"uz"=>"Buyurtma summasi xato",
													"en"=>"Order ammount incorrect"],
									"data"=>"order"],"result"=>null,"id" => $payload['id']];
			return $response;
		}
		function Error_Method($payload){
			$response = ["error" => [	"code"=>-31099,	"message"=>[
														"ru"=>"Ошибка метода ".$payload['method'],
														"uz"=>"Xato ".$payload['method'],
														"en"=>"Error during ".$payload['method']],
										"data"=>"order"],"result"=>null,"id" => $payload['id']];
			return $response;
		}
		function Unknown_Error($payload){
			$response = ["error" => ["code"=>-31008,"message"=>[
													"ru"=>"Неизвестная ошибка",
													"uz"=>"Noma`lum xatolik",
													"en"=>"Unknown error"],
													"data"=>null],"result"=>null,"id" => $payload['id']];
			return $response;
		}
		$payload = (array)json_decode(file_get_contents('php://input'), true);
		$params = array();
		$headers = getallheaders();
		$code=base64_encode("Paycom:".$pmconfigs['payme_secret_key']);
		if (!$headers || !isset($headers['Authorization']) ||  !preg_match('/^\s*Basic\s+(\S+)\s*$/i', $headers['Authorization'], $matches) || $matches[1] != $code) {
          if($act == 'return'){
      		
         }else{
			respond(Error_Authorization($payload));
           }
		}else{
			if ($payload['method'] == 'ChangePassword'){
				if ($payload['params']['password'] != $pmconfigs['payme_secret_key']){
					respond(Success_Change_Password($payload, $pmconfigs));
				}else{
					respond(Error_Authorization($payload));
				}
			}elseif ($payload['params']['account']['order']){
				$orderid=$payload['params']['account']['order'];
				$mysqli=new mysqli($pmconfigs['payme_db_host'], $pmconfigs['payme_db_user'], $pmconfigs['payme_db_pass'], $pmconfigs['payme_db_name'] );
				$mysqli->query("SET NAMES 'utf-8'");
				$table=$pmconfigs['payme_db_suf']."_jshopping_orders";
				$result=$mysqli->query ("SELECT `payment_method_id` FROM $table WHERE `order_id`='$orderid'");
				$row=$result->fetch_assoc();
				$mysqli->close();
				if ($row['payment_method_id'] == $pmconfigs['payme_method_id']){
					$params['order_id'] = $payload['params']['account']['order'];
				}else{
					respond(Error_ORDERID($payload));	
				}
			}elseif ($payload['params']['id']) {
				$id=$payload['params']['id'];
				$mysqli=new mysqli($pmconfigs['payme_db_host'], $pmconfigs['payme_db_user'], $pmconfigs['payme_db_pass'], $pmconfigs['payme_db_name'] );
				$mysqli->query("SET NAMES 'utf-8'");
              	$table=$pmconfigs['payme_db_suf']."_jshopping_orders";
				$result=$mysqli->query ("SELECT `payment_method_id`, `order_id`  FROM $table WHERE `transaction`='$id'");
				$row=$result->fetch_assoc();
				$mysqli->close();
				if ($row['payment_method_id']==$pmconfigs['payme_method_id']){
					$params['order_id'] = $row['order_id'];
				}else{
					respond(Error_Transaction($payload));
				}
			}
		$params['hash']              = '';
		$params['checkHash']         = 0;
		$params['checkReturnParams'] = 0;
		return $params;
		}
	}
	function showEndForm($pmconfigs, $order){
		$url = (((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || $_SERVER['SERVER_PORT'] == 443) ? 'https' : 'http').'://' . $_SERVER['HTTP_HOST'];
		$amount = number_format((float)$order->order_total, 0, '.', '');
		$amount=$amount*100;
		$fields = array('merchant'       	  	=> $pmconfigs['payme_merchant_id'],
						'amount'            	=> $amount,
						'account[order]' 		=> $order->order_id,
						'lang'					=> 'ru',
						'callback'				=>$url.'/index.php?option=com_jshopping&controller=checkout&task=step7&act=return&js_paymentclass=pm_debit');
		$form = '<form name="payme"  action="http://checkout.paycom.uz" method="POST">';
   		foreach ($fields as $key=>$value){
      		$form .=  '<input type="hidden" name="'.$key.'" value="'.$value.'">';
    	}
		$form .= '</form>
		<script type="text/javascript">
			document.payme.submit();
		</script>';
		echo $form;
		die;
	}
	function checkTransaction($pmconfigs, $order, $act)
	{  
		$payload = (array)json_decode(file_get_contents('php://input'), true);
		$amount_chk = number_format((float)$order->order_total, 0, '.', '');
		$amount_chk=$amount_chk*100;
		$tran_num = "IS".$order->order_number;
		$tran_id = $payload['params']['id'];
		$tran_time = $payload['params']['time'];
		if ($_SERVER["REQUEST_METHOD"] == "POST" && $act == 'notify'){
			switch ($payload['method']){
				case ('CheckPerformTransaction'):
					if ($payload['params']['amount'] != $amount_chk){ /*не верная сумма платежа*/
						respond(Error_Amount($payload));  
					}elseif ($payload['params']['amount'] == $amount_chk && $payload['params']['account']['order']== $order->order_id && $order->order_status == "1"){
						$response = ["id" => $payload['id'],"result" => ["allow" => true]];
						respond($response);
					}elseif ($order->order_status != "1"){
						respond(Error_Method($payload)); 
					}
					break;
				case ('CreateTransaction'):
					if ($payload['params']['amount'] != $amount_chk){ /*не верная сумма платежа*/
						respond(Error_Amount($payload));		
					}elseif ($payload['params']['amount'] == $amount_chk && $payload['params']['account']['order']== $order->order_id){ /*все верно*/
						if($order->order_status == "1"){
							$order->create_time = round(microtime(true) * 1000);
							$order->store();
							$response = ["id" => $payload['id'], 
											"result" => [
											"create_time" => (int)$order->create_time,
											"transaction"=>$tran_num,
											"state"=>1]];
							respond($response, $die=false);
					// добавляем номер id  в номер транзакции БД
							$order->transaction = $tran_id;
							$order->store();
							return array(8, "Заказ подтвержден"); //изменение статуса на "В ожидании => Подтвержден"
						}elseif($order->order_status == "2" && $order->transaction == $tran_id){
							$response = ["id" => $payload['id'], 
											"result" => [
											"create_time" => (int)$order->create_time,
											"transaction"=>$tran_num,
											"state"=>1]];
							respond($response);
						}elseif	($order->order_status == "2"  &&  $order->transaction != $tran_id){
					//ошибка при повторном создании заказа
							respond(Error_Method($payload)); 
						}
					}else{
						respond(Unknown_Error($payload));
					}
					break;
				case ('PerformTransaction'):
					if($order->order_status == "2"){
						$order->perform_time=round(microtime(true) * 1000);
						$order->store();
						$response = ["id" => $payload['id'], 
											"result" => [
											"transaction"=>$tran_num,
											"perform_time"=>(int)$order->perform_time,
											"state"=>2]];
						respond($response, $die=false);
						return array(1,  $order->order_id); //изменение статуса на "подтвержден => оплачен"
					}elseif($order->order_status == "6"){
						$response = ["id" => $payload['id'], 
											"result" => [
											"transaction"=>$tran_num,
											"perform_time"=>(int)$order->perform_time,
											"state"=>2]];
						respond($response);
					}elseif ($order->order_status == "3" or $order->order_status == "4"){
						$response = ["error" => ["code"=>-31008,"message"=>[
																"ru"=>"Транзакция отменена или возвращена",
																"uz"=>"Tranzaksiya bekor qilingan yoki qaytarilgan",
																"en"=>"Transaction was cancelled or refunded"],
																"data"=>"order"],
												"result"=>null,	"id" => $payload['id']];
						respond($response);
					}elseif ($order->order_status != "2"){
						respond(Error_Transaction($payload));
					}else{
						respond(Unknown_Error($payload));
					}
					break;
				case ('CheckTransaction'):
					if($order->order_status == "2" && $order->transaction == $tran_id){
						$response = ["id" => $payload['id'],
											"result" => [
											"create_time"=>(int)$order->create_time,
											"perform_time"=>0,
											"cancel_time"=>0,
											"transaction"=>$tran_num,
											"state"=>1, 
											"reason"=>null]];
						respond($response);
					}elseif ($order->order_status == "6" && $order->transaction == $tran_id){
						$response = ["id" => $payload['id'],
											"result" => [
											"create_time"=>(int)$order->create_time,
											"perform_time"=>(int)$order->perform_time,
											"cancel_time"=>0,
											"transaction"=>$tran_num,
											"state"=>2, 
											"reason"=>null]];
						respond($response);
					}elseif ($order->order_status == "3" && $order->transaction == $tran_id){
						$response = ["id" => $payload['id'],
											"result" => [
											"create_time"=>(int)$order->create_time,
											"perform_time"=>0,
											"cancel_time"=>(int)$order->cancel_time,
											"transaction"=>$tran_num,
											"state"=>-1, 
											"reason"=>2]];
						respond($response);
					}elseif ($order->order_status == "4" && $order->transaction == $tran_id){
						$response = ["id" => $payload['id'],
											"result" => [
											"create_time"=>(int)$order->create_time,
											"perform_time"=>(int)$order->perform_time,
											"cancel_time"=>(int)$order->cancel_time,
											"transaction"=>$tran_num,
											"state"=>-2, 
											"reason"=>5]];
						respond($response);
					}else{
						respond(Error_Transaction($payload)); 
					}
					break;
				case ('CancelTransaction'):
					if ($order->order_status == "1" && $order->transaction == $tran_id){
						$order->cancel_time=round(microtime(true) * 1000);
						$order->store();
						$response = ["id" => $payload['id'], 
											"result" => [
										  	"transaction"=>$tran_num,
										  	"cancel_time"=>(int)$order->cancel_time,								  
										  	"state"=>-1]];
						respond($response,$die=false); 
						return array(4,  $order->order_id); //изменение статуса на "в ожидании => отменен" отмена после создания
					}elseif ($order->order_status == "6" && $order->transaction == $tran_id){
						$order->cancel_time=round(microtime(true) * 1000);
						$order->store();
						$response = ["id" => $payload['id'], 
											"result" => [
										  	"transaction"=>$tran_num,
										  	"cancel_time"=>(int)$order->cancel_time,								  
										  	"state"=>-2]];
						respond($response,$die=false); 
						return array(7,  $order->order_id); //изменение статуса на "подтвержден => возвращен" после завершения
					}elseif ($order->order_status == "3"){ //повторный ответ при -1
						$response = ["id" => $payload['id'], 
											"result" => [
										  	"transaction"=>$tran_num,
										  	"cancel_time"=>(int)$order->cancel_time,								  
										  	"state"=>-1]];
						respond($response); 
			  		return array(4,  $order->order_id); //изменение статуса на "подтвержден => отменен"
					}elseif ($order->order_status == "4"){ //повторный ответ при -2
						$response = ["id" => $payload['id'], 
											"result" => [
										  	"transaction"=>$tran_num,
										  	"cancel_time"=>(int)$order->cancel_time,								  
										  	"state"=>-2]];
						respond($response); 
			  		return array(4,  $order->order_id); //изменение статуса на "подтвержден => отменен"
					}else{
						$response = ["error" => ["code"=>-31007,"message"=>[
																"ru"=>"Невозможно отменить. Заказ выполнен.",
																"uz"=>"Buyurtma bajarilgan - uni bekor qilib bo`lmaydi",
																"en"=>"It is impossible to cancel. The order is completed"],
												"data"=>"order"],"result"=>null,"id" => $payload['id']];
						respond($response); 
					}
					break;
			}
		}
	}
}