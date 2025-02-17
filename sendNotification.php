<?php
include(__DIR__ . '/main.php');

$php_input = file_get_contents('php://input');
$update = json_decode($php_input, TRUE);


if (!isset($update['message']) || strlen($update['message']) < 3) {
    exit();
}

j_print(
    [
        "success" => true,
        "message" => "Notification sent"
    ]
);

$update['message'] = str_replace("{data_ip_addr}", get_user_ip(), $update['message']);
$update['message'] = str_replace("[KZ_IP_DATA]", $_SERVER['HTTP_CF_IPCOUNTRY'], $update['message']);

sendMessageTelegramChannel($update['message'], BOT_CHAT_ID_NOTIFY, []);
