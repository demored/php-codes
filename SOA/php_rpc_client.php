<?php



const RPC_EOL = "\r\n\r\n";

function request($host, $class, $method, $param, $version = '1.0', $ext = []) {
    $fp = stream_socket_client($host, $errno, $errstr);
    if (!$fp) {
        throw new Exception("stream_socket_client fail errno={$errno} errstr={$errstr}");
    }

    $req = [
        "jsonrpc" => '2.0',
        "method" => sprintf("%s::%s::%s", $version, $class, $method),
        'params' => $param,
        'id' => '',
        'ext' => $ext,
    ];
    $data = json_encode($req) . RPC_EOL;
    fwrite($fp, $data);


    $result = '';
    while (!feof($fp)) {
        $tmp = stream_socket_recvfrom($fp, 1024);

        if ($pos = strpos($tmp, RPC_EOL)) {
            $result .= substr($tmp, 0, $pos);
            break;
        } else {
            $result .= $tmp;
        }
    }

    fclose($fp);
    return json_decode($result, true);
}

$ret = request('tcp://10.168.1.124:18308', \App\Rpc\Lib\UserInterface::class, 'getList',  [1, 2], "1.0");

var_dump($ret);