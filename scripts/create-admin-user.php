<?php

declare(strict_types=1);

$dsn = 'mysql:host=127.0.0.1;port=3306;dbname=edvora_tech;charset=utf8mb4';
$email = 'admin@edvoratech.local';
$password = 'EdvoraAdmin!2026';

try {
    $pdo = new PDO($dsn, 'root', '', [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $sql = <<<SQL
        INSERT INTO users (name, email, email_verified_at, password, role, status, created_at, updated_at)
        VALUES (:name, :email, :email_verified_at, :password, :role, :status, NOW(), NOW())
        ON DUPLICATE KEY UPDATE
            name = VALUES(name),
            email_verified_at = VALUES(email_verified_at),
            password = VALUES(password),
            role = VALUES(role),
            status = VALUES(status),
            updated_at = NOW()
    SQL;

    $statement = $pdo->prepare($sql);
    $statement->execute([
        ':name' => 'Admin Panel',
        ':email' => $email,
        ':email_verified_at' => date('Y-m-d H:i:s'),
        ':password' => password_hash($password, PASSWORD_BCRYPT),
        ':role' => 'admin',
        ':status' => 'active',
    ]);

    $verification = $pdo->prepare('SELECT id, name, email, role, status FROM users WHERE email = :email LIMIT 1');
    $verification->execute([':email' => $email]);
    $user = $verification->fetch();

    if (!$user) {
        throw new RuntimeException('Admin user was not found after insert.');
    }

    echo json_encode([
        'created' => true,
        'id' => $user['id'],
        'name' => $user['name'],
        'email' => $user['email'],
        'role' => $user['role'],
        'status' => $user['status'],
        'password' => $password,
    ], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES), PHP_EOL;
} catch (Throwable $exception) {
    fwrite(STDERR, $exception->getMessage() . PHP_EOL);
    exit(1);
}
