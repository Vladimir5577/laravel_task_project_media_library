Получение токена для аутентификации:

POST /api/login
{
"email": "user@example.com",
"password": "password"
}


Создание проекта:

POST /api/projects
{
"name": "New Project",
"description": "This is a new project"
}


Создание задачи:

POST /api/projects/{project_id}/tasks
{
"title": "New Task",
"description": "Task description",
"status": "planned",
"due_date": "2025-12-31",
"user_id": 1,
"attachment": file // Загружаемый файл
}


Получение задач для проекта:

GET /api/projects/{project_id}/tasks


Обновление задачи:

PUT /api/tasks/{task_id}
{
"title": "Updated Task",
"status": "in_progress"
}


Удаление задачи:

DELETE /api/tasks/{task_id}




Перейдите в веб-интерфейс MailHog:
http://localhost:8025
