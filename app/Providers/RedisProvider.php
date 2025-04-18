<?php
namespace App\Providers;

use Illuminate\Support\Facades\Redis;


class RedisProvider{

    public function get($key)
    {
        return Redis::get($key);
    }

    public function set($key, $value)
    {
        return Redis::set($key, $value);
    }

    public function delete($key)
    {
        return Redis::delete($key);
    }

    public function close()
    {
        return Redis::close();
    }
    public function getRedis()
    {
        return Redis::connection();
    }

    public function getHost()
    {
        return Redis::getHost();
    }
    public function setHost($host)
    {
        Redis::setHost($host);
    }
    public function getPort()
    {
        return Redis::getPort();
    }
    public function setPort($port)
    {
        Redis::setPort($port);
    }
}

// Redis;