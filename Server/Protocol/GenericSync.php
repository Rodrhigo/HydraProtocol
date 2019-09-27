<?php

class GenericSync {
    //public $Name; Head no puede editar el nombre
    private $Pbk;
    private $Code;
    protected $Message;
    private $IsOwner;
    /**
     * @var Manual Password;
     */
    private $Password;

    public function __construct($Pbk, $Code, $Password, $Message) {
        $this->Pbk = $Pbk;
        $this->Code = $Code;
        $this->Password = $Password;
        //$this->Options = $Options;
        $this->Message = $Message;
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

    public abstract function Process();

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
}