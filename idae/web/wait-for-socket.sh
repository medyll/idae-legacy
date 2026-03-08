#!/bin/sh
set -eu

HOST=${SOCKET_HOST:-socket}
PORT=${SOCKET_PORT:-3005}
URL="http://$HOST:$PORT/health"
MAX_RETRIES=${MAX_RETRIES:-60}
SLEEP=${SLEEP:-2}

echo "Waiting for socket at $URL"
count=0
until curl -sf "$URL" > /dev/null; do
  count=$((count+1))
  if [ "$count" -ge "$MAX_RETRIES" ]; then
    echo "Socket did not become ready after $MAX_RETRIES attempts"
    exit 1
  fi
  sleep $SLEEP
done

echo "Socket is ready at $URL"
exit 0
