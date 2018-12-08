import React, { Component } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';

class AddedUser extends Component {
    render() {
        if (!this.props.user) return (<p>No user added. Add a user...</p>);
        else
            return (
                <p>{ this.props.user.name }</p>
            );
    }
}

AddedUser.propTypes = {
    user: PropTypes.object
}

function mapStateToProps(state) {
    return {
        user: state.addedUser
    };
}

function matchDispatchToProps(dispatch) {
    return bindActionCreators({}, dispatch);
}

export default connect(mapStateToProps)(AddedUser);
