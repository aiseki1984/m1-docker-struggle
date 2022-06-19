FROM golang:1.15 AS builder
RUN go get -d -v github.com/mailhog/mhsendmail \
  && cd /go/src/github.com/mailhog/mhsendmail/ \
  && GOOS=linux GOARCH=arm64 go build -o mhsendmail .



FROM php:7.4-apache

# Use Mailcatcher
# RUN apt-get update -qq \
#   && apt-get install -y \
#     ruby \
#     ruby-dev \
#     rubygems \
#     libsqlite3-dev \
#   && apt-get autoremove \
#   && apt-get clean
# RUN gem install mailcatcher

# Use mailhog
# install mhsendmail
# RUN curl -sSL https://github.com/mailhog/mhsendmail/releases/download/v0.2.0/mhsendmail_linux_amd64 -o mhsendmail \
#     && chmod +x mhsendmail \
#     && mv mhsendmail /usr/local/bin/mhsendmail

COPY --from=builder /go/src/github.com/mailhog/mhsendmail/mhsendmail /usr/local/bin/
# RUN chmod +x /usr/local/bin/mhsendmail \
#      && echo 'sendmail_path = "/usr/local/bin/mhsendmail --smtp-addr=mail:1025"' > /usr/local/etc/php/conf.d/sendmail.ini

# COPY ./php.ini /usr/local/etc/php/
