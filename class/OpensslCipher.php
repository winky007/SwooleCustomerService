<?PHP

class OpensslCipher
{
    const KEY = 'lkjdfadf%(()*()&&*%$^123#$G';

    public static function encode($plaintext)
    {
        $key = self::KEY;
        $data = openssl_encrypt($plaintext, 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        $data = base64_encode($data);
        return $data;
    }

    public static function decode($ciphertext)
    {
        $key = self::KEY;
        $decrypted = openssl_decrypt(base64_decode($ciphertext), 'AES-128-ECB', $key, OPENSSL_RAW_DATA);
        return $decrypted;
    }
}