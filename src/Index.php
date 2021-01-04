<?php


namespace Masaichi\Weather;


class Index
{
    public function index()
    {
        $key = 'a25e9710f44e8bb11edb47ea9617fbd3';
        $city = '深圳';
        try {
            $weather = new Weather($key);
            $result  = $weather->getWeather($city);
        } catch (Exception $e) {
            $message = $e->getMessage();
            if ($e instanceof InvalidArgumentException) {
                $message = '参数异常：' . $message;
            } elseif ($e instanceof HttpException) {
                $message = '接口异常：' . $message;
            }
            var_dump($message);
            exit;
        }
        var_dump($result);
    }
}