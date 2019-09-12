<?php

// Use SHA256 hash algorithm
$alg = OPENSSL_ALGO_SHA256;
//$privateKeyString =
/*"-----BEGIN EC PARAMETERS-----
BgUrgQQACg==";
-----END EC PARAMETERS-----*/
/*"-----BEGIN EC PRIVATE KEY-----
MIGiAgEAMBMGByqGSM49AgEGCCqGSM49AwEHBHkwdwIBAQQgXs8a7vE7afeUc6UFyiL+p5sj/8ADhTP4eQCQ0qiRm9ygCgYIKoZIzj0DAQehRANCAASusCthQJLKzY/0ZsruKXsZ8Wky88sy5kBLwQJVPddmhNocCPWb8yJSo+SvuWzHcO4tiuQBeD4O/uim3B3qVjFZoA0wCwYDVR0PMQQDAgCA
-----END EC PRIVATE KEY-----";*/
// Both string and file link works the same
// $privateKey = openssl_get_privatekey("file://privateKey.pem");
//$privateKey = openssl_get_privatekey($privateKeyString);

//$message = "hola";
//echo "Message:\n" . $message . "\n<br>";


/*$signature = "ExC+nA6EwClq6XMHpYxgIWe0kYqtnLm0n0fw27Sqa8t75plPX1WnT/FFVDzW3RQJ0tRrJ2Y1mGDtTjjiM+3KMA==";
$signature = base64_decode($signature);

if (openssl_sign($message, $signature, $privateKey, $alg)) {
    $signature = base64_encode($signature); // Send the data in base64

    echo "Signature: " . $signature . "\n<br>";
} else {
    echo "Failed to sign message: " . openssl_error_string() . "\n";
    exit;
}*/
 //128 96


$message = "0";
$signature = "MEUCIQCbxTNhXouZl6TM8yEAOQWbgQTlQLo84tL8jD+rYrXQsAIgCYh/UgGUNxWyUNyat9sIH2To15p3GmQnhf0cOtoNp9g=";

$signature = base64_decode($signature);
//Original Value: MFYwEAYHKoZIzj0CAQYFK4EEAAoDQgAE91vCtp7tO4FyJbpgSS824PiuLR7LPNdwt+rcIe0uE19RUJz2Jgm8tRRDHmBVzoQXNxcwVD1HfRMtU0wnUJOuAQ==
$ContentKey = "MFYwEAYHKoZIzj0CAQYFK4EEAAoDQgAEo1yy5tI/XmCI+YSHRAhP6RtLLN7klsJD
JyTWpyBL4x29cXxfgi3Ta69Xfb90ni8b7VoVhx/S1k0vdW8lPNfrgw==";
$Cert ="-----BEGIN PUBLIC KEY-----
$ContentKey
-----END PUBLIC KEY-----";//88 Length
echo strlen(base64_decode($ContentKey));
//echo strlen(base64_decode("AAAAE2VjZHNhLXNoYTItbmlzdHAyNTYAAAAIbmlzdHAyNTYAAABBBHiHWISIJIOjpwiofjAtrcYw2QhzBvrt9OnKBjW/RdH+XG9igxGZK+vzj5Ssbsp1lvpsTTA7YdQMLGOMarHm90Y="));
// Both string and file link works the same
// $publicKey = openssl_get_publickey("file://publicKey.pem");
$publicKey = openssl_get_publickey($Cert);
//echo $Cert;
// Verify signature.
//phpinfo();exit;
print_r(openssl_get_md_methods());exit;
$success = openssl_verify($message, $signature, $publicKey, "ecdsa-with-SHA1");

if ($success === -1) {
    echo "openssl_verify() failed with error.  " . openssl_error_string() . "\n";
} elseif ($success === 1) {
    echo "Signature verification was successful!\n";
} else {
    echo "Signature verification failed.  Incorrect key or data has been tampered with\n";
}
?>
