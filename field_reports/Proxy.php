<?php
declare(strict_types=1);
namespace FieldReports;

/**
 * Field Reportsの機能を呼び出すためのProxyインターフェースです。
 * 
 */
interface Proxy
{
    /**
     * バージョン番号を取得します。
     * 
     * @return バージョン番号
     * @throws ReportsException Field Reportsで発生した例外
     */
    public function version(): string;

    /**
     * レンダリング・パラメータを元にレンダリングを実行します。
     * 
     * @param string|array $param JSON文字列または連想配列形式レンダリング・パラメータ
     * @return string PDFデータ
     * @throws ReportsException Field Reportsで発生した例外
     * @see ユーザーズ・マニュアル「第5章 レンダリングパラメータ」
     */
    public function render($param): string;

    /**
     * PDFデータを解析し，フィールドや注釈の情報を取得します。
     * 
     * @param string $pdf PDFデータ
     * @return array 解析結果
     * @throws ReportsException Field Reportsで発生した例外
     */
    public function parse(string $pdf): array;
}