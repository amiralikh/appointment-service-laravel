# Appointment Manager API Documentation

## Overview

The Appointment Manager API provides endpoints for managing healthcare appointments, services, and health professionals. This REST API follows standard HTTP conventions and returns JSON responses.

**Base URL:** `/api/v1`

**Content-Type:** `application/json`

**Accept:** `application/json`

---

## Table of Contents

1. [Services](#services)
2. [Health Professionals](#health-professionals)
3. [Appointments](#appointments)
4. [Error Responses](#error-responses)

---

## Services

Services represent the types of healthcare services available for booking appointments.

### List All Services

Retrieves a list of all available services, ordered alphabetically by name.

**Endpoint:** `GET /api/v1/services`

**Request Example:**

```bash
curl -X GET "http://localhost/api/v1/services" \
  -H "Accept: application/json"
```

**Response (200 OK):**

```json
{
  "data": [
    {
      "id": 1,
      "name": "Cardiology Checkup",
      "description": "Complete cardiovascular health assessment",
      "duration_minutes": 45,
      "price": "150.00",
      "formatted_price": "$150.00",
      "created_at": "2025-12-17T10:00:00+00:00"
    },
    {
      "id": 2,
      "name": "Dental Checkup",
      "description": "Routine dental examination and cleaning",
      "duration_minutes": 30,
      "price": "75.00",
      "formatted_price": "$75.00",
      "created_at": "2025-12-17T10:05:00+00:00"
    }
  ]
}
```

### Get Service by ID

Retrieves details of a specific service.

**Endpoint:** `GET /api/v1/services/{id}`

**Parameters:**
- `id` (integer, required): The service ID

**Request Example:**

```bash
curl -X GET "http://localhost/api/v1/services/1" \
  -H "Accept: application/json"
```

**Response (200 OK):**

```json
{
  "data": {
    "id": 1,
    "name": "Cardiology Checkup",
    "description": "Complete cardiovascular health assessment",
    "duration_minutes": 45,
    "price": "150.00",
    "formatted_price": "$150.00",
    "created_at": "2025-12-17T10:00:00+00:00"
  }
}
```

**Error Response (404 Not Found):**

```json
{
  "message": "Service not found"
}
```

---

## Health Professionals

Health professionals represent healthcare providers available for appointments.

### List All Health Professionals

Retrieves a list of all health professionals, ordered alphabetically by name.

**Endpoint:** `GET /api/v1/health-professionals`

**Request Example:**

```bash
curl -X GET "http://localhost/api/v1/health-professionals" \
  -H "Accept: application/json"
```

**Response (200 OK):**

```json
{
  "data": [
    {
      "id": 1,
      "name": "Dr. Alice Smith",
      "specialization": "Cardiologist",
      "email": "alice.smith@example.com",
      "phone": "+1-555-0101",
      "created_at": "2025-12-17T09:00:00+00:00"
    },
    {
      "id": 2,
      "name": "Dr. Bob Johnson",
      "specialization": "Dentist",
      "email": "bob.johnson@example.com",
      "phone": "+1-555-0102",
      "created_at": "2025-12-17T09:15:00+00:00"
    }
  ]
}
```

### Get Health Professional by ID

Retrieves details of a specific health professional.

**Endpoint:** `GET /api/v1/health-professionals/{id}`

**Parameters:**
- `id` (integer, required): The health professional ID

**Request Example:**

```bash
curl -X GET "http://localhost/api/v1/health-professionals/1" \
  -H "Accept: application/json"
```

**Response (200 OK):**

```json
{
  "data": {
    "id": 1,
    "name": "Dr. Alice Smith",
    "specialization": "Cardiologist",
    "email": "alice.smith@example.com",
    "phone": "+1-555-0101",
    "created_at": "2025-12-17T09:00:00+00:00"
  }
}
```

**Error Response (404 Not Found):**

```json
{
  "message": "Health professional not found"
}
```

---

## Appointments

Appointments represent scheduled healthcare appointments between customers and health professionals.

### Create Appointment

Creates a new appointment. The system will check for professional availability and dispatch a confirmation email to the customer.

**Endpoint:** `POST /api/v1/appointments`

**Request Body Parameters:**
- `service_id` (integer, required): The ID of the service
- `health_professional_id` (integer, required): The ID of the health professional
- `customer_email` (string, required): Customer's email address (must be valid email format)
- `date` (datetime, required): Scheduled appointment date and time (must be in the future, format: Y-m-d H:i:s)
- `notes` (string, optional): Additional notes (max 1000 characters)

**Validation Rules:**
- `service_id`: Must exist in the services table
- `health_professional_id`: Must exist in the health_professionals table
- `customer_email`: Must be a valid email address
- `date`: Must be a valid date/time in the future
- Professional must not have another non-cancelled appointment within 1 hour of the requested time

**Request Example:**

```bash
curl -X POST "http://localhost/api/v1/appointments" \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "service_id": 1,
    "health_professional_id": 1,
    "customer_email": "john.doe@example.com",
    "date": "2025-12-20 14:00:00",
    "notes": "First time patient"
  }'
```

**Response (201 Created):**

```json
{
  "data": {
    "id": 1,
    "service": {
      "id": 1,
      "name": "Cardiology Checkup",
      "duration_minutes": 45,
      "price": "150.00"
    },
    "health_professional": {
      "id": 1,
      "name": "Dr. Alice Smith",
      "specialization": "Cardiologist"
    },
    "customer_email": "john.doe@example.com",
    "scheduled_at": "2025-12-20T14:00:00+00:00",
    "status": "pending",
    "notes": "First time patient",
    "created_at": "2025-12-17T10:30:00+00:00"
  }
}
```

**Error Response (422 Unprocessable Entity):**

Validation errors:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "service_id": [
      "The selected service does not exist."
    ],
    "date": [
      "The appointment date must be in the future."
    ],
    "customer_email": [
      "The customer email field must be a valid email address."
    ]
  }
}
```

Time slot unavailable:

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "date": [
      "The selected time slot is not available for this health professional."
    ]
  }
}
```

### Get Appointment by ID

Retrieves details of a specific appointment, including related service and health professional information.

**Endpoint:** `GET /api/v1/appointments/{id}`

**Parameters:**
- `id` (integer, required): The appointment ID

**Request Example:**

```bash
curl -X GET "http://localhost/api/v1/appointments/1" \
  -H "Accept: application/json"
```

**Response (200 OK):**

```json
{
  "data": {
    "id": 1,
    "service": {
      "id": 1,
      "name": "Cardiology Checkup",
      "duration_minutes": 45,
      "price": "150.00"
    },
    "health_professional": {
      "id": 1,
      "name": "Dr. Alice Smith",
      "specialization": "Cardiologist"
    },
    "customer_email": "john.doe@example.com",
    "scheduled_at": "2025-12-20T14:00:00+00:00",
    "status": "pending",
    "notes": "First time patient",
    "created_at": "2025-12-17T10:30:00+00:00"
  }
}
```

**Error Response (404 Not Found):**

```json
{
  "message": "Appointment not found"
}
```

---

## Error Responses

The API uses standard HTTP status codes to indicate the success or failure of requests.

### Status Codes

| Status Code | Description |
|-------------|-------------|
| 200 | OK - Request succeeded |
| 201 | Created - Resource created successfully |
| 404 | Not Found - Resource not found |
| 422 | Unprocessable Entity - Validation error |
| 500 | Internal Server Error - Server error |

### Error Response Format

All error responses follow a consistent format:

```json
{
  "message": "Error description"
}
```

For validation errors (422):

```json
{
  "message": "The given data was invalid.",
  "errors": {
    "field_name": [
      "Error message 1",
      "Error message 2"
    ]
  }
}
```

---

## Appointment Status Values

Appointments can have the following status values:

- `pending`: Appointment is pending confirmation
- `confirmed`: Appointment has been confirmed
- `cancelled`: Appointment has been cancelled

---

## Availability Logic

When booking an appointment, the system checks if the health professional is available:

1. The professional must not have another non-cancelled appointment scheduled within 1 hour (before or after) of the requested time
2. Cancelled appointments do not block time slots
3. If the time slot is unavailable, a validation error is returned

**Example:**
- Existing appointment: 14:00 - 15:00
- Blocked time range: 13:00 - 16:00
- Available time: Any time before 13:00 or after 16:00

---

## Email Notifications

When an appointment is successfully created, the system automatically sends a confirmation email to the customer's email address. The email includes:

- Service name
- Health professional name
- Scheduled date and time
- Customer email

The email is queued and sent asynchronously with 3 retry attempts (60 seconds backoff between retries).

---

## Examples

### Complete Booking Flow

1. **Get available services:**

```bash
curl -X GET "http://localhost/api/v1/services"
```

2. **Get available health professionals:**

```bash
curl -X GET "http://localhost/api/v1/health-professionals"
```

3. **Create an appointment:**

```bash
curl -X POST "http://localhost/api/v1/appointments" \
  -H "Content-Type: application/json" \
  -d '{
    "service_id": 1,
    "health_professional_id": 1,
    "customer_email": "customer@example.com",
    "date": "2025-12-25 10:00:00"
  }'
```

4. **View appointment details:**

```bash
curl -X GET "http://localhost/api/v1/appointments/1"
```

---

## Testing

The API includes comprehensive test coverage with Pest PHP:

- **Feature Tests**: Test complete API endpoints and workflows
- **Unit Tests**: Test individual components (services, repositories, jobs)
- **PHPStan Level 7**: Static analysis for type safety

Run tests:

```bash
php artisan test
```

Run PHPStan analysis:

```bash
vendor/bin/phpstan analyse
```

---

## Technical Details

### Technology Stack

- **Framework**: Laravel 12
- **PHP Version**: 8.2+
- **Database**: SQLite (development), MySQL/PostgreSQL (production)
- **Testing**: Pest PHP 4
- **Static Analysis**: PHPStan Level 7
- **Queue**: Configurable (sync, redis, database)
- **Mail**: Configurable (SMTP, mailgun, etc.)

### Architecture

The application follows a clean architecture pattern:

- **Controllers**: Handle HTTP requests and responses
- **Services**: Business logic layer
- **Repositories**: Data access layer
- **Models**: Eloquent ORM models
- **Resources**: API response transformation
- **Jobs**: Asynchronous task processing
- **Requests**: Form request validation

### Code Quality

- PHPStan Level 7 compliance
- 63 passing tests with 180 assertions
- Type-safe codebase with PHPDoc annotations
- Comprehensive error handling
- Database transactions for data integrity

---

## Support

For issues or questions, please contact the development team or open an issue in the project repository.

---

## Version History

**v1.0.0** - Initial release
- Services management endpoints
- Health professionals management endpoints
- Appointment booking and retrieval
- Email notifications
- Availability checking
- Comprehensive test coverage
