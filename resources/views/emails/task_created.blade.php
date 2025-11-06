<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Новая задача</title>
</head>
<body>
<h1>Новая задача создана</h1>
<p>Здравствуйте, {{ $task->user->name }}!</p>
<p>В проекте "{{ $task->project->name }}" была создана новая задача.</p>
<p><strong>Заголовок:</strong> {{ $task->title }}</p>
<p><strong>Описание:</strong> {{ $task->description }}</p>
<p><strong>Статус:</strong> {{ ucfirst($task->status) }}</p>
<p><strong>Дата завершения:</strong> {{ $task->due_date ? $task->due_date : 'Не указана' }}</p>
</body>
</html>
