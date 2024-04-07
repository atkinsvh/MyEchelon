const express = require('express');
const { exec } = require('child_process');

const app = express();
const port = 3000;

app.use(express.static('public'));

// Route to handle force logout
app.get('/forceLogout', (req, res) => {
    exec('ssh username@hostname "logout command"', (error, stdout, stderr) => {
        if (error) {
            console.error(`Error executing command: ${error.message}`);
            return;
        }
        console.log(`Command output: ${stdout}`);
        console.error(`Command error: ${stderr}`);
    });
    res.send('Force logout command sent.');
});

// Define other routes for your actions similarly

app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
});
