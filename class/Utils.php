<?PHP

class Utils
{
    public static function debug($input, $tag = '')
    {
        if ($tag) echo "{$tag} start\n";
        echo json_encode($input, JSON_UNESCAPED_UNICODE) . "\n";
        if ($tag) echo "{$tag} end\n";
    }

    public static function h($text)
    {
        return htmlentities($text, ENT_COMPAT | ENT_HTML401, 'utf-8');
    }

    public static function redis()
    {
        $redis = new Redis();
        $redis->connect(REDIS_HOST, REDIS_PORT);
        return $redis;
    }
}