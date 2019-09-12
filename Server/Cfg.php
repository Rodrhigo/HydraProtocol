<?php

use Hashids\Hashids;
use Hashids\HashidsException;

class Cfg {
    const SOCKET_SERVERADDRESS = "127.0.0.1";
    const SOCKET_SERVERPORT = 16223;

    const SQL_USER = "root";
    const SQL_PASS = "";
    const SQL_HOST = "127.0.0.1";
    const SQL_PORT = 3036;

    /** @var string Code Characters */
    const HASHID_CHARACTERS = "abcdefghijklmnopgrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ01234567890-";
    /** @var string  Put your Random words, JUST SET ONE TIME */
    const HASHID_HEADSALT = "";
    /** @var string Put your Random words need be != HASHID_HEADSALT, JUST SET ONE TIME(for collision) */
    const HASHID_PACKETSALT = "";

    private static $Hashid_Head;
    public static $Hashid_Packet;

    public static function Init() {
        Cfg::$HASHID_HEAD = new Hashids(Cfg::HASHID_PACKETSALT, 4/*<=3 Reserved*/, Cfg::HASHID_CHARACTERS);
        if (strlen(HASHID_HEADSALT) < 10) self::EchoExit("HASHID_HEADSALT");
        if (strlen(self::HASHID_PACKETSALT) < 10) self::EchoExit("HASHID_PACKETSALT");
    }

    public static function EchoExit($ConstOrField) {
        echo "Check/Set  $ConstOrField in Cfg.php";
    }

    public static function GetHashidForHead() {
        return self::$Hashid_Head;
    }

    public static function GetHashidForPacket() {
        return self::$Hashid_Packet;
    }

}

Cfg::Init();




