<?php
include_once 'Inc/Functions.php';
include_once 'Protocol/GenericNode.php';

class Packet extends GenericNode {
    private $Heads;
    /** @var array Options */
    private $Options = null;

    /** @var array My Heads */


    public function __construct(string $Code/*string $PbkPEM, */) {
        parent::__construct(/*$PbkPEM,*/ $Code, $PacketArray['options'] ?? null, $PacketArray['name'] ?? null, CascadeType::Packet);


    }

    public static function NewPacketWithoutCode($Pbk, $Name, array $Options = array(), $Heads = null, $Message = null) {
        SQL::Query("ALTER TABLE packet auto_increment = 1");//Prevent big Jump on massive inserts
        SQL::Query("insert into packet(name,message,options) values('" . SQL::Escape($Name) . "','" . SQL::Escape($Message) . "','" . SQL::Escape(json_encode($Options)) . "')");
        $InsertID = SQL::LastInsertID();
        $Code = Cfg::GetHashidForPacket()->encode($InsertID);
        SQL::Query("update packet set code='$Code' where id='$InsertID'");
        return new Packet($Pbk, null, null);
    }


    public function Update($Name = null, $Msg = null, $Options = null, $AddRemove = Array('Remove' => Array(), 'Add' => Array()), $Sync = null) {
        if (!$this->IsOwner()) return false;
        //SQL::AutoCommit(false);
        $this->SetOptions($Options);
        $TotalAffected = 0;

        foreach ($AddRemove as $Key => $ArrayValue) {
            $Key = strtolower($Key);
            if ($Key = 'add') $TotalAffected += $this->AddHeads($ArrayValue);
            elseif ($Key != 'remove') $TotalAffected += $this->RemoveHeads($ArrayValue);
            else continue;
        }
        if ($Sync != null && is_array($Sync)) $TotalAffected += $this->Sync();
        if ($TotalAffected > 0) $this->Heads = null;//Then GetHeads() work fine.
        //SQL::AutoCommit(true);
        return true;
    }

    /**
     * @param $HeadUrls array Array('MyStaticUrl'=>'MirrorCode', 'ExampleStatic'=>null, 'Static2'=>'MirrorCode')
     */
    private function Sync($HeadUrls) {
        if (!$this->IsOwner() || !is_array($HeadUrls)) return 0;
        $MyStatic = $this->FilterOwnerHeads($HeadUrls, true);
        $Values = Array();
        foreach ($MyStatic as $HeadUrl => $MirrorCode) $Values = "('" . EscapeUrl($this->GetCode()) . "'','" . EscapeUrl($HeadUrl) . "','" . $MirrorCode . "')";
        SQL::Query("delete from packet_pointer where pbk='" . $this->GetEscapePbk() . "' and packet_code='" . EscapeUrl($this->GetCode()) . "' ");
        SQL::Query("insert into packet_pointer('packet_code', 'static_code', 'mirror_code') values " . join(',', $Values));
        return SQL::AffectedRows();
    }

    /**
     * @param $HeadUrls array Array('MyStaticUrl'=>'MirrorCode', 'ExampleStatic'=>null, 'Static2'=>'MirrorCode')
     */
    private function AddHeads($HeadUrls) {
        if ($this->Mode != PacketMode::AddRemove || !$this->IsOwner()) return 0;
        if (count($MyStatic = $this->FilterOwnerHeads($HeadUrls, true)) === 0) return 0;
        $Values = Array();

        foreach ($MyStatic as $Head) $Values[] = "('" . EscapeUrl($this->GetCode()) . "','" . EscapeUrl($HeadUrls['staticurl']) . "')";
        $this->RemoveHeads(array_keys($HeadUrls));
        SQL::Query("insert into packet_pointer(packet_code, static_code, mirror_code) values " . join(',', $Values));
        return SQL::AffectedRows();
    }

    private function RemoveHeads($HeadUrls) {
        if ($this->Mode != PacketMode::AddRemove || !$this->IsOwner()) return 0;
        $Where = Array();
        if (count($MyStatic = $this->FilterOwnerHeads($HeadUrls, true)) === 0) return 0;
        foreach ($MyStatic as $Head) $Where[] = "(static_url='" . EscapeUrl($Head/*Already Escape(From db) but..*/) . "')";
        SQL::Query("delete from packet_pointer where " . join(' or ', $Where));
        return SQL::AffectedRows();
    }

    /**
     * string[]
     */
    public static function GetOptions($Code) {
        //if (!$this->IsOwner()) return null;
        $Options = Array();
        $Query = SQL::Query("select options from packet where packet_code='" . EscapeCode($Code) . "' limit 1");
        if ($Query->num_rows == 1) $Options = json_decode($Query->fetch_assoc()->options);
        return $Options;
    }

    public function GetHeads() {
        if ($this->Heads != null && is_array($this->Heads)) return $this->Heads;

        $Heads = Array();
        $Query = SQL::Query("select pp.mirror_code, creation_date, ip, du.static_code, server, dynamic_host, dynamic_subdomain, " .
            "dynamic_path, fname_original, fname_upload, pass_auto, pass_manual, fsize, fhash, fhash_centaur " .
            "from packet_pointer pp, dynamic_url du where " .
            "packet_code='" . EscapeCode($this->GetCode()) . "' and du.static_code=pp.static_code and pbk='" . $this->GetEscapePbk() . "'"
        );
        while ($Row = $Query->fetch_assoc()) {
            $Heads[$Row->Code] = new Head($this->GetPbk(), $Row->Code, $this->GetCode(), $this->mirror_code, $Row->dynamic_host, $Row->dynamic_subdomain,
                $Row->dynamic_path,
                $Row->fname_upload, $Row->fname_original, $Row->fsize, $Row->pass_auto,
                $Row->pass_manual, $Row->fhash, $Row->fhash_centaur
            );
        }
        return ($this->Heads = $Heads);
    }


    /**
     * @param $NewOptions
     * @return array|null Return null if IsOwner=false otherwise return funsion Array of OldOptions('jdcrypt'=>true,'captcha'=>false) with NewOptions
     * ('jdcrypt'=>false)=> ('jdcrypt'=>false,
     * 'captcha'=>false)
     */
    private function SetOptions($NewOptions) {
        if (!$this->IsOwner()) return null;
        $OldOptions = $this->GetOptions();
        if ($NewOptions === null) return $OldOptions;

        if (is_string($NewOptions) && in_array($NewOptions, Array("clear", "inherit", "default"))) $OldOptions = $NewOptions = Array();
        else {
            if (!is_array($NewOptions)) $NewOptions = Array();
            foreach ($NewOptions as $Key => $Enable) $NewOptions[strtolower($Key)] = ($Enable === true || $Enable === 1 || $Enable === "on");
        }


        $FusionOptions = array_merge($OldOptions, $NewOptions);//$NewOptions overwrite $OldOptions keys
        SQL::Query("update packet set options='" . SQL::Escape(json_encode($FusionOptions)) . "' where packet_code='" . SQL::Escape($this->GetCode()) . "'");
        $this->Options = $FusionOptions;
    }

}