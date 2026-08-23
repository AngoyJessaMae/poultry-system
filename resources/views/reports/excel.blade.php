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