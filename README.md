## Task project medialibrary

### Install in docker:
1. Copy .env.example to .env (optionally put you credentials inside).
```bash
$ cp .env.example .env
```

2. Build - for the first time.
```bash
$ docker-compose build 
```

3. run use -d flag for detach mode.
```bash
$ docker-compose up 
```
optionally - go to docker container for future installation
```bash
$ docker exec -it php_container bash
```

4. Install dependencies:
```bash
$ composer install
```

5. Go to database UI and create 2 databases name like in .env file - "media_library"
   and for testing - "media_library_test" (name in phpunit.xml). UI available:
   > type in browser - localhost:8087

6. Run migration.
```bash
$ php artisan migrate
```

7. Optionally generate app key
```bash
$ php artisan key:generate
```


## Postman
### For all request use Header - [Accept - application/json]

1. Register user:
    > /api/register POST
body
```json
{
    "name": "Bob",
    "email": "bob@bob.com",
    "password": "1234"
}
```

2. Login:
   > /api/login - POST
body
```json
{
    "email": "bob@bob.com",
    "password": "1234"
}
```
In response will be token.

3. Create project:
    > /api/projects - POST
body
```json
{
    "name": "New Project",
    "description": "This is a new project"
}
```

4. Create task:
    > /api/projects/{project_id}/tasks - POST
use form-data to sent file
```json
{
    "title": "New Task",
    "description": "Task description",
    "status": "planned",
    "due_date": "2025-12-31",
    "user_id": 1,
    "attachment": file // File upload use form-data
}
```
5. Get all tasks:
    > /api/projects/{project_id}/tasks - GET

   
6. Update task:
    > /api/tasks/{task_id} - PUT
body
```json
{
    "title": "Updated Task",
    "status": "in_progress"
}
```

7. Delete task:
    > /api/tasks/{task_id} - DELETE


## Mailtrap

Web interface MailHog:
    > localhost:8025

## Testing
```bash
$ php artisan tets
```
