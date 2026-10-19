FROM php:8.2-fpm

# تثبيت المتطلبات ونظام الـ PostgreSQL إذا كنت تستخدم Supabase
RUN apt-get update && apt-get install -y \
    git \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libpq-dev \
    zip \
    unzip

# تثبيت الحزم المطلوبة لـ PHP بما فيها pdo_pgsql للاتصال بـ Supabase
RUN docker-php-ext-install pdo pdo_mysql pdo_pgsql mbstring exif pcntl bcmath gd

# تثبيت Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# تحديد المجلد الرئيسي للعمل
WORKDIR /var/www/html

# نسخ ملفات المشروع إلى الحاوية
COPY . .

# تثبيت حزم لارافيل عبر Composer
RUN composer install --no-dev --optimize-autoloader

# ضبط الصلاحيات للمجلدات
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache