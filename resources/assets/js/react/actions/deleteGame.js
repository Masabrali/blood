import handleResponse from './handleResponse';
import gameDeleted from './gameDeleted';

export default function (game) {
    
    return dispatch => {
        return fetch(`/api/games/${game}`, {
            method: 'DELETE',
            headers: {
                'Content-Type': 'application/json'
            }
        })
        .then(handleResponse)
        .then( data => dispatch( gameDeleted(data) ));
    };
}
