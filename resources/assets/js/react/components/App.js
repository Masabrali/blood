// Import React
import React, { Component } from 'react';
import { Router, Switch, Route } from 'react-router-dom';

// Imoprt your components
import NavBar from './NavBar';
import Users from './Users';

// Import your Containers
import Games from '../containers/Games';
import GameForm from '../containers/GameForm';

export default class App extends Component {
    render() {
        return (
            <div className="container p-2">
                <NavBar />
                <hr />

                <Switch>
                    <Route path="/redux" component={ Users } />
                    <Route path="/users" component={ Users } />
                    <Route exact={true} path="/games/new" component={ GameForm } />
                    <Route path="/games" component={ Games } />
                    <Route path="/game/:id" component={ GameForm } />
                </Switch>

            </div>
        );
    }
}
