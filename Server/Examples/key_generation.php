<?php

require __DIR__ . "/../vendor/autoload.php";

use Mdanter\Ecc\EccFactory;
use Mdanter\Ecc\Serializer\PrivateKey\PemPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PrivateKey\DerPrivateKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\DerPublicKeySerializer;
use Mdanter\Ecc\Serializer\PublicKey\PemPublicKeySerializer;
//use Mdanter\Ecc\Tests\Serializer\PublicKey\DerPublicKeySerializerTest;

$adapter = EccFactory::getAdapter();
$generator = EccFactory::getSecgCurves()->generator256r1();
$private = $generator->createPrivateKey();

$derSerializer = new DerPrivateKeySerializer($adapter);
$der = $derSerializer->serialize($private);
echo sprintf("DER encoding:\n%s\n\n", base64_encode($der));

$pemSerializer = new PemPrivateKeySerializer($derSerializer);
$pem = $pemSerializer->serialize($private);
echo sprintf("PEM encoding:\n%s\n\n", $pem);


//Public
$derSerializer = new DerPublicKeySerializer($adapter);
$pemSerializer = new PemPublicKeySerializer($derSerializer);
$pem = $pemSerializer->serialize($private->getPublicKey());
echo sprintf("\n\nPEM encoding:\n%s\n\n", $pem);
