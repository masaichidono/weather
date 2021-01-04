<?php

namespace Masaichi\Weather\tests;

use core\Response;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use Masaichi\Weather\Exception;
use Masaichi\Weather\HttpException;
use Masaichi\Weather\InvalidArgumentException;
use Masaichi\Weather\Weather;
use Mockery\Matcher\AnyArgs;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;

class WeatherTest extends TestCase
{
    private $key;

    public function test__construct()
    {
        $this->key = 'a25e9710f44e8bb11edb47ea9617fbd3';
    }

    /**
     * @group normal
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testGetWeatherWithInvalidType()
    {
        $w = new Weather($this->key);

        $this->expectException(InvalidArgumentException::class);

        //断言消息
        $this->expectExceptionMessage('Invalid type: foo');
        $w->getWeather('深圳', 'foo');
        $this->fail('Failed to assert getWeather throw exception with invalid argument.');
    }

    /**
     * @group normal
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function testGetWeatherWithInvalidFormat()
    {
        $w = new Weather('mock-key');
        $this->expectException(InvalidArgumentException::class);
        $w->getWeather('深圳', 'base', 'array');
        $this->fail('Failed to assert getWeather throw exception with invalid argument.');
    }

    /**
     * @group ignore
     */
    public function testGetWeather()
    {
        //json
        $response = new \GuzzleHttp\Psr7\Response(200, [], '{"success": true}');
        $client   = \Mockery::mock(Client::class);
        $client->allows()->get('https://restapi.amap.com/v3/weather/weatherInfo', [
            'query' => [
                'key'        => $this->key,
                'city'       => '深圳',
                'output'     => 'json',
                'extensions' => 'base',
            ]
        ])->andReturn($response);

        $w = \Mockery::mock(Weather::class, [$this->key])->makePartial();
        $w->allows()->getHttpClient()->andReturn($client);

        $this->assertSame(['success' => true], $w->getWeather('深圳'));
    }

    /**
     * @group normal
     */
    public function testGetWeatherRuntimeException()
    {
        $client = \Mockery::mock(Client::class);
        $client->allows()->get(new AnyArgs())
            ->andThrow(new HttpException('request timeout')); //抛出超时异常
        $w = \Mockery::mock(Weather::class, [$this->key])->makePartial();
        $w->allows()->getHttpClient()->andReturn($client);

        //断言调用时会产生异常
        $this->expectException(HttpException::class);
        $this->expectExceptionMessage('request timeout');

        $w->getWeather('深圳');

    }

    /**
     * @group ignore
     */
    public function testSetOptions()
    {
        $w = new Weather($this->key);
        //设置参数前为null
        $this->assertNull($w->getHttpClient()->getConfit('timeout'));

        $w->setGuzzleOptions(['timeout' => 5000]);

        $this->assertSame(5000, $w->getHttpClient()->getConfig('timeout'));
    }
}