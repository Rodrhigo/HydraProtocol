<?php
class CryptoException extends Exception {
    public function __construct($CryptoExceptionCode, $Message) {
        parent::__construct($Message, $CryptoExceptionCode, null);
    }
}