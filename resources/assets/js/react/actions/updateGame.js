import handleResponse from './handleResponse';
import gameUpdated from './gameUpdated';

export default function(game) {
    return dispatch => {
        return fetch('/api/games/update', {
            method: 'PUT',
            body: JSON.stringify(game),
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(handleResponse)
        .then( data => dispatch( gameUpdated(data) ) );
    };
}
