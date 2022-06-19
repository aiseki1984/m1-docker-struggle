# mailhog 動かない問題

```shell
$ docker run -p 8025:8025 -p 1025:1025 -d --name=mailhog mailhog/mailhog
WARNING: The requested image\'s platform (linux/amd64) does not match the detected host platform (linux/arm64/v8) and no specific platform was requested
$ docker run -p 8025:8025 -it mailhog/mailhog:latest
$ docker run --platform linux/amd64 -p 8025:8025 -it mailhog/mailhog:latest
# 代替案　Mailcatcher
$ docker run -d -p 1080:1080 --name mailcatcher schickling/mailcatcher
# 動いた！
```

これからは Mailhog ではなくて Mailcatcher を使っていくか。。。
https://goodlife.tech/posts/mailcatcher
https://github.com/markshust/docker-magento/issues/433

```yml
mail:
  image: schickling/mailcatcher
  ports:
    - "1080:1080"
    - "1025:1025"
```

## Mailhog を build する

どうしても Mailhog を使いたいので、自分でビルドして使う。野良 image を参考に
https://hub.docker.com/r/cd2team/mailhog

```Dockerfile
ARG GOTAG=1.18-alpine
FROM golang:${GOTAG} as builder
# MAINTAINER CD2Team <codesign2@icloud.com>

RUN set -x \
  && buildDeps='git musl-dev gcc' \
  && apk add --update $buildDeps \
  && GOPATH=/tmp/gocode go install github.com/mailhog/MailHog@latest

FROM alpine:latest
WORKDIR /bin
COPY --from=builder tmp/gocode/bin/MailHog /bin/MailHog
EXPOSE 1025 8025
ENTRYPOINT ["MailHog"]
```

これで Mailhog の image を構築する

## mhsendmail

https://github.com/mailhog/mhsendmail/issues/28
こちらも同様に、GO でビルドしてから、使いたい image にコピーする

```Dockerfile
FROM golang:1.15 AS builder
RUN go get -d -v github.com/mailhog/mhsendmail \
  && cd /go/src/github.com/mailhog/mhsendmail/ \
  && GOOS=linux GOARCH=arm64 go build -o mhsendmail .

FROM php:7.4-apache
COPY --from=builder /go/src/github.com/mailhog/mhsendmail/mhsendmail /usr/local/bin/
```

sendmail の path を`sendmail_path = "/usr/local/bin/mhsendmail --smtp-addr=mailhog:1025"`のようにすることを忘れないように。（今回は php なので php.ini）

# M1 で mysql 動かない問題

mysql:8 では動かなかったので

```Dockerfile
FROM mysql:8.0.29-oracle
```

のようにした。
docker-compose.yml で、platform を指定する必要はなし。

# mysql chown permission denied

lima.yml の設定で、mountType: 9p にして、mouts の location の設定も

```yml
mountType: 9p
- location: "~/_workspace"
  writable: true
  9p:
    cache: "mmap"
```

のようにすべし。
permissiondenied の時に生成されたフォルダ等は `rm -rf ./dabtabase` のようにして消してから、再起動すること
https://zenn.dev/wim/articles/build_lima_environment_on_m1_mac
