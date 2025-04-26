<?php
require '../include/conecta.php';

// Busca tipos de imóvel para o select
$tipos_imovel = $db->query("SELECT * FROM tipos_imovel ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);

// Exclusão
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $db->prepare("DELETE FROM imoveis WHERE id = ?")->execute([$id]);
    header("Location: imoveis.php");
    exit;
}

// Inserção ou atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $tipo_id = (int) ($_POST['tipo_id'] ?? 0);
    $bairro = trim($_POST['bairro'] ?? '');
    $quartos = (int) ($_POST['quartos'] ?? 0);
    $preco = (float) ($_POST['preco'] ?? 0);
    $id_edit = $_POST['id_edit'] ?? null;

    if ($tipo_id && $bairro && $preco > 0) {
        if ($id_edit) {
            $stmt = $db->prepare("UPDATE imoveis SET tipo_id = ?, bairro = ?, quartos = ?, preco = ? WHERE id = ?");
            $stmt->execute([$tipo_id, $bairro, $quartos, $preco, $id_edit]);
        } else {
            $stmt = $db->prepare("INSERT INTO imoveis (tipo_id, bairro, quartos, preco) VALUES (?, ?, ?, ?)");
            $stmt->execute([$tipo_id, $bairro, $quartos, $preco]);
        }
        header("Location: imoveis.php");
        exit;
    } else {
        echo "<div class='alert alert-danger'>Preencha todos os campos obrigatórios corretamente.</div>";
    }
}

$imoveis = $db->query("
    SELECT imoveis.*, tipos_imovel.nome AS tipo_nome
    FROM imoveis
    LEFT JOIN tipos_imovel ON imoveis.tipo_id = tipos_imovel.id
    ORDER BY imoveis.id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Imóveis</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="container py-5">

    <h1 class="mb-4">Cadastrar Imóvel</h1>
    <form method="POST" class="row g-3 mb-5">
        <input type="hidden" name="id_edit" value="">
        <div class="col-md-3">
            <select name="tipo_id" class="form-select" required>
                <option value="">Selecione o Tipo</option>
                <?php foreach ($tipos_imovel as $tipo){ ?>
                    <option value="<?= $tipo['id'] ?>"><?= htmlspecialchars($tipo['nome']) ?></option>
                <?php }; ?>
            </select>
        </div>
        <div class="col-md-3">
            <input type="text" name="bairro" class="form-control" placeholder="Bairro" required>
        </div>
        <div class="col-md-2">
            <input type="number" name="quartos" class="form-control" placeholder="Quartos" min="0" required>
        </div>
        <div class="col-md-2">
            <input type="number" step="0.01" name="preco" class="form-control" placeholder="Preço (R$)" min="0.01" required>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success w-100">Salvar</button>
        </div>
    </form>

    <h2>Lista de Imóveis</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Tipo</th>
                <th>Bairro</th>
                <th>Quartos</th>
                <th>Preço</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($imoveis as $imovel){ ?>
            <tr>
                <td><?= htmlspecialchars($imovel['tipo_nome']) ?></td>
                <td><?= htmlspecialchars($imovel['bairro']) ?></td>
                <td><?= $imovel['quartos'] ?></td>
                <td>R$ <?= number_format($imovel['preco'], 2, ',', '.') ?></td>
                <td>
                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#editModal<?= $imovel['id'] ?>"><i class="bi bi-pencil-square"></i></button>
                    <a href="?delete=<?= $imovel['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Tem certeza que deseja excluir este imóvel?');"><i class="bi bi-trash"></i></a>
                </td>
            </tr>

            <!-- Modal de Edição -->
            <div class="modal fade" id="editModal<?= $imovel['id'] ?>" tabindex="-1" aria-labelledby="editModalLabel<?= $imovel['id'] ?>" aria-hidden="true">
              <div class="modal-dialog">
                <div class="modal-content">
                  <form method="POST" action="imoveis.php">
                    <div class="modal-header">
                      <h5 class="modal-title" id="editModalLabel<?= $imovel['id'] ?>">Editar Imóvel</h5>
                      <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_edit" value="<?= $imovel['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Tipo</label>
                            <select name="tipo_id" class="form-select" required>
                                <?php foreach ($tipos_imovel as $tipo){ ?>
                                    <option value="<?= $tipo['id'] ?>" <?= $imovel['tipo_id'] == $tipo['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($tipo['nome']) ?>
                                    </option>
                                <?php }; ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Bairro</label>
                            <input type="text" name="bairro" class="form-control" required value="<?= htmlspecialchars($imovel['bairro']) ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Quartos</label>
                            <input type="number" name="quartos" class="form-control" required min="0" value="<?= $imovel['quartos'] ?>">
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Preço (R$)</label>
                            <input type="number" step="0.01" name="preco" class="form-control" required min="0.01" value="<?= $imovel['preco'] ?>">
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

    <a href="../index.php" class="btn btn-secondary mt-3">Voltar</a>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
