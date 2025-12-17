# Appointment Manager API

A robust RESTful API for managing healthcare appointments, built with Laravel 12. This system allows clients to book appointments with health professionals, manage services, and receive automated email confirmations.

## Features

- **Service Management**: Browse and retrieve healthcare services with pricing and duration
- **Health Professional Directory**: Access information about available healthcare professionals and their specializations
- **Appointment Booking**: Schedule appointments with automatic email confirmations
- **Queue-Based Email Notifications**: Asynchronous email delivery for appointment confirmations
- **Comprehensive Testing**: Full test coverage with Pest PHP testing framework
- **Static Analysis**: Code quality ensured with PHPStan (Level 6)
- **Repository Pattern**: Clean architecture with repository and service layers
- **API Resources**: Consistent JSON responses with Laravel API Resources
- **RESTful Design**: Well-structured API endpoints following REST principles

## Tech Stack

- **Framework**: Laravel 12
- **PHP**: 8.2+
- **Database**: SQLite (default), supports MySQL/PostgreSQL
- **Queue**: Database-driven queue system
- **Testing**: Pest PHP
- **Static Analysis**: PHPStan
- **Code Style**: Laravel Pint

## Requirements

- PHP 8.2 or higher
- Composer
- SQLite extension (or MySQL/PostgreSQL)
- Node.js & NPM (for asset compilation)

## Installation

### Backend Setup

1. Clone the repository:
```bash
git clone <repository-url>
cd appointment-manager
```

2. Install PHP dependencies:
```bash
composer install
```

3. Copy environment file and configure:
```bash
cp .env.example .env
php artisan key:generate
```

4. Create database and run migrations:
```bash
touch database/database.sqlite
php artisan migrate
```

5. (Optional) Seed database with realistic data:
```bash
php artisan seed:realistic-data
```

6. Install frontend dependencies and build assets:
```bash
npm install
npm run build
```

7. Start the development server:
```bash
php artisan serve
```

8. Start the queue worker (for email notifications):
```bash
php artisan queue:work
```

### Frontend Setup

The API is designed to work with a Nuxt.js frontend application.

1. Clone the frontend repository:
```bash
git clone https://github.com/amiralikh/nuxt-appointment-form
cd nuxt-appointment-form
```

2. Install dependencies:
```bash
npm install
```

3. Configure the API endpoint in your `.env` file:
```bash
NUXT_PUBLIC_API_URL=http://localhost:8000/api/v1
```

4. Start the development server:
```bash
npm run dev
```

The frontend will be available at `http://localhost:3000`

### Quick Start (All-in-One)

For convenience, you can use the composer scripts:

```bash
# Complete setup
composer setup

# Development mode (runs server, queue, logs, and vite)
composer dev

# Run tests
composer test
```

## API Documentation

### Base URL

```
http://localhost:8000/api/v1
```

### Endpoints

#### Services

- `GET /services` - List all services
- `GET /services/{id}` - Get service details

#### Health Professionals

- `GET /health-professionals` - List all health professionals
- `GET /health-professionals/{id}` - Get health professional details

#### Appointments

- `POST /appointments` - Create new appointment
- `GET /appointments/{id}` - Get appointment details

### Example Request

**Create Appointment**

```http
POST /api/v1/appointments
Content-Type: application/json

{
  "service_id": 1,
  "health_professional_id": 1,
  "customer_email": "customer@example.com",
  "scheduled_at": "2025-12-20 14:00:00",
  "notes": "First time visit"
}
```

**Response**

```json
{
  "data": {
    "id": 1,
    "service": {
      "id": 1,
      "name": "General Consultation",
      "description": "Standard medical consultation",
      "duration_minutes": 30,
      "price": "50.00"
    },
    "health_professional": {
      "id": 1,
      "name": "Dr. John Smith",
      "specialization": "General Practice",
      "email": "dr.smith@example.com",
      "phone": "+1234567890"
    },
    "customer_email": "customer@example.com",
    "scheduled_at": "2025-12-20T14:00:00.000000Z",
    "status": "pending",
    "notes": "First time visit",
    "created_at": "2025-12-18T10:30:00.000000Z"
  }
}
```

### Postman Collection

A complete Postman collection is included in the repository:
- `Appointment-Manager-API.postman_collection.json`

