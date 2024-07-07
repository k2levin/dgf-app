# Booking Ticket API

## Requirements
- [x] 1. Implement user authentication and authorization.
- [x] 2. CRUD operations for events and ticket bookings.
- [x] 3. Use Laravel's Eloquent ORM with optimistic locking for handling concurrency.
- [x] 4. Apply middleware for rate limiting to prevent abuse of the API.
- [x] 5. Ensure that the API responses are consistent and provide meaningful error messages in case of booking conflicts.
- [x] 6. Write database migration scripts for the required database structure, considering the need for handling concurrency.
- [x] 7. Use Laravel's form request validation to validate booking data.

## Tasks
- [x] 1. Provide the migration script for the database structure, including any fields necessary for optimistic locking.
- [x] 2. Write the necessary Eloquent models and define their relationships, including the method for handling optimistic locking.
- [x] 3. Create the API routes and associate them with the appropriate controller methods, ensure that routes handling bookings are designed to handle concurrency.

## Optional
- [x] - Include test cases using Laravel's testing facilities to simulate concurrent booking scenarios and ensure the system handles them correctly.
- [x] - Documentration or a tool like Swagger to demonstrate API usage.

## Setup Steps
- tested with PHP 8.1.22 and MySQL version: 8.0.33
- `composer install`
- `php artisan migrate:refresh --seed`
- `php artisan test`
- view API Documentation at folder "swagger/index.html"
