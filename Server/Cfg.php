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

    /** @var Hashids */
    private static $Hashid_Head;
    /** @var Hashids */
    public static $Hashid_Packet;

    public static function Init() {
        if (strlen(HASHID_HEADSALT) < 10) self::EchoExit("HASHID_HEADSALT");
        if (strlen(self::HASHID_PACKETSALT) < 10) self::EchoExit("HASHID_PACKETSALT");
        if (substr_count(HASHID_CHARACTERS, '"') + substr_count(HASHID_CHARACTERS, "'") > 0) self::EchoExit("HASHID_CHARACTERS");

        Cfg::$Hashid_Head = new Hashids(Cfg::HASHID_HEADSALT, 4/*<=3 Reserved*/, Cfg::HASHID_CHARACTERS);
        Cfg::$Hashid_Packet = new Hashids(Cfg::HASHID_PACKETSALT, 4, Cfg::HASHID_CHARACTERS);
    }

    public static function EchoExit($ConstOrField) {
        echo "Check/Set  $ConstOrField in Cfg.php";
        exit;
    }

    public static function GetHashidForHead() {
        return self::$Hashid_Head;
    }

    public static function GetHashidForPacket() {
        return self::$Hashid_Packet;
    }


}

Cfg::Init();




