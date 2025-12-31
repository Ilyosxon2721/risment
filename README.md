# RISMENT - Professional Fulfillment for Marketplaces

Платформа фулфилмента для маркетплейсов Узбекистана (Uzum, Wildberries, Ozon, Yandex Market).

## 🚀 Features

### Core Functionality
- ✅ Multi-language support (Russian / Uzbek)
- ✅ Subscription plans management
- ✅ FBS/FBO cost calculators  
- ✅ Marketplace management services
- ✅ Client dashboard
- ✅ Invoicing system
- ✅ Support ticket system
- ✅ CMS for content management
- ✅ Email notifications

### Technical Stack
- **Backend:** Laravel 11
- **Frontend:** Blade templates + Vanilla JS/CSS  
- **Database:** MySQL 8.0+
- **Cache/Queue:** Redis
- **Payments:** Click, Payme
- **Analytics:** Google Analytics, Yandex Metrika

## 📋 Requirements

- PHP 8.2+
- MySQL 8.0+
- Redis
- Composer 2.x
- Node.js 18.x+
- NPM 9.x+

## 🔧 Installation

### 1. Clone Repository
```bash
git clone https://github.com/Ilyosxon2721/risment.git
cd risment
```

### 2. Install Dependencies
```bash
composer install
npm install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

Edit `.env` with your database and mail credentials.

### 4. Database Setup
```bash
php artisan migrate
php artisan db:seed
```

### 5. Build Assets
```bash
npm run build
```

### 6. Storage Link
```bash
php artisan storage:link
```

### 7. Run Development Server
```bash
php artisan serve
```

Visit: `http://localhost:8000`

## 🌐 Production Deployment

See [DEPLOYMENT.md](DEPLOYMENT.md) for detailed production deployment guide.

### Quick Deploy (on server)
```bash
sudo chmod +x deploy.sh
sudo ./deploy.sh
```

## 📁 Project Structure

```
risment/
├── app/
│   ├── Http/Controllers/      # Application controllers
│   ├── Models/                # Eloquent models
│   ├── Services/              # Business logic services
│   └── Mail/                  # Email notifications
├── database/
│   ├── migrations/            # Database migrations
│   └── seeders/               # Database seeders
├── resources/
│   ├── views/                 # Blade templates
│   ├── lang/                  # Translations (ru, uz)
│   └── css/js/               # Frontend assets
├── routes/
│   └── web.php               # Web routes
├── public/                   # Public assets
└── storage/                  # File storage
```

## 🔐 Security

- All passwords are hashed with bcrypt
- CSRF protection enabled
- SQL injection prevention via Eloquent ORM
- XSS protection in blade templates
- Environment variables for sensitive data

## 📊 Environment Variables

Key environment variables (see `.env.example` for full list):

```env
APP_NAME=RISMENT
APP_ENV=production
APP_URL=https://yourdomain.com

DB_DATABASE=risment_db
DB_USERNAME=risment_user
DB_PASSWORD=your_password

MAIL_HOST=smtp.gmail.com
MAIL_USERNAME=your-email@gmail.com

GOOGLE_ANALYTICS_ID=G-XXXXXXXXXX
YANDEX_METRIKA_ID=XXXXXXXX

CLICK_MERCHANT_ID=xxx
PAYME_MERCHANT_ID=xxx
```

## 🧪 Testing

```bash
php artisan test
```

## 📝 Development

### Clear Caches
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Optimize for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
composer dump-autoload --optimize
```

## 🗺️ Roadmap

See [TODO.md](TODO.md) for detailed roadmap and upcoming features.

**Current Status:** 93% ready for production

### Next Steps:
- Payment UI completion
- Analytics ID configuration
- Final security audit
- Performance optimization

## 📞 Support

For issues or questions:
- Create an issue in this repository
- Contact: support@risment.uz

## 📄 License

Proprietary - All rights reserved © 2024-2026 RISMENT

## 👥 Contributing

This is a private project. For contributions, please contact the project maintainers.

---

**Last Updated:** January 1, 2026  
**Version:** 1.0-rc (Release Candidate)
