<?php
declare(strict_types=1);
namespace FieldReports;

require_once __DIR__ . '/Proxy.php';
require_once __DIR__ . '/ExecProxy.php';
require_once __DIR__ . '/HttpProxy.php';
require_once __DIR__ . '/ReportsException.php';

use FieldReports\HttpProxy;
use FieldReports\ExecProxy;

/**
 * Field Reportsと連携するためのProxyオブジェクトを生成します。
 * 
 */
class Bridge
{
    /**
     * 引数で与えられるURIに応じたField Reports Proxyオブジェクトを返却します。
     * 
     *      // コマンド連携時:
     *      $reports = FieldReports\Bridge::create_proxy("exec:/usr/local/bin/reports?cwd=/usr/share&amp;logleve=3");
     *
     *      // HTTP連携時:
     *      $reports = FieldReports\Bridge::create_proxy("http://localhost:50080/");
     * 
     * @param $uri Field Reportsとの接続方法を示すURI
     * <p>$uriがnullの場合，環境変数'REPORTS_PROXY'からURIを取得します。
     * 環境変数'REPORTS_PROXY'も未設定の場合の既定値は"exec:reports"です。</p>
     * 
     *  URI書式（コマンド連携時）:
     * 
     *      exec:{exePath}?cwd={cwd}&amp;loglevel={logLevel}
     *
     *   - cwd, loglevelは省略可能です。
     *   - loglevelが0より大きい場合，"php://stderr"にログを出力します。
     * 
     *  URI書式（HTTP連携時）:
     * 
     *      http://{hostName}:{portNumber}/
     *
     * @return Proxy Field Reports Proxyオブジェクト
     */
    public static function create_proxy($uri = null)
    {
        if (is_null($uri))
            $uri = getenv("REPORTS_PROXY") != false ? getenv("REPORTS_PROXY") : "exec:reports";
        $u = parse_url($uri);
        if ($u["scheme"] == "exec") {
            parse_str(key_exists('query', $u) ? $u["query"] : "", $q);
            $exe_path = $u["path"];
            $cwd = key_exists('cwd', $q) ? $q["cwd"] : ".";
            $loglevel = key_exists('loglevel', $q) ? (int)$q["loglevel"] : 0;
            return Bridge::create_exec_proxy($exe_path, $cwd, $loglevel, null);
        }
        return Bridge::create_http_proxy($uri);
    }

    /** コマンド呼び出しによりField Reportsと連携するProxyオブジェクトを生成します。
     * 
     * @param string $exe_path Field Reportsコマンドのパス
     * @param string $cwd Field Reportsプロセス実行時のカレントディレクトリ
     * @param int $loglevel ログ出力レベル（0: ログを出力しない，1: ERRORログ，2: WARNログ，3: INFOログ，4: DEBUGログ）
     * @param $logout ログ出力先Stream
     *        nullの場合，"php://stderr"にログを出力します。
     * @return ExecProxy Field Reports Proxyオブジェクト
     */
    public static function create_exec_proxy(
        $exe_path="reports", $cwd=".", $loglevel=0, $logout = null)
    {
        return new ExecProxy($exe_path, $cwd, $loglevel, $logout);
    }

    /**
     * HTTP通信によりField Reportsと連携するProxyオブジェクトを生成します。
     * 
     * @param string $base_address ベースURI
     * @return HttpProxy Field Reports Proxyオブジェクト
     */
    public static function create_http_proxy(string $base_address = "http://localhost:50080/")
    {
        return new HttpProxy($base_address);
    }
}