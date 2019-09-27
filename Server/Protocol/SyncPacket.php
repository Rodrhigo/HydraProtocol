<?php

class SyncPacket extends GenericSync {
    /** @var SyncHead */
    private $Heads;
    private $Title;
    private $Options = null;

    public $Mode;
    public $Add;
    public $Remove;
    public $Sync;

    public function __construct($Pbk, $Code, $PacketArray) {
        $this->Add = NewArray($PacketArray['add'] ?? null);
        $this->Remove = NewArray($PacketArray['remove'] ?? null);
        $this->Sync = NewArray($PacketArray['sync'] ?? null);
        $this->Mode = in_array($PacketArray['mode'] ?? null, Array(PacketMode::NoChildUpdate, PacketMode::AddRemove, PacketMode::Sync)) ? $PacketArray['mode'] : PacketMode::NoChildUpdate;
        $this->Title = $PacketArray['title'] ?? null;
        $this->Options = $PacketArray['options'] ?? null;
        parent::__construct($Pbk, $Code, $PacketArray['message'] ?? null);
    }

    public function Process() {
        if (!$this->IsOwner()) return null;
        $CodeEsc = EscapeCode($this->GetCode());
        $Values = array();
        $OwnerCodes = array();

        if ($this->Mode == PacketMode::Sync) {
            SQL::Query("delete from packet_pointer where packet_code='$CodeEsc'");
            $Remove = $Add = $this->FilterOwnerHeads($this->Sync);

            //$Values[] = "('$CodeEsc','')";
        } elseif ($this->Mode == PacketMode::AddRemove) {
            $Add = $this->FilterOwnerHeads($this->Add);
            $Remove = array_intersect($Add, $this->FilterOwnerHeads($this->Remove));
        }

        SQL::Query("delete from packet_pointer pp where " . ArrayFormat($Remove, "static_code='%s' ", " or ", false));
        SQL::Query("insert into packet_pointer(packet_code, static_code) values" . ArrayFormat($Add, "('$CodeEsc', '%s')", ","));

        if ($this->GetOptions()) {

        }

        $Update = array();
        if ($this->Title != null) $Update[] = "name='" . SQL::Escape($this->Title) . "'";
        if ($this->Message != null) $Update[] = "message='" . SQL::Escape($this->Message) . "'";
        if ($this->GetOptions()) {
            $CurrentOptions = Packet::GetOptions($this->GetCode());
            $NewOptions = array_merge($CurrentOptions, $this->GetOptions());//Captcha=false,JdCrypt=false,NewOptions=Captcha=true => Captcha=true,JdCrypt=true

            $Update[] = "options='" . SQL::Escape(json_encode($NewOptions)) . "'";
        }
        SQL::Query("update packet set " . join(",", $Update));

        return new Packet($this->GetPbk(), $this->GetCode());
        //if(isset($Packets[$Json['id']]))conti
    }

}