<?php
// database/migrations/2024_01_01_000002_create_tenants_table.php

class CreateTenantsTable
{
    public function up(): void
    {
        $db = \SwiftPHP\Database\Database::getInstance();
        
        $db->query("
            CREATE TABLE IF NOT EXISTS tenants (
                id INTEGER PRIMARY KEY AUTOINCREMENT,
                name VARCHAR(255) NOT NULL,
                domain VARCHAR(255) UNIQUE,
                settings TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            )
        ")->get();
    }

    public function down(): void
    {
        $db = \SwiftPHP\Database\Database::getInstance();
        $db->query("DROP TABLE IF EXISTS tenants")->get();
    }
}
