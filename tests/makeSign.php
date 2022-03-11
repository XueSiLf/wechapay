<?php
/**
 * Created by PhpStorm.
 * Author: XueSi
 * Email: 1592328848@qq.com
 * Date: 2022/3/11 9:23:37
 */

require_once __DIR__ . '/vendor/autoload.php';

use Lava\WeChatPay\Crypto\Rsa;
use Lava\WeChatPay\Formatter;

$params = [
    'appId' => 'wx8888888888888888',
    'timeStamp' => (string)Formatter::timestamp(),
    'nonceStr' => Formatter::nonce(),
    'package' => 'prepay_id=wx201410272009395522657a690389285100',
];

$merchantPrivateKeyFilePath = 'file:///path/to/merchant/apiclient_key.pem';
$merchantPrivateKeyInstance = Rsa::from($merchantPrivateKeyFilePath);

$params += ['paySign' => Rsa::sign(
    Formatter::joinedByLineFeed(...array_values($params)),
    $merchantPrivateKeyInstance
), 'signType' => 'RSA'];