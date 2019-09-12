<?php
include_once 'Inc/Functions.php';

class Head extends GenericNode {
    public $ID;
    private bool $IsNewID;
    //public $UpName; = parent::name
    public $OriginalName;

    private $PacketCode;
    private $MirrorCode;

    private $DynamicHost;
    /** @var in the future you can crypt you link, some host(rare) use subdomain for generate links MiID123.example.com */
    private $DynamicSubdomain;
    /** @var
     * can be crypted, $DynamicHost=googledrive.com and $DynamicPath='/open?id=xx' or $DynamicPath='"#$!%!"#"!$' crypted and then the user need
     * access with fragment in the static url, example = autouploader.net/ex4mpl3#MyDecryptedCodeForDynamicPathOrSubdomain
     */
    private $DynamicPath;

    private $fSize;
    private $fHash;
    private $fHashCentaur;

    public function __construct($PbkUnique, $Code, $PacketCode, $MirrorCode, $DynamicHost, $DynamicSubdomain, $DynamicPath, $UpName, $OriginalName,
                                $fSize, $AutoPass, $ManualPass, $fHash, $fCentaurHash) {
        parent::__construct($PbkUnique, $Code, $UpName, NodeType::Head, $AutoPass, $ManualPass);
        $this->PacketCode = $PacketCode;
        $this->OriginalName = $OriginalName;
        $this->DynamicHost = $DynamicHost;
        $this->DynamicSubdomain = $DynamicSubdomain;
        $this->DynamicPath = $DynamicPath;
        $this->fSize = $fSize;
        $this->fHash = $fHash;
        $this->fHashCentaur = $fCentaurHash;
        $this->MirrorCode = $MirrorCode;
    }

    public static function NewHeadByCode($PbkUnique, string $Code, $PacketCode) {
        new Head($PbkUnique, $Code, null);
    }

    public static function NewHeadByHeadArray($PbkUnique, $HeadArray) {
        return new Head(['code'] ?? null, $HeadArray['options'] ?? null, $HeadArray['upname'] ?? null, CascadeType::Head);
    }

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

        $Query = SQL::Query("update dynamic_url set " . join(',', $Set) . " where id=(select id from dynamic_url where pbk='" . $this->GetEscapePbk() . "' and static_code='".(EscapeCode($this->GetCode()))."' order by id desc limit 1)");
        return $Query->num_rows;
    }


}