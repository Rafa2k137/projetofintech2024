<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('config.php');

    // Recebe o e-mail do usuário
    $email = $_POST['email'];

    // Verifica se o e-mail está no banco de dados
    $sql = "SELECT id_usuario FROM usuario WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // Se o e-mail existe, exibe uma mensagem de sucesso e redireciona para a tela de alterar senha
        echo "
        <div class='alert alert-success' id='alerta-sucesso'>
        Um e-mail com as instruções foi enviado!
        </div>
        <script>
        setTimeout(function() {
            window.location.href = 'altera_senha.php?email=" . urlencode($email) . "'; // Redireciona para a tela de alterar senha
            }, 3000); // Espera 3 segundos para redirecionar
            </script>
            ";
        } else {
        // E-mail não encontrado
            echo "<div class='alert alert-danger'>E-mail não encontrado.</div>";
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <br>
        <br>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Recuperação de Senha</title>
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
            <h2>Recuperação de Senha</h2>
            <br>
            <form method="POST">
                <div class="mb-3">
                    <label for="email" class="form-label">Informe seu E-mail</label>
                    <!-- Usando a classe personalizada form-campo para controlar a largura -->
                    <input type="email" class="form-control form-campo" id="email" name="email" required>
                </div>
                <button type="submit" class="btn btn-success">Enviar Link</button>
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
