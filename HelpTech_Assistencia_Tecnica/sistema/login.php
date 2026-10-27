<?php
require_once __DIR__ . '/config/auth.php';
if (!empty($_SESSION['usuario_id'])) {
    $perfilAtual = mb_strtolower(trim((string)($_SESSION['usuario_perfil'] ?? '')));
    header('Location: ' . ($perfilAtual === 'cliente' ? 'cliente/index.php' : 'dashboard.php'));
    exit;
}
require_once __DIR__ . '/config/conexao.php';
$erro = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? ''); $senhaDigitada = $_POST['senha'] ?? '';
    if ($email === '' || $senhaDigitada === '') $erro = 'Informe seu e-mail e sua senha.';
    else {
        $stmt = $conn->prepare('SELECT id,nome,email,senha,perfil FROM usuarios WHERE email=? LIMIT 1');
        $stmt->bind_param('s', $email); $stmt->execute(); $u = $stmt->get_result()->fetch_assoc();
        // password_verify para hashes modernos; comparação legada para bancos que ainda guardam senha em texto.
        $valido = $u && (password_verify($senhaDigitada, $u['senha']) || hash_equals((string)$u['senha'], (string)$senhaDigitada));
        if ($valido) {
            session_regenerate_id(true); $_SESSION['usuario_id']=(int)$u['id']; $_SESSION['usuario_nome']=$u['nome']; $_SESSION['usuario_perfil']=$u['perfil'] ?? 'Usuário'; $_SESSION['ultima_atividade']=time();
            header('Location: ' . (mb_strtolower(trim((string)$_SESSION['usuario_perfil'])) === 'cliente' ? 'cliente/index.php' : 'dashboard.php')); exit;
        }
        $erro = 'E-mail ou senha inválidos. Confira os dados e tente novamente.';
    }
}
?>
<!doctype html><html lang="pt-BR"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title>Entrar | HelpTech</title><link rel="stylesheet" href="css/style.css"></head>
<body class="login-page"><main class="login-card"><div class="login-logo"><span class="brand-mark">H</span><div><h1>HelpTech</h1><span class="eyebrow">Assistência técnica</span></div></div><p>Acesse o painel para gerenciar clientes, equipamentos e ordens de serviço.</p>
<?php if (isset($_GET['expirada'])): ?><div class="alert error">Sua sessão expirou por inatividade. Entre novamente.</div><?php endif; ?>
<?php if ($erro): ?><div class="alert error"><?= h($erro) ?></div><?php endif; ?>
<form method="post" autocomplete="on"><label for="email">E-mail</label><input id="email" type="email" name="email" autocomplete="username" required autofocus value="<?= h($_POST['email'] ?? '') ?>"><label for="senha">Senha</label><input id="senha" type="password" name="senha" autocomplete="current-password" required><button class="button-login" type="submit">Entrar no sistema <span aria-hidden="true">→</span></button></form><div class="footer-note">Acesso restrito a usuários autorizados</div></main></body></html>
