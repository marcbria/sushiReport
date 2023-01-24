FROM php:8.1-cli

RUN apt-get update && apt-get install -y \
    libxml2-dev \
    curl \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
&& rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install xml \
    && docker-php-ext-install curl \
    && docker-php-ext-enable xsl

COPY . /usr/src/sushiReport
WORKDIR /usr/src/sushiReport

CMD [ "php", "./sushi.php" ]

