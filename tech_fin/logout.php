<?php
session_start();  // Inicia a sessão

// Verifica se a sessão está ativa
if (isset($_SESSION['email'])) {
    // Finaliza a sessão
    session_unset();  // Remove todas as variáveis da sessão
    session_destroy();  // Destrói a sessão

    // Redireciona o usuário para a página de login
    header("Location: login.php");
    exit;
} else {
    // Caso a sessão já tenha sido finalizada ou o usuário não esteja logado
    header("Location: login.php");
    exit;
}
?>