import React, { Component } from 'react';
import { Link, NavLink } from 'react-router-dom';

export default class NavBar extends Component {
    render() {
        return (
            <nav className="navbar">
                <div className="navbar-brand">
                    <Link to="/redux">Redux</Link>
                </div>
                <nav className="nav nav-pills pull-right">
                    <NavLink className="nav-link" activeClassName="active" to="/redux">Redux</NavLink>

                    <NavLink className="nav-link" activeClassName="active" to="/users">Users</NavLink>

                    <NavLink className="nav-link" activeClassName="active" to="/games">Games</NavLink>
                </nav>
            </nav>
        )
    }
}
