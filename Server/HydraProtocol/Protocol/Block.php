<?php

require __DIR__ . "/../vendor/autoload.php";
include_once 'Crypto/Asymetric/ECDSA.php';
include_once 'Sql.php';

class Block implements IBlock {

    /*public $Cipher;
    public $KeySize;
    public $HashAlgorithm;
    public $KeyFormat;*/
    /**
     * @var AsymetricKey ECDSA|RSA
     */
    public $Asymmetric;
    protected $Lines;

    //private function Block($Cipher, $Pbk, $KeySize, $HashAlgorithm, $Lines, $Signature, $ID = null) {

    /**
     * Block constructor.
     * @param $Asymmetric AsymetricKey
     * @param $Lines Array
     * @param null $ID string
     */
    private function Block($Asymmetric, $Lines, $ID = null) {
        /*$this->Cipher = $Cipher;
        $this->KeySize = $KeySize;
        $this->HashAlgorithm = $HashAlgorithm;
        $this->Lines = $Lines;
        $this->Pbk = $Pbk;*/
        if ($Asymmetric == null || !($Asymmetric instanceof AsymetricKey)) throw new InvalidArgumentException();
        $this->Asymmetric = $Asymmetric;
        $this->Lines = $Lines;
        $this->ID = $ID;
    }

    /**
     * @param $Asymmetric
     * @param $Content
     * @param int|null $ID
     * @return Block
     * @throws CryptoException
     */
    public static function NewBlockByContent($Asymmetric, $Content, int $ID = null) {
        if ($Asymmetric == null) {
            $Asymmetric = ECDSA::NewECDSAByPvk(null);
        }

        return new Block($Asymmetric, self::ContentToLines($Content), $ID);
    }


    public static function GetNameValueInHeader($Params) {
        $Return = Array();
        $NameAndValueDetected = false;
        $DefaultNames = Array('cipher', 'keysize', 'signhash');
        for ($i = 0; $i < count($Params); $i++) {
            $NameValue = explode(':', $Params[$i], 2);
            //RequiredValue1-BadOptional:MyBadValue-RequieredValue2 (Optional/BadOptional must be in the end)
            if (count($NameValue) == 2 && !$NameAndValueDetected) $NameAndValueDetected = true; elseif (isset($DefaultNames[$i])) array_unshift($NameValue, $DefaultNames[$i]);
            $NameValue[0] = strtolower($NameValue[0]);
            //$CurrentName = (!$NameAndValueDetected && isset($DefaultNames[$i])) ? strtolower($DefaultNames[$i]) : strtolower($NameValue[0]);
            if (isset($Return[$NameValue[0]])) return null;//RSA-1024-sha2-cipher:example, cipher already exists(cipher:RSA)

            $Return[$NameValue[0]] = count($NameValue) == 2 ? strtolower($NameValue[1]) : null;
            //if (trim($NameValue[0]) == '' && $i < count($DefaultNames)) return null;
            //if ($DefaultNames != null && array_key_exists($i, $DefaultNames)) $Return[strtolower($DefaultNames[$i])] = trim($NameValue[count($NameValue) == 2 ? 1 : 0]);
            //else $Return[$NameValue[count($NameValue) - 1]] = null;
        }

        foreach ($DefaultNames as $RequiredName) {
            if (!isset($Return[$RequiredName]) || trim($Return[$RequiredName] . '') == '') return null;
        }
        return $Return;
    }

    public static function GetBlockName($BlockID) {
        return "Block" . $BlockID != null ? "-$BlockID" : '';
    }

    public function GetCurrentBlockName() {
        self::GetBlockName($this->ID);
    }

    public function GetLines() {
        return $this->Lines;
    }

    /*
     * §[Cipher:]ECDSA-256-SHA256-Secp256k1-Format:InsidePem|ExampleCompressAlg:None§Pbk
     * [Cipher:] Optional
     */

    public static function NewBlockByHeaderContentHash($Header, $Content, $DecodedSignature) {
        $LeftRightHeader = explode('§', $Header);
        if (count($LeftRightHeader) != 3 || $LeftRightHeader[0] != '' || $LeftRightHeader[1] == '' || $LeftRightHeader[2] == '') throw new BlockMalFormatted(NewBlockResponse::MalFormatted);
        $HeaderParams = explode('-', $LeftRightHeader[1]);
        $Pbk = $LeftRightHeader[2];
        //§ECDSA-256-SHA256-Curve:secp256k1-HiWorld-Hi:Bye§7

        /* $NameValue = split(':', $HeaderParams[$i], 1);
          if (strlen($NameValue) == 0) return NewBlockResponse::MalFormatted;
          if (!$NameValue || count($NameValue) == 0 || (count($NameValue) === 1 && $i != 0)) return NewBlockResponse::MalFormatted;
          $ParamName = strtolower($i == 0 ? 'cipher' : $NameValue[0]);
          $ParamValue = $NameValue[count($NameValue) - 1]; */
        $HeaderValue = Block::GetNameValueInHeader($HeaderParams);
        if (!isset($HeaderValue['pbkformat'])) $HeaderValue['pbkformat'] = "insidepem";
        //print_r($HeaderValue);
        if ($HeaderValue == null) throw new BlockInvalid(NewBlockResponse::MalFormatted);
        $FuncName = "_HeaderBlock_cipher_" . $HeaderValue['cipher'];

        if (!method_exists('Block', $FuncName)) throw new BlockInvalid(NewBlockResponse::UnknownCipher);
        try {
            $Block = call_user_func("Block::$FuncName", $Pbk, $HeaderValue, $Content, $DecodedSignature); //return new Block();
            return $Block;
        } catch (Exception $Ex) {
            throw new BlockInvalid(NewBlockResponse::UnexpectedErrorInCipher, null/* $Ex */);
        }
    }

