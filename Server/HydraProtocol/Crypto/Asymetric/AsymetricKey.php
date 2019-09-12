<?php

/*
 * Estara asociado a una conexión Socket, evito instanciar cada vez una llave
 */

abstract class AsymetricKey {
    protected $Pvk;
    protected $Pbk;
    private $PbkUnique;
    private $PbkPEM;
    private $_PEMPbkOneLine;
    protected $Name;
    protected $KeySize;
    /** @var string[] */
    protected $CurveOrExtra;
    protected $PbkFormat;
    protected $SignatureHashAlgorithm;

    //protected $DefaultSignHash;

    public abstract function Verify($BlockContent, $Signature);

    abstract protected function _GetPbkUnique();

    abstract protected function _GetPbkPEM();

    abstract public function Sign($Data);

    public function __construct($Name, $KeySize, $SignatureHashAlgorithm, $CurveOrExtra = Array()) {
        $this->Name = $Name;
        $this->KeySize = $KeySize;
        $this->SignatureHashAlgorithm = $SignatureHashAlgorithm;
        if (!is_array($CurveOrExtra)) $CurveOrExtra = Array();
        $this->CurveOrExtra = $CurveOrExtra;
    }

    public function GetName() {
        return $this->Name;
    }

    public function GetPbkUnique() {
        return $this->PbkUnique ?? $this->PbkUnique = $this->_GetPbkUnique();
    }

    public function GetPbkPEM() {
        return $this->PbkPEM ?? $this->PbkPEM = $this->_GetPbkPEM();
    }

    public function PEMPbkOneLine() {
        if ($this->_PEMPbkOneLine == null) $this->_PEMPbkOneLine = str_replace("\r\n", "",
            str_replace("-----END PUBLIC KEY-----", "",
                str_replace("-----BEGIN PUBLIC KEY-----", '', $this->GetPbkPEM())));
        return $this->_PEMPbkOneLine;
    }

    public function SignBase64($Data) {
        return $this->Sign($Data);
    }


    public function ToString() {
        $CurveOrExtra = "";
        foreach ($this->CurveOrExtra as $Key => $Value) $CurveOrExtra .= "-$Key:$Value";

        $Pieces = Array($this->GetName(), $this->KeySize, $this->SignatureHashAlgorithm . $CurveOrExtra);
        /*("-" + string . Join("-", CurveOrExtra . Select(x => x . Key + ":" + x . Value).ToArray())) : "") +
(blockPbkFormat != BlockPbkFormat . InsidePEM ? "-PbkFormat:" + blockPbkFormat . ToString() : null);*/
        return join("-", $Pieces);
    }
}