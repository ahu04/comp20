const express = require('express')
const router = express.Router()
const Equity = require('../models/equity')

// this file contains all the routes that the frontend can access to 
// retrieve data from, and post data to the databse

// Get all company names and stock ticker
router.get('/', async (req, res) => {
    try {
        const equities = await Equity.find();
        res.json(equities)
    } catch (err) {
        res.status(500).json({message: err.message})
    }
})

// Post one entry to the database
// (entry is an object that contains company name and its stock ticker)
router.post('/', async (req, res) => {
    const equity = new Equity({
        company_name: req.body.company_name,
        stock_ticker: req.body.stock_ticker
    })

    try {
        // .save() is from mongoose, it posts the equity to the mongodb database
        const newEquity = await equity.save()
        // return success status
        res.status(201).json(newEquity)
    } catch (err) {
        res.status(400).json({message: err.message})
    }
});

// Get data through specified stock ticker
router.get('/ticker/:id', async (req, res) => {
    try {
        // find all entries from the database with stock ticker == req.params.id which is passed in from the frontend
        const equities = await Equity.find({ stock_ticker: req.params.id }).exec();
        res.json(equities)
    } catch (err) {
        res.status(500).json({message: err.message})
    }
})

// Get data through specified company name
router.get('/company/:id', async (req, res) => {
    try {
        // find all entries from the database with company name == req.params.id which is passed in from the frontend
        const equities = await Equity.find({ company_name: req.params.id }).exec();
        // return the result
        res.json(equities)
    } catch (err) {
        res.status(500).json({message: err.message})
    }
})

// Form action post for searching
router.post('/search', async (req, res) => {
    try {
        let equities;
        // check the 'searchOption' from the form data, i.e. check which option is selected
        if (req.body.searchOption == "stock_ticker") {
            // search the entry in the database based on the search input
            equities = await Equity.find({ stock_ticker: req.body.searchInput }).exec();
        } else {
            equities = await Equity.find({ company_name: req.body.searchInput }).exec();
        }
        // return the result
        res.json(equities);
    } catch (err) {
        res.status(404).json({message: err.message})
    }
})

module.exports = router