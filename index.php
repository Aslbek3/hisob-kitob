<?php
// Database klassini ulaymiz
require_once 'src/Database.php';

// Bot tokeningizni shu yerga yozing
$botToken = "SIZNING_BOT_TOKENINGIZ"; 
$apiUrl = "https://api.telegram.org/bot$botToken/";

// Telegramdan kelgan ma'lumotni olish
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update) {
    exit; // Agar xabar bo'lmasa, kod to'xtaydi
}

$message = $update['message'];
$chatId = $message['chat']['id'];
$text = $message['text'];
$firstName = $message['from']['first_name'];

$db = new Database();

// Bot buyruqlarini tekshirish
if ($text == '/start') {
    $javob = "Salom, $firstName! \nMen xarajatlaringizni hisoblayman. \n\nSarfni yozing (masalan: 25000) yoki jami xarajatni ko'rish uchun /jami buyrug'ini bosing.";
    sendMessage($chatId, $javob, $apiUrl);
} 
// Agar foydalanuvchi son (xarajat miqdori) yuborsa
elseif (is_numeric($text)) {
    $miqdor = (float)$text;
    $db->addExpense($chatId, $miqdor); // Bazaga user_id bilan saqlash
    
    $javob = "✅ $miqdor so'm saqlandi. \nKategoriyani keyinchalik AI orqali aniqlaymiz.";
    sendMessage($chatId, $javob, $apiUrl);
} 
// Jami xarajatni ko'rsatish
elseif ($text == '/jami') {
    $total = $db->getTotalAmount($chatId);
    $javob = "💰 Sizning jami xarajatlaringiz: " . number_format($total, 0, ',', ' ') . " so'm.";
    sendMessage($chatId, $javob, $apiUrl);
} 
else {
    $javob = "Iltimos, xarajat miqdorini son bilan kiriting (masalan: 10000).";
    sendMessage($chatId, $javob, $apiUrl);
}

// Telegramga xabar yuborish funksiyasi
function sendMessage($chatId, $text, $apiUrl) {
    $url = $apiUrl . "sendMessage?chat_id=$chatId&text=" . urlencode($text);
    file_get_contents($url);
}
