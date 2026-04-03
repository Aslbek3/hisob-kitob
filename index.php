<?php
// Barcha kerakli fayllarni ulash
require_once 'vendor/autoload.php';
require_once 'src/Config.php';
require_once 'src/Database.php';
require_once 'src/ExcelExport.php';

// Telegramdan kelgan xabarni olish
$content = file_get_contents("php://input");
$update = json_decode($content, true);

if (!$update || !isset($update['message'])) exit;

$message = $update['message'];
$chatId = $message['chat']['id'];
$text = $message['text'] ?? '';
$apiUrl = Config::getApiUrl();

$db = new Database();

// --- LOGIKA BOSHLANDI ---

if ($text == '/start') {
    sendMessage($chatId, "Salom! Men xarajatlaringizni hisoblayman.\n\n- Son yuboring (masalan: 50000)\n- /jami - Jami hisob\n- /export - Excel hisobot", $apiUrl);
} 
// Agar foydalanuvchi son yuborsa (xarajat)
elseif (is_numeric($text)) {
    $db->addExpense($chatId, $text);
    sendMessage($chatId, "✅ $text so'm saqlandi.", $apiUrl);
} 
// Jami xarajatni ko'rish
elseif ($text == '/jami') {
    $total = $db->getTotalAmount($chatId);
    sendMessage($chatId, "💰 Jami xarajatlaringiz: " . number_format($total, 0, ',', ' ') . " so'm.", $apiUrl);
} 
// Excel fayl qilib yuklab olish
elseif ($text == '/export') {
    $data = $db->getUserData($chatId);
    if (empty($data)) {
        sendMessage($chatId, "Hozircha ma'lumot yo'q.", $apiUrl);
    } else {
        $excel = new ExcelExport();
        $fileName = $excel->generate($data, $chatId);
        sendDocument($chatId, $fileName, $apiUrl);
        unlink($fileName); // Faylni yuborgach, serverdan o'chirish
    }
}

// --- FUNKSIYALAR ---

function sendMessage($chatId, $text, $apiUrl) {
    file_get_contents($apiUrl . "sendMessage?chat_id=$chatId&text=" . urlencode($text));
}

function sendDocument($chatId, $file, $apiUrl) {
    $post_fields = [
        'chat_id' => $chatId,
        'document' => new CURLFile(realpath($file))
    ];
    $ch = curl_init($apiUrl . "sendDocument");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_fields);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_exec($ch);
    curl_close($ch);
}
