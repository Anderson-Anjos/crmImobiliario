<?php
require '../include/conecta.php';


// Buscar tipos de imóvel
$tipos_imovel = $db->query("SELECT * FROM tipos_imovel ORDER BY nome ASC")->fetchAll(PDO::FETCH_ASSOC);

// Exclusão
if (isset($_GET['delete'])) {
    $id = (int) $_GET['delete'];
    $db->prepare("DELETE FROM clientes WHERE id = ?")->execute([$id]);
    header("Location: clientes.php");
    exit;
}

// Inserção ou atualização
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome'] ?? '');
    $telefone = trim($_POST['telefone'] ?? '');
    $tipo_id = (int) ($_POST['tipo_id'] ?? 0);
    $preco_min = isset($_POST['preco_min']) ? (float) $_POST['preco_min'] : null;
    $preco_max = isset($_POST['preco_max']) ? (float) $_POST['preco_max'] : null;
    $quartos_min = isset($_POST['quartos_min']) ? (int) $_POST['quartos_min'] : null;
    $bairros = $_POST['bairros'] ?? '';
    $id_edit = $_POST['id_edit'] ?? null;

    if ($nome && $telefone && $tipo_id) {
        if ($id_edit) {
            $stmt = $db->prepare("UPDATE clientes SET nome = ?, telefone = ?, tipo_id = ?, preco_min = ?, preco_max = ?, quartos_min = ?, bairros = ? WHERE id = ?");
            $stmt->execute([$nome, $telefone, $tipo_id, $preco_min, $preco_max, $quartos_min, $bairros, $id_edit]);
            header("Location: clientes.php");
        } else {
            $stmt = $db->prepare("INSERT INTO clientes (nome, telefone, tipo_id, preco_min, preco_max, quartos_min, bairros) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nome, $telefone, $tipo_id, $preco_min, $preco_max, $quartos_min, $bairros]);
            $id_cliente = $db->lastInsertId();
            header("Location: clientes.php?sucesso=1&id_cliente=$id_cliente");
        }
        exit;

    } else {
        echo "<div class='alert alert-danger'>Preencha os campos obrigatórios.</div>";
    }
}

