<?php
require './include/conecta.php';

$id_cliente = $_GET['id_cliente'] ?? null;
if (!$id_cliente) {
    echo 'Cliente não informado.';
    exit;
}

// Pega dados do cliente
$sql = "SELECT clientes.*, tipos_imovel.nome as tipo_nome
        FROM clientes
        JOIN tipos_imovel ON clientes.tipo_id = tipos_imovel.id
        WHERE clientes.id = ?";
$stmt = $db->prepare($sql);
$stmt->execute([$id_cliente]);
$cliente = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$cliente) {
    echo 'Cliente não encontrado.';
    exit;
}

// Busca imóveis que combinam
$sqlImoveis = "SELECT imoveis.*, tipos_imovel.nome as tipo_nome
               FROM imoveis
               JOIN tipos_imovel ON imoveis.tipo_id = tipos_imovel.id
               WHERE 1=1";

$params = [];

// Filtro: Tipo de imóvel (sempre obrigatório)
if (!empty($cliente['tipo_id'])) {
    $sqlImoveis .= " AND imoveis.tipo_id = ?";
    $params[] = $cliente['tipo_id'];
}

// Filtro: Quartos mínimos
if (isset($cliente['quartos_min'])) {
    $sqlImoveis .= " AND imoveis.quartos >= ?";
    $params[] = $cliente['quartos_min'];
}

// Filtro: Faixa de preço
if (isset($cliente['preco_min'])) {
    $sqlImoveis .= " AND imoveis.preco >= ?";
    $params[] = $cliente['preco_min'];
}

if (isset($cliente['preco_max'])) {
    $sqlImoveis .= " AND imoveis.preco <= ?";
    $params[] = $cliente['preco_max'];
}

// Filtro: Bairros desejados
if (!empty($cliente['bairros'])) {
    $bairrosArray = array_map('trim', explode(',', $cliente['bairros']));
    $placeholders = implode(',', array_fill(0, count($bairrosArray), '?'));
    $sqlImoveis .= " AND imoveis.bairro IN ($placeholders)";
    $params = array_merge($params, $bairrosArray);
}

// Agora prepara e executa
$stmtImoveis = $db->prepare($sqlImoveis);
$stmtImoveis->execute($params);

// Pega os resultados
$imoveis = $stmtImoveis->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="container">
    <h4>Cliente: <?= htmlspecialchars($cliente['nome']) ?> (<?= htmlspecialchars($cliente['telefone']) ?>)</h4>
    <p><strong>Interesse:</strong> <?= htmlspecialchars($cliente['tipo_nome']) ?>, <?= $cliente['quartos_min'] ?>+ quartos, R$ <?= number_format($cliente['preco_min'],2,',','.') ?> a R$ <?= number_format($cliente['preco_max'],2,',','.') ?>, Bairros: <?= htmlspecialchars($cliente['bairros']) ?></p>

    <?php if (count($imoveis) > 0){ ?>
        <div class="row g-4">
            <?php foreach ($imoveis as $imovel){ ?>
                <div class="col-md-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($imovel['tipo_nome']) ?></h5>
                            <p class="card-text">
                                <strong>Bairro:</strong> <?= htmlspecialchars($imovel['bairro']) ?><br>
                                <strong>Quartos:</strong> <?= $imovel['quartos'] ?><br>
                                <strong>Preço:</strong> R$ <?= number_format($imovel['preco'], 2, ',', '.') ?>
                            </p>
                        </div>
                    </div>
                </div>
            <?php }; ?>
        </div>
    <?php } else{ ?>
        <div class="alert alert-warning">Nenhum imóvel encontrado para este cliente.</div>
    <?php }; ?>
</div>
