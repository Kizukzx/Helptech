<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_strict_mode', '1');
    session_start();
}
$tempoLimite = 1800; // 30 minutos sem atividade
if (isset($_SESSION['ultima_atividade']) && time() - $_SESSION['ultima_atividade'] > $tempoLimite) {
    $_SESSION = [];
    session_destroy();
    header('Location: ' . (str_contains($_SERVER['PHP_SELF'], '/cliente/') || str_contains($_SERVER['PHP_SELF'], '/cliente/') || str_contains($_SERVER['PHP_SELF'], '/clientes/') || str_contains($_SERVER['PHP_SELF'], '/equipamentos/') || str_contains($_SERVER['PHP_SELF'], '/ordens/')) ? '../login.php?expirada=1' : 'login.php?expirada=1');
    exit;
}
$_SESSION['ultima_atividade'] = time();
function exigirLogin(): void {
    if (empty($_SESSION['usuario_id'])) {
        $raiz = (str_contains($_SERVER['PHP_SELF'], '/cliente/') || str_contains($_SERVER['PHP_SELF'], '/cliente/') || str_contains($_SERVER['PHP_SELF'], '/clientes/') || str_contains($_SERVER['PHP_SELF'], '/equipamentos/') || str_contains($_SERVER['PHP_SELF'], '/ordens/')) ? '../login.php' : 'login.php';
        header('Location: ' . $raiz); exit;
    }
}
function h($valor): string { return htmlspecialchars((string)($valor ?? ''), ENT_QUOTES, 'UTF-8'); }
function urlBase(string $path=''): string {
    $sub = (str_contains($_SERVER['PHP_SELF'], '/cliente/') || str_contains($_SERVER['PHP_SELF'], '/clientes/') || str_contains($_SERVER['PHP_SELF'], '/equipamentos/') || str_contains($_SERVER['PHP_SELF'], '/ordens/')) ? '../' : '';
    return $sub . $path;
}
function cpfCriptografar(string $cpf): string {
    $key = hash('sha256', APP_ENCRYPTION_KEY, true); $iv = random_bytes(16);
    $encrypted = openssl_encrypt($cpf, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);
    return base64_encode($iv . $encrypted);
}
function cpfDescriptografar(?string $cpf): string {
    if (!$cpf) return '';
    $raw = base64_decode($cpf, true); if ($raw === false || strlen($raw) < 17) return $cpf;
    $key = hash('sha256', APP_ENCRYPTION_KEY, true); $value = openssl_decrypt(substr($raw,16), 'AES-256-CBC', $key, OPENSSL_RAW_DATA, substr($raw,0,16));
    return $value === false ? $cpf : $value;
}

function exigirPerfil(array $perfis): void {
    exigirLogin();
    $perfil = mb_strtolower(trim((string)($_SESSION['usuario_perfil'] ?? '')));
    $permitidos = array_map(fn($p) => mb_strtolower(trim($p)), $perfis);
    if (!in_array($perfil, $permitidos, true)) {
        $destino = $perfil === 'cliente' ? 'cliente/index.php' : 'dashboard.php';
        header('Location: ' . urlBase($destino));
        exit;
    }
}
function ehEquipe(): bool {
    return in_array(mb_strtolower(trim((string)($_SESSION['usuario_perfil'] ?? ''))), ['administrador','admin','técnico','tecnico','trabalhador'], true);
}
