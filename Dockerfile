FROM webdevops/php-nginx:8.4-alpine

# تحديد مجلد العمل
WORKDIR /var/www/html

# نسخ ملفات المشروع
COPY . .

# ضبط المتغيرات
ENV WEBROOT=/var/www/html/public
ENV PHP_MEMORY_LIMIT=512M

# تحديث وتثبيت حزم Composer بالاعتماد على PHP 8.4
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

# ضبط الصلاحيات للمجلدات
RUN chown -R application:application /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache