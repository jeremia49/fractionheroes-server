# Student Dashboard with Face-Capture Game Integration

A Laravel-based student management system with integrated face-capture game functionality. Admins can manage students and monitor their game sessions, while the game can track student progress and capture face images during gameplay.

## 🚀 Features

- **Student Management**: Add, edit, and manage student accounts
- **Game Session Tracking**: Monitor face-capture game sessions
- **Face Image Gallery**: Browse captured face images with quality assessment
- **Progress Analytics**: Track student performance and game statistics
- **API Integration**: RESTful API for game integration
- **Admin Dashboard**: Comprehensive overview of all activities

## 🛠️ Technology Stack

- **Backend**: Laravel 12.x
- **Database**: MySQL 8.0
- **Frontend**: Bootstrap 5, Blade Templates
- **API Authentication**: Laravel Sanctum
- **Containerization**: Docker & Docker Compose
- **Database Management**: phpMyAdmin

## 📋 Prerequisites

- Docker and Docker Compose
- Git

## 🚀 Quick Start

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd dashboard
   ```

2. **Start the application**
   ```bash
   docker-compose up -d
   ```

3. **Run database migrations**
   ```bash
   docker-compose exec app php artisan migrate
   ```

4. **Create storage link for face images**
   ```bash
   docker-compose exec app php artisan storage:link
   ```

5. **Access the application**
   - **Dashboard**: http://localhost:8000
   - **phpMyAdmin**: http://localhost:8080 (username: `root`, password: `password`)

## 🎮 Game Integration API

The system provides a comprehensive API for integrating face-capture games. All API endpoints are prefixed with `/api/v1/`.

### Authentication

Most API endpoints require authentication using Laravel Sanctum tokens.

#### Register a Student
```http
POST /api/v1/register
Content-Type: application/json

{
    "username": "student123",
    "password": "password123",
    "full_name": "John Doe",
    "school": "Example School",
    "class": "Grade 10",
    "class_type": "Regular"
}
```

#### Student Login
```http
POST /api/v1/login
Content-Type: application/json

{
    "username": "student123",
    "password": "password123"
}
```

**Response:**
```json
{
    "success": true,
    "token": "1|abc123def456...",
    "student": {
        "id": 1,
        "username": "student123",
        "full_name": "John Doe",
        "school": "Example School",
        "class": "Grade 10",
        "class_type": "Regular"
    }
}
```

### Game Session Management

#### 1. Start a Game Session

**Endpoint:** `POST /api/v1/games/start-session`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "total_rounds": 10,
    "game_settings": {
        "difficulty": "medium",
        "time_limit": 30,
        "face_capture_enabled": true
    }
}
```

**Response:**
```json
{
    "success": true,
    "session": {
        "id": 1,
        "session_id": "GAME_20250707_001",
        "student_id": 1,
        "total_rounds": 10,
        "completed_rounds": 0,
        "total_score": 0,
        "game_status": "active",
        "started_at": "2025-07-07T10:00:00Z",
        "game_settings": {
            "difficulty": "medium",
            "time_limit": 30,
            "face_capture_enabled": true
        }
    }
}
```

#### 2. Capture Face Image

