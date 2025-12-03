<?php

// database/migrations/2024_01_01_000001_add_auth_fields_to_users_table.php

class AddAuthFieldsToUsersTable
{
    public function up(): void
    {
        $db = \SwiftPHP\Database\Database::getInstance();

        // Add role, tenant_id, and permissions columns
        $db->query("
            ALTER TABLE users 
            ADD COLUMN role VARCHAR(50) DEFAULT 'user',
            ADD COLUMN tenant_id INTEGER NULL,
            ADD COLUMN permissions TEXT NULL,
            ADD COLUMN created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ")->get();
    }

    public function down(): void
    {
        $db = \SwiftPHP\Database\Database::getInstance();

        $db->query("
            ALTER TABLE users 
            DROP COLUMN role,
            DROP COLUMN tenant_id,
            DROP COLUMN permissions,
            DROP COLUMN created_at,
            DROP COLUMN updated_at
        ")->get();
    }
}
