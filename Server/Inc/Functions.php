<?php
include_once ROOT . "/Protocol/SocketServer.php";

function KeyToLower($Array, $Deep = 1/*, $NodeDotCodeMode = false*/) {
    $NewArray = $Deep == 1 ? Array() : $Array;
    if ($Deep != 1) $NewArray = array_change_key_case($NewArray, CASE_LOWER);
    foreach ($Array as $Key => $ChildArray) {
        if ($Deep == 1) {
            $Split = explode('.', $Key, 2);
            if (count($Split) != 2) continue;
            if (strtolower($Split[0]) == "head" || strtolower($Split[0]) == "packet") $Key = strtolower($Split[0]) . '.' . $Split[1];//PaCkEt.MyID => packet
            //.MyID
            else continue;
        }else $Key = strtolower($Key);
        if (is_array($ChildArray)) $NewArray[$Key] = KeyToLower($ChildArray, $Deep + 1);
    }
    return $NewArray;
}

function NewArray($ArrayOrValueOrNull) {
    if ($ArrayOrValueOrNull == null) return Array(); elseif (is_array($ArrayOrValueOrNull)) return $ArrayOrValueOrNull;
    else return Array($ArrayOrValueOrNull);
}

/** Evitamos abrir/ejecutar una conexión Sql para escapar los caracteres
 * @param $Url
 * @return string Escape Packet Or Head Url
 */
function EscapeCode($HeadOrPacketCode) {
    $Matches = null;
    preg_match('/' . Param::UrlValidCharacters . '/', $HeadOrPacketCode, $Matches);
    return $Matches;
}

function JustAZ($String, $ToLower = false, $Zero9 = false) {
    $Matches = null;
    preg_match('/[a-z' . ($Zero9 ? '0-9' : '') . ']/', $String, $Matches);
    return $Matches;
}

function SwitchServerStatus($ServerON) {
    if ($ServerON === true) {
        $Server = new SocketServer();//apc_store
        $Server->Listener();
    } elseif ($ServerON === false) {

    }
}

function ArrayFormat($Array, $Format = '%1$s', $JoinGlue = "", $EscapeRequired = false) {
    $Str = array();
    foreach ($Array as $Key => $Value) $Str[] = sprintf($Format, $EscapeRequired ? SQL::Escape($Value) : $Value, $Key);
    return join($JoinGlue, $Str);
}