<?php
session_start();
if (!isset($_SESSION["usuario_id"])) {
    header("Location: ../login.php");
    exit;
}
require_once "../config/conexao.php";

$id = intval($_GET["id"] ?? 0);
$resultado = $conn->query("SELECT * FROM chamados WHERE id='$id'")->fetch_assoc();

if (!$resultado) die("Chamado não encontrado.");

if ($_SERVER["REQUEST_METHOD"] === 'POST') {
    $titulo = trim($_POST["titulo"] ?? '');
    $descricao = trim($_POST["descricao"] ?? '');
    $prioridade = $_POST["prioridade"] ?? 'Média';
    $status = $_POST["status"] ?? 'Aberto';

    $resultado = $conn->query("UPDATE chamados SET titulo='$titulo',descricao='$descricao',prioridade='$prioridade',status='$status' WHERE id=$id");
    if ($resultado) {
        header("Location: listar.php");
        exit;
    }
}
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <title>Editar chamado</title>
    <link rel="stylesheet" href="../css/style.css">
</head>

<body>
    <header class="topbar"><strong>HelpTech</strong><a href="../logout.php">Sair</a></header>
    <main class="container form-container">
        <h1>Editar chamado #<?= $resultado["id"] ?></h1>
        <form method="post">
            <label>Título</label>
            <input name="titulo" value="<?= htmlspecialchars($resultado["titulo"]) ?>" required>
            <label>Descrição</label>
            <textarea name="descricao" rows="6" required><?= htmlspecialchars($resultado["descricao"]) ?></textarea>
            <label>Prioridade</label>
            <select name="prioridade">
                <?php foreach (['Baixa', 'Média', 'Alta'] as $o): ?>
                    <option <?= $resultado["prioridade"] === $o ? 'selected' : '' ?>><?= $o ?></option>
                <?php endforeach; ?>
            </select>
            <label>Status</label>
            <select name="status">
                <?php foreach (['Aberto', 'Em atendimento', 'Resolvido'] as $o): ?>
                    <option <?= $resultado["status"] === $o ? 'selected' : '' ?>><?= $o ?></option>
                <?php endforeach; ?>
            </select>
            <button>Atualizar</button>
            <a class="button secondary" href="listar.php">Cancelar</a>
        </form>
    </main>
</body>

</html>