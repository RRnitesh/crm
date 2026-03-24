const express = require('express');
const bodyParser = require('body-parser');
const cors = require('cors');
const { logTicketsToFile } = require('./fileLogger'); 

const app = express();
const PORT = 5000;

// Middleware
app.use(cors());
app.use(bodyParser.json());

app.post('/api/log-breached-tickets', (req, res) => {
    const { ticketIds } = req.body;

    logTicketsToFile(ticketIds);

    console.log('Breached Tickets Received:', ticketIds);
});

// Start server
app.listen(PORT, () => {
    console.log(`Node.js service running on http://localhost:${PORT}`);
});