const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const multer = require('multer');
const path = require('path');
const cors = require('cors');

const app = express();
const server = http.createServer(app);
const io = new Server(server, {
  cors: {
    origin: "*",
    methods: ["GET", "POST"],
    credentials: true,
  },
});

// Store user tokens with their expiration times
const userTokens = new Map(); // Use a Map to store token data by socket ID

// Middleware for CORS
app.use(cors());

// Middleware for serving static files from the uploads directory
app.use('/uploads', express.static(path.join(__dirname, 'uploads')));

// Middleware for file upload
const storage = multer.diskStorage({
  destination: (req, file, cb) => {
    cb(null, 'uploads/');
  },
  filename: (req, file, cb) => {
    cb(null, Date.now() + path.extname(file.originalname)); // Unique filename
  },
});

const upload = multer({ storage });

// Flag to check if the file-upload event has been emitted
let fileUploadEmitted = false;

// Socket.IO connection
io.on('connection', (socket) => {
  console.log('A user connected');

  // Handle storing the token when 'sendToken' is called from another site
  socket.on('sendToken', (token) => {
    console.log('Received token:', token);

    const expirationTime = Date.now() + 3600000; // Set expiration time for 1 hour
    userTokens.set(socket.id, { token, expirationTime });
    console.log('Token stored successfully.');
  });

  // Handle token validation
// Handle token validation
socket.on('sendToken', (userID, token) => {
  console.log('Received token for user:', userID, token);

  const expirationTime = Date.now() + 3600000; // 1-hour expiration
  userTokens.set(userID, { token, expirationTime });
  console.log('Token stored successfully for user:', userID);
});

// Validate token using the user identifier
socket.on('sendToken2', (userID, token23) => {
  console.log('Received token2 for user:', userID, token23);

  // Retrieve token data for the given userID
  const storedTokenData = userTokens.get(userID);
  
  if (storedTokenData) {
    console.log('Stored token data:', storedTokenData);

    // Check if the token matches and it hasn't expired
    if (storedTokenData.token === token23 && Date.now() < storedTokenData.expirationTime) {
      console.log('Token is valid for user:', userID);
      socket.emit('tokenValidated', true); // Notify client of valid token
    } else {
      console.log('Invalid or expired token for user:', userID);
      socket.emit('tokenValidated', false); // Notify client of invalid token
    }
  } else {
    console.log('No token found for user:', userID);
    socket.emit('tokenValidated', false); // Notify client if no token is found
  }
});


  socket.on('senderTriggerness', (action, id) => {
    io.emit('receiverTriggerness', action, id);
  });

  socket.on('joinRoom', (room, username) => {
    socket.join(room);
    socket.to(room).emit('recieverkoto', { username: 'System', text: `${username} has joined the chat conversation.` });
  });

  socket.on('message', ({ room, msg }) => {
    io.to(room).emit('recieverkoto', msg);
  });

  socket.on('typing', ({ room }) => {
    socket.to(room).emit('typing');
  });

  socket.on('disconnect', () => {
    console.log('A user disconnected');
    // // Remove token data when the socket disconnects
    // userTokens.delete(socket.id);
  });
});

// Upload endpoint
app.post('/upload', upload.single('file'), (req, res) => {
  if (!req.file) {
    console.log('No file uploaded.');
    return res.status(400).send({ error: 'No file uploaded.' });
  }

  console.log('File uploaded:', req.file);
  const fileUrl = `uploads/${req.file.filename}`; // Full URL of the uploaded file
  // Emit the socket event only if it hasn't been emitted yet
  if (!fileUploadEmitted) {
    io.emit('file-upload', {
      name: req.file.filename,
      type: req.file.mimetype,
    });

    // Set the flag to true to prevent further emissions
    fileUploadEmitted = true;

    // Optionally reset the flag after a timeout (e.g., after 1 second)
    setTimeout(() => {
      fileUploadEmitted = false; // Reset the flag
    }, 1000); // Adjust the timeout as needed
  }

  // Send response back once
  res.json({ file: fileUrl }); // Return the URL of the uploaded file
});

// Start the server
const PORT = process.env.PORT || 5500;
server.listen(PORT, () => {
  console.log(`WebSocket server is running on http://localhost:${PORT}`);
});
