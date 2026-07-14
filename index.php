<?php

require_once 'config.php';

$stmt = $pdo->query("SELECT * FROM tareas ORDER BY created_at DESC");
$tareas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$pendientes = array_filter($tareas, fn($t) => !$t['completada']);
$completadas = array_filter($tareas, fn($t) => $t['completada']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Tareas</title>
    <style>
        body { font-family: sans-serif; max-width: 600px; margin: 40px auto; padding: 0 20px; }
        h1 { color: #1a1a2e; }
        form.nueva { display: flex; gap: 8px; margin-bottom: 32px; }
        form.nueva input { flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 6px; font-size: 15px; }
        form.nueva button { padding: 10px 20px; background: #1D9E75; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 15px; }
        .tarea { display: flex; align-items: center; justify-content: space-between; padding: 12px; border: 1px solid #eee; border-radius: 8px; margin-bottom: 8px; }
        .tarea.hecha { opacity: 0.5; text-decoration: line-through; }
        .acciones { display: flex; gap: 6px; }
        .acciones button { padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px; }
        .btn-completar { background: #e8f5e9; color: #2e7d32; }
        .btn-eliminar { background: #fdecea; color: #c62828; }
        h2 { font-size: 14px; color: #888; text-transform: uppercase; letter-spacing: 1px; margin: 24px 0 12px; }
    </style>
</head>
<body>

<h1>Mis tareas</h1>

<form class="nueva" action="/acciones.php" method="POST">
    <input type="hidden" name="accion" value="crear">
    <input type="text" name="titulo" placeholder="Nueva tarea..." autofocus>
    <button type="submit">Agregar</button>
</form>

<?php if ($pendientes): ?>
<h2>Pendientes (<?= count($pendientes) ?>)</h2>
<?php foreach ($pendientes as $tarea): ?>
<div class="tarea">
    <span><?= htmlspecialchars($tarea['titulo']) ?></span>
    <div class="acciones">
        <form action="/acciones.php" method="POST">
            <input type="hidden" name="accion" value="completar">
            <input type="hidden" name="id" value="<?= $tarea['id'] ?>">
            <button class="btn-completar">✓ Completar</button>
        </form>
        <form action="/acciones.php" method="POST">
            <input type="hidden" name="accion" value="eliminar">
            <input type="hidden" name="id" value="<?= $tarea['id'] ?>">
            <button class="btn-eliminar">✕ Eliminar</button>
        </form>
    </div>
</div>
<?php endforeach ?>
<?php endif ?>

<?php if ($completadas): ?>
<h2>Completadas (<?= count($completadas) ?>)</h2>
<?php foreach ($completadas as $tarea): ?>
<div class="tarea hecha">
    <span><?= htmlspecialchars($tarea['titulo']) ?></span>
    <div class="acciones">
        <form action="/acciones.php" method="POST">
            <input type="hidden" name="accion" value="eliminar">
            <input type="hidden" name="id" value="<?= $tarea['id'] ?>">
            <button class="btn-eliminar">✕ Eliminar</button>
        </form>
    </div>
</div>
<?php endforeach ?>
<?php endif ?>

</body>
</html>