Import this file into Postman for easy API testing.

## Project Structure

```
app/
├── Console/Commands/      # Artisan commands
│   └── SeedRealisticData.php
├── Http/
│   ├── Controllers/Api/   # API Controllers
│   │   ├── AppointmentController.php
│   │   ├── HealthProfessionalController.php
│   │   └── ServiceController.php
│   ├── Requests/          # Form request validation
│   │   └── StoreAppointmentRequest.php
│   └── Resources/         # API Resources for JSON responses
│       ├── AppointmentResource.php
│       ├── HealthProfessionalResource.php
│       └── ServiceResource.php
├── Jobs/                  # Queue jobs
│   └── SendAppointmentConfirmation.php
├── Mail/                  # Mailable classes
│   └── AppointmentConfirmationMail.php
├── Models/                # Eloquent models
│   ├── Appointment.php
│   ├── HealthProfessional.php
│   └── Service.php
├── Repositories/          # Repository pattern implementation
│   ├── Contracts/
│   │   └── AppointmentRepositoryInterface.php
│   └── AppointmentRepository.php
└── Services/              # Business logic layer
    └── AppointmentService.php
```

## Architecture

This application follows clean architecture principles:

1. **Controllers**: Handle HTTP requests and responses
2. **Requests**: Validate incoming data
3. **Services**: Contain business logic
4. **Repositories**: Handle data persistence (abstraction layer for database operations)
5. **Resources**: Transform models into JSON responses
6. **Jobs**: Handle asynchronous tasks
7. **Mail**: Email template management

## Testing

The project includes comprehensive test coverage:

```bash
# Run all tests
php artisan test

# Run with coverage
php artisan test --coverage

# Run specific test suite
php artisan test --testsuite=Feature
php artisan test --testsuite=Unit
```

### Test Structure

- **Feature Tests**: API endpoint integration tests
  - `AppointmentControllerTest.php`
  - `HealthProfessionalControllerTest.php`
  - `ServiceControllerTest.php`

- **Unit Tests**: Business logic and component tests
  - `AppointmentServiceTest.php`
  - `AppointmentRepositoryTest.php`
  - `SendAppointmentConfirmationTest.php`

## Code Quality

### Static Analysis

Run PHPStan for static analysis:

```bash
vendor/bin/phpstan analyse
```

Configuration: `phpstan.neon`

### Code Formatting

Format code with Laravel Pint:

```bash
vendor/bin/pint
```

## Configuration

### Database

The application uses SQLite by default. To use MySQL or PostgreSQL:

1. Update `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=appointment_manager
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

2. Run migrations:
```bash
php artisan migrate
```

### Email

Configure email settings in `.env`:

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_username
MAIL_PASSWORD=your_password
MAIL_FROM_ADDRESS="noreply@appointmentmanager.com"
MAIL_FROM_NAME="${APP_NAME}"
```

For development, emails are logged by default (`MAIL_MAILER=log`).

### Queue

The application uses database queues by default. For production, consider Redis:

```env
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

Don't forget to run the queue worker:

```bash
php artisan queue:work
```

## Development Commands

### Custom Commands

- `php artisan seed:realistic-data` - Generate realistic test data for development

### Useful Laravel Commands

- `php artisan route:list` - List all registered routes
- `php artisan migrate:fresh --seed` - Fresh migration with seeding
- `php artisan queue:work` - Process queue jobs
- `php artisan pail` - View application logs in real-time

## Production Deployment

1. Set environment to production:
```env
APP_ENV=production
APP_DEBUG=false
```

2. Optimize application:
```bash
composer install --optimize-autoloader --no-dev
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

3. Set up queue worker as a system service
4. Configure proper database (MySQL/PostgreSQL)
5. Set up email service (e.g., AWS SES, Mailgun)
6. Enable HTTPS
7. Set up proper backup strategy

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Run tests (`php artisan test`)
5. Run static analysis (`vendor/bin/phpstan analyse`)
6. Format code (`vendor/bin/pint`)
7. Push to the branch (`git push origin feature/amazing-feature`)
8. Open a Pull Request

## License

This project is licensed under the MIT License.

## Support

For issues, questions, or contributions, please open an issue in the repository.