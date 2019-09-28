<?php
include_once 'hashids/HashidsInterface.php';
include_once 'hashids/Hashids.php';
include_once 'hashids/HashidsException.php';
include_once 'vendor/autoload.php';

use Hashids\Hashids;
use Hashids\HashidsException;

define('ROOT', $_SERVER['DOCUMENT_ROOT']);
const SOCKET_SERVERADDRESS = "127.0.0.1";
const SOCKET_SERVERPORT = 16223;

const SQL_USER = "root";
const SQL_PASS = "";
const SQL_HOST = "127.0.0.1";
const SQL_PORT = 3306;
const SQL_SCHEMA= null;

/** @var string Code Characters */
const HASHID_CHARACTERS = "abcdefghijklmnopgrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890-";
/** @var string  Put your Random words, JUST SET ONE TIME */
const HASHID_HEADSALT = "1234567890";
/** @var string Put your Random words need be != HASHID_HEADSALT, JUST SET ONE TIME(for collision) */
const HASHID_PACKETSALT = "1234567890";
/** Reserved Paths, miweb.com/(admin|user|tos|pay|service)  */
const RESERVED_PATHS = array('admin', 'user', 'tos');

$HASHID_HEAD = new Hashids(HASHID_HEADSALT, 4/*<=3 Reserved*/, HASHID_CHARACTERS);
$HASHID_PACKET = new Hashids(HASHID_PACKETSALT, 4, HASHID_CHARACTERS);

class Cfg {
    public static function EchoExit($ConstOrField) {
        echo "Check/Set  $ConstOrField in Cfg.php";
        exit;
    }

    public static function Init() {
        if (strlen(HASHID_HEADSALT) < 10) self::EchoExit("HASHID_HEADSALT");
        if (strlen(HASHID_PACKETSALT) < 10) self::EchoExit("HASHID_PACKETSALT");
        if (substr_count(HASHID_CHARACTERS, '"') + substr_count(HASHID_CHARACTERS, "'") > 0) self::EchoExit("HASHID_CHARACTERS");

    }

    public static function GetHashidForHead() {
        return self::$Hashid_Head;
    }

    public static function GetHashidForPacket() {
        return self::$Hashid_Packet;
    }
}

Cfg::Init();




