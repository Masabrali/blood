import setGame from './setGame';

export default function (game) {
    return dispatch => {
        fetch(`/api/games/${game}`)
        .then( response => response.json() )
        .then( data => dispatch(setGame(data) ))
    };
}
