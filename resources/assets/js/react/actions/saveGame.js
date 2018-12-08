import handleResponse from './handleResponse';
import addGame from './addGame';

export default function(game) {
    return dispatch => {
        return fetch('/api/games/save', {
            method: 'POST',
            body: JSON.stringify(game),
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(handleResponse)
        .then( data => dispatch( addGame(data) ) );
    }
}
