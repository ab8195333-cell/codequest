FROM webdevops/php-nginx:8.4-alpine

# ضبط مسار الـ Web Root الخاص بصورة webdevops ليوجه Nginx إلى مجلد public
ENV WEBROOT=/var/www/html/public
ENV PHP_DOCUMENT_ROOT=/var/www/html/public

# تحديد مجلد العمل
WORKDIR /var/www/html

# نسخ ملفات المشروع
COPY . /var/www/html

# تثبيت الحزم بدون مشاكل
RUN composer install --no-dev --optimize-autoloader --no-scripts --ignore-platform-reqs

# ضبط الصلاحيات للمجلدات
RUN chown -R application:application /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache