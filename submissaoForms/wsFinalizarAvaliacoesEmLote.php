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
        
        $p = filter_input_array(INPUT_POST);
        
        if (!isset($p['submissoes'])) {
            header('Location: ../finalizarAvaliacoesEmLote.php?Item=Atualizado');
        }
        
        else {
            foreach ($p['submissoes'] as $idSubmissao) {
                if (Submissao::finalizarSubmissao($idSubmissao)) {
                    $submissao = Submissao::retornaDadosSubmissao($idSubmissao);
                    $emails = array();
                    foreach (UsuariosDaSubmissao::listaUsuariosDaSubmissaoComFiltro($submissao->getId(), '', '','','') as $userSubmissao) {
                        array_push($emails, UsuarioPedrina::retornaDadosUsuario($userSubmissao->getIdUsuario())->getEmail());
                    }
                    // REVER
                    emailFinalizacaoSubmissao($submissao,$emails); // Envio de email para os usuários saberem que a submissão foi finalizada

                    if (in_array($submissao->getIdTipoSubmissao(), array(1,2)) && $submissao->getIdSituacaoSubmissao()==4) { /* Caso seja uma submissão Parcial/Corrigida e tenha sido considerada Aprovada depois
                                                                                                                              das devidas correções, é gerada uma submissão Final automaticamente */
                            $evento = Evento::retornaDadosEvento($submissao->getIdEvento());
                            $modalidade = Modalidade::retornaDadosModalidade($submissao->getIdModalidade());
                            $novoArquivo = $evento->getNome() . "-" . $modalidade->getDescricao() . "-" . substr(md5(time()), 0,15) . "-Final.pdf";
                            $idUsuariosAdd = "";

                            foreach (UsuariosDaSubmissao::listaUsuariosDaSubmissaoComFiltro($submissao->getId(), '', '','','') as $user) {
                                $idUsuariosAdd .= $user->getIdUsuario() . ";";
                            }


                            if (Submissao::adicionarSubmissao($submissao->getIdEvento(), $submissao->getIdArea(), $submissao->getIdModalidade(),3,3,$novoArquivo,$submissao->getTitulo(),
                                                              $submissao->getResumo(),$submissao->getPalavrasChave(),$submissao->getRelacaoCom(),$idUsuariosAdd,$submissao->getId())) {

                                copy(dirname('__DIR__') . '/' . $pastaSubmissoes . $submissao->getArquivo(), dirname('__DIR__') . '/' . $pastaSubmissoes . $novoArquivo);

                                $novosAvaliadores = "";
                                $emails = array();

                                $prazo = Evento::retornaDadosEvento($submissao->getIdEvento())->getPrazoFinalEnvioAvaliacaoFinal();
                                $avaliadoresAnteriores = Avaliacao::listaAvaliacoesComFiltro('', $submissao->getId(), '');

                                foreach ($avaliadoresAnteriores as $avaliador) { 
                                    $novosAvaliadores .= $avaliador->getIdUsuario() . ";";
                                    array_push($emails, UsuarioPedrina::retornaDadosUsuario($avaliador->getIdUsuario())->getEmail());   
                                }

                                $sub = Submissao::retornaDadosSubmissao(Submissao::retornaIdUltimaSubmissao());



                                if (Avaliacao::adicionarAvaliacoes($sub->getId(), 3, $submissao->getIdModalidade(), $novosAvaliadores, $prazo)) {
				// RETIRADO MOMENTANEAMENTE O ENVIO DE EMAILS PARA OS AVALIADORES	
				// emailAtribuicaoAvaliacao($sub, $prazo, $emails);
                                    header('Location: ../finalizarAvaliacoesEmLote.php?Item=Atualizado');
                                }
                                else header('Location: ../finalizarAvaliacoesEmLote.php?Item=NaoAtualizado');

                            }
                            else {
                                echo "OCORREU UM ERRO. CONTACTE O ADMINISTRADOR";
                                exit(1);
                            }
                            //GERA UMA NOVA SUBMISSAO

                        }
                        else if ($submissao->getIdTipoSubmissao()==3 && $submissao->getIdSituacaoSubmissao()==7) { /* Caso seja uma submissão Final e todos os avaliadores tenham terminado
                                                                                                                a avaliação, são gerados certificados de Apresentação para os submissores */

                          //  foreach(UsuariosDaSubmissao::listaUsuariosDaSubmissaoComFiltro($submissao->getId(), '', '','','') as $user) {
                          //      $evento = Evento::retornaDadosEvento($submissao->getIdEvento());
                          //      gerarCertificado($evento, Usuario::retornaDadosUsuario($user->getIdUsuario()),1,$pastaCertificados);
                          //  }
                        }
                       // else {echo "PQP"; exit(1);}
                }
                else echo "Erro na Finalização de uma submissão";
            }
            header('Location: ../finalizarAvaliacoesEmLote.php?Item=Atualizado');
        }
    }
    else echo "<script>window.alert('Erro no Envio de Submissões!');window.history.back();</script>";
?>
