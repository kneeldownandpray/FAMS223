#!/bin/bash

PORT=5500
SERVER_SCRIPT="server.js"

# Get the last process using the port
PID=$(sudo lsof -t -i:$PORT | tail -n 1)

# If there's a process using the port, kill only the last one
if [ -n "$PID" ]; then
  echo "Port $PORT is in use by PID $PID. Killing the process..."
  sudo kill -9 $PID
else
  echo "Port $PORT is free."
fi

# Start the server
echo "Starting the server..."
node $SERVER_SCRIPT

# Check if the server started correctly
if [ $? -eq 0 ]; then
  echo "Server started successfully!"
else
  echo "Failed to start the server."
fi
