<?php

use PHPUnit\Framework\TestCase;
use FieldReports\Bridge;
use FieldReports\ReportsException;

class ReportsTest extends TestCase
{
    private $proxy;

    public function setUp(): void
    {
        $this->proxy = Bridge::create_proxy();
    }

    public function testVersion()
    {
        $result = $this->proxy->version();
        $this->assertEquals("2.", substr($result, 0, 2));
    }

    public function testRender1()
    {
        $param = <<<EOS
        {
          "template": {"paper": "A4"},
          "context": {
            "hello": {
              "new": "Tx",
              "value": "Hello, World!",
              "rect": [100, 700, 400, 750]
            }
          }
        }
        EOS;
        $result = $this->proxy->render($param);
        $this->assertEquals("%PDF-1.6", substr($result, 0, 8));
        $this->assertEquals("%%EOF\n", substr($result, -6));
    }

    public function testRender2()
    {
        $param = [
            "template" => ["paper" => "A4"],
            "context" => [
                "hello" => [
                    "new" => "Tx",
                    "value" => "Hello, World!",
                    "rect" => [100, 700, 400, 750]
                ]
            ]
        ];
        $result = $this->proxy->render($param);
        $this->assertEquals("%PDF-1.6", substr($result, 0, 8));
        $this->assertEquals("%%EOF\n", substr($result, -6));
    }

    public function testRender3()
    {
        $this->expectException(ReportsException::class);
        $result = $this->proxy->render("{,}");
        $this->assertEquals("%PDF-1.6", substr($result, 0, 8));
        $this->assertEquals("%%EOF\n", substr($result, -6));
    }

    public function testParse()
    {
        $pdf = file_get_contents("test/mitumori.pdf");
        $result = $this->proxy->parse($pdf);
        $this->assertIsArray($result["template"]);
        $this->assertIsArray($result["context"]);
    }
}