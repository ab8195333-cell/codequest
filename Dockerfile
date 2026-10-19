FROM webdevops/php-nginx:8.4-alpine

# تحديد مجلد العمل أولاً
WORKDIR /var/www/html

# نسخ ملفات المشروع إلى مجلد العمل
COPY . /var/www/html

# ضبط مسار الـ Web Root لـ Nginx
ENV WEBROOT=/var/www/html/public

# تثبيت الحزم 
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

# ضبط الصلاحيات للمجلدات
RUN chown -R application:application /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache