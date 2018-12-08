import React, { Component } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';

// Import Actions
import selectUser from '../actions/selectUser';

class UserList extends Component {

    constructor(props) {

        super(props);

        this.state = {
            users: props.users
        };

        this.users = this.users.bind(this);
    }

    users(user, index) {
        return (
            <li
                key={index}
                index={index}
                onClick={ () => this.props.selectUser(user) }
            >
                { user.name }
            </li>
        )
    }

    render() {
        return (
            <ul>
                { this.props.users.map(this.users) }
            </ul>
        );
    }
}

UserList.propTypes = {
    users: PropTypes.array.isRequired
}

function mapStateToProps(state) {
    return {
        users: state.users
    };
}

function matchDispatchToProps(dispatch) {
    return bindActionCreators({
        selectUser: selectUser
    }, dispatch);
}

export default connect(mapStateToProps, matchDispatchToProps)(UserList);
