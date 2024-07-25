<?php
function preenchimento($conn, $serie)
{
    $sql = "SELECT 
                TB02112_ESTADO Estado,
                TB01008_NOME Cliente,
                TB02112_LOCAL Local,
                TB02112_EMAIL Email,
                (SELECT TOP 1 TB02117_TOTPB FROM TB02117 WHERE TB02117_NUMSERIE = TB02112_NUMSERIE ORDER BY TB02117_DTCAD DESC) UltCont,
                TB02112_NUMSERIE Serie,
				TB02112_FONEAUX Tel
                
            FROM TB02112
            LEFT JOIN TB02111 ON TB02111_CODIGO = TB02112_CODIGO
            LEFT JOIN TB01008 ON TB01008_CODIGO = TB02111_CODCLI

            WHERE TB02112_NUMSERIE = '$serie'
    ";
    $stmt = sqlsrv_query($conn, $sql);

   while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
        $estado = $row['Estado'];
        $Cliente = $row['Cliente'];
        $Local = $row['Local'];
        $UltCont = $row['UltCont'];
        $Email = $row['Email'];
        $Serie= $row['Serie'];
        $Tel= $row['Tel'];
    }

    return [$estado, $Cliente, $Local, $UltCont, $Email, $Serie, $Tel];

}