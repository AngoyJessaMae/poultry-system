<style>
    @page { size: A4 landscape; margin: 8mm; }
    body { font-family: Arial, sans-serif; font-size: 8px; color: #111; }
    h1 { text-align: center; font-size: 15px; margin: 0 0 8px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #555; padding: 3px; text-align: center; vertical-align: middle; }
    .header th { background: #e5e7eb; text-align: left; width: 18%; }
    .header td { text-align: left; width: 32%; }
    .group { background: #d1d5db; font-weight: bold; }
    .subhead { background: #f3f4f6; font-weight: bold; }
</style>

<h1>Daily Production Monitoring Form</h1>
<table class="header">
    <tr><th>Name of Farm</th><td>{{ config('farm.name') }}</td><th>Batch / Flock</th><td>{{ $batch->batch_code }}</td></tr>
    <tr><th>Date of Loading / Arrival</th><td>{{ $batch->arrival_date->format('Y-m-d') }}</td><th>Flockman / Recorder</th><td>{{ $batch->flockman?->name ?: '________________________________' }}</td></tr>
    <tr><th>No. of Heads Load</th><td>{{ $batch->initial_quantity }}</td><th>Initial Weight (average grams/bird)</th><td>{{ number_format($batch->initial_weight_grams, 2) }} g</td></tr>
</table>
<br>
<table>
    <thead>
        <tr>
            <th class="group" rowspan="2">Days / Age</th><th class="group" rowspan="2">Date</th>
            <th class="group" colspan="3">Mortality</th><th class="group" colspan="2">ALW (grams)</th>
            <th class="group" rowspan="2">Feed Type</th><th class="group" colspan="4">Feed Consumed (grams)</th><th class="group" colspan="2">FCR</th>
        </tr>
        <tr class="subhead">
            <th>Heads</th><th>CU % (ACT)</th><th>CU % (STD)</th><th>Actual</th><th>Standard</th>
            <th>Actual Daily</th><th>Actual Total</th><th>Standard Daily</th><th>Standard Total</th><th>Actual</th><th>Standard</th>
        </tr>
    </thead>
    <tbody>
        @foreach($rows as $row)
            <tr>
                <td>{{ $row['age'] }}</td><td>{{ $row['date']->format('Y-m-d') }}</td><td>{{ $row['mortality_heads'] }}</td>
                <td>{{ $row['actual_mortality_percent'] === null ? '' : number_format($row['actual_mortality_percent'], 4) }}</td><td>{{ $row['standard_mortality_percent'] === null ? '' : number_format($row['standard_mortality_percent'], 4) }}</td>
                <td>{{ $row['actual_live_weight_grams'] === null ? '' : number_format($row['actual_live_weight_grams'], 2) }}</td><td>{{ $row['standard_live_weight_grams'] === null ? '' : number_format($row['standard_live_weight_grams'], 2) }}</td>
                <td>{{ $row['feed_type'] }}</td><td>{{ $row['actual_feed_grams'] === null ? '' : number_format($row['actual_feed_grams'], 2) }}</td><td>{{ number_format($row['cumulative_actual_feed_grams'], 2) }}</td>
                <td>{{ $row['standard_feed_grams'] === null ? '' : number_format($row['standard_feed_grams'], 2) }}</td><td>{{ number_format($row['cumulative_standard_feed_grams'], 2) }}</td>
                <td>{{ $row['actual_fcr'] === null ? '' : number_format($row['actual_fcr'], 6) }}</td><td>{{ $row['standard_fcr'] === null ? '' : number_format($row['standard_fcr'], 6) }}</td>
            </tr>
        @endforeach
    </tbody>
</table>