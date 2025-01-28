#!/bin/bash

# Navigate to the focus-admin and run Quasar dev
echo "Starting Quasar for focus-admin..."
cd focus-admin || exit
quasar dev &

# Wait for 3 seconds before starting the next process
sleep 3

# Navigate to focus-front and run Quasar dev
echo "Starting Quasar for focus-front..."
cd ../focus-front || exit
quasar dev &

# Wait for another 3 seconds before starting the socket server
sleep 3

# Navigate to socket-server and start the server
echo "Starting the socket server..."
cd ../socket-server || exit
./run.sh &

echo "All processes started!"