    /*
     * 
     */

    /**
     * @param $Pbk
     * @param $CipherParams
     * @param $Content
     * @param $Hash
     * @return Block|BlockMalFormatted
     * @throws \FG\ASN1\Exception\ParserException
     */
    public static function _HeaderBlock_cipher_ecdsa($Pbk, $CipherParams, $Content, $Signature) {
        if ($CipherParams['keysize'] != 256) throw BlockInvalid(NewBlockResponse::NotSupportedKeySize);

        if (in_array(strtolower($CipherParams['signhash']), Array('sha256'))) $Algorithm = $CipherParams['signhash']; else throw new BlockInvalid(NewBlockResponse::SignHashNotSupported);

        if (!isset($CipherParams['curve'])) throw new BlockInvalid(NewBlockResponse::HeaderParamRequired, "Curve");
        if ($CipherParams['curve'] != 'secp256k1' && $CipherParams['curve'] != 'secp256r1') throw new BlockInvalid(NewBlockResponse::CurveNotSupported);

        if ($CipherParams['pbkformat'] != 'insidepem' && $CipherParams['pbkformat'] != 'shortpem') throw new BlockInvalid(NewBlockResponse::CipherNotSupportPbkFormat);
        if (strlen($Pbk) < 64) throw new BlockInvalid(NewBlockResponse::InvalidPbk);

        $PbkInsidePEM = ($CipherParams['pbkformat'] == 'shortpem' ? ($CipherParams['curve'] == 'secp256k1' ? ECDSA::Secpk1PbkPEM64Start : ECDSA::Secpr1PbkPEM64Start) : '') . $Pbk;
        $PbkPEM = "-----BEGIN PUBLIC KEY-----\r\n" . $PbkInsidePEM . "\r\n-----END PUBLIC KEY-----";
        $ECDSA = ECDSA::NewECDSAByPbk($PbkPEM, $Algorithm, 256, $CipherParams['curve'], $CipherParams['pbkformat']);
        if (!$ECDSA->Verify($Content, $Signature)) throw new BlockInvalid(NewBlockResponse::YouAreNotTheOwner);


        //'ecdsa', $Pbk, $CipherParams['keysize'], $Algorithm, $NameValueLines, $Signature,
        return new Block($ECDSA, self::ContentToLines($Content), $CipherParams['id'] ?? null);
    }

    private static function ContentToLines($Content) {
        $_Lines = explode('\n', $Content);
        $Lines = Array();
        foreach ($_Lines as $Line) {
            $NameValue = explode(':', $Line, 2);
            if (trim($NameValue[0]) == "") continue;
            $Lines[$NameValue[0]] = count($NameValue) == 2 ? $NameValue[1] : null;
        }
        return $Lines;
    }

    public function ToResponse() {
        $Block = "§" . $this->GetCurrentBlockName();
        $Block .= $this->ProcessBlock();
        return $Block . $this->GetCurrentBlockName() . "§";
    }

    /*public function ProcessBlock($DeleteMe = null) {
        if (isset($this->Lines['cascade'])) {
            $Cascade = json_decode($DeleteMe ?? $this->Lines['cascade']);
            $Sql = "";
            foreach ($Cascade as $PacketOrNode) {

            }
        }
    }*/

    /**
     * @param GenericNode[] $Cascade
     */
    public function ProcessBlock($Cascade) {
        //$Cascade = json_decode($json);
        //$Packets = Array();
        //$Nodes = Array();
        $GenericNodes = Array();
        //$PacketNode = Array('packet' => &$Packets, 'node' => $Nodes);
        $Sql = "";
        $x = "";
        foreach ($Cascade as $Key => $PacketOrHead) {
            /*if (!isset($PacketOrNode['id'])) continue;
            if (!isset($PacketOrNode['type'])) continue;
            if (array_key_exists($Type, array('node', 'packet'))) continue;*/
            $Split = explode('.', $Key);
            $Type = $Split[0];
            $Id = $Split[1];

            $GenericNodes[$Key] = !$PacketOrHead->IsOwner() ? null : ($Type == CascadeType::Packet ? $this->ProcessPacket
            (new Packet($this->Asymmetric->GetPbkUnique(), $Id, $PacketOrHead)) : $this->ProcessHead(new Head
            ($this->Asymmetric->GetPbkUnique(), $Id, $PacketOrHead)));
        }
    }

    /**
     * Used in Cascade and Packe
     */
    public function GetUniqueID() {

    }

    /*public static function ProccessRecursive($PacketOrNode, int $Deep) {

    }*/

    public function ProcessPacket(Packet $Packet) {
        if ($Packet->Mode == PacketMode::Sync) {

        } elseif ($Packet->Mode == PacketMode::AddRemove) {

        }
        //if(isset($Packets[$Json['id']]))conti
    }

    public function ProcessHead(Head $Head) {

    }

    private function GetHeader() {
        return Hydra::S . $this->Asymmetric->ToString() . "-pbkformat:insidepem" . Hydra::S .
            $this->Asymmetric->PEMPbkOneLine();
    }

    private function GetFooter() {
        return $this->Asymmetric->SignBase64($this->GetContent());
    }

    public function GetContent() {
        $Content = "";
        foreach ($this->Lines as $Key => $Value) $Content .= "\n$Key:$Value";
        return trim($Content, "\n");
    }

    public function ToString() {
        return join("\n", Array($this->GetHeader(), $this->GetContent(), $this->GetFooter()));
    }
}
