<?php

// Inicia a sessão se ela ainda não estiver ativa.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Verifica se a variável 'usuario_logado' existe na sessão. Se não existir, significa que o usuário não se autenticou.
if (!isset($_SESSION['usuario_logado']) || $_SESSION['usuario_logado'] !== true) {
    
    // Redireciona o usuário para a página de login.
    header("Location: index.php?erro=restrito");
    
    // Encerra a execução do script para garantir que o redirecionamento ocorra imediatamente.
    exit();
}

// Se a sessão existir e 'usuario_logado' for true, a execução do script continua normalmente.
// Define um tempo limite de 30 minutos (1800 segundos).
$tempo_limite = 1800;

// Verifica se a variável de 'ultimo_acesso' existe na sessão.
if (isset($_SESSION['ultimo_acesso'])) {
    
    // Calcula o tempo decorrido desde o último acesso.
    $tempo_decorrido = time() - $_SESSION['ultimo_acesso'];
    
    // Se o tempo decorrido for maior que o limite, destrói a sessão.
    if ($tempo_decorrido > $tempo_limite) {
        session_unset();     // Remove todas as variáveis de sessão.
        session_destroy();   // Destrói a sessão.
        
        // Redireciona para o login com uma mensagem de sessão expirada.
        header("Location: index.php?erro=expirou");
        exit();
    }
}

// Atualiza o tempo do último acesso para o tempo atual, renovando a sessão.
$_SESSION['ultimo_acesso'] = time();

?>