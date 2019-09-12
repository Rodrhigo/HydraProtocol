<?php

abstract class GenericNode {
    private $PbkUnique;
    private $PbkEscape;
    private $Code;
    private $Name;

    private $PassAuto;
    private $PassManual;
    /**
     * @var bool if you use # before the Url, $IsNewUrl = true. #RefNewID123
     */
    private $IsNewUrl;
    /**
     * @var string packet|node use NodeType::Packet|CascadeType::Node
     */
    private $Type;
    /** @var array cache Owner Heads, Array('AxE'=>true, 'Aww'=>false, 'Oke'=>true) */
    private $OwnerHeads;
    /**
     * @var int|NodeOrPacketOwner|null
     */
    private $IsOwner = null;

    public function __construct($PbkUnique, $Code, $Name, $Type, $PassAuto, $PassManual) {
        $this->PbkUnique = $PbkUnique;
        $this->Code = trim(substr($Code, ($Code . ' ')[0] == '#' ? 1 : 0));
        $this->IsNewID = ($Code . ' ')[0] == '#';
        $this->Name = $Name;
        $this->Type == $Type;
        $this->PassAuto = $PassAuto;
        $this->PassManual = $PassManual;
    }

    public function GenericUpdate($Name = null, $Password = null, $Message = null) {
        if ($Name == null && $Password == null && $Message == null) return false;
        if (!$this->IsOwner()) return false;

        Sql::Query("update " . ($this->Type == NodeType::Packet ? "packet" : "url_dynamic") . " set " . ($Name !== null ? "name='" .
                Sql::Escape($Name) . "'" : "")
            . ($Password !== null ? "password='" . Sql::Escape($Password) . "'" : "")
            . ($Message !== null ? "message='" . Sql::Escape($Message) . "'" : ""));
    }

    /**
     * @return int|NodeOrPacketOwner
     */
    public function IsOwner() {//If Admin, true
        if ($this->IsOwner !== null && is_int($this->IsOwner)) return $this->IsOwner;
        $Query = Sql::Query("select pbk from packet where packet_url=''" . $this->Escape($this->ID) . "' limit 1");
        if ($Query->num_rows != 1) return $IsOwner = NodeOrPacketOwner::NoExits;

        $Pbk = $Query->fetch_assoc()->pbk;
        if ($this->PbkUnique == $Pbk) return $IsOwner = NodeOrPacketOwner::Owner; else return $IsOwner = NodeOrPacketOwner::NoOwner;
    }

    public function GetPbk() {
        return $this->PbkUnique;
    }

    /** Check if you are the owner of the $HeadsUrls
     * @param $HeadsUrls
     * @param bool $IsHeadInKey if is true = Array('MyHeadUrl'=>null / MirrorCode), false = Array(0=>'MyHeadUrl'....n=>'xxx')
     * @return array Owner Heads
     */
    public function FilterOwnerHeads($HeadsUrls, $IsHeadInKey = false) {
        $MyStatic = $Where = Array();
        foreach ($HeadsUrls as $Key => $Value) {
            $HeadUrl = $IsHeadInKey ? $Key : $Value;
            if (!isset($this->OwnerHeads[$HeadUrl])) $Where[] = '(static_url=' . EscapeUrl($HeadUrl) . " and pbk='" . $this->GetEscapePbk() . "')";
            elseif ($this->OwnerHeads[$HeadUrl] === true) $MyStatic[] = $HeadUrl;
        }
        if (count($Where) > 0) {
            $Query = SQL::Query("select static_url from static_url where " . join(" or ", $Where));
            while ($StaticUrl = $Query->fetch_assoc()) $MyStatic[] = $StaticUrl->static_url;
        }
        return $MyStatic;
    }

    public function JoinAndEscapeUrls($Glue, $Urls) {
        foreach ($Urls as $Key => $Url) $Urls[$Key] = EscapeUrl($Url);
        return join($Glue, $Urls);
    }

    public function GetEscapePbk() {
        if ($this->PbkEscape === null && $this->GetPbk() !== null) {
            $this->PbkEscape = SQL::Escape($this->GetPbk());
        }
        return $this->PbkEscape;
    }

    public function GetName() {
        return $this->Name;
    }

    public function GetCode() {
        return $this->Code;
    }

    public function IsNewUrl() {
        return $this->IsNewUrl;
    }

    public function GetType() {
        return $this->Type;
    }
}