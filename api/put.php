<?php
/**
 * Created by PhpStorm.
 * User: toinebakkeren
 * Date: 06-06-16
 * Time: 14:39
 */

header('Content-Type: application/json');

$result = array();

$username = $_POST['username'];
$text = $_POST['text'];
$password = $_POST['password'];
$result['debug'] = $text;

$key = pack('H*', hash('sha256', $password));

$encrypted = encrypt($key, $text);

$result["dataSave"] = savedata($username, $encrypted);
$result["dataSaved"] = $encrypted;

echo json_encode($result);

function encrypt ($key, $payload) {
    $iv = mcrypt_create_iv(IV_SIZE, MCRYPT_DEV_URANDOM);
    $crypt = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $payload, MCRYPT_MODE_CBC, $iv);
    $combo = $iv . $crypt;
    $garble = base64_encode($iv . $crypt);
    return $garble;
}

function savedata($user, $data) {
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

    $sql = "INSERT INTO data (name, message)
VALUES ($user , $data)";

    if ($conn->query($sql) === TRUE) {
        $conn->close();
        return "New record created successfully";
    } else {
        $conn->close();
        return "Error: " . $sql . "<br>" . $conn->error;
    }
}

function decrypt ($key, $garble) {
    $combo = base64_decode($garble);
    $iv = substr($combo, 0, IV_SIZE);
    $crypt = substr($combo, IV_SIZE, strlen($combo));
    $payload = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $key, $crypt, MCRYPT_MODE_CBC, $iv);
    return $payload;
}
