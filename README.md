# DK Infotech Website

<a href="https://internalmgmt.dkinfotechsolutions.com/">Here you go</a>

DK Infotech is a full-stack company website developed to provide a professional digital presence for the organization. The application combines a responsive frontend with a Laravel and PHP-based backend, MySQL database integration, and dynamic content management.

---

## Features

### Backend

* **Laravel Backend**:

  * Built using Laravel and PHP for server-side application development.
* **Database Management**:

  * MySQL database for storing and managing application data.
* **Dynamic Content**:

  * Backend-driven content management and database operations.
* **API Integration**:

  * RESTful API endpoints for communication between the frontend and backend.
* **Server-Side Logic**:

  * Laravel controllers, models, routes, and middleware for application functionality.

### Frontend

* **Responsive UI**:

  * Responsive interface designed for desktop, tablet, and mobile devices.
* **Bootstrap**:

  * Bootstrap used for responsive layouts and UI components.
* **jQuery**:

  * jQuery used for dynamic interactions and client-side functionality.
* **HTML & CSS**:

  * Structured and styled using HTML5 and CSS3.
* **JavaScript**:

  * Client-side functionality and interactive features.

---

## Project Structure

```text
DK-Infotech-Website/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   └── Middleware/
│   ├── Models/
│   └── ...
│
├── config/
├── database/
│   ├── migrations/
│   └── seeders/
│
├── public/
│   ├── css/
│   ├── js/
│   ├── images/
│   └── ...
│
├── resources/
│   ├── views/
│   ├── css/
│   └── js/
│
├── routes/
│   ├── web.php
│   └── api.php
│
├── storage/
├── .env
├── artisan
└── composer.json
```

---

## Installation

### Prerequisites

* PHP 8.1 or higher
* Composer
* MySQL
* Laravel
* Apache/XAMPP or another PHP-compatible server

### Steps

1. Clone the repository:

```bash
git clone https://github.com/achitrans/DK-Infotech-Website.git
cd DK-Infotech-Website
```

2. Install PHP dependencies:

```bash
composer install
```

3. Create the environment file:

```bash
cp .env.example .env
```

4. Configure your MySQL database in the `.env` file:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database
DB_USERNAME=your_username
DB_PASSWORD=your_password
```

5. Generate the Laravel application key:

```bash
php artisan key:generate
```

6. Run database migrations:

```bash
php artisan migrate
```

7. Start the Laravel development server:

```bash
php artisan serve
```

The application will be available at:

```text
http://127.0.0.1:8000
```

---

## API Endpoints

The application uses Laravel API routes for backend communication.

### Website Routes

* `GET /` — Homepage
* `GET /about` — Company information
* `GET /services` — Company services
* `GET /contact` — Contact section

### API Routes

* `GET /api/...` — Retrieve application data
* `POST /api/...` — Create or submit data
* `PUT /api/...` — Update existing data
* `DELETE /api/...` — Delete data

> Update the endpoint list above with the exact API routes implemented in the project.

---

## Technologies Used

### Backend

* Laravel
* PHP
* MySQL
* Laravel Eloquent ORM
* REST APIs

### Frontend

* HTML5
* CSS3
* JavaScript
* jQuery
* Bootstrap

---

## Contributing

Contributions are welcome! If you would like to improve the project:

1. Fork the repository.
2. Create a new branch.
3. Make your changes.
4. Commit your changes.
5. Push your branch.
6. Submit a Pull Request.

---

## Acknowledgments

Special thanks to **DK Infotech** for providing the opportunity to work on this project and gain practical experience in full-stack web development using Laravel, PHP, MySQL, jQuery, and Bootstrap.

---

## Developer

**Abhilasha Chitrans**

GitHub: [@achitrans](https://github.com/achitrans)
