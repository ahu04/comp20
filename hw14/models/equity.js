const mongoose = require('mongoose');

// defining a 'schema' i.e. structure of the entry to go into
// the mongodb database

const equitySchema = new mongoose.Schema({
    company_name: {
        type: String,
        required: true
    },
    stock_ticker: {
        type: String,
        required: true
    }
})

module.exports = mongoose.model('Equity', equitySchema)