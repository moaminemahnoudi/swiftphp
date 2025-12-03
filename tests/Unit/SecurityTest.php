<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use SwiftPHP\Security\Security;

class SecurityTest extends TestCase
{
    public function test_csrf_token_generation()
    {
        // Mock session
        if (session_status() === PHP_SESSION_NONE) {
            // session_start() might fail in CLI if headers sent, but let's try or mock $_SESSION
            $_SESSION = [];
        }

        $token = Security::generateCsrfToken();

        $this->assertNotEmpty($token);
        $this->assertEquals($token, $_SESSION['csrf_token']);
        $this->assertTrue(Security::verifyCsrfToken($token));
    }

    public function test_sanitize()
    {
        $input = '<script>alert("xss")</script>';
        $sanitized = Security::sanitize($input);

        $this->assertEquals('&lt;script&gt;alert(&quot;xss&quot;)&lt;/script&gt;', $sanitized);
    }

    public function test_validation()
    {
        $data = ['email' => 'invalid-email', 'age' => '1'];
        $rules = [
            'email' => 'required|email',
            'age' => 'min:2' // min length 2 chars
        ];

        $errors = Security::validate($data, $rules);

        $this->assertArrayHasKey('email', $errors);
        $this->assertArrayHasKey('age', $errors); // '10' is length 2, wait. min:2 usually means value >= 2 or length?
        // Looking at Security.php: preg_match('/min:(\d+)/', $rule, $matches) && strlen($value) < $matches[1]
        // So it checks string length. '10' has length 2. So it should pass if min:2.
        // Let's check logic: if strlen < min, error. 2 < 2 is false. So it passes.

        // Let's test failure
        $data2 = ['name' => 'A'];
        $rules2 = ['name' => 'min:2'];
        $errors2 = Security::validate($data2, $rules2);
        $this->assertArrayHasKey('name', $errors2);
    }
}
