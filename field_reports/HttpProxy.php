<?php
declare(strict_types=1);
namespace FieldReports;

use Exception;
use RuntimeException;
use GuzzleHttp\Client;
use FieldReports\ReportsException;

/// @cond
class HttpProxy implements Proxy
{
    private $client;

    /**
     * HttpProxyコンストラクタ
     * 
     * @param string $base_address ベースURI
     */
    public function __construct(string $base_address)
    {
        $this->client = new Client(['base_uri' => $base_address]);
    }

    public function version(): string
    {
        try {
            $response = $this->client->request('GET', '/version');
            return $response->getBody()->getContents(); 
        } catch (Exception $exn) {
            throw new ReportsException($exn, "Fail to HTTP comunication");
        }
    }

    public function render($param): string
    {
        try {
            $jparam = is_array($param) ? json_encode($param, JSON_UNESCAPED_UNICODE) : $param;
            $response = $this->client->request('POST', '/render', [
                'headers' => ['Content-Type' => "application/json"],
                'body' => $jparam]);
            return $response->getBody()->getContents(); 
        } catch (Exception $exn) {
            throw new ReportsException($exn, "Fail to HTTP comunication");
        }
    }

    public function parse(string $pdf): array
    {
        try {
            $response = $this->client->request('POST', '/parse', [
                'headers' => ['Content-Type' => "application/pdf"],
                'body' => $pdf]);
            return json_decode($response->getBody()->getContents(), true);
        } catch (Exception $exn) {
            throw new ReportsException($exn, "Fail to HTTP comunication");
        }
    }
}
/// @endcond