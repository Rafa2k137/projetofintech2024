<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['email'])) {
    header("Location: login.php"); // Redireciona para a tela de login se o usuário não estiver logado
    exit;
}

// Se o nome do usuário não estiver na sessão, consultamos o banco de dados
if (!isset($_SESSION['nome'])) {
    include('config.php'); // Conecte-se ao banco de dados

    // Obtém o nome do usuário pelo email
    $email = $_SESSION['email'];
    $sql = "SELECT nome FROM usuario WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Se encontrado, armazena o nome na sessão
        $row = $result->fetch_assoc();
        $_SESSION['nome'] = $row['nome'];
    } else {
        // Se não encontrar o nome, redireciona para login
        header("Location: login.php");
        exit;
    }
}

// Definindo as variáveis de conexão
$host = "localhost";  // ou o endereço do servidor MySQL
$dbname = "dba_tech_fin";  // nome do banco de dados
$username = "root";  // nome de usuário do MySQL
$password = "";  // senha do MySQL

// Tentando se conectar ao banco de dados
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION); // Ativa o modo de erro para exceções
} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();  // Exibe a mensagem de erro caso a conexão falhe
}

// Se o formulário de exclusão for submetido
if (isset($_POST['delete'])) {
    if (isset($_POST['ids']) && !empty($_POST['ids'])) {
        $ids = implode(',', $_POST['ids']);
        // Executa a exclusão no banco de dados
        $deleteQuery = "DELETE FROM DESPESA WHERE id_despesa IN ($ids)";
        $pdo->exec($deleteQuery);
    }
}

// Definir a quantidade de registros por página
$registros_por_pagina = 5;

// Contar o número total de registros
$query_total = "SELECT COUNT(*) FROM DESPESA";
$stmt_total = $pdo->query($query_total);
$total_registros = $stmt_total->fetchColumn();

// Calcular o número total de páginas
$total_paginas = ceil($total_registros / $registros_por_pagina);

// Obter a página atual (se não houver, assume a página 1)
$pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;

// Calcular o índice inicial para a consulta SQL
$inicio = ($pagina_atual - 1) * $registros_por_pagina;

// Consultar os registros da tabela DESPESA com limite e offset
$query = "SELECT * FROM DESPESA LIMIT :inicio, :registros_por_pagina";
$stmt = $pdo->prepare($query);
$stmt->bindParam(':inicio', $inicio, PDO::PARAM_INT);
$stmt->bindParam(':registros_por_pagina', $registros_por_pagina, PDO::PARAM_INT);
$stmt->execute();
$despesas = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Despesas</title>
    <link rel="stylesheet" href="estilos.css">
    <!-- Incluindo o CSS do Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* Customizações da paginação */
        .pagination .page-item.active .page-link {
            background-color: #006400; /* Verde escuro para a página ativa */
            color: white; /* Texto branco para a página ativa */
        }

        .pagination .page-item .page-link {
            background-color: white; /* Fundo branco para as outras páginas */
            color: #006400; /* Texto verde para as outras páginas */
        }

        .pagination .page-item.disabled .page-link {
            background-color: white; /* Fundo branco para itens desabilitados */
            color: #ccc; /* Texto cinza para itens desabilitados */
        }

        .pagination .page-link {
            border: 1px solid #006400; /* Borda verde nas páginas */
        }

        .pagination .page-item:hover .page-link {
            background-color: #006400; /* Fundo verde quando passa o mouse */
            color: white; /* Texto branco ao passar o mouse */
        }
    </style>
