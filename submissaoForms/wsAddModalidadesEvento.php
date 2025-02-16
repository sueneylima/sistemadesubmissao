<?php

    include dirname(__DIR__) . '/inc/includes.php';
    
    session_start();
    
    loginObrigatorio();

    $usuario = new UsuarioPedrina();
    $usuario = $_SESSION['usuario'];

    verificarPermissaoAcesso(Perfil::retornaDadosPerfil($usuario->getIdPerfil())->getDescricao(),['Administrador'],"../paginaInicial.php"); //Apenas os perfis ao lado podem acessar a página
            
    $metodoHttp = filter_input(INPUT_SERVER, 'REQUEST_METHOD');

    date_default_timezone_set('America/Sao_Paulo');

    if ($metodoHttp === 'POST') {
        try {
            $p = filter_input_array(INPUT_POST);    

            
            $idEvento = $p['idEvento'];
            $modalidadesAdd = "";
            
            if (isset($p['modalidades'])) {
                foreach ($p['modalidades'] as $modalidade) {
                    $modalidadesAdd = $modalidadesAdd . $modalidade . ";";
                }
            }
            else header('Location: ../gerenciarEventos.php?');
          
            
            if (ModalidadeEvento::adicionarModalidadeEvento($idEvento, $modalidadesAdd)) {
                header('Location: ../gerenciarEventos.php?Item=Criado');
            }
            else echo "<script>window.alert('Houve um erro na Inserção. Tente adicionar posteriormente')</script>";
            
            
        } catch (Exception $e) {
            $e->getMessage();
        }

    } else {
        //$_SESSION['msg'] = "Você deve fazer login no sistema";
        header('Location: ../index.php');
    }

?>