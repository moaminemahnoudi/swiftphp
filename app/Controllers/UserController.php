<?php

namespace App\Controllers;

use App\Models\User;
use SwiftPHP\Core\Controller;
use SwiftPHP\Security\Security;

class UserController extends Controller
{
    public function index(): string
    {
        $users = User::all();
        return $this->view('users.index', ['users' => $users]);
    }

    public function show(string $id): string
    {
        $user = User::find((int)$id);
        
        if (!$user) {
            http_response_code(404);
            return $this->view('errors.404');
        }
        
        return $this->view('users.show', ['user' => $user]);
    }

    public function create(): string
    {
        return $this->view('users.create');
    }

    public function store(): string
    {
        $data = $_POST;
        
        // Validate CSRF
        if (!Security::verifyCsrfToken($data['_token'] ?? '')) {
            return $this->view('users.create', ['errors' => ['Invalid CSRF token'], 'old' => $data]);
        }
        
        // Validate input
        $errors = Security::validate($data, [
            'name' => 'required|min:2|max:50',
            'email' => 'required|email',
            'password' => 'required|min:8'
        ]);
        
        if ($data['password'] !== $data['password_confirmation']) {
            $errors['password_confirmation'] = 'Passwords do not match';
        }
        
        if (!empty($errors)) {
            return $this->view('users.create', ['errors' => $errors, 'old' => $data]);
        }
        
        // Create user
        $user = new User([
            'name' => Security::sanitize($data['name']),
            'email' => Security::sanitize($data['email']),
            'password' => Security::hashPassword($data['password'])
        ]);
        
        if ($user->save()) {
            $this->redirect('/users');
        }
        
        return $this->view('users.create', ['errors' => ['Failed to create user'], 'old' => $data]);
    }
}