<?php require_once __DIR__.'/../config/auth.php'; exigirPerfil(['Administrador','Técnico','Trabalhador']); header('Location: ../atendimentos.php'); exit;
