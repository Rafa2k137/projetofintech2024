<?php
session_start(); // Inicia a sessão

// Definindo variáveis para limpar os campos após o envio
$email = '';
$senha = '';
$nome = '';
$dt_nascimento = '';
$genero = '';
$cpf = '';
$cnpj = '';
$tipo_telefone = '';
$telefone = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('config.php');

    $email = $_POST['email'];
    $senha = $_POST['senha'];
    $nome = $_POST['nome'];
    $dt_nascimento = $_POST['dt_nascimento'];
    $genero = $_POST['genero'];
    $cpf = $_POST['cpf'];
    $cnpj = $_POST['cnpj'];
    $tipo_telefone = $_POST['tipo_telefone'];
    $telefone = $_POST['telefone'];

    // Verifica se o e-mail já está cadastrado no banco de dados
    $sql_check_email = "SELECT * FROM usuario WHERE email = '$email'";
    $result = $conn->query($sql_check_email);

    if ($result->num_rows > 0) {
        // Se o e-mail já existir, exibe mensagem de erro
        $_SESSION['mensagem'] = "<div id='mensagem' class='alert alert-danger'>Este e-mail já está cadastrado.</div>";
        
        // Limpa os campos após o erro e recarrega a página
        $email = '';
        $senha = '';
        $nome = '';
        $dt_nascimento = '';
        $genero = '';
        $cpf = '';
        $cnpj = '';
        $tipo_telefone = '';
        $telefone = '';
        
        // Redireciona para a própria página para evitar resubmissão
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } else {
        // Caso o e-mail não exista, insere o novo usuário
        $sql_insert = "INSERT INTO usuario (email, senha, nome, dt_nascimento, genero, cpf, cnpj, tipo_telefone, telefone)
        VALUES ('$email', '$senha', '$nome', '$dt_nascimento', '$genero', '$cpf', '$cnpj', '$tipo_telefone', '$telefone')";

        if ($conn->query($sql_insert) === TRUE) {
            // Mensagem de sucesso armazenada na sessão
            $_SESSION['mensagem'] = "<div id='mensagem' class='alert alert-success'>Usuário cadastrado com sucesso!</div>";
            
            // Limpa os campos após a inserção
            $email = '';
            $senha = '';
            $nome = '';
            $dt_nascimento = '';
            $genero = '';
            $cpf = '';
            $cnpj = '';
            $tipo_telefone = '';
            $telefone = '';
            
            // Redireciona para evitar resubmissão do formulário
            header("Location: " . $_SERVER['PHP_SELF']);
            exit;
        } else {
            // Mensagem de erro armazenada na sessão
            $_SESSION['mensagem'] = "<div id='mensagem' class='alert alert-danger'>Erro ao cadastrar usuario: " . $conn->error . "</div>";
        }
    }
}

