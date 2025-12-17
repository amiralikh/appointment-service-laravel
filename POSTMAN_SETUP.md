# Postman Collection Setup Guide

This guide will help you import and use the Appointment Manager API Postman collection.

## Files Included

- `Appointment-Manager-API.postman_collection.json` - Main API collection with all endpoints
- `Appointment-Manager.postman_environment.json` - Environment variables for local development

## Import Instructions

### 1. Import the Collection

1. Open Postman
2. Click on **Import** button (top left)
3. Select the file: `Appointment-Manager-API.postman_collection.json`
4. Click **Import**

### 2. Import the Environment

1. Click on **Import** button
2. Select the file: `Appointment-Manager.postman_environment.json`
3. Click **Import**
4. Select **"Appointment Manager - Local"** from the environment dropdown (top right)

### 3. Configure Base URL

The collection uses the environment variable `{{base_url}}` which is set to `http://localhost` by default.

**To change the base URL:**

1. Click the environment dropdown (top right)
2. Select **"Appointment Manager - Local"**
3. Click the eye icon to view variables
4. Edit the `base_url` value (e.g., `http://localhost:8000`, `https://your-domain.com`)
5. Click **Save**

## Collection Structure

The collection is organized into three main folders:

### 1. Services
- **GET** Get All Services - List all available services
- **GET** Get Service by ID - Retrieve a specific service

### 2. Health Professionals
- **GET** Get All Health Professionals - List all health professionals
- **GET** Get Health Professional by ID - Retrieve a specific professional

### 3. Appointments
- **POST** Create Appointment - Book a new appointment
- **GET** Get Appointment by ID - Retrieve appointment details

## Example Requests

### Get All Services
```
GET {{base_url}}/api/v1/services
```

### Create Appointment
```
POST {{base_url}}/api/v1/appointments
Content-Type: application/json

{
    "service_id": 1,
    "health_professional_id": 1,
    "customer_email": "john.doe@example.com",
    "date": "2025-12-20 14:00:00",
    "notes": "First time patient"
}
```

## Sample Responses

Each request in the collection includes example responses:

- **Success responses** (200, 201) - What you'll get when the request succeeds
- **Error responses** (404, 422) - What you'll see when there are validation errors or resources not found

## Testing Workflow

### 1. Setup Database
First, ensure your local database is seeded with data:

```bash
php artisan migrate:fresh
php artisan db:seed-realistic
```

### 2. Test Sequence

Follow this sequence to test the complete booking flow:

1. **Get All Services** - Choose a service ID
2. **Get All Health Professionals** - Choose a professional ID
3. **Create Appointment** - Use the IDs from steps 1 and 2
4. **Get Appointment by ID** - Verify the created appointment

### 3. Test Validation

Try these scenarios to test validation:

- Create appointment with invalid email
- Create appointment with past date
- Create appointment with non-existent service ID
- Try to book the same professional at the same time (double booking)

## Environment Variables

Current variables in the environment:

| Variable | Default Value | Description |
|----------|---------------|-------------|
| `base_url` | `http://localhost` | Base URL for the API |
| `api_version` | `v1` | API version |

### Adding More Variables

You can add more variables for testing:

1. Click the environment dropdown
2. Click **Edit** on your environment
3. Add new variables:
   - `service_id` - Store a service ID for reuse
   - `professional_id` - Store a professional ID for reuse
   - `appointment_id` - Store created appointment ID

## Tips

### Auto-Save IDs

You can add test scripts to automatically save IDs from responses:

**Example: Save appointment ID after creation**

In the "Create Appointment" request, go to the **Tests** tab and add:

```javascript
const response = pm.response.json();
if (response.data && response.data.id) {
    pm.environment.set("appointment_id", response.data.id);
}
```

Then use `{{appointment_id}}` in subsequent requests.

### Pre-request Scripts

Set dynamic dates for appointments:

In the "Create Appointment" request, go to **Pre-request Script** tab:

```javascript
// Set appointment date to 7 days from now at 2 PM
const futureDate = new Date();
futureDate.setDate(futureDate.getDate() + 7);
futureDate.setHours(14, 0, 0, 0);

const formattedDate = futureDate.toISOString()
    .slice(0, 19)
    .replace('T', ' ');

pm.environment.set("appointment_date", formattedDate);
```

Then in the request body, use: `"date": "{{appointment_date}}"`

## Common Issues

### Issue: Connection Refused
**Solution:** Make sure your Laravel application is running:
```bash
php artisan serve
```

### Issue: 404 Not Found
**Solution:** Verify the base URL includes the port if needed:
- Change from `http://localhost` to `http://localhost:8000`

### Issue: CSRF Token Mismatch
**Solution:** This is an API, so CSRF protection should be disabled for API routes. Verify in `bootstrap/app.php` or middleware configuration.

### Issue: Database Empty
**Solution:** Seed the database:
```bash
php artisan db:seed-realistic
```

## Additional Resources

- **API Documentation:** See `API_DOCUMENTATION.md` for detailed endpoint documentation
- **PHPStan:** Run `vendor/bin/phpstan analyse` to check code quality
- **Tests:** Run `php artisan test` to execute all tests

## Support

For issues or questions:
1. Check the API documentation
2. Run the test suite to verify functionality
3. Check Laravel logs at `storage/logs/laravel.log`

---

**Version:** 1.0.0
**Last Updated:** December 17, 2025
