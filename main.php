<?php
session_start();
include(__DIR__ . '/config.php');

date_default_timezone_set('Europe/Moscow');

function j_print($array)
{
    return print(json_encode($array));
}

function qr($query)
{
    global $db;
    return $db->query($query);
}

function num($query)
{
    global $db;
    return $db->query($query)->num_rows;
}

function fet($query)
{
    global $db;
    return $db->query($query)->fetch_assoc();
}

function qr_fet($query)
{
    return $query->fetch_assoc();
}

function qr_num($query)
{
    return $query->num_rows;
}

function qr_res($query)
{
    return $query->fetch_object();
}

function escapeStr($str)
{
    global $db;
    return $db->real_escape_string($str);
}

function gen_str($length = 20)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function insert_gen_qr($table, $arr, $global_return_sql_query = false)
{
    global $db;
    $tbl_names = [];
    $tbl_values = [];
    foreach ($arr as $key => $value) {
        $tbl_names[] = "`$key`";
        if ($value == 'reset') {
            $tbl_values[] = 'NULL';
        } else {
            $tbl_values[] = "'" . escapeStr($value) . "'";
        }
    }
    $names = implode(',', $tbl_names);
    $values = implode(',', $tbl_values);
    qr("INSERT INTO `$table`($names) VALUES ($values)");
    if ($global_return_sql_query)
        return "INSERT INTO `$table`($names) VALUES ($values)";
    return (int) $db->insert_id;
}

function getUserByID($id)
{
    $q = qr("SELECT * FROM `users` WHERE `id` = '$id'");
    if ((int) qr_num($q) == 0)
        return false;
    return qr_res($q);
}

function qrArrayTable($qr)
{
    $arr = [];
    $q = qr($qr);
    while ($res = qr_res($q)) {
        $arr[] = (object) $res;
    }
    return $arr;
}

function getArrayTable($table, $options = false, $text = null)
{
    $arr = [];
    if ($options)
        $q = qr("SELECT * FROM `$table` WHERE " . $text);
    else
        $q = qr("SELECT * FROM `$table`");
    while ($res = qr_res($q)) {
        $arr[] = (object) $res;
    }
    return $arr;
}

function get_setting_by_id($id)
{
    $id = (int) $id;
    $q = qr("SELECT * FROM `settings` WHERE `id` = '$id'");
    if ((int) qr_num($q) == 0)
        return false;
    return qr_res($q);
}

function get_currency_by_id($id)
{
    $id = (int) $id;
    $q = qr("SELECT * FROM `currency` WHERE `id` = '$id'");
    if ((int) qr_num($q) == 0)
        return false;
    return qr_res($q);
}

function get_currency_by_code($code)
{
    $code = escapeStr($code);
    $q = qr("SELECT * FROM `currency` WHERE `code` = '$code'");
    if ((int) qr_num($q) == 0)
        return false;
    return qr_res($q);
}

function int($value)
{
    return (int) $value;
}

function get_setting_by_code($code)
{
    $code = escapeStr($code);
    $q = qr("SELECT * FROM `settings` WHERE `name` = '$code'");
    if ((int) qr_num($q) == 0)
        return false;
    return qr_res($q);
}

function get_order_by_hash($code)
{
    $code = escapeStr($code);
    $q = qr("SELECT * FROM `orders` WHERE `hash` = '$code'");
    if ((int) qr_num($q) == 0)
        return false;
    return qr_res($q);
}


function curl_get_request_with_user_agent($url)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

    $headers = [
        'user-agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/117.0.0.0 Safari/537.36',
    ];
    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

    $resp = curl_exec($curl);
    curl_close($curl);
    return $resp;
}

function get_user_ip()
{
    return $_SERVER['HTTP_X_FORWARDED_FOR'];
}

function get_user_agent()
{
    return $_SERVER['HTTP_USER_AGENT'];
}


function gen_text($array)
{
    $text = "";
    $firstItem = true;
    foreach ($array as $item) {
        if ($firstItem) {
            $text .= $item;
            $firstItem = false;
        } else {
            $text .= "\n" . $item;
        }
    }
    return $text;
}
function sendMessageTelegramChannel($text, $chat_id, $kb = [])
{
    $website = 'https://api.telegram.org/bot' . __TG_TOKEN;



    $reply_data = ['inline_keyboard' => $kb];

    if (!is_array($kb) || count($kb) == 0) {
        $kb = [];
    }

    $reply = json_encode($reply_data);

    $post_fields = [
        'chat_id' => $chat_id,
        'reply_markup' => $reply,
        'text' => $text,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true
    ];

    $url = $website . '/sendMessage';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type:multipart/form-data'
    ));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    $output = curl_exec($ch);
    return $output;
}


