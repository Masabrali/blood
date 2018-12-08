import React, { Component } from 'react';
import ReactDOM from 'react-dom';
import $ from 'jquery';

import Movies from './Movies';
import Board from './Board';
import CheckBox from './CheckBox'

export default class Masasi extends Component {
    render () {

        return (

          <div className="card">
              <div className="card-header">
                  <h1>Masasi Component</h1>
              </div>
              <div className="card-body">

                  <br />
                  <Movies />
                  <br />
                  <Board />
                  <br/>
                  <CheckBox />

              </div>
          </div>

        );
    }
};

ReactDOM.render(<Masasi />, $('#masasi')[0]);
