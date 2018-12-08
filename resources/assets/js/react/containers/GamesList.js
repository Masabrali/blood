import React, { Component } from 'react';
import { bindActionCreators } from 'redux';
import { connect } from 'react-redux';
import PropTypes from 'prop-types';

// Import Components
import GameCard from '../components/GameCard';

class GamesList extends Component {

    constructor(props) {

        super(props);

        this.gamesList = this.gamesList.bind(this);
    }

    emptyMessage() {
        return (<p>There are no games yet in your collecton</p>);
    }

    gamesList() {
        return (
            <div className="row">
                {
                    this.props.games.map((game, index) => (
                        <GameCard key={game.id} index={game.id} game={ game } deleteGame={ this.props.deleteGame } />
                    ))
                }
            </div>
        );
    }

    render() {
        return ( (this.props.games.length === 0)? this.emptyMessage():this.gamesList() );
    }
}

GamesList.propTypes = {
    games: PropTypes.array.isRequired,
    deleteGame: PropTypes.func.isRequired
};

function mapStateToProps(state) {
    return {
        games: state.games
    };
}

function matchDispatchToProps(dispatch) {
    return bindActionCreators({}, dispatch);
}

export default connect(mapStateToProps)(GamesList);