// Recupera a mensagem da sessão, se houver
$mensagem = '';
if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    unset($_SESSION['mensagem']);  // Limpa a mensagem após exibição
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <br>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário</title>
    <link rel="stylesheet" href="estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Função para aplicar a máscara de CPF
        function mascaraCPF(cpf) {
            cpf = cpf.replace(/\D/g, ''); // Remove tudo o que não é número
            cpf = cpf.replace(/^(\d{3})(\d{3})(\d{3})(\d{2})$/, '$1.$2.$3-$4'); // Aplica a máscara
            return cpf;
        }

        // Função para validar o CPF
        function validarCPF(cpf) {
            cpf = cpf.replace(/[^\d]+/g, ''); // Remove caracteres não numéricos
            if (cpf.length !== 11 || /^(.)\1{10}$/.test(cpf)) {
                return false; // Verifica se todos os números são iguais
            }
            let soma = 0;
            let resto;
            for (let i = 1; i <= 9; i++) {
                soma += parseInt(cpf.charAt(i - 1)) * (11 - i);
            }
            resto = (soma * 10) % 11;
            if (resto == 10 || resto == 11) resto = 0;
            if (resto != parseInt(cpf.charAt(9))) return false;
            soma = 0;
            for (let i = 1; i <= 10; i++) {
                soma += parseInt(cpf.charAt(i - 1)) * (12 - i);
            }
            resto = (soma * 10) % 11;
            if (resto == 10 || resto == 11) resto = 0;
            return resto == parseInt(cpf.charAt(10));
        }

        // Função para aplicar a máscara de CNPJ
        function mascaraCNPJ(cnpj) {
            cnpj = cnpj.replace(/\D/g, ''); // Remove tudo o que não é número
            cnpj = cnpj.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})$/, '$1.$2.$3/$4-$5'); // Aplica a máscara
            return cnpj;
        }

        // Função para validar o CNPJ
        function validarCNPJ(cnpj) {
            cnpj = cnpj.replace(/[^\d]+/g, ''); // Remove caracteres não numéricos
            if (cnpj.length !== 14 || /^(.)\1{13}$/.test(cnpj)) {
                return false; // Verifica se todos os números são iguais
            }

            let soma = 0;
            let pos = 5;
            for (let i = 0; i < 12; i++) {
                soma += parseInt(cnpj.charAt(i)) * pos--;
                if (pos < 2) pos = 9;
            }

            let resto = soma % 11;
            if (resto < 2) resto = 0;
            else resto = 11 - resto;

            if (resto !== parseInt(cnpj.charAt(12))) return false;

            soma = 0;
            pos = 6;
            for (let i = 0; i < 13; i++) {
                soma += parseInt(cnpj.charAt(i)) * pos--;
                if (pos < 2) pos = 9;
            }

            resto = soma % 11;
            if (resto < 2) resto = 0;
            else resto = 11 - resto;

            return resto === parseInt(cnpj.charAt(13));
        }

        // Função para habilitar/desabilitar o botão de submit com base nas validações do CPF e CNPJ
        function validarFormulario() {
            const cpf = document.getElementById('cpf').value;
            const cnpj = document.getElementById('cnpj').value;
            const btnSubmit = document.querySelector('button[type="submit"]');

            const cpfErrorMessage = document.getElementById('cpf_error_message');
            const cnpjErrorMessage = document.getElementById('cnpj_error_message');

            // Verifica se o CPF é válido
            const cpfValido = validarCPF(cpf);
            // Verifica se o CNPJ é válido
            const cnpjValido = validarCNPJ(cnpj);

            // Exibe a mensagem de erro para CPF ou CNPJ inválido
            cpfErrorMessage.style.display = cpfValido ? 'none' : 'block';
            cnpjErrorMessage.style.display = cnpjValido ? 'none' : 'block';

            // Habilita o botão apenas se ambos os campos (CPF e CNPJ) forem válidos
            if (cpfValido && cnpjValido) {
                btnSubmit.disabled = false;
            } else {
                btnSubmit.disabled = true;
            }
        }

        // Função para aplicar as máscaras de CPF e CNPJ em tempo real
        function tratarCampos() {
            const cpfInput = document.getElementById('cpf');
            const cnpjInput = document.getElementById('cnpj');

            cpfInput.addEventListener('input', function() {
                this.value = mascaraCPF(this.value);
            });

            cnpjInput.addEventListener('input', function() {
                this.value = mascaraCNPJ(this.value);
            });

            cpfInput.addEventListener('blur', validarFormulario);
            cnpjInput.addEventListener('blur', validarFormulario);
        }

        // Função para esconder a mensagem após 3 segundos e redirecionar para a página de login
        function esconderMensagem() {
            setTimeout(function() {
                const mensagem = document.getElementById('mensagem');
                if (mensagem) {
                    mensagem.style.display = 'none';  // Esconde a mensagem após 3 segundos
                }
                // Redireciona para a página de login após o sucesso
                window.location.href = "login.php";
            }, 3000); // 3 segundos
        }

        // Função para limpar os campos de erro
        function limparCamposErro() {
            const mensagemErro = document.getElementById('mensagem');
            if (mensagemErro && mensagemErro.classList.contains('alert-danger')) {
                setTimeout(function() {
                    window.location.reload(); // Recarrega a página para limpar os campos após 3 segundos
                }, 3000);
            }
        }

        window.onload = function() {
            tratarCampos(); // Aplica a máscara e validação para CPF e CNPJ ao carregar

            // Se a mensagem for de erro, chama a função para limpar os campos após 3 segundos
            limparCamposErro();

            const mensagem = document.getElementById('mensagem');
            if (mensagem && mensagem.classList.contains('alert-success')) {
                esconderMensagem(); // Chama a função para esconder a mensagem e redirecionar se a mensagem for de sucesso
            }
        };
    </script>
    <style>
        .form-campo {
            width: 500px;
            margin-left: auto;
            margin-right: auto;
        }

        /* Estilo para o texto de "Selecione" em cinza */
        option[value=""]:disabled {
            color: gray;
        }

        /* Garantir que o texto de "Selecione" desapareça após clicar */
        select:focus option[value=""]:disabled {
            color: transparent;
        }

        /* Para garantir que o texto "Selecione" não seja exibido se o usuário já selecionou outra opção */
        select:focus option {
            color: black;
        }
    </style>
