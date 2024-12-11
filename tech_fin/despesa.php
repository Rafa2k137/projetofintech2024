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

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos.css">
    <!-- Link para o CSS do Bootstrap -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
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
                <li class="nav-item">
                    <a class="nav-link active" href="index.php"><strong>Home</strong></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="recibo.php"><strong>Recibos</strong></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="despesa.php"><strong>Despesas</strong></a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php"><strong>Sair</strong></a>
                </li>
            </ul>
        </div>
    </div>
</nav>
<br><br><br>
<!-- Corpo da página -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <a href="cadastra_despesa.php" class="btn btn-success btn-lg btn-block">Incluir Despesas</a>
        </div>
        <div class="col-md-4">
            <a href="consulta_despesa.php" class="btn btn-primary btn-lg btn-block">Consultar Despesas</a>
        </div>
    </div>
</div>

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

<!-- Script do Bootstrap (JavaScript) -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