**Endpoint:** `POST /api/v1/games/capture-face`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: multipart/form-data
```

**Request Body:**
```form-data
session_id: GAME_20250707_001
round_number: 1
image: [binary image data]
capture_quality: good
confidence_score: 85.5
face_landmarks: "left_eye:120,150;right_eye:180,150;nose:150,180"
```

**Response:**
```json
{
    "success": true,
    "face": {
        "id": 1,
        "session_id": "GAME_20250707_001",
        "round_number": 1,
        "image_path": "faces/2025/07/face_001.jpg",
        "capture_quality": "good",
        "confidence_score": 85.5,
        "face_landmarks": "left_eye:120,150;right_eye:180,150;nose:150,180",
        "captured_at": "2025-07-07T10:05:00Z"
    }
}
```

#### 3. End Game Session

**Endpoint:** `POST /api/v1/games/end-session`

**Headers:**
```
Authorization: Bearer {token}
Content-Type: application/json
```

**Request Body:**
```json
{
    "session_id": "GAME_20250707_001",
    "final_score": 850,
    "total_rounds": 10,
    "game_status": "completed"
}
```

**Response:**
```json
{
    "success": true,
    "session": {
        "id": 1,
        "session_id": "GAME_20250707_001",
        "game_status": "completed",
        "total_score": 850,
        "total_rounds": 10,
        "completed_rounds": 10,
        "average_score": 85.0,
        "duration": 300,
        "ended_at": "2025-07-07T10:10:00Z"
    }
}
```

### Data Retrieval APIs

#### Get Session Information
```http
GET /api/v1/games/session/{sessionId}
Authorization: Bearer {token}
```

Returns session summary (score, rounds, status, etc.).

#### Get Student Game History
```http
GET /api/v1/students/{studentId}/game-history
Authorization: Bearer {token}
```

#### Get Current User Info
```http
GET /api/v1/me
Authorization: Bearer {token}
```

### 🏆 Global Leaderboard

**Endpoint:**
`GET /api/v1/leaderboard`

**Headers:**
`Authorization: Bearer {token}`

**Query Parameters (optional):**
- `limit` (default: 10) — Number of top students to return

**Response Example:**
```json
{
  "success": true,
  "leaderboard": [
    {
      "id": 1,
      "username": "student1",
      "full_name": "Alice Smith",
      "school": "Example School",
      "class": "Grade 10",
      "class_type": "Regular",
      "total_score": 950,
      "average_score": 95.0,
      "sessions_played": 12
    },
    {
      "id": 2,
      "username": "student2",
      "full_name": "Bob Lee",
      "school": "Example School",
      "class": "Grade 11",
      "class_type": "Regular",
      "total_score": 900,
      "average_score": 90.0,
      "sessions_played": 10
    }
    // ...
  ]
}
```

- Students are ranked by `total_score` (sum of all completed game sessions).
- You can change the number of results with `?limit=20`.

### Logout
```http
POST /api/v1/logout
Authorization: Bearer {token}
```

## 🎯 Game Integration Workflow

### Typical Game Session Flow

1. **Student Login**
   ```javascript
   const loginResponse = await fetch('/api/v1/login', {
       method: 'POST',
       headers: { 'Content-Type': 'application/json' },
       body: JSON.stringify({ username, password })
   });
   const { token } = await loginResponse.json();
   ```

2. **Start Session**
   ```javascript
   const sessionResponse = await fetch('/api/v1/games/start-session', {
       method: 'POST',
       headers: {
           'Authorization': `Bearer ${token}`,
           'Content-Type': 'application/json'
       },
       body: JSON.stringify({
           total_rounds: 10,
           game_settings: { difficulty: 'medium' }
       })
   });
   const { session } = await sessionResponse.json();
   ```

3. **End Session**
   ```javascript
   await fetch('/api/v1/games/end-session', {
       method: 'POST',
       headers: {
           'Authorization': `Bearer ${token}`,
           'Content-Type': 'application/json'
       },
       body: JSON.stringify({
           session_id: session.session_id,
           final_score: totalScore,
           total_rounds: session.total_rounds,
           game_status: 'completed'
       })
   });
   ```

## 📊 Admin Dashboard Features

### Game Sessions Overview
- View all active and completed game sessions
- Monitor real-time statistics
- Track student performance trends

### Face Gallery
- Browse all captured face images
- Filter by quality (good, poor, failed)
- View detailed capture information

### Student Analytics
- Individual student game history
- Performance charts over time
- Session completion rates

## 🔧 Configuration

### Environment Variables
Create a `.env` file based on `.env.example`:
```env
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=student_dashboard
DB_USERNAME=root
DB_PASSWORD=password
```

### Storage Configuration
Face images are stored in `storage/app/public/faces/` and are accessible via the web.

## 🐳 Docker Services

- **app**: Laravel application (port 8000)
- **mysql**: MySQL database (port 3306)
- **phpmyadmin**: Database management (port 8080)

## 📝 API Error Handling

All API endpoints return consistent error responses:

```json
{
    "success": false,
    "message": "Error description",
    "errors": {
        "field": ["Validation error message"]
    }
}
```

Common HTTP status codes:
- `200`: Success
- `201`: Created
- `400`: Bad Request
- `401`: Unauthorized
- `404`: Not Found
- `422`: Validation Error
- `500`: Server Error

## 🔒 Security Considerations

- All game APIs require authentication
- Face images are stored securely
- Session tokens expire automatically
- Input validation on all endpoints
- CSRF protection enabled

## 🚀 Deployment

For production deployment:

1. Update environment variables
2. Set up proper SSL certificates
3. Configure database backups
4. Set up monitoring and logging
5. Configure proper file permissions

## 📞 Support

For questions or issues:
1. Check the API documentation above
2. Review the Laravel logs in `storage/logs/`
3. Check the database for data integrity
4. Verify Docker container status

## 📄 License

This project is licensed under the MIT License.
