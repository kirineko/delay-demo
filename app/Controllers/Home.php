<?php namespace App\Controllers;

use Config\Services;
use DqClient;
use Predis\Client;

class Home extends BaseController
{

	public $redis;

	public function __construct()
	{
		$this->redis = new Client();
	}

	public function index()
	{
		return view('welcome_message');
	}

	//--------------------------------------------------------------------

	public function list()
	{
		$type = $this->request->getMethod();

		if ($type == 'get') {

			$order_ids = $this->redis->lrange('orders', 0, -1);

			$orders = [];
			foreach ($order_ids as $id) {
				$order_info = $this->redis->hgetall("order:{$id}:info");
				$orders[$id] = $order_info;
			}

			$data = ['data' => $orders];
			return view('order_list', $data);
		}

		if ($type == 'post') {

			$order_id = $this->redis->incr('order:id');
			$order = [
				'id' => $order_id,
				'status' => '未支付'
			];
			$this->redis->hmset("order:{$order_id}:info", $order);
			$this->redis->lpush('orders', [$order_id]);

			//server列表
			$server=array(
				'127.0.0.1:6789',
			);
			$dqClient = new DqClient();
			$dqClient->addServer($server);
			$topic ='order_checker'; //topic在后台注册
			$id = uniqid();
			$data=array(
				'id'=>$id,
				'body'=>array(
					'id' => $order_id
				)
			);
			//添加
			$boolRet = $dqClient->add($topic, $data);
			echo 'add success';

			return redirect()->to('list', null, 'get');
		}	
	}
}
