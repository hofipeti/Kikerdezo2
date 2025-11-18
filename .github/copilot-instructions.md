# AI Coding Agent Instructions

## Project Overview
This project is a PHP-based web application that uses Docker for containerized development. It includes a MySQL database and a PHPMyAdmin interface for database management. The application appears to be a login system with user authentication.

### Key Components:
- **PHP Application**: Located in the `php-app/` directory, this includes the main application logic and HTML files.
- **MySQL Database**: Configured in the `docker-compose.yml` file, with initialization scripts in the `sql/` directory.
- **Docker Compose**: Manages the multi-container setup, including the PHP app, MySQL, and PHPMyAdmin.

---

## Development Workflow

### Building and Running the Application
1. Ensure Docker is installed and running on your system.
2. Use the following command to build and start the application:
   ```powershell
   docker-compose up -d --build
   ```
3. Access the application at `http://localhost:8080`.
4. Access PHPMyAdmin at `http://localhost:8888`.

### Stopping the Application
To stop the running containers, use:
```powershell
docker-compose down
```

---

## Codebase Structure

### PHP Application
- **Entry Points**:
  - `index.html`: The main login page.
  - `login.php`: Handles user authentication.
  - `logout.php`: Manages user logout.
- **Configuration**:
  - `inc/config.php`: Contains database connection settings.

### Database
- **Initialization Script**:
  - `sql/kikerdezo_01.sql`: Sets up the initial database schema and data.

### Docker Compose
- **Services**:
  - `php-app`: Runs the PHP application.
  - `mysql`: Hosts the MySQL database.
  - `phpmyadmin`: Provides a web interface for database management.

---

## Project-Specific Conventions

### Database Queries
- Use prepared statements for all database queries to prevent SQL injection. Example:
  ```php
  $stmt = $conn->prepare("SELECT * FROM user WHERE login = ? AND (password = SHA2(?, 256))");
  $stmt->bind_param("ss", $username, $password);
  ```

### Session Management
- Sessions are used for user authentication. Example:
  ```php
  session_start();
  $_SESSION['user_id'] = $row['user_id'];
  ```

---

## External Dependencies
- **Bootstrap**: Used for styling the application. Included via CDN in `index.html`.
- **Docker**: Manages the development environment.
- **PHPMyAdmin**: Provides a GUI for managing the MySQL database.

---

## Notes for AI Agents
- Always validate user input to ensure security.
- Follow the existing patterns for database queries and session management.
- When adding new features, ensure they integrate seamlessly with the Dockerized environment.
- Update the `docker-compose.yml` file if new services or dependencies are added.

---

For any questions or clarifications, refer to the `docker-compose.yml` file and the `php-app/` directory for examples of existing patterns.