FROM webdevops/php-nginx:8.4-alpine

# تحديد مجلد العمل
WORKDIR /var/www/html

# نسخ ملفات المشروع بالكامل
COPY . /var/www/html

# نسخ ملف إعدادات Nginx المخصص لتوجيه المسار إلى public بشكل صحيح
COPY docker/nginx.conf /opt/docker/etc/nginx/vhost.conf

# تثبيت الحزم بدون مشاكل
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

# ضبط الصلاحيات للمجلدات
RUN chown -R application:application /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache