<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel APIs</title>
    <style>
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f4f4f4; }
        tr:nth-child(even) { background: #f9f9f9; }
    </style>
</head>
<body>
    <h1>Estatísticas de Chamadas das APIs</h1>

    <table>
        <thead>
            <tr>
                <th>Endpoint (URI)</th>
                <th>Chamadas</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($stats as $stat)
                <tr>
                    <td>{{ $stat['uri'] }}</td>
                    <td>{{ $stat['count'] }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2">Nenhuma rota encontrada.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
