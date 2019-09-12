<?php
//include_once 'Inc/Functions.php';
//include_once 'Protocol/HydraProtocol.php';

$x = "761232131231266666a";

exit;
$Hydra = new HydraProtocol();
/*AddNode: Agrega un Nodo, Sync: Sincroniza/setea el paquete con los nodos enviados, RemoveNode: Elimina un nodo*/
$Json = Array();
$Json['PaCket.#MyRefID-1'] = Array('Type' => 'Packet', "Name" => "Marvel Packet", 'Mode' => 'AddRemove', 'Add' => Array('StaticUrlID-1', 'StaticUrlID-2'), 'Remove' => Array('StaticIDHello', 'StaticIDWorld'));
$Json['nOde.#MyRefID-1'] = Array('Type' => 'Node', 'OriginalName' => 'Marvel vs Campcom.rar', 'UpName' => 'M4rv3l v$ C4mpc0m.rar');
$Json['packet.#MyRefID-1'] = Array('Type' => 'Aspx', 'OriginalName' => 'Marvel vs Campcom.rar', 'UpName' => 'M4rv3l v$ C4mpc0m.rar');
$Json[] = Array('Id' => 'SwQwEr', 'Type' => 'Packet', 'AddNode' => Array(0 => Array('Id' => '-1'), 'UpName' => 'M4rv3l v$ C4mpc0m.rar', 'OriginalName' => 'Marvel vs Campcom.rar'));
$Json[] = Array('Id' => 'SwQwEr', 'Type' => 'Packet', 'Sync' => Array(0 => Array('Id' => '-1'), 'UpName' => 'M4rv3l v$ C4mpc0m.rar', 'OriginalName' => 'Marvel vs Campcom.rar'));
$Json[] = Array('Id' => '-1', 'Type' => 'Packet', 'Sync' => Array(0 => Array('Id' => '-1'), 'UpName' => 'M4rv3l v$ C4mpc0m.rar', 'OriginalName' => 'Marvel vs Campcom.rar'));

$Json = KeyToLower($Json);
//print_r($Json);
$JsonEncode = json_encode($Json);
//Block::ProcessBlockStatic($Json);
/* @var $Blocks Block[] */

