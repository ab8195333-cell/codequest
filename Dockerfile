FROM richarvey/nginx-php-fpm:latest

# نسخ ملفات المشروع إلى مجلد العمل
COPY . /var/www/html

# تحديد مجلد التشغيل العام لـ Nginx
ENV WEBROOT=/var/www/html/public
ENV RUN_SCRIPTS=1
ENV SKIP_COMPOSER=0

# تثبيت Composer وتحديث حزم التوافقية
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
RUN composer update --no-dev --optimize-autoloader --ignore-platform-reqs

# تحديد مجلد العمل
WORKDIR /var/www/html