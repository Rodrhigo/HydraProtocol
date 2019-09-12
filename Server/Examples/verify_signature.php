<?php

require __DIR__ . "/../vendor/autoload.php";

use Mdanter\Ecc\Crypto\Signature\SignHasher;
use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Crypto\Signature\Signer;
use Mdanter\Ecc\Serializer\PublicKey\PemPublicKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\Signature\DerSignatureSerializer;

# Same parameters as creating_signature.php

$adapter = EccFactory::getAdapter();
//$generator = EccFactory::getNistCurves()->generator384();
$algorithm = 'sha256';
/*$sigData = base64_decode('MEQCIHK+HXgq0AjeKfmdI9l4uGBL0keIiZiQOCEyij25B/X/AiAQs++18Vhb0Q9tqWjzWUNTAMLEzUKF0XzKyHQ028/q4Q==');
$document = 'I am writing today...';*/
$sigData = base64_decode("MEQCICWZWsW7FNTlQzkU8QPxx3L0aGgjNTdnb9OWaJpx0b3wAiB4QL0Qys+qLBNNIF88sIL0HUDs24w0UC3ueGchLUaEMw==");
$document = "0";
//$generator = EccFactory::getNistCurves()->generator256();
$generator = EccFactory::getSecgCurves()->generator256k1();

// Parse signature
$sigSerializer = new DerSignatureSerializer();
$sig = $sigSerializer->parse($sigData);

// Parse public key
//$keyData = file_get_contents(__DIR__ . '/../tests/data/openssl-secp256r1.pub.pem');
$keyData = "-----BEGIN PUBLIC KEY-----
MFYwEAYHKoZIzj0CAQYFK4EEAAoDQgAETx0i0hgeOIwIa5PUObkSOEBXjT7qYfr6
CQ/trd9CsoJ+Rt/ODfpHqcOMdjBtdmqg3aNfWtX2M3hwQj8VKpf50g==
-----END PUBLIC KEY-----";
$derSerializer = new DerPublicKeySerializer($adapter);
$pemSerializer = new PemPublicKeySerializer($derSerializer);
$key = $pemSerializer->parse($keyData);

$hasher = new SignHasher($algorithm);
$hash = $hasher->makeHash($document, $generator);

$signer = new Signer($adapter);
$check = $signer->verify($key, $sig, $hash);

if ($check) {
    echo "Signature verified\n";
} else {
    echo "Signature validation failed\n";
}
