import React, { Component } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';

// Import Actions
import addUser from '../actions/addUser';

class AddUser extends Component {

    constructor(props) {
        super(props);

        this.submit = this.submit.bind(this);
    }

    submit(e) {
        e.preventDefault();

        this.props.addUser({ name: e.target.name.value });

        return false;
    }

    render() {
        return (
            <div className="container my-2 p-2">
                <form className="form" onSubmit={ this.submit }>
                    <label className="control-label">
                        <span>Name:&nbsp;</span>
                        <input type="text" name="name" ref="name" className="form-control mb-1" />
                        <button type="submit" className="btn btn-primary">Add User</button>
                    </label>
                </form>
            </div>
        );
    }
}

function mapStateToProps(state) {
    return {
        users: state.users
    };
}

function matchDispatchToProps(dispatch) {
    return bindActionCreators({
        addUser: addUser
    }, dispatch);
}

export default connect(mapStateToProps, matchDispatchToProps)(AddUser);
