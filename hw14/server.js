require('dotenv').config()

// set up express server
const express = require('express');
const app = express();
const mongoose = require('mongoose');
var port = process.env.PORT || 3000;

// connect to mongodb database through mongoose
mongoose.connect(process.env.MONGODB_URI)
const db = mongoose.connection;
db.on('error', (error) => console.error(error))
db.once('open', () => console.log("Connected to MongoDB Database"))

// so that the server can read JSON data from the database
app.use(express.urlencoded({extended: true})); 
app.use(express.json());

// bypass annoying CORS errors
app.use(function(req, res, next) {
    res.setHeader("Access-Control-Allow-Origin", "*");
    res.setHeader("Access-Control-Allow-Credentials", "true");
    res.setHeader("Access-Control-Allow-Methods", "GET,HEAD,OPTIONS,POST,PUT");
    res.setHeader("Access-Control-Allow-Headers", "Access-Control-Allow-Headers, Origin,Accept, X-Requested-With, Content-Type, Access-Control-Request-Method, Access-Control-Request-Headers");
    next();
});

// we have the equitiesRouter set up here so that the frontend can
// 'connect' to the backend and talk to it
const equitiesRouter = require('./routes/equities');
app.use('/equities', equitiesRouter);

app.listen(port, () => console.log(`⚡️ Server has started on port ${port}.`))