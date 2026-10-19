FROM webdevops/php-nginx:8.3-alpine

# تحديد مجلد العمل
WORKDIR /var/www/html

# نسخ ملفات المشروع
COPY . .

# ضبط المتغيرات
ENV WEBROOT=/var/www/html/public
ENV PHP_MEMORY_LIMIT=512M

# تثبيت حزم Composer للإنتاج
RUN composer install --no-dev --optimize-autoloader --no-scripts

# ضبط الصلاحيات للمجلدات التي يحتاجها لارافيل
RUN chown -R application:application /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache