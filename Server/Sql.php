<?php
include_once 'Cfg.php';

class Sql {
    /**
     * @var Mysqli
     */
    private static $Mysqli;

    public static function Connect() {
        if (self::$Mysqli == null) self::$Mysqli = new mysqli(SQL_HOST, SQL_USER, SQL_PASS, SQL_SCHEMA, SQL_PORT);
        return self::$Mysqli;
    }

    public static function Query($Sql) {
        self::Connect();
        return self::$Mysqli->query($Sql);
    }

    public static function AffectedRows() {
        return mysqli_affected_rows(self::$Mysqli);
    }

    public static function LastInsertID() {
        return self::$Mysqli->insert_id;
    }

    public static function AutoCommit(bool $AutoCommit) {
        self::$Mysqli->autocommit($AutoCommit);
    }

    public static function Commit() {
        self::$Mysqli->autocommit(true);
        return self::$Mysqli->commit();
    }

    public static function Close() {
        self::$Mysqli->close();
        self::$Mysqli = null;
    }

    public static function Escape($String) {
        return ($String == null || $String == '') ? '' : self::Connect()->real_escape_string($String);
    }

    /**
     * Alias of SQL::Escape
     * @param $String
     */
    public static function Esc($String) {
        self::Escape($String);
    }

}