</head>
<body>
    <!-- Menu de navegação -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand"><strong>Tech-Fin</strong></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav">
                    <li class="nav-item"><a class="nav-link active" href="index.php"><strong>Home</strong></a></li>
                    <li class="nav-item"><a class="nav-link" href="recibo.php"><strong>Recibos</strong></a></li>
                    <li class="nav-item"><a class="nav-link" href="despesa.php"><strong>Despesas</strong></a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php"><strong>Sair</strong></a></li>
                </ul>
            </div>
        </div>
    </nav>
    <br><br><br>
    <div class="container mt-5">
        <h2 class="text-center">Despesas</h2>
        <br>
        <form method="POST" action="">
            <!-- Tabela de resultados -->
            <table class="table table-striped table-bordered">
                <thead>
                    <tr>
                        <th><input type="checkbox" id="selectAll" onclick="toggleCheckboxes()"></th>
                        <th>Despesa</th>
                        <th>Nota Fiscal</th>
                        <th>Data de Emissão</th>
                        <th>CPF Fornecedor</th>
                        <th>CNPJ Fornecedor</th>
                        <th>Quantidade</th>
                        <th>Valor Unitário</th>
                        <th>Valor Total</th>
                        <th>Data e Hora da Inclusão</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($despesas as $despesa): ?>
                        <tr>
                            <td><input type="checkbox" name="ids[]" value="<?php echo $despesa['id_despesa']; ?>"></td>
                            <td><?php echo htmlspecialchars($despesa['id_despesa']); ?></td>
                            <td><?php echo htmlspecialchars($despesa['nota_fiscal']); ?></td>
                            <td><?php echo htmlspecialchars($despesa['dt_emissao']); ?></td>
                            <td><?php echo htmlspecialchars($despesa['cpf_fornecedor']); ?></td>
                            <td><?php echo htmlspecialchars($despesa['cnpj_fornecedor']); ?></td>
                            <td><?php echo htmlspecialchars($despesa['quantidade']); ?></td>
                            <td><?php echo 'R$ ' . number_format($despesa['vl_unitario'], 2, ',', '.'); ?></td>
                            <td><?php echo 'R$ ' . number_format($despesa['vl_total'], 2, ',', '.'); ?></td>
                            <td><?php echo htmlspecialchars($despesa['data_cadastro']); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <br>
            <div class="text-center">
                <button type="submit" name="delete" class="btn btn-danger">Excluir Selecionados</button>
            </div>
        </form>
        <br>

        <!-- Controles de Paginação -->
        <nav aria-label="Page navigation">
            <ul class="pagination justify-content-center">
                <li class="page-item <?php echo ($pagina_atual <= 1) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?pagina=<?php echo $pagina_atual - 1; ?>" aria-label="Anterior">
                        <span aria-hidden="true">&laquo;</span>
                    </a>
                </li>
                <?php for ($i = 1; $i <= $total_paginas; $i++): ?>
                    <li class="page-item <?php echo ($i == $pagina_atual) ? 'active' : ''; ?>">
                        <a class="page-link" href="?pagina=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
                <li class="page-item <?php echo ($pagina_atual >= $total_paginas) ? 'disabled' : ''; ?>">
                    <a class="page-link" href="?pagina=<?php echo $pagina_atual + 1; ?>" aria-label="Próximo">
                        <span aria-hidden="true">&raquo;</span>
                    </a>
                </li>
            </ul>
        </nav>
        <br>
        <div class="mt-3 text-center">
            <a href="cadastra_despesa.php">Incluir Despesas</a>
        </div>
    </div>

    <!-- Script para selecionar/desmarcar todas as checkboxes -->
    <script>
        function toggleCheckboxes() {
            var checkboxes = document.querySelectorAll('input[name="ids[]"]');
            var selectAll = document.getElementById('selectAll');
            checkboxes.forEach(function(checkbox) {
                checkbox.checked = selectAll.checked;
            });
        }
    </script>

    <br><br>
    <!-- Rodapé -->
    <footer class="footer">
        <p>&copy; 2024 Todos os direitos reservados.</p>
        <p>Desenvolvido por Rafael Vasconcelos - Trabalho em andamento.</p>
        <div class="social-links">
            <span>Facebook</span>
            <span>Twitter</span>
            <span>Instagram</span>
        </div>
        <div class="contact-info">
            <p>Contato: (11) 1234-5678 | (11) 9876-5432</p>
        </div>
    </footer>

    <!-- Incluindo o JS do Bootstrap -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
