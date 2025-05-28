FROM trellis-api:latest as builder
RUN php artisan trellis:download-app --latest --api-endpoint=http://localhost:9213

FROM nginx:stable

COPY --from=builder /var/www/trellis-app/www /var/www/trellis-app/www

EXPOSE 80

COPY ./docker/conf.d/*.conf /etc/nginx/conf.d/
RUN rm /etc/nginx/conf.d/default.conf
# RUN mv ./docker/default.nginx.conf /etc/nginx/nginx.conf
# RUN nginx -t -c /etc/nginx/nginx.conf

# CMD ["nginx", "-g", "daemon off;"]
