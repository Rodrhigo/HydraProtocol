<?php
include_once 'Inc/Functions.php';

class Head extends GenericNode {
    public $ID;
    /** @var bool */
    private $IsNewID;
    //public $UpName; = parent::name
    //public $OriginalName;

    private $PacketCode;
    private $MirrorCode;
    /** @var Dynamic */
    private $Dynamic;

    //private $DynamicHost;
    /** @var in the future you can crypt you link, some host(rare) use subdomain for generate links MiID123.example.com */
    //private $DynamicSubdomain;
    /** @var
     * can be crypted, $DynamicHost=googledrive.com and $DynamicPath='/open?id=xx' or $DynamicPath='"#$!%!"#"!$' crypted and then the user need
     * access with fragment in the static url, example = autouploader.net/ex4mpl3#MyDecryptedCodeForDynamicPathOrSubdomain
     */
    //private $DynamicPath;

    //private $fSize;
    //private $fHash;
    //private $fHashCentaur;

    public function __construct($PbkUnique, $Code, $PacketCode = null, Dynamic $Dynamic = null, $MirrorCode = null /*$DynamicHost, $DynamicSubdomain,
 $DynamicPath, $UpName, $OriginalName, $fSize, $AutoPass, $ManualPass, $fHash, $fCentaurHash*/) {
        parent::__construct($PbkUnique, $Code, $Dynamic->GetUpName(), NodeType::Head, $Dynamic->GetUpPass(), $Dynamic->GetManualPass());
        $this->PacketCode = $PacketCode;
        $this->MirrorCode = $MirrorCode;
        $this->Dynamic = $Dynamic;
        //$this->OriginalName = $OriginalName;
        //$this->DynamicHost = $DynamicHost;
        //$this->DynamicSubdomain = $DynamicSubdomain;
        //$this->DynamicPath = $DynamicPath;
        //$this->fSize = $fSize;
        //$this->fHash = $fHash;
        //$this->fHashCentaur = $fCentaurHash;
    }

    public function NewHeadWithoutCode($Pbk, $Name, $Dynamic, $PacketCode = null, $MirrorID = null, $Message = null) {
        if ($Dynamic == null || !is_a($Dynamic, 'Dynamic')) return null;
        SQL::Query("ALTER TABLE static_url auto_increment = 1");//Prevent big Jump on massive inserts
        SQL::Query("insert into static_url(message) values('" . SQL::Escape($Message) . "')");
        $InsertID = SQL::LastInsertID();
        $Code = Cfg::GetHashidForHead()->encode($InsertID);
        SQL::Query("update static_url set code='$Code' where id='$InsertID'");
        //$Packet->Update('add')
        return new Head($Pbk, "#" . $Code, $PacketCode, $Dynamic, $MirrorID);
    }

    public static function NewHeadByCode($PbkUnique, string $Code, $PacketCode) {
        new Head($PbkUnique, $Code, null);
    }

    public static function NewHeadByHeadArray($PbkUnique, $HeadArray) {
        return new Head(['code'] ?? null, $HeadArray['options'] ?? null, $HeadArray['upname'] ?? null, CascadeType::Head);
    }

    public static function GetMirrorCodes($Code) {
        $Mirror = Array();
        $Query = SQL::Query("select pp1.static_code from packet_pointer pp1,packet_pointer pp2 where pp1.mirror_code=pp2.mirror_code and pp2.static_code='" .
            EscapeCode($Code) . "'");
        while($Row = $Query->fetch_assoc()){
            $Mirror[] = $Query['static_code'];
        }
    }

    /*public static function GetMirrorHeads($Code){
        self::GetMirrorCodes($Code);
    }*/

    public function AddDynamic(Dynamic $Dynamic) {
        //if (!$this->IsOwner()/*Centaur*/) return false;
        self::AddDynamic2CustomStatic($this->GetCode(), $Dynamic);
    }

    public static function AddDynamic2CustomStatic($StaticCode, Dynamic $Dynamic) {
        $Query = SQL::Query("insert into dynamic_url(static_code, server, dynamic_host, dynamic_subdomain, dynamic_path, fname_original, fname_upload," .
            "pass_auto, pass_manual, fsize, fhash, fhash_ref) values('" .
            EscapeCode($Dynamic->GetServer()) . "','" . SQL::Esc($Dynamic->GetHost()) . "','" . SQL::Esc($Dynamic->GetSubdomain()) . "','" .
            SQL::Esc($Dynamic->GetPath()) . "','" . SQL::Esc($Dynamic->GetOName()) . "','" . SQL::Esc($Dynamic->GetUpName()) . "','" .
            SQL::Esc($Dynamic->GetUpPass()) . "','" . SQL::Esc($Dynamic->GetManualPass()) . "','" . $Dynamic->GetFSize() . "','" .
            SQL::Esc($Dynamic->GetFHash()) . "','" . SQL::Esc($Dynamic->GetRefHash()) . "')"
        );
    }

    public function Update($OriginalName = null, $CustomPassword = null, $Message = null) {
        if (!$this->IsOwner()) return false;
        $Set = array();
        if ($OriginalName != null) $Set[] = "fname_original='" . SQL::Escape($OriginalName) . "'";
        if ($CustomPassword != null) $Set[] = "pass_manual='" . SQL::Escape($CustomPassword) . "'";
        if ($Message != null) $Set[] = "message='" . SQL::Escape($Message) . "'";
        if (count($Set) == 0) return true;

        $Query = SQL::Query("update dynamic_url set " . join(',', $Set) . " where id=(select id from dynamic_url where pbk='" . $this->GetEscapePbk() . "' and static_code='" . (EscapeCode($this->GetCode())) . "' order by id desc limit 1)");
        return $Query->num_rows;
    }

    /*public static function NewHeadWithoutCode($PbkOwner, Dynamic $Dynamic, $PacketCode) {
        SQL::Query("ALTER TABLE packet auto_increment = 1");
        $Query = SQL::Query("insert into static_url(date) values(CURRENT_TIMESTAMP)");
        $InsertID = SQL::LastInsertID();
        $Code = Cfg::GetHashidForHead()->encode($InsertID);
        SQL::Query("update packet set code='$Code' where id='$InsertID'");
        return new Head($PbkOwner, $Code, null);
    }*/

}