import React, { Component } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';

// Import Actions
import fetchGames from '../actions/fetchGames';
import deleteGame from '../actions/deleteGame';

// Import containers
import GamesList from './GamesList';

class Games extends Component {

    constructor(props) {

        super(props);

        this.state = {
            errors: {},
            loading: false,
            done: false
        }

        this.deleteGame = this.deleteGame.bind(this);
    }

    componentDidMount () {
        this.props.fetchGames();
    }

    refreshGames() {
        this.props.fetchGames();
    }

    deleteGame(game) {

        this.setState({ loading: true });

        this.props.deleteGame(game)
        .then(
            (data) => {
                if (data.errors !== undefined) {

                    let errors = data.errors;

                    this.setState({ errors, loading: false });

                } else {
                    this.setState({ loading: false, done: true });

                    setTimeout(function () {
                        this.setState({ done: false });
                    }.bind(this), 2000);
                }
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

    render() {
        return (
            <div className="container games">
                <div className="container clear-fix">
                    <a href="#" className="link float-right" onClick={ this.refreshGames.bind(this) }>Refresh</a>
                    <Link className="link float-right mr-1" to="/games/new">New Game</Link>

                    <h3>Games</h3>
                </div>

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

                { this.state.done && <div className="alert alert-success">
                    <span>Item deleted successfully</span>
                </div> }

                <GamesList games={ this.props.games } deleteGame={this.deleteGame} />
            </div>
        );
    }
}

Games.propTypes = {
    games: PropTypes.array.isRequired,
    fetchGames: PropTypes.func.isRequired,
    deleteGame: PropTypes.func.isRequired
};

function mapStateToProps(state) {
    return {
        games: state.games
    };
}

function matchDispatchToProps(dispatch) {
    return bindActionCreators({
        fetchGames: fetchGames,
        deleteGame: deleteGame
    }, dispatch);
}

export default connect(mapStateToProps, matchDispatchToProps)(Games);
