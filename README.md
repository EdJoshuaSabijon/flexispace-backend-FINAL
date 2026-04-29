# FlexiSpace - Modular Furniture E-Commerce

A full-stack e-commerce application for modular furniture, built with Laravel (backend) and React + Vite (frontend).

## Features

- **Customer Features**
  - Product browsing with search and category filters
  - Shopping cart functionality
  - Order placement and tracking
  - User dashboard with order history
  - Real-time notifications for order updates

- **Admin Features**
  - Product management (CRUD + image upload)
  - Order management with status updates
  - Customer management
  - Dashboard with statistics

## Tech Stack

### Backend
- Laravel 12
- Laravel Sanctum (API Authentication)
- MySQL Database
- Mailtrap (Email)

### Frontend
- React 18
- Vite
- React Router DOM
- Axios
- Tailwind CSS
- Lucide React (Icons)

## Project Structure

```
flexispace-backend/    # Laravel Backend
flexispace-frontend/   # React Frontend
```

## Backend Setup

1. **Install Dependencies**
```bash
cd flexispace-backend
composer install
```

2. **Environment Configuration**
```bash
cp .env.example .env
```

Configure your `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=flexispace
DB_USERNAME=root
DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mailtrap_username
MAIL_PASSWORD=your_mailtrap_password
MAIL_FROM_ADDRESS=noreply@flexispace.com
```

3. **Run Migrations and Seeders**
```bash
php artisan migrate
php artisan db:seed
```

This will create:
- Admin user: `admin@flexispace.com` / `admin123`
- 5 sample furniture products

4. **Create Storage Link**
```bash
php artisan storage:link
```

5. **Start Development Server**
```bash
php artisan serve
```

Backend will run on `http://localhost:8000`

## Frontend Setup

1. **Install Dependencies**
```bash
cd flexispace-frontend
npm install
```

2. **Environment Configuration**
The `.env` file is already configured:
```env
VITE_API_URL=http://localhost:8000
```

3. **Start Development Server**
```bash
npm run dev
```

Frontend will run on `http://localhost:5173`

## Deployment

### Backend (Railway)

1. Push your code to GitHub
2. Create a new project on Railway
3. Connect your GitHub repository
4. Configure environment variables in Railway
5. Railway will automatically detect the Procfile and deploy

### Frontend (Vercel)

1. Push your code to GitHub
2. Create a new project on Vercel
3. Connect your GitHub repository
4. Configure environment variables:
   - `VITE_API_URL`: Your deployed backend URL
5. Deploy

## API Endpoints

### Authentication
- `POST /api/register` - Register new user
- `POST /api/login` - Customer login
- `POST /api/admin/login` - Admin login
- `POST /api/logout` - Logout
- `GET /api/user` - Get current user

### Products
- `GET /api/products` - List all products
- `POST /api/products` - Create product (Admin)
- `PUT /api/products/{id}` - Update product (Admin)
- `DELETE /api/products/{id}` - Delete product (Admin)
- `POST /api/products/{id}/image` - Upload product image (Admin)

### Orders
- `GET /api/orders` - List orders (Admin sees all, Customer sees own)
- `POST /api/orders` - Place order
- `PUT /api/orders/{id}/status` - Update order status (Admin)

### Notifications
- `GET /api/notifications` - List user notifications
- `PUT /api/notifications/{id}/read` - Mark notification as read

### Admin
- `GET /api/admin/customers` - List all customers (Admin)

## Default Credentials

**Admin:**
- Email: `admin@flexispace.com`
- Password: `admin123`

## License

This project is open-sourced software licensed under the MIT license.
