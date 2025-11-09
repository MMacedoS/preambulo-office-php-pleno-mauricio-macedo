<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aviso de Locação em Atraso</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .alert {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        .alert-title {
            font-weight: bold;
            color: #856404;
            margin-bottom: 10px;
        }
        .info-box {
            background-color: #f8f9fa;
            border-left: 4px solid #667eea;
            padding: 15px;
            margin: 15px 0;
            border-radius: 4px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px solid #e9ecef;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: 600;
            color: #667eea;
        }
        .info-value {
            color: #333;
        }
        .movies-list {
            margin: 20px 0;
        }
        .movie-item {
            background-color: #f8f9fa;
            padding: 12px;
            margin: 8px 0;
            border-radius: 4px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .movie-title {
            flex: 1;
            font-weight: 500;
        }
        .movie-quantity {
            background-color: #667eea;
            color: white;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            margin-left: 10px;
        }
        .penalty-summary {
            background-color: #f8d7da;
            border: 1px solid #f5c6cb;
            border-radius: 4px;
            padding: 20px;
            margin: 20px 0;
            text-align: center;
        }
        .penalty-amount {
            font-size: 32px;
            font-weight: bold;
            color: #721c24;
            margin: 10px 0;
        }
        .penalty-description {
            color: #721c24;
            font-size: 14px;
        }
        .action-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 4px;
            margin-top: 20px;
            text-align: center;
        }
        .footer {
            background-color: #f8f9fa;
            padding: 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #e9ecef;
        }
        .divider {
            height: 2px;
            background: linear-gradient(to right, #667eea, #764ba2);
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Aviso de Locação em Atraso</h1>
        </div>

        <div class="content">
            <p>Olá <strong>{{ $dados['usuario_nome'] }}</strong>,</p>

            <div class="alert">
                <div class="alert-title">Sua locação está em atraso!</div>
                <p>
                    A devolução de seus filmes estava prevista para <strong>{{ $dados['data_devolucao_prevista'] }}</strong>.
                    Já passaram <strong>{{ $dados['dias_atraso'] }} dias</strong> desde a data de devolução.
                </p>
            </div>

            <div class="divider"></div>

            <h2 style="color: #667eea; margin-top: 0;">Detalhes da Locação</h2>

            <div class="info-box">
                <div class="info-row">
                    <span class="info-label">ID da Locação:</span>
                    <span class="info-value">#{{ $dados['locacao_id'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Data Prevista de Devolução:</span>
                    <span class="info-value">{{ $dados['data_devolucao_prevista'] }}</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Dias em Atraso:</span>
                    <span class="info-value">{{ $dados['dias_atraso'] }} dias</span>
                </div>
                <div class="info-row">
                    <span class="info-label">Quantidade de Filmes:</span>
                    <span class="info-value">{{ $dados['quantidade_filmes'] }} filmes</span>
                </div>
            </div>

            <h3 style="color: #667eea;">Filmes em Atraso</h3>
            <div class="movies-list">
                @foreach($dados['filmes'] as $filme)
                    <div class="movie-item">
                        <span class="movie-title">{{ $filme['titulo'] }}</span>
                        <span class="movie-quantity">{{ $filme['quantidade'] }}x</span>
                    </div>
                @endforeach
            </div>

            <div class="divider"></div>

            <h2 style="color: #721c24; margin-top: 0;">Informações sobre Multa</h2>

            <div class="penalty-summary">
                <div class="penalty-description">
                    Multa de R$ 5,00 por filme por dia de atraso
                </div>
                <div class="penalty-amount">
                    R$ {{ number_format($dados['multa_total_acumulada'], 2, ',', '.') }}
                </div>
                <div class="penalty-description">
                    Multa acumulada até o momento
                </div>
                <div style="margin-top: 15px; font-size: 13px; color: #721c24;">
                    ({{ $dados['quantidade_filmes'] }} filmes x R$ 5,00 x {{ $dados['dias_atraso'] }} dias)
                </div>
            </div>

            <div style="background-color: #e7f3ff; border-left: 4px solid #0066cc; padding: 15px; margin: 20px 0; border-radius: 4px;">
                <strong style="color: #0066cc;">Importante:</strong>
                <p style="margin: 10px 0 0 0; color: #333;">
                    A multa continua acumulando enquanto os filmes não forem devolvidos.
                    Devsolva seus filmes o mais rápido possível para evitar mais cobranças.
                </p>
            </div>

            <a href="#" class="action-button">Reportar Devolução</a>
        </div>

        <div class="footer">
            <p style="margin: 0;">
                Este é um email automático gerado pelo sistema de locação de filmes.
                Por favor, não responda este email.
            </p>
            <p style="margin: 10px 0 0 0; color: #999;">
                Locação ID: {{ $dados['locacao_uuid'] }}
            </p>
        </div>
    </div>
</body>
</html>
