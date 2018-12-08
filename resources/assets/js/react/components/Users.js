import React, { Component } from 'react';
import PropTypes from 'prop-types';

import UserList from '../containers/UserList';
import UserDetails from '../containers/UserDetails';
import AddedUser from '../containers/AddedUser';
import AddUser from '../containers/AddUser';

class Users extends Component {
    render() {
        return (
            <div className="container users">
                <h3>Username List:</h3>
                <UserList />
                <br />
                <hr />
                <h4>User details</h4>
                <UserDetails />
                <br />
                <hr />
                <h4>Added User</h4>
                <AddedUser />
                <br />
                <hr />
                <AddUser />
            </div>
        );
    }
}

UserList.propTypes = {}

export default Users;
