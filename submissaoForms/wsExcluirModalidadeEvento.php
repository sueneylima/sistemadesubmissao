<?php

    include dirname(__DIR__) . '/inc/includes.php';
    
    session_start();
    
    loginObrigatorio();

    $usuario = new UsuarioPedrina();
    $usuario = $_SESSION['usuario'];

    verificarPermissaoAcesso(Perfil::retornaDadosPerfil($usuario->getIdPerfil())->getDescricao(),['Administrador'],"../paginaInicial.php"); //Apenas os perfis ao lado podem acessar a página
            
    $metodoHttp = filter_input(INPUT_SERVER, 'REQUEST_METHOD');

    date_default_timezone_set('America/Sao_Paulo');

    if ($metodoHttp === 'GET') {
        try {
            
            $idModalidade = $_GET['id'];

           
            $modalidade = ModalidadeEvento::retornaDadosModalidadeEvento($idModalidade);

            
            if ($modalidade->getId()!="") {
                if (ModalidadeEvento::excluirModalidadeEvento($idModalidade)) {
                    header("Location: ../gerenciarEventos.php?Item=Excluido");
                }
                else {
                    header("Location: ../gerenciarEventos.php?Item=NaoExcluido");
                }
            }
            else {
                echo "<script>window.alert('Modalidade Inválida!');window.history.back();</script>";
            }
        } catch (Exception $e) {
            $e->getMessage();
        }

    } else {
        //$_SESSION['msg'] = "Você deve fazer login no sistema";
        header('Location: ../index.php');
    }

?>