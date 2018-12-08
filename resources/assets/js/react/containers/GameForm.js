import React, { Component } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import classnames from 'classnames';
import { Redirect } from 'react-router';

// Import actions
import saveGame from '../actions/saveGame';
import fetchGame from '../actions/fetchGame';
import updateGame from '../actions/updateGame';

// Import other containers and components

class GameForm extends Component {

    constructor(props) {

        super(props);

        this.state =  {
            id: (props.game && props.game.id)? props.game.id : null,
            title: (props.game && props.game.title)? props.game.title : '',
            cover: (props.game && props.game.cover)? props.game.cover : '',
            errors: {},
            loading: false,
            done: false
        };

        this.handleChange = this.handleChange.bind(this);
        this.handleSubmit = this.handleSubmit.bind(this);
    }

    componentWillReceiveProps (nextProps) {
        this.setState({
            id: nextProps.game.id,
            title: nextProps.game.title,
            cover: nextProps.game.cover
        });
    }

    componentDidMount() {
        if (this.props.match.params.id) this.props.fetchGame(this.props.match.params.id);
    }

    handleChange(e) {

        if (!!this.state.errors[ e.target.name ]) {

            let errors = Object.assign({}, this.state.errors);

            delete errors[ e.target.name ];

            this.setState({
                [e.target.name]: e.target.value,
                errors: errors
            });

        } else this.setState({ [e.target.name]: e.target.value });
    }

    handleSubmit(e) {
        e.preventDefault();

        // Validation
        let errors = {};
        if (this.state.title === '') errors.title = "Title can't be empty";
        if (this.state.cover === '') errors.cover = "Cover can't be empty";
        this.setState({ errors: errors });

        // Hand;e Data Submission to server
        const isValid = Object.keys( errors ).length === 0;

        if (isValid) {
            const { id, title, cover } = this.state;

            this.setState({ loading: true });

            if (id)
                this.props.updateGame({ id, title, cover }).then(
                    (data) => {
                        if (data.errors !== undefined) {

                            let errors = data.errors;

                            this.setState({ errors, loading: false });

                        } else
                            this.setState({ loading: false, done: true });
                    },
                    (error) => {

                        let errors = this.state.errors;

                        errors.global = {
                            type: error.response.status,
                            message: error.response.statusText
                        };

                        this.setState({ errors, loading: false });
                    }
                );
            else
                this.props.saveGame({ title, cover }).then(
                    (data) => {
                        if (data.errors !== undefined) {

                            let errors = data.errors;

                            this.setState({ errors, loading: false });

                        } else
                            this.setState({ loading: false, done: true });
                    },
                    (error) => {

                        let errors = this.state.errors;

                        errors.global = {
                            type: error.response.status,
                            message: error.response.statusText
                        };

                        this.setState({ errors, loading: false });
                    }
                );
        }

        return false;
    }

    render() {
        const form = (
            <form className={ classnames('form', { loading: this.state.loading }) } onSubmit={ this.handleSubmit }>
                <h3>Add New Game</h3>

                { this.state.loading && <div className="alert alert-info">
                    <span>Loading...</span>
                </div> }

                { !!this.state.errors.global && <div className="alert alert-danger">
                    <span>
                        {
                            this.state.errors.global.type + " " + this.state.errors.global.message
                        }
                    </span>
                </div> }

                <div className="form-group">
                    <label className="form-label control-label" htmlFor="title">Title</label>
                    <input className={ classnames('form-control', { 'is-invalid': !!this.state.errors.title }) } type="text" name="title" id="title" value={ this.state.title } onChange={ this.handleChange } />

                    {
                        this.state.errors.title !== ''  && <div className="invalid-feedback d-block">
                            <strong>{ this.state.errors.title }</strong>
                        </div>
                    }
                </div>
                <div className="form-group">
                    <label className="form-label control-label" htmlFor="cover">Cover</label>
                    <input className={ classnames('form-control', { 'is-invalid': !!this.state.errors.cover }) } type="text" name="cover" id="cover" value={ this.state.cover } onChange={ this.handleChange } />

                    {
                        this.state.errors.cover !== '' && <div className="invalid-feedback d-block">
                            <strong>{ this.state.errors.cover }</strong>
                        </div>
                    }
                </div>
                <div className="form-group">
                    {
                        this.state.cover !== '' && <img className="img img-responseive thumbnail border" alt="cover" src={ this.state.cover } />
                    }
                </div>
                <div className="form-group">
                    <button className={ classnames('btn', 'btn-primary', { 'disabled': this.state.loading }) } disabled={ !!this.state.loading }>Save</button>
                </div>
            </form>
        );

        return (
            <div className="container">
                { (!!this.state.done)? <Redirect to="/games" /> : form }
            </div>
        );
    }
}

GameForm.propTypes = {};

function mapStateToProps(state, props) {

    if (props.match.params.id)
        return {
            game: state.games.find( item => item.id === Number(props.match.params.id) )
        };
    else
        return { game: null };
}

function matchDispatchToProps(dispatch) {
    return bindActionCreators({
        saveGame: saveGame,
        fetchGame: fetchGame,
        updateGame: updateGame
    }, dispatch);
}

export default connect(mapStateToProps, matchDispatchToProps)(GameForm);
