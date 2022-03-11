<?php
/**
 * Created by PhpStorm.
 * Author: XueSi
 * Email: 1592328848@qq.com
 * Date: 2022/3/11 9:24:51
 */

namespace Lava\WeChatPay\Util;

use function openssl_x509_read;
use function openssl_x509_parse;
use function file_get_contents;
use function strtoupper;
use function strpos;

use UnexpectedValueException;

use Lava\WeChatPay\Crypto\Rsa;

/**
 * Util for read private key and certificate.
 */
class PemUtil
{
    private const LOCAL_FILE_PROTOCOL = 'file://';

    /**
     * Read private key from file
     * @param string $filepath - PEM encoded private key file path
     *
     * @return \OpenSSLAsymmetricKey|resource|mixed
     * @deprecated v1.2.0 - Use `Rsa::from` instead
     *
     */
    public static function loadPrivateKey(string $filepath)
    {
        return Rsa::from((false === strpos($filepath, self::LOCAL_FILE_PROTOCOL) ? self::LOCAL_FILE_PROTOCOL : '') . $filepath);
    }

    /**
     * Read private key from string
     * @param \OpenSSLAsymmetricKey|resource|string|mixed $content - PEM encoded private key string content
     *
     * @return \OpenSSLAsymmetricKey|resource|mixed
     * @deprecated v1.2.0 - Use `Rsa::from` instead
     *
     */
    public static function loadPrivateKeyFromString($content)
    {
        return Rsa::from($content);
    }

    /**
     * Read certificate from file
     *
     * @param string $filepath - PEM encoded X.509 certificate file path
     *
     * @return \OpenSSLCertificate|object|resource|bool - X.509 certificate resource identifier on success or FALSE on failure
     * @throws UnexpectedValueException
     */
    public static function loadCertificate(string $filepath)
    {
        $content = file_get_contents($filepath);
        if (false === $content) {
            throw new UnexpectedValueException("Loading the certificate failed, please checking your {$filepath} input.");
        }

        return openssl_x509_read($content);
    }

    /**
     * Read certificate from string
     *
     * @param \OpenSSLCertificate|object|resource|string|mixed $content - PEM encoded X.509 certificate string content
     *
     * @return \OpenSSLCertificate|object|resource|bool - X.509 certificate resource identifier on success or FALSE on failure
     */
    public static function loadCertificateFromString($content)
    {
        return openssl_x509_read($content);
    }

    /**
     * Parse Serial Number from Certificate
     *
     * @param \OpenSSLCertificate|object|resource|string|mixed $certificate Certificates string or resource
     *
     * @return string - The serial number
     * @throws UnexpectedValueException
     */
    public static function parseCertificateSerialNo($certificate): string
    {
        $info = openssl_x509_parse($certificate);
        if (false === $info || !isset($info['serialNumberHex'])) {
            throw new UnexpectedValueException('Read the $certificate failed, please check it whether or nor correct');
        }

        return strtoupper($info['serialNumberHex'] ?? '');
    }
}