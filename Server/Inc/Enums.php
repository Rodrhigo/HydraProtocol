<?php

abstract class ProtocolResponse {
    const Ok = 1;
    const InvalidFormat = 2;
}

abstract class NewBlockResponse {
    const Ok = 1;
    const MalFormatted = -1;
    const YouAreNotTheOwner = -2;
    const UnknownCipher = -3;
    const UnexpectedErrorInCipher = -4;
    const KeySizeNotSupported = -5;
    const SignHashNotSupported = -6;
    const CurveNotSupported = -7;
    const HeaderParamRequired = -8;
    const CipherNotSupportPbkFormat = -9;
    const InvalidPbk = -10;
}

abstract class NodeType {
    const Head = "head";
    const Packet = "packet";
}

abstract class PacketOptions{
    const JDownloaderCrypt = "jdcrypt";
    const Captcha = "captcha";
    /** @var string[] Array('JDownloaderCrypt'=>'jdcrypt',....) */
    public static $Options;

    static function Init(){
        $Class = new ReflectionClass(__CLASS__);
        self::$Options = $Class->getConstants();
    }
}
PacketOptions::Init();


abstract class BlockPbkFormat{
    const InsidePEM = "insidepem";
    const ShortPem = "shortpem";
}

abstract class PacketMode {
    const NoChildUpdate = "nochildupdate";
    const AddRemove = "addremove";
    const Sync = "sync";
}

abstract class NodeOrPacketOwner {
    const NoExits = -1;
    const NoOwner = 0;
    const Owner = 1;
}

abstract class CryptoException {
    const InvalidKeySize = 1;
    const InvalidPvk = 2;
    const InvalidCurve = 3;
    const PvkNull = 4;
    const PbkNull = 5;
}

/**
 * Class Hydra DefaultServer Transfer/Receive Fields
 */
abstract class Hydra{
    const S = '§';
}