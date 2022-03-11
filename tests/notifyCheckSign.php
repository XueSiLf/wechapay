<?php
/**
 * Created by PhpStorm.
 * Author: XueSi
 * Email: 1592328848@qq.com
 * Date: 2022/3/11 9:15:05
 */

require_once __DIR__ . '/vendor/autoload.php';

use Lava\WeChatPay\Crypto\Rsa;
use Lava\WeChatPay\Crypto\AesGcm;
use Lava\WeChatPay\Formatter;

$inWechatpaySignature = '';// 请根据实际情况获取
$inWechatpayTimestamp = '';// 请根据实际情况获取
$inWechatpaySerial = '';// 请根据实际情况获取
$inWechatpayNonce = '';// 请根据实际情况获取
$inBody = '';// 请根据实际情况获取，例如: file_get_contents('php://input');

$apiv3Key = '';// 在商户平台上设置的APIv3密钥

// 根据通知的平台证书序列号，查询本地平台证书文件，
// 假定为 `/path/to/wechatpay/inWechatpaySerial.pem`
$platformPublicKeyInstance = Rsa::from('file:///path/to/wechatpay/inWechatpaySerial.pem', Rsa::KEY_TYPE_PUBLIC);

// 检查通知时间偏移量，允许5分钟之内的偏移
$timeOffsetStatus = 300 >= abs(Formatter::timestamp() - (int)$inWechatpayTimestamp);
$verifiedStatus = Rsa::verify(
// 构造验签名串
    Formatter::joinedByLineFeed($inWechatpayTimestamp, $inWechatpayNonce, $inBody),
    $inWechatpaySignature,
    $platformPublicKeyInstance
);
if ($timeOffsetStatus && $verifiedStatus) {
    // 转换通知的JSON文本消息为PHP Array数组
    $inBodyArray = (array)json_decode($inBody, true);
    // 使用PHP7的数据解构语法，从Array中解构并赋值变量
    ['resource' => [
        'ciphertext' => $ciphertext,
        'nonce' => $nonce,
        'associated_data' => $aad
    ]] = $inBodyArray;
    // 加密文本消息解密
    $inBodyResource = AesGcm::decrypt($ciphertext, $apiv3Key, $nonce, $aad);
    // 把解密后的文本转换为PHP Array数组
    $inBodyResourceArray = (array)json_decode($inBodyResource, true);
    print_r($inBodyResourceArray);// 打印解密后的结果
}