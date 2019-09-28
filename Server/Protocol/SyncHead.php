<?php
include_once 'Protocol/Dynamic.php';

class SyncHead extends GenericSync {
    /** @var Dynamic|null */
    private $Dynamic;
    private $MirrorCode;

    public function __construct($Pbk, $Code, $IP, $Head) {
        $Dynamic = null;
        if (array_key_exists('dynamic', $Head)) {
            $Dyn = $Head['dynamic'];
            $Dynamic = new Dynamic($Dyn['server'] ?? null, $Dyn['host'] ?? null, $Dyn['subdomain'] ?? null, $Dyn['path'] ?? null, $Dyn['name'] ?? $Dyn['upname'] ?? null, $Dyn['originalname'] ?? null, $Dyn['fsize'] ?? 0, $Dyn['passauto'] ?? $Dyn['autopass'] ?? null, $Dyn['passmanual'] ?? $Dyn['manualpass'] ?? null, $Dyn['fhash'] ?? null, $Dyn['refhash'] ?? null
            );
        }
        if ($Dynamic != null && $Dynamic->IsValid()) $this->Dynamic = $Dynamic;
        /* If $Code is RefCode create NewCode ...*/
        parent::__construct($Pbk, $Code, NodeType::Head, $IP, $Head['manualpass'] ?? $Head['passmanual'] ?? $Head['password'], $Head['message'] ?? null);
    }


    public function _Process() {
        if ($this->Dynamic == null) {
            if ($this->GetPassword() != null) {
                $Row = SQL::Query("select id from dynamic_url where static_code='" . EscapeCode($this->GetCode()) . "' order by id desc")->fetch_assoc();
                if ($Row != null && ($LastID = (int)$Row['id']) > 0) {
                    SQL::Query("update dynamic_url set pass_manual='" . SQL::Escape($this->GetPassword()) . "' where id='$LastID'");
                }
            }
        } elseif ($this->Dynamic->IsValid()) {
            SQL::Query("insert into dynamic_url(static_code,host,subdomain,path,fname_original,fname_upload,pass_auto,pass_manunal,fsize,fhash,refhash) values(" .
                "'" . EscapeCode($this->GetCode()) . "','" .
                SQL::Escape($this->Dynamic->GetHost()) . "','" .
                SQL::Escape($this->Dynamic->GetSubdomain()) . "','" .
                SQL::Escape($this->Dynamic->GetPath()) . "','" .
                SQL::Escape($this->Dynamic->GetOName()) . "','" .
                SQL::Escape($this->Dynamic->GetUpName()) . "','" .
                SQL::Escape($this->Dynamic->GetUpPass()) . "','" .
                SQL::Escape($this->Dynamic->GetManualPass()) . "','" .
                ((int)$this->Dynamic->GetFSize()) . "','" .
                SQL::Escape($this->Dynamic->GetFHash()) . "','" .
                SQL::Escape($this->Dynamic->GetRefHash()) . "')"
            );
        }
        return new Head($this->GetPbk(), $this->GetCode());
    }

}