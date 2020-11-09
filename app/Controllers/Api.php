<?php namespace App\Controllers;

use CodeIgniter\API\ResponseTrait;
use Predis\Client;

class Api extends BaseController
{

    use ResponseTrait;

	public $redis;

	public function __construct()
	{
		$this->redis = new Client();
	}

    public function cancel()
    {
        $id = $this->request->getPost('id');
        $this->redis->hset("order:{$id}:info", 'status', '已取消');
        $data = [
            'code' => 200,
            'data' => [
                'status' => '已取消'
            ]
        ];
        return $this->respond($data);
    }

    public function success()
    {
        $id = $this->request->getPost('id');
        $this->redis->hset("order:{$id}:info", 'status', '已支付');
        $data =  [
            'code' => 200,
            'data' => [
                'status' => '已支付'
            ]
        ];
        return $this->respond($data);
    }
}
