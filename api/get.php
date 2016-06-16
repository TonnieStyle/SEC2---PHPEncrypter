<?php
/**
 * Created by PhpStorm.
 * User: toinebakkeren
 * Date: 06-06-16
 * Time: 14:40
 */

header('Content-Type: application/json');

$result = array();

$username = $_POST['username'];
$password = $_POST['password'];

$key = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

$encrypted = getData($username, $password);

if (!$result["dataReceived"]) {
    $decrypted = decrypt($encrypted);
    $result["dataReceived"] = $decrypted;
}

echo json_encode($result);

function getData($user, $password) {
    $servername = "localhost";
    $dbname = "sec2";
    $dbuser = "root";
    $dbpw = "root";
    global $result;

    // Create connection
    $conn = new mysqli($servername, $dbuser, $dbpw, $dbname);

    // Check connection
    if ($conn->connect_error) {
        $result["debug"] = "Connection failed: " . $conn->connect_error;
        return("Connection failed: " . $conn->connect_error);
    }

    $sql = "SELECT * FROM data WHERE name='$user' AND password='$password'";

    $result["sql"] = $sql;

    $sqlresult = $conn->query($sql);

    if ($sqlresult->num_rows > 0) {
        // output data of each row
        while($row = $sqlresult->fetch_assoc()) {
            return $row["message"];
        }
    }
    else {
        $result["dataReceived"] = "Er zijn geen rijen gevonden! Waarschijnlijk is je gebruikesnaam/wachtwoord fout!";
    }

    $conn->close();
}

function decrypt($message) {
    global $iv_size;
    global $key;

    $enc_message = $message;
    $enc_message_dec = base64_decode($enc_message);
    $iv_dec = substr($enc_message_dec, 0, $iv_size);

    $message_dec = substr($enc_message_dec, $iv_size);
    $message = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $message_dec, MCRYPT_MODE_CBC, $iv_dec);

    return $message;
}
