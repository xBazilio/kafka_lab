# syntax=docker/dockerfile:1
FROM debian:stable
RUN ["apt", "update"]
RUN ["apt", "install", "-y", "default-jre"]
VOLUME ["/opt/kafka"]
VOLUME ["/opt/zookeeper"]
WORKDIR "/opt/kafka"
CMD ["bin/zookeeper-server-start.sh", "config/zookeeper.properties"]
