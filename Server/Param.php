<?php

class Param {
    const SOCKET_SERVERADDRESS = "127.0.0.1";
    const SOCKET_SERVERPORT = 16223;

    const SQL_USER = "root";
    const SQL_PASS = "";
    const SQL_HOST = "127.0.0.1";
    const SQL_PORT = 3036;

    /** @var string Code Characters */
    const HASHID_CHARACTERS = "abcdefghijklmnopgrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890-";
    const HASHID_PACKETSALT = "";//Put your Random words
    private $x = "test hello";

    //static $HASHID_HEAD = new Hashids\Hashids('salto url estaTica 123wiii', 3, 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-');

    function __contruct() {

    }
}

$Serialize = new Param();
$x = serialize($Serialize);
file_put_contents('test', $x);

echo 234;
exit;