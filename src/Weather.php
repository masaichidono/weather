<?php


namespace Masaichi\Weather;


use GuzzleHttp\Client;

class Weather
{
    protected $key;
    protected $guzzleOptions = [];

    public function __construct($key)
    {
        $this->key = $key;
    }

    /**
     * 获取http客户端
     * @return Client
     */
    public function getHttpClient()
    {
        return new Client($this->guzzleOptions);
    }

    /**
     * 设置参数
     * @param $options
     */
    public function setGuzzleOptions($options)
    {
        $this->guzzleOptions = $options;
    }

    /**
     * 查询天气
     * @param $city
     * @param string $type
     * @param string $format
     * @return mixed|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function getWeather($city, $type = 'base', $format = 'json')
    {
        $api = 'https://restapi.amap.com/v3/weather/weatherInfo';
        //异常验证
        if (!in_array($format, ['xml', 'json'])) {
            throw new InvalidArgumentException('Invalid response format: ' . $format);
        }
        if (!in_array($type, ['base', 'all'])) {
            throw new InvalidArgumentException('Invalid type: ' . $type);
        }
        //过滤数组中为空的数据
        $query = array_filter([
            'key'        => $this->key,
            'city'       => $city,
            'output'     => $format,
            'extensions' => $type,
        ]);
        try {
            $response = $this->getHttpClient()->get($api, [
                'query' => $query
            ])->getBody()->getContents();
            return 'json' == $format ? json_decode($response, true) : $response;
        } catch (\Exception $e) {
            throw new HttpException($e->getMessage(), $e->getCode(), $e);
        }

    }
}