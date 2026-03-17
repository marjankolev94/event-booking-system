# Event Booking System API

Event Booking System API is Restful API for managing Events & Bookings and is built using PHP/Laravel.

## Features

- User registration & login (API tokens)
- Create, list, view, and delete events
- Create bookings for events
- Prevent overbooking (capacity check)
- Update booking status (pending, confirmed, cancelled)
- Filter bookings by email
- Event progress (Percentage of Booked Seats)

## Tech Stack
- Laravel
- MySQL
- Laravel Sanctum (API Authentication)
- PHPUnit (Testing)

## Installation
1. Clone the repository:
   ```bash
   git clone https://github.com/marjankolev94/event-booking-system.git

2. Navigate to the project directory:
   ```bash
   cd event-booking-system

3. Install dependencies:
   ```bash
   composer install

4. Set up environment variables:
- Copy `.env.example` to `.env` and configure your database and application settings as needed.

5. Run migrations:
   ```bash
   php artisan migrate
   
6. Serve the application
   ```bash
   php artisan serve

   There is no visual presentation of this app.

7. Authentication

   This API uses Bearer Token (Sanctum).

    After login, include token in headers:
   ```bash
   Authorization: Bearer YOUR_TOKEN
   Accept: application/json

## API Endpoints    
- Auth
    ```bash
  POST /api/register
  POST /api/login
- Events
    ```bash
    POST /api/events
    GET /api/events
    GET /api/events/{id}
    DELETE /api/events/{id}
    GET /api/events/{id}/progress
- Bookings
    ```bash
    POST /api/events/{event_id}/bookings
    GET /api/events/{event_id}/bookings
    PATCH /api/bookings/{id}
    GET /api/bookings/search?email=example@test.com

Example: Event Create

    POST http://localhost:8000/api/events
    Body:
    {
        "name": "Concert Nr. 5",
        "description": "This is the sixth Concert for this year",
        "start_date": "2026-03-20 14:00:00",
        "end_date": "2026-03-31 00:00:00",
        "capacity": 20
    }

## Runing Tests
Set up testing database in `.env.testing`, then run:
`php artisan test`

## Prerequisites
- PHP and Laravel are installed on your system
- A web server environment (such as XAMPP or Laravel Sail)


### Usage
- Postman can be used for testing the API calls
