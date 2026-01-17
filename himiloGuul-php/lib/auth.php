<?php
require_once __DIR__ . '/../connection.php';

function is_logged_in(){
    return !empty($_SESSION['user']);
}

function require_login(){
    if (!is_logged_in()){
        header('Location: /projects/himiloGuul-php/login.php');
        exit;
    }
}

function current_user(){
    return $_SESSION['user'] ?? null;
}

/**
 * Remember-me token helper
 * Cookie format: himilog_remember = selector:validator (validator is base64)
 * selector stored plaintext in DB, validator hashed (password_hash) in DB
 */
function create_remember_token($user_id){
    global $pdo;
    $selector = bin2hex(random_bytes(8));
    $validator = bin2hex(random_bytes(32));
    $token_hash = password_hash($validator, PASSWORD_DEFAULT);
    $expires = (new DateTime('+30 days'))->format('Y-m-d H:i:s');
    $ins = $pdo->prepare('INSERT INTO auth_tokens (user_id,selector,token_hash,expires_at) VALUES (:u,:s,:t,:e)');
    $ins->execute([':u'=>$user_id,':s'=>$selector,':t'=>$token_hash,':e'=>$expires]);
    $cookie = $selector . ':' . $validator;
    setcookie('himilog_remember', $cookie, time() + 60*60*24*30, '/', '', false, true);
    return true;
}

function clear_remember_tokens_for_user($user_id){
    global $pdo;
    $del = $pdo->prepare('DELETE FROM auth_tokens WHERE user_id = :u');
    $del->execute([':u'=>$user_id]);
}

function login_with_remember(){
    if (is_logged_in()) return false;
    if (empty($_COOKIE['himilog_remember'])) return false;
    global $pdo;
    $parts = explode(':', $_COOKIE['himilog_remember']);
    if (count($parts) !== 2) return false;
    list($selector, $validator) = $parts;
    $stmt = $pdo->prepare('SELECT * FROM auth_tokens WHERE selector = :s LIMIT 1');
    $stmt->execute([':s'=>$selector]);
    $row = $stmt->fetch();
    if (!$row) return false;
    if (new DateTime($row['expires_at']) < new DateTime()){
        // expired
        $del = $pdo->prepare('DELETE FROM auth_tokens WHERE id = :id'); $del->execute([':id'=>$row['id']]);
        return false;
    }
    if (password_verify($validator, $row['token_hash'])){
        // valid, fetch user and set session
        $u = $pdo->prepare('SELECT id,username,role FROM users WHERE id = :id LIMIT 1');
        $u->execute([':id'=>$row['user_id']]);
        $user = $u->fetch();
        if ($user){
            session_regenerate_id(true);
            $_SESSION['user'] = ['id'=>$user['id'],'username'=>$user['username'],'role'=>$user['role']];
            return true;
        }
    }
    return false;
}

function logout_user(){
    if (!empty($_SESSION['user']['id'])){
        clear_remember_tokens_for_user($_SESSION['user']['id']);
    }
    setcookie('himilog_remember', '', time()-3600, '/', '', false, true);
    session_unset();
    session_destroy();
}

function require_admin(){
    if (empty($_SESSION['user']) || ($_SESSION['user']['role'] ?? '') !== 'Admin'){
        // simple access denied
        header('HTTP/1.1 403 Forbidden');
        echo '<h3>Access denied</h3><p>You must be an admin to access this page.</p>';
        exit;
    }
}

?>