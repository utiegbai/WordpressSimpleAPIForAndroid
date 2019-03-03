<?php

class Url {

public static function host($key)
{
            switch ($key) {
            case 'protocol':
            return isset($_SERVER['HTTPS']) && filter_var($_SERVER['HTTPS'], FILTER_VALIDATE_BOOLEAN) ? 'https://' : 'http://';
            break;

            case 'name':
            return $_SERVER['HTTP_HOST'];
            break;

            case 'domainpart':
            return explode('.', self::host('name'), 2)[0];
            break;

            case 'domain':
            return self::host('name');
            break;

            case 'pathinfo':
            return pathinfo($_SERVER['PHP_SELF']);
            break;

            case 'plainurl':
            return self::host('protocol').self::host('name');
            break;

            case 'url':
            return rtrim(self::host('protocol').self::host('name').self::host('pathinfo')['dirname'], '/\\');
            break;

            case 'fulluri':
            return self::host('protocol').self::host('name').$_SERVER['REQUEST_URI'];
            break;

            case 'uri':
            return substr($_SERVER['REQUEST_URI'], 1);
            break;

            case 'assets':
            return self::host('plainurl').'/assets';
            break;

            case 'thispage':
            return substr(basename($_SERVER['PHP_SELF']), 0, strrpos(basename($_SERVER['PHP_SELF']), '.'));
            break;

            default:
            return false;
            break;
        }
}

}

?>