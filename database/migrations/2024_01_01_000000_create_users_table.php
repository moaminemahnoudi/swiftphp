<?php

class CreateUsersTable
{
    public function up(): void
    {
        $db = \SwiftPHP\Database\Database::getInstance();
        $db->query("
            CREATE TABLE IF NOT EXISTS users (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255) UNIQUE NOT NULL,
                password VARCHAR(255) NOT NULL,
                status VARCHAR(50) DEFAULT 'active',
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ")->get();
    }

    public function down(): void
    {
        $db = \SwiftPHP\Database\Database::getInstance();
        $db->query("DROP TABLE IF EXISTS users")->get();
    }
}
