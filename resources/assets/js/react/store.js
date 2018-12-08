import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import { createStore, applyMiddleware } from 'redux';
import thunk from 'redux-thunk';
import { composeWithDevTools } from 'redux-devtools-extension';

import reducers from './reducers/root';

const store = createStore(
    reducers,
    composeWithDevTools(
        applyMiddleware(thunk)
    )
);

export default store;
