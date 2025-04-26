<?php
include './include/conecta.php';

// Buscando as quantidades
$qtdImoveis = $db->query('SELECT COUNT(*) FROM imoveis')->fetchColumn();
$qtdTipos = $db->query('SELECT COUNT(*) FROM tipos_imovel')->fetchColumn();
$qtdClientes = $db->query('SELECT COUNT(*) FROM clientes')->fetchColumn();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Sistema Imobiliário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f8f9fa;
        }
        .card:hover {
            transform: scale(1.03);
            transition: 0.3s;
        }
        .card-count {
            font-size: 2rem;
            font-weight: bold;
            color: #0d6efd;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-5">
  <div class="container">
    <a class="navbar-brand" href="#">Sistema Imobiliário</a>
  </div>
</nav>

<div class="container py-5">
    <div class="text-center mb-5">
        <h1 class="display-5">Dashboard</h1>
        <p class="lead">Bem-vindo! Veja abaixo o resumo do sistema.</p>
    </div>

    <div class="row g-4">

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title">Imóveis</h5>
                    <p class="card-count"><?= $qtdImoveis ?></p>
                    <p class="card-text">Cadastre, edite e visualize imóveis disponíveis.</p>
                    <a href="./imoveis/imoveis.php" class="btn btn-primary mt-3">Gerenciar Imóveis</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title">Tipos de Imóvel</h5>
                    <p class="card-count"><?= $qtdTipos ?></p>
                    <p class="card-text">Gerencie os tipos: casa, kitnet, apartamento, etc.</p>
                    <a href="./tipo_imoveis/tipos_imovel.php" class="btn btn-primary mt-3">Gerenciar Tipos</a>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm h-100">
                <div class="card-body d-flex flex-column justify-content-between">
                    <h5 class="card-title">Clientes & Interesses</h5>
                    <p class="card-count"><?= $qtdClientes ?></p>
                    <p class="card-text">Cadastre interesses dos clientes e encontre oportunidades.</p>
                    <a href="./clientes/clientes.php" class="btn btn-primary mt-3">Gerenciar Clientes</a>
                </div>
            </div>
        </div>

    </div>

</div>

<footer class="bg-primary text-white text-center py-3 mt-5">
    <div class="container">
        <small>
            &copy; <?= date('Y') ?> Sistema Imobiliário. Todos os direitos reservados.
        </small>
    </div>
</footer>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
