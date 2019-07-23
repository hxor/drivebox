<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Dropbox File</title>
</head>
<body>
    <form action="{{ url('drop') }}" method="post" enctype="multipart/form-data">
        @csrf
        <label for="">File(s)</label>
        <input type="file" name="file[]" id="" multiple="true">
        <button type="submit">Upload</button>
    </form>
    <table>
        <thead>
            <tr>
                <th>File</th>
                <th>Type</th>
                <th>Size</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($files as $file)
            <tr>
                <td>
                    <a href="{{ url('drop/'.$file->file) }}">
                        {{ $file->file }}
                    </a>
                </td>
                <td>{{ $file->type }}</td>
                <td>{{ number_format($file->size / 1024, 1) }} Kb</td>
                <td>
                    <a href="{{ url('drop/'.$file->file.'/download') }}">
                        Download
                    </a> |
                    <a href="{{ url('drop/'.$file->id.'/destroy') }}">
                        Delete
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
