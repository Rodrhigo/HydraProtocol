<?php

class SyncHead extends GenericSync {
    private Dynamic $Dynamic;

    public function __construct($Pbk, $Code, $Head) {
        $Dynamic = null;
        if (isset($Head['dynamic'])) {
            $Dyn = $Head['dynamic'];
            $Dynamic = new Dynamic("", $Dyn['host'], $Dyn['subdomain'], $Dyn['path'], $Dyn['name'] ?? $Dyn['upname'] ?? null, $Dyn['originalname'], $Dyn['fsize'], $Dyn['passauto'] ?? $Dyn['autopass'] ?? null, $Dyn['passmanual'] ?? $Dyn['manualpass'] ?? null, $Dyn['fhash'], $Dyn['refhash']);
        }
        if ($Dynamic != null && $Dynamic->IsValid()) $this->Dynamic = $Dynamic;
        parent::__construct($Pbk, $Code, $Head['message'] ?? null);
    }


    public function Process() {
        if ($this->Dynamic == null) return;

    }

}