FROM webdevops/php-nginx:8.4-alpine

# تحديد مجلد العمل ومسار الـ Web Root لـ Nginx
ENV WEBROOT=/var/www/html/public

# نسخ ملفات المشروع إلى الحاوية
COPY . /var/www/html

# تثبيت الحزم بدون مشاكل
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

# ضبط الصلاحيات للمجلدات
RUN chown -R application:application /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache