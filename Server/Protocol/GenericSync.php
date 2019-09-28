<?php

abstract class GenericSync {
    //public $Name; Head no puede editar el nombre
    private $Pbk;
    private $Code;
    protected $Message;
    private $IsOwner;
    private $IP;
    /** @var NodeType */
    private $Type;
    /**
     * @var Manual Password;
     */
    private $Password;

    public function __construct($Pbk, $Code, $NodeType, $IP, $Password, $Message) {
        $this->Pbk = $Pbk;
        $this->Code = $Code;
        $this->Password = $Password;
        $this->IP = $IP;
        $this->Type = $NodeType;
        //$this->Options = $Options;
        $this->Message = $Message;
    }

    /**
     * @return int|NodeOrPacketOwner
     */
    public function IsOwner() {//If Admin, true
        if ($this->IsOwner !== null && is_int($this->IsOwner)) return $this->IsOwner;

        $Query = Sql::Query("select pbk from " . ($this->Type == NodeType::Packet ? "packet" : "static_url") . " where code='" . $this->Escape($this->GetCode()
            ) . "'  limit 1");//and pbk='" . $this->Escape($this->GetPbk()) . "'

        if ($Query->num_rows != 1) return $IsOwner = NodeOrPacketOwner::NoExits;

        $Pbk = $Query->fetch_assoc()->pbk;
        if ($this->PbkUnique == $Pbk) return $IsOwner = NodeOrPacketOwner::Owner; else return $IsOwner = NodeOrPacketOwner::NoOwner;
    }

    /**
     * @return GenericNode|Packet|Head|null
     */
    public function Process() {
        if ($this->IsOwner()) return $this->_Process();
        else return null;
    }

    protected abstract function _Process();


    public function GetCode() {
        return $this->Code;
    }

    public function GetOptions() {
        return $this->Options;
    }

    public function GetPbk() {
        return $this->PbkUnique;
    }

    public function GetPassword() {
        return $this->Password;
    }

    public function GetIP() {
        return $this->IP;
    }
}