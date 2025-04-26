<?php
require './include/conecta.php';

// Exclusão
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $db->prepare("DELETE FROM tipos_imovel WHERE id = ?")->execute([$id]);
    header("Location: tipos_imovel.php");
    exit;
}

// Inserção ou atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $id_edit = $_POST['id_edit'] ?? null;

    if ($nome !== '') {
        if ($id_edit) {
            $stmt = $db->prepare("UPDATE tipos_imovel SET nome = ? WHERE id = ?");
            $stmt->execute([$nome, $id_edit]);
        } else {
            $stmt = $db->prepare("INSERT INTO tipos_imovel (nome) VALUES (?)");
            $stmt->execute([$nome]);
        }
        header("Location: tipos_imovel.php");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Preencha o nome do tipo de imóvel.</div>";
    }
}

$tipos = $db->query("SELECT * FROM tipos_imovel ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Tipos de Imóvel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="container py-5">

    <h1 class="mb-4">Cadastrar Tipo de Imóvel</h1>

    <form method="POST" class="row g-3 mb-5">
        <input type="hidden" name="id_edit" value="">
        <div class="col-md-6">
            <input type="text" name="nome" class="form-control" placeholder="Nome do Tipo (ex: Casa, Apartamento)" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success w-100">Salvar</button>
        </div>
    </form>

    <h2>Lista de Tipos de Imóvel</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($tipos as $tipo){ ?>
            <tr>
                <td><?= htmlspecialchars($tipo['nome']) ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $tipo['id'] ?>"><i class="bi bi-pencil-square"></i></button>
                    <a href="?delete=<?= $tipo['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este tipo?');"><i class="bi bi-trash"></i></a>
                </td>
            </tr>

            <!-- Modal de Edição -->
            <div class="modal fade" id="editModal<?= $tipo['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $tipo['id'] ?>" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form method="POST" action="tipos_imovel.php">
                    <div class="modal-header">
                      <h5 class="modal-title" id="editModalLabel<?= $tipo['id'] ?>">Editar Tipo de Imóvel</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_edit" value="<?= $tipo['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="nome" class="form-control" required value="<?= htmlspecialchars($tipo['nome']) ?>">
                        </div>
                    </div>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                      <button type="submit" class="btn btn-primary">Salvar alterações</button>
                    </div>
                  </form>
                </div>
              </div>
            </div>
            <!-- Fim Modal -->
            <?php }; ?>
        </tbody>
    </table>

    <a href="./index.php" class="btn btn-secondary mt-3">Voltar</a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
