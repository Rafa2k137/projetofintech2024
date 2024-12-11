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
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
    <link rel="stylesheet" href="estilos.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>
        // Função que exibe a mensagem de boas-vindas por 3 segundos e carrega o conteúdo de "Quem Somos"
        window.onload = function() {
            // Exibir a mensagem de boas-vindas
            document.getElementById('welcome-message').style.display = 'block';

            // Após 3 segundos, esconder a mensagem de boas-vindas e mostrar o conteúdo de "Quem Somos"
            setTimeout(function() {
                document.getElementById('welcome-message').style.display = 'none';
                document.getElementById('about-us').style.display = 'block';
            }, 3000);
        };
    </script>
</head>
<br><br><br><br>
<br><br><br><br>
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

    <br><br><br><br>

    <div class="container mt-4">
        <!-- Mensagem de boas-vindas -->
        <div id="welcome-message" class="alert alert-success" style="display:none;">
           <h2>Olá, <?php echo $_SESSION['nome']; ?>!</h2>
           <p>Seja Bem-vindo(a) a Tech-Fin!</p>
       </div>

       <!-- Conteúdo de "Quem Somos" -->
       <div id="about-us" style="display:none;">
        <h2>Quem Somos</h2>
        <br>
        <p>Na Tech-Fin, acreditamos que o futuro das finanças está na inovação e na simplicidade. Somos uma empresa dedicada a transformar a gestão financeira de empreendedores e pequenas empresas, oferecendo uma plataforma moderna, acessível e inteligente que vai além das soluções financeiras tradicionais. Nosso produto é uma solução completa para gestão financeira, economia e expansão de negócios, que permite controlar e planejar as finanças de forma intuitiva e eficiente.</p>
        <p>Em um cenário onde as soluções bancárias tradicionais frequentemente falham em atender as necessidades reais dos empreendedores, a Tech-Fin se destaca por proporcionar soluções rápidas, práticas e fáceis de usar. Nossa plataforma oferece dashboards dinâmicos, relatórios analíticos detalhados e uma integração inteligente com dados contábeis e bancários, garantindo o controle total das finanças e o acompanhamento estratégico do negócio.</p>
        <h2>Visão</h2>
        <br>
        <p>Ser a plataforma financeira de referência para pequenas e médias empresas, capacitando empreendedores com as ferramentas necessárias para tomar decisões mais informadas, expandir seus negócios e atingir o sucesso a longo prazo, de maneira sustentável.</p>
        <h2>Missão</h2>
        <br>
        <p>Revolucionar a gestão financeira de pequenos negócios, oferecendo soluções que simplificam a complexidade do controle financeiro, permitem o planejamento estratégico e promovem o crescimento e a saúde econômica das empresas, contribuindo para a prosperidade de cada cliente.</p>
        <h2>Valores</h2>
        <br>
        <p><strong>. Inovação:</strong> Estamos sempre em busca de novas soluções que atendam às necessidades de nossos clientes.</p>
        <p><strong>. Simplicidade:</strong> Tornamos a gestão financeira mais acessível e fácil de usar.</p>
        <p><strong>. Eficiência:</strong> Priorizamos a otimização de processos e a entrega de resultados concretos.</p>
        <p><strong>. Proximidade:</strong> Buscamos sempre entender as necessidades de nossos clientes e oferecer um suporte personalizado.</p>
        <p>Com a Tech-Fin, o gerenciamento financeiro se torna mais acessível, eficiente e produtivo, criando um ambiente favorável para o crescimento e sucesso de cada negócio.</p>
    </div>
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
