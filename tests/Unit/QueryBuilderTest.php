<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use SwiftPHP\Database\QueryBuilder;

class QueryBuilderTest extends TestCase
{
    private \PDO $pdo;

    protected function setUp(): void
    {
        $this->pdo = new \PDO('sqlite::memory:');
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        $this->pdo->exec("CREATE TABLE users (id INTEGER PRIMARY KEY, name TEXT, email TEXT)");
    }

    public function test_select_query_generation()
    {
        $qb = new QueryBuilder($this->pdo, '', [], 'users');
        $qb->select(['name', 'email']);

        // We can't easily test the private SQL property without reflection or a getter,
        // but we can test the execution result if we insert data first.

        $this->pdo->exec("INSERT INTO users (name, email) VALUES ('John', 'john@example.com')");

        $result = $qb->get();

        $this->assertCount(1, $result);
        $this->assertEquals('John', $result[0]['name']);
    }

    public function test_where_clause()
    {
        $this->pdo->exec("INSERT INTO users (name, email) VALUES ('John', 'john@example.com')");
        $this->pdo->exec("INSERT INTO users (name, email) VALUES ('Jane', 'jane@example.com')");

        $qb = new QueryBuilder($this->pdo, '', [], 'users');
        $result = $qb->select()->where('name', '=', 'Jane')->get();

        $this->assertCount(1, $result);
        $this->assertEquals('Jane', $result[0]['name']);
    }

    public function test_insert()
    {
        $qb = new QueryBuilder($this->pdo, '', [], 'users');
        $qb->insert(['name' => 'Bob', 'email' => 'bob@example.com']);

        $stmt = $this->pdo->query("SELECT * FROM users WHERE name = 'Bob'");
        $result = $stmt->fetch(\PDO::FETCH_ASSOC);

        $this->assertEquals('bob@example.com', $result['email']);
    }
}
