#!/bin/bash
docker run -it \
  --network=kafka-lab \
  --volume="$PWD/src:/var/www" \
  kafka_lab_phpcli1 \
  /bin/bash