$Blocks = Array();
$ECDSA = ECDSA::NewECDSA();
$Blocks[] = Block::NewBlockByContent($ECDSA, "Cascade:$JsonEncode");
echo $Blocks[0]->ToString() . "\n<br><br>\n";
$ReceivedBlocks = "§Hydra
hi
Hydra§\n" . $Blocks[0]->ToString() . "\n§ecdsa-256-SHA256-Curve:Secp256k1§MFYwEAYHKoZIzj0CAQYFK4EEAAoDQgAEtJX7piU16frTvL73QkqZ3jo1ztENQ4ZmyufdKqdXILkSxcpgaBjwPjLeHQZDoKwX+wwAp9nBtC/zZzzRRVFbag==
Cascade: {Packets:[{PacketId:-1,Name:\"My Packet\",Heads:{HeadId:-1,OriginalName:Mi+Movie.mp4,UpName:M$1+M0V1E.mp4}}]}
MEQCIBUWo9S32DyGsgt9B6vjMtQjMnAB514cpD6PasqR7P5pAiABvlAgemWaxbBq0bCRStu7yrn7OuUwNwVxcx4/Nfw9fw==
§ECDSA-256-SHA256-Curve:Secp256k1§MFYwEAYHKoZIzj0CAQYFK4EEAAoDQgAE++Ynpv4rJFZASHkQs0ZyEC3pG7L5lI8K2dSDQBJdv75J6OII67myK0lAHzsFAWy4gBQn7BIsobEUPNwPAcjh4A==
Cascade: {Packets:[{PacketId:-1,Name:\"My Packet\",Heads:{HeadId:-1,OriginalName:Mi+Movie.mp4,UpName:M$1+M0V1E.mp4}}]}
MEUCIQD3L503aQNOYxj67NJjM+tiqBJdhSiB8oGpmh25qXoUhAIgLlXPLz186VT8QVnmj5MoDv5tpNTA1tbGgs7oCk+LBM4=
§RSA-256-SHA256-Curve:Secp256k1§MFYwEAYHKoZIzj0CAQYFK4EEAAoDQgAE++Ynpv4rJFZASHkQs0ZyEC3pG7L5lI8K2dSDQBJdv75J6OII67myK0lAHzsFAWy4gBQn7BIsobEUPNwPAcjh4A==
Cascade: {Packets:[{PacketId:-1,Name:\"My Packet\",Heads:{HeadId:-1,OriginalName:Mi+Movie.mp4,UpName:M$1+M0V1E.mp4}}]}
MEUCIQD3L503aQNOYxj67NJjM+tiqBJdhSiB8oGpmh25qXoUhAIgLlXPLz186VT8QVnmj5MoDv5tpNTA1tbGgs7oCk+LBM4=
§ECDSA-256-SHA256-Curve:Secp256k1§MFYwEAYHKoZIzj0CAQYFK4EEAAoDQgAE++Ynpv4rJFZASHkQs0ZyEC3pG7L5lI8K2dSDQBJdv75J6OII67myK0lAHzsFAWy4gBQn7BIsobEUPNwPAcjh4A==
MEUCIQD3L503aQNOYxj67NJjM+tiqBJdhSiB8oGpmh25qXoUhAIgLlXPLz186VT8QVnmj5MoDv5tpNTA1tbGgs7oCk+LBM4=";
//echo $ReceivedBlocks;
$Blocks = $Hydra->DecodeBlocks($ReceivedBlocks);

print_r($Blocks);


exit;
error_reporting(E_ALL ^ E_NOTICE);
//echo "Hello Worlddd";exit;
set_time_limit(60 * 3/**/);

/* Activar el volcado de salida implícito, así veremos lo que estamo obteniendo
 * mientras llega. */
ob_implicit_flush();

$address = '127.0.0.1';//;
$port = 16223;

if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "socket_create() falló: razón: " . socket_strerror(socket_last_error()) . "\n";
}

if (socket_bind($sock, $address, $port) === false) {
    echo "socket_bind() falló: razón: " . socket_strerror(socket_last_error($sock)) . "\n";
}

if (socket_listen($sock, 5) === false) {
    echo "socket_listen() falló: razón: " . socket_strerror(socket_last_error($sock)) . "\n";
}

//clients array
$clients = array();

do {
    $read = array();
    $read[] = $sock;

    $read = array_merge($read, $clients);

    // Set up a blocking call to socket_select
    if (socket_select($read, $write = null, $except = null, $tv_sec = 5) < 1) {
        //    SocketServer::debug("Problem blocking socket_select?");
        continue;
    }

    // Handle new Connections
    if (in_array($sock, $read)) {

        if (($msgsock = socket_accept($sock)) === false) {
            echo "socket_accept() falló: razón: " . socket_strerror(socket_last_error($sock)) . "\n";
            break;
        }
        $clients[] = $msgsock;
        $key = array_keys($clients, $msgsock);
        /* Enviar instrucciones. */
        $msg = "\nBienvenido al Servidor De Prueba de PHP. \n" . "Usted es el cliente numero: {$key[0]}\n" . "Para salir, escriba 'quit'. Para cerrar el servidor escriba 'shutdown'.\n";
        socket_write($msgsock, $msg, strlen($msg));
    }

    // Handle Input
    foreach ($clients as $key => $client) { // for each client        
        if (in_array($client, $read)) {
            if (false === ($buf = socket_read($client, 2048, PHP_NORMAL_READ))) {
                echo "socket_read() falló: razón: " . socket_strerror(socket_last_error($client)) . "\n";
                break 2;
            }
            if (!$buf = trim($buf)) {
                continue;
            }
            if ($buf == 'quit') {
                unset($clients[$key]);
                socket_close($client);
                break;
            }
            if ($buf == 'shutdown') {
                socket_close($client);
                break 2;
            }
            $talkback = "Cliente {$key}: Usted dijo '$buf'.\n";
            socket_write($client, $talkback, strlen($talkback));
            echo "$buf\n";
        }
    }
} while (true);

socket_close($sock);
?>