<?php
session_start();
header('Content-type: text/html; charset=ISO-8895-1');
include_once "../DB/conexaoSQL.php";
include_once "../DB/acoes.php";
include_once "../Config.php";

$estado = $_POST['estado'];
$local = $_POST['local'];
$email = $_POST['e-mail'];
$contpb = $_POST['contador'];
$serie = $_POST['serie'];
$whatsapp = $_POST['whatsapp'];
$solicitante = $_POST['solicitante'];
$defeito = $_POST['defeito'];
$trava =

    $_SESSION['travaSes'] = $_POST['trava'];

$trava = $_SESSION['travaSes'];

if (isset($serie) /* && $trava == 1 */) {
    gravaOS($conn, $estado, $local, $email, $contpb, $serie, $whatsapp, $solicitante, $defeito);
    $_SESSION['travaSes'] = NULL;
} else {
    return;
}

/* Pega o ultimo numero de OS aberto */
$sql = "SELECT TOP 1 
        TB02115_CODIGO numOS 
        FROM TB02115 
        ORDER BY TB02115_DTCAD DESC
    ";
$stmt = sqlsrv_query($conn, $sql);

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $numOS .= $row['numOS'];

}
/* Grava o histórico do primeiro status na abertura */
gravaHistorico($conn, $numOS, $serie, $defeito, $statusInicial);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DATABIT</title>
    <link rel="stylesheet" href="../CSS/style.css">
    <style>
        .div-form{
            filter: blur(10px);
        }
    </style>
</head>

<body>
    <div class="div-save">
        <form class="form-voltar" id="form-voltar" action="<?= $url ?>/inputSerie.php">
            <!-- <img src="../img/logo.jpg" alt="logo"> -->
            <h1>OS <b class="OSCriada"><?= $numOS ?></b> ABERTA!</h1>
            <button onclick="window.location.reload()" type="submit" class="popup-btn">Fechar</button>
        </form>
    </div>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../JS/script.js" charset="utf-8"></script>
</body>

</html>