<?php
session_start();  // Inicia ou retoma a sessão

// Verifica se o usuário está logado, caso contrário, pode redirecioná-lo para a tela de login
if (isset($_SESSION['email'])) {
    header("Location: index.php");  // Se o usuário estiver logado, redireciona para a página inicial
    exit;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('config.php');  // Inclui a conexão com o banco de dados

    // Recebe os dados do formulário
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    // Previne SQL Injection
    $email = mysqli_real_escape_string($conn, $email);
    $senha = mysqli_real_escape_string($conn, $senha);

    // Consulta o banco de dados para verificar o usuário
    $sql = "SELECT id_usuario, email, senha FROM usuario WHERE email = '$email'";
    $result = $conn->query($sql);

    // Se o usuário for encontrado
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verifica se a senha informada corresponde à senha armazenada (sem hash)
        if ($senha === $row['senha']) {
            // Armazena os dados do usuário na sessão
            $_SESSION['id_usuario'] = $row['id_usuario'];
            $_SESSION['email'] = $row['email'];

            // Redireciona o usuário para a página inicial
            header("Location: index.php");
            exit;
        } else {
            // Se a senha estiver incorreta
            echo "<div class='alert alert-danger'>Usuário ou senha inválidos.</div>";
        }
    } else {
        // Se o usuário não for encontrado
        echo "<div class='alert alert-danger'>Usuário ou senha inválidos.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <br>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <!-- Link para o arquivo CSS -->
    <link rel="stylesheet" href="estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-4">
        <h2 class="text-center">Login</h2>
        <br>
        <form method="POST" class="d-flex justify-content-center flex-column align-items-center">
            <div class="mb-3" style="width: 500px;"> <!-- Ajuste de largura do campo -->
                <label for="email" class="form-label">E-mail</label>
                <!-- Ajuste de largura do campo de email -->
                <input type="email" class="form-control" id="email" name="email" required maxlength="50" style="padding: 10px; font-size: 16px;">
            </div>
            <div class="mb-3" style="width: 500px;"> <!-- Ajuste de largura do campo -->
                <label for="senha" class="form-label">Senha</label>
                <!-- Ajuste de largura do campo de senha -->
                <input type="password" class="form-control" id="senha" name="senha" required maxlength="50" style="padding: 10px; font-size: 16px;">
            </div>
            <!-- Ajuste de largura do botão -->
            <button type="submit" class="btn btn-success">Entrar</button>
        </form>

        <div class="mt-3 text-center">
            <a href="recupera_senha.php">Esqueceu a senha?</a> | 
            <a href="cadastro.php">Cadastrar-se</a>
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
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>


