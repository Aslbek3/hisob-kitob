<?php

class Config {
    // MongoDB ulanish kodi (Siz bergan URL)
    const MONGO_URL = 'mongodb+srv://Aslbekhisob:qaszqasz@cluster0.jwmhnil.mongodb.net/?retryWrites=true&w=majority';

    // Telegram Bot Token (@BotFather dan olingan)
    const BOT_TOKEN = '8012951804:AAFPwnaxqa81WwIgKwHrrnhIrMT-uwjQbs4';

    // Ma'lumotlar bazasi sozlamalari
    const DB_NAME = 'expens_db';
    const COLLECTION_NAME = 'user_expenses';

    // API manzili (avtomatik shakllanadi)
    public static function getApiUrl() {
        return "https://api.telegram.org/bot" . self::BOT_TOKEN . "/";
    }
}