</head>
<br><br><br><br><br><br>
<br><br><br><br><br><br>
<body>
    <!-- Exibição da mensagem de sucesso ou erro -->
    <?php if ($mensagem != '') echo $mensagem; ?>
    <div class="container mt-4">
        <h2>Cadastro de Usuário</h2>
        <br>
        <form method="POST">
            <div class="mb-3">
                <label for="email" class="form-label">E-mail</label>
                <input type="email" class="form-control form-campo" id="email" name="email" value="<?php echo $email; ?>" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control form-campo" id="senha" name="senha" required>
            </div>
            <div class="mb-3">
                <label for="nome" class="form-label">Nome</label>
                <input type="text" class="form-control form-campo" id="nome" name="nome" required>
            </div>
            <div class="mb-3">
                <label for="dt_nascimento" class="form-label">Data de Nascimento</label>
                <input type="date" class="form-control form-campo" id="dt_nascimento" name="dt_nascimento" required>
            </div>
            <div class="mb-3">
                <label for="genero" class="form-label">Gênero</label>
                <select class="form-select form-campo" id="genero" name="genero" required>
                    <option value="" disabled selected>Selecione...</option> <!-- Opção inicial, desabilitada -->
                    <option value="M">Masculino</option>
                    <option value="F">Feminino</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="cpf" class="form-label">CPF</label>
                <input type="text" class="form-control form-campo" id="cpf" name="cpf" value="<?php echo $cpf; ?>" required>
                <span id="cpf_error_message" style="color: red; display: none;">CPF inválido</span>
            </div>
            <div class="mb-3">
                <label for="cnpj" class="form-label">CNPJ</label>
                <input type="text" class="form-control form-campo" id="cnpj" name="cnpj" value="<?php echo $cnpj; ?>" required>
                <span id="cnpj_error_message" style="color: red; display: none;">CNPJ inválido</span>
            </div>
            <div class="mb-3">
                <label for="tipo_telefone" class="form-label">Tipo de Telefone</label>
                <select class="form-select form-campo" id="tipo_telefone" name="tipo_telefone" required>
                    <option value="" disabled selected>Selecione...</option> <!-- Opção inicial, desabilitada -->
                    <option value="C">Comercial</option>
                    <option value="R">Residencial</option>
                    <option value="P">Pessoal</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="telefone" class="form-label">Telefone</label>
                <input type="text" class="form-control form-campo" id="telefone" name="telefone" required>
            </div>
            <button type="submit" class="btn btn-success" disabled>Salvar</button>
            <div class="mt-3 text-center">
                <a href="login.php">Já possuí cadastro? Faça Login</a>
            </div>
        </form>
    </div>

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
</body>
</html>
