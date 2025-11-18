<!DOCTYPE html>
<html>
<head>
    <title>Contrato</title>
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
            font-size: 17px;
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
            margin-bottom: 0;
        }

        .table-pdf tr{
            width: 100%;
        }

        .table-pdf th, .table-pdf td {
            border: 1px solid black;
            padding: 0 3px;
            text-align: left;
        }

        .table-pdf th:nth-child(1), .table-pdf td:nth-child(1) {
            width: 60%; /* Ajuste conforme necessário */
        }

        .table-pdf th:nth-child(2), .table-pdf td:nth-child(2),
        .table-pdf th:nth-child(3), .table-pdf td:nth-child(3) {
            width: 20%; /* Ajuste conforme necessário */
        }

        .box-text{
            font-size: 12px;
            font-family: "Sans-Sarif";
            line-height: 15px;
            font-weight: 500;
            color: #000;
        }

        .box-text p{
            margin-bottom: 0;
        }

        .second-list{
            padding: 0;
            margin-bottom: 20px;
        }

        .second-list li{
            display: block;
            margin-bottom: 0;
            font-size: 12px;
        }

        .second-list li b{
            font-size: 14px;
            line-height: 15px;
            text-transform: uppercase;
            color: #000;
        }

        .title-end-pdf{
            font-size: 17px;
            font-weight: 400;
            color: #000;
            text-align: center;
            margin-bottom: 50px;
        }
        .clearfix::after {
            content: "";
            display: table;
            clear: both;
        }

        .two-columns .box-content{
            border-top: 1px solid #000;
            width: 300px;
            float: left;
            text-align: center;
            clear: both;
            margin-bottom: 20px;
        }

        .two-columns .first-box{
            float: left;
            clear: both;
        }

        .two-columns .second-box{
            float: right;
            clear: both;
        }

        .two-columns .box-content span{
            display: block;
            font-size: 13px;
            font-weight: 700;
        }

        .footer{
            position: absolute;
            bottom: 0;
            right: 0;
        }
        </style>
</head>
<body class="body-page">
    <img class="logo-top" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/images/logo-small.png'))) }}" alt="">
    <img class="logo-bottom" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/images/logo-small-opacity.png'))) }}" alt="">
    <header class="header-pdf">
        <div class="box-title-header">
            <figure>
                <img src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/images/logo.png'))) }}" alt="">
            </figure>
        </div>
    </header>
    <h1 class="title-pdf">Contrato</h1>
    <ul class="list-topics-pdf">
        <li>I) <b>CONTRATANTE</b>: <b>{{ $content['client']['name'] }}</b>, RESIDENTE NA RUA {{ $content['client']['address'] }}, N° {{ $content['client']['number'] }}, {{ $content['client']['neighborhood'] }}, {{ $content['client']['city'] }} – {{ $content['client']['state'] }} / CEP: {{ $content['client']['cep'] }}. <b>CPF: {{ $content['client']['cpf'] }}.</b></li>
        <li>II) <b>CONTRATADO</b>: SEM MULTAS SERVIÇOS ADMINISTRATIVOS LTDA, INSCRITO SOB O CNPJ: 40.931.767/0001-59, LOCALIZADA NA RUA JOSÉ TEÓFILO MARQUES, 10, SALA 705, BURITIS, BELO HORIZONTE, ESTADO DE MINAS GERAIS</li>
        <li>III) O pagamento deverá ser realizado imediatamente após assinatura do contrato da seguinte forma: {{ $content['payment_method'] }} – <b> TOTAL: {{ $content['process_value'] }}</b></li>
        <li>VI) <span><b>DESCRITIVO DE SERVIÇOS:</b></span>
            <table class="table-pdf">
                <thead>
                    <tr>
                        <th>SERVIÇO CONTRATADO:</th>
                        <th>VALOR</th>
                        <th>Nº AIT:</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $content['service']['name'] }}</td>
                        <td>{{ $content['process_value'] }}</td>
                        <td>{{ $content['ait'] }}</td>
                    </tr>
                </tbody>
            </table>
        </li>
    </ul>
    <div class="box-text">
        <p>
            Parágrafo único: O inadimplemento do pagamento enseja a incidência de multa de 2% (dois por cento), juros de mora de 1% (um por 
            cento), correção monetária desde o dia de vencimento, de acordo com índice IGP-M e interrupção imediata da prestação do serviço. Por 
            este termo particular, as partes acima qualificadas nos itens I e II firmam o presente Contrato de Prestação de
            serviços, mediante as cláusulas seguintes:
        </p>
    </div>
    <ul class="second-list">
        <li>1) O objeto do presente termo é a prestação de serviços visando apresentar defesa administrativa e recursos em todas esferas administrativas cabíveis dos serviços descritos no <b>item IV.</b></li>
        <li>2) Em remuneração, o <b>CONTRATANTE</b> pagará aos <b>CONTRATADOS</b> a quantia mencionada no item IV acima, ao tempo da assinatura do presente contrato.</li>
        <li>3) Para o bom desempenho das funções acima descritas, o <b>CONTRATANTE</b> fornecerá aos <b>CONTRATADOS</b> toda e qualquer informação e documentação que estiver relacionada ao objeto deste contrato, bem como repassar ao contratado todas as intimações recebidas pelo contratante.</li>
        <li>4) O <b>CONTRATANTE</b>, reconhece já haver recebido a orientação preventiva, comportamental e procedimental para a consecução dos serviços, bem como informado da impossibilidade de garantia de deferimento dos recursos/defesas protocolados.</li>
        <li>5ª) Em caso de pagamento parcelado do preço estipulado no <b>item III</b> o inadimplemento de qualquer parcela implica na interrupção imediata da prestação de serviço, na instância administrativa em que se encontra a infração/processo nesta data e, também antecipação das parcelas vincendas, a contar do primeiro dia útil seguinte à data de vencimento.</li>
        <li>5.1 Em caso de inadimplemento, será interrompida imediatamente toda a prestação de serviço, ainda que chegada a data limite da infração/processo em instância superior.</li>
        <li>5.2 A prestação de serviço somente será retomada, na instância em que se encontra, mediante adimplemento, ficando impossibilitado o contratante de exigir perdas e danos por eventuais instâncias perdidas em razão do inadimplemento a que deu causa.</li>
        <li>6ª) OS <b>CONTRATADOS</b> não se responsabilizam por extravio da documentação ou por protocolar fora do prazo em razão de demora no envio da documentação, que deverá ser apresentada com o mínimo de 20 (vinte) dias de antecedência da data máxima para protocolo da defesa/recurso.</li>
    </ul>
    <h1 class="title-end-pdf">Belo Horizonte, {{ Carbon\Carbon::now()->isoFormat('D \d\e MMMM \d\e YYYY') }}.</h1>
    <div class="two-columns clearfix">
        <div class="box-content first-box">
            <div class="box-signature">
                <span>SEM MULTAS SEVIÇOS ADMINISTRATIVOS</span>
                <span>CNPJ: 40.931.767/0001-59</span>
            </div>
        </div>
        <div class="box-content second-box">
            <div class="box-signature">
                <span>{{ $content['client']['name'] }}</span>
                <span>CPF: {{ $content['client']['cpf'] }}</span>
            </div>
        </div>
    </div>
    <img class="footer" src="data:image/png;base64,{{ base64_encode(file_get_contents(public_path('assets/images/footer-page.png'))) }}" alt="">
</body>
</html>