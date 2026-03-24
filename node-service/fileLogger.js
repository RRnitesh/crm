const fs = require('fs');
const path = require('path');

const filePath = path.join(__dirname, 'breached_tickets.txt');

function logTicketsToFile(ticketIds) {
    if (!ticketIds) return;

    const data = ticketIds.join(', ') + '\n';

    fs.appendFile(filePath, data, (err) => {
        if (err) console.error('Failed to write to file:', err);
        else console.log('Tickets appended to file:', filePath);
    });
}

module.exports = { logTicketsToFile };