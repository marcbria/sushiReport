FROM php:8.1-cli

RUN apt-get update && apt-get install -y \
    curl \
    libxml2-dev \
    libxslt-dev \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
&& rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install curl xml xsl\
    && docker-php-ext-install curl xml xsl

COPY . /usr/src/sushiReport
WORKDIR /usr/src/sushiReport

CMD [ "php", "./sushiReport.php" ]
