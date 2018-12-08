
import React, { Component } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';

class UserDetails extends Component {

    render() {
        if (!this.props.user) return (<p>No user selected. Select a user...</p>);
        else
            return (
                <p>{ this.props.user.name }</p>
            );
    }
}

UserDetails.propTypes = {
    user: PropTypes.object
}

function mapStateToProps(state) {
    return {
        user: state.activeUser
    };
}

function matchDispatchToProps(dispatch) {
    return bindActionCreators({}, dispatch);
}

export default connect(mapStateToProps)(UserDetails);
