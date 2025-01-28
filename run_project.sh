#!/bin/bash

# Ports to check
PORTS=(9000 9001)

# Loop through the ports and kill the processes using them
for PORT in "${PORTS[@]}"; do
  # Get the last process using the port
  PID=$(sudo lsof -t -i:$PORT | tail -n 1)

  # If there's a process using the port, kill only the last one
  if [ -n "$PID" ]; then
    echo "Port $PORT is in use by PID $PID. Killing the process..."
    sudo kill -9 $PID
  else
    echo "Port $PORT is free."
  fi
done

# Check if Quasar CLI is installed, if not install it
if ! command -v quasar &> /dev/null; then
  echo "Quasar CLI not found. Installing..."
  npm install -g @quasar/cli
fi

# Start Quasar for focus-admin
echo "Starting Quasar for focus-admin..."
cd focus-admin || exit
quasar dev &

# Wait for 3 seconds before starting the next process
sleep 3

# Start Quasar for focus-front
echo "Starting Quasar for focus-front..."
cd ../focus-front || exit
quasar dev &

# Wait for another 3 seconds before starting the socket server
sleep 3

# Start the socket server
echo "Starting the socket server..."
cd ../socket-server || exit
./run.sh &

echo "All processes started!"
