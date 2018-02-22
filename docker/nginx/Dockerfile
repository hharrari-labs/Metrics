FROM debian:jessie
 
MAINTAINER Mathieu Thomas <mthomas@blahp.fr>
 
RUN apt-get update && apt-get install -y \
    nginx
 
ADD nginx.conf /etc/nginx/
ADD Metrics-SF2-highcharts.conf /etc/nginx/sites-available/
 
RUN ln -s /etc/nginx/sites-available/Metrics-SF2-highcharts.conf /etc/nginx/sites-enabled/Metrics-SF2-highcharts
RUN rm /etc/nginx/sites-enabled/default
 
RUN echo "upstream php-upstream { server php:9000; }" > /etc/nginx/conf.d/upstream.conf
 
RUN usermod -u 501 www-data
 
CMD ["nginx"]
 
EXPOSE 80
EXPOSE 443