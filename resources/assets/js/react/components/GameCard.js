import React, { Component } from 'react';
import PropTypes from 'prop-types';
import { Link } from 'react-router-dom';

export default class GameCard extends Component {
    render() {
        return (
            <div className="col-md-3 mb-4">
                <div className="card">
                    <div className="card-body p-0">
                        <img className="img img-responsive w-100 h-auto" src={ this.props.game.cover } alt={ this.props.game.cover } />
                    </div>
                    <div className="card-footer">
                        <header className="header pb-2">
                            <h5 className="font-weight-bold">{ this.props.game.title }</h5>
                        </header>
                        <div className="btn-group w-100" role="group" aria-label="Basic example">
                            <Link to={ `/game/${this.props.game.id}` } className="btn btn-outline-warning w-50">Edit</Link>
                            <button className="btn btn-outline-danger w-50" onClick={ () => this.props.deleteGame(this.props.game.id) }>Delete</button>
                        </div>
                    </div>
                </div>
            </div>
        );
    }
}

GameCard.propTypes = {
    game: PropTypes.object.isRequired
}
