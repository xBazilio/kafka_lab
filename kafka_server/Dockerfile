# syntax=docker/dockerfile:1
FROM debian:stable
RUN ["apt", "update"]
RUN ["apt", "install", "-y", "default-jre"]
VOLUME ["/opt/kafka"]
WORKDIR "/opt/kafka"
CMD ["bin/kafka-server-start.sh", "config/server.properties"]
