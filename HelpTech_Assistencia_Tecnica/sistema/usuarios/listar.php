<?php require_once __DIR__.'/config/auth.php'; exigirPerfil(['Administrador','Técnico','Trabalhador']); ?>
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../login.php");
    exit;
}
require_once "../config/conexao.php";

$resultado = $conn->query("SELECT id,nome,email,perfil FROM usuarios ORDER BY nome");

?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <title>Usuários</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <header class="topbar"><strong>HelpTech</strong>
        <div><a href="../dashboard.php">Dashboard</a> | <a href="../logout.php">Sair</a></div>
    </header>
    <main class="container">
        <h1>Usuários</h1>
        <div class="table-wrapper">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>E-mail</th>
                        <th>Perfil</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($usuario = $resultado->fetch_assoc()): ?>
                        <tr>
                            <td><?= $usuario["id"] ?></td>
                            <td><?= htmlspecialchars($usuario["nome"]) ?></td>
                            <td><?= htmlspecialchars($usuario["email"]) ?></td>
                            <td><?= htmlspecialchars($usuario["perfil"]) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </main>
</body>

</html>