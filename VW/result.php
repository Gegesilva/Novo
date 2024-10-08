<?php
header('Content-type: text/html; charset=ISO-8895-1');
include_once "../DB/conexaoSQL.php";
include_once "../DB/filtros.php";
include_once "../Config.php";


/* $serie = $_POST['serie']; */
$estado = $_POST['estado'];
$pessoa = $_POST['pessoa'];
$consumo = $_POST['consumo'];
$condicao = $_POST['condicao'];
$tabelaCust = $_POST['tabela'];
$cliente = $pessoa . ':' . $estado;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $series = explode(',', $_POST['selecionado']); // Converte a string em array
    /* print_r($series); */ // Exibe o array
}

/* Pega o codigo da cindição pelo nome passado pelo input da lista */
$sql = "SELECT TB01014_CODIGO Cod FROM TB01014
        WHERE TB01014_NOME = '$condicao'";

$stmt = sqlsrv_query($conn, $sql);

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $codCond = $row['Cod'];
}

/* Pega o codigo da tabela pelo nome passado pelo input da lista */
$sql = "SELECT TB01020_CODIGO Cod FROM TB01020
        WHERE TB01020_NOME = '$tabelaCust'
        AND TB01020_SITUACAO = 'A'";

$stmt = sqlsrv_query($conn, $sql);

while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $tabelaCustCod = $row['Cod'];
}

