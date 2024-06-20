# Routes

## Sprint 2

| URL                  | HTTP Method | Controller           | Method   | Title                       | Content               | Nom                |
| -------------------- | ----------- | -------------------- | -------- | --------------------------- | --------------------- | ------------------ |
| `/`                  | `GET`       | `MainController`     | `home`   | Bienvenue sur "nom du site" | Backoffice dashboard  | `main-home`        |
| `/activities`        | `GET`       | `ActivityController` | `list`   | Liste des activités         | Activities list       | `activity-list`    |
| `/activities/[i:id]` | `GET`       | `ActivityController` | `read`   | Visualiser une activité     | Detail of an activity | `activity-details` |
| `/activities`        | `POST`      | `ActivityController` | `create` | Créer une activité          | create an activity    | `activity-create`  |
| `/activities/[i:id]` | `PATCH`     | `ActivityController` | `update` | Éditer une activité         | update an activity    | `activity-update`  |
| `/activities/[i:id]` | `DELETE`    | `ActivityController` | `delete` | Supprimer une activité      | activity delete       | `activity-delete`  |
| `/users`             | `GET`       | `UserController`     | `read`   | Liste des utilisateurs      | Users list            | `user-list`        |
| `/users/[i:id]`      | `GET`       | `UserController`     | `read`   | Visualiser un utilisateur   | Form to add a user    | `user-details`     |
| `/users`             | `POST`      | `UserController`     | `add`    | Ajouter un utilisateur      | Form to add a user    | `user-create`      |
| `/users/[i:id]`      | `PATCH`     | `UserController`     | `update` | Éditer un utilisateur       | Form to update a user | `user-update`      |
| `/users/[i:id]`      | `DELETE`    | `UserController`     | `delete` | Supprimer un utilisateur    | User delete           | `user-delete`      |
