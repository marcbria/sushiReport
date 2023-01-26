<?php

use PHPUnit\Framework\TestCase;

class SushiReportTest extends TestCase
{
    public function testLoadXML()
    {
        $sushi = new SushiReport('config/config.json');
        $result = $sushi->loadXML('anuarioiet');
        $this->assertNotFalse(strpos($result, 'No data avaliable'));
    }

    public function testQueryString()
    {
        $sushi = new SushiReport('config/config.json');
        $result = $sushi->queryString();
        $this->assertEquals($result, "/sushiLite/v1_7/GetReport?Report=JR1&Release=4&BeginDate=2022-01-01&EndDate=2022-01-01");
    }

    public function testGetXML()
    {
        $sushi = new SushiReport('config/config.json');
        $result = $sushi->getXML('https://revistes.uab.cat/brumal/sushiLite/v1_7/GetReport?Report=JR1&Release=4&BeginDate=2023-01-01&EndDate=2023-01-01');
        $this->assertNotFalse(strpos($result, '<?xml version'));
    }
}
