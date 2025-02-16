<?php

    include 'inc/includes.php';
    
    session_start();
    
    loginObrigatorio();

    $usuario = new UsuarioPedrina();
    $usuario = $_SESSION['usuario'];
    
    verificarPermissaoAcesso(Perfil::retornaDadosPerfil($usuario->getIdPerfil())->getDescricao(),['Administrador'],"../paginaInicial.php"); //Apenas os perfis ao lado podem acessar a página    
    
        
?>
<!DOCTYPE html>
<html lang="pt-br">
    <head>
        <meta charset="UTF-8">
        <meta name="author" content="José Sueney de Lima">
        <meta name="keywords" content="Evento, IFRN-SC, IFRN, Santa Cruz, sistema">
        <meta name="description" content="Página inicial do sistema de submissão de trabalhos do IFRN campus Santa Cruz">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>SS - Distribuir Avaliadores</title>
        <?php
            include 'inc/css.php';
            include 'inc/javascript.php';
        ?>
        
    </head>
    
    <body>
        
        <?php 
            include 'inc/pInicial.php';
            include 'inc/modal.php';
        ?>
        
        <fieldset>
            <h3 align='center'>Distribuir Avaliadores</h3>
            
            <form method="post" action="<?=htmlspecialchars('submissaoForms/wsDitribuirAvaliacoes.php');?>">
                
                <table align='center'>
                    <tr>
                        <th class='direta'>Selecione o Tipo de Avaliação: </th>
                        <td>
                            <select class='campoDeEntrada' id="select-tipoAvaliacao" name="select-tipoAvaliacao" onchange="loadInfoEvento(document.getElementById('select-Eventos').value,this.value,'info-evento')">
                                <?php
                                    foreach (TipoSubmissao::listaTipoSubmissoes() as $tipo) {
                                        echo "<option value='".$tipo->getId() . "'>" . $tipo->getDescricao() . "</option>";
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th class='direta'>Selecione o Evento: </th>
                        <td>
                            <select class='campoDeEntrada' id="select-Eventos" name="select-Eventos" onchange="loadInfoEvento(this.value,document.getElementById('select-tipoAvaliacao').value,'info-evento')">
                                <option value="">Selecione um evento</option>
                                <?php
                                    foreach (Evento::listaEventos() as $evento) {
                                        echo "<option value='".$evento->getId() . "'>" . $evento->getNome() . "</option>";
                                    }
                                ?>
                            </select>
                        </td>
                    </tr>
                        
                </table>
                <br>
                <div id="info-evento" name="info-evento" align="center"></div>
           </form>
        </fieldset>
            
        <?php 
                include dirname(__FILE__) . '/inc/pFinal.php'; 
            ?>
    </body>
</html>