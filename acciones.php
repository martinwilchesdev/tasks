<?php

require_once 'config.php';

$accion = $_POST['accion'] ?? '';

if ($accion === 'crear') {
    $titulo = trim($_POST['titulo'] ?? '');
    if ($titulo !== '') {
        $stmt = $pdo->prepare("INSERT INTO tareas (titulo) VALUES (?)");
        $stmt->execute([$titulo]);
    }
}

if ($accion === 'completar') {
    $id = (int) $_POST['id'];
    $stmt = $pdo->prepare("UPDATE tareas SET completada = 1 WHERE id = ?");
    $stmt->execute([$id]);
}

if ($accion === 'eliminar') {
    $id = (int) $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM tareas WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: /');
exit;