function int_odds($value)
{
    return (int) (!(int) $value);
}


function c_count($array = [])
{
    return gettype($array) != 'array' ? 0 : (int)count($array);
}

function find_str($full_str, $who_find)
{
    return (bool)(strpos($full_str, $who_find) !== false);
}
function telegram_init_username($username)
{
    return (strlen($username) < 3) ? '' : $username;
}

function deleteMessage($chat_remove_id = 0, $message_id = 0)
{
    $website = 'https://api.telegram.org/bot' . __TG_TOKEN;

    global $chat_id, $msg_id;

    $gl_chat_id = $chat_id;
    $gl_msg_id = $msg_id;

    if ((int) $chat_remove_id != 0) {
        $gl_chat_id = $chat_remove_id;
        $gl_msg_id = $message_id;
    }

    $post_fields = [
        'chat_id' => $gl_chat_id,
        'message_id' => $gl_msg_id,
    ];
    $url = $website . '/deleteMessage';

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type:multipart/form-data'
    ));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    $output = curl_exec($ch);

    return $output;
}

function inline_keyboard($chatId, $photo = false, $override = false, $disable_web_page_preview = false)
{
    global $kb, $txt, $message_thread_id, $send_video, $delete_keyboard_force;

    $thisKB = $override['kb'] ?? $kb;
    $thisTXT = $override['txt'] ?? $txt;

    if ($photo && strlen($photo) < 5)
        $photo = false;

    if (!is_array($kb) || count($kb) == 0) {
        $kb = [];
    }

    $website = 'https://api.telegram.org/bot' . __TG_TOKEN;

    $reply_data = ['inline_keyboard' => $thisKB];

    if (int($delete_keyboard_force) == 1)
        $reply_data['remove_keyboard'] = true;

    $reply = json_encode($reply_data);

    if ($send_video) {
        $post_fields = [
            'chat_id' => $chatId,
            'reply_markup' => $reply,
            'video' => $photo,
            'caption' => $thisTXT,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => $disable_web_page_preview
        ];
        $url = $website . '/sendVideo';
    } elseif ($photo) {
        $post_fields = [
            'chat_id' => $chatId,
            'reply_markup' => $reply,
            'photo' => $photo,
            'caption' => $thisTXT,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => $disable_web_page_preview
        ];
        $url = $website . '/sendPhoto';
    } else {
        $post_fields = [
            'chat_id' => $chatId,
            'reply_markup' => $reply,
            'text' => $thisTXT,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => $disable_web_page_preview
        ];
        $url = $website . '/sendMessage';
    }

    if ((int) $message_thread_id > 0) {
        $post_fields['message_thread_id'] = $message_thread_id;
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:multipart/form-data']);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);

    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

    $output = curl_exec($ch);
    curl_close($ch);

    return $output;
}
function edit_inline_keyboard($chat_id, $msg_id, $photo = false, $override = false, $disable_web_page_preview = false, $edit_caption = false)
{
    global $kb, $txt, $capt_ent;

    $thisKB = $override['kb'] ?? $kb;
    $thisTXT = $override['txt'] ?? $txt;

    $website = 'https://api.telegram.org/bot' . __TG_TOKEN;

    $reply_markup = json_encode([
        'inline_keyboard' => $thisKB
    ]);

    if ($photo) {
        $media_post = json_encode([
            'type' => 'photo',
            'media' => $photo,
            'caption' => $thisTXT,
            'parse_mode' => 'HTML'
        ]);
        $post_fields = [
            'chat_id' => $chat_id,
            'message_id' => $msg_id,
            'media' => $media_post,
            'reply_markup' => $reply_markup,
            'disable_web_page_preview' => $disable_web_page_preview
        ];
        $url = $website . '/editMessageMedia';
    } elseif ($edit_caption) {
        $post_fields = [
            'chat_id' => $chat_id,
            'message_id' => $msg_id,
            'caption' => $thisTXT,
            'reply_markup' => $reply_markup,
            'parse_mode' => 'HTML',
            'disable_web_page_preview' => $disable_web_page_preview
        ];
        if (is_array($capt_ent) && count($capt_ent) > 0) {
            $post_fields['caption_entities'] = json_encode($capt_ent);
        }
        $url = $website . '/editMessageCaption';
    } else {
        $post_fields = [
            'chat_id' => $chat_id,
            'message_id' => $msg_id,
            'text' => $thisTXT,
            'parse_mode' => 'HTML',
            'reply_markup' => $reply_markup,
            'disable_web_page_preview' => $disable_web_page_preview
        ];
        $url = $website . '/editMessageText';
    }

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type:multipart/form-data']);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    $output = curl_exec($ch);
    curl_close($ch);

    return $output;
}

function answerCallbackQuery($message, $chat_id = 0, $callback_query_id = 0, $show_alert = true)
{
    $website = 'https://api.telegram.org/bot' . __TG_TOKEN;
    $url = $website . '/answerCallbackQuery';

    if ((int) $chat_id == 0) {
        global $chat_id;
    }
    if ((int) $callback_query_id == 0) {
        global $callback_id;
        $callback_query_id = $callback_id;
    }
    $post_fields = array(
        'callback_query_id' => $callback_query_id,
        'text' => $message,
        'parse_mode' => 'HTML',
        'show_alert' => $show_alert,
        'chat_id' => $chat_id
    );

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type:multipart/form-data'
    ));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    $output = curl_exec($ch);
    return $output;
}

