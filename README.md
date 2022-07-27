PHP Bridge 2.0.0
================

Field Reports PHP Bridge（以降，本モジュールと表記します）は，
PDF帳票ツールField ReportsをPHPから利用するためのライブラリです。

PHP Bridge APIを通じて，Field Reportsの各機能を呼び出すことができます。

* Field Reportsのバージョンを取得する。

* レンダリング・パラメータを元にPDFを生成し，結果をバイナリ文字列として受け取る。

* PDFデータを解析し，フィールドや注釈の情報を取得する。

## 必要条件
### Field Reports本体

本モジュールのご利用に際しては，Field Reports本体が導入済みである必要があります。

Field Reportsのご購入もしくは試用版のダウンロードにつきましては，
下記サイトをご参照ください。

https://www.field-works.co.jp/製品情報/

### 連携手段の選択

本モジュールとField Reports本体との連携方法として，以下の２種類があります。
システム構成に応じて，適切な連携方法を選択してください。

* コマンド呼び出しによる連携
    - reports本体と本モジュールを同一マシンに配置する必要があります。
    - パスが通る場所にreportsコマンドを置くか，reportsコマンドのパスをAPIに渡してください。

* HTTP通信による連携
    - Field Reports本体をリモートマシンに配置することができます。
    - Field Reportsは，サーバーモードで常駐起動させてください（`reports server`）。
    - サーバーモードで使用するポート番号（既定値：`50080`）の通信を許可してください。

### PHP処理系

* PHP 7.4以上

## インストール
### ソースファイルからのインストール

ソースファイルを展開して，
`field_reports`ディレクトリ配下のソースファイルを所定の格納場所にコピーしてご利用ください。

### Composerを利用してのインストール

Composerから参照可能なモジュールが，GitHubに登録されています。

`composer.json`ファイルを下記のように記述してください。

```json:composer.json
{
    "require": {
        "field-reports/php-bridge": "2.0.0"
    },
    "repositories": [
        {
            "type": "vcs",
            "url": "https://github.com/field-works/php-bridge.git"
        }
    ]
}
```

以下のコマンドを実行してください。

```
$ composer install
```

※Tokenの入力が求められた場合は，個人アクセストークンを作成して入力してください。

https://github.com/settings/tokens

## 動作確認
### コマンド連携時

以下のコマンドを実行してください。

```
$ php -a
php > require('<ソース格納場所>/field_reports/bridge.php');
php > $reports = FieldReports\Bridge::create_proxy("exec:/usr/local/bin/reports");
php > echo $reports->version();
2.0.0rc6
php > echo $reports->render([]);
%PDF-1.6...
```

* 動作環境に応じて，create_proxy()に与えるパスを適宜変更してください。  
  （Windowsでは，"exec:C:/Program Files/Field Works/Field Reports x.x/bin/reports.exe"など）

### HTTP通携時

Field Reportsをサーバーモードで起動してください。

```
$ reports server -l3
```

次に，以下のコマンドを実行してください。

```
$ php -a
php > require('<ソース格納場所>/field_reports/bridge.php');
php > $reports = FieldReports\Bridge::create_proxy("http://localhost:50080/");
php > echo $reports->version();
2.0.0rc6
php > echo $reports->render([]);
%PDF-1.6...
```

* 動作環境に応じて，create_proxy()に与えるURLを適宜変更してください。  

## API使用例

```php
<?php
use FieldReports\Bridge;

$reports = Bridge::create_proxy();
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
$pdf = $reports->render($param);
```

## ライセンス

本モジュールのソースコードは，BSDライセンスのオープンソースとします。

    https://github.com/field-works/php-bridge/

以下のような場合，自由に改変／再配布していただいて結構です。

* 独自機能の追加

* ビルド/実行環境等の違いにより，本モジュールが正常に機能しない。

* 未サポートのPHPバージョンへの対応のため改造が必要。

* 他の言語の拡張ライブラリ作成のベースとして利用したい。

ただし，ソースを改変したモジュール自体において問題が発生し場合については，
サポート対応いたしかねますのでご了承ください
（Field Reports本体もしくはオリジナルの本モジュールに起因する問題であれば対応いたします）。

## 著者

* 合同会社フィールドワークス / Field Works, LLC
* https://www.field-works.co.jp/
* support@field-works.co.jp