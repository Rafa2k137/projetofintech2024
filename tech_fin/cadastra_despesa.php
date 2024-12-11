<?php
session_start();
if (!isset($_SESSION['email'])) {
    header("Location: login.php");
    exit;
}

// Definindo variáveis para limpar os campos após o envio
$nota_fiscal = '';
$dt_emissao = '';
$cpf_fornecedor = '';
$cnpj_fornecedor = '';
$quantidade = '';
$vl_unitario = '';
$mensagem = '';

// Verifica se o formulário foi enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('config.php');

    // Verificação para garantir que o id_usuario está disponível na sessão
    if (!isset($_SESSION['id_usuario'])) {
        echo "<div class='alert alert-danger'>Erro: ID do usuário não encontrado na sessão.</div>";
        exit;
    }

    $id_usuario = $_SESSION['id_usuario'];  // Acessa o ID do usuário armazenado na sessão
    $nota_fiscal = $_POST['nota_fiscal'];
    $dt_emissao = $_POST['dt_emissao'];
    $cpf_fornecedor = $_POST['cpf_fornecedor'];
    $cnpj_fornecedor = $_POST['cnpj_fornecedor'];
    $quantidade = $_POST['quantidade'];
    $vl_unitario = $_POST['vl_unitario'];
    $vl_total = $quantidade * $vl_unitario;  // Cálculo de vl_total

    // Verifica se o ID do usuário existe na tabela usuario
    $sql_verifica_usuario = "SELECT id_usuario FROM usuario WHERE id_usuario = '$id_usuario'";
    $resultado = $conn->query($sql_verifica_usuario);
    if ($resultado->num_rows == 0) {
        echo "<div class='alert alert-danger'>Erro: ID de usuário não encontrado no banco de dados.</div>";
        exit;
    }

    // Inserção no banco de dados
    $sql = "INSERT INTO despesa (id_usuario, nota_fiscal, dt_emissao, cpf_fornecedor, cnpj_fornecedor, quantidade, vl_unitario, vl_total)
    VALUES ('$id_usuario', '$nota_fiscal', '$dt_emissao', '$cpf_fornecedor', '$cnpj_fornecedor', '$quantidade', '$vl_unitario', '$vl_total')";

    if ($conn->query($sql) === TRUE) {
        // Mensagem de sucesso armazenada na sessão
        $_SESSION['mensagem'] = "<div id='mensagem' class='alert alert-success'>Despesa cadastrada com sucesso!</div>";
        
        // Limpa os campos após a inserção
        $nota_fiscal = '';
        $dt_emissao = '';
        $cpf_fornecedor = '';
        $cnpj_fornecedor = '';
        $quantidade = '';
        $vl_unitario = '';
    } else {
        // Mensagem de erro armazenada na sessão
        $_SESSION['mensagem'] = "<div id='mensagem' class='alert alert-danger'>Erro ao cadastrar despesa: " . $conn->error . "</div>";
    }

    // Redireciona para a própria página para evitar o resubmissão do formulário
    header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

// Recupera a mensagem da sessão, se houver
if (isset($_SESSION['mensagem'])) {
    $mensagem = $_SESSION['mensagem'];
    unset($_SESSION['mensagem']);  // Limpa a mensagem após exibição
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastrar Despesa</title>
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
            const cpf = document.getElementById('cpf_fornecedor').value;
            const cnpj = document.getElementById('cnpj_fornecedor').value;
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

        // Função para tratar a máscara e validação em tempo real para CPF e CNPJ
        function tratarCampos() {
            const cpfInput = document.getElementById('cpf_fornecedor');
            const cnpjInput = document.getElementById('cnpj_fornecedor');

            cpfInput.addEventListener('input', function() {
                // Aplica a máscara de CPF enquanto digita
                this.value = mascaraCPF(this.value);
            });

            cnpjInput.addEventListener('input', function() {
                // Aplica a máscara de CNPJ enquanto digita
                this.value = mascaraCNPJ(this.value);
            });

            // Valida CPF e CNPJ quando o campo perde o foco
            cpfInput.addEventListener('blur', validarFormulario);
            cnpjInput.addEventListener('blur', validarFormulario);
        }

        // Função para esconder a mensagem após 3 segundos
        function esconderMensagem() {
            setTimeout(function() {
                const mensagem = document.getElementById('mensagem');
                if (mensagem) {
                    mensagem.style.display = 'none';
                }
            }, 3000); // 3 segundos
        }

        // Inicializa a validação dos campos ao carregar a página
        window.onload = function() {
            tratarCampos(); // Inicia a função de CPF e CNPJ ao carregar

            // Se houver mensagem de sucesso, chama a função para escondê-la após 3 segundos
            const mensagem = document.getElementById('mensagem');
            if (mensagem) {
                esconderMensagem();
            }
        };
    </script>
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
                        <a class="nav-link" href="index.php"><strong>Home</strong></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="recibo.php"><strong>Recibos</strong></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="despesa.php"><strong>Despesas</strong></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php"><strong>Sair</strong></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>
    <br><br><br><br>
    <br><br><br><br><br>
    <div class="container mt-4">
        <h2>Incluir Despesa</h2>
        <br>

        <!-- Exibição da mensagem de sucesso -->
        <?php if ($mensagem != '') echo $mensagem; ?>

        <form method="POST">
            <!-- Campos do formulário de despesa -->
            <div class="mb-3">
                <label for="nota_fiscal" class="form-label">Nota Fiscal</label>
                <input type="text" class="form-control" id="nota_fiscal" name="nota_fiscal" value="<?php echo $nota_fiscal; ?>" required>
            </div>
            <div class="mb-3">
                <label for="dt_emissao" class="form-label">Data de Emissão</label>
                <input type="date" class="form-control" id="dt_emissao" name="dt_emissao" value="<?php echo $dt_emissao; ?>" required>
            </div>
            <div class="mb-3">
                <label for="cpf_fornecedor" class="form-label">CPF do Fornecedor</label>
                <input type="text" class="form-control" id="cpf_fornecedor" name="cpf_fornecedor" value="<?php echo $cpf_fornecedor; ?>" required>
                <div id="cpf_error_message" class="text-danger" style="display: none;">CPF inválido</div>
            </div>
            <div class="mb-3">
                <label for="cnpj_fornecedor" class="form-label">CNPJ do Fornecedor</label>
                <input type="text" class="form-control" id="cnpj_fornecedor" name="cnpj_fornecedor" value="<?php echo $cnpj_fornecedor; ?>" required>
                <div id="cnpj_error_message" class="text-danger" style="display: none;">CNPJ inválido</div>
            </div>
            <div class="mb-3">
                <label for="quantidade" class="form-label">Quantidade</label>
                <input type="number" class="form-control" id="quantidade" name="quantidade" value="<?php echo $quantidade; ?>" required>
            </div>
            <div class="mb-3">
                <label for="vl_unitario" class="form-label">Valor Unitário</label>
                <input type="number" class="form-control" id="vl_unitario" name="vl_unitario" value="<?php echo $vl_unitario; ?>" required>
            </div>
            <button type="submit" class="btn btn-success" disabled>Salvar</button>
            <br><br>
            <div class="mt-3 text-center">
             <a href="consulta_despesa.php">Consultar Despesas</a>
         </div>
     </form>
 </div>

 <!-- Bootstrap JS e estilo.css -->
 <link rel="stylesheet" href="estilos.css">
 <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

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
