<?php
/*$pbk = "045ab40e87626cf7c1f28d5f562e163a9211875fb51bf10afba1d91f0e77d47795709b0c19cc4eaa538550a756c8833ccc6110cc31e9dfd3a4caf50151fd5cb6f9";
$pbk = implode(array_map("chr", array( 3, 66, 0, 4 ))).pack("H*", $pbk);
$pbk = base64_encode($pbk);
echo $pbk."<br>";//. strlen($pbk);
//exit;
$pbk = "A0IABARatA6HYmz3wfKNX1YuFjqSEYdftRvxCvuh2R8Od9R3lXCbDBnMTqpThVCn
VsiDPMxhEMwx6d/TpMr1AVH9XLb5";
$pbk ="-----BEGIN PUBLIC KEY-----\r\n$pbk\r\n-----END PUBLIC KEY-----";*/
echo strlen(base64_decode("MFYwEAYHKoZIzj0CAQYFK4EEAAoDQgAE91vCtp7tO4FyJbpgSS824PiuLR7LPNdwt+rcIe0uE19RUJz2Jgm8tRRDHmBVzoQXNxcwVD1HfRMtU0wnUJOuAQ=="));
$pbk = "-----BEGIN PUBLIC KEY-----
ecdsa-sha2-nistp256 AAAAE2VjZHNhLXNoYTItbmlzdHAyNTYAAAAIbmlzdHAyNTYAAABBBFxglMzX8iUgnwPqB25Sx1FMw6NBAOgb8rQyNjF6+quHL9a1MX064K5DkpSNogaiLdfuknUgCZ2Z63B5rLqLxmk= 
-----END PUBLIC KEY-----";
echo $pbk . "\n<br>";
$alg = OPENSSL_ALGO_SHA256;

$publicKey = openssl_get_publickey($pbk);
$signature = "SyLJWdWP6BXU28/22QLLxlN/ybvXIVLf+bsB2y8s3NB1580m2tqYFvozNdVuHsk18XHrpJZjOvPHq9le9sfMyA==";
$success = openssl_verify("hi", base64_decode($signature), $publicKey, $alg);
if ($success === -1) {
    echo "openssl_verify() failed with error.  " . openssl_error_string() . "\n";
} elseif ($success === 1) {
    echo "Signature verification was successful!\n<br>";
} else {
    echo "Signature verification failed.  Incorrect key or data has been tampered with\n";
}


exit;
$pubkey = '-----BEGIN PUBLIC KEY-----
MIGeMA0GCSqGSIb3DQEBAQUAA4GMADCBiAKBgNQGlY7gL9GIFzJ9gHHmPM1IDnVMxOPsyMxo8baW
uzxwobVIPBv7GSB/zci4pjX6YRAGHU0paN7TwlSMEevdmL6ptqyzqXxy7r8w9o8v5V7hM0lZV5ew
4laJMt+weWBIJidzt6pPL1hepIKhtdKOw6/IuJ6N+UWg1r1JD2OvBSQJAgMBAAE=
-----END PUBLIC KEY-----';
new Crypt_RSA();
$privkey = '...private key here...';
$data = "diSjQYOuEKtOtaEAdhqz1aYMoqXIMC57ZcE3jeSs69EqzQwH6hl57biNUMslRHTEKow1+OOoSHaA+vYgLYx/pMVT3Nc+Vu+SN6WVRO+2N8lz7xsW7iBr4jLPq1Ad05cWHL89b4gvs9x/1P/P/vCkJmBxA3YCcAFCSs4STJMPPQU=";
$decrypted = "";
echo openssl_public_decrypt(base64_decode($data), $decrypted, $pubkey) ? "true" : "false";
echo "<br>" . $decrypted;


exit;

function encrypt($data) {
    if (openssl_public_encrypt($data, $encrypted, $pubkey)) $data = base64_encode($encrypted); else
        throw new Exception('Unable to encrypt data. Perhaps it is bigger than the key size?');

    return $data;
}

function decrypt($data) {
    if (openssl_private_decrypt(base64_decode($data), $decrypted, $privkey)) $data = $decrypted; else
        $data = '';

    return $data;
}

openssl_private_decrypt(base64_decode($data), $decrypted, $privkey);

$Key = "-----BEGIN PUBLIC KEY-----
MIGeMA0GCSqGSIb3DQEBAQUAA4GMADCBiAKBgNQGlY7gL9GIFzJ9gHHmPM1IDnVMxOPsyMxo8baW
uzxwobVIPBv7GSB/zci4pjX6YRAGHU0paN7TwlSMEevdmL6ptqyzqXxy7r8w9o8v5V7hM0lZV5ew
4laJMt+weWBIJidzt6pPL1hepIKhtdKOw6/IuJ6N+UWg1r1JD2OvBSQJAgMBAAE=
-----END PUBLIC KEY-----";


exit;