function bold($txt)
{
    return "<b>{$txt}</b>";
}

function msg($text, $photo = false)
{
    global $chat_id;
    $website = 'https://api.telegram.org/bot' . __TG_TOKEN;
    if ($photo) {
        $post_fields = [
            'chat_id' => $chat_id,
            'photo' => $photo,
            'caption' => $text,
            'parse_mode' => 'HTML'
        ];
        $url = $website . '/sendPhoto';
    } else {
        $post_fields = [
            'chat_id' => $chat_id,
            'text' => $text,
            'parse_mode' => 'HTML'
        ];
        $url = $website . '/sendMessage';
    }
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Content-Type:multipart/form-data'
    ));
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    $output = curl_exec($ch);
    return $output;
}





function telegram_sendDocument($txt, $chatId, $rawFileData = false, $fileName = "file.txt")
{
    $website = "https://api.telegram.org/bot" . __TG_TOKEN;

    $post_fields = [
        'chat_id' => $chatId,
        'caption' => $txt,
        'parse_mode' => 'HTML',
        'disable_web_page_preview' => true,
    ];

    if ($rawFileData) {
        $post_fields['document'] = curl_file_create(
            'data://application/octet-stream;base64,' . base64_encode($rawFileData),
            'application/octet-stream',
            $fileName
        );
    }

    $url = $website . "/sendDocument";

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HTTPHEADER, ["Content-Type:multipart/form-data"]);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    $output = curl_exec($ch);
    curl_close($ch);

    return $output;
}


class AnObj extends stdClass
{
    public function __call($method, $args)
    {
        if ($this instanceof self && isset($this->{$method}) && is_callable($this->{$method})) {
            return call_user_func_array($this->{$method}, $args);
        }
        return null;
    }

    public static function __callStatic($method, $args)
    {
        $instance = new self();
        if (isset($instance->{$method}) && is_callable($instance->{$method})) {
            return call_user_func_array([$instance, $method], $args);
        } else {
            throw new BadMethodCallException("Static method $method does not exist.");
        }
    }

    public static function fromStdClass(stdClass $stdObj)
    {
        $anObj = new self();
        foreach (get_object_vars($stdObj) as $key => $value) {
            $anObj->{$key} = $value;
        }
        return $anObj;
    }

    public static function fromStd($stdObj)
    {
        $anObj = new self();
        foreach ($stdObj as $key => $value) {
            $anObj->{$key} = $value;
        }
        return $anObj;
    }

    public static function createEmpty()
    {
        return new self();
    }
}

function trim_space($str)
{
    return ltrim(rtrim($str));
}

function get_user_manage_text($u)
{
    $txt = gen_text([
        bold("📂 Профиль пользователя"),
        "",
        "👤 Фамилия: " . bold($u->surname),
        "🏦 Баланс: " . bold($u->balance) . "₽",
        "🆔: <code>" . $u->id . "</code>",
        "",
        "Телеграмм: {$u->first_name} {$u->last_name} (@{$u->username})"
    ]);
    $kb = [
        [
            [
                'text' => 'Изменить баланс',
                'callback_data' => json_encode([
                    'adm' => 'u_ch_balance',
                    'i' => $u->id
                ])
            ],
            [
                'text' => 'Пополнить баланс',
                'callback_data' => json_encode([
                    'adm' => 'u_add_balance',
                    'i' => $u->id
                ])
            ],
        ],
        [
            [
                'text' => (int($u->ban) == 0 ? 'Заблокировать' : "Разблокировать"),
                'callback_data' => json_encode([
                    'adm' => 'u_sw_ban',
                    'i' => $u->id
                ])
            ],
        ],
    ];

    return [$txt, $kb];
}


function validateTime($time)
{
    $d = DateTime::createFromFormat('H:i', $time);
    return $d && $d->format('H:i') === $time;
}

