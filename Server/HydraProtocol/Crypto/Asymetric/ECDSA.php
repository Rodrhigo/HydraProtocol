<?php
require "vendor/autoload.php";

use Mdanter\Ecc\Crypto\Signature\SignHasher;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Serializer\PublicKey\PemPublicKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;

include_once 'AsymetricKey.php';

class ECDSA extends AsymetricKey {
    /*
     * Curve
     */
    private $Generator;
    private $Adapter;
    const Secpk1PbkPEM64Start = "MFYwEAYHKoZIzj0CAQYFK4EEAAoDQgAE";
    const Secpr1PbkPEM64Start = "MFkwEwYHKoZIzj0CAQYIKoZIzj0DAQcDQgAE";

    private function ECDSA($Pvk, $PbkPEM, $DefaultSignatureHashAlgorithm = 'sha256', $KeySize = 256, $Curve = 'secp256k1') {
        $KeySize = (int)$KeySize;
        if ($KeySize != 256/*&& 384 521*/) throw new CryptoException(CryptoException::InvalidKeySize);

        $this->Adapter = EccFactory::getAdapter();
        $derSerializer = new DerPublicKeySerializer($this->Adapter);
        $pemSerializer = new PemPublicKeySerializer($derSerializer);

        if ($Curve == 'secp256k1'/* && $Curve!=secp256r1*/) $this->Generator = EccFactory::getSecgCurves()->generator256k1(); elseif ($Curve == 'secp256r1'/* && $Curve!=secp256r1*/) $this->Generator = EccFactory::getSecgCurves()->generator256r1();
        else throw new CryptoException(CryptoException::InvalidCurve);//throw new BlockInvalid("Invalid ECDSA Curve");

        if ($PbkPEM === null) {
            if ($Pvk == null) {
                $this->Pvk = $this->Generator->createPrivateKey();
                $this->Pbk = $this->Pvk->getPublicKey();
            } else {
                //Decode Pvk
            }
        } else {
            //"-----BEGIN PUBLIC KEY-----\r\n" . self::StartPbkPEM64 . base64_encode($PbkUnique) . "\r\n-----END PUBLIC KEY-----";
            $this->Pbk = $pemSerializer->parse($PbkPEM);
        }
        parent::__construct("ecdsa", $KeySize, "sha256", Array('curve' => $Curve));
    }

    public static function NewECDSAByPbk($PbkPEM, $DefaultSignatureHashAlgorithm, $KeySize = 256, $Curve = 'secp256k1') {
        return new ECDSA(null, $PbkPEM, $DefaultSignatureHashAlgorithm, $KeySize);
    }

    public static function NewECDSAByPvk($Pvk) {
        if ($Pvk == null) throw new CryptoException(CryptoException::PvkNull);
        try {
            if (false /*Pvk Decode() ==false <-- Can be Throw Exception*/) throw Exception();//Check or Throw Exception InvalidPvk
        } catch (Exception $Ex) {
            throw new CryptoException(CryptoException::InvalidPvk);
        }
    }

    public static function NewECDSA() {
        return new ECDSA(null, null);
    }

    public function Verify($BlockContent, $Signature, $SignatureAlgorithm = null) {
        $sigSerializer = new DerSignatureSerializer();
        $sig = $sigSerializer->parse($Signature);
        //$keyData = "-----BEGIN PUBLIC KEY-----\n$this->PbkPEM\n-----END PUBLIC KEY-----";


        $hasher = new SignHasher($SignatureAlgorithm ?? $this->SignatureHashAlgorithm ?? "sha256");
        $hash = $hasher->makeHash($BlockContent, $this->Generator);

        $signer = new Signer($this->Adapter);
        return $signer->verify($this->Pbk, $sig, $hash);
    }

    protected function _GetPbkPEM() {
        $derSerializer = new DerPublicKeySerializer($this->Adapter);
        $pemSerializer = new PemPublicKeySerializer($derSerializer);
        return $pemSerializer->serialize($this->Pbk);
    }

    protected function _GetPbkUnique() {
        $PbkUnique = str_replace('\n-----END PUBLIC KEY-----', '', str_replace('-----BEGIN PUBLIC KEY-----\n', '', $this->GetPbkPEM()));
        $PbkUnique = str_replace('\n', '', $PbkUnique);
        return base64_decode(substr($PbkUnique, 24));
    }

    public function Sign($Data) {
        $hasher = new SignHasher($this->SignatureHashAlgorithm, $this->Adapter);
        $hash = $hasher->makeHash($Data, $this->Generator);
        $random = \Mdanter\Ecc\Random\RandomGeneratorFactory::getHmacRandomGenerator($this->Pvk, $hash, $this->SignatureHashAlgorithm);
        $randomK = $random->generate($this->Generator->getOrder());

        $signer = new Signer($this->Adapter);
        $signature = $signer->sign($this->Pvk, $hash, $randomK);

        $serializer = new DerSignatureSerializer();
        $serializedSig = $serializer->serialize($signature);
        $Base64 = base64_encode($serializedSig);
        return $Base64;
    }
}