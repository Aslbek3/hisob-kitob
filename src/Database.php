<?php
require 'vendor/autoload.php';

class Database {
    private $collection;

    public function __construct() {
        // Siz bergan ulanish kodi (URL)
        $uri = "mongodb+srv://Aslbekhisob:qaszqasz@cluster0.jwmhnil.mongodb.net/?retryWrites=true&w=majority";
        
        try {
            $client = new MongoDB\Client($uri);
            // "expens_db" - baza nomi, "user_expenses" - jadval nomi
            $this->collection = $client->expens_db->user_expenses;
        } catch (Exception $e) {
            die("Bazaga ulanishda xatolik: " . $e->getMessage());
        }
    }

    /**
     * Yangi xarajat qo'shish
     * @param int $userId - Telegram foydalanuvchi IDsi
     * @param float $amount - Miqdori
     * @param string $category - Kategoriya
     */
    public function addExpense($userId, $amount, $category = "Boshqa") {
        return $this->collection->insertOne([
            'tg_user_id' => $userId,
            'amount'     => (float)$amount,
            'category'   => $category,
            'timestamp'  => new MongoDB\BSON\UTCDateTime()
        ]);
    }

    /**
     * Faqat bitta foydalanuvchining hamma xarajatlarini olish
     * @param int $userId
     */
    public function getUserData($userId) {
        $cursor = $this->collection->find(['tg_user_id' => $userId]);
        return $cursor->toArray();
    }

    /**
     * Foydalanuvchining jami xarajatini hisoblash
     * @param int $userId
     */
    public function getTotalAmount($userId) {
        $pipeline = [
            ['$match' => ['tg_user_id' => $userId]],
            ['$group' => ['_id' => null, 'total' => ['$sum' => '$amount']]]
        ];
        $result = $this->collection->aggregate($pipeline)->toArray();
        return $result[0]['total'] ?? 0;
    }
}
