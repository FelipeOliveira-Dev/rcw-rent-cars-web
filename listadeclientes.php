<!--
=========================================================
 Light Bootstrap Dashboard - v2.0.1
=========================================================

 Product Page: https://www.creative-tim.com/product/light-bootstrap-dashboard
 Copyright 2019 Creative Tim (https://www.creative-tim.com)
 Licensed under MIT (https://github.com/creativetimofficial/light-bootstrap-dashboard/blob/master/LICENSE)

 Coded by Creative Tim

=========================================================

 The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.  -->
<?php
    include('classes/conexao.class.php');
    include('classes/clientes.class.php');
    include('classes/helpper.class.php');
    $pdo = new Conexao;
    $pdo = $pdo->conecta();

    session_start();
    if(isset($_SESSION['cpf']) AND isset($_SESSION['senha'])){
        $select = $pdo->prepare("SELECT nivel, nome_func FROM funcionarios WHERE cpf = :cpf AND senha = :senha AND nivel <= 2");
        $select->bindValue(':cpf', $_SESSION['cpf']);
        $select->bindValue(':senha', $_SESSION['senha']);
        $select->execute();
        $dadosLogin = $select->fetch(PDO::FETCH_ASSOC);
        define("NOME", $dadosLogin['nome_func']);
        define("NIVEL", $dadosLogin['nivel']);

        if($select->rowCount() == 0){
            session_destroy();
            header('location:index.php');
        }
    } else{
        session_destroy();
        header('location: index.php');
    }
    $acao = (isset($_REQUEST['acao'])?$_REQUEST['acao']:null);
    if ($acao == "sair") {
        session_destroy();
        header("location:index.php");
    }
    
    $clientes = new Clientes($pdo);
    $acao = (isset($_REQUEST['acao'])?$_REQUEST['acao']:null);
    if($acao == 'excluircliente'){
        $clientes->apagarClientes();
        header("Location: listadeclientes.php");
    }
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <link rel="apple-touch-icon" sizes="76x76" href="assets/img/apple-icon.png">
    <link rel="icon" type="image/png" href="assets/img/carro.png">
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <title>Sistema RCW - Rent Cars Web</title>
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <!--     Fonts and icons     -->
    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/latest/css/font-awesome.min.css" />
    <!-- CSS Files -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet" />
    <link href="assets/css/light-bootstrap-dashboard.css?v=2.0.0 " rel="stylesheet" />
    <!-- CSS Just for demo purpose, don't include it in your project -->
    <link href="assets/css/demo.css" rel="stylesheet" />

</head>

<body>
    <div class="wrapper">
        <div class="sidebar" data-image="assets/img/menu/audi.jpg" data-color="blue">
            <!--
        Tip 1: You can change the color of the sidebar using: data-color="purple | blue | green | orange | red"

        Tip 2: you can also add an image using data-image tag
    -->
            <div class="sidebar-wrapper">
                <div class="logo">
                    <label class="simple-text">Sistema RCW</label>
                </div>
                <ul class="nav">
                    <li>
                        <a class="nav-link" href="paginicial.php">
                            <i class="nc-icon nc-bank"></i>
                            <p>Início</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="./aluguel.php">
                            <i class="nc-icon nc-bus-front-12"></i>
                            <p>Aluguel</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="./cadastroc.php">
                            <i class="nc-icon nc-single-02"></i>
                            <p>Cadastro de Clien.</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="./cadastrov.php">
                            <i class="nc-icon nc-delivery-fast"></i>
                            <p>Cadastro de Veíc.</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="./cadastrof.php">
                            <i class="nc-icon nc-badge"></i>
                            <p>Cadastro de Func.</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="./listadealugueis.php">
                            <i class="nc-icon nc-notes"></i>
                            <p>Lista de Aluguéis</p>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="./listadeclientes.php">
                            <i class="nc-icon nc-single-02"></i>
                            <p>Lista de Clientes</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="./listadeveiculos.php">
                            <i class="nc-icon nc-delivery-fast"></i>
                            <p>Lista de Veículos</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="./listadefuncionarios.php">
                            <i class="nc-icon nc-badge"></i>
                            <p>Lista de Func.</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="./relatorios.php">
                            <i class="nc-icon nc-paper-2"></i>
                            <p>Relatórios</p>
                        </a>
                    </li>
                    <li>
                        <a class="nav-link" href="?acao=sair">
                            <i class="nc-icon nc-key-25"></i>
                            <p>Sair</p>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="main-panel">
            <!-- Navbar -->
            <nav class="navbar navbar-expand-lg " color-on-scroll="500">
                <div class="container-fluid">
                    <span class="navbar-brand"> Lista de Veículos </span>
                </div>
            </nav>
            <!-- End Navbar -->
            <div class="content">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="card strpied-tabled-with-hover">
                                <div class="card-header ">
                                    <h4 class="card-title">Clientes cadastrados no Sistema</h4>
                                    <?php
                                        if ($acao == 'realizaredicao') {
                                            $clientes->editarCliente();
                                        }
                                    ?>
                                    
                                </div>
                                <div class="card-body table-full-width table-responsive">
                                    <table class="table table-hover table-striped">
                                        <thead class="thead-dark">
                                            <tr>
                                                <th>ID</th>
                                                <th>Nome</th>
                                                <th>CPF</th>
                                                <th>Data de Nascimento</th>
                                                <th>Telefone</th>
                                                <th>Ações Possíveis</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if($clientes->listarClientes() != null){
                                                foreach ($clientes->listarClientes() as $dados) {
                                                echo "<tr>
                                                    <td>$dados->idCliente</td>
                                                    <td>$dados->nome</td>
                                                    <td>$dados->cpf</td>
                                                    <td>".Helpper::dataBR($dados->data_nasc)."</td>
                                                    <td>$dados->telefone</td>
                                                    <td>
                                                    <a href='?acao=editarcliente&idCliente=$dados->idCliente'><button class='btn btn-warning'>Atualizar</button></a>
                                                    <a href='?acao=excluircliente&idCliente=$dados->idCliente'><button class='btn btn-danger'>Excluir</button></a>
                                                    </td>
                                                </tr>";
                                                }
                                            }else{
                                                echo "<h3 class='alert alert-danger'>Ainda não ha dados a serem mostrados.</h3>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                            <?php  
                                if ($acao == 'editarcliente') {
                                    include('editarcliente.php');
                                }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <footer class="footer">
                <div class="container-fluid">
                    <nav>
                        <ul class="footer-menu">
                            <li>
                                <a href="#">
                                    Home
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Company
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Portfolio
                                </a>
                            </li>
                            <li>
                                <a href="#">
                                    Blog
                                </a>
                            </li>
                        </ul>
                        <p class="copyright text-center">
                            ©
                            <script>
                                document.write(new Date().getFullYear())
                            </script>
                            <a href="">Sistema SLR</a>
                        </p>
                    </nav>
                </div>
            </footer>
        </div>
    </div>
</body>
<!--   Core JS Files   -->
<script src="assets/js/core/jquery.3.2.1.min.js" type="text/javascript"></script>
<script src="assets/js/core/popper.min.js" type="text/javascript"></script>
<script src="assets/js/core/bootstrap.min.js" type="text/javascript"></script>
<script src="assets/js/jquery.mask.min.js" type="text/javascript"></script>
<script src="assets/js/mascarafunc.js" type="text/javascript"></script>
<script type="text/javascript">
    mascara();
</script>
<!--  Plugin for Switches, full documentation here: http://www.jque.re/plugins/version3/bootstrap.switch/ -->
<script src="assets/js/plugins/bootstrap-switch.js"></script>
<!--  Google Maps Plugin    -->
<script type="text/javascript" src="https://maps.googleapis.com/maps/api/js?key=YOUR_KEY_HERE"></script>
<!--  Chartist Plugin  -->
<script src="assets/js/plugins/chartist.min.js"></script>
<!--  Notifications Plugin    -->
<script src="assets/js/plugins/bootstrap-notify.js"></script>
<!-- Control Center for Light Bootstrap Dashboard: scripts for the example pages etc -->
<script src="assets/js/light-bootstrap-dashboard.js?v=2.0.0 " type="text/javascript"></script>
<!-- Light Bootstrap Dashboard DEMO methods, don't include it in your project! -->
<script src="assets/js/demo.js"></script>

</html>
