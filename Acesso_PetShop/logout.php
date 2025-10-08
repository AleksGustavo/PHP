<?php

// Inicia a sessão para garantir que ela possa ser destruída.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// 1. Remove todas as variáveis de sessão.
session_unset();

// Isso destrói o arquivo da sessão no servidor e remove o ID da sessão do navegador.
session_destroy();

//Redireciona o usuário para a página de login com uma mensagem de sucesso.
header("Location: index.php?logout=sucesso");

// Encerra a execução do script.
exit();
?>