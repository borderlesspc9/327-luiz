<!DOCTYPE html>
<html>
<head>
    
    <style>
        .body-page{
            position: relative;
        }

        .logo-top{
            position: absolute;
            top: -48px;
            right: -48px;
        }

        .logo-bottom{
            position: absolute;
            bottom: -48px;
            left: -48px;
            z-index: -1;
        }

        .title-pdf{
            font-size: 25px;
            font-weight: 700;
            color: #000;
            text-align: center;
        }

        .list-topics-pdf{
            padding: 0;
            margin-bottom: 20px;
        }

        .list-topics-pdf li{
            display: block;
            font-size: 15px;
            color: #000;
            margin-bottom: 15px;
        }

        .table-pdf {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
            margin-bottom: 15px;
        }

        .table-pdf th, .table-pdf td {
            border: 1px solid black;
            padding: 0 3px;
            text-align: left;
        }

        .table-pdf td{
            font-size: 18px;
            font-weight: 500;
            color: #000;
        }

        .table-pdf .two-columns td:nth-child(1) {
            width: 25%; /* Ajuste conforme necessário */
        }

        .table-pdf .two-columns td:nth-child(2) {
            width: 75%; /* Ajuste conforme necessário */
        }

        
        .table-pdf .three-columns td:nth-child(1) {
            width: 15%; /* Ajuste conforme necessário */
        }

        .table-pdf .three-columns td:nth-child(2) {
            width: 50%; /* Ajuste conforme necessário */
        }

        .table-pdf .three-columns td:nth-child(3) {
            width: 35%; /* Ajuste conforme necessário */
        }

        .box-text{
            font-size: 20px;
            line-height: 26px;
            font-weight: 400;
            color: #000;
        }

        .box-text p{
            margin-bottom: 20px;
        }

        .box-text p:last-child{
            margin-bottom: 0;
        }

        .address{
            display: block;
            font-size: 17px;
            color: #000;
            margin-top: 100px;
            margin-bottom: 100px;
        }

        .address-footer{
            display: block;
            font-size: 17px;
            color: #000;
            text-align: center;
        }

        .box-signature{
            width: 100%;
            font-size: 17px;
            font-weight: 700;
            color: #000;
            position: relative;
            margin-bottom: 30px;
        }

        .box-signature span{
            display: block;
            position: absolute;
            border-bottom: 1px solid #000;
            height: 1px;
            width: 90%;
            right: 0;
            bottom: 0;
            background: #000;
        }
        </style>
</head>
<body class="body-page">
    <h1 class="title-pdf">Procuração</h1>
    <table class="table-pdf">
        <tbody>
            <tr class="two-columns">
                <td>Nome:</td>
                
                <td colspan="2">{{ $content['name'] }}</td>
            </tr>
            <tr class="three-columns">
                <td>CPF:</td>
                <td>{{ $content['cpf'] }}</td>
                <td>RG: {{ $content['rg'] }}{{ $content['rg_letter'] ? $content['rg_letter'] : '' }} {{ $content['rg_issuer'] ? '/ ' . $content['rg_issuer'] : '' }}</td>
            </tr>
            <tr class="two-columns">
                <td>Endereço:</td>
                <td colspan="2">{{ $content['address'] }}</td>
            </tr>
            <tr class="two-columns">
                <td>Bairro:</td>
                <td colspan="2">{{ $content['neighborhood'] }}</td>
            </tr>
            <tr class="three-columns">
                <td>Cidade:</td>
                <td>{{ $content['city'] }}</td>
                <td>CEP: {{ $content['cep'] }}</td>
            </tr>
            <tr class="two-columns">
                <td>Data de </br>nacimento:</td>
                <td colspan="2">{{ $content['birth_date'] }}</td>
            </tr>
        </tbody>
    </table>
    <div class="box-text">
        <p> <b>OUTORGADO: JEFFERSON MARCONE PEREIRA NUNES</b>, brasileiro, casado, advogado, regularmente inscrito na OAB/MG sob o n.º <b>198001</b>, com escritório profissional localizado à Rua Monsenhor João Martins, 1.918 - loja 1, Novo Progresso, Contagem, Minas Gerais.</p>
        <p> <b>PODERES</b>: para representar o outorgante nos órgãos da Administração Pública, sejam elas repartições públicas federais, estaduais, municipais ou autarquias, em especial todos os assuntos de seu interesse, utilizando os poderes da cláusula “ad judicia” e os especiais dos art.105 do NCPC no que se refere a processo administrativo e infrações de trânsito, podendo transigir, acordar, discordar, concordar, desistir, renunciar, firmar compromissos, como também, assinar, pagar, dar entrada ou retirar livros e documentos, formular requerimentos, apresentar réplicas, apresentar defesas, recursos e impugnações, requerer e apresentar documentos, enfim, praticar todos os atos necessários e em lei permitidos, para o fiel e completo desempenho deste mandato ficando ratificados demais atos eventualmente praticados, inclusive atos passados.</p>
    </div>

    <span class="address">Belo Horizonte, {{ \Carbon\Carbon::now()->isoFormat('dddd, D \d\e MMMM \d\e YYYY') }}.</span>
    
    <div class="box-signature">
        ASSINATURA: ____________________________________________________________________
    </div>

    <span class="address-footer">Rua Monsenhor João Martins, 1.918 – Loja 1 – Novo Progresso, Contagem - MG</span>
</body>
</html>