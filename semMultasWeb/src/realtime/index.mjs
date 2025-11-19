// index.mjs
import express from 'express';
import { createServer } from 'node:http';
import { join } from 'node:path';
import { Server } from 'socket.io';
import bodyParser from 'body-parser';
import cors from 'cors';

const app = express();
const server = createServer(app);
const io = new Server(server, {
  cors: {
    origin: '*',
  },
});

app.use(bodyParser.json());
app.use(cors());

app.get('/', (req, res) => {
  res.sendFile(join(__dirname, 'index.html'));
});

app.post('/proccess', (req, res) => {
  const proccess = req.body;
  io.emit('proccess', proccess);
  res.status(200).send({
    status: 'Proccess updated',
    data: proccess,
  });
});

io.on('connection', (socket) => {
  console.log('a user connected');

  socket.on('disconnect', () => {
    console.log('user disconnected');
  });

  socket.on('error', (error) => {
    console.error('Socket error:', error);
  });

  socket.on('connect_error', (error) => {
    console.error('Connect error:', error);
  });
});

server.listen(3000, '0.0.0.0', () => {
  console.log('listening on *:3000');
});

// Adicione um tempo de espera para o servidor
server.setTimeout(60000); // 60 segundos