/* Da o nome correto para a variavel consumo */
if($consumo == 'N'){
    $nomeConsumo = 'Revenda';
}else{
    $nomeConsumo = 'Consumo';
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DATABIT</title>
    <link rel="stylesheet" href="../CSS/styleResult.css">
    <link rel="stylesheet" href="../CSS/styleBtn.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
</head>

<body>
            <div class="cabecalho-result">
                <img class="logo" src="../img/logo.jpg" alt="logo">
                <div class="info-bloco">
                    <div>
                        <span><b>Estado:</b> <?= $estado; ?></span>
                        <span><b>Pessoa</b>: <?= $cliente; ?></span>
                    </div>
                    <div>
                        <span><b>Tipo Consumo:</b> <?= $nomeConsumo; ?></span>
                        <span><b>Condição:</b> <?= $condicao; ?></span>
                    </div>
                    <div>
                        <span><b>Tabela:</b> <?= $tabelaCust; ?></span>
                    </div>
                </div>
            </div>

            <h6 class="msg-os"></h6>
            <div class="form-group">
                <div class="form-input">
                    <table id="minhaTabela" class="table table-sm" style="font-size: 12px;">
                        <thead>
                            <tr>
                                <th>PRODUTO</th>
                                <th>REFERENCIA</th>
                                <th>VALOR BASE</th>
                                <th>FATOR</th>
                                <th>VALOR IPI</th>
                                <th>ST</th>
                                <th>DIFAL ST</th>
                                <th id="valor1">VALOR FINAL</th>
                                <th>MEDIDOR PB</th>
                                <th>MEDIDOR COLOR</th>
                                <th>MEDIDOR TOTAL</th>
                            </tr>
                        </thead>
                        <?php
                        /* if ($_SERVER['REQUEST_METHOD'] === 'POST') { */
                        // Verifica se alguma célula foi selecionada
                        if (isset($series) && is_array($series)) {
                            // Recupera os valores das células selecionadas
                            $selecionados = $series;

                            foreach ($selecionados as $item) {
                                $serie = htmlspecialchars($item);
                                $sql = "-- Parametros de Entrada
                                        DECLARE @EMPRESA VARCHAR(2); -- C�digo da Empresa
                                        DECLARE @SERIAL VARCHAR(30); -- NUMERO DE SERIE
                                        DECLARE @PRODUTO VARCHAR(5); -- C�digo do Produto
                                        DECLARE @OPERACAO VARCHAR(2); -- C�digo Opera��o de Venda
                                        DECLARE @CONDICAO VARCHAR(3); -- C�digo Condi��o de Pagamento
                                        DECLARE @CLIENTE VARCHAR(8); -- C�digo do cliente (ZZZZZZZZ, para um cliente gen�rico)(F:SP, F:RJ, F:UF - PARA PESSOA FISICA)(J:SP, J:RJ, J:UF - PARA PESSOA JURIDICA)
                                        DECLARE @VENDACONS VARCHAR(1); -- Venda para Consumo (S ou N)
                                        DECLARE @TABELA VARCHAR(2); -- C�digo da Tabela de Pre�os
                                        DECLARE @VALORINICIAL NUMERIC(11,2) -- Valor Inicial (preencher com zero)
                                        DECLARE @VALORBASE NUMERIC(11,2) -- Valor Inicial (preencher com zero)
                                        
                                        -- Sintaxe 
                                        SELECT @SERIAL = '$serie' -- Pedir no PHP
                                        SELECT @EMPRESA = (SELECT top 1 TB02054_CODEMP FROM TB02054 WHERE TB02054_NUMSERIE = @SERIAL AND TB02054_QTPROD > TB02054_QTPRODS) 
                                        SELECT @PRODUTO = (select top 1 TB02054_PRODUTO FROM TB02054 WHERE TB02054_NUMSERIE = @SERIAL and TB02054_CODEMP = @EMPRESA AND TB02054_QTPROD > TB02054_QTPRODS) 
                                        SELECT @OPERACAO = '00'; --Fixo 00
                                        SELECT @CONDICAO = '$codCond'; -- Lista suspensa da TB01014
                                        SELECT @CLIENTE = '$cliente'; -- F: ou J: + estado
                                        SELECT @VENDACONS = '$consumo'; --'N' REVENDA / 'S' CONSUMO
                                        SELECT @TABELA = '$tabelaCustCod'; 
                                        SELECT @VALORINICIAL = 0; -- FIXAR 0
                                        
                                        select 
                                        
                                        @VALORBASE = VALOR * (SELECT TOP 1 TB02054_FATOR FROM TB02054 WHERE TB02054_PRODUTO = @PRODUTO AND TB02054_CODEMP = @EMPRESA AND TB02054_NUMSERIE = @SERIAL)
                                        
                                        from FT02002(@EMPRESA,@PRODUTO,@OPERACAO,@CONDICAO,@CLIENTE,@VENDACONS,@TABELA,@VALORINICIAL)
                                        
                                        
                                        select 
                                        
                                        
                                        @PRODUTO AS CODPRODUTO,
                                        TB01010_REFERENCIA AS REFERENCIA,
                                        TB01010_NOME AS NOME,
                                        FORMAT(VALOR, 'C', 'pt-br') AS VALORBASE,
                                        TB02054_FATOR AS FATOR,
                                        FORMAT(VALORIPI, 'C','pt-br') AS VALORIPI,
                                        FORMAT(VALORST, 'C', 'pt-br') AS ST,
                                        DIFALIQ AS DIFALST,
                                        FORMAT(VALORFINAL, 'C', 'pt-br') AS VALORFINAL,
                                        TB02054_MEDIDORPB MEDIDORPB,
                                        TB02054_MEDIDORCOLOR MEDIDORCOLOR,
                                        TB02054_MEDIDORTOTAL MEDIDORTOTAL,
                                        TB02054_PONTUACAO PONTUACAO
                                        
                                        from FT02002(@EMPRESA,@PRODUTO,@OPERACAO,@CONDICAO,@CLIENTE,@VENDACONS,@TABELA,@VALORBASE)
                                        LEFT JOIN TB01010 ON TB01010_CODIGO = @PRODUTO
                                        LEFT JOIN TB02054 ON TB02054_PRODUTO = @PRODUTO AND TB02054_CODEMP = @EMPRESA AND TB02054_NUMSERIE = @SERIAL
                                    ";
                                $stmt = sqlsrv_query($conn, $sql);

                                ?>


                                <?php
                                $tabela = "";
                                while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
                                    $tabela .= "<tr>";
                                    $tabela .= "<td>" . $row['CODPRODUTO'] . "</td>";
                                    $tabela .= "<td>" . $row['REFERENCIA'] . "</td>";
                                    $tabela .= "<td>" . $row['VALORBASE'] . "</td>";
                                    $tabela .= "<td>" . $row['FATOR'] . "</td>";
                                    $tabela .= "<td>" . $row['VALORIPI'] . "</td>";
                                    $tabela .= "<td>" . $row['ST'] . "</td>";
                                    $tabela .= "<td>" . $row['DIFALST'] . "</td>";
                                    $tabela .= "<td>" . $row['VALORFINAL'] . "</td>";
                                    $tabela .= "<td>" . $row['MEDIDORPB'] . "</td>";
                                    $tabela .= "<td>" . $row['MEDIDORCOLOR'] . "</td>";
                                    $tabela .= "<td>" . $row['MEDIDORTOTAL'] . "</td>";
                                    $tabela .= "</tr>";
                                }
                                /* $tabela .= "</table>"; */

                                print ($tabela);
                            }
                        }
                        /* } */

                        ?>
                    </table>
                </div>
            </div>
            <div class="btn-index">
                <input type="hidden" name="trava" id="trava" value="1">
                <button onClick="voltar();" type="submit" class="voltar-btn-form">Voltar</button>
            </div>
            <input type="hidden" id="urlOS" value="<?= $url ?>/save.php">

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="../JS/script.js" charset="utf-8"></script>
</body>

</html>