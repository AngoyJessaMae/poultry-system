<!DOCTYPE html>
<html>
<head>
    <title>Report</title>
    <style>
        body { font-family: sans-serif; }
        h1 { text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        th, td { border: 1px solid #ddd; padding: 8px; }
        th { background-color: #f2f2f2; }
    </style>
</head>
<body>
    <h1>Babia Poultry Farm Report</h1>

    <h2>Analytics</h2>
    <table>
        <tr>
            <th>Feed-to-Weight Ratio</th>
            <td>{{ number_format($analytics['feedToWeightRatio'], 2) }}</td>
        </tr>
        <tr>
            <th>Mortality Rate</th>
            <td>{{ number_format($analytics['mortalityRate'], 2) }}%</td>
        </tr>
        <tr>
            <th>Total Sales</th>
            <td>₱{{ number_format($analytics['totalSales'], 2) }}</td>
        </tr>
        <tr>
            <th>Profit Difference</th>
            <td style="color: {{ $analytics['profitDifference'] >= 0 ? 'green' : 'red' }};">{{ number_format($analytics['profitDifference'], 2) }}%</td>
        </tr>
    </table>

    <h2>Data</h2>
    @if($filters['report_type'] === 'all')
        @foreach($data as $type => $records)
            <h3>{{ ucfirst($type) }}</h3>
            @if($records->isEmpty())
                <p>No data for this period.</p>
            @else
                <table>
                    <thead>
                        <tr>
                            @foreach(array_keys($records->first()->toArray()) as $key)
                                <th>{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($records as $record)
                            <tr>
                                @foreach($record->toArray() as $value)
                                    <td>{{ $value }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        @endforeach
    @else
        @if($data->isEmpty())
            <p>No data for this period.</p>
        @else
            <table>
                <thead>
                    <tr>
                        @foreach(array_keys($data->first()->toArray()) as $key)
                            <th>{{ ucfirst(str_replace('_', ' ', $key)) }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach($data as $record)
                        <tr>
                            @foreach($record->toArray() as $value)
                                <td>{{ $value }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    @endif
</body>
</html>