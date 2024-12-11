<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('config.php');

    // Verifica se o e-mail foi passado pela URL
    if (isset($_GET['email'])) {
        $email = $_GET['email']; // Pega o e-mail da URL
        $nova_senha = $_POST['nova_senha'];
        $confirmar_senha = $_POST['confirmar_senha'];

        // Verifica se as senhas coincidem
        if ($nova_senha === $confirmar_senha) {
            // Atualiza a senha no banco de dados SEM hash
            $sql_update = "UPDATE usuario SET senha = '$nova_senha' WHERE email = '$email'";

            if ($conn->query($sql_update) === TRUE) {
                // Mensagem de sucesso com redirecionamento
                echo "
                <div class='alert alert-success' id='alerta-sucesso'>
                Senha alterada com sucesso!
                </div>
                <script>
                setTimeout(function() {
                    window.location.href = 'login.php'; // Redireciona para a tela de login após 3 segundos
                    }, 3000);
                    </script>
                    ";
                } else {
                    echo "<div class='alert alert-danger'>Erro ao alterar a senha.</div>";
                }
            } else {
                echo "<div class='alert alert-danger'>As senhas não coincidem.</div>";
            }
        } else {
            echo "<div class='alert alert-danger'>E-mail não encontrado na URL.</div>";
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <br>
        <br>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=d
        evice-width, initial-scale=1.0">
        <title>Alteração de Senha</title>
        <link rel="stylesheet" href="estilos.css">
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
        <style>
            /* CSS para controlar a largura do campo */
            .form-campo {
                width: 500px;
                margin-left: auto;
                margin-right: auto;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <h2>Alteração de Senha</h2>
            <br>
            <form method="POST">
                <div class="mb-3">
                    <label for="nova_senha" class="form-label">Nova Senha</label>
                    <input type="password" class="form-control form-campo" id="nova_senha" name="nova_senha" required>
                </div>
                <div class="mb-3">
                    <label for="confirmar_senha" class="form-label">Confirmar Senha</label>
                    <input type="password" class="form-control form-campo" id="confirmar_senha" name="confirmar_senha" required>
                </div>
                <button type="submit" class="btn btn-success">Alterar</button>
            </form>
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
    </body>
    </html>
