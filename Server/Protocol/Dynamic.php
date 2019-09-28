<?php

class Dynamic {
    private $Host;
    private $Subdomain;
    private $Path;

    private $SuggestServerName;
    private $CreationTime;
    private $UpName;
    private $OriginalName;
    private $fSize;
    private $AutoPass;
    private $ManualPass;
    private $fHash;
    private $RefHash;

    public function __construct($SuggestedServerName, $DynamicHost, $DynamicSubdomain, $DynamicPath, $UpName, $OriginalName, $fSize, $AutoPass,
                                $ManualPass, $fHash, $RefHash) {
        $this->Host = $DynamicHost;
        $this->Subdomain = $DynamicSubdomain;
        $this->Path = $DynamicPath;
        $this->UpName = $UpName;
        $this->OriginalName = $OriginalName;
        $this->fSize = ctype_digit($fSize . '') ? (int)$fSize : 0;
        $this->AutoPass = $AutoPass;
        $this->ManualPass = $ManualPass;
        $this->fHash = $fHash;
        $this->RefHash = $RefHash;
    }

    public function IsValid() {
        return true;
    }

    public function GetHost() { return $this->Host; }

    public function GetSubdomain() { return $this->Subdomain; }

    public function GetPath() { return $this->Path; }

    public function GetCreationTime() { return $this->CreationTime; }

    public function GetUpName() { return $this->UpName; }

    public function GetOName() { return $this->OriginalName(); }

    public function GetFSize() { return (int)$this->fSize; }

    /**
     * @return string Password provided by the uploader manager after upload the file.
     */
    public function GetUpPass() { return $this->AutoPass; }

    /**
     * @return string Password provided by the user/uploader in any moment(Example: 1 week after upload the file).
     */
    public function GetManualPass() { return $this->ManualPass; }

    public function GetFHash() { return $this->fHash; }

    /**
     * @return mixed Hash provided by thrid party of the file that he download. The server compare his hash with the autor hash, prevent scam.
     */
    public function GetRefHash() { return $this->RefHash; }
}





