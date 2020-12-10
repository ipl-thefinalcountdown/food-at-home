import ioserver, { Socket } from 'socket.io';

let server = require('http').createServer()
let io = ioserver(server)

server.listen(8888, function () {
	console.log('Websocket server listening on *:8888')
})

io.on('connection', function (socket : Socket) {
	console.log('Client has connected. Socket ID = ' + socket.id)
})
