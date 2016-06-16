<?php
/**
 * Created by PhpStorm.
 * User: toinebakkeren
 * Date: 06-06-16
 * Time: 14:39
 */

header('Content-Type: application/json');

$sqlresult = array();

$username = $_POST['username'];
$text = $_POST['text'];
$password = $_POST['password'];
$sqlresult['debug'] = $text;

$key = pack('H*', "bcb04b7e103a0cd8b54763051cef08bc55abe029fdebae5e1d417e2ffb2a00a3");
$iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
$iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);

$encrypted = encrypt($key, $text);

$sqlresult["dataSave"] = savedata($username, $encrypted, $password);
$sqlresult["dataSaved"] = $encrypted;

echo json_encode($sqlresult);

function encrypt ($key, $payload) {
    global $key;
    global $iv;

    $encryptedMessage = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $payload, MCRYPT_MODE_CBC, $iv);
    $encryptedMessage  = $iv . $encryptedMessage;
    $message_base64 = base64_encode($encryptedMessage);

    return $message_base64;
}

function savedata($user, $data, $password) {
    $servername = "localhost";
    $dbname = "sec2";
    $dbuser = "root";
    $dbpw = "root";

    // Create connection
    $conn = new mysqli($servername, $dbuser, $dbpw, $dbname);

    // Check connection
    if ($conn->connect_error) {
        return("Connection failed: " . $conn->connect_error);
    }

    $sql = "INSERT INTO data (name, message, password)
VALUES ('$user' , '$data', '$password')";

    if ($conn->query($sql) === TRUE) {
        $conn->close();
        return "New record created successfully";
    } else {
        $conn->close();
        return "Error: " . $sql . $conn->error;
    }
}
