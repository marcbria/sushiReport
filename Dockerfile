FROM php:8-cli

RUN apt-get update && apt-get install -y \
    libxml2-dev \
    curl \
    libcurl4-openssl-dev \
    pkg-config \
    libssl-dev \
&& rm -rf /var/lib/apt/lists/*

COPY . /usr/src/sushiReport
WORKDIR /usr/src/sushiReport

CMD [ "php", "./sushi.php" ]