// Listagem
$clientes = $db->query("
    SELECT clientes.*, tipos_imovel.nome AS tipo_nome
    FROM clientes
    LEFT JOIN tipos_imovel ON clientes.tipo_id = tipos_imovel.id
    ORDER BY clientes.id DESC
")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Clientes / Interesses</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="container py-5">

    <h1 class="mb-4">Cadastrar Interesse de Cliente</h1>
    <form method="POST" class="row g-3 mb-5">
        <input type="hidden" name="id_edit" value="">
        <div class="col-md-3">
            <input type="text" name="nome" class="form-control" placeholder="Nome" required>
        </div>
        <div class="col-md-3">
            <input type="text" name="telefone" class="form-control" placeholder="Telefone" required>
        </div>
        <div class="col-md-3">
            <select name="tipo_id" class="form-select" required>
                <option value="">Tipo de Imóvel</option>
                <?php foreach ($tipos_imovel as $tipo): ?>
                    <option value="<?= $tipo['id'] ?>"><?= htmlspecialchars($tipo['nome']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-3">
            <input type="number" name="quartos_min" class="form-control" placeholder="Número mínimo de quartos">
        </div>
        <div class="col-md-3">
            <input type="number" step="0.01" name="preco_min" class="form-control" placeholder="preco de preço mínima (R$)">
        </div>
        <div class="col-md-3">
            <input type="number" step="0.01" name="preco_max" class="form-control" placeholder="preco de preço máxima (R$)">
        </div>
        <div class="col-md-6">
            <input type="text" name="bairros" class="form-control" placeholder="Lista de bairros desejados (separados por vírgula)">
        </div>
        <div class="col-md-12">
            <button type="submit" class="btn btn-success">Salvar</button>
        </div>
    </form>

    <h2>Lista de Clientes / Interesses</h2>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Telefone</th>
                <th>Tipo de Imóvel</th>
                <th>preco de Preço</th>
                <th>Quartos Mínimos</th>
                <th>Bairros</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente){ ?>
            <tr>
                <td><?= htmlspecialchars($cliente['nome']) ?></td>
                <td><?= htmlspecialchars($cliente['telefone']) ?></td>
                <td><?= htmlspecialchars($cliente['tipo_nome']) ?></td>
                <td>
                    <?php
                    if ($cliente['preco_min'] !== null && $cliente['preco_max'] !== null) {
                        echo "R$ " . number_format($cliente['preco_min'], 2, ',', '.') . " - R$ " . number_format($cliente['preco_max'], 2, ',', '.');
                    } elseif ($cliente['preco_min'] !== null) {
                        echo "A partir de R$ " . number_format($cliente['preco_min'], 2, ',', '.');
                    } elseif ($cliente['preco_max'] !== null) {
                        echo "Até R$ " . number_format($cliente['preco_max'], 2, ',', '.');
                    } else {
                        echo "Não informado";
                    }
                    ?>
                </td>
                <td><?= $cliente['quartos_min'] ?? '-' ?></td>
                <td><?= htmlspecialchars($cliente['bairros']) ?></td>
                <td>
                    <button class="btn btn-info btn-sm" onclick="abrirRecomendacao(<?= $cliente['id'] ?>)"><i class="bi bi-search"></i></button>
                    <button class="btn btn-primary btn-sm"  data-bs-toggle="modal"  data-bs-target="#editModal" data-id="<?= $cliente['id'] ?>"
                data-nome="<?= htmlspecialchars($cliente['nome'], ENT_QUOTES) ?>" data-telefone="<?= htmlspecialchars($cliente['telefone'], ENT_QUOTES) ?>"
                data-tipo="<?= $cliente['tipo_id'] ?>" data-preco-min="<?= $cliente['preco_min'] ?>" data-preco-max="<?= $cliente['preco_max'] ?>"
                data-quartos-min="<?= $cliente['quartos_min'] ?>" data-bairros="<?= htmlspecialchars($cliente['bairros'], ENT_QUOTES) ?>"><i class="bi bi-pencil-square"></i></button>
                    <a href="?delete=<?= $cliente['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Deseja excluir?');"><i class="bi bi-trash"></i></a>
                </td>
            </tr>
            <?php }; ?>
        </tbody>
    </table>

    <a href="../index.php" class="btn btn-secondary mt-3">Voltar</a>

    <!-- Modal de Edição -->
<div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editModalLabel">Editar Cliente / Interesse</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id_edit" id="edit-id">
        <div class="mb-3">
          <label>Nome</label>
          <input type="text" name="nome" id="edit-nome" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Telefone</label>
          <input type="text" name="telefone" id="edit-telefone" class="form-control" required>
        </div>
        <div class="mb-3">
          <label>Tipo de Imóvel</label>
          <select name="tipo_id" id="edit-tipo" class="form-select" required>
            <option value="">Tipo de Imóvel</option>
            <?php foreach ($tipos_imovel as $tipo){ ?>
                <option value="<?= $tipo['id'] ?>"><?= htmlspecialchars($tipo['nome']) ?></option>
            <?php }; ?>
          </select>
        </div>
        <div class="mb-3">
          <label>preco Mínima</label>
          <input type="number" step="0.01" name="preco_min" id="edit-preco-min" class="form-control">
        </div>
        <div class="mb-3">
          <label>preco Máxima</label>
          <input type="number" step="0.01" name="preco_max" id="edit-preco-max" class="form-control">
        </div>
        <div class="mb-3">
          <label>Número mínimo de quartos</label>
          <input type="number" name="quartos_min" id="edit-quartos-min" class="form-control">
        </div>
        <div class="mb-3">
          <label>Bairros</label>
          <input type="text" name="bairros" id="edit-bairros" class="form-control">
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-success">Salvar Alterações</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
      </div>
    </form>
  </div>
</div>

<!-- Modal de Recomendações -->
<div class="modal fade" id="recomendacoesModal" tabindex="-1" aria-labelledby="recomendacoesModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="recomendacoesModalLabel">Imóveis Recomendados</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
      </div>
      <div class="modal-body" id="conteudoRecomendacoes">
      </div>
      <div class="modal-footer">
        <a href="clientes.php" class="btn btn-secondary">Voltar</a>
        <a href="../imoveis/imoveis.php" class="btn btn-primary">Cadastrar Novo Imóvel</a>
      </div>
    </div>
  </div>
</div>
</body>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
const editModal = document.getElementById('editModal');
editModal.addEventListener('show.bs.modal', function (event) {
    const button = event.relatedTarget;

    document.getElementById('edit-id').value = button.getAttribute('data-id');
    document.getElementById('edit-nome').value = button.getAttribute('data-nome');
    document.getElementById('edit-telefone').value = button.getAttribute('data-telefone');
    document.getElementById('edit-tipo').value = button.getAttribute('data-tipo');
    document.getElementById('edit-preco-min').value = button.getAttribute('data-preco-min');
    document.getElementById('edit-preco-max').value = button.getAttribute('data-preco-max');
    document.getElementById('edit-quartos-min').value = button.getAttribute('data-quartos-min');
    document.getElementById('edit-bairros').value = button.getAttribute('data-bairros');
});
</script>

<?php if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1 && isset($_GET['id_cliente'])){ ?>
<script>
$(document).ready(function() {
    $.get('recomendacoes_modal.php', { id_cliente: <?= (int)$_GET['id_cliente'] ?> }, function(data) {
        $('#conteudoRecomendacoes').html(data);
        var recomendacoesModal = new bootstrap.Modal(document.getElementById('recomendacoesModal'));
        recomendacoesModal.show();
    });
});
</script>
<?php }; ?>
<script>
function abrirRecomendacao(idCliente) {
    $.get('recomendacoes_modal.php', { id_cliente: idCliente }, function(data) {
        $('#conteudoRecomendacoes').html(data);
        var recomendacoesModal = new bootstrap.Modal(document.getElementById('recomendacoesModal'));
        recomendacoesModal.show();
    });
}
</script>


</html>
