import $ from 'jquery';
import React, { Component } from 'react';
import ReactDOM, { render } from 'react-dom';
import { createStore } from 'redux';
import { Provider } from 'react-redux';
import { BrowserRouter } from 'react-router-dom';

import store from './store';

import App from './components/App';

render(
    <BrowserRouter>
        <Provider store={ store }>
            <App />
        </Provider>
    </BrowserRouter>
, $('#app')[0]);
