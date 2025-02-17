<?php
$callback = $update['callback_query'];
$chat_id = $callback['message']['chat']['id'];
$user_id = $callback['from']['id'];
$uid = (string) $user_id;
$username = $callback['from']['username'];
$first_name = $callback['from']['first_name'];
$last_name = $callback['from']['last_name'];
$cl_message = $callback['data'];
$callback_id = $callback['id'];
$json = json_decode($cl_message);
if (!$json)
    return print('json error');
$msg_id = $callback['message']['message_id'];
$is_callback_answer_with_photo = isset($callback['message']['photo']);
$callback_message = $callback['message']['text'];
$message_callback = $callback['message'];
$message_text = $message_callback['text'];
$message_caption = $message_callback['caption'];
$message_entities = $message_callback['entities'];

if (isset($json->t) && $json->t == 'get_confirm') {
    sleep(3);

    msg("❌ Verification failed, hCaptcha token expired.");

    msg(gen_text([
        '<pre><code class="language-@safeguard">🔰 Generating alternative verification method...</code></pre>'
    ]));
    usleep(1500000);
    $txt = gen_text([
        "Verify you're human with Safeguard Captcha",
        "Click 'Verify' and complete the captcha to gain entry."
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

    inline_keyboard($chat_id, BOT_MESSAGE_PHOTO_URL